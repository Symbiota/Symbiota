ALTER TABLE `symbneon`.`omcollections` 
CHANGE COLUMN `datasetID` `datasetID` VARCHAR(400) NULL DEFAULT NULL ;

ALTER TABLE `symbneon`.`NeonShipment` 
ADD COLUMN `receiptTimestamp` DATETIME NULL AFTER `receiptstatus`;
