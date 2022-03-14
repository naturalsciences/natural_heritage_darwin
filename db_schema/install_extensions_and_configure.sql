create extension postgis;
create extension hstore;
create extension pg_trgm;
create extension "uuid-ossp";
create extension fuzzystrmatch;
create extension pgcrypto;
CREATE EXTENSION plpython3u;

ALTER ROLE darwin2 SET search_path = darwin2,public;
ALTER DATABASE darwin2 SET datestyle ='dmy';
