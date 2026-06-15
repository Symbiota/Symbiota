INSERT INTO `schemaversion` (versionnumber) VALUES ('3.5');




#Adjust column order so that taxon author field is closer to sciname
ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `institutionCode` `institutionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `ownerInstitutionCode`,
  CHANGE COLUMN `collectionCode` `collectionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `institutionCode`,
  CHANGE COLUMN `institutionID` `institutionID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionCode`,
  CHANGE COLUMN `collectionID` `collectionID` VARCHAR(255) NULL DEFAULT NULL AFTER `institutionID`,
  CHANGE COLUMN `datasetID` `datasetID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionID`,
  CHANGE COLUMN `organismID` `organismID` VARCHAR(150) NULL DEFAULT NULL AFTER `datasetID`,
  CHANGE COLUMN `scientificNameAuthorship` `scientificNameAuthorship` VARCHAR(255) NULL DEFAULT NULL AFTER `sciname`;


#Ensure that there are no geothes terms that contain double spaces, which has been an issue
UPDATE geographicthesaurus
SET geoterm = replace(geoterm, "  ", " ") 
WHERE geoterm LIKE "%  %";



#Add definitions for omoccurrences processingStatus controlled vocabularies 
INSERT INTO `ctcontrolvocab` (`title`, `tableName`, `fieldName`) 
  VALUES ('Occurrence Processing Status terms', 'omoccurrences', 'processingStatus');
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Unprocessed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 1" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 2" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 3" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Pending Review" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Expert Required" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Reviewed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Closed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";



