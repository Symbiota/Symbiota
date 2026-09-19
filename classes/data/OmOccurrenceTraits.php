<?php
include_once($SERVER_ROOT . '/classes/data/DataCore.php');

class OmOccurrenceTraits extends DataCore{

	private $traitID = 0;

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	//tmtraits functions
	private function setTraitFieldMap(){
		$this->fieldMap = array('traitID' => 'pk', 'traitName' => 's', 'traitType' => 's', 'units' => 's', 'description' => 's', 'refUrl' => 's', 'notes' => 's',
			'projectGroup' => 's', 'isPublic' => 'i', 'includeInSearch' => 'i', 'dynamicProperties' => 's', 'modifiedUid' => 'i', 'dateLastModified' => 'd', 'createdUid' => 'i');
	}

	public function getTraitArr($conditionArr = null, $orderByArr = null){
		$this->setTraitFieldMap();
		return $this->getRecordArr('tmtraits', $conditionArr, $orderByArr);
	}

	public function getTraitArrById(){
		if(!$this->traitID){
			$this->errorMessage = 'TRAITID_NOT_SET';
			return false;
		}
		$this->setTraitFieldMap();
		$pkArr = array('traitID' => $this->traitID);
		$traitArr = $this->getRecordArr('tmtraits', $pkArr);
		return $traitArr[$this->traitID];
	}

	public function insertTrait($inputArr){
		$this->setTraitFieldMap();
		if(empty($inputArr['createdUid'])){
			$inputArr['createdUid'] = $GLOBALS['SYMB_UID'];
		}
		$controlType = '[{"controlType":"' . $inputArr['dynamicproperties'] .'"}]';
		$inputArr['dynamicproperties'] = $controlType;
		return $this->insertRecord('tmtraits', $inputArr);
	}

	public function updateTrait($inputArr){
		if(!$this->traitID){
			$this->errorMessage = 'TRAITID_NOT_SET';
			return false;
		}
		$this->setTraitFieldMap();
		if(empty($inputArr['modifiedUid'])){
			$inputArr['modifiedUid'] = $GLOBALS['SYMB_UID'];
		}
		$controlType = '[{"controlType":"' . $inputArr['dynamicproperties'] .'"}]';
		$inputArr['dynamicproperties'] = $controlType;
		$pkArr = array('traitID' => $this->traitID);
		return $this->updateRecord('tmtraits', $pkArr, $inputArr);
	}

	public function deleteTrait(){
		if(!$this->traitID){
			$this->errorMessage = 'TRAITID_NOT_SET';
			return false;
		}
		$this->setTraitFieldMap();
		$pkArr = array('traitID' => $this->traitID);
		return $this->deleteRecord('tmtraits', $pkArr);
	}

	//tmstates functions

	private function setTraitStateMap(){
		$this->fieldMap = array('stateid' => 'pk', 'traitid' => 'i', 'statecode' => 's', 'statename' => 's', 'description' => 's', 'refUrl' => 's', 'notes' => 's',
			'sortseq' => 'i', 'modifiedUid' => 'i', 'datelastmodified' => 'd', 'createdUid' => 'i');
	}

	public function getTraitStateArr(){
		$this->setTraitStateMap();
		$pkArr = array('traitid' => $this->traitID);
		return $this->getRecordArr('tmstates', $pkArr);
	}

	public function insertTraitState($inputArr){
		if(!isset($inputArr['traitid'])){
			if($this->traitID){
				$inputArr['traitid'] = $this->traitID;
			}
			else{
				$this->errorMessage = 'TRAITID_NOT_SET';
				return false;
			}
		}
		$this->setTraitStateMap();
		if(empty($inputArr['statecode'])){
			$inputArr['statecode'] = $this->getTraitStateKeyIncrement();
		}
		if(empty($inputArr['enteredby']) && empty($inputArr['enteredBy'])){
			$inputArr['enteredBy'] = $GLOBALS['PARAMS_ARR']['un'];
		}
		return $this->insertRecord('tmstates', $inputArr);
	}

	private function getTraitStateKeyIncrement(){
		$statecodeValue = 1;
		//Get highest character set ID value (statecode) and increase by 1
		$sql = 'SELECT statecode FROM tmstates WHERE traitid = ? ORDER BY (statecode+1) DESC ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->traitID);
			$stmt->execute();
			$rs = $stmt->get_result();
			if($r = $rs->fetch_object()){
				if(is_numeric($r->statecode)){
					$statecodeValue = $r->statecode + 1;
				}
			}
			$rs->free();
			$stmt->close();
		}
		return $statecodeValue;
	}

	public function updateTraitState($inputArr){
		$this->setTraitStateMap();
		if(!$this->traitID){
			$this->errorMessage = 'TRAITID_NOT_SET';
			return false;
		}
		if(empty($inputArr['statecode'])){
			$this->errorMessage = 'ERROR_STATECODE_IS_NULL';
			return false;
		}
		$statecode = $inputArr['statecode'];
		$pkArr = array('traitid' => $this->traitID, 'statecode' => $statecode);
		return $this->updateRecord('tmstates', $pkArr, $inputArr);
	}

	public function deleteTraitState($stateID){
		if(!$this->traitID){
			$this->errorMessage = 'TRAITID_NOT_SET';
			return false;
		}
		if(!$stateID){
			$this->errorMessage = 'ERROR_STATEID_IS_NULL';
			return false;
		}
		if(!is_numeric($stateID)){
			$this->errorMessage = 'ERROR_STATEID_IS_NOT_NUMERIC';
			return false;
		}
		$this->setTraitStateMap();
		$pkArrStateID = array('stateid' => $stateID);
		
		return $this->deleteRecord('tmstates', $pkArrStateID);
	}

	//tmattributes functions

	private function setTraitAttributeMap(){
		$this->fieldMap = array('stateid' => 'pk', 'occid' => 'pk', 'modifier' => 's', 'xvalue' => 'd', 'mediaID' => 'i', 'imagecoordinates' => 's', 'source' => 's',
			'notes' => 's', 'statuscode' => 'i', 'modifiedUid' => 'i', 'datelastmodified' => 'd', 'createdUid' => 'i');
	}

	public function insertAttribute($inputArr){
		$this->setTraitAttributeMap();
		return $this->insertRecord('tmattributes', $inputArr);
	}

	public function batchUpdateAttribute($statusCode, $notes, $sourceStr, $occid, $traitIdArr){
		$status = false;
		foreach($traitIdArr as $traitID){
			$sql = 'UPDATE tmattributes a INNER JOIN tmstates s ON a.stateid = s.stateid
				SET a.statusCode = ?, a.notes = ?, a.source = ?, a.modifieduid = ?, a.datelastmodified = NOW()
				WHERE a.occid = ? AND s.traitid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('issiii', $statusCode, $notes, $sourceStr, $GLOBALS['SYMB_UID'], $occid, $traitID);
				$stmt->execute();
				if($stmt->affected_rows){
					$status = true;
				}
				elseif($stmt->error) $this->errorMessage = $stmt->error;
				$stmt->close();
			}
		}
		return $status;
	}

	//tmtraitdependencies functions


	//tmtraittaxalink functions



	//General data retrival functions


	//Setters and getters
	public function getTraitID(){
		return $this->traitID;
	}

	public function setTraitID($id){
		if(is_numeric($id)) $this->traitID = $id;
	}
}
?>
