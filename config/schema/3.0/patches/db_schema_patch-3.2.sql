INSERT INTO schemaversion (versionnumber) values ("3.2");


ALTER TABLE `taxa` 
  ADD COLUMN `rankName` VARCHAR(45) NULL AFTER `rankID`,
  CHANGE COLUMN `modifiedTimeStamp` `modifiedTimestamp` DATETIME NULL DEFAULT NULL ,
  CHANGE COLUMN `InitialTimeStamp` `initialTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ;

 
  
  