<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class OccurrenceExportSupport{

	private $conn;
	private $redactLocalities = true;
	private $rareReaderArr = array();
	private $delimiter = ',';
	private $charSetSource = '';
	private $charSetOut = '';
	private $zipFile = false;
	private $sqlWhere = '';
	private $conditionArr = array();
	private $taxonFilter;
	private $errorArr = array();

	public function __construct(){
		$this->conn = MySQLiConnectionFactory::getCon('readonly');

		if($GLOBALS['IS_ADMIN'] || array_key_exists('CollAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppAdmin', $GLOBALS['USER_RIGHTS']) || array_key_exists('RareSppReadAll', $GLOBALS['USER_RIGHTS'])){
			$this->redactLocalities = false;
		}
		if(array_key_exists('CollEditor', $GLOBALS['USER_RIGHTS'])){
			$this->rareReaderArr = $GLOBALS['USER_RIGHTS']['CollEditor'];
		}
		if(array_key_exists('RareSppReader', $GLOBALS['USER_RIGHTS'])){
			$this->rareReaderArr = array_unique(array_merge($this->rareReaderArr,$GLOBALS['USER_RIGHTS']['RareSppReader']));
		}

		//Character set
		$this->charSetSource = strtoupper($GLOBALS['CHARSET']);
		$this->charSetOut = $this->charSetSource;
	}

	public function __destruct(){
		if(!($this->conn === false)) $this->conn->close();
	}

	public function downloadChecklist(){
		$filePath = $this->getOutputFilePath();
		$fileName = $this->getOutputFileName();
		$contentDesc = 'Symbiota Checklist File';
		if($this->zipFile){
			//Create zip file, load data, then stream to user
			$zipArchive = null;
			if(class_exists('ZipArchive')){
				$zipArchive = new ZipArchive;
				$zipArchive->open($filePath.$fileName, ZipArchive::CREATE);
			}
			else{
				$this->errorArr[] = 'ERROR: Zip File creation not supported, see portal manager';
				$contentDesc = 'Output file ERROR: Zip File creation not supported';
			}
			if($zipArchive){
				$tempName = 'checklist';
				$tempPath = $filePath.$tempName.'_'.time();
				if($this->delimiter=="\t"){
					$tempPath .= '.tab';
					$tempName .= '.tab';
				}
				elseif($this->delimiter==','){
					$tempPath .= '.csv';
					$tempName .= '.csv';
				}
				else{
					$tempPath .= '.txt';
					$tempName .= '.txt';
				}
				$fh = fopen($tempPath, "w");
				$this->writeOutData($fh);
				fclose($fh);
				if(file_exists($tempPath)){
					$zipArchive->addFile($tempPath,$tempName);
					$zipArchive->close();
					unlink($tempPath);
				}
			}
		}
		else{
			//Create regular file and then streamed out to user
			$fh = fopen($filePath.$fileName, "w");
			$this->writeOutData($fh);
			fclose($fh);
		}
		//Send data file out
		ob_start();
		ob_clean();
		ob_end_flush();
		header('Content-Description: '.$contentDesc);
		header('Content-Type: '.$this->getContentType());
		header('Content-Disposition: attachment; filename='.$fileName);
		header('Content-Transfer-Encoding: '.$this->charSetOut);
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.filesize($filePath.$fileName));
		ob_clean();
		flush();
		readfile($filePath.$fileName);
		//Clean up
		if(file_exists($filePath.$fileName)) unlink($filePath.$fileName);
	}

	private function writeOutData($outstream){
		$recCnt = 0;
		if($outstream){
			$sql = $this->getSql();
			$result = $this->conn->query($sql,MYSQLI_USE_RESULT);
			if($result){
				$outputHeader = true;
				while($row = $result->fetch_assoc()){
					if($outputHeader){
						//Write column names out to file
						if($this->delimiter == ","){
							fputcsv($outstream, array_keys($row));
						}
						else{
							fwrite($outstream, implode($this->delimiter, array_keys($row))."\n");
						}
						$outputHeader = false;
					}
					$this->encodeArr($row);
					if($this->delimiter == ","){
						fputcsv($outstream, $row);
					}
					else{
						fwrite($outstream, implode($this->delimiter,$row)."\n");
					}
					$recCnt++;
				}
			}
			else{
				echo "Recordset is empty.\n";
			}
			if($result) $result->close();
		}
		return $recCnt;
	}

	private function getSql(){
		$sql = '';
		if($this->taxonFilter){
			$sql = 'SELECT DISTINCT ts.family, t.sciname AS scientificName, CONCAT_WS(" ",t.unitind1,t.unitname1) AS genus, '.
				'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet, t.unitind3 AS taxonRank, t.unitname3 AS infraSpecificEpithet, t.author AS scientificNameAuthorship '.
				'FROM omoccurrences o INNER JOIN taxstatus ts ON o.TidInterpreted = ts.Tid '.
				'INNER JOIN taxa t ON ts.TidAccepted = t.Tid ';
			$sql .= $this->setTableJoins($this->sqlWhere);
			$sql .= $this->sqlWhere.'AND t.RankId > 140 AND (ts.taxauthid = '.$this->taxonFilter.') ';
			if($this->redactLocalities){
				if($this->rareReaderArr){
					$sql .= 'AND (o.recordSecurity = 0 OR c.collid IN('.implode(',',$this->rareReaderArr).')) ';
				}
				else{
					$sql .= 'AND (o.recordSecurity = 0) ';
				}
			}
			$sql .= OccurrenceUtil::appendFullProtectionSQL();
			$sql .= 'ORDER BY ts.family, t.SciName ';
		}
		else{
			$sql = 'SELECT DISTINCT IFNULL(o.family,"not entered") AS family, o.sciname, CONCAT_WS(" ",t.unitind1,t.unitname1) AS genus, '.
				'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet, t.unitind3 AS taxonRank, t.unitname3 AS infraSpecificEpithet, t.author AS scientificNameAuthorship '.
				'FROM omoccurrences o LEFT JOIN taxa t ON o.tidinterpreted = t.tid LEFT JOIN taxstatus ts ON t.tid = ts.tid ';
			$sql .= $this->setTableJoins($this->sqlWhere);
			$sql .= $this->sqlWhere.'AND o.SciName NOT LIKE "%aceae" AND o.SciName NOT LIKE "%idea" AND o.SciName NOT IN ("Plantae","Polypodiophyta") ';
			if($this->redactLocalities){
				if($this->rareReaderArr){
					$sql .= 'AND (o.recordSecurity = 0 OR c.collid IN('.implode(',',$this->rareReaderArr).')) ';
				}
				else{
					$sql .= 'AND (o.recordSecurity = 0) ';
				}
			}
			$sql .= OccurrenceUtil::appendFullProtectionSQL();
			$sql .= 'ORDER BY IFNULL(IFNULL(ts.family, o.family),"not entered"), o.SciName ';
		}
		return $sql;
	}

	private function setTableJoins($sqlWhere){
		$sqlJoin = '';
		if($sqlWhere){
			if(strpos($sqlWhere,'e.taxauthid')) $sqlJoin .= 'INNER JOIN taxaenumtree e ON o.tidinterpreted = e.tid ';
			if(strpos($sqlWhere,'ctl.clid')) $sqlJoin .= 'INNER JOIN fmvouchers v ON o.occid = v.occid INNER JOIN fmchklsttaxalink ctl ON v.clTaxaID = ctl.clTaxaID ';
			if(strpos($sqlWhere,'p.lngLatPoint')) $sqlJoin .= 'INNER JOIN omoccurpoints p ON o.occid = p.occid ';
			if (strpos($sqlWhere, 'ds.datasetid')) $sqlJoin .= 'INNER JOIN omoccurdatasetlink ds ON o.occid = ds.occid ';
			if (strpos($sqlWhere, 'paleo.') || strpos($sqlWhere, 'early.myaStart')) $sqlJoin .= 'INNER JOIN omoccurpaleo paleo ON o.occid = paleo.occid ';
			if(strpos($sqlWhere, 'early.myaStart')){
				$sqlJoin .= 'JOIN omoccurpaleogts early ON paleo.earlyInterval = early.gtsterm ';
				$sqlJoin .= 'JOIN omoccurpaleogts late ON paleo.lateInterval = late.gtsterm ';
				$sqlJoin .= 'CROSS JOIN searchRange search ';
			}
		}
		return $sqlJoin;
	}

	private function getOutputFilePath(){
		$retStr = $GLOBALS['TEMP_DIR_ROOT'];
		if(substr($retStr,-1) != '/' && substr($retStr,-1) != "\\") $retStr .= '/';
		if(file_exists($retStr.'exports/')){
			$retStr .= 'exports/';
		}
		return $retStr;
	}

	private function getOutputFileName(){
		$retStr .= 'Checklist_' . time();
		//Set extension
		if($this->zipFile){
			$retStr .= ".zip";
		}
		elseif($this->delimiter=="\t"){
			$retStr .= ".tab";
		}
		elseif($this->delimiter==','){
			$retStr .= ".csv";
		}
		else{
			$retStr .= ".txt";
		}
		return $retStr;
	}

	private function getContentType(){
		if($this->zipFile){
			return 'application/zip; charset='.$this->charSetOut;
		}
		elseif($this->delimiter == 'comma' || $this->delimiter == ','){
			return 'text/csv; charset='.$this->charSetOut;
		}
		else{
			return 'text/html; charset='.$this->charSetOut;
		}
	}

	//Data retrieval support functions
	public function getCollectionMetadata($collid){
		$retArr = array();
		if(is_numeric($collid)){
			$sql = 'SELECT institutioncode, collectioncode, collectionname, managementtype FROM omcollections WHERE collid = '.$collid;
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$retArr['instcode'] = $r->institutioncode;
				$retArr['collcode'] = $r->collectioncode;
				$retArr['collname'] = $r->collectionname;
				$retArr['manatype'] = $r->managementtype;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function getProcessingStatusList($collid = 0){
		$psArr = array();
		$sql = 'SELECT DISTINCT processingstatus FROM omoccurrences ';
		if($collid){
			$sql .= 'WHERE collid = '.$collid;
		}
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			if($r->processingstatus) $psArr[] = $r->processingstatus;
		}
		$rs->free();
		//Special sort
		$templateArr = array('unprocessed','unprocessed-nlp','pending duplicate','stage 1','stage 2','stage 3','pending review','reviewed');
		//Get all active processing statuses and then merge all extra statuses that may exists for one reason or another
		return array_merge(array_intersect($templateArr,$psArr),array_diff($psArr,$templateArr));
	}

	public function getAttributeTraits($collid = ''){
		$retArr = array();
		$sql = 'SELECT DISTINCT t.traitid, t.traitname, s.stateid, s.statename '.
			'FROM tmtraits t INNER JOIN tmstates s ON t.traitid = s.traitid '.
			'INNER JOIN tmattributes a ON s.stateid = a.stateid '.
			'INNER JOIN omoccurrences o ON a.occid = o.occid ';
		if($collid) $sql .= 'WHERE o.collid = '.$collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->traitid]['name'] = $r->traitname;
			$retArr[$r->traitid]['state'][$r->stateid] = $r->statename;
		}
		$rs->free();
		return $retArr;
	}

	//General setter, getters, and other configurations
	public function setSqlWhere($sqlStr){
		$this->sqlWhere = $sqlStr;
	}

	public function setDelimiter($d){
		if($d == 'tab' || $d == "\t"){
			$this->delimiter = "\t";
		}
		elseif($d == 'csv' || $d == 'comma' || $d == ','){
			$this->delimiter = ",";
		}
		else{
			$this->delimiter = $d;
		}
	}

	public function setCharSetOut($cs){
		$cs = strtoupper($cs);
		if($cs == 'ISO-8859-1' || $cs == 'UTF-8'){
			$this->charSetOut = $cs;
		}
	}

	public function setZipFile($c){
		$this->zipFile = $c;
	}

	public function getErrorArr(){
		return $this->errorArr;
	}

	public function setRedactLocalities($cond){
		if($cond == 0 || $cond === false){
			$this->redactLocalities = false;
		}
	}

	public function setTaxonFilter($filter){
		if(is_numeric($filter)){
			$this->taxonFilter = $filter;
		}
	}

	//Misc functions
	private function encodeArr(&$inArr){
		if($this->charSetSource && $this->charSetOut != $this->charSetSource){
			foreach($inArr as $k => $v){
				$inArr[$k] = $this->encodeStr($v);
			}
		}
	}

	private function encodeStr($inStr){
		$retStr = $inStr;
		if($retStr){
			if($this->charSetOut && $this->charSetOut != $this->charSetSource){
				$retStr = mb_convert_encoding($retStr, $this->charSetOut, mb_detect_encoding($retStr, 'UTF-8,ISO-8859-1,ISO-8859-15'));
			}
		}
		return $retStr;
	}

	private function cleanInStr($inStr){
		$retStr = trim($inStr);
		$retStr = preg_replace('/\s\s+/', ' ',$retStr);
		$retStr = $this->conn->real_escape_string($retStr);
		return $retStr;
	}
}
?>
