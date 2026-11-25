INSERT INTO `schemaversion` (versionnumber) VALUES ('3.5');

#Add definitions for omoccurrences processingStatus controlled vocabularies 
INSERT INTO `ctcontrolvocab` (`title`, `tableName`, `fieldName`) 
  VALUES ('Occurrence Processing Status terms', 'omoccurrences', 'processingStatus');
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Unprocessed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 1" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 2" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Stage 3" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Pending Review" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Expert Required" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Reviewed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";
INSERT INTO `ctcontrolvocabterm` (`cvID`, `term`) 
  SELECT cvID, "Closed" FROM ctcontrolvocab WHERE tableName = "omoccurrences" AND fieldName = "processingStatus";


#Clear year, month, and day fields when eventDate and eventDate2 values conflict
UPDATE omoccurrences
SET `year` = NULL, `month` = NULL, `day` = NULL
WHERE `year` IS NOT NULL AND eventDate IS NOT NULL AND eventDate2 IS NOT NULL AND YEAR(eventDate) != YEAR(eventDate2);

UPDATE omoccurrences
SET `month` = NULL, `day` = NULL
WHERE `month` IS NOT NULL AND eventDate IS NOT NULL AND eventDate2 IS NOT NULL AND MONTH(eventDate) != MONTH(eventDate2);

UPDATE omoccurrences
SET `day` = NULL
WHERE `day` IS NOT NULL AND eventDate IS NOT NULL AND eventDate2 IS NOT NULL AND eventDate != eventDate2;


#Adjust column order so that taxon author field is closer to sciname
ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `institutionCode` `institutionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `ownerInstitutionCode`,
  CHANGE COLUMN `collectionCode` `collectionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `institutionCode`,
  CHANGE COLUMN `institutionID` `institutionID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionCode`,
  CHANGE COLUMN `collectionID` `collectionID` VARCHAR(255) NULL DEFAULT NULL AFTER `institutionID`,
  CHANGE COLUMN `datasetID` `datasetID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionID`,
  CHANGE COLUMN `organismID` `organismID` VARCHAR(150) NULL DEFAULT NULL AFTER `datasetID`,
  CHANGE COLUMN `scientificNameAuthorship` `scientificNameAuthorship` VARCHAR(255) NULL DEFAULT NULL AFTER `sciname`;


#Remove duplicate uploadspectemp indexes 
ALTER TABLE `uploadspectemp` 
  DROP INDEX `Index_uploadspec_othercatalognumbers` ,
  DROP INDEX `Index_uploadspec_catalognumber` ,
  DROP INDEX `Index_uploadspec_sciname` ,
  DROP INDEX `Index_uploadspectemp_dbpk` ,
  DROP INDEX `Index_uploadspectemp_occid` ;

ALTER TABLE `uploadspectemp` 
  ADD INDEX `IX_uploadspectemp_occid` (`occid` ASC, `collid` ASC);


#Remove function that was only used within db_schema_3.2.sql db patch 
DROP FUNCTION `swap_wkt_coords`;


#Convert occurrence points spatial indexing table from MyISAM to InnoDB
DROP TABLE omoccurpoints;

