<?php
include_once($SERVER_ROOT.'/config/dbconnection.php');

class IgsnManager{

	private $conn;

	private $errorStr;

 	public function __construct(){
 		$this->conn = MySQLiConnectionFactory::getCon("write");
 	}

 	public function __destruct(){
		if($this->conn) $this->conn->close();
	}

	public function getIgsnTaskReport(){
		$this->setNullNeonIdentifiers();
		$retArr = array();
		$sql = 'SELECT c.collid, CONCAT_WS("-",c.institutioncode,c.collectioncode) as collcode, c.collectionname, count(o.occid) as cnt
			FROM omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid
			INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE c.institutionCode = "NEON" AND c.collid NOT IN(81,84,93) AND o.occurrenceId IS NULL AND s.errorMessage IS NULL AND s.sampleReceived = 1
			GROUP BY c.collid';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]['collcode'] = $r->collcode;
			$retArr[$r->collid]['collname'] = $r->collectionname;
			$retArr[$r->collid]['cnt'] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	private function setNullNeonIdentifiers(){
		$sql = 'UPDATE omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid '.
			'SET o.catalognumber = o.occurrenceID '.
			'WHERE o.occurrenceID LIKE "NEON%" AND o.catalognumber IS NULL';
		$this->conn->query($sql);
	}

	//Functions for coordinating IGSN with central NEON system
	public function getIgsnSynchronizationReport(){
		$retArr = array();
		$sql = 'SELECT IFNULL(s.igsnPushedToNEON,"x") as igsnPushedToNEON, COUNT(s.samplePK) as cnt
			FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE o.occurrenceID LIKE "NEON%" AND o.collid NOT IN(81,84,93) GROUP BY s.igsnPushedToNEON';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$code = $r->igsnPushedToNEON;
			if(is_numeric($code) && $code > 9 && $code < 99) $code = 10;
			$retArr[$code] = $r->cnt;
		}
		$rs->free();
		return $retArr;
	}

	public function synchronizeIgsn($recTarget, $startIndex, $limit, $resetSession){
		/* Synchronization codes
		 *   null = unchecked (also due to data return error)
		 *   0 = Unsynchronized
		 *   100 = Unsynchronized within current session
		 *   1 = IGSN succesfully synchronized
		 *   2 = IGSNs mismatch
		 *   3 = unchecked but submitted to NEON
		 *   10 to 99 = data return error, amount above the value of 10 indicates the number of times harvest fails
		 */
		set_time_limit(3600);
		echo '<li>Starting to synchronize IGSNs</li>';
		flush();
		ob_flush();
		if($resetSession) $this->resetSession();
		$apiUrlBase = 'https://data.neonscience.org/api/v0/samples/view?';
		$neonApiKey = (isset($GLOBALS['NEON_API_KEY'])?$GLOBALS['NEON_API_KEY']:'');
		$sql = 'SELECT o.occid, o.occurrenceID, s.sampleCode, s.sampleUuid, s.sampleID, s.sampleClass, s.igsnPushedToNEON
			FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid
			WHERE (o.occurrenceID LIKE "NEON%") AND o.collid NOT IN(81,84,93) ';
		if($recTarget == 'unsynchronized'){
			$sql .= 'AND (s.igsnPushedToNEON = 0) ';
		}
		else{
			//$recTarget == unchecked
			$sql .= 'AND (s.igsnPushedToNEON = 3) ';
		}
		if($startIndex) $sql .= 'AND (o.occurrenceID > "'.$this->cleanInStr($startIndex).'") ';
		$sql .= 'ORDER BY o.occurrenceID ';
		if(!is_numeric($limit)) $limit = 1000;
		$sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		$totalCnt = 0;
		$syncCnt = 0;
		$mismatchCnt = 0;
		$unsyncCnt = 0;
		$errorCnt = 0;
		$finalIgsn = '';
		while($r = $rs->fetch_object()){
			$url = $apiUrlBase;
			if($r->sampleCode) $url .= 'barcode='.$r->sampleCode;
			elseif($r->sampleUuid) $url .= 'sampleUuid='.$r->sampleUuid;
			elseif($r->sampleID && $r->sampleClass) $url .= 'sampleTag='.$r->sampleID.'&sampleClass='.$r->sampleClass;
			else{
				echo '<li>ERROR unable to build NEON API url ('.$r->occid.')</li>';
				continue;
			}
			if($neonApiKey) $url .= '&apiToken='.$neonApiKey;
			$igsnPushedToNEON = '';
			if($json = @file_get_contents($url)){
				$resultArr = json_decode($json, true);
				if(!isset($resultArr['error']) && isset($resultArr['data']['sampleViews'])){
					foreach($resultArr['data']['sampleViews'] as $sampleViewArr){
						if(isset($sampleViewArr['archiveGuid']) && $sampleViewArr['archiveGuid']){
							if($sampleViewArr['archiveGuid'] == $r->occurrenceID){
								$igsnPushedToNEON = 1;
								$syncCnt++;
							}
							else{
								$igsnPushedToNEON = 2;
								$mismatchCnt++;
							}
						}
						else{
							$unsyncCnt++;
							if($r->igsnPushedToNEON == 0) $igsnPushedToNEON = 100;
							else $igsnPushedToNEON = 0;
						}
					}
				}
				else $igsnPushedToNEON = 10;
			}
			else $igsnPushedToNEON = 10;
			if($igsnPushedToNEON == 10){
				$errorCnt++;
				if($r->igsnPushedToNEON > 9 && $r->igsnPushedToNEON < 99) $igsnPushedToNEON = ++$r->igsnPushedToNEON;
				echo 'Data return error: '.$url.'<br>';
			}
			$this->updateIgsnStatus($igsnPushedToNEON, array($r->occid));
			$totalCnt++;
			if($totalCnt%100 == 0){
				echo '<li style="margin-left: 15px">'.$totalCnt.' checked</li>';
				flush();
				ob_flush();
			}
			$finalIgsn = $r->occurrenceID;
		}
		$rs->free();
		echo '<li>Total checked: '.$totalCnt.', Synchronized: '.$syncCnt.', Not in NEON: '.$unsyncCnt.', Mismatched: '.$mismatchCnt.', API errors: '.$errorCnt.'</li>';
		return $finalIgsn;
	}

