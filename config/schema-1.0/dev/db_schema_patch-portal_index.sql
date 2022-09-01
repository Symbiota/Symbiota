#One of the following will fail, depending on what has been applied
ALTER TABLE `omcollections` 
  ADD COLUMN `collectionGuid` TEXT NULL AFTER `aggKeysStr`;

ALTER TABLE `omcollections` 
  ADD COLUMN `recordID` TEXT NULL AFTER `aggKeysStr`;

ALTER TABLE `omcollections` 
  CHANGE COLUMN `CollID` `collID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `InstitutionCode` `institutionCode` VARCHAR(45) NOT NULL ,
  CHANGE COLUMN `CollectionCode` `collectionCode` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `CollectionName` `collectionName` VARCHAR(150) NOT NULL ,
  CHANGE COLUMN `collectionId` `collectionID` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `fulldescription` `fullDescription` VARCHAR(2000) NULL DEFAULT NULL ,
  CHANGE COLUMN `Homepage` `homepage` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `IndividualUrl` `individualUrl` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `latitudedecimal` `latitudeDecimal` DOUBLE(8,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `longitudedecimal` `longitudeDecimal` DOUBLE(9,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `CollType` `collType` VARCHAR(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations' ,
  CHANGE COLUMN `ManagementType` `managementType` VARCHAR(45) NULL DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data' ,
  CHANGE COLUMN `PublicEdits` `publicEdits` INT(1) UNSIGNED NOT NULL DEFAULT 1 ,
  CHANGE COLUMN `collectionguid` `collectionGuid` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `securitykey` `securityKey` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `guidtarget` `guidTarget` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `accessrights` `accessRights` VARCHAR(1000) NULL DEFAULT NULL ,
  CHANGE COLUMN `SortSeq` `sortSeq` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `omcollections` 
  ADD COLUMN `dwcTermJson` TEXT NULL AFTER `aggKeysStr`;


DROP TABLE IF EXISTS `portalindex`;

CREATE TABLE `portalindex` (
  `portalID` int(11) NOT NULL AUTO_INCREMENT,
  `portalName` varchar(150) NOT NULL,
  `acronym` varchar(45) DEFAULT NULL,
  `portalDescription` varchar(250) DEFAULT NULL,
  `urlRoot` varchar(150) NOT NULL,
  `securityKey` varchar(45) DEFAULT NULL,
  `symbiotaVersion` varchar(45) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `manager` varchar(45) DEFAULT NULL,
  `managerEmail` varchar(45) DEFAULT NULL,
  `primaryLead` varchar(45) DEFAULT NULL,
  `primaryLeadEmail` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`portalID`),
  UNIQUE KEY `UQ_portalIndex_guid` (`guid`)
);

DROP TABLE IF EXISTS `portalpublications`;

CREATE TABLE `portalpublications` (
  `pubid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pubTitle` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `collid` int(10) unsigned NOT NULL,
  `portalID` int(11) NOT NULL,
  `direction` varchar(45) NOT NULL,
  `criteriaJson` text DEFAULT NULL,
  `includeDeterminations` int(11) DEFAULT 1,
  `includeImages` int(11) DEFAULT 1,
  `autoUpdate` int(11) DEFAULT 0,
  `lastDateUpdate` datetime DEFAULT NULL,
  `updateInterval` int(11) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pubid`),
  KEY `FK_portalpub_collid_idx` (`collid`),
  KEY `FK_portalpub_portalID_idx` (`portalID`),
  KEY `FK_portalpub_uid_idx` (`createdUid`),
  CONSTRAINT `FK_portalpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_portalID` FOREIGN KEY (`portalID`) REFERENCES `portalindex` (`portalID`) ON DELETE CASCADE ON UPDATE CASCADE
);

DROP TABLE IF EXISTS `portaloccurrences`;

CREATE TABLE `portaloccurrences` (
  `occid` int(10) unsigned NOT NULL,
  `pubid` int(10) unsigned NOT NULL,
  `targetOccid` int(11) NOT NULL,
  `verification` int(11) NOT NULL DEFAULT 0,
  `refreshtimestamp` datetime NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occid`,`pubid`),
  KEY `FK_portalOccur_occid_idx` (`occid`),
  KEY `FK_portalOccur_pubID_idx` (`pubid`),
  CONSTRAINT `FK_portalOccur_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalOccur_pubid` FOREIGN KEY (`pubid`) REFERENCES `portalpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
);




