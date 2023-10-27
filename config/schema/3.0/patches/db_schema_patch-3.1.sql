
ALTER TABLE `omoccurresource` 
  RENAME TO  `deprecated_omoccurresource` ;

ALTER TABLE `omoccurassociations` 
  ADD COLUMN `associationType` VARCHAR(45) NOT NULL AFTER `occid`;

ALTER TABLE `omoccurassociations` 
  DROP INDEX `UQ_omoccurassoc_sciname` ,
  ADD UNIQUE INDEX `UQ_omoccurassoc_sciname` (`occid` ASC, `verbatimSciname` ASC, `associationType` ASC);

ALTER TABLE `omoccurassociations` 
  ADD INDEX `IX_occurassoc_identifier` (`identifier` ASC),
  ADD INDEX `IX_occurassoc_recordID` (`recordID` ASC);
  

ALTER TABLE `omoccurassociations` 
  DROP INDEX `omossococcur_occid_idx`,
  ADD INDEX `FK_ossococcur_occid_idx` (`occid` ASC);

ALTER TABLE `omoccurassociations` 
  DROP INDEX `omossococcur_occidassoc_idx`,
  ADD INDEX `FK_ossococcur_occidassoc_idx` (`occidAssociate` ASC);

ALTER TABLE `omoccurassociations` 
  DROP INDEX `INDEX_verbatimSciname`,
  ADD INDEX `IX_occurassoc_verbatimSciname` (`verbatimSciname` ASC);


ALTER TABLE `omoccurassociations` 
  ADD UNIQUE INDEX `UQ_omoccurassoc_identifier` (`occid` ASC, `identifier` ASC);

UPDATE omoccurassociations
SET associationType = "internalOccurrence"
WHERE associationType = "" AND occidAssociate IS NOT NULL;

UPDATE omoccurassociations
SET associationType = "externalOccurrence"
WHERE associationType = "" AND occidAssociate IS NULL AND resourceUrl IS NOT NULL;

UPDATE omoccurassociations
SET associationType = "observational"
WHERE associationType = "" AND occidAssociate IS NULL AND resourceUrl IS NULL AND verbatimSciname IS NOT NULL;


ALTER TABLE `ctcontrolvocab` 
  ADD COLUMN `filterVariable` VARCHAR(150) NOT NULL DEFAULT '' AFTER `fieldName`,
  DROP INDEX `UQ_ctControlVocab` ,
  ADD UNIQUE INDEX `UQ_ctControlVocab` (`title` ASC, `tableName` ASC, `fieldName` ASC, `filterVariable` ASC);


INSERT INTO ctcontrolvocab(title, tableName, fieldName)
VALUES("Occurrence Associations Type", "omoccurassociations", "associationType");
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "internalOccurrence", "Occurrence - Internally Managed" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "associationType";
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "externalOccurrence", "Occurrence - Externally Managed" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "associationType";
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "observational", "Simple Observation" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "associationType";
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "resource", "General Resource" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "associationType";

INSERT INTO ctcontrolvocab(title, tableName, fieldName, filterVariable)
VALUES("Occurrence Associations Type", "omoccurassociations", "relationship", "associationType:resource");
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "fieldNotes", "Field Notes" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "relationship" AND filterVariable = "associationType:resource";
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "genericResource", "Generic Resource" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "relationship" AND filterVariable = "associationType:resource";


  