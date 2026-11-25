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
		$this->setSqlInternal();
		$this->setSqlInternalInverse();
		$this->setSqlSpecimenDuplicates();
		$this->setSqlExsicateDuplicates();
		$this->setFileHandler($filePath);
	}

	//Based on https://rs.gbif.org/extension/resource_relationship_2024-02-19.xml
	private function setFieldArr(){
		$columnArr = array();
		$columnArr['coreid'] = 'o.occid';
		$termArr['resourceRelationshipID'] = 'http://rs.tdwg.org/dwc/terms/resourceRelationshipID';
		$columnArr['resourceRelationshipID'] = 'IFNULL(oa.instanceID, oa.recordID)';
		$termArr['resourceID'] = 'http://rs.tdwg.org/dwc/terms/resourceID';
		$columnArr['resourceID'] = 'IFNULL(o.occurrenceID, o.recordID)';
		$termArr['relationshipOfResourceID'] = 'http://rs.tdwg.org/dwc/terms/relationshipOfResourceID';
		$columnArr['relationshipOfResourceID'] = 'oa.relationshipID';
		$termArr['relatedResourceID'] = 'http://rs.tdwg.org/dwc/terms/relatedResourceID';
		$columnArr['relatedResourceID'][0] = 'IFNULL(oa.objectID, oa.resourceUrl)';
		$columnArr['relatedResourceID'][1] = 'IFNULL(IFNULL(IFNULL(oa.objectID, oo.occurrenceID), oo.recordID), oa.resourceUrl)';
		$termArr['relationshipOfResource'] = 'http://rs.tdwg.org/dwc/terms/relationshipOfResource';
		$columnArr['relationshipOfResource'] = 'oa.relationship';
		$termArr['relationshipAccordingTo'] = 'http://rs.tdwg.org/dwc/terms/relationshipAccordingTo';
		$columnArr['relationshipAccordingTo'] = 'oa.accordingTo';
 		$termArr['relationshipEstablishedDate'] = 'http://rs.tdwg.org/dwc/terms/relationshipEstablishedDate';
 		$columnArr['relationshipEstablishedDate'] = 'oa.establishedDate';
		$termArr['relationshipRemarks'] = 'http://rs.tdwg.org/dwc/terms/relationshipRemarks';
		$columnArr['relationshipRemarks'] = 'oa.notes';
		$termArr['scientificName'] = 'http://rs.tdwg.org/dwc/terms/scientificName';
		$columnArr['scientificName'][0] = 'oa.verbatimSciName AS sciname';
		$columnArr['scientificName'][1] = 'CASE WHEN oa.associationType = "observational" THEN oa.verbatimSciName ELSE IFNULL(t.sciname, oo.sciname) END AS sciname'; // Note that t.sciname delivers the subject sciname; hence, o.sciname
		$termArr['references'] = 'http://purl.org/dc/terms/references';
		$columnArr['references'][0] = '"" as `references`';
		$columnArr['references'][1] = 'CONCAT("'.$this->serverPath . '/collections/individual/index.php?guid=", oo.recordID) AS `references`';
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

	private function setSqlBase(){
		//External, observation, and resource associations
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $fieldValue){
				if(is_array($fieldValue)) $fieldValue = $fieldValue[0];
				if($fieldValue) $sqlFrag .= ', ' . $fieldValue;
			}
			$this->sqlArr[] = 'SELECT DISTINCT ' . trim($sqlFrag, ', ') . ' FROM omoccurrences o
				INNER JOIN omexportoccurrences e ON o.occid = e.occid
				INNER JOIN omoccurassociations oa ON o.occid = oa.occid
				WHERE oa.occidAssociate IS NULL AND (e.omExportID = ?) ';
		}
	}

	private function setSqlInternal(){
		//Internal associations
		if($this->fieldArr){
			$sqlFrag = '';
			foreach($this->fieldArr['fields'] as $fieldValue){
				if(is_array($fieldValue)) $fieldValue = $fieldValue[1];
				if($fieldValue) $sqlFrag .= ', ' . $fieldValue;
			}
			$this->sqlArr[] = 'SELECT DISTINCT ' . trim($sqlFrag, ', ') . ' FROM omoccurrences o
				INNER JOIN omexportoccurrences e ON o.occid = e.occid
				INNER JOIN omoccurassociations oa ON o.occid = oa.occid
				INNER JOIN omoccurrences oo ON oa.occidAssociate = oo.occid
				LEFT JOIN taxa t ON oo.tidInterpreted = t.tid
				WHERE (e.omExportID = ?) ';
		}
	}

	private function setSqlInternalInverse(){
		//Inverse of internal associations
		if($this->fieldArr){
			$sqlFrag = '';
			$this->fieldArr['fields']['relationshipOfResource'] = 'terms.inverseRelationship';
			foreach($this->fieldArr['fields'] as $fieldValue){
				if(is_array($fieldValue)) $fieldValue = $fieldValue[1];
				if($fieldValue) $sqlFrag .= ', ' . $fieldValue;
			}
			$this->sqlArr[] = 'SELECT DISTINCT ' . trim($sqlFrag, ', ') . ' FROM omoccurrences o
				INNER JOIN omexportoccurrences e ON o.occid = e.occid
				INNER JOIN omoccurassociations oa ON o.occid = oa.occidAssociate
				INNER JOIN omoccurrences oo ON oa.occid = oo.occid
				LEFT JOIN taxa t ON oo.tidInterpreted = t.tid
				LEFT JOIN (SELECT t.term, t.inverseRelationship
				FROM ctcontrolvocabterm t INNER JOIN ctcontrolvocab v ON t.cvID = v.cvID
				WHERE v.tablename = "omoccurassociations" AND fieldName = "relationship" AND t.inverseRelationship IS NOT NULL) terms ON oa.relationship = terms.term
				WHERE (e.omExportID = ?) ';
		}
	}

	private function setSqlSpecimenDuplicates(){
		if($this->fieldArr){
			$modArr = array();
			$modArr['coreid'] = 'x.occid';
			$modArr['resourceID'] = 'IFNULL(o.occurrenceID, o.recordID) AS resourceID';
			$modArr['relatedResourceID'] = 'IFNULL(oa.occurrenceID, oa.recordID) AS relatedResourceID';
			$modArr['references'] = 'CONCAT("'.$this->serverPath . '/collections/individual/index.php?guid=",oa.recordID) AS `references`';
			$modArr['relationshipOfResource'] = '"Duplicate of" AS relationshipOfResource';
			$modArr['scientificName'] = 'oa.sciName';
			$modArr['basisOfRecord'] = 'oa.basisOfRecord';
			$modArr['relationshipEstablishedDate'] = 'l.initialtimestamp AS relationshipEstablishedDate';
			$modArr['subType'] = '"specimenDuplicate" AS subType';
			$modArr['relationshipRemarks'] = '"Specimen Duplicate" AS relationshipRemarks';

			$selectArr = array();
			foreach($this->fieldArr['fields'] as $termName => $fieldValue){
				if(is_array($fieldValue)) $fieldValue = $fieldValue[0];
				if(isset($modArr[$termName])){
					$selectArr[] = $modArr[$termName];
				}
				else{
					//Default to empty output for that column, which is needed to maintain column alignment within output file
					$selectArr[] = '"" AS ' . $termName;
				}
			}

			//Append all specimen duplicates, excluding those linked to an exsiccati
			$this->sqlArr[] = 'SELECT ' . implode(',', $selectArr) .
				' FROM omexportoccurrences x INNER JOIN omoccurrences o ON x.occid = o.occid
				INNER JOIN omoccurduplicatelink s ON x.occid = s.occid
				INNER JOIN omoccurduplicates d ON s.duplicateid = d.duplicateid
				INNER JOIN omoccurduplicatelink l ON d.duplicateid = l.duplicateid
				INNER JOIN omoccurrences oa ON l.occid = oa.occid
				LEFT JOIN omexsiccatiocclink e ON l.occid = e.occid
				WHERE (x.omExportID = ?) AND e.occid IS NULL AND s.occid != l.occid ';
		}
	}

	private function setSqlExsicateDuplicates(){
		if($this->fieldArr){
			$modArr = array();
			$modArr['coreid'] = 'x.occid';
			$modArr['resourceID'] = 'IFNULL(o.occurrenceID, o.recordID) AS resourceID';
			$modArr['relatedResourceID'] = 'IFNULL(oa.occurrenceID, oa.recordID) AS relatedResourceID';
			$modArr['references'] = 'CONCAT("'.$this->serverPath . '/collections/individual/index.php?guid=",oa.recordID) AS `references`';
			$modArr['relationshipOfResource'] = '"Duplicate of" AS relationshipOfResource';
			$modArr['scientificName'] = 'oa.sciName';
			$modArr['basisOfRecord'] = 'oa.basisOfRecord';
			$modArr['relationshipEstablishedDate'] = 'l2.initialtimestamp AS relationshipEstablishedDate';
			$modArr['dynamicProperties'] = 'JSON_OBJECT("title", REPLACE(t.title,\'"\',"\'"), "abbreviation", t.abbreviation, "editor", t.editor, "range", t.exsrange, "number", n.exsnumber, "notes", l.notes) AS dynamicProperties';
			$modArr['subType'] = '"exsiccataeSpecimenDuplicate" AS subType';
			$modArr['relationshipRemarks'] = '"Exsiccatae Specimen Duplicate" AS relationshipRemarks';

			$selectArr = array();
			foreach($this->fieldArr['fields'] as $termName => $fieldValue){
				if(is_array($fieldValue)) $fieldValue = $fieldValue[0];
				if(isset($modArr[$termName])){
					$selectArr[] = $modArr[$termName];
				}
				else{
					//Default to empty output for that column, which is needed to maintain column alignment within output file
					$selectArr[] = '"" AS ' . $termName;
				}
			}

			//Append all exsiccati records
			$this->sqlArr[] = 'SELECT ' . implode(',', $selectArr) .
			' FROM omexportoccurrences x INNER JOIN omoccurrences o ON x.occid = o.occid
			INNER JOIN omexsiccatiocclink l ON x.occid = l.occid
			INNER JOIN omexsiccatinumbers n ON l.omenid = n.omenid
			INNER JOIN omexsiccatititles t ON n.ometid = t.ometid
			INNER JOIN omexsiccatiocclink l2 ON n.omenid = l2.omenid
			INNER JOIN omoccurrences oa ON l2.occid = oa.occid
			WHERE x.omExportID = ? AND o.occid != oa.occid';
		}
	}
}

?>