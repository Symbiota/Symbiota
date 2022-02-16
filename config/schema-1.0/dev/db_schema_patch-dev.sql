INSERT IGNORE INTO schemaversion (versionnumber) values ("1.3");

ALTER TABLE `agents`
  ADD INDEX `FK_agents_familyname` (`familyName` ASC),
  ADD UNIQUE INDEX `UQ_agents_guid` (`guid` ASC);

ALTER TABLE `agents` 
  RENAME INDEX `firstname` TO `FK_agents_firstname`;

ALTER TABLE `agents` 
  ALTER INDEX `FK_agents_firstname`;

CREATE TABLE `agentoccurrencelink` (
  `agentID` BIGINT(20) NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `isPrimary` INT NULL,
  `createdUid` INT UNSIGNED NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `dateLastModified` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`agentID`, `occid`),
  INDEX `FK_agentoccurlink_occid_idx` (`occid` ASC),
  INDEX `FK_agentoccurlink_created_idx` (`createdUid` ASC),
  INDEX `FK_agentoccurlink_modified_idx` (`modifiedUid` ASC),
  INDEX `FK_agentoccurlink_isPrimary` (`isPrimary` ASC),
  CONSTRAINT `FK_agentoccurlink_agentID`  FOREIGN KEY (`agentID`)  REFERENCES `agents` (`agentID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_created`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_modified` FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE);

INSERT IGNORE INTO agents(familyName,firstName,middleName,startYearActive,endYearActive,notes,rating,guid)
  SELECT DISTINCT familyname, firstname, middlename, startyearactive, endyearactive, notes, rating, guid 
  FROM omcollectors c LEFT JOIN agents a ON c.guid = a.guid
  WHERE a.guid IS NULL;

INSERT IGNORE INTO agentoccurrencelink(agentID, occid, isPrimary)
  SELECT a.agentID, o.occid, 1 as isPrimary
  FROM agents a INNER JOIN omcollectors c ON a.guid = c.guid
  INNER JOIN omoccurrences o ON c.recordedbyid = o.recordedbyid;

ALTER TABLE `ctcontrolvocab` 
  ADD COLUMN `collid` INT UNSIGNED NULL DEFAULT NULL AFTER `cvID`,
  ADD INDEX `FK_ctControlVocab_collid_idx` (`collid` ASC);

ALTER TABLE `ctcontrolvocab` 
  ADD CONSTRAINT `FK_ctControlVocab_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `fmprojects` 
  CHANGE COLUMN `projname` `projname` VARCHAR(75) NOT NULL ;

ALTER TABLE `guidoccurrences` 
  ADD COLUMN `occurrenceID` VARCHAR(45) NULL AFTER `archiveobj`;

ALTER TABLE `igsnverification` 
  CHANGE COLUMN `status` `syncStatus` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `igsnverification` 
  DROP FOREIGN KEY `FK_igsn_occid`;

ALTER TABLE `igsnverification` 
  CHANGE COLUMN `occid` `occidInPortal` INT(10) UNSIGNED NULL DEFAULT NULL ;

ALTER TABLE `igsnverification` 
  ADD COLUMN `occidInSesar` INT UNSIGNED NULL AFTER `occidInPortal`;

ALTER TABLE `igsnverification` 
  ADD CONSTRAINT `FK_igsn_occid`  FOREIGN KEY (`occidInPortal`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE;
  
ALTER TABLE `images` 
  ADD COLUMN `hashFunction` VARCHAR(45) NULL AFTER `sourceIdentifier`,
  ADD COLUMN `hashValue` VARCHAR(45) NULL AFTER `hashFunction`;

ALTER TABLE `imagetagkey` 
  ADD COLUMN `resourceLink` VARCHAR(250) NULL AFTER `description_en`,
  ADD COLUMN `audubonCoreTarget` VARCHAR(45) NULL AFTER `resourceLink`;

ALTER TABLE `imagetagkey` 
  CHANGE COLUMN `description_en` `description` VARCHAR(255) NOT NULL ;

CREATE TABLE `imagetaggroup` (
  `imgTagGroupID` INT NOT NULL AUTO_INCREMENT,
  `groupName` VARCHAR(45) NOT NULL,
  `category` VARCHAR(45) NULL,
  `resourceUrl` VARCHAR(150) NULL,
  `audubonCoreTarget` VARCHAR(45) NULL,
  `controlType` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`imgTagGroupID`),
  INDEX `IX_imagetaggroup` (`groupName` ASC)
);

ALTER TABLE `imagetagkey` 
  ADD COLUMN `imgTagGroupID` INT NULL AFTER `tagkey`,
  ADD INDEX `FK_imageTagKey_imgTagGroupID_idx` (`imgTagGroupID` ASC);

ALTER TABLE `imagetagkey` 
  ADD CONSTRAINT `FK_imageTagKey_imgTagGroupID`  FOREIGN KEY (`imgTagGroupID`)  REFERENCES `imagetaggroup` (`imgTagGroupID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `imagetag` 
  ADD COLUMN `imageBoundingBox` VARCHAR(45) NULL AFTER `keyvalue`,
  ADD COLUMN `notes` VARCHAR(250) NULL AFTER `imageBoundingBox`;

ALTER TABLE `imageprojects` 
  ADD COLUMN `projectType` VARCHAR(45) NULL AFTER `description`,
  ADD COLUMN `collid` INT UNSIGNED NULL AFTER `projectType`,
  CHANGE COLUMN `uidcreated` `uidcreated` INT(11) UNSIGNED NULL DEFAULT NULL ,
  ADD INDEX `FK_imageproject_collid_idx` (`collid` ASC),
  ADD INDEX `FK_imageproject_uid_idx` (`uidcreated` ASC);

ALTER TABLE `imageprojects` 
  ADD CONSTRAINT `FK_imageproject_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_imageproject_uid`  FOREIGN KEY (`uidcreated`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `institutions` 
  ADD COLUMN `institutionID` VARCHAR(45) NULL AFTER `iid`;

ALTER TABLE `geographicthesaurus` 
  ADD COLUMN `geoLevel` INT NOT NULL AFTER `category`;

ALTER TABLE `geographicthesaurus` 
  DROP FOREIGN KEY `FK_geothes_parentID`;

ALTER TABLE `geographicthesaurus` 
ADD CONSTRAINT `FK_geothes_parentID`  FOREIGN KEY (`parentID`)  REFERENCES `geographicthesaurus` (`geoThesID`)  ON DELETE RESTRICT  ON UPDATE CASCADE;

ALTER TABLE `geographicthesaurus` 
  ADD UNIQUE INDEX `UQ_geothes` (`geoterm` ASC, `parentID` ASC);

//Get rid of old geographic thesaurus tables that were never used
DROP TABLE geothescontinent;
DROP TABLE geothescountry;
DROP TABLE geothesstateprovince;
DROP TABLE geothescounty;
DROP TABLE geothesmunicipality;

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

DROP TABLE omcollectors;

CREATE TABLE `omcollproperties` (
  `collPropID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `collid` INT UNSIGNED NOT NULL,
  `propCategory` VARCHAR(45) NOT NULL,
  `propTitle` VARCHAR(45) NOT NULL,
  `propJson` LONGTEXT NULL,
  `notes` VARCHAR(255) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`collPropID`),
  INDEX `FK_omcollproperties_collid_idx` (`collid` ASC),
  INDEX `FK_omcollproperties_uid_idx` (`modifiedUid` ASC),
  CONSTRAINT `FK_omcollproperties_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)   ON DELETE CASCADE   ON UPDATE CASCADE,
  CONSTRAINT `FK_omcollproperties_uid`   FOREIGN KEY (`modifiedUid`)   REFERENCES `users` (`uid`)   ON DELETE CASCADE   ON UPDATE CASCADE);


CREATE TABLE `portalindex` (
  `portalID` INT NOT NULL AUTO_INCREMENT,
  `portalName` VARCHAR(45) NOT NULL,
  `acronym` VARCHAR(45) NULL,
  `portalDescription` VARCHAR(250) NULL,
  `urlRoot` VARCHAR(150) NOT NULL,
  `securityKey` VARCHAR(45) NULL,
  `symbiotaVersion` VARCHAR(45) NULL,
  `guid` VARCHAR(45) NULL,
  `manager` VARCHAR(45) NULL,
  `managerEmail` VARCHAR(45) NULL,
  `primaryLead` VARCHAR(45) NULL,
  `primaryLeadEmail` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`portalID`));

ALTER TABLE `portalindex` 
  ADD UNIQUE INDEX `UQ_portalIndex_guid` (`guid` ASC);

ALTER TABLE `omcollpublications` 
  RENAME TO `portalpublications` ;

ALTER TABLE `portalpublications` 
  DROP FOREIGN KEY `FK_adminpub_collid`;

ALTER TABLE `portalpublications` 
  DROP COLUMN `securityguid`,
  DROP COLUMN `targeturl`,
  ADD COLUMN `portalID` INT NULL AFTER `collid`,
  CHANGE COLUMN `collid` `collid` INT(10) UNSIGNED NULL ,
  CHANGE COLUMN `criteriajson` `criteriaJson` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `includedeterminations` `includeDeterminations` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `includeimages` `includeImages` INT(11) NULL DEFAULT 1,
  CHANGE COLUMN `autoupdate` `autoUpdate` INT(11) NULL DEFAULT 0,
  CHANGE COLUMN `lastdateupdate` `lastDateUpdate` DATETIME NULL DEFAULT NULL,
  CHANGE COLUMN `updateinterval` `updateInterval` INT(11) NULL DEFAULT NULL,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  ADD INDEX `FK_portalpub_portalID_idx` (`portalID` ASC);

ALTER TABLE `portalpublications` 
  ADD CONSTRAINT `FK_portalpub_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalpub_portalID`  FOREIGN KEY (`portalID`)  REFERENCES `portalindex` (`portalID`)  ON DELETE RESTRICT  ON UPDATE NO ACTION;

ALTER TABLE `portalpublications` 
  ADD COLUMN `pubTitle` VARCHAR(45) NULL AFTER `pubid`,
  ADD COLUMN `description` VARCHAR(250) NULL AFTER `pubTitle`,
  ADD COLUMN `createdUid` INT UNSIGNED NULL AFTER `updateInterval`;

ALTER TABLE `omcollpuboccurlink` 
  RENAME TO  `portaloccurrences` ;

ALTER TABLE `portaloccurrences` 
  DROP FOREIGN KEY `FK_ompubpubid`;

ALTER TABLE `portaloccurrences` 
  ADD COLUMN `portalID` INT NOT NULL AFTER `occid`,
  ADD COLUMN `targetOccid` INT NULL AFTER `pubid`,
  CHANGE COLUMN `occid` `occid` INT(10) UNSIGNED NOT NULL FIRST,
  CHANGE COLUMN `pubid` `pubid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`occid`, `portalID`),
  ADD INDEX `FK_portalOccur_portalID_idx` (`portalID` ASC);

