<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class ChecklistVoucherAdmin extends Manager {

	protected $clid;
	protected $clName;
	protected $clMetadata;
	protected $childClidArr = array();
	private $footprintWkt;
	private $queryVariablesArr = array();

	function __construct($con=null) {
		parent::__construct(null, 'write', $con);
	}

	function __destruct(){
		parent::__destruct();
	}

	public function setClid($clid){
		if(is_numeric($clid)){
			$this->clid = $clid;
			$this->setMetaData();
			//Get children checklists
			$sqlBase = 'SELECT ch.clidchild, cl2.name '.
				'FROM fmchecklists cl INNER JOIN fmchklstchildren ch ON cl.clid = ch.clid '.
				'INNER JOIN fmchecklists cl2 ON ch.clidchild = cl2.clid '.
				'WHERE (cl2.type != "excludespp") AND cl.clid IN(';
			$sql = $sqlBase.$this->clid.')';
			do{
				$childStr = "";
				$rsChild = $this->conn->query($sql);
				while($r = $rsChild->fetch_object()){
					$this->childClidArr[] = $r->clidchild;
					$childStr .= ','.$r->clidchild;
				}
				$sql = $sqlBase.substr($childStr,1).')';
			}while($childStr);
		}
	}

	private function setMetaData(){
		if($this->clid){
			$sql = 'SELECT clid, name, locality, publication, abstract, authors, parentclid, notes, latcentroid, longcentroid, pointradiusmeters, '.
				'footprintwkt, access, defaultSettings, dynamicsql, datelastmodified, uid, type, initialtimestamp '.
				'FROM fmchecklists WHERE (clid = '.$this->clid.')';
		 	$rs = $this->conn->query($sql);
			if($rs){
		 		if($row = $rs->fetch_object()){
					$this->clName = $row->name;
					$this->clMetadata["locality"] = $row->locality;
					$this->clMetadata["notes"] = $row->notes;
					$this->clMetadata["type"] = $row->type;
					$this->clMetadata["publication"] = $row->publication;
					$this->clMetadata["abstract"] = $row->abstract;
					$this->clMetadata["authors"] = $row->authors;
					$this->clMetadata["parentclid"] = $row->parentclid;
					$this->clMetadata["uid"] = $row->uid;
					$this->clMetadata["latcentroid"] = $row->latcentroid;
					$this->clMetadata["longcentroid"] = $row->longcentroid;
					$this->clMetadata["pointradiusmeters"] = $row->pointradiusmeters;
					$this->clMetadata['footprintwkt'] = $row->footprintwkt;
					$this->clMetadata["access"] = $row->access;
					$this->clMetadata["defaultSettings"] = $row->defaultSettings;
					$this->clMetadata["dynamicsql"] = $row->dynamicsql;
					$this->clMetadata["datelastmodified"] = $row->datelastmodified;
				}
				$rs->free();
			}
			else{
				trigger_error('ERROR: unable to set checklist metadata => '.$sql, E_USER_ERROR);
			}
			//Temporarly needed as a separate call until db_schema_patch-1.1.sql is applied
			$sql = 'SELECT headerurl FROM fmchecklists WHERE (clid = '.$this->clid.')';
			$rs = $this->conn->query($sql);
			if($rs){
				if($r = $rs->fetch_object()){
					$this->clMetadata['headerurl'] = $r->headerurl;
				}
				$rs->free();
			}
		}
	}

	public function getPolygonCoordinates(){
		$retArr = array();
		if($this->clid){
			if($this->clMetadata['dynamicsql']){
				$sql = 'SELECT o.decimallatitude, o.decimallongitude FROM omoccurrences o ';
				if($this->clMetadata['footprintwkt'] && substr($this->clMetadata['footprintwkt'],0,7) == 'POLYGON'){
					$sql .= 'INNER JOIN omoccurpoints p ON o.occid = p.occid WHERE (ST_Within(p.point,GeomFromText("'.$this->clMetadata['footprintwkt'].'"))) ';
				}
				else{
					$this->setCollectionVariables();
					$sql .= 'WHERE ('.$this->getSqlFrag().') ';
				}
				$sql .= 'LIMIT 50';
				//echo $sql; exit;
				$rs = $this->conn->query($sql);
				while($r = $rs->fetch_object()){
					$retArr[] = $r->decimallatitude.','.$r->decimallongitude;
				}
				$rs->free();
			}
		}
		return $retArr;
	}

	//Dynamic query variable functions
	public function setCollectionVariables(){
		if($this->clid){
			$sql = 'SELECT name, dynamicsql, footprintwkt FROM fmchecklists WHERE (clid = '.$this->clid.')';
			$result = $this->conn->query($sql);
			if($row = $result->fetch_object()){
				$this->clName = $this->cleanOutStr($row->name);
				$this->footprintWkt = $row->footprintwkt;
				$sqlFrag = $row->dynamicsql;
				$varArr = json_decode($sqlFrag,true);
				if(json_last_error() != JSON_ERROR_NONE){
					$varArr = $this->parseSqlFrag($sqlFrag);
					$this->saveQueryVariables($varArr);
				}
				$this->queryVariablesArr = $varArr;
			}
			else{
				$this->clName = 'Unknown';
			}
			$result->free();
			//Get children checklists
			$sqlChildBase = 'SELECT clidchild FROM fmchklstchildren WHERE clid IN(';
			$sqlChild = $sqlChildBase.$this->clid.')';
			do{
				$childStr = "";
				$rsChild = $this->conn->query($sqlChild);
				while($rChild = $rsChild->fetch_object()){
					$this->childClidArr[] = $rChild->clidchild;
					$childStr .= ','.$rChild->clidchild;
				}
				$sqlChild = $sqlChildBase.substr($childStr,1).')';
			}while($childStr);
		}
	}

	private function parseSqlFrag($sqlFrag){
		$retArr = array();
		if($sqlFrag){
			if(preg_match('/country = "([^"]+)"/',$sqlFrag,$m)){
				$retArr['country'] = $m[1];
			}
			if(preg_match('/stateprovince = "([^"]+)"/',$sqlFrag,$m)){
				$retArr['state'] = $m[1];
			}
			if(preg_match('/county LIKE "([^%"]+)%"/',$sqlFrag,$m)){
				$retArr['county'] = trim($m[1],' %');
			}
			if(preg_match('/locality LIKE "%([^%"]+)%"/',$sqlFrag,$m)){
				$retArr['locality'] = trim($m[1],' %');
			}
			if(preg_match('/parenttid = (\d+)\)/',$sqlFrag,$m)){
				$retArr['taxon'] = $this->getSciname($m[1]);
			}
			if(preg_match_all('/AGAINST\("([^()"]+)"\)/',$sqlFrag,$m)){
				$retArr['recordedby'] = implode(',',$m[1]);
			}
			if(preg_match('/decimallatitude BETWEEN ([-\.\d]+) AND ([-\.\d]+)\D+/',$sqlFrag,$m)){
				$retArr['latsouth'] = $m[1];
				$retArr['latnorth'] = $m[2];
			}
			if(preg_match('/decimallongitude BETWEEN ([-\.\d]+) AND ([-\.\d]+)\D+/',$sqlFrag,$m)){
				$retArr['lngwest'] = $m[1];
				$retArr['lngeast'] = $m[2];
			}
			if(preg_match('/collid = (\d+)\D/',$sqlFrag,$m)){
				$retArr['collid'] = $m[1];
			}
			if(preg_match('/decimallatitude/',$sqlFrag)){
				$retArr['onlycoord'] = 1;
			}
			if(preg_match('/cultivationStatus/',$sqlFrag)){
				$retArr['excludecult'] = 1;
			}
		}
		return $retArr;
	}

	public function saveQueryVariables($postArr){
		$fieldArr = array('country','state','county','locality','taxon','collid','recordedby','latnorth','latsouth','lngeast','lngwest','onlycoord','includewkt','excludecult');
		$jsonArr = array();
		foreach($fieldArr as $fieldName){
			if(isset($postArr[$fieldName]) && $postArr[$fieldName]) $jsonArr[$fieldName] = $postArr[$fieldName];
		}
		$sql = 'UPDATE fmchecklists c SET c.dynamicsql = '.($jsonArr?'"'.$this->cleanInStr(json_encode($jsonArr)).'"':'NULL').' WHERE (c.clid = '.$this->clid.')';
		//echo $sql; exit;
		if(!$this->conn->query($sql)){
			$statusStr = 'ERROR: unable to create or modify search statement ('.$this->conn->error.')';
		}
	}

	public function deleteQueryVariables(){
		$statusStr = '';
		if($this->conn->query('UPDATE fmchecklists c SET c.dynamicsql = NULL WHERE (c.clid = '.$this->clid.')')){
			unset($this->queryVariablesArr);
			$this->queryVariablesArr = array();
		}
		else{
			$statusStr = 'ERROR: '.$this->conn->error;
		}
		return $statusStr;
	}

	public function getQueryVariableArr(){
		return $this->queryVariablesArr;
	}

	public function getQueryVariableStr(){
		$retStr = '';
		if(isset($this->queryVariablesArr['collid'])){
			$collArr = $this->getCollectionList($this->queryVariablesArr['collid']);
			$retStr .= current($collArr).'; ';
		}
		if(isset($this->queryVariablesArr['country'])) $retStr .= $this->queryVariablesArr['country'].'; ';
		if(isset($this->queryVariablesArr['state'])) $retStr .= $this->queryVariablesArr['state'].'; ';
		if(isset($this->queryVariablesArr['county'])) $retStr .= $this->queryVariablesArr['county'].'; ';
		if(isset($this->queryVariablesArr['locality'])) $retStr .= $this->queryVariablesArr['locality'].'; ';
		if(isset($this->queryVariablesArr['taxon'])) $retStr .= $this->queryVariablesArr['taxon'].'; ';
		if(isset($this->queryVariablesArr['recordedby'])) $retStr .= $this->queryVariablesArr['recordedby'].'; ';
		if(isset($this->queryVariablesArr['latsouth']) && isset($this->queryVariablesArr['latnorth'])) $retStr .= 'Lat between '.$this->queryVariablesArr['latsouth'].' and '.$this->queryVariablesArr['latnorth'].'; ';
		if(isset($this->queryVariablesArr['lngwest']) && isset($this->queryVariablesArr['lngeast'])) $retStr .= 'Long between '.$this->queryVariablesArr['lngwest'].' and '.$this->queryVariablesArr['lngeast'].'; ';
		if(isset($this->queryVariablesArr['includewkt'])) $retStr .= 'Search based on polygon; ';
		if(isset($this->queryVariablesArr['excludecult'])) $retStr .= 'Exclude cultivated/captive records; ';
		if(isset($this->queryVariablesArr['onlycoord'])) $retStr .= 'Only include occurrences with coordinates; ';
		return trim($retStr,' ;');
	}

	public function getSqlFrag(){
		$sqlFrag = '';
		if(isset($this->queryVariablesArr['country']) && $this->queryVariablesArr['country']){
			$countrySql = '';
			$countryArr = explode(';',str_replace(',',';',$this->queryVariablesArr['country']));
			foreach($countryArr as $cTerm){
				$countrySql .= ',"'.$this->cleanInStr(trim($cTerm)).'"';
			}
			$sqlFrag .= 'AND (o.country IN('.trim($countrySql,',').')) ';
		}
		if(isset($this->queryVariablesArr['state']) && $this->queryVariablesArr['state']){
			$stateSql = '';
			$stateArr = explode(';',str_replace(',',';',$this->queryVariablesArr['state']));
			foreach($stateArr as $sTerm){
				$stateSql .= ',"'.$this->cleanInStr(trim($sTerm)).'"';
			}
			$sqlFrag .= 'AND (o.stateprovince IN('.trim($stateSql,',').')) ';
		}
		if(isset($this->queryVariablesArr['county']) && $this->queryVariablesArr['county']){
			$countyStr = str_replace(';',',',$this->queryVariablesArr['county']);
			$cArr = explode(',', $countyStr);
			$cStr = '';
			foreach($cArr as $str){
				$cStr .= 'OR (o.county LIKE "'.$this->cleanInStr($str).'%") ';
			}
			$sqlFrag .= 'AND ('.substr($cStr, 2).') ';
		}
		//taxonomy
		if(isset($this->queryVariablesArr['taxon']) && $this->queryVariablesArr['taxon']){
			$tStr = $this->cleanInStr($this->queryVariablesArr['taxon']);
			$tidPar = $this->getTid($tStr);
			if($tidPar){
				$sqlFrag .= 'AND (o.tidinterpreted IN (SELECT ts.tid FROM taxaenumtree e INNER JOIN taxstatus ts ON e.tid = ts.tidaccepted WHERE ts.taxauthid = 1 AND e.taxauthid = 1 AND e.parenttid = '.$tidPar.')) ';
			}
		}
		//Locality and Latitude and longitude
		$locStr = '';
		if(isset($this->queryVariablesArr['locality']) && $this->queryVariablesArr['locality']){
			$localityStr = str_replace(';',',',$this->queryVariablesArr['locality']);
			$locArr = explode(',', $localityStr);
			foreach($locArr as $str){
				$str = $this->cleanInStr($str);
				if(strlen($str) > 4){
					$locStr .= 'OR (MATCH(f.locality) AGAINST(\'"'.$str.'"\' IN BOOLEAN MODE)) ';
				}
				else{
					$locStr .= 'OR (o.locality LIKE "%'.$str.'%") ';
				}
				//$locStr .= 'OR (o.locality LIKE "%'.$this->cleanInStr($str).'%") ';
			}
		}
		$llStr = '';
		if(isset($this->queryVariablesArr['latnorth']) && isset($this->queryVariablesArr['latsouth']) && is_numeric($this->queryVariablesArr['latnorth']) && is_numeric($this->queryVariablesArr['latsouth'])){
			if(isset($this->queryVariablesArr['lngwest']) && isset($this->queryVariablesArr['lngeast']) && is_numeric($this->queryVariablesArr['lngwest']) && is_numeric($this->queryVariablesArr['lngeast'])){
				$llStr .= '(o.decimallatitude BETWEEN '.$this->queryVariablesArr['latsouth'].' AND '.$this->queryVariablesArr['latnorth'].') '.
					'AND (o.decimallongitude BETWEEN '.$this->queryVariablesArr['lngwest'].' AND '.$this->queryVariablesArr['lngeast'].') ';
			}
		}
		if(isset($this->queryVariablesArr['includewkt']) && $this->queryVariablesArr['includewkt'] && $this->footprintWkt){
			//Searh based on polygon
			$sqlFrag .= 'AND (ST_Within(p.point,GeomFromText("'.$this->footprintWkt.'"))) ';
			$llStr = false;
		}
		if(isset($this->queryVariablesArr['latlngor']) && $this->queryVariablesArr['latlngor'] && $locStr && $llStr){
			//Query coordinates or locality string
			$sqlFrag .= 'AND (('.substr($locStr, 2).') OR ('.trim($llStr).')) ';
		}
		else{
			if($locStr) $sqlFrag .= 'AND ('.substr($locStr, 2).') ';
			if($llStr) $sqlFrag .= 'AND ('.trim($llStr).') ';
		}
		if(isset($this->queryVariablesArr['onlycoord']) && $this->queryVariablesArr['onlycoord'] && !$llStr && $llStr !== false){
			//Use occurrences only with coordinates
			$sqlFrag .= 'AND (o.decimallatitude IS NOT NULL) ';
		}
		//Exclude taxonomy
		if(isset($this->queryVariablesArr['excludecult']) && $this->queryVariablesArr['excludecult']){
			$sqlFrag .= 'AND (o.cultivationStatus = 0 OR o.cultivationStatus IS NULL) ';
		}
		//Limit by collection
		if(isset($this->queryVariablesArr['collid']) && is_numeric($this->queryVariablesArr['collid'])){
			$sqlFrag .= 'AND (o.collid IN('.$this->queryVariablesArr['collid'].')) ';
		}
		//Limit by collector
		if(isset($this->queryVariablesArr['recordedby']) && $this->queryVariablesArr['recordedby']){
			$collStr = str_replace(',', ';', strtolower($this->queryVariablesArr['recordedby']));
			$collArr = explode(';',$collStr);
			$tempArr = array();
			foreach($collArr as $str){
				if(strlen($str) < 4 || in_array($str,array('best','little'))){
					//Need to avoid FULLTEXT stopwords interfering with return
					$tempArr[] = '(o.recordedby LIKE "%'.$this->cleanInStr($str).'%")';
				}
				else{
					$tempArr[] = '(MATCH(f.recordedby) AGAINST("'.$this->cleanInStr($str).'"))';
				}
			}
			$sqlFrag .= 'AND ('.implode(' OR ', $tempArr).') ';
		}
		//Save SQL fragment
		if($sqlFrag) $sqlFrag = trim(substr($sqlFrag,3));
		return $sqlFrag;
	}

	//Voucher loading functions
	public function linkVouchers($occidArr){
		$retStatus = '';
		$sqlFrag = '';
		foreach($occidArr as $v){
			$vArr = explode('-',$v);
			if(count($vArr) == 2 && $vArr[0] && $vArr[1]) $sqlFrag .= ',('.$this->clid.','.$vArr[0].','.$vArr[1].')';
		}
		$sql = 'INSERT IGNORE INTO fmvouchers(clid,occid,tid) VALUES '.substr($sqlFrag,1);
		//echo $sql;
		if(!$this->conn->query($sql)){
			trigger_error('Unable to link voucher; '.$this->conn->error,E_USER_WARNING);
		}
		return $retStatus;
	}

	public function linkVoucher($taxa,$occid,$morphoSpecies=""){
		if(!is_numeric($taxa)){
			$rs = $this->conn->query('SELECT tid FROM taxa WHERE (sciname = "'.$this->conn->real_escape_string($taxa).'")');
			if($r = $rs->fetch_object()){
				$taxa = $r->tid;
			}
		}
		$sql = 'INSERT INTO fmvouchers(clid,tid,occid) VALUES ('.$this->clid.','.$taxa.','.$occid.')';
		if($this->conn->query($sql)){
			return 1;
		}
		else{
			if($this->conn->errno == 1062){
				//trigger_error('Specimen already a voucher for checklist ');
				return 'Specimen already a voucher for checklist';
			}
			else{
				//trigger_error('Attempting to resolve by adding species to checklist; '.$this->conn->error,E_USER_WARNING);
				$sql2 = 'INSERT INTO fmchklsttaxalink(tid,clid,morphospecies) VALUES('.$taxa.','.$this->clid.',"'.$morphoSpecies.'")';
				if($this->conn->query($sql2)){
					if($this->conn->query($sql)){
						return 1;
					}
					else{
						//echo 'Name added to list, though still unable to link voucher';
						//trigger_error('Name added to checklist, though still unable to link voucher": '.$this->conn->error,E_USER_WARNING);
						return 'Name added to checklist, though unable to link voucher: '.$this->conn->error;
					}
				}
				else{
					//echo 'Unable to link voucher; unknown error';
					//trigger_error('Unable to link voucher; '.$this->conn->error,E_USER_WARNING);
					return 'Unable to link voucher: '.$this->conn->error;
				}
			}
		}
	}

	public function linkTaxaVouchers($occidArr, $useCurrentTaxon = true, $linkVouchers = true){
		$tidsUsed = array();
		foreach($occidArr as $v){
			$vArr = explode('-',$v);
			$tid = $vArr[1];
			$occid = $vArr[0];
			if(count($vArr) == 2 && is_numeric($occid) && is_numeric($tid)){
				if($useCurrentTaxon){
					$sql = 'SELECT tidaccepted FROM taxstatus WHERE taxauthid = 1 AND tid = '.$tid;
					$rs = $this->conn->query($sql);
					if($r = $rs->fetch_object()){
						$tid = $r->tidaccepted;
					}
					$rs->free();
				}
				if(!in_array($tid,$tidsUsed)){
					//Add name to checklist
					$sql = 'INSERT INTO fmchklsttaxalink(clid,tid) VALUES('.$this->clid.','.$tid.')';
					$tidsUsed[] = $tid;
					//echo $sql;
					if(!$this->conn->query($sql)){
						trigger_error('Unable to add taxon; '.$this->conn->error,E_USER_WARNING);
					}
				}
				if($linkVouchers){
					$sql = 'INSERT INTO fmvouchers(clid,occid,tid) VALUES ('.$this->clid.','.$occid.','.$tid.')';
					if(!$this->conn->query($sql)){
						trigger_error('Unable to link taxon voucher; '.$this->conn->error,E_USER_WARNING);
					}
				}
			}
		}
	}

	public function batchAdjustChecklist($postArr){
		$occidArr = $postArr['occid'];
		$removeTidArr = array();
		foreach($occidArr as $occid){
			//Get checklist tid
			$tidChecklist = 0;
			$sql = 'SELECT tid FROM fmvouchers WHERE (clid = '.$this->clid.') AND (occid = '.$occid.')';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$removeTidArr[] = $r->tid;
			}
			$rs->free();
			//Get voucher tid
			$tidVoucher = 0;
			$sql1 = 'SELECT tidinterpreted FROM omoccurrences WHERE (occid = '.$occid.')';
			$rs1 = $this->conn->query($sql1);
			if($r1 = $rs1->fetch_object()){
				$tidVoucher = $r1->tidinterpreted;
			}
			$rs1->free();
			//Make sure target name is already linked to checklist
			$sql2 = 'INSERT IGNORE INTO fmchklsttaxalink(tid, clid, morphospecies, familyoverride, habitat, abundance, notes, explicitExclude, source, internalnotes, dynamicProperties) '.
				'SELECT '.$tidVoucher.' as tid, c.clid, c.morphospecies, c.familyoverride, c.habitat, c.abundance, c.notes, c.explicitExclude, c.source, c.internalnotes, c.dynamicProperties '.
				'FROM fmchklsttaxalink c INNER JOIN fmvouchers v ON c.tid = v.tid AND c.clid = v.clid '.
				'WHERE (c.clid = '.$this->clid.') AND (v.occid = '.$occid.')';
			$this->conn->query($sql2);
			//Transfer voucher to new name
			$sql3 = 'UPDATE fmvouchers SET tid = '.$tidVoucher.' WHERE (clid = '.$this->clid.') AND (occid = '.$occid.')';
			$this->conn->query($sql3);
		}
		if(array_key_exists('removetaxa',$postArr)){
			//Remove taxa where all vouchers have been removed
			$sql4 = 'DELETE c.* FROM fmchklsttaxalink c LEFT JOIN fmvouchers v ON c.clid = v.clid AND c.tid = v.tid '.
				'WHERE (c.clid = '.$this->clid.') AND (c.tid IN('.implode(',', $removeTidArr).')) AND (v.clid IS NULL)';
			$this->conn->query($sql4);
		}
	}

	public function vouchersExist(){
		$bool = false;
		if($this->clid){
			$sql = 'SELECT tid FROM fmvouchers WHERE (clid = '.$this->clid.') LIMIT 1';
			$rs = $this->conn->query($sql);
			if($rs->num_rows) $bool = true;
			$rs->free();
		}
		return $bool;
	}

	//Misc data functions
	public function getCollectionList($collId = 0){
		$retArr = array();
		$sql = 'SELECT collid, collectionname FROM omcollections ';
		if($collId) $sql .= 'WHERE collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid] = $r->collectionname;
		}
		$rs->free();
		asort($retArr);
		return $retArr;
	}

	private function getSciname($tid){
		$retStr = '';
		if(is_numeric($tid)){
			$sql = 'SELECT sciname FROM taxa WHERE tid = '.$tid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retStr = $r->sciname;
			}
			$rs->free();
		}
		return $retStr;
	}

	private function getTid($sciname){
		$tidRet = 0;
		$sql = 'SELECT tid FROM taxa WHERE sciname = ("'.$sciname.'")';
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$tidRet = $r->tid;
		}
		$rs->free();
		return $tidRet;
	}

	public function getVoucherProjects(){
		global $USER_RIGHTS;
		$retArr = array();
		$runQuery = true;
		$sql = 'SELECT collid, collectionname FROM omcollections WHERE (colltype = "Observations" OR colltype = "General Observations") ';
		if(!array_key_exists('SuperAdmin',$USER_RIGHTS)){
			$collInStr = '';
			foreach($USER_RIGHTS as $k => $v){
				if($k == 'CollAdmin' || $k == 'CollEditor'){
					$collInStr .= ','.implode(',',$v);
				}
			}
			if($collInStr){
				$sql .= 'AND collid IN ('.substr($collInStr,1).') ';
			}
			else{
				$runQuery = false;
			}
		}
		$sql .= 'ORDER BY colltype,collectionname';
		//echo $sql;
		if($runQuery){
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr[$r->collid] = $r->collectionname;
				}
				$rs->free();
			}
			if($retArr){
				//Tag collection most likely to be target
				$sql = 'SELECT o.collid, COUNT(v.occid) as cnt FROM fmvouchers v INNER JOIN omoccurrences o ON v.occid = o.occid '.
					'WHERE v.clid = '.$this->clid.' AND o.collid IN('.implode(',', array_keys($retArr)).') GROUP BY o.collid ORDER BY cnt DESC';
				if($rs = $this->conn->query($sql)){
					if($r = $rs->fetch_object()) $retArr['target'] = $r->collid;
					$rs->free();
				}
			}
		}
		return $retArr;
	}

	public function hasVoucherProjects(){
		global $USER_RIGHTS;
		$retBool = false;
		$runQuery = true;
		$sql = 'SELECT collid, collectionname FROM omcollections WHERE (colltype = "Observations" OR colltype = "General Observations") ';
		if(!array_key_exists('SuperAdmin',$USER_RIGHTS)){
			$collInStr = '';
			foreach($USER_RIGHTS as $k => $v){
				if($k == 'CollAdmin' || $k == 'CollEditor'){
					$collInStr .= ','.implode(',',$v);
				}
			}
			if($collInStr){
				$sql .= 'AND collid IN ('.substr($collInStr,1).') ';
			}
			else{
				$runQuery = false;
			}
		}
		$sql .= ' LIMIT 1';
		//echo $sql;
		if($runQuery){
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					$retBool = true;
				}
				$rs->free();
			}
		}
		return $retBool;
	}

	//Setters and getters
	public function getClid(){
		return $this->clid;
	}

	public function getChildClidArr(){
		return $this->childClidArr;
	}

	public function getClName(){
		return $this->clName;
	}

	public function getClMetadata(){
		return $this->clMetadata;
	}

	public function getClFootprintWkt(){
		return $this->footprintWkt;
	}

	//Misc functions
	protected function encodeArr(&$inArr){
		$charSetOut = 'ISO-8859-1';
		$charSetSource = strtoupper($GLOBALS['CHARSET']);
		if($charSetSource && $charSetOut != $charSetSource){
			foreach($inArr as $k => $v){
				$inArr[$k] = $this->encodeStr($v);
			}
		}
	}

	protected function encodeStr($inStr){
		$charSetSource = strtoupper($GLOBALS['CHARSET']);
		$charSetOut = 'ISO-8859-1';
		$retStr = $inStr;
		if($inStr && $charSetSource){
			if($charSetOut == 'UTF-8' && $charSetSource == 'ISO-8859-1'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1',true) == 'ISO-8859-1'){
					$retStr = utf8_encode($inStr);
					//$retStr = iconv("ISO-8859-1//TRANSLIT","UTF-8",$inStr);
				}
			}
			elseif($charSetOut == "ISO-8859-1" && $charSetSource == 'UTF-8'){
				if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') == 'UTF-8'){
					$retStr = utf8_decode($inStr);
					//$retStr = iconv("UTF-8","ISO-8859-1//TRANSLIT",$inStr);
				}
			}
		}
		return $retStr;
	}
}
?>