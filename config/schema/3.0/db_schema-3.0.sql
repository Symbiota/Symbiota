SET FOREIGN_KEY_CHECKS=0;

--
-- Table structure for table `adminconfig`
--

CREATE TABLE `adminconfig` (
  `configID` INT NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(45) NULL,
  `attributeName` VARCHAR(45) NOT NULL,
  `attributeValue` VARCHAR(1000) NOT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `notes` VARCHAR(45) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `modifiedTimestamp` DATETIME NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`configID`),
  INDEX `FK_adminConfig_uid_idx` (`modifiedUid` ASC),
  UNIQUE INDEX `UQ_adminconfig_name` (`attributeName` ASC),
  CONSTRAINT `FK_adminConfig_uid`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE RESTRICT  ON UPDATE RESTRICT
);

    
--
-- Table structure for table `adminlanguages`
--

CREATE TABLE `adminlanguages` (
  `langid` int(11) NOT NULL AUTO_INCREMENT,
  `langName` varchar(45) NOT NULL,
  `iso639_1` varchar(10) DEFAULT NULL,
  `iso639_2` varchar(10) DEFAULT NULL,
  `ISO 639-3` varchar(3) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`langid`),
  UNIQUE KEY `index_langname_unique` (`langname`)
) ENGINE=InnoDB;


--
-- Table structure for table `agentdeterminationlink`
--

