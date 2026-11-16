-- THIS FILE IS ONLY FOR CI.
-- It is required inorder to build fresh schema without getting
-- block from statements with the comment "Skip if 3.0 install"

-- Tables needed for 3.1 skips
CREATE TABLE `omoccurresource` (
  `resourceID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `occid` INT UNSIGNED NOT NULL,
  `reourceTitle` VARCHAR(45) NOT NULL,
  `resourceType` VARCHAR(45) NOT NULL,
  `uri` VARCHAR(250) NOT NULL,
  `source` VARCHAR(45) NULL,
  `resourceIdentifier` VARCHAR(45) NULL,
  `notes` VARCHAR(250) NULL,
  `modifiedUid` INT UNSIGNED NULL,
  `createdUid` INT UNSIGNED NULL,
  `initialTimestamp` TIMESTAMP NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`resourceID`),
  INDEX `FK_omoccurresource_occid_idx` (`occid` ASC),
  INDEX `FK_omoccurresource_modUid_idx` (`modifiedUid` ASC),
  INDEX `FK_omoccurresource_createdUid_idx` (`createdUid` ASC),
  CONSTRAINT `FK_omoccurresource_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurresource_modUid`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurresource_createdUid`  FOREIGN KEY (`createdUid`)  REFERENCES `users` (`uid`)  ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Tables needed for 3.2 skips
CREATE TABLE `imageprojects` (
  `imgprojid` int(11) NOT NULL AUTO_INCREMENT,
  `projectname` varchar(75) NOT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `projectType` varchar(45) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `ispublic` int(11) NOT NULL DEFAULT 1,
  `notes` varchar(250) DEFAULT NULL,
  `uidcreated` int(11) unsigned DEFAULT NULL,
  `sortsequence` int(11) DEFAULT 50,
  `initialtimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgprojid`),
  KEY `FK_imageproject_collid_idx` (`collid`),
  KEY `FK_imageproject_uid_idx` (`uidcreated`),
  CONSTRAINT `FK_imageproject_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imageproject_uid` FOREIGN KEY (`uidcreated`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `imageprojectlink` (
  `imgid` int(10) unsigned NOT NULL,
  `imgprojid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgid`,`imgprojid`),
  KEY `FK_imageprojlink_imgprojid_idx` (`imgprojid`),
  CONSTRAINT `FK_imageprojectlink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imageprojlink_imgprojid` FOREIGN KEY (`imgprojid`) REFERENCES `imageprojects` (`imgprojid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `taxaprofilepubs` (
  `tppid` int(11) NOT NULL AUTO_INCREMENT,
  `pubtitle` varchar(150) NOT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `uidowner` int(10) unsigned DEFAULT NULL,
  `externalurl` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageterm` varchar(250) DEFAULT NULL,
  `accessrights` varchar(250) DEFAULT NULL,
  `ispublic` int(11) DEFAULT NULL,
  `inclusive` int(11) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`tppid`),
  KEY `FK_taxaprofilepubs_uid_idx` (`uidowner`),
  KEY `INDEX_taxaprofilepubs_title` (`pubtitle`),
  CONSTRAINT `FK_taxaprofilepubs_uid` FOREIGN KEY (`uidowner`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `taxaprofilepubimagelink` (
  `imgid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`imgid`,`tppid`),
  KEY `FK_tppubimagelink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubimagelink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubimagelink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;
