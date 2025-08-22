#fields and indexes for search by polygons
ADD COLUMN isSearchable TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE geographicpolygon ADD SPATIAL INDEX(footprintPolygon);
ALTER TABLE omoccurpoints ADD SPATIAL INDEX(lngLatPoint);