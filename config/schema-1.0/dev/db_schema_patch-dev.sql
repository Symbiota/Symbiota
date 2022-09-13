INSERT IGNORE INTO schemaversion (versionnumber) values ("1.3");

ALTER TABLE `agents` 
  CHANGE COLUMN `taxonomicgroups` `taxonomicGroups` VARCHAR(900) NULL DEFAULT NULL ,
  CHANGE COLUMN `collectionsat` `collectionsAt` VARCHAR(900) NULL DEFAULT NULL ,
  CHANGE COLUMN `mbox_sha1sum` `mboxSha1Sum` CHAR(40) NULL DEFAULT NULL ;

ALTER TABLE `agents`
  ADD INDEX `IX_agents_familyname` (`familyName` ASC),
  ADD UNIQUE INDEX `UQ_agents_guid` (`guid` ASC);

ALTER TABLE `agents` 
  DROP INDEX `firstname`,
  ADD INDEX `IX_agents_firstname` (`firstName` ASC);

ALTER TABLE `agents` 
  ADD INDEX `FK_agents_preferred_recby_idx` (`preferredRecByID` ASC),
  DROP INDEX `FK_agents_preferred_recby`;

CREATE TABLE `agentoccurrencelink` (
  `agentID` BIGINT(20) NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `role` VARCHAR(45) NOT NULL DEFAULT '',
  `createdUid` INT UNSIGNED NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `dateLastModified` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`agentID`, `occid`, `role`),
  INDEX `FK_agentoccurlink_occid_idx` (`occid` ASC),
  INDEX `FK_agentoccurlink_created_idx` (`createdUid` ASC),
  INDEX `FK_agentoccurlink_modified_idx` (`modifiedUid` ASC),
  INDEX `FK_agentoccurlink_role` (`role` ASC),
  CONSTRAINT `FK_agentoccurlink_agentID`  FOREIGN KEY (`agentID`)  REFERENCES `agents` (`agentID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_created`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_modified` FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE
);

INSERT IGNORE INTO agents(familyName,firstName,middleName,startYearActive,endYearActive,notes,rating,guid)
  SELECT DISTINCT c.familyname, c.firstname, c.middlename, c.startyearactive, c.endyearactive, c.notes, c.rating, c.guid 
  FROM omcollectors c LEFT JOIN agents a ON c.guid = a.guid
  WHERE a.guid IS NULL;

INSERT IGNORE INTO agentoccurrencelink(agentID, occid, role)
  SELECT a.agentID, o.occid, "primaryCollector"
  FROM agents a INNER JOIN omcollectors c ON a.guid = c.guid
  INNER JOIN omoccurrences o ON c.recordedbyid = o.recordedbyid;

CREATE TABLE `agentdeterminationlink` (
  `agentID` BIGINT(20) NOT NULL,
  `detID` INT UNSIGNED NOT NULL,
  `role` VARCHAR(45) NOT NULL DEFAULT '',
  `createdUid` INT UNSIGNED NULL DEFAULT NULL,
  `modifiedUid` INT UNSIGNED NULL DEFAULT NULL,
  `dateLastModified` DATETIME NULL DEFAULT NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`agentID`, `detID`, `role`),
  INDEX `FK_agentdetlink_detid_idx` (`detID` ASC),
  INDEX `FK_agentdetlink_modified_idx` (`modifiedUid` ASC),
  INDEX `FK_agentdetlink_created_idx` (`createdUid` ASC),
  INDEX `IX_agentdetlink_role` (`role` ASC),
  CONSTRAINT `FK_agentdetlink_agentID`  FOREIGN KEY (`agentID`)  REFERENCES `agents` (`agentID`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_detid`  FOREIGN KEY (`detID`)  REFERENCES `omoccurdeterminations` (`detid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_modified`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_created`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE
);

ALTER TABLE `agentlinks` 
  CHANGE COLUMN `isprimarytopicof` `isPrimaryTopicOf` TINYINT(1) NOT NULL DEFAULT 1 ;

ALTER TABLE `agentnames` 
  ENGINE = InnoDB ,
  CHANGE COLUMN `agentNamesID` `agentNamesID` BIGINT(20) NOT NULL ,
  CHANGE COLUMN `agentID` `agentID` BIGINT(20) NOT NULL ,
  CHANGE COLUMN `type` `nameType` VARCHAR(32) NOT NULL DEFAULT 'Full Name' ,
  CHANGE COLUMN `name` `agentName` VARCHAR(255) NOT NULL ;

ALTER TABLE `agentnames` 
  ADD CONSTRAINT `FK_agentnames_agentID`  FOREIGN KEY (`agentID`)  REFERENCES `agents` (`agentID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `agentnames` 
  ADD INDEX `IX_agentnames_name` (`agentName` ASC),
  DROP INDEX `ft_collectorname` ;

ALTER TABLE `agentnames` 
  ADD UNIQUE INDEX `UQ_agentnames_unique` (`agentID` ASC, `nameType` ASC, `agentName` ASC),
  ADD INDEX `IX_agentnames_type` (`nameType` ASC);

ALTER TABLE `agentnames` 
  DROP INDEX `type`,
  DROP INDEX `agentid`;

ALTER TABLE `agentnumberpattern` 
  ADD INDEX `IX_agentnumberpattern_agentid` (`agentID` ASC),
  DROP INDEX `agentid`;

ALTER TABLE `agentrelations` 
  ADD INDEX `FK_agentrelations_fromagentid_idx` (`fromAgentID` ASC),
  ADD INDEX `FK_agentrelations_toagentid_idx` (`toAgentID` ASC),
  ADD INDEX `FK_agentrelations_relationship_idx` (`relationship` ASC),
  DROP INDEX `relationship`,
  DROP INDEX `toagentid`,
  DROP INDEX `fromagentid`;

ALTER TABLE `agentteams` 
  DROP FOREIGN KEY `agentteams_ibfk_1`,
  DROP FOREIGN KEY `agentteams_ibfk_2`;

ALTER TABLE `agentteams` 
  ADD INDEX `FK_agentteams_teamagentid_idx` (`teamAgentID` ASC),
  ADD INDEX `FK_agentteams_memberagentid_idx` (`memberAgentID` ASC),
  DROP INDEX `memberagentid`,
  DROP INDEX `teamagentid`;

ALTER TABLE `agentteams` 
  ADD CONSTRAINT `FK_agentteams_teamAgentID`  FOREIGN KEY (`teamAgentID`)  REFERENCES `agents` (`agentID`)  ON DELETE NO ACTION  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_agentteams_memberAgentID`  FOREIGN KEY (`memberAgentID`)  REFERENCES `agents` (`agentID`)  ON DELETE NO ACTION  ON UPDATE CASCADE; 


ALTER TABLE `fmchecklists` 
  CHANGE COLUMN `Name` `name` VARCHAR(100) NOT NULL ,
  CHANGE COLUMN `Title` `title` VARCHAR(150) NULL DEFAULT NULL ,
  CHANGE COLUMN `Locality` `locality` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `Publication` `publication` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `Abstract` `abstract` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `Authors` `authors` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `Type` `type` VARCHAR(50) NULL DEFAULT 'static' ,
  CHANGE COLUMN `dynamicsql` `dynamicSql` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `Parent` `parent` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `parentclid` `parentClid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `Notes` `notes` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `LatCentroid` `latCentroid` DOUBLE(9,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `LongCentroid` `longCentroid` DOUBLE(9,6) NULL DEFAULT NULL ,
  CHANGE COLUMN `pointradiusmeters` `pointRadiusMeters` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `footprintWKT` `footprintWkt` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `percenteffort` `percentEffort` INT(11) NULL DEFAULT NULL ,
  CHANGE COLUMN `Access` `access` VARCHAR(45) NULL DEFAULT 'private' ,
  CHANGE COLUMN `SortSequence` `sortSequence` INT(10) UNSIGNED NOT NULL DEFAULT 50 ,
  CHANGE COLUMN `DateLastModified` `dateLastModified` DATETIME NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `fmchecklists` 
  CHANGE COLUMN `CLID` `clid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `fmchecklists`
  ADD COLUMN `dynamicProperties` TEXT NULL AFTER `headerUrl`,
  ADD COLUMN `guid` VARCHAR(45) NULL AFTER `expiration`,
  ADD COLUMN `recordID` VARCHAR(45) NULL AFTER `guid`,
  ADD COLUMN `modifiedUid` INT UNSIGNED NULL AFTER `recordID`;

ALTER TABLE `fmchklstcoordinates`
  ADD COLUMN `sourceName` VARCHAR(75) NULL AFTER `decimalLongitude`,
  ADD COLUMN `sourceIdentifier` VARCHAR(45) NULL AFTER `sourceName`,
  ADD COLUMN `referenceUrl` VARCHAR(250) NULL AFTER `sourceIdentifier`,
  ADD COLUMN `dynamicProperties` TEXT NULL AFTER `notes`,
  CHANGE COLUMN `chklstcoordid` `clCoordID` INT(11) NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `decimallatitude` `decimalLatitude` DOUBLE NOT NULL ,
  CHANGE COLUMN `decimallongitude` `decimalLongitude` DOUBLE NOT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;


ALTER TABLE `ctcontrolvocab` 
  ADD COLUMN `collid` INT UNSIGNED NULL DEFAULT NULL AFTER `cvID`,
  ADD INDEX `FK_ctControlVocab_collid_idx` (`collid` ASC);

ALTER TABLE `ctcontrolvocab` 
  ADD CONSTRAINT `FK_ctControlVocab_collid`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`CollID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

ALTER TABLE `fmprojects` 
  CHANGE COLUMN `projname` `projname` VARCHAR(75) NOT NULL ;

ALTER TABLE `geographicthesaurus` 
  ADD COLUMN `geoLevel` INT NOT NULL AFTER `category`;

ALTER TABLE `geographicthesaurus` 
  DROP FOREIGN KEY `FK_geothes_parentID`;

ALTER TABLE `geographicthesaurus` 
ADD CONSTRAINT `FK_geothes_parentID`  FOREIGN KEY (`parentID`)  REFERENCES `geographicthesaurus` (`geoThesID`)  ON DELETE RESTRICT  ON UPDATE CASCADE;

ALTER TABLE `geographicthesaurus` 
  ADD UNIQUE INDEX `UQ_geothes` (`geoterm` ASC, `parentID` ASC);

#Get rid of old geographic thesaurus tables that were never used
DROP TABLE geothesmunicipality;
DROP TABLE geothescounty;
DROP TABLE geothesstateprovince;
DROP TABLE geothescountry;
DROP TABLE geothescontinent;


ALTER TABLE `glossary` 
  ADD COLUMN `plural` VARCHAR(150) NULL AFTER `term`,
  ADD COLUMN `termType` VARCHAR(45) NULL AFTER `plural`,
  ADD COLUMN `origin` VARCHAR(45) NULL AFTER `langid`,
  ADD COLUMN `notesInternal` VARCHAR(250) NULL AFTER `notes`,
  ADD INDEX `IX_gloassary_plural` (`plural` ASC);

ALTER TABLE `glossary` 
  CHANGE COLUMN `resourceurl` `resourceUrl` VARCHAR(600) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

CREATE TABLE `glossarycategory` (
  `glossCatID` INT NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(45) NULL,
  `rankID` INT NULL DEFAULT 10,
  `langID` INT NULL,
  `parentCatID` INT NULL,
  `translationCatID` INT NULL,
  `notes` VARCHAR(150) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` TIMESTAMP NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`glossCatID`),
  INDEX `FK_glossarycategory_lang_idx` (`langID` ASC),
  INDEX `IX_glossarycategory_cat` (`category` ASC),
  INDEX `FK_glossarycategory_transCatID_idx` (`translationCatID` ASC),
  INDEX `FK_glossarycategory_parentCatID_idx` (`parentCatID` ASC),
  UNIQUE INDEX `UQ_glossary_category_term` (`category` ASC, `langID` ASC, `rankid` ASC),
  CONSTRAINT `FK_glossarycategory_lang`   FOREIGN KEY (`langID`)  REFERENCES `adminlanguages` (`langid`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_transCatID`  FOREIGN KEY (`translationCatID`)  REFERENCES `glossarycategory` (`glossCatID`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_parentCatID`  FOREIGN KEY (`parentCatID`)  REFERENCES `glossarycategory` (`glossCatID`)  ON DELETE SET NULL  ON UPDATE CASCADE;
);

CREATE TABLE `glossarycategorylink` (
  `glossCatLinkID` INT NOT NULL AUTO_INCREMENT,
  `glossID` INT UNSIGNED NOT NULL,
  `glossCatID` INT NOT NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`glossCatLinkID`),
  INDEX `FK_glossCatLink_glossID_idx` (`glossID` ASC),
  INDEX `FK_glossCatLink_glossCatID_idx` (`glossCatID` ASC),
  CONSTRAINT `FK_glossCatLink_glossID`  FOREIGN KEY (`glossID`)  REFERENCES `glossary` (`glossid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_glossCatLink_glossCatID`  FOREIGN KEY (`glossCatID`)  REFERENCES `glossarycategory` (`glossCatID`)  ON DELETE CASCADE  ON UPDATE CASCADE
);


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
  ADD COLUMN `tagDescription` VARCHAR(255) NOT NULL AFTER `description_en`;

UPDATE imagetagkey
  SET tagDescription = description_en
  WHERE tagDescription = "";

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

#ALTER TABLE `omcollections` 
#  ADD COLUMN `collectionGuid` TEXT NULL AFTER `aggKeysStr`;

ALTER TABLE `omcollections` 
  ADD COLUMN `recordID` TEXT NULL AFTER `aggKeysStr`;

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
  CONSTRAINT `FK_omcollproperties_uid`   FOREIGN KEY (`modifiedUid`)   REFERENCES `users` (`uid`)   ON DELETE CASCADE   ON UPDATE CASCADE
);


CREATE TABLE `omoccuraccess` (
  `occurAccessID` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ipaddress` VARCHAR(45) NOT NULL,
  `accessType` VARCHAR(45) NOT NULL,
  `queryStr` TEXT NULL,
  `userAgent` TEXT NULL,
  `frontendGuid` VARCHAR(45) NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`occurAccessID`)
) ENGINE = MyISAM;

CREATE TABLE `omoccuraccesslink` (
  `occurAccessID` BIGINT(20) UNSIGNED NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `initialTimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`occurAccessID`, `occid`)
) ENGINE = MyISAM;

CREATE TABLE `omoccuraccesssummary` (
  `oasid` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ipaddress` VARCHAR(45) NOT NULL,
  `accessDate` DATE NOT NULL,
  `cnt` INT UNSIGNED NOT NULL,
  `accessType` VARCHAR(45) NOT NULL,
  `queryStr` TEXT NULL,
  `userAgent` TEXT NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`oasid`),
  UNIQUE INDEX `UNIQUE_occuraccess` (`ipaddress` ASC, `accessdate` ASC, `accesstype` ASC)
);

CREATE TABLE `omoccuraccesssummarylink` (
  `oasid` BIGINT(20) UNSIGNED NOT NULL,
  `occid` INT UNSIGNED NOT NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`oasid`, `occid`),
  INDEX `omoccuraccesssummarylink_occid_idx` (`occid` ASC),
  CONSTRAINT `FK_omoccuraccesssummarylink_oasid`  FOREIGN KEY (`oasid`)  REFERENCES `omoccuraccesssummary` (`oasid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccuraccesssummarylink_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE
);

DROP TABLE omoccuraccessstats;


ALTER TABLE `omoccurdatasets` 
  CHANGE COLUMN `datasetid` `datasetID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  ADD COLUMN `datasetIdentifier` VARCHAR(150) NULL AFTER `description`,
  ADD COLUMN `datasetName` VARCHAR(150) NULL AFTER `datasetID`,
  ADD COLUMN `bibliographicCitation` VARCHAR(500) NULL AFTER `datasetName`,
  CHANGE COLUMN `sortsequence` `sortSequence` INT(11) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `omoccurdeterminations` 
  DROP FOREIGN KEY `FK_omoccurdets_idby`;

ALTER TABLE `omoccurdeterminations` 
  DROP INDEX `FK_omoccurdets_idby_idx` ;

ALTER TABLE `omoccuredits` 
  CHANGE COLUMN `FieldName` `fieldName` VARCHAR(45) NOT NULL ,
  CHANGE COLUMN `FieldValueNew` `fieldValueNew` TEXT NOT NULL ,
  CHANGE COLUMN `FieldValueOld` `fieldValueOld` TEXT NOT NULL ,
  CHANGE COLUMN `ReviewStatus` `reviewStatus` INT(1) NOT NULL DEFAULT 1 COMMENT '1=Open;2=Pending;3=Closed' ,
  CHANGE COLUMN `AppliedStatus` `appliedStatus` INT(1) NOT NULL DEFAULT 0 COMMENT '0=Not Applied;1=Applied' ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `omoccuredits` 
  ADD COLUMN `isActive` INT(1) NULL DEFAULT NULL COMMENT '0 = not the value applied within the active field, 1 = valued applied within active field' AFTER `editType`,
  ADD COLUMN `reapply` INT(1) NULL COMMENT '0 = do not reapply edit; 1 = reapply edit when snapshot is refreshed, if edit isActive and snapshot value still matches old value ' AFTER `isActive`;

ALTER TABLE `omoccuredits` 
  DROP FOREIGN KEY `fk_omoccuredits_uid`;

ALTER TABLE `omoccuredits` 
  ADD CONSTRAINT `fk_omoccuredits_uid`  FOREIGN KEY (`uid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE CASCADE;

ALTER TABLE `omoccuredits` 
  ADD INDEX `IX_omoccuredits_timestamp` (`initialtimestamp` ASC);


UPDATE omoccuridentifiers SET identifiername = "" WHERE identifiername IS NULL;

ALTER TABLE `omoccuridentifiers` 
  CHANGE COLUMN `identifiername` `identifiername` VARCHAR(45) NOT NULL DEFAULT '' COMMENT 'barcode, accession number, old catalog number, NPS, etc' ;

ALTER TABLE `omoccuridentifiers` 
  ADD UNIQUE INDEX `UQ_omoccuridentifiers` (`occid` ASC, `identifiervalue` ASC, `identifiername` ASC);

ALTER TABLE `omoccuridentifiers` 
  DROP INDEX `Index_value` ;

ALTER TABLE `omoccuridentifiers` 
  ADD INDEX `IX_omoccuridentifiers_value` (`identifiervalue` ASC);


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

ALTER TABLE `omoccurrences` 
  DROP INDEX `Index_latlng`,
  ADD INDEX `IX_occurrences_lat` (`decimalLatitude` ASC),
  ADD INDEX `IX_occurrences_lng` (`decimalLongitude` ASC);

ALTER TABLE `omoccurrences` 
  DROP FOREIGN KEY `FK_omoccurrences_tid`,
  DROP FOREIGN KEY `FK_omoccurrences_uid`;

ALTER TABLE `omoccurrences` 
  CHANGE COLUMN `tidinterpreted` `tidInterpreted` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `fieldnumber` `fieldNumber` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `genericcolumn1` `genericColumn1` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `genericcolumn2` `genericColumn2` VARCHAR(100) NULL DEFAULT NULL ,
  CHANGE COLUMN `observeruid` `observerUid` INT(10) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `processingstatus` `processingStatus` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `omoccurrences` 
  ADD CONSTRAINT `FK_omoccurrences_tid`  FOREIGN KEY (`tidInterpreted`)  REFERENCES `taxa` (`tid`)  ON DELETE SET NULL  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_omoccurrences_uid`  FOREIGN KEY (`observerUid`)  REFERENCES `users` (`uid`);


#DROP TABLE IF EXISTS `portaloccurrences`;
#DROP TABLE IF EXISTS `portalpublications`;
#DROP TABLE IF EXISTS `portalindex`;

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

CREATE TABLE `portalpublications` (
  `pubid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pubTitle` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `guid` VARCHAR(45) NULL,
  `collid` int(10) unsigned NULL,
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
  UNIQUE INDEX `UQ_portalpub_guid` (`guid` ASC),
  CONSTRAINT `FK_portalpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_portalID` FOREIGN KEY (`portalID`) REFERENCES `portalindex` (`portalID`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `portaloccurrences` (
  `portalOccurrencesID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `pubid` int(10) unsigned NOT NULL,
  `remoteOccid` int(11) NULL DEFAULT NULL,
  `verification` int(11) NOT NULL DEFAULT 0,
  `refreshTimestamp` datetime NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`portalOccurrencesID`),
  KEY `FK_portalOccur_occid_idx` (`occid`),
  KEY `FK_portalOccur_pubID_idx` (`pubid`),
  UNIQUE INDEX `UQ_portalOccur_occid_pubid` (`occid` ASC, `pubid` ASC),
  CONSTRAINT `FK_portalOccur_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalOccur_pubid` FOREIGN KEY (`pubid`) REFERENCES `portalpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
);


ALTER TABLE `specprocessorprojects` 
  ADD COLUMN `customStoredProcedure` VARCHAR(45) NULL AFTER `source`,
  ADD COLUMN `createdByUid` INT UNSIGNED NULL AFTER `lastrundate`,
  ADD INDEX `FK_specprocprojects_uid_idx` (`createdByUid` ASC);

ALTER TABLE `specprocessorprojects`
  ADD CONSTRAINT `FK_specprocprojects_uid`  FOREIGN KEY (`createdByUid`)  REFERENCES `users` (`uid`)  ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `taxa` 
  CHANGE COLUMN `TID` `tid` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `RankId` `rankID` SMALLINT(5) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `SciName` `sciName` VARCHAR(250) NOT NULL ,
  CHANGE COLUMN `UnitInd1` `unitInd1` VARCHAR(1) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitName1` `unitName1` VARCHAR(50) NOT NULL ,
  CHANGE COLUMN `UnitInd2` `unitInd2` VARCHAR(1) NULL DEFAULT NULL ,
  CHANGE COLUMN `UnitName2` `unitName2` VARCHAR(50) NULL DEFAULT 't' ,
  CHANGE COLUMN `UnitName3` `unitName3` VARCHAR(35) NULL DEFAULT NULL ,
  CHANGE COLUMN `PhyloSortSequence` `phyloSortSequence` TINYINT(3) UNSIGNED NULL DEFAULT NULL ,
  CHANGE COLUMN `Source` `source` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `Notes` `notes` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `Hybrid` `hybrid` VARCHAR(50) NULL DEFAULT NULL ,
  CHANGE COLUMN `SecurityStatus` `securityStatus` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality' ;

UPDATE IGNORE taxa SET author = "" WHERE author IS NULL;

ALTER TABLE `taxa` 
  CHANGE COLUMN `Author` `author` VARCHAR(150) NOT NULL DEFAULT "";

ALTER TABLE `taxa` 
  DROP INDEX `sciname_unique` ,
  ADD UNIQUE INDEX `sciname_unique` (`SciName` ASC, `RankId` ASC);
  
ALTER TABLE `taxstatus` 
  CHANGE COLUMN `taxonomicSource` `taxonomicSource` VARCHAR(500) NULL DEFAULT NULL;

ALTER TABLE `taxalinks` 
  ADD INDEX `FK_taxaLinks_tid` (`tid` ASC),
  ADD UNIQUE INDEX `UQ_taxaLinks_tid_url` (`tid` ASC, `url` ASC);

ALTER TABLE `taxalinks` 
  DROP INDEX `Index_unique` ;

ALTER TABLE `uploadspectemp` 
  ADD COLUMN `eventTime` VARCHAR(45) NULL AFTER `verbatimEventDate`,
  CHANGE COLUMN `LatestDateCollected` `eventDate2` DATE NULL DEFAULT NULL AFTER `eventDate`;

ALTER TABLE `uploadspecparameters` 
  DROP FOREIGN KEY `FK_uploadspecparameters_coll`;

ALTER TABLE `uploadspecparameters` 
  ADD COLUMN `internalQuery` VARCHAR(250) NULL AFTER `schemaName`;

ALTER TABLE `uploadspecparameters` 
  CHANGE COLUMN `CollID` `collid` INT(10) UNSIGNED NOT NULL ,
  CHANGE COLUMN `UploadType` `uploadType` INT(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Direct; 2 = DiGIR; 3 = File' ,
  CHANGE COLUMN `Platform` `platform` VARCHAR(45) NULL DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker' ,
  CHANGE COLUMN `Code` `code` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `Path` `path` VARCHAR(500) NULL DEFAULT NULL ,
  CHANGE COLUMN `PkField` `pkField` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `Username` `username` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `Password` `password` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `SchemaName` `schemaName` VARCHAR(150) NULL DEFAULT NULL ,
  CHANGE COLUMN `QueryStr` `queryStr` TEXT NULL DEFAULT NULL ,
  CHANGE COLUMN `cleanupsp` `cleanupSP` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

ALTER TABLE `uploadspecparameters` 
  ADD CONSTRAINT `FK_uploadspecparameters_coll`  FOREIGN KEY (`collid`)  REFERENCES `omcollections` (`collID`)  ON DELETE CASCADE  ON UPDATE CASCADE;

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

# Modify the institutions table so that some fields can hold more data
# This is to allow extra content from GrSciColl/Index Herbariorum (e.g., multiple contacts)
ALTER TABLE `institutions` 
  CHANGE COLUMN `InstitutionName2` `InstitutionName2` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `Phone` `Phone` VARCHAR(100) NULL DEFAULT NULL,
  CHANGE COLUMN `Contact` `Contact` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `Email` `Email` VARCHAR(255) NULL DEFAULT NULL,
  CHANGE COLUMN `Notes` `Notes` VARCHAR(19500) NULL DEFAULT NULL;
