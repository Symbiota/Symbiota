<?php
include_once('UuidFactory.php');

class OmOccurAssociations{

	private $conn;
	private $assocID = null;
	private $occid = null;
	private $schemaMap = array();
	private $parameterArr = array();
	private $typeStr = '';
	private $errorMessage = '';

	public function __construct($conn){
		$this->conn = $conn;
		$this->schemaMap = array('occidAssociate' => 'i', 'relationship' => 's', 'relationshipID' => 's', 'subType' => 's', 'identifier' => 's', 'basisOfRecord' => 's', 'resourceUrl' => 's',
			'verbatimSciname' => 's', 'tid' => 'i', 'locationOnHost' => 's', 'conditionOfAssociate' => 's', 'establishedDate' => 's', 'imageMapJSON' => 's', 'dynamicProperties' => 's',
			'notes' => 's', 'accordingTo' => 's', 'sourceIdentifier' => 's', 'recordID' => 's', 'createdUid' => 'i', 'modifiedTimestamp' => 's',
			'modifiedUid' => 'i');
	}

	public function __destruct(){
	}

	public function getAssociationArr(){
		$retArr = array();
		$uidArr = array();
		$sql = 'SELECT assocID, occid, '.implode(', ', array_keys($this->schemaMap)).', initialTimestamp FROM omoccurassociations WHERE ';
		if($this->assocID) $sql .= '(assocID = '.$this->assocID.') ';
		elseif($this->occid) $sql .= '(occid = '.$this->occid.') ';
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_assoc()){
				$retArr[$r['assocID']] = $r;
				$uidArr[$r['createdUid']] = $r['createdUid'];
				$uidArr[$r['modifiedUid']] = $r['modifiedUid'];
			}
			$rs->free();
		}
		if($uidArr){
			//Add user names for modified and created by
			$sql = 'SELECT uid, firstname, lastname, username FROM users WHERE uid IN('.implode(',', $uidArr).')';
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$uidArr[$r->uid] = $r->lastname . ($r->firstname ? ', ' . $r->firstname : '');
				}
				$rs->free();
			}
			foreach($retArr as $assocID => $assocArr){
				if($assocArr['createdUid'] && array_key_exists($assocArr['createdUid'], $uidArr)) $retArr[$assocID]['createdBy'] = $uidArr[$assocArr['createdUid']];
				if($assocArr['modifiedUid'] && array_key_exists($assocArr['modifiedUid'], $uidArr)) $retArr[$assocID]['modifiedBy'] = $uidArr[$assocArr['modifiedUid']];
			}
		}
		return $retArr;
	}

	public function insertAssociation($inputArr){
		$status = false;
		if($this->occid){
			if(!isset($inputArr['createdUid'])) $inputArr['createdUid'] = $GLOBALS['SYMB_UID'];
			$sql = 'INSERT INTO omoccurassociations(occid, recordID';
			$sqlValues = '?, ?, ';
			$paramArr = array($this->occid);
			$paramArr[] = UuidFactory::getUuidV4();
			$this->typeStr = 'is';
			$this->setParameterArr($inputArr);
			foreach($this->parameterArr as $fieldName => $value){
				$sql .= ', '.$fieldName;
				$sqlValues .= '?, ';
				$paramArr[] = $value;
			}
			$sql .= ') VALUES('.trim($sqlValues, ', ').') ';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error){
						$this->assocID = $stmt->insert_id;
						$status = true;
					}
					else $this->errorMessage = 'ERROR inserting omoccurassociations record (2): '.$stmt->error;
				}
				else $this->errorMessage = 'ERROR inserting omoccurassociations record (1): '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for omoccurassociations insert: '.$this->conn->error;
		}
		return $status;
	}

	public function updateAssociation($inputArr){
		$status = false;
		if($this->assocID && $this->conn){
			$this->setParameterArr($inputArr);
			$paramArr = array();
			$sqlFrag = '';
			foreach($this->parameterArr as $fieldName => $value){
				$sqlFrag .= $fieldName . ' = ?, ';
				$paramArr[] = $value;
			}
			$paramArr[] = $this->assocID;
			$this->typeStr .= 'i';
			$sql = 'UPDATE omoccurassociations SET '.trim($sqlFrag, ', ').' WHERE (assocID = ?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($this->typeStr, ...$paramArr);
				$stmt->execute();
				if($stmt->affected_rows || !$stmt->error) $status = true;
				else $this->errorMessage = 'ERROR updating omoccurassociations record: '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for updating omoccurassociations: '.$this->conn->error;
		}
		return $status;
	}

	private function setParameterArr($inputArr){
		foreach($this->schemaMap as $field => $type){
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = trim($inputArr[$postField]);
				if(!$value) $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr .= ($this->typeStr ? ',' : '') . $type;
			}
		}
		if(isset($inputArr['occid']) && $inputArr['occid'] && !$this->occid) $this->occid = $inputArr['occid'];
	}

	public function deleteAssociation(){
		if($this->assocID){
			$sql = 'DELETE FROM omoccurassociations WHERE assocID = '.$this->assocID;
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR deleting omoccurassociations record: '.$this->conn->error;
				return false;
			}
		}
	}

	//Setters and getters
	public function setAssocID($id){
		if(is_numeric($id)) $this->assocID = $id;
	}

	public function getAssocID(){
		return $this->assocID;
	}

	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}

	public function getSchemaMap(){
		return $this->schemaMap;
	}

	public function getErrorMessage(){
		return $this->errorMessage;
	}
}
?>