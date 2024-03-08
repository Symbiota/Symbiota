# Clean up localitySecurity for occurrences that are cultivated and have not explicitly had their localitySecurity edited to be 1

UPDATE omoccurrences SET localitySecurityReason = 'a user explicitly set localitySecurity on this occurrence' WHERE occid IN (SELECT occid FROM omoccuredits WHERE fieldName='localitySecurity' AND fieldValueNew=1);
UPDATE omoccurrences SET localitySecurity=0 WHERE occid IN (SELECT occid FROM omoccurrences WHERE cultivationStatus=1 AND localitySecurity=1 AND localitySecurityReason IS NULL);