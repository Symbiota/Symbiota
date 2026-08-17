<?php
include_once($SERVER_ROOT . '/classes/data/DataCore.php');

class KmTaxonCharacters extends DataCore{

	private $cid = 0;
	protected $langId;

	function __construct() {
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	//kmcharacter functions
	public function getCharacterArr($conditionArr = null, $orderByArr = null){
		$this->setCharacterFieldMap();
		return $this->getRecordArr('kmcharacters', $conditionArr, $orderByArr);
	}

	public function getCharacterArrByCid(){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$this->setCharacterFieldMap();
		$pkArr = array('cid' => $this->cid);
		$charArr = $this->getRecordArr('kmcharacters', $pkArr);
		return $charArr[$this->cid];
	}

	public function insertCharacter($inputArr){
		$this->setCharacterFieldMap();
		if(empty($inputArr['enteredby']) && empty($inputArr['enteredBy'])){
			$inputArr['enteredBy'] = $GLOBALS['PARAMS_ARR']['un'];
		}
		return $this->insertRecord('kmcharacters', $inputArr);
	}

	public function updateCharacter($inputArr){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$this->setCharacterFieldMap();
		$pkArr = array('cid' => $this->cid);
		return $this->updateRecord('kmcharacters', $pkArr, $inputArr);
	}

	public function deleteCharacter(){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		//TODO: Check to make sure no character states have been coded, thus blocking deletion

		$pkArr = array('cid' => $this->cid);
		return $this->deleteRecord('kmcharacters', $pkArr);
	}

	private function setCharacterFieldMap(){
		$this->fieldMap = array('cid' => 'pk', 'charName' => 's', 'charType' => 's', 'difficultyRank' => 'i', 'hid' => 'i', 'units' => 's', 'description' => 's', 'glossID' => 'i',
			'helpUrl' => 's', 'notes' => 's', 'enteredBy' => 's', 'sortSequence' => 'i');
	}

	//Character State functions
	public function getCharacterStateArr(){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$this->setCharacterStateFieldMap();
		$pkArr = array('cid' => $this->cid);
		return $this->getRecordArr('kmcs', $pkArr);
	}

	public function insertCharacterState($inputArr){
		if(!isset($inputArr['cid'])){
			if($this->cid){
				$inputArr['cid'] = $this->cid;
			}
			else{
				$this->errorMessage = 'CID_NOT_SET';
				return false;
			}
		}
		$this->setCharacterStateFieldMap();
		if(empty($inputArr['cs'])){
			$inputArr['cs'] = $this->getCharacterStateKeyIncrement();
		}
		if(empty($inputArr['enteredby']) && empty($inputArr['enteredBy'])){
			$inputArr['enteredBy'] = $GLOBALS['PARAMS_ARR']['un'];
		}
		return $this->insertRecord('kmcs', $inputArr);
	}

	private function getCharacterStateKeyIncrement(){
		$csValue = 1;
		//Get highest character set ID value (CS) and increase by 1
		$sql = 'SELECT cs FROM kmcs WHERE cid = ? ORDER BY (cs+1) DESC ';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->cid);
			$stmt->execute();
			$rs = $stmt->get_result();
			if($r = $rs->fetch_object()){
				if(is_numeric($r->cs)){
					$csValue = $r->cs + 1;
				}
			}
			$rs->free();
			$stmt->close();
		}
		return $csValue;
	}

	public function updateCharacterState($inputArr){
		$this->setCharacterStateFieldMap();
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		if(empty($inputArr['cs'])){
			$this->errorMessage = 'ERROR_CS_IS_NULL';
			return false;
		}
		$cs = $inputArr['cs'];
		$pkArr = array('cid' => $this->cid, 'cs' => $cs);
		return $this->updateRecord('kmcs', $pkArr, $inputArr);
	}

	public function deleteCharacterState($cs){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		if(!$cs){
			$this->errorMessage = 'ERROR_CS_IS_NULL';
			return false;
		}
		if(!is_numeric($cs)){
			$this->errorMessage = 'ERROR_CS_IS_NOT_NUMERIC';
			return false;
		}
		$this->setCharacterStateFieldMap();
		$pkArr = array('cid' => $this->cid, 'cs' => $cs);
		$this->deleteRecord('kmcsimages', $pkArr);
		$this->deleteRecord('kmcslang', $pkArr);
		$charDependanceArr = $this->getCharacterDependanceArr(array('cidDependance' => $this->cid, 'csDependance' => $cs));
		foreach($charDependanceArr as $charDependID => $charDependArr){
			$this->deleteRecord('kmchardependance', $charDependID);
		}
		$this->deleteRecord('kmdescr', $pkArr);
		return $this->deleteRecord('kmcs', $pkArr);
	}

	private function setCharacterStateFieldMap(){
		$this->fieldMap = array('cid' => 'pk', 'cs' => 'pk', 'charStateName' => 's', 'implicit' => 'i', 'notes' => 's', 'description' => 's',
			'illustrationUrl' => 's', 'referenceUrl' => 's', 'glossID' => 'i', 'sortSequence' => 'i', 'enteredBy' => 's');
	}

	//Character dependance
	public function getCharacterDependanceArr($conditionArr = null, $orderByArr = null){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$conditionArr['cid'] = $this->cid;
		$this->setCharacterDependanceMap();
		return $this->getRecordArr('kmchardependance', $conditionArr, $orderByArr);
	}

	public function insertCharacterDependance($inputArr){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$inputArr['cid'] = $this->cid;
		$this->setCharacterDependanceMap();
		return $this->insertRecord('kmchardependance', $inputArr);
	}

	public function deleteCharacterDependance($charDependID){
		$this->setCharacterDependanceMap();
		return $this->deleteRecord('kmchardependance', $charDependID);
	}

	private function setCharacterDependanceMap(){
		$this->fieldMap = array('charDependID' => 'pk', 'cid' => 'i', 'cidDependance' => 'i', 'csDependance' => 's');
	}

	//Character State image functions
	public function getCharacterStateImageArr($conditionArr = null, $orderByArr = null){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$conditionArr['cid'] = $this->cid;
		$this->setCharacterStateImageMap();
		$imgArr = $this->getRecordArr('kmcsimages', $conditionArr, $orderByArr);
		if($imgArr) return array_shift($imgArr);
		return null;
	}

	public function insertCharacterStateImage($inputArr){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$inputArr['cid'] = $this->cid;
		$this->setCharacterStateImageMap();
		return $this->insertRecord('kmcsimages', $inputArr);
	}

	public function deleteCharacterStateImage($csImgId){
		$this->setCharacterStateImageMap();
		return $this->deleteRecord('kmcsimages', $csImgId);
	}

	private function setCharacterStateImageMap(){
		$this->fieldMap = array('csImgID' => 'pk', 'cid' => 'i', 'cs' => 'i', 'url' => 's', 'notes' => 's', 'sortSequence' => 'i', 'username' => 's');
	}

	//Taxon relavence function
	public function getTaxonRelevance(){
		$retArr = array();
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$sql = 'SELECT l.tid, l.relation, l.notes, t.sciname FROM kmchartaxalink l INNER JOIN taxa t ON l.tid = t.tid WHERE l.cid = ?';
		if($stmt = $this->conn->prepare($sql)){
			$stmt->bind_param('i', $this->cid);
			$stmt->execute();
			$rs = $stmt->get_result();
			while($r = $rs->fetch_object()){
				$retArr[$r->relation][$r->tid]['sciname'] = $r->sciname;
				$retArr[$r->relation][$r->tid]['notes'] = $r->notes;
			}
			$stmt->close();
		}
		return $retArr;
	}

	public function insertTaxonRelevance($tid, $relation, $notes){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$this->setCharacterTaxaMap();
		$inputArr = array('cid' => $this->cid, 'tid' => $tid, 'notes' => $notes);
		return $this->insertRecord('kmchartaxalink', $inputArr);
	}

	public function deleteTaxonRelevance($tid){
		if(!$this->cid){
			$this->errorMessage = 'CID_NOT_SET';
			return false;
		}
		$this->setCharacterTaxaMap();
		return $this->deleteRecord('kmchartaxalink', array('cid' => $this->cid, 'tid' => $tid));
	}

	private function setCharacterTaxaMap(){
		$this->fieldMap = array('charTaxaLinkID' => 'pk', 'cid' => 'i', 'tid' => 'i', 'relation' => 's', 'notes' => 's');
	}

	//Character heading functions
	public function getCharacterHeadingArr($conditionArr = null, $orderByArr = null){
		$this->setCharacterHeadingMap();
		$condArr = array();
		if($this->langId) $condArr['langid'] = $this->langId;
		return $this->getRecordArr('kmcharheading', $condArr, array('sortsequence', 'headingname'));
	}

	public function insertCharacterHeading($name, $notes, $sortSeq){
		$this->setCharacterHeadingMap();
		$inputArr = array('headingName' => $name, 'notes' => $notes, 'langid' => $this->langId, 'sortSequence' => $sortSeq);
		return $this->insertRecord('kmcharheading', $inputArr);
	}

	public function updateCharacterHeading($hid, $name, $notes, $sortSeq){
		$this->setCharacterHeadingMap();
		$inputArr = array('headingName' => $name, 'notes' => $notes, 'langid' => $this->langId, 'sortSequence' => $sortSeq);
		return $this->updateRecord('kmcharheading', $inputArr);
	}

	public function deleteHeading($hid){
		$this->setCharacterHeadingMap();
		return $this->deleteRecord('kmcharheading', $hid);
	}

	private function setCharacterHeadingMap(){
		$this->fieldMap = array('hid' => 'pk', 'headingName' => 's', 'language' => 's', 'langID' => 'i', 'notes' => 's', 'sortSequence' => 'i');
	}

	//General data retrival functions
	public function getLanguageArr(){
		$retArr = array();
		$this->fieldMap = array('langID' => 'pk', 'langName' => 's');
		$retArr = $this->getRecordArr('adminlanguages', null, array('langName'));
		return $retArr;
	}

	protected function getLangArr($lang){
		$this->fieldMap = array('langID' => 'pk', 'langName' => 's', 'iso639_1' => 's');
		$langArr = $this->getRecordArr('adminlanguages', array('iso639_1' => $lang));
		if(!$langArr) $langArr = $this->getRecordArr('adminlanguages', array('langname' => $lang));
		if(!$langArr) return array();
		return $langArr;
	}

	//Setters and getters
	public function getCid(){
		return $this->cid;
	}

	public function setCid($cid){
		if(is_numeric($cid)) $this->cid = $cid;
	}
}
?>
