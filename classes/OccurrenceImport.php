<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
include_once($SERVER_ROOT.'/classes/UuidFactory.php');
include_once($SERVER_ROOT.'/classes/OccurrenceMaintenance.php');

class OccurrenceImport extends Manager{

	private $uploadType;
	private $matchByOccurrenceID = true;
	private $matchByCatalogNumber = false;
	private $matchByOtherCatalogNumbers = false;
	private $createNewRecord = false;
	private $processingCnt = 1;

	private $uploadTargetPath;
	private $uploadFileName;

	private $targetArr;
	private $fieldMap = array();		//array(sourceName => symbIndex)
	private $translationMap = array('imageurl'=>'url','accessuri'=>'url','sciname'=>'scientificname');

	const IMPORT_IMAGE_MAP = 1;
	const IMPORT_ASSOCIATIONS = 2;
	const IMPORT_DETERMINATIONS = 3;

	function __construct() {
		parent::__construct(null, 'write');
		set_time_limit(2000);
		ini_set('auto_detect_line_endings', true);
		$this->setUploadTargetPath();
	}

	function __destruct(){
		parent::__destruct();
		if(file_exists($this->uploadTargetPath.$this->uploadFileName)){
			//unlink($this->uploadTargetPath.$this->uploadFileName);
		}
	}

