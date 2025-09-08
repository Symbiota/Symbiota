#fields and indexes for search by polygons
ALTER TABLE geographicthesaurus
    ADD COLUMN isSearchable TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE omoccurpoints ADD SPATIAL INDEX(lngLatPoint);