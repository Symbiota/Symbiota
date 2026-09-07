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
	public function getTraitArr($conditionArr = null, $orderByArr = null){
		$this->setTraitFieldMap();
		return $this->getRecordArr('tmtraits', $conditionArr, $orderByArr);
	}

	public function insertTrait($inputArr){
		$this->setTraitFieldMap();
		if(empty($inputArr['createdUid'])){
			$inputArr['createdUid'] = $GLOBALS['SYMB_UID'];
		}
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

	private function setTraitFieldMap(){
		$this->fieldMap = array('traitID' => 'pk', 'traitName' => 's', 'traitType' => 's', 'units' => 's', 'description' => 's', 'refUrl' => 's', 'notes' => 's',
			'projectGroup' => 's', 'isPublic' => 'i', 'includeInSearch' => 'i', 'dynamicProperties' => 's', 'modifiedUid' => 'i', 'dateLastModified' => 'd', 'createdUid' => 'i');
	}

	//tmstates functions



	//tmattributes functions



	public function batchUpdateAttribute($statusCode, $notes, $sourceStr, $occid, $traitIdArr){
		$status = false;
		foreach($traitIdArr as $traitID){
			$sql = 'UPDATE tmattributes a INNER JOIN tmstates s ON a.stateid = s.stateid
				SET a.statusCode = ?, a.notes = ?, a.source = ?, a.modifieduid = ?, a.datelastmodified = NOW()
				WHERE a.occid = ? AND s.traitid = ?';
			if($stmt = $this->prepare($sql)){
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
