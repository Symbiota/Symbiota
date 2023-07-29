INSERT INTO schemaversion (versionnumber) values ("3.1");

# Needed to ensure basisOfRecord values are tagged correctly based on collection type (aka collType field)
UPDATE omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid
SET o.basisofrecord = "PreservedSpecimen"
WHERE (o.basisofrecord = "HumanObservation" OR o.basisofrecord IS NULL) AND c.colltype = 'Preserved Specimens'
AND o.occid NOT IN(SELECT occid FROM omoccuredits WHERE fieldname = "basisofrecord");
