<?php
include_once($SERVER_ROOT . '/config/dbconnection.php');
include_once($SERVER_ROOT . '/classes/utilities/OccurrenceUtil.php');
include_once($SERVER_ROOT . '/classes/utilities/UuidFactory.php');
include_once($SERVER_ROOT . '/classes/utilities/Language.php');

class OmMaterialSample{

	private $conn;
	private $connInherited = false;
	private $matSampleID;
	private $occid;
	private $schemaMap = array();
	private $parameterArr = array();
	private $typeStr = '';
	private $errorMessage;

	function __construct($conn = null){
		if($conn){
			$this->conn = $conn;
			$this->connInherited = true;
		}
		else $this->conn = MySQLiConnectionFactory::getCon('write');
		$this->schemaMap = array('sampleType' => 's', 'catalogNumber' => 's', 'guid' => 's', 'sampleCondition' => 's', 'disposition' => 's', 'preservationType' => 's',
			'preparationDetails' => 's', 'preparationDate' => 's', 'preparedByUid' => 'i', 'individualCount' => 'i', 'sampleSize' => 's', 'storageLocation' => 's', 'remarks' => 's');
	}

	function __destruct(){
		if(!($this->conn === null) && !$this->connInherited) $this->conn->close();
	}

	public function getMaterialSampleArr(){
		$retArr = array();
		$sql = 'SELECT m.matSampleID, m.'.implode(', m.', array_keys($this->schemaMap)).', CONCAT_WS(", ",u.lastname,u.firstname) as preparedBy, m.dynamicFields, m.recordID, m.initialTimestamp
			FROM ommaterialsample m LEFT JOIN users u ON m.preparedByUid = u.uid WHERE m.occid = '.$this->occid;
		if($rs = $this->conn->query($sql)){
			while($r = $rs->fetch_assoc()){
				$retArr[$r['matSampleID']] = $r;
			}
			$rs->free();
		}
		return $retArr;
	}

