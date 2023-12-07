<?php
include_once($SERVER_ROOT.'/classes/OccurrenceTaxaManager.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSearchSupport.php');

class ImageLibrarySearch extends OccurrenceTaxaManager{

	private $dbStr;
	private $taxonType = 2;
	private $taxaStr;
	private $useThes = 1;
	private $photographerUid;
	private $tags;
	private $keywords;
	private $imageCount = 'all';
	private $imageType = 0;

	private $recordCount = 0;
	private $tidFocus;
	private $searchSupportManager = null;
	private $sqlWhere = '';

	function __construct() {
		parent::__construct();
		if(array_key_exists('TID_FOCUS', $GLOBALS) && preg_match('/^[\d,]+$/', $GLOBALS['TID_FOCUS'])){
			$this->tidFocus = $GLOBALS['TID_FOCUS'];
		}
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getImageArr($pageRequest,$cntPerPage){
		$retArr = Array();
		$includeOccurrenceTable = false;
		if($this->imageType == 1 || $this->imageType == 2 || ($this->dbStr && $this->dbStr != 'all')) $includeOccurrenceTable = true;
		$this->setSqlWhere();
		$this->setRecordCnt();
		$sql = 'SELECT i.imgid, i.tid, i.url, i.thumbnailurl, i.originalurl, i.photographeruid, i.caption, i.occid, ';
		if($includeOccurrenceTable) $sql .= 'IFNULL(t.sciname,o.sciname) as sciname ';
		else $sql .= 't.sciname ';
		/*
		$sql = 'SELECT DISTINCT i.imgid, o.tidinterpreted, t.tid, t.sciname, i.url, i.thumbnailurl, i.originalurl, i.photographeruid, i.caption, '.
			'o.occid, o.stateprovince, o.catalognumber, CONCAT_WS("-",c.institutioncode, c.collectioncode) as instcode ';
		*/
		$sqlWhere = $this->sqlWhere;
		if($this->imageCount == 'taxon') $sqlWhere .= 'GROUP BY sciname ';
		elseif($this->imageCount == 'specimen') $sqlWhere .= 'GROUP BY i.occid ';
		if($this->sqlWhere){
			if($includeOccurrenceTable) $sqlWhere .= 'ORDER BY o.sciname ';
			else  $sqlWhere .= 'ORDER BY t.sciname ';
		}
		$bottomLimit = ($pageRequest - 1)*$cntPerPage;
		$sql .= $this->getSqlBase().$sqlWhere.'LIMIT '.$bottomLimit.','.$cntPerPage;
		$occArr = array();
		$result = $this->conn->query($sql);
		$imgId = 0;
		while($r = $result->fetch_object()){
			if($imgId == $r->imgid) continue;
			$imgId = $r->imgid;
			$retArr[$imgId]['imgid'] = $r->imgid;
			//$retArr[$imgId]['tidaccepted'] = $r->tidinterpreted;
			$retArr[$imgId]['tid'] = $r->tid;
			$retArr[$imgId]['sciname'] = $r->sciname;
			$retArr[$imgId]['url'] = $r->url;
			$retArr[$imgId]['thumbnailurl'] = $r->thumbnailurl;
			$retArr[$imgId]['originalurl'] = $r->originalurl;
			$retArr[$imgId]['uid'] = $r->photographeruid;
			$retArr[$imgId]['caption'] = $r->caption;
			$retArr[$imgId]['occid'] = $r->occid;
			//$retArr[$imgId]['stateprovince'] = $r->stateprovince;
			//$retArr[$imgId]['catalognumber'] = $r->catalognumber;
			//$retArr[$imgId]['instcode'] = $r->instcode;
			if($r->occid) $occArr[$r->occid] = $r->occid;
		}
		$result->free();
		if($occArr){
			//Get occurrence data
			$collArr = array();
			$sql2 = 'SELECT occid, catalognumber, sciname, recordedby, stateprovince, collid FROM omoccurrences WHERE occid IN('.implode(',',$occArr).')';
			$rs2 = $this->conn->query($sql2);
			while($r2 = $rs2->fetch_object()){
				$retArr['occ'][$r2->occid]['catnum'] = $r2->catalognumber;
				$retArr['occ'][$r2->occid]['sciname'] = $r2->sciname;
				$retArr['occ'][$r2->occid]['recordedby'] = $r2->recordedby;
				$retArr['occ'][$r2->occid]['stateprovince'] = $r2->stateprovince;
				$retArr['occ'][$r2->occid]['collid'] = $r2->collid;
				$collArr[$r2->collid] = $r2->collid;
			}
			$rs2->free();
			//Get collection data
			$sql3 = 'SELECT collid, CONCAT_WS("-",institutioncode, collectioncode) as instcode FROM omcollections WHERE collid IN('.implode(',',$collArr).')';
			$rs3 = $this->conn->query($sql3);
			while($r3 = $rs3->fetch_object()){
				$retArr['coll'][$r3->collid] = $r3->instcode;
			}
			$rs3->free();
		}
		return $retArr;
	}

	private function setSqlWhere(){
		$sqlWhere = '';
		if($this->dbStr){
			$sqlWhere .= OccurrenceSearchSupport::getDbWhereFrag($this->cleanInStr($this->dbStr));
		}
		if(isset($this->taxaArr['taxa'])){
			$sqlWhereTaxa = $this->getTaxonWhereFrag();
			if(!$this->imageType || $this->imageType == 3){
				if(strpos($sqlWhereTaxa, 'o.tidinterpreted')) $sqlWhereTaxa = str_replace('o.tidinterpreted', 't.tid', $sqlWhereTaxa);
				if(strpos($sqlWhereTaxa, 'o.sciname')) $sqlWhereTaxa = str_replace('o.sciname', 't.sciname', $sqlWhereTaxa);
				if(strpos($sqlWhereTaxa, 'o.family')) $sqlWhereTaxa = str_replace('o.family', 'ts.family', $sqlWhereTaxa);
			}
			if($sqlWhereTaxa) $sqlWhere .= 'AND ('.substr($sqlWhereTaxa,3).') ';
		}
		elseif($this->tidFocus){
			$sqlWhere .= 'AND (e.parenttid IN('.$this->tidFocus.')) AND (e.taxauthid = 1) ';
		}
		if($this->photographerUid){
			$sqlWhere .= 'AND (i.photographeruid IN('.$this->photographerUid.')) ';
		}
		if($this->tags){
			$sqlWhere .= 'AND (it.keyvalue = "'.$this->cleanInStr($this->tags).'") ';
		}
		if($this->keywords){
			$keywordArr = explode(";",$this->keywords);
			$tempArr = Array();
			foreach($keywordArr as $value){
				$tempArr[] = "(ik.keyword LIKE '%".$this->cleanInStr($value)."%')";
			}
			$sqlWhere .= "AND (".implode(" OR ",$tempArr).") ";
		}
		if($this->imageType){
			if($this->imageType == 1){
				//Specimen Images
				$sqlWhere .= 'AND (i.occid IS NOT NULL) AND (c.colltype = "Preserved Specimens") ';
			}
			elseif($this->imageType == 2){
				//Image Vouchered Observations
				$sqlWhere .= 'AND (i.occid IS NOT NULL) AND (c.colltype != "Preserved Specimens") ';
			}
			elseif($this->imageType == 3){
				//Field Images (lacking specific locality details)
				$sqlWhere .= 'AND (i.occid IS NULL) ';
			}
		}
		if(strpos($sqlWhere,'ts.taxauthid')) $sqlWhere = str_replace('t.tid', 'ts.tid', $sqlWhere);
		if($sqlWhere) $this->sqlWhere = 'WHERE '.substr($sqlWhere,4);
	}

	private function setRecordCnt(){
		$sql = 'SELECT COUNT(DISTINCT i.imgid) AS cnt ';
		if($this->imageCount){
			if($this->imageCount == 'taxon') $sql = "SELECT COUNT(DISTINCT i.tid) AS cnt ";
			elseif($this->imageCount == 'specimen') $sql = "SELECT COUNT(DISTINCT i.occid) AS cnt ";
			else $sql = "SELECT COUNT(DISTINCT i.imgid) AS cnt ";
		}
		$sql .= $this->getSqlBase().$this->sqlWhere;
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$this->recordCount = $r->cnt;
		}
		$rs->free();
	}

