<?php
include_once($SERVER_ROOT . '/classes/data/DataCore.php');
include_once($SERVER_ROOT . '/classes/utilities/OccurrenceUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');

class OmDatasets extends DataCore{

	protected $datasetID = 0;
	private $validUserRoles = array('DatasetAdmin', 'DatasetEditor', 'DatasetReader');
	private $userRoleArr = array('DatasetAdmin' => 'DS_ADMIN', 'DatasetEditor' => 'DS_EDITOR', 'DatasetReader' => 'DS_READER', 'owner' => 'DS_OWNER');

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getDatasetArr($conditionArr = null, $orderByArr = null){
		$this->setDatasetMap();
		return $this->getRecordArr('omoccurdatasets', $conditionArr, $orderByArr);
	}

	public function insertDataset($inputArr){
		$this->setDatasetMap();
		if(empty($inputArr['uid'])){
			$inputArr['uid'] = $GLOBALS['SYMB_UID'];
		}
		$inputArr['recordID'] = UuidFactory::getUuidV4();
		if($this->insertRecord('omoccurdatasets', $inputArr)){
			$this->datasetID = $this->primaryKey;
			return true;
		}
		return false;
	}

	public function updateDataset($inputArr){
		if(!$this->datasetID){
			$this->errorMessage = 'ID_NOT_SET';
			return false;
		}
		$this->setDatasetMap();
		$pkArr = array('datasetID' => $this->datasetID);
		return $this->updateRecord('omoccurdatasets', $pkArr, $inputArr);
	}

	public function deleteDataset($datasetID = null){
		if(!$datasetID){
			if($this->datasetID){
				$datasetID = $this->datasetID;
			}
			else{
				$this->errorMessage = 'ID_NOT_SET';
				return false;
			}
		}
		$this->setDatasetMap();
		$status = false;
		$status = $this->deleteRecord('omoccurdatasets', $this->datasetID);
		if($status){
			$this->deleteUserRole();
		}
		return $status;
	}

	private function setDatasetMap(){
		$this->fieldMap = array('datasetID' => 'pk', 'datasetName' => 's', 'bibliographicCitation' => 's', 'category' => 's', 'isPublic' => 'i', 'parentDatasetID' => 'i',
			'description' => 's', 'datasetIdentifier' => 's', 'notes' => 's', 'dynamicProperties' => 's', 'sortSequence' => 'i', 'uid' => 'i', 'recordID' => 's',
			'initialTimestamp' => 'x');
	}

	//Occurrence functions
	public function getOccurrences($conditionArr = null){
		if(!$conditionArr) $conditionArr['datasetID'] = $this->datasetID;
		$this->setDatasetOccurrenceMap();
		return $this->getRecordArr('omoccurdatasetlink', $conditionArr);
	}

