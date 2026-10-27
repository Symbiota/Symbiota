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

	public function getDatasetList() {
		$retArr = array();
		$cond = array('isPublic' => 1);
		$orderBy = array('category','datasetName');
		$datasetArr = $this->getDatasetArr($cond, $orderBy);
		foreach($datasetArr as $id => $dsArr){
			$cat = $dsArr['category'];
			if(!$cat) $cat = 'UNDEFINED_CATEGORY';
			$retArr[$cat][$id] = $dsArr['datasetName'];
		}
		return $retArr;
	}

	public function getDatasetsByUser($uid = null){
		$retArr = array();
		if(!$uid){
			if($GLOBALS['SYMB_UID']) $uid = $GLOBALS['SYMB_UID'];
			else return false;
		}

		//Get datasets owned by user
		$conditionArr = array('uid' => $uid);
		$orderByArr = array('sortSequence','datasetName');
		$retArr = $this->getDatasetArr($conditionArr, $orderByArr);
		foreach($retArr as $id => $dsArr){
			$retArr[$id]['role'][] = 'owner';
		}

		if($userRoles = $this->getUserRoles(null, $uid)) {
			//Get datasets shared with user
			$condArr = array('datasetID' => array_keys($userRoles));
			if($sharedDatasets = $this->getDatasetArr($condArr, $orderByArr)){
				//Add role for each dataset
				foreach($sharedDatasets as $id => $sharedDS){
					$retArr[$id] = $sharedDS;
					$role = array_keys($userRoles[$id]);
					$retArr[$id]['role'] = $role;
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

	public function getDatasetMetadata($publicOnly = true) {
		$retArr = array();
		if ($this->datasetID) {
			$cond = array('datasetID' => $this->datasetID);
			if($publicOnly) $cond['isPbulic'] = 1;
			$dsArr = $this->getDatasetArr($cond);
			$retArr = $dsArr[$this->datasetID];
		}
		return $retArr;
	}

	public function cloneDataset() {
		$status = true;
		$datasetMeta = $this->getDatasetMetadata(false);
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
}
