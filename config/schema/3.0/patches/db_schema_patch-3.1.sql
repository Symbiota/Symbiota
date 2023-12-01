INSERT INTO schemaversion (versionnumber) values ("3.1");


ALTER TABLE `fmchklsttaxalink` 
  DROP FOREIGN KEY `FK_chklsttaxalink_cid`;

ALTER TABLE `fmchklsttaxalink` 
  ADD CONSTRAINT `FK_chklsttaxalink_cid`  FOREIGN KEY (`clid`)  REFERENCES `fmchecklists` (`clid`)  ON DELETE CASCADE  ON UPDATE CASCADE;


#Set foreign keys for fmchklstcoordinates
ALTER TABLE `fmchklstcoordinates` 
  DROP INDEX `FKchklsttaxalink` ;

ALTER TABLE `fmchklstcoordinates` 
  ADD INDEX `FK_checklistCoord_tid_idx` (`tid` ASC),
  ADD INDEX `FK_checklistCoord_clid_idx` (`clid` ASC);

ALTER TABLE `fmchklstcoordinates` 
  ADD UNIQUE INDEX `UQ_checklistCoord_unique` (`clid` ASC, `tid` ASC, `decimalLatitude` ASC, `decimalLongitude` ASC);

ALTER TABLE `fmchklstcoordinates` 
  ADD CONSTRAINT `FK_checklistCoord_clid`  FOREIGN KEY (`clid`)  REFERENCES `fmchecklists` (`clid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_checklistCoord_tid`  FOREIGN KEY (`tid`)  REFERENCES `taxa` (`tid`)  ON DELETE CASCADE  ON UPDATE CASCADE;


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


# Needed to ensure basisOfRecord values are tagged correctly based on collection type (aka collType field)
UPDATE omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid
  SET o.basisofrecord = "PreservedSpecimen"
  WHERE (o.basisofrecord = "HumanObservation" OR o.basisofrecord IS NULL) AND c.colltype = 'Preserved Specimens'
  AND o.occid NOT IN(SELECT occid FROM omoccuredits WHERE fieldname = "basisofrecord");

#Standardize naming of indexes within occurrence table 
ALTER TABLE `omoccurrences` 
  ADD INDEX `FK_occurrences_collid` (`collid` ASC);

ALTER TABLE `omoccurrences` 
  RENAME INDEX `Index_collid` TO `UQ_occurrences_collid_dbpk`,
  RENAME INDEX `UNIQUE_occurrenceID` TO `UQ_occurrences_occurrenceID`,
  RENAME INDEX `Index_sciname` TO `IX_occurrences_sciname`,
  RENAME INDEX `Index_family` TO `IX_occurrences_family`,
  RENAME INDEX `Index_country` TO `IX_occurrences_country`,
  RENAME INDEX `Index_state` TO `IX_occurrences_state`,
  RENAME INDEX `Index_county` TO `IX_occurrences_county`,
  RENAME INDEX `Index_collector` TO `IX_occurrences_collector`,
  RENAME INDEX `Index_gui` TO `IX_occurrences_gui`,
  RENAME INDEX `Index_ownerInst` TO `IX_occurrences_ownerInst`,
  RENAME INDEX `FK_omoccurrences_tid` TO `FK_occurrences_tid_idx`,
  RENAME INDEX `FK_omoccurrences_uid` TO `FK_occurrences_uid_idx`,
  RENAME INDEX `Index_municipality` TO `IX_occurrences_municipality`,
  RENAME INDEX `Index_collnum` TO `IX_occurrences_collnum`,
  RENAME INDEX `Index_catalognumber` TO `IX_occurrences_catalognumber`,
  RENAME INDEX `Index_eventDate` TO `IX_occurrences_eventDate`,
  RENAME INDEX `Index_occurrences_procstatus` TO `IX_occurrences_procstatus`,
  RENAME INDEX `occelevmin` TO `IX_occurrences_occelevmin`,
  RENAME INDEX `occelevmax` TO `IX_occurrences_occelevmax`,
  RENAME INDEX `Index_occurrences_cult` TO `IX_occurrences_cult`,
  RENAME INDEX `Index_occurrences_typestatus` TO `IX_occurrences_typestatus`,
  RENAME INDEX `Index_occurDateLastModifed` TO `IX_occurrences_dateLastModifed`,
  RENAME INDEX `Index_occurDateEntered` TO `IX_occurrences_dateEntered`,
  RENAME INDEX `Index_occurRecordEnteredBy` TO `IX_occurrences_recordEnteredBy`,
  RENAME INDEX `Index_locality` TO `IX_occurrences_locality`,
  RENAME INDEX `Index_otherCatalogNumbers` TO `IX_occurrences_otherCatalogNumbers`,
  RENAME INDEX `Index_locationID` TO `IX_occurrences_locationID`,
  RENAME INDEX `Index_eventID` TO `IX_occurrences_eventID`,
  RENAME INDEX `Index_occur_localitySecurity` TO `IX_occurrences_localitySecurity`,
  RENAME INDEX `IX_omoccur_eventDate2` TO `IX_occurrences_eventDate2`,
  RENAME INDEX `IX_omoccurrences_recordID` TO `IX_occurrences_recordID`;


#deprecate omoccurresource table in preference for omoccurassociations 
ALTER TABLE `omoccurresource` 
  RENAME TO  `deprecated_omoccurresource` ;



ALTER TABLE `ctcontrolvocab` 
  ADD COLUMN `filterVariable` VARCHAR(150) NOT NULL DEFAULT '' AFTER `fieldName`,
  DROP INDEX `UQ_ctControlVocab` ,
  ADD UNIQUE INDEX `UQ_ctControlVocab` (`title` ASC, `tableName` ASC, `fieldName` ASC, `filterVariable` ASC);


INSERT INTO ctcontrolvocab(title, tableName, fieldName, filterVariable)
VALUES("Occurrence Associations Type", "omoccurassociations", "relationship", "associationType:resource");
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "fieldNotes", "Field Notes" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "relationship" AND filterVariable = "associationType:resource";
INSERT INTO ctcontrolvocabterm(cvID, term, termDisplay)
SELECT cvID, "genericResource", "Generic Resource" FROM ctcontrolvocab WHERE tableName = "omoccurassociations" AND fieldName = "relationship" AND filterVariable = "associationType:resource";


