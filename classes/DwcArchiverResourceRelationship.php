<?php
include_once($SERVER_ROOT . '/classes/DwcArchiverBaseManager.php');

class DwcArchiverResourceRelationship extends DwcArchiverBaseManager{
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
		$termArr['resourceRelationshipID'] = 'http://rs.tdwg.org/dwc/terms/resourceRelationshipID';
		$columnArr['resourceRelationshipID'] = 'IFNULL(oa.instanceID, oa.recordID)';
		$termArr['resourceID'] = 'http://rs.tdwg.org/dwc/terms/resourceID';
		$columnArr['resourceID'] = 'IFNULL(o.occurrenceID, o.recordID)';
		$termArr['relationshipOfResourceID'] = 'http://rs.tdwg.org/dwc/terms/relationshipOfResourceID';
		$columnArr['relationshipOfResourceID'] = 'oa.relationshipID';
		$termArr['relatedResourceID'] = 'http://rs.tdwg.org/dwc/terms/relatedResourceID';
		$columnArr['relatedResourceID'] = 'IFNULL(IFNULL(IFNULL(oa.objectID, oo.occurrenceID), oo.recordID), oa.resourceUrl)';
		$termArr['relationshipOfResource'] = 'http://rs.tdwg.org/dwc/terms/relationshipOfResource';
		$columnArr['relationshipOfResource'] = 'oa.relationship';
		$termArr['relationshipAccordingTo'] = 'http://rs.tdwg.org/dwc/terms/relationshipAccordingTo';
		$columnArr['relationshipAccordingTo'] = 'oa.accordingTo';
 		$termArr['relationshipEstablishedDate'] = 'http://rs.tdwg.org/dwc/terms/relationshipEstablishedDate';
 		$columnArr['relationshipEstablishedDate'] = 'oa.establishedDate';
		$termArr['relationshipRemarks'] = 'http://rs.tdwg.org/dwc/terms/relationshipRemarks';
		$columnArr['relationshipRemarks'] = 'oa.notes';
		$termArr['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
		$columnArr['scientificName'] = "CASE WHEN oa.associationType = 'observational' THEN oa.verbatimSciName ELSE ot.sciname END AS sciname"; // Note that t.sciname delivers the subject sciname; hence, o.sciname

		$termArr['associd'] = 'https://symbiota.org/terms/associd';
		$columnArr['associd'] = 'oa.associd';
		$termArr['associationType'] = 'https://symbiota.org/terms/associationType';
		$columnArr['associationType'] = 'oa.associationType';
		$termArr['subType'] = 'https://symbiota.org/terms/subType';
		$columnArr['subType'] = 'oa.subType';
		$termArr['objectID'] = 'https://symbiota.org/terms/objectID';
		$columnArr['objectID'] = 'oa.objectID';
		$termArr['identifier'] = 'https://symbiota.org/terms/identifier';
		$columnArr['identifier'] = 'oa.identifier';
		$termArr['basisOfRecord'] = 'http://rs.tdwg.org/dwc/terms/basisOfRecord';
		$columnArr['basisOfRecord'] = 'oa.basisOfRecord';
		$termArr['tid'] = 'https://symbiota.org/terms/tid';
		$columnArr['tid'] = 'oa.tid';
		$termArr['locationOnHost'] = 'https://symbiota.org/terms/locationOnHost';
		$columnArr['locationOnHost'] = 'oa.locationOnHost';
		$termArr['conditionOfAssociate'] = 'https://symbiota.org/terms/conditionOfAssociate';
		$columnArr['conditionOfAssociate'] = 'oa.conditionOfAssociate';
		$termArr['imageMapJSON'] = 'https://symbiota.org/terms/imageMapJSON';
		$columnArr['imageMapJSON'] = 'oa.imageMapJSON';
		$termArr['dynamicProperties'] = 'http://rs.tdwg.org/dwc/terms/dynamicProperties';
		$columnArr['dynamicProperties'] = 'oa.dynamicProperties';
		$termArr['sourceIdentifier'] = 'https://symbiota.org/terms/sourceIdentifier';
		$columnArr['sourceIdentifier'] = 'oa.sourceIdentifier';
		$termArr['recordID'] = 'https://symbiota.org/terms/recordID';
		$columnArr['recordID'] = 'oa.recordID';
		$termArr['createdUid'] = 'https://symbiota.org/terms/createdUid';
		$columnArr['createdUid'] = 'oa.createdUid';
		$termArr['modifiedTimestamp'] = 'https://symbiota.org/terms/modifiedTimestamp';
		$columnArr['modifiedTimestamp'] = 'oa.modifiedTimestamp';
		$termArr['modifiedUid'] = 'https://symbiota.org/terms/modifiedUid';
		$columnArr['modifiedUid'] = 'oa.modifiedUid';
		$termArr['initialtimestamp'] = 'https://symbiota.org/terms/initialtimestamp';
		$columnArr['initialtimestamp'] = 'oa.initialtimestamp';


		$this->fieldArr['terms'] = $this->trimBySchemaType($termArr);
		$this->fieldArr['fields'] = $this->trimBySchemaType($columnArr);
	}

	private function trimBySchemaType($dataArr){
		$trimArr = array();
		if($this->schemaType == 'dwc'){
			$trimArr = array('associd', 'associationType', 'subType', 'objectID', 'identifier',
			 'verbatimSciname', 'tid', 'locationOnHost', 'conditionOfAssociate',
			  'imageMapJSON', 'sourceIdentifier', 'recordID', 'createdUid',
			   'modifiedTimestamp', 'modifiedUid', 'initialtimestamp');
		}
		return array_diff_key($dataArr, array_flip($trimArr));
	}


	public function setSqlBase($getInverse = false){
		if($this->fieldArr){
			$sqlFrag = '';
			if(!$getInverse){
				foreach($this->fieldArr['fields'] as $colName){
					if($colName) $sqlFrag .= ', ' . $colName;
				}
				$this->sqlBase = 'SELECT DISTINCT ' . trim($sqlFrag, ', ') . ' FROM omoccurrences o
					INNER JOIN omoccurassociations oa ON o.occid = oa.occid
					LEFT JOIN omoccurrences oo ON oo.occid = oa.occidAssociate
					LEFT JOIN taxa t on t.tid = oo.tidInterpreted
					LEFT JOIN taxa ot on ot.tid = oo.tidInterpreted';
			}
			else{
				$this->fieldArr['fields']['relationship'] = 'terms.inverseRelationship AS relationship';
				foreach($this->fieldArr['fields'] as $colName){
					if($colName) $sqlFrag .= ', ' . $colName;
				}
				$this->sqlBase = 'SELECT DISTINCT ' . trim($sqlFrag, ', ') . ' FROM omoccurrences o
					INNER JOIN omoccurassociations oa ON o.occid = oa.occidAssociate
					INNER JOIN omoccurrences oo ON oo.occid = oa.occid
					LEFT JOIN taxa t on t.tid = o.tidInterpreted
					LEFT JOIN taxa ot on ot.tid = oo.tidInterpreted
					LEFT JOIN (SELECT t.term, t.inverseRelationship
					FROM ctcontrolvocabterm t INNER JOIN ctcontrolvocab v ON t.cvID = v.cvID
					WHERE v.tablename = "omoccurassociations" AND fieldName = "relationship" AND t.inverseRelationship IS NOT NULL) terms ON oa.relationship = terms.term ';
			}
		}
	}

}

?>