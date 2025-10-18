

ALTER TABLE `adminconfig` 
  RENAME TO  `adminproperties`;

ALTER TABLE `adminproperties` 
  ADD COLUMN `propType` VARCHAR(45) NULL AFTER `category`,
  ADD COLUMN `tableName` VARCHAR(45) NULL AFTER `propValue`,
  ADD COLUMN `tablePK` INT NULL AFTER `tableName`,
  CHANGE COLUMN `configID` `propID` INT(11) NOT NULL AUTO_INCREMENT ,
  CHANGE COLUMN `attributeName` `propName` VARCHAR(45) NOT NULL ,
  CHANGE COLUMN `attributeValue` `propValue` TEXT NOT NULL ;

ALTER TABLE `adminproperties` 
  DROP FOREIGN KEY `FK_adminConfig_uid`;

ALTER TABLE `adminproperties` 
  DROP INDEX `UQ_adminconfig_name`,
  DROP INDEX `FK_adminConfig_uid_idx`;

ALTER TABLE `adminproperties` 
  ADD CONSTRAINT `FK_adminproperties_uid`  FOREIGN KEY (`modifiedUid`)  REFERENCES `users` (`uid`);

ALTER TABLE `adminproperties` 
  ADD INDEX `FK_adminproperties_uid_idx` (`modifiedUid` ASC),
  ADD INDEX `IX_adminproperties_category` (`category` ASC),
  ADD INDEX `IX_adminproperties_type` (`propType` ASC),
  ADD INDEX `IX_adminproperties_name` (`propName` ASC),
  ADD INDEX `IX_adminproperties_table` (`tableName` ASC, `tablePK` ASC);

  