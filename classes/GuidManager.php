<?php
include_once($SERVER_ROOT . '/classes/Manager.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');

class GuidManager extends Manager{

	private $silent = false;
	private $collid = 0;
	private $extensionArr = array();

	public function __construct($con = null){
		parent::__construct(null, 'write', $con);

		$this->extensionArr = array();
		$this->extensionArr['determination']['table'] = 'omoccurdeterminations';
		$this->extensionArr['determination']['pk'] = 'detid';
		$this->extensionArr['media']['table'] = 'media';
		$this->extensionArr['media']['pk'] = 'mediaID';
		$this->extensionArr['association']['table'] = 'omoccurassociations';
		$this->extensionArr['association']['pk'] = 'assocID';

	}

	public function __destruct(){
		parent::__destruct();
	}

	public function populateGuids(){
		set_time_limit(1000);

		$this->logOrEcho('Starting batch GUID processing (' . date('Y-m-d h:i:s A') . ")\n");

		//Populate Collection GUIDs
		$this->logOrEcho('Populating collection GUIDs (all collections by default)');
		$sql = 'SELECT collid FROM omcollections WHERE collectionguid IS NULL ';
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" WHERE collectionguid IS NULL AND collid = '.$r->collid;
				if(!$this->conn->query($insSql)){
					$this->logOrEcho('ERROR: ' . $this->conn->error, 1);
				}
				$recCnt++;
			}
			$rs->free();
		}
		$this->logOrEcho('Finished: ' . number_format($recCnt) . " collection records processed\n", 1);

		//Populate occurrence GUIDs
		$this->logOrEcho("Populating occurrence GUIDs\n");
		$sql = 'SELECT occid FROM omoccurrences WHERE recordID IS NULL ';
		if($this->collid) $sql .= 'AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE omoccurrences SET recordID = "'.$guid.'" WHERE (recordID IS NULL) AND (occid = '.$r->occid.')';
				if(!$this->conn->query($insSql)){
					$this->logOrEcho('ERROR: occur guids'.$this->conn->error, 1);
				}
				$recCnt++;
				if($recCnt%1000 === 0) $this->logOrEcho(number_format($recCnt) . ' records processed', 1);
			}
			$rs->free();
		}
		$this->logOrEcho('Finished: ' . number_format($recCnt) . " occurrence records processed\n", 1);

		$this->populateExtensionGuids($this->collid);

		$this->logOrEcho('GUID batch processing complete (' . date('Y-m-d h:i:s A') . ")\n");
	}

	private function populateExtensionGuids(){
		$useUuidFunction = true;
		$extArr = $this->getExtensionCounts();
		foreach($extArr as $name => $cnt){
			$tableName = $this->extensionArr[$name]['table'];
			$this->logOrEcho('Populating ' . $cnt . ' ' . $name . ' GUIDs');
			if(!$cnt){
				$this->logOrEcho('Skipped, nothing to do', 1);
				continue;
			}
			$runningCnt = 0;
			while($cnt > 0){
				$status = 0;
				if($useUuidFunction){
					$status = $this->populateExtensionGuidsAttempt1($tableName);
					if($status === false) $useUuidFunction = false;
				}
				if(!$useUuidFunction){
					$pkName = $this->extensionArr[$name]['pk'];
					$status = $this->populateExtensionGuidsAttempt2($tableName, $pkName);
				}
				if($status){
					$this->logOrEcho(number_format($status) . ' records processed', 1);
					$cnt -= $status;
					$runningCnt += $status;
				}
				else break;
			}
			$this->logOrEcho('Finished: ' . number_format($runningCnt) . ' total records processed (' . date('Y-m-d h:i:s A') . ')', 1);
		}
	}

	private function populateExtensionGuidsAttempt1($tableName){
		$status = 0;
		$sql = 'UPDATE ' . $tableName . ' SET recordID = UUID() WHERE recordID IS NULL LIMIT 100000';
		if($this->collid) $sql = 'UPDATE ' . $tableName . ' e INNER JOIN omoccurrences o ON e.occid = o.occid SET e.recordID = UUID() WHERE e.recordID IS NULL AND o.collid = ? LIMIT 100000';
		try{
			if($stmt = $this->conn->prepare($sql)){
				if($this->collid) $stmt->bind_param('i', $this->collid);
				$stmt->execute();
				if($stmt->error) $status = false;
				else $status = $stmt->affected_rows;
				$stmt->close();
			}
		}
		catch (Exception $e) {
			return false;
		}
		return $status;
	}

	private function populateExtensionGuidsAttempt2($tableName, $pkName){
		//Populate UUIDs using a function, due to db engine not supporting UUID function
		$sql = 'SELECT ' . $pkName . ' as pk FROM ' . $tableName . ' WHERE recordID IS NULL ';
		if($this->collid){
			$sql = 'SELECT e.' . $pkName . ' as pk FROM ' . $tableName . ' e INNER JOIN omoccurrences o ON e.occid = o.occid WHERE e.recordID IS NULL AND o.collid = ? ';
		}
		$sql .= 'LIMIT 5000';
		$pkArr = array();
		if($stmt = $this->conn->prepare($sql)){
			if($this->collid) $stmt->bind_param('i', $this->collid);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($pk);
			while($stmt->fetch()){
				$pkArr[] = $pk;
			}
			$stmt->close();
		}

		$recCnt = 0;
		foreach($pkArr as $key){
			$guid = UuidFactory::getUuidV4();
			$insSql = 'UPDATE ' . $tableName . ' SET recordID = "' . $guid . '" WHERE (recordID IS NULL) AND (' . $pkName . ' = ' . $key . ')';
			if(!$this->conn->query($insSql)){
				$this->errorMessage = $this->conn->error;
			}
			$recCnt++;
		}
		return $recCnt;
	}

	public function getCollectionCount(){
		$retCnt = 0;
		$sql = 'SELECT count(collid) as reccnt FROM omcollections WHERE collectionguid IS NULL ';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getOccurrenceCount(){
		$retCnt = 0;
		$sql = 'SELECT COUNT(occid) as reccnt FROM omoccurrences WHERE recordID IS NULL ';
		if($this->collid) $sql .= 'AND collid = ' . $this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getExtensionCounts(){
		$retArr = array();
		foreach($this->extensionArr as $name => $unitArr){
			$sql = 'SELECT COUNT(e.' . $unitArr['pk'] . ') as reccnt FROM ' . $unitArr['table'] . ' e WHERE e.recordID IS NULL';
			if($this->collid){
				$sql = 'SELECT COUNT(e.' . $unitArr['pk'] . ') as reccnt FROM ' . $unitArr['table'] . ' e INNER JOIN omoccurrences o ON e.occid = o.occid WHERE e.recordID IS NULL AND o.collid = ?';
			}
			if($stmt = $this->conn->prepare($sql)){
				$cnt = 0;
				if($this->collid) $stmt->bind_param('i', $this->collid);
				$stmt->execute();
				$stmt->bind_result($cnt);
				if($stmt->fetch()){
					$retArr[$name] = $cnt;
				}
				$stmt->close();
			}
		}
		return $retArr;
	}

	//Data functions
	public function getCollectionName(){
		$retStr = '';
		if($this->collid){
			$sql = 'SELECT CONCAT(collectionname," (",CONCAT_WS("-",institutioncode,collectioncode),")") as collname FROM omcollections WHERE collid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->collid);
				$stmt->execute();
				$stmt->bind_result($retStr);
				$stmt->fetch();
				$stmt->close();
			}
		}
		return $retStr;
	}

	//setters and getters
	public function setCollid($c){
		if(is_numeric($c)) $this->collid = $c;
	}
}
?>
