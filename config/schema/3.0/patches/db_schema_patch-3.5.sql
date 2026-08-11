INSERT INTO `schemaversion` (versionnumber) VALUES ('3.5');




#Adjust column order so that taxon author field is closer to sciname
ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `institutionCode` `institutionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `ownerInstitutionCode`,
  CHANGE COLUMN `collectionCode` `collectionCode` VARCHAR(64) NULL DEFAULT NULL AFTER `institutionCode`,
  CHANGE COLUMN `institutionID` `institutionID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionCode`,
  CHANGE COLUMN `collectionID` `collectionID` VARCHAR(255) NULL DEFAULT NULL AFTER `institutionID`,
  CHANGE COLUMN `datasetID` `datasetID` VARCHAR(255) NULL DEFAULT NULL AFTER `collectionID`,
  CHANGE COLUMN `organismID` `organismID` VARCHAR(150) NULL DEFAULT NULL AFTER `datasetID`,
  CHANGE COLUMN `scientificNameAuthorship` `scientificNameAuthorship` VARCHAR(255) NULL DEFAULT NULL AFTER `sciname`;


#Ensure that there are no geothes terms that contain double spaces, which has been an issue
UPDATE geographicthesaurus
SET geoterm = replace(geoterm, "  ", " ") 
WHERE geoterm LIKE "%  %";

#Add indexed table for polygon search
CREATE TABLE `occurrencepolygonindex` (
  `geoThesID` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `indexedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoThesID`, `occid`),
  KEY `IX_occurrencepolygonindex_occid` (`occid`),
  CONSTRAINT `FK_occpolyindex_geothesid` FOREIGN KEY (`geoThesID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occpolyindex_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `geographicpolygon`
  ADD COLUMN `polygonIndexStatus` enum('pending','building','ready','failed') NOT NULL DEFAULT 'pending',
  ADD COLUMN `polygonIndexedAt` timestamp NULL DEFAULT NULL,
  ADD COLUMN `polygonIndexRecordCount` int unsigned DEFAULT NULL,
  ADD COLUMN `polygonIndexError` varchar(255) DEFAULT NULL;

DROP TRIGGER IF EXISTS `occurrencepolygonindex_point_insert`;
DROP TRIGGER IF EXISTS `occurrencepolygonindex_point_update`;
DROP TRIGGER IF EXISTS `occurrencepolygonindex_point_delete`;
DROP TRIGGER IF EXISTS `omoccurrences_update`;

DELIMITER //
# changing the old trigger since it only updates on both lat and long changes.
CREATE TRIGGER `omoccurrences_update` AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
  IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
    IF OLD.`decimalLatitude` IS NULL OR OLD.`decimalLongitude` IS NULL OR NOT (NEW.`decimalLatitude` <=> OLD.`decimalLatitude`) OR NOT (NEW.`decimalLongitude` <=> OLD.`decimalLongitude`) THEN
      IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
        UPDATE omoccurpoints
        SET `point` = Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`), `lngLatPoint` = Point(NEW.`decimalLongitude`, NEW.`decimalLatitude`)
        WHERE `occid` = NEW.`occid`;
      ELSE
        INSERT INTO omoccurpoints (`occid`,`point`,`lngLatPoint`)
        VALUES (NEW.`occid`, Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`), Point(NEW.`decimalLongitude`, NEW.`decimalLatitude`));
      END IF;
    END IF;
  ELSE
    IF OLD.`decimalLatitude` IS NOT NULL OR OLD.`decimalLongitude` IS NOT NULL THEN
      DELETE FROM omoccurpoints WHERE `occid` = NEW.`occid`;
    END IF;
  END IF;
END//

CREATE TRIGGER `occurrencepolygonindex_point_insert` AFTER INSERT ON `omoccurpoints`
FOR EACH ROW BEGIN
  INSERT IGNORE INTO occurrencepolygonindex(geoThesID, occid, indexedAt)
  SELECT gp.geoThesID, NEW.occid, NOW()
  FROM geographicpolygon gp
  INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
  WHERE gth.isSearchable = 1
  AND gth.geoLevel = 110
  AND gp.polygonIndexStatus = 'ready'
  AND MBRContains(gp.footprintPolygon, NEW.lngLatPoint)
  AND ST_Contains(gp.footprintPolygon, NEW.lngLatPoint);
END//

CREATE TRIGGER `occurrencepolygonindex_point_update` AFTER UPDATE ON `omoccurpoints`
FOR EACH ROW BEGIN
  DELETE FROM occurrencepolygonindex WHERE occid = OLD.occid;

  INSERT IGNORE INTO occurrencepolygonindex(geoThesID, occid, indexedAt)
  SELECT gp.geoThesID, NEW.occid, NOW()
  FROM geographicpolygon gp
  INNER JOIN geographicthesaurus gth ON gp.geoThesID = gth.geoThesID
  WHERE gth.isSearchable = 1
  AND gth.geoLevel = 110
  AND gp.polygonIndexStatus = 'ready'
  AND MBRContains(gp.footprintPolygon, NEW.lngLatPoint)
  AND ST_Contains(gp.footprintPolygon, NEW.lngLatPoint);
END//

CREATE TRIGGER `occurrencepolygonindex_point_delete` BEFORE DELETE ON `omoccurpoints`
FOR EACH ROW BEGIN
  DELETE FROM occurrencepolygonindex WHERE occid = OLD.occid;
END//

DELIMITER ;

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

