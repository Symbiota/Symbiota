
CREATE TABLE `occurrencepolygonindex` (
  `geoThesID` int(11) NOT NULL,
  `occid` int(11) NOT NULL,
  `indexedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`geoThesID`, `occid`),
  KEY `IX_occurrencepolygonindex_occid` (`occid`)
) ENGINE=InnoDB;

ALTER TABLE `geographicpolygon`
  ADD COLUMN `polygonIndexStatus` enum('pending','building','ready','failed') NOT NULL DEFAULT 'pending',
  ADD COLUMN `polygonIndexedAt` datetime DEFAULT NULL,
  ADD COLUMN `polygonIndexRecordCount` int unsigned DEFAULT NULL,
  ADD COLUMN `polygonIndexError` varchar(255) DEFAULT NULL;

  -- CREATE TABLE `occurrencepolygonindex` (
--   `geoThesID` int(11) NOT NULL,
--   `occid` int(11) NOT NULL,
--   `indexedAt` timestamp NOT NULL DEFAULT current_timestamp(),
--   PRIMARY KEY (`geoThesID`, `occid`),
--   KEY `IX_occurrencepolygonindex_occid` (`occid`),
--   CONSTRAINT `FK_occurrencepolygonindex_geothes` FOREIGN KEY (`geoThesID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
--   CONSTRAINT `FK_occurrencepolygonindex_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
-- ) ENGINE=InnoDB;

-- ALTER TABLE `geographicpolygon`
--   ADD COLUMN `polygonIndexStatus` enum('pending','building','ready','failed') NOT NULL DEFAULT 'pending' AFTER `geoJSON`,
--   ADD COLUMN `polygonIndexedAt` datetime DEFAULT NULL AFTER `polygonIndexStatus`,
--   ADD COLUMN `polygonIndexRecordCount` int unsigned DEFAULT NULL AFTER `polygonIndexedAt`,
--   ADD COLUMN `polygonIndexError` varchar(255) DEFAULT NULL AFTER `polygonIndexRecordCount`;