	public function loadData($postArr){
		if(isset($postArr['filename']) && isset($postArr['tf'])){
			$this->fieldMap = array_flip($postArr['tf']);
			$fullPath = $GLOBALS['SERVER_ROOT'].(substr($GLOBALS['SERVER_ROOT'],-1) != '/'?'/':'').'temp/data/'.$postArr['filename'];
			if($fh = fopen($fullPath,'rb')){
				$this->initProcessor('processing/imgmap');
				$this->logOrEcho('Starting to processing input file '.$postArr['filename'].' ('.date('Y-m-d H:i:s').')');
				fgetcsv($fh);	//Advance one row to skipper header row
				while($recordArr = fgetcsv($fh)){
					$identifierArr = array();
					if(isset($this->fieldMap['occurrenceid'])){
						if($recordArr[$this->fieldMap['occurrenceid']]) $identifierArr['occurrenceID'] = $recordArr[$this->fieldMap['occurrenceid']];
					}
					if(isset($this->fieldMap['catalognumber'])){
						if($recordArr[$this->fieldMap['catalognumber']]) $identifierArr['catalogNumber'] = $recordArr[$this->fieldMap['catalognumber']];
					}
					if(isset($this->fieldMap['othercatalognumbers'])){
						if(recordArr[$this->fieldMap['othercatalognumbers']]) $identifierArr['otherCatalogNumbers'] = $recordArr[$this->fieldMap['othercatalognumbers']];
					}
					$this->logOrEcho('#'.$this->processingCnt.': Processing Catalog Number: '.implode(', ', $identifierArr));
					$occidArr = $this->getOccurrencePK($identifierArr);
					if(!$occidArr){
						if($this->createNewRecord){
							$newOccid = $this->insertNewOccurrence($identifierArr);
							if($newOccid) $occidArr[$newOccid] = 0;
							else $this->logOrEcho('Unable to find record with matching '.($catalogNumber?'catalogNumber':'otherCatalogNumbers').'; new occurrence record created',1);
						}
						else $this->logOrEcho('SKIPPED: Unable to find record matching identifier; image not mapped', 1);
					}
					if($occidArr){
						if($this->uploadType == IMPORT_IMAGE_MAP){
							$this->importImage($recordArr, $occidArr);
						}
						elseif($this->uploadType == IMPORT_ASSOCIATIONS){

						}
						elseif($this->uploadType == IMPORT_DETERMINATIONS){

						}
					}
					$this->processingCnt++;
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

				$this->logOrEcho('Generating recordID GUID for all new records...');
				$uuidManager = new UuidFactory();
				$uuidManager->setSilent(1);
				$uuidManager->populateGuids($this->collid);
			}
			unlink($fullPath);
			$this->logOrEcho('Done process image mapping ('.date('Y-m-d H:i:s').')');
		}
	}

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
			if($newOccid && isset($identifierArr['otherCatalogNumbers'])){
				$this->insertAdditionalIdentifier($newOccid, $identifierArr['otherCatalogNumbers']);
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

	private function importImage($recordArr, $occidArr){
		$originalUrl = null;
		if(isset($this->fieldMap['originalurl'])) $originalUrl = $recordArr[$this->fieldMap['originalurl']];
		$url = null;
		if(isset($this->fieldMap['url'])) $url = $recordArr[$this->fieldMap['url']];
		$thumbnailUrl = null;
		if(isset($this->fieldMap['thumbnailurl'])) $thumbnailUrl = $recordArr[$this->fieldMap['thumbnailurl']];
		$sourceUrl = null;
		if(isset($this->fieldMap['sourceurl'])) $sourceUrl = $recordArr[$this->fieldMap['sourceurl']];

		//Check to see if image with matching filename is already linked. If so, remove and replace with new
		$origUrl = substr($originalUrl, 5);
		$baseUrl = substr($url, 5);
		foreach($occidArr as $occid => $tid){
			$sql = 'SELECT imgid, url, originalurl, thumbnailurl FROM images WHERE (occid = ?)';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $occid);
				$stmt->execute();
				$stmt->bind_result($imgID, $testBaseUrl, $testOrigUrl, $testThumbUrl);
				while ($stmt->fetch()){
					$testOrigUrl = substr($testOrigUrl, 5);
					$testBaseUrl = substr($testBaseUrl, 5);
					$replaceImg = false;
					if($testOrigUrl && $testOrigUrl == $origUrl) $replaceImg = true;
					elseif($testBaseUrl && $testBaseUrl == $baseUrl) $replaceImg = true;
					elseif($testOrigUrl && $testOrigUrl == $baseUrl) $replaceImg = true;
					elseif($testBaseUrl && $testBaseUrl == $origUrl) $replaceImg = true;
					if($replaceImg){
						$sql2 = 'UPDATE images SET url = ?, originalurl = ?, thumbnailurl = ?, sourceurl = ? WHERE imgid = ?';
						if($stmt = $this->conn->prepare($sql2)){
							$stmt->bind_param('i', $imgID);
							$stmt->execute();

						}
						if($this->conn->query($sql2)){
							$this->logOrEcho('Existing image replaced with new image mapping: <a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.($catalogNumber?$catalogNumber:$otherCatalogNumbers).'</a>',1);
							//Delete physical images if previous version was mapped locally
							$this->deleteImage($r1->url);
							$this->deleteImage($r1->originalurl);
							$this->deleteImage($r1->thumbnailurl);
							unset($occArr[$occid]);
							break;
						}
						else{
							$this->logOrEcho('ERROR updating existing image record: '.$this->conn->error,1);
						}

					}
				}
				$stmt->close();
			}


			$rs1 = $this->conn->query($sql1);
			while($r1 = $rs1->fetch_object()){
				$testOrigUrl = substr($r1->originalurl,5);
				$testBaseUrl = substr($r1->url,5);
				$replaceImg = false;
				if($testOrigUrl && $testOrigUrl == $origUrl) $replaceImg = true;
				elseif($testBaseUrl && $testBaseUrl == $baseUrl) $replaceImg = true;
				elseif($testOrigUrl && $testOrigUrl == $baseUrl) $replaceImg = true;
				elseif($testBaseUrl && $testBaseUrl == $origUrl) $replaceImg = true;
				if($replaceImg){
					$sql2 = 'UPDATE images '.
							'SET url = "'.$url.'", originalurl = "'.$originalUrl.'", thumbnailurl = '.($thumbnailUrl?'"'.$thumbnailUrl.'"':'NULL').', '.
							'sourceurl = '.($sourceUrl?'"'.$sourceUrl.'"':'NULL').' '.
							'WHERE imgid = '.$r1->imgid;
					if($this->conn->query($sql2)){
						$this->logOrEcho('Existing image replaced with new image mapping: <a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.($catalogNumber?$catalogNumber:$otherCatalogNumbers).'</a>',1);
						//Delete physical images if previous version was mapped locally
						$this->deleteImage($r1->url);
						$this->deleteImage($r1->originalurl);
						$this->deleteImage($r1->thumbnailurl);
						unset($occArr[$occid]);
						break;
					}
					else{
						$this->logOrEcho('ERROR updating existing image record: '.$this->conn->error,1);
					}
				}
			}
			$rs1->free();
		}
		foreach($occidArr as $occid => $tid){
			$fieldArr = array();
			$fieldArr['url'] =  $url;
			if($thumbnailUrl) $fieldArr['thumbnailurl'] =  $thumbnailUrl;
			$fieldArr['originalurl'] =  $originalUrl;
			if($sourceUrl) $fieldArr['sourceurl'] = $sourceUrl;
			if($tid) $fieldArr['tid'] =  $tid;
			if(!$this->insertImage($occid,$fieldArr)) $this->logOrEcho('ERROR loading image: '.$this->conn->error,1);
		}

	}

	//Mapping functions
	public function getTargetFieldArr(){
		$retArr = array();
		if($this->uploadType == IMPORT_IMAGE_MAP){
			$retArr = array('url','originalUrl','scientificName','tid','photographer','photographerUid','caption','locality','sourceUrl','anatomy',
				'notes','owner','copyright','sortSequence','institutionCode','collectionCode','catalogNumber','occid');
		}
		elseif($this->uploadType == IMPORT_ASSOCIATIONS){
			$retArr = array();
		}
		elseif($this->uploadType == IMPORT_DETERMINATIONS){
			$retArr = array();
		}
		return $retArr;
	}

	public function getSourceArr(){
		$sourceArr = array();
		$fh = fopen($this->uploadTargetPath.$this->uploadFileName,'rb') or die("Can't open file");
		$headerArr = fgetcsv($fh);
		foreach($headerArr as $k => $field){
			$fieldStr = strtolower(trim($field));
			if($fieldStr){
				$sourceArr[$k] = $fieldStr;
			}
		}
		return $sourceArr;
	}

	public function getTranslation($inStr){
		$retStr = '';
		$inStr = strtolower($inStr);
		if(array_key_exists($inStr,$this->translationMap)) $retStr = $this->translationMap[$inStr];
		return $retStr;
	}

	//File management functions
	public function setUploadFile($ulFileName){
		if($ulFileName){
			$this->uploadFileName = $ulFileName;
		}
		elseif(array_key_exists('uploadfile',$_FILES)){
			$this->uploadFileName = time().'_'.$_FILES['uploadfile']['name'];
			if(!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $this->uploadTargetPath.$this->uploadFileName)){
				//echo 'Error';
			}
		}
        if(file_exists($this->uploadTargetPath.$this->uploadFileName) && substr($this->uploadFileName,-4) == ".zip"){
			$zip = new ZipArchive;
			$zip->open($this->uploadTargetPath.$this->uploadFileName);
			$zipFile = $this->uploadTargetPath.$this->uploadFileName;
			$fileName = $zip->getNameIndex(0);
			$zip->extractTo($this->uploadTargetPath);
			$zip->close();
			unlink($zipFile);
			$this->uploadFileName = time().'_'.$fileName;
			rename($this->uploadTargetPath.$fileName,$this->uploadTargetPath.$this->uploadFileName);
        }
	}

	private function setUploadTargetPath(){
		$tPath = $GLOBALS["tempDirRoot"];
		if(!$tPath){
			$tPath = ini_get('upload_tmp_dir');
		}
		if(!$tPath){
			$tPath = $GLOBALS["serverRoot"]."/temp/downloads";
		}
		if(substr($tPath,-1) != '/') $tPath .= "/";
		$this->uploadTargetPath = $tPath;
	}

	//Basic setters and getters
	public function setUploadType($uploadType){
		if(is_numeric($uploadType)) $this->uploadType = $uploadType;
	}

	public function setMatchByOccurrenceID($bool){
		if($bool) $this->matchByOccurrenceID = true;
		else $this->matchByOccurrenceID = false;
	}

	public function setMatchByCatalogNumber($bool){
		if($bool) $this->matchByCatalogNumber = true;
		else $this->matchByCatalogNumber = false;
	}

	public function setMatchByOtherCatalogNumbers($bool){
		if($bool) $this->matchByOtherCatalogNumbers = true;
		else $this->matchByOtherCatalogNumbers = false;
	}

	public function setCreateNewRecord($b){
		if($b) $this->createNewRecord = true;
		else $this->createNewRecord = false;
	}

	public function setUploadFileName($fileName){
		$this->uploadFileName = $fileName;
	}

	public function getUploadFileName(){
		return $this->uploadFileName;
	}

	public function getTargetArr(){
		return $this->targetArr;
	}

	public function setFieldMap($fm){
		$this->fieldMap = $fm;
	}

	public function getFieldMap(){
		return $this->fieldMap;
	}
}
?>