
  
  
ALTER TABLE `omoccurrences` 
  CHANGE COLUMN `localitySecurity` `recordSecurity` INT(10) NULL DEFAULT 0 COMMENT '0 = no security; 1 = hidden locality; 5 = hide full record' ,
  CHANGE COLUMN `localitySecurityReason` `securityReason` VARCHAR(100) NULL DEFAULT NULL ;

UPDATE omoccurrences
  SET recordSecurity = 0 
  WHERE recordSecurity IS NULL;

