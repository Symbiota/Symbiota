<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class OccurrenceEditorMaterialSample extends Manager{

	private $occid;
	private $matSampleID;

	function __construct(){
		parent::__construct(null,'write');
	}

	function __destruct(){
 		parent::__destruct();
	}

	public function getMaterialSampleArr(){
		$retArr = array();
		$sql = 'SELECT m.matSampleID, m.sampleType, m.catalogNumber, m.guid, m.sampleCondition, m.disposition, m.preservationType, m.preparationDetails, m.preparationDate,
			m.preparedByUid, CONCAT_WS(", ",u.lastname,u.firstname) as preparedBy, m.individualCount, m.sampleSize, m.storageLocation, m.remarks, m.dynamicFields, m.recordID, m.initialTimestamp
			FROM ommaterialsample m LEFT JOIN users u ON m.preparedByUid = u.uid WHERE m.occid = '.$this->occid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_assoc()){
			$retArr[$r['matSampleID']] = $r;
		}
		$rs->free();
		return $retArr;
	}

	public function insertMaterialSample($postArr){
		if($this->occid){
			$sql = 'INSERT INTO ommaterialsample(occid, sampleType, catalogNumber, guid, sampleCondition, disposition, preservationType, preparationDetails, preparationDate,
				preparedByUid, individualCount, sampleSize, storageLocation, remarks)
				VALUES('.$this->occid.','.($postArr['ms_sampleType']?'"'.$postArr['ms_sampleType'].'"':'NULL').','.($postArr['ms_catalogNumber']?'"'.$postArr['ms_catalogNumber'].'"':'NULL').','.
				($postArr['ms_guid']?'"'.$postArr['ms_guid'].'"':'NULL').','.
				(is_numeric($postArr['ms_sampleCondition'])?$postArr['ms_sampleCondition']:'NULL').','.($postArr['ms_disposition']?'"'.$postArr['ms_disposition'].'"':'NULL').','.
				($postArr['ms_preservationType']?'"'.$postArr['ms_preservationType'].'"':'NULL').','.(is_numeric($postArr['ms_preparationDetails'])?$postArr['ms_preparationDetails']:'NULL').','.
				(is_numeric($postArr['ms_preparationDate'])?$postArr['ms_preparationDate']:'NULL').','.(is_numeric($postArr['ms_preparedByUid'])?$postArr['ms_preparedByUid']:'NULL').','.
				($postArr['ms_individualCount']?'"'.$postArr['ms_individualCount'].'"':'NULL').','.(is_numeric($postArr['ms_sampleSize'])?$postArr['ms_sampleSize']:'NULL').','.
				($postArr['ms_storageLocation']?'"'.$postArr['ms_storageLocation'].'"':'NULL').','.($postArr['ms_remarks']?'"'.$postArr['ms_remarks'].'"':'NULL').')';
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR inserting new material sample record into database: '.$this->conn->error;
				return false;
			}
		}
	}

	public function updateMaterialSample($postArr){
		if($this->matSampleID){
			$sql = 'UPDATE ommaterialsample SET sampleType = "'.$this->cleanInStr($postArr['ms_sampleType']).
				'",catalogNumber = '.($postArr['ms_catalogNumber']?'"'.$postArr['ms_catalogNumber'].'"':'NULL').
				',guid = '.($postArr['ms_guid']?'"'.$postArr['ms_guid'].'"':'NULL').
				',sampleCondition = '.($postArr['ms_sampleCondition']?'"'.$postArr['ms_sampleCondition'].'"':'NULL').
				',disposition = '.($postArr['ms_disposition']?'"'.$postArr['ms_disposition'].'"':'NULL').
				',preservationType = '.($postArr['ms_preservationType']?'"'.$postArr['ms_preservationType'].'"':'NULL').
				',preparationDetails = '.($postArr['ms_preparationDetails']?'"'.$postArr['ms_preparationDetails'].'"':'NULL').
				',preparationDate = '.($postArr['ms_preparationDate']?'"'.$postArr['ms_preparationDate'].'"':'NULL').
				',preparedByUid = '.($postArr['ms_preparedByUid']?'"'.$postArr['ms_preparedByUid'].'"':'NULL').
				',individualCount = '.($postArr['ms_individualCount']?'"'.$postArr['ms_individualCount'].'"':'NULL').
				',sampleSize = '.($postArr['ms_sampleSize']?'"'.$postArr['ms_sampleSize'].'"':'NULL').
				',storageLocation = '.($postArr['ms_storageLocation']?'"'.$postArr['ms_storageLocation'].'"':'NULL').
				',remarks = '.($postArr['ms_remarks']?'"'.$postArr['ms_remarks'].'"':'NULL').' WHERE matSampleID = '.$this->matSampleID;
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR updating material sample record into database: '.$this->conn->error;
				return false;
			}
		}
	}

	public function deleteMaterialSample(){
		if($this->matSampleID){
			$sql = 'DELETE FROM ommaterialsample WHERE matSampleID = '.$this->matSampleID;
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR updating material sample record into database: '.$this->conn->error;
				return false;
			}
		}
	}

	//Data lookup functions
	public function getMSTypeControlValues(){
		$retArr = array();
		$sql = 'SELECT v.tableName, v.fieldName, t.term, v.limitToList FROM ctcontrolvocabterm t INNER JOIN ctcontrolvocab v ON t.cvID = v.cvID
			WHERE v.tableName IN("ommaterialsample","ommaterialsampleextended") ORDER BY t.term';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tableName][$r->fieldName]['t'][] = $r->term;
			$retArr[$r->tableName][$r->fieldName]['l'] = $r->limitToList;
		}
		return $retArr;
	}

	//Misc support functions
	public function cleanFormData(&$postArr){
		foreach($postArr as $k => $v){
			if(substr($k,0,3) == 'ms_') $postArr[$k] = filter_var($v,FILTER_SANITIZE_STRING);
		}
	}

	//Setters and getters
	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}

	public function getOccid(){
		return $this->occid;
	}

	public function setMatSampleID($id){
		if(is_numeric($id)) $this->matSampleID = $id;
	}

	public function getMatSampleID(){
		return $this->matSampleID;
	}
}
?>