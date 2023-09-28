INSERT INTO schemaversion (versionnumber) values ("3.1");

#Set foreign keys for fmchklstcoordinates
ALTER TABLE `fmchklstcoordinates` 
  ADD INDEX `FK_checklistCoord_tid_idx` (`tid` ASC);

ALTER TABLE `fmchklstcoordinates` 
  ADD CONSTRAINT `FK_checklistCoord_clid`  FOREIGN KEY (`clid`)  REFERENCES `fmchecklists` (`clid`)  ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_checklistCoord_tid`  FOREIGN KEY (`tid`)  REFERENCES `taxa` (`tid`)  ON DELETE CASCADE  ON UPDATE CASCADE;


# Needed to ensure basisOfRecord values are tagged correctly based on collection type (aka collType field)
UPDATE omoccurrences o INNER JOIN omcollections c ON o.collid = c.collid
SET o.basisofrecord = "PreservedSpecimen"
WHERE (o.basisofrecord = "HumanObservation" OR o.basisofrecord IS NULL) AND c.colltype = 'Preserved Specimens'
AND o.occid NOT IN(SELECT occid FROM omoccuredits WHERE fieldname = "basisofrecord");


