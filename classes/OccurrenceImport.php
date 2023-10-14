<?php
include_once('UtilitiesFileImport.php');
include_once('ImageShared.php');
include_once('OmMaterialSample.php');
include_once('OmOccurAssociations.php');
include_once('OccurrenceMaintenance.php');

class OccurrenceImport extends UtilitiesFileImport{

	private $collid;
	private $collMetaArr = array();
	private $importType;
	private $createNewRecord = false;

	private $importManager = null;

	private const IMPORT_ASSOCIATIONS = 1;
	private const IMPORT_DETERMINATIONS = 2;
	private const IMPORT_IMAGE_MAP = 3;
	private const IMPORT_MATERIAL_SAMPLE = 4;

	function __construct() {
		parent::__construct(null, 'write');
		set_time_limit(2000);
		ini_set('auto_detect_line_endings', true);
	}

	function __destruct(){
		parent::__destruct();
	}

	public function loadData($postArr){
		if($this->fileName && isset($postArr['tf'])){
			$this->fieldMap = array_flip($postArr['tf']);
			$fullPath = $this->targetPath . $this->fileName;
			if($fh = fopen($fullPath,'rb')){
				$this->logOrEcho('Starting to process input file '.$this->fileName.' ('.date('Y-m-d H:i:s').')');
				fgetcsv($fh);	//Advance one row to skipper header row
				$cnt = 1;
				while($recordArr = fgetcsv($fh)){
					$identifierArr = array();
					if(isset($this->fieldMap['occurrenceid'])){
						if($recordArr[$this->fieldMap['occurrenceid']]) $identifierArr['occurrenceID'] = $recordArr[$this->fieldMap['occurrenceid']];
						unset($this->fieldMap['occurrenceid']);
					}
					if(isset($this->fieldMap['catalognumber'])){
						if($recordArr[$this->fieldMap['catalognumber']]) $identifierArr['catalogNumber'] = $recordArr[$this->fieldMap['catalognumber']];
						unset($this->fieldMap['catalognumber']);
					}
					if(isset($this->fieldMap['othercatalognumbers'])){
						if(recordArr[$this->fieldMap['othercatalognumbers']]) $identifierArr['otherCatalogNumbers'] = $recordArr[$this->fieldMap['othercatalognumbers']];
						unset($this->fieldMap['othercatalognumbers']);
					}
					$this->logOrEcho('#'.$cnt.': Processing Catalog Number: '.implode(', ', $identifierArr));
					if($occidArr = $this->getOccurrencePK($identifierArr)){
						$this->insertRecord($recordArr, $occidArr);
					}
					$cnt++;
				}
				fclose($fh);

				$occurMain = new OccurrenceMaintenance($this->conn);
				$this->logOrEcho('Updating statistics...');
				if(!$occurMain->updateCollectionStatsBasic($this->collid)){
					$errorArr = $occurMain->getErrorArr();
					foreach($errorArr as $errorStr){
						$this->logOrEcho($errorStr,1);
					}
				}
			}
			unlink($fullPath);
			$this->logOrEcho('Done process image mapping ('.date('Y-m-d H:i:s').')');
		}
	}

