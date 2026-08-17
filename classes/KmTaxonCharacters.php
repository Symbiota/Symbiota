<?php
include_once('Manager.php');

class KeyTaxonCharacters extends Manager{

	private $cid = 0;

	private $lang = 'english';
	private $langId;
	//private $langId;

	private $fieldMap = array();
	private $parameterArr = array();
	private $typeStr = '';
	private $primaryKey;

	function __construct() {
		parent::__construct(null, 'write');
	}

	function __destruct(){
		if(!($this->conn === null)) $this->conn->close();
	}

	//kmcharacter functions
	public function getCharacterArr(){
		$retArr = array();
		if($this->cid){
			$this->setCharacterFieldMap();
			$sql = 'SELECT `' . implode('`,`', array_keys($this->fieldMap)) . '` FROM kmcharacters WHERE cid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->cid);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_assoc()){
					foreach($this->fieldMap as $fieldName => $type){
						$retArr[$fieldName] = $r[$fieldName];
					}
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	public function insertCharacter($inputArr){
		$status = false;
		$this->setCharacterFieldMap();
		$this->setParameterArr($inputArr);
		$sql = 'INSERT INTO kmcharacters(`';
		$sqlValues = '';
		$paramArr = array();
		$delimiter = '';
		foreach($this->parameterArr as $fieldName => $value){
			if($value != 'pk'){
				$sql .= $delimiter . $fieldName;
				$sqlValues .= $delimiter . '?';
				$paramArr[] = $value;
				$delimiter = '`, `';
			}
		}
		$sql .= '`) VALUES(' . $sqlValues . ') ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($this->typeStr, ...$paramArr);
			if($stmt->execute()){
				if($stmt->affected_rows || !$stmt->error){
					$this->primaryKey = $stmt->insert_id;
					$status = true;
				}
				else $this->errorMessage = $stmt->error;
			}
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		else $this->errorMessage = $this->conn->error;
		return $status;
	}

	public function updateCharacter($inputArr){
		$status = false;
		$this->setCharacterFieldMap();
		$this->setParameterArr($inputArr);
		$sqlFrag = '';
		$paramArr = array();
		foreach($this->parameterArr as $fieldName => $value){
			$sqlFrag .= $fieldName . ' = ?, ';
			$paramArr[] = $value;
		}
		$sql = 'UPDATE kmcharacters SET '.trim($sqlFrag, ', ').' WHERE (cid = ?)';
		if($paramArr){
			$paramArr[] = $this->cid;
			$this->typeStr .= 'i';
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($this->typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error) $status = true;
					else $this->errorMessage = $stmt->error;
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = $this->conn->error;
		}
		return $status;
	}

	public function deleteCharacter(){
		$status = false;
		//TODO: Check to make sure no character states have been coded
		$sql = 'DELETE FROM kmcharacters WHERE cid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->clid);
			$stmt->execute();
			if($stmt->affected_rows && !$stmt->error){
				$status = true;
			}
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		else{
			$this->errorMessage = $this->conn->error;
		}
		return $status;
	}

	private function setCharacterFieldMap(){
		$this->fieldMap = array('cid' => 'pk', 'charName' => 's', 'charType' => 's', 'difficultyRank' => 'i', 'hid' => 'i', 'units' => 's', 'description' => 's', 'glossID' => 'i',
			'helpUrl' => 's', 'notes' => 's', 'enteredBy' => 's', 'sortSequence' => 'i');
	}

	//Character State functions
	public function getCharacterStateArr(){
		$retArr = array();
		if($this->cid){
			$this->setCharacterStateFieldMap();
			$sql = 'SELECT `' . implode('`,`', array_keys($this->fieldMap)) . '` FROM kmcharacters WHERE cid = ?';
			if($stmt = $this->conn->prepare($sql)){
				$stmt->bind_param('i', $this->cid);
				$stmt->execute();
				$rs = $stmt->get_result();
				while($r = $rs->fetch_assoc()){
					foreach($this->fieldMap as $fieldName => $type){
						$retArr[$fieldName] = $r[$fieldName];
					}
				}
				$rs->free();
				$stmt->close();
			}
		}
		return $retArr;
	}

	private function setCharacterStateFieldMap(){
		$this->fieldMap = array('cid' => 'pk', 'cs' => 'pk', 'charStateName' => 's', 'implicit' => 'i', 'notes' => 's', 'description' => 's', 'illustrationUrl' => 's', 'glossid' => 'i', 'sortSequence' => 'i');
	}


	public function createCharState($postArr, $un){
		$csValue = 1;
		if($this->cid){
			//Get highest character set ID value (CS) and increase by 1
			$sql = 'SELECT cs FROM kmcs WHERE cid = '.$this->cid.' ORDER BY (cs+1) DESC ';
			if($rs = $this->conn->query($sql)){
				if($r = $rs->fetch_object()){
					if(is_numeric($r->cs)){
						$csValue = $r->cs + 1;
					}
				}
				$rs->free();
			}
			$csName = $postArr['charstatename'];
			$glossID = null;
			if(isset($postArr['glossid']) && is_numeric($postArr['glossid'])) $glossID = $postArr['glossid'];
			$description = $postArr['description'];
			$notes = $postArr['notes'];
			$sortSequence = $postArr['sortsequence'];
			$sql = 'INSERT INTO kmcs(cid,cs,charstatename,implicit,glossid,description,notes,sortsequence,enteredby) '.
				'VALUES('.$this->cid.',"'.$csValue.'","'.$this->cleanInStr($csName).'",1,'.
				($glossID?$glossID:'NULL').','.
				($description?'"'.$this->cleanInStr($description).'"':'NULL').','.
				($notes?'"'.$this->cleanInStr($notes).'"':'NULL').','.
				(is_numeric($sortSequence)?$this->cleanInStr($sortSequence):100).',"'.$un.'") ';
			//echo $sql;
			if(!$this->conn->query($sql)){
				trigger_error('ERROR: Creation of new character failed: '.$this->conn->error);
			}
		}
		return $csValue;
	}

	public function editCharState($pArr){
		$statusStr = '';
		$cs = $pArr['cs'];
		$targetArr = array('charstatename','glossid','description','notes','sortsequence');
		$sql = '';
		foreach($pArr as $k => $v){
			if(in_array($k,$targetArr)){
				$sql .= ','.$k.'='.($v?'"'.$this->cleanInStr($v).'"':'NULL');
			}
		}
		$sql = 'UPDATE kmcs SET '.substr($sql,1).' WHERE (cid = '.$this->cid.') AND (cs = '.$cs.')';
		//echo $sql;
		if($this->conn->query($sql)){
			$statusStr = 'SUCCESS: information saved';
		}
		else{
			$statusStr = 'ERROR: Editing of character state failed: '.$this->conn->error.'<br/>';
			$statusStr .= 'SQL: '.$sql;
		}
		return $statusStr;
	}

	public function deleteCharState($cs){
		$status = '';
		if(is_numeric($cs)){
			//Delete images links
			$sql = 'DELETE FROM kmcsimages WHERE (cid = '.$this->cid.') AND (cs = '.$cs.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$status = 'ERROR deleting character state images: '.$this->conn->error.', '.$sql;
			}

			//Delete language links
			$sql = 'DELETE FROM kmcslang WHERE (cid = '.$this->cid.') AND (cs = '.$cs.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$status = 'ERROR deleting character state languages: '.$this->conn->error.', '.$sql;
			}

			//Delete character dependance links
			$sql = 'DELETE FROM kmchardependance WHERE (ciddependance = '.$this->cid.') AND (csdependance = '.$cs.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$status = 'ERROR deleting character dependance linked to character state: '.$this->conn->error.', '.$sql;
			}

			//Delete description links
			$sql = 'DELETE FROM kmdescr WHERE (cid = '.$this->cid.') AND (cs = '.$cs.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$status = 'ERROR deleting descriptions linked to character state: '.$this->conn->error.', '.$sql;
			}