	private function updateIgsnStatus($igsnPushedToNEON, $occidArr, $cond = ''){
		if(is_numeric($igsnPushedToNEON) && $occidArr){
			$cnt = count($occidArr);
			$sqlFrag = trim(str_repeat('?,', $cnt), ',');
			$bindStr = 's' . str_repeat('i', $cnt);
			$sql = 'UPDATE NeonSample SET igsnPushedToNEON = ? WHERE occid IN('.$sqlFrag.') ';
			if($cond) $sql .= $cond;
			if($stmt = $this->conn->prepare($sql)){
				if($stmt->bind_param($bindStr, $igsnPushedToNEON, ...$occidArr)){
					$stmt->execute();
					if($stmt->error) $this->errorStr = '<li>ERROR updating igsnPushedToNEON field: '.$stmt->error.'</li>';
					elseif(!$stmt->affected_rows) $this->errorStr = '<li>Unable to update igsnPushedToNEON</li>';
					$stmt->close();
				}
				else $this->errorStr = '<li>ERROR updating igsnPushedToNEON: '.$this->conn->error.'</li>';
			}
			else $this->errorStr = '<li>ERROR preparing update of igsnPushedToNEON: '.$this->conn->error.'</li>';
		}
	}

	private function resetSession(){
		$sql = 'UPDATE NeonSample SET igsnPushedToNEON = 0 WHERE igsnPushedToNEON = 100';
		$this->conn->query($sql);
	}

	public function exportReport($recTarget, $startIndex, $limit, $markAsSubmitted){
		header ('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		$fieldMap = array('sampleID' => 's.sampleID', 'sampleCode' => 's.sampleCode', 'sampleFate' => '"archived" AS sampleFate',
			'sampleClass' => 's.sampleClass', 'archiveGuid' => 'o.occurrenceID', 'catalogueNumber' => 'o.catalogNumber',
			'externalURLs' => 'CONCAT("https://biorepo.neonscience.org/portal/collections/individual/index.php?occid=", o.occid) AS referenceUrl',
			'collectionCode' => 'CONCAT_WS(":", c.institutionCode, c.collectionCode) as collectionCode',
			'archiveStartDate' => '"" AS archiveStartDate', 'archiveMedium' => 's.archiveMedium', 'storageTemperature' => '"" AS storageTemperature',
			'scientificName' => '"" AS scientificName', 'scientificNameAuthorship' => '"" AS scientificNameAuthorship', 'identificationQualifier' => '"" AS identificationQualifier',
			'sex' => '"" AS sex', 'reproductiveCondition' => '"" AS reproductiveCondition', 'lifeStage' => '"" AS lifeStage', 'identifiedBy' => '"" AS identifiedBy',
			'accessionNumber' => '"" AS accessionNumber', 'remarks' => '"" AS remarks', 'archiveLaboratoryName' => '"" AS archiveLaboratoryName'
		);
		$sql = 'SELECT o.occid, '.implode(', ',$fieldMap).'
			FROM omoccurrences o INNER JOIN NeonSample s ON o.occid = s.occid INNER JOIN omcollections c ON c.collid = o.collid
			WHERE (o.occurrenceID LIKE "NEON%") AND o.collid NOT IN(81,84,93) ';
		if($startIndex) $sql .= 'AND (o.occurrenceID > "'.$this->cleanInStr($startIndex).'") ';
		if($recTarget == 'unsynchronized'){
			$sql .= 'AND (s.igsnPushedToNEON = 0) ';
		}
		elseif($recTarget == 'unchecked'){
			$sql .= 'AND (s.igsnPushedToNEON = 3) ';
		}
		else{
			//$recTarget == notsubmitted
			$sql .= 'AND (s.igsnPushedToNEON IS NULL) ';
		}
		$sql .= 'ORDER BY o.occurrenceID ';
		if(!is_numeric($limit)) $limit = 1000;
		$sql .= 'LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		if($rs->num_rows){
			$occidArr = array();
			$fileName = 'biorepoIGSNReport_'.date('Y-m-d').'.csv';
			header ('Content-Type: text/csv');
			header ('Content-Disposition: attachment; filename="'.$fileName.'"');
			$out = fopen('php://output', 'w');
			fputcsv($out, array_keys($fieldMap));
			while($row = $rs->fetch_assoc()){
				if($markAsSubmitted) $occidArr[] = $row['occid'];
				unset($row['occid']);
				$this->encodeArr($row);
				fputcsv($out, $row);
			}
			$rs->free();
			fclose($out);
			if($occidArr) $this->updateIgsnStatus(3, $occidArr, 'AND (igsnPushedToNEON IS NULL) ');
			return true;
		}
		return false;
	}

	//Setters and getters
	public function getErrorStr(){
		return $this->errorStr;
	}

	//Misc functions
	private function encodeArr(&$inArr){
		foreach($inArr as $k => $v){
			$inArr[$k] = $this->encodeStr($v);
		}
	}

	private function encodeStr($inStr){
		$retStr = $inStr;
		if($inStr){
			if(mb_detect_encoding($inStr,'UTF-8,ISO-8859-1') == 'UTF-8') $retStr = utf8_decode($inStr);
		}
		return $retStr;
	}

	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>