	private function getSqlBase(){
		$sql = 'FROM images i ';
		if($this->taxaArr){
			$sql .= 'INNER JOIN taxa t ON i.tid = t.tid ';
		}
		else{
			$sql .= 'LEFT JOIN taxa t ON i.tid = t.tid ';
		}
		if(strpos($this->sqlWhere,'ts.taxauthid')){
			$sql .= 'INNER JOIN taxstatus ts ON i.tid = ts.tid ';
		}
		if(strpos($this->sqlWhere,'e.taxauthid') || $this->tidFocus){
			$sql .= 'INNER JOIN taxaenumtree e ON i.tid = e.tid ';
		}
		if($this->tags){
			$sql .= 'INNER JOIN imagetag it ON i.imgid = it.imgid ';
		}
		if($this->keywords){
			$sql .= 'INNER JOIN imagekeywords ik ON i.imgid = ik.imgid ';
		}
		if($this->imageType == 1 || $this->imageType == 2){
			$sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid INNER JOIN omcollections c ON o.collid = c.collid ';
		}
		elseif($this->dbStr && $this->dbStr != 'all'){
			$sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid ';
		}
		return $sql;
	}

	public function getFullCollectionList($catId = ''){
		if(!$this->searchSupportManager) $this->searchSupportManager = new OccurrenceSearchSupport($this->conn);
		if($this->dbStr) $this->searchSupportManager->setCollidStr($this->dbStr);
		return $this->searchSupportManager->getFullCollectionList($catId, true);
	}

