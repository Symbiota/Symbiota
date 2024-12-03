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

    //Based on https://rs.gbif.org/extension/gbif/1.0/identifier.xml
	private function setFieldArr(){
		$columnArr = array();
		$columnArr['coreid'] = 'occid';
		$termArr['resourceRelationshipID'] = 'https://dwc.tdwg.org/terms/#dwc:resourceRelationshipID';
		$columnArr['resourceRelationshipID'] = 'instanceID';
		$termArr['resourceID'] = 'https://dwc.tdwg.org/terms/#dwc:resourceID';
		$columnArr['resourceID'] = 'occurrenceID';
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

}

?>