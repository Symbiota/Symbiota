


ALTER TABLE `taxa` 
  ADD COLUMN `sourceIdentifier` VARCHAR(150) NULL AFTER `source`;

ALTER TABLE `taxa` 
  DROP INDEX `rankid_index`,
  DROP INDEX `unitname1_index`,
  DROP INDEX `sciname_index`,
  DROP INDEX `idx_taxacreated`;

ALTER TABLE `taxa` 
  ADD INDEX `IX_taxa_rankid` (`rankid` ASC),
  ADD INDEX `IX_taxa_unitname1` (`unitname1` ASC),
  ADD INDEX `IX_taxa_sciname` (`sciname` ASC),
  ADD INDEX `IX_taxa_initialTimestamp` (`initialTimestamp` ASC);


ALTER TABLE `taxaresourcelinks` 
  CHANGE COLUMN `taxaresourceid` `taxaResourceID` INT(11) NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `sourcename` `sourceName` VARCHAR(150) NOT NULL ,
  CHANGE COLUMN `sourceidentifier` `sourceIdentifier` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `sourceguid` `sourceGUID` VARCHAR(150) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `taxaresourcelinks` 
  DROP FOREIGN KEY `FK_taxaresource_tid`;

ALTER TABLE `taxaresourcelinks` 
  DROP INDEX `UNIQUE_taxaresource`,
  DROP INDEX `taxaresource_name`,
  DROP INDEX `FK_taxaresource_tid_idx`;
  
ALTER TABLE `taxaresourcelinks` 
  ADD UNIQUE INDEX `UQ_taxaResource_tid_source` (`tid` ASC, `sourceName` ASC),
  ADD INDEX `IX_taxaResource_sourceName` (`sourceName` ASC),
  ADD INDEX `IX_taxaResource_sourceID` (`sourceIdentifier` ASC),
  ADD INDEX `FK_taxaResource_tid_idx` (`tid` ASC);

ALTER TABLE `taxaresourcelinks` 
  ADD CONSTRAINT `FK_taxaResource_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `uploadtaxa` 
  ADD COLUMN `sourceIdentifier` VARCHAR(150) NULL AFTER `source`;

ALTER TABLE `uploadtaxa` 
  CHANGE COLUMN `TID` `tid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `SourceId` `sourceID` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `UpperTaxonomy` `upperTaxonomy` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `Family` `family` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `RankId` `rankID` SMALLINT(5) NULL DEFAULT NULL ,
  CHANGE COLUMN `RankName` `rankName` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `scinameinput` `scinameInput` VARCHAR(250) NOT NULL ,
  CHANGE COLUMN `SciName` `sciName` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitInd1` `unitInd1` VARCHAR(1) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitName1` `unitName1` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitInd2` `unitInd2` VARCHAR(1) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitName2` `unitName2` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitInd3` `unitInd3` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitName3` `unitName3` VARCHAR(35) NULL DEFAULT NULL ,
  CHANGE COLUMN `Author` `author` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `InfraAuthor` `infraAuthor` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `Acceptance` `acceptance` INT(10) UNSIGNED NULL DEFAULT 1 COMMENT '0 = not accepted; 1 = accepted' ,
  CHANGE COLUMN `TidAccepted` `tidAccepted` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `AcceptedStr` `acceptedStr` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `SourceAcceptedId` `sourceAcceptedID` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `UnacceptabilityReason` `unacceptabilityReason` VARCHAR(24) NULL DEFAULT NULL ,
  CHANGE COLUMN `ParentTid` `parentTid` INT(10) NULL DEFAULT NULL ,
  CHANGE COLUMN `ParentStr` `parentStr` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `SourceParentId` `sourceParentId` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `SecurityStatus` `securityStatus` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality' ,
  CHANGE COLUMN `Source` `source` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `Notes` `notes` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `vernlang` `vernLang` VARCHAR(15) NULL DEFAULT NULL ,
  CHANGE COLUMN `Hybrid` `hybrid` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `ErrorStatus` `errorStatus` VARCHAR(150) NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `uploadtaxa` 
  DROP INDEX `UNIQUE_sciname`,
  DROP INDEX `sourceID_index`,
  DROP INDEX `sourceAcceptedId_index`,
  DROP INDEX `sciname_index`,
  DROP INDEX `scinameinput_index`,
  DROP INDEX `parentStr_index`,
  DROP INDEX `acceptedStr_index`,
  DROP INDEX `unitname1_index`,
  DROP INDEX `sourceParentId_index`,
  DROP INDEX `acceptance_index`;

ALTER TABLE `uploadtaxa` 
  ADD UNIQUE INDEX `UQ_scinameAuthorRankid` (`rankID` ASC, `sciname` ASC, `author` ASC, `acceptedStr` ASC),
  ADD INDEX `IX_uploadtaxa_sourceID` (`sourceID` ASC),
  ADD INDEX `IX_uploadtaxa_sourceAcceptedID` (`sourceAcceptedID` ASC),
  ADD INDEX `IX_uploadtaxa_sciname` (`sciname` ASC),
  ADD INDEX `IX_uploadtaxa_scinameInput` (`scinameInput` ASC),
  ADD INDEX `IX_uploadtaxa_parentStr` (`parentStr` ASC),
  ADD INDEX `IX_uploadtaxa_acceptedStr` (`acceptedStr` ASC),
  ADD INDEX `IX_uploadtaxa_unitName1` (`unitName1` ASC),
  ADD INDEX `IX_uploadtaxa_sourceParentID` (`sourceParentID` ASC),
  ADD INDEX `IX_uploadtaxa_acceptance` (`acceptance` ASC);


