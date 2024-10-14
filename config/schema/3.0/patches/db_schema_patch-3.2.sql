INSERT INTO `schemaversion` (versionnumber) values ("3.2");

ALTER TABLE `omoccurpaleogts`
  ADD COLUMN `myaStart` FLOAT NULL DEFAULT NULL AFTER `rankname`,
  ADD COLUMN `myaEnd` FLOAT NULL DEFAULT NULL AFTER `myaStart`,
  ADD COLUMN `errorRange` FLOAT NULL DEFAULT NULL AFTER `myaEnd`,
  ADD COLUMN `colorCode` VARCHAR(10) NULL DEFAULT NULL AFTER `errorRange`;




