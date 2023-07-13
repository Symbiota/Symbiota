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

-- Add missing columns to the omocurrences table where the quick entry data is stored
ALTER TABLE `omoccurrences` 
    ADD COLUMN `filedUnder` int DEFAULT NULL,
    ADD COLUMN `geoWithin` int DEFAULT NULL,
    ADD COLUMN `barcode` int(8) NOT NULL,
    ADD COLUMN `herbarium` varchar(4) NOT NULL,
    ADD COLUMN `accesNum` varchar(255) DEFAULT NULL,
    ADD COLUMN `currName` int DEFAULT NULL,
    ADD COLUMN `idQualifier` varchar(16) DEFAULT NULL,
    ADD COLUMN `detText` text DEFAULT NULL,
    ADD COLUMN `provenance` text DEFAULT NULL,
    ADD COLUMN `container` int DEFAULT NULL,
    ADD COLUMN `collTrip` int DEFAULT NULL,
    ADD COLUMN `highGeo` int DEFAULT NULL,
    ADD COLUMN `frequency` varchar(255) DEFAULT NULL,
    ADD COLUMN `prepMethod` int DEFAULT NULL,
    ADD COLUMN `format` int DEFAULT NULL,
    ADD COLUMN `verbLat` varchar(255) DEFAULT NULL,
    ADD COLUMN `verbLong` varchar(255) DEFAULT NULL,
    ADD COLUMN `method` varchar(255) DEFAULT NULL;

-- Create tables to hold dropdown list values
/*Methodology:
1. Create empty table to hold values
2. Link the primary key of that table to the omoccurrences table
*/

-- Table to hold values for the filedUnder column
DROP TABLE IF EXISTS `dropdown_filedUnder_values`;
CREATE TABLE `dropdown_filedUnder_values` (
  `dd_filedUnderID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `genus` text,
  `species` text,
  `authorName` text,
  `vascularity` text,
  `family` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_filedUnderID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_filedUnder` FOREIGN KEY (`filedUnder`) REFERENCES `dropdown_filedUnder_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_filedUnder_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_filedUnder
BEFORE INSERT ON dropdown_filedUnder_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

CREATE TRIGGER TR_update_dd_filedUnder
BEFORE UPDATE ON dropdown_filedUnder_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

-- Table to hold values for the currentName column
DROP TABLE IF EXISTS `dropdown_currName_values`;
CREATE TABLE `dropdown_currName_values` (
  `dd_currNameID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `genus` text,
  `species` text,
  `authorName` text,
  `vascularity` text,
  `family` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_currNameID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_currName` FOREIGN KEY (`currName`) REFERENCES `dropdown_currName_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_currName_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_currName
BEFORE INSERT ON dropdown_currName_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

CREATE TRIGGER TR_update_dd_currName
BEFORE UPDATE ON dropdown_currName_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

-- Table to hold values for the identifiedBy column
DROP TABLE IF EXISTS `dropdown_identifiedBy_values`;
CREATE TABLE `dropdown_identifiedBy_values` (
  `dd_identifiedByID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `names` text,
  `type` text,
  `activeYears` text,
  `area` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_identifiedByID`)
);

ALTER TABLE `omoccurrences`
    CHANGE `identifiedBy` `identifiedBy` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_identifiedBy` FOREIGN KEY (`identifiedBy`) REFERENCES `dropdown_identifiedBy_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_identifiedBy_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_identifiedBy
BEFORE INSERT ON dropdown_identifiedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

CREATE TRIGGER TR_update_dd_identifiedBy
BEFORE UPDATE ON dropdown_identifiedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

-- Table to hold values for the collectors column
DROP TABLE IF EXISTS `dropdown_recordedBy_values`;
CREATE TABLE `dropdown_recordedBy_values` (
  `dd_recordedByID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `names` text,
  `type` text,
  `activeYears` text,
  `area` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_recordedByID`)
);

ALTER TABLE `omoccurrences`
    CHANGE `recordedBy` `recordedBy` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_recordedBy` FOREIGN KEY (`recordedBy`) REFERENCES `dropdown_recordedBy_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_recordedBy_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_recordedBy
BEFORE INSERT ON dropdown_recordedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

CREATE TRIGGER TR_update_dd_recordedBy
BEFORE UPDATE ON dropdown_recordedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

