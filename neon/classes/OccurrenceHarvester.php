<?php
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
include_once($SERVER_ROOT.'/classes/TaxonomyUtilities.php');
include_once($SERVER_ROOT.'/classes/TaxonomyHarvester.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/config/symbini.php');

class OccurrenceHarvester{

	private $conn;
	private $activeCollid = 0;
	private $currentOccurArr;
	private $currentDetArr;
	private $fateLocationArr;
	private $taxonCodeArr = array();
	private $taxonArr = array();
	private $stateArr = array();
	private $personnelArr = array();
	private $timezone = 'America/Denver';
	private $sampleClassArr = array();
	private $domainSiteArr = array();
	private $replaceFieldValues = false;
	private $neonApiBaseUrl;
	private $neonApiKey;
	private $errorStr;
	private $errorLogArr = array();

	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon('write');
		$this->neonApiBaseUrl = 'https://data.neonscience.org/api/v0';
		if(isset($GLOBALS['NEON_API_KEY'])) $this->neonApiKey = $GLOBALS['NEON_API_KEY'];
	}

	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	//Occurrence harvesting functions
	public function getHarvestReport($shipmentPK){
		$retArr = array();
		$sql = 'SELECT s.errorMessage AS errMsg, COUNT(s.samplePK) as sampleCnt, COUNT(o.occid) as occurrenceCnt '.
			'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
			'WHERE s.checkinuid IS NOT NULL AND s.sampleReceived = 1 AND (o.collid NOT IN(81,84) OR o.collid IS NULL) ';
		if($shipmentPK) $sql .= 'AND s.shipmentPK = '.$shipmentPK;
		$sql .= ' GROUP BY errMsg';
		$rs= $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$errMsg = $r->errMsg;
			if(!$errMsg) $errMsg = 'null';
			$retArr[$errMsg]['s-cnt'] = $r->sampleCnt;
			$retArr[$errMsg]['o-cnt'] = $r->occurrenceCnt;
		}
		$rs->free();
		return $retArr;
	}

	public function batchHarvestOccid($postArr){
		//Set variables
		$status = false;
		if(isset($postArr['replaceFieldValues']) && $postArr['replaceFieldValues']) $this->setReplaceFieldValues(true);
		$sqlWhere = '';
		$sqlPrefix = '';
		if(isset($postArr['scbox'])){
			$sqlWhere = 'AND s.samplePK IN('.implode(',',$postArr['scbox']).')';
		}
		elseif($postArr['action'] == 'harvestOccurrences'){
			if(isset($postArr['nullOccurrencesOnly'])){
				$sqlWhere .= 'AND (s.occid IS NULL) ';
			}
			if($postArr['collid']){
				$sqlWhere .= 'AND (o.collid = '.$postArr['collid'].') ';
			}
			if($postArr['errorStr'] == 'nullError'){
				$sqlWhere .= 'AND (s.errorMessage IS NULL) ';
			}
			elseif($postArr['errorStr']){
				$sqlWhere .= 'AND (s.errorMessage = "'.$this->cleanInStr($postArr['errorStr']).'") ';
			}
			if($postArr['harvestDate']){
				$sqlWhere .= 'AND (s.harvestTimestamp IS NULL OR s.harvestTimestamp < "'.$postArr['harvestDate'].'") ';
			}
			$sqlPrefix = 'ORDER BY s.shipmentPK ';
			if(isset($postArr['limit']) && is_numeric($postArr['limit'])) $sqlPrefix .= 'LIMIT '.$postArr['limit'];
			else $sqlPrefix .= 'LIMIT 1000 ';
		}
		if($sqlWhere){
			$sqlWhere = 'WHERE s.checkinuid IS NOT NULL AND s.sampleReceived = 1 AND (s.acceptedForAnalysis = 1 || o.occid IS NOT NULL) AND (o.collid NOT IN(81,84) OR o.collid IS NULL) '.$sqlWhere;
			$status = $this->batchHarvestOccurrences($sqlWhere.$sqlPrefix);
		}
		return $status;
	}

	private function batchHarvestOccurrences($sqlWhere){
		set_time_limit(3600);
		if($sqlWhere){
			$this->setStateArr();
			$this->setDomainSiteArr();
			$this->protectCuratorAnnotations();
			//if(!$this->setSampleClassArr()) echo '<li>'.$this->errorStr.'</li>';
			echo '<li>Target record count: '.number_format($this->getTargetCount($sqlWhere)).'</li>';
			$collArr = array();
			$occidArr = array();
			$cnt = 1;
			$shipmentPK = '';
			$sql = 'SELECT s.samplePK, s.shipmentPK, s.sampleID, s.hashedSampleID, s.alternativeSampleID, s.sampleUuid, s.sampleCode, s.sampleClass, s.taxonID, '.
				's.individualCount, s.filterVolume, s.namedLocation, s.collectDate, s.symbiotaTarget, s.igsnPushedToNEON, s.occid '.
				'FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.
				$sqlWhere;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->errorStr = '';
				if($shipmentPK != $r->shipmentPK){
					$shipmentPK = $r->shipmentPK;
					echo '<li><b>Processing shipment #'.$shipmentPK.'</b></li>';
				}
				echo '<li style="margin-left:15px">'.$cnt.': '.($r->occid?($this->replaceFieldValues?'Rebuilding':'Appending'):'Harvesting').' '.($r->sampleID?$r->sampleID:$r->sampleCode).'... ';
				$sampleArr = array();
				$sampleArr['samplePK'] = $r->samplePK;
				$sampleArr['sampleID'] = strtoupper($r->sampleID);
				$sampleArr['hashedSampleID'] = $r->hashedSampleID;
				$sampleArr['alternativeSampleID'] = strtoupper($r->alternativeSampleID);
				$sampleArr['sampleUuid'] = $r->sampleUuid;
				$sampleArr['sampleCode'] = $r->sampleCode;
				$sampleArr['sampleClass'] = $r->sampleClass;
				$sampleArr['taxonID'] = $r->taxonID;
				$sampleArr['individualCount'] = $r->individualCount;
				$sampleArr['filterVolume'] = $r->filterVolume;
				$sampleArr['namedLocation'] = $r->namedLocation;
				$sampleArr['collectDate'] = $r->collectDate;
				$sampleArr['symbiotaTarget'] = $r->symbiotaTarget;
				$sampleArr['igsnPushedToNEON'] = $r->igsnPushedToNEON;
				$sampleArr['occid'] = $r->occid;
				$this->setOccurrenceRecord($r->occid);
				if(isset($this->currentOccurArr['occurrenceID'])) $sampleArr['occurrenceID'] = $this->currentOccurArr['occurrenceID'];
				if($this->harvestNeonApi($sampleArr)){
					if($dwcArr = $this->getDarwinCoreArr($sampleArr)){
						if($occid = $this->loadOccurrenceRecord($dwcArr, $r->samplePK, $r->occid)){
							if(!in_array($dwcArr['collid'],$collArr)) $collArr[] = $dwcArr['collid'];
							$occidArr[] = $occid;
							echo '<a href="'.$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid='.$occid.'" target="_blank">success!</a>';
						}
						if($this->errorStr) echo '</li><li style="margin-left:30px">WARNING: '.$this->errorStr.'</li>';
						else echo '</li>';
					}
					else{
						echo '</li><li style="margin-left:30px">'.$this->errorStr.'</li>';
					}
				}
				else{
					echo '</li><li style="margin-left:30px">ABORT: '.trim($this->errorStr, ';, ').'</li>';
				}
				$cnt++;
				flush();
				ob_flush();
			}
			$rs->free();
			if($shipmentPK){
				$this->adjustTaxonomy($occidArr);
				//Set recordID GUIDs
				echo '<li>Setting recordID UUIDs for all occurrence records...</li>';
				$uuidManager = new UuidFactory();
				$uuidManager->setSilent(1);
				$uuidManager->populateGuids();
				//Update stats for each collection affected
				if($collArr){
					echo '<li>Update stats for each collection...</li>';
					$collManager = new OccurrenceCollectionProfile();
					foreach($collArr as $collID){
						echo '<li style="margin-left:15px">Stat update for collection <a href="'.$GLOBALS['CLIENT_ROOT'].'/collections/misc/collprofiles.php?collid='.$collID.'" target="_blank">#'.$collID.'</a>...</li>';
						$collManager->setCollid($collID);
						$collManager->updateStatistics(false);
						flush();
						ob_flush();
					}
				}
			}
			else echo '<li><b>No records processed. Note that records have to be checked in before occurrences can be harvested.</b></li>';
			//Log any notices, warnings, and errors
			if($this->errorLogArr){
				$logPath = $GLOBALS['SERVER_ROOT'].'/neon/content/logs/occurHarvest_error_'.date('Y-m-d').'.log';
				$logFH = fopen($logPath, 'a');
				fwrite($logFH,'Harvesting event: '.date('Y-m-d H:i:s')."\n");
				fwrite($logFH,'-------------------------'."\n");
				foreach($this->errorLogArr as $errStr){
					fwrite($logFH,$errStr."\n");
				}
				fwrite($logFH,"\n\n");
				fclose($logFH);
			}
		}
		return false;
	}

	private function protectCuratorAnnotations(){
		//Temporary code needed until until db patch 1.3 is officialize and annoator edits saved
		$this->conn->query('UPDATE omoccurdeterminations SET enteredByUid = 16 WHERE identifiedBy LIKE "%Laura% Steger%" AND enteredByUid IS NULL');
		$this->conn->query('UPDATE omoccurdeterminations SET enteredByUid = 3 WHERE identifiedBy LIKE "%Andrew Johnston%" AND enteredByUid IS NULL');
		$this->conn->query('UPDATE omoccurdeterminations SET enteredByUid = 56 WHERE identifiedBy LIKE "R% Liao" AND enteredByUid IS NULL');
	}

	private function getTargetCount($sqlWhere){
		$retCnt = 0;
		$sql = 'SELECT COUNT(s.samplePK) AS cnt FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid '.$sqlWhere;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->cnt;
		}
		$rs->free();
		return $retCnt;
	}

	private function setOccurrenceRecord($occid){
		$retArr = array();
		unset($this->currentOccurArr);
		$this->currentOccurArr = array();
		unset($this->currentDetArr);
		$this->currentDetArr = array();
		if($occid){
			$sql = 'SELECT catalogNumber, occurrenceID, eventID, recordedBy, eventDate, eventDate2, individualCount,
				reproductiveCondition, sex, lifeStage, associatedTaxa, occurrenceRemarks, preparations, dynamicProperties, verbatimAttributes, habitat, country,
				stateProvince, county, locality, locationID, decimalLatitude, decimalLongitude, coordinateUncertaintyInMeters,
				verbatimCoordinates, georeferenceSources, minimumElevationInMeters, maximumElevationInMeters, verbatimElevation
				FROM omoccurrences WHERE occid = '.$occid;
			$rs = $this->conn->query($sql);
			$this->currentOccurArr = $rs->fetch_assoc();
			$rs->free();

			$sql2 = 'SELECT detid, sciname, scientificNameAuthorship, taxonRemarks, identifiedBy, dateIdentified,
				identificationRemarks, identificationReferences, identificationQualifier, isCurrent, enteredByUid
				FROM omoccurdeterminations WHERE occid = '.$occid;
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_assoc()){
				$this->currentDetArr[$r2['detid']] = $r2;
			}
			$rs2->free();
		}
		return $retArr;
	}

	private function harvestNeonApi(&$sampleArr){
		$this->setSampleErrorMessage($sampleArr['samplePK'], '');
		$url = '';
		$sampleViewArr = array();

		if($sampleArr['sampleCode']){
			$url = $this->neonApiBaseUrl.'/samples/view?barcode='.$sampleArr['sampleCode'].'&apiToken='.$this->neonApiKey;
		}
		elseif($sampleArr['sampleUuid']){
			$url = $this->neonApiBaseUrl.'/samples/view?sampleUuid='.$sampleArr['sampleUuid'].'&apiToken='.$this->neonApiKey;
		}
		elseif(isset($sampleArr['occurrenceID']) && $sampleArr['occurrenceID']){
			$url = $this->neonApiBaseUrl.'/samples/view?archiveGuid='.$sampleArr['occurrenceID'].'&apiToken='.$this->neonApiKey;
		}
		elseif($sampleArr['sampleID'] && $sampleArr['sampleClass']){
			$url = $this->neonApiBaseUrl.'/samples/view?sampleTag='.urlencode($sampleArr['sampleID']).'&sampleClass='.urlencode($sampleArr['sampleClass']).'&apiToken='.$this->neonApiKey;
		}
		else{
			$this->errorStr = 'Sample identifiers incomplete';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
			return false;
		}
		$sampleViewArr = $this->getNeonApiArr($url);
		//echo 'url: '.$url.'<br/>';

		if(!isset($sampleViewArr['sampleViews'])){
			$this->errorStr = 'NEON API failed to return sample data';
			//$this->errorStr .= ': (<a href="'.$url.'" target="_blank">'.$url.'</a>)';
			$this->updateSampleRecord(array('errorMessage'=>$this->errorStr),$sampleArr['samplePK']);
			return false;
		}
		if(count($sampleViewArr['sampleViews']) > 1){
			$this->errorStr = 'Harvest skipped: NEON API returned multiple sampleViews ';
			$this->updateSampleRecord(array('errorMessage'=>$this->errorStr),$sampleArr['samplePK']);
			return false;
		}
		$viewArr = current($sampleViewArr['sampleViews']);
		if(!$this->checkIdentifiers($viewArr, $sampleArr)) return false;
		//Get fateLocation and process parent samples
		unset($this->fateLocationArr);
		$this->fateLocationArr = array();
		$this->processViewArr($sampleArr, $sampleViewArr);
		if($this->fateLocationArr){
			ksort($this->fateLocationArr);
			$locArr = current($this->fateLocationArr);
			$sampleArr['fate_location'] = $locArr['loc'];
			if(!isset($sampleArr['collect_end_date'])) $sampleArr['collect_end_date'] = $locArr['date'];
		}
		return true;
	}

	private function checkIdentifiers($viewArr, &$sampleArr){
		$status = true;
		$neonSampleUpdate = array();
		if(isset($viewArr['sampleUuid']) && $viewArr['sampleUuid']){
			//Populate or verify/coordinate sampleUuid
			if(!$sampleArr['sampleUuid']){
				$sampleArr['sampleUuid'] = $viewArr['sampleUuid'];
				$neonSampleUpdate['sampleUuid'] = $viewArr['sampleUuid'];
			}
			elseif($sampleArr['sampleUuid'] != $viewArr['sampleUuid']){
				$this->errorLogArr[] = 'NOTICE: sampleUuid updated from '.$sampleArr['sampleUuid'].' to '.$viewArr['sampleUuid'];
				$this->errorStr .= '; <span style="color:red">DATA ISSUE</span>: sampleUuid failing to match (old: '.$sampleArr['sampleUuid'].', new: '.$viewArr['sampleUuid'].')';
				$status = false;
			}
		}
		if($sampleArr['sampleClass'] && isset($viewArr['sampleClass']) && $sampleArr['sampleClass'] != $viewArr['sampleClass']){
			//sampleClass are not equal; don't update, just record within NeonSample error field and then skip harvest of this record
			$this->errorStr .= '; <span style="color:red">DATA ISSUE</span>: sampleClass failing to match (old: '.$sampleArr['sampleClass'].', new: '.$viewArr['sampleClass'].')';
			$status = false;
		}
		if(isset($viewArr['archiveGuid']) && $viewArr['archiveGuid']){
			$igsnMatch = array();
			if(preg_match('/(NEON[A-Z,0-9]{5})/', $viewArr['archiveGuid'], $igsnMatch)){
				if($sampleArr['occid']){
					//This is a reharvest event, check to make sure IGSNs match
					if(isset($sampleArr['occurrenceID']) && $sampleArr['occurrenceID']){
						if($sampleArr['occurrenceID'] == $igsnMatch[1]){
							$neonSampleUpdate['igsnPushedToNEON'] = 1;
						}
						else{
							$this->setSampleErrorMessage($sampleArr['samplePK'], '<span style="color:red">DATA ISSUE</span>: IGSN failing to match with API value');
							$neonSampleUpdate['igsnPushedToNEON'] = 2;
						}
					}
					else{
						if(!$this->igsnExists($igsnMatch[1],$sampleArr)){
							if(!$this->updateOccurrenceIgsn($igsnMatch[1], $sampleArr['occid'])){
								$this->setSampleErrorMessage($sampleArr['samplePK'], 'NOTICE: unable to update igsn: '.$this->conn->error);
								$neonSampleUpdate['igsnPushedToNEON'] = 3;
							}
						}
					}
				}
				else{
					//New record should use ISGN, if it is not already assigned to another record
					if(!$this->igsnExists($igsnMatch[1],$sampleArr)) $sampleArr['occurrenceID'] = $igsnMatch[1];
				}
			}
		}
		if($sampleArr['sampleID'] && isset($viewArr['sampleTag']) && $sampleArr['sampleID'] != $viewArr['sampleTag'] && $sampleArr['hashedSampleID'] != $viewArr['sampleTag']){
			//sampleIDs (sampleTags) are not equal; report error and abort harvest
			if(substr($viewArr['sampleTag'],-1) == '=' || !preg_match('/[_\.]+/',$viewArr['sampleTag'])){
				$neonSampleUpdate['hashedSampleID'] = $viewArr['sampleTag'];
				$sampleArr['hashedSampleID'] = $viewArr['sampleTag'];
			}
			else{
				$this->errorStr .= '; <span style="color:red">DATA ISSUE</span>: sampleID failing to match';
				$status = false;
				/*
				 if($this->updateSampleID($viewArr['sampleTag'], $sampleArr['sampleID'], $sampleArr['samplePK'], $sampleArr['occid'])){
				 $this->errorLogArr[] = 'NOTICE: sampleID updated from '.$sampleArr['sampleID'].' to '.$viewArr['sampleTag'].' (samplePK: '.$sampleArr['samplePK'].', occid: '.$sampleArr['occid'].')';
				 }
				 else{
				 $errMsg = (isset($neonSampleUpdate['errorMessage'])?$neonSampleUpdate['errorMessage'].'; ':'');
				 $errMsg .= 'DATA ISSUE: failed to reset sampleID using changed API value';
				 $this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
				 }
				 */
			}
		}
		if(!$status) $this->setSampleErrorMessage($sampleArr['samplePK'], trim($this->errorStr, '; '));
		$this->updateSampleRecord($neonSampleUpdate,$sampleArr['samplePK']);
		return $status;
	}

	private function updateSampleRecord($neonSampleUpdate,$samplePK){
		if($neonSampleUpdate){
			$sqlInsert = '';
			foreach($neonSampleUpdate as $field => $value){
				$sqlInsert .= $field.' = "'.$this->cleanInStr($value).'", ';
			}
			$sql = 'UPDATE NeonSample SET '.trim($sqlInsert,', ').' WHERE (samplePK = '.$samplePK.')';
			if(!$this->conn->query($sql)){
				echo '</li><li style="margin-left:30px">ERROR updating NeonSample record: '.$this->conn->error.'</li>';
			}
		}
	}

	private function igsnExists($igsn, &$sampleArr){
		$occid = 0;
		$sql = 'SELECT occid FROM omoccurrences WHERE occurrenceid = "'.$igsn.'" ';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$occid = $r->occid;
		}
		$rs->free();
		if($occid){
			//Another records exists within the portal with the same IGSN (not ideal)
			$sampleArr['occurrenceID'] = $igsn.'-dupe (data issue!)';
			$errMsg = 'DATA ISSUE: another record exists with duplicate IGSN registered within NEON API';
			$this->setSampleErrorMessage($sampleArr['samplePK'], $errMsg);
			return true;
		}
		return false;
	}

	private function updateOccurrenceIgsn($igsn, $occid){
		$status = false;
		$sql = 'UPDATE omoccurrences SET occurrenceID = "'.$igsn.'" WHERE occurrenceid IS NULL AND occid = '.$occid;
		if($this->conn->query($sql)) $status = true;
		return $status;
	}

	private function processViewArr(&$sampleArr, $viewArr, $sampleRank = 0){
		if(!isset($viewArr['sampleViews'])){
			$this->errorStr = 'sampleViews object failed to be returned from NEON API';
			return false;
		}
		$viewArr = current($viewArr['sampleViews']);
		//if(isset($viewArr['sampleClass']) && $viewArr['sampleClass'] == 'mam_pertrapnight_in.tagID') $this->createRelationship($viewArr['childSampleIdentifiers']);
		//parse Sample Event details
		$eventArr = $viewArr['sampleEvents'];
		$harvestIdentifications = true;
		if($sampleRank && isset($sampleArr['identifications'])) $harvestIdentifications = false;
		if($eventArr){
			foreach($eventArr as $eArr){
				$tableName = $eArr['ingestTableName'];
				if(strpos($tableName,'shipment')) continue;
				//if(strpos($tableName,'identification')) continue;
				//if(strpos($tableName,'sorting')) continue;
				if(strpos($tableName,'archive')) continue;
				if(strpos($tableName,'barcoding')) continue;
				if(strpos($tableName,'dnaStandardTaxon')) continue;
				if(strpos($tableName,'dnaExtraction')) continue;
				if(strpos($tableName,'markerGeneSequencing')) continue;
				if(strpos($tableName,'metagenomeSequencing')) continue;
				if(strpos($tableName,'metabarcodeTaxonomy')) continue;
				if(strpos($tableName,'pcrAmplification')) continue;
				if(strpos($tableName,'perarchivesample')) continue;
				if(strpos($tableName,'persample')) continue;
				if(strpos($tableName,'pertaxon')) continue;
				if(strpos($tableName,'pervial')) continue;
				if($tableName == 'mpr_perpitprofile_in') continue;
				$fieldArr = $eArr['smsFieldEntries'];
				$fateLocation = ''; $fateDate = '';
				$readAssocTaxon = false;
				$identArr = array(); $assocMedia = array(); $assocTaxa = array();
				$tableArr = array();
				foreach($fieldArr as $fArr){
					if($tableName == 'tck_pathogenresults_in'){
						if($fArr['smsKey'] == 'analysis_type' && $fArr['smsValue'] == 'Positive'){
							$readAssocTaxon = true;
						}
						if($fArr['smsKey'] == 'taxon' && $fArr['smsValue'] != 'HardTick DNA Quality' && $readAssocTaxon){
							$assocTaxa['verbatimSciname'] = $fArr['smsValue'];
							$assocTaxa['relationship'] = 'hostOf';
						}
					}
					else{
						if($fArr['smsKey'] == 'fate_location') $fateLocation = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'collection_location' && $fArr['smsValue']) $tableArr['collection_location'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'fate_date' && $fArr['smsValue']) $fateDate = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'event_id' && $fArr['smsValue']) $tableArr['event_id'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'collected_by' && $fArr['smsValue']) $tableArr['collected_by'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'collect_start_date' && $fArr['smsValue']) $tableArr['collect_start_date'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'collect_end_date' && $fArr['smsValue']) $tableArr['collect_end_date'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'specimen_count' && $fArr['smsValue']) $tableArr['specimen_count'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'temperature' && $fArr['smsValue']) $tableArr['temperature'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'decimal_latitude' && $fArr['smsValue']) $tableArr['decimal_latitude'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'decimal_longitude' && $fArr['smsValue']) $tableArr['decimal_longitude'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'coordinate_uncertainty' && $fArr['smsValue']) $tableArr['coordinate_uncertainty'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'elevation' && $fArr['smsValue']) $tableArr['elevation'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'elevation_uncertainty' && $fArr['smsValue']) $tableArr['elevation_uncertainty'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'verbatim_depth' && $fArr['smsValue']) $tableArr['verbatim_depth'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'minimum_depth_in_meters' && $fArr['smsValue']) $tableArr['minimum_depth_in_meters'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'maximum_depth_in_meters' && $fArr['smsValue']) $tableArr['maximum_depth_in_meters'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'reproductive_condition' && $fArr['smsValue']) $tableArr['reproductive_condition'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'sex' && $fArr['smsValue']) $tableArr['sex'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'life_stage' && $fArr['smsValue']) $tableArr['life_stage'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'associated_taxa' && $fArr['smsValue']) $tableArr['associated_taxa'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'remarks' && $fArr['smsValue']) $tableArr['remarks'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'preservative_concentration' && $fArr['smsValue']) $tableArr['preservative_concentration'] = $fArr['smsValue'].', ';
						elseif($fArr['smsKey'] == 'preservative_volume' && $fArr['smsValue']) $tableArr['preservative_volume'] = $fArr['smsValue'].', ';
						elseif($fArr['smsKey'] == 'preservative_type' && $fArr['smsValue']) $tableArr['preservative_type'] = $fArr['smsValue'].', ';
						elseif($fArr['smsKey'] == 'sample_type' && $fArr['smsValue']) $tableArr['sample_type'] = $fArr['smsValue'];
						//elseif($fArr['smsKey'] == 'sample_condition' && $fArr['smsValue']) $tableArr['sample_condition'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'sample_mass' && $fArr['smsValue']) $tableArr['sample_mass'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'sample_volume' && $fArr['smsValue']) $tableArr['sample_volume'] = $fArr['smsValue'];
						elseif($fArr['smsKey'] == 'associated_media'){
							if(!strpos($fArr['smsValue'],'biorepo.neonscience.org/portal')) $assocMedia['url'] = $fArr['smsValue'];
						}
						elseif($fArr['smsKey'] == 'photographed_by') $assocMedia['photographer'] = $fArr['smsValue'];
						if($harvestIdentifications){
							if($fArr['smsKey'] == 'taxon' && $fArr['smsValue']){
								$identArr['sciname'] = $fArr['smsValue'];
								$identArr['taxon'] = $fArr['smsValue'];
							}
							elseif($fArr['smsKey'] == 'taxon_published' && $fArr['smsValue']) $identArr['taxonPublished'] = $fArr['smsValue'];
							elseif($fArr['smsKey'] == 'identified_by' && $fArr['smsValue']) $identArr['identifiedBy'] = $this->translatePersonnelArr($fArr['smsValue']);
							elseif($fArr['smsKey'] == 'identified_date' && $fArr['smsValue']) $identArr['dateIdentified'] = $fArr['smsValue'];
							elseif($fArr['smsKey'] == 'identification_remarks' && $fArr['smsValue']) $identArr['identificationRemarks'] = $fArr['smsValue'];
							elseif($fArr['smsKey'] == 'identification_references' && $fArr['smsValue']) $identArr['identificationReferences'] = $fArr['smsValue'];
							elseif($fArr['smsKey'] == 'identification_qualifier' && $fArr['smsValue']) $identArr['identificationQualifier'] = $fArr['smsValue'];
						}
					}
				}
				if($assocMedia && isset($assocMedia['url'])) $tableArr['assocMedia'][] = $assocMedia;

				if(isset($tableArr['collection_location']) && $tableArr['collection_location'] && !strpos($tableArr['collection_location'], ' ')){
					$score = 1;
					$this->fateLocationArr[$score]['loc'] = $tableArr['collection_location'];
					$this->fateLocationArr[$score]['date'] = $fateDate;
				}
				elseif($fateDate && $fateLocation && !strpos($fateLocation, ' ')){
					$score = $sampleRank.':'.$fateDate;
					if(strpos($tableName,'fielddata')) $score = 2;
					$this->fateLocationArr[$score]['loc'] = $fateLocation;
					$this->fateLocationArr[$score]['date'] = $fateDate;
				}
				$sampleArr = array_merge($tableArr, $sampleArr);
				if($identArr && isset($identArr['sciname']) && $identArr['sciname']){
					$identArr['taxonRemarks'] = 'Identification source: harvested from NEON API';
					if(!isset($identArr['dateIdentified']) || !$identArr['dateIdentified']){
						if($fateDate) $identArr['dateIdentified'] = $fateDate;
					}
					if(!isset($identArr['identifiedBy']) || !$identArr['identifiedBy']) $identArr['identifiedBy'] = 'undefined';
					if(!isset($identArr['dateIdentified']) || !$identArr['dateIdentified']) $identArr['dateIdentified'] = 's.d.';
					$hash = hash('md5', str_replace(' ', '', $identArr['sciname'].$identArr['identifiedBy'].$identArr['dateIdentified']));
					$sampleArr['identifications'][$hash] = $identArr;
				}
				if($assocTaxa && isset($assocTaxa['verbatimSciname'])){
					$taxaArr = $this->getTaxonArr($assocTaxa['verbatimSciname']);
					if(isset($taxaArr['tidInterpreted'])) $assocTaxa['tid'] = $taxaArr['tidInterpreted'];
					$hash = hash('md5', str_replace(' ', '', $assocTaxa['verbatimSciname'].$assocTaxa['relationship']));
					$sampleArr['associations'][$hash] = $assocTaxa;
				}
			}
		}
		$sampleRank++;
		if(isset($viewArr['parentSampleIdentifiers'][0]['sampleUuid'])){
			//Get parent data
			$url = $this->neonApiBaseUrl.'/samples/view?sampleUuid='.$viewArr['parentSampleIdentifiers'][0]['sampleUuid'].'&apiToken='.$this->neonApiKey;
			$parentViewArr = $this->getNeonApiArr($url);
			$this->processViewArr($sampleArr, $parentViewArr, $sampleRank);
		}
	}

	private function getDarwinCoreArr($sampleArr){
		$dwcArr = array();
		if($sampleArr['samplePK']){
			if($this->setCollectionIdentifier($dwcArr,$sampleArr['sampleClass'])){
				//Get data that was provided within manifest
				$dwcArr['identifiers']['NEON sampleCode (barcode)'] = (isset($sampleArr['sampleCode'])?$sampleArr['sampleCode']:'');
				$dwcArr['identifiers']['NEON sampleID'] = (isset($sampleArr['sampleID'])?$sampleArr['sampleID']:'');
				$dwcArr['identifiers']['NEON sampleUUID'] = (isset($sampleArr['sampleUuid'])?$sampleArr['sampleUuid']:'');
				$dwcArr['identifiers']['NEON sampleID Hash'] = (isset($sampleArr['hashedSampleID'])?$sampleArr['hashedSampleID']:'');
				if(isset($sampleArr['event_id'])) $dwcArr['eventID'] = $sampleArr['event_id'];
				if(isset($sampleArr['specimen_count'])) $dwcArr['individualCount'] = $sampleArr['specimen_count'];
				elseif(isset($sampleArr['individualCount'])) $dwcArr['individualCount'] = $sampleArr['individualCount'];
				if(isset($sampleArr['reproductive_condition'])) $dwcArr['reproductiveCondition'] = $sampleArr['reproductive_condition'];
				if(isset($sampleArr['sex'])) $dwcArr['sex'] = $sampleArr['sex'];
				if(isset($sampleArr['life_stage'])) $dwcArr['lifeStage'] = $sampleArr['life_stage'];
				if(isset($sampleArr['associated_taxa'])) $dwcArr['associatedTaxa'] = $this->translateAssociatedTaxa($sampleArr['associated_taxa']);
				$occurRemarks = array();
				if(isset($sampleArr['remarks'])) $occurRemarks[] = $sampleArr['remarks'];
				if(isset($sampleArr['sample_type'])){
					$sampleType = $sampleArr['sample_type'];
					if($sampleType == 'M') $sampleType = 'mineral';
					elseif($sampleType == 'O') $sampleType = 'organic';
					$occurRemarks[] = 'sample type: '.$sampleType;
				}
				if($occurRemarks) $dwcArr['occurrenceRemarks'] = implode('; ',$occurRemarks);
				if(isset($sampleArr['assocMedia'])) $dwcArr['assocMedia'] = $sampleArr['assocMedia'];
				if(isset($sampleArr['coordinate_uncertainty']) && $sampleArr['coordinate_uncertainty']) $dwcArr['coordinateUncertaintyInMeters'] = $sampleArr['coordinate_uncertainty'];
				if(isset($sampleArr['decimal_latitude'])) $dwcArr['decimalLatitude'] = $sampleArr['decimal_latitude'];
				if(isset($sampleArr['decimal_longitude'])) $dwcArr['decimalLongitude'] = $sampleArr['decimal_longitude'];
				if(isset($sampleArr['elevation'])){
					if(isset($sampleArr['elevation_uncertainty'])){
						$dwcArr['minimumElevationInMeters'] = round($sampleArr['elevation']-$sampleArr['elevation_uncertainty']);
						$dwcArr['maximumElevationInMeters'] = round($sampleArr['elevation']+$sampleArr['elevation_uncertainty']);
						$dwcArr['verbatimElevation'] = $sampleArr['elevation'].'m (+-'.$sampleArr['elevation_uncertainty'].'m)';
					}
					else{
						$dwcArr['minimumElevationInMeters'] = $sampleArr['elevation'];
						$dwcArr['maximumElevationInMeters'] = $sampleArr['elevation'];
					}
				}
				$prepArr = array();
				if(isset($sampleArr['preservative_type'])) $prepArr[] = 'preservative type: '.$sampleArr['preservative_type'];
				if(isset($sampleArr['preservative_volume'])) $prepArr[] = 'preservative volume: '.$sampleArr['preservative_volume'];
				if(isset($sampleArr['preservative_concentration'])) $prepArr[] = 'preservative concentration: '.$sampleArr['preservative_concentration'];
				if(!in_array($dwcArr['collid'], array(19,28,42))){
					if(isset($sampleArr['sample_mass']) && strpos($sampleArr['symbiotaTarget'],'sample mass') === false) $prepArr[] = 'sample mass: '.$sampleArr['sample_mass'];
					if(isset($sampleArr['sample_volume']) && strpos($sampleArr['symbiotaTarget'],'sample volume') === false) $prepArr[] = 'sample volume: '.$sampleArr['sample_volume'];
				}
				if($prepArr) $dwcArr['preparations'] = implode(', ',$prepArr);
				$dynProp = array();
				if(isset($sampleArr['filterVolume'])) $dynProp[] = 'filterVolume: '.$sampleArr['filterVolume'];
				if(isset($sampleArr['temperature'])) $dynProp[] = 'temperature: '.$sampleArr['temperature'];
				if(isset($sampleArr['minimum_depth_in_meters'])) $dynProp[] = 'minimum depth: '.$sampleArr['minimum_depth_in_meters'].'m ';
				if(isset($sampleArr['maximum_depth_in_meters'])) $dynProp[] = 'maximum depth: '.$sampleArr['maximum_depth_in_meters'].'m ';
				if(isset($sampleArr['verbatim_depth'])) $dynProp[] = 'verbatim depth: '.$sampleArr['verbatim_depth'];
				//if(isset($sampleArr['sample_condition'])) $dynProp[] = 'sample condition: '.$sampleArr['sample_condition'];
				if($dynProp) $dwcArr['dynamicProperties'] = implode(', ',$dynProp);

				if(isset($sampleArr['collected_by']) && $sampleArr['collected_by']) $dwcArr['recordedBy'] = $this->translatePersonnelArr($sampleArr['collected_by']);
				if(isset($sampleArr['collect_end_date']) && $sampleArr['collect_end_date']){
					if(isset($sampleArr['collect_start_date']) && $sampleArr['collect_start_date'] != $sampleArr['collect_end_date']){
						$dwcArr['eventDate'] = $sampleArr['collect_start_date'];
						$dwcArr['eventDate2'] = $sampleArr['collect_end_date'];
					}
					else $dwcArr['eventDate'] = $sampleArr['collect_end_date'];
				}
				elseif(isset($sampleArr['collectDate']) && $sampleArr['collectDate'] && $sampleArr['collectDate'] != '0000-00-00') $dwcArr['eventDate'] = $sampleArr['collectDate'];
				elseif($sampleArr['sampleID']){
					if(preg_match('/\.(20\d{2})(\d{2})(\d{2})\./',$sampleArr['sampleID'],$m)){
						//Get date from sampleID
						$dwcArr['eventDate'] = $m[1].'-'.$m[2].'-'.$m[3];
					}
				}
				//Build proper location code
				$locationStr = '';
				if(isset($sampleArr['fate_location']) && $sampleArr['fate_location']) $locationStr = $sampleArr['fate_location'];
				elseif($sampleArr['namedLocation']) $locationStr = $sampleArr['namedLocation'];
				if($locationStr){
					if($this->setNeonLocationData($dwcArr, $locationStr)){
						if(isset($dwcArr['domainID'])){
							$locStr = $this->domainSiteArr[$dwcArr['domainID']].' ('.$dwcArr['domainID'].'), ';
							if(isset($dwcArr['siteID'])) $locStr .= $this->domainSiteArr[$dwcArr['siteID']].' ('.$dwcArr['siteID'].'), ';
							if(isset($dwcArr['locality'])) $locStr .= $dwcArr['locality'];
							$dwcArr['locality'] = trim($locStr,', ');
						}
						if(isset($dwcArr['plotDim'])){
							$dwcArr['locality'] .= $dwcArr['plotDim'];
							unset($dwcArr['plotDim']);
						}
						$dwcArr['locationID'] = $locationStr;
					}
					else{
						$dwcArr['locality'] = $sampleArr['namedLocation'];
						$this->errorStr = 'locality data failed to populate';
						$this->setSampleErrorMessage($sampleArr['samplePK'], $this->errorStr);
						//return false;
					}
					if(isset($sampleArr['collection_location']) && $sampleArr['collection_location']){
						if(!isset($dwcArr['locationID']) || $dwcArr['locationID'] != $sampleArr['collection_location']) $dwcArr['locality'] .= ', '.trim($sampleArr['collection_location'],' ,;.');
					}
					if(isset($dwcArr['locality']) && $dwcArr['locality']) $dwcArr['locality'] = trim($dwcArr['locality'],' ,;.');
				}

				//Taxonomic fields
				$skipTaxonomy = array(5,6,10,13,16,21,23,31,41,42,45,58,60,61,62,67,68,69,76,92);
				if(!in_array($dwcArr['collid'],$skipTaxonomy)){
					$identArr = array();
					if(isset($sampleArr['identifications'])){
						$identArr = $sampleArr['identifications'];
					}
					if($sampleArr['taxonID']){
						$hash = hash('md5', str_replace(' ','',$sampleArr['taxonID'].'manifests.d.'));
						$identArr[$hash] = array('sciname' => $sampleArr['taxonID'], 'identifiedBy' => 'manifest', 'dateIdentified' => 's.d.', 'taxonRemarks' => 'Identification source: inferred from shipment manifest');
					}
					if(!$identArr){
						//Identifications not supplied via API nor manifest, thus try to grab from sampleID
						$taxonCode = '';
						$taxonRemarks = '';
						if($dwcArr['collid'] == 30) $taxonCode = 'Soil';
						elseif($dwcArr['collid'] == 56){
							if(preg_match('/\.\d{4}\.\d{1,2}\.([A-Z]{2,15}\d{0,2})\./', $sampleArr['sampleID'], $m)){
								$taxonCode = $m[1];
								$taxonRemarks = 'Identification source: parsed from NEON sampleID';
							}
						}
						elseif(!in_array($dwcArr['collid'], array(22,50,57))){
							if(preg_match('/\.\d{8}\.([A-Z]{2,15}\d{0,2})\./',$sampleArr['sampleID'], $m)){
								$taxonCode = $m[1];
								$taxonRemarks = 'Identification source: parsed from NEON sampleID';
							}
						}
						if($taxonCode){
							$hash = hash('md5', str_replace(' ','',$taxonCode.'sampleIDs.d.'));
							$identArr[$hash] = array('sciname' => $taxonCode, 'identifiedBy' => 'sampleID', 'dateIdentified' => 's.d.', 'taxonRemarks' => $taxonRemarks);
						}
					}
					if($identArr){
						$isCurrentKey = true;
						foreach($this->currentDetArr as $detObj){
							if($detObj['isCurrent'] && $detObj['enteredByUid'] && $detObj['enteredByUid'] != 50){
								//There is a current determination that needs to be maintained as the central current determination
								$isCurrentKey = false;
								break;
							}
						}
						$bestDate = 0;
						foreach($identArr as $idKey => &$idArr){
							if(!isset($idArr['sciname'])) unset($identArr[$idKey]);
							//Translate NEON taxon codes or check/clean scientific name submitted
							if(preg_match('/^[A-Z0-9]+$/', $idArr['sciname'])){
								//Taxon is a NEON code that needs to be translated
								if($taxaArr = $this->translateTaxonCode($idArr['sciname'])){
									$idArr = array_merge($idArr, $taxaArr);
								}
							}
							else{
								if($taxaArr = $this->getTaxonArr($idArr['sciname'])){
									if(isset($idArr['scientificNameAuthorship']) && $idArr['scientificNameAuthorship']) unset($taxaArr['scientificNameAuthorship']);
									$idArr = array_merge($idArr, $taxaArr);
								}
							}
							if($isCurrentKey !== false){
								//Evaluate if any incoming determinations should be tagged as isCurrent
								if($isCurrentKey === true) $isCurrentKey = $idKey;
								if(isset($idArr['dateIdentified']) && preg_match('/^\d{4}/', $idArr['dateIdentified']) && $idArr['dateIdentified'] > $bestDate){
									$bestDate = $idArr['dateIdentified'];
									$isCurrentKey = $idKey;
								}
							}
						}
						if(!is_bool($isCurrentKey)) $identArr[$isCurrentKey]['isCurrent'] = 1;
						//Check to see if any determination need to be projected
						$appendIdentArr = array();
						foreach($identArr as $idKey => &$idArr){
							if(!empty($idArr['taxonPublished'])){
								if($idArr['taxonPublished'] != $idArr['taxon']){
									$idArrClone = $idArr;
									$idArrClone['sciname'] = $idArr['taxonPublished'];
									unset($idArrClone['scientificNameAuthorship']);
									unset($idArrClone['family']);
									if(preg_match('/^[A-Z0-9]+$/', $idArrClone['sciname'])){
										//Taxon is a NEON code that needs to be translated
										if($taxaArr = $this->translateTaxonCode($idArrClone['sciname'])){
											$idArrClone = array_merge($idArrClone, $taxaArr);
										}
									}
									else{
										if($taxaArr = $this->getTaxonArr($idArrClone['sciname'])){
											$idArrClone = array_merge($idArrClone, $taxaArr);
										}
									}
									$appendIdentArr[] = $idArrClone;
									$idArr['securityStatus'] = 1;
									$idArr['securityStatusReason'] = 'Locked - NEON redaction list';
								}
							}
						}
						if($appendIdentArr) $identArr = array_merge($identArr, $appendIdentArr);
						$dwcArr['identifications'] = $identArr;
					}
				}
				// Occurrence associations
				if(isset($sampleArr['associations'])){
					$dwcArr['associations'] = $sampleArr['associations'];
				}
				//Add DwC fields that were imported as part of the manifest file
				if($sampleArr['symbiotaTarget']){
					if($symbArr = json_decode($sampleArr['symbiotaTarget'],true)){
						foreach($symbArr as $symbField => $symbValue){
							if($symbValue !== '' && !isset($dwcArr[$symbField])) $dwcArr[$symbField] = $symbValue;
						}
					}
				}
			}
			else{
				$this->errorStr = 'ERROR: unable to retrieve collid using sampleClass: '.$sampleArr['sampleClass'];
				$this->setSampleErrorMessage($sampleArr['samplePK'], 'unable to retrieve collid using sampleClass');
				return false;
			}
		}
		if(isset($dwcArr['eventDate'])) $dwcArr['eventDate'] = $this->formatDate($dwcArr['eventDate']);
		if(isset($dwcArr['eventDate2'])){
			$dwcArr['eventDate2'] = $this->formatDate($dwcArr['eventDate2']);
			if($dwcArr['eventDate'] == $dwcArr['eventDate2']) unset($dwcArr['eventDate2']);
		}
		$this->applyCustomAdjustments($dwcArr);
		return $dwcArr;
	}

	private function updateSampleID($newSampleID, $oldSampleID, $samplePK, $occid){
		$status = true;
		$sql = 'UPDATE NeonSample SET sampleID = "'.$newSampleID.'", alternativeSampleID = CONCAT_WS(", ",alternativeSampleID,"'.$oldSampleID.'") WHERE samplePK = '.$samplePK;
		if(!$this->conn->query($sql)){
			$status = false;
		}
		if($occid){
			$sql = 'UPDATE omoccuridentifiers SET identifierValue = "'.$newSampleID.'" WHERE identifiername = "NEON sampleID" AND occid = '.$occid;
			if(!$this->conn->query($sql)){
				$status = false;
			}
		}
		return $status;
	}

	private function setCollectionIdentifier(&$dwcArr,$sampleClass){
		$status = false;
		if($sampleClass){
			$sql = 'SELECT collid, datasetName FROM omcollections
				WHERE (datasetID = "'.$sampleClass.'") OR (datasetID LIKE "%,'.$sampleClass.',%") OR (datasetID LIKE "'.$sampleClass.',%") OR (datasetID LIKE "%,'.$sampleClass.'")';
			$rs = $this->conn->query($sql);
			if($rs->num_rows == 1){
				$r = $rs->fetch_object();
				$this->activeCollid = $r->collid;
				$dwcArr['collid'] = $r->collid;
				if($r->datasetName) $dwcArr['verbatimAttributes'] = $r->datasetName;
				else $dwcArr['verbatimAttributes'] = $sampleClass;
				$status = true;
			}
			$rs->free();
		}
		return $status;
	}

	private function setNeonLocationData(&$dwcArr, $locationName){
		$url = $this->neonApiBaseUrl.'/locations/'.urlencode($locationName).'?apiToken='.$this->neonApiKey;
		//echo 'loc url: '.$url.'<br/>';
		$resultArr = $this->getNeonApiArr($url);
		if(!$resultArr) return false;
		if(isset($resultArr['locationType']) && $resultArr['locationType']){
			if($resultArr['locationType'] == 'SITE') $dwcArr['siteID'] = $resultArr['locationName'];
			elseif($resultArr['locationType'] == 'DOMAIN') $dwcArr['domainID'] = $resultArr['locationName'];
		}
		if(isset($resultArr['locationDescription']) && $resultArr['locationDescription']){
			$parStr = str_replace(array('"',', RELOCATABLE',', CORE','Parent'),'',$resultArr['locationDescription']);
			$parStr = str_replace('re - Reach','Reach',$parStr);
			$parStr = preg_replace('/ at site [A-Z]+/', '', $parStr);
			$parStr = trim($parStr,' ,;');
			if($parStr){
				if($resultArr['locationType'] != 'SITE' && $resultArr['locationType'] != 'DOMAIN'){
					$localityStr = '';
					if(isset($dwcArr['locality'])) $localityStr = $dwcArr['locality'];
					$dwcArr['locality'] = $parStr.', '.$localityStr;
				}
			}
		}

		if(!isset($dwcArr['decimalLatitude']) && isset($resultArr['locationDecimalLatitude']) && $resultArr['locationDecimalLatitude']){
			$dwcArr['decimalLatitude'] = $resultArr['locationDecimalLatitude'];
		}
		if(!isset($dwcArr['decimalLongitude']) && isset($resultArr['locationDecimalLongitude']) && $resultArr['locationDecimalLongitude']){
			$dwcArr['decimalLongitude'] = $resultArr['locationDecimalLongitude'];
		}
		if(!isset($dwcArr['verbatimCoordinates']) && isset($resultArr['locationUtmEasting']) && $resultArr['locationUtmEasting']){
			$dwcArr['verbatimCoordinates'] = trim($resultArr['locationUtmZone'].$resultArr['locationUtmHemisphere'].' '.$resultArr['locationUtmEasting'].'E '.$resultArr['locationUtmNorthing'].'N');
		}
		$elevMin = '';
		$elevMax = '';
		$elevUncertainty = '';
		if(isset($resultArr['locationElevation']) && $resultArr['locationElevation']){
			$elevMin = round($resultArr['locationElevation']);
		}
		if(isset($resultArr['elevation_uncertainty']) && $resultArr['elevation_uncertainty']){
			$elevUncertainty = $resultArr['elevation_uncertainty'];
		}
		$locPropArr = $resultArr['locationProperties'];
		if($locPropArr){
			$habitatArr = array();
			foreach($locPropArr as $propArr){
				if(!isset($dwcArr['georeferenceSources']) && $propArr['locationPropertyName'] == 'Value for Coordinate source'){
					$dwcArr['georeferenceSources'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['coordinateUncertaintyInMeters']) && $propArr['locationPropertyName'] == 'Value for Coordinate uncertainty'){
					$dwcArr['coordinateUncertaintyInMeters'] = $propArr['locationPropertyValue'];
				}
				elseif($propArr['locationPropertyName'] == 'Value for Minimum elevation'){
					$elevMin = round($propArr['locationPropertyValue']);
				}
				elseif($propArr['locationPropertyName'] == 'Value for Maximum elevation'){
					$elevMax = round($propArr['locationPropertyValue']);
				}
				elseif(!isset($dwcArr['country']) && $propArr['locationPropertyName'] == 'Value for Country'){
					$countryValue = $propArr['locationPropertyValue'];
					if($countryValue == 'unitedStates') $countryValue = 'United States';
					elseif($countryValue == 'USA') $countryValue = 'United States';
					$dwcArr['country'] = $countryValue;
				}
				elseif(!isset($dwcArr['county']) && $propArr['locationPropertyName'] == 'Value for County'){
					$dwcArr['county'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['geodeticDatum']) && $propArr['locationPropertyName'] == 'Value for Geodetic datum'){
					$dwcArr['geodeticDatum'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['plotDim']) && $propArr['locationPropertyName'] == 'Value for Plot dimensions'){
					$dwcArr['plotDim'] = ' (plot dimensions: '.$propArr['locationPropertyValue'].')';
				}
				elseif(!isset($habitatArr['landcover']) && strpos($propArr['locationPropertyName'],'Value for National Land Cover Database') !== false){
					$habitatArr['landcover'] = $propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['aspect']) && $propArr['locationPropertyName'] == 'Value for Slope aspect'){
					$habitatArr['aspect'] = 'slope aspect: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['gradient']) && $propArr['locationPropertyName'] == 'Value for Slope gradient'){
					$habitatArr['gradient'] = 'slope gradient: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($habitatArr['soil']) && $propArr['locationPropertyName'] == 'Value for Soil type order'){
					if($dwcArr['collid'] == 30 && !isset($dwcArr['identifications'])){
						$dwcArr['identifications'][] = array('sciname' => $propArr['locationPropertyValue'], 'identifiedBy' => 'NEON Lab', 'dateIdentified' => 's.d.', 'isCurrent' => 1);
					}
					$habitatArr['soil'] = 'soil type order: '.$propArr['locationPropertyValue'];
				}
				elseif(!isset($dwcArr['stateProvince']) && $propArr['locationPropertyName'] == 'Value for State province'){
					$stateStr = $propArr['locationPropertyValue'];
					if(array_key_exists($stateStr, $this->stateArr)) $stateStr = $this->stateArr[$stateStr];
					$this->setTimezone($stateStr);
					$dwcArr['stateProvince'] = $stateStr;
				}
			}
			if($habitatArr) $dwcArr['habitat'] = implode('; ',$habitatArr);
		}
		if($elevMin && !isset($dwcArr['minimumElevationInMeters'])) $dwcArr['minimumElevationInMeters'] = $elevMin;
		if($elevMax && $elevMax != $elevMin && !isset($dwcArr['maximumElevationInMeters'])) $dwcArr['maximumElevationInMeters'] = $elevMax;
		if($elevUncertainty) $dwcArr['verbatimElevation'] = trim($elevMin.' - '.$elevMax,' -').' ('.$elevUncertainty.')';

		if(isset($resultArr['locationParent']) && $resultArr['locationParent']){
			if($resultArr['locationParent'] != 'REALM'){
				$this->setNeonLocationData($dwcArr, $resultArr['locationParent']);
			}
		}
		return true;
	}

	private function applyCustomAdjustments(&$dwcArr){
		if($dwcArr['collid'] == 75){
			//Tick pathogen extracts
			$dwcArr['individualCount'] = 1;
			$dwcArr['preparations'] = '-80 degrees C.';
			$dwcArr['lifeStage'] = 'Nymph';
			$dwcArr['sex'] = '';
		}
		elseif($dwcArr['collid'] == 29 || $dwcArr['collid'] == 39){
			$dwcArr['individualCount'] = 1;
		}
	}

	private function loadOccurrenceRecord($dwcArr, $samplePK, $occid){
		if($dwcArr){
			$domainID = (isset($dwcArr['domainID'])?$dwcArr['domainID']:0);
			$siteID = (isset($dwcArr['siteID'])?$dwcArr['siteID']:0);
			unset($dwcArr['domainID']);
			unset($dwcArr['siteID']);
			if(!isset($dwcArr['identifications'])){
				$sciname = '';
				if($dwcArr['collid'] == 5 || $dwcArr['collid'] == 67){
					$sciname = 'Benthic Microbe';
				}
				elseif($dwcArr['collid'] == 6 || $dwcArr['collid'] == 68){
					$sciname = 'Surface Water Microbe';
				}
				elseif($dwcArr['collid'] == 31 || $dwcArr['collid'] == 69){
					$sciname = 'Soil Microbe';
				}
				elseif($dwcArr['collid'] == 41){
					$sciname = 'Dry Deposition';
				}
				elseif($dwcArr['collid'] == 42){
					$sciname = 'Wet Deposition';
				}
				elseif($dwcArr['collid'] == 92){
					$sciname = 'Aquatic Sediments';
				}
				if($sciname){
					$idDate = 's.d.';
					if(!empty($dwcArr['eventDate'])) $idDate = $dwcArr['eventDate'];
					$dwcArr['identifications'][] = array('sciname' => $sciname, 'identifiedBy' => 'NEON Lab', 'dateIdentified' => $idDate, 'isCurrent' => 1);
				}
			}
			$numericFieldArr = array('collid','decimalLatitude','decimalLongitude','minimumElevationInMeters','maximumElevationInMeters');
			$sql = '';
			$skipFieldArr = array('occid','collid','identifiers','assocmedia','identifications','associations');
			if($occid){
				if($this->replaceFieldValues){
					//Only replace values that have not yet been explicitly modified
					$skipFieldArr = array_merge($skipFieldArr, $this->getOccurrenceEdits($occid));
				}
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array(strtolower($fieldName), $skipFieldArr)) continue;
					if($this->replaceFieldValues){
						if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
							$sql .= ', '.$fieldName.' = '.$this->cleanInStr($fieldValue).' ';
						}
						else{
							$sql .= ', '.$fieldName.' = "'.$this->cleanInStr($fieldValue).'" ';
						}
						if(array_key_exists($fieldName, $this->currentOccurArr) && $this->currentOccurArr[$fieldName] != $fieldValue){
							$this->versionEdit($occid, $fieldName, $this->currentOccurArr[$fieldName], $fieldValue);
						}
					}
					else{
						if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
							$sql .= ', '.$fieldName.' = IFNULL('.$fieldName.','.$this->cleanInStr($fieldValue).') ';
						}
						else{
							$sql .= ', '.$fieldName.' = IFNULL('.$fieldName.',"'.$this->cleanInStr($fieldValue).'") ';
						}
					}
				}
				if($sql) $sql = 'UPDATE omoccurrences SET '.substr($sql, 1).' WHERE (occid = '.$occid.')';
			}
			else{
				$sql1 = ''; $sql2 = '';
				foreach($dwcArr as $fieldName => $fieldValue){
					if(in_array(strtolower($fieldName),$skipFieldArr)) continue;
					$fieldValue = $this->cleanInStr($fieldValue);
					if(in_array($fieldName, $numericFieldArr) && is_numeric($fieldValue)){
						$sql1 .= $fieldName.',';
						$sql2 .= $fieldValue.',';
					}
					else{
						if($fieldValue){
							$sql1 .= $fieldName.',';
							$sql2 .= '"'.trim($fieldValue,',; ').'",';
						}
					}
				}
				$sql = 'INSERT INTO omoccurrences(collid,'.$sql1.'dateentered) VALUES('.$dwcArr['collid'].','.$sql2.'NOW())';
			}
			if($sql){
				if($this->conn->query($sql)){
					if(!$occid){
						$occid = $this->conn->insert_id;
						if($occid) $this->conn->query('UPDATE NeonSample SET occid = '.$occid.', occidOriginal = IFNULL(occidOriginal,'.$occid.') WHERE (occid IS NULL) AND (samplePK = '.$samplePK.')');
					}
					$this->conn->query('UPDATE NeonSample SET harvestTimestamp = now() WHERE (samplePK = '.$samplePK.')');
					if(isset($dwcArr['identifiers'])) $this->setOccurrenceIdentifiers($dwcArr['identifiers'], $occid);
					if(isset($dwcArr['assocMedia'])) $this->setAssociatedMedia($dwcArr['assocMedia'], $occid);
					if(isset($dwcArr['identifications'])) $this->setIdentifications($occid, $dwcArr['identifications']);
					if(isset($dwcArr['associations'])) $this->setAssociations($occid, $dwcArr['associations']);
					$this->setDatasetIndexing($domainID,$occid);
					$this->setDatasetIndexing($siteID,$occid);
				}
				else{
					$this->errorStr = 'ERROR updating/creating new occurrence record: '.$this->conn->error;
					return false;
				}
			}
		}
		return $occid;
	}

	private function getOccurrenceEdits($occid){
		$retArr = array();
		$sql = 'SELECT DISTINCT fieldname FROM omoccuredits WHERE (uid != 50 OR appliedstatus = 0) AND occid = '.$occid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = strtolower($r->fieldname);
		}
		$rs->free();
		//Include identification edits
		$sql = 'SELECT sciname, identifiedBy, dateIdentified FROM omoccurdeterminations WHERE (enteredByUid IS NULL OR enteredByUid != 50) AND occid = '.$occid;
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$retArr[] = 'sciname';
			$retArr[] = 'scientificnameauthorship';
			$retArr[] = 'identifiedby';
			$retArr[] = 'dateidentified';
			$retArr[] = 'taxonremarks';
			$retArr[] = 'identificationremarks';
		}
		$rs->free();
		return $retArr;
	}

	private function versionEdit($occid, $fieldName, $oldValue, $newValue){
		if(strtolower(trim($oldValue)) != strtolower(trim($newValue)) && $fieldName != 'coordinateUncertaintyInMeters'){
			$sql = 'INSERT INTO omoccuredits(occid, fieldName, fieldValueOld, fieldValueNew, appliedStatus, uid)
				VALUES('.$occid.',"'.$fieldName.'","'.$this->cleanInStr($oldValue).'","'.$this->cleanInStr($newValue).'", 1, 50)';
			if(!$this->conn->query($sql)){
				$this->errorStr = 'ERROR versioning edit: '.$this->conn->error;
				echo $this->errorStr;
			}
		}
	}

	private function setOccurrenceIdentifiers($idArr, $occid){
		if($idArr && $occid){
			//Do not reset identifiers that were explicitly edited by someone
			$sql = 'SELECT fieldValueOld, fieldValueNew FROM omoccuredits WHERE fieldname IN("omoccuridentifier","omoccuridentifiers") AND uid != 50 AND occid = '.$occid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				if($p = strpos($r->fieldValueOld, ': ')) unset($idArr[substr($r->fieldValueOld, 0, $p)]);
				if($p = strpos($r->fieldValueNew, ': ')) unset($idArr[substr($r->fieldValueNew, 0, $p)]);
			}
			$rs->free();
			//Reset identifiers
			$delSql = 'DELETE FROM omoccuridentifiers WHERE identifiername IN("'.implode('","',array_keys($idArr)).'") AND (occid = '.$occid.')';
			$this->conn->query($delSql);
			foreach($idArr as $idName => $idValue){
				if($idValue){
					$sortBy = 'NULL';
					if($idName=='NEON sampleID') $sortBy = 5;
					elseif($idName=='NEON sampleID Hash') $sortBy = 10;
					elseif($idName=='NEON sampleCode (barcode)') $sortBy = 15;
					elseif($idName=='NEON sampleUUID') $sortBy = 20;
					$sql = 'INSERT INTO omoccuridentifiers(occid, identifiername, identifierValue, sortBy)
						VALUES('.$occid.',"'.$this->cleanInStr($idName).'","'.$this->cleanInStr($idValue).'",'.$sortBy.')';
					if(!$this->conn->query($sql)){
						//$this->errorStr = 'ERROR loading occurrence identifiers: '.$this->conn->error;
					}
				}
			}
		}
	}

	private function setAssociatedMedia($assocMedia, $occid){
		if($assocMedia && $occid){
			foreach($assocMedia as $mediaArr){
				$loadMedia = true;
				$sqlTest = 'SELECT url, originalUrl FROM images WHERE occid = '.$occid;
				$rsTest = $this->conn->query($sqlTest);
				while($rTest = $rsTest->fetch_object()){
					if($rTest->originalUrl == $mediaArr['url'] || $rTest->url == $mediaArr['url']){
						$loadMedia = false;
						break;
					}
				}
				$rsTest->free();
				if($loadMedia){
					$sql = 'INSERT INTO images(occid, originalUrl, photographer) VALUES('.$occid.',"'.$mediaArr['url'].'",'.(isset($mediaArr['photographer'])?'"'.$mediaArr['photographer'].'"':'NULL').')';
					if(!$this->conn->query($sql)){
						//$this->errorStr = 'ERROR loading associatedMedia: '.$this->conn->error;
					}
				}
			}
		}
	}

	private function setIdentifications($occid, $identArr){
		if($occid){
			//Remove invalid identifications
			foreach($identArr as $k => $v){
				if(!isset($v['sciname'])) unset($identArr[$k]);
				elseif($v['identifiedBy'] == 'undefined' && $v['dateIdentified'] == 's.d.' && count($identArr) > 1){
					//unset($identArr[$k]);
				}
			}
			//Remove old annotations entered by the occurrence harvester that are not present within new harvest
			$oldID = '';
			$newID = '';
			if($this->currentDetArr){
				$incommingIsCurrentExists = false;
				foreach($this->currentDetArr as $detID => $cdArr){
					$deleteDet = true;
					if($cdArr['enteredByUid'] && $cdArr['enteredByUid'] != 50){
						$deleteDet = false;
					}
					if($deleteDet){
						foreach($identArr as $idKey => $idArr){
							if($cdArr['sciname'] == $idArr['sciname'] && $cdArr['identifiedBy'] == $idArr['identifiedBy'] && $cdArr['dateIdentified'] == $idArr['dateIdentified']){
								$identArr[$idKey]['updateDetID'] = $detID;
								$deleteDet = false;
							}
							if(!empty($idArr['isCurrent'])) $incommingIsCurrentExists = true;
						}
					}
					if($deleteDet) $this->deleteDetermination($detID);
					else{
						if(!empty($cdArr['isCurrent']) && $incommingIsCurrentExists) $this->updateDetermination(array('updateDetID' => $detID, 'isCurrent' => 0));
					}
					if($cdArr['isCurrent']){
						if(!$oldID || !empty($cdArr['securityStatus'])) $oldID = $cdArr['sciname'];
					}
				}
			}
			//Check old IDs against new IDs and unset existing isCurrent determinations
			foreach($identArr as $idArr){
				if(!$oldID){
					if($idArr['identifiedBy'] == 'manifest' || $idArr['identifiedBy'] == 'sampleID') $oldID = $idArr['sciname'];
				}
				if(!empty($idArr['isCurrent'])){
					if(!$newID || !empty($cdArr['securityStatus'])) $newID = $idArr['sciname'];
				}
			}
			if($oldID && $newID && $oldID != $newID){
				$this->setSampleErrorMessage('occid:'.$occid, 'Curatorial Check: possible ID conflict');
			}
			foreach($identArr as $idArr){
				if(($idArr['identifiedBy'] != 'manifest' && $idArr['identifiedBy'] != 'sampleID') || (isset($idArr['isCurrent']) && $idArr['isCurrent'])){
					if(empty($idArr['updateDetID'])) $this->insertDetermination($occid, $idArr);
					else $this->updateDetermination($idArr);
					//Following code needed until omoccurdeterminations is activated as central determination source
					if(isset($idArr['isCurrent']) && $idArr['isCurrent'] && (!isset($idArr['securityStatus']) || !$idArr['securityStatus'])){
						$this->updateOccurrence($occid, $idArr);
					}
				}
			}
		}
	}

	private function insertDetermination($occid, $idArr){
		$status = true;
		$scientificName = $idArr['sciname'];
		$tidInterpreted = null;
		if(isset($idArr['tidInterpreted']) && $idArr['tidInterpreted']) $tidInterpreted = $idArr['tidInterpreted'];
		$identifiedBy = $idArr['identifiedBy'];
		$dateIdentified = 's.d.';
		if(isset($idArr['dateIdentified']) && $idArr['dateIdentified']) $dateIdentified = $idArr['dateIdentified'];
		$scientificNameAuthorship = null;
		if(isset($idArr['scientificNameAuthorship']) && $idArr['scientificNameAuthorship']) $scientificNameAuthorship = $idArr['scientificNameAuthorship'];
		$family = null;
		if(isset($idArr['family']) && $idArr['family']) $family = $idArr['family'];
		$taxonRemarks = null;
		if(isset($idArr['taxonRemarks']) && $idArr['taxonRemarks']) $taxonRemarks = $idArr['taxonRemarks'];
		$identificationRemarks = null;
		if(isset($idArr['identificationRemarks']) && $idArr['identificationRemarks']) $identificationRemarks = $idArr['identificationRemarks'];
		$identificationReferences = null;
		if(isset($idArr['identificationReferences']) && $idArr['identificationReferences']) $identificationReferences = $idArr['identificationReferences'];
		$identificationQualifier = null;
		if(isset($idArr['identificationQualifier']) && $idArr['identificationQualifier']) $identificationQualifier = $idArr['identificationQualifier'];
		$securityStatus = 0;
		if(isset($idArr['securityStatus']) && $idArr['securityStatus']) $securityStatus = 1;
		$securityStatusReason = null;
		if(isset($idArr['securityStatusReason']) && $idArr['securityStatusReason']) $securityStatusReason = $idArr['securityStatusReason'];
		$isCurrent = 0;
		if(isset($idArr['isCurrent']) && $idArr['isCurrent']) $isCurrent = 1;
		$enteredByUid = 50;
		$sql = 'INSERT INTO omoccurdeterminations(occid, sciname, tidInterpreted, identifiedBy, dateIdentified, scientificNameAuthorship, family, taxonRemarks,
			identificationRemarks, identificationReferences, identificationQualifier, securityStatus, securityStatusReason, isCurrent, enteredByUid)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		if($stmt = $this->conn->prepare($sql)) {
			$stmt->bind_param('isissssssssisii', $occid, $scientificName, $tidInterpreted, $identifiedBy, $dateIdentified, $scientificNameAuthorship, $family, $taxonRemarks,
				$identificationRemarks, $identificationReferences, $identificationQualifier, $securityStatus, $securityStatusReason, $isCurrent, $enteredByUid);
			$stmt->execute();
			if($stmt->error){
				echo '<li style="margin-left:30px">ERROR adding identification to omoccurdetermination: '.$stmt->error.'</li>';
				$status = false;
			}
			$stmt->close();
		}
		else{
			echo '<li style="margin-left:30px">ERROR preparing statement for adding identification to omoccurdetermination: '.$this->conn->error.'</li>';
			$status = false;
		}
		return $status;
	}

	private function updateDetermination($idArr){
		$status = true;
		$detID = $idArr['updateDetID'];
		unset($idArr['updateDetID']);
		if($detID){
			$sqlFrag = '';
			$typeStr = '';
			$paramArr = array();
			if(!empty($idArr['sciname'])){
				$sqlFrag .= 'sciname = ?, ';
				$typeStr .= 's';
				$paramArr[] = $idArr['sciname'];
			}
			if(isset($idArr['tidInterpreted'])){
				$sqlFrag .= 'tidInterpreted = ?, ';
				$typeStr .= 'i';
				if($idArr['tidInterpreted']) $paramArr[] = $idArr['tidInterpreted'];
				else $paramArr[] = null;
			}
			if(isset($idArr['identifiedBy'])){
				$sqlFrag .= 'identifiedBy = ?, ';
				$typeStr .= 's';
				$paramArr[] = $idArr['identifiedBy'];
			}
			if(isset($idArr['dateIdentified'])){
				$sqlFrag .= 'dateIdentified = ?, ';
				$typeStr .= 's';
				if($idArr['dateIdentified']) $paramArr[] = $idArr['dateIdentified'];
				else $paramArr[] = 's.d.';
			}
			if(isset($idArr['scientificNameAuthorship'])){
				$sqlFrag .= 'scientificNameAuthorship = ?, ';
				$typeStr .= 's';
				if($idArr['scientificNameAuthorship']) $paramArr[] = $idArr['scientificNameAuthorship'];
				else $paramArr[] = null;
			}
			if(isset($idArr['family'])){
				$sqlFrag .= 'family = ?, ';
				$typeStr .= 's';
				if($idArr['family']) $paramArr[] = $idArr['family'];
				else $paramArr[] = null;
			}
			if(isset($idArr['taxonRemarks'])){
				$sqlFrag .= 'taxonRemarks = ?,';
				$typeStr .= 's';
				if($idArr['taxonRemarks']) $paramArr[] = $idArr['taxonRemarks'];
				else $paramArr[] = null;
			}
			if(isset($idArr['identificationRemarks'])){
				$sqlFrag .= 'identificationRemarks = ?, ';
				$typeStr .= 's';
				if($idArr['identificationRemarks']) $paramArr[] = $idArr['identificationRemarks'];
				else $paramArr[] = null;
			}
			if(isset($idArr['identificationReferences'])){
				$sqlFrag .= 'identificationReferences = ?, ';
				$typeStr .= 's';
				if($idArr['identificationReferences']) $paramArr[] = $idArr['identificationReferences'];
				else $paramArr[] = null;
			}
			if(isset($idArr['identificationQualifier'])){
				$sqlFrag .= 'identificationQualifier = ?, ';
				$typeStr .= 's';
				if($idArr['identificationQualifier']) $paramArr[] = $idArr['identificationQualifier'];
				else $paramArr[] = null;
			}
			if(isset($idArr['securityStatus'])){
				$sqlFrag .= 'securityStatus = ?, ';
				$typeStr .= 'i';
				if($idArr['securityStatus']) $paramArr[] = 1;
				else $paramArr[] = 0;
			}
			if(isset($idArr['securityStatusReason'])){
				$sqlFrag .= 'securityStatusReason = ?, ';
				$typeStr .= 's';
				if($idArr['securityStatusReason']) $paramArr[] = $idArr['securityStatusReason'];
				else $paramArr[] = null;
			}
			if(isset($idArr['isCurrent'])){
				$sqlFrag .= 'isCurrent = ?, ';
				$typeStr .= 'i';
				if($idArr['isCurrent']) $paramArr[] = 1;
				else $paramArr[] = 0;
			}
			if($sqlFrag){
				$typeStr .= 'i';
				$paramArr[] = $detID;
				$sql = 'UPDATE omoccurdeterminations SET '.trim($sqlFrag,', ').' WHERE detID = ?';
				if($stmt = $this->conn->prepare($sql)) {
					$stmt->bind_param($typeStr, ...$paramArr);
					$stmt->execute();
					if($stmt->error){
						echo '<li style="margin-left:30px">ERROR updating identification within omoccurdetermination: '.$stmt->error.'</li>';
						$status = false;
					}
					$stmt->close();
				}
				else{
					echo '<li style="margin-left:30px">ERROR preparing statement for updating within omoccurdetermination: '.$this->conn->error.'</li>';
					$status = false;
				}
			}
		}
		return $status;
	}

	private function updateOccurrence($occid, $idArr){
		$status = true;
		$scientificName = $idArr['sciname'];
		$tidInterpreted = null;
		if(isset($idArr['tidInterpreted']) && $idArr['tidInterpreted']) $tidInterpreted = $idArr['tidInterpreted'];
		$identifiedBy = $idArr['identifiedBy'];
		$dateIdentified = 's.d.';
		if(isset($idArr['dateIdentified']) && $idArr['dateIdentified']) $dateIdentified = $idArr['dateIdentified'];
		$scientificNameAuthorship = null;
		if(isset($idArr['scientificNameAuthorship']) && $idArr['scientificNameAuthorship']) $scientificNameAuthorship = $idArr['scientificNameAuthorship'];
		$family = null;
		if(isset($idArr['family']) && $idArr['family']) $family = $idArr['family'];
		$taxonRemarks = null;
		if(isset($idArr['taxonRemarks']) && $idArr['taxonRemarks']) $taxonRemarks = $idArr['taxonRemarks'];
		$identificationRemarks = null;
		if(isset($idArr['identificationRemarks']) && $idArr['identificationRemarks']) $identificationRemarks = $idArr['identificationRemarks'];
		$identificationReferences = null;
		if(isset($idArr['identificationReferences']) && $idArr['identificationReferences']) $identificationReferences = $idArr['identificationReferences'];
		$identificationQualifier = null;
		if(isset($idArr['identificationQualifier']) && $idArr['identificationQualifier']) $identificationQualifier = $idArr['identificationQualifier'];
		$sql = 'UPDATE omoccurrences
			SET sciname = ?, tidInterpreted = ?, identifiedBy = ?, dateIdentified = ?, scientificNameAuthorship = ?, family = ?, taxonRemarks = ?,
			identificationRemarks = ?, identificationReferences = ?, identificationQualifier = ?
			WHERE occid = ?';
		if($stmt = $this->conn->prepare($sql)) {
			$stmt->bind_param('sissssssssi', $scientificName, $tidInterpreted, $identifiedBy, $dateIdentified, $scientificNameAuthorship, $family, $taxonRemarks,
				$identificationRemarks, $identificationReferences, $identificationQualifier, $occid);
			$stmt->execute();
			if($stmt->error){
				echo '<li style="margin-left:30px">ERROR updating current identification within omoccurrences table: '.$stmt->error.'</li>';
				$status = false;
			}
			$stmt->close();
		}
		else{
			echo '<li style="margin-left:30px">ERROR preparing statement for updating identification within omoccurrences: '.$this->conn->error.'</li>';
			$status = false;
		}
		return $status;
	}

	private function deleteDetermination($detid){
		if(is_numeric($detid)){
			$sql = 'DELETE FROM omoccurdeterminations WHERE detid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $detid);
				$stmt->execute();
				if($stmt->error){
					$this->errorStr = 'ERROR deteling determination (#'.$detid.'):'.$this->conn->error;
				}
				$stmt->close();
			}
		}
	}

	private function setAssociations($occid, $assocArr){
		$status = true;
		foreach($assocArr as $assocUnit){
			$scientificName = $assocUnit['verbatimSciname'];
			$tid = null;
			if(isset($assocUnit['tidInterpreted']) && $assocUnit['tidInterpreted']) $tid = $assocUnit['tidInterpreted'];
			$relationship = $assocUnit['relationship'];
			$createdUid = 50;
			$sql = 'INSERT INTO omoccurassociations(occid, verbatimSciname, tid, relationship, createdUid) VALUES(?, ?, ?, ?, ?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('isisi', $occid, $scientificName, $tid, $relationship, $createdUid);
				$stmt->execute();
				if($stmt->error){
					echo '<li style="margin-left:30px">ERROR inserting occurrence association: '.$stmt->error.'</li>';
					$status = false;
				}
				$stmt->close();
			}
			else{
				echo '<li style="margin-left:30px">ERROR preparing statement for inserting occurrence association: '.$this->conn->error.'</li>';
				$status = false;
			}
		}
		return $status;
	}

	private function setDatasetIndexing($datasetName, $occid){
		if($datasetName && $occid){
			$sql = 'INSERT INTO omoccurdatasetlink(datasetid, occid) SELECT datasetid, '.$occid.' FROM omoccurdatasets WHERE name = "'.$datasetName.'"';
			if(!$this->conn->query($sql)){
				if($this->conn->errno != 1062) $this->errorStr = 'ERROR assigning occurrence to '.$datasetName.' dataset: '.$this->conn->errno.' - '.$this->conn->error;
			}
		}
	}

	private function getNeonApiArr($url){
		$retArr = array();
		//echo 'url: '.$url.'<br/>';
		if($url){
			//Request URL example: https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam
			$json = @file_get_contents($url);
			//echo 'json1: '.$json; exit;

			/*
			//curl -X GET --header 'Accept: application/json' 'https://data.neonscience.org/api/v0/locations/TOOL_073.mammalGrid.mam'
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_PUT, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Accept: application/json') );
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
			$json = curl_exec($curl);
			*/
			if($json){
				$resultArr = json_decode($json,true);
				if(isset($resultArr['data'])){
					$retArr = $resultArr['data'];
				}
				elseif(isset($resultArr['error'])){
					$this->errorStr = 'ERROR thrown accessing NEON API: url ='.$url;
					if(isset($resultArr['error']['status'])) '; '.$this->errorStr .= $resultArr['error']['status'];
					if(isset($resultArr['error']['detail'])) '; '.$this->errorStr .= $resultArr['error']['detail'];
					$retArr = false;
				}
				else{
					$this->errorStr = 'ERROR retrieving NEON data: '.$url;
					$retArr = false;
				}
			}
			else{
				//$this->errorStr = 'ERROR: unable to access NEON API: '.$url;
				$retArr = false;
			}
			//curl_close($curl);
		}
		return $retArr;
	}

	private function translateAssociatedTaxa($inStr){
		$retStr = '';
		$taxaCodeArr = explode('|', $inStr);
		foreach($taxaCodeArr as $strFrag){
			$taxaArr = $this->translateTaxonCode($strFrag);
			if(isset($taxaArr['sciname']) && $taxaArr['sciname']) $retStr .= ', ' . trim($taxaArr['sciname']);
			else $retStr .= ', ' . $strFrag;
		}
		return trim($retStr, ', ');
	}

	private function translateTaxonCode($taxonCode){
		$retArr = array();
		$taxonGroup = $this->getTaxonGroup($this->activeCollid);
		$taxonCode = trim($taxonCode);
		if($taxonCode && $taxonGroup){
			if(!isset($this->taxonCodeArr[$taxonGroup][$taxonCode])){
				$tid = 0;
				$sciname = '';
				$sql = 'SELECT t.tid, n.sciname, n.scientificNameAuthorship, n.family
					FROM neon_taxonomy n LEFT JOIN taxa t ON n.sciname = t.sciname
					WHERE n.taxonGroup = "'.$this->cleanInStr($taxonGroup).'" AND n.taxonCode = "'.$this->cleanInStr($taxonCode).'"';
				if($rs = $this->conn->query($sql)){
					while($r = $rs->fetch_object()){
						$tid = $r->tid;
						$sciname = $r->sciname;
						$this->taxonCodeArr[$taxonGroup][$taxonCode]['tid'] = $tid;
						$this->taxonCodeArr[$taxonGroup][$taxonCode]['sciname'] = $sciname;
						$this->taxonCodeArr[$taxonGroup][$taxonCode]['author'] = $r->scientificNameAuthorship;
						$this->taxonCodeArr[$taxonGroup][$taxonCode]['family'] = $r->family;
					}
					$rs->free();
				}
				else echo 'ERROR populating taxonomy codes: '.$sql;
				if(!$tid && $sciname){
					//Verify name via Catalog of Life and if valid, add to thesaurus
					$harvester = new TaxonomyHarvester();
					$harvester->setKingdomName($this->getKingdomName());
					$harvester->setTaxonomicResources(array('col'));
					if($newTid = $harvester->processSciname($sciname)){
						$this->taxonCodeArr[$taxonGroup][$taxonCode]['tid'] = $newTid;
					}
				}
			}
			if(isset($this->taxonCodeArr[$taxonGroup][$taxonCode])){
				$retArr['tidInterpreted'] = $this->taxonCodeArr[$taxonGroup][$taxonCode]['tid'];
				$retArr['sciname'] = $this->taxonCodeArr[$taxonGroup][$taxonCode]['sciname'];
				$retArr['scientificNameAuthorship'] = $this->taxonCodeArr[$taxonGroup][$taxonCode]['author'];
				$retArr['family'] = $this->taxonCodeArr[$taxonGroup][$taxonCode]['family'];
			}
		}
		return $retArr;
	}

	private function getTaxonGroup($collid){
		$taxonGroup = array( 45 => 'ALGAE', 46 => 'ALGAE', 47 => 'ALGAE', 49 => 'ALGAE', 50 => 'ALGAE', 55 => 'ALGAE', 60 => 'ALGAE', 62 => 'ALGAE', 73 => 'ALGAE',
			11 => 'BEETLE', 13 => 'BEETLE', 14 => 'BEETLE', 16 => 'BEETLE', 39 => 'BEETLE', 44 => 'BEETLE', 63 => 'BEETLE',
			20 => 'FISH', 66 => 'FISH',
			12 => 'HERPETOLOGY', 15 => 'HERPETOLOGY', 70 => 'HERPETOLOGY',
			21 => 'MACROINVERTEBRATE', 22 => 'MACROINVERTEBRATE', 48 => 'MACROINVERTEBRATE', 52 => 'MACROINVERTEBRATE', 53 => 'MACROINVERTEBRATE', 57 => 'MACROINVERTEBRATE', 61 => 'MACROINVERTEBRATE',
			29 => 'MOSQUITO', 56 => 'MOSQUITO', 58 => 'MOSQUITO', 59 => 'MOSQUITO', 65 => 'MOSQUITO',
			7 => 'PLANT', 8 => 'PLANT', 9 => 'PLANT', 10 => 'PLANT', 18 => 'PLANT', 23 => 'PLANT', 40 => 'PLANT', 54 => 'PLANT', 76 => 'PLANT',
			17 => 'SMALL_MAMMAL', 19 => 'SMALL_MAMMAL', 24 => 'SMALL_MAMMAL', 25 => 'SMALL_MAMMAL', 26 => 'SMALL_MAMMAL', 27 => 'SMALL_MAMMAL', 28 => 'SMALL_MAMMAL', 64 => 'SMALL_MAMMAL', 71 => 'SMALL_MAMMAL', 85 => 'SMALL_MAMMAL', 90 => 'SMALL_MAMMAL', 91 => 'SMALL_MAMMAL',
			30 => 'SOIL', 79 => 'SOIL',
			75 => 'TICK'
		);
		if(array_key_exists($collid, $taxonGroup)) return $taxonGroup[$collid];
		return false;
	}

	private function getKingdomName(){
		if(in_array($this->activeCollid, array( 11,12,13,14,15,16,17,19,20,21,22,24,25,26,27,28,29,39,48,52,53,56,57,58,59,61,63,64,65,66,70,71,75 ))) return 'Animalia';
		elseif(in_array($this->activeCollid, array( 7,8,9,10,18,23,40,54,76 ))) return 'Plantae';
		//Let's use Plantae for algae group, which works for now
		elseif(in_array($this->activeCollid, array( 45,46,47,49,50,55,60,62,73 ))) return 'Plantae';
		//soils: 30,79
		//Microbes: 5,6,31,67,68,69
		//environmental: 41,42
		return '';
	}

	private function getTaxonArr($sciname){
		if(substr($sciname, -4) == ' sp.') $sciname = trim(substr($sciname, 0, strlen($sciname) - 4));
		elseif(substr($sciname, -4) == ' spp.') $sciname = trim(substr($sciname, 0, strlen($sciname) - 5));
		$retArr = $this->getTaxon($sciname);
		if(!$retArr){
			//Parse name in case author is inbedded within taxon
			$scinameArr = TaxonomyUtilities::parseScientificName($sciname, $this->conn);
			if(isset($scinameArr['sciname']) && $scinameArr['sciname']){
				$sciname = $scinameArr['sciname'];
				$retArr = $this->getTaxon($sciname);
				if(isset($scinameArr['author']) && $scinameArr['author']) $retArr['scientificNameAuthorship'] = $scinameArr['author'];
			}
			if(!$retArr){
				//Verify name via Catalog of Life and if valid, add to thesaurus
				$harvester = new TaxonomyHarvester();
				$harvester->setKingdomName($this->getKingdomName());
				$harvester->setTaxonomicResources(array('col'));
				if($harvester->processSciname($sciname)){
					$retArr = $this->getTaxon($sciname);
				}
			}
		}
		return $retArr;
	}

	private function getTaxon($sciname){
		$retArr = array();
		$targetTaxon = '';
		$sciname2 = '';
		if(array_key_exists($sciname, $this->taxonArr)){
			$targetTaxon = $sciname;
		}
		elseif(substr($sciname,-1) == 's'){
			//Soil taxon needs to have s removed from end of word
			$sciname2 = substr($sciname,0,-1);
			if(array_key_exists($sciname2, $this->taxonArr)){
				$targetTaxon = $sciname2;
			}
		}
		if(!$targetTaxon){
			$sql = 'SELECT t.tid, t.sciname, t.author, ts.family
				FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid
				WHERE ts.taxauthid = 1 AND t.sciname IN("'.$this->cleanInStr($sciname).'"'.($this->cleanInStr($sciname2)?',"'.$this->cleanInStr($sciname2).'"':'').')';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$this->taxonArr[$r->sciname]['tid'] = $r->tid;
					$this->taxonArr[$r->sciname]['author'] = $r->author;
					$this->taxonArr[$r->sciname]['family'] = $r->family;
					$targetTaxon = $r->sciname;
				}
			}
			$rs->free();
		}
		if($targetTaxon){
			$retArr['sciname'] = $targetTaxon;
			$retArr['tidInterpreted'] = $this->taxonArr[$targetTaxon]['tid'];
			$retArr['scientificNameAuthorship'] = $this->taxonArr[$targetTaxon]['author'];
			$retArr['family'] = $this->taxonArr[$targetTaxon]['family'];
		}
		return $retArr;
	}

	private function adjustTaxonomy($occidArr){
		// Update tidInterpreted index
		// These statements should no longer be needed since tids are explicitly set upon harvest, but we'll keep to ensure that nothing falls through the cracks
		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname
			INNER JOIN taxstatus ts ON t.tid = ts.tid
			SET o.tidInterpreted = t.tid
			WHERE o.tidInterpreted IS NULL AND o.family = ts.family';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating occurrence tidInterpreted with family match: '.$sql;
		}
		$sql = 'UPDATE omoccurdeterminations d INNER JOIN taxa t ON d.sciname = t.sciname
			INNER JOIN omoccurrences o ON d.occid = o.occid
			INNER JOIN taxstatus ts ON t.tid = ts.tid
			SET d.tidInterpreted = t.tid
			WHERE d.tidInterpreted IS NULL AND o.family = ts.family';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating determination tidInterpreted with family match: '.$sql;
		}

		$sql = 'UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname SET o.tidinterpreted = t.tid WHERE (o.tidinterpreted IS NULL)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating tidInterpreted: '.$sql;
		}
		$sql = 'UPDATE omoccurdeterminations d INNER JOIN taxa t ON d.sciname = t.sciname SET d.tidinterpreted = t.tid WHERE (d.tidinterpreted IS NULL)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating tidInterpreted: '.$sql;
		}

		//Update Mosquito taxa details
		$sql = 'UPDATE omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			INNER JOIN taxa t ON o.sciname = t.sciname
			INNER JOIN taxstatus ts ON t.tid = ts.tid
			SET o.scientificNameAuthorship = t.author, o.tidinterpreted = t.tid, o.family = ts.family
			WHERE (o.collid = 29) AND (o.scientificNameAuthorship IS NULL) AND (o.family IS NULL) AND (ts.taxauthid = 1)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating occurrence taxonomy codes: '.$sql;
		}
		$sql = 'UPDATE omoccurrences o INNER JOIN omoccurdeterminations d ON o.occid = d.occid
			INNER JOIN NeonSample s ON d.occid = s.occid
			INNER JOIN taxa t ON d.sciname = t.sciname
			INNER JOIN taxstatus ts ON t.tid = ts.tid
			SET d.scientificNameAuthorship = t.author, d.tidinterpreted = t.tid, d.family = ts.family
			WHERE (o.collid = 29) AND (d.scientificNameAuthorship IS NULL) AND (d.family IS NULL) AND (ts.taxauthid = 1)';
		if(!$this->conn->query($sql)){
			echo 'ERROR updating determination taxonomy codes: '.$sql;
		}

		//Run custon stored procedure that preforms some special assignment tasks
		if(!$this->conn->query('call occurrence_harvesting_sql()')){
			echo 'ERROR running stored procedure occurrence_harvesting_sql: '.$this->conn->error;
		}

		//Run stored procedure that protects rare and sensitive species
		if(!$this->conn->query('call sensitive_species_protection()')){
			echo 'ERROR running stored procedure sensitive_species_protection: '.$this->conn->error;
		}
	}

	private function translatePersonnelArr($persStr){
		$retStr = $persStr;
		if(array_key_exists($persStr, $this->personnelArr)){
			$retStr = $this->personnelArr[$persStr];
		}
		else{
			//Look to see if string can be translated via NeonPersonnel table
			$sql = 'SELECT full_info FROM NeonPersonnel WHERE neon_email = "'.$this->cleanInStr($persStr).'" OR orcid = "'.$this->cleanInStr($persStr).'"';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->personnelArr[$persStr] = $r->full_info;
				$retStr = $r->full_info;
			}
			$rs->free();
		}
		return $retStr;
	}

	private function setStateArr(){
		$sql = 'SELECT DISTINCT s.abbrev, s.statename '.
			'FROM lkupstateprovince s INNER JOIN lkupcountry c ON s.countryId = c.countryId '.
			'WHERE c.iso = "us" ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->stateArr[$r->abbrev] = $r->statename;
		}
		$rs->free();
	}

	private function setSampleClassArr(){
		$status = false;
		$result = $this->getNeonApiArr($this->neonApiBaseUrl.'/samples/supportedClasses?apiToken='.$this->neonApiKey);
		if(isset($result['entries'])){
			foreach($result['entries'] as $classArr){
				$this->sampleClassArr[$classArr['key']] = $classArr['value'];
			}
			$status = true;
		}
		return $status;
	}

	private function setDomainSiteArr(){
		$sql = 'SELECT domainNumber, domainName, siteID, siteName FROM neon_field_sites';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->domainSiteArr[$r->domainNumber] = $r->domainName;
			$this->domainSiteArr[$r->siteID] = $r->siteName;
		}
		$rs->free();
	}

	private function setTimezone($state){
		$tzArr = array();
		$tzArr['Alabama'] = 'America/Chicago';
		$tzArr['Alaska'] = 'America/Anchorage';
		$tzArr['Arizona'] = 'America/Phoenix';
		$tzArr['Arkansas'] = 'America/Chicago';
		$tzArr['California'] = 'America/Los_Angeles';
		$tzArr['Colorado'] = 'America/Denver';
		$tzArr['Connecticut'] = 'America/New_York';
		$tzArr['Delaware'] = 'America/New_York';
		$tzArr['District of Columbia'] = 'America/New_York';
		$tzArr['Florida'] = 'America/New_York';
		$tzArr['Georgia'] = 'America/New_York';
		$tzArr['Hawaii'] = 'Pacific/Honolulu';
		$tzArr['Idaho'] = 'America/Denver';
		$tzArr['Illinois'] = 'America/Chicago';
		$tzArr['Indiana'] = 'America/New_York';
		$tzArr['Iowa'] = 'America/Chicago';
		$tzArr['Kansas'] = 'America/Chicago';
		$tzArr['Kentucky'] = 'America/Chicago';
		$tzArr['Louisiana'] = 'America/Chicago';
		$tzArr['Maine'] = 'America/New_York';
		$tzArr['Maryland'] = 'America/New_York';
		$tzArr['Massachusetts'] = 'America/New_York';
		$tzArr['Michigan'] = 'America/New_York';
		$tzArr['Minnesota'] = 'America/Chicago';
		$tzArr['Mississippi'] = 'America/Chicago';
		$tzArr['Missouri'] = 'America/Chicago';
		$tzArr['Montana'] = 'America/Denver';
		$tzArr['Nebraska'] = 'America/Chicago';
		$tzArr['Nevada'] = 'America/Los_Angeles';
		$tzArr['New Hampshire'] = 'America/New_York';
		$tzArr['New Jersey'] = 'America/New_York';
		$tzArr['New Mexico'] = 'America/Denver';
		$tzArr['New York'] = 'America/New_York';
		$tzArr['North Carolina'] = 'America/New_York';
		$tzArr['North Dakota'] = 'America/Chicago';
		$tzArr['Ohio'] = 'America/New_York';
		$tzArr['Oklahoma'] = 'America/Chicago';
		$tzArr['Oregon'] = 'America/Los_Angeles';
		$tzArr['Pennsylvania'] = 'America/New_York';
		$tzArr['Puerto Rico '] = 'America/Puerto_Rico';
		$tzArr['Rhode Island'] = 'America/New_York';
		$tzArr['South Carolina'] = 'America/New_York';
		$tzArr['South Dakota'] = 'America/Chicago';
		$tzArr['Tennessee'] = 'America/Chicago';
		$tzArr['Texas'] = 'America/Chicago';
		$tzArr['Utah'] = 'America/Denver';
		$tzArr['Vermont'] = 'America/New_York';
		$tzArr['Virginia'] = 'America/New_York';
		$tzArr['Washington'] = 'America/Los_Angeles';
		$tzArr['West Virginia'] = 'America/New_York';
		$tzArr['Wisconsin'] = 'America/Chicago';
		$tzArr['Wyoming'] = 'America/Denver';
		if($state && !empty($tzArr[$state])) $this->timezone = $tzArr[$state];
	}

	private function setSampleErrorMessage($id, $msg){
		$sql = 'UPDATE NeonSample SET errorMessage = CONCAT_WS("; ", "'.$this->cleanInStr($msg).'", errorMessage) ';
		if(!$msg) $sql = 'UPDATE NeonSample SET errorMessage = NULL ';
		if(substr($id, 0, 6) == 'occid:') $sql .= 'WHERE (occid = '.substr($id, 6).') ';
		else $sql .= 'WHERE (samplePK = '.$id.') ';
		if(!$msg) $sql .= 'AND errorMessage NOT LIKE "Curatorial Check:%"';
		$this->conn->query($sql);
	}

	//General data return functions
	public function getTargetCollectionArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT c.collid, CONCAT(c.collectionName, " (",CONCAT_WS(":",c.institutionCode,c.collectionCode),")") as name
			FROM omcollections c INNER JOIN omoccurrences o ON c.collid = o.collid INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE c.institutioncode = "NEON" AND c.collid NOT IN(81,84)';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->name;
		}
		$rs->free();
		return $retArr;
	}

	//Setters and getters
	public function setReplaceFieldValues($bool){
		if($bool) $this->replaceFieldValues = true;
	}

	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function formatDate($dateStr){
		if(preg_match('/^(20\d{2})-(\d{2})-(\d{2})T\d{2}/', $dateStr)){
			//UTC datetime
			$dt = new DateTime($dateStr, new DateTimeZone('UTC'));
			$dt->setTimezone(new DateTimeZone($this->timezone));
			$dateStr = $dt->format('Y-m-d');
		}
		elseif(preg_match('/^(20\d{2})-(\d{2})-(\d{2})\D*/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(20\d{2})(\d{2})(\d{2})\D+/', $dateStr, $m)) $dateStr = $m[1].'-'.$m[2].'-'.$m[3];
		elseif(preg_match('/^(\d{1,2})\/(\d{1,2})\/(20\d{2})/', $dateStr, $m)){
			$month = $m[1];
			if(strlen($month) == 1) $month = '0'.$month;
			$day = $m[2];
			if(strlen($day) == 1) $day = '0'.$day;
			$dateStr = $m[3].'-'.$month.'-'.$day;
		}
		return $dateStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>