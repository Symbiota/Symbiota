

ALTER TABLE `portalindex` 
  ADD COLUMN `lastContact` TIMESTAMP NULL AFTER `notes`,
  ADD COLUMN `modifiedTimestamp` TIMESTAMP NULL AFTER `lastContact`,
  ADD COLUMN `apiVersion` VARCHAR(45) NULL AFTER `symbiotaVersion`;


