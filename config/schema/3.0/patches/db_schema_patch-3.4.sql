#field for search by polygons
ALTER TABLE geographicthesaurus
    ADD COLUMN isSearchable TINYINT(1) NOT NULL DEFAULT 0;