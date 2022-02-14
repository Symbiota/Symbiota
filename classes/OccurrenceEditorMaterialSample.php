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
		$status = false;
		if($this->occid){
			$reqArr = $this->getRequestArr($postArr);
			$sql = 'INSERT INTO ommaterialsample(occid, sampleType, catalogNumber, guid, sampleCondition, disposition, preservationType, preparationDetails, preparationDate,
				preparedByUid, individualCount, sampleSize, storageLocation, remarks)
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('issssssssissss', $this->occid, $reqArr['sampleType'], $reqArr['catalogNumber'], $reqArr['guid'], $reqArr['sampleCondition'], $reqArr['disposition'],
					$reqArr['preservationType'], $reqArr['preparationDetails'], $reqArr['preparationDate'], $reqArr['preparedByUid'], $reqArr['individualCount'],
					$reqArr['sampleSize'], $reqArr['storageLocation'], $reqArr['remarks']);
				$stmt->execute();
				if($stmt->affected_rows || !$stmt->error) $status = $stmt->insert_id;
				else $this->errorMessage = 'ERROR inserting new material sample record into database: '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for MS insert: '.$this->conn->error;
			if(!$status) echo $this->errorMessage;
		}
		return $status;
	}

	public function updateMaterialSample($postArr){
		$status = false;
		if(is_numeric($this->matSampleID)){
			$reqArr = $this->getRequestArr($postArr);
			$sql = 'UPDATE ommaterialsample SET sampleType = ?, catalogNumber = ?, guid = ?, sampleCondition = ?, disposition = ?,
				preservationType = ?, preparationDetails = ?, preparationDate = ?, preparedByUid = ?, individualCount = ?, sampleSize = ?,
				storageLocation = ?, remarks = ? WHERE matSampleID = ?';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param('ssssssssissssi', $reqArr['sampleType'], $reqArr['catalogNumber'], $reqArr['guid'], $reqArr['sampleCondition'], $reqArr['disposition'],
					$reqArr['preservationType'], $reqArr['preparationDetails'], $reqArr['preparationDate'], $reqArr['preparedByUid'], $reqArr['individualCount'],
					$reqArr['sampleSize'], $reqArr['storageLocation'], $reqArr['remarks'], $this->matSampleID);
				$stmt->execute();
				if($stmt->affected_rows || !$stmt->error) $status = true;
				else $this->errorMessage = 'ERROR updating material sample record in database: '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for MS update: '.$this->conn->error;
		}
		return $status;
	}

	public function deleteMaterialSample(){
		if(is_numeric($this->matSampleID)){
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

	private function getRequestArr($postArr){
		$retArr = array();
		$retArr['sampleType'] = ($postArr['ms_sampleType']?$this->cleanInStr($postArr['ms_sampleType']):NULL);
		$retArr['catalogNumber'] = ($postArr['ms_catalogNumber']?$this->cleanInStr($postArr['ms_catalogNumber']):NULL);
		$retArr['guid'] = ($postArr['ms_guid']?$this->cleanInStr($postArr['ms_guid']):NULL);
		$retArr['sampleCondition'] = ($postArr['ms_sampleCondition']?$this->cleanInStr($postArr['ms_sampleCondition']):NULL);
		$retArr['disposition'] = ($postArr['ms_disposition']?$this->cleanInStr($postArr['ms_disposition']):NULL);
		$retArr['preservationType'] = ($postArr['ms_preservationType']?$this->cleanInStr($postArr['ms_preservationType']):NULL);
		$retArr['preparationDetails'] = ($postArr['ms_preparationDetails']?$this->cleanInStr($postArr['ms_preparationDetails']):NULL);
		$retArr['preparationDate'] = ($postArr['ms_preparationDate']?$this->cleanInStr($postArr['ms_preparationDate']):NULL);
		$retArr['preparedByUid'] = (is_numeric($postArr['ms_preparedByUid'])?$postArr['ms_preparedByUid']:NULL);
		$retArr['individualCount'] = ($postArr['ms_individualCount']?$this->cleanInStr($postArr['ms_individualCount']):NULL);
		$retArr['sampleSize'] = ($postArr['ms_sampleSize']?$this->cleanInStr($postArr['ms_sampleSize']):NULL);
		$retArr['storageLocation'] = ($postArr['ms_storageLocation']?$this->cleanInStr($postArr['ms_storageLocation']):NULL);
		$retArr['remarks'] = ($postArr['ms_remarks']?$this->cleanInStr($postArr['ms_remarks']):NULL);
		return $retArr;
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