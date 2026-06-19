<?php
include_once('Manager.php');
include_once('utilities/Sanitize.php');
include_once('utilities/UuidFactory.php');

class OmExsiccate extends Manager{

	private $primaryKeyName;
	private $primaryKeyValue;
	private $fieldMap = array();
	private $parameterArr = array();
	private $typeStr = '';
	private $hidden = array();
	private $notFillable = array();

	public function __construct($conType = 'write') {
		parent::__construct(null, $conType);
	}

	public function __destruct(){
		parent::__destruct();
	}

	private function setFieldMap($tableName){
		$this->primaryKeyName = null;
		$this->primaryKeyValue = null;
		unset($this->fieldMap);
		unset($this->hidden);
		unset($this->notFillable);
		if($tableName == 'omexsiccatititles'){
			$this->primaryKeyName = 'ometid';
			$this->fieldMap = array('title' => 's', 'abbreviation' => 's', 'editor' => 's', 'exsRange' => 's', 'startDate' => 's', 'endDate' => 's',
				'source' => 's', 'sourceIdentifier' => 's', 'notes' => 's', 'lastEditedBy' => 's', 'recordID' => 's', 'initialTimestamp' => 'd');
			$this->notFillable = array('initialTimestamp', 'recordID');
			return true;
		}
		if($tableName == 'omexsiccatinumbers'){
			$this->primaryKeyName = 'omenid';
			$this->fieldMap = array('exsNumber' => 's', 'ometid' => 'i', 'notes' => 's', 'initialTimestamp' => 'd');
			$this->notFillable = array('initialTimestamp');
			return true;
		}
		if($tableName == 'omexsiccatiocclink'){
			$this->primaryKeyName = 'omexid';
			$this->fieldMap = array('omenid' => 'i', 'occid' => 'i', 'ranking' => 'i', 'notes' => 's', 'initialTimestamp' => 'd');
			$this->notFillable = array('initialTimestamp');
			return true;
		}
		return false;
	}

	public function getTableData($tableName, $condition){
		$retArr = array();
		if($this->setFieldMap($tableName)){
			$type = '';
			$paramArr = array();
			$sql = 'SELECT ' . $this->primaryKeyName . ', ' . implode(',', $this->fieldMap) . ' FROM ' . $tableName;
			if(is_numeric($condition)){
				$sql .= 'WHERE ' . $this->primaryKeyName . ' = ?';
				$type = 'i';
				$paramArr[] = $condition;
			}
			if($stmt = $this->conn->prepare($sql)){
				if($type) $stmt->bind_param($type, ...$paramArr);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_array(MYSQLI_ASSOC)){
					$pk = $r[$this->primaryKey];
					foreach($this->fieldMap as $fieldName => $type){
						$value = $r[$fieldName];
						if($type == 's'){
							if($value === null) $value = '';
							else $value = Sanitize::outString($value);
						}
						$retArr[$this->primaryKey][$fieldName] = $value;
					}
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function insertRecord($tableName, $inputArr){
		$status = false;
		if($this->setFieldMap($tableName)){
			$this->setParameterArr($inputArr);
			$sql = 'INSERT INTO ' . $tableName . '(';
			$sqlValues = '';
			$paramArr = array();
			$delimiter = '';
			if(array_key_exists('recordID', $this->fieldMap)){
				$sql .= 'recordID';
				$sqlValues .= '?';
				$paramArr[] = UuidFactory::getUuidV4();
				$delimiter = ', ';
			}
			foreach($this->parameterArr as $fieldName => $value){
				if(!in_array($fieldName, $this->notFillable)){
					$sql .= $delimiter . $fieldName;
					$sqlValues .= $delimiter . '?';
					$paramArr[] = $value;
					$delimiter = ', ';
				}
			}
			$sql .= ') VALUES(' . $sqlValues . ') ';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error){
						$this->primaryKeyValue = $stmt->insert_id;
						$status = true;
					}
					else $this->errorMessage = $stmt->error;
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = $this->conn->error;
		}
		return $status;
	}

	public function updateRecord($tableName, $inputArr){
		$status = false;
		if(!$this->primaryKeyValue){
			$this->errorMessage = 'PRIMARY_KET_NOT_SET';
			return false;
		}
		if($this->setFieldMap($tableName)){
			$this->setParameterArr($inputArr);
			$sqlFrag = '';
			$paramArr = array();
			foreach($this->parameterArr as $fieldName => $value){
				if(!in_array($fieldName, $this->notFillable)){
					$sqlFrag .= $fieldName . ' = ?, ';
					$paramArr[] = $value;
				}
			}
			$sql = 'UPDATE ' . $tableName . ' SET ' . trim($sqlFrag, ', ') . ' WHERE (' . $this->primaryKeyName . ' = ?)';
			if($paramArr){
				$paramArr[] = $this->primaryKeyValue;
				$this->typeStr .= 'i';
				if($stmt = $this->conn->prepare($sql)) {
					$stmt->bind_param($this->typeStr, ...$paramArr);
					if($stmt->execute()){
						if($stmt->affected_rows || !$stmt->error) $status = true;
						else $this->errorMessage = $stmt->error;
					}
					else $this->errorMessage = $stmt->error;
					$stmt->close();
				}
				else $this->errorMessage = $this->conn->error;
			}
		}
		return $status;
	}

	public function deleteRecord($tableName){
		$status = false;
		if(!$this->primaryKeyValue){
			$this->errorMessage = 'PRIMARY_KET_NOT_SET';
			return false;
		}
		if($this->setFieldMap($tableName)){
			$sql = 'DELETE FROM ' . $tableName . ' WHERE ' . $this->primaryKeyName . ' = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->primaryKeyValue);
				$stmt->execute();
				if($stmt->affected_rows && !$stmt->error){
					$status = true;
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
		}
		return $status;
	}

	//Mics support functions
	private function setParameterArr($inputArr){
		//Reset class variables, which is very important if more than one write function is called per class instance
		unset($this->parameterArr);
		$this->parameterArr = array();
		$this->typeStr = '';
		//Prepare type and value variables used within prepared statement
		foreach($this->fieldMap as $field => $type){
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = trim($inputArr[$postField]);
				if($type == 'i') $value = Sanitize::int($value);
				if(!$value) $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr .= $type;
			}
		}
	}

	//Setter and getter functions
	public function getPrimaryKeyValue(){
		return $this->primaryKeyValue;
	}
}
?>
