# Clean up localitySecurity for occurrences that are cultivated and have not explicitly had their localitySecurity edited to be 1 (and are missing a security reason) more recently than it has been edited to 0.

UPDATE omoccurrences o INNER JOIN omoccuredits e ON o.occid = e.occid
LEFT JOIN (SELECT occid, ocedid FROM omoccuredits WHERE fieldName = "localitySecurity" AND fieldValueNew = 0) e2 ON e.occid = e2.occid AND e.ocedid < e2.ocedid
SET o.localitySecurity = 1, o.localitySecurityReason = "[Security Setting Explicitly Locked]"
WHERE o.localitySecurityReason IS NULL AND e.fieldName = "localitySecurity" AND e.fieldValueNew = 1
AND e2.occid IS NULL;

UPDATE omoccurrences SET localitySecurity=0 WHERE cultivationStatus=1 AND localitySecurity=1 AND localitySecurityReason IS NULL;