			//Delete character states
			$sql = 'DELETE FROM kmcs WHERE (cid = '.$this->cid.') AND (cs = '.$cs.')';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$status = 'ERROR deleting character state: '.$this->conn->error.', '.$sql;
			}
		}
		return $status;
	}

	public function uploadCsImage($formArr){
		global $PARAMS_ARR;
		$statusStr = '';
		if(is_numeric($formArr['cid']) && is_numeric($formArr['cs'])){
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
					if($this->createNewCsImage($_FILES['urlupload']['tmp_name'], $imageRootPath . $fileName)) {
						//Add url to database
						$notes = $this->cleanInStr($formArr['notes']);
						$sql = 'INSERT INTO kmcsimages(cid, cs, url, notes, sortsequence, username) '.
							'VALUES('.$formArr['cid'].','.$formArr['cs'].',"'.$imageRootUrl.$fileName.'",'.
							($notes?'"'.$notes.'"':'NULL').','.
							(is_numeric($formArr['sortsequence'])?$formArr['sortsequence']:'50').',"'.$PARAMS_ARR['un'].'")';
						if(!$this->conn->query($sql)){
							$statusStr = 'ERROR loading char state image: '.$this->conn->error;
						}
					} else {
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

	private function createNewCsImage($path, $fileName){
		$status = false;
		$imgWidth = 800;
		$qualityRating= 100;
		list($width, $height) = getimagesize(str_replace(' ', '%20', $path));
		if($width <= 0) {
			return $status;
		}

		$imgHeight = ($imgWidth*($height/$width));

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

	public function deleteCsImage($csImgId){
		$statusStr = 'SUCCESS: image uploaded successful';
		//Remove image from file system
	 	$imageRootPath = $GLOBALS['MEDIA_ROOT_PATH'];
		if(substr($imageRootPath,-1) != "/") $imageRootPath .= "/";
		$imageRootPath .= 'ident/csimgs/';
		$sql = 'SELECT url FROM kmcsimages WHERE csimgid = '.$csImgId;
		$rs = $this->conn->query($sql);
		if($r = $rs->fetch_object()){
			$url = $r->url;
			$url = substr($url,strrpos($url,'/')+1);
			unlink($imageRootPath.$url);
		}
		$rs->free();
		//Remove image record from database
		$sqlDel = 'DELETE FROM kmcsimages WHERE csimgid = '.$csImgId;
		if(!$this->conn->query($sqlDel)){
			$statusStr = 'ERROR: unable to delete image; '.$this->error;
		}
		return $statusStr;
	}

	public function getTaxonRelevance(){
		$retArr = array();
		if($this->cid){
			$sql = 'SELECT l.tid, l.relation, l.notes, t.sciname FROM kmchartaxalink l INNER JOIN taxa t ON l.tid = t.tid WHERE l.cid = '.$this->cid;
			//echo $sql;
			if($rs = $this->conn->query($sql)){
				while($r = $rs->fetch_object()){
					$retArr[$r->relation][$r->tid]['sciname'] = $r->sciname;
					$retArr[$r->relation][$r->tid]['notes'] = $r->notes;
				}
				$rs->free();
			}
			else{
				trigger_error('unable to get Taxon Links; '.$this->conn->error);
			}
		}
		return $retArr;
	}

	public function saveTaxonRelevance($tid,$rel,$notes){
		$statusStr = '';
		if($this->cid && is_numeric($tid)){
			$sql = 'INSERT INTO kmchartaxalink(cid,tid,relation,notes) VALUES('.$this->cid.','.$tid.',"'.$this->cleanInStr($rel).'","'.$this->cleanInStr($notes).'")';
			//echo $sql;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR: unable to add Taxon Relevance; '.$this->conn->error;
				//trigger_error('ERROR: unable to add Taxon Relevance; '.$this->conn->error);
			}
		}
		return $statusStr;
	}

	public function deleteTaxonRelevance($tid){
		$statusStr = 'SUCCESS: taxon linkage removed';
		if($this->cid && is_numeric($tid)){
			$sql = 'DELETE FROM kmchartaxalink WHERE cid = '.$this->cid.' AND tid = '.$tid;
			//echo $sql;
			if(!$this->conn->query($sql)){
				$statusStr = 'ERROR: unable to delete Taxon Relevance; '.$this->conn->error;
				trigger_error('ERROR: unable to delete Taxon Relevance; '.$this->conn->error);
			}
		}
		return $statusStr;
	}

	public function getHeadingArr(){
		$retArr = array();
		$sql = 'SELECT hid, headingname, notes, sortsequence FROM kmcharheading ';
		if($this->langId) $sql .= 'WHERE (langid = '.$this->langId.') ';
		$sql .= 'ORDER BY sortsequence,headingname';
		//echo $sql;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->hid]['name'] = $r->headingname;
			$retArr[$r->hid]['notes'] = $r->notes;
			$retArr[$r->hid]['sortsequence'] = $r->sortsequence;
		}
		$rs->free();
		return $retArr;
	}

	public function addHeading($name,$notes,$sortSeq){
		$statusStr = '';
		$sql = 'INSERT INTO kmcharheading(headingname,notes,langid,sortsequence) VALUES ("'.$name.'",'.($notes?'"'.$notes.'"':'NULL').','.$this->langId.','.(is_numeric($sortSeq)?$sortSeq:'NULL').')';
		if(!$this->conn->query($sql)){
			$statusStr = 'Error adding heading: '.$this->conn->error;
		}
		return $statusStr;
	}

	public function editHeading($hid,$name,$notes,$sortSeq){
		$statusStr = '';
		$sql = 'UPDATE kmcharheading '.
			'SET headingname = "'.$name.'", '.
			'notes = '.($notes?'"'.$notes.'"':'NULL').', '.
			'sortsequence = '.(is_numeric($sortSeq)?$sortSeq:'NULL').
			' WHERE hid = '.$hid;
		if(!$this->conn->query($sql)){
			$statusStr = 'Error editing heading: '.$this->conn->error;
		}
		return $statusStr;
	}

	public function deleteHeading($hid){
		$statusStr = '';
		$sql = 'DELETE FROM kmcharheading WHERE hid = '.$hid;
		if(!$this->conn->query($sql)){
			$statusStr = 'Error deleting heading: '.$this->conn->error;
		}
		return $statusStr;
	}

	//General data retrival functions
	public function getCharacterArr(){
		$retArr = array();
		$sql = 'SELECT c.cid, IFNULL(cl.charname, c.charname) AS charname, c.hid
			FROM kmcharacters c LEFT JOIN (SELECT cid, charname FROM kmcharacterlang WHERE langid = ?) cl ON c.cid = cl.cid
			ORDER BY c.sortsequence, cl.charname, c.charname';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->langId);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_object()){
				$hid = ($r->hid?$r->hid:0);
				$retArr[$hid][$r->cid] = $r->charname;
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	public function getGlossaryList(){
		$retArr = array();
		$sql = 'SELECT glossid, term, language FROM glossary';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			//$k variable is needed to so that list can be alphabetical even when html tags (e.g. italics) are embedded into the terms
			$k = strip_tags(strtolower($r->term));
			$retArr[$k][$r->glossid]['term'] = $r->term;
			$retArr[$k][$r->glossid]['lang'] = $r->language;
		}
		$rs->free();
		ksort($retArr);
		return $retArr;
	}

	public function getLanguageArr(){
		$retArr = array();
		$sql = 'SELECT langid, langname FROM adminlanguages ORDER BY langname';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->langid] = $r->langname;
		}
		$rs->free();
		return $retArr;
	}

	//General shared functions
	protected function getRecordArr($tableName, $pkArr){
		$retArr = array();
		$sql = 'SELECT `' . implode('`,`', array_keys($this->fieldMap)) . '` FROM `' . $tableName . '` WHERE ' . $pkFieldName . ' = ?';
		$paramArr = array();
		$typeStr = '';
		$sql .= $this->setPrimaryKeyCondition($pkArr, $paramArr, $typeStr);
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_assoc()){
				foreach($this->fieldMap as $fieldName => $type){
					$retArr[$fieldName] = $r[$fieldName];
				}
			}
			$rs->free();
			$stmt->close();
		}
		return $retArr;
	}

	protected function insertRecord($tableName, $inputArr){
		$status = false;
		if($tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if($inputArr){
			$this->errorMessage = 'INPUT_ARR_EMPTY';
			return false;
		}
		$this->setParameterArr($inputArr);
		$sql = 'INSERT INTO `' . $tableName . '`(`';
		$sqlValues = '';
		$paramArr = array();
		$delimiter = '';
		foreach($this->parameterArr as $fieldName => $value){
			if($value != 'pk'){
				$sql .= $delimiter . $fieldName;
				$sqlValues .= $delimiter . '?';
				$paramArr[] = $value;
				$delimiter = '`, `';
			}
		}
		$sql .= '`) VALUES(' . $sqlValues . ') ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($this->typeStr, ...$paramArr);
			if($stmt->execute()){
				if($stmt->affected_rows || !$stmt->error){
					$this->primaryKey = $stmt->insert_id;
					$status = true;
				}
				else $this->errorMessage = $stmt->error;
			}
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		else $this->errorMessage = $this->conn->error;
		return $status;
	}

	protected function updateRecord($tableName, $pkArr, $inputArr){
		$status = false;
		if($tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if($pkArr){
			$this->errorMessage = 'PK_ARR_EMPTY';
			return false;
		}
		if($inputArr){
			$this->errorMessage = 'INPUT_ARR_EMPTY';
			return false;
		}
		$this->setParameterArr($inputArr);
		$sqlFrag = '';
		$paramArr = array();
		$typeStr = '';
		foreach($this->parameterArr as $fieldName => $value){
			$sqlFrag .= $fieldName . ' = ?, ';
			$paramArr[] = $value;
		}
		$sql = 'UPDATE `' . $tableName . '` SET '.trim($sqlFrag, ', ').' WHERE ';
		if($paramArr){
			$sql .= $this->setPrimaryKeyCondition($pkArr, $paramArr, $typeStr);
			if($stmt = $this->conn->prepare($sql)) {
				$stmt->bind_param($typeStr, ...$paramArr);
				if($stmt->execute()){
					if($stmt->affected_rows || !$stmt->error) $status = true;
					else $this->errorMessage = $stmt->error;
				}
				else $this->errorMessage = $stmt->error;
				$stmt->close();
			}
			else $this->errorMessage = $this->conn->error;
		}
		return $status;
	}

	protected function deleteRecord($tableName, $pkArr){
		$status = false;
		if($tableName){
			$this->errorMessage = 'TABLE_NAME_EMPTY';
			return false;
		}
		if($pkArr){
			$this->errorMessage = 'PK_ARR_EMPTY';
			return false;
		}
		$sql = 'DELETE FROM `' . $tableName . '` WHERE ';
		$paramArr = array();
		$typeStr = '';
		$sql .= $this->setPrimaryKeyCondition($pkArr, $paramArr, $typeStr);
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param($typeStr, ...$paramArr);
			$stmt->execute();
			if($stmt->affected_rows && !$stmt->error){
				$status = true;
			}
			else $this->errorMessage = $stmt->error;
			$stmt->close();
		}
		else{
			$this->errorMessage = $this->conn->error;
		}
		return $status;
	}

	private function setPrimaryKeyCondition($pkArr, &$paramArr, &$typeStr){
		$sqlFrag = '';
		$delimiter = '';
		foreach($pkArr as $pkName => $pkValue){
			$sqlFrag .= $delimiter . '`' . $pkName . '` = ? ';
			$paramArr[] = $pkValue;
			$typeStr .= 'i';
			$delimiter = 'AND ';
		}
		return $sqlFrag;
	}

	private function setParameterArr($inputArr){
		//Reset class variables, which is very important if more than one write function is called per class instance
		unset($this->parameterArr);
		$this->parameterArr = array();
		$this->typeStr = '';
		//Prepare type and value variables used within prepared statement
		foreach($this->fieldMap as $field => $type){
			$postField = '';
			if(isset($inputArr[$field])) $postField = $field;
			elseif(isset($inputArr[strtolower($field)])) $postField = strtolower($field);
			if($postField){
				$value = trim($inputArr[$postField]);
				if(!$value) $value = null;
				$this->parameterArr[$field] = $value;
				$this->typeStr .= $type;
			}
		}
	}

	//Setters and getters
	public function getCid(){
		return $this->cid;
	}

	public function setCid($cid){
		if(is_numeric($cid)) $this->cid = $cid;
	}

	public function setLanguage($l){
		$this->lang = $l;
	}

	public function setLangId($lang=''){
		if(!$lang){
			if($GLOBALS['DEFAULT_LANG']) $lang = $GLOBALS['DEFAULT_LANG'];
			else $lang = 'English';
		}
		if(is_numeric($lang)) $this->langId = $lang;
		else{
			$sql = 'SELECT langid FROM adminlanguages WHERE langname = "'.$this->cleanInStr($lang).'" OR iso639_1 = "'.$this->cleanInStr($lang).'" OR iso639_2 = "'.$this->cleanInStr($lang).'" ';
			$rs = $this->conn->query($sql);
			if($r = $rs->fetch_object()){
				$this->langId = $r->langid;
			}
			$rs->free();
		}
	}

	//General functions
	private function cleanInStr($str){
		$newStr = trim($str);
		$newStr = preg_replace('/\s\s+/', ' ',$newStr);
		$newStr = $this->conn->real_escape_string($newStr);
		return $newStr;
	}
}
?>