	public function getOccurrenceDetails($datasetId, $pageNumber = 1, $retLimit = 500) {
		$retArr = array();
		if ($datasetId) {
			$params[] = $datasetId;
			$typeStr = 'i';
			$sql = 'SELECT o.occid, o.catalognumber, o.occurrenceid ,o.othercatalognumbers,
				o.sciname, o.family, o.recordedby, o.recordnumber, o.eventdate,
				o.country, o.stateprovince, o.county, o.locality, o.decimallatitude, o.decimallongitude, dl.notes
				FROM omoccurrences o INNER JOIN omoccurdatasetlink dl ON o.occid = dl.occid
				WHERE dl.datasetid = ? ';
			$sql .= OccurrenceUtil::appendFullProtectionSQL();
			if($retLimit){
				$sql .= 'LIMIT ?, ?';
				$params[] = ($pageNumber - 1) * $retLimit;
				$params[] = $retLimit;
				$typeStr .= 'ii';
			}
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($typeStr, ...$params);
				$stmt->execute();
				$rs = $stmt->get_result();
				while ($r = $rs->fetch_object()) {
					$catNum = '';
					if ($r->catalognumber) $catNum = $r->catalognumber;
					elseif ($r->occurrenceid) $catNum = $r->occurrenceid;
					elseif ($r->othercatalognumbers) $catNum = $r->othercatalognumbers;
					$retArr[$r->occid]['catnum'] = $catNum;
					$sciname = $r->sciname;
					if ($r->family) $sciname .= ' (' . $r->family . ')';
					$retArr[$r->occid]['sciname'] = $sciname;
					$collStr = $r->recordedby . ' ' . $r->recordnumber;
					if ($r->eventdate) $collStr .= ' [' . $r->eventdate . ']';
					$retArr[$r->occid]['coll'] = $collStr;
					$retArr[$r->occid]['loc'] = trim($r->country . ', ' . $r->stateprovince . ', ' . $r->county . ', ' . $r->locality, ', ');
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function getOccurrenceCount() {
		$returnVal = 0;
		if ($this->datasetID) {
			$sql = 'SELECT COUNT(o.occid) as cnt FROM omoccurrences o INNER JOIN omoccurdatasetlink dl ON o.occid = dl.occid WHERE dl.datasetid = ? ';
			$sql .= OccurrenceUtil::appendFullProtectionSQL();
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->datasetID);
				$stmt->execute();
				$stmt->bind_result($returnVal);
				$stmt->fetch();
				$stmt->close();
			}
		}
		return $returnVal;
	}

	public function insertOccurrences($input){
		//Input: single occid, or an array of multiple occids
		if(!$this->datasetID){
			$this->errorMessage = 'ID_NOT_SET';
			return false;
		}
		$this->setDatasetOccurrenceMap();
		if(is_numeric($input)){
			$inputArr = array('datasetID' => $this->datasetID, 'occid' => $input);
			return $this->insertRecord('omoccurdatasetlink', $inputArr);
		}
		elseif(is_array($input)){
			$status = false;
			foreach($input as $occid){
				$inputArr = array('datasetID' => $this->datasetID, 'occid' => $occid);
				if($this->insertRecord('omoccurdatasetlink', $inputArr)) $status = true;
			}
			return $status;
		}
	}

	protected function copyOccurrences($targetDatasetID){
		$status = false;
		$sql = 'INSERT INTO omoccurdatasetlink(occid, datasetid, notes) SELECT occid, ?, notes FROM omoccurdatasetlink WHERE datasetid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $targetDatasetID, $this->datasetID);
			$stmt->execute();
			if($stmt->affectedRows) $status = true;
			$stmt->fetch();
			$stmt->close();
		}
		return $status;
	}