	private function insertRecord($recordArr, $occidArr){
		if($this->importType == self::IMPORT_IMAGE_MAP){
			$importManager = new ImageShared($this->conn);
			if(!isset($this->fieldMap['originalurl']) || $recordArr[$this->fieldMap['originalurl']]) return false;
			foreach($occidArr as $occid => $tid){
				$importManager->setOccid($occid);
				$importManager->setTid($tid);
				$importManager->setImgLgUrl($recordArr[$this->fieldMap['originalurl']]);
				if(!empty($recordArr[$this->fieldMap['url']])) $importManager->setImgWebUrl($recordArr[$this->fieldMap['url']]);
				if(!empty($recordArr[$this->fieldMap['thumbnailurl']])) $importManager->setImgTnUrl($recordArr[$this->fieldMap['thumbnailurl']]);
				if(!empty($recordArr[$this->fieldMap['photographer']])) $importManager->setPhotographer($recordArr[$this->fieldMap['photographer']]);
				if(!empty($recordArr[$this->fieldMap['caption']])) $importManager->setCaption($recordArr[$this->fieldMap['caption']]);
				if(!empty($recordArr[$this->fieldMap['sourceUrl']])) $importManager->setSourceUrl($recordArr[$this->fieldMap['sourceUrl']]);
				if(!empty($recordArr[$this->fieldMap['anatomy']])) $importManager->setAnatomy($recordArr[$this->fieldMap['anatomy']]);
				if(!empty($recordArr[$this->fieldMap['notes']])) $importManager->setNotes($recordArr[$this->fieldMap['notes']]);
				if(!empty($recordArr[$this->fieldMap['owner']])) $importManager->setOwner($recordArr[$this->fieldMap['owner']]);
				if(!empty($recordArr[$this->fieldMap['copyright']])) $importManager->setCopyright($recordArr[$this->fieldMap['copyright']]);
				if(!empty($recordArr[$this->fieldMap['sortoccurrence']])) $importManager->setSortOccurrence($recordArr[$this->fieldMap['sortOccurrence']]);
				if($importManager->insertImage()){
					$this->logOrEcho('Image loaded suucessfully!', 1);
				}
				else{
					$this->logOrEcho('ERROR loading image: '.$importManager->getErrStr(), 1);
				}
				$importManager->reset();
			}
		}
		elseif($this->importType == self::IMPORT_DETERMINATIONS){
			foreach($occidArr as $occid => $tid){

			}
		}
		elseif($this->importType == self::IMPORT_ASSOCIATIONS){
			$importManager = new OmOccurAssociations();
			foreach($occidArr as $occid => $tid){
				$importManager->setOccid($occid);
				$fieldArr = array_keys($importManager->getFieldMap());
				$assocArr = array();
				foreach($fieldArr as $field){
					$fieldLower = strtolower($field);
					if(!empty($recordArr[$this->fieldMap[$fieldLower]])) $assocArr[$field] = $recordArr[$this->fieldMap[$fieldLower]];
				}
				if(!$importManager->insertAssociations($assocArr)){
					$this->logOrEcho('ERROR loading Occurrence Association: '.$importManager->getErrorMessage(), 1);
				}
			}
		}
		elseif($this->importType == self::IMPORT_MATERIAL_SAMPLE){
			$importManager = new OmMaterialSample($this->conn);
			foreach($occidArr as $occid => $tid){
				$importManager->setOccid($occid);
				$fieldArr = array_keys($importManager->getFieldMap());
				$msArr = array();
				foreach($fieldArr as $field){
					$fieldLower = strtolower($field);
					if(!empty($recordArr[$this->fieldMap[$fieldLower]])) $msArr[$field] = $recordArr[$this->fieldMap[$fieldLower]];
				}
				if(!$importManager->insertMaterialSample($msArr)){
					$this->logOrEcho('ERROR loading Material Sample: '.$importManager->getErrorMessage(), 1);
				}
			}
		}
	}

	//Identifier and occid functions
	protected function getOccurrencePK($identifierArr){
		$retArr = array();
		$sql = 'SELECT DISTINCT o.occid FROM omoccurrences o ';
		$sqlConditionArr = array();
		if(isset($identifierArr['occurrenceID'])){
			$occurrenceID = $this->cleanInStr($identifierArr['occurrenceID']);
			$sqlConditionArr[] = '(o.occurrenceID = "'.$occurrenceID.'" OR o.recordID = "'.$occurrenceID.'")';
		}
		if(isset($identifierArr['catalogNumber'])){
			$sqlConditionArr[] = '(o.catalogNumber = "'.$this->cleanInStr($identifierArr['catalogNumber']).'")';
		}
		if(isset($identifierArr['otherCatalogNumbers'])){
			$otherCatalogNumbers = $this->cleanInStr($identifierArr['otherCatalogNumbers']);
			$sqlConditionArr[] = '(o.othercatalognumbers = "'.$otherCatalogNumbers.'" OR i.identifierValue = "'.$otherCatalogNumbers.'")';
			$sql .= 'LEFT JOIN omoccuridentifiers i ON o.occid = i.occid ';
		}
		if($sqlConditionArr){
			$sql .= 'WHERE (o.collid = '.$this->collid.') AND ('.implode(' OR ', $sqlConditionArr).') ';
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$retArr[] = $r->occid;
			}
			$rs->free();
		}
		if(!$retArr){
			if($this->createNewRecord){
				$newOccid = $this->insertNewOccurrence($identifierArr);
				if($newOccid) $retArr[] = $newOccid;
			}
			else $this->logOrEcho('SKIPPED: Unable to find record matching identifier; image not mapped', 1);
		}

