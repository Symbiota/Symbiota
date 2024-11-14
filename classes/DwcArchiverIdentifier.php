<?php
include_once($SERVER_ROOT . '/classes/DwcArchiverBaseManager.php');

class DwcArchiverIdentifier extends DwcArchiverBaseManager{

	public function __construct($connOverride){
		parent::__construct('write', $connOverride);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function initiateProcess($filePath){
		$this->setFieldArr();
		$this->setSqlBase();

		$this->setFileHandler($filePath);
	}

	//Based on https://rs.gbif.org/extension/gbif/1.0/identifier.xml
	private function setFieldArr(){
		$columnArr = array();
		$columnArr['coreid'] = 'id.occid';
		$termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
		$columnArr['identifier'] = 'id.identifierValue';
		$termArr['title'] = 'http://purl.org/dc/terms/title';
		$columnArr['title'] = 'id.identifierName';
		$termArr['format'] = 'http://purl.org/dc/terms/format';
		$columnArr['format'] = 'id.format';
		$termArr['notes'] = 'https://symbiota.org/terms/identifier/notes';
		$columnArr['notes'] = 'id.notes';
		$termArr['sortBy'] = 'https://symbiota.org/terms/identifier/notes';
		$columnArr['sortBy'] = 'id.sortBy';
 		$termArr['recordID'] = 'https://symbiota.org/terms/identifier/recordID';
 		$columnArr['recordID'] = 'id.recordID';
		$termArr['initialTimestamp'] = 'https://symbiota.org/terms/identifier/initialTimestamp';
		$columnArr['initialTimestamp'] = 'id.initialTimestamp';

		$retArr['terms'] = $this->trimBySchemaType($termArr);
		$retArr['fields'] = $this->trimBySchemaType($columnArr);
		return $retArr;
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'backup'){
			//$trimArr = array();
		}
		elseif($this->schemaType == 'dwc'){
			$trimArr = array('notes', 'sortBy');
		}
		return array_diff_key($dataArr,array_flip($trimArr));
	}

	private function setSqlBase(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $colName){
				if($colName) $sqlFrag .= ', ' . $colName;
			}
			$this->sqlBase = 'SELECT ' . trim($sqlFrag, ', ') . 'FROM omoccuridentifiers ';
		}
	}
}
?>