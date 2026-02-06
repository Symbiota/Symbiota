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
		$this->setSql();

		$this->setFileHandler($filePath);
	}

	//Based on https://rs.gbif.org/extension/gbif/1.0/identifier.xml
	private function setFieldArr(){
		$columnArr = array();
		$termArr = array();
		$columnArr['coreid'] = 'i.occid';
		$termArr['identifier'] = 'http://purl.org/dc/terms/identifier';
		$columnArr['identifier'] = 'i.identifierValue';
		$termArr['title'] = 'http://purl.org/dc/terms/title';
		$columnArr['title'] = 'i.identifierName';
		$termArr['format'] = 'http://purl.org/dc/terms/format';
		$columnArr['format'] = 'i.format';
		$termArr['notes'] = 'https://symbiota.org/terms/identifier/notes';
		$columnArr['notes'] = 'i.notes';
		$termArr['sortBy'] = 'https://symbiota.org/terms/identifier/sortBy';
		$columnArr['sortBy'] = 'i.sortBy';
 		$termArr['recordID'] = 'https://symbiota.org/terms/identifier/recordID';
 		$columnArr['recordID'] = 'i.recordID';
		$termArr['initialTimestamp'] = 'https://symbiota.org/terms/identifier/initialTimestamp';
		$columnArr['initialTimestamp'] = 'i.initialTimestamp';

		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] = $this->trimBySchemaType($columnArr);
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'backup'){
			//$trimArr = array();
		}
		elseif($this->schemaType == 'dwc'){
			$trimArr = array('notes', 'sortBy');
		}
		return array_diff_key($dataArr, array_flip($trimArr));
	}

	private function setSql(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $colName){
				if($colName) $sqlFrag .= ', ' . $colName;
			}
			$this->sqlArr[] = 'SELECT ' . trim($sqlFrag, ', ') . ' FROM omoccuridentifiers i INNER JOIN omexportoccurrences e ON i.occid = e.occid WHERE (e.omExportID = ?) ';
		}
	}
}
?>