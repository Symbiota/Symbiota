<?php
include_once('data/KmTaxonCharacters.php');

class KeyCharacterAdmin extends KmTaxonCharacters{

	private $lang = 'english';

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function uploadCharacterStateImage($formArr){
		$statusStr = '';
		if(is_numeric($formArr['cs'])){
			$imageRootPath = $GLOBALS['MEDIA_ROOT_PATH'];
			if(substr($imageRootPath,-1) != "/") $imageRootPath .= "/";
			if(file_exists($imageRootPath)){
				$imageRootPath .= 'ident/';
				if(!file_exists($imageRootPath)){
					if(!mkdir($imageRootPath)){
						return 'ERROR, unable to create upload directory: '.$imageRootPath;
					}
				}
				$imageRootPath .= 'csimgs/';
				if(!file_exists($imageRootPath)){
					if(!mkdir($imageRootPath)){
						return 'ERROR, unable to create upload directory: '.$imageRootPath;
					}
				}
				//Create url prefix
				$imageRootUrl = $GLOBALS['MEDIA_ROOT_URL'];
				if(substr($imageRootUrl,-1) != "/") $imageRootUrl .= "/";
				$imageRootUrl .= 'ident/csimgs/';

				//Image is to be downloaded
				$fileName = $this->cleanFileName(basename($_FILES['urlupload']['name']), $imageRootUrl);

				if(file_exists($_FILES['urlupload']['tmp_name'])){
					if($this->createNewCharacterStateImage($_FILES['urlupload']['tmp_name'], $imageRootPath . $fileName)) {
						//Add url to database
						$url = $imageRootUrl . $fileName;
						$inputArr = array('cs' => $formArr['cs'], 'url' => $url, 'notes' => $formArr['notes']);
						if(!empty($formArr['sortsequence'])) $inputArr['sortSequence'] = $formArr['sortsequence'];
						else $inputArr['sortSequence'] = 50;
						$this->insertCharacterStateImage($inputArr);
					}
					else {
						return 'Error: Unable to create image file: ' . $imageRootPath . $fileName;
					}
				}
				else{
					return 'ERROR uploading file, file does not exist: ' . $_FILES['urlupload']['tmp_name'];
				}
			}
		}
		else{
			$statusStr = 'ERROR: Upload path does not exist (path: ' . $imageRootPath . ')';
		}
		return $statusStr;
	}

	private function cleanFileName($fName,$subPath){
		if($fName){
			$pos = strrpos($fName,'.');
			$ext = substr($fName,$pos+1);
			$fName = substr($fName,0,$pos);
			$fName = str_replace(" ","_",$fName);
			$fName = str_replace(array(chr(231),chr(232),chr(233),chr(234),chr(260)),"a",$fName);
			$fName = str_replace(array(chr(230),chr(236),chr(237),chr(238)),"e",$fName);
			$fName = str_replace(array(chr(239),chr(240),chr(241),chr(261)),"i",$fName);
			$fName = str_replace(array(chr(247),chr(248),chr(249),chr(262)),"o",$fName);
			$fName = str_replace(array(chr(250),chr(251),chr(263)),"u",$fName);
			$fName = str_replace(array(chr(264),chr(265)),"n",$fName);
			$fName = preg_replace("/[^a-zA-Z0-9\-_\.]/", "", $fName);
			if(strlen($fName) > 30) {
				$fName = substr($fName,0,30);
			}
			//Check and see if file already exists, if so, rename filename until it has a unique name
	 		$tempFileName = $fName;
	 		$cnt = 1;
	 		while(file_exists($subPath.$fName)){
	 			$tempFileName = str_ireplace(".jpg","_".$cnt.".jpg",$fName);
	 			$cnt++;
	 		}
		}
 		return $tempFileName.'.'.$ext;
 	}

 	private function createNewCharacterStateImage($path, $fileName){
		$status = false;
		$imgWidth = 800;
		$qualityRating= 100;
		list($width, $height) = getimagesize(str_replace(' ', '%20', $path));
		if($width <= 0) {
			return $status;
		}

		$imgHeight = round($imgWidth*($height/$width));

   		$sourceImg = imagecreatefromjpeg($path);
		$newImg = imagecreatetruecolor($imgWidth,$imgHeight);
		imagecopyresampled($newImg,$sourceImg,0,0,0,0,$imgWidth,$imgHeight,$width,$height);
		//imagecopyresized($newImg,$sourceImg,0,0,0,0,$imgWidth,$imgHeight,$width,$height);
		$status = imagejpeg($newImg, $fileName, $qualityRating);
		if($status){
			imagedestroy($newImg);
			imagedestroy($sourceImg);
		}
		return $status;
	}

	public function removeCharacterStateImage($csImgId){
		//Remove image from file system
	 	$imageRootPath = $GLOBALS['MEDIA_ROOT_PATH'];
		if(substr($imageRootPath,-1) != "/") $imageRootPath .= "/";
		$imageRootPath .= 'ident/csimgs/';
		if($imageArr = $this->getCharacterStateImageArr(array('csImgId' => $csImgId))){
			$url = substr($imageArr['url'], strrpos($imageArr['url'], '/') + 1);
			unlink($imageRootPath . $url);
		}
		//Remove image record from database
		return $this->deleteCharacterStateImage($csImgId);
	}

	//General data retrival functions
	public function getCharacterList(){
		$retArr = array();
		$charArr = $this->getCharacterArr(null, array('sortSequence','charName'));
		foreach($charArr as $cid => $unitArr){
			$hid = $unitArr['hid'];
			if(!$hid) $hid = 'UNDEFINED';
			$retArr[$hid][$cid] = $unitArr['charName'];
		}
		return $retArr;
	}

	public function getGlossaryList(){
		$retArr = array();
		$this->fieldMap = array('glossID' => 'pk', 'term' => 's', 'language' => 's');
		$glossArr = $this->getRecordArr('glossary', null, array('term'));
		foreach($glossArr as $glossID => $glossUnitArr){
			//$k variable is needed to so that list can be alphabetical even when html tags (e.g. italics) are embedded into the terms
			$k = strip_tags(strtolower($glossUnitArr['term']));
			$retArr[$k][$glossID]['term'] = $glossUnitArr['term'];
			$retArr[$k][$glossID]['lang'] = $glossUnitArr['language'];
		}
		ksort($retArr);
		return $retArr;
	}

	//Setters and getters
	public function setLanguage($l){
		$this->lang = $l;
	}

	public function setLangId($lang=''){
		if(!$lang){
			if($GLOBALS['DEFAULT_LANG']) $lang = $GLOBALS['DEFAULT_LANG'];
			else $lang = 'English';
		}
		if($lang){
			if(is_numeric($lang)) $this->langId = $lang;
			else{
				$langArr = $this->getLangArr($lang);
				foreach($langArr as $langID => $langStr){
					$this->langId = $langID;
					$this->lang = $langStr;
				}
			}
		}
	}
}
?>
