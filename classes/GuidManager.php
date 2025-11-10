<?php
include_once($SERVER_ROOT . '/config/dbconnection.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');

class GuidManager {

	private $silent = 0;
	private $conn;
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
	}

	public function __destruct(){
		if($this->destructConn && !($this->conn === null)){
			$this->conn->close();
			$this->conn = null;
		}
	}

	public function populateGuids($collId = 0){
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
		if($collId) $sql .= 'AND collid = '.$collId;
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

		//Populate determination GUIDs
		$this->echoStr("Populating determination GUIDs\n");
		$sql = 'SELECT d.detid FROM omoccurdeterminations d ';
		if($collId) $sql .= 'INNER JOIN omoccurrences o ON d.occid = o.occid ';
		$sql .= 'WHERE d.recordID IS NULL ';
		if($collId) $sql .= 'AND o.collid = '.$collId;
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE omoccurdeterminations SET recordID = "'.$guid.'" WHERE (recordID IS NULL) AND (detid = '.$r->detid.')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: det guids '.$this->conn->error);
				}
				$recCnt++;
				if($recCnt%1000 === 0) $this->echoStr($recCnt.' records processed');
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt determination records processed\n");

		//Populate image GUIDs
		$this->echoStr("Populating image GUIDs\n");
		$sql = 'SELECT m.mediaID FROM media m ';
		if($collId) $sql .= 'INNER JOIN omoccurrences o ON m.occid = o.occid ';
		$sql .= 'WHERE m.recordID IS NULL ';
		if($collId) $sql .= 'AND o.collid = '.$collId;
		$rs = $this->conn->query($sql);
		$recCnt = 0;
		if($rs->num_rows){
			while($r = $rs->fetch_object()){
				$guid = UuidFactory::getUuidV4();
				$insSql = 'UPDATE media SET recordID = "'.$guid.'" WHERE (recordID IS NULL) AND (mediaID = ' . $r->mediaID .')';
				if(!$this->conn->query($insSql)){
					$this->echoStr('ERROR: image guids; '.$this->conn->error);
				}
				$recCnt++;
				if($recCnt%1000 === 0) $this->echoStr($recCnt.' records processed');
			}
			$rs->free();
		}
		$this->echoStr("Finished: $recCnt image records processed\n");

		$this->echoStr("GUID batch processing complete (".date('Y-m-d h:i:s A').")\n");
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

	public function getOccurrenceCount($collId = 0){
		$retCnt = 0;
		$sql = 'SELECT COUNT(occid) as reccnt FROM omoccurrences WHERE recordID IS NULL ';
		if($collId) $sql .= 'AND collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getDeterminationCount($collId = 0){
		$retCnt = 0;
		$sql = 'SELECT COUNT(d.detid) as reccnt FROM omoccurdeterminations d ';
		if($collId) $sql .= 'INNER JOIN omoccurrences o ON d.occid = o.occid ';
		$sql .= 'WHERE d.recordID IS NULL ';
		if($collId) $sql .= 'AND o.collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getImageCount($collId = 0){
		$retCnt = 0;
		$sql = 'SELECT COUNT(m.mediaID) as reccnt FROM media m ';
		if($collId) $sql .= 'INNER JOIN omoccurrences o ON m.occid = o.occid ';
		$sql .= 'WHERE m.recordID IS NULL and m.mediaType = "image"';
		if($collId) $sql .= 'AND o.collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retCnt = $r->reccnt;
		}
		$rs->free();
		return $retCnt;
	}

	public function getCollectionName($collId){
		$retStr = '';
		$sql = 'SELECT CONCAT(collectionname," (",CONCAT_WS("-",institutioncode,collectioncode),")") as collname FROM omcollections WHERE collid = '.$collId;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retStr = $r->collname;
		}
		$rs->free();
		return $retStr;
	}

	public function setSilent($c){
		$this->silent = $c;
	}

	public function getSilent(){
		return $this->silent;
	}

	private function echoStr($str){
		if(!$this->silent){
			echo '<li>'.$str.'</li>';
			ob_flush();
			flush();
		}
	}
}
?>