	public function transferOccurrences($targetDatasetID){
		$status = false;
		$sql = 'UPDATE IGNORE omoccurdatasetlink SET datasetID = ? WHERE datasetid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('ii', $targetDatasetID, $this->datasetID);
			$stmt->execute();
			if($stmt->affectedRows) $status = true;
			$stmt->fetch();
			$stmt->close();
		}
		return $status;
	}

	public function deleteOccurrences($input = null){
		//Input: single occid, or an array of multiple occids
		if(is_array($input)){
			$status = false;
			$sql = 'DELETE IGNORE FROM omoccurdatasetlink WHERE occid IN(' . trim(str_repeat('?,', count($input)), ',') . ') AND datasetID = ?';
			$typeStr = 'i' . str_repeat('i', count($input));
			$input[] = $this->datasetID;
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($typeStr, ...$input);
				$stmt->execute();
				if($stmt->affected_rows) $status = true;
				elseif($stmt->error) $this->errorMessage = $stmt->error;
				$stmt->close();
			}
			return $status;
		}
		elseif(is_numeric($input)){
			$inputArr = array('datasetID' => $this->datasetID, 'occid' => $input);
			return $this->deleteRecord('omoccurdatasetlink', $inputArr);
		}
		elseif($this->datasetID){
			$inputArr = array('datasetID' => $this->datasetID);
			return $this->deleteRecord('omoccurdatasetlink', $inputArr);
		}
		$this->errorMessage = 'ID_NOT_SET';
		return false;
	}

	private function setDatasetOccurrenceMap(){
		$this->fieldMap = array('datasetID' => 'pk', 'occid' => 'pk', 'notes' => 's', 'initialTimestamp' => 'x');
	}

	//User role functions
	public function getUserRoles($datasetId = 0, $uid = 0) {
		$retArr = array();
		$typeStr = '';
		$paramArr = array();
		$sql = 'SELECT r.tablePK, r.uid, r.role, CONCAT_WS(", ",u.lastname,u.firstname) as username
			FROM userroles r INNER JOIN users u ON r.uid = u.uid
			WHERE r.role IN("' . implode('","', array_keys($this->userRoleArr)) . '") AND (r.tablename = "omoccurdatasets") ';
		if($uid){
			$paramArr[] = $uid;
			$typeStr .= 'i';
			$sql .= 'AND (r.uid = ?) ';
		}
		if($datasetId){
			$paramArr[] = $datasetId;
			$typeStr .= 'i';
			$sql .= 'AND (r.tablepk = ?)';
		}
		if($stmt = $this->conn->prepare($sql)){
			if($typeStr) $stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			$rs = $stmt->get_result();
			while ($r = $rs->fetch_object()) {
				$retArr[$r->tablePK][$r->uid]['name'] = $r->username;
				$retArr[$r->tablePK][$r->uid]['roles'][$r->role] = $this->translateUserRole($r->role);
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	protected function translateUserRole($dbValue){
		global $LANG;
		if(isset($LANG[$this->userRoleArr[$dbValue]])) return $LANG[$this->userRoleArr[$dbValue]];
		return $dbValue;
	}

	public function insertUserRole($uid, $role) {
		$status = false;
		if (!is_numeric($uid)) return false;
		if(!array_key_exists($role, $this->userRoleArr)) return false;
		$uidAssignedBy = $GLOBALS['SYMB_UID'];
		$sql = 'INSERT IGNORE INTO userroles(uid, role, tableName, tablePK, uidAssignedBy) VALUES(?, ?, "omoccurdatasets", ?, ?)';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('isii', $uid, $role, $this->datasetID, $uidAssignedBy);
			$stmt->execute();
			if($stmt->affected_rows) $status = true;
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		return $status;
	}

	public function deleteUserRole($uid = 0, $role = 0) {
		$status = false;
		$typeStr = 'i';
		$paramArr = array($this->datasetID);
		$sql = 'DELETE FROM userroles WHERE (tablename = "omoccurdatasets") AND (tablepk = ?) ';
		if($uid){
			$typeStr .= 'i';
			$paramArr[] = $uid;
			$sql .= ' AND (uid = ?) ';
		}
		if($role){
			if(!array_key_exists($role, $this->userRoleArr)) return false;
			$typeStr .= 's';
			$paramArr[] = $role;
			$sql .= 'AND (role = ?) ';
		}
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			if($stmt->affected_rows) $status = true;
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		return $status;
	}

	//General data retrival functions - TODO: need to create and merge into shared data class functions
	public function getUserList($term) {
		$retArr = array();
		if($term){
			$term = '%' . $term . '%';
			$sql = 'SELECT uid, CONCAT(CONCAT_WS(", ", lastname, firstname)," - ", username," [#", uid,"]") AS username
				FROM users WHERE lastname LIKE ? OR username LIKE ?
				ORDER BY lastname, firstname';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('ss', $term, $term);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_object()) {
					$retArr[] = array('id' => $r->uid, 'label' => $r->username);
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function getCollName($collid) {
		$collName = '';
		if ($collid && is_numeric($collid)) {
			$sql = 'SELECT institutioncode, collectioncode, collectionname, colltype FROM omcollections WHERE collid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $collid);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_object()) {
					$collName = $r->collectionname . ' (' . $r->institutioncode . ($r->collectionname ? ':' . $r->collectionname : '') . ')';
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $collName;
	}

	//Setters and getters
	public function getDatasetID(){
		return $this->datasetID;
	}

	public function setDatasetID($id){
		if(is_numeric($id)) $this->datasetID = $id;
	}
}
?>
