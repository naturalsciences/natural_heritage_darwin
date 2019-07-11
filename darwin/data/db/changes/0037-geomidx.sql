set search_path=darwin2,public;

CREATE INDEX idx_gist_specimens_flat_gtu_location_geom ON specimens_flat USING GIST ( (gtu_location::geometry) );
CREATE INDEX idx_gtu_location_geom ON gtu USING GIST ( (location::geometry) );

