INSERT INTO `schemaversion` (versionnumber) values ("3.4");

#Increase user password field to accommodate new bcrypt hash 
ALTER TABLE `users` 
  CHANGE COLUMN `password` `password` VARCHAR(255) NULL DEFAULT NULL ;
  
  
