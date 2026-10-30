<?php
include_once($SERVER_ROOT . '/classes/data/OmDatasets.php');

class OccurrenceDataset extends OmDatasets {

	private $errorArr = array();

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getDatasetMetadata() {
		$retArr = array();
		if ($this->datasetID) {
			$cond = array('datasetID' => $this->datasetID);
			$dsArr = $this->getDatasetArr($cond);
			$retArr = $dsArr[$this->datasetID];
		}
		return $retArr;
	}

	public function getDatasetList() {
		//Returns and array of public datasets plus those owned by or shared with user
		$retArr = array();
		//Get public datasets
		$cond = array('isPublic' => 1);
		$retArr = $this->getDatasetArr($cond);
		//Append user datasets
		if($userDatasets = $this->getDatasetsByUser()){
			$retArr = $userDatasets + $retArr;
		}
		uasort($retArr, function($a, $b) {
			return strcasecmp($a['datasetName'], $b['datasetName']);
		});
		return $retArr;
	}

	public function getDatasetsGroupedByCategory() {
		$retArr = array();
		$datasetArr = $this->getDatasetList();
		foreach($datasetArr as $id => $dsArr){
			$cat = $dsArr['category'];
			if(!$cat) $cat = 'Z-UNDEFINED_CATEGORY';
			$retArr[$cat][$id]['datasetName'] = $dsArr['datasetName'];
			$retArr[$cat][$id]['isPublic'] = $dsArr['isPublic'];
			if(!empty($dsArr['role'])){
				$retArr[$cat][$id]['role'] = implode(', ', $dsArr['role']);
			}
		}
		ksort($retArr);
		return $retArr;
	}

	public function getDatasetsByUser($uid = null){
		//Returns datasets owned by or shared with user
		$retArr = array();
		if(!$uid){
			//Default to current user
			if($GLOBALS['SYMB_UID']) $uid = $GLOBALS['SYMB_UID'];
			else return false;
		}

		//Get datasets owned by user
		$conditionArr = array('uid' => $uid);
		$orderByArr = array('sortSequence','datasetName');
		$retArr = $this->getDatasetArr($conditionArr, $orderByArr);
		foreach($retArr as $id => $dsArr){
			$retArr[$id]['role']['owner'] = $this->translateUserRole('owner');
		}

		if($userRoles = $this->getUserRoles(null, $uid)) {
			//Get datasets shared with user
			$datasetKeyArr = array_keys($userRoles);
			$condArr = array('datasetID' => $datasetKeyArr);
			if($sharedDatasets = $this->getDatasetArr($condArr, $orderByArr)){
				//Add role for each dataset
				foreach($sharedDatasets as $id => $sharedDS){
					$retArr[$id] = $sharedDS;
					$retArr[$id]['role'] = $userRoles[$id][$uid]['roles'];
				}
			}
		}
		return $retArr;
	}

	public function getDatasetsByOccurrence($occid){
		$retArr = array();
		if($occid){
			$occurArr = $this->getOccurrences(array('occid' => $occid));
			if(!$occurArr) return $retArr;
			$dsIdArr = array();
			foreach($occurArr as $dsArr){
				$dsIdArr[] = $dsArr['datasetID'];
			}
			$conditionArr = array('datasetID' => $dsIdArr);
			$orderByArr = array('sortSequence','datasetName');
			$retArr = $this->getDatasetArr($conditionArr, $orderByArr);
		}
		return $retArr;
	}

	public function cloneDataset() {
		$status = true;
		$datasetMeta = $this->getDatasetMetadata();
		$newName = $datasetMeta['datasetName'] . ' - Copy (' . time() . ')';
		//Create new dataset
		$dsArr['datasetName'] = $newName;
		$dsArr['uid'] = $GLOBALS['SYMB_UID'];
		unset($dsArr['datasetID']);
		unset($dsArr['datasetIdentifier']);
		unset($dsArr['recordID']);
		unset($dsArr['initialTimestamp']);
		if($this->insertDataset($dsArr)){
			$newDatasetID = $this->conn->insert_id;
			//Insert all occurrences
			$this->copyOccurrences($newDatasetID);
			$status = true;
		}
		return $status;
	}

	//Cart/Basket functions
	public function addCartOccurrence($occidInput){
		if(!$occidInput) return false;
		if($this->setCartDatasetID()){
			if($this->insertOccurrences($occidInput)) return true;
		}
		return false;
	}

	public function deleteCartOccurrence($occidInput){
		if(!$occidInput) return false;
		if($this->setCartDatasetID()){
			if($this->deleteOccurrences($occidInput)) return true;
		}
		return false;
	}

	public function clearCart(){
		if($this->setCartDatasetID()){
			if($this->deleteOccurrences()) return true;
		}
		return true;
	}

	private function setCartDatasetID(){
		if(empty($GLOBALS['SYMB_UID'])){
			$this->errorMessage = 'USER_NOT_LOGGED';
			return false;
		}
		$condArr = array('uid' => $GLOBALS['SYMB_UID'], 'datasetName' => 'USER_CART');
		$datasetArr = $this->getDatasetArr($condArr);
		if($datasetArr){
			$this->datasetID = key($datasetArr);
			return true;
		}
		else{
			if($this->insertDataset($condArr)){
				$this->datasetID = $this->datasetID;
				return true;
			}
		}
		return false;
	}
}
