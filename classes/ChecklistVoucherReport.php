<?php
include_once($SERVER_ROOT.'/classes/ChecklistVoucherAdmin.php');

class ChecklistVoucherReport extends ChecklistVoucherAdmin {

	private $missingTaxaCount = 0;

	function __construct($con = null) {
		parent::__construct($con);
	}

	function __destruct(){
		parent::__destruct();
	}

	//Listing function for tabs
	public function getVoucherCnt(){
		$vCnt = 0;
		if($this->clid){
			$sql = 'SELECT count(*) AS vcnt FROM fmvouchers WHERE (clid = '.$this->clid.')';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$vCnt = $r->vcnt;
			}
			$rs->free();
		}
		return $vCnt;
	}

	public function getNonVoucheredCnt(){
		$uvCnt = 0;
		$sql = 'SELECT count(t.tid) AS uvcnt '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
			'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
			'WHERE v.clid IS NULL AND (ctl.clid = '.$this->clid.') AND ts.taxauthid = 1 ';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$uvCnt = $row->uvcnt;
		}
		$rs->free();
		return $uvCnt;
	}

	public function getNonVoucheredTaxa($startLimit,$limit = 100){
		$retArr = Array();
		$sql = 'SELECT t.tid, ts.family, t.sciname '.
			'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
			'INNER JOIN fmchklsttaxalink ctl ON t.tid = ctl.tid '.
			'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
			'WHERE v.clid IS NULL AND (ctl.clid = '.$this->clid.') AND ts.taxauthid = 1 '.
			'ORDER BY ts.family, t.sciname '.
			'LIMIT '.($startLimit?$startLimit.',':'').$limit;
		//echo '<div>'.$sql.'</div>';
		$rs = $this->conn->query($sql);
		while($row = $rs->fetch_object()){
			$retArr[$row->family][$row->tid] = $this->cleanOutStr($row->sciname);
		}
		$rs->free();
		return $retArr;
	}

	public function getNewVouchers($startLimit = 500,$includeAll = 1){
		$retArr = Array();
		if($sqlFrag = $this->getSqlFrag()){
			if($includeAll == 1 || $includeAll == 2){
				$sql = 'SELECT DISTINCT cl.tid AS cltid, t.sciname AS clsciname, o.occid, c.institutioncode, c.collectioncode, o.catalognumber, '.
					'o.tidinterpreted, o.sciname, o.recordedby, o.recordnumber, o.eventdate, CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
					'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid '.
					'INNER JOIN taxstatus ts ON o.tidinterpreted = ts.tid '.
					'INNER JOIN fmchklsttaxalink cl ON ts.tidaccepted = cl.tid '.
					'INNER JOIN taxa t ON cl.tid = t.tid ';
				$sql .= $this->getTableJoinFrag($sqlFrag);
				$sql .= 'WHERE ('.$sqlFrag.') AND (cl.clid = '.$this->clid.') AND (ts.taxauthid = 1) ';
				if($includeAll == 1){
					$idArr = $this->getVoucherIDs('tid');
					if($idArr) $sql .= 'AND cl.tid NOT IN('.implode(',',$idArr).') ';
				}
				elseif($includeAll == 2){
					$idArr = $this->getVoucherIDs('occid');
					if($idArr) $sql .= 'AND o.occid NOT IN('.implode(',',$idArr).') ';
				}
				$sql .= 'ORDER BY ts.family, o.sciname LIMIT '.$startLimit.', 500';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->cltid][$r->occid]['tid'] = $r->tidinterpreted;
					$sciName = $r->clsciname;
					if($r->clsciname <> $r->sciname) $sciName .= '<br/>spec id: '.$r->sciname;
					$retArr[$r->cltid][$r->occid]['sciname'] = $sciName;
					$collCode = '';
					if(!$r->catalognumber || strpos($r->catalognumber, $r->institutioncode) === false){
						$collCode = $r->institutioncode.($r->collectioncode?'-'.$r->collectioncode:'');
					}
					$collCode .= ($collCode?'-':'').($r->catalognumber?$r->catalognumber:'[catalog number null]');
					$retArr[$r->cltid][$r->occid]['collcode'] = $collCode;
					$retArr[$r->cltid][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$r->cltid][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$r->cltid][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$r->cltid][$r->occid]['locality'] = $r->locality;
				}
			}
			elseif($includeAll == 3){
				$sql = 'SELECT DISTINCT t.tid AS cltid, t.sciname AS clsciname, o.occid, '.
					'c.institutioncode, c.collectioncode, o.catalognumber, '.
					'o.tidinterpreted, o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
					'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
					'FROM omcollections AS c INNER JOIN omoccurrences AS o ON c.collid = o.collid '.
					'LEFT JOIN taxa AS t ON o.tidinterpreted = t.TID '.
					'LEFT JOIN taxstatus AS ts ON t.TID = ts.tid ';
				$sql .= $this->getTableJoinFrag($sqlFrag);
				$sql .= 'WHERE ('.$sqlFrag.') AND ((t.RankId < 220)) ';
				$idArr = $this->getVoucherIDs('occid');
				if($idArr) $sql .= 'AND (o.occid NOT IN('.implode(',',$idArr).')) ';
				$sql .= 'ORDER BY o.family, o.sciname LIMIT '.$startLimit.', 500';
				//echo '<div>'.$sql.'</div>';
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[$r->cltid][$r->occid]['tid'] = $r->tidinterpreted;
					$sciName = $r->clsciname;
					if($r->clsciname <> $r->sciname) $sciName .= '<br/>spec id: '.$r->sciname;
					$retArr[$r->cltid][$r->occid]['sciname'] = $sciName;
					$collCode = '';
					if(!$r->catalognumber || strpos($r->catalognumber, $r->institutioncode) === false){
						$collCode = $r->institutioncode.($r->collectioncode?'-'.$r->collectioncode:'');
					}
					$collCode .= ($collCode?'-':'').($r->catalognumber?$r->catalognumber:'[catalog number null]');
					$retArr[$r->cltid][$r->occid]['collcode'] = $collCode;
					$retArr[$r->cltid][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$r->cltid][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$r->cltid][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$r->cltid][$r->occid]['locality'] = $r->locality;
				}
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getMissingTaxa(){
		$retArr = Array();
		if($sqlFrag = $this->getSqlFrag()){
			$sql = 'SELECT DISTINCT t.tid, t.sciname, o.sciname AS occur_sciname '.$this->getMissingTaxaBaseSql($sqlFrag).' LIMIT 1000 ';
			//echo '<div>'.$sql.'</div>'; exit;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$sciStr = $r->sciname;
				if($r->sciname != $r->occur_sciname) $sciStr .= ' (syn: '.$r->occur_sciname.')';
				$retArr[$r->tid] = $this->cleanOutStr($sciStr);
			}
			asort($retArr);
			$rs->free();
		}
		$this->missingTaxaCount = count($retArr);
		return $retArr;
	}

	public function getMissingTaxaSpecimens($limitIndex, $limitRange = 1000){
		$retArr = Array();
		if($sqlFrag = $this->getSqlFrag()){
			$sqlBase = $this->getMissingTaxaBaseSql($sqlFrag);
			$sql = 'SELECT DISTINCT o.occid, c.institutioncode ,c.collectioncode, o.catalognumber, '.
				'o.tidinterpreted, t.sciname, o.sciname AS occur_sciname, o.recordedby, o.recordnumber, o.eventdate, '.
				'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
				$sqlBase.' LIMIT '.($limitIndex?($limitIndex*1000).',':'').$limitRange;
			//echo '<div>'.$sql.'</div>';
			$cnt = 0;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[$r->sciname][$r->occid]['o_sn'] = $r->occur_sciname;
				$retArr[$r->sciname][$r->occid]['tid'] = $r->tidinterpreted;
				$collCode = '';
				if(!$r->catalognumber || strpos($r->catalognumber, $r->institutioncode) === false){
					$collCode = $r->institutioncode.($r->collectioncode?'-'.$r->collectioncode:'');
				}
				$collCode .= ($collCode?'-':'').($r->catalognumber?$r->catalognumber:'[catalog number null]');
				$retArr[$r->sciname][$r->occid]['collcode'] = $collCode;
				$retArr[$r->sciname][$r->occid]['recordedby'] = $r->recordedby;
				$retArr[$r->sciname][$r->occid]['recordnumber'] = $r->recordnumber;
				$retArr[$r->sciname][$r->occid]['eventdate'] = $r->eventdate;
				$retArr[$r->sciname][$r->occid]['locality'] = $r->locality;
				$cnt++;
			}
			$rs->free();
			ksort($retArr);

			//Set missing taxa count
			if($cnt<1000){
				$sqlB = 'SELECT COUNT(DISTINCT ts.tidaccepted) as cnt '.$sqlBase;
				//echo '<div>'.$sqlB.'</div>'; exit;
				$rsB = $this->conn->query($sqlB);
				if($r = $rsB->fetch_object()){
					$this->missingTaxaCount = $r->cnt;
				}
				$rsB->free();
			}
			else $this->missingTaxaCount = '1000+';
		}
		return $retArr;
	}

	public function getConflictVouchers(){
		$retArr = Array();
		$clidStr = $this->clid;
		if($this->childClidArr){
			$clidStr .= ','.implode(',',$this->childClidArr);
		}
		$sql = 'SELECT DISTINCT t.tid, v.clid, t.sciname AS listid, o.recordedby, o.recordnumber, o.sciname, o.identifiedby, o.dateidentified, o.occid '.
			'FROM taxstatus ts1 INNER JOIN omoccurrences o ON ts1.tid = o.tidinterpreted '.
			'INNER JOIN fmvouchers v ON o.occid = v.occid '.
			'INNER JOIN taxstatus ts2 ON v.tid = ts2.tid '.
			'INNER JOIN taxa t ON v.tid = t.tid '.
			'INNER JOIN taxstatus ts3 ON ts1.tidaccepted = ts3.tid '.
			'WHERE (v.clid IN('.$clidStr.')) AND ts1.taxauthid = 1 AND ts2.taxauthid = 1 AND ts1.tidaccepted <> ts2.tidaccepted '.
			'AND ts1.parenttid <> ts2.tidaccepted AND v.tid <> o.tidinterpreted AND ts3.parenttid <> v.tid '.
			'ORDER BY t.sciname ';
		//echo $sql;
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($row = $rs->fetch_object()){
			$clSciname = $row->listid;
			$voucherSciname = $row->sciname;
			//if(str_replace($voucherSciname)) continue;
			$retArr[$cnt]['tid'] = $row->tid;
			$retArr[$cnt]['clid'] = $row->clid;
			$retArr[$cnt]['occid'] = $row->occid;
			$retArr[$cnt]['listid'] = $clSciname;
			$collStr = $row->recordedby;
			if($row->recordnumber) $collStr .= ' ('.$row->recordnumber.')';
			$retArr[$cnt]['recordnumber'] = $this->cleanOutStr($collStr);
			$retArr[$cnt]['specid'] = $this->cleanOutStr($voucherSciname);
			$idBy = $row->identifiedby;
			if($row->dateidentified) $idBy .= ' ('.$this->cleanOutStr($row->dateidentified).')';
			$retArr[$cnt]['identifiedby'] = $this->cleanOutStr($idBy);
			$cnt++;
		}
		$rs->free();
		return $retArr;
	}

	//Export functions used within voucherreporthandler.php
	public function exportMissingOccurCsv(){
		if($sqlFrag = $this->getSqlFrag()){
			$fileName = 'Missing_'.$this->getExportFileName().'.csv';

			$fieldArr = $this->getOccurrenceFieldArr();
			$localitySecurityFields = $this->getLocalitySecurityArr();

			$exportSql = 'SELECT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
				$this->getMissingTaxaBaseSql($sqlFrag,true);
			//echo $exportSql;
			$this->exportCsv($fileName,$exportSql,$localitySecurityFields);
		}
	}

	private function getMissingTaxaBaseSql($sqlFrag, $asExport = false){
		$clidStr = $this->clid;
		if($this->childClidArr) $clidStr .= ','.implode(',',$this->childClidArr);
		$retSql = 'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.collid '.
			'INNER JOIN taxstatus ts ON o.tidinterpreted = ts.tid '.
			'INNER JOIN taxa t ON ts.tidaccepted = t.tid ';
		if($asExport) $retSql .= 'LEFT JOIN guidoccurrences g ON o.occid = g.occid ';
		$retSql .= $this->getTableJoinFrag($sqlFrag);
		$retSql .= 'WHERE ('.$sqlFrag.') AND (t.rankid > 219) AND (ts.taxauthid = 1) ';
		$idArr = $this->getVoucherIDs('occid');
		if($idArr) $retSql .= 'AND (o.occid NOT IN('.implode(',',$idArr).')) ';
		$retSql .= 'AND (ts.tidaccepted NOT IN(SELECT ts.tidaccepted FROM fmchklsttaxalink cl INNER JOIN taxstatus ts ON cl.tid = ts.tid WHERE ts.taxauthid = 1 AND cl.clid IN('.$clidStr.'))) ';
		return $retSql;
	}

	public function getMissingProblemTaxa(){
		$retArr = Array();
		if($sqlFrag = $this->getSqlFrag()){
			//Make sure tidinterpreted are valid
			//$this->conn->query('UPDATE omoccurrences o INNER JOIN taxa t ON o.sciname = t.sciname SET o.tidinterpreted = t.tid WHERE o.tidinterpreted IS NULL');
			//Grab records
			$sql = 'SELECT DISTINCT o.occid, c.institutioncode, c.collectioncode, o.catalognumber, '.
				'o.sciname, o.recordedby, o.recordnumber, o.eventdate, '.
				'CONCAT_WS("; ",o.country, o.stateprovince, o.county, o.locality) as locality '.
				$this->getProblemTaxaSql($sqlFrag);
			//echo $sql;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$sciname = $r->sciname;
				if($sciname){
					$collCode = '';
					if(!$r->catalognumber || strpos($r->catalognumber, $r->institutioncode) === false){
						$collCode = $r->institutioncode.($r->collectioncode?'-'.$r->collectioncode:'');
					}
					$collCode .= ($collCode?'-':'').($r->catalognumber?$r->catalognumber:'[catalog number null]');
					$retArr[$sciname][$r->occid]['collcode'] = $collCode;
					$retArr[$sciname][$r->occid]['recordedby'] = $r->recordedby;
					$retArr[$sciname][$r->occid]['recordnumber'] = $r->recordnumber;
					$retArr[$sciname][$r->occid]['eventdate'] = $r->eventdate;
					$retArr[$sciname][$r->occid]['locality'] = $r->locality;
				}
			}
			$rs->free();
		}
		$this->missingTaxaCount = count($retArr);
		return $retArr;
	}

	public function exportProblemTaxaCsv(){
		$fileName = 'ProblemTaxa_'.$this->getExportFileName().'.csv';

		if($sqlFrag = $this->getSqlFrag()){
			$fieldArr = $this->getOccurrenceFieldArr();
			$localitySecurityFields = $this->getLocalitySecurityArr();
			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.$this->getProblemTaxaSql($sqlFrag,true);
			$this->exportCsv($fileName,$sql,$localitySecurityFields);
		}
	}

	private function getProblemTaxaSql($sqlFrag, $asExport = false){
		//$clidStr = $this->clid;
		//if($this->childClidArr) $clidStr .= ','.implode(',',$this->childClidArr);
		$retSql = 'FROM omoccurrences o LEFT JOIN omcollections c ON o.collid = c.CollID ';
		if($asExport) $retSql .= 'LEFT JOIN guidoccurrences g ON o.occid = g.occid ';
		$retSql .= $this->getTableJoinFrag($sqlFrag);
		$retSql .= 'WHERE ('.$sqlFrag.') AND (o.tidinterpreted IS NULL) AND (o.sciname IS NOT NULL) ';
		$idArr = $this->getVoucherIDs('occid');
		if($idArr) $retSql .= 'AND (o.occid NOT IN('.implode(',',$idArr).')) ';
		return $retSql;
	}

	private function getVoucherIDs($idType){
		$retArr = array();
		$clidStr = $this->clid;
		if($this->childClidArr){
			$clidStr .= ','.implode(',',$this->childClidArr);
		}
		$sql = 'SELECT DISTINCT '.$idType.' as id FROM fmvouchers WHERE CLID IN('.$clidStr.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[] = $r->id;
		}
		$rs->free();
		return $retArr;
	}

	private function getTableJoinFrag($sqlFrag){
		$retSql = '';
		if(strpos($sqlFrag,'MATCH(f.recordedby)') || strpos($sqlFrag,'MATCH(f.locality)')){
			$retSql .= 'INNER JOIN omoccurrencesfulltext f ON o.occid = f.occid ';
		}
		if(strpos($sqlFrag,'p.point')){
			$retSql .= 'INNER JOIN omoccurpoints p ON o.occid = p.occid ';
		}
		return $retSql;
	}

	public function downloadChecklistCsv(){
		if($this->clid){
			$fieldArr = array('tid'=>'t.tid AS Taxon_Local_ID');
			$fieldArr['clhabitat'] = 'ctl.habitat AS habitat';
			$fieldArr['clabundance'] = 'ctl.abundance';
			$fieldArr['clNotes'] = 'ctl.notes';
			$fieldArr['clSource'] = 'ctl.source';
			$fieldArr['editorNotes'] = 'ctl.internalnotes';
			$fieldArr['family'] = 'IFNULL(ctl.familyoverride,ts.family) AS family';
			$fieldArr['scientificName'] = 't.sciName AS scientificName';
			$fieldArr['author'] = 't.author AS scientificNameAuthorship';

			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}

			$fileName = $this->getExportFileName().'.csv';
			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).' '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN fmchklsttaxalink ctl ON ctl.tid = t.tid '.
				'WHERE (ts.taxauthid = 1) AND (ctl.clid IN('.$clidStr.')) ';
			$this->exportCsv($fileName,$sql);
		}
	}

	public function downloadVoucherCsv(){
		if($this->clid){
			$fileName = $this->getExportFileName().'.csv';

			$fieldArr = array('tid'=>'t.tid AS taxonID', 'family'=>'IFNULL(ctl.familyoverride,ts.family) AS family', 'scientificName'=>'t.sciname', 'author'=>'t.author AS scientificNameAuthorship');
			$fieldArr['clhabitat'] = 'ctl.habitat AS cl_habitat';
			$fieldArr['clabundance'] = 'ctl.abundance';
			$fieldArr['clNotes'] = 'ctl.notes';
			$fieldArr['clSource'] = 'ctl.source';
			$fieldArr['editorNotes'] = 'ctl.internalnotes';
			$fieldArr = array_merge($fieldArr,$this->getOccurrenceFieldArr());
			$fieldArr['family'] = 'ts.family';
			$fieldArr['scientificName'] = 't.sciName AS scientificName';

			$localitySecurityFields = $this->getLocalitySecurityArr();

			$clidStr = $this->clid;
			if($this->childClidArr){
				$clidStr .= ','.implode(',',$this->childClidArr);
			}

			$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
				'FROM taxa t INNER JOIN taxstatus ts ON t.tid = ts.tid '.
				'INNER JOIN fmchklsttaxalink ctl ON ctl.tid = t.tid '.
				'LEFT JOIN fmvouchers v ON ctl.clid = v.clid AND ctl.tid = v.tid '.
				'LEFT JOIN omoccurrences o ON v.occid = o.occid '.
				'LEFT JOIN omcollections c ON o.collid = c.collid '.
				'LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
				'WHERE (ts.taxauthid = 1) AND (ctl.clid IN('.$clidStr.')) ';
			$this->exportCsv($fileName,$sql,$localitySecurityFields);
		}
	}

	public function downloadAllOccurrenceCsv(){
		if($this->clid){
			$fileName = $this->getExportFileName().'.csv';
			if($sqlFrag = $this->getSqlFrag()){
				$fieldArr = array('tid'=>'t.tid AS taxonID', 'family'=>'IFNULL(ctl.familyoverride,ts.family) AS family', 'scientificName'=>'t.sciname', 'author'=>'t.author AS scientificNameAuthorship');
				$fieldArr['clhabitat'] = 'ctl.habitat AS cl_habitat';
				$fieldArr['clabundance'] = 'ctl.abundance';
				$fieldArr['clNotes'] = 'ctl.notes';
				$fieldArr['clSource'] = 'ctl.source';
				$fieldArr['editorNotes'] = 'ctl.internalnotes';
				$fieldArr = array_merge($fieldArr,$this->getOccurrenceFieldArr());
				$fieldArr['family'] = 'ts.family';
				$fieldArr['scientificName'] = 't.sciName AS scientificName';

				$localitySecurityFields = $this->getLocalitySecurityArr();

				$clidStr = $this->clid;
				if($this->childClidArr){
					$clidStr .= ','.implode(',',$this->childClidArr);
				}

				$sql = 'SELECT DISTINCT '.implode(',',$fieldArr).', o.localitysecurity, o.collid '.
					'FROM fmchklsttaxalink ctl INNER JOIN taxa t ON ctl.tid = t.tid '.
					'INNER JOIN taxstatus ts ON ctl.tid = ts.tid '.
					'LEFT JOIN taxstatus ts2 ON ts.tidaccepted = ts2.tidaccepted '.
					'LEFT JOIN omoccurrences o ON ts2.tid = o.tidinterpreted '.
					'LEFT JOIN omcollections c ON o.collid = c.collid '.
					'LEFT JOIN guidoccurrences g ON o.occid = g.occid '.
					$this->getTableJoinFrag($sqlFrag).
					'WHERE ('.$sqlFrag.') AND (ts.taxauthid = 1) AND (ts2.taxauthid = 1) AND (ctl.clid IN('.$clidStr.')) ';
				$this->exportCsv($fileName,$sql,$localitySecurityFields);
			}
		}
	}

	private function exportCsv($fileName,$sql,$localitySecurityFields = null){
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header ('Content-Type: text/csv');
		header ('Content-Disposition: attachment; filename="'.$fileName.'"');
		//echo $sql; exit;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$headerArr = array();
			$fields = mysqli_fetch_fields($rs);
			foreach ($fields as $val) {
				$headerArr[] = $val->name;
			}
			$rareSpeciesReader = $this->isRareSpeciesReader();
			$out = fopen('php://output', 'w');
			fputcsv($out, $headerArr);
			while($row = $rs->fetch_assoc()){
				if($localitySecurityFields){
					$localSecurity = ($row["localitysecurity"]?$row["localitysecurity"]:0);
					if(!$rareSpeciesReader && $localSecurity != 1 && (!array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS']) || !in_array($row['collid'],$GLOBALS['USER_RIGHTS']['RareSppReader']))){
						$redactStr = '';
						foreach($localitySecurityFields as $fieldName){
							if($row[$fieldName]) $redactStr .= ','.$fieldName;
						}
						if($redactStr) $row['informationWithheld'] = 'Fields with redacted values (e.g. rare species localities):'.trim($redactStr,', ');
					}
				}
				$this->encodeArr($row);
				fputcsv($out, $row);
			}
			$rs->free();
			fclose($out);
		}
		else{
			echo "Recordset is empty.\n";
		}
	}

	protected function getExportFileName(){
		$fileName = $this->clName;
		if($fileName){
			$fileName = str_replace(Array('.',' ',':','&','"',"'",'(',')','[',']'),'',$fileName);
			if(strlen($fileName) > 20){
				$nameArr = explode(' ',$fileName);
				foreach($nameArr as $k => $w){
					if(strlen($w) > 3) $nameArr[$k] = substr($w,0,4);
				}
				$fileName = implode('',$nameArr);
			}
		}
		else{
			$fileName = 'symbiota';
		}
		$fileName .= '_'.time();
		return $fileName;
	}

	private function getOccurrenceFieldArr(){
		$retArr = array('o.family AS family_occurrence', 'o.sciName AS scientificName_occurrence', 'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode','IFNULL(o.collectionCode,c.collectionCode) AS collectionCode',
			'CASE guidTarget WHEN "symbiotaUUID" THEN IFNULL(o.occurrenceID,g.guid) WHEN "occurrenceId" THEN o.occurrenceID WHEN "catalogNumber" THEN o.catalogNumber ELSE "" END AS occurrenceID',
			'o.catalogNumber', 'o.otherCatalogNumbers', 'o.identifiedBy', 'o.dateIdentified',
 			'o.recordedBy', 'o.recordNumber', 'o.eventDate', 'o.country', 'o.stateProvince', 'o.county', 'o.municipality', 'o.locality',
 			'o.decimalLatitude', 'o.decimalLongitude', 'o.coordinateUncertaintyInMeters', 'o.minimumElevationInMeters', 'o.maximumelevationinmeters',
			'o.verbatimelevation', 'o.habitat', 'o.occurrenceRemarks', 'o.associatedTaxa', 'o.reproductivecondition', 'o.informationWithheld', 'o.occid');
		$retArr[] = 'g.guid AS recordID';
		$serverDomain = "http://";
		if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $serverDomain = "https://";
		$serverDomain .= $_SERVER["SERVER_NAME"];
		if($_SERVER["SERVER_PORT"] && $_SERVER["SERVER_PORT"] != 80 && $_SERVER['SERVER_PORT'] != 443) $serverDomain .= ':'.$_SERVER["SERVER_PORT"];
		$retArr[] = 'CONCAT("'.$serverDomain.$GLOBALS['CLIENT_ROOT'].'/collections/individual/index.php?occid=",o.occid) as `references`';
		return $retArr;

		/*
		return array('family'=>'o.family','scientificName'=>'o.sciName AS scientificName_occurrence','institutionCode'=>'IFNULL(o.institutionCode,c.institutionCode) AS institutionCode',
			'collectionCode'=>'IFNULL(o.collectionCode,c.collectionCode) AS collectionCode','occurrenceID'=>'o.occurrenceID',
			'catalogNumber'=>'o.catalogNumber','identifiedBy'=>'o.identifiedBy','dateIdentified'=>'o.dateIdentified',
			'recordedBy'=>'o.recordedBy','recordNumber'=>'o.recordNumber','eventDate'=>'o.eventDate','country'=>'o.country',
			'stateProvince'=>'o.stateProvince','county'=>'o.county','municipality'=>'o.municipality','locality'=>'o.locality',
			'decimalLatitude'=>'o.decimalLatitude','decimalLongitude'=>'o.decimalLongitude','coordinateUncertaintyInMeters'=>'o.coordinateUncertaintyInMeters','minimumElevationInMeters'=>'o.minimumElevationInMeters',
			'maximumElevationInMeters'=>'o.maximumelevationinmeters','verbatimElevation'=>'o.verbatimelevation',
			'habitat'=>'o.habitat','occurrenceRemarks'=>'o.occurrenceRemarks','associatedTaxa'=>'o.associatedTaxa',
			'reproductiveCondition'=>'o.reproductivecondition','informationWithheld'=>'o.informationWithheld','occid'=>'o.occid');
		*/
	}

	private function getLocalitySecurityArr(){
		return array('recordNumber','eventDate','locality','decimalLatitude','decimalLongitude','minimumElevationInMeters',
			'minimumElevationInMeters','habitat','occurrenceRemarks');
	}

	//Misc fucntions
	public function getMissingTaxaCount(){
		return $this->missingTaxaCount;
	}

	private function isRareSpeciesReader(){
		$canReadRareSpp = false;
		if($GLOBALS['IS_ADMIN']
			|| array_key_exists("CollAdmin", $GLOBALS['USER_RIGHTS'])
			|| array_key_exists("RareSppAdmin", $GLOBALS['USER_RIGHTS']) || array_key_exists("RareSppReadAll", $GLOBALS['USER_RIGHTS'])){
			$canReadRareSpp = true;
		}
		return $canReadRareSpp;
	}
}
?>