
#geothesaurus schema and data adjustments
ALTER TABLE `omoccurrences` 
  ADD INDEX `IX_occurrences_countryCode` (`countryCode` ASC),
  ADD INDEX `IX_occurrences_continent` (`continent` ASC);

UPDATE geographicthesaurus g INNER JOIN geographicthesaurus a ON g.acceptedID = a.geoThesID 
  SET g.iso2 = a.iso2
  WHERE g.iso2 IS NULL AND a.iso2 IS NOT NULL;

#Populate NULL country codes
UPDATE omoccurrences o INNER JOIN geographicthesaurus g ON o.country = g.geoterm
  SET o.countryCode = g.iso2
  WHERE o.countryCode IS NULL AND g.geoLevel = 50 AND g.acceptedID IS NULL AND g.iso2 IS NOT NULL;

#Fix bad country code (likely bad imported values)
UPDATE omoccurrences o INNER JOIN geographicthesaurus g ON o.country = g.geoterm
  SET o.countryCode = g.iso2
  WHERE o.countryCode != g.iso2 AND g.geoLevel = 50 AND g.acceptedID IS NULL AND g.iso2 IS NOT NULL;

#Populate NULL continent values
UPDATE omoccurrences o INNER JOIN geographicThesaurus g ON o.countryCode = g.iso2 
  INNER JOIN geographicThesaurus p ON g.parentID = p.geoThesID
  SET o.continent = p.geoTerm
  WHERE o.continent IS NULL AND g.geoLevel = 50 AND p.acceptedID IS NULL AND g.acceptedID IS NULL;

#Fix bad continent values (likely bad improted values)
UPDATE omoccurrences o INNER JOIN geographicThesaurus g ON o.countryCode = g.iso2
  INNER JOIN geographicThesaurus p ON g.parentID = p.geoThesID
  SET o.continent = p.geoTerm
  WHERE o.continent != p.geoTerm AND g.geoLevel = 50 AND g.acceptedID IS NULL;