	public function insertMaterialSample($inputArr){
		$status = false;
		if($this->occid && $this->conn){
			$sql = 'INSERT INTO ommaterialsample(occid, recordID';
			$sqlValues = '?, ?, ';
			$paramArr = array($this->occid);
			$paramArr[] = UuidFactory::getUuidV4();
			$this->typeStr = 'is';
			$this->setParameterArr($inputArr);
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
						$this->matSampleID = $stmt->insert_id;
						$status = true;
					}
					else $this->errorMessage = 'ERROR inserting material sample record (2): '.$stmt->error;
				}
				else $this->errorMessage = 'ERROR inserting material sample record (1): '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for material sample insert: '.$this->conn->error;
		}
		return $status;
	}

	public function updateMaterialSample($inputArr){
		$status = false;
		if($this->matSampleID && $this->conn){
			$this->setParameterArr($inputArr);
			$paramArr = array();
			$sqlFrag = '';
			foreach($this->parameterArr as $fieldName => $value){
				$sqlFrag .= $fieldName . ' = ?, ';
				$paramArr[] = $value;
			}
			$paramArr[] = $this->matSampleID;
			$this->typeStr .= 'i';
			$sql = 'UPDATE ommaterialsample SET '.trim($sqlFrag, ', ').' WHERE (matSampleID = ?)';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($this->typeStr, ...$paramArr);
				$stmt->execute();
				if($stmt->affected_rows || !$stmt->error) $status = true;
				else $this->errorMessage = 'ERROR updating material sample: '.$stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = 'ERROR preparing statement for updating material sample: '.$this->conn->error;
		}
		return $status;
	}

	public function deleteMaterialSample(){
		if($this->matSampleID){
			$sql = 'DELETE FROM ommaterialsample WHERE matSampleID = '.$this->matSampleID;
			if($this->conn->query($sql)){
				return true;
			}
			else{
				$this->errorMessage = 'ERROR deleting material sample record: '.$this->conn->error;
				return false;
			}
		}
	}

	private function setParameterArr($inputArr){
		foreach($this->schemaMap as $field => $type){
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			elseif(isset($inputArr['ms_' . $field])) $postField = 'ms_' . $field;

			if($postField){
				$value = trim($inputArr[$postField]);
				if($value === '') $value = null;
				elseif($value){
					if(strtolower($postField) == 'preparationdate') $value = OccurrenceUtil::formatDate($value);
					if(strtolower($postField) == 'preparedbyuid') $value = OccurrenceUtil::verifyUser($value, $this->conn);
				}
				$this->parameterArr[$field] = $value;
				$this->typeStr .= $type;
			}
		}
		if(isset($inputArr['occid']) && $inputArr['occid'] && !$this->occid) $this->occid = $inputArr['occid'];
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
			if(substr($k,0,3) == 'ms_') $postArr[$k] = htmlspecialchars($v, ENT_COMPAT | ENT_HTML401 | ENT_SUBSTITUTE);
		}
	}

	//Setters and getters
	public function setMatSampleID($id){
		if(is_numeric($id)) $this->matSampleID = $id;
	}

	public function getMatSampleID(){
		return $this->matSampleID;
	}

	public function setOccid($id){
		if(is_numeric($id)) $this->occid = $id;
	}

	public function getOccid(){
		return $this->occid;
	}

	public function getSchemaMap(){
		return $this->schemaMap;
	}

	public function getErrorMessage(){
		return $this->errorMessage;
	}

	/**
	 * Function to get key values of material sample fields and their translated
	 * display text.
	 *
	 * NOTE /content/lang/collections/fieldterms/materialSampleVars.[$LANG_TAG].php
	 * must be loaded for this work see classes/utilities/Language.php for loading it.
	 *
	 * @return Array Key Value array with translated field names
	 * @throws conditon
	 **/
	public static function getMsLabels(): Array {
		global $LANG;
		Language::load('collections/fieldterms/materialSampleVars');
		return [
			'sampleType' => $LANG['SAMPLE_TYPE'],
			'catalogNumber' => $LANG['CATALOG_NUMBER'],
			'guid' => $LANG['GUID'],
			'sampleCondition' => $LANG['SAMPLE_CONDITION'],
			'disposition' => $LANG['DISPOSITION'],
			'preservationType' => $LANG['PRESERVATION_TYPE'],
			'preparationDetails' => $LANG['PREPARATION DETAILS'],
			'preparationDate' => $LANG['PREPARATION_DATE'],
			'preparedBy' => $LANG['PREPARED_BY'],
			'individualCount' => $LANG['INDIVIDUAL COUNT'],
			'sampleSize' => $LANG['SAMPLE_SIZE'],
			'storageLocation' => $LANG['STORAGE_LOCATION'],
			'remarks' => $LANG['REMARKS'],
			'concentration' => $LANG['CONCENTRATION'],
			'concentrationUnit' => $LANG['CONCENTRATION_UNIT'],
			'ratioOfAbsorbance260/230' => $LANG['RATIO_OF_ABSORBANCE_260/230'],
			'ratioOfAbsorbance260/230' => $LANG['RATIO_OF_ABSORBANCE_260/280'],
			'volume' => $LANG['VOLUME'],
			'weight' => $LANG['WEIGHT'],
			'weightMethod' => $LANG['WEIGHT_METHOD'],
			'purification Method' => $LANG['PURIFICATION_METHOD'],
			'quality' => $LANG['QUALITY'],
			'qualityRemarks' => $LANG['QUALITY_REMARKS'],
			'qualityCheckDate' => $LANG['QUALITY_CHECK_DATE'],
			'sampleSize' => $LANG['SAMPLE_SIZE'],
			'sieving' => $LANG['SIEVING'],
			'dnaHybridization' => $LANG['DNA_HYBRIDIZATION'],
			'dnaMeltingPoint' => $LANG['DNA_MELTING_POINT'],
			'estimatedSize' => $LANG['ESTIMATED_SIZE'],
			'poolDnaExtracts' => $LANG['POOL_DNA_EXTRACTS'],
			'sampleDesignation' => $LANG['SAMPLE_DESIGNATION']
		];
	 }
}
?>
