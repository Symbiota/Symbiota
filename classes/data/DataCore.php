<?php
include_once('Manager.php');

class DataCore extends Manager{

	protected $fieldMap = array();
	protected $parameterArr = array();
	protected $typeStr = '';
	protected $primaryKey;

	function __construct() {
		parent::__construct(null, 'write');
	}

	function __destruct(){
		parent::__destruct();
	}

	protected function getRecordArr($tableName, $conditionArr = null, $orderByArr = null){
		$retArr = array();
		if(!$tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if(!$this->fieldMap){
			$this->errorMessage = 'FIELD_MAP_NOT_DEFINED';
			return false;
		}
		$pkArr = array();
		foreach($this->fieldMap as $n => $t){
			if($t == 'pk') $pkArr[$n] = '';
		}
		if(!$pkArr){
			$this->errorMessage = 'PRIMARY_KEY_NOT_DEFINED';
			return false;
		}
		$sql = 'SELECT `' . implode('`,`', array_keys($this->fieldMap)) . '` FROM `' . $tableName . '` ';
		$paramArr = array();
		$typeStr = '';
		if($conditionArr){
			$condSql = $this->getConditionSql($conditionArr, $paramArr, $typeStr);
			if($condSql) $sql .= 'WHERE ' . $condSql;
		}
		if($orderByArr){
			$sql .= 'ORDER BY ' . implode(', ', $orderByArr);
		}
		if($stmt = $this->conn->prepare($sql)){
			if($paramArr) $stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_assoc()){
				foreach($pkArr as $pkName => $v){
					$pkArr[$pkName] = $r[$pkName];
				}
				$key = implode('-', $pkArr);
				foreach($this->fieldMap as $fieldName => $type){
					$retArr[$key][$fieldName] = $r[$fieldName];
				}
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	protected function insertRecord($tableName, $inputArr){
		$status = false;
		if(!$tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if(!$inputArr){
			$this->errorMessage = 'INPUT_ARR_EMPTY';
			return false;
		}
		$this->setParameterArr($inputArr);
		$sql = 'INSERT IGNORE INTO `' . $tableName . '`(`' . implode('`, `', array_keys($this->parameterArr)) . '`) VALUES(' . trim(str_repeat('?,', count($this->parameterArr)), ',') . ') ';
		$paramArr = array_values($this->parameterArr);
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($this->typeStr, ...$paramArr);
			if($stmt->execute()){
				if($stmt->affected_rows || !$stmt->error){
					$this->primaryKey = $stmt->insert_id;
					$status = true;
				}
				else $this->errorMessage = $stmt->error;
			}
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		else $this->errorMessage = $this->conn->error;
		return $status;
	}

	protected function updateRecord($tableName, $pkArr, $inputArr){
		$status = false;
		if(!$tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if(!$pkArr){
			$this->errorMessage = 'PK_ARR_EMPTY';
			return false;
		}
		if(!$inputArr){
			$this->errorMessage = 'INPUT_ARR_EMPTY';
			return false;
		}
		$this->setParameterArr($inputArr);
		$paramArr = array_values($this->parameterArr);
		$sql = 'UPDATE IGNORE `' . $tableName . '` SET ' . implode(' = ?,', array_keys($this->parameterArr)) . ' = ? ';
		if($paramArr){
			if($sqlWhere = $this->getConditionSql($pkArr, $paramArr, $this->typeStr)){
				$sql .= 'WHERE ' . $sqlWhere;
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

	protected function deleteRecord($tableName, $pkArr){
		$status = false;
		if(!$tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if(!$pkArr){
			$this->errorMessage = 'PK_ARR_EMPTY';
			return false;
		}
		if(is_numeric($pkArr)){
			//Assumes there is only one PK defined in fieldMap
			$pkFieldName = array_search('pk', $this->fieldMap);
			$pkArr = array($pkFieldName => $pkArr);
		}
		$sql = 'DELETE IGNORE FROM `' . $tableName . '` WHERE ';
		$paramArr = array();
		$typeStr = '';
		if($sqlWhere = $this->getConditionSql($pkArr, $paramArr, $typeStr)){
			$sql .= $sqlWhere;
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param($typeStr, ...$paramArr);
				$stmt->execute();
				if($stmt->affected_rows && !$stmt->error){
					$status = true;
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
			else{
				$this->errorMessage = $this->conn->error;
			}
		}
		else{
			$this->errorMessage = 'CRITERIA_UNDEFINED';
		}
		return $status;
	}

	protected function setParameterArr($inputArr){
		//Reset class variables, which is very important if more than one write function is called per class instance
		unset($this->parameterArr);
		$this->parameterArr = array();
		$this->typeStr = '';
		//Prepare type and value variables used within prepared statement
		foreach($this->fieldMap as $field => $type){
			if($type == 'x' || $type == 'pk') continue;
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = trim($inputArr[$postField]);
				if(trim($value) === '') $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr .= $type;
			}
		}
	}

	private function getConditionSql($condArr, &$paramArr, &$typeStr){
		$sqlFrag = '';
		$delimiter = '';
		foreach($this->fieldMap as $fieldName => $type){
			$inputField = '';
			if(isset($condArr[$fieldName])) $inputField = $fieldName;
			elseif(isset($condArr[strtolower($fieldName)])) $inputField = strtolower($fieldName);
			if($inputField){
				if(is_array($condArr[$inputField])){
					$inputArr = $condArr[$inputField];
					$inputItemCount = count($inputArr);
					$sqlFrag .= $delimiter . ' `' . $fieldName . '` IN(' . trim(str_repeat('?,', $inputItemCount), ',') . ') ';
					$paramArr = $inputArr;
					if($type == 'pk') $type = 'i';
					$typeStr .= str_repeat($type, $inputItemCount);
				}
				else{
					$sqlFrag .= $delimiter . ' `' . $fieldName . '` = ? ';
					$value = trim($condArr[$inputField]);
					if($value === '') $value = null;
					$paramArr[] = $value;
					if($type == 'pk') $type = 'i';
					$typeStr .= $type;
				}
				$delimiter = 'AND';
			}
		}
		return $sqlFrag;
	}

	//Setters and getters
	public function getPrimaryKey(){
		return $this->primaryKey;
	}
}
?>
