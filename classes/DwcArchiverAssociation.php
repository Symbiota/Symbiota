<?php
include_once($SERVER_ROOT . '/classes/DwcArchiverBaseManager.php');

class DwcArchiverAssociation extends DwcArchiverBaseManager{
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

    //Based on https://rs.gbif.org/extension/resource_relationship_2024-02-19.xml
	private function setFieldArr(){
		$columnArr = array();
		$columnArr['coreid'] = 'occid';
		$termArr['resourceRelationshipID'] = 'https://dwc.tdwg.org/terms/#dwc:resourceRelationshipID';
		$columnArr['resourceRelationshipID'] = 'instanceID';
		$termArr['resourceID'] = 'https://dwc.tdwg.org/terms/#dwc:resourceID';
		$columnArr['resourceID'] = 'occid';
        $termArr['relationshipOfResourceID'] = 'https://dwc.tdwg.org/terms/#dwc:relationshipOfResourceID';
		$columnArr['relationshipOfResourceID'] = 'relationshipID';
		$termArr['relatedResourceID'] = 'https://dwc.tdwg.org/terms/#dwc:relatedResourceID';
		$columnArr['relatedResourceID'] = 'occidAssociate'; // @TODO make this also work resourceUrl or objectId.. what is the objectID alias about?
        $termArr['relationshipOfResource'] = 'https://dwc.tdwg.org/terms/#dwc:relationshipOfResource';
		$columnArr['relationshipOfResource'] = 'relationship';
		$termArr['relationshipAccordingTo'] = 'https://dwc.tdwg.org/terms/#dwc:relationshipAccordingTo';
		$columnArr['relationshipAccordingTo'] = 'accordingTo';
 		$termArr['relationshipEstablishedDate'] = 'https://dwc.tdwg.org/terms/#dwc:relationshipEstablishedDate';
 		$columnArr['relationshipEstablishedDate'] = 'establishedDate';
		$termArr['relationshipRemarks'] = 'https://dwc.tdwg.org/terms/#dwc:relationshipRemarks';
		$columnArr['relationshipRemarks'] = 'notes';

        

		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] = $this->trimBySchemaType($columnArr);
	}

    private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'backup'){
			//$trimArr = array();
		}
		elseif($this->schemaType == 'dwc'){
			// $trimArr = array('notes', 'sortBy'); // @TODO revisit if you don't think these should be identical
		}
		return array_diff_key($dataArr, array_flip($trimArr));
	}

	// @TODO decide if setDynamicFields is needed

	private function setSqlBase(){
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $colName){
				if($colName) $sqlFrag .= ', ' . $colName;
			}
			$this->sqlBase = 'SELECT ' . trim($sqlFrag, ', ') . ' FROM omoccurassociations ';
		}
	}

}

?>