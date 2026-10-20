<?php
include_once($SERVER_ROOT . '/classes/data/DataCore.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');

class OmDatasets extends DataCore{

	private $datasetID = 0;

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getDatasetArr($conditionArr = null, $orderByArr = null){
		$this->setFieldMap();
		return $this->getRecordArr('omoccurdatasets', $conditionArr, $orderByArr);
	}

	public function getDatasetArrByID(){
		if(!$this->cid){
			$this->errorMessage = 'ID_NOT_SET';
			return false;
		}
		$this->setFieldMap();
		$pkArr = array('datasetID' => $this->datasetID);
		$recordArr = $this->getRecordArr('omoccurdatasets', $pkArr);
		return $recordArr[$this->datasetID];
	}

	public function insertDataset($inputArr){
		$this->setFieldMap();
		if(empty($inputArr['uid'])){
			$inputArr['uid'] = $GLOBALS['SYMB_UID'];
		}
		$inputArr['recordID'] = UuidFactory::getUuidV4();
		return $this->insertRecord('omoccurdatasets', $inputArr);
	}

	public function updateDataset($inputArr){
		if(!$this->datasetID){
			$this->errorMessage = 'ID_NOT_SET';
			return false;
		}
		$this->setFieldMap();
		$pkArr = array('datasetID' => $this->datasetID);
		return $this->updateRecord('omoccurdatasets', $pkArr, $inputArr);
	}

	public function deleteDataset(){
		if(!$this->datasetID){
			$this->errorMessage = 'ID_NOT_SET';
			return false;
		}
		$this->setCharacterFieldMap();
		$pkArr = array('datasetID' => $this->datasetID);
		return $this->deleteRecord('omoccurdatasets', $pkArr);
	}

	private function setFieldMap(){
		$this->fieldMap = array('datasetID' => 'pk', 'datasetName' => 's', 'bibliographicCitation' => 's', 'category' => 's', 'isPublic' => 'i', 'parentDatasetID' => 'i',
			'description' => 's', 'datasetIdentifier' => 's', 'notes' => 's', 'dynamicProperties' => 's', 'sortSequence' => 'i', 'uid' => 'i', 'recordID' => 's');
	}

	//Setters and getters
	public function getDatasetID(){
		return $this->cid;
	}

	public function setDatasetID($id){
		if(is_numeric($id)) $this->datasetID = $id;
	}
}
?>
