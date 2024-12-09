INSERT INTO `schemaversion` (versionnumber) values ("3.2");

-- Drop deprecated_media (This causes FOREIGN KEY conflicts with media CONSTRAINTS)
DROP TABLE IF EXISTS `deprecated_media`;
-- Define media
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `mediaID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `thumbnailUrl` varchar(255) DEFAULT NULL, 
  `originalUrl` varchar(255) DEFAULT NULL,
  `archiveUrl` varchar(255) DEFAULT NULL,
  `sourceUrl` varchar(250) DEFAULT NULL,
  `referenceUrl` varchar(255) DEFAULT NULL,
  `mediaType` varchar(45) DEFAULT NULL,
  `imageType` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(250) DEFAULT NULL,
  `creator` varchar(45) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `anatomy` varchar(100) DEFAULT NULL,
  `notes` varchar(350) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `mediaMD5` varchar(45) DEFAULT NULL,
  `hashValue` varchar(45) DEFAULT NULL,
  `hashFunction` varchar(45) DEFAULT NULL,
  `pixelYDimension` int(11) DEFAULT NULL,
  `pixelXDimension` int(11) DEFAULT NULL,
  `dynamicProperties` text DEFAULT NULL,
  `defaultDisplay` int(11) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `accessRights` varchar(255) DEFAULT NULL,
  `sortSequence` int(11) DEFAULT NULL,
  `sortOccurrence` int(11) DEFAULT 5,
  `creatorUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mediaID`),
  CONSTRAINT `FK_media_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_media_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_creator_uid` FOREIGN KEY (`creatorUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO 
	media(mediaID, tid, occid, 
	url, thumbnailUrl, archiveUrl, originalUrl, sourceurl, referenceUrl,
	caption, creatoruid, creator, owner, 
	mediaMD5, format, imagetype,
	locality, notes, anatomy,
	username, sourceIdentifier, 
	hashFunction, hashValue,
	pixelYDimension, pixelXDimension,
	dynamicProperties, defaultDisplay, recordID,
	copyright, rights, accessRights,
	sortSequence, sortOccurrence, 
	initialTimestamp, 
	mediaType) 
	SELECT 
		imgid, tid, occid,  
		url, thumbnailUrl, archiveUrl, originalUrl, sourceurl, referenceUrl,
		caption, photographerUid, photographer, owner,  
		mediaMD5, format,imagetype,
		locality, notes, anatomy,
		username, sourceIdentifier, 
		hashFunction, hashValue,
		pixelYDimension, pixelXDimension,
		dynamicProperties, defaultDisplay, recordID,
		copyright, rights, accessRights,
		sortSequence, sortOccurrence,
		initialTimestamp, 
		"image" as mediaType
	from images;

ALTER TABLE imagetag DROP CONSTRAINT FK_imagetag_imgid;
ALTER TABLE imagetag ADD CONSTRAINT FOREIGN KEY (`imgid`) REFERENCES media(mediaID);