ALTER TABLE `portaloccurrence` 
  ADD CONSTRAINT `FK_portalOccur_pubid`  FOREIGN KEY (`pubid`)  REFERENCES `portalpublications` (`pubid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_portalOccur_portalID`  FOREIGN KEY (`portalID`)  REFERENCES `portalindex` (`portalID`)  ON DELETE CASCADE  ON UPDATE CASCADE;


CREATE TABLE `omcrowdsourceproject` (
  `csProjID` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(45) NOT NULL,
  `description` VARCHAR(250) NULL,
  `instructions` TEXT NULL,
  `trainingurl` VARCHAR(250) NULL,
  `managers` VARCHAR(150) NULL,
  `criteria` VARCHAR(1500) NULL,
  `notes` VARCHAR(250) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`csProjID`));

ALTER TABLE `omcrowdsourceproject` 
  ADD INDEX `FK_croudsourceproj_uid_idx` (`modifiedUid` ASC) ;

ALTER TABLE `omcrowdsourceproject`
  ADD CONSTRAINT `FK_croudsourceproj_uid`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `omcrowdsourcequeue` 
  ADD COLUMN `csProjID` INT NULL AFTER `omcsid`,
  ADD INDEX `FK_omcrowdsourcequeue_csProjID_idx` (`csProjID` ASC);

