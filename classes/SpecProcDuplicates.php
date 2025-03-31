<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class SpecProcDuplicates extends Manager {

	private $collid;
	private $fieldArr = array();
	private $activeFieldArr = array();
	private $occFieldArr;
	private $geoFieldArr;
	private $collMetaArr = array();

	function __construct() {
		parent::__construct(null,'write');
		//$this->scinameFieldArr = array('family','sciname','taxonRemarks','identifiedBy','dateIdentified','identificationReferences','identificationRemarks','identificationQualifier');
		$this->occurFieldArr = array('recordedBy','recordNumber','associatedCollectors','eventDate','verbatimEventDate','habitat','substrate','fieldNotes','fieldnumber',
			'occurrenceRemarks','informationWithheld','associatedOccurrences','dataGeneralizations','associatedTaxa','dynamicProperties','verbatimAttributes','behavior',
			'reproductiveCondition','cultivationStatus','establishmentMeans','lifeStage','sex','individualCount','samplingProtocol','samplingEffort','preparations',
			'locationID','country','stateProvince','county','municipality','waterBody','locality','recordSecurity','securityReason','locationRemarks',
			'minimumElevationInMeters','maximumElevationInMeters','verbatimElevation','minimumDepthInMeters','maximumDepthInMeters','verbatimDepth',
			'disposition','storageLocation','language','verbatimCoordinates');
		$this->geoFieldArr = array('decimalLatitude','decimalLongitude','verbatimCoordinates','geodeticDatum','coordinateUncertaintyInMeters','footprintWKT','georeferencedBy','georeferenceProtocol',
			'georeferenceSources','georeferenceVerificationStatus','georeferenceRemarks');
	}

	function __destruct(){
 		parent::__destruct();
	}

	//Duplicate matching tools
	public function buildDuplicateArr($evaluationType, $evaluationDate, $processingStatus = 'unprocessed', $limit = 100){
		$retArr = array();
		if($evaluationType != 'dupe' && $evaluationType != 'exsiccate') return false;
		if($this->collid){
			$this->fieldArr = array_unique(array_merge($this->occurFieldArr,$this->geoFieldArr));
			$sql = 'SELECT o.occid, o.collid, IFNULL(o.catalogNumber,o.othercatalogNumbers) as catalogNumber, o.'.implode(',o.',$this->fieldArr).' ';
			$orderBy = '';
			if($evaluationType == 'exsiccate'){
				//Match dupes based on Exsiccati
				$sql .= ' ';
				$orderBy = ' ';
			}
			else{
				//Match dupes based on specimen duplicate tables
				$sql .= ',d.duplicateid as dupeid FROM omoccurduplicatelink d INNER JOIN omoccurrences o ON d.occid = o.occid ';
				$orderBy = 'ORDER BY d.duplicateid ';
			}
			$sql .= 'WHERE o.collid = '.$this->collid.' ';
			if($processingStatus) $sql .= 'AND o.processingstatus = "'.$processingStatus.'" ';
			if($evaluationDate){
				$sql .= 'AND o.occid NOT IN(SELECT occid FROM specprocstatus WHERE initialTimestamp > "'.$evaluationDate.'") ';
			}
			//$sql .= 'AND o.occid = 4113806 ';
			if($orderBy) $sql .= $orderBy;
			//echo $sql;
			$rs = $this->conn->query($sql);
			$occid = 0;
			$cnt = 0;
			while($r = $rs->fetch_assoc()){
				//Load subject occurrence record into array
				$recArr = array();
				$occid = $r['occid'];
				$recArr[0]['occid']['v'] = $r['occid'];
				$recArr[0]['collid']['v'] = $r['collid'];
				$recArr[0]['catalogNumber']['v'] = $r['catalogNumber'];
				foreach($this->fieldArr as $fieldName){
					if($r[$fieldName]) $recArr[0][$fieldName]['v'] = '---';
					else $recArr[0][$fieldName]['v'] = '';
				}
				if($evaluationType == 'exsiccate'){
					//Exsiccate table

				}
				else{
					//Duplicate table
					if($this->appendDuplicates($recArr,$r['dupeid'],$occid)){
						if($this->evaluateDuplicateArr($recArr)){
							$retArr[$occid] = $recArr;
							$cnt++;
						}
					}
				}
				$this->setAsEvaluated($occid,'duplicateMatch');
				if($cnt > $limit) break;
			}
			$rs->free();
		}
		return $retArr;
	}

	private function appendDuplicates(&$recArr,$dupeid,$occid){
		$status = false;
		$sql = 'SELECT o.occid, o.collid, IFNULL(o.catalogNumber,o.othercatalogNumbers) as catalogNumber, '.implode(',',$this->fieldArr).' '.
			'FROM omoccurrences o INNER JOIN omoccurduplicatelink d ON o.occid = d.occid '.
			'WHERE d.duplicateid = '.$dupeid.' AND d.occid != '.$occid;
		$rs = $this->conn->query($sql);
		$cnt = 1;
		while($r = $rs->fetch_assoc()){
			$status = true;
			$recArr[$cnt]['occid']['v'] = $r['occid'];
			$recArr[$cnt]['collid']['v'] = $r['collid'];
			$recArr[$cnt]['catalogNumber']['v'] = $r['catalogNumber'];
			foreach($this->fieldArr as $fieldName){
				if($r[$fieldName] !== ''){
					if($recArr[0][$fieldName]['v'] == '---') $recArr[$cnt][$fieldName]['v'] = '---';
					else $recArr[$cnt][$fieldName]['v'] = $r[$fieldName];
				}
				else $recArr[$cnt][$fieldName]['v'] = '';
			}
			$this->collMetaArr[$r['collid']] = 0;
			$cnt++;
		}
		$rs->free();
		return $status;
	}

	private function evaluateDuplicateArr(&$recArr){
		$status = false;
		$subjectArr = $recArr[0];
		//Process general occurrence fields without any coordination between fields
		foreach($this->occurFieldArr as $fieldName){
			$currentVal = $subjectArr[$fieldName]['v'];
			if(!$currentVal){
				$rateArr = array();
				$topRank = 0;
				$bestKey = '';
				for($i = 1; $i < count($recArr); $i++){
					//Iterate through each duplicate record
					$fieldStr = trim($recArr[$i][$fieldName]['v']);
					if($fieldStr){
						$testStr = $this->normalizeFieldValue($fieldStr);
						if(array_key_exists($testStr,$rateArr)){
							$rank = $rateArr[$testStr]['r'];
							$rank++;
							if($rank > $topRank){
								$topRank = $rank;
								$bestKey = $testStr;
							}
							$rateArr[$testStr]['r'] = $rank;
						}
						else{
							$rateArr[$testStr]['r'] = 1;
							if(!$bestKey){
								//Seed evaluation values
								$topRank = 1;
								$bestKey = $testStr;
							}
						}
						$rateArr[$testStr]['i'][] = $i;
					}
				}
				if($bestKey){
					//Tag field as being adjusted
					$this->activeFieldArr[$fieldName] = 1;
					$selectArr = $rateArr[$bestKey]['i'];
					foreach($selectArr as $i){
						//Set preferred string within object record
						if(!isset($recArr[0][$fieldName]['p'])) $recArr[0][$fieldName]['p'] = $recArr[$i][$fieldName]['v'];
						//Tag matching duplicate fields as being used
						$recArr[$i][$fieldName]['c'] = 's';
					}
					$status = true;
				}
			}
		}
		//Process georeference fields with field coordinated against each other
		$curLatValue = $subjectArr['decimalLatitude']['v'];
		$curLngValue = $subjectArr['decimalLongitude']['v'];
		$curVerbatimCoordValue = $subjectArr['verbatimCoordinates']['v'];
		if(!$curLatValue && !$curLngValue && !$curVerbatimCoordValue){
			$rateArr = array();
			$bestTestKeyArr = array();
			for($i = 1; $i < count($recArr); $i++){
				//Iterate through each duplicate record and evaluate
				$latValue = trim($recArr[$i]['decimalLatitude']['v']);
				$lngValue = trim($recArr[$i]['decimalLongitude']['v']);
				//$verbatimCoordValue = trim($recArr[$i]['verbatimCoordinates']['v']);
				if($latValue && $lngValue){
					$testKey = $latValue.' '.$lngValue;
					if(array_key_exists($testKey,$rateArr)){
						$rank = $rateArr[$testKey]['r'];
						$rank++;
						$bestTestKeyArr[$testKey] = $rank;
						$rateArr[$testKey]['r'] = $rank;
					}
					else{
						$rateArr[$testKey]['r'] = 1;
						$bestTestKeyArr[$testKey] = 1;
					}
					$rateArr[$testKey]['i'][] = $i;
				}
			}
			arsort($bestTestKeyArr);
			$bestScore = current($bestTestKeyArr);
			foreach($bestTestKeyArr as $key => $score){
				if($score < $bestScore){
					unset($bestTestKeyArr[$key]);
				}
			}
			$bestTestKey = key($bestTestKeyArr);
			if($rateArr){
				$bestIndex = current($rateArr[$bestTestKey]['i']);
				if(count($bestTestKeyArr) > 1){
					//More than one record is best match, evaluate which is best match
					$topScore = 0;
					foreach($bestTestKeyArr as $k => $s){
						foreach($rateArr[$k]['i'] as $index){
							$score = 0;
							if(strlen($recArr[$index]['decimalLatitude']['v']) > 5 && strlen($recArr[$index]['decimalLatitude']['v']) < 10) $score += 2;
							if(strlen($recArr[$index]['decimalLongitude']['v']) > 7 && strlen($recArr[$index]['decimalLongitude']['v']) < 12) $score += 2;
							if(isset($recArr[$index]['verbatimCoordinates']['v']) && $recArr[$index]['verbatimCoordinates']['v']) $score += 4;
							if(isset($recArr[$index]['coordinateUncertaintyInMeters']['v']) && $recArr[$index]['coordinateUncertaintyInMeters']['v'])  $score += 5;
							if(isset($recArr[$index]['geodeticDatum']['v']) && $recArr[$index]['geodeticDatum']['v'])  $score++;
							if(isset($recArr[$index]['georeferencedBy']['v']) && $recArr[$index]['georeferencedBy']['v'])  $score++;
							if(isset($recArr[$index]['georeferenceProtocol']['v']) && $recArr[$index]['georeferenceProtocol']['v'])  $score++;
							if(isset($recArr[$index]['georeferenceSources']['v']) && $recArr[$index]['georeferenceSources']['v'])  $score++;
							if(isset($recArr[$index]['georeferenceVerificationStatus']['v']) && $recArr[$index]['georeferenceVerificationStatus']['v'])  $score++;
							if($score > $topScore){
								$topScore = $score;
								$bestIndex = $index;
								$bestTestKey = $k;
							}
						}
					}
				}
				foreach($this->geoFieldArr as $fieldName){
					//Tag field as being adjusted
					$this->activeFieldArr[$fieldName] = 1;
					//Set preferred string within object record
					$recArr[0][$fieldName]['p'] = $recArr[$bestIndex][$fieldName]['v'];
					//Tag matching duplicate fields as being used
					$recArr[$bestIndex][$fieldName]['c'] = 's';
				}
				$status = true;
			}
		}
		return $status;
	}

	private function normalizeFieldValue($str){
		$str = preg_replace("/[^A-Za-z0-9]/", '', $str);
		return $str;
	}

	private function setAsEvaluated($occid,$processName){
		$sql = 'INSERT INTO specprocstatus(occid,processName,processorUid) VALUES('.$occid.',"'.$processName.'",'.$GLOBALS['SYMB_UID'].')';
		if(!$this->conn->query($sql)){
			$this->errorMessage = 'ERROR registering occurrence as evaluated: '.$this->conn->error;
			echo $this->errorMessage;
		}
	}

	public function getCollMetaArr(){
		if(!$this->collMetaArr || !current($this->collMetaArr)){
			$collStr = $this->collid;
			if($this->collMetaArr) $collStr .= ','.implode(',',$this->collMetaArr);
			$sql = 'SELECT collid, CONCAT_WS(":",institutionCode, collectionCode) AS collcode, collectionName FROM omcollections WHERE collid IN('.$collStr.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->collMetaArr[$r->collid] = array('collcode' => $r->collcode, 'name' => $r->collectionName);
			}
			$rs->free();
		}
		return $this->collMetaArr;
	}

	//NLP Alignment tools
	public function batchBuildFragments(){
		set_time_limit(600);
		$this->logOrEcho('Starting batch process');
		$sql = 'SELECT r.prlid, r.rawstr '.
			'FROM specprocessorrawlabels r LEFT JOIN specprococrfrag f ON r.prlid = f.prlid '.
			'WHERE f.prlid IS NULL LIMIT 1000';
		$rs = $this->conn->query($sql);
		$cnt = 1;
		while($r = $rs->fetch_object()){
			if($this->processFragment($r->rawstr,$r->prlid)){
				if($cnt%1000 == 0) $this->logOrEcho($cnt.' OCR records',1);
			}
			$cnt++;
		}
		$rs->free();
		$this->logOrEcho('Batch process finished');
	}

	private function processFragment($rawOcr,$prlid){
		$status = false;
		//Clean string
		$rawOcr = str_replace('.', ' ',$rawOcr);
		$rawOcr = preg_replace('/\s\s+/',' ',$rawOcr);
		$rawOcr = trim(preg_replace('/[^a-zA-Z0-9\s]/','',$rawOcr));
		if(strlen($rawOcr) > 10){
			//Load into database
			$wordArr = preg_split("/\s/", $rawOcr);
			$previousWord = '';
			$cnt = 0;
			$sqlFrag = '';
			if(count($wordArr) > 1){
				foreach($wordArr as $w){
					if($previousWord){
						$keyTerm = $previousWord.$w;
						$sqlFrag .= ',('.$prlid.',"'.$previousWord.'","'.$w.'","'.$keyTerm.'",'.$cnt.')';
					}
					$previousWord = $w;
				}
				$sql = 'INSERT INTO specprococrfrag(prlid,firstword,secondword,keyterm,wordorder) '.
					'VALUES'.substr($sqlFrag,1);
				//$this->logOrEcho($sql);
				if($this->conn->query($sql)){
					$status = true;
					$cnt++;
				}
				else{
					$this->logOrEcho('ERROR loading terms (#'.$prlid.'): '.$this->conn->error, $indent = 1);
					$this->logOrEcho($sql);
				}
			}
		}
		return $status;
	}

	//Setters and getters
	public function setCollID($id){
		if(is_numeric($id)) $this->collid = $id;
	}

	public function getActiveFieldArr(){
		return $this->activeFieldArr;
	}
}
?>