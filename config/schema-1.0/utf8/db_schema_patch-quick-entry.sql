INSERT IGNORE INTO schemaversion (versionnumber) values ("HUH");

--
-- Table structure for table `image_batch`
--

DROP TABLE IF EXISTS `image_batch`;
CREATE TABLE `image_batch` (
  `image_batch_id` int(11) NOT NULL AUTO_INCREMENT,
  `ingest_date` timestamp NOT NULL,
  `batch_name` varchar(100) NOT NULL,
  `image_batch_path` varchar(100) NOT NULL,
  PRIMARY KEY (`image_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table 'image_batch_XREF'
--

DROP TABLE IF EXISTS `image_batch_XREF`;
CREATE TABLE `image_batch_XREF` (
  `imgid` int(10) unsigned NOT NULL,
  `image_batch_id` int(11) NOT NULL,
  `ordinal` INT(10) NOT NULL,
  PRIMARY KEY (`imgid`,`image_batch_id`),
  KEY `FK_image_batch_XREF_imgid_idx` (`imgid`),
  KEY `FK_image_batch_XREF_image_batch_id_idx` (`image_batch_id`),
  CONSTRAINT `FK_image_batch_XREF_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_image_batch_XREF_image_batch_id` FOREIGN KEY (`image_batch_id`) REFERENCES `image_batch` (`image_batch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `image_barcode`
--

DROP TABLE IF EXISTS `image_barcode`;
CREATE TABLE `image_barcode` (
  `imgid` int(10) unsigned NOT NULL,
  `barcode` varchar(255) NOT NULL,
  PRIMARY KEY (`imgid`),
  KEY `FK_image_barcode_imgid_idx` (`imgid`),
  CONSTRAINT `FK_image_barcode_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tr_batch`
--

DROP TABLE IF EXISTS `tr_batch`;
CREATE TABLE `tr_batch` (
  `tr_batch_id` int(10) NOT NULL AUTO_INCREMENT,
  `image_batch_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `completed_date` timestamp NULL,
  PRIMARY KEY (`tr_batch_id`),
  KEY `FK_tr_batch_image_batch_id_idx` (`image_batch_id`),
  CONSTRAINT `FK_tr_batch_image_batch_id` FOREIGN KEY (`image_batch_id`) REFERENCES `image_batch` (`image_batch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tr_user_batch`
--

DROP TABLE IF EXISTS `tr_user_batch`;
CREATE TABLE `tr_user_batch` (
  `tr_user_batch_id` int(10) NOT NULL AUTO_INCREMENT,
  `tr_batch_id` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `last_position` int(10) NOT NULL,
  PRIMARY KEY (`tr_user_batch_id`),
  KEY `FK_tr_user_batch_tr_batch_id_idx` (`tr_batch_id`),
  KEY `FK_tr_user_batch_uid_idx` (`uid`),
  CONSTRAINT `FK_tr_user_batch_tr_batch_id` FOREIGN KEY (`tr_batch_id`) REFERENCES `tr_batch` (`tr_batch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tr_user_batch_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `tr_batchImage`
--

DROP TABLE IF EXISTS `tr_batchImage`;
CREATE TABLE `tr_batchImage` (
  `tr_batch_id` int(10) NOT NULL,
  `imgid` int(10) unsigned NOT NULL,
  `ordinal` int(10) NOT NULL,
  PRIMARY KEY (`tr_batch_id`,`imgid`),
  KEY `FK_tr_batchImage_tr_batch_id_idx` (`tr_batch_id`),
  KEY `FK_tr_batchImage_imgid_idx` (`imgid`),
  CONSTRAINT `FK_tr_batchImage_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tr_batchImage_tr_batch_id` FOREIGN KEY (`tr_batch_id`) REFERENCES `tr_batch` (`tr_batch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Spark! Summer 2023 changes:
-- Add missing columns to the omocurrences table where the quick entry data is stored

ALTER TABLE `omoccurrences` 
    ADD COLUMN `filedUnder` varchar(255) DEFAULT NULL,
    ADD COLUMN `geoWithin` text DEFAULT NULL,
    ADD COLUMN `barcode` int(8) NOT NULL,
    ADD COLUMN `herbarium` varchar(4) NOT NULL,
    ADD COLUMN `accesNum` varchar(255) DEFAULT NULL,
    ADD COLUMN `currName` varchar(255) DEFAULT NULL,
    ADD COLUMN `idQualifier` varchar(16) DEFAULT NULL,
    ADD COLUMN `detText` text DEFAULT NULL,
    ADD COLUMN `provenance` text DEFAULT NULL,
    ADD COLUMN `container` varchar(255) DEFAULT NULL,
    ADD COLUMN `collTrip` varchar(255) DEFAULT NULL,
    ADD COLUMN `highGeo` text DEFAULT NULL,
    ADD COLUMN `frequency` varchar(255) DEFAULT NULL,
    ADD COLUMN `prepMethod` varchar(64) DEFAULT NULL,
    ADD COLUMN `format` varchar(64) DEFAULT NULL,
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
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `filedUnder` `filedUnder` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_filedUnder` FOREIGN KEY (`filedUnder`) REFERENCES `dropdown_filedUnder_values`(`id`);
/*
-- Code to reverse the changes if needed
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_filedUnder`,
    CHANGE `filedUnder` `filedUnder` varchar(255) DEFAULT NULL;

DROP TABLE IF EXISTS `dropdown_filedUnder_values`;
*/

-- Table to hold values for the currentName column
DROP TABLE IF EXISTS `dropdown_currName_values`;
CREATE TABLE `dropdown_currName_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `currName` `currName` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_currName` FOREIGN KEY (`currName`) REFERENCES `dropdown_currName_values`(`id`);

-- Table to hold values for the identifiedBy column
DROP TABLE IF EXISTS `dropdown_identifiedBy_values`;
CREATE TABLE `dropdown_identifiedBy_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `identifiedBy` `identifiedBy` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_identifiedBy` FOREIGN KEY (`identifiedBy`) REFERENCES `dropdown_identifiedBy_values`(`id`);

-- Table to hold values for the collectors column
DROP TABLE IF EXISTS `dropdown_recordedBy_values`;
CREATE TABLE `dropdown_recordedBy_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `recordedBy` `recordedBy` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_recordedBy` FOREIGN KEY (`recordedBy`) REFERENCES `dropdown_recordedBy_values`(`id`);

-- Table to hold values for the container column
DROP TABLE IF EXISTS `dropdown_container_values`;
CREATE TABLE `dropdown_container_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `container` `container` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_container` FOREIGN KEY (`container`) REFERENCES `dropdown_container_values`(`id`);

-- Table to hold values for the collTrip column
DROP TABLE IF EXISTS `dropdown_collTrip_values`;
CREATE TABLE `dropdown_collTrip_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `collTrip` `collTrip` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_collTrip` FOREIGN KEY (`collTrip`) REFERENCES `dropdown_collTrip_values`(`id`);

-- Table to hold values for the geoWithin column
DROP TABLE IF EXISTS `dropdown_geoWithin_values`;
CREATE TABLE `dropdown_geoWithin_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `geoWithin` `geoWithin` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_geoWithin` FOREIGN KEY (`geoWithin`) REFERENCES `dropdown_geoWithin_values`(`id`);

-- Table to hold values for the highGeo column
DROP TABLE IF EXISTS `dropdown_highGeo_values`;
CREATE TABLE `dropdown_highGeo_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `highGeo` `highGeo` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_highGeo` FOREIGN KEY (`highGeo`) REFERENCES `dropdown_highGeo_values`(`id`);

-- Table to hold values for the prepMethod column
DROP TABLE IF EXISTS `dropdown_prepMethod_values`;
CREATE TABLE `dropdown_prepMethod_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `prepMethod` `prepMethod` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_prepMethod` FOREIGN KEY (`prepMethod`) REFERENCES `dropdown_prepMethod_values`(`id`);

-- Table to hold values for the format column
DROP TABLE IF EXISTS `dropdown_format_values`;
CREATE TABLE `dropdown_format_values` (
  `id` int NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
);

ALTER TABLE `omoccurrences`
    CHANGE `format` `format` int DEFAULT NULL,
    ADD CONSTRAINT `FK_omoccurrences_format` FOREIGN KEY (`format`) REFERENCES `dropdown_format_values`(`id`);

-- Updates made in the week of June 5th 2023 to standardize primary key naming, add occid column and initialtimestamp

-- reformatting dropdown_filedUnder_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_filedUnder`;

ALTER TABLE `dropdown_filedUnder_values`
    CHANGE `id` `dd_filedUnderID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_filedUnder` FOREIGN KEY (`filedUnder`) REFERENCES `dropdown_filedUnder_values`(`dd_filedUnderID`);

-- reformatting dropdown_currName_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_currName`;

ALTER TABLE `dropdown_currName_values`
    CHANGE `id` `dd_currNameID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_currName` FOREIGN KEY (`currName`) REFERENCES `dropdown_currName_values`(`dd_currNameID`);

-- reformatting dropdown_identifiedBy_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_identifiedBy`;

ALTER TABLE `dropdown_identifiedBy_values`
    CHANGE `id` `dd_identifiedByID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_identifiedBy` FOREIGN KEY (`identifiedBy`) REFERENCES `dropdown_identifiedBy_values`(`dd_identifiedByID`);

-- reformatting dropdown_recordedBy_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_recordedBy`;

ALTER TABLE `dropdown_recordedBy_values`
    CHANGE `id` `dd_recordedByID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_recordedBy` FOREIGN KEY (`recordedBy`) REFERENCES `dropdown_recordedBy_values`(`dd_recordedByID`);

-- reformatting dropdown_container_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_container`;

ALTER TABLE `dropdown_container_values`
    CHANGE `id` `dd_containerID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_container` FOREIGN KEY (`container`) REFERENCES `dropdown_container_values`(`dd_containerID`);

-- reformatting dropdown_collTrip_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_collTrip`;

ALTER TABLE `dropdown_collTrip_values`
    CHANGE `id` `dd_collTripID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_collTrip` FOREIGN KEY (`collTrip`) REFERENCES `dropdown_collTrip_values`(`dd_collTripID`);

-- reformatting dropdown_geoWithin_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_geoWithin`;

ALTER TABLE `dropdown_geoWithin_values`
    CHANGE `id` `dd_geoWithinID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_geoWithin` FOREIGN KEY (`geoWithin`) REFERENCES `dropdown_geoWithin_values`(`dd_geoWithinID`);

-- reformatting dropdown_highGeo_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_highGeo`;

ALTER TABLE `dropdown_highGeo_values`
    CHANGE `id` `dd_highGeoID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_highGeo` FOREIGN KEY (`highGeo`) REFERENCES `dropdown_highGeo_values`(`dd_highGeoID`);

-- reformatting dropdown_prepMethod_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_prepMethod`;

ALTER TABLE `dropdown_prepMethod_values`
    CHANGE `id` `dd_prepMethodID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_prepMethod` FOREIGN KEY (`prepMethod`) REFERENCES `dropdown_prepMethod_values`(`dd_prepMethodID`);

-- reformatting dropdown_format_values
ALTER TABLE `omoccurrences`
    DROP FOREIGN KEY `FK_omoccurrences_format`;

ALTER TABLE `dropdown_format_values`
    CHANGE `id` `dd_formatID` int NOT NULL AUTO_INCREMENT,
    ADD `occid` int unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `omoccurrences`
    ADD CONSTRAINT `FK_omoccurrences_format` FOREIGN KEY (`format`) REFERENCES `dropdown_format_values`(`dd_formatID`);

-- constraining the format of the data in the dropdown table filedUnder
ALTER TABLE `dropdown_filedUnder_values`
    ADD `genus` text,
    ADD `species` text,
    ADD `authorName` text,
    ADD `vascularity` text,
    ADD `family` text,
    CHANGE `value` `displayValue` varchar(255);
/* function for getting the scientific name for filedUnder and currName (abandoned because using system function CONCAT is sufficient)
DELIMITER //

CREATE FUNCTION get_sciname(
    genus text,
    species text,
    authorName text,
    vascularity text,
    family text
)
RETURNS text
BEGIN
    DECLARE result text;
    
    SET result = CONCAT(genus, " ", species, " ", authorName, " [", vascularity, ", ", family, "]");
    
    RETURN result;
END //

DELIMITER ;
*/

-- Create trigger to update the displayValue column in dropdown_filedUnder_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_filedUnder
BEFORE INSERT ON dropdown_filedUnder_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

CREATE TRIGGER TR_update_dd_filedUnder
BEFORE UPDATE ON dropdown_filedUnder_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

-- constraining the format of the data in the dropdown table currName
ALTER TABLE `dropdown_currName_values`
    ADD `genus` text,
    ADD `species` text,
    ADD `authorName` text,
    ADD `vascularity` text,
    ADD `family` text,
    CHANGE `value` `displayValue` varchar(255);
    
-- Create trigger to update the displayValue column in dropdown_currName_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_currName
BEFORE INSERT ON dropdown_currName_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

CREATE TRIGGER TR_update_dd_currName
BEFORE UPDATE ON dropdown_currName_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.genus, " ", NEW.species, " ", NEW.authorName, " [", NEW.vascularity, ", ", NEW.family, "]");

-- constraining the format of the data in the dropdown table identifiedBy
ALTER TABLE `dropdown_identifiedBy_values`
    ADD `names` text,
    ADD `type` text,
    ADD `activeYears` text,
    ADD `area` text,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_identifiedBy_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_identifiedBy
BEFORE INSERT ON dropdown_identifiedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

CREATE TRIGGER TR_update_dd_identifiedBy
BEFORE UPDATE ON dropdown_identifiedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

-- constraining the format of the data in the dropdown table recordedBy
ALTER TABLE `dropdown_recordedBy_values`
    ADD `names` text,
    ADD `type` text,
    ADD `activeYears` text,
    ADD `area` text,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_recordedBy_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_recordedBy
BEFORE INSERT ON dropdown_recordedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

CREATE TRIGGER TR_update_dd_recordedBy
BEFORE UPDATE ON dropdown_recordedBy_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.names, " [", NEW.type, " ", NEW.activeYears, "] (", NEW.area, ")");

-- constraining the format of the data in the dropdown table geoWithin
ALTER TABLE `dropdown_geoWithin_values`
    ADD `area` text,
    ADD `areaType` text,
    ADD `synonym` text DEFAULT NULL,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_geoWithin_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_geoWithin
BEFORE INSERT ON dropdown_geoWithin_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " [", NEW.areaType, "] ", NEW.synonym);

CREATE TRIGGER TR_update_dd_geoWithin
BEFORE UPDATE ON dropdown_geoWithin_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " [", NEW.areaType, "] ", NEW.synonym);

-- constraining the format of the data in the dropdown table highGeo
ALTER TABLE `dropdown_highGeo_values`
    ADD `area` text,
    ADD `country` text,
    ADD `areaType` text,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_highGeo_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_highGeo
BEFORE INSERT ON dropdown_highGeo_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " (", NEW.country, ") [", NEW.areaType, "]");

CREATE TRIGGER TR_update_dd_highGeo
BEFORE UPDATE ON dropdown_highGeo_values
FOR EACH ROW
SET NEW.displayValue = CONCAT(NEW.area, " (", NEW.country, ") [", NEW.areaType, "]");

-- constraining the format of the data in the dropdown table container
ALTER TABLE `dropdown_container_values`
    ADD `container` text,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_container_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_container
BEFORE INSERT ON dropdown_container_values
FOR EACH ROW
SET NEW.displayValue = NEW.container;

CREATE TRIGGER TR_update_dd_container
BEFORE UPDATE ON dropdown_container_values
FOR EACH ROW
SET NEW.displayValue = NEW.container;

-- constraining the format of the data in the dropdown table collTrip
ALTER TABLE `dropdown_collTrip_values`
    ADD `collTrip` text,
    CHANGE `value` `displayValue` varchar(255);

-- Create trigger to update the displayValue column in dropdown_collTrip_values with the right value with every insert and update
CREATE TRIGGER TR_insert_dd_collTrip
BEFORE INSERT ON dropdown_collTrip_values
FOR EACH ROW
SET NEW.displayValue = NEW.collTrip;

CREATE TRIGGER TR_update_dd_collTrip
BEFORE UPDATE ON dropdown_collTrip_values
FOR EACH ROW
SET NEW.displayValue = NEW.collTrip;

-- reformatting image_barcode
ALTER TABLE `image_barcode`
    CHANGE `imgid` `imgID` int(10) unsigned NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

-- reformatting image_batch_XREF
ALTER TABLE `image_batch_XREF`
    CHANGE `imgid` `imgID` int(10) unsigned NOT NULL,
    CHANGE `image_batch_id` `image_batchID` int(11) NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

-- reformatting image_batch
ALTER TABLE `tr_batch`
    DROP FOREIGN KEY `FK_tr_batch_image_batch_id`;
ALTER TABLE `image_batch_XREF`
    DROP FOREIGN KEY `FK_image_batch_XREF_image_batch_id`;

ALTER TABLE `image_batch`
    CHANGE `image_batch_id` `image_batchID` int(11) NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `tr_batch`
    ADD CONSTRAINT `FK_tr_batch_image_batch_id` FOREIGN KEY (`image_batch_id`) REFERENCES `image_batch` (`image_batchID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `image_batch_XREF`
    ADD CONSTRAINT `FK_image_batch_XREF_image_batch_id` FOREIGN KEY (`image_batchID`) REFERENCES `image_batch` (`image_batchID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- reformatting tr_batch
ALTER TABLE `tr_batchImage`
    DROP FOREIGN KEY `FK_tr_batchImage_tr_batch_id`;
ALTER TABLE `tr_user_batch`
    DROP FOREIGN KEY `FK_tr_user_batch_tr_batch_id`;
ALTER TABLE `tr_batch`
    DROP FOREIGN KEY `FK_tr_batch_image_batch_id`;

ALTER TABLE `tr_batch`
    CHANGE `tr_batch_id` `tr_batchID` int(10) NOT NULL,
    CHANGE `image_batch_id` `image_batchID` int(11) NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

ALTER TABLE `tr_batchImage`
    ADD CONSTRAINT `FK_tr_batchImage_tr_batch_id` FOREIGN KEY (`tr_batch_id`) REFERENCES `tr_batch` (`tr_batchID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `tr_user_batch`
    ADD CONSTRAINT `FK_tr_user_batch_tr_batch_id` FOREIGN KEY (`tr_batch_id`) REFERENCES `tr_batch` (`tr_batchID`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `tr_batch`
    ADD CONSTRAINT `FK_tr_batch_image_batch_id` FOREIGN KEY (`image_batchID`) REFERENCES `image_batch` (`image_batchID`) ON DELETE CASCADE ON UPDATE CASCADE;

-- reformatting tr_batchImage
ALTER TABLE `tr_batchImage`
    CHANGE `tr_batch_id` `tr_batchID` int(10) NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;

-- reformatting tr_user_batch
ALTER TABLE `tr_user_batch`
    CHANGE `tr_user_batch_id` `tr_user_batchID` int(10) NOT NULL AUTO_INCREMENT,
    CHANGE `tr_batch_id` `tr_batchID` int(10) NOT NULL,
    ADD `initialtimestamp` TIMESTAMP NULL DEFAULT current_timestamp;