	public function outputFullCollArr($occArr, $targetCatID = 0){
		if(!$this->searchSupportManager) $this->searchSupportManager = new OccurrenceSearchSupport($this->conn);
		$this->searchSupportManager->outputFullCollArr($occArr, $targetCatID, false, false);
	}

	//Misc support functions
	public function getQueryTermStr(){
		$retStr = '';
		if($this->dbStr) $retStr .= '&db='.$this->dbStr;
		if($this->taxonType) $retStr .= '&taxontype='.$this->taxonType;
		if($this->taxaStr) $retStr .= '&taxa='.$this->taxaStr;
		if($this->useThes) $retStr .= '&usethes=1';
		if($this->photographerUid) $retStr .= '&phuid='.$this->photographerUid;
		if($this->tags) $retStr .= '&tags='.urlencode($this->tags);
		if($this->keywords) $retStr .= '&keywords='.$this->keywords;
		if($this->imageCount) $retStr .= '&imagecount='.$this->imageCount;
		if($this->imageType) $retStr .= '&imagetype='.$this->imageType;
		return trim($retStr,' &');
	}

	private function getPhotographerStr($uidStr){
		$retArr = array();
		if($uidStr){
			$sql = 'SELECT CONCAT_WS(" ",firstname,lastname) as name FROM users WHERE uid IN('.$uidStr.')';
			$rs = $this->conn->query($sql);
			while ($r = $rs->fetch_object()) {
				$retArr[] = $r->name;
			}
			$rs->free();
		}
		return implode(', ',$retArr);
	}

	private function getCollectionStr($collidStr){
		$retArr = array();
		$collidStr = trim($collidStr,';, ');
		if($collidStr && preg_match('/^[,\s\d]+$/', $collidStr)){
			$sql = 'SELECT CONCAT(collectionname," (",CONCAT_WS(" ",institutioncode,collectioncode),")") as collname FROM omcollections WHERE collid IN('.$collidStr.')';
			$rs = $this->conn->query($sql);
			while ($r = $rs->fetch_object()) {
				$retArr[] = $r->collname;
			}
			$rs->free();
		}
		return implode(', ',$retArr);
	}

	//Listing functions
	public function getPhotographerUidArr(){
		$retArr = array();
		$sql1 = 'SELECT DISTINCT photographeruid FROM images WHERE photographeruid IS NOT NULL';
		$rs1 = $this->conn->query($sql1);
		while ($r1 = $rs1->fetch_object()) {
			$retArr[$r1->photographeruid] = '';
		}
		$rs1->free();
		if($retArr){
			$sql2 = 'SELECT uid, CONCAT_WS(", ", lastname, firstname) AS fullname FROM users WHERE uid IN('.implode(',',array_keys($retArr)).')';
			$rs2 = $this->conn->query($sql2);
			while ($r2 = $rs2->fetch_object()) {
				$retArr[$r2->uid] = $r2->fullname;
			}
			$rs2->free();
		}
		asort($retArr,SORT_NATURAL | SORT_FLAG_CASE);
		return $retArr;
	}

