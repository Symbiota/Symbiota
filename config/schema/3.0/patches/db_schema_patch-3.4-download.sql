

#download staging tables
CREATE TABLE `omexport` (
  `omExportID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `category` VARCHAR(45) NOT NULL,
  `tagName` VARCHAR(45) NOT NULL,
  `queryTerms` MEDIUMTEXT NOT NULL,
  `portalDomain` VARCHAR(45) NULL,
  `expiration` DATETIME NULL,
  `ipAddress` VARCHAR(45) NULL,
  `notes` VARCHAR(255) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp
  PRIMARY KEY (`omExportID`));

ALTER TABLE `omexport` 
  ADD INDEX `FK_omexport_uid_idx` (`uid` ASC);

ALTER TABLE `omexport` 
  ADD CONSTRAINT `FK_omexport_uid`  FOREIGN KEY (`uid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE;


CREATE TABLE `omexportoccurrences` (
  `pmExportOccurID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `omExportID` INT UNSIGNED NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `otherCatalogNumbers` TEXT NULL,
  `higherClassification` TEXT NULL,
  `kingdom` VARCHAR(45) NULL,
  `phylum` VARCHAR(45) NULL,
  `class` VARCHAR(45) NULL,
  `order` VARCHAR(45) NULL,
  `taxonID` INT UNSIGNED NULL,
  `scientificNameAuthorship` VARCHAR(45) NULL,
  `genus` VARCHAR(45) NULL,
  `subgenus` VARCHAR(45) NULL,
  `specificEpithet` VARCHAR(45) NULL,
  `taxonRank` VARCHAR(45) NULL,
  `verbatimTaxonRank` VARCHAR(45) NULL,
  `infraspecificEpithet` VARCHAR(45) NULL,
  `cultivarEpithet` VARCHAR(45) NULL,
  `tradeName` VARCHAR(45) NULL,
  `acceptedNameUsage` VARCHAR(45) NULL,
  `acceptedNameUsageAuthorship` VARCHAR(45) NULL,
  `acceptedNameUsageID` VARCHAR(45) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp
  PRIMARY KEY (`pmExportStagingID`));



ALTER TABLE `omexportoccurrences` 
  ADD INDEX `FK_omexportoccur_omExportID_idx` (`omExportID` ASC),
  ADD INDEX `FK_omexportoccur_occid_idx` (`occid` ASC);

ALTER TABLE `omexportoccurrences` 
  ADD CONSTRAINT `FK_omexportoccur_omExportID`  FOREIGN KEY (`omExportID`)  REFERENCES `omexport` (`omExportID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_omexportoccur_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `omexportoccurrences` 
  ADD UNIQUE INDEX `UQ_omexportoccur_unique` (`omExportID` ASC, `occid` ASC);

