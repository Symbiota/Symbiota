



ALTER TABLE `omexsiccatititles` 
  CHANGE COLUMN `exsrange` `exsRange` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `startdate` `startDate` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `enddate` `endDate` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `lasteditedby` `lastEditedBy` VARCHAR(45) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP();

ALTER TABLE `omexsiccatititles` 
  DROP INDEX `index_exsiccatiTitle` ;

ALTER TABLE `omexsiccatititles` 
  ADD INDEX `IX_exsiccatititle_title` (`title` ASC);

ALTER TABLE `omexsiccatinumbers` 
  CHANGE COLUMN `exsnumber` `exsNumber` VARCHAR(45) NOT NULL,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP();

ALTER TABLE `omexsiccatinumbers` 
  DROP FOREIGN KEY `FK_exsiccatiTitleNumber`;

ALTER TABLE `omexsiccatinumbers`
  DROP INDEX `FK_exsiccatiTitle` ,
  DROP INDEX `FK_exsiccatiTitleNumber` ,
  DROP INDEX `Index_omexsiccatinumbers_unique`;
  
ALTER TABLE `omexsiccatinumbers`
  ADD INDEX `FK_exsiccatiNumber_ometid_idx` (`ometid` ASC),
  ADD INDEX `FK_exsiccatiNumber_number_idx` (`exsNumber` ASC),
  ADD UNIQUE INDEX `UQ_exsiccatiNumber_ometid` (`ometid` ASC, `exsNumber` ASC);

ALTER TABLE `omexsiccatinumbers` 
  ADD CONSTRAINT `FK_exsiccatinumber_ometid` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `omexsiccatiocclink`
  ADD COLUMN `omexid` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`omexid`);

ALTER TABLE `omexsiccatiocclink` 
  DROP FOREIGN KEY `FKExsiccatiNumOccLink1`,
  DROP FOREIGN KEY `FKExsiccatiNumOccLink2`;

ALTER TABLE `omexsiccatiocclink` 
  DROP INDEX `UniqueOmexsiccatiOccLink` ,
  DROP INDEX `FKExsiccatiNumOccLink2` ,
  DROP INDEX `FKExsiccatiNumOccLink1` ;

ALTER TABLE `omexsiccatiocclink`
  ADD INDEX `FK_exsiccatiOccLink_omenid_idx` (`omenid` ASC),
  ADD UNIQUE INDEX `UQ_exsiccatiOccLink_occid` (`occid` ASC),
  ADD INDEX `IX_exsiccati_occid_fk` (`occid` ASC);

ALTER TABLE `omexsiccatiocclink` 
  ADD CONSTRAINT `FK_exsiccatiOccLink_omenid` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_exsiccatiOccLink_occid`  FOREIGN KEY (`occid`)  REFERENCES `omoccurrences` (`occid`) ON DELETE RESTRICT ON UPDATE CASCADE;


ALTER TABLE `specprocessorrawlabels`
  CHANGE COLUMN `rawstr` `rawStr` TEXT NOT NULL ,
  CHANGE COLUMN `processingvariables` `processingVariables` VARCHAR(250) NULL DEFAULT NULL ,
  CHANGE COLUMN `sortsequence` `sortSequence` INT(11) NULL DEFAULT NULL ,
  CHANGE COLUMN `initialtimestamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;