	public function getTagArr(){
		$retArr = array();
		$sql = 'SELECT DISTINCT keyvalue FROM imagetag ORDER BY keyvalue ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_object()){
				$retArr[] = $r->keyvalue;
			}
		}
		$rs->free();
		return $retArr;
	}

	public function getKeywordSuggest($queryStr){
		global $CHARSET;
		$retArr = array();
		$sql = 'SELECT DISTINCT keyword FROM imagekeywords WHERE keyword LIKE "'.$this->cleanInStr($queryStr).'%" LIMIT 10 ';
		$rs = $this->conn->query($sql);
		$i = 0;
		while ($r = $rs->fetch_object()) {
			$retArr[$i]['name'] = html($r->keyword, ENT_COMPAT, $CHARSET);
			$i++;
		}
		$rs->free();
		return $retArr;
	}

	private function resetTaxaStr(){
		$sql = 'SELECT sciname FROM taxa WHERE (tid = '.$this->taxaStr.')';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()) {
			$this->taxaStr = $r->sciname;
		}
		$rs->free();
	}

	//Setters and getters
	public function setCollectionVariables($reqArr){
		$this->dbStr = OccurrenceSearchSupport::getDbRequestVariable($reqArr);
	}

	public function setTaxonType($t){
		if(is_numeric($t)) $this->taxonType = $t;
	}

	public function getTaxonType(){
		return $this->taxonType;
	}

	public function setTaxaStr($str){
		if(strpos($str,'<') === false){
			$this->taxaStr = filter_var(trim($str), FILTER_SANITIZE_STRING);
			if($this->taxaStr){
				if(is_numeric($this->taxaStr)) $this->resetTaxaStr();
				$this->setTaxonRequestVariable(array('taxa'=>$this->taxaStr,'taxontype'=>$this->taxonType,'usethes'=>$this->useThes));
			}
		}
	}

	public function getTaxaStr(){
		return $this->taxaStr;
	}

	public function setUseThes($u){
		if(is_numeric($u)) $this->useThes = $u;
	}

	public function getUseThes(){
		return $this->useThes;
	}

	public function setPhotographerUid($uid){
		if(is_numeric($uid)) $this->photographerUid = $uid;
	}

	public function getPhotographerUid(){
		return $this->photographerUid;
	}

	public function setTags($t){
		if(strpos($t,'<') === false) $this->tags = filter_var($t, FILTER_SANITIZE_STRING);
	}

	public function getTags(){
		return $this->tags;
	}

	public function setKeywords($k){
		if(strpos($k,'<') === false) $this->keywords = filter_var($k, FILTER_SANITIZE_STRING);
	}

	public function getKeywords(){
		return $this->keywords;
	}

	public function setImageCount($c){
		if(in_array($c, array('all','taxon','specimen'))) $this->imageCount = $c;
	}

	public function getImageCount(){
		return $this->imageCount;
	}

	public function setImageType($t){
		if(is_numeric($t)) $this->imageType = $t;
	}

	public function getImageType(){
		return $this->imageType;
	}

	public function getRecordCnt(){
		return $this->recordCount;
	}

	public function getSearchTermDisplayStr(){
		$retStr = '';
		if($this->dbStr) $retStr .= $this->getCollectionStr($this->dbStr);
		if($this->taxaStr) $retStr .= '; '.$this->taxaStr;
		if($this->photographerUid) $retStr .= '; '.$this->getPhotographerStr($this->photographerUid);
		if($this->tags) $retStr .= '; '.$this->tags;
		if($this->keywords) $retStr .= '; '.$this->keywords;
		if($this->imageType == 1) $retStr .= '; Limit to specimens';
		elseif($this->imageType == 2) $retStr .= '; Limit to observations';
		elseif($this->imageType == 3) $retStr .= '; Limit to field images';
		return htmlspecialchars(trim($retStr,';, '));
	}
}
?>