
  
  
ALTER TABLE `omoccurrences` 
  CHANGE COLUMN `localitySecurity` `recordSecurity` INT(10) NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality; 5 = hide full record' ,
  CHANGE COLUMN `localitySecurityReason` `securityReason` VARCHAR(100) NULL DEFAULT NULL ;

ALTER TABLE `omoccurrences` 
  DROP INDEX `IX_occurrences_localitySecurity` ;

ALTER TABLE `omoccurrences` 
  ADD INDEX `IX_occurrences_recordSecurity` (`recordSecurity` ASC);


UPDATE omoccurrences
  SET recordSecurity = 0 
  WHERE recordSecurity IS NULL;

