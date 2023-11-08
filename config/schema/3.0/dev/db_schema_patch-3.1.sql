INSERT IGNORE INTO schemaversion (versionnumber) values ("3.1");



#Occurrence Determinations 
ALTER TABLE `omoccurdeterminations` 
  DROP FOREIGN KEY `FK_omoccurdets_uid`;

ALTER TABLE `omoccurdeterminations` 
  ADD COLUMN `identificationUncertain` INT(2) NULL AFTER `tidInterpreted`,
  ADD COLUMN `modifiedUid` INT UNSIGNED NULL AFTER `createdUid`,
  CHANGE COLUMN `enteredByUid` `createdUid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  ADD INDEX `FK_omoccurdets_mod_idx` (`modifiedUid` ASC),
  ADD CONSTRAINT `FK_omoccurdets_uid`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_omoccurdets_mod`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE;
  

# Transfer current determinations from omoccurrences table
INSERT IGNORE INTO omoccurdeterminations(occid, identifiedBy, dateIdentified, family, sciname, verbatimIdentification, scientificNameAuthorship, tidInterpreted, 
identificationQualifier, genus, specificEpithet, verbatimTaxonRank, infraSpecificEpithet, isCurrent, identificationReferences, identificationRemarks, 
taxonRemarks)
SELECT occid, IFNULL(identifiedBy, "unknown"), IFNULL(dateIdentified, "s.d."), family, IFNULL(sciname, "undefined"), scientificName, scientificNameAuthorship, tidInterpreted, identificationQualifier, 
genus, specificEpithet, taxonRank, infraSpecificEpithet, 1 as isCurrent, identificationReferences, identificationRemarks, taxonRemarks
FROM omoccurrences;