-- Table to hold values for the container column
DROP TABLE IF EXISTS `dropdown_container_values`;
CREATE TABLE `dropdown_container_values` (
  `dd_containerID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `container` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_containerID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_container` FOREIGN KEY (`container`) REFERENCES `dropdown_container_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_container_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_container
BEFORE INSERT ON dropdown_container_values
FOR EACH ROW
SET NEW.displayValue = NEW.container;

CREATE TRIGGER TR_update_dd_container
BEFORE UPDATE ON dropdown_container_values
FOR EACH ROW
SET NEW.displayValue = NEW.container;

-- Table to hold values for the collTrip column
DROP TABLE IF EXISTS `dropdown_collTrip_values`;
CREATE TABLE `dropdown_collTrip_values` (
  `dd_collTripID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `collTrip` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_collTripID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_collTrip` FOREIGN KEY (`collTrip`) REFERENCES `dropdown_collTrip_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_collTrip_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_collTrip
BEFORE INSERT ON dropdown_collTrip_values
FOR EACH ROW
SET NEW.displayValue = NEW.collTrip;

CREATE TRIGGER TR_update_dd_collTrip
BEFORE UPDATE ON dropdown_collTrip_values
FOR EACH ROW
SET NEW.displayValue = NEW.collTrip;

-- Table to hold values for the geoWithin column
DROP TABLE IF EXISTS `dropdown_geoWithin_values`;
CREATE TABLE `dropdown_geoWithin_values` (
  `dd_geoWithinID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `area` text,
  `areaType` text,
  `synonym` text, DEFAULT NULL,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_geoWithinID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_geoWithin` FOREIGN KEY (`geoWithin`) REFERENCES `dropdown_geoWithin_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_geoWithin_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_geoWithin
BEFORE INSERT ON dropdown_geoWithin_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " [", NEW.areaType, "] ", NEW.synonym);

CREATE TRIGGER TR_update_dd_geoWithin
BEFORE UPDATE ON dropdown_geoWithin_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " [", NEW.areaType, "] ", NEW.synonym);

-- Table to hold values for the highGeo column
DROP TABLE IF EXISTS `dropdown_highGeo_values`;
CREATE TABLE `dropdown_highGeo_values` (
  `dd_highGeoID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `area` text,
  `country` text,
  `areaType` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_highGeoID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_highGeo` FOREIGN KEY (`highGeo`) REFERENCES `dropdown_highGeo_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_highGeo_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_highGeo
BEFORE INSERT ON dropdown_highGeo_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " (", NEW.country, ") [", NEW.areaType, "]");

CREATE TRIGGER TR_update_dd_highGeo
BEFORE UPDATE ON dropdown_highGeo_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " (", NEW.country, ") [", NEW.areaType, "]");

-- Table to hold values for the prepMethod column
DROP TABLE IF EXISTS `dropdown_prepMethod_values`;
CREATE TABLE `dropdown_prepMethod_values` (
  `dd_prepMethodID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `prepMethod` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_prepMethodID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_prepMethod` FOREIGN KEY (`prepMethod`) REFERENCES `dropdown_prepMethod_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_prepMethod_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_prepMethod
BEFORE INSERT ON dropdown_prepMethod_values
FOR EACH ROW
SET NEW.displayValue = NEW.prepMethod;

CREATE TRIGGER TR_update_dd_prepMethod
BEFORE UPDATE ON dropdown_prepMethod_values
FOR EACH ROW
SET NEW.displayValue = NEW.prepMethod;

-- Table to hold values for the format column
DROP TABLE IF EXISTS `dropdown_format_values`;
CREATE TABLE `dropdown_format_values` (
  `dd_formatID` int NOT NULL AUTO_INCREMENT,
  `displayValue` varchar(255),
  `format` text,
  `occid` int unsigned NOT NULL,
  `initialtimestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dd_formatID`)
);

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_format` FOREIGN KEY (`format`) REFERENCES `dropdown_format_values`(`id`);

-- Create trigger to update the displayValue column in dropdown_format_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_format
BEFORE INSERT ON dropdown_format_values
FOR EACH ROW
SET NEW.displayValue = NEW.format;

CREATE TRIGGER TR_update_dd_format
BEFORE UPDATE ON dropdown_format_values
FOR EACH ROW
SET NEW.displayValue = NEW.format;