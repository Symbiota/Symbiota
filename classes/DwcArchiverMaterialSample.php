<?php
include_once($SERVER_ROOT.'/classes/DwcArchiverBaseManager.php');

class DwcArchiverMaterialSample extends DwcArchiverBaseManager{

	public function __construct($connOverride){
		parent::__construct('write', $connOverride);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function initiateProcess($filePath){
		$this->setFieldArr();
		$this->setDynamicFields();
		$this->setSql();

		$this->setFileHandler($filePath);
	}

	private function setFieldArr(){
		$columnArr['coreid'] = 'm.occid';
		$termArr['materialSampleID'] = 'http://rs.tdwg.org/dwc/terms/materialSampleID';
		$columnArr['materialSampleID'] = 'm.guid';
		$termArr['sampleType'] = 'http://data.ggbn.org/schemas/ggbn/terms/materialSampleType';
		$columnArr['sampleType'] = 'm.sampleType';
		$termArr['catalogNumber'] = 'http://rs.tdwg.org/dwc/terms/catalogNumber';
		$columnArr['catalogNumber'] = 'm.catalogNumber';
		$termArr['sampleCondition'] = 'https://symbiota.org/terms/sampleCondition';
		$columnArr['sampleCondition'] = 'm.sampleCondition';
		$termArr['disposition'] = 'http://rs.tdwg.org/dwc/terms/disposition';
		$columnArr['disposition'] = 'm.disposition';
		$termArr['preservationType'] = 'http://data.ggbn.org/schemas/ggbn/terms/preservationType';
		$columnArr['preservationType'] = 'm.preservationType';
		$termArr['preparationDetails'] = 'http://data.ggbn.org/schemas/ggbn/terms/preparationProcess';
		$columnArr['preparationDetails'] = 'm.preparationDetails';
		$termArr['preparationDate'] = 'http://data.ggbn.org/schemas/ggbn/terms/preparationDate';
		$columnArr['preparationDate'] = 'm.preparationDate';
		$termArr['preparedBy'] = 'http://data.ggbn.org/schemas/ggbn/terms/preparedBy';
		$columnArr['preparedBy'] = 'CONCAT_WS(", ", u.lastname, u.firstname) AS preparedBy';
		$termArr['individualCount'] = 'http://rs.tdwg.org/dwc/terms/individualCount';
		$columnArr['individualCount'] = 'm.individualCount';
		$termArr['sampleSize'] = 'http://gensc.org/ns/mixs/samp_size';
		$columnArr['sampleSize'] = 'm.sampleSize';
		$termArr['storageLocation'] = 'https://symbiota.org/terms/storageLocation';
		$columnArr['storageLocation'] = 'm.storageLocation';
		$termArr['remarks'] = 'https://symbiota.org/terms/materialSampleRemarks';
		$columnArr['remarks'] = 'm.remarks';
		$termArr['recordID'] = 'http://symbiota.org/terms/recordID';
		$columnArr['recordID'] = 'm.recordID';

		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] = $this->trimBySchemaType($columnArr);
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'backup'){
			//$trimArr = array('Owner', 'UsageTerms', 'WebStatement');
		}
		return array_diff_key($dataArr,array_flip($trimArr));
	}

	private function setDynamicFields(){
		$sql = 'SELECT t.term, t.resourceUrl FROM ctcontrolvocab v INNER JOIN ctcontrolvocabterm t ON v.cvID = t.cvID WHERE v.tableName = "ommaterialsampleextended" AND v.fieldName = "fieldName"';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->fieldArr['terms'][$r->term] = $r->resourceUrl;
			$this->fieldArr['fields'][$r->term] = 'msDynamicField';
		}
		$rs->free();
	}

	private function setSql(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $colName){
				if($colName && $colName != 'msDynamicField') $sqlFrag .= ', '.$colName;
			}
			$this->sql = 'SELECT '.trim($sqlFrag,', ').' FROM ommaterialsample m
				INNER JOIN omexportoccurrences e ON m.occid = e.occid
				LEFT JOIN users u ON m.preparedByUid = u.uid
				WHERE (e.omExportID = ?) ';
		}
	}
}
?>