CREATE TABLE `agentdeterminationlink` (
  `agentID` bigint(20) NOT NULL,
  `detID` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL DEFAULT '',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentID`,`detID`,`role`),
  KEY `FK_agentdetlink_detid_idx` (`detID`),
  KEY `FK_agentdetlink_modified_idx` (`modifiedUid`),
  KEY `FK_agentdetlink_created_idx` (`createdUid`),
  KEY `IX_agentdetlink_role` (`role`),
  CONSTRAINT `FK_agentdetlink_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_created` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_detid` FOREIGN KEY (`detID`) REFERENCES `omoccurdeterminations` (`detid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_modified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentlinks`
--

CREATE TABLE `agentlinks` (
  `agentLinksID` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentID` bigint(20) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `link` varchar(900) DEFAULT NULL,
  `isPrimaryTopicOf` tinyint(1) NOT NULL DEFAULT 1,
  `text` varchar(50) DEFAULT NULL,
  `createdUid` int(11) unsigned DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentLinksID`),
  KEY `FK_agentlinks_agentID_idx` (`agentID`),
  KEY `FK_agentlinks_modUid_idx` (`modifiedUid`),
  KEY `FK_agentlinks_createdUid_idx` (`createdUid`),
  CONSTRAINT `FK_agentlinks_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentlinks_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agentlinks_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentnames`
--

CREATE TABLE `agentnames` (
  `agentNamesID` bigint(20) NOT NULL,
  `agentID` bigint(20) NOT NULL,
  `nameType` varchar(32) NOT NULL DEFAULT 'Full Name',
  `agentName` varchar(255) NOT NULL,
  `language` varchar(6) DEFAULT 'en_us',
  `createdUid` int(11) DEFAULT NULL,
  `modifiedUid` int(11) DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentNamesID`),
  UNIQUE KEY `UQ_agentnames_unique` (`agentID`,`nameType`,`agentName`),
  KEY `IX_agentnames_name` (`agentName`),
  CONSTRAINT `FK_agentnames_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentnumberpattern`
--

CREATE TABLE `agentnumberpattern` (
  `agentNumberPatternID` bigint(20) NOT NULL,
  `agentID` bigint(20) NOT NULL,
  `numberType` varchar(50) DEFAULT 'Collector number',
  `numberPattern` varchar(255) DEFAULT NULL,
  `numberPatternDescription` varchar(900) DEFAULT NULL,
  `startYear` int(11) DEFAULT NULL,
  `endYear` int(11) DEFAULT NULL,
  `integerIncrement` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`agentNumberPatternID`),
  KEY `IX_agentnumberpattern_agentid` (`agentID`),
  CONSTRAINT `agentnumberpattern_ibfk_1` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentoccurrencelink`
--

CREATE TABLE `agentoccurrencelink` (
  `agentID` bigint(20) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL DEFAULT '',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentID`,`occid`,`role`),
  KEY `FK_agentoccurlink_occid_idx` (`occid`),
  KEY `FK_agentoccurlink_created_idx` (`createdUid`),
  KEY `FK_agentoccurlink_modified_idx` (`modifiedUid`),
  KEY `FK_agentoccurlink_role` (`role`),
  CONSTRAINT `FK_agentoccurlink_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_created` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_modified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentrelations`
--

CREATE TABLE `agentrelations` (
  `agentRelationsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromAgentID` bigint(20) NOT NULL,
  `toAgentID` bigint(20) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `notes` varchar(900) DEFAULT NULL,
  `createdUid` int(11) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentRelationsID`),
  KEY `FK_agentrelations_modUid_idx` (`modifiedUid`),
  KEY `FK_agentrelations_createUid_idx` (`createdUid`),
  KEY `FK_agentrelations_fromagentid_idx` (`fromAgentID`),
  KEY `FK_agentrelations_toagentid_idx` (`toAgentID`),
  KEY `FK_agentrelations_relationship_idx` (`relationship`),
  CONSTRAINT `FK_agentrelations_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_1` FOREIGN KEY (`fromAgentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_2` FOREIGN KEY (`toAgentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_3` FOREIGN KEY (`relationship`) REFERENCES `ctrelationshiptypes` (`relationship`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `agentID` bigint(20) NOT NULL AUTO_INCREMENT,
  `familyName` varchar(45) NOT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `middleName` varchar(45) DEFAULT NULL,
  `startYearActive` int(11) DEFAULT NULL,
  `endYearActive` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 10,
  `guid` varchar(150) DEFAULT NULL,
  `preferredRecByID` bigint(20) DEFAULT NULL,
  `biography` text DEFAULT NULL,
  `taxonomicGroups` varchar(150) DEFAULT NULL,
  `collectionsAt` varchar(150) DEFAULT NULL,
  `curated` tinyint(1) DEFAULT 0,
  `notOtherwiseSpecified` tinyint(1) DEFAULT 0,
  `type` enum('Individual','Team','Organization') DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `suffix` varchar(32) DEFAULT NULL,
  `nameString` text DEFAULT NULL,
  `mboxSha1Sum` char(40) DEFAULT NULL,
  `yearOfBirth` int(11) DEFAULT NULL,
  `yearOfBirthModifier` varchar(12) DEFAULT '',
  `yearOfDeath` int(11) DEFAULT NULL,
  `yearOfDeathModifier` varchar(12) DEFAULT '',
  `living` enum('Y','N','?') NOT NULL DEFAULT '?',
  `recordID` char(43) DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`agentID`),
  UNIQUE KEY `UQ_agents_guid` (`guid`),
  KEY `FK_agents_modUid_idx` (`modifiedUid`),
  KEY `FK_agents_createdUid_idx` (`createdUid`),
  KEY `IX_agents_familyname` (`familyName`),
  KEY `IX_agents_firstname` (`firstName`),
  KEY `FK_agents_preferred_recby_idx` (`preferredRecByID`),
  CONSTRAINT `FK_agents_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_preferred_recby` FOREIGN KEY (`preferredRecByID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `agentsfulltext`
--

CREATE TABLE `agentsfulltext` (
  `agentsFulltextID` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentID` int(11) NOT NULL,
  `biography` text DEFAULT NULL,
  `taxonomicGroups` text DEFAULT NULL,
  `collectionsAt` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `name` text DEFAULT NULL,
  PRIMARY KEY (`agentsFulltextID`),
  FULLTEXT KEY `ft_collectorbio` (`biography`,`taxonomicGroups`,`collectionsAt`,`notes`,`name`)
) ENGINE=MyISAM;


--
-- Table structure for table `agentteams`
--

CREATE TABLE `agentteams` (
  `agentTeamID` bigint(20) NOT NULL AUTO_INCREMENT,
  `teamAgentID` bigint(20) NOT NULL,
  `memberAgentID` bigint(20) NOT NULL,
  `ordinal` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentTeamID`),
  KEY `FK_agentteams_teamagentid_idx` (`teamAgentID`),
  KEY `FK_agentteams_memberagentid_idx` (`memberAgentID`),
  CONSTRAINT `FK_agentteams_memberAgentID` FOREIGN KEY (`memberAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_agentteams_teamAgentID` FOREIGN KEY (`teamAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `configpage`
--

CREATE TABLE `configpage` (
  `configPageID` int(11) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(45) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cssName` varchar(45) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'english',
  `displayMode` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`configpageid`)
) ENGINE=InnoDB;


--
-- Table structure for table `configpageattributes`
--

CREATE TABLE `configpageattributes` (
  `attributeID` int(11) NOT NULL AUTO_INCREMENT,
  `configPageID` int(11) NOT NULL,
  `objID` varchar(45) DEFAULT NULL,
  `objName` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'text, submit, div',
  `width` int(11) DEFAULT NULL,
  `top` int(11) DEFAULT NULL,
  `left` int(11) DEFAULT NULL,
  `styleStr` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attributeid`),
  KEY `FK_configpageattributes_id_idx` (`configpageid`),
  CONSTRAINT `FK_configpageattributes_id` FOREIGN KEY (`configpageid`) REFERENCES `configpage` (`configpageid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `ctcontrolvocab`
--

CREATE TABLE `ctcontrolvocab` (
  `cvID` int(11) NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned DEFAULT NULL,
  `title` varchar(45) NOT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `tableName` varchar(45) NOT NULL,
  `fieldName` varchar(45) NOT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `ontologyClass` varchar(150) DEFAULT NULL,
  `ontologyUrl` varchar(150) DEFAULT NULL,
  `limitToList` int(2) DEFAULT 0,
  `dynamicProperties` text DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cvID`),
  KEY `FK_ctControlVocab_createUid_idx` (`createdUid`),
  KEY `FK_ctControlVocab_modUid_idx` (`modifiedUid`),
  KEY `FK_ctControlVocab_collid_idx` (`collid`),
  UNIQUE INDEX `UQ_ctControlVocab` (`title` ASC, `tableName` ASC, `fieldName` ASC),
  CONSTRAINT `FK_ctControlVocab_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocab_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocab_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `ctcontrolvocabterm`
--

CREATE TABLE `ctcontrolvocabterm` (
  `cvTermID` int(11) NOT NULL AUTO_INCREMENT,
  `cvID` int(11) NOT NULL,
  `parentCvTermID` int(11) DEFAULT NULL,
  `term` varchar(45) NOT NULL,
  `termDisplay` varchar(75) DEFAULT NULL,
  `inverseRelationship` varchar(45) DEFAULT NULL,
  `collective` varchar(45) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `ontologyClass` varchar(150) DEFAULT NULL,
  `ontologyUrl` varchar(150) DEFAULT NULL,
  `activeStatus` int(11) DEFAULT 1,
  `notes` varchar(250) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cvTermID`),
  UNIQUE KEY `UQ_controlVocabTerm` (`cvID`,`term`),
  KEY `FK_ctcontrolVocabTerm_cvID_idx` (`cvID`),
  KEY `FK_ctControlVocabTerm_createUid_idx` (`createdUid`),
  KEY `FK_ctControlVocabTerm_modUid_idx` (`modifiedUid`),
  KEY `IX_controlVocabTerm_term` (`term`),
  KEY `FK_ctControlVocabTerm_cvTermID` (`parentCvTermID`),
  CONSTRAINT `FK_ctControlVocabTerm_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_cvID` FOREIGN KEY (`cvID`) REFERENCES `ctcontrolvocab` (`cvID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_cvTermID` FOREIGN KEY (`parentCvTermID`) REFERENCES `ctcontrolvocabterm` (`cvTermID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `ctnametypes`
--

CREATE TABLE `ctnametypes` (
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB;


--
-- Table structure for table `ctrelationshiptypes`
--

CREATE TABLE `ctrelationshiptypes` (
  `relationship` varchar(50) NOT NULL,
  `inverse` varchar(50) DEFAULT NULL,
  `collective` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`relationship`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmchecklists`
--

CREATE TABLE `fmchecklists` (
  `clid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `locality` varchar(500) DEFAULT NULL,
  `publication` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `authors` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'static',
  `politicalDivision` varchar(45) DEFAULT NULL,
  `dynamicSql` varchar(500) DEFAULT NULL,
  `parent` varchar(50) DEFAULT NULL,
  `parentClid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `latCentroid` double(9,6) DEFAULT NULL,
  `longCentroid` double(9,6) DEFAULT NULL,
  `pointRadiusMeters` int(10) unsigned DEFAULT NULL,
  `footprintWkt` text DEFAULT NULL,
  `percentEffort` int(11) DEFAULT NULL,
  `access` varchar(45) DEFAULT 'private',
  `cidKeyLimits` varchar(250) DEFAULT NULL,
  `defaultSettings` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT 50,
  `expiration` int(10) unsigned DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clid`),
  KEY `FK_checklists_uid` (`uid`),
  KEY `name` (`name`,`type`) USING BTREE,
  CONSTRAINT `FK_checklists_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmchklstchildren`
--

CREATE TABLE `fmchklstchildren` (
  `clid` int(10) unsigned NOT NULL,
  `clidChild` int(10) unsigned NOT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clid`,`clidchild`),
  KEY `FK_fmchklstchild_clid_idx` (`clid`),
  KEY `FK_fmchklstchild_child_idx` (`clidchild`),
  CONSTRAINT `FK_fmchklstchild_child` FOREIGN KEY (`clidchild`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmchklstchild_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `fmchklstcoordinates`
--

CREATE TABLE `fmchklstcoordinates` (
  `clCoordID` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `decimalLatitude` double NOT NULL,
  `decimalLongitude` double NOT NULL,
  `sourceName` varchar(75) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clCoordID`),
  KEY `FKchklsttaxalink` (`clid`,`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmchklstprojlink`
--

CREATE TABLE `fmchklstprojlink` (
  `pid` int(10) unsigned NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `clNameOverride` varchar(100) DEFAULT NULL,
  `mapChecklist` smallint(6) DEFAULT 1,
  `sortSequence` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pid`,`clid`),
  KEY `FK_chklst` (`clid`),
  CONSTRAINT `FK_chklstprojlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`),
  CONSTRAINT `FK_chklstprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmchklsttaxalink`
--

CREATE TABLE `fmchklsttaxalink` (
  `clTaxaID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `morphoSpecies` varchar(45) NOT NULL DEFAULT '',
  `familyOverride` varchar(50) DEFAULT NULL,
  `habitat` varchar(250) DEFAULT NULL,
  `abundance` varchar(50) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `explicitExclude` smallint(6) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `nativity` varchar(50) DEFAULT NULL COMMENT 'native, introducted',
  `endemic` varchar(45) DEFAULT NULL,
  `invasive` varchar(45) DEFAULT NULL,
  `internalNotes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`clTaxaID`),
  UNIQUE KEY `UQ_chklsttaxalink` (`clid`,`tid`,`morphoSpecies`),
  KEY `FK_chklsttaxalink_cid` (`clid`),
  KEY `FK_chklsttaxalink_tid` (`tid`),
  CONSTRAINT `FK_chklsttaxalink_cid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`),
  CONSTRAINT `FK_chklsttaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmdynamicchecklists`
--

CREATE TABLE `fmdynamicchecklists` (
  `dynclid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `details` varchar(250) DEFAULT NULL,
  `uid` varchar(45) DEFAULT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'DynamicList',
  `notes` varchar(250) DEFAULT NULL,
  `expiration` datetime NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`dynclid`)
) ENGINE=InnoDB;


--
-- Table structure for table `fmdyncltaxalink`
--

CREATE TABLE `fmdyncltaxalink` (
  `dynclid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`dynclid`,`tid`),
  KEY `FK_dyncltaxalink_taxa` (`tid`),
  CONSTRAINT `FK_dyncltaxalink_dynclid` FOREIGN KEY (`dynclid`) REFERENCES `fmdynamicchecklists` (`dynclid`) ON DELETE CASCADE,
  CONSTRAINT `FK_dyncltaxalink_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `fmprojects`
--

CREATE TABLE `fmprojects` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projName` varchar(75) NOT NULL,
  `displayName` varchar(150) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `briefDescription` varchar(300) DEFAULT NULL,
  `fullDescription` varchar(5000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `occurrenceSearch` int(10) unsigned NOT NULL DEFAULT 0,
  `isPublic` int(10) unsigned NOT NULL DEFAULT 0,
  `dynamicProperties` text DEFAULT NULL,
  `parentPid` int(10) unsigned DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT 50,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pid`),
  KEY `FK_parentpid_proj` (`parentpid`),
  CONSTRAINT `FK_parentpid_proj` FOREIGN KEY (`parentpid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


--
-- Table structure for table `fmvouchers`
--

CREATE TABLE `fmvouchers` (
  `voucherID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clTaxaID` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `editorNotes` varchar(50) DEFAULT NULL,
  `preferredImage` int(11) DEFAULT 0,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`voucherID`),
  KEY `FK_fmvouchers_occ_idx` (`occid`),
  KEY `FK_fmvouchers_tidclid_idx` (`clTaxaID`),
  UNIQUE INDEX `UQ_fmvouchers_clTaxaID_occid` (`clTaxaID` ASC, `occid` ASC),
  CONSTRAINT `FK_fmvouchers_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmvouchers_tidclid` FOREIGN KEY (`clTaxaID`) REFERENCES `fmchklsttaxalink` (`clTaxaID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `geographicpolygon`
--

CREATE TABLE `geographicpolygon` (
  `geoThesID` int(11) NOT NULL,
  `footprintPolygon` polygon NOT NULL,
  `footprintWKT` longtext DEFAULT NULL,
  `geoJSON` longtext DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoThesID`),
  SPATIAL KEY `IX_geopoly_polygon` (`footprintPolygon`)
) ENGINE=MyISAM;


--
-- Table structure for table `geographicthesaurus`
--

CREATE TABLE `geographicthesaurus` (
  `geoThesID` int(11) NOT NULL AUTO_INCREMENT,
  `geoTerm` varchar(100) DEFAULT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `iso2` varchar(45) DEFAULT NULL,
  `iso3` varchar(45) DEFAULT NULL,
  `numCode` int(11) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `geoLevel` int(11) NOT NULL,
  `termStatus` int(11) DEFAULT NULL,
  `acceptedID` int(11) DEFAULT NULL,
  `parentID` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProps` text DEFAULT NULL,
  `footprintWKT` text DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoThesID`),
  UNIQUE KEY `UQ_geothes` (`geoterm`,`parentID`),
  KEY `IX_geothes_termname` (`geoterm`),
  KEY `IX_geothes_abbreviation` (`abbreviation`),
  KEY `IX_geothes_iso2` (`iso2`),
  KEY `IX_geothes_iso3` (`iso3`),
  KEY `FK_geothes_acceptedID_idx` (`acceptedID`),
  KEY `FK_geothes_parentID_idx` (`parentID`),
  CONSTRAINT `FK_geothes_acceptedID` FOREIGN KEY (`acceptedID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_geothes_parentID` FOREIGN KEY (`parentID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `glossary`
--

CREATE TABLE `glossary` (
  `glossID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(150) NOT NULL,
  `plural` varchar(150) DEFAULT NULL,
  `termType` varchar(45) DEFAULT NULL,
  `definition` varchar(2000) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'English',
  `langid` int(10) unsigned DEFAULT NULL,
  `origin` varchar(45) DEFAULT NULL,
  `source` varchar(1000) DEFAULT NULL,
  `translator` varchar(250) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `notesInternal` varchar(250) DEFAULT NULL,
  `resourceUrl` varchar(600) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`glossid`),
  KEY `Index_term` (`term`),
  KEY `Index_glossary_lang` (`language`),
  KEY `FK_glossary_uid_idx` (`uid`),
  KEY `IX_gloassary_plural` (`plural`),
  CONSTRAINT `FK_glossary_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB;


--
-- Table structure for table `glossarycategory`
--

CREATE TABLE `glossarycategory` (
  `glossCatID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) DEFAULT NULL,
  `rankID` int(11) DEFAULT 10,
  `langID` int(11) DEFAULT NULL,
  `parentCatID` int(11) DEFAULT NULL,
  `translationCatID` int(11) DEFAULT NULL,
  `notes` varchar(150) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`glossCatID`),
  UNIQUE KEY `UQ_glossary_category_term` (`category`,`langID`,`rankID`),
  KEY `FK_glossarycategory_lang_idx` (`langID`),
  KEY `IX_glossarycategory_cat` (`category`),
  KEY `FK_glossarycategory_transCatID_idx` (`translationCatID`),
  KEY `FK_glossarycategory_parentCatID_idx` (`parentCatID`),
  CONSTRAINT `FK_glossarycategory_lang` FOREIGN KEY (`langID`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_parentCatID` FOREIGN KEY (`parentCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_transCatID` FOREIGN KEY (`translationCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `glossarycategorylink`
--

CREATE TABLE `glossarycategorylink` (
  `glossCatLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `glossID` int(10) unsigned NOT NULL,
  `glossCatID` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`glossCatLinkID`),
  KEY `FK_glossCatLink_glossID_idx` (`glossID`),
  KEY `FK_glossCatLink_glossCatID_idx` (`glossCatID`),
  CONSTRAINT `FK_glossCatLink_glossCatID` FOREIGN KEY (`glossCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossCatLink_glossID` FOREIGN KEY (`glossID`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `glossaryimages`
--

CREATE TABLE `glossaryimages` (
  `glimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `glossID` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailUrl` varchar(255) DEFAULT NULL,
  `structures` varchar(150) DEFAULT NULL,
  `sortSequence` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `createdBy` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`glimgid`),
  KEY `FK_glossaryimages_gloss` (`glossid`),
  KEY `FK_glossaryimages_uid_idx` (`uid`),
  CONSTRAINT `FK_glossaryimages_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossaryimages_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB;


--
-- Table structure for table `glossarysources`
--

CREATE TABLE `glossarysources` (
  `tid` int(10) unsigned NOT NULL,
  `contributorTerm` varchar(1000) DEFAULT NULL,
  `contributorImage` varchar(1000) DEFAULT NULL,
  `translator` varchar(1000) DEFAULT NULL,
  `additionalSources` varchar(1000) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tid`),
  CONSTRAINT `FK_glossarysources_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `glossarytaxalink`
--

CREATE TABLE `glossarytaxalink` (
  `glossID` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`glossid`,`tid`),
  KEY `glossarytaxalink_ibfk_1` (`tid`),
  CONSTRAINT `FK_glossarytaxa_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytaxa_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `glossarytermlink`
--

CREATE TABLE `glossarytermlink` (
  `gltlinkid` int(10) NOT NULL AUTO_INCREMENT,
  `glossGrpID` int(10) unsigned NOT NULL,
  `glossID` int(10) unsigned NOT NULL,
  `relationshipType` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`gltlinkid`),
  UNIQUE KEY `Unique_termkeys` (`glossgrpid`,`glossid`),
  KEY `glossarytermlink_ibfk_1` (`glossid`),
  CONSTRAINT `FK_glossarytermlink_glossgrpid` FOREIGN KEY (`glossgrpid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytermlink_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `igsnverification`
--

CREATE TABLE `igsnverification` (
  `igsn` varchar(15) NOT NULL,
  `occidInPortal` int(10) unsigned DEFAULT NULL,
  `occidInSesar` int(10) unsigned DEFAULT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `syncStatus` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  KEY `FK_igsn_occid_idx` (`occidInPortal`),
  KEY `INDEX_igsn` (`igsn`),
  CONSTRAINT `FK_igsn_occid` FOREIGN KEY (`occidInPortal`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `imagekeywords`
--

CREATE TABLE `imagekeywords` (
  `imgKeywordID` int(11) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyword` varchar(45) NOT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgkeywordid`),
  KEY `FK_imagekeywords_imgid_idx` (`imgid`),
  KEY `FK_imagekeyword_uid_idx` (`uidassignedby`),
  KEY `INDEX_imagekeyword` (`keyword`),
  CONSTRAINT `FK_imagekeyword_uid` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_imagekeywords_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `imgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumbnailUrl` varchar(255) DEFAULT NULL,
  `originalUrl` varchar(255) DEFAULT NULL,
  `archiveUrl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographerUid` int(10) unsigned DEFAULT NULL,
  `imageType` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `referenceUrl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `accessRights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(350) DEFAULT NULL,
  `anatomy` varchar(100) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `hashFunction` varchar(45) DEFAULT NULL,
  `hashValue` varchar(45) DEFAULT NULL,
  `mediaMD5` varchar(45) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `defaultDisplay` int(11) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT 50,
  `sortOccurrence` int(11) DEFAULT 5,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgid`) USING BTREE,
  KEY `Index_tid` (`tid`),
  KEY `FK_images_occ` (`occid`),
  KEY `FK_photographeruid` (`photographerUid`),
  KEY `Index_images_datelastmod` (`initialTimestamp`),
  KEY `IX_images_recordID` (`recordID`),
  CONSTRAINT `FK_images_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
  CONSTRAINT `FK_photographeruid` FOREIGN KEY (`photographerUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxaimagestid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `imagetag`
--

CREATE TABLE `imagetag` (
  `imagetagid` bigint(20) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyValue` varchar(30) NOT NULL,
  `imageBoundingBox` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imagetagid`),
  UNIQUE KEY `imgid` (`imgid`,`keyvalue`),
  KEY `keyvalue` (`keyvalue`),
  KEY `FK_imagetag_imgid_idx` (`imgid`),
  CONSTRAINT `FK_imagetag_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imagetag_tagkey` FOREIGN KEY (`keyvalue`) REFERENCES `imagetagkey` (`tagkey`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `imagetaggroup`
--

CREATE TABLE `imagetaggroup` (
  `imgTagGroupID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(45) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `audubonCoreTarget` varchar(45) DEFAULT NULL,
  `controlType` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgTagGroupID`),
  KEY `IX_imagetaggroup` (`groupName`)
) ENGINE=InnoDB;


--
-- Table structure for table `imagetagkey`
--

CREATE TABLE `imagetagkey` (
  `tagkey` varchar(30) NOT NULL,
  `imgTagGroupID` int(11) DEFAULT NULL,
  `shortlabel` varchar(30) NOT NULL,
  `description_en` varchar(255) NOT NULL,
  `tagDescription` varchar(255) NOT NULL,
  `resourceLink` varchar(250) DEFAULT NULL,
  `audubonCoreTarget` varchar(45) DEFAULT NULL,
  `sortOrder` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tagkey`),
  KEY `sortorder` (`sortorder`),
  KEY `FK_imageTagKey_imgTagGroupID_idx` (`imgTagGroupID`),
  CONSTRAINT `FK_imageTagKey_imgTagGroupID` FOREIGN KEY (`imgTagGroupID`) REFERENCES `imagetaggroup` (`imgTagGroupID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `iid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institutionID` varchar(45) DEFAULT NULL,
  `institutionCode` varchar(45) NOT NULL,
  `institutionName` varchar(150) NOT NULL,
  `institutionName2` varchar(255) DEFAULT NULL,
  `address1` varchar(150) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `stateProvince` varchar(45) DEFAULT NULL,
  `postalCode` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`iid`),
  KEY `FK_inst_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_inst_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


--
-- Table structure for table `kmcharacterlang`
--

CREATE TABLE `kmcharacterlang` (
  `cid` int(10) unsigned NOT NULL,
  `charName` varchar(150) NOT NULL,
  `language` varchar(45) DEFAULT NULL,
  `langid` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `helpUrl` varchar(500) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cid`,`langid`) USING BTREE,
  KEY `FK_charlang_lang_idx` (`langid`),
  CONSTRAINT `FK_characterlang_1` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_charlang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


--
-- Table structure for table `kmcharacters`
--

CREATE TABLE `kmcharacters` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `charname` varchar(150) NOT NULL,
  `chartype` varchar(2) NOT NULL DEFAULT 'UM',
  `defaultlang` varchar(45) NOT NULL DEFAULT 'English',
  `difficultyrank` smallint(5) unsigned NOT NULL DEFAULT 1,
  `hid` int(10) unsigned DEFAULT NULL,
  `units` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `glossid` int(10) unsigned DEFAULT NULL,
  `helpurl` varchar(500) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `activationCode` int(11) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `enteredby` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cid`),
  KEY `Index_charname` (`charname`),
  KEY `Index_sort` (`sortsequence`),
  KEY `FK_charheading_idx` (`hid`),
  KEY `FK_kmchar_glossary_idx` (`glossid`),
  CONSTRAINT `FK_charheading` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_kmchar_glossary` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmchardependance`
--

CREATE TABLE `kmchardependance` (
  `CID` int(10) unsigned NOT NULL,
  `CIDDependance` int(10) unsigned NOT NULL,
  `CSDependance` varchar(16) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CSDependance`,`CIDDependance`,`CID`) USING BTREE,
  KEY `FK_chardependance_cid_idx` (`CID`),
  KEY `FK_chardependance_cs_idx` (`CIDDependance`,`CSDependance`),
  CONSTRAINT `FK_chardependance_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_chardependance_cs` FOREIGN KEY (`CIDDependance`, `CSDependance`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmcharheading`
--

CREATE TABLE `kmcharheading` (
  `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `headingname` varchar(255) NOT NULL,
  `language` varchar(45) DEFAULT 'English',
  `langid` int(11) NOT NULL,
  `notes` longtext DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`hid`,`langid`) USING BTREE,
  UNIQUE KEY `unique_kmcharheading` (`headingname`,`langid`),
  KEY `HeadingName` (`headingname`) USING BTREE,
  KEY `FK_kmcharheading_lang_idx` (`langid`),
  CONSTRAINT `FK_kmcharheading_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
) ENGINE=InnoDB;


--
-- Table structure for table `kmcharheadinglang`
--

CREATE TABLE `kmcharheadinglang` (
  `hid` int(10) unsigned NOT NULL,
  `langid` int(11) NOT NULL,
  `headingname` varchar(100) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`hid`,`langid`),
  KEY `FK_kmcharheadinglang_langid` (`langid`),
  CONSTRAINT `FK_kmcharheadinglang_hid` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcharheadinglang_langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmchartaxalink`
--

CREATE TABLE `kmchartaxalink` (
  `CID` int(10) unsigned NOT NULL DEFAULT 0,
  `TID` int(10) unsigned NOT NULL DEFAULT 0,
  `Status` varchar(50) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `Relation` varchar(45) NOT NULL DEFAULT 'include',
  `EditabilityInherited` bit(1) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`CID`,`TID`),
  KEY `FK_CharTaxaLink-TID` (`TID`),
  CONSTRAINT `FK_chartaxalink_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_chartaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmcs`
--

CREATE TABLE `kmcs` (
  `cid` int(10) unsigned NOT NULL DEFAULT 0,
  `cs` varchar(16) NOT NULL,
  `CharStateName` varchar(255) DEFAULT NULL,
  `Implicit` tinyint(1) NOT NULL DEFAULT 0,
  `Notes` longtext DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `IllustrationUrl` varchar(250) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `glossid` int(10) unsigned DEFAULT NULL,
  `StateID` int(10) unsigned DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `EnteredBy` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cs`,`cid`),
  KEY `FK_cs_chars` (`cid`),
  KEY `FK_kmcs_glossid_idx` (`glossid`),
  CONSTRAINT `FK_cs_chars` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcs_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmcsimages`
--

CREATE TABLE `kmcsimages` (
  `csimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `url` varchar(255) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` varchar(45) NOT NULL DEFAULT '50',
  `username` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`csimgid`),
  KEY `FK_kscsimages_kscs_idx` (`cid`,`cs`),
  CONSTRAINT `FK_kscsimages_kscs` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `kmcslang`
--

CREATE TABLE `kmcslang` (
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `charstatename` varchar(150) NOT NULL,
  `language` varchar(45) NOT NULL,
  `langid` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `intialtimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cid`,`cs`,`langid`),
  KEY `FK_cslang_lang_idx` (`langid`),
  CONSTRAINT `FK_cslang_1` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_cslang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


--
-- Table structure for table `kmdescr`
--

CREATE TABLE `kmdescr` (
  `TID` int(10) unsigned NOT NULL DEFAULT 0,
  `CID` int(10) unsigned NOT NULL DEFAULT 0,
  `Modifier` varchar(255) DEFAULT NULL,
  `CS` varchar(16) NOT NULL,
  `X` double(15,5) DEFAULT NULL,
  `TXT` longtext DEFAULT NULL,
  `PseudoTrait` int(5) unsigned DEFAULT 0,
  `Frequency` int(5) unsigned NOT NULL DEFAULT 5 COMMENT 'Frequency of occurrence; 1 = rare... 5 = common',
  `Inherited` varchar(50) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Seq` int(10) DEFAULT NULL,
  `Notes` longtext DEFAULT NULL,
  `DateEntered` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`TID`,`CID`,`CS`),
  KEY `CSDescr` (`CID`,`CS`),
  CONSTRAINT `FK_descr_cs` FOREIGN KEY (`CID`, `CS`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_descr_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omcollcategories`
--

CREATE TABLE `omcollcategories` (
  `ccpk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(75) NOT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `acronym` varchar(45) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `inclusive` int(2) DEFAULT 1,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ccpk`) USING BTREE
) ENGINE=InnoDB;


--
-- Table structure for table `omcollcatlink`
--

CREATE TABLE `omcollcatlink` (
  `ccpk` int(10) unsigned NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `isPrimary` tinyint(1) DEFAULT 1,
  `sortsequence` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ccpk`,`collid`),
  KEY `FK_collcatlink_coll` (`collid`),
  CONSTRAINT `FK_collcatlink_cat` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_collcatlink_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omcollections`
--

CREATE TABLE `omcollections` (
  `collID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institutionCode` varchar(45) NOT NULL,
  `collectionCode` varchar(45) DEFAULT NULL,
  `collectionName` varchar(150) NOT NULL,
  `collectionID` varchar(100) DEFAULT NULL,
  `datasetID` varchar(250) DEFAULT NULL,
  `datasetName` varchar(100) DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `fullDescription` varchar(2000) DEFAULT NULL,
  `homepage` varchar(250) DEFAULT NULL,
  `resourceJson` longtext DEFAULT NULL CHECK (json_valid(`resourceJson`)),
  `individualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(250) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `contactJson` longtext DEFAULT NULL CHECK (json_valid(`contactJson`)),
  `latitudeDecimal` double(8,6) DEFAULT NULL,
  `longitudeDecimal` double(9,6) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `collType` varchar(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations',
  `managementType` varchar(45) DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data',
  `publicEdits` int(1) unsigned NOT NULL DEFAULT 1,
  `collectionGuid` varchar(45) DEFAULT NULL,
  `securityKey` varchar(45) DEFAULT NULL,
  `guidTarget` varchar(45) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageTerm` varchar(250) DEFAULT NULL,
  `publishToGbif` int(11) DEFAULT NULL,
  `publishToIdigbio` int(11) DEFAULT NULL,
  `aggKeysStr` varchar(1000) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `dwcTermJson` text DEFAULT NULL,
  `dwcaUrl` varchar(250) DEFAULT NULL,
  `bibliographicCitation` varchar(1000) DEFAULT NULL,
  `accessRights` varchar(1000) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `sortSeq` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`collID`) USING BTREE,
  UNIQUE KEY `Index_inst` (`institutionCode`,`collectionCode`),
  KEY `FK_collid_iid_idx` (`iid`),
  CONSTRAINT `FK_collid_iid` FOREIGN KEY (`iid`) REFERENCES `institutions` (`iid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omcollectionstats`
--

CREATE TABLE `omcollectionstats` (
  `collid` int(10) unsigned NOT NULL,
  `recordcnt` int(10) unsigned NOT NULL DEFAULT 0,
  `georefcnt` int(10) unsigned DEFAULT NULL,
  `familycnt` int(10) unsigned DEFAULT NULL,
  `genuscnt` int(10) unsigned DEFAULT NULL,
  `speciescnt` int(10) unsigned DEFAULT NULL,
  `uploaddate` datetime DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `uploadedby` varchar(45) DEFAULT NULL,
  `dynamicProperties` longtext DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`collid`),
  CONSTRAINT `FK_collectionstats_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB;


--
-- Table structure for table `omcollproperties`
--

CREATE TABLE `omcollproperties` (
  `collPropID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `propCategory` varchar(45) NOT NULL,
  `propTitle` varchar(45) NOT NULL,
  `propJson` longtext DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`collPropID`),
  KEY `FK_omcollproperties_collid_idx` (`collid`),
  KEY `FK_omcollproperties_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_omcollproperties_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcollproperties_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omcrowdsourcecentral`
--

CREATE TABLE `omcrowdsourcecentral` (
  `omcsid` int(11) NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `instructions` text DEFAULT NULL,
  `trainingurl` varchar(500) DEFAULT NULL,
  `editorlevel` int(11) NOT NULL DEFAULT 0 COMMENT '0=public, 1=public limited, 2=private',
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`omcsid`),
  UNIQUE KEY `Index_omcrowdsourcecentral_collid` (`collid`),
  KEY `FK_omcrowdsourcecentral_collid` (`collid`),
  CONSTRAINT `FK_omcrowdsourcecentral_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB;


--
-- Table structure for table `omcrowdsourceproject`
--

CREATE TABLE `omcrowdsourceproject` (
  `csProjID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `trainingurl` varchar(250) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `criteria` varchar(1500) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`csProjID`),
  KEY `FK_croudsourceproj_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_croudsourceproj_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omcrowdsourcequeue`
--

CREATE TABLE `omcrowdsourcequeue` (
  `idomcrowdsourcequeue` int(11) NOT NULL AUTO_INCREMENT,
  `omcsid` int(11) NOT NULL,
  `csProjID` int(11) DEFAULT NULL,
  `occid` int(10) unsigned NOT NULL,
  `reviewstatus` int(11) NOT NULL DEFAULT 0 COMMENT '0=open,5=pending review, 10=closed',
  `uidprocessor` int(10) unsigned DEFAULT NULL,
  `points` int(11) DEFAULT NULL COMMENT '0=fail, 1=minor edits, 2=no edits <default>, 3=excelled',
  `isvolunteer` int(2) NOT NULL DEFAULT 1,
  `dateProcessed` datetime DEFAULT NULL,
  `dateReviewed` datetime DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idomcrowdsourcequeue`),
  UNIQUE KEY `Index_omcrowdsource_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_uid` (`uidprocessor`),
  KEY `FK_omcrowdsourcequeue_csProjID_idx` (`csProjID`),
  CONSTRAINT `FK_omcrowdsourcequeue_csProjID` FOREIGN KEY (`csProjID`) REFERENCES `omcrowdsourceproject` (`csProjID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_uid` FOREIGN KEY (`uidprocessor`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omexsiccatinumbers`
--

CREATE TABLE `omexsiccatinumbers` (
  `omenid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exsnumber` varchar(45) NOT NULL,
  `ometid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`omenid`),
  UNIQUE KEY `Index_omexsiccatinumbers_unique` (`exsnumber`,`ometid`),
  KEY `FK_exsiccatiTitleNumber` (`ometid`),
  CONSTRAINT `FK_exsiccatiTitleNumber` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`)
) ENGINE=InnoDB;


--
-- Table structure for table `omexsiccatiocclink`
--

CREATE TABLE `omexsiccatiocclink` (
  `omenid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT 50,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`omenid`,`occid`),
  UNIQUE KEY `UniqueOmexsiccatiOccLink` (`occid`),
  KEY `FKExsiccatiNumOccLink1` (`omenid`),
  KEY `FKExsiccatiNumOccLink2` (`occid`),
  CONSTRAINT `FKExsiccatiNumOccLink1` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE CASCADE,
  CONSTRAINT `FKExsiccatiNumOccLink2` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omexsiccatititles`
--

CREATE TABLE `omexsiccatititles` (
  `ometid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `abbreviation` varchar(100) DEFAULT NULL,
  `editor` varchar(150) DEFAULT NULL,
  `exsrange` varchar(45) DEFAULT NULL,
  `startdate` varchar(45) DEFAULT NULL,
  `enddate` varchar(45) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `lasteditedby` varchar(45) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ometid`),
  KEY `index_exsiccatiTitle` (`title`)
) ENGINE=InnoDB;


--
-- Table structure for table `ommaterialsample`
--

CREATE TABLE `ommaterialsample` (
  `matSampleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `sampleType` varchar(45) NOT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `guid` varchar(150) DEFAULT NULL,
  `sampleCondition` varchar(45) DEFAULT NULL,
  `disposition` varchar(45) DEFAULT NULL,
  `preservationType` varchar(45) DEFAULT NULL,
  `preparationDetails` varchar(250) DEFAULT NULL,
  `preparationDate` date DEFAULT NULL,
  `preparedByUid` int(10) unsigned DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `sampleSize` varchar(45) DEFAULT NULL,
  `storageLocation` varchar(45) DEFAULT NULL,
  `remarks` varchar(250) DEFAULT NULL,
  `dynamicFields` longtext DEFAULT NULL CHECK (json_valid(`dynamicFields`)),
  `recordID` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`matSampleID`),
  UNIQUE KEY `UQ_ommatsample_recordID` (`recordID`),
  UNIQUE KEY `UQ_ommatsample_catNum` (`occid`,`catalogNumber`),
  UNIQUE KEY `UQ_ommatsample_guid` (`occid`,`guid`),
  KEY `FK_ommatsample_occid_idx` (`occid`),
  KEY `FK_ommatsample_prepUid_idx` (`preparedByUid`),
  CONSTRAINT `FK_ommatsample_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ommatsample_prepUid` FOREIGN KEY (`preparedByUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `ommaterialsampleextended`
--

CREATE TABLE `ommaterialsampleextended` (
  `matSampleExtendedID` int(11) NOT NULL AUTO_INCREMENT,
  `matSampleID` int(10) unsigned NOT NULL,
  `fieldName` varchar(45) NOT NULL,
  `fieldValue` varchar(250) NOT NULL,
  `fieldUnits` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`matSampleExtendedID`),
  KEY `FK_matsampleextend_matSampleID_idx` (`matSampleID`),
  KEY `IX_matsampleextend_fieldName` (`fieldName`),
  KEY `IX_matsampleextend_fieldValue` (`fieldValue`),
  CONSTRAINT `FK_matsampleextend_matSampleID` FOREIGN KEY (`matSampleID`) REFERENCES `ommaterialsample` (`matSampleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccuraccess`
--

CREATE TABLE `omoccuraccess` (
  `occurAccessID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(45) NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `queryStr` text DEFAULT NULL,
  `userAgent` text DEFAULT NULL,
  `frontendGuid` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occurAccessID`)
) ENGINE=MyISAM;


--
-- Table structure for table `omoccuraccesslink`
--

CREATE TABLE `omoccuraccesslink` (
  `occurAccessID` bigint(20) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occurAccessID`,`occid`)
) ENGINE=MyISAM;


--
-- Table structure for table `omoccuraccesssummary`
--

CREATE TABLE `omoccuraccesssummary` (
  `oasid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(45) NOT NULL,
  `accessDate` date NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `queryStr` text DEFAULT NULL,
  `userAgent` text DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`oasid`),
  UNIQUE KEY `UNIQUE_occuraccess` (`ipaddress`,`accessDate`,`accessType`)
) ENGINE=InnoDB;


--
-- Table structure for table `omoccuraccesssummarylink`
--

CREATE TABLE `omoccuraccesssummarylink` (
  `oasid` bigint(20) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`oasid`,`occid`),
  KEY `omoccuraccesssummarylink_occid_idx` (`occid`),
  CONSTRAINT `FK_omoccuraccesssummarylink_oasid` FOREIGN KEY (`oasid`) REFERENCES `omoccuraccesssummary` (`oasid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccuraccesssummarylink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurarchive`
--

CREATE TABLE `omoccurarchive` (
  `archiveID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `archiveObj` text NOT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `occurrenceID` varchar(255) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `archiveReason` varchar(45) DEFAULT NULL,
  `remarks` varchar(150) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`archiveID`),
  UNIQUE KEY `UQ_occurarchive_occid` (`occid`),
  KEY `IX_occurarchive_catnum` (`catalogNumber`),
  KEY `IX_occurarchive_occurrenceID` (`occurrenceID`),
  KEY `IX_occurarchive_recordID` (`recordID`),
  KEY `FK_occurarchive_uid_idx` (`createdUid`),
  CONSTRAINT `FK_occurarchive_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurassociations`
--

CREATE TABLE `omoccurassociations` (
  `associd` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `occidAssociate` int(10) unsigned DEFAULT NULL,
  `relationship` varchar(150) NOT NULL COMMENT 'dwc:relationshipOfResource',
  `relationshipID` varchar(45) DEFAULT NULL COMMENT 'dwc:relationshipOfResourceID (e.g. ontology link)',
  `subType` varchar(45) DEFAULT NULL,
  `identifier` varchar(250) DEFAULT NULL COMMENT 'dwc:relatedResourceID (object identifier)',
  `basisOfRecord` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(250) DEFAULT NULL COMMENT 'link to resource',
  `verbatimSciname` varchar(250) DEFAULT NULL,
  `tid` int(11) unsigned DEFAULT NULL,
  `locationOnHost` varchar(250) DEFAULT NULL,
  `conditionOfAssociate` varchar(250) DEFAULT NULL,
  `establishedDate` datetime DEFAULT NULL COMMENT 'dwc:relationshipEstablishedDate',
  `imageMapJSON` text DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL COMMENT 'dwc:relationshipRemarks',
  `accordingTo` varchar(45) DEFAULT NULL COMMENT 'dwc:relationshipAccordingTo (verbatim text)',
  `sourceIdentifier` varchar(45) DEFAULT NULL COMMENT 'dwc:resourceRelationshipID, if association was defined externally ',
  `recordID` varchar(45) DEFAULT NULL COMMENT 'dwc:resourceRelationshipID, if association was defined internally ',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`associd`),
  UNIQUE KEY `UQ_omoccurassoc_occid` (`occid`,`occidAssociate`,`relationship`),
  UNIQUE KEY `UQ_omoccurassoc_external` (`occid`,`relationship`,`resourceUrl`),
  UNIQUE KEY `UQ_omoccurassoc_sciname` (`occid`,`verbatimSciname`),
  KEY `omossococcur_occid_idx` (`occid`),
  KEY `omossococcur_occidassoc_idx` (`occidAssociate`),
  KEY `FK_occurassoc_tid_idx` (`tid`),
  KEY `FK_occurassoc_uidmodified_idx` (`modifiedUid`),
  KEY `FK_occurassoc_uidcreated_idx` (`createdUid`),
  KEY `INDEX_verbatimSciname` (`verbatimSciname`),
  CONSTRAINT `FK_occurassoc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_occidassoc` FOREIGN KEY (`occidAssociate`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurcomments`
--

CREATE TABLE `omoccurcomments` (
  `comid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `reviewstatus` int(10) unsigned NOT NULL DEFAULT 0,
  `parentcomid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`comid`),
  KEY `fk_omoccurcomments_occid` (`occid`),
  KEY `fk_omoccurcomments_uid` (`uid`),
  CONSTRAINT `fk_omoccurcomments_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccurcomments_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurdatasetlink`
--

CREATE TABLE `omoccurdatasetlink` (
  `occid` int(10) unsigned NOT NULL,
  `datasetid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occid`,`datasetid`),
  KEY `FK_omoccurdatasetlink_datasetid` (`datasetid`),
  KEY `FK_omoccurdatasetlink_occid` (`occid`),
  CONSTRAINT `FK_omoccurdatasetlink_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasetlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurdatasets`
--

CREATE TABLE `omoccurdatasets` (
  `datasetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datasetName` varchar(150) DEFAULT NULL,
  `bibliographicCitation` varchar(500) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `isPublic` int(11) DEFAULT NULL,
  `parentDatasetID` int(10) unsigned DEFAULT NULL,
  `includeInSearch` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `datasetIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `sortSequence` int(11) DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`datasetID`),
  KEY `FK_omoccurdatasets_uid_idx` (`uid`),
  KEY `FK_omcollections_collid_idx` (`collid`),
  KEY `FK_omoccurdatasets_parent_idx` (`parentDatasetID`),
  CONSTRAINT `FK_omcollections_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_parent` FOREIGN KEY (`parentDatasetID`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurdeterminations`
--

CREATE TABLE `omoccurdeterminations` (
  `detid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiedBy` varchar(255) NOT NULL DEFAULT '',
  `identifiedByAgentID` bigint(20) DEFAULT NULL,
  `identifiedByID` varchar(45) DEFAULT NULL,
  `dateIdentified` varchar(45) NOT NULL DEFAULT '',
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `higherClassification` varchar(150) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) NOT NULL,
  `verbatimIdentification` varchar(250) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `tidInterpreted` int(10) unsigned DEFAULT NULL,
  `identificationQualifier` varchar(255) DEFAULT NULL,
  `genus` varchar(45) DEFAULT NULL,
  `specificEpithet` varchar(45) DEFAULT NULL,
  `verbatimTaxonRank` varchar(45) DEFAULT NULL,
  `taxonRank` varchar(45) DEFAULT NULL,
  `infraSpecificEpithet` varchar(45) DEFAULT NULL,
  `isCurrent` int(2) DEFAULT 0,
  `printQueue` int(2) DEFAULT 0,
  `appliedStatus` int(2) DEFAULT 1,
  `securityStatus` int(11) NOT NULL DEFAULT 0,
  `securityStatusReason` varchar(100) DEFAULT NULL,
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(2000) DEFAULT NULL,
  `identificationRemarks` varchar(2000) DEFAULT NULL,
  `taxonRemarks` varchar(2000) DEFAULT NULL,
  `identificationVerificationStatus` varchar(45) DEFAULT NULL,
  `taxonConceptID` varchar(45) DEFAULT NULL,
  `identificationID` varchar(45) DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT 10,
  `recordID` varchar(45) DEFAULT NULL,
  `enteredByUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`detid`),
  UNIQUE KEY `UQ_omoccurdets_unique` (`occid`,`dateIdentified`,`identifiedBy`,`sciname`),
  KEY `FK_omoccurdets_tid` (`tidInterpreted`),
  KEY `IX_omoccurdets_dateIdInterpreted` (`dateIdentifiedInterpreted`),
  KEY `IX_omoccurdets_sciname` (`sciname`),
  KEY `IX_omoccurdets_family` (`family`),
  KEY `IX_omoccurdets_isCurrent` (`isCurrent`),
  KEY `FK_omoccurdets_agentID_idx` (`identifiedByAgentID`),
  KEY `FK_omoccurdets_uid_idx` (`enteredByUid`),
  KEY `IX_omoccurdets_recordID` (`recordID`),
  KEY `FK_omoccurdets_dateModified` (`dateLastModified`),
  KEY `FK_omoccurdets_initialTimestamp` (`initialTimestamp`),
  CONSTRAINT `FK_omoccurdets_agentID` FOREIGN KEY (`identifiedByAgentID`) REFERENCES `agents` (`agentID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_tid` FOREIGN KEY (`tidInterpreted`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_omoccurdets_uid` FOREIGN KEY (`enteredByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurduplicatelink`
--

CREATE TABLE `omoccurduplicatelink` (
  `occid` int(10) unsigned NOT NULL,
  `duplicateid` int(11) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occid`,`duplicateid`),
  KEY `FK_omoccurdupelink_occid_idx` (`occid`),
  KEY `FK_omoccurdupelink_dupeid_idx` (`duplicateid`),
  CONSTRAINT `FK_omoccurdupelink_dupeid` FOREIGN KEY (`duplicateid`) REFERENCES `omoccurduplicates` (`duplicateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdupelink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurduplicates`
--

CREATE TABLE `omoccurduplicates` (
  `duplicateid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `dupeType` varchar(45) NOT NULL DEFAULT 'Exact Duplicate',
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`duplicateid`)
) ENGINE=InnoDB;


--
-- Table structure for table `omoccureditlocks`
--
CREATE TABLE `omoccureditlocks` (
  `occid` int(10) unsigned NOT NULL,
  `uid` int(11) NOT NULL,
  `ts` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`occid`)
) ENGINE=InnoDB;


--
-- Table structure for table `omoccuredits`
--

CREATE TABLE `omoccuredits` (
  `ocedid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `tableName` varchar(45) DEFAULT NULL,
  `fieldName` varchar(45) NOT NULL,
  `fieldValueNew` text NOT NULL,
  `fieldValueOld` text NOT NULL,
  `reviewStatus` int(1) NOT NULL DEFAULT 1 COMMENT '1=Open;2=Pending;3=Closed',
  `appliedStatus` int(1) NOT NULL DEFAULT 0 COMMENT '0=Not Applied;1=Applied',
  `editType` int(11) DEFAULT 0 COMMENT '0 = general edit, 1 = batch edit',
  `isActive` int(1) DEFAULT NULL COMMENT '0 = not the value applied within the active field, 1 = valued applied within active field',
  `reapply` int(1) DEFAULT NULL COMMENT '0 = do not reapply edit; 1 = reapply edit when snapshot is refreshed, if edit isActive and snapshot value still matches old value ',
  `guid` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ocedid`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_omoccuredits_uid` (`uid`),
  KEY `fk_omoccuredits_occid` (`occid`),
  KEY `IX_omoccuredits_timestamp` (`initialTimestamp`),
  KEY `FK_omoccuredits_tableName` (`tableName`),
  KEY `FK_omoccuredits_fieldName` (`fieldName`),
  KEY `FK_omoccuredits_reviewedStatus` (`reviewStatus`),
  KEY `FK_omoccuredits_appliedStatus` (`appliedStatus`),
  CONSTRAINT `fk_omoccuredits_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccuredits_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurexchange`
--

CREATE TABLE `omoccurexchange` (
  `exchangeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(30) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `transactionType` varchar(10) DEFAULT NULL,
  `in_out` varchar(3) DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateReceived` date DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `totalExMounted` int(5) DEFAULT NULL,
  `totalExUnmounted` int(5) DEFAULT NULL,
  `totalGift` int(5) DEFAULT NULL,
  `totalGiftDet` int(5) DEFAULT NULL,
  `adjustment` int(5) DEFAULT NULL,
  `invoiceBalance` int(6) DEFAULT NULL,
  `invoiceMessage` varchar(500) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdBy` varchar(20) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`exchangeid`),
  KEY `FK_occexch_coll` (`collid`),
  CONSTRAINT `FK_occexch_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurgenetic`
--

CREATE TABLE `omoccurgenetic` (
  `idoccurgenetic` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifier` varchar(150) DEFAULT NULL,
  `resourcename` varchar(150) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `locus` varchar(500) DEFAULT NULL,
  `resourceurl` varchar(500) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idoccurgenetic`),
  UNIQUE KEY `UNIQUE_omoccurgenetic` (`occid`,`resourceurl`),
  KEY `FK_omoccurgenetic` (`occid`),
  KEY `INDEX_omoccurgenetic_name` (`resourcename`),
  CONSTRAINT `FK_omoccurgenetic` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurgeoindex`
--

CREATE TABLE `omoccurgeoindex` (
  `tid` int(10) unsigned NOT NULL,
  `decimallatitude` double NOT NULL,
  `decimallongitude` double NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tid`,`decimallatitude`,`decimallongitude`),
  CONSTRAINT `FK_specgeoindex_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccuridentifiers`
--

CREATE TABLE `omoccuridentifiers` (
  `idomoccuridentifiers` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiervalue` varchar(45) NOT NULL,
  `identifiername` varchar(45) NOT NULL DEFAULT '' COMMENT 'barcode, accession number, old catalog number, NPS, etc',
  `notes` varchar(250) DEFAULT NULL,
  `sortBy` int(11) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idomoccuridentifiers`),
  UNIQUE KEY `UQ_omoccuridentifiers` (`occid`,`identifiervalue`,`identifiername`),
  KEY `FK_omoccuridentifiers_occid_idx` (`occid`),
  KEY `IX_omoccuridentifiers_value` (`identifiervalue`),
  CONSTRAINT `FK_omoccuridentifiers_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurloans`
--

CREATE TABLE `omoccurloans` (
  `loanid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loanIdentifierOwn` varchar(30) DEFAULT NULL,
  `loanIdentifierBorr` varchar(30) DEFAULT NULL,
  `collidOwn` int(10) unsigned DEFAULT NULL,
  `collidBorr` int(10) unsigned DEFAULT NULL,
  `iidOwner` int(10) unsigned DEFAULT NULL,
  `iidBorrower` int(10) unsigned DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateSentReturn` date DEFAULT NULL,
  `receivedStatus` varchar(250) DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `totalBoxesReturned` int(5) DEFAULT NULL,
  `numSpecimens` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `shippingMethodReturn` varchar(50) DEFAULT NULL,
  `dateDue` date DEFAULT NULL,
  `dateReceivedOwn` date DEFAULT NULL,
  `dateReceivedBorr` date DEFAULT NULL,
  `dateClosed` date DEFAULT NULL,
  `forWhom` varchar(50) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `invoiceMessageOwn` varchar(500) DEFAULT NULL,
  `invoiceMessageBorr` varchar(500) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdByOwn` varchar(30) DEFAULT NULL,
  `createdByBorr` varchar(30) DEFAULT NULL,
  `processingStatus` int(5) unsigned DEFAULT 1,
  `processedByOwn` varchar(30) DEFAULT NULL,
  `processedByBorr` varchar(30) DEFAULT NULL,
  `processedByReturnOwn` varchar(30) DEFAULT NULL,
  `processedByReturnBorr` varchar(30) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`loanid`),
  KEY `FK_occurloans_owninst` (`iidOwner`),
  KEY `FK_occurloans_borrinst` (`iidBorrower`),
  KEY `FK_occurloans_owncoll` (`collidOwn`),
  KEY `FK_occurloans_borrcoll` (`collidBorr`),
  CONSTRAINT `FK_occurloans_borrcoll` FOREIGN KEY (`collidBorr`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_borrinst` FOREIGN KEY (`iidBorrower`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owncoll` FOREIGN KEY (`collidOwn`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owninst` FOREIGN KEY (`iidOwner`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurloansattachment`
--

CREATE TABLE `omoccurloansattachment` (
  `attachmentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loanid` int(10) unsigned DEFAULT NULL,
  `exchangeid` int(10) unsigned DEFAULT NULL,
  `title` varchar(80) NOT NULL,
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`attachmentid`),
  KEY `FK_occurloansattachment_loanid_idx` (`loanid`),
  KEY `FK_occurloansattachment_exchangeid_idx` (`exchangeid`),
  CONSTRAINT `FK_occurloansattachment_exchangeid` FOREIGN KEY (`exchangeid`) REFERENCES `omoccurexchange` (`exchangeid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloansattachment_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurloanslink`
--

CREATE TABLE `omoccurloanslink` (
  `loanid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `returndate` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`loanid`,`occid`),
  KEY `FK_occurloanlink_occid` (`occid`),
  KEY `FK_occurloanlink_loanid` (`loanid`),
  CONSTRAINT `FK_occurloanlink_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloanlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurloanuser`
--

CREATE TABLE `omoccurloanuser` (
  `loanid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedByUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`loanid`,`uid`),
  KEY `FK_occurloan_uid_idx` (`uid`),
  KEY `FK_occurloan_modifiedByUid_idx` (`modifiedByUid`),
  CONSTRAINT `FK_occurloan_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloan_modifiedByUid` FOREIGN KEY (`modifiedByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloan_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurpaleo`
--

CREATE TABLE `omoccurpaleo` (
  `paleoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `eon` varchar(65) DEFAULT NULL,
  `era` varchar(65) DEFAULT NULL,
  `period` varchar(65) DEFAULT NULL,
  `epoch` varchar(65) DEFAULT NULL,
  `earlyInterval` varchar(65) DEFAULT NULL,
  `lateInterval` varchar(65) DEFAULT NULL,
  `absoluteAge` varchar(65) DEFAULT NULL,
  `storageAge` varchar(65) DEFAULT NULL,
  `stage` varchar(65) DEFAULT NULL,
  `localStage` varchar(65) DEFAULT NULL,
  `biota` varchar(65) DEFAULT NULL COMMENT 'Flora or Fanua',
  `biostratigraphy` varchar(65) DEFAULT NULL COMMENT 'Biozone',
  `taxonEnvironment` varchar(65) DEFAULT NULL COMMENT 'Marine or not',
  `lithogroup` varchar(65) DEFAULT NULL,
  `formation` varchar(65) DEFAULT NULL,
  `member` varchar(65) DEFAULT NULL,
  `bed` varchar(65) DEFAULT NULL,
  `lithology` varchar(250) DEFAULT NULL,
  `stratRemarks` varchar(250) DEFAULT NULL,
  `element` varchar(250) DEFAULT NULL,
  `slideProperties` varchar(1000) DEFAULT NULL,
  `geologicalContextID` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`paleoID`),
  UNIQUE KEY `UNIQUE_occid` (`occid`),
  KEY `FK_paleo_occid_idx` (`occid`),
  CONSTRAINT `FK_paleo_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurpaleogts`
--

CREATE TABLE `omoccurpaleogts` (
  `gtsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gtsterm` varchar(45) NOT NULL,
  `rankid` int(11) NOT NULL,
  `rankname` varchar(45) DEFAULT NULL,
  `parentgtsid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`gtsid`),
  UNIQUE KEY `UNIQUE_gtsterm` (`gtsid`),
  KEY `FK_gtsparent_idx` (`parentgtsid`),
  CONSTRAINT `FK_gtsparent` FOREIGN KEY (`parentgtsid`) REFERENCES `omoccurpaleogts` (`gtsid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurpoints`
--

CREATE TABLE `omoccurpoints` (
  `geoID` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(11) NOT NULL,
  `point` point NOT NULL,
  `errradiuspoly` polygon DEFAULT NULL,
  `footprintpoly` polygon DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoID`),
  UNIQUE KEY `occid` (`occid`),
  SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM;


--
-- Table structure for table `omoccurrences`
--

CREATE TABLE `omoccurrences` (
  `occid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `organismID` varchar(150) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidInterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text DEFAULT NULL,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text DEFAULT NULL,
  `identificationRemarks` text DEFAULT NULL,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumber` varchar(45) DEFAULT NULL COMMENT 'Collector Number',
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `eventDate2` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `eventTime` varchar(45) DEFAULT NULL,
  `habitat` text DEFAULT NULL COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `fieldNotes` text DEFAULT NULL,
  `fieldNumber` varchar(45) DEFAULT NULL,
  `eventID` varchar(150) DEFAULT NULL,
  `occurrenceRemarks` text DEFAULT NULL COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text DEFAULT NULL,
  `associatedTaxa` text DEFAULT NULL COMMENT 'Associated Species',
  `dynamicProperties` text DEFAULT NULL,
  `verbatimAttributes` text DEFAULT NULL,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(150) DEFAULT NULL,
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `locationID` varchar(150) DEFAULT NULL,
  `continent` varchar(45) DEFAULT NULL,
  `waterBody` varchar(75) DEFAULT NULL,
  `parentLocationID` varchar(150) DEFAULT NULL,
  `islandGroup` varchar(75) DEFAULT NULL,
  `island` varchar(75) DEFAULT NULL,
  `countryCode` varchar(5) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text DEFAULT NULL,
  `localitySecurity` int(10) DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text DEFAULT NULL,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text DEFAULT NULL,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferencedDate` datetime DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(500) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text DEFAULT NULL,
  `availability` int(2) DEFAULT NULL,
  `disposition` varchar(250) DEFAULT NULL,
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericColumn1` varchar(100) DEFAULT NULL,
  `genericColumn2` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observerUid` int(10) unsigned DEFAULT NULL,
  `processingStatus` varchar(45) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(250) DEFAULT NULL,
  `dynamicFields` text DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `dateEntered` datetime DEFAULT NULL,
  `dateLastModified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`occid`) USING BTREE,
  UNIQUE KEY `Index_collid` (`collid`,`dbpk`),
  UNIQUE KEY `UNIQUE_occurrenceID` (`occurrenceID`),
  KEY `Index_sciname` (`sciname`),
  KEY `Index_family` (`family`),
  KEY `Index_country` (`country`),
  KEY `Index_state` (`stateProvince`),
  KEY `Index_county` (`county`),
  KEY `Index_collector` (`recordedBy`),
  KEY `Index_ownerInst` (`ownerInstitutionCode`),
  KEY `FK_omoccurrences_tid` (`tidInterpreted`),
  KEY `FK_omoccurrences_uid` (`observerUid`),
  KEY `Index_municipality` (`municipality`),
  KEY `Index_collnum` (`recordNumber`),
  KEY `Index_catalognumber` (`catalogNumber`),
  KEY `Index_eventDate` (`eventDate`),
  KEY `Index_occurrences_procstatus` (`processingStatus`),
  KEY `occelevmax` (`maximumElevationInMeters`),
  KEY `occelevmin` (`minimumElevationInMeters`),
  KEY `Index_occurrences_cult` (`cultivationStatus`),
  KEY `Index_occurrences_typestatus` (`typeStatus`),
  KEY `Index_occurDateLastModifed` (`dateLastModified`),
  KEY `Index_occurDateEntered` (`dateEntered`),
  KEY `Index_occurRecordEnteredBy` (`recordEnteredBy`),
  KEY `Index_locality` (`locality`(100)),
  KEY `Index_otherCatalogNumbers` (`otherCatalogNumbers`),
  KEY `IX_omoccur_eventDate2` (`eventDate2`),
  KEY `Index_locationID` (`locationID`),
  KEY `Index_eventID` (`eventID`),
  KEY `Index_occur_localitySecurity` (`localitySecurity`),
  KEY `IX_occurrences_lat` (`decimalLatitude`),
  KEY `IX_occurrences_lng` (`decimalLongitude`),
  KEY `IX_omoccurrences_recordID` (`recordID`),
  CONSTRAINT `FK_omoccurrences_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_tid` FOREIGN KEY (`tidInterpreted`) REFERENCES `taxa` (`tid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_uid` FOREIGN KEY (`observerUid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB;


DELIMITER |
CREATE TRIGGER `omoccurrences_insert` AFTER INSERT ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO omoccurpoints (`occid`,`point`) 
		VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
	END IF;
	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
		VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
	END IF;
END;
|

CREATE TRIGGER `omoccurrences_update` AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurpoints 
			SET `point` = Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`)
			WHERE `occid` = NEW.`occid`;
		ELSE 
			INSERT INTO omoccurpoints (`occid`,`point`) 
			VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
		END IF;
	ELSE
		DELETE FROM omoccurpoints WHERE `occid` = NEW.`occid`;
	END IF;

	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurrencesfulltext WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurrencesfulltext 
			SET `recordedby` = NEW.`recordedby`,`locality` = CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`)
			WHERE `occid` = NEW.`occid`;
		ELSE
			INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
			VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
		END IF;
	ELSE 
		DELETE FROM omoccurrencesfulltext WHERE `occid` = NEW.`occid`;
	END IF;
END;
|

CREATE TRIGGER `omoccurrences_delete` BEFORE DELETE ON `omoccurrences`
FOR EACH ROW BEGIN
	DELETE FROM omoccurpoints WHERE `occid` = OLD.`occid`;
	DELETE FROM omoccurrencesfulltext WHERE `occid` = OLD.`occid`;
END;
|
DELIMITER ;


--
-- Table structure for table `omoccurrencesfulltext`
--

CREATE TABLE `omoccurrencesfulltext` (
  `occid` int(11) NOT NULL,
  `locality` text DEFAULT NULL,
  `recordedby` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`occid`),
  FULLTEXT KEY `ft_occur_locality` (`locality`),
  FULLTEXT KEY `ft_occur_recordedby` (`recordedby`)
) ENGINE=MyISAM;


--
-- Table structure for table `omoccurrevisions`
--

CREATE TABLE `omoccurrevisions` (
  `orid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `oldValues` text DEFAULT NULL,
  `newValues` text DEFAULT NULL,
  `externalSource` varchar(45) DEFAULT NULL,
  `externalEditor` varchar(100) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `reviewStatus` int(11) DEFAULT NULL,
  `appliedStatus` int(11) DEFAULT NULL,
  `errorMessage` varchar(500) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `externalTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`orid`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_omrevisions_occid_idx` (`occid`),
  KEY `fk_omrevisions_uid_idx` (`uid`),
  KEY `Index_omrevisions_applied` (`appliedStatus`),
  KEY `Index_omrevisions_reviewed` (`reviewStatus`),
  KEY `Index_omrevisions_source` (`externalSource`),
  KEY `Index_omrevisions_editor` (`externalEditor`),
  CONSTRAINT `fk_omrevisions_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omrevisions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `omoccurverification`
--

CREATE TABLE `omoccurverification` (
  `ovsid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `category` varchar(45) NOT NULL,
  `ranking` int(11) NOT NULL,
  `protocol` varchar(100) DEFAULT NULL,
  `source` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ovsid`),
  UNIQUE KEY `UNIQUE_omoccurverification` (`occid`,`category`),
  KEY `FK_omoccurverification_occid_idx` (`occid`),
  KEY `FK_omoccurverification_uid_idx` (`uid`),
  CONSTRAINT `FK_omoccurverification_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurverification_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `portalindex`
--

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
) ENGINE=InnoDB;


--
-- Table structure for table `portaloccurrences`
--

CREATE TABLE `portaloccurrences` (
  `portalOccurrencesID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `pubid` int(10) unsigned NOT NULL,
  `remoteOccid` int(11) DEFAULT NULL,
  `verification` int(11) NOT NULL DEFAULT 0,
  `refreshTimestamp` datetime NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`portalOccurrencesID`),
  UNIQUE KEY `UQ_portalOccur_occid_pubid` (`occid`,`pubid`),
  KEY `FK_portalOccur_occid_idx` (`occid`),
  KEY `FK_portalOccur_pubID_idx` (`pubid`),
  CONSTRAINT `FK_portalOccur_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalOccur_pubid` FOREIGN KEY (`pubid`) REFERENCES `portalpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `portalpublications`
--

CREATE TABLE `portalpublications` (
  `pubid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pubTitle` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `portalID` int(11) NOT NULL,
  `direction` varchar(45) NOT NULL,
  `criteriaJson` text DEFAULT NULL,
  `includeDeterminations` int(11) DEFAULT 1,
  `includeImages` int(11) DEFAULT 1,
  `autoUpdate` int(11) DEFAULT 1,
  `lastDateUpdate` datetime DEFAULT NULL,
  `updateInterval` int(11) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pubid`),
  UNIQUE KEY `UQ_portalpub_guid` (`guid`),
  KEY `FK_portalpub_collid_idx` (`collid`),
  KEY `FK_portalpub_portalID_idx` (`portalID`),
  KEY `FK_portalpub_uid_idx` (`createdUid`),
  CONSTRAINT `FK_portalpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_portalID` FOREIGN KEY (`portalID`) REFERENCES `portalindex` (`portalID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referenceagentlinks`
--

CREATE TABLE `referenceagentlinks` (
  `refid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `createdbyid` int(11) NOT NULL,
  PRIMARY KEY (`refid`,`agentid`)
) ENGINE=InnoDB;


--
-- Table structure for table `referenceauthorlink`
--

CREATE TABLE `referenceauthorlink` (
  `refid` int(11) NOT NULL,
  `refauthid` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`refauthid`),
  KEY `FK_refauthlink_refid_idx` (`refid`),
  KEY `FK_refauthlink_refauthid_idx` (`refauthid`),
  CONSTRAINT `FK_refauthlink_refauthid` FOREIGN KEY (`refauthid`) REFERENCES `referenceauthors` (`refauthorid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refauthlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referenceauthors`
--

CREATE TABLE `referenceauthors` (
  `refauthorid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refauthorid`),
  KEY `INDEX_refauthlastname` (`lastname`)
) ENGINE=InnoDB;


--
-- Table structure for table `referencechecklistlink`
--

CREATE TABLE `referencechecklistlink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`clid`),
  KEY `FK_refcheckllistlink_refid_idx` (`refid`),
  KEY `FK_refcheckllistlink_clid_idx` (`clid`),
  CONSTRAINT `FK_refchecklistlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchecklistlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referencechklsttaxalink`
--

CREATE TABLE `referencechklsttaxalink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`clid`,`tid`),
  KEY `FK_refchktaxalink_clidtid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_refchktaxalink_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`clid`, `tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchktaxalink_ref` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referencecollectionlink`
--

CREATE TABLE `referencecollectionlink` (
  `refid` int(11) NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`collid`),
  KEY `FK_refcollectionlink_collid_idx` (`collid`),
  CONSTRAINT `FK_refcollectionlink_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refcollectionlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB;


--
-- Table structure for table `referencedatasetlink`
--

CREATE TABLE `referencedatasetlink` (
  `refid` int(11) NOT NULL,
  `datasetid` int(10) unsigned NOT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`datasetid`),
  KEY `FK_refdataset_datasetid_idx` (`datasetid`),
  KEY `FK_refdataset_uid_idx` (`createdUid`),
  CONSTRAINT `FK_refdataset_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refdataset_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refdataset_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referenceobject`
--

CREATE TABLE `referenceobject` (
  `refid` int(11) NOT NULL AUTO_INCREMENT,
  `parentRefId` int(11) DEFAULT NULL,
  `ReferenceTypeId` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `secondarytitle` varchar(250) DEFAULT NULL,
  `shorttitle` varchar(250) DEFAULT NULL,
  `tertiarytitle` varchar(250) DEFAULT NULL,
  `alternativetitle` varchar(250) DEFAULT NULL,
  `typework` varchar(150) DEFAULT NULL,
  `figures` varchar(150) DEFAULT NULL,
  `pubdate` varchar(45) DEFAULT NULL,
  `edition` varchar(45) DEFAULT NULL,
  `volume` varchar(45) DEFAULT NULL,
  `numbervolumnes` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `pages` varchar(45) DEFAULT NULL,
  `section` varchar(45) DEFAULT NULL,
  `placeofpublication` varchar(45) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `isbn_issn` varchar(45) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `ispublished` varchar(45) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `cheatauthors` varchar(400) DEFAULT NULL,
  `cheatcitation` varchar(500) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`),
  KEY `INDEX_refobj_title` (`title`),
  KEY `FK_refobj_parentrefid_idx` (`parentRefId`),
  KEY `FK_refobj_typeid_idx` (`ReferenceTypeId`),
  CONSTRAINT `FK_refobj_parentrefid` FOREIGN KEY (`parentRefId`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refobj_reftypeid` FOREIGN KEY (`ReferenceTypeId`) REFERENCES `referencetype` (`ReferenceTypeId`)
) ENGINE=InnoDB;


--
-- Table structure for table `referenceoccurlink`
--

CREATE TABLE `referenceoccurlink` (
  `refid` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`occid`),
  KEY `FK_refoccurlink_refid_idx` (`refid`),
  KEY `FK_refoccurlink_occid_idx` (`occid`),
  CONSTRAINT `FK_refoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refoccurlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referencetaxalink`
--

CREATE TABLE `referencetaxalink` (
  `refid` int(11) NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`refid`,`tid`),
  KEY `FK_reftaxalink_refid_idx` (`refid`),
  KEY `FK_reftaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_reftaxalink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reftaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `referencetype`
--

CREATE TABLE `referencetype` (
  `ReferenceTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `ReferenceType` varchar(45) NOT NULL,
  `IsParent` int(11) DEFAULT NULL,
  `Title` varchar(45) DEFAULT NULL,
  `SecondaryTitle` varchar(45) DEFAULT NULL,
  `PlacePublished` varchar(45) DEFAULT NULL,
  `Publisher` varchar(45) DEFAULT NULL,
  `Volume` varchar(45) DEFAULT NULL,
  `NumberVolumes` varchar(45) DEFAULT NULL,
  `Number` varchar(45) DEFAULT NULL,
  `Pages` varchar(45) DEFAULT NULL,
  `Section` varchar(45) DEFAULT NULL,
  `TertiaryTitle` varchar(45) DEFAULT NULL,
  `Edition` varchar(45) DEFAULT NULL,
  `Date` varchar(45) DEFAULT NULL,
  `TypeWork` varchar(45) DEFAULT NULL,
  `ShortTitle` varchar(45) DEFAULT NULL,
  `AlternativeTitle` varchar(45) DEFAULT NULL,
  `ISBN_ISSN` varchar(45) DEFAULT NULL,
  `Figures` varchar(45) DEFAULT NULL,
  `addedByUid` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ReferenceTypeId`),
  UNIQUE KEY `ReferenceType_UNIQUE` (`ReferenceType`)
) ENGINE=InnoDB;


--
-- Table structure for table `salixwordstats`
--

CREATE TABLE `salixwordstats` (
  `swsid` int(11) NOT NULL AUTO_INCREMENT,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `locality` int(4) NOT NULL DEFAULT 0,
  `localityFreq` int(4) NOT NULL DEFAULT 0,
  `habitat` int(4) NOT NULL DEFAULT 0,
  `habitatFreq` int(4) NOT NULL DEFAULT 0,
  `substrate` int(4) NOT NULL DEFAULT 0,
  `substrateFreq` int(4) NOT NULL DEFAULT 0,
  `verbatimAttributes` int(4) NOT NULL DEFAULT 0,
  `verbatimAttributesFreq` int(4) NOT NULL DEFAULT 0,
  `occurrenceRemarks` int(4) NOT NULL DEFAULT 0,
  `occurrenceRemarksFreq` int(4) NOT NULL DEFAULT 0,
  `totalcount` int(4) NOT NULL DEFAULT 0,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`swsid`),
  UNIQUE KEY `INDEX_unique` (`firstword`,`secondword`),
  KEY `INDEX_secondword` (`secondword`)
) ENGINE=InnoDB;


--
-- Table structure for table `schemaversion`
--

CREATE TABLE `schemaversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versionnumber` varchar(20) NOT NULL,
  `dateapplied` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `versionnumber_UNIQUE` (`versionnumber`)
) ENGINE=InnoDB;


--
-- Table structure for table `specprocessorprojects`
--

CREATE TABLE `specprocessorprojects` (
  `spprid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `projectType` varchar(45) DEFAULT NULL,
  `specKeyPattern` varchar(45) DEFAULT NULL,
  `patternReplace` varchar(45) DEFAULT NULL,
  `replaceStr` varchar(45) DEFAULT NULL,
  `specKeyRetrieval` varchar(45) DEFAULT NULL,
  `coordX1` int(10) unsigned DEFAULT NULL,
  `coordX2` int(10) unsigned DEFAULT NULL,
  `coordY1` int(10) unsigned DEFAULT NULL,
  `coordY2` int(10) unsigned DEFAULT NULL,
  `sourcePath` varchar(250) DEFAULT NULL,
  `targetPath` varchar(250) DEFAULT NULL,
  `imgUrl` varchar(250) DEFAULT NULL,
  `webPixWidth` int(10) unsigned DEFAULT 1200,
  `tnPixWidth` int(10) unsigned DEFAULT 130,
  `lgPixWidth` int(10) unsigned DEFAULT 2400,
  `jpgCompression` int(11) DEFAULT 70,
  `createTnImg` int(10) unsigned DEFAULT 1,
  `createLgImg` int(10) unsigned DEFAULT 1,
  `additionalOptions` text DEFAULT NULL,
  `source` varchar(45) DEFAULT NULL,
  `processingCode` int(10) DEFAULT NULL,
  `lastRunDate` date DEFAULT NULL,
  `createdByUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`spprid`),
  KEY `FK_specprocessorprojects_coll` (`collid`),
  KEY `FK_specprocprojects_uid_idx` (`createdByUid`),
  CONSTRAINT `FK_specprocessorprojects_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_specprocprojects_uid` FOREIGN KEY (`createdByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `specprocessorrawlabels`
--

CREATE TABLE `specprocessorrawlabels` (
  `prlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `rawstr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`prlid`),
  KEY `FK_specproc_images` (`imgid`),
  KEY `FK_specproc_occid` (`occid`),
  CONSTRAINT `FK_specproc_images` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_specproc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


DELIMITER |
CREATE TRIGGER `specprocessorrawlabelsfulltext_insert` AFTER INSERT ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  INSERT INTO specprocessorrawlabelsfulltext (
    `prlid`,
    `imgid`,
    `rawstr`
  ) VALUES (
    NEW.`prlid`,
    NEW.`imgid`,
    NEW.`rawstr`
  );
END;
|

CREATE TRIGGER `specprocessorrawlabelsfulltext_update` AFTER UPDATE ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  UPDATE specprocessorrawlabelsfulltext SET
    `imgid` = NEW.`imgid`,
    `rawstr` = NEW.`rawstr`
  WHERE `prlid` = NEW.`prlid`;
END;
|
DELIMITER ;


--
-- Table structure for table `specprocessorrawlabelsfulltext`
--


CREATE TABLE `specprocessorrawlabelsfulltext` (
  `prlid` int(11) NOT NULL,
  `imgid` int(11) NOT NULL,
  `rawstr` text NOT NULL,
  PRIMARY KEY (`prlid`),
  KEY `Index_ocr_imgid` (`imgid`),
  FULLTEXT KEY `Index_ocr_fulltext` (`rawstr`)
) ENGINE=MyISAM;


DELIMITER |
CREATE TRIGGER `specprocessorrawlabelsfulltext_delete` BEFORE DELETE ON `specprocessorrawlabelsfulltext`
FOR EACH ROW BEGIN
  DELETE FROM specprocessorrawlabelsfulltext WHERE `prlid` = OLD.`prlid`;
END;
|
DELIMITER ;


--
-- Table structure for table `specprocnlp`
--

CREATE TABLE `specprocnlp` (
  `spnlpid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `sqlfrag` varchar(250) NOT NULL,
  `patternmatch` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`spnlpid`),
  KEY `FK_specprocnlp_collid` (`collid`),
  CONSTRAINT `FK_specprocnlp_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `specprocnlpfrag`
--

CREATE TABLE `specprocnlpfrag` (
  `spnlpfragid` int(10) NOT NULL AUTO_INCREMENT,
  `spnlpid` int(10) NOT NULL,
  `fieldname` varchar(45) NOT NULL,
  `patternmatch` varchar(250) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortseq` int(5) DEFAULT 50,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`spnlpfragid`),
  KEY `FK_specprocnlpfrag_spnlpid` (`spnlpid`),
  CONSTRAINT `FK_specprocnlpfrag_spnlpid` FOREIGN KEY (`spnlpid`) REFERENCES `specprocnlp` (`spnlpid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `specprocnlpversion`
--

CREATE TABLE `specprocnlpversion` (
  `nlpverid` int(11) NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `archivestr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`nlpverid`),
  KEY `FK_specprocnlpver_rawtext_idx` (`prlid`),
  CONSTRAINT `FK_specprocnlpver_rawtext` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON UPDATE CASCADE
) ENGINE=InnoDB COMMENT='Archives field name - value pairs of NLP results loading into an omoccurrence record. This way, results can be easily redone at a later date without copying over date modifed afterward by another user or process ';


--
-- Table structure for table `specprococrfrag`
--

CREATE TABLE `specprococrfrag` (
  `ocrfragid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `keyterm` varchar(45) DEFAULT NULL,
  `wordorder` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ocrfragid`),
  KEY `FK_specprococrfrag_prlid_idx` (`prlid`),
  KEY `Index_keyterm` (`keyterm`),
  CONSTRAINT `FK_specprococrfrag_prlid` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `specprocstatus`
--

CREATE TABLE `specprocstatus` (
  `spsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `processName` varchar(45) NOT NULL,
  `result` varchar(45) DEFAULT NULL,
  `processVariables` varchar(150) NOT NULL,
  `processorUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`spsID`),
  KEY `specprocstatus_occid_idx` (`occid`),
  KEY `specprocstatus_uid_idx` (`processorUid`),
  CONSTRAINT `specprocstatus_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `specprocstatus_uid` FOREIGN KEY (`processorUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxa`
--

CREATE TABLE `taxa` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) DEFAULT '',
  `rankID` smallint(5) unsigned DEFAULT 0,
  `sciName` varchar(250) NOT NULL,
  `unitInd1` varchar(1) DEFAULT NULL,
  `unitName1` varchar(50) NOT NULL,
  `unitInd2` varchar(1) DEFAULT NULL,
  `unitName2` varchar(50) DEFAULT 't',
  `unitInd3` varchar(45) DEFAULT NULL,
  `unitName3` varchar(35) DEFAULT NULL,
  `author` varchar(150) NOT NULL DEFAULT '',
  `phylosortSequence` tinyint(3) unsigned DEFAULT NULL,
  `reviewStatus` int(11) DEFAULT NULL,
  `displayStatus` int(11) DEFAULT NULL,
  `isLegitimate` int(11) DEFAULT NULL,
  `nomenclaturalStatus` varchar(45) DEFAULT NULL,
  `nomenclaturalCode` varchar(45) DEFAULT NULL,
  `statusNotes` varchar(50) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `hybrid` varchar(50) DEFAULT NULL,
  `securityStatus` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tid`),
  UNIQUE KEY `UQ_taxa_sciname` (`sciName`, `rankID`, `kingdomName` ASC),
  KEY `rankid_index` (`rankID`),
  KEY `unitname1_index` (`unitName1`,`unitName2`) USING BTREE,
  KEY `FK_taxa_uid_idx` (`modifiedUid`),
  KEY `sciname_index` (`sciName`),
  KEY `idx_taxa_kingdomName` (`kingdomName`),
  KEY `idx_taxacreated` (`initialTimestamp`),
  CONSTRAINT `FK_taxa_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB;

# The default UNIQUE INDEX applied above supports cross-kingdom homonyms
# Run following statement to create a UNIQUE INDEX that supports homonyms within a single kingdom (not recommended)
# ALTER TABLE `taxa` DROP INDEX `UQ_taxa_sciname`, ADD UNIQUE INDEX `UQ_taxa_sciname` (`sciName` ASC, `author` ASC, `rankID` ASC, `kingdomName` ASC);
# Run following statement to create a UNIQUE INDEX that restricts homonyms, which is ideal for single kingdom portals and best method to avoid dupicate names 
# ALTER TABLE `taxa` DROP INDEX `UQ_taxa_sciname`, ADD UNIQUE INDEX `UQ_taxa_sciname` (`sciName` ASC, `rankID` ASC);


--
-- Table structure for table `taxadescrblock`
--

CREATE TABLE `taxadescrblock` (
  `tdbid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tdProfileID` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `caption` varchar(40) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `language` varchar(45) DEFAULT 'English',
  `langid` int(11) DEFAULT NULL,
  `displaylevel` int(10) unsigned NOT NULL DEFAULT 1 COMMENT '1 = short descr, 2 = intermediate descr',
  `uid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tdbid`),
  KEY `FK_taxadesc_lang_idx` (`langid`),
  KEY `FK_taxadescrblock_tid_idx` (`tid`),
  KEY `FK_taxadescrblock_tdProfileID_idx` (`tdProfileID`),
  CONSTRAINT `FK_taxadesc_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_taxadescrblock_tdProfileID` FOREIGN KEY (`tdProfileID`) REFERENCES `taxadescrprofile` (`tdProfileID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxadescrprofile`
--

CREATE TABLE `taxadescrprofile` (
  `tdProfileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `authors` varchar(100) DEFAULT NULL,
  `caption` varchar(40) NOT NULL,
  `projectDescription` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `publication` varchar(500) DEFAULT NULL,
  `urlTemplate` varchar(250) DEFAULT NULL,
  `internalNotes` varchar(250) DEFAULT NULL,
  `langid` int(11) DEFAULT 1,
  `defaultDisplayLevel` int(11) DEFAULT 1,
  `dynamicProperties` text DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tdProfileID`),
  KEY `FK_taxadescrprofile_langid_idx` (`langid`),
  KEY `FK_taxadescrprofile_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_taxadescrprofile_langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxadescrprofile_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxadescrstmts`
--

CREATE TABLE `taxadescrstmts` (
  `tdsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tdbid` int(10) unsigned NOT NULL,
  `heading` varchar(75) DEFAULT NULL,
  `statement` text NOT NULL,
  `displayheader` int(10) unsigned NOT NULL DEFAULT 1,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT 89,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tdsid`),
  KEY `FK_taxadescrstmts_tblock` (`tdbid`),
  CONSTRAINT `FK_taxadescrstmts_tblock` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxaenumtree`
--

CREATE TABLE `taxaenumtree` (
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL,
  `parenttid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tid`,`taxauthid`,`parenttid`),
  KEY `FK_tet_taxa` (`tid`),
  KEY `FK_tet_taxauth` (`taxauthid`),
  KEY `FK_tet_taxa2` (`parenttid`),
  CONSTRAINT `FK_tet_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxa2` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxalinks`
--

CREATE TABLE `taxalinks` (
  `tlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `inherit` int(11) DEFAULT 1,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT 50,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tlid`),
  UNIQUE KEY `UQ_taxaLinks_tid_url` (`tid`,`url`),
  KEY `FK_taxaLinks_tid` (`tid`),
  CONSTRAINT `FK_taxalinks_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `taxamaps`
--

CREATE TABLE `taxamaps` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mid`),
  KEY `FK_tid_idx` (`tid`),
  CONSTRAINT `FK_taxamaps_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `taxaresourcelinks`
--

CREATE TABLE `taxaresourcelinks` (
  `taxaresourceid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `sourcename` varchar(150) NOT NULL,
  `sourceidentifier` varchar(45) DEFAULT NULL,
  `sourceguid` varchar(150) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `ranking` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`taxaresourceid`),
  UNIQUE KEY `UNIQUE_taxaresource` (`tid`,`sourcename`),
  KEY `taxaresource_name` (`sourcename`),
  KEY `FK_taxaresource_tid_idx` (`tid`),
  CONSTRAINT `FK_taxaresource_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `taxauthority`
--

CREATE TABLE `taxauthority` (
  `taxauthid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isprimary` int(1) unsigned NOT NULL DEFAULT 0,
  `name` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `editors` varchar(150) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `isactive` int(1) unsigned NOT NULL DEFAULT 1,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`taxauthid`) USING BTREE
) ENGINE=InnoDB;


--
-- Table structure for table `taxavernaculars`
--

CREATE TABLE `taxavernaculars` (
  `TID` int(10) unsigned NOT NULL DEFAULT 0,
  `VernacularName` varchar(80) NOT NULL,
  `Language` varchar(15) DEFAULT NULL,
  `langid` int(11) DEFAULT NULL,
  `Source` varchar(50) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `isupperterm` int(2) DEFAULT 0,
  `sortSequence` int(10) DEFAULT 50,
  `VID` int(10) NOT NULL AUTO_INCREMENT,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`VID`),
  UNIQUE KEY `unique-key` (`VernacularName`,`TID`,`langid`),
  KEY `tid1` (`TID`),
  KEY `vernacularsnames` (`VernacularName`),
  KEY `FK_vern_lang_idx` (`langid`),
  CONSTRAINT `FK_vern_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_vernaculars_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB;


--
-- Table structure for table `taxonunits`
--

CREATE TABLE `taxonunits` (
  `taxonunitid` int(11) NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) NOT NULL DEFAULT 'Organism',
  `rankid` smallint(5) unsigned NOT NULL DEFAULT 0,
  `rankname` varchar(15) NOT NULL,
  `suffix` varchar(45) DEFAULT NULL,
  `dirparentrankid` smallint(6) NOT NULL,
  `reqparentrankid` smallint(6) DEFAULT NULL,
  `modifiedby` varchar(45) DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`taxonunitid`),
  UNIQUE KEY `UNIQUE_taxonunits` (`kingdomName`,`rankid`)
) ENGINE=InnoDB;


--
-- Table structure for table `taxstatus`
--

CREATE TABLE `taxstatus` (
  `tid` int(10) unsigned NOT NULL,
  `tidaccepted` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL COMMENT 'taxon authority id',
  `parenttid` int(10) unsigned DEFAULT NULL,
  `family` varchar(50) DEFAULT NULL,
  `taxonomicStatus` varchar(45) DEFAULT NULL,
  `taxonomicSource` varchar(500) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `UnacceptabilityReason` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT 50,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tid`,`tidaccepted`,`taxauthid`) USING BTREE,
  KEY `FK_taxstatus_tidacc` (`tidaccepted`),
  KEY `FK_taxstatus_taid` (`taxauthid`),
  KEY `Index_ts_family` (`family`),
  KEY `Index_parenttid` (`parenttid`),
  KEY `FK_taxstatus_uid_idx` (`modifiedUid`),
  KEY `Index_tid` (`tid`),
  CONSTRAINT `FK_taxstatus_parent` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_taid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_taxstatus_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_tidacc` FOREIGN KEY (`tidaccepted`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB;


--
-- Table structure for table `tmattributes`
--

CREATE TABLE `tmattributes` (
  `stateid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `modifier` varchar(100) DEFAULT NULL,
  `xvalue` double(15,5) DEFAULT NULL,
  `imgid` int(10) unsigned DEFAULT NULL,
  `imagecoordinates` varchar(45) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `statuscode` tinyint(4) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`stateid`,`occid`),
  KEY `FK_tmattr_stateid_idx` (`stateid`),
  KEY `FK_tmattr_occid_idx` (`occid`),
  KEY `FK_tmattr_imgid_idx` (`imgid`),
  KEY `FK_attr_uidcreate_idx` (`createdUid`),
  KEY `FK_tmattr_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_tmattr_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_stateid` FOREIGN KEY (`stateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidcreate` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `tmstates`
--

CREATE TABLE `tmstates` (
  `stateid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `traitid` int(10) unsigned NOT NULL,
  `statecode` varchar(2) NOT NULL,
  `statename` varchar(75) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `refurl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortseq` int(11) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`stateid`),
  UNIQUE KEY `traitid_code_UNIQUE` (`traitid`,`statecode`),
  KEY `FK_tmstate_uidcreated_idx` (`createdUid`),
  KEY `FK_tmstate_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_tmstates_traits` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `tmtraitdependencies`
--

CREATE TABLE `tmtraitdependencies` (
  `traitid` int(10) unsigned NOT NULL,
  `parentstateid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`traitid`,`parentstateid`),
  KEY `FK_tmdepend_traitid_idx` (`traitid`),
  KEY `FK_tmdepend_stateid_idx` (`parentstateid`),
  CONSTRAINT `FK_tmdepend_stateid` FOREIGN KEY (`parentstateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmdepend_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `tmtraits`
--

CREATE TABLE `tmtraits` (
  `traitid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `traitname` varchar(100) NOT NULL,
  `traittype` varchar(2) NOT NULL DEFAULT 'UM',
  `units` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `refurl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `projectGroup` varchar(45) DEFAULT NULL,
  `isPublic` int(11) DEFAULT 1,
  `includeInSearch` int(11) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`traitid`),
  KEY `traitsname` (`traitname`),
  KEY `FK_traits_uidcreated_idx` (`createdUid`),
  KEY `FK_traits_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_traits_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_traits_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `tmtraittaxalink`
--

CREATE TABLE `tmtraittaxalink` (
  `traitid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `relation` varchar(45) NOT NULL DEFAULT 'include',
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`traitid`,`tid`),
  KEY `FK_traittaxalink_traitid_idx` (`traitid`),
  KEY `FK_traittaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_traittaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_traittaxalink_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `uploaddetermtemp`
--

CREATE TABLE `uploaddetermtemp` (
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `identifiedBy` varchar(255) NOT NULL DEFAULT '',
  `dateIdentified` varchar(45) NOT NULL DEFAULT '',
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `higherClassification` varchar(150) DEFAULT NULL,
  `sciname` varchar(255) NOT NULL,
  `verbatimIdentification` varchar(250) DEFAULT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `genus` varchar(45) DEFAULT NULL,
  `specificEpithet` varchar(45) DEFAULT NULL,
  `verbatimTaxonRank` varchar(45) DEFAULT NULL,
  `taxonRank` varchar(45) DEFAULT NULL,
  `infraSpecificEpithet` varchar(45) DEFAULT NULL,
  `iscurrent` int(2) DEFAULT 0,
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(255) DEFAULT NULL,
  `taxonRemarks` varchar(2000) DEFAULT NULL,
  `identificationVerificationStatus` varchar(45) DEFAULT NULL,
  `taxonConceptID` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT 10,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  KEY `Index_uploaddet_occid` (`occid`),
  KEY `Index_uploaddet_collid` (`collid`),
  KEY `Index_uploaddet_dbpk` (`dbpk`)
) ENGINE=InnoDB;


--
-- Table structure for table `uploadglossary`
--

CREATE TABLE `uploadglossary` (
  `term` varchar(150) DEFAULT NULL,
  `definition` varchar(1000) DEFAULT NULL,
  `language` varchar(45) DEFAULT NULL,
  `source` varchar(1000) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `translator` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `resourceurl` varchar(600) DEFAULT NULL,
  `tidStr` varchar(100) DEFAULT NULL,
  `synonym` tinyint(1) DEFAULT NULL,
  `newGroupId` int(10) DEFAULT NULL,
  `currentGroupId` int(10) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  KEY `term_index` (`term`),
  KEY `relatedterm_index` (`newGroupId`)
) ENGINE=InnoDB;


--
-- Table structure for table `uploadimagetemp`
--

CREATE TABLE `uploadimagetemp` (
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `originalurl` varchar(255) DEFAULT NULL,
  `archiveurl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographeruid` int(10) unsigned DEFAULT NULL,
  `imagetype` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `referenceurl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `accessrights` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  KEY `Index_uploadimg_occid` (`occid`),
  KEY `Index_uploadimg_collid` (`collid`),
  KEY `Index_uploadimg_dbpk` (`dbpk`),
  KEY `Index_uploadimg_ts` (`initialTimestamp`)
) ENGINE=InnoDB;


--
-- Table structure for table `uploadspecmap`
--

CREATE TABLE `uploadspecmap` (
  `usmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uspid` int(10) unsigned NOT NULL,
  `sourcefield` varchar(45) NOT NULL,
  `symbdatatype` varchar(45) NOT NULL DEFAULT 'string' COMMENT 'string, numeric, datetime',
  `symbspecfield` varchar(45) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`usmid`),
  UNIQUE KEY `Index_unique` (`uspid`,`symbspecfield`,`sourcefield`),
  CONSTRAINT `FK_uploadspecmap_usp` FOREIGN KEY (`uspid`) REFERENCES `uploadspecparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `uploadspecparameters`
--

CREATE TABLE `uploadspecparameters` (
  `uspid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `uploadType` int(10) unsigned NOT NULL DEFAULT 1 COMMENT '1 = Direct; 2 = DiGIR; 3 = File',
  `title` varchar(45) NOT NULL,
  `platform` varchar(45) DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker',
  `server` varchar(150) DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `driver` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `pkField` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `schemaName` varchar(150) DEFAULT NULL,
  `internalQuery` varchar(250) DEFAULT NULL,
  `queryStr` text DEFAULT NULL,
  `cleanupSP` varchar(45) DEFAULT NULL,
  `endpointPublic` int(11) DEFAULT NULL,
  `dlmisvalid` int(10) unsigned DEFAULT 0,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`uspid`),
  KEY `FK_uploadspecparameters_coll` (`collid`),
  KEY `FK_uploadspecparameters_uid_idx` (`createdUid`),
  CONSTRAINT `FK_uploadspecparameters_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_uploadspecparameters_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB;


--
-- Table structure for table `uploadspectemp`
--

CREATE TABLE `uploadspectemp` (
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `organismID` varchar(150) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text DEFAULT NULL,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text DEFAULT NULL,
  `identificationRemarks` text DEFAULT NULL,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumberPrefix` varchar(45) DEFAULT NULL,
  `recordNumberSuffix` varchar(45) DEFAULT NULL,
  `recordNumber` varchar(32) DEFAULT NULL COMMENT 'Collector Number',
  `CollectorFamilyName` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `CollectorInitials` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `eventDate2` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `eventTime` varchar(45) DEFAULT NULL,
  `habitat` text DEFAULT NULL COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `host` varchar(250) DEFAULT NULL,
  `fieldNotes` text DEFAULT NULL,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `eventID` varchar(45) DEFAULT NULL,
  `occurrenceRemarks` text DEFAULT NULL COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text DEFAULT NULL,
  `associatedMedia` text DEFAULT NULL,
  `associatedReferences` text DEFAULT NULL,
  `associatedSequences` text DEFAULT NULL,
  `associatedTaxa` text DEFAULT NULL COMMENT 'Associated Species',
  `dynamicProperties` text DEFAULT NULL COMMENT 'Plant Description?',
  `verbatimAttributes` text DEFAULT NULL,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(150) DEFAULT NULL,
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `locationID` varchar(150) DEFAULT NULL,
  `parentLocationID` varchar(150) DEFAULT NULL,
  `continent` varchar(45) DEFAULT NULL,
  `waterBody` varchar(150) DEFAULT NULL,
  `islandGroup` varchar(75) DEFAULT NULL,
  `island` varchar(75) DEFAULT NULL,
  `countryCode` varchar(5) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text DEFAULT NULL,
  `localitySecurity` int(10) DEFAULT 0 COMMENT '0 = display locality, 1 = hide locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text DEFAULT NULL,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text DEFAULT NULL,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `latDeg` int(11) DEFAULT NULL,
  `latMin` double DEFAULT NULL,
  `latSec` double DEFAULT NULL,
  `latNS` varchar(3) DEFAULT NULL,
  `lngDeg` int(11) DEFAULT NULL,
  `lngMin` double DEFAULT NULL,
  `lngSec` double DEFAULT NULL,
  `lngEW` varchar(3) DEFAULT NULL,
  `verbatimLatitude` varchar(45) DEFAULT NULL,
  `verbatimLongitude` varchar(45) DEFAULT NULL,
  `UtmNorthing` varchar(45) DEFAULT NULL,
  `UtmEasting` varchar(45) DEFAULT NULL,
  `UtmZoning` varchar(45) DEFAULT NULL,
  `trsTownship` varchar(45) DEFAULT NULL,
  `trsRange` varchar(45) DEFAULT NULL,
  `trsSection` varchar(45) DEFAULT NULL,
  `trsSectionDetails` varchar(45) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferencedDate` datetime DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(255) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `elevationNumber` varchar(45) DEFAULT NULL,
  `elevationUnits` varchar(45) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text DEFAULT NULL,
  `disposition` varchar(250) DEFAULT NULL,
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `exsiccatiIdentifier` int(11) DEFAULT NULL,
  `exsiccatiNumber` varchar(45) DEFAULT NULL,
  `exsiccatiNotes` varchar(250) DEFAULT NULL,
  `paleoJSON` text DEFAULT NULL,
  `materialSampleJSON` text DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observeruid` int(11) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `dateEntered` datetime DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(45) DEFAULT NULL,
  `processingStatus` varchar(45) DEFAULT NULL,
  `tempfield01` text DEFAULT NULL,
  `tempfield02` text DEFAULT NULL,
  `tempfield03` text DEFAULT NULL,
  `tempfield04` text DEFAULT NULL,
  `tempfield05` text DEFAULT NULL,
  `tempfield06` text DEFAULT NULL,
  `tempfield07` text DEFAULT NULL,
  `tempfield08` text DEFAULT NULL,
  `tempfield09` text DEFAULT NULL,
  `tempfield10` text DEFAULT NULL,
  `tempfield11` text DEFAULT NULL,
  `tempfield12` text DEFAULT NULL,
  `tempfield13` text DEFAULT NULL,
  `tempfield14` text DEFAULT NULL,
  `tempfield15` text DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  KEY `FK_uploadspectemp_coll` (`collid`),
  KEY `Index_uploadspectemp_occid` (`occid`),
  KEY `Index_uploadspectemp_dbpk` (`dbpk`),
  KEY `Index_uploadspec_sciname` (`sciname`),
  KEY `Index_uploadspec_catalognumber` (`catalogNumber`),
  KEY `Index_uploadspec_othercatalognumbers` (`otherCatalogNumbers`),
  CONSTRAINT `FK_uploadspectemp_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `uploadtaxa`
--

CREATE TABLE `uploadtaxa` (
  `TID` int(10) unsigned DEFAULT NULL,
  `SourceId` int(10) unsigned DEFAULT NULL,
  `Family` varchar(50) DEFAULT NULL,
  `RankId` smallint(5) DEFAULT NULL,
  `RankName` varchar(45) DEFAULT NULL,
  `scinameinput` varchar(250) NOT NULL,
  `SciName` varchar(250) DEFAULT NULL,
  `UnitInd1` varchar(1) DEFAULT NULL,
  `UnitName1` varchar(50) DEFAULT NULL,
  `UnitInd2` varchar(1) DEFAULT NULL,
  `UnitName2` varchar(50) DEFAULT NULL,
  `UnitInd3` varchar(45) DEFAULT NULL,
  `UnitName3` varchar(35) DEFAULT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `InfraAuthor` varchar(100) DEFAULT NULL,
  `taxonomicStatus` varchar(45) DEFAULT NULL,
  `Acceptance` int(10) unsigned DEFAULT 1 COMMENT '0 = not accepted; 1 = accepted',
  `TidAccepted` int(10) unsigned DEFAULT NULL,
  `AcceptedStr` varchar(250) DEFAULT NULL,
  `SourceAcceptedId` int(10) unsigned DEFAULT NULL,
  `UnacceptabilityReason` varchar(24) DEFAULT NULL,
  `ParentTid` int(10) DEFAULT NULL,
  `ParentStr` varchar(250) DEFAULT NULL,
  `SourceParentId` int(10) unsigned DEFAULT NULL,
  `SecurityStatus` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality',
  `Source` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `vernacular` varchar(250) DEFAULT NULL,
  `vernlang` varchar(15) DEFAULT NULL,
  `Hybrid` varchar(50) DEFAULT NULL,
  `ErrorStatus` varchar(150) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `UNIQUE_sciname` (`SciName`,`RankId`,`Author`,`AcceptedStr`),
  KEY `sourceID_index` (`SourceId`),
  KEY `sourceAcceptedId_index` (`SourceAcceptedId`),
  KEY `sciname_index` (`SciName`),
  KEY `scinameinput_index` (`scinameinput`),
  KEY `parentStr_index` (`ParentStr`),
  KEY `acceptedStr_index` (`AcceptedStr`),
  KEY `unitname1_index` (`UnitName1`),
  KEY `sourceParentId_index` (`SourceParentId`),
  KEY `acceptance_index` (`Acceptance`)
) ENGINE=InnoDB;


--
-- Table structure for table `useraccesstokens`
--

CREATE TABLE `useraccesstokens` (
  `tokenID` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `device` varchar(50) DEFAULT NULL,
  `experationDate` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tokenID`),
  KEY `FK_useraccesstokens_uid_idx` (`uid`),
  CONSTRAINT `FK_useraccess_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `userroles`
--

CREATE TABLE `userroles` (
  `userRoleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL,
  `tableName` varchar(45) DEFAULT NULL,
  `tablePK` int(11) DEFAULT NULL,
  `secondaryVariable` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uidAssignedBy` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`userRoleID`),
  UNIQUE KEY `Unique_userroles` (`uid`,`role`,`tableName`,`tablePK`),
  KEY `FK_userroles_uid_idx` (`uid`),
  KEY `FK_usrroles_uid2_idx` (`uidAssignedBy`),
  KEY `Index_userroles_table` (`tableName`,`tablePK`),
  CONSTRAINT `FK_userrole_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_userrole_uid_assigned` FOREIGN KEY (`uidAssignedBy`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;


--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `department` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `regionOfInterest` varchar(45) DEFAULT NULL,
  `url` varchar(400) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `accessrRights` varchar(250) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `lastLoginDate` datetime DEFAULT NULL,
  `loginModified` datetime DEFAULT NULL,
  `validated` int(11) NOT NULL DEFAULT 0,
  `dynamicProperties` text DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`uid`),
  UNIQUE KEY `IX_users_email` (`email`,`lastName`) USING BTREE,
  UNIQUE KEY `UQ_users_username` (`username`)
) ENGINE=InnoDB;


--
-- Table structure for table `usertaxonomy`
--

CREATE TABLE `usertaxonomy` (
  `idusertaxonomy` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL DEFAULT 1,
  `editorStatus` varchar(45) DEFAULT NULL,
  `geographicScope` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idusertaxonomy`),
  UNIQUE KEY `usertaxonomy_UNIQUE` (`uid`,`tid`,`taxauthid`,`editorstatus`),
  KEY `FK_usertaxonomy_uid_idx` (`uid`),
  KEY `FK_usertaxonomy_tid_idx` (`tid`),
  KEY `FK_usertaxonomy_taxauthid_idx` (`taxauthid`),
  CONSTRAINT `FK_usertaxonomy_taxauthid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS=1;


#Data
#Load initial support data
#Set default admin user and permissions 
INSERT INTO `users` VALUES (1,'General','Administrator',NULL,NULL,NULL,NULL,NULL,'NA',NULL,'NA',NULL,'NA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin','*4ACFE3202A5FF5CF467898FC58AAB1D615029441',NULL,NULL,0,NULL,'2023-03-26 18:14:32');
INSERT INTO `userroles` VALUES (2,1,'SuperAdmin',NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:14:32');

#Prime adminlanguage table with default language data
INSERT INTO `adminlanguages` VALUES (1,'English','en',NULL,NULL,NULL,'2023-03-26 18:15:40'),(2,'German','de',NULL,NULL,NULL,'2023-03-26 18:15:40'),(3,'French','fr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(4,'Dutch','nl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(5,'Italian','it',NULL,NULL,NULL,'2023-03-26 18:15:40'),(6,'Spanish','es',NULL,NULL,NULL,'2023-03-26 18:15:40'),(7,'Polish','pl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(8,'Russian','ru',NULL,NULL,NULL,'2023-03-26 18:15:40'),(9,'Japanese','ja',NULL,NULL,NULL,'2023-03-26 18:15:40'),(10,'Portuguese','pt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(11,'Swedish','sv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(12,'Chinese','zh',NULL,NULL,NULL,'2023-03-26 18:15:40'),(13,'Catalan','ca',NULL,NULL,NULL,'2023-03-26 18:15:40'),(14,'Ukrainian','uk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(15,'Norwegian (Bokm´┐¢l)','no',NULL,NULL,NULL,'2023-03-26 18:15:40'),(16,'Finnish','fi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(17,'Vietnamese','vi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(18,'Czech','cs',NULL,NULL,NULL,'2023-03-26 18:15:40'),(19,'Hungarian','hu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(20,'Korean','ko',NULL,NULL,NULL,'2023-03-26 18:15:40'),(21,'Indonesian','id',NULL,NULL,NULL,'2023-03-26 18:15:40'),(22,'Turkish','tr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(23,'Romanian','ro',NULL,NULL,NULL,'2023-03-26 18:15:40'),(24,'Persian','fa',NULL,NULL,NULL,'2023-03-26 18:15:40'),(25,'Arabic','ar',NULL,NULL,NULL,'2023-03-26 18:15:40'),(26,'Danish','da',NULL,NULL,NULL,'2023-03-26 18:15:40'),(27,'Esperanto','eo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(28,'Serbian','sr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(29,'Lithuanian','lt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(30,'Slovak','sk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(31,'Malay','ms',NULL,NULL,NULL,'2023-03-26 18:15:40'),(32,'Hebrew','he',NULL,NULL,NULL,'2023-03-26 18:15:40'),(33,'Bulgarian','bg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(34,'Slovenian','sl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(35,'Volap´┐¢k','vo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(36,'Kazakh','kk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(37,'Waray-Waray','war',NULL,NULL,NULL,'2023-03-26 18:15:40'),(38,'Basque','eu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(39,'Croatian','hr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(40,'Hindi','hi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(41,'Estonian','et',NULL,NULL,NULL,'2023-03-26 18:15:40'),(42,'Azerbaijani','az',NULL,NULL,NULL,'2023-03-26 18:15:40'),(43,'Galician','gl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(44,'Simple English','simple',NULL,NULL,NULL,'2023-03-26 18:15:40'),(45,'Norwegian (Nynorsk)','nn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(46,'Thai','th',NULL,NULL,NULL,'2023-03-26 18:15:40'),(47,'Newar / Nepal Bhasa','new',NULL,NULL,NULL,'2023-03-26 18:15:40'),(48,'Greek','el',NULL,NULL,NULL,'2023-03-26 18:15:40'),(49,'Aromanian','roa-rup',NULL,NULL,NULL,'2023-03-26 18:15:40'),(50,'Latin','la',NULL,NULL,NULL,'2023-03-26 18:15:40'),(51,'Occitan','oc',NULL,NULL,NULL,'2023-03-26 18:15:40'),(52,'Tagalog','tl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(53,'Haitian','ht',NULL,NULL,NULL,'2023-03-26 18:15:40'),(54,'Macedonian','mk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(55,'Georgian','ka',NULL,NULL,NULL,'2023-03-26 18:15:40'),(56,'Serbo-Croatian','sh',NULL,NULL,NULL,'2023-03-26 18:15:40'),(57,'Telugu','te',NULL,NULL,NULL,'2023-03-26 18:15:40'),(58,'Piedmontese','pms',NULL,NULL,NULL,'2023-03-26 18:15:40'),(59,'Cebuano','ceb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(60,'Tamil','ta',NULL,NULL,NULL,'2023-03-26 18:15:40'),(61,'Belarusian (Tara´┐¢kievica)','be-x-old',NULL,NULL,NULL,'2023-03-26 18:15:40'),(62,'Breton','br',NULL,NULL,NULL,'2023-03-26 18:15:40'),(63,'Latvian','lv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(64,'Javanese','jv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(65,'Albanian','sq',NULL,NULL,NULL,'2023-03-26 18:15:40'),(66,'Belarusian','be',NULL,NULL,NULL,'2023-03-26 18:15:40'),(67,'Marathi','mr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(68,'Welsh','cy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(69,'Luxembourgish','lb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(70,'Icelandic','is',NULL,NULL,NULL,'2023-03-26 18:15:40'),(71,'Bosnian','bs',NULL,NULL,NULL,'2023-03-26 18:15:40'),(72,'Yoruba','yo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(73,'Malagasy','mg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(74,'Aragonese','an',NULL,NULL,NULL,'2023-03-26 18:15:40'),(75,'Bishnupriya Manipuri','bpy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(76,'Lombard','lmo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(77,'West Frisian','fy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(78,'Bengali','bn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(79,'Ido','io',NULL,NULL,NULL,'2023-03-26 18:15:40'),(80,'Swahili','sw',NULL,NULL,NULL,'2023-03-26 18:15:40'),(81,'Gujarati','gu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(82,'Malayalam','ml',NULL,NULL,NULL,'2023-03-26 18:15:40'),(83,'Western Panjabi','pnb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(84,'Afrikaans','af',NULL,NULL,NULL,'2023-03-26 18:15:40'),(85,'Low Saxon','nds',NULL,NULL,NULL,'2023-03-26 18:15:40'),(86,'Sicilian','scn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(87,'Urdu','ur',NULL,NULL,NULL,'2023-03-26 18:15:40'),(88,'Kurdish','ku',NULL,NULL,NULL,'2023-03-26 18:15:40'),(89,'Cantonese','zh-yue',NULL,NULL,NULL,'2023-03-26 18:15:40'),(90,'Armenian','hy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(91,'Quechua','qu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(92,'Sundanese','su',NULL,NULL,NULL,'2023-03-26 18:15:40'),(93,'Nepali','ne',NULL,NULL,NULL,'2023-03-26 18:15:40'),(94,'Zazaki','diq',NULL,NULL,NULL,'2023-03-26 18:15:40'),(95,'Asturian','ast',NULL,NULL,NULL,'2023-03-26 18:15:40'),(96,'Tatar','tt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(97,'Neapolitan','nap',NULL,NULL,NULL,'2023-03-26 18:15:40'),(98,'Irish','ga',NULL,NULL,NULL,'2023-03-26 18:15:40'),(99,'Chuvash','cv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(100,'Samogitian','bat-smg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(101,'Walloon','wa',NULL,NULL,NULL,'2023-03-26 18:15:40'),(102,'Amharic','am',NULL,NULL,NULL,'2023-03-26 18:15:40'),(103,'Kannada','kn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(104,'Alemannic','als',NULL,NULL,NULL,'2023-03-26 18:15:40'),(105,'Buginese','bug',NULL,NULL,NULL,'2023-03-26 18:15:40'),(106,'Burmese','my',NULL,NULL,NULL,'2023-03-26 18:15:40'),(107,'Interlingua','ia',NULL,NULL,NULL,'2023-03-26 18:15:40');

#Prime controlled vocabulary tables  with default data
INSERT INTO `ctcontrolvocab` VALUES (1,NULL,'Occurrence Relationship Terms',NULL,NULL,'omoccurassociations','relationship',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2020-12-03 04:35:38'),(2,NULL,'Occurrence Relationship subTypes',NULL,NULL,'omoccurassociations','subType',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2020-12-03 05:56:13'),(3,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','sampleType',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(4,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','disposition',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(5,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','preservationType',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(6,NULL,'Material Sample Type',NULL,NULL,'ommaterialsampleextended','fieldName',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53');
INSERT INTO `ctcontrolvocabterm` VALUES (1,1,NULL,'subsampleOf',NULL,'originatingSampleOf',NULL,'a sample or occurrence that was subsequently derived from an originating sample',NULL,'has part: http://purl.obolibrary.org/obo/BFO_0000050',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:36:51'),(2,1,NULL,'partOf',NULL,'partOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:38:32'),(3,1,NULL,'siblingOf',NULL,'siblingOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:38:32'),(4,1,NULL,'originatingSampleOf',NULL,'subsampleOf',NULL,'a sample or occurrence that is the originator of a subsequently modified or partial sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,'originatingSourceOf ??  It isn\'t necessarily a sample.  Could be an observation or occurrence or individual etc',NULL,NULL,NULL,'2020-12-03 06:27:02'),(5,1,NULL,'sharesOriginatingSample',NULL,'sharesOriginatingSample',NULL,'two samples or occurrences that were subsequently derived from the same originating sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(6,2,NULL,'tissue',NULL,NULL,NULL,'a tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(7,2,NULL,'blood',NULL,NULL,NULL,'a blood-tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(8,2,NULL,'fecal',NULL,NULL,NULL,'a fecal sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(9,2,NULL,'hair',NULL,NULL,NULL,'a hair sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(10,2,NULL,'genetic',NULL,NULL,NULL,'a genetic extraction sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(11,1,NULL,'derivedFromSameIndividual',NULL,'derivedFromSameIndividual',NULL,'a sample or occurrence that is derived from the same biological individual as another occurrence or sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(12,1,NULL,'analyticalStandardOf',NULL,'hasAnalyticalStandard',NULL,'a sample or occurrence that serves as an analytical standard or control for another occurrence or sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(13,1,NULL,'hasAnalyticalStandard',NULL,'analyticalStandardof',NULL,'a sample or occurrence that has an available analytical standard or control',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(14,1,NULL,'hasHost',NULL,'hostOf',NULL,'X \'has host\' y if and only if: x is an organism, y is an organism, and x can live on the surface of or within the body of y',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002454',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(15,1,NULL,'hostOf',NULL,'hasHost',NULL,'X is \'Host of\' y if and only if: x is an organism, y is an organism, and y can live on the surface of or within the body of x',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002453',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(16,1,NULL,'ecologicallyOccursWith',NULL,'ecologicallyOccursWith',NULL,'An interaction relationship describing an occurrence occurring with another organism in the same time and space or same environment',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0008506',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(17,3,NULL,'tissue',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(18,3,NULL,'culture strain',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(19,3,NULL,'specimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(20,3,NULL,'DNA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(21,3,NULL,'RNA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(22,3,NULL,'Protein',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(23,3,NULL,'Skin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(24,3,NULL,'Skull',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(25,3,NULL,'liver',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(26,4,NULL,'being processed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(27,4,NULL,'in collection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(28,4,NULL,'deaccessioned',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(29,4,NULL,'consumed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(30,4,NULL,'discarded',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(31,4,NULL,'missing',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(32,4,NULL,'on exhibit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(33,5,NULL,'alsever\'s solution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(34,5,NULL,'arsenic',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(35,5,NULL,'Bouin\'s solution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(36,5,NULL,'buffer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(37,5,NULL,'cleared',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(38,5,NULL,'carbonization',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(39,5,NULL,'DMSO/EDTA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(40,5,NULL,'DESS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(41,5,NULL,'DMSO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(42,5,NULL,'desiccated',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(43,5,NULL,'dry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(44,5,NULL,'ethanol 95%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(45,5,NULL,'ethanol 80%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(46,5,NULL,'ethanol 75%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(47,5,NULL,'ethanol 70%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(48,5,NULL,'EDTA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(49,5,NULL,'sampleDesignation',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(50,5,NULL,'Frozen -20┬░C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(51,5,NULL,'Frozen -80┬░C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(52,5,NULL,'Frozen -196┬░C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(53,5,NULL,'Liquid Nitrogen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(54,6,NULL,'concentration',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/concentration',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(55,6,NULL,'concentrationMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationConcentrationAndRatios',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(56,6,NULL,'ratioOfAbsorbance260_230',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_230',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(57,6,NULL,'ratioOfAbsorbance260_280',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_280',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(58,6,NULL,'volume',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/volume',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(59,6,NULL,'weight',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/weight',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(60,6,NULL,'weightMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationWeight',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(61,6,NULL,'purificationMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/purificationMethod',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(62,6,NULL,'quality',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/quality',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(63,6,NULL,'qualityRemarks',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/qualityRemarks',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(64,6,NULL,'qualityCheckDate',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/qualityCheckDate',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(65,6,NULL,'sieving',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/sieving',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(66,6,NULL,'dnaHybridization',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/DNADNAHybridization',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(67,6,NULL,'dnaMeltingPoint',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/DNAMeltingPoint',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(68,6,NULL,'estimatedSize',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/estimated_size',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(69,6,NULL,'poolDnaExtracts',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/pool_dna_extracts',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(70,6,NULL,'sampleDesignation',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/sampleDesignation',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53');

#Prime occurrence relationships table with default data
INSERT INTO `ctrelationshiptypes` VALUES ('Child of','Parent of','Children'),('Could be','Confused with','Confused with'),('Spouse of','Spouse of','Married to'),('Student of','Teacher of','Students');
INSERT INTO `ctnametypes` VALUES ('Also Known As'),('First Initials Last'),('First Last'),('Full Name'),('Initials Last Name'),('Last Name, Initials'),('Standard Abbreviation'),('Standard DwC List');

#Prime image tags table with default data
INSERT INTO `imagetagkey` VALUES ('Diagnostic',NULL,'Diagnostic','Image contains a diagnostic character.','Image contains a diagnostic character.',NULL,NULL,70,'2023-03-26 18:15:40'),('Handwriting',NULL,'Handwritten','Image has handwritten label text.','Image has handwritten label text.',NULL,NULL,40,'2023-03-26 18:15:40'),('HasIDLabel',NULL,'Annotation','Image shows an annotation/identification label.','Image shows an annotation/identification label.',NULL,NULL,20,'2023-03-26 18:15:40'),('HasLabel',NULL,'Label','Image shows label data.','Image shows label data.',NULL,NULL,10,'2023-03-26 18:15:40'),('HasOrganism',NULL,'Organism','Image shows an organism.','Image shows an organism.',NULL,NULL,0,'2023-03-26 18:15:40'),('HasProblem',NULL,'QC Problem','There is a problem with this image.','There is a problem with this image.',NULL,NULL,60,'2023-03-26 18:15:40'),('ImageOfAdult',NULL,'Adult','Image contains the adult organism.','Image contains the adult organism.',NULL,NULL,80,'2023-03-26 18:15:40'),('ImageOfImmature',NULL,'Immature','Image contains the immature organism.','Image contains the immature organism.',NULL,NULL,90,'2023-03-26 18:15:40'),('ShowsHabitat',NULL,'Habitat','Field image of habitat.','Field image of habitat.',NULL,NULL,50,'2023-03-26 18:15:40'),('TypedText',NULL,'Typed/Printed','Image has typed or printed text.','Image has typed or printed text.',NULL,NULL,30,'2023-03-26 18:15:40');

#Prime paleo tables with default data
INSERT INTO `omoccurpaleogts` VALUES (1,'Precambrian',10,'supereon',NULL,'2023-03-26 18:16:18'),(2,'Archean',20,'eon',1,'2023-03-26 18:16:18'),(3,'Hadean',20,'eon',1,'2023-03-26 18:16:18'),(4,'Phanerozoic',20,'eon',1,'2023-03-26 18:16:18'),(5,'Proterozoic',20,'eon',1,'2023-03-26 18:16:18'),(9,'Eoarchean',30,'era',2,'2023-03-26 18:16:18'),(10,'Paleoarchean',30,'era',2,'2023-03-26 18:16:18'),(11,'Mesoarchean',30,'era',2,'2023-03-26 18:16:18'),(12,'Neoarchean',30,'era',2,'2023-03-26 18:16:18'),(13,'Paleozoic',30,'era',4,'2023-03-26 18:16:18'),(14,'Mesozoic',30,'era',4,'2023-03-26 18:16:18'),(15,'Cenozoic',30,'era',4,'2023-03-26 18:16:18'),(16,'Paleoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(17,'Mesoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(18,'Neoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(24,'Cambrian',40,'period',13,'2023-03-26 18:16:18'),(25,'Ordovician',40,'period',13,'2023-03-26 18:16:18'),(26,'Silurian',40,'period',13,'2023-03-26 18:16:18'),(27,'Devonian',40,'period',13,'2023-03-26 18:16:18'),(28,'Carboniferous',40,'period',13,'2023-03-26 18:16:18'),(29,'Permian',40,'period',13,'2023-03-26 18:16:18'),(30,'Triassic',40,'period',14,'2023-03-26 18:16:18'),(31,'Jurassic',40,'period',14,'2023-03-26 18:16:18'),(32,'Cretaceous',40,'period',14,'2023-03-26 18:16:18'),(33,'Paleogene',40,'period',15,'2023-03-26 18:16:18'),(34,'Neogene',40,'period',15,'2023-03-26 18:16:18'),(35,'Quaternary',40,'period',15,'2023-03-26 18:16:18'),(36,'Siderian',40,'period',16,'2023-03-26 18:16:18'),(37,'Rhyacian',40,'period',16,'2023-03-26 18:16:18'),(38,'Orosirian',40,'period',16,'2023-03-26 18:16:18'),(39,'Statherian',40,'period',16,'2023-03-26 18:16:18'),(40,'Calymmian',40,'period',17,'2023-03-26 18:16:18'),(41,'Ectasian',40,'period',17,'2023-03-26 18:16:18'),(42,'Stenian',40,'period',17,'2023-03-26 18:16:18'),(43,'Tonian',40,'period',18,'2023-03-26 18:16:18'),(44,'Gryogenian',40,'period',18,'2023-03-26 18:16:18'),(45,'Ediacaran',40,'period',18,'2023-03-26 18:16:18'),(55,'Lower Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(56,'Middle Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(57,'Upper Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(58,'Lower Ordovician',50,'epoch',25,'2023-03-26 18:16:18'),(59,'Middle Ordovician',50,'epoch',25,'2023-03-26 18:16:18'),(60,'Upper Ordivician',50,'epoch',25,'2023-03-26 18:16:18'),(61,'Llandovery',50,'epoch',26,'2023-03-26 18:16:18'),(62,'Wenlock',50,'epoch',26,'2023-03-26 18:16:18'),(63,'Ludlow',50,'epoch',26,'2023-03-26 18:16:18'),(64,'Pridoli',50,'epoch',26,'2023-03-26 18:16:18'),(65,'Lower Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(66,'Middle Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(67,'Upper Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(68,'Mississippian',40,'period',13,'2023-03-26 18:16:18'),(69,'Pennsylvanian',40,'period',13,'2023-03-26 18:16:18'),(70,'Cisuralian',50,'epoch',29,'2023-03-26 18:16:18'),(71,'Guadalupian',50,'epoch',29,'2023-03-26 18:16:18'),(72,'Lopingian',50,'epoch',29,'2023-03-26 18:16:18'),(73,'Lower Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(74,'Middle Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(75,'Upper Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(76,'Lower Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(77,'Middle Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(78,'Upper Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(79,'Lower Cretaceous',50,'epoch',32,'2023-03-26 18:16:18'),(80,'Upper Cretaceous',50,'epoch',32,'2023-03-26 18:16:18'),(81,'Paleocene',50,'epoch',33,'2023-03-26 18:16:18'),(82,'Eocene',50,'epoch',33,'2023-03-26 18:16:18'),(83,'Oligocene',50,'epoch',33,'2023-03-26 18:16:18'),(84,'Miocene',50,'epoch',34,'2023-03-26 18:16:18'),(85,'Pliocene',50,'epoch',34,'2023-03-26 18:16:18'),(86,'Pleistocene',50,'epoch',35,'2023-03-26 18:16:18'),(87,'Holocene',50,'epoch',35,'2023-03-26 18:16:18'),(118,'Tremadocian',60,'age',58,'2023-03-26 18:16:18'),(119,'Floian',60,'age',58,'2023-03-26 18:16:18'),(120,'Dapingian',60,'age',59,'2023-03-26 18:16:18'),(121,'Darriwilian',60,'age',59,'2023-03-26 18:16:18'),(122,'Sandbian',60,'age',60,'2023-03-26 18:16:18'),(123,'Katian',60,'age',60,'2023-03-26 18:16:18'),(124,'Hirnantian',60,'age',60,'2023-03-26 18:16:18'),(125,'Rhuddanian',60,'age',61,'2023-03-26 18:16:18'),(126,'Aeronian',60,'age',61,'2023-03-26 18:16:18'),(127,'Telychian',60,'age',61,'2023-03-26 18:16:18'),(128,'Sheinwoodian',60,'age',62,'2023-03-26 18:16:18'),(129,'Homerian',60,'age',62,'2023-03-26 18:16:18'),(130,'Gorstian',60,'age',63,'2023-03-26 18:16:18'),(131,'Ludfordian',60,'age',63,'2023-03-26 18:16:18'),(132,'Lochkovian',60,'age',65,'2023-03-26 18:16:18'),(133,'Pragian',60,'age',65,'2023-03-26 18:16:18'),(134,'Emsian',60,'age',65,'2023-03-26 18:16:18'),(135,'Eifelian',60,'age',66,'2023-03-26 18:16:18'),(136,'Givetian',60,'age',66,'2023-03-26 18:16:18'),(137,'Frasnian',60,'age',67,'2023-03-26 18:16:18'),(138,'Famennian',60,'age',67,'2023-03-26 18:16:18'),(139,'Lower Mississippian',60,'age',68,'2023-03-26 18:16:18'),(140,'Middle Mississippian',60,'age',68,'2023-03-26 18:16:18'),(141,'Upper Mississippian',60,'age',68,'2023-03-26 18:16:18'),(142,'Lower Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(143,'Middle Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(144,'Upper Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(145,'Asselian',60,'age',70,'2023-03-26 18:16:18'),(146,'Sakmarian',60,'age',70,'2023-03-26 18:16:18'),(147,'Artinskian',60,'age',70,'2023-03-26 18:16:18'),(148,'Kungurian',60,'age',70,'2023-03-26 18:16:18'),(149,'Roadian',60,'age',71,'2023-03-26 18:16:18'),(150,'Wordian',60,'age',71,'2023-03-26 18:16:18'),(151,'Capitanian',60,'age',71,'2023-03-26 18:16:18'),(152,'Wuchiapingian',60,'age',72,'2023-03-26 18:16:18'),(153,'Changhsingian',60,'age',72,'2023-03-26 18:16:18'),(154,'Induan',60,'age',73,'2023-03-26 18:16:18'),(155,'Olenekian',60,'age',73,'2023-03-26 18:16:18'),(156,'Anisian',60,'age',74,'2023-03-26 18:16:18'),(157,'Ladinian',60,'age',74,'2023-03-26 18:16:18'),(158,'Carnian',60,'age',75,'2023-03-26 18:16:18'),(159,'Norian',60,'age',75,'2023-03-26 18:16:18'),(160,'Rhaetian',60,'age',75,'2023-03-26 18:16:18'),(161,'Hettangian',60,'age',76,'2023-03-26 18:16:18'),(162,'Sinemurian',60,'age',76,'2023-03-26 18:16:18'),(163,'Pliensbachian',60,'age',76,'2023-03-26 18:16:18'),(164,'Toarcian',60,'age',76,'2023-03-26 18:16:18'),(165,'Aalenian',60,'age',77,'2023-03-26 18:16:18'),(166,'Bajocian',60,'age',77,'2023-03-26 18:16:18'),(167,'Bathonian',60,'age',77,'2023-03-26 18:16:18'),(168,'Callovian',60,'age',77,'2023-03-26 18:16:18'),(169,'Oxfordian',60,'age',78,'2023-03-26 18:16:18'),(170,'Kimmeridgian',60,'age',78,'2023-03-26 18:16:18'),(171,'Tithonian',60,'age',78,'2023-03-26 18:16:18'),(172,'Berriasian',60,'age',79,'2023-03-26 18:16:18'),(173,'Valanginian',60,'age',79,'2023-03-26 18:16:18'),(174,'Hauterivian',60,'age',79,'2023-03-26 18:16:18'),(175,'Barremian',60,'age',79,'2023-03-26 18:16:18'),(176,'Aptian',60,'age',79,'2023-03-26 18:16:18'),(177,'Albian',60,'age',79,'2023-03-26 18:16:18'),(178,'Cenomanian',60,'age',80,'2023-03-26 18:16:18'),(179,'Turonian',60,'age',80,'2023-03-26 18:16:18'),(180,'Coniacian',60,'age',80,'2023-03-26 18:16:18'),(181,'Santonian',60,'age',80,'2023-03-26 18:16:18'),(182,'Campanian',60,'age',80,'2023-03-26 18:16:18'),(183,'Maastrichtian',60,'age',80,'2023-03-26 18:16:18'),(184,'Danian',60,'age',81,'2023-03-26 18:16:18'),(185,'Selandian',60,'age',81,'2023-03-26 18:16:18'),(186,'Thanetian',60,'age',81,'2023-03-26 18:16:18'),(187,'Ypresian',60,'age',82,'2023-03-26 18:16:18'),(188,'Lutetian',60,'age',82,'2023-03-26 18:16:18'),(189,'Bartonian',60,'age',82,'2023-03-26 18:16:18'),(190,'Priabonian',60,'age',82,'2023-03-26 18:16:18'),(191,'Rupelian',60,'age',83,'2023-03-26 18:16:18'),(192,'Chattian',60,'age',83,'2023-03-26 18:16:18'),(193,'Aquitanian',60,'age',84,'2023-03-26 18:16:18'),(194,'Burdigalian',60,'age',84,'2023-03-26 18:16:18'),(195,'Langhian',60,'age',84,'2023-03-26 18:16:18'),(196,'Serravallian',60,'age',84,'2023-03-26 18:16:18'),(197,'Tortonian',60,'age',84,'2023-03-26 18:16:18'),(198,'Messinian',60,'age',84,'2023-03-26 18:16:18'),(199,'Zanclean',60,'age',85,'2023-03-26 18:16:18'),(200,'Piacenzian',60,'age',85,'2023-03-26 18:16:18'),(201,'Gelasian',60,'age',86,'2023-03-26 18:16:18'),(202,'Calabrian',60,'age',86,'2023-03-26 18:16:18'),(203,'Middle Pleistocene',60,'age',86,'2023-03-26 18:16:18'),(204,'Upper Pleistocene',60,'age',86,'2023-03-26 18:16:18');

#Prime Reference tables with default data
INSERT INTO `referencetype` VALUES (1,'Generic',NULL,'Title','SecondaryTitle','PlacePublished','Publisher','Volume','NumberVolumes','Number','Pages','Section','TertiaryTitle','Edition','Date','TypeWork','ShortTitle','AlternativeTitle','Isbn_Issn','Figures',NULL,'2014-06-17 07:27:12'),(2,'Journal Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title','Alt. Jour.',NULL,'Figures',NULL,'2014-06-17 07:27:12'),(3,'Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(4,'Book Section',NULL,'Title','Book Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(5,'Manuscript',NULL,'Title','Collection Title','City',NULL,NULL,NULL,'Number','Pages',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(6,'Edited Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(7,'Magazine Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(8,'Newspaper Article',NULL,'Title','Periodical Title','City',NULL,NULL,NULL,NULL,'Pages','Section',NULL,'Edition','Date','Type Art.','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(9,'Conference Proceedings',NULL,'Title','Conf. Name','Conf. Loc.','Publisher','Volume','No. Vols.',NULL,'Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(10,'Thesis',NULL,'Title','Academic Dept.','City','University',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Thesis Type','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(11,'Report',NULL,'Title',NULL,'City','Institution',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,'Rpt. No.','Figures',NULL,'2014-06-17 07:27:12'),(12,'Personal Communication',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(13,'Computer Program',NULL,'Title',NULL,'City','Publisher','Version',NULL,NULL,NULL,NULL,NULL,'Platform','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(14,'Electronic Source',NULL,'Title',NULL,NULL,'Publisher','Access Year','Extent','Acc. Date',NULL,NULL,NULL,'Edition','Date','Medium','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(15,'Audiovisual Material',NULL,'Title','Collection Title','City','Publisher',NULL,NULL,'Number',NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(16,'Film or Broadcast',NULL,'Title','Series Title','City','Distributor',NULL,NULL,NULL,'Length',NULL,NULL,NULL,'Date','Medium','Short Title',NULL,'ISBN',NULL,NULL,'2014-06-17 07:27:12'),(17,'Artwork',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(18,'Map',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,'Scale',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(19,'Patent',NULL,'Title','Published Source','Country','Assignee','Volume','No. Vols.','Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,'Pat. No.','Figures',NULL,'2014-06-17 07:27:12'),(20,'Hearing',NULL,'Title','Committee','City','Publisher',NULL,NULL,'Doc. No.','Pages',NULL,'Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(21,'Bill',NULL,'Title','Code',NULL,NULL,'Code Volume',NULL,'Bill No.','Pages','Section','Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(22,'Statute',NULL,'Title','Code',NULL,NULL,'Code Number',NULL,'Law No.','1st Pg.','Section',NULL,'Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(23,'Case',NULL,'Title',NULL,NULL,'Court','Reporter Vol.',NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(24,'Figure',NULL,'Title','Source Program',NULL,NULL,NULL,'-',NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(25,'Chart or Table',NULL,'Title','Source Program',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(26,'Equation',NULL,'Title','Source Program',NULL,NULL,'Volume',NULL,'Number',NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(27,'Book Series',1,'Title',NULL,'City','Publisher',NULL,'No. Vols.',NULL,'Pages',NULL,NULL,'Edition','Date',NULL,NULL,NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(28,'Determination',NULL,'Title',NULL,NULL,'Institution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(29,'Sub-Reference',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(30,'Periodical',1,'Title',NULL,'City',NULL,'Volume',NULL,'Issue',NULL,NULL,NULL,'Edition','Date',NULL,'Short Title','Alt. Jour.',NULL,NULL,NULL,'2014-10-31 04:34:44'),(31,'Web Page',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-10-31 04:37:12');

#Prime taxa tables with default parent nodes (e.g. base node and kingdoms) and other support data
INSERT INTO `taxauthority` VALUES (1,1,'Central Thesaurus',NULL,NULL,NULL,NULL,NULL,NULL,1,'2023-03-26 18:14:32');
INSERT INTO `taxonunits` VALUES (24,'Organism',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(25,'Organism',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(26,'Organism',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(27,'Organism',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(28,'Organism',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(29,'Organism',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(30,'Organism',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(31,'Organism',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(32,'Organism',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(33,'Organism',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(34,'Organism',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(35,'Organism',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(36,'Organism',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(37,'Organism',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(38,'Organism',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(39,'Organism',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(40,'Organism',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(41,'Organism',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(42,'Organism',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(43,'Organism',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(44,'Organism',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(45,'Organism',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(46,'Organism',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(47,'Organism',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(48,'Organism',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(49,'Monera',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(50,'Monera',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(51,'Monera',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(52,'Monera',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(53,'Monera',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(54,'Monera',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(55,'Monera',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(56,'Monera',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(57,'Monera',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(58,'Monera',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(59,'Monera',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(60,'Monera',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(61,'Monera',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(62,'Monera',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(63,'Monera',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(64,'Monera',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(65,'Monera',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(66,'Monera',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(67,'Protista',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(68,'Protista',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(69,'Protista',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(70,'Protista',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(71,'Protista',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(72,'Protista',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(73,'Protista',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(74,'Protista',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(75,'Protista',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(76,'Protista',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(77,'Protista',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(78,'Protista',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(79,'Protista',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(80,'Protista',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(81,'Protista',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(82,'Protista',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(83,'Protista',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(84,'Protista',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(85,'Plantae',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(86,'Plantae',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(87,'Plantae',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(88,'Plantae',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(89,'Plantae',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(90,'Plantae',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(91,'Plantae',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(92,'Plantae',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(93,'Plantae',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(94,'Plantae',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(95,'Plantae',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(96,'Plantae',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(97,'Plantae',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(98,'Plantae',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(99,'Plantae',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(100,'Plantae',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(101,'Plantae',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(102,'Plantae',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(103,'Plantae',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(104,'Plantae',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(105,'Plantae',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(106,'Plantae',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(107,'Plantae',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(108,'Plantae',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(109,'Plantae',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(110,'Fungi',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(111,'Fungi',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(112,'Fungi',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(113,'Fungi',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(114,'Fungi',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(115,'Fungi',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(116,'Fungi',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(117,'Fungi',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(118,'Fungi',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(119,'Fungi',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(120,'Fungi',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(121,'Fungi',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(122,'Fungi',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(123,'Fungi',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(124,'Fungi',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(125,'Fungi',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(126,'Fungi',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(127,'Fungi',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(128,'Fungi',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(129,'Fungi',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(130,'Fungi',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(131,'Fungi',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(132,'Fungi',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(133,'Fungi',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(134,'Fungi',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(135,'Animalia',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(136,'Animalia',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(137,'Animalia',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(138,'Animalia',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(139,'Animalia',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(140,'Animalia',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(141,'Animalia',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(142,'Animalia',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(143,'Animalia',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(144,'Animalia',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(145,'Animalia',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(146,'Animalia',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(147,'Animalia',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(148,'Animalia',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(149,'Animalia',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(150,'Animalia',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(151,'Animalia',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(152,'Animalia',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32');
INSERT INTO `taxa` VALUES (1,NULL,1,'Organism',NULL,'Organism',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(2,NULL,10,'Monera',NULL,'Monera',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(3,NULL,10,'Protista',NULL,'Protista',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(4,NULL,10,'Plantae',NULL,'Plantae',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(5,NULL,10,'Fungi',NULL,'Fungi',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(6,NULL,10,'Animalia',NULL,'Animalia',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32');
INSERT INTO `taxstatus` VALUES (1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(2,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(3,3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(4,4,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(5,5,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(6,6,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32');

#Update schema table
INSERT IGNORE INTO schemaversion (versionnumber) values ("3.0");

#Create default thesaurus
INSERT INTO `geographicthesaurus` (`geoThesID`, `geoterm`, `abbreviation`, `iso2`, `iso3`, `numcode`, `category`, `geoLevel`, `termstatus`, `acceptedID`, `parentID`) VALUES(1, "Africa", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(2, "Antarctica", NULL, NULL, "ATA", 10, NULL, 40, NULL, NULL, NULL),(3, "Asia", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(4, "Europe", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(5, "North America", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(6, "Oceania", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(7, "South America", NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL),(8, "Aruba", NULL, "AW", "ABW", 533, NULL, 50, NULL, NULL, 7),(9, "Afghanistan", NULL, "AF", "AFG", 4, NULL, 50, NULL, NULL, 3),(10, "Angola", NULL, "AO", "AGO", 24, NULL, 50, NULL, NULL, 1),(11, "Anguilla", NULL, "AI", "AIA", 660, NULL, 50, NULL, NULL, 5),(12, "Albania", NULL, "AL", "ALB", 8, NULL, 50, NULL, NULL, 4),(13, "Andorra", NULL, "AD", "AND", 20, NULL, 50, NULL, NULL, 4),(14, "United Arab Emirates", NULL, "AE", "ARE", 784, NULL, 50, NULL, NULL, 3),(15, "Argentina", NULL, "AR", "ARG", 32, NULL, 50, NULL, NULL, 7),(16, "Armenia", NULL, "AM", "ARM", 51, NULL, 50, NULL, NULL, 3),(17, "American Samoa", NULL, "AS", "ASM", 16, NULL, 50, NULL, NULL, 6),(18, "Antigua and Barbuda", NULL, "AG", "ATG", 28, NULL, 50, NULL, NULL, 5),(19, "Australia", NULL, "AU", "AUS", 36, NULL, 50, NULL, NULL, 6),(20, "Austria", NULL, "AT", "AUT", 40, NULL, 50, NULL, NULL, 4),(21, "Azerbaijan", NULL, "AZ", "AZE", 31, NULL, 50, NULL, NULL, 3),(22, "Burundi", NULL, "BI", "BDI", 108, NULL, 50, NULL, NULL, 1),(23, "Belgium", NULL, "BE", "BEL", 56, NULL, 50, NULL, NULL, 4),(24, "Benin", NULL, "BJ", "BEN", 204, NULL, 50, NULL, NULL, 1),(25, "Bonaire, Sint Eustatius, and Saba", NULL, "BQ", "BES", 535, NULL, 50, NULL, NULL, 7),(26, "Burkina Faso", NULL, "BF", "BFA", 854, NULL, 50, NULL, NULL, 1),(27, "Bangledesh", NULL, "BD", "BGD", 50, NULL, 50, NULL, NULL, 3),(28, "Bulgaria", NULL, "BG", "BGR", 100, NULL, 50, NULL, NULL, 4),(29, "Bahrain", NULL, "BH", "BHR", 48, NULL, 50, NULL, NULL, 3),(30, "Bahamas", NULL, "BS", "BHS", 44, NULL, 50, NULL, NULL, 5),(31, "Bosnia and Herzegovina", NULL, "BA", "BIH", 70, NULL, 50, NULL, NULL, 4),(32, "Saint Barthelemy", NULL, "BL", "BLM", 652, NULL, 50, NULL, NULL, 5),(33, "Belarus", NULL, "BY", "BLR", 112, NULL, 50, NULL, NULL, 4),(34, "Belize", NULL, "BZ", "BLZ", 84, NULL, 50, NULL, NULL, 7),(35, "Bermuda", NULL, "BM", "BMU", 60, NULL, 50, NULL, NULL, 5),(36, "Bolivia", NULL, "BO", "BOL", 68, NULL, 50, NULL, NULL, 7),(37, "Brazil", NULL, "BR", "BRA", 76, NULL, 50, NULL, NULL, 7),(38, "Barbados", NULL, "BB", "BRB", 52, NULL, 50, NULL, NULL, 5),(39, "Brunei Darussalam", NULL, "BN", "BRN", 96, NULL, 50, NULL, NULL, 3),(40, "Bhutan", NULL, "BT", "BTN", 64, NULL, 50, NULL, NULL, 3),(41, "Botswana", NULL, "BW", "BWA", 72, NULL, 50, NULL, NULL, 1),(42, "Central African Republic", NULL, "CF", "CAF", 140, NULL, 50, NULL, NULL, 1),(43, "Canada", NULL, "CA", "CAN", 124, NULL, 50, NULL, NULL, 5),(44, "Switzerland", NULL, "CH", "CHE", 756, NULL, 50, NULL, NULL, 4),(45, "Chile", NULL, "CL", "CHL", 152, NULL, 50, NULL, NULL, 7),(46, "China", NULL, "CN", "CHN", 156, NULL, 50, NULL, NULL, 3),(47, "Côte d’Ivoire", NULL, "CI", "CIV", 384, NULL, 50, NULL, NULL, 1),(48, "Cameroon", NULL, "CM", "CMR", 120, NULL, 50, NULL, NULL, 1),(49, "Democratic Republic of the Congo", NULL, "CD", "COD", 180, NULL, 50, NULL, NULL, 1),(50, "Congo", NULL, "CG", "COG", 178, NULL, 50, NULL, NULL, 1),(51, "Cook Islands", NULL, "CK", "COK", 184, NULL, 50, NULL, NULL, 6),(52, "Colombia", NULL, "CO", "COL", 170, NULL, 50, NULL, NULL, 7),(53, "Comoros", NULL, "KM", "COM", 174, NULL, 50, NULL, NULL, 1),(54, "Cabo Verde", NULL, "CV", "CPV", 132, NULL, 50, NULL, NULL, 1),(55, "Costa Rica", NULL, "CR", "CRI", 188, NULL, 50, NULL, NULL, 5),(56, "Cuba", NULL, "CU", "CUB", 192, NULL, 50, NULL, NULL, 5),(57, "Curacao", NULL, "CW", "CUW", 531, NULL, 50, NULL, NULL, 7),(58, "Cayman Islands", NULL, "KY", "CYM", 136, NULL, 50, NULL, NULL, 5),(59, "Cyprus", NULL, "CY", "CYP", 196, NULL, 50, NULL, NULL, 4),(60, "Czech Republic", NULL, "CZ", "CZE", 203, NULL, 50, NULL, NULL, 4),(61, "Germany", NULL, "DE", "DEU", 276, NULL, 50, NULL, NULL, 4),(62, "Djibouti", NULL, "DJ", "DJI", 262, NULL, 50, NULL, NULL, 3),(63, "Dominica", NULL, "DM", "DMA", 212, NULL, 50, NULL, NULL, 5),(64, "Denmark", NULL, "DK", "DNK", 208, NULL, 50, NULL, NULL, 4),(65, "Dominican Republic", NULL, "DO", "DOM", 214, NULL, 50, NULL, NULL, 5),(66, "Algeria", NULL, "DZ", "DZA", 12, NULL, 50, NULL, NULL, 1),(67, "Ecuador", NULL, "EC", "ECU", 218, NULL, 50, NULL, NULL, 7),(68, "Egypt", NULL, "EG", "EGY", 818, NULL, 50, NULL, NULL, 1),(69, "Eritrea", NULL, "ER", "ERI", 232, NULL, 50, NULL, NULL, 1),(70, "Spain", NULL, "ES", "ESP", 724, NULL, 50, NULL, NULL, 4),(71, "Estonia", NULL, "EE", "EST", 233, NULL, 50, NULL, NULL, 4),(72, "Ethiopia", NULL, "ET", "ETH", 231, NULL, 50, NULL, NULL, 1),(73, "Finland", NULL, "FI", "FIN", 246, NULL, 50, NULL, NULL, 4),(74, "Fiji", NULL, "FJ", "FJI", 242, NULL, 50, NULL, NULL, 6),(75, "Falkland Islands", NULL, "FK", "FLK", 238, NULL, 50, NULL, NULL, 7),(76, "France", NULL, "FR", "FRA", 250, NULL, 50, NULL, NULL, 4),(77, "Faroe Islands", NULL, "FO", "FRO", 234, NULL, 50, NULL, NULL, 4),(78, "Federated States of Micronesia", NULL, "FM", "FSM", 583, NULL, 50, NULL, NULL, 6),(79, "Gabon", NULL, "GA", "GAB", 266, NULL, 50, NULL, NULL, 1),(80, "United Kingdom", NULL, "GB", "GBR", 826, NULL, 50, NULL, NULL, 4),(81, "Georgia", NULL, "GE", "GEO", 268, NULL, 50, NULL, NULL, 4),(82, "Guernsey", NULL, "GG", "GGY", 831, NULL, 50, NULL, NULL, 4),(83, "Ghana", NULL, "GH", "GHA", 288, NULL, 50, NULL, NULL, 1),(84, "Gibraltar", NULL, "GI", "GIB", 292, NULL, 50, NULL, NULL, 4),(85, "Guinea", NULL, "GN", "GIN", 324, NULL, 50, NULL, NULL, 1),(86, "Guadeloupe", NULL, "GP", "GLP", 312, NULL, 50, NULL, NULL, 5),(87, "Gambia, The", NULL, "GM", "GMB", 270, NULL, 50, NULL, NULL, 1),(88, "Guinea-Bissau", NULL, "GW", "GNB", 624, NULL, 50, NULL, NULL, 1),(89, "Equatorial Guinea", NULL, "GQ", "GNQ", 226, NULL, 50, NULL, NULL, 1),(90, "Greece", NULL, "GR", "GRC", 300, NULL, 50, NULL, NULL, 4),(91, "Grenada", NULL, "GD", "GRD", 308, NULL, 50, NULL, NULL, 5),(92, "Greenland", NULL, "GL", "GRL", 304, NULL, 50, NULL, NULL, 5),(93, "Guatemala", NULL, "GT", "GTM", 320, NULL, 50, NULL, NULL, 5),(94, "French Guiana", NULL, "GF", "GUF", 254, NULL, 50, NULL, NULL, 7),(95, "Guam", NULL, "GU", "GUM", 316, NULL, 50, NULL, NULL, 6),(96, "Guyana", NULL, "GY", "GUY", 328, NULL, 50, NULL, NULL, 7),(97, "Honduras", NULL, "HN", "HND", 340, NULL, 50, NULL, NULL, 5),(98, "Croatia", NULL, "HR", "HRV", 191, NULL, 50, NULL, NULL, 4),(99, "Haiti", NULL, "HT", "HTI", 332, NULL, 50, NULL, NULL, 5),(100, "Hungary", NULL, "HU", "HUN", 348, NULL, 50, NULL, NULL, 4),(101, "India", NULL, "IN", "IND", 356, NULL, 50, NULL, NULL, 3),(102, "Indonesia", NULL, "ID", "IDN", 360, NULL, 50, NULL, NULL, 6),(103, "Isle of Man", NULL, "IM", "IMN", 833, NULL, 50, NULL, NULL, 4),(104, "Ireland", NULL, "IE", "IRL", 372, NULL, 50, NULL, NULL, 4),(105, "Iran", NULL, "IR", "IRN", 364, NULL, 50, NULL, NULL, 3),(106, "Iraq", NULL, "IQ", "IRQ", 368, NULL, 50, NULL, NULL, 3),(107, "Iceland", NULL, "IS", "ISL", 352, NULL, 50, NULL, NULL, 4),(108, "Israel", NULL, "IL", "ISR", 376, NULL, 50, NULL, NULL, 3),(109, "Italy", NULL, "IT", "ITA", 380, NULL, 50, NULL, NULL, 4),(110, "Jamaica", NULL, "JM", "JAM", 388, NULL, 50, NULL, NULL, 5),(111, "Jordan", NULL, "JO", "JOR", 400, NULL, 50, NULL, NULL, 3),(112, "Japan", NULL, "JP", "JPN", 392, NULL, 50, NULL, NULL, 3),(113, "Kazakhstan", NULL, "KZ", "KAZ", 398, NULL, 50, NULL, NULL, 3),(114, "Kenya", NULL, "KE", "KEN", 404, NULL, 50, NULL, NULL, 1),(115, "Kyrgyzstan", NULL, "KG", "KGZ", 417, NULL, 50, NULL, NULL, 3),(116, "Cambodia", NULL, "KH", "KHM", 116, NULL, 50, NULL, NULL, 3),(117, "Kiribati", NULL, "KI", "KIR", 296, NULL, 50, NULL, NULL, 6),(118, "Saint Kitts and Nevis", NULL, "KN", "KNA", 659, NULL, 50, NULL, NULL, 5),(119, "Korea, Republic of", NULL, "KR", "KOR", 410, NULL, 50, NULL, NULL, 3),(120, "Kuwait", NULL, "KW", "KWT", 414, NULL, 50, NULL, NULL, 3),(121, "Laos", NULL, "LA", "LAO", 418, NULL, 50, NULL, NULL, 3),(122, "Lebanon", NULL, "LB", "LBN", 422, NULL, 50, NULL, NULL, 3),(123, "Liberia", NULL, "LR", "LBR", 430, NULL, 50, NULL, NULL, 1),(124, "Libya", NULL, "LY", "LBY", 434, NULL, 50, NULL, NULL, 1),(125, "Saint Lucia", NULL, "LC", "LCA", 662, NULL, 50, NULL, NULL, 5),(126, "Liechtenstein", NULL, "LI", "LIE", 438, NULL, 50, NULL, NULL, 4),(127, "Sri Lanka", NULL, "LK", "LKA", 144, NULL, 50, NULL, NULL, 3),(128, "Lesotho", NULL, "LS", "LSO", 426, NULL, 50, NULL, NULL, 1),(129, "Lithuania", NULL, "LT", "LTU", 440, NULL, 50, NULL, NULL, 4),(130, "Luxembourg", NULL, "LU", "LUX", 442, NULL, 50, NULL, NULL, 4),(131, "Latvia", NULL, "LV", "LVA", 428, NULL, 50, NULL, NULL, 4),(132, "Morocco", NULL, "MA", "MAR", 504, NULL, 50, NULL, NULL, 1),(133, "Monaco", NULL, "MC", "MCO", 492, NULL, 50, NULL, NULL, 4),(134, "Moldova", NULL, "MD", "MDA", 498, NULL, 50, NULL, NULL, 4),(135, "Madagascar", NULL, "MG", "MDG", 450, NULL, 50, NULL, NULL, 1),(136, "Maldives", NULL, "MV", "MDV", 462, NULL, 50, NULL, NULL, 3),(137, "Mexico", NULL, "MX", "MEX", 484, NULL, 50, NULL, NULL, 5),(138, "Marshall Islands", NULL, "MH", "MHL", 584, NULL, 50, NULL, NULL, 6),(139, "Macedonia", NULL, "MK", "MKD", 807, NULL, 50, NULL, NULL, 4),(140, "Mali", NULL, "ML", "MLI", 466, NULL, 50, NULL, NULL, 1),(141, "Malta", NULL, "MT", "MLT", 470, NULL, 50, NULL, NULL, 4),(142, "Myanmar", NULL, "MM", "MMR", 104, NULL, 50, NULL, NULL, 3),(143, "Montenegro", NULL, "ME", "MNE", 499, NULL, 50, NULL, NULL, 4),(144, "Mongolia", NULL, "MN", "MNG", 496, NULL, 50, NULL, NULL, 3),(145, "Northern Mariana Islands", NULL, "MP", "MNP", 580, NULL, 50, NULL, NULL, 6),(146, "Mozambique", NULL, "MZ", "MOZ", 508, NULL, 50, NULL, NULL, 4),(147, "Mauritania", NULL, "MR", "MRT", 478, NULL, 50, NULL, NULL, 1),(148, "Montserrat", NULL, "MS", "MSR", 500, NULL, 50, NULL, NULL, 5),(149, "Martinique", NULL, "MQ", "MTQ", 474, NULL, 50, NULL, NULL, 5),(150, "Mauritius", NULL, "MU", "MUS", 480, NULL, 50, NULL, NULL, 1),(151, "Malawi", NULL, "MW", "MWI", 454, NULL, 50, NULL, NULL, 1),(152, "Malaysia", NULL, "MY", "MYS", 458, NULL, 50, NULL, NULL, 3),(153, "Mayotte", NULL, "YT", "MYT", 175, NULL, 50, NULL, NULL, 1),(154, "Namibia", NULL, "NA", "NAM", 516, NULL, 50, NULL, NULL, 1),(155, "New Caledonia", NULL, "NC", "NCL", 540, NULL, 50, NULL, NULL, 6),(156, "Niger", NULL, "NE", "NER", 562, NULL, 50, NULL, NULL, 1),(157, "Nigeria", NULL, "NG", "NGA", 566, NULL, 50, NULL, NULL, 1),(158, "Nicaragua", NULL, "NI", "NIC", 558, NULL, 50, NULL, NULL, 5),(159, "Niue", NULL, "NU", "NIU", 570, NULL, 50, NULL, NULL, 6),(160, "Netherlands, The", NULL, "NL", "NLD", 528, NULL, 50, NULL, NULL, 4),(161, "Norway", NULL, "NO", "NOR", 578, NULL, 50, NULL, NULL, 4),(162, "Nepal", NULL, "NP", "NPL", 524, NULL, 50, NULL, NULL, 3),(163, "Nauru", NULL, "NR", "NRU", 520, NULL, 50, NULL, NULL, 6),(164, "New Zealand", NULL, "NZ", "NZL", 554, NULL, 50, NULL, NULL, 6),(165, "Oman", NULL, "OM", "OMN", 512, NULL, 50, NULL, NULL, 3),(166, "Pakistan", NULL, "PK", "PAK", 586, NULL, 50, NULL, NULL, 3),(167, "Panama", NULL, "PA", "PAN", 591, NULL, 50, NULL, NULL, 5),(168, "Pitcairn Island", NULL, "PN", "PCN", 612, NULL, 50, NULL, NULL, 6),(169, "Peru", NULL, "PE", "PER", 604, NULL, 50, NULL, NULL, 7),(170, "Philippines, The", NULL, "PH", "PHL", 608, NULL, 50, NULL, NULL, 6),(171, "Palau", NULL, "PW", "PLW", 585, NULL, 50, NULL, NULL, 6),(172, "Papua New Guinea", NULL, "PG", "PNG", 598, NULL, 50, NULL, NULL, 6),(173, "Poland", NULL, "PL", "POL", 616, NULL, 50, NULL, NULL, 4),(174, "Portugal", NULL, "PT", "PRT", 620, NULL, 50, NULL, NULL, 4),(175, "Paraguay", NULL, "PY", "PRY", 600, NULL, 50, NULL, NULL, 7),(176, "Palestine", NULL, "PS", "PSE", 275, NULL, 50, NULL, NULL, 3),(177, "French Polynesia", NULL, "PF", "PYF", 258, NULL, 50, NULL, NULL, 6),(178, "Qatar", NULL, "QA", "QAT", 634, NULL, 50, NULL, NULL, 3),(179, "Réunion", NULL, "RE", "REU", 638, NULL, 50, NULL, NULL, 1),(180, "Romania", NULL, "RO", "ROU", 642, NULL, 50, NULL, NULL, 4),(181, "Russian Federation", NULL, "RU", "RUS", 643, NULL, 50, NULL, NULL, 3),(182, "Saudi Arabia", NULL, "SA", "SAU", 682, NULL, 50, NULL, NULL, 3),(183, "Sudan", NULL, "SD", "SDN", 729, NULL, 50, NULL, NULL, 1),(184, "Senegal", NULL, "SN", "SEN", 686, NULL, 50, NULL, NULL, 1),(185, "Singapore", NULL, "SG", "SGP", 702, NULL, 50, NULL, NULL, 3),(186, "Saint Helena, Ascension, and Tristan da Cunha", NULL, "SH", "SHN", 654, NULL, 50, NULL, NULL, 1),(187, "Solomon Islands", NULL, "SB", "SLB", 90, NULL, 50, NULL, NULL, 6),(188, "Sierra Leone", NULL, "SL", "SLE", 694, NULL, 50, NULL, NULL, 1),(189, "El Salvador", NULL, "SV", "SLV", 222, NULL, 50, NULL, NULL, 5),(190, "San Marino", NULL, "SM", "SMR", 674, NULL, 50, NULL, NULL, 4),(191, "Somalia", NULL, "SO", "SOM", 706, NULL, 50, NULL, NULL, 1),(192, "Serbia", NULL, "RS", "SRB", 688, NULL, 50, NULL, NULL, 4),(193, "South Sudan", NULL, "SS", "SSD", 728, NULL, 50, NULL, NULL, 1),(194, "Sao Tome and Principe", NULL, "ST", "STP", 678, NULL, 50, NULL, NULL, 1),(195, "Suriname", NULL, "SR", "SUR", 740, NULL, 50, NULL, NULL, 7),(196, "Slovakia", NULL, "SK", "SVK", 703, NULL, 50, NULL, NULL, 4),(197, "Slovenia", NULL, "SI", "SVN", 705, NULL, 50, NULL, NULL, 4),(198, "Sweden", NULL, "SE", "SWE", 752, NULL, 50, NULL, NULL, 4),(199, "Eswatini", NULL, "SZ", "SWZ", 748, NULL, 50, NULL, NULL, 1),(200, "Seychelles", NULL, "SC", "SYC", 690, NULL, 50, NULL, NULL, 1),(201, "Syrian Arab Republic", NULL, "SY", "SYR", 760, NULL, 50, NULL, NULL, 3),(202, "Turks and Caicos", NULL, "TC", "TCA", 796, NULL, 50, NULL, NULL, 5),(203, "Chad", NULL, "TD", "TCD", 148, NULL, 50, NULL, NULL, 1),(204, "Togo", NULL, "TG", "TGO", 768, NULL, 50, NULL, NULL, 1),(205, "Thailand", NULL, "TH", "THA", 764, NULL, 50, NULL, NULL, 3),(206, "Tajikistan", NULL, "TJ", "TJK", 762, NULL, 50, NULL, NULL, 3),(207, "Tokelau", NULL, "TK", "TKL", 772, NULL, 50, NULL, NULL, 6),(208, "Turkmenistan", NULL, "TM", "TKM", 795, NULL, 50, NULL, NULL, 3),(209, "Timor-Leste", NULL, "TL", "TLS", 626, NULL, 50, NULL, NULL, 3),(210, "Tonga", NULL, "TO", "TON", 776, NULL, 50, NULL, NULL, 6),(211, "Trinidad and Tobago", NULL, "TT", "TTO", 780, NULL, 50, NULL, NULL, 7),(212, "Tunisia", NULL, "TN", "TUN", 788, NULL, 50, NULL, NULL, 1),(213, "Turkey", NULL, "TR", "TUR", 792, NULL, 50, NULL, NULL, 3),(214, "Tuvalu", NULL, "TV", "TUV", 798, NULL, 50, NULL, NULL, 6),(215, "Taiwan", NULL, "TW", "TWN", 158, NULL, 50, NULL, NULL, 3),(216, "Tanzania", NULL, "TZ", "TZA", 834, NULL, 50, NULL, NULL, 1),(217, "Uganda", NULL, "UG", "UGA", 800, NULL, 50, NULL, NULL, 1),(218, "Ukraine", NULL, "UA", "UKR", 804, NULL, 50, NULL, NULL, 4),(219, "Uruguay", NULL, "UY", "URY", 858, NULL, 50, NULL, NULL, 7),(220, "United States", NULL, "US", "USA", 840, NULL, 50, NULL, NULL, 5),(221, "Uzbekistan", NULL, "UZ", "UZB", 860, NULL, 50, NULL, NULL, 3),(222, "Vatican City", NULL, "VA", "VAT", 336, NULL, 50, NULL, NULL, 4),(223, "Saint Vincent and the Grenadines", NULL, "VC", "VCT", 670, NULL, 50, NULL, NULL, 5),(224, "Venezuela", NULL, "VE", "VEN", 862, NULL, 50, NULL, NULL, 7),(225, "British Virgin Islands", NULL, "VG", "VGB", 92, NULL, 50, NULL, NULL, 5),(226, "Virgin Islands", NULL, "VI", "VIR", 850, NULL, 50, NULL, NULL, 5),(227, "Viet Nam", NULL, "VN", "VNM", 704, NULL, 50, NULL, NULL, 3),(228, "Vanuatu", NULL, "VU", "VUT", 548, NULL, 50, NULL, NULL, 6),(229, "Wallis and Futuna", NULL, "WF", "WLF", 876, NULL, 50, NULL, NULL, 6),(230, "Samoa", NULL, "WS", "WSM", 882, NULL, 50, NULL, NULL, 6),(231, "Kosovo", NULL, "XK", "XKX", NULL, NULL, 50, NULL, NULL, 4),(232, "Yemen", NULL, "YE", "YEM", 887, NULL, 50, NULL, NULL, 3),(233, "South Africa", NULL, "ZA", "ZAF", 710, NULL, 50, NULL, NULL, 1),(234, "Zambia", NULL, "ZM", "ZMB", 894, NULL, 50, NULL, NULL, 1),(235, "Zimbabwe", NULL, "ZW", "ZWE", 716, NULL, 50, NULL, NULL, 1),(236, "Korea, Democratic Peoples Republic of", NULL, "KP", "PRK", 408, NULL, 50, NULL, NULL, 3),(237, "Rwanda", NULL, "RW", "RWA", 646, NULL, 50, NULL, NULL, 1),(238, "Kandahar", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(239, "Zabul", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(240, "Uruzgan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(241, "Daykundi", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(242, "Ghanzi", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(243, "Paktika", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(244, "Khost", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(245, "Paktia", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(246, "Logar", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(247, "Wardak", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(248, "Kabul", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(249, "Nangarhar", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(250, "Laghman", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(251, "Kapisa", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(252, "Parwan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(253, "Panjshir", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(254, "Kunar", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(255, "Nuristan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(256, "Baghlan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(257, "Bamyan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(258, "Samangan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(259, "Kunduz", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(260, "Takhar", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(261, "Balkh", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(262, "Sar-e Pol", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(263, "Jowzjan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(264, "Faryab", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(265, "Badghis", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(266, "Ghor", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(267, "Herat", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(268, "Farah", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(269, "Nimruz", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(270, "Helmand", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(271, "Badakhshan", NULL, NULL, "AFG", NULL, NULL, 60, NULL, NULL, 9),(272, "Bengo", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(273, "Benguela", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(274, "Bié", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(275, "Cabinda", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(276, "Cunene", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(277, "Huambo", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(278, "Huíla", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(279, "Cuando Cubango", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(280, "Cuanza Norte", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(281, "Cuanza Sul", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(282, "Luanda", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(283, "Lunda Norte", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(284, "Lunda Sul", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(285, "Malanje", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(286, "Moxico", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(287, "Namibe", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(288, "Uíge", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(289, "Zaire", NULL, NULL, "AGO", NULL, NULL, 60, NULL, NULL, 10),(290, "Berat", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(291, "Dibër", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(292, "Durrës", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(293, "Elbasan", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(294, "Fier", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(295, "Gjirokastër", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(296, "Korçë", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(297, "Kukës", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(298, "Lezhë", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(299, "Shkodër", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(300, "Tiranë", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(301, "Vlorë", NULL, NULL, "ALB", NULL, NULL, 60, NULL, NULL, 12),(302, "Sant Julia de Loria", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(303, "Canillo", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(304, "Ordino", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(305, "La Massana", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(306, "Encamp", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(307, "Escaldes-Engordany", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(308, "Andorra la Vella", NULL, NULL, "AND", NULL, NULL, 60, NULL, NULL, 13),(309, "Abu Dhabi", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(310, "Ajman", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(311, "Dubai", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(312, "Fujairah", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(313, "Ras al-Khaimah", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(314, "Sharjah", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(315, "Umm al-Quwain", NULL, NULL, "ARE", NULL, NULL, 60, NULL, NULL, 14),(316, "Buenos Aires", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(317, "Catamarca", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(318, "Chaco", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(319, "Chubut", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(320, "Ciudad Autónoma de Buenos Aires", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(321, "Córdoba", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(322, "Corrientes", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(323, "Formosa", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(324, "Jujuy", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(325, "La Pampa", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(326, "La Roja", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(327, "Mendoza", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(328, "Misiones", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(329, "Neuquén", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(330, "Río Negro", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(331, "Salta", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(332, "San Juan", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(333, "San Luis", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(334, "Santa Cruz", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(335, "Santa Fe", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(336, "Santiago del Estero", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(337, "Tierra del Fuego", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(338, "Tucumán", NULL, NULL, "ARG", NULL, NULL, 60, NULL, NULL, 15),(339, "Gegharkunik", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(340, "Syunik", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(341, "Tavush", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(342, "Shirak", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(343, "Lori", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(344, "Kotayk", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(345, "Vayots Dzor", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(346, "Armavir", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(347, "Aragatsotn", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(348, "Yerevan", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(349, "Ararat", NULL, NULL, "ARM", NULL, NULL, 60, NULL, NULL, 16),(350, "Redonda", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(351, "Saint Philip", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(352, "Saint John", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(353, "Barbuda", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(354, "Saint Mary", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(355, "Saint Paul", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(356, "Saint Peter", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(357, "Saint George", NULL, NULL, "ATG", NULL, NULL, 60, NULL, NULL, 18),(358, "New South Wales", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(359, "Victoria", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(360, "Queensland", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(361, "South Australia", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(362, "Western Australia", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(363, "Tasmania", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(364, "Northern Territory", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(365, "Australian Capital Territory", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(366, "Other Territories", NULL, NULL, "AUS", NULL, NULL, 60, NULL, NULL, 19),(367, "Burgenland", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(368, "Kärnten", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(369, "Niederösterreich", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(370, "Oberösterreich", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(371, "Salzburg", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(372, "Steiermark", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(373, "Tirol", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(374, "Vorarlberg", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(375, "Wien", NULL, NULL, "AUT", NULL, NULL, 60, NULL, NULL, 20),(376, "Nakhchivan Autonomous Republic", NULL, NULL, "AZE", NULL, NULL, 60, NULL, NULL, 21),(377, "Contiguous Azerbaijan", NULL, NULL, "AZE", NULL, NULL, 60, NULL, NULL, 21),(378, "Gitega", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(379, "Kirundo", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(380, "Muyinga", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(381, "Ngozi", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(382, "Makamba", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(383, "Rutana", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(384, "Ruyigi", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(385, "Cankuzo", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(386, "Karuzi", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(387, "Cibitoke", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(388, "Kayanza", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(389, "Bubanza", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(390, "Muramvya", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(391, "Bujumbura Rural", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(392, "Bujumbura Mairie", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(393, "Bururi", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(394, "Mwaro", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(395, "Rumonge", NULL, NULL, "BDI", NULL, NULL, 60, NULL, NULL, 22),(396, "Brussels Hoofdstedelijk", NULL, NULL, "BEL", NULL, NULL, 60, NULL, NULL, 23),(397, "Vlaams Gewest", NULL, NULL, "BEL", NULL, NULL, 60, NULL, NULL, 23),(398, "Wallonne Gewest", NULL, NULL, "BEL", NULL, NULL, 60, NULL, NULL, 23),(399, "Borgou", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(400, "Alibori", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(401, "Atakora", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(402, "Donga", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(403, "Collines", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(404, "Plateau", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(405, "Zou", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(406, "Littoral", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(407, "Kouffo", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(408, "Oueme", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(409, "Atlanique", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(410, "Mono", NULL, NULL, "BEN", NULL, NULL, 60, NULL, NULL, 24),(411, "Centre", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(412, "Boucle du Mouhoun", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(413, "Cascades", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(414, "Centre-Est", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(415, "Centre-Nord", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(416, "Centre-Ouest", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(417, "Centre-Sud", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(418, "Est", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(419, "Hauts-Bassins", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(420, "Nord", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(421, "Plateau Central", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(422, "Sahel", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(423, "Sud-Ouest", NULL, NULL, "BFA", NULL, NULL, 60, NULL, NULL, 26),(424, "Chittagong", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(425, "Dhaka", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(426, "Mymensingh", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(427, "Rajshani", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(428, "Rangpur", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(429, "Sylhet", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(430, "Khulna", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(431, "Barisal", NULL, NULL, "BGD", NULL, NULL, 60, NULL, NULL, 27),(432, "Blagoevgrad", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(433, "Burgas", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(434, "Dobrich", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(435, "Gabrovo", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(436, "Haskovo", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(437, "Kardzhali", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(438, "Kyustendil", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(439, "Lovech", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(440, "Montana", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(441, "Pazardzhik", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(442, "Pernik", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(443, "Pleven", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(444, "Plovdiv", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(445, "Razgrad", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(446, "Ruse", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(447, "Shumen", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(448, "Silistra", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(449, "Sliven", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(450, "Smolyan", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(451, "Sofia", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(452, "Sofia City", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(453, "Stara Zagora", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(454, "Targovishte", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(455, "Varna", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(456, "Veliko Tarnovo", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(457, "Vidin", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(458, "Vratsa", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(459, "Yambol", NULL, NULL, "BGR", NULL, NULL, 60, NULL, NULL, 28),(460, "Northern", NULL, NULL, "BHR", NULL, NULL, 60, NULL, NULL, 29),(461, "Southern", NULL, NULL, "BHR", NULL, NULL, 60, NULL, NULL, 29),(462, "Capital", NULL, NULL, "BHR", NULL, NULL, 60, NULL, NULL, 29),(463, "Muharraq", NULL, NULL, "BHR", NULL, NULL, 60, NULL, NULL, 29),(464, "Acklins", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(465, "Biminis", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(466, "Berry Islands", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(467, "Black Point", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(468, "Cat Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(469, "Central Abaco", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(470, "Central Andros", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(471, "Central Eleuthera", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(472, "City of Freeport", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(473, "Crooked Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(474, "East Grand Bahama", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(475, "Exuma", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(476, "Grand Cay", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(477, "Harbour Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(478, "Hope Town", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(479, "Inagua", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(480, "Long Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(481, "Mangrove Cay", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(482, "Mayaguana", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(483, "Moore's Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(484, "New Providence", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(485, "North Abaco", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(486, "North Andros", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(487, "North Eleuthera", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(488, "Ragged Island", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(489, "Rum Cay", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(490, "San Salvador", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(491, "South Abaco", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(492, "South Andros", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(493, "South Eleuthera", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(494, "Spanish Wells", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(495, "West Grand Bahama", NULL, NULL, "BHS", NULL, NULL, 60, NULL, NULL, 30),(496, "Brčko District", NULL, NULL, "BIH", NULL, NULL, 60, NULL, NULL, 31),(497, "Republika Srpska", NULL, NULL, "BIH", NULL, NULL, 60, NULL, NULL, 31),(498, "Federation of Bosnia and Herzegovina", NULL, NULL, "BIH", NULL, NULL, 60, NULL, NULL, 31),(499, "Gomel", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(500, "Vitebsk", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(501, "Grodno", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(502, "Brest", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(503, "Mogilev", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(504, "Minsk", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(505, "Minsk City", NULL, NULL, "BLR", NULL, NULL, 60, NULL, NULL, 33),(506, "Orange Walk", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(507, "Corozal", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(508, "Belize", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(509, "Cayo", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(510, "Stann Creek", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(511, "Toledo", NULL, NULL, "BLZ", NULL, NULL, 60, NULL, NULL, 34),(512, "Beni", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(513, "Chuquisaca", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(514, "Cochabamba", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(515, "La Paz", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(516, "Oruro", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(517, "Pando", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(518, "Potosí", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(519, "Santa Cruz", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(520, "Tarija", NULL, NULL, "BOL", NULL, NULL, 60, NULL, NULL, 36),(521, "Roraima", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(522, "Amapa", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(523, "Amazonas", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(524, "Para", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(525, "Maranhao", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(526, "Ceara", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(527, "Rio Granda do Norte", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(528, "Paraiba", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(529, "Piaui", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(530, "Pernambuco", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(531, "Alagoas", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(532, "Acre", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(533, "Rondonia", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(534, "Sergipe", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(535, "Tocantins", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(536, "Bahia", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(537, "Mato Grosso", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(538, "Goias", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(539, "Distrito Federal", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(540, "Minas Gerais", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(541, "Espirito Santo", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(542, "Mato Grosso do Sul", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(543, "Rio de Jeneiro", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(544, "Sao Paulo", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(545, "Parana", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(546, "Santa Catarina", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(547, "Rio Grande do Sul", NULL, NULL, "BRA", NULL, NULL, 60, NULL, NULL, 37),(548, "Saint George", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(549, "Saint Lucy", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(550, "Saint Andrew", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(551, "Saint Peter", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(552, "Saint James", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(553, "Saint Thomas", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(554, "Saint Joseph", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(555, "Christ Church", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(556, "Saint Michael", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(557, "Saint Philip", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(558, "Saint John", NULL, NULL, "BRB", NULL, NULL, 60, NULL, NULL, 38),(559, "Belait", NULL, NULL, "BRN", NULL, NULL, 60, NULL, NULL, 39),(560, "Tutong", NULL, NULL, "BRN", NULL, NULL, 60, NULL, NULL, 39),(561, "Brunei-Muara", NULL, NULL, "BRN", NULL, NULL, 60, NULL, NULL, 39),(562, "Temburong", NULL, NULL, "BRN", NULL, NULL, 60, NULL, NULL, 39),(563, "Trashigang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(564, "Trashiyangtse", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(565, "Mongar", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(566, "Lhuntse", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(567, "Bumthang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(568, "Sarpang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(569, "Trongsa", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(570, "Tsirang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(571, "Dagana", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(572, "Wangdue Phodrang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(573, "Punakha", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(574, "Thimpu", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(575, "Gasa", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(576, "Chukha", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(577, "Samtse", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(578, "Haa", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(579, "Paro", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(580, "Pemagatshel", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(581, "Samdrup Jongkhar", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(582, "Zhemgang", NULL, NULL, "BTN", NULL, NULL, 60, NULL, NULL, 40),(583, "South-East", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(584, "Kgatleng", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(585, "North-East", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(586, "Central", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(587, "Ghanzi", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(588, "Kweneng", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(589, "Southern", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(590, "Kgalagadi", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(591, "North-West", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(592, "Chobe", NULL, NULL, "BWA", NULL, NULL, 60, NULL, NULL, 41),(593, "Mbomou", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(594, "Sangha-Mbaéré", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(595, "Mambéré-Kadéï", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(596, "Lobaye", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(597, "Nana-Mambéré", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(598, "Ombella-M'Poko", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(599, "Ouham-Pendé", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(600, "Ouham", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(601, "Nana-Grébizi", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(602, "Kémo", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(603, "Vakaga", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(604, "Bamingui-Bangoran", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(605, "Ouaka", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(606, "Basse-Kotto", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(607, "Haute-Kotto", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(608, "Haut-Mbomou", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(609, "Bangui", NULL, NULL, "CAF", NULL, NULL, 60, NULL, NULL, 42),(610, "Newfoundland and Labrador", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(611, "Prince Edward Island", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(612, "Nova Scotia", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(613, "New Brunswick", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(614, "Quebec", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(615, "Ontario", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(616, "Manitoba", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(617, "Saskatchewan", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(618, "Alberta", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(619, "British Columbia", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(620, "Yukon", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(621, "Northwest Territories", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(622, "Nunavut", NULL, NULL, "CAN", NULL, NULL, 60, NULL, NULL, 43),(623, "Aargau", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(624, "Appenzell Ausserrhoden", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(625, "Appenzell Innerrhoden", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(626, "Basel-Landschaft", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(627, "Basel-Stadt", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(628, "Bern", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(629, "Fribourg", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(630, "Genève", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(631, "Glarus", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(632, "Graubünden", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(633, "Jura", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(634, "Luzern", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(635, "Neuchâtel", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(636, "Nidwalden", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(637, "Obwalden", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(638, "Schaffhausen", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(639, "Schwyz", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(640, "Solothurn", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(641, "St. Gallen", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(642, "Thurgau", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(643, "Ticino", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(644, "Uri", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(645, "Valais", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(646, "Vaud", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(647, "Zug", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(648, "Zürich", NULL, NULL, "CHE", NULL, NULL, 60, NULL, NULL, 44),(649, "Antofagasta", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(650, "Arica y Parinacota", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(651, "Atacama", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(652, "Aysén", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(653, "Coquimbo", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(654, "Araucanía", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(655, "Los Lagos", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(656, "Los Ríos", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(657, "Magallanes y de la Antártica Chilena", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(658, "Ñuble", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(659, "Tarapacá", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(660, "Valparaíso", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(661, "Biobío", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(662, "Libertador Bernardo O'Higgins", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(663, "Maule", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(664, "Metropolitana de Santiago", NULL, NULL, "CHL", NULL, NULL, 60, NULL, NULL, 45),(665, "Hainan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(666, "Taiwan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(667, "Guangxi Zhuang Autonomous Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(668, "Fujian", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(669, "Yunnan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(670, "Guizhou", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(671, "Jiangxi", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(672, "Hunan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(673, "Zhejiang", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(674, "Shanghai", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(675, "Chongqing", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(676, "Hubei", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(677, "Sichuan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(678, "Anhui", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(679, "Jiangsu", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(680, "Henan", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(681, "Tibet Autonomous Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(682, "Shandong", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(683, "Qinghai", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(684, "Ningxia Ningxia Hui Autonomous Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(685, "Shaanxi", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(686, "Tianjin", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(687, "Shanxi", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(688, "Beijing", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(689, "Gansu", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(690, "Hebei", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(691, "Liaoning", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(692, "Jilin", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(693, "Xinjiang Uyghur Autonomous Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(694, "Inner Mongolia Autonomous Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(695, "Heilongjiang", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(696, "Macau Special Administrative Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(697, "Hong Kong Special Administrative Region", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(698, "Guangzhou", NULL, NULL, "CHN", NULL, NULL, 60, NULL, NULL, 46),(699, "Bas-Sassandra", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(700, "Denguele", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(701, "District Autonome D'Abidjan", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(702, "District Autonome De Yamoussoukro", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(703, "Goh-Djiboua", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(704, "Lacs", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(705, "Montagnes", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(706, "Sassandra-Marahoue", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(707, "Savanes", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(708, "Valle Du Bandama", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(709, "Woroba", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(710, "Zanzan", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(711, "Lagunes", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(712, "Comoe", NULL, NULL, "CIV", NULL, NULL, 60, NULL, NULL, 47),(713, "Centre", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(714, "Far North", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(715, "North", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(716, "North-West", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(717, "Adamaoua", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(718, "East", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(719, "South", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(720, "South-West", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(721, "West", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(722, "Littoral", NULL, NULL, "CMR", NULL, NULL, 60, NULL, NULL, 48),(723, "Upper Uele", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(724, "Ituri", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(725, "Tshopo", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(726, "Lower Uele", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(727, "Mongala", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(728, "Nord-Ubangi", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(729, "Tshuapa", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(730, "Équateur", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(731, "Haut-Katanga", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(732, "Haut-Lomami", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(733, "Kongo-Central", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(734, "Kwango", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(735, "Sankuru", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(736, "Sud-Ubangi", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(737, "Tanganyika", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(738, "Kasai", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(739, "Kasai-Oriental", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(740, "Maniema", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(741, "North Kivu", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(742, "South Kivu", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(743, "Central Kasai", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(744, "Lomami", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(745, "Lualaba", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(746, "Kinshasa", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(747, "Kwilu", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(748, "Mai-Ndombe", NULL, NULL, "COD", NULL, NULL, 60, NULL, NULL, 49),(749, "Bouenza", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(750, "Cuvette", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(751, "Cuvette-Ouest", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(752, "Kouilou", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(753, "Likouala", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(754, "Lékoumou", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(755, "Niari", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(756, "Plateaux", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(757, "Pool", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(758, "Sangha", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(759, "Brazzaville", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(760, "Pointe-Noire", NULL, NULL, "COG", NULL, NULL, 60, NULL, NULL, 50),(761, "Caldas", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(762, "Amazonas", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(763, "Meta", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(764, "Cundinamarca", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(765, "Tolima", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(766, "Antioquia", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(767, "Atlántico", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(768, "Bolívar", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(769, "Cesar", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(770, "Magdalena", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(771, "Sucre", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(772, "Córdoba", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(773, "La Guajira", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(774, "Chocó", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(775, "Valle del Cauca", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(776, "Norte de Santander", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(777, "Quindío", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(778, "Vichada", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(779, "Vaupés", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(780, "Santander", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(781, "Risaralda", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(782, "Putumayo", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(783, "Nariño", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(784, "Guaviare", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(785, "Guainía", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(786, "Bogota Capital District", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(787, "Arauca", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(788, "Boyacá", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(789, "Casanare", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(790, "Cauca", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(791, "Caquetá", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(792, "Huila", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(793, "Archipiélago de San Andrés, Providencia y Santa Catalina", NULL, NULL, "COL", NULL, NULL, 60, NULL, NULL, 52),(794, "Moheli", NULL, NULL, "COM", NULL, NULL, 60, NULL, NULL, 53),(795, "Anjouan", NULL, NULL, "COM", NULL, NULL, 60, NULL, NULL, 53),(796, "Grande Comore", NULL, NULL, "COM", NULL, NULL, 60, NULL, NULL, 53),(797, "Boa Vista", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(798, "Brava", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(799, "Maio", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(800, "Sal", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(801, "São Vicente", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(802, "Ribeira Brava", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(803, "Tarrafal de São Nicolau", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(804, "Porto Novo", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(805, "Ribeira Grande", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(806, "Paul", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(807, "Mosteiros", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(808, "Santa Catarina do Fogo", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(809, "São Filipe", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(810, "São Miguel", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(811, "Tarrafal", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(812, "Santa Catarina", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(813, "Santa Cruz", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(814, "São Salvador do Mundo", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(815, "São Lourenço dos Órgãos", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(816, "São Domingos", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(817, "Praia", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(818, "Ribeira Grande de Santiago", NULL, NULL, "CPV", NULL, NULL, 60, NULL, NULL, 54),(819, "Heredia", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(820, "Guanacaste", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(821, "Alajuela", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(822, "San José", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(823, "Puntarenas", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(824, "Cartago", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(825, "Limón", NULL, NULL, "CRI", NULL, NULL, 60, NULL, NULL, 55),(826, "Isle of Youth", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(827, "Havana", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(828, "Matanzas", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(829, "Pinar del Rio", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(830, "Camagüey", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(831, "Ciego de Avila", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(832, "Cienfuegos", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(833, "Granma", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(834, "Guantánamo", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(835, "Holguín", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(836, "Las Tunas", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(837, "Santiago de Cuba", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(838, "Sancti Spiritus", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(839, "Villa Clara", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(840, "Artemisa", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(841, "Mayabeque", NULL, NULL, "CUB", NULL, NULL, 60, NULL, NULL, 56),(842, "Larnaca", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(843, "Limassol", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(844, "Paphos", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(845, "Nicosia", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(846, "Famagusta", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(847, "Kyrenia", NULL, NULL, "CYP", NULL, NULL, 60, NULL, NULL, 59),(848, "Hlavní město Praha", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(849, "Středočeský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(850, "Jihočeský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(851, "Plzeňský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(852, "Karlovarský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(853, "Ústecký", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(854, "Liberecký", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(855, "Královéhradecký", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(856, "Pardubický", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(857, "Kraj Vysočina", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(858, "Jihomoravský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(859, "Olomoucký", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(860, "Moravskoslezský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(861, "Zlínský", NULL, NULL, "CZE", NULL, NULL, 60, NULL, NULL, 60),(862, "Baden-Württemberg", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(863, "Bayern", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),
  (864, "Berlin", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(865, "Brandenburg", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(866, "Bremen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(867, "Hamburg", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(868, "Hessen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(869, "Mecklenburg-Vorpommern", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(870, "Niedersachsen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(871, "Nordrhein-Westfalen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(872, "Rheinland-Pfalz", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(873, "Saarland", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(874, "Sachsen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(875, "Sachsen-Anhalt", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(876, "Schleswig-Holstein", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(877, "Thüringen", NULL, NULL, "DEU", NULL, NULL, 60, NULL, NULL, 61),(878, "Ali Sabieh", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(879, "Arta", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(880, "Dikhil", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(881, "Tadjourah", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(882, "Obock", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(883, "Djibouti", NULL, NULL, "DJI", NULL, NULL, 60, NULL, NULL, 62),(884, "Saint Andrew", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(885, "Saint Joseph", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(886, "Saint Patrick", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(887, "Saint David", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(888, "Saint Mark", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(889, "Saint Luke", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(890, "Saint George", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(891, "Saint Paul", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(892, "Saint John", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(893, "Saint Peter", NULL, NULL, "DMA", NULL, NULL, 60, NULL, NULL, 63),(894, "Nordjylland", NULL, NULL, "DNK", NULL, NULL, 60, NULL, NULL, 64),(895, "Midtjylland", NULL, NULL, "DNK", NULL, NULL, 60, NULL, NULL, 64),(896, "Sjælland", NULL, NULL, "DNK", NULL, NULL, 60, NULL, NULL, 64),(897, "Hovedstaden", NULL, NULL, "DNK", NULL, NULL, 60, NULL, NULL, 64),(898, "Syddanmark", NULL, NULL, "DNK", NULL, NULL, 60, NULL, NULL, 64),(899, "Monte Cristi", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(900, "Dajabón", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(901, "La Estrelleta", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(902, "Independencia", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(903, "Pedernales", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(904, "La Altagracia", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(905, "El Seybo", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(906, "Hato Mayor", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(907, "Samaná", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(908, "María Trinidad Sánchez", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(909, "Espaillat", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(910, "Puerto Plata", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(911, "Barahona", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(912, "Azua", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(913, "Peravia", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(914, "San Cristóbal", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(915, "Santo Domingo", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(916, "Distrito Nacional", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(917, "San Pedro de Macorís", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(918, "La Romana", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(919, "Valverde", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(920, "Santiago Rodríguez", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(921, "La Vega", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(922, "Hermanas", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(923, "Duarte", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(924, "Santiago", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(925, "Bahoruco", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(926, "San Juan", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(927, "Monseñor Nouel", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(928, "Sánchez Ramírez", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(929, "San José de Ocoa", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(930, "Monte Plata", NULL, NULL, "DOM", NULL, NULL, 60, NULL, NULL, 65),(931, "Tiaret", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(932, "Ouargla", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(933, "Saïda", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(934, "Aïn Defla", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(935, "Adrar", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(936, "Aïn Témouchent", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(937, "Béchar", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(938, "Biskra", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(939, "Bordj Bou Arreridj‎", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(940, "Chlef", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(941, "Constantine", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(942, "El Tarf", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(943, "Ghardaia", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(944, "Tindouf", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(945, "Skikda", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(946, "Tipaza", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(947, "Tissemsilt", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(948, "Tlemcen", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(949, "Sidi Bel Abbès", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(950, "Sétif", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(951, "Relizane", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(952, "Oum El Bouaghi", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(953, "Naâma", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(954, "M'Sila", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(955, "Mila", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(956, "Illizi", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(957, "Guelma", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(958, "Djelfa", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(959, "El Oued", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(960, "Batna", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(961, "Algiers", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(962, "Laghouat", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(963, "Jijel", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(964, "Mascara", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(965, "Médéa", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(966, "Mostaganem", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(967, "Tébessa", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(968, "Boumerdès", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(969, "Blida", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(970, "Bejaia", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(971, "Tamanrasset", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(972, "Khenchela", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(973, "Oran", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(974, "Bouira", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(975, "El Bayadh", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(976, "Annaba", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(977, "Souk Ahras", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(978, "Tizi Ouzou", NULL, NULL, "DZA", NULL, NULL, 60, NULL, NULL, 66),(979, "Pastaza", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(980, "Carchi", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(981, "Loja", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(982, "Zamora Chinchipe", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(983, "El Oro", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(984, "Esmeraldas", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(985, "Imbabura", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(986, "Sucumbios", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(987, "Santa Elena", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(988, "Santo Domingo de los Tsáchilas", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(989, "Pichincha", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(990, "Manabi", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(991, "Azuay", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(992, "Cañar", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(993, "Guayas", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(994, "Los Ríos", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(995, "Cotopaxi", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(996, "Bolívar", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(997, "Tungurahua", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(998, "Chimborazo", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(999, "Morona Santiago", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(1000, "Napo", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(1001, "Orellana", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(1002, "Galápagos", NULL, NULL, "ECU", NULL, NULL, 60, NULL, NULL, 67),(1003, "North Sinai", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1004, "South Sinai", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1005, "Aswan", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1006, "Red Sea", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1007, "Matrouh", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1008, "New Valley", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1009, "Alexandria", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1010, "Ismailia", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1011, "Suez", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1012, "Gharbiyya", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1013, "Faiyum", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1014, "Beni Suef", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1015, "Minya Governate", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1016, "Asyut", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1017, "Sohag", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1018, "Qena", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1019, "Luxor Governate", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1020, "Giza", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1021, "Monufia", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1022, "Beheira", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1023, "Cairo", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1024, "Qalyubia", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1025, "Dakahlia", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1026, "Damietta", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1027, "Kafr el-Sheikh", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1028, "Port Said", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1029, "Al Sharqia", NULL, NULL, "EGY", NULL, NULL, 60, NULL, NULL, 68),(1030, "Anseba", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1031, "Gash-Barka", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1032, "Debub", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1033, "Maekel", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1034, "Northen Red Sea", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1035, "Southern Red Sea", NULL, NULL, "ERI", NULL, NULL, 60, NULL, NULL, 69),(1036, "Canarias", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1037, "Ciudad Autónoma de Melilla", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1038, "Ciudad Autónoma de Ceuta", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1039, "La Rioja", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1040, "País Vasco/Euskadi", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1041, "Comunidad Foral de Navarra", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1042, "Región de Murcia", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1043, "Comunidad de Madrid", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1044, "Galicia", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1045, "Extremadura", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1046, "Comunitat Valenciana", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1047, "Cataluña/Catalunya", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1048, "Castilla-La Mancha", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1049, "Castilla y León", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1050, "Cantabria", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1051, "Illes Balears", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1052, "Principado de Asturias", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1053, "Aragón", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1054, "Andalucía", NULL, NULL, "ESP", NULL, NULL, 60, NULL, NULL, 70),(1055, "Saare", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1056, "Pärnu", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1057, "Hiiu", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1058, "Lääne", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1059, "Ida-Viru", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1060, "Harju", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1061, "Lääne-Viru", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1062, "Tartu", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1063, "Valga", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1064, "Viljandi", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1065, "Põlva", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1066, "Järva", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1067, "Rapla", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1068, "Võru", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1069, "Jõgeva", NULL, NULL, "EST", NULL, NULL, 60, NULL, NULL, 71),(1070, "Addis Ababa", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1071, "Afar", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1072, "Amhara", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1073, "Beneshangul Gumu", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1074, "Dire Dawa", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1075, "Gambela", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1076, "Hareri", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1077, "Oromia", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1078, "SNNPR", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1079, "Somali", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1080, "Tigray", NULL, NULL, "ETH", NULL, NULL, 60, NULL, NULL, 72),(1081, "Uusimaa", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1082, "Finland Proper", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1083, "Southern Savonia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1084, "Northern Savonia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1085, "Tavastia Proper", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1086, "Central Finland", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1087, "Pirkanmaa", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1088, "South Ostrobothnia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1089, "Keski-Pohjanmaa", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1090, "Päijät-Häme", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1091, "Lapland", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1092, "Northern Ostrobothnia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1093, "Kainuu", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1094, "North Karelia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1095, "Ostrobothnia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1096, "Satakunta", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1097, "South Karelia", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1098, "Kymenlaakso", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1099, "Åland Islands", NULL, NULL, "FIN", NULL, NULL, 60, NULL, NULL, 73),(1100, "Northern", NULL, NULL, "FJI", NULL, NULL, 60, NULL, NULL, 74),(1101, "Eastern", NULL, NULL, "FJI", NULL, NULL, 60, NULL, NULL, 74),(1102, "Central", NULL, NULL, "FJI", NULL, NULL, 60, NULL, NULL, 74),(1103, "Western", NULL, NULL, "FJI", NULL, NULL, 60, NULL, NULL, 74),(1104, "Île-de-France", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1105, "Centre-Val de Loire", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1106, "Bourgogne-Franche-Comté", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1107, "Normandie", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1108, "Hauts-de-France", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1109, "Grand Est", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1110, "Pays de la Loire", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1111, "Bretagne", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1112, "Nouvelle-Aquitaine", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1113, "Occitanie", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1114, "Auvergne-Rhône-Alpes", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1115, "Provence-Alpes-Côte d'Azur", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1116, "Corse", NULL, NULL, "FRA", NULL, NULL, 60, NULL, NULL, 76),(1117, "Yap", NULL, NULL, "FSM", NULL, NULL, 60, NULL, NULL, 78),(1118, "Chuuk", NULL, NULL, "FSM", NULL, NULL, 60, NULL, NULL, 78),(1119, "Pohnpei", NULL, NULL, "FSM", NULL, NULL, 60, NULL, NULL, 78),(1120, "Kosrae", NULL, NULL, "FSM", NULL, NULL, 60, NULL, NULL, 78),(1121, "Woleu-Ntem", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1122, "Estuaire", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1123, "Moyen-Ogooué", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1124, "Ogooué-Lolo", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1125, "Haut-Ogooué", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1126, "Ngounié", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1127, "Ogooué-Maritime", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1128, "Nyanga", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1129, "Ogooué-Ivindo", NULL, NULL, "GAB", NULL, NULL, 60, NULL, NULL, 79),(1130, "Northern Ireland", NULL, NULL, "GBR", NULL, NULL, 60, NULL, NULL, 80),(1131, "England", NULL, NULL, "GBR", NULL, NULL, 60, NULL, NULL, 80),(1132, "Scotland", NULL, NULL, "GBR", NULL, NULL, 60, NULL, NULL, 80),(1133, "Wales", NULL, NULL, "GBR", NULL, NULL, 60, NULL, NULL, 80),(1134, "Abkhazia", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1135, "Kakheti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1136, "Kvemo Kartli", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1137, "Tbilisi", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1138, "Mtskheta-Mtianeti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1139, "Samtskhe–Javakheti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1140, "Adjara", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1141, "Guria", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1142, "Shida Kartli", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1143, "Imereti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1144, "Racha-Lechkhumi and Kvemo Svaneti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1145, "Samegrelo-Zemo Svaneti", NULL, NULL, "GEO", NULL, NULL, 60, NULL, NULL, 81),(1146, "Western North", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1147, "Ahafo", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1148, "Bono East", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1149, "Savannah", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1150, "North East", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1151, "Oti", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1152, "Western", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1153, "Eastern", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1154, "Northern", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1155, "Central", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1156, "Ashanti", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1157, "Bono", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1158, "Volta", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1159, "Upper West", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1160, "Upper East", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1161, "Greater Accra", NULL, NULL, "GHA", NULL, NULL, 60, NULL, NULL, 83),(1162, "Boke", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1163, "Conakry", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1164, "Faranah", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1165, "Kankan", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1166, "Kindia", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1167, "Labe", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1168, "Mamou", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1169, "Nzerekore", NULL, NULL, "GIN", NULL, NULL, 60, NULL, NULL, 85),(1170, "Banjul", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1171, "Basse", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1172, "Brikama", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1173, "Janjanbureh", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1174, "Kanifing", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1175, "Kerewan", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1176, "Kuntaur", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1177, "Mansakonko", NULL, NULL, "GMB", NULL, NULL, 60, NULL, NULL, 87),(1178, "Bissau", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1179, "Bafatá", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1180, "Biombo", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1181, "Bolama", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1182, "Cacheu", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1183, "Gabu", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1184, "Oio", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1185, "Quinara", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1186, "Tombali", NULL, NULL, "GNB", NULL, NULL, 60, NULL, NULL, 88),(1187, "Annobón", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1188, "Bioko Norte", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1189, "Bioko Sur", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1190, "Litoral", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1191, "Kié-Ntem", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1192, "Centro Sur", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1193, "Wele-Nzas", NULL, NULL, "GNQ", NULL, NULL, 60, NULL, NULL, 89),(1194, "Agion Oros", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1195, "Crete", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1196, "Attica", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1197, "Macedonia-Thrace", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1198, "Epirus-Western Macedonia", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1199, "Peloponisos-W. Greece & Ionian", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1200, "Egean", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1201, "Thessalia-Central Greece", NULL, NULL, "GRC", NULL, NULL, 60, NULL, NULL, 90),(1202, "Saint Andrew", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1203, "Saint David", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1204, "Saint Mark", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1205, "Saint John", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1206, "Saint George", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1207, "Saint Patrick", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1208, "Southern Grenadine Islands", NULL, NULL, "GRD", NULL, NULL, 60, NULL, NULL, 91),(1209, "Northeast Greenland National Park", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1210, "Avannaata", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1211, "Sermersooq", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1212, "Kujalleq", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1213, "Qeqqata", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1214, "Qeqertalik", NULL, NULL, "GRL", NULL, NULL, 60, NULL, NULL, 92),(1215, "Alta Verapaz", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1216, "Suchitepéquez", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1217, "El Progreso", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1218, "Zacapa", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1219, "Petén", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1220, "Quiché", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1221, "Chiquimula", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1222, "Sacatepéquez", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1223, "Guatemala", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1224, "Izabal", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1225, "Jalapa", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1226, "San Marcos", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1227, "Huehuetenango", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1228, "Totonicapán", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),
  (1229, "Quetzaltenango", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1230, "Retalhuleu", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1231, "Baja Verapaz", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1232, "Sololá", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1233, "Escuintla", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1234, "Chimaltenango", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1235, "Jutiapa", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1236, "Santa Rosa", NULL, NULL, "GTM", NULL, NULL, 60, NULL, NULL, 93),(1237, "Barina-Waini", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1238, "Pomeroon-Supenaam", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1239, "Essequibo Islands-West Demerara", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1240, "Demerara-Mahaica", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1241, "Cuyuni-Mazaruni", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1242, "Potaro-Siparuni", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1243, "Upper Takutu-Upper Essequibo", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1244, "Mahaica-Berbice", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1245, "East Berbice-Corentyne", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1246, "Upper Demerara-Berbice", NULL, NULL, "GUY", NULL, NULL, 60, NULL, NULL, 96),(1247, "Gracias a Dios", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1248, "Bay Islands", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1249, "Choluteca", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1250, "Colón", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1251, "El Paraíso", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1252, "Olancho", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1253, "Valle", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1254, "Atlántida", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1255, "Comayagua", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1256, "Copán", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1257, "Cortés", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1258, "Francisco Morazán", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1259, "Intibucá", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1260, "La Paz", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1261, "Lempira", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1262, "Ocotepeque", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1263, "Santa Bárbara", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1264, "Yoro", NULL, NULL, "HND", NULL, NULL, 60, NULL, NULL, 97),(1265, "Osijek-Baranja", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1266, "Vukovar-Syrmia", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1267, "Brod-Posavina", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1268, "Virovitica-Podravina", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1269, "Požega-Slavonia", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1270, "Sisak-Moslavina", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1271, "Bjelovar-Bilogora", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1272, "Međimurje", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1273, "Koprivnica-Križevci", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1274, "Varaždin", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1275, "Krapina-Zagorje", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1276, "City of Zagreb", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1277, "Zagreb County", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1278, "Karlovac", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1279, "Istria", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1280, "Primorje-Gorski Kotar", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1281, "Lika-Senj", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1282, "Zadar County", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1283, "Šibenik-Knin", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1284, "Split-Dalmatia", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1285, "Dubrovnik-Neretva", NULL, NULL, "HRV", NULL, NULL, 60, NULL, NULL, 98),(1286, "l'Ouest", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1287, "la Grande-Anse", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1288, "Nippes", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1289, "Sud-Est", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1290, "Nord-Ouest", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1291, "Nord", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1292, "Nord-Est", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1293, "Centre", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1294, "Artibonite", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1295, "Sud", NULL, NULL, "HTI", NULL, NULL, 60, NULL, NULL, 99),(1296, "Pest", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1297, "Szabolcs-Szatmár-Bereg", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1298, "Hajdú-Bihar", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1299, "Borsod-Abaúj-Zemplén", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1300, "Nógrád", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1301, "Heves", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1302, "Békés", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1303, "Csongrád-Csanád", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1304, "Jász-Nagykun-Szolnok", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1305, "Komárom-Esztergom", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1306, "Győr-Moson-Sopron", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1307, "Vas", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1308, "Zala", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1309, "Veszprém", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1310, "Baranya", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1311, "Somogy", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1312, "Tolna", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1313, "Bács-Kiskun", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1314, "Fejér", NULL, NULL, "HUN", NULL, NULL, 60, NULL, NULL, 100),(1315, "Bali", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1316, "West Nusa Tenggara", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1317, "Banten", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1318, "Central Java", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1319, "West Java", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1320, "Central Kalimantan", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1321, "South Kalimantan", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1322, "West Kalimantan", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1323, "Central Sulawesi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1324, "Gorontalo", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1325, "North Sulawesi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1326, "South Sulawesi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1327, "Southeast Sulawesi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1328, "West Sulawesi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1329, "Aceh", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1330, "Bengkulu", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1331, "Jambi", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1332, "Lampung", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1333, "Riau", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1334, "West Sumatra", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1335, "South Sumatra", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1336, "North Sumatra", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1337, "East Nusa Tenggara", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1338, "Maluku", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1339, "North Maluku", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1340, "East Java", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1341, "Bangka-Belitung Islands", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1342, "Riau Islands", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1343, "Papua", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1344, "West Papua", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1345, "East Kalimantan", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1346, "North Kalimantan", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1347, "Special Region of Yogyakarta", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1348, "Jakarta Special Capital Region", NULL, NULL, "IDN", NULL, NULL, 60, NULL, NULL, 102),(1349, "Andaman and Nicobar Islands", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1350, "Andhra Pradesh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1351, "Arunāchal Pradesh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1352, "Assam", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1353, "Bihār", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1354, "Chandīgarh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1355, "Chhattīsgarh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1356, "Delhi", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1357, "Dādra and Nagar Haveli and Damān and Diu", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1358, "Goa", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1359, "Gujarāt", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1360, "Haryāna", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1361, "Himāchal Pradesh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1362, "Jammu and Kashmīr", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1363, "Jhārkhand", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1364, "Karnātaka", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1365, "Kerala", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1366, "Ladākh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1367, "Lakshadweep", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1368, "Madhya Pradesh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1369, "Mahārāshtra", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1370, "Manipur", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1371, "Meghālaya", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1372, "Mizoram", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1373, "Nāgāland", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1374, "Odisha", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1375, "Puducherry", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1376, "Punjab", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1377, "Rājasthān", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1378, "Sikkim", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1379, "Tamil Nādu", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1380, "Telangāna", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1381, "Tripura", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1382, "Uttar Pradesh", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1383, "Uttarākhand", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1384, "West Bengal", NULL, NULL, "IND", NULL, NULL, 60, NULL, NULL, 101),(1385, "Ulster", NULL, NULL, "IRL", NULL, NULL, 60, NULL, NULL, 104),(1386, "Leinster", NULL, NULL, "IRL", NULL, NULL, 60, NULL, NULL, 104),(1387, "Munster", NULL, NULL, "IRL", NULL, NULL, 60, NULL, NULL, 104),(1388, "Connacht", NULL, NULL, "IRL", NULL, NULL, 60, NULL, NULL, 104),(1389, "Mazandaran", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1390, "North Khorasan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1391, "Kerman", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1392, "Ilam", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1393, "Lorestan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1394, "Markazi", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1395, "Chaharmahal and Bakhtiari", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1396, "Kermanshah", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1397, "Hamadan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1398, "Qazvin", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1399, "Gilan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1400, "Zanjan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1401, "Semnan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1402, "Isfahan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1403, "Kohgiluyeh and Boyer-Ahmad", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1404, "Kurdistan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1405, "West Azerbaijan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1406, "Fars", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1407, "Bushehr", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1408, "Ardabil", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1409, "Golestan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1410, "Razavi Khorasan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1411, "South Khorasan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1412, "Sistan and Baluchestan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1413, "Qom", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1414, "Alborz", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1415, "East Azerbaijan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1416, "Yazd", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1417, "Hormozgan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1418, "Khuzestan", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1419, "Tehran", NULL, NULL, "IRN", NULL, NULL, 60, NULL, NULL, 105),(1420, "Al-Anbar", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1421, "Karbala", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1422, "An-Najaf", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1423, "Babil", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1424, "Baghdad", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1425, "Al-Qadisiyah", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1426, "Al-Muthanna", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1427, "Dhi Qar", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1428, "Al-Basrah", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1429, "Maysan", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1430, "Wasit", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1431, "Ninawa", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1432, "Dohuk", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1433, "Salah al-Din", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1434, "Diyala", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1435, "Kirkuk", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1436, "Erbil", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1437, "Al-Sulaimaniyah", NULL, NULL, "IRQ", NULL, NULL, 60, NULL, NULL, 106),(1438, "Capital", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1439, "Eastern", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1440, "Northeastern", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1441, "Northwestern", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1442, "Westfjords", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1443, "Western", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1444, "Southern Peninsula", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1445, "Southern", NULL, NULL, "ISL", NULL, NULL, 60, NULL, NULL, 107),(1446, "Southern", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1447, "Central", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1448, "Jerusalem", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1449, "Tel Aviv", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1450, "Haifa", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1451, "Northern", NULL, NULL, "ISR", NULL, NULL, 60, NULL, NULL, 108),(1452, "Nord-Ovest", NULL, NULL, "ITA", NULL, NULL, 60, NULL, NULL, 109),(1453, "Nord-Est", NULL, NULL, "ITA", NULL, NULL, 60, NULL, NULL, 109),(1454, "Centro", NULL, NULL, "ITA", NULL, NULL, 60, NULL, NULL, 109),(1455, "Sud", NULL, NULL, "ITA", NULL, NULL, 60, NULL, NULL, 109),(1456, "Isole", NULL, NULL, "ITA", NULL, NULL, 60, NULL, NULL, 109),(1457, "Saint Ann", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1458, "Trelawny", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1459, "Saint Mary", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1460, "Portland", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1461, "Saint James", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1462, "Hanover", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1463, "Westmoreland", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1464, "Saint Elizabeth", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1465, "Saint Thomas", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1466, "Clarendon", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1467, "Saint Catherine", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1468, "Manchester", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1469, "Kingston", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1470, "Saint Andrew", NULL, NULL, "JAM", NULL, NULL, 60, NULL, NULL, 110),(1471, "Ma'an", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1472, "Karak", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1473, "Madaba", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1474, "Balqa", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1475, "Jerash", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1476, "Ajloun", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1477, "Irbid", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1478, "Mafraq", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1479, "Tafilah", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1480, "Aqaba", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1481, "Amman", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1482, "Zarqa", NULL, NULL, "JOR", NULL, NULL, 60, NULL, NULL, 111),(1483, "Osaka Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1484, "Oita", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1485, "Hyogo Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1486, "Fukui Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1487, "Shiga", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1488, "Nara Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1489, "Gifu Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1490, "Mie Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1491, "Wakayama Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1492, "Tokyo", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1493, "Fukuoka Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1494, "Saitama", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1495, "Fukushima", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1496, "Tochigi", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1497, "Aomori", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1498, "Saga Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1499, "Nagasaki Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1500, "Miyazaki Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1501, "Kumamoto", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1502, "Kagoshima Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1503, "Gunma", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1504, "Kyoto Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1505, "Chiba", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1506, "Ibaraki", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1507, "Kanagawa", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1508, "Hiroshima", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1509, "Tottori Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1510, "Shimane", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1511, "Niigata", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1512, "Nagano", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1513, "Aichi Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1514, "Yamanashi", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1515, "Miyagi", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1516, "Yamagata", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1517, "Akita", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1518, "Iwate", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1519, "Toyama", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1520, "Shizuoka", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1521, "Ishikawa Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1522, "Okayama Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1523, "Tokushima Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1524, "Kochi Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1525, "Ehime Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1526, "Kagawa Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1527, "Okinawa Prefecture", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1528, "Hokkaido", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1529, "Yamaguchi", NULL, NULL, "JPN", NULL, NULL, 60, NULL, NULL, 112),(1530, "Pavlodar", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1531, "Jambyl", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1532, "Kostanay", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1533, "Mangystau", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1534, "Karaganda", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1535, "Kyzylorda", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1536, "East Kazakhstan", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1537, "Aktobe", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1538, "Atyrau", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1539, "South Kazakhstan", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1540, "Akmola", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1541, "Almaty", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1542, "North Kazakhstan", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1543, "West Kazakhstan", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1544, "Astana", NULL, NULL, "KAZ", NULL, NULL, 60, NULL, NULL, 113),(1545, "Turkana", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1546, "Marsabit", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1547, "Mandera", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1548, "Wajir", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1549, "West Pokot", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1550, "Samburu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1551, "Isiolo", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1552, "Baringo", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1553, "Elgeyo-Marakwet", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1554, "Trans Nzoia", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1555, "Bungoma", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1556, "Garissa", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1557, "Uasin Gishu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1558, "Kakamega", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1559, "Laikipia", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1560, "Busia", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1561, "Meru", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1562, "Nandi", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1563, "Siaya", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1564, "Nakuru", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1565, "Vihiga", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1566, "Nyandarua", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1567, "Tharaka", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1568, "Kericho", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1569, "Kisumu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1570, "Nyeri", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1571, "Tana River", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1572, "Kitui", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1573, "Kirinyaga", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1574, "Embu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1575, "Homa Bay", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1576, "Bomet", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1577, "Nyamira", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1578, "Narok", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1579, "Kisii", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1580, "Murang'a", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1581, "Migori", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1582, "Kiambu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1583, "Machakos", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1584, "Kajiado", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1585, "Nairobi", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),
  (1586, "Makueni", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1587, "Lamu", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1588, "Kilifi", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1589, "Taita Taveta", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1590, "Kwale", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1591, "Mombasa", NULL, NULL, "KEN", NULL, NULL, 60, NULL, NULL, 114),(1592, "Batken", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1593, "Osh", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1594, "Talas", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1595, "Jalal-Abad", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1596, "Issyk-Kul", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1597, "Chuy", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1598, "Naryn", NULL, NULL, "KGZ", NULL, NULL, 60, NULL, NULL, 115),(1599, "Stung Treng", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1600, "Preah Vihear", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1601, "Kampong Thom", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1602, "Phnom Penh", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1603, "Koh Kong", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1604, "Kep", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1605, "Kampot", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1606, "Takeo", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1607, "Kandal", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1608, "Pursat", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1609, "Ratanakiri Province", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1610, "Kampong Chhnang", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1611, "Pailin", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1612, "Bantey Meanchey", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1613, "Siem Reap", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1614, "Oddar Meanchey", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1615, "Kampong Cham", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1616, "Mondulkiri", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1617, "Kratie", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1618, "Prey Veng", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1619, "Kampong Speu", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1620, "Svay Rieng", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1621, "Preah Sihanouk", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1622, "Tbong Khmum", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1623, "Battambang", NULL, NULL, "KHM", NULL, NULL, 60, NULL, NULL, 116),(1624, "Gilbert Islands", NULL, NULL, "KIR", NULL, NULL, 60, NULL, NULL, 117),(1625, "Phoenix Islands", NULL, NULL, "KIR", NULL, NULL, 60, NULL, NULL, 117),(1626, "Line Islands", NULL, NULL, "KIR", NULL, NULL, 60, NULL, NULL, 117),(1627, "Christ Church Nichola Town", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1628, "Saint Anne Sandy Point", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1629, "Saint George Basseterre", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1630, "Saint George Gingerland", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1631, "Saint James Windward", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1632, "Saint John Capisterre", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1633, "Saint John Figtree", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1634, "Saint Mary Cayon", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1635, "Saint Paul Capisterre", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1636, "Saint Paul Charlestown", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1637, "Saint Peter Basseterre", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1638, "Saint Thomas Lowland", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1639, "Saint Thomas Middle Island", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1640, "Trinity Palmetto Point", NULL, NULL, "KNA", NULL, NULL, 60, NULL, NULL, 118),(1641, "Gangwon", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1642, "Gyeonggi", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1643, "South Chungcheong", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1644, "Incheon", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1645, "North Jeolla", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1646, "South Jeolla", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1647, "South Gyeongsang", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1648, "Busan", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1649, "Ulsan", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1650, "North Gyeongsang", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1651, "Jeju", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1652, "Seoul", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1653, "Daejeon", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1654, "Sejong", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1655, "North Chungcheong", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1656, "Gwangju", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1657, "Daegu", NULL, NULL, "KOR", NULL, NULL, 60, NULL, NULL, 119),(1658, "Ahmadi", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1659, "Al Asimah", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1660, "Farwaniya", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1661, "Hawalli", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1662, "Jahra", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1663, "Mubarak Al-Kabeer", NULL, NULL, "KWT", NULL, NULL, 60, NULL, NULL, 120),(1664, "Xaignabouli", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1665, "Attapeu", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1666, "Bokeo", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1667, "Bolikhamsai", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1668, "Champasak", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1669, "Houaphan", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1670, "Khammouane", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1671, "Luang Namtha", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1672, "Luang Prabang", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1673, "Oudomxay", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1674, "Phongsaly", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1675, "Salavan", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1676, "Savannakhet", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1677, "Vientiane Capital", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1678, "Vientiane", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1679, "Xaisomboun", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1680, "Xekong", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1681, "Xiangkhouang", NULL, NULL, "LAO", NULL, NULL, 60, NULL, NULL, 121),(1682, "Baalbek-Hermel", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1683, "Beyrouth", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1684, "Liban-Nord", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1685, "Mont-Liban", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1686, "Liban-Sud", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1687, "Nabatîyé", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1688, "Béqaa", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1689, "Aakkâr", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1690, "Keserwan-Jbeil", NULL, NULL, "LBN", NULL, NULL, 60, NULL, NULL, 122),(1691, "Bomi", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1692, "Bong", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1693, "Gbarpolu", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1694, "Grand Bassa", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1695, "Grand Cape Mount", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1696, "Grand Gedeh", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1697, "Grand Kru", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1698, "Lofa", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1699, "Margibi", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1700, "Maryland", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1701, "Montserrado", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1702, "Nimba", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1703, "River Gee", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1704, "Rivercess", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1705, "Sinoe", NULL, NULL, "LBR", NULL, NULL, 60, NULL, NULL, 123),(1706, "Ghadamis", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1707, "An Nuqat al Khams", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1708, "Al Kufrah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1709, "Murzuq", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1710, "Al Butnan", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1711, "Ajdabiya", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1712, "Ash Shati'", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1713, "Ghat", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1714, "Surt", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1715, "Misratah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1716, "Al Marqab", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1717, "Tajura' wa an Nawahi al Arba", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1718, "Az Zawiyah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1719, "Al Qubbah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1720, "Al Jabal al Akhdar", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1721, "Al Marj", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1722, "Benghazi", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1723, "Al Jifarah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1724, "Mizdah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1725, "Al Jufrah", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1726, "Sabha", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1727, "Wadi al Hayaa", NULL, NULL, "LBY", NULL, NULL, 60, NULL, NULL, 124),(1728, "Vieux Fort", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1729, "Anse la Raya", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1730, "Castries", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1731, "Gros Islet", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1732, "Dennery", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1733, "Micoud", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1734, "Soufrière", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1735, "Choiseul", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1736, "Laborie", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1737, "Canaries", NULL, NULL, "LCA", NULL, NULL, 60, NULL, NULL, 125),(1738, "Triesen", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1739, "Schellenberg", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1740, "Gamprin", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1741, "Triesenberg", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1742, "Eschen", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1743, "Ruggell", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1744, "Mauren", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1745, "Schaan", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1746, "Balzers", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1747, "Planken", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1748, "Vaduz", NULL, NULL, "LIE", NULL, NULL, 60, NULL, NULL, 126),(1749, "Northern", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1750, "Eastern", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1751, "Central", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1752, "North Central", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1753, "North Western", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1754, "Sabaragamuwa", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1755, "Southern", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1756, "Uva", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1757, "Western", NULL, NULL, "LKA", NULL, NULL, 60, NULL, NULL, 127),(1758, "Mokhotlong", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1759, "Maseru", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1760, "Butha-Buthe", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1761, "Mohale's Hoek", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1762, "Mafeteng", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1763, "Quthing", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1764, "Berea", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1765, "Thaba-Tseka", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1766, "Qacha's Nek", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1767, "Leribe", NULL, NULL, "LSO", NULL, NULL, 60, NULL, NULL, 128),(1768, "Utena", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1769, "Marijampole", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1770, "Vilnius", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1771, "Panevezys", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1772, "Kaunas", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1773, "Alytus", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1774, "Klaipeda", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1775, "Telsiai", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1776, "Siauliai", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1777, "Taurage", NULL, NULL, "LTU", NULL, NULL, 60, NULL, NULL, 129),(1778, "Echternach", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1779, "Grevenmacher", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1780, "Remich", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1781, "Mersch", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1782, "Capellen", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1783, "Luxembourg", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1784, "Redange", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1785, "Diekirch", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1786, "Esch-sur-Alzette", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1787, "Clervaux", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1788, "Wiltz", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1789, "Vianden", NULL, NULL, "LUX", NULL, NULL, 60, NULL, NULL, 130),(1790, "Alūksnes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1791, "Līvānu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1792, "Gulbenes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1793, "Ventspils", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1794, "Valkas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1795, "Salaspils", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1796, "Jelgavas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1797, "Rēzeknes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1798, "Jūrmalas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1799, "Rīga", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1800, "Liepājas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1801, "Dienvidkurzemes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1802, "Kuldīgas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1803, "Saldus", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1804, "Talsu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1805, "Tukuma", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1806, "Dobeles", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1807, "Mārupes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1808, "Bauskas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1809, "Ogres", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1810, "Aizkraukles", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1811, "Jēkabpils", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1812, "Ludzas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1813, "Balvu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1814, "Madonas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1815, "Smiltenes", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1816, "Cēsu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1817, "Valmieras", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1818, "Ādažu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1819, "Ropažu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1820, "Siguldas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1821, "Preiļu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1822, "Krāslavas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1823, "Daugavpils", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1824, "Ķekavas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1825, "Olaines", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1826, "Saulkrastu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1827, "Limbažu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1828, "Augšdaugavas", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1829, "Varakļānu", NULL, NULL, "LVA", NULL, NULL, 60, NULL, NULL, 131),(1830, "Laâyoune-Sakia El Hamra", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1831, "Tangier-Tetouan-Al Hoceima", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1832, "Oriental", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1833, "Drâa-Tafilalet", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1834, "Souss-Massa", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1835, "Guelmim-Oued Noun", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1836, "Casablanca-Settat", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1837, "Marrakech-Safi", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1838, "Dakhla-Oued Ed-Dahab", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1839, "Rabat-Salé-Kenitra", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1840, "Fez-Meknes", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1841, "Béni Mellal-Khénifra", NULL, NULL, "MAR", NULL, NULL, 60, NULL, NULL, 132),(1842, "Monaco", NULL, NULL, "MCO", NULL, NULL, 60, NULL, NULL, 133),(1843, "Cahul", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1844, "Gagauzia", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1845, "Taraclia", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1846, "Cantemir", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1847, "Basarabeasca", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1848, "Leova", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1849, "Cimislia", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1850, "Stefan Voda", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1851, "Bender", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1852, "Causeni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1853, "Hincesti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1854, "Ialoveni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1855, "Nisporeni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1856, "Chisinau", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1857, "Anenii Noi", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1858, "Criuleni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1859, "Straseni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1860, "Ungheni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1861, "Dubasari", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1862, "Calarasi", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1863, "Transnistria", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1864, "Falesti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1865, "Orhei", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1866, "Glodeni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1867, "Balti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1868, "Telenesti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1869, "SIngerei", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1870, "RIscani", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1871, "Rezina", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1872, "Soldanesti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1873, "Floresti", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1874, "Drochia", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1875, "Edinet", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1876, "Briceni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1877, "Soroca", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1878, "Donduseni", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1879, "Ocnita", NULL, NULL, "MDA", NULL, NULL, 60, NULL, NULL, 134),(1880, "Diana", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1881, "Sava", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1882, "Analanjirofo", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1883, "Amoron'i Mania", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1884, "Ihorombe", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1885, "Melaky", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1886, "Menabe", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1887, "Vakinankaratra", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1888, "Atsinanana", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1889, "Alaotra-Mangoro", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1890, "Sofia", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1891, "Anosy", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1892, "Boeny", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1893, "Betsiboka", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1894, "Analamanga", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1895, "Bongolava", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1896, "Itasy", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1897, "Atsimo-Andrefana", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1898, "Androy", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1899, "Atsimo-Atsinanana", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1900, "Matsiatra Ambony", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1901, "Vatovavy-Fitovinany", NULL, NULL, "MDG", NULL, NULL, 60, NULL, NULL, 135),(1902, "Haa Alif", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1903, "Haa Dhaalu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1904, "Noonu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1905, "Shaviyani", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1906, "Lhaviyani", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1907, "Raa", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1908, "Baa", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1909, "Malé", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1910, "Alif Alif", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1911, "Alif Dhaalu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1912, "Meemu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1913, "Dhaalu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1914, "Faafu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1915, "Thaa", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1916, "Laamu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1917, "Gaafu Alif", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1918, "Gaafu Dhaalu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1919, "Gnaviyani", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1920, "Addu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1921, "Kaafu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1922, "Vaavu", NULL, NULL, "MDV", NULL, NULL, 60, NULL, NULL, 136),(1923, "Aguascalientes", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1924, "Baja California", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1925, "Baja California Sur", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1926, "Campeche", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1927, "Chiapas", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1928, "Chihuahua", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1929, "Coahuila de Zaragoza", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1930, "Colima", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1931, "Distrito Federal", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1932, "Durango", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1933, "Guanajuato", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1934, "Guerrero", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1935, "Hidalgo", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1936, "Jalisco", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1937, "Mexico", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1938, "Michoacan de Ocampo", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1939, "Morelos", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1940, "Nayarit", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1941, "Nuevo Leon", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1942, "Oaxaca", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1943, "Puebla", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1944, "Queretaro de Arteaga", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1945, "Quintana Roo", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),
  (1946, "San Luis Potosi", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1947, "Sinaloa", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1948, "Sonora", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1949, "Tabasco", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1950, "Tamaulipas", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1951, "Tlaxcala", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1952, "Veracruz de Ignacio de la Llave", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1953, "Yucatan", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1954, "Zacatecas", NULL, NULL, "MEX", NULL, NULL, 60, NULL, NULL, 137),(1955, "Kwajalein", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1956, "Kili", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1957, "Rongelap", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1958, "Likiep", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1959, "Lib", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1960, "Namu", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1961, "Ailinglaplap", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1962, "Ailuk", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1963, "Arno", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1964, "Aur", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1965, "Ebon", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1966, "Enewetak", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1967, "Jabat", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1968, "Lae", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1969, "Majuro", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1970, "Mejit", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1971, "Namdrik", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1972, "Ujae", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1973, "Utirik", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1974, "Wotho", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1975, "Jaluit", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1976, "Mili", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1977, "Maloelap", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1978, "Wotje", NULL, NULL, "MHL", NULL, NULL, 60, NULL, NULL, 138),(1979, "Pelagonia", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1980, "Southwest", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1981, "Vardar", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1982, "Polog", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1983, "Southeast", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1984, "East", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1985, "Skopje", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1986, "Northeast", NULL, NULL, "MKD", NULL, NULL, 60, NULL, NULL, 139),(1987, "Bamako", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1988, "Gao", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1989, "Kayes", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1990, "Kidal", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1991, "Koulikouro", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1992, "Mopti", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1993, "Segou", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1994, "Sikasso", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1995, "Tombouctou", NULL, NULL, "MLI", NULL, NULL, 60, NULL, NULL, 140),(1996, "Ghajnsielem", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(1997, "Qala", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(1998, "Saint Lawerence", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(1999, "Gharb", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2000, "Ghasri", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2001, "Żebbuġ Gozo", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2002, "Kerċem", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2003, "Munxar", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2004, "Fontana", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2005, "Rabat Gozo", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2006, "Xgħajra", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2007, "Sannat", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2008, "Xewkija", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2009, "Nadur", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2010, "Marsaskala", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2011, "Mellieħa", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2012, "Gżira", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2013, "Mġarr", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2014, "Saint Paul's Bay", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2015, "Naxxar", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2016, "Għargħur", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2017, "Pembroke", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2018, "Saint Julian's", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2019, "Sliema", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2020, "Swieqi", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2021, "Saint John", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2022, "Iklin", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2023, "Valletta", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2024, "Ta' Xbiex", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2025, "Floriana", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2026, "Pietà", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2027, "Msida", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2028, "Ħamrun", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2029, "Santa Venera", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2030, "Birkirkara", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2031, "Balzan", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2032, "Lija", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2033, "Mosta", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2034, "Mdina", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2035, "Mtarfa", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2036, "Rabat Malta", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2037, "Dingli", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2038, "Attard", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2039, "Xagħra", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2040, "Kalkara", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2041, "Birgu", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2042, "Żebbuġ Malta", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2043, "Qormi", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2044, "Isla", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2045, "Siġġiewi", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2046, "Luqa", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2047, "Marsa", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2048, "Qrendi", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2049, "Mqabba", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2050, "Kirkop", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2051, "Safi", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2052, "Żurrieq", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2053, "Paola", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2054, "Bormla", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2055, "Tarxien", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2056, "Fgura", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2057, "Saint Lucia's", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2058, "Gudja", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2059, "Għaxaq", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2060, "Birżebbuġa", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2061, "Żejtun", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2062, "Żabbar", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2063, "Marsaxlokk", NULL, NULL, "MLT", NULL, NULL, 60, NULL, NULL, 141),(2064, "Ayeyarwady", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2065, "Chin", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2066, "Saigang", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2067, "Kachin", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2068, "Kayah", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2069, "Kayin", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2070, "Magway", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2071, "Mandalay", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2072, "Mon", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2073, "Rakhine", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2074, "Tanitharyi", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2075, "Bago", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2076, "Yangon", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2077, "Shan", NULL, NULL, "MMR", NULL, NULL, 60, NULL, NULL, 142),(2078, "Herceg Novi", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2079, "Plav", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2080, "Rožaje", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2081, "Andrijevica", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2082, "Berane", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2083, "Podgorica", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2084, "Bar", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2085, "Bijelo Polje", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2086, "Budva", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2087, "Cetinje", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2088, "Danilovgrad", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2089, "Kolašin", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2090, "Kotor", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2091, "Mojkovac", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2092, "Nikšić", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2093, "Pljevlja", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2094, "Plužine", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2095, "Tivat", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2096, "Ulcinj", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2097, "Šavnik", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2098, "Žabljak", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2099, "Gusinje", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2100, "Petnjica", NULL, NULL, "MNE", NULL, NULL, 60, NULL, NULL, 143),(2101, "Uvs", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2102, "Khovd", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2103, "Zavkhan", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2104, "Bulgan", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2105, "Dornogovi", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2106, "Ulaanbaatar", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2107, "Govisumber", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2108, "Sükhbaatar", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2109, "Govi-Altai", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2110, "Orkhon", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2111, "Arkhangai", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2112, "Bayankhongor", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2113, "Dundgovi", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2114, "Ömnögovi", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2115, "Övörkhangai", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2116, "Töv", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2117, "Dornod", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2118, "Selenge", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2119, "Hovsgel", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2120, "Bayan-Ölgii", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2121, "Darkhan-Uul", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2122, "Khentii", NULL, NULL, "MNG", NULL, NULL, 60, NULL, NULL, 144),(2123, "Cabo Delgado", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2124, "Nampula", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2125, "Niassa", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2126, "Gaza", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2127, "Inhambane", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2128, "Manica", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2129, "Maputo", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2130, "Sofala", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2131, "Tete", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2132, "Zambézia", NULL, NULL, "MOZ", NULL, NULL, 60, NULL, NULL, 146),(2133, "Adrar", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2134, "Assaba", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2135, "Brakna", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2136, "Dakhlet Nouadhibou", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2137, "Gorgol", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2138, "Guidimaka", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2139, "Hodh ech Chargui", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2140, "Hodh el Gharbi", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2141, "Inchiri", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2142, "Nouakchott", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2143, "Tagant", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2144, "Tiris Zemmour", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2145, "Trarza", NULL, NULL, "MRT", NULL, NULL, 60, NULL, NULL, 147),(2146, "Black River", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2147, "Flacq", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2148, "Grand Port", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2149, "Moka", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2150, "Pamplemousses", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2151, "Plaines Wilhems", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2152, "Port Louis", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2153, "Rivière du Rempart", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2154, "Savanne", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2155, "Rodrigues", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2156, "St. Brandon", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2157, "Agaléga", NULL, NULL, "MUS", NULL, NULL, 60, NULL, NULL, 150),(2158, "Central", NULL, NULL, "MWI", NULL, NULL, 60, NULL, NULL, 151),(2159, "Southern", NULL, NULL, "MWI", NULL, NULL, 60, NULL, NULL, 151),(2160, "Northern", NULL, NULL, "MWI", NULL, NULL, 60, NULL, NULL, 151),(2161, "Selangor", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2162, "Johor", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2163, "Kuala Lumpur", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2164, "Malacca", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2165, "Negeri Sembilan", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2166, "Sabah", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2167, "Sarawak", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2168, "Kelantan", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2169, "Putrajaya", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2170, "Terengganu", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2171, "Pahang", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2172, "Kedah", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2173, "Perlis", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2174, "Perak", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2175, "Penang", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2176, "Labuan", NULL, NULL, "MYS", NULL, NULL, 60, NULL, NULL, 152),(2177, "Karas", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2178, "Kunene", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2179, "Cunene", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2180, "Omusati", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2181, "Ohangwena", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2182, "Kavango", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2183, "Caprivi", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2184, "Hardap", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2185, "Otjozondjupa", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2186, "Omaheke", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2187, "Erongo", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2188, "Khomas", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2189, "Oshana", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2190, "Oshikoto", NULL, NULL, "NAM", NULL, NULL, 60, NULL, NULL, 154),(2191, "Tahoua/Agadez", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2192, "Dossa", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2193, "Niamey", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2194, "Tillaberi", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2195, "Maradi", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2196, "Zinder/Diffa", NULL, NULL, "NER", NULL, NULL, 60, NULL, NULL, 156),(2197, "Cross River", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2198, "Abuja Federal Capital Territory", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2199, "Ogun", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2200, "Oyo", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2201, "Sokoto", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2202, "Zamfara", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2203, "Lagos", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2204, "Akwa Ibom", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2205, "Bayelsa", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2206, "Ondo", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2207, "Delta", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2208, "Rivers", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2209, "Kwara", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2210, "Kogi", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2211, "Benue", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2212, "Borno", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2213, "Katsina", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2214, "Plateau", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2215, "Edo", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2216, "Jigawa", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2217, "Anambra", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2218, "Kano", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2219, "Nasarawa", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2220, "Kebbi", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2221, "Imo", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2222, "Gombe", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2223, "Adamawa", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2224, "Yobe", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2225, "Abia", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2226, "Ekiti", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2227, "Osun", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2228, "Bauchi", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2229, "Niger", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2230, "Kaduna", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2231, "Enugu", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2232, "Taraba", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2233, "Ebonyi", NULL, NULL, "NGA", NULL, NULL, 60, NULL, NULL, 157),(2234, "Rio San Juan", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2235, "Boaco", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2236, "Chontales", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2237, "Managua", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2238, "Leon", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2239, "Chinandega", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2240, "Madriz", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2241, "Estelí", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2242, "Nueva Segovia", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2243, "Matagalpa", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2244, "North Carribean Coast Autonomous Region", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2245, "South Atlantic Autonomous Region", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2246, "Jinotega", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2247, "Carazo", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2248, "Granada", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2249, "Masaya", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2250, "Rivas", NULL, NULL, "NIC", NULL, NULL, 60, NULL, NULL, 158),(2251, "Tuapa", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2252, "Namukulu", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2253, "Hikutavake", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2254, "Toi", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2255, "Mutalau", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2256, "Lakepa", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2257, "Liku", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2258, "Hakupu", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2259, "Vaiea", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2260, "Avatele", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2261, "Tamakautoga", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2262, "Alofi South", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2263, "Alofi North", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2264, "Makefu", NULL, NULL, "NIU", NULL, NULL, 60, NULL, NULL, 159),(2265, "Groningen", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2266, "Drenthe", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2267, "Utrecht", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2268, "Flevoland", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2269, "Overijssel", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2270, "Noord-Holland", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2271, "Gelderland", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2272, "Zuid-Holland", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2273, "Fryslân", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2274, "Noord-Brabant", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2275, "Limburg", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2276, "Zeeland", NULL, NULL, "NLD", NULL, NULL, 60, NULL, NULL, 160),(2277, "Vestfold og Telemark", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2278, "Agder", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2279, "Viken", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2280, "Rogaland", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2281, "Nordland", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2282, "Møre og Romsdal", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2283, "Vestland", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2284, "Oslo", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2285, "Trøndelag", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2286, "Troms og Finnmark", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2287, "Innlandet", NULL, NULL, "NOR", NULL, NULL, 60, NULL, NULL, 161),(2288, "Province 1", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2289, "Province 2", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2290, "Bagmati", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2291, "Gandaki", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2292, "Lumbini", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2293, "Karnali", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2294, "Sudurpaschim", NULL, NULL, "NPL", NULL, NULL, 60, NULL, NULL, 162),(2295, "Meneng", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2296, "Buada", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2297, "Anibare", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2298, "Ijuw", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2299, "Anabar", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2300, "Anetan", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2301, "Ewa", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2302, "Baiti", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2303, "Uaboe", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2304, "Nibok", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2305, "Denigomodu", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2306, "Aiwo", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2307, "Yaren", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2308, "Boe", NULL, NULL, "NRU", NULL, NULL, 60, NULL, NULL, 163),(2309, "Northland", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2310, "Auckland", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2311, "Waikato", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2312, "Bay of Plenty", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2313, "Gisborne", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2314, "Hawke's Bay", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2315, "Taranaki", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),
  (2316, "Manawatu-Wanganui", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2317, "Wellington", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2318, "West Coast", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2319, "Canterbury", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2320, "Otago", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2321, "Southland", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2322, "Tasman", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2323, "Nelson", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2324, "Marlborough", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2325, "Chatham Islands Territory", NULL, NULL, "NZL", NULL, NULL, 60, NULL, NULL, 164),(2326, "Dhofar", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2327, "Al Batinah", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2328, "Az Zahirah", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2329, "Muscat", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2330, "Ash Sharqiyah", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2331, "Ad Dakhiliyah", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2332, "Al Wusta", NULL, NULL, "OMN", NULL, NULL, 60, NULL, NULL, 165),(2333, "Balochistan", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2334, "Sindh", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2335, "Gilgit-Baltistan", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2336, "Azad Kashmir", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2337, "Khyber Pakhtunkhwa", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2338, "Punjab", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2339, "Islamabad Capital Territory", NULL, NULL, "PAK", NULL, NULL, 60, NULL, NULL, 166),(2340, "Bocas del Toro", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2341, "Colón", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2342, "Darién", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2343, "Comarca Emberá-Wounaan", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2344, "Comarca Guna Yala", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2345, "Herrera", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2346, "Los Santos", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2347, "Comarca Ngäbe-Buglé", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2348, "Panamá Oeste", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2349, "Panamá", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2350, "Veraguas", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2351, "Chiriquí", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2352, "Coclé", NULL, NULL, "PAN", NULL, NULL, 60, NULL, NULL, 167),(2353, "Puno", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2354, "Tumbes", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2355, "Piura", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2356, "Lambayeque", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2357, "Cajamarca", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2358, "Amazonas", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2359, "La Libertad", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2360, "Ancash", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2361, "San Martín", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2362, "Huánuco", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2363, "Pasco", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2364, "Lima", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2365, "El Callao", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2366, "Municipalidad Metropolitana de Lima", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2367, "Ucayali", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2368, "Junín", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2369, "Ica", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2370, "Huancavelica", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2371, "Madre de Dios", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2372, "Cusco", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2373, "Apurímac", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2374, "Ayacucho", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2375, "Arequipa", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2376, "Moquegua", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2377, "Tacna", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2378, "Loreto", NULL, NULL, "PER", NULL, NULL, 60, NULL, NULL, 169),(2379, "ARMM", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2380, "CAR", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2381, "NCR", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2382, "Ilocos Region", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2383, "Cagayan Valley", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2384, "Central Luzon", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2385, "Calabarzon", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2386, "Mimaropa", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2387, "Zamboanga Peninsula", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2388, "Bicol Region", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2389, "Western Visayas", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2390, "Central Visayas", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2391, "Eastern Visayas", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2392, "Northern Mindanao", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2393, "Davao Region", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2394, "Soccsksargen", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2395, "Caraga", NULL, NULL, "PHL", NULL, NULL, 60, NULL, NULL, 170),(2396, "Aimeliik", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2397, "Airai", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2398, "Angaur", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2399, "Hatohobei", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2400, "Koror", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2401, "Melekeok", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2402, "Ngaraard", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2403, "Ngarchelong", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2404, "Ngardmau", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2405, "Ngeremlengui", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2406, "Ngatpang", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2407, "Ngchesar", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2408, "Ngiwal", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2409, "Peleliu", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2410, "Sonsorol", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2411, "Kayangel", NULL, NULL, "PLW", NULL, NULL, 60, NULL, NULL, 171),(2412, "Autonomous Region of Bougainville", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2413, "Central Province", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2414, "Chimbu (Simbu)", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2415, "East New Britain", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2416, "East Sepik", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2417, "Eastern Highlands", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2418, "Enga", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2419, "Gulf", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2420, "Hela", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2421, "Jiwaka", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2422, "Madang", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2423, "Manus", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2424, "Milne Bay", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2425, "Morobe", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2426, "National Capital District", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2427, "New Ireland", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2428, "Northern (Oro)", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2429, "Southern Highlands", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2430, "West New Britain", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2431, "West Sepik (Sandaun)", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2432, "Western Highlands", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2433, "Western", NULL, NULL, "PNG", NULL, NULL, 60, NULL, NULL, 172),(2434, "Subcarpathian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2435, "Podlaskie", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2436, "Łódź", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2437, "Lower Silesian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2438, "Opole", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2439, "Świętokrzyskie", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2440, "Masovian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2441, "Greater Poland", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2442, "Lesser Poland", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2443, "Lubusz", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2444, "Kuyavian-Pomeranian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2445, "Pomeranian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2446, "West Pomeranian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2447, "Lublin", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2448, "Warmian-Masurian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2449, "Silesian", NULL, NULL, "POL", NULL, NULL, 60, NULL, NULL, 173),(2450, "Jagang", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2451, "Kangwon", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2452, "Nampo", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2453, "North Hamgyong", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2454, "North Hwanghae", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2455, "North Pyongan", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2456, "Pyongyang", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2457, "Ryanggang", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2458, "South Hamgyong", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2459, "South Hwanghae", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2460, "South Pyongan", NULL, NULL, "PRK", NULL, NULL, 60, NULL, NULL, 236),(2461, "Região Autónoma da Madeira", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2462, "Região Autonoma dos Açores", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2463, "Aveiro", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2464, "Beja", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2465, "Braga", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2466, "Braganãa", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2467, "Castelo Branco", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2468, "Coimbra", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2469, "Ãvora", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2470, "Faro", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2471, "Guarda", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2472, "Leiria", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2473, "Lisboa", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2474, "Portalegre", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2475, "Porto", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2476, "Santarãm", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2477, "Setãbal", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2478, "Viana do Castelo", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2479, "Vila Real", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2480, "Viseu", NULL, NULL, "PRT", NULL, NULL, 60, NULL, NULL, 174),(2481, "Asuncion", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2482, "Concepcion", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2483, "San Pedro", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2484, "Cordillera", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2485, "Guaira", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2486, "Caaguazu", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2487, "Caazapa", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2488, "Itapua", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2489, "Misiones", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2490, "Paraguari", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2491, "Alto Parana", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2492, "Central", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2493, "Ñeembucu", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2494, "Amambay", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2495, "Canındeyu", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2496, "Presidente Hayes", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2497, "Boqueron", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2498, "Alto Paraguay", NULL, NULL, "PRY", NULL, NULL, 60, NULL, NULL, 175),(2499, "West Bank", NULL, NULL, "PSE", NULL, NULL, 60, NULL, NULL, 176),(2500, "Gaza", NULL, NULL, "PSE", NULL, NULL, 60, NULL, NULL, 176),(2501, "Al Khor and Al Thakhira", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2502, "Al Wakra", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2503, "Al Shamal", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2504, "Al Rayyan", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2505, "Al Sheehaniya", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2506, "Doha", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2507, "Umm Slal", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2508, "Al Daayen", NULL, NULL, "QAT", NULL, NULL, 60, NULL, NULL, 178),(2509, "Alba", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2510, "Arad", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2511, "Arges", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2512, "Bacau", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2513, "Bihor", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2514, "Bistrita-Nasaud", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2515, "Botosani", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2516, "Braila", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2517, "Brasov", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2518, "Buzau", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2519, "Calarası", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2520, "Caras-Severin", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2521, "Cluj", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2522, "Constanta", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2523, "Covasna", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2524, "Dambovita", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2525, "Dolj", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2526, "Galati", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2527, "Giurgiu", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2528, "Gorj", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2529, "Harghita", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2530, "Hunedoara", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2531, "Ialomita", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2532, "Iasi", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2533, "Ilfov", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2534, "Maramures", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2535, "Mehedinti", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2536, "Bucuresti", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2537, "Mures", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2538, "Neamt", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2539, "Olt", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2540, "Prahova", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2541, "Salaj", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2542, "Satu Mare", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2543, "Sibiu", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2544, "Suceava", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2545, "Teleorman", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2546, "Timis", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2547, "Tulcea", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2548, "Valcea", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2549, "Vaslui", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2550, "Vrancea", NULL, NULL, "ROU", NULL, NULL, 60, NULL, NULL, 180),(2551, "Altai Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2552, "Republic of Mordovia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2553, "Tula", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2554, "Kurgan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2555, "Ingushetia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2556, "Khanty-Mansiysk Autonomous Okrug – Ugra", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2557, "Kirov", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2558, "Komi Republic", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2559, "Kostroma", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2560, "Krasnoyarsk Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2561, "Zabaykalsky Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2562, "Sverdlovsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2563, "Volgograd", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2564, "Irkutsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2565, "Perm Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2566, "Pskov", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2567, "Rostov", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2568, "Ryazan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2569, "Adygea", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2570, "Samara", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2571, "Khakassia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2572, "Tambov", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2573, "Tatarstan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2574, "Tomsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2575, "Nizhny Novgorod", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2576, "Republic of Karelia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2577, "Arkhangelsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2578, "Astrakhan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2579, "Belgorod", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2580, "Bryansk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2581, "Buryatia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2582, "Chechnya", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2583, "Chelyabinsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2584, "Chuvashia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2585, "Tyumen", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2586, "North Ossetia–Alania", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2587, "Penza", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2588, "Amur", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2589, "Kabardino-Balkaria", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2590, "Krasnodar Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2591, "Kursk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2592, "Leningrad", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2593, "Mari El", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2594, "Moscow", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2595, "Murmansk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2596, "Nenets Autonomous Okrug", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2597, "Novgorod", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2598, "Novosibirsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2599, "Omsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2600, "Oryol", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2601, "Saint Petersburg", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2602, "Sakhalin", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2603, "Sakha Republic", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2604, "Saratov", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2605, "Smolensk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2606, "Stavropol Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2607, "Tuva", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2608, "Tver", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2609, "Udmurtia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2610, "Kaluga", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2611, "Lipetsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2612, "Magadan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2613, "Ulyanovsk", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2614, "Vladimir", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2615, "Vologda", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2616, "Yaroslavl", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2617, "Voronezh", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2618, "Yamalo-Nenets Autonomous Okrug", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2619, "Altai Republic", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2620, "Ivanovo", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2621, "Jewish Autonomous", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2622, "Kalmykia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2623, "Kamchatka Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2624, "Karachay-Cherkessia", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2625, "Kemerovo", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2626, "Khabarovsk Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2627, "Chukotka Autonomous Okrug", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2628, "Dagestan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2629, "Kaliningrad", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2630, "Orenburg", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2631, "Primorsky Krai", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2632, "Bashkortostan", NULL, NULL, "RUS", NULL, NULL, 60, NULL, NULL, 181),(2633, "City of Kigali", NULL, NULL, "RWA", NULL, NULL, 60, NULL, NULL, 237),(2634, "Southern", NULL, NULL, "RWA", NULL, NULL, 60, NULL, NULL, 237),(2635, "Northern", NULL, NULL, "RWA", NULL, NULL, 60, NULL, NULL, 237),(2636, "Eastern", NULL, NULL, "RWA", NULL, NULL, 60, NULL, NULL, 237),(2637, "Western", NULL, NULL, "RWA", NULL, NULL, 60, NULL, NULL, 237),(2638, "Eastern", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2639, "Najran", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2640, "Northern Borders", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2641, "Hayel", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2642, "Riyadh", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2643, "Asir", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2644, "Makkah", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2645, "Tabuk", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2646, "Al Madinah", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2647, "Al-Qassim", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2648, "Al Bahah", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2649, "Jazan", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2650, "Al Jawf", NULL, NULL, "SAU", NULL, NULL, 60, NULL, NULL, 182),(2651, "Abyei PCA", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2652, "Gezira", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2653, "Blue Nile", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2654, "Central Darfur", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2655, "East Darfur", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2656, "Gedaref", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2657, "Kassala", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2658, "Khartoum", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2659, "North Darfur", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2660, "North Kordofan", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2661, "Northern", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2662, "Red Sea", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2663, "River Nile", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2664, "Sennar", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2665, "South Darfur", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2666, "South Kordofan", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2667, "West Darfur", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2668, "West Kordofan", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2669, "White Nile", NULL, NULL, "SDN", NULL, NULL, 60, NULL, NULL, 183),(2670, "Dakar", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2671, "Diourbel", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2672, "Fatick", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2673, "Kaffrine", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),
  (2674, "Kaolack", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2675, "Kolda", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2676, "Louga", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2677, "Matam", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2678, "Saint Louis", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2679, "Sedhiou", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2680, "Tambacounda", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2681, "Thies", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2682, "Ziguinchor", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2683, "Kedougou", NULL, NULL, "SEN", NULL, NULL, 60, NULL, NULL, 184),(2684, "Central Region", NULL, NULL, "SGP", NULL, NULL, 60, NULL, NULL, 185),(2685, "East Region", NULL, NULL, "SGP", NULL, NULL, 60, NULL, NULL, 185),(2686, "North-East region", NULL, NULL, "SGP", NULL, NULL, 60, NULL, NULL, 185),(2687, "North Region", NULL, NULL, "SGP", NULL, NULL, 60, NULL, NULL, 185),(2688, "West Region", NULL, NULL, "SGP", NULL, NULL, 60, NULL, NULL, 185),(2689, "Rennell and Bellona", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2690, "Makira", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2691, "Temotu", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2692, "Malaita", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2693, "Capital Territory (Honiara)", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2694, "Guadalcanal", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2695, "Central", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2696, "Western", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2697, "Isabel", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2698, "Choiseul", NULL, NULL, "SLB", NULL, NULL, 60, NULL, NULL, 187),(2699, "Southern", NULL, NULL, "SLE", NULL, NULL, 60, NULL, NULL, 188),(2700, "Eastern", NULL, NULL, "SLE", NULL, NULL, 60, NULL, NULL, 188),(2701, "Western Area", NULL, NULL, "SLE", NULL, NULL, 60, NULL, NULL, 188),(2702, "Northern", NULL, NULL, "SLE", NULL, NULL, 60, NULL, NULL, 188),(2703, "Cuscatlán", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2704, "Usulután", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2705, "La Libertad", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2706, "Ahuachapán", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2707, "Sonsonate", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2708, "San Vicente", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2709, "San Miguel", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2710, "La Unión", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2711, "Morazán", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2712, "La Paz", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2713, "Santa Ana", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2714, "Chalatenango", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2715, "San Salvador", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2716, "Cabañas", NULL, NULL, "SLV", NULL, NULL, 60, NULL, NULL, 189),(2717, "Acquaviva", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2718, "Borgo Maggiore", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2719, "Chiesanuova", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2720, "Domagnano", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2721, "Faetano", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2722, "Fiorentino", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2723, "Montegiardino", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2724, "Città di San Marino", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2725, "Serravalle", NULL, NULL, "SMR", NULL, NULL, 60, NULL, NULL, 190),(2726, "Hiiraan", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2727, "Sanaag", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2728, "Banadir", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2729, "Togdheer", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2730, "Bakool", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2731, "Gedo", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2732, "Middle Shebelle", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2733, "Woqooyi Galbeed", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2734, "Sool", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2735, "Nugaal", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2736, "Mudug", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2737, "Middle Juba", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2738, "Lower Shebelle", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2739, "Lower Juba", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2740, "Galgaduud", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2741, "Bay", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2742, "Bari", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2743, "Awdal", NULL, NULL, "SOM", NULL, NULL, 60, NULL, NULL, 191),(2744, "Syrmia", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2745, "South Banat", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2746, "North Banat", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2747, "North Backa", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2748, "Central Banat", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2749, "West Backa", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2750, "South Backa", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2751, "Belgrade", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2752, "Bor", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2753, "Macva", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2754, "Pcinja", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2755, "Kolubara", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2756, "Podunavlje", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2757, "Branicevo", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2758, "Sumadija", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2759, "Pomoravlje", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2760, "Moravica", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2761, "Zajecar", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2762, "Zlatibor", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2763, "Raska", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2764, "Pirot", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2765, "Jablanica", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2766, "Toplica", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2767, "Nisava", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2768, "Rasina", NULL, NULL, "SRB", NULL, NULL, 60, NULL, NULL, 192),(2769, "Lakes", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2770, "Central Equatoria", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2771, "Eastern Equatoria", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2772, "Upper Nile", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2773, "Western Bahr el Ghazal", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2774, "Unity", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2775, "Northern Bahr el Ghazal", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2776, "Jonglei", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2777, "Western Equatoria", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2778, "Warrap", NULL, NULL, "SSD", NULL, NULL, 60, NULL, NULL, 193),(2779, "Príncipe Province", NULL, NULL, "STP", NULL, NULL, 60, NULL, NULL, 194),(2780, "São Tomé Province", NULL, NULL, "STP", NULL, NULL, 60, NULL, NULL, 194),(2781, "Brokopondo", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2782, "Commewijne", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2783, "Coronie", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2784, "Marowijne", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2785, "Nickerie", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2786, "Para", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2787, "Paramaribo", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2788, "Saramacca", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2789, "Sipaliwini", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2790, "Wanica", NULL, NULL, "SUR", NULL, NULL, 60, NULL, NULL, 195),(2791, "Bratislava", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2792, "Trnava", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2793, "Trenčín", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2794, "Nitra", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2795, "Žilina", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2796, "Banská Bystrica", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2797, "Prešov", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2798, "Košice", NULL, NULL, "SVK", NULL, NULL, 60, NULL, NULL, 196),(2799, "Vzhodna", NULL, NULL, "SVN", NULL, NULL, 60, NULL, NULL, 197),(2800, "Zahodna Slovenija", NULL, NULL, "SVN", NULL, NULL, 60, NULL, NULL, 197),(2801, "Västernorrlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2802, "Västerbottens", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2803, "Jämtlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2804, "Gävleborgs", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2805, "Dalarnas", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2806, "Uppsala", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2807, "Västmanlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2808, "Örebro", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2809, "Värmlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2810, "Västra Götalands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2811, "Hallands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2812, "Skåne", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2813, "Kronobergs", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2814, "Blekinge", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2815, "Kalmar", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2816, "Jönköpings", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2817, "Stockholms", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2818, "Östergötlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2819, "Södermanlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2820, "Norrbottens", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2821, "Gotlands", NULL, NULL, "SWE", NULL, NULL, 60, NULL, NULL, 198),(2822, "Manzini", NULL, NULL, "SWZ", NULL, NULL, 60, NULL, NULL, 199),(2823, "Lubombo", NULL, NULL, "SWZ", NULL, NULL, 60, NULL, NULL, 199),(2824, "Shiselweni", NULL, NULL, "SWZ", NULL, NULL, 60, NULL, NULL, 199),(2825, "Hhohho", NULL, NULL, "SWZ", NULL, NULL, 60, NULL, NULL, 199),(2826, "Outer Isla", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2827, "Baie Saint", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2828, "Grand Anse", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2829, "La Digue a", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2830, "Anse Aux P", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2831, "Anse Boile", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2832, "Anse Etoil", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2833, "Anse Royal", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2834, "Au Cap", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2835, "Baie Lazar", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2836, "Beau Vallo", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2837, "Bel Air", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2838, "Bel Ombre", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2839, "Cascade", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2840, "Glacis", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2841, "Grand'Anse", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2842, "La RiviÃ¨re", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2843, "Les Mamell", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2844, "Mont Buxto", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2845, "Mont Fleur", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2846, "Plaisance", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2847, "Pointe La", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2848, "Port Glaud", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2849, "Roche CaÃ¯m", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2850, "Saint Loui", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2851, "Takamaka", NULL, NULL, "SYC", NULL, NULL, 60, NULL, NULL, 200),(2852, "Damascus", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2853, "Aleppo", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2854, "Rural Damascus", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2855, "Homs", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2856, "Hama", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2857, "Lattakia", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2858, "Idleb", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2859, "Al-Hasakeh", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2860, "Deir-ez-Zor", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2861, "Tartous", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2862, "Ar-Raqqa", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2863, "Dar'a", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2864, "As-Sweida", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2865, "Quneitra", NULL, NULL, "SYR", NULL, NULL, 60, NULL, NULL, 201),(2866, "Tibesti", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2867, "Borkou", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2868, "Ennedi-Ouest", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2869, "Wadi Fira", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2870, "Salamat", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2871, "Sila", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2872, "Ouaddaï", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2873, "Guéra", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2874, "Batha", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2875, "Hadjer-Lamis", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2876, "Bahr el Gazel", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2877, "Kanem", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2878, "Chari-Baguirmi", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2879, "Lac", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2880, "Logone Occidental", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2881, "Logone Oriental", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2882, "Mandoul", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2883, "Mayo-Kebbi Est", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2884, "Mayo-Kebbi Ouest", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2885, "Moyen-Chari", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2886, "Tandjilé", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2887, "N'Djamena Region", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2888, "Ennedi-Est", NULL, NULL, "TCD", NULL, NULL, 60, NULL, NULL, 203),(2889, "Savanes", NULL, NULL, "TGO", NULL, NULL, 60, NULL, NULL, 204),(2890, "Kara", NULL, NULL, "TGO", NULL, NULL, 60, NULL, NULL, 204),(2891, "Centrale", NULL, NULL, "TGO", NULL, NULL, 60, NULL, NULL, 204),(2892, "Plateaux", NULL, NULL, "TGO", NULL, NULL, 60, NULL, NULL, 204),(2893, "Maritime", NULL, NULL, "TGO", NULL, NULL, 60, NULL, NULL, 204),(2894, "Roi Et", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2895, "Phayao", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2896, "Nakhon Sawan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2897, "Nong Bua Lam Phu", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2898, "Chachoengsao", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2899, "Surin", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2900, "Nakhon Ratchasima", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2901, "Sukhothai", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2902, "Phetchaburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2903, "Chaiyaphum", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2904, "Phetchabun", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2905, "Surat Thani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2906, "Si Sa Ket", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2907, "Nong Khai", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2908, "Bangkok", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2909, "Khon Kaen", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2910, "Loei", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2911, "Saraburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2912, "Uthai Thani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2913, "Chumphon", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2914, "Kalasin", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2915, "Phitsanulok", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2916, "Ranong", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2917, "Udon Thani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2918, "Sakon Nakhon", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2919, "Chiang Rai", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2920, "Tak", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2921, "Nakhon Pathom", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2922, "Lamphun", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2923, "Trat", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2924, "Ratchaburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2925, "Yasothon", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2926, "Phrae", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2927, "Yala", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2928, "Uttaradit", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2929, "Buri Ram", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2930, "Kamphaeng Phet", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2931, "Lampang", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2932, "Nonthaburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2933, "Bueng Kan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2934, "Narathiwat", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2935, "Ubon Ratchathani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2936, "Prachuap Khiri Khan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2937, "Songkhla", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2938, "Phuket", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2939, "Ang Thong", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2940, "Maha Sarakham", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2941, "Trang", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2942, "Suphan Buri", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2943, "Sing Buri", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2944, "Samut Songkhram", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2945, "Rayong", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2946, "Prachin Buri", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2947, "Phra Nakhon Si Ayutthaya", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2948, "Phichit", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2949, "Phatthalung", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2950, "Phangnga", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2951, "Pattani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2952, "Pathum Thani", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2953, "Nakhon Si Thammarat", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2954, "Nakhon Nayok", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2955, "Mukdahan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2956, "Lopburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2957, "Krabi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2958, "Chon Buri", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2959, "Chiang Mai", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2960, "Chai Nat", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2961, "Amnat Charoen", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2962, "Samut Sakhon", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2963, "Samut Prakan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2964, "Nan", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2965, "Chanthaburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2966, "Nakhon Phanom", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2967, "Kanchanaburi", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2968, "Mae Hong Son", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2969, "Sa Kaeo", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2970, "Satun", NULL, NULL, "THA", NULL, NULL, 60, NULL, NULL, 205),(2971, "Sughd Region", NULL, NULL, "TJK", NULL, NULL, 60, NULL, NULL, 206),(2972, "Gorno-Badakhshan Autonomous Region", NULL, NULL, "TJK", NULL, NULL, 60, NULL, NULL, 206),(2973, "Districts of Republican Subordination", NULL, NULL, "TJK", NULL, NULL, 60, NULL, NULL, 206),(2974, "Khatlon Region", NULL, NULL, "TJK", NULL, NULL, 60, NULL, NULL, 206),(2975, "Dushanbe", NULL, NULL, "TJK", NULL, NULL, 60, NULL, NULL, 206),(2976, "Balkan", NULL, NULL, "TKM", NULL, NULL, 60, NULL, NULL, 208),(2977, "Ahai", NULL, NULL, "TKM", NULL, NULL, 60, NULL, NULL, 208),(2978, "Dasoguz", NULL, NULL, "TKM", NULL, NULL, 60, NULL, NULL, 208),(2979, "Lebap", NULL, NULL, "TKM", NULL, NULL, 60, NULL, NULL, 208),(2980, "Mary", NULL, NULL, "TKM", NULL, NULL, 60, NULL, NULL, 208),(2981, "Oecusse", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2982, "Aileu", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2983, "Ainaro", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2984, "Baucau", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2985, "Bobonaro", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2986, "Cova Lima", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2987, "Dili", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2988, "Ermera", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2989, "Lautém", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2990, "Liquiçá", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2991, "Manatuto", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2992, "Manufahi", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2993, "Viqueque", NULL, NULL, "TLS", NULL, NULL, 60, NULL, NULL, 209),(2994, "Ha'apai", NULL, NULL, "TON", NULL, NULL, 60, NULL, NULL, 210),(2995, "Niuas", NULL, NULL, "TON", NULL, NULL, 60, NULL, NULL, 210),(2996, "Tongatapu", NULL, NULL, "TON", NULL, NULL, 60, NULL, NULL, 210),(2997, "Vava'u", NULL, NULL, "TON", NULL, NULL, 60, NULL, NULL, 210),(2998, "'Eua", NULL, NULL, "TON", NULL, NULL, 60, NULL, NULL, 210),(2999, "Tobago", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3000, "Princes Town", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3001, "Rio Claro-Mayaro", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3002, "Couva-Tabaquite-Talparo", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3003, "San Fernando", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3004, "Siparia", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3005, "Point Fortin", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3006, "Penal-Debe", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3007, "Diego Martin", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3008, "Port of Spain", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3009, "San Juan-Laventille", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3010, "Tunapuna-Piarco", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3011, "Sangre Grande", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3012, "Chaguanas", NULL, NULL, "TTO", NULL, NULL, 60, NULL, NULL, 211),(3013, "Tunis", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3014, "Ben Arous", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3015, "El Kef", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3016, "Sousse", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3017, "Sfax", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3018, "Jendouba", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3019, "Kairouan", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3020, "Kasserine", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3021, "Mahdia", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3022, "Manouba", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3023, "Sidi Bouzid", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3024, "Kébili", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3025, "Béja", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3026, "Tataouine", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3027, "Gabès", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3028, "Bizerte", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3029, "Ariana", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3030, "Nabeul", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3031, "Monastir", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),
  (3032, "Siliana", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3033, "Zaghouan", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3034, "Gafsa", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3035, "Médenine", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3036, "Tozeur", NULL, NULL, "TUN", NULL, NULL, 60, NULL, NULL, 212),(3037, "Adana", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3038, "Adıyaman", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3039, "Afyonkarahisar", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3040, "Ağrı", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3041, "Amasya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3042, "Antalya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3043, "Artvin", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3044, "Aydın", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3045, "Balıkesir", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3046, "Ankara", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3047, "Aksaray", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3048, "Ardahan", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3049, "Bartın", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3050, "Batman", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3051, "Bayburt", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3052, "Bilecik", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3053, "Bingöl", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3054, "Bitlis", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3055, "Bolu", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3056, "Burdur", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3057, "Bursa", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3058, "Çanakkale", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3059, "Çankırı", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3060, "Çorum", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3061, "Denizli", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3062, "Diyarbakır", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3063, "Düzce", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3064, "Edirne", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3065, "Elazığ", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3066, "Erzincan", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3067, "Erzurum", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3068, "Eskişehir", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3069, "Gaziantep", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3070, "Giresun", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3071, "Gümüşhane", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3072, "Hakkâri", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3073, "Hatay", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3074, "Iğdır", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3075, "Isparta", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3076, "İstanbul", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3077, "İzmir", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3078, "Kahramanmaraş", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3079, "Karabük", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3080, "Karaman", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3081, "Kars", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3082, "Kastamonu", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3083, "Kayseri", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3084, "Kilis", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3085, "Kırıkkale", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3086, "Kırklareli", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3087, "Kırşehir", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3088, "Kocaeli", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3089, "Konya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3090, "Kütahya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3091, "Malatya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3092, "Manisa", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3093, "Mardin", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3094, "Mersin", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3095, "Muğla", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3096, "Muş", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3097, "Nevşehir", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3098, "Niğde", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3099, "Ordu", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3100, "Osmaniye", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3101, "Rize", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3102, "Sakarya", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3103, "Samsun", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3104, "Siirt", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3105, "Sinop", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3106, "Sivas", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3107, "Tekirdağ", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3108, "Tokat", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3109, "Trabzon", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3110, "Tunceli", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3111, "Uşak", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3112, "Van", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3113, "Yalova", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3114, "Yozgat", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3115, "Zonguldak", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3116, "Şırnak", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3117, "Şanlıurfa", NULL, NULL, "TUR", NULL, NULL, 60, NULL, NULL, 213),(3118, "Nanumanga", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3119, "Nanumea", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3120, "Niutao", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3121, "Nui", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3122, "Vaitupu", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3123, "Nukufetau", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3124, "Funafuti", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3125, "Nukulaelae", NULL, NULL, "TUV", NULL, NULL, 60, NULL, NULL, 214),(3126, "Hsinchu", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3127, "Miaoli", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3128, "Matsu Islands", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3129, "Kinmen", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3130, "Chiayi", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3131, "Yilan", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3132, "Nantou", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3133, "Changhua", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3134, "Pingtung", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3135, "Taitung", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3136, "Hualien", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3137, "Penghu", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3138, "Yunlin", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3139, "Keelung", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3140, "Taichung", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3141, "Taoyuan", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3142, "New Taipei", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3143, "Tainan", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3144, "Taipei", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3145, "Kaohsiung", NULL, NULL, "TWN", NULL, NULL, 60, NULL, NULL, 215),(3146, "Kilimanjaro", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3147, "Mara", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3148, "Manyara", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3149, "Arusha", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3150, "Kagera", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3151, "Mbeya", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3152, "Singida", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3153, "Mtwara", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3154, "Iringa", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3155, "Dar es Salaam", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3156, "Lindi", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3157, "Tabora", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3158, "Pwani", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3159, "Ruvuma", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3160, "Morogoro", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3161, "Rukwa", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3162, "Kigoma", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3163, "Tanga", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3164, "Dodoma", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3165, "Zanzibar South & Central", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3166, "North Pemba", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3167, "Zanzibar North", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3168, "Zanzibar Urban/West", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3169, "South Pemba", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3170, "Katavi", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3171, "Njombe", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3172, "Geita", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3173, "Simiyu", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3174, "Mwanza", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3175, "Shinyanga", NULL, NULL, "TZA", NULL, NULL, 60, NULL, NULL, 216),(3176, "Northern", NULL, NULL, "UGA", NULL, NULL, 60, NULL, NULL, 217),(3177, "Eastern", NULL, NULL, "UGA", NULL, NULL, 60, NULL, NULL, 217),(3178, "Central", NULL, NULL, "UGA", NULL, NULL, 60, NULL, NULL, 217),(3179, "Western", NULL, NULL, "UGA", NULL, NULL, 60, NULL, NULL, 217),(3180, "Kherson", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3181, "Volyn", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3182, "Rivne", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3183, "Zhytomyr", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3184, "Kyiv", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3185, "Chernihiv", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3186, "Sumy", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3187, "Kharkiv", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3188, "Luhansk", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3189, "Donetsk", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3190, "Zaporizhia", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3191, "Lviv", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3192, "Ivano-Frankivsk", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3193, "Zakarpattia", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3194, "Ternopil", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3195, "Chernivtsi", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3196, "Odessa", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3197, "Mykolaiv", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3198, "Autonomous Republic of Crimea", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3199, "Vinnytsia", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3200, "Khmelnytskyi", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3201, "Cherkasy", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3202, "Poltava", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3203, "Dnipropetrovsk", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3204, "Kirovohrad", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3205, "Sevastopol", NULL, NULL, "UKR", NULL, NULL, 60, NULL, NULL, 218),(3206, "Salto", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3207, "Artigas", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3208, "Canelones", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3209, "Rivera", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3210, "Montevideo", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3211, "Maldonado", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3212, "Lavalleja", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3213, "Florida", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3214, "San José", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3215, "Treinta y Tres", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3216, "Flores", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3217, "Durazno", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3218, "Soriano", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3219, "Colonia", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3220, "Rocha", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3221, "Cerro Largo", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3222, "Tacuarembó", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3223, "Paysandú", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3224, "Río Negro", NULL, NULL, "URY", NULL, NULL, 60, NULL, NULL, 219),(3225, "Mississippi", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3226, "North Carolina", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3227, "Oklahoma", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3228, "Virginia", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3229, "West Virginia", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3230, "Louisiana", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3231, "Michigan", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3232, "Massachusetts", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3233, "Idaho", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3234, "Florida", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3235, "Nebraska", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3236, "Washington", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3237, "New Mexico", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3238, "Puerto Rico", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3239, "South Dakota", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3240, "Texas", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3241, "California", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3242, "Alabama", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3243, "Georgia", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3244, "Pennsylvania", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3245, "Missouri", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3246, "Colorado", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3247, "Utah", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3248, "Tennessee", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3249, "Wyoming", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3250, "New York", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3251, "Kansas", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3252, "Alaska", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3253, "Nevada", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3254, "Illinois", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3255, "Vermont", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3256, "Montana", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3257, "Iowa", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3258, "South Carolina", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3259, "New Hampshire", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3260, "Arizona", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3261, "District of Columbia", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3262, "American Samoa", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3263, "United States Virgin Islands", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3264, "New Jersey", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3265, "Maryland", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3266, "Maine", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3267, "Hawaii", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3268, "Delaware", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3269, "Guam", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3270, "Commonwealth of the Northern Mariana Islands", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3271, "Rhode Island", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3272, "Kentucky", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3273, "Ohio", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3274, "Wisconsin", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3275, "Oregon", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3276, "North Dakota", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3277, "Arkansas", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3278, "Indiana", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3279, "Minnesota", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3280, "Connecticut", NULL, NULL, "USA", NULL, NULL, 60, NULL, NULL, 220),(3281, "Andijan", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3282, "Namangan", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3283, "Fergana", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3284, "Republic of Karakalpakstan", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3285, "Xorazm", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3286, "Navoiy", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3287, "Surxondaryo", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3288, "Samarqand", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3289, "Tashkent", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3290, "Sirdaryo", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3291, "Jizzakh", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3292, "Bukhara", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3293, "Qashqadaryo", NULL, NULL, "UZB", NULL, NULL, 60, NULL, NULL, 221),(3294, "Charlotte", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3295, "Saint Andrew", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3296, "Saint David", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3297, "Saint George", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3298, "Saint Patrick", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3299, "Grenadines", NULL, NULL, "VCT", NULL, NULL, 60, NULL, NULL, 223),(3300, "Amazonas", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3301, "Anzoátegui", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3302, "Apure", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3303, "Aragua", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3304, "Barinas", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3305, "Bolívar", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3306, "Carabobo", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3307, "Cojedes", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3308, "Delta Amacuro", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3309, "Distrito Capital", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3310, "Falcón", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3311, "Guárico", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3312, "La Guaira", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3313, "Lara", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3314, "Mérida", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3315, "Miranda", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3316, "Monagas", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3317, "Nueva Esparta", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3318, "Portuguesa", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3319, "Sucre", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3320, "Táchira", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3321, "Trujillo", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3322, "Yaracuy", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3323, "Zulia", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3324, "Dependencias Federales", NULL, NULL, "VEN", NULL, NULL, 60, NULL, NULL, 224),(3325, "An Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3326, "Bà Rịa–Vũng Tàu", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3327, "Bắc Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3328, "Bắc Kạn", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3329, "Bạc Liêu", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3330, "Bắc Ninh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3331, "Bến Tre", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3332, "Bình Định", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3333, "Bình Dương", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3334, "Bình Phước", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3335, "Bình Thuận", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3336, "Cà Mau", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3337, "Cần Thơ", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3338, "Cao Bằng", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3339, "Côn Đảo", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3340, "Đà Nẵng", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3341, "Đắk Lắk", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3342, "Đắk Nông", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3343, "Điện Biên", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3344, "Đồng Nai", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3345, "Đồng Tháp", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3346, "Gia Lai", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3347, "Hà Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3348, "Hà Nam", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3349, "Hà Nội", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3350, "Hà Tĩnh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3351, "Hải Dương", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3352, "Hải Phòng", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3353, "Hậu Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3354, "Ho Chi Minh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3355, "Hòa Bình", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3356, "Hưng Yên", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3357, "Khánh Hòa", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3358, "Kiên Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3359, "Kon Tum", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3360, "Lai Châu", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3361, "Lâm Đồng", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3362, "Lạng Sơn", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3363, "Lào Cai", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3364, "Long An", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3365, "Nam Định", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3366, "Nghệ An", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3367, "Ninh Bình", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3368, "Ninh Thuận", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3369, "Phú Thọ", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3370, "Phú Yên", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3371, "Quảng Bình", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3372, "Quảng Nam", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3373, "Quảng Ngãi", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3374, "Quảng Ninh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3375, "Quảng Trị", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3376, "Sóc Trăng", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3377, "Sơn La", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3378, "Tây Ninh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3379, "Thái Bình", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3380, "Thái Nguyên", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3381, "Thanh Hóa", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3382, "Thừa Thiên Huế", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3383, "Tiền Giang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3384, "Trà Vinh", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3385, "Tuyên Quang", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3386, "Vĩnh Long", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3387, "Vĩnh Phúc", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),
  (3388, "Yên Bái", NULL, NULL, "VNM", NULL, NULL, 60, NULL, NULL, 227),(3389, "Sanma", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3390, "Torba", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3391, "Penama", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3392, "Malampa", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3393, "Shefa Province", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3394, "Tafea", NULL, NULL, "VUT", NULL, NULL, 60, NULL, NULL, 228),(3395, "A'ana", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3396, "Aiga-i-le-Tai", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3397, "Atua", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3398, "Fa'asaleleage", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3399, "Gaga'emauga", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3400, "Gaga'ifomauga", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3401, "Palauli", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3402, "Satupa'itea", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3403, "Tuamasaga", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3404, "Va'a-o-Fonoti", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3405, "Vaisigano", NULL, NULL, "WSM", NULL, NULL, 60, NULL, NULL, 230),(3406, "Prishtina", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3407, "Mitrovica", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3408, "Peja", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3409, "Gjilan", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3410, "Gjakova", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3411, "Prizren", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3412, "Ferizaj", NULL, NULL, "XKX", NULL, NULL, 60, NULL, NULL, 231),(3413, "Sanʿaʾ", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3414, "Lahij", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3415, "‘Adan", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3416, "Al Hudaydah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3417, "Ta'izz", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3418, "Shabwah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3419, "Hadhramaut", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3420, "Abyan", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3421, "Al Jawf", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3422, "Ibb", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3423, "Al Bayda'", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3424, "Ad Dali'", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3425, "Al Mahwit", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3426, "Sa'dah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3427, "Hajjah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3428, "Dhamar", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3429, "Amran", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3430, "Al Mahrah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3431, "Ma'rib", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3432, "Raymah", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3433, "Socotra", NULL, NULL, "YEM", NULL, NULL, 60, NULL, NULL, 232),(3434, "Eastern Cape", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3435, "Free State", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3436, "Gauteng", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3437, "KwaZulu-Natal", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3438, "Limpopo", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3439, "Mpumalanga", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3440, "North West", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3441, "Nothern Cape", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3442, "Western Cape", NULL, NULL, "ZAF", NULL, NULL, 60, NULL, NULL, 233),(3443, "Eastern", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3444, "Muchinga", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3445, "North-Western", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3446, "Luapula", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3447, "Central", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3448, "Southern", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3449, "Lusaka", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3450, "Copperbelt", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3451, "Northern", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3452, "Western", NULL, NULL, "ZMB", NULL, NULL, 60, NULL, NULL, 234),(3453, "Bulawayo", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3454, "Harare", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3455, "Manicaland", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3456, "Mashonaland Central", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3457, "Mashonaland East", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3458, "Mashonaland West", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3459, "Masvingo", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3460, "Matabeleland North", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3461, "Matabeleland South", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3462, "Midlands", NULL, NULL, "ZWE", NULL, NULL, 60, NULL, NULL, 235),(3463, "Shqipëria", NULL, NULL, NULL, NULL, NULL, 50, NULL, 5, 4),(3464, "Dzayer", NULL, NULL, NULL, NULL, NULL, 50, NULL, 60, 1),(3465, "Al-Jazā'ir", NULL, NULL, NULL, NULL, NULL, 50, NULL, 60, 1),(3466, "Ngola", NULL, NULL, NULL, NULL, NULL, 50, NULL, 3, 1),(3467, "Hayastán", NULL, NULL, NULL, NULL, NULL, 50, NULL, 9, 3),(3468, "Österreich", NULL, NULL, NULL, NULL, NULL, 50, NULL, 14, 4),(3469, "Azərbaycan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 15, 3),(3470, "Al-Baḥrayn", NULL, NULL, NULL, NULL, NULL, 50, NULL, 23, 3),(3471, "België", NULL, NULL, NULL, NULL, NULL, 50, NULL, 17, 4),(3472, "Belgique", NULL, NULL, NULL, NULL, NULL, 50, NULL, 17, 4),(3473, "Belgien", NULL, NULL, NULL, NULL, NULL, 50, NULL, 17, 4),(3474, "Druk Yul", NULL, NULL, NULL, NULL, NULL, 50, NULL, 34, 3),(3475, "Buliwya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 30, 7),(3476, "Wuliwya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 30, 7),(3477, "Volívia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 30, 7),(3478, "Bosna i Hercegovina", NULL, NULL, NULL, NULL, NULL, 50, NULL, 25, 4),(3479, "Brasil", NULL, NULL, NULL, NULL, NULL, 50, NULL, 31, 7),(3480, "Bălgariya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 22, 4),(3481, "Bălgarija", NULL, NULL, NULL, NULL, NULL, 50, NULL, 22, 4),(3482, "Uburundi", NULL, NULL, NULL, NULL, NULL, 50, NULL, 16, 1),(3483, "Kămpŭchéa", NULL, NULL, NULL, NULL, NULL, 50, NULL, 109, 3),(3484, "Cameroun", NULL, NULL, NULL, NULL, NULL, 50, NULL, 42, 1),(3485, "Centrafrique", NULL, NULL, NULL, NULL, NULL, 50, NULL, 36, 1),(3486, "Bêafrîka", NULL, NULL, NULL, NULL, NULL, 50, NULL, 36, 1),(3487, "Tchad", NULL, NULL, NULL, NULL, NULL, 50, NULL, 196, 1),(3488, "Tšād", NULL, NULL, NULL, NULL, NULL, 50, NULL, 196, 1),(3489, "Zhōngguó", NULL, NULL, NULL, NULL, NULL, 50, NULL, 40, 3),(3490, "Komori", NULL, NULL, NULL, NULL, NULL, 50, NULL, 47, 1),(3491, "Juzur al-Qamar", NULL, NULL, NULL, NULL, NULL, 50, NULL, 47, 1),(3492, "Comores", NULL, NULL, NULL, NULL, NULL, 50, NULL, 47, 1),(3493, "République du Congo", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 1),(3494, "Repubilika ya Kôngo", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 1),(3495, "Republíki ya Kongó", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 1),(3496, "République Démocratique du Congo", NULL, NULL, NULL, NULL, NULL, 50, NULL, 43, 1),(3497, "Republíki ya Kongó Demokratíki", NULL, NULL, NULL, NULL, NULL, 50, NULL, 43, 1),(3498, "Repubilika ya Kôngo ya Dimokalasi", NULL, NULL, NULL, NULL, NULL, 50, NULL, 43, 1),(3499, "Jamhuri ya Kidemokrasia ya Kongo", NULL, NULL, NULL, NULL, NULL, 50, NULL, 43, 1),(3500, "Kūki 'Āirani", NULL, NULL, NULL, NULL, NULL, 50, NULL, 45, 6),(3501, "Hrvatska", NULL, NULL, NULL, NULL, NULL, 50, NULL, 92, 4),(3502, "Kòrsou", NULL, NULL, NULL, NULL, NULL, 50, NULL, 51, 7),(3503, "Kypros", NULL, NULL, NULL, NULL, NULL, 50, NULL, 53, 4),(3504, "Česká Republika", NULL, NULL, NULL, NULL, NULL, 50, NULL, 54, 4),(3505, "Česko", NULL, NULL, NULL, NULL, NULL, 50, NULL, 54, 4),(3506, "Danmark", NULL, NULL, NULL, NULL, NULL, 50, NULL, 25, 4),(3507, "Jībūtī", NULL, NULL, NULL, NULL, NULL, 50, NULL, 56, 3),(3508, "Jabuuti", NULL, NULL, NULL, NULL, NULL, 50, NULL, 56, 3),(3509, "Gabuuti", NULL, NULL, NULL, NULL, NULL, 50, NULL, 56, 3),(3510, "República Dominicana", NULL, NULL, NULL, NULL, NULL, 50, NULL, 59, 5),(3511, "Timor Lorosa'e", NULL, NULL, NULL, NULL, NULL, 50, NULL, 202, 3),(3512, "East Timor", NULL, NULL, NULL, NULL, NULL, 50, NULL, 202, 3),(3513, "Misr", NULL, NULL, NULL, NULL, NULL, 50, NULL, 62, 1),(3514, "Masr", NULL, NULL, NULL, NULL, NULL, 50, NULL, 62, 1),(3515, "Guinea Ecuatorial", NULL, NULL, NULL, NULL, NULL, 50, NULL, 83, 1),(3516, "Guinée équatoriale", NULL, NULL, NULL, NULL, NULL, 50, NULL, 83, 1),(3517, "Guiné Equatorial", NULL, NULL, NULL, NULL, NULL, 50, NULL, 83, 1),(3518, "Iritriya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 63, 1),(3519, "Ertra", NULL, NULL, NULL, NULL, NULL, 50, NULL, 63, 1),(3520, "Eesti", NULL, NULL, NULL, NULL, NULL, 50, NULL, 65, 4),(3521, "Swaziland", NULL, NULL, NULL, NULL, NULL, 50, NULL, 192, 1),(3522, "Ityop'ia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 66, 1),(3523, "Føroyar", NULL, NULL, NULL, NULL, NULL, 50, NULL, 71, 4),(3524, "Færøerne", NULL, NULL, NULL, NULL, NULL, 50, NULL, 71, 4),(3525, "Viti", NULL, NULL, NULL, NULL, NULL, 50, NULL, 68, 6),(3526, "Suomi", NULL, NULL, NULL, NULL, NULL, 50, NULL, 67, 4),(3527, "Guyane", NULL, NULL, NULL, NULL, NULL, 50, NULL, 88, 7),(3528, "Polynésie française", NULL, NULL, NULL, NULL, NULL, 50, NULL, 170, 6),(3529, "République gabonaise", NULL, NULL, NULL, NULL, NULL, 50, NULL, 73, 1),(3530, "Sak'art'velo", NULL, NULL, NULL, NULL, NULL, 50, NULL, 75, 4),(3531, "Deutschland", NULL, NULL, NULL, NULL, NULL, 50, NULL, 55, 4),(3532, "Gaana", NULL, NULL, NULL, NULL, NULL, 50, NULL, 77, 1),(3533, "Gana", NULL, NULL, NULL, NULL, NULL, 50, NULL, 77, 1),(3534, "Hellas", NULL, NULL, NULL, NULL, NULL, 50, NULL, 84, 4),(3535, "Ellada", NULL, NULL, NULL, NULL, NULL, 50, NULL, 84, 4),(3536, "Kalaallit Nunaat", NULL, NULL, NULL, NULL, NULL, 50, NULL, 86, 5),(3537, "Grønland", NULL, NULL, NULL, NULL, NULL, 50, NULL, 86, 5),(3538, "Guåhån", NULL, NULL, NULL, NULL, NULL, 50, NULL, 89, 6),(3539, "Guinée", NULL, NULL, NULL, NULL, NULL, 50, NULL, 79, 1),(3540, "Gine", NULL, NULL, NULL, NULL, NULL, 50, NULL, 79, 1),(3541, "Guiné-Bissau", NULL, NULL, NULL, NULL, NULL, 50, NULL, 82, 1),(3542, "Ayiti", NULL, NULL, NULL, NULL, NULL, 50, NULL, 93, 5),(3543, "Magyarország", NULL, NULL, NULL, NULL, NULL, 50, NULL, 94, 4),(3544, "Ísland", NULL, NULL, NULL, NULL, NULL, 50, NULL, 100, 4),(3545, "Bhārôt", NULL, NULL, NULL, NULL, NULL, 50, NULL, 101, 3),(3546, "Bhārat", NULL, NULL, NULL, NULL, NULL, 50, NULL, 101, 3),(3547, "Bhārata", NULL, NULL, NULL, NULL, NULL, 50, NULL, 101, 3),(3548, "Bhāratam", NULL, NULL, NULL, NULL, NULL, 50, NULL, 101, 3),(3549, "Bhāratadēśam", NULL, NULL, NULL, NULL, NULL, 50, NULL, 101, 3),(3550, "Al-'Iraq", NULL, NULL, NULL, NULL, NULL, 50, NULL, 106, 3),(3551, "Êraq", NULL, NULL, NULL, NULL, NULL, 50, NULL, 106, 3),(3552, "Éire", NULL, NULL, NULL, NULL, NULL, 50, NULL, 104, 4),(3553, "Ellan Vannin", NULL, NULL, NULL, NULL, NULL, 50, NULL, 103, 4),(3554, "Yisra'el", NULL, NULL, NULL, NULL, NULL, 50, NULL, 108, 3),(3555, "Israʼiyl", NULL, NULL, NULL, NULL, NULL, 50, NULL, 108, 3),(3556, "Italia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 109, 4),(3557, "Nihon", NULL, NULL, NULL, NULL, NULL, 50, NULL, 112, 3),(3558, "Nippon", NULL, NULL, NULL, NULL, NULL, 50, NULL, 112, 3),(3559, "Al-’Urdun", NULL, NULL, NULL, NULL, NULL, 50, NULL, 111, 3),(3560, "Qazaqstan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 113, 3),(3561, "Chosŏn", NULL, NULL, NULL, NULL, NULL, 50, NULL, 236, 3),(3562, "Bukchosŏn", NULL, NULL, NULL, NULL, NULL, 50, NULL, 236, 3),(3563, "Hanguk", NULL, NULL, NULL, NULL, NULL, 50, NULL, 119, 3),(3564, "Namhan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 119, 3),(3565, "Kosova", NULL, NULL, NULL, NULL, NULL, 50, NULL, 231, 4),(3566, "Dawlat ul-Kuwayt", NULL, NULL, NULL, NULL, NULL, 50, NULL, 120, 3),(3567, "il-ikwet", NULL, NULL, NULL, NULL, NULL, 50, NULL, 120, 3),(3568, "Lao", NULL, NULL, NULL, NULL, NULL, 50, NULL, 121, 3),(3569, "Latvija", NULL, NULL, NULL, NULL, NULL, 50, NULL, 131, 4),(3570, "Lubnān", NULL, NULL, NULL, NULL, NULL, 50, NULL, 122, 3),(3571, "Liban", NULL, NULL, NULL, NULL, NULL, 50, NULL, 122, 3),(3572, "Lietuva", NULL, NULL, NULL, NULL, NULL, 50, NULL, 129, 4),(3573, "Lëtzebuerg", NULL, NULL, NULL, NULL, NULL, 50, NULL, 130, 4),(3574, "Luxemburg", NULL, NULL, NULL, NULL, NULL, 50, NULL, 130, 4),(3575, "Madagasikara", NULL, NULL, NULL, NULL, NULL, 50, NULL, 135, 1),(3576, "Dhivehi Raajje", NULL, NULL, NULL, NULL, NULL, 50, NULL, 136, 3),(3577, "Aorōkin Ṃajeḷ", NULL, NULL, NULL, NULL, NULL, 50, NULL, 138, 6),(3578, "Muritan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 147, 1),(3579, "Agawec", NULL, NULL, NULL, NULL, NULL, 50, NULL, 147, 1),(3580, "Mūrītānyā", NULL, NULL, NULL, NULL, NULL, 50, NULL, 147, 1),(3581, "Maurice", NULL, NULL, NULL, NULL, NULL, 50, NULL, 150, 1),(3582, "Moris", NULL, NULL, NULL, NULL, NULL, 50, NULL, 150, 1),(3583, "Maore", NULL, NULL, NULL, NULL, NULL, 50, NULL, 153, 1),(3584, "Múnegu", NULL, NULL, NULL, NULL, NULL, 50, NULL, 133, 4),(3585, "Mongol Uls", NULL, NULL, NULL, NULL, NULL, 50, NULL, 144, 3),(3586, "Crna Gora", NULL, NULL, NULL, NULL, NULL, 50, NULL, 143, 4),(3587, "Amerruk", NULL, NULL, NULL, NULL, NULL, 50, NULL, 132, 1),(3588, "Elmeɣrib", NULL, NULL, NULL, NULL, NULL, 50, NULL, 132, 1),(3589, "Moçambique", NULL, NULL, NULL, NULL, NULL, 50, NULL, 146, 4),(3590, "Myanma", NULL, NULL, NULL, NULL, NULL, 50, NULL, 142, 3),(3591, "Burma", NULL, NULL, NULL, NULL, NULL, 50, NULL, 142, 3),(3592, "Namibië", NULL, NULL, NULL, NULL, NULL, 50, NULL, 154, 1),(3593, "Naoero", NULL, NULL, NULL, NULL, NULL, 50, NULL, 163, 6),(3594, "Nederland", NULL, NULL, NULL, NULL, NULL, 50, NULL, 160, 4),(3595, "Nederlân", NULL, NULL, NULL, NULL, NULL, 50, NULL, 160, 4),(3596, "Nouvelle-Calédonie", NULL, NULL, NULL, NULL, NULL, 50, NULL, 155, 6),(3597, "Aotearoa", NULL, NULL, NULL, NULL, NULL, 50, NULL, 164, 6),(3598, "Nijeriya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 157, 1),(3599, "Naìjíríyà", NULL, NULL, NULL, NULL, NULL, 50, NULL, 157, 1),(3600, "Nàìjíríà", NULL, NULL, NULL, NULL, NULL, 50, NULL, 157, 1),(3601, "Severna Makedonija", NULL, NULL, NULL, NULL, NULL, 50, NULL, 139, 4),(3602, "Maqedonia e Veriut", NULL, NULL, NULL, NULL, NULL, 50, NULL, 139, 4),(3603, "Notte Mariånas", NULL, NULL, NULL, NULL, NULL, 50, NULL, 145, 6),(3604, "Norge", NULL, NULL, NULL, NULL, NULL, 50, NULL, 161, 4),(3605, "Noreg", NULL, NULL, NULL, NULL, NULL, 50, NULL, 161, 4),(3606, "Norga", NULL, NULL, NULL, NULL, NULL, 50, NULL, 161, 4),(3607, "Vuodna", NULL, NULL, NULL, NULL, NULL, 50, NULL, 161, 4),(3608, "Nöörje", NULL, NULL, NULL, NULL, NULL, 50, NULL, 161, 4),(3609, "‘Umān", NULL, NULL, NULL, NULL, NULL, 50, NULL, 165, 3),(3610, "Belau", NULL, NULL, NULL, NULL, NULL, 50, NULL, 171, 6),(3611, "Filasṭīn", NULL, NULL, NULL, NULL, NULL, 50, NULL, 176, 3),(3612, "Papua Niugini", NULL, NULL, NULL, NULL, NULL, 50, NULL, 172, 6),(3613, "Papua Niu Gini", NULL, NULL, NULL, NULL, NULL, 50, NULL, 172, 6),(3614, "Paraguái", NULL, NULL, NULL, NULL, NULL, 50, NULL, 175, 7),(3615, "Piruw", NULL, NULL, NULL, NULL, NULL, 50, NULL, 169, 7),(3616, "Pilipinas", NULL, NULL, NULL, NULL, NULL, 50, NULL, 170, 6),(3617, "Pitkern Ailen", NULL, NULL, NULL, NULL, NULL, 50, NULL, 168, 6),(3618, "Polska", NULL, NULL, NULL, NULL, NULL, 50, NULL, 173, 4),(3619, "La Réunion", NULL, NULL, NULL, NULL, NULL, 50, NULL, 179, 1),(3620, "Rossiya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 181, 3),(3621, "Rossiâ", NULL, NULL, NULL, NULL, NULL, 50, NULL, 181, 3),(3622, "Russia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 181, 3),(3623, "São Tomé e Príncipe", NULL, NULL, NULL, NULL, NULL, 50, NULL, 194, 1),(3624, "Al-Mamlaka Al-‘Arabiyyah as Sa‘ūdiyyah", NULL, NULL, NULL, NULL, NULL, 50, NULL, 182, 3),(3625, "Senegaal", NULL, NULL, NULL, NULL, NULL, 50, NULL, 184, 1),(3626, "Srbija", NULL, NULL, NULL, NULL, NULL, 50, NULL, 192, 4),(3627, "Sesel", NULL, NULL, NULL, NULL, NULL, 50, NULL, 200, 1),(3628, "Singapura", NULL, NULL, NULL, NULL, NULL, 50, NULL, 185, 3),(3629, "Xīnjiāpō", NULL, NULL, NULL, NULL, NULL, 50, NULL, 185, 3),(3630, "Singapur", NULL, NULL, NULL, NULL, NULL, 50, NULL, 185, 3),(3631, "Slovensko", NULL, NULL, NULL, NULL, NULL, 50, NULL, 196, 4),(3632, "Slovenija", NULL, NULL, NULL, NULL, NULL, 50, NULL, 197, 4),(3633, "Solomon Aelan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 187, 6),(3634, "Soomaaliya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 191, 1),(3635, "aş-Şūmāl", NULL, NULL, NULL, NULL, NULL, 50, NULL, 191, 1),(3636, "Suid-Afrika", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3637, "iNingizimu Afrika", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3638, "uMzantsi Afrika", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3639, "Afrika-Borwa", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3640, "Aforika Borwa", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3641, "Afurika Tshipembe", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3642, "Afrika Dzonga", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3643, "iSewula Afrika", NULL, NULL, NULL, NULL, NULL, 50, NULL, 233, 1),(3644, "Sudan Kusini", NULL, NULL, NULL, NULL, NULL, 50, NULL, 193, 1),(3645, "Paguot Thudän", NULL, NULL, NULL, NULL, NULL, 50, NULL, 193, 1),(3646, "España", NULL, NULL, NULL, NULL, NULL, 50, NULL, 70, 4),(3647, "Espanya", NULL, NULL, NULL, NULL, NULL, 50, NULL, 70, 4),(3648, "Espainia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 70, 4),(3649, "Espanha", NULL, NULL, NULL, NULL, NULL, 50, NULL, 70, 4),(3650, "Statia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 25, 7),(3651, "As-Sudan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 183, 1),(3652, "Schweiz", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 4),(3653, "Suisse", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 4),(3654, "Svizzera", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 4),(3655, "Svizra", NULL, NULL, NULL, NULL, NULL, 50, NULL, 44, 4),(3656, "Zhōnghuá", NULL, NULL, NULL, NULL, NULL, 50, NULL, 215, 3),(3657, "Mínguó", NULL, NULL, NULL, NULL, NULL, 50, NULL, 215, 3),(3658, "Tojikistan", NULL, NULL, NULL, NULL, NULL, 50, NULL, 206, 3),(3659, "Thai", NULL, NULL, NULL, NULL, NULL, 50, NULL, 205, 3),(3660, "Prathet Thai", NULL, NULL, NULL, NULL, NULL, 50, NULL, 205, 3),(3661, "Ratcha-anachak Thai", NULL, NULL, NULL, NULL, NULL, 50, NULL, 205, 3),(3662, "Tunes", NULL, NULL, NULL, NULL, NULL, 50, NULL, 212, 1),(3663, "Tūns", NULL, NULL, NULL, NULL, NULL, 50, NULL, 212, 1),(3664, "Türkiye", NULL, NULL, NULL, NULL, NULL, 50, NULL, 213, 3),(3665, "Ukrajina", NULL, NULL, NULL, NULL, NULL, 50, NULL, 218, 4),(3666, "Al-’Imārat Al-‘Arabiyyah Al-Muttaḥidah", NULL, NULL, NULL, NULL, NULL, 50, NULL, 14, 3),(3667, "Y Deyrnas Unedig", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3668, "Unitit Kinrick", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3669, "Rìoghachd Aonaichte", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3670, "Ríocht Aontaithe", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3671, "An Rywvaneth Unys", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3672, "Britain", NULL, NULL, NULL, NULL, NULL, 50, NULL, 80, 4),(3673, "Estados Unidos", NULL, NULL, NULL, NULL, NULL, 50, NULL, 220, 5),(3674, "USA", NULL, NULL, NULL, NULL, NULL, 50, NULL, 220, 5),(3675, "U.S.", NULL, NULL, NULL, NULL, NULL, 50, NULL, 220, 5),(3676, "États-Unis", NULL, NULL, NULL, NULL, NULL, 50, NULL, 220, 5),(3677, "‘Amelika Hui Pū ‘ia", NULL, NULL, NULL, NULL, NULL, 50, NULL, 220, 5),(3678, "O‘zbekiston", NULL, NULL, NULL, NULL, NULL, 50, NULL, 221, 3),(3679, "Holy See", NULL, NULL, NULL, NULL, NULL, 50, NULL, 222, 4),(3680, "Civitas Vaticana", NULL, NULL, NULL, NULL, NULL, 50, NULL, 222, 4),(3681, "Città del Vaticano", NULL, NULL, NULL, NULL, NULL, 50, NULL, 222, 4),(3682, "Vietnam", NULL, NULL, NULL, NULL, NULL, 50, NULL, 227, 3),(3683, "Wallis-et-Futuna", NULL, NULL, NULL, NULL, NULL, 50, NULL, 229, 6),(3684, "ʻUvea mo Futuna", NULL, NULL, NULL, NULL, NULL, 50, NULL, 229, 6),(3685, "Al-Yaman", NULL, NULL, NULL, NULL, NULL, 50, NULL, 232, 3);

