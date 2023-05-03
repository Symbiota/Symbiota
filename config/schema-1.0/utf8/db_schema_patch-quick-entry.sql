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

-- Add missing columns to the omocurrences table where the quick entry data is stored

ALTER TABLE `omoccurrences` 
    ADD COLUMN `filedUnder` varchar(255) DEFAULT NULL,
    ADD COLUMN `geographyWithin` text DEFAULT NULL,
    ADD COLUMN `barcode` int(8) NOT NULL,
    ADD COLUMN `herbarium` varchar(4) NOT NULL,
    ADD COLUMN `accessionNum` varchar(255) DEFAULT NULL,
    ADD COLUMN `currentName` varchar(255) DEFAULT NULL,
    ADD COLUMN `idqualifier` varchar(16) DEFAULT NULL,
    ADD COLUMN `detText` text DEFAULT NULL,
    ADD COLUMN `provenance` text DEFAULT NULL,
    ADD COLUMN `container` varchar(255) DEFAULT NULL,
    ADD COLUMN `collectingTrip` varchar(255) DEFAULT NULL,
    ADD COLUMN `higherGeography` text DEFAULT NULL,
    ADD COLUMN `frequency` varchar(255) DEFAULT NULL,
    ADD COLUMN `prepMethod` varchar(64) DEFAULT NULL,
    ADD COLUMN `format` varchar(64) DEFAULT NULL,
    ADD COLUMN `verbatimLatitude` varchar(255) DEFAULT NULL,
    ADD COLUMN `verbatimLongitude` varchar(255) DEFAULT NULL,
    ADD COLUMN `method` varchar(255) DEFAULT NULL;
