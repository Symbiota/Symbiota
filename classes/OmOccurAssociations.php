<?php

include_once('UuidFactory.php');

class OmOccurAssociations{

	private $conn;
	private $occid;
	private $parameterArr = array();
	private $typeStr = '';
	private $assocID = null;
	private $errorMessage = '';

	public function __construct($conn){
		$this->conn = $conn;
	}

	public function __destruct(){
	}

	public function updateAssociation($postArr){
		$status = false;
		if($this->occid && $this->conn){
			$this->setParameterArr($postArr);
			$paramArr = array();
			$sqlFrag = '';
			foreach($this->parameterArr as $fieldName => $value){
				$sqlFrag .= $fieldName . ' = ?, ';
				$paramArr[] = $value;
			}
			$paramArr[] = $this->occid;
			$sql = 'UPDATE omoccurassociations SET '.trim($sqlFrag, ', ').' WHERE (occid = ?)';
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

	public function insertAssociations($postArr){
		$status = false;
		$this->setParameterArr($postArr);
		$sql = 'INSERT INTO omoccurassociations(occid, recordID';
		$sqlValues = '?, ?, ';
		$paramArr = array($this->occid);
		$paramArr[] = UuidFactory::getUuidV4();
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
		return $status;
	}

	private function setParameterArr($postArr){
		$fieldArr = array('occidAssociate' => 'i', 'relationship' => 's', 'relationshipID' => 's', 'subType' => 's', 'identifier' => 's', 'basisOfRecord' => 's', 'resourceUrl' => 's',
			'verbatimSciname' => 's', 'tid' => 'i', 'locationOnHost' => 's', 'conditionOfAssociate' => 's', 'establishedDate' => 's', 'imageMapJSON' => 's', 'dynamicProperties' => 's',
			'notes' => 's', 'accordingTo' => 's', 'sourceIdentifier' => 's', 'recordID' => 's', 'createdUid' => 'i', 'modifiedTimestamp' => 's',
			'modifiedUid' => 'i');
		foreach($fieldArr as $field => $type){
			$postField = '';
			if(isset($postArr[$field])) $postField = $field;
			elseif(isset($postArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = $postArr[$postField];
				if(!$value) $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr = $type;
			}
		}
	}

	//Setters and getters
	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}

	public function getAssocID(){
		return $this->assocID;
	}

	public function getErrorMessage(){
		return $this->errorMessage;
	}
}
?>