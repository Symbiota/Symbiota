

ALTER TABLE `portalindex` 
  ADD COLUMN `lastContact` TIMESTAMP NULL AFTER `notes`,
  ADD COLUMN `modifiedTimestamp` TIMESTAMP NULL AFTER `lastContact`;

