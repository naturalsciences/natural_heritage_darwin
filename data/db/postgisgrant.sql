GRANT ALL ON TABLE geometry_columns TO :dbuser;
GRANT ALL ON TABLE spatial_ref_sys TO :dbuser;
--ALTER ROLE :dbname SET search_path TO "$user", postgis, public;
--ALTER ROLE postgres SET search_path TO "$user", postgis, public;