		return $retArr;
	}

	protected function insertNewOccurrence($identifierArr){
		$newOccid = 0;
		if(isset($identifierArr['occurrenceID'])){
			$this->logOrEcho('SKIPPED: Unable to create new record based on occurrenceID', 1);
			return false;
		}
		$sql1 = 'INSERT INTO omoccurrences(collid, processingstatus, dateentered';
		$sql2 = 'VALUES('.$this->collid.', "unprocessed", now()';
		if(isset($identifierArr['catalogNumber'])){
			$sql1 .= ', catalogNumber';
			$sql2 .= ', '.$this->cleanInStr($identifierArr['catalogNumber']);
		}
		$sql = $sql1.') '.$sql2.')';
		if($this->conn->query($sql)){
			$newOccid = $this->conn->insert_id;
			if($newOccid){
				if(isset($identifierArr['otherCatalogNumbers'])) $this->insertAdditionalIdentifier($newOccid, $identifierArr['otherCatalogNumbers']);
				$this->logOrEcho('Unable to find record with matching '.implode(',', $identifierArr).'; new occurrence record created',1);
			}
		}
		else{
			$this->logOrEcho('ERROR creating new occurrence record: '.$this->conn->error,1);
		}
		return $newOccid;
	}

	protected function insertAdditionalIdentifier($occid, $identifierValue){
		$status = false;
		$sql = 'INSERT INTO omoccuridentifiers(occid, identifierValue, modifiedUid) VALUES(?, ?, ?) ';
		if($stmt = $this->conn->prepare($sql)) {
			$stmt->bind_param('iss', $occid, $identifierValue, $GLOBALS['SYMB_UID']);
			$stmt->execute();
			if($stmt->affected_rows || !$stmt->error) $status = true;
			else $this->errorMessage = 'ERROR inserting additional identifier: '.$stmt->error;
			$stmt->close();
		}
		else $this->errorMessage = 'ERROR preparing statement for inserting additional identifier: '.$this->conn->error;
		return $status;
	}

	//Mapping functions
	public function setTargetFieldArr(){
		$fieldArr = array();
		if($this->importType == self::IMPORT_IMAGE_MAP){
			$fieldArr = array('url','originalUrl','thumbnailUrl','photographer','caption','sourceUrl','anatomy','notes','owner','copyright','sortOccurrence');
		}
		elseif($this->importType == self::IMPORT_ASSOCIATIONS){
			$fieldArr = array('occidAssociate', 'relationship', 'relationshipID', 'subType', 'identifier', 'basisOfRecord', 'resourceUrl', 'verbatimSciname');
		}
		elseif($this->importType == self::IMPORT_DETERMINATIONS){
			$fieldArr = array();
		}
		elseif($this->importType == self::IMPORT_MATERIAL_SAMPLE){
			$fieldArr = array();
		}
		$fieldArr[] = 'catalogNumber';
		$fieldArr[] = 'otherCatalogNumbers';
		$fieldArr[] = 'occurrenceID';
		foreach($fieldArr as $field){
			$this->targetFieldMap[strtolower($field)] = $field;
		}
	}

	private function defineTranslationMap(){
		if($this->translationMap === null){
			if($this->importType == self::IMPORT_IMAGE_MAP){
				$this->translationMap = array('web' => 'url', 'webviewoptional' => 'url', 'thumbnail' => 'thumbnailurl','thumbnailoptional' => 'thumbnailurl',
					'largejpg' => 'originalurl', 'large' => 'originalurl', 'imageurl' => 'url', 'accessuri' => 'url');
			}
			elseif($this->importType == self::IMPORT_ASSOCIATIONS){
				$this->translationMap = array();
			}
			elseif($this->importType == self::IMPORT_DETERMINATIONS){
				$this->translationMap = array();
			}
			elseif($this->importType == self::IMPORT_MATERIAL_SAMPLE){
				$this->translationMap = array();
			}
		}
	}

	//Data set functions
	private function setCollMetaArr(){
		$sql = 'SELECT institutionCode, collectionCode, collectionName FROM omcollections WHERE collid = '.$this->collid;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->collMetaArr['instCode'] = $r->institutionCode;
			$this->collMetaArr['collCode'] = $r->collectionCode;
			$this->collMetaArr['collName'] = $r->collectionName;
		}
		$rs->free();
	}

	//Basic setters and getters
	public function setCollid($id){
		if(is_numeric($id)) $this->collid = $id;
	}

	public function getCollid(){
		return $this->collid;
	}

	public function getCollMeta($field){
		$fieldValue = '';
		if(isset($this->collMetaArr[$field])) return $this->collMetaArr[$field];
		return $fieldValue;
	}

	public function setCreateNewRecord($b){
		if($b) $this->createNewRecord = true;
		else $this->createNewRecord = false;
	}

	public function setImportType($importType){
		if(is_numeric($importType)) $this->importType = $importType;
		$this->defineTranslationMap();
	}
}
?>