CREATE TABLE `omoccurpoints` (
  `geoID` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) UNSIGNED NOT NULL,
  `lngLatPoint` point NOT NULL,
  `errradiuspoly` polygon DEFAULT NULL,
  `footprintpoly` polygon DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoID`),
  UNIQUE KEY `IX_omoccurpoints_occid` (`occid`),
  SPATIAL KEY `IX_omoccurpoints_latLngPoint` (`lngLatPoint`)
) ENGINE=InnoDB;

ALTER TABLE `omoccurpoints` 
ADD CONSTRAINT `FK_oomoccurpoints_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`)  ON DELETE CASCADE  ON UPDATE CASCADE;

INSERT INTO omoccurpoints (occid, lngLatPoint) 
  SELECT occid, POINT(decimalLongitude, decimalLatitude)
  FROM omoccurrences
  WHERE decimalLatitude between -90 and 90 and decimalLongitude between -180 and 180;


#Ensure that there are no geothes terms that contain double spaces, which has been an issue
UPDATE geographicthesaurus
SET geoterm = replace(geoterm, "  ", " ") 
WHERE geoterm LIKE "%  %";

#Add indexed table for polygon search
CREATE TABLE `geographicpolygonindex` (
  `geoThesID` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoThesID`, `occid`),
  KEY `IX_geopolygonindex_occid` (`occid`),
  CONSTRAINT `FK_geopolygonindex_geothesid` FOREIGN KEY (`geoThesID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_geopolygonindex_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `geographicpolygon`
  ADD COLUMN `indexStatus` enum('pending','building','ready','failed') NOT NULL DEFAULT 'pending' AFTER `geoJSON`,
  ADD COLUMN `indexedAt` timestamp NULL DEFAULT NULL AFTER `indexStatus`,
  ADD COLUMN `indexRecordCount` int unsigned DEFAULT NULL AFTER `indexedAt`,
  ADD COLUMN `indexError` varchar(255) DEFAULT NULL AFTER `indexRecordCount`;

DROP TRIGGER IF EXISTS `geopolygonindex_point_insert`;
DROP TRIGGER IF EXISTS `geopolygonindex_point_update`;
DROP TRIGGER IF EXISTS `geopolygonindex_point_delete`;
DROP TRIGGER IF EXISTS `omoccurrences_update`;

DELIMITER //

CREATE TRIGGER `geopolygonindex_point_insert` AFTER INSERT ON `omoccurpoints`
FOR EACH ROW BEGIN
  INSERT IGNORE INTO geographicpolygonindex(geoThesID, occid)
  SELECT gp.geoThesID, NEW.occid
  FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
  WHERE gth.isSearchable = 1 AND gth.geoLevel = 110 AND gp.polygonIndexStatus = 'ready'
  AND MBRContains(gp.footprintPolygon, NEW.lngLatPoint)
  AND ST_Contains(gp.footprintPolygon, NEW.lngLatPoint);
END//

CREATE TRIGGER `geopolygonindex_point_update` AFTER UPDATE ON `omoccurpoints`
FOR EACH ROW BEGIN
  DELETE FROM geographicpolygonindex WHERE occid = OLD.occid;

  INSERT IGNORE INTO geographicpolygonindex(geoThesID, occid)
  SELECT gp.geoThesID, NEW.occid
  FROM geographicpolygon gp INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
  WHERE gth.isSearchable = 1 AND gth.geoLevel = 110 AND gp.polygonIndexStatus = 'ready'
  AND MBRContains(gp.footprintPolygon, NEW.lngLatPoint)
  AND ST_Contains(gp.footprintPolygon, NEW.lngLatPoint);
END//

CREATE TRIGGER `geopolygonindex_point_delete` BEFORE DELETE ON `omoccurpoints`
FOR EACH ROW BEGIN
  DELETE FROM geographicpolygonindex WHERE occid = OLD.occid;
END//

DELIMITER ;


#Trigger adjustments to omoccurrences table to adjust to InnoDB omoccurpoints table change (no longer need delete due to cascading relationship)
DELIMITER $$

DROP TRIGGER IF EXISTS `omoccurrences_insert` $$
DROP TRIGGER IF EXISTS `omoccurrences_update` $$
DROP TRIGGER IF EXISTS `omoccurrences_delete` $$

CREATE DEFINER=`root`@`localhost` TRIGGER `omoccurrences_insert` 
  AFTER INSERT ON `omoccurrences` 
  FOR EACH ROW BEGIN 
    IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN 
      INSERT INTO omoccurpoints (`occid`, `lngLatPoint`) VALUES (NEW.`occid`, Point(NEW.`decimalLongitude`, NEW.`decimalLatitude`)); 
    END IF; 
END$$

# changing the old trigger since it only updates on both lat and long changes.
CREATE DEFINER=`root`@`localhost` TRIGGER `omoccurrences_update`
AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
  IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
    IF OLD.`decimalLatitude` IS NULL OR OLD.`decimalLongitude` IS NULL OR NOT (NEW.`decimalLatitude` <=> OLD.`decimalLatitude`) OR NOT (NEW.`decimalLongitude` <=> OLD.`decimalLongitude`) THEN
      IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
        UPDATE omoccurpoints
        SET `lngLatPoint` = Point(NEW.`decimalLongitude`, NEW.`decimalLatitude`)
        WHERE `occid` = NEW.`occid`;
      ELSE
        INSERT INTO omoccurpoints (`occid`,`lngLatPoint`)
        VALUES (NEW.`occid`, Point(NEW.`decimalLongitude`, NEW.`decimalLatitude`));
      END IF;
    END IF;
  ELSE
    IF OLD.`decimalLatitude` IS NOT NULL OR OLD.`decimalLongitude` IS NOT NULL THEN
      DELETE FROM omoccurpoints WHERE `occid` = NEW.`occid`;
    END IF;
  END IF;
END$$

DELIMITER ;

