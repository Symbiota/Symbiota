<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/ImageShared.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMaintenance.php');

class OccurrenceImport extends Manager{

	private $collid;
	private $importType;
	private $createNewRecord = false;

	private $uploadTargetPath;
	private $importFileName;

	private $translationMap = null;
	private $fieldMap = array();		//array(sourceName => symbIndex)

	const IMPORT_DETERMINATIONS = 1;
	const IMPORT_IMAGE_MAP = 2;
	const IMPORT_MATERIAL_SAMPLE = 3;
	const IMPORT_ASSOCIATIONS = 4;

	function __construct() {
		parent::__construct(null, 'write');
		set_time_limit(2000);
		ini_set('auto_detect_line_endings', true);
	}

	function __destruct(){
		parent::__destruct();
	}

	public function loadData($postArr){
		if(isset($postArr['filename']) && isset($postArr['tf'])){
			$this->fieldMap = array_flip($postArr['tf']);
			$fullPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) != '/'?'/':'').'temp/data/'.$postArr['filename'];
			if($fh = fopen($fullPath,'rb')){
				$this->initProcessor('processing/imgmap');
				$this->logOrEcho('Starting to processing input file '.$postArr['filename'].' ('.date('Y-m-d H:i:s').')');
				fgetcsv($fh);	//Advance one row to skipper header row
				$cnt = 1;
				$importManager = null;
				if($this->importType == IMPORT_IMAGE_MAP) $importManager = new ImageShared($this->conn);
				elseif($this->importType == IMPORT_MATERIAL_SAMPLE) $importManager = new OccurrenceEditorMaterialSample();
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
						if($this->importType == IMPORT_IMAGE_MAP){
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
						elseif($this->importType == IMPORT_ASSOCIATIONS){
							foreach($occidArr as $occid => $tid){

							}
						}
						elseif($this->importType == IMPORT_DETERMINATIONS){
							foreach($occidArr as $occid => $tid){

							}
						}
						elseif($this->importType == IMPORT_MATERIAL_SAMPLE){
							foreach($occidArr as $occid => $tid){
								$msArr = array('occid' => $occid);
								if(!empty($recordArr[$this->fieldMap['sampletype']])) $msArr['sampleType'] = $recordArr[$this->fieldMap['sampletype']];
								if(!empty($recordArr[$this->fieldMap['catalognumber']])) $msArr['catalogNumber'] = $recordArr[$this->fieldMap['catalognumber']];
								if(!empty($recordArr[$this->fieldMap['guid']])) $msArr['guid'] = $recordArr[$this->fieldMap['guid']];
								if(!empty($recordArr[$this->fieldMap['samplecondition']])) $msArr['sampleCondition'] = $recordArr[$this->fieldMap['samplecondition']];
								if(!empty($recordArr[$this->fieldMap['disposition']])) $msArr['disposition'] = $recordArr[$this->fieldMap['disposition']];
								if(!empty($recordArr[$this->fieldMap['preservationtype']])) $msArr['preservationType'] = $recordArr[$this->fieldMap['preservationtype']];
								if(!empty($recordArr[$this->fieldMap['preparationdetails']])) $msArr['preparationDetails'] = $recordArr[$this->fieldMap['preparationdetails']];
								if(!empty($recordArr[$this->fieldMap['preparationdate']])) $msArr['preparationDate'] = $recordArr[$this->fieldMap['preparationdate']];
								if(!empty($recordArr[$this->fieldMap['preparedbyuid']])) $msArr['preparedByUid'] = $recordArr[$this->fieldMap['preparedbyuid']];
								if(!empty($recordArr[$this->fieldMap['individualcount']])) $msArr['individualCount'] = $recordArr[$this->fieldMap['individualcount']];
								if(!empty($recordArr[$this->fieldMap['samplesize']])) $msArr['sampleSize'] = $recordArr[$this->fieldMap['samplesize']];
								if(!empty($recordArr[$this->fieldMap['storagelocation']])) $msArr['storageLocation'] = $recordArr[$this->fieldMap['storagelocation']];
								if(!empty($recordArr[$this->fieldMap['remarks']])) $msArr['remarks'] = $recordArr[$this->fieldMap['remarks']];
								if($importManager->insertMaterialSample($msArr)){
									$this->logOrEcho('ERROR loading Material Sample: '.$importManager->getErrorMessage(), 1);
								}
							}
						}
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

	//Identifier and occid functions
	private function getOccurrencePK($identifierArr){
		$retArr = array();
		$sql = 'SELECT DISTINCT o.occid, o.tidinterpreted FROM omoccurrences o ';
		$sqlConditionArr = array();
		if(isset($identifierArr[])){
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
				$retArr[$r->occid] = $r->tidinterpreted;
			}
			$rs->free();
		}
		if(!$retArr){
			if($this->createNewRecord){
				$newOccid = $this->insertNewOccurrence($identifierArr);
				if($newOccid) $retArr[$newOccid] = 0;
			}
			else $this->logOrEcho('SKIPPED: Unable to find record matching identifier; image not mapped', 1);
		}

		return $retArr;
	}

	private function insertNewOccurrence($identifierArr){
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

	private function insertAdditionalIdentifier($occid, $identifierValue){
		$status = false;
		$sql = 'INSERT INTO omoccuridentifiers(occid, identifierValue, modifiedUid) VALUES(?, ?, ?) ';
		if($stmt = $this->conn->prepare($sql)) {
			$stmt->bind_param('iss', $occid, $identifierValue, $GLOBALS['SYMB_UID']);
			$stmt->execute();
			if($stmt->affected_rows || !$stmt->error) $status = true;
			else $this->errorMessage = 'ERROR updating omcollections record: '.$stmt->error;
			$stmt->close();
		}
		else $this->errorMessage = 'ERROR preparing statement for inserting additional identifier: '.$this->conn->error;
		return $status;
	}

	//Mapping functions
	public function getTargetFieldArr(){
		$retArr = array();
		$fieldArr = array('catalogNumber', 'otherCatalogNumbers', 'occurrenceID');
		if($this->importType == IMPORT_IMAGE_MAP){
			$fieldArr = array('url','originalUrl','thumbnailUrl','photographer','caption','sourceUrl','anatomy','notes','owner','copyright','sortOccurrence');
		}
		elseif($this->importType == IMPORT_ASSOCIATIONS){
			$fieldArr = array();
		}
		elseif($this->importType == IMPORT_DETERMINATIONS){
			$fieldArr = array();
		}
		elseif($this->importType == IMPORT_MATERIAL_SAMPLE){
			$fieldArr = array();
		}
		foreach($fieldArr as $field){
			$retArr[strtolower($field)] = $field;
		}
		return $retArr;
	}

	public function getTranslation($sourceField){
		$retStr = strtolower($sourceField);
		$this->setTranslationMap();
		$retStr = preg_replace('/[^a-z]+/', '', $retStr);
		if(array_key_exists($retStr, $this->translationMap)) $retStr = $this->translationMap[$retStr];
		return $retStr;
	}

	private function setTranslationMap(){
		if($this->translationMap === null){
			if($this->importType == IMPORT_IMAGE_MAP){
				$this->translationMap = array('web' => 'url', 'webviewoptional' => 'url', 'thumbnail' => 'thumbnailurl','thumbnailoptional' => 'thumbnailurl',
					'largejpg' => 'originalurl', 'large' => 'originalurl', 'imageurl' => 'url', 'accessuri' => 'url');
			}
			elseif($this->importType == IMPORT_ASSOCIATIONS){
				$this->translationMap = array();
			}
			elseif($this->importType == IMPORT_DETERMINATIONS){
				$this->translationMap = array();
			}
			elseif($this->importType == IMPORT_MATERIAL_SAMPLE){
				$this->translationMap = array();
			}
		}
	}

	public function getSourceFieldArr(){
		$sourceArr = array();
		if($this->importFileName){
			$importPath = $this->getImportTargetPath();
			$fh = fopen($importPath . $this->importFileName, 'rb') or die('unable to open file');
			$headerArr = fgetcsv($fh);
			foreach($headerArr as $k => $field){
				$fieldStr = trim($field);
				if($fieldStr) $sourceArr[$k] = $fieldStr;
			}
		}
		return $sourceArr;
	}

	//File management functions
	public function setImportFile(){
		if($importPath = $this->getImportTargetPath()){
			$fileName = '';
			if(array_key_exists('importfile', $_FILES)){
				$fileName = $this->cleanFileName($_FILES['importfile']['name']);
				if(!move_uploaded_file($_FILES['importfile']['tmp_name'], $importPath . $fileName)){
					$this->errorMessage = 'FATAL ERROR: unable to move upload file';
					return false;
				}
			}
			if(file_exists($importPath . $fileName) && substr($$fileName, -4) == '.zip'){
				$zip = new ZipArchive;
				$zip->open($importPath. $fileName);
				$zipFile = $importPath . $fileName;
				$fileName = $zip->getNameIndex(0);
				$zip->extractTo($importPath);
				$zip->close();
				unlink($zipFile);
				$cleanFileName = $this->cleanFileName($fileName);
				rename($importPath.$fileName, $importPath.$cleanFileName);
	        }
	        if($fileName){
		        $this->importFileName = $fileName;
		        return true;
	        }
		}
		return false;
	}

	public function cleanFileName($fileName){
		$ext = strtolower(substr($fileName, -3));
		if($ext != 'csv' && $ext != 'zip') return false;
		$fileName = substr($fileName, 0, -4);
		$fileName = str_replace(array('%20', '%23' ,' ','__'), '_', $fileName);
		$fileName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $fileName);
		$fileName = trim($fileName,' _-');
		if(strlen($fileName) > 30) $fileName = substr($fileName, 0, 30);
		$fileName .= '_' . time() . '.' . $ext;
		return $fileName;
	}

	private function getImportTargetPath(){
		$targetPath = $GLOBALS['TEMP_DIR_ROOT'];
		if(substr($targetPath,-1) != '/') $targetPath .= '/';
		if(is_dir($targetPath . 'data/')) $targetPath .= 'data/';
		if(!is_dir($targetPath)) $targetPath = $GLOBALS['SERVER_ROOT'].'/temp/data';
		if(!is_writable($targetPath)){
			$this->errorMessage = 'FATAL ERROR: target directory does not exist or is not writable by web server ('.$targetPath.')';
			return false;
		}
		return $targetPath;
	}

	//Basic setters and getters
	public function setCollid($id){
		if(is_numeric($id)) $this->collid = $id;
	}

	public function getCollid(){
		return $this->collid;
	}

	public function setImportType($importType){
		if(is_numeric($importType)) $this->importType = $importType;
	}

	public function setCreateNewRecord($b){
		if($b) $this->createNewRecord = true;
		else $this->createNewRecord = false;
	}

	public function setImportFileName($fileName){
		if($fileName) $this->importFileName = $fileName;
	}

	public function getImportFileName(){
		return $this->importFileName;
	}
}
?>