<?php
include_once($SERVER_ROOT . '/config/dbconnection.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');

class GuidManager {

	private $silent = false;
	private $conn;
	private $collid = 0;
	private $extensionArr = array();
	private $destructConn = true;

	public function __construct($con = null){
		if($con){
			//Inherits connection from another class
			$this->conn = $con;
			$this->destructConn = false;
		}
		else{
			$this->conn = MySQLiConnectionFactory::getCon("write");
		}
		$this->extensionArr = array();
		$this->extensionArr['determination']['table'] = 'omoccurdeterminations';
		$this->extensionArr['determination']['pk'] = 'detid';
		$this->extensionArr['media']['table'] = 'media';
		$this->extensionArr['media']['pk'] = 'mediaID';
		$this->extensionArr['association']['table'] = 'omoccurassociations';
		$this->extensionArr['association']['pk'] = 'assocID';

	}

	public function __destruct(){
		if($this->destructConn && !($this->conn === null)){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function populateGuids(){
		set_time_limit(1000);

		$this->echoStr("Starting batch GUID processing (".date('Y-m-d h:i:s A').")\n");

		//Populate Collection GUIDs
		$this->echoStr("Populating collection GUIDs (all collections by default)");
		$sql = 'SELECT collid FROM omcollections WHERE collectionguid IS NULL ';
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE omcollections SET collectionguid = "'.$guid.'" WHERE collectionguid IS NULL AND collid = '.$r->collid;
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: '.$this->conn->error);
				}
				$recCnt++;
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt collection records processed\n");

		//Populate occurrence GUIDs
		$this->echoStr("Populating occurrence GUIDs\n");
		$sql = 'SELECT occid FROM omoccurrences WHERE recordID IS NULL ';
		if($this->collid) $sql .= 'AND collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE omoccurrences SET recordID = "'.$guid.'" WHERE (recordID IS NULL) AND (occid = '.$r->occid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: occur guids'.$this->conn->error);
				}
				$recCnt++;
				if($recCnt%1000 === 0) $this->echoStr($recCnt.' records processed');
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt occurrence records processed\n");

		$this->populateExtensionGuids($this->collid);

		$this->echoStr("GUID batch processing complete (".date('Y-m-d h:i:s A').")\n");
	}

	private function populateExtensionGuids(){
		foreach($this->extensionArr as $name => $unitArr){
			$this->echoStr('Populating ' . $name . ' GUIDs');
			$sql = 'SELECT e.' . $unitArr['pk'] . ' as pk FROM ' . $unitArr['table'] . ' e WHERE e.recordID IS NULL';
			if($this->collid){
				$sql = 'SELECT e.' . $unitArr['pk'] . ' as pk FROM ' . $unitArr['table'] . ' e INNER JOIN omoccurrences o ON e.occid = o.occid WHERE e.recordID IS NULL AND o.collid = ?';
			}
			if($stmt = $this->conn->prepare($sql)){
				if($this->collid) $stmt->bind_param('i', $this->collid);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($pk);
				$recCnt = 0;
				while($stmt->fetch()){
					$guid = UuidFactory::getUuidV4();
					$insSql = 'UPDATE ' . $unitArr['table'] . ' SET recordID = "' . $guid . '" WHERE (recordID IS NULL) AND (' . $unitArr['pk'] . ' = ' . $pk . ')';
					if(!$this->conn->query($insSql)){
						$this->echoStr('ERROR assigning ' . $name . ' guids: ' . $this->conn->error);
					}
					$recCnt++;
					if($recCnt%1000 === 0) $this->echoStr($recCnt.' records processed');
				}
				$stmt->close();
			}
			$this->echoStr('Finished: ' . $recCnt . ' ' . $name . ' records processed');
		}
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

	public function setSilent($bool){
		if($bool) $this->silent = true;
	}

	public function getSilent(){
		return $this->silent;
	}

	//misc functions
	private function echoStr($str){
		if(!$this->silent){
			echo '<li>'.$str.'</li>';
			ob_flush();
			flush();
		}
	}
}
?>
