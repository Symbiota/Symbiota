INSERT INTO `schemaversion` (versionnumber) values ("3.3");

#Changes needed for full-record protections 
ALTER TABLE `omoccurrences` 
  CHANGE COLUMN `localitySecurity` `recordSecurity` INT(10) NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality; 5 = hide full record',
  CHANGE COLUMN `localitySecurityReason` `securityReason` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `omoccurrences` 
  DROP INDEX `IX_occurrences_localitySecurity` ;

ALTER TABLE `omoccurrences` 
  ADD INDEX `IX_occurrences_recordSecurity` (`recordSecurity` ASC);

UPDATE omoccurrences
  SET recordSecurity = 0 
  WHERE recordSecurity IS NULL;


#Synchronizes column sizes between temporary upload and target tables
ALTER TABLE `omoccurdeterminations`
  CHANGE COLUMN `taxonRemarks` `taxonRemarks` text,
  CHANGE COLUMN `identificationReferences` `identificationReferences` text,
  CHANGE COLUMN `identificationRemarks` `identificationRemarks` text;

ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `labelProject` `labelProject` varchar(250) DEFAULT NULL,
  CHANGE COLUMN `taxonRemarks` `taxonRemarks` text,
  CHANGE COLUMN `identificationReferences` `identificationReferences` text,
  CHANGE COLUMN `identificationRemarks` `identificationRemarks` text,
  CHANGE COLUMN `eventID` `eventID` varchar(150) DEFAULT NULL,
  CHANGE COLUMN `georeferenceRemarks` `georeferenceRemarks` varchar(500) DEFAULT NULL,
  CHANGE COLUMN `recordNumber` `recordNumber` varchar(45) DEFAULT NULL COMMENT 'Collector Number',
  CHANGE COLUMN `waterBody` `waterBody` varchar(75) DEFAULT NULL,
  CHANGE COLUMN `localitySecurity` `recordSecurity` INT(10) NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality; 5 = hide full record',
  CHANGE COLUMN `localitySecurityReason` `securityReason` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `uploaddetermtemp`
  CHANGE COLUMN `taxonRemarks` `taxonRemarks` text,
  CHANGE COLUMN `identificationReferences` `identificationReferences` text,
  CHANGE COLUMN `identificationRemarks` `identificationRemarks` text;


#Alterations to key-value upload staging table. Most important is addition of index to speed up linking to existing records
ALTER TABLE `uploadkeyvaluetemp` 
  ADD COLUMN `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp AFTER `type`,
  ADD INDEX `IX_uploadKeyValue_dbpk` (`dbpk` ASC);

ALTER TABLE `uploadkeyvaluetemp` 
  DROP FOREIGN KEY `FK_uploadKeyValue_collid`;

ALTER TABLE `uploadkeyvaluetemp` 
  CHANGE COLUMN `collid` `collid` INT(10) UNSIGNED NOT NULL ;

ALTER TABLE `uploadkeyvaluetemp` 
  ADD CONSTRAINT `FK_uploadKeyValue_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE;


#Portal Index field additions  
ALTER TABLE `portalindex` 
  ADD COLUMN `lastContact` TIMESTAMP NULL AFTER `notes`,
  ADD COLUMN `modifiedTimestamp` TIMESTAMP NULL AFTER `lastContact`,
  ADD COLUMN `apiVersion` VARCHAR(45) NULL AFTER `symbiotaVersion`;
