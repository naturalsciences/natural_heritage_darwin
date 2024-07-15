
 CREATE database darwin2;
 Create user darwin2 with password 'darwin2';
 Create user d2viewer with password 'd2viewer';
 Create user ipt_viewer with password 'ipt_viewer';
 create extension postgis;
create extension hstore;
create extension pg_trgm;
create extension "uuid-ossp";
create extension fuzzystrmatch;
create extension pgcrypto;
CREATE EXTENSION plpython3u;

ALTER ROLE darwin2 SET search_path = darwin2,public;
ALTER DATABASE darwin2 SET datestyle ='dmy';

CREATE AGGREGATE array_concat_agg(anycompatiblearray) (
   SFUNC = array_cat,
   STYPE = anycompatiblearray
);
$