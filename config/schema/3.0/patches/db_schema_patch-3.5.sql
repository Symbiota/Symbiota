INSERT INTO `schemaversion` (versionnumber) VALUES ('3.5');




#Adjust column order so that taxon author field is closer to sciname
ALTER TABLE `uploadspectemp` 
  CHANGE COLUMN `scientificNameAuthorship` `scientificNameAuthorship` VARCHAR(255) NULL DEFAULT NULL AFTER `sciname`;


