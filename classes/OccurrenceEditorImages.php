<?php
include_once($SERVER_ROOT.'/classes/OccurrenceEditorManager.php');
include_once($SERVER_ROOT.'/classes/SpecProcessorOcr.php');
include_once($SERVER_ROOT.'/classes/ImageShared.php');
if($LANG_TAG != 'en' && file_exists($SERVER_ROOT.'/content/lang/classes/OccurrenceEditorImages'.$LANG_TAG.'.php')) include_once($SERVER_ROOT.'/content/lang/classes/OccurrenceEditorImages.'.$LANG_TAG.'.php');
else include_once($SERVER_ROOT.'/content/lang/classes/OccurrenceEditorImages.en.php');

class OccurrenceEditorImages extends OccurrenceEditorManager {

	private $photographerArr = Array();
	private $imageRootPath = "";
	private $imageRootUrl = "";
	private $activeImgId = 0;

	public function __construct(){
 		parent::__construct();
	}

	public function __destruct(){
 		parent::__destruct();
	}

	public function getImageMap(){
		$imageMap = parent::getImageMap();
		if($imageMap){
			$imageTagArr = $this->getImageTags(implode(',',array_keys($imageMap)));
			foreach($imageTagArr as $imgId => $vArr){
				$imageMap[$imgId]['tags'] = $vArr;
			}
		}
		return $imageMap;
	}

