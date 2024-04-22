INSERT IGNORE INTO schemaversion (versionnumber) values ("1.3");
-- Spark! Summer 2023 team

-- Create batch table
DROP TABLE IF EXISTS `batch`;
CREATE TABLE `batch` (
  `batchID` int(11) NOT NULL AUTO_INCREMENT,
  `ingest_date` timestamp NOT NULL,
  `completed_date` timestamp NULL,
  `batch_name` varchar(100) NOT NULL,
  `image_batch_path` varchar(100) NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`batchID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create cross-reference table between the batch table and images table
DROP TABLE IF EXISTS `batch_XREF`;
CREATE TABLE `batch_XREF` (
  `imgid` int(10) unsigned NOT NULL,
  `batchID` int(11) NOT NULL,
  `ordinal` INT(10) NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`imgid`,`batchID`),
  KEY `FK_batch_XREF_img` (`imgid`),
  KEY `FK_batch_XREF_batch` (`batchID`),
  CONSTRAINT `FK_batch_XREF_img` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_batch_XREF_batch` FOREIGN KEY (`batchID`) REFERENCES `batch` (`batchID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create batch user table
DROP TABLE IF EXISTS `batch_user`;
CREATE TABLE `batch_user` (
  `batch_userID` int(10) NOT NULL AUTO_INCREMENT,
  `batchID` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `last_position` int(10) NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp,
  PRIMARY KEY (`batch_userID`),
  KEY `FK_batch_user_batch` (`batchID`),
  KEY `FK_batch_user_user` (`uid`),
  CONSTRAINT `FK_batch_user_batch` FOREIGN KEY (`batchID`) REFERENCES `batch` (`batchID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_batch_user_user` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `images_barcode`;
CREATE TABLE `images_barcode` (
  `imgid` int(10) unsigned NOT NULL,
  `barcode` varchar(255) NOT NULL,
  `occid` int unsigned NOT NULL,
  PRIMARY KEY (`barcode`),
  KEY `FK_images_barcode_images` (`imgid`),
  KEY `FK_images_barcode_omoccurrences` (`occid`),
  CONSTRAINT `FK_images_barcode_images` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_images_barcode_omoccurrences` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add missing columns to the omocurrences table where the quick entry data is stored
ALTER TABLE `omoccurrences` 
    ADD COLUMN `filedUnder` varchar(255) DEFAULT NULL,
    ADD COLUMN `geoWithin` varchar(255) DEFAULT NULL,
    ADD COLUMN `herbarium` varchar(4) NOT NULL,
    ADD COLUMN `accesNum` varchar(255) DEFAULT NULL,
    ADD COLUMN `currName` varchar(255) DEFAULT NULL,
    ADD COLUMN `idQualifier` varchar(16) DEFAULT NULL,
    ADD COLUMN `detText` text DEFAULT NULL,
    ADD COLUMN `provenance` text DEFAULT NULL,
    ADD COLUMN `container` varchar(255) DEFAULT NULL,
    ADD COLUMN `collTrip` varchar(255) DEFAULT NULL,
    ADD COLUMN `highGeo` varchar(255) DEFAULT NULL,
    ADD COLUMN `frequency` varchar(255) DEFAULT NULL,
    ADD COLUMN `prepMethod` varchar(255) DEFAULT NULL,
    ADD COLUMN `format` varchar(255) DEFAULT NULL,
    ADD COLUMN `verbLat` varchar(255) DEFAULT NULL,
    ADD COLUMN `verbLong` varchar(255) DEFAULT NULL,
    ADD COLUMN `method` varchar(255) DEFAULT NULL;

-- DWCA ingestion ingests the data firstly into the uploadspectemp table
-- That uploadspectemp table's eventDate column uses date format, when it should be
-- varchar(32) like the omoccurrences table. Not sure if it's intended, but this fixes it for now:
ALTER TABLE `uploadspectemp` MODIFY `eventDate` varchar(32);

-- Create views for dropdown columns
CREATE VIEW dd_filedUnder_view AS
SELECT DISTINCT filedUnder
FROM omoccurrences;
CREATE VIEW dd_currName_view AS
SELECT DISTINCT currName 
FROM omoccurrences;
CREATE VIEW dd_identifiedBy_view AS
SELECT DISTINCT identifiedBy 
FROM omoccurrences;
CREATE VIEW dd_collectors_view AS
SELECT DISTINCT recordedBy 
FROM omoccurrences;
CREATE VIEW dd_collTrip_view AS
SELECT DISTINCT collTrip 
FROM omoccurrences;
CREATE VIEW dd_geoWithin_view AS
SELECT DISTINCT geoWithin 
FROM omoccurrences;
CREATE VIEW dd_highGeo_view AS
SELECT DISTINCT highGeo 
FROM omoccurrences;