ALTER TABLE `omcrowdsourcequeue` 
  ADD CONSTRAINT `FK_omcrowdsourcequeue_csProjID`  FOREIGN KEY (`csProjID`)  REFERENCES `omcrowdsourceproject` (`csProjID`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `omcrowdsourcequeue` 
  ADD COLUMN `dateProcessed` DATETIME NULL AFTER `isvolunteer`,
  ADD COLUMN `dateReviewed` DATETIME NULL AFTER `dateProcessed`;


ALTER TABLE `omoccurassociations` 
  CHANGE COLUMN `condition` `conditionOfAssociate` VARCHAR(250) NULL DEFAULT NULL ;

ALTER TABLE `omoccurrences` 
  DROP FOREIGN KEY `FK_omoccurrences_recbyid`;

ALTER TABLE `omoccurrences` 
  DROP COLUMN `recordedbyid`,
  DROP INDEX `FK_recordedbyid` ;

ALTER TABLE `specprocessorprojects` 
  ADD COLUMN `customStoredProcedure` VARCHAR(45) NULL AFTER `source`,
  ADD COLUMN `createdByUid` INT UNSIGNED NULL AFTER `lastrundate`,
  ADD INDEX `FK_specprocprojects_uid_idx` (`createdByUid` ASC);

ALTER TABLE `specprocessorprojects`
  ADD CONSTRAINT `FK_specprocprojects_uid`  FOREIGN KEY (`createdByUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `taxa` 
  CHANGE COLUMN `Author` `Author` VARCHAR(100) NOT NULL ;

UPDATE IGNORE taxa SET author = "" WHERE author IS NULL;

ALTER TABLE `taxa` 
  DROP INDEX `sciname_unique` ,
  ADD UNIQUE INDEX `sciname_unique` (`SciName` ASC, `RankId` ASC);
  
ALTER TABLE `taxstatus` 
  CHANGE COLUMN `taxonomicSource` `taxonomicSource` VARCHAR(500) NULL DEFAULT NULL;

ALTER TABLE `uploadspectemp` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`,
  CHANGE COLUMN `LatestDateCollected` `eventDate2` DATE NULL DEFAULT NULL AFTER `eventDate`;

ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `establishmentMeans` `establishmentMeans` VARCHAR(150) NULL DEFAULT NULL,
  CHANGE COLUMN `disposition` `disposition` varchar(250) NULL DEFAULT NULL,
  ADD COLUMN `observeruid` INT NULL AFTER `language`,
  ADD COLUMN `dateEntered` DATETIME NULL AFTER `recordEnteredBy`;

ALTER TABLE `uploadspectemp` 
  ADD COLUMN `eventID` VARCHAR(45) NULL AFTER `fieldnumber`;

ALTER TABLE `uploadspectemp` 
  DROP COLUMN `materialSampleID`,
  ADD COLUMN `materialSampleJSON` TEXT NULL AFTER `paleoJSON`;


ALTER TABLE `omoccurrences` 
  ADD COLUMN `type` VARCHAR(45) NULL AFTER `verbatimEventDate`;
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`;

#Material Sample schema developments
CREATE TABLE `ommaterialsample` (
  `matSampleID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `sampleType` VARCHAR(45) NOT NULL,
  `catalogNumber` VARCHAR(45) NULL,
  `guid` VARCHAR(150) NULL,
  `sampleCondition` VARCHAR(45) NULL,
  `disposition` VARCHAR(45) NULL,
  `preservationType` VARCHAR(45) NULL,
  `preparationDetails` VARCHAR(250) NULL,
  `preparationDate` DATE NULL,
  `preparedByUid` INT UNSIGNED NULL,
  `individualCount` VARCHAR(45) NULL,
  `sampleSize` VARCHAR(45) NULL,
  `storageLocation` VARCHAR(45) NULL,
  `remarks` VARCHAR(250) NULL,
  `dynamicFields` JSON NULL,
  `recordID` VARCHAR(45) NULL,
  `initialtimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`matSampleID`),
  INDEX `FK_ommatsample_occid_idx` (`occid` ASC),
  INDEX `FK_ommatsample_prepUid_idx` (`preparedByUid` ASC),
  CONSTRAINT `FK_ommatsample_occid` FOREIGN KEY (`occid`)   REFERENCES `omoccurrences` (`occid`)   ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_ommatsample_prepUid`   FOREIGN KEY (`preparedByUid`)   REFERENCES `users` (`uid`)   ON DELETE CASCADE  ON UPDATE CASCADE);

ALTER TABLE `ommaterialsample`
  ADD UNIQUE INDEX `UQ_ommatsample_recordID` (`recordID`);

INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsample","sampleType",1);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "tissue", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "culture strain", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "specimen", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "RNA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Protein", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Skin", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Skull", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "liver", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "sampleType";

INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsample","disposition",1);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "being processed", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "in collection", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "deaccessioned", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "consumed", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "discarded", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "missing", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "on exhibit", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "disposition";

INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsample","preservationType",1);

INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "alsever's solution", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "arsenic", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Bouin's solution", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "buffer", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "cleared", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "carbonization", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DMSO/EDTA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DESS", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "DMSO", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "desiccated", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "dry", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ethanol 95%", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ethanol 80%", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ethanol 75%", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "ethanol 70%", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "EDTA", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "sampleDesignation", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Frozen -20°C", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Frozen -80°C", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Frozen -196°C", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";
INSERT INTO ctcontrolvocabterm(cvID, term, activeStatus) SELECT cvID, "Liquid Nitrogen", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsample" AND fieldName = "preservationType";


CREATE TABLE `ommaterialsampleextended` (
  `matSampleExtendedID` INT NOT NULL AUTO_INCREMENT,
  `matSampleID` INT UNSIGNED NOT NULL,
  `fieldName` VARCHAR(45) NOT NULL,
  `fieldValue` VARCHAR(250) NOT NULL,
  `fieldUnits` VARCHAR(45) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`matSampleExtendedID`),
  INDEX `FK_matsampleextend_matSampleID_idx` (`matSampleID` ASC),
  INDEX `IX_matsampleextend_fieldName` (`fieldName` ASC),
  INDEX `IX_matsampleextend_fieldValue` (`fieldValue` ASC),
  CONSTRAINT `FK_matsampleextend_matSampleID`  FOREIGN KEY (`matSampleID`)   REFERENCES `ommaterialsample` (`matSampleID`)   ON DELETE CASCADE   ON UPDATE CASCADE);