	public function getIdentifierArr(){
		$retArr = array();
		$sql = 'SELECT o.catalogNumber, o.otherCatalogNumbers, i.identifierValue
			FROM omoccurrences o LEFT JOIN omoccuridentifiers i ON o.occid = i.occid WHERE (o.occid = '.$this->occid.')';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			if(!$cnt){
				if($r->catalogNumber) $retArr[] = $r->catalogNumber;
				if($r->otherCatalogNumbers) $retArr[] = $r->otherCatalogNumbers;
			}
			if($r->identifierValue) $retArr[] = $r->identifierValue;
			$cnt++;
		}
		$rs->free();
		return $retArr;
	}

	/**
	 * Takes parameters from a form submission and modifies an existing image record
	 * in the database.
	 */
	public function addImageOccurrence($postArr){
		global $LANG;
		$status = true;
		if($this->addOccurrence($postArr)){
			if($this->addImage($postArr)){
				if($this->activeImgId){
					//Load OCR
					$rawStr = '';
					$ocrSource = '';
					if($postArr['ocrblock']){
						$rawStr = trim($postArr['ocrblock']);
						if($postArr['ocrsource']) $ocrSource = $postArr['ocrsource'];
						else $ocrSource = 'User submitted';
					}
					elseif(isset($postArr['tessocr']) && $postArr['tessocr']){
						$ocrManager = new SpecProcessorOcr();
						$rawStr = $ocrManager->ocrImageById($this->activeImgId);
						$ocrSource = 'Tesseract';
					}
					if($rawStr){
						if($ocrSource) $ocrSource .= ': '.date('Y-m-d');
						$sql = 'INSERT INTO specprocessorrawlabels(imgid, rawstr, source) VALUES('.$this->activeImgId.',"'.$this->cleanInStr($rawStr).'","'.$this->cleanInStr($ocrSource).'")';
						if(!$this->conn->query($sql)){
							$this->errorStr = $LANG['ERROR_LOAD_OCR'].': '.$this->conn->error;
						}
					}
				}
			}
		}
		else{
			$status = false;
		}
		return $status;
	}

	public function editImage(){
		global $LANG;
		$this->setRootpaths();
		$status = "Image editted successfully!";
		$imgId = $_REQUEST["imgid"];
	 	$url = $_REQUEST["url"];
	 	$tnUrl = $_REQUEST["tnurl"];
	 	$origUrl = $_REQUEST["origurl"];
	 	if(array_key_exists("renameweburl",$_REQUEST)){
	 		$oldUrl = $_REQUEST["oldurl"];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldUrl);
	 		$newWebName = str_replace($this->imageRootUrl,$this->imageRootPath,$url);
	 		if($url != $oldUrl){
	 			if(file_exists($newWebName)){
 					$status = $LANG['ERROR_UNABLE_MODIFY'].'; ';
		 			$url = $oldUrl;
	 			}
	 			else{
		 			if(!rename($oldName,$newWebName)){
		 				$url = $oldUrl;
			 			$status .= $LANG['URL_FAILED'].'; ';
		 			}
	 			}
	 		}
		}
		if(array_key_exists("renametnurl",$_REQUEST)){
	 		$oldTnUrl = $_REQUEST["oldtnurl"];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldTnUrl);
	 		$newName = str_replace($this->imageRootUrl,$this->imageRootPath,$tnUrl);
	 		if($tnUrl != $oldTnUrl){
	 			if(file_exists($newName)){
 					$status = $LANG['ERROR_FILE_EXISTS'].'; ';
		 			$tnUrl = $oldTnUrl;
	 			}
	 			else{
		 			if(!rename($oldName,$newName)){
		 				$tnUrl = $oldTnUrl;
			 			$status = $LANG['THUMBNAIL_FAILED'].'; ';
		 			}
	 			}
	 		}
		}
		if(array_key_exists("renameorigurl",$_REQUEST)){
	 		$oldOrigUrl = $_REQUEST["oldorigurl"];
	 		$oldName = str_replace($this->imageRootUrl,$this->imageRootPath,$oldOrigUrl);
	 		$newName = str_replace($this->imageRootUrl,$this->imageRootPath,$origUrl);
	 		if($origUrl != $oldOrigUrl){
	 			if(file_exists($newName)){
 					$status = $LANG['ERROR_FILE_EXISTS'].'; ';
		 			$tnUrl = $oldTnUrl;
	 			}
	 			else{
		 			if(!rename($oldName,$newName)){
		 				$origUrl = $oldOrigUrl;
			 			$status .= $LANG['THUMBNAIL_FAILED'].'; ';
		 			}
	 			}
	 		}
		}
		$occId = $_REQUEST['occid']?$_REQUEST['occid']:null;
		$caption = $_REQUEST['caption']?$this->cleanInStr($_REQUEST['caption']):null;
		$photographer = $_REQUEST['photographer']?$this->cleanInStr($_REQUEST['photographer']):null;
		$photographerUid = $_REQUEST['photographeruid']?$_REQUEST['photographeruid']:null;
		$notes = $_REQUEST['notes']?$this->cleanInStr($_REQUEST['notes']):null;
		$copyRight = $_REQUEST['copyright']?$this->cleanInStr($_REQUEST['copyright']):null;
		$sort = is_numeric($_REQUEST['sortoccurrence'])?$_REQUEST['sortoccurrence']:5;
		$sourceUrl = $_REQUEST['sourceurl']?$this->cleanInStr($_REQUEST['sourceurl']):null;

		//If central images are on remote server and new ones stored locally, then we need to use full domain
		//e.g. this portal is sister portal to central portal
		if($GLOBALS['imageDomain']){
			if(substr($url,0,1) == '/'){
				$url = 'http://'.$_SERVER['HTTP_HOST'].$url;
			}
			if($tnUrl && substr($tnUrl,0,1) == '/'){
				$tnUrl = 'http://'.$_SERVER['HTTP_HOST'].$tnUrl;
			}
			if($origUrl && substr($origUrl,0,1) == '/'){
				$origUrl = 'http://'.$_SERVER['HTTP_HOST'].$origUrl;
			}
		}

		$imgUpdateStatus = false;
		$sql = 'UPDATE images SET url=?,thumbnailurl=?,originalurl=?,occid=?,caption=?,photographer=?,photographeruid=?,notes=?,sortoccurrence=?,copyright=?,imagetype=?,sourceurl=? WHERE (imgid= ?)';
		$stmt = $this->conn->stmt_init();
		$stmt->prepare($sql);
		$imageType = 'specimen';
		$stmt->bind_param('sssissisisssi',$url,$tnUrl,$origUrl,$occId,$caption,$photographer, $photographerUid,$notes,$sort,$copyRight,$imageType,$sourceUrl,$imgId);
		if($stmt->execute()) $imgUpdateStatus = true;
		$stmt->close();

		if($imgUpdateStatus){
			// update image tags
			$kArr = $this->getImageTagArr();
			foreach($kArr as $key => $description) {
				// Note: By using check boxes, we can't tell the difference between
				// an unchecked checkbox and the checkboxes not being present on the
				// form, we'll get around this by including the original state of the
				// tags for each image in a hidden field.
				$sql = null;
				if(array_key_exists("ch_$key",$_REQUEST)) {
					if(!$_REQUEST["hidden_$key"]) $sql = 'INSERT IGNORE into imagetag (imgid,keyvalue) values (?,?)';
				}
				else{
					// checkbox is not selected and this tag was used for this image
					if($_REQUEST["hidden_$key"]==1) $sql = 'DELETE from imagetag where imgid = ? and keyvalue = ?';
				}
				if($sql!=null) {
					$stmt = $this->conn->stmt_init();
					$stmt->prepare($sql);
					if ($stmt) {
						$stmt->bind_param('is',$imgId,$key);
						if (!$stmt->execute()) {
							//$status .= ' ('.$LANG['WARNING_FAILED_TAG']." [$key] ".$LANG['FOR']." $imgId.  " . $stmt->error ;
						}
						$stmt->close();
					}
				}
			}
		}
		else $status .= $LANG['ERROR_NOT_CHANGED'].', '.$this->conn->error;
		return $status;
	}

	public function deleteImage($imgIdDel, $removeImg){
		$status = true;
		$imgManager = new ImageShared();
		if(!$imgManager->deleteImage($imgIdDel, $removeImg)){
			$this->errorStr = implode('',$imgManager->getErrArr());
			$status = false;
		}
		return $status;
	}

	public function remapImage($imgId, $targetOccid = 0){
		global $LANG;
		$status = true;
		if(!is_numeric($imgId)){
			return false;
		}
		if($targetOccid == 'new'){
			$sql = 'INSERT INTO omoccurrences(collid, observeruid,processingstatus) SELECT collid, observeruid, "unprocessed" FROM omoccurrences WHERE occid = '.$this->occid;
			if($this->conn->query($sql)){
				$targetOccid = $this->conn->insert_id;
				$status = $targetOccid;
			}
			else{
				$this->errorArr[] = $LANG['UNABLE_RELINK_BLANK'].': '.$this->conn->error;
				return false;
			}
		}
		if($targetOccid && is_numeric($targetOccid)){
			$sql = 'UPDATE images SET occid = '.$targetOccid.' WHERE (imgid = '.$imgId.')';
			if($this->conn->query($sql)){
				$imgSql = 'UPDATE images i INNER JOIN omoccurrences o ON i.occid = o.occid SET i.tid = o.tidinterpreted WHERE (i.imgid = '.$imgId.')';
				//echo $imgSql;
				$this->conn->query($imgSql);
			}
			else{
				$this->errorArr[] = $LANG['UNABLE_REMAP_ANOTHER'].': '.$this->conn->error;
				return false;
			}
		}
		else{
			$sql = 'UPDATE images SET occid = NULL WHERE (imgid = '.$imgId.')';
			if(!$this->conn->query($sql)){
				$this->errorArr[] = $LANG['UNABLE_DISSOCIATE'].': '.$this->conn->error;
				return false;
			}
		}
		return $status;
	}

	public function addImage($postArr){
		$status = true;
		$imgManager = new ImageShared();

		//Set target path
		$subTargetPath = $this->collMap['institutioncode'];
		if($this->collMap['collectioncode']) $subTargetPath .= '_'.$this->collMap['collectioncode'];
		$subTargetPath .= '/';
		if(!$this->occurrenceMap) $this->setOccurArr();
		$catNum = $this->occurrenceMap[$this->occid]['catalognumber'];
		if($catNum){
			$catNum = str_replace(array('/','\\',' '), '', $catNum);
			if(preg_match('/^(\D{0,8}\d{4,})/', $catNum, $m)){
				$catPath = substr($m[1], 0, -3);
				if(is_numeric($catPath) && strlen($catPath)<5) $catPath = str_pad($catPath, 5, "0", STR_PAD_LEFT);
				$subTargetPath .= $catPath.'/';
			}
			else{
				$subTargetPath .= '00000/';
			}
		}
		else{
			$subTargetPath .= date('Ym').'/';
		}
		$imgManager->setTargetPath($subTargetPath);

		//Import large image or not
		if(array_key_exists('nolgimage',$postArr) && $postArr['nolgimage']==1){
			$imgManager->setMapLargeImg(false);
		}
		else{
			$imgManager->setMapLargeImg(true);
		}

		//Set image metadata variables
		if(array_key_exists('caption',$postArr)) $imgManager->setCaption($postArr['caption']);
		if(array_key_exists('photographeruid',$postArr)) $imgManager->setPhotographerUid($postArr['photographeruid']);
		if(array_key_exists('photographer',$postArr)) $imgManager->setPhotographer($postArr['photographer']);
		if(array_key_exists('sourceurl',$postArr)) $imgManager->setSourceUrl($postArr['sourceurl']);
		if(array_key_exists('copyright',$postArr)) $imgManager->setCopyright($postArr['copyright']);
		if(array_key_exists('notes',$postArr)) $imgManager->setNotes($postArr['notes']);
		if(array_key_exists('sortoccurrence',$postArr)) $imgManager->setSortOccurrence($postArr['sortoccurrence']);

		$sourceImgUri = $postArr['imgurl'];
		if($sourceImgUri){
			//Source image is a URI supplied by user
			$imgManager->parseUrl($sourceImgUri);
			$imgWeb = '';
			$imgThumb = '';
			if(isset($postArr['weburl']) && $postArr['weburl']) $imgWeb = $postArr['weburl'];
			if(isset($postArr['tnurl']) && $postArr['tnurl']) $imgThumb = $postArr['tnurl'];
			if($imgThumb && !$imgWeb) $imgManager->setCreateWebDerivative(false);
			if($imgWeb) $imgManager->setImgWebUrl($imgWeb);
			if($imgThumb) $imgManager->setImgTnUrl($imgThumb);
			if(array_key_exists('copytoserver',$postArr) && $postArr['copytoserver']){
				if(!$imgManager->copyImageFromUrl()) $status = false;
			}
			else $imgManager->setImgLgUrl($sourceImgUri);
		}
		else{
			//Image is a file upload
			if(!$imgManager->uploadImage()) $status = false;
		}
		$imgManager->setOccid($this->occid);
		if(isset($this->occurrenceMap[$this->occid]['tidinterpreted'])) $imgManager->setTid($this->occurrenceMap[$this->occid]['tidinterpreted']);
		if($imgManager->processImage()){
			$this->activeImgId = $imgManager->getActiveImgId();
		}

		//Load tags
		$status = $imgManager->insertImageTags($postArr);

		//Get errors and warnings
		$this->errorStr = $imgManager->getErrStr();
		return $status;
	}

	private function setRootPaths(){
		$this->imageRootPath = $GLOBALS["imageRootPath"];
		if(substr($this->imageRootPath,-1) != "/") $this->imageRootPath .= "/";
		$this->imageRootUrl = $GLOBALS["imageRootUrl"];
		if(substr($this->imageRootUrl,-1) != "/") $this->imageRootUrl .= "/";
	}

	public function getPhotographerArr(){
		if(!$this->photographerArr){
			$sql = "SELECT u.uid, CONCAT_WS(', ',u.lastname,u.firstname) AS fullname ".
				"FROM users u ORDER BY u.lastname, u.firstname ";
			$result = $this->conn->query($sql);
			while($row = $result->fetch_object()){
				$this->photographerArr[$row->uid] = $this->cleanOutStr($row->fullname);
			}
			$result->free();
		}
		return $this->photographerArr;
	}

	public function getImageTagArr(){
		$retArr = Array();
		$sql = 'SELECT tagkey, description_en FROM imagetagkey ORDER BY sortorder';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->tagkey] = $r->description_en;
		}
		$rs->free();
		return $retArr;
	}
}
?>