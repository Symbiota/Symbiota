<?php
include_once($SERVER_ROOT . '/classes/DwcArchiverBaseManager.php');

class DwcArchiverDetermination extends DwcArchiverBaseManager{

	private $extended = false;

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

	private function setFieldArr(){
		$fieldArr = array();
		$termArr = array();
		$fieldArr['coreid'] = 'x.occid';
		$termArr['identifiedBy'] = 'http://rs.tdwg.org/dwc/terms/identifiedBy';
		$fieldArr['identifiedBy'] = 'd.identifiedBy';
		//$termArr['identifiedByID'] = 'https://symbiota.org/terms/identifiedByID';
		//$fieldArr['identifiedByID'] = 'd.idbyid';
		$termArr['dateIdentified'] = 'http://rs.tdwg.org/dwc/terms/dateIdentified';
		$fieldArr['dateIdentified'] = 'd.dateIdentified';
		$termArr['identificationQualifier'] = 'http://rs.tdwg.org/dwc/terms/identificationQualifier';
		$fieldArr['identificationQualifier'] = 'd.identificationQualifier';
		$termArr['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
		$fieldArr['scientificName'] = 'd.sciName AS scientificName';
		$termArr['tidInterpreted'] = 'https://symbiota.org/terms/tidInterpreted';
		$fieldArr['tidInterpreted'] = 'd.tidinterpreted';
		$termArr['identificationIsCurrent'] = 'https://symbiota.org/terms/identificationIsCurrent';
		$fieldArr['identificationIsCurrent'] = 'd.iscurrent';
		$termArr['scientificNameAuthorship'] = 'http://rs.tdwg.org/dwc/terms/scientificNameAuthorship';
		$fieldArr['scientificNameAuthorship'] = 'd.scientificNameAuthorship';
		$termArr['genus'] = 'http://rs.tdwg.org/dwc/terms/genus';
		$fieldArr['genus'] = 'CONCAT_WS(" ",t.unitind1,t.unitname1) AS genus';
		$termArr['specificEpithet'] = 'http://rs.tdwg.org/dwc/terms/specificEpithet';
		$fieldArr['specificEpithet'] = 'CONCAT_WS(" ",t.unitind2,t.unitname2) AS specificEpithet';
		$termArr['taxonRank'] = 'http://rs.tdwg.org/dwc/terms/taxonRank';
		$fieldArr['taxonRank'] = 't.unitind3 AS taxonRank';
		$termArr['infraspecificEpithet'] = 'http://rs.tdwg.org/dwc/terms/infraspecificEpithet';
		$fieldArr['infraspecificEpithet'] = 't.unitname3 AS infraspecificEpithet';
		$termArr['identificationReferences'] = 'http://rs.tdwg.org/dwc/terms/identificationReferences';
		$fieldArr['identificationReferences'] = 'd.identificationReferences';
		$termArr['identificationRemarks'] = 'http://rs.tdwg.org/dwc/terms/identificationRemarks';
		$fieldArr['identificationRemarks'] = 'd.identificationRemarks';
		$termArr['recordID'] = 'http://portal.idigbio.org/terms/recordID';
		$fieldArr['recordID'] = 'd.recordID AS recordID';
		$termArr['modified'] = 'http://purl.org/dc/terms/modified';
		$fieldArr['modified'] = 'd.initialTimeStamp AS modified';

		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] =  $this->trimBySchemaType($fieldArr);
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'dwc'){
			$trimArr = array('identifiedByID', 'tidInterpreted', 'identificationIsCurrent');
		}
		elseif($this->schemaType == 'symbiota'){
			if(!$this->extended){
				$trimArr = array('identifiedByID', 'tidInterpreted');
			}
		}
		elseif($this->schemaType == 'backup'){
			$trimArr = array();
		}
		return array_diff_key($dataArr, array_flip($trimArr));
	}

	private function setSql(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $colName){
				if($colName) $sqlFrag .= ', ' . $colName;
			}
			$this->sqlArr[] = 'SELECT ' . trim($sqlFrag, ', ') . ' FROM omoccurdeterminations d INNER JOIN omexportoccurrences x ON d.occid = x.occid
				LEFT JOIN taxa t ON d.tidinterpreted = t.tid
				WHERE x.omExportID = ? AND d.appliedstatus = 1 ';
		}
	}

	//Setters and getters
	public function setExtended($bool){
		if($bool) $this->extended = true;
	}
}
?>