INSERT INTO ctcontrolvocab(title,tableName,fieldName, limitToList)
  VALUES("Material Sample Type","ommaterialsampleextended","fieldName",0);

INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "concentration", "http://data.ggbn.org/schemas/ggbn/terms/concentration", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "concentrationMethod", "http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationConcentrationAndRatios", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "ratioOfAbsorbance260_230", "http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_230", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "ratioOfAbsorbance260_280", "http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_280", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "volume", "http://data.ggbn.org/schemas/ggbn/terms/volume", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "weight", "http://data.ggbn.org/schemas/ggbn/terms/weight", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "weightMethod", "http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationWeight", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "purificationMethod", "http://data.ggbn.org/schemas/ggbn/terms/purificationMethod", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "quality", "http://data.ggbn.org/schemas/ggbn/terms/quality", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "qualityRemarks", "http://data.ggbn.org/schemas/ggbn/terms/qualityRemarks", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "qualityCheckDate", "http://data.ggbn.org/schemas/ggbn/terms/qualityCheckDate", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "sieving", "http://gensc.org/ns/mixs/sieving", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "dnaHybridization", "http://data.ggbn.org/schemas/ggbn/terms/DNADNAHybridization", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "dnaMeltingPoint", "http://data.ggbn.org/schemas/ggbn/terms/DNAMeltingPoint", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "estimatedSize", "http://gensc.org/ns/mixs/estimated_size", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "poolDnaExtracts", "http://gensc.org/ns/mixs/pool_dna_extracts", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";
INSERT INTO ctcontrolvocabterm(cvID, term, resourceUrl, activeStatus) SELECT cvID, "sampleDesignation", "http://data.ggbn.org/schemas/ggbn/terms/sampleDesignation", 1 FROM ctcontrolvocab WHERE tableName = "ommaterialsampleextended" AND fieldName = "fieldName";

