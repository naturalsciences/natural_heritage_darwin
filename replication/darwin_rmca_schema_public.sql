--
-- PostgreSQL database dump
--

-- Dumped from database version 12.9 (Ubuntu 12.9-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 15.3

-- Started on 2023-12-22 16:35:14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 12 (class 2615 OID 467103077)
-- Name: darwin2; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA darwin2;


ALTER SCHEMA darwin2 OWNER TO darwin2;

--
-- TOC entry 10 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 3 (class 3079 OID 467180471)
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA public;


--
-- TOC entry 4276 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- TOC entry 4 (class 3079 OID 467095362)
-- Name: hstore; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA public;


--
-- TOC entry 4277 (class 0 OID 0)
-- Dependencies: 4
-- Name: EXTENSION hstore; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION hstore IS 'data type for storing sets of (key, value) pairs';


--
-- TOC entry 5 (class 3079 OID 467098586)
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- TOC entry 4278 (class 0 OID 0)
-- Dependencies: 5
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


--
-- TOC entry 2 (class 3079 OID 467094347)
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- TOC entry 4279 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry and geography spatial types and functions';


--
-- TOC entry 315 (class 1255 OID 467260983)
-- Name: fct_chk_referencedrecord(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_chk_referencedrecord() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists integer;
BEGIN
	  IF NEW.record_id != -1 THEN --ftheeten 2019 01 18
		  EXECUTE 'SELECT 1 WHERE EXISTS ( SELECT id FROM ' || quote_ident(NEW.referenced_relation)  || ' WHERE id=' || quote_literal(NEW.record_id) || ')' INTO rec_exists;
		  IF rec_exists IS NULL THEN
		    RAISE EXCEPTION 'The referenced record does not exists % %',NEW.referenced_relation, NEW.record_id;
		  END IF;	 
	  END IF;	
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_referencedrecord() OWNER TO darwin2;

--
-- TOC entry 1078 (class 1255 OID 467103078)
-- Name: fct_mask_date(timestamp without time zone, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_mask_date(date_fld timestamp without time zone, mask_fld integer) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$

  SELECT
CASE WHEN ($2 & 32)!=0 THEN date_part('year',$1)::text ELSE 'xxxx' END || '-' ||
CASE WHEN ($2 & 16)!=0 THEN date_part('month',$1)::text ELSE 'xx' END || '-' ||
CASE WHEN ($2 & 8)!=0 THEN date_part('day',$1)::text ELSE 'xx' END;
$_$;


ALTER FUNCTION darwin2.fct_mask_date(date_fld timestamp without time zone, mask_fld integer) OWNER TO darwin2;

--
-- TOC entry 1083 (class 1255 OID 467107244)
-- Name: fct_mask_date_ymd_gbif(timestamp without time zone, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_mask_date_ymd_gbif(date_fld timestamp without time zone, mask_fld integer) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$

  SELECT
 replace( CASE WHEN ($2 & 32)!=0 THEN date_part('year',$1)::text ELSE 'xxxx' END ||'-'||
  CASE WHEN ($2 & 16)!=0 THEN lpad(date_part('month',$1)::text, 2,'0') ELSE 'xx' END  ||'-'||
   CASE WHEN ($2 & 8)!=0 THEN lpad(date_part('day',$1)::text, 2,'0') ELSE 'xx'-- END
  /*
  CASE WHEN ($2 & 8)!=0 THEN date_part('day',$1)::text ELSE 'xx' END|| '-'||
CASE WHEN ($2 & 16)!=0 THEN date_part('month',$1)::text ELSE 'xx' END || '-' ||
CASE WHEN ($2 & 32)!=0 THEN date_part('year',$1)::text ELSE 'xxxx'*/
END,'-xx','');
$_$;


ALTER FUNCTION darwin2.fct_mask_date_ymd_gbif(date_fld timestamp without time zone, mask_fld integer) OWNER TO darwin2;

--
-- TOC entry 313 (class 1255 OID 467260981)
-- Name: fct_nbr_in_synonym(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_nbr_in_synonym() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  nbr integer = 0 ;
BEGIN

  SELECT count(id) INTO nbr FROM classification_synonymies WHERE
      referenced_relation = NEW.referenced_relation
      AND record_id = NEW.record_id
      AND group_name = NEW.group_name;

  IF TG_OP = 'INSERT' THEN
    IF nbr > 1 THEN
      RAISE EXCEPTION 'You can ''t set this synonym twice!';
    END IF;
  ELSE
--     RAISE info 'nbr %', nbr;
    IF nbr > 2 THEN
      RAISE EXCEPTION 'You can ''t set this synonym twice!';
    END IF;
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_nbr_in_synonym() OWNER TO darwin2;

--
-- TOC entry 314 (class 1255 OID 467260982)
-- Name: fct_reinit_sequences_synonyms(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_reinit_sequences_synonyms() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists integer;
BEGIN
	  IF NEW.record_id != -1 THEN --ftheeten 2019 01 18
		  PERFORM SETVAL('classification_synonymies_group_id_seq', (SELECT MAX(group_id)+1 FROM classification_synonymies));
	  END IF;	
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_reinit_sequences_synonyms() OWNER TO darwin2;

--
-- TOC entry 1084 (class 1255 OID 467104486)
-- Name: fct_rmca_flush_tables(); Type: FUNCTION; Schema: darwin2; Owner: postgres
--

CREATE FUNCTION darwin2.fct_rmca_flush_tables() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	TRUNCATE TABLE darwin2.catalogue_levels CASCADE;
	TRUNCATE TABLE darwin2.catalogue_people CASCADE;
	TRUNCATE TABLE darwin2.codes CASCADE;
	TRUNCATE TABLE darwin2.collections CASCADE;
	TRUNCATE TABLE darwin2.country_cleaning CASCADE;
	TRUNCATE TABLE darwin2.ext_links CASCADE;
	TRUNCATE TABLE darwin2.flat_dict CASCADE;
	TRUNCATE TABLE darwin2.identifications CASCADE;
	TRUNCATE TABLE darwin2.people CASCADE;
	TRUNCATE TABLE darwin2.specimens CASCADE;
	TRUNCATE TABLE darwin2.specimens_stable_ids CASCADE;
	TRUNCATE TABLE darwin2.src_mv_specimen_public CASCADE;
	TRUNCATE TABLE darwin2.src_taxonomy CASCADE;
	TRUNCATE TABLE darwin2.tags CASCADE;
	TRUNCATE TABLE darwin2.taxonomy CASCADE;
	TRUNCATE TABLE darwin2.tmv_collections_full_path_recursive_public CASCADE;
	TRUNCATE TABLE darwin2.tmv_search_public_specimen CASCADE;
	TRUNCATE TABLE darwin2.tmv_specimen_public CASCADE;
	TRUNCATE TABLE darwin2.tmv_taxonomy_by_collection CASCADE;
	TRUNCATE TABLE darwin2.users CASCADE;
	TRUNCATE TABLE darwin2.users_tracking CASCADE;
	
	
	
return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_flush_tables() OWNER TO postgres;

--
-- TOC entry 1081 (class 1255 OID 467103079)
-- Name: fct_rmca_refresh_materialized_view(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_refresh_materialized_view() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	REFRESH materialized view darwin2.collections ;
	REFRESH materialized view darwin2.ext_links ;
	REFRESH materialized view darwin2.flat_dict ;
	REFRESH materialized view darwin2.identifications ;
	
	REFRESH materialized view darwin2.mv_collections_full_path_recursive ;
	REFRESH materialized view darwin2.mv_collections_full_path_recursive_public ;
	REFRESH materialized view darwin2.people ;
	REFRESH MATERIALIZED VIEW darwin2.mv_taxa_in_specimens;
	REFRESH materialized view darwin2.taxonomy ;
	REFRESH materialized view darwin2.specimens ;
	REFRESH materialized view darwin2.mv_specimen_public ;
	REFRESH materialized view darwin2.specimens_stable_ids ;
	REFRESH materialized view darwin2.tags ;
	REFRESH materialized view darwin2.codes ;
	REFRESH materialized view darwin2.gtu ;
	REFRESH materialized view darwin2.catalogue_people ;
	REFRESH materialized view darwin2.v_rdf_view  ;
	REFRESH materialized view darwin2.mv_specimen_public ;
	REFRESH MATERIALIZED VIEW mv_search_public_specimen;
return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_refresh_materialized_view() OWNER TO darwin2;

--
-- TOC entry 1082 (class 1255 OID 467103080)
-- Name: fct_rmca_refresh_materialized_view_and_consult_tables(); Type: FUNCTION; Schema: darwin2; Owner: postgres
--

CREATE FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	
	TRUNCATE  darwin2.src_mv_specimen_public;
	TRUNCATE  darwin2.src_taxonomy;
	TRUNCATE  darwin2.collections;
	TRUNCATE  darwin2.ext_links;
	TRUNCATE  darwin2.flat_dict;
	TRUNCATE  darwin2.identifications;
	TRUNCATE  darwin2.people;
	TRUNCATE  darwin2.taxonomy;
	TRUNCATE  darwin2.specimens;
	TRUNCATE  darwin2.specimens_stable_ids;
	TRUNCATE  darwin2.tags;
	TRUNCATE  darwin2.codes;
	TRUNCATE  darwin2.gtu;
	TRUNCATE  darwin2.people;
	truncate darwin2.users_tracking;

	INSERT INTO darwin2.src_mv_specimen_public SELECT * FROM fdw_113.mv_specimen_public;
	INSERT INTO darwin2.src_taxonomy SELECT * FROM fdw_113.taxonomy;
--REFRESH materialized view darwin2.collections ;
	INSERT INTO  darwin2.collections SELECT *  FROM darwin2.v_mv_collections;
	--REFRESH materialized view darwin2.ext_links ;
	INSERT INTO darwin2.ext_links SELECT *  FROM darwin2.v_mv_ext_links ;
	--REFRESH materialized view darwin2.flat_dict ;
	INSERT INTO darwin2.flat_dict SELECT *  FROM darwin2.v_mv_flat_dict ;
	--REFRESH materialized view darwin2.identifications ;
	INSERT INTO darwin2.identifications SELECT *  FROM darwin2.v_mv_identifications ;
	
	REFRESH materialized view darwin2.mv_collections_full_path_recursive ;
	REFRESH materialized view darwin2.mv_collections_full_path_recursive_public ;
	--REFRESH materialized view darwin2.people ;
	INSERT INTO darwin2.people SELECT * FROM darwin2.v_mv_people;
	REFRESH MATERIALIZED VIEW darwin2.mv_taxa_in_specimens;
	--REFRESH materialized view darwin2.taxonomy ;
	INSERT INTO darwin2.taxonomy SELECT * FROM darwin2.v_mv_taxonomy;
	--REFRESH materialized view darwin2.specimens ;
	INSERT INTO darwin2.specimens SELECT * FROM darwin2.v_mv_specimens;
	REFRESH materialized view darwin2.mv_specimen_public ;
	--REFRESH materialized view darwin2.specimens_stable_ids ;
	INSERT INTO darwin2.specimens_stable_ids SELECT * FROM darwin2.v_mv_specimens_stable_ids;
	--REFRESH materialized view darwin2.tags ;
	INSERT INTO darwin2.tags SELECT * FROM darwin2.v_mv_tags;
	--REFRESH materialized view darwin2.codes ;
	INSERT INTO darwin2.codes SELECT * FROM darwin2.v_mv_codes;
	--REFRESH materialized view darwin2.gtu ;
	INSERT INTO darwin2.gtu SELECT * FROM darwin2.v_mv_gtu;
	--REFRESH materialized view darwin2.catalogue_people ;
	INSERT INTO darwin2.people SELECT * FROM darwin2.v_mv_people;
	
	--REFRESH materialized view darwin2.v_rdf_view  ;
	REFRESH materialized view darwin2.mv_specimen_public ;
	REFRESH MATERIALIZED VIEW darwin2.mv_search_public_specimen;
	
	refresh materialized view darwin2.mv_taxonomy_by_collection;
	truncate darwin2.tmv_collections_full_path_recursive_public;
	truncate darwin2.tmv_search_public_specimen;
	truncate darwin2.tmv_specimen_public;
	truncate darwin2.tmv_taxonomy_by_collection;
	INSERT INTO  darwin2.tmv_collections_full_path_recursive_public select * from darwin2.mv_collections_full_path_recursive_public;
	INSERT INTO  darwin2.tmv_search_public_specimen select * from darwin2.tmv_search_public_specimen;
	INSERT INTO  darwin2.tmv_specimen_public select * from darwin2.mv_specimen_public;
	INSERT INTO  darwin2.tmv_taxonomy_by_collection select * from darwin2.mv_taxonomy_by_collection;
	insert into darwin2.users_tracking select * from darwin2.v_users_tracking_public_specimens;

return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables() OWNER TO postgres;

--
-- TOC entry 1085 (class 1255 OID 467104461)
-- Name: fct_rmca_refresh_materialized_view_and_consult_tables_after_rep(); Type: FUNCTION; Schema: darwin2; Owner: postgres
--

CREATE FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	
truncate darwin2.taxonomy;
REFRESH MATERIALIZED VIEW darwin2.mv_collections_full_path_recursive_public;
INSERT into darwin2.taxonomy select distinct * from darwin2.v_mv_taxonomy;
REFRESH MATERIALIZED VIEW darwin2.mv_taxa_in_specimens;
REFRESH MATERIALIZED VIEW darwin2.mv_taxonomy_by_collection;
REFRESH MATERIALIZED VIEW darwin2.mv_search_public_specimen;
REFRESH MATERIALIZED VIEW darwin2.mv_specimen_public;
return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() OWNER TO postgres;

--
-- TOC entry 1077 (class 1255 OID 467103081)
-- Name: fct_rmca_sort_taxon_get_parent_level_text(integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_sort_taxon_get_parent_level_text(id_taxon integer, id_level integer) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
 returned varchar;
 arr varchar[];
 path_elem varchar;
 tmp_level int;
 tmp_name varchar;
BEGIN
	returned:=NULL;
	arr:= regexp_split_to_array((SELECt path FROM darwin2.taxonomy where id=id_taxon),'/');
	--added if lower taxon known is family
	SELECT level_ref,name INTO tmp_level,tmp_name FROM darwin2.taxonomy WHERE id= COALESCE(NULLIF(id_taxon::text,''),'-1')::int;
	IF tmp_level=id_level THEN
			RETURN tmp_name;
	END if;
	--end added
	FOR path_elem IN SELECT unnest(arr)
	LOOP
		SELECT level_ref,name INTO tmp_level,tmp_name FROM darwin2.taxonomy WHERE id= COALESCE(NULLIF(path_elem,''),'-1')::int;
		IF tmp_level=id_level THEN
			RETURN tmp_name;
		END if;
	END LOOP;

	return returned;
END;

$$;


ALTER FUNCTION darwin2.fct_rmca_sort_taxon_get_parent_level_text(id_taxon integer, id_level integer) OWNER TO darwin2;

--
-- TOC entry 332 (class 1255 OID 467261014)
-- Name: fct_rmca_taxo_get_children(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_children(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with
 c as (select id	   
	   from taxonomy  where
	   (path like '%/'||$1::varchar||'/%'
	   or id=$1)
	  
	  
 ) 
 select distinct id from c
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_children(record_id integer) OWNER TO darwin2;

--
-- TOC entry 333 (class 1255 OID 467261015)
-- Name: fct_rmca_taxo_get_direct_children(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_direct_children(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with
 c as (select id	   
	   from taxonomy  where
	   (path like '%/'||$1::varchar||'/'
	   or id=$1)
	  
	  
 ) 
 select distinct id from c
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_direct_children(record_id integer) OWNER TO darwin2;

--
-- TOC entry 334 (class 1255 OID 467261016)
-- Name: fct_rmca_taxo_get_syno(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with a as 
(
select group_id, record_id from classification_synonymies
where referenced_relation='taxonomy'
and record_id=$1
),
b as 
(select classification_synonymies.record_id from classification_synonymies
 inner join a on classification_synonymies.group_id=a.group_id
 where referenced_relation='taxonomy'
 union select $1
 )

 
select distinct record_id FROM b
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno(record_id integer) OWNER TO darwin2;

--
-- TOC entry 1044 (class 1255 OID 467261017)
-- Name: fct_rmca_taxo_get_syno_children(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno_children(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
 

with a  as 
(
select id, path from taxonomy where id=$1

),
b as (
select taxonomy.id from taxonomy inner join a on taxonomy.path like a.path||a.id::varchar||'/%'
	union 
	select id from a
),
c as (
select record_id, group_id from b inner join classification_synonymies on b.id= record_id where
	referenced_relation='taxonomy'

),
d as (
select distinct d.record_id from classification_synonymies d inner JOIN c ON d.group_id=c.group_id where
	referenced_relation='taxonomy'
),
e as (
select distinct record_id  from  (

select id record_id from b
	union 
	select record_id from c
	union select record_id from d
)e_tmp
) 


select record_id from e
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno_children(record_id integer) OWNER TO darwin2;

--
-- TOC entry 1043 (class 1255 OID 467262424)
-- Name: fct_rmca_taxo_get_syno_children_public(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno_children_public(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with 

a_init as (

select id from src_taxonomy where path||'/'||id::varchar||'/' LIKE '%'||record_id::varchar||'%' 
),
a as 
(
select group_id, record_id from classification_synonymies
	inner join a_init on record_id=a_init.id
where referenced_relation='taxonomy'

),
b as 
(select classification_synonymies.record_id from classification_synonymies
 inner join a on classification_synonymies.group_id=a.group_id
 where referenced_relation='taxonomy'
 union select $1
 ),
 c as (select id	   
	   from src_taxonomy ,
	   b where
	   (path||'/'||id::varchar||'/' like '%/'||b.record_id::varchar||'/%'
	   or id=$1)
	  
	  
 ),d
 as (
 select group_id, record_id from classification_synonymies , c
where referenced_relation='taxonomy'
and record_id=c.id
 ),
 e as
 (
 select classification_synonymies.record_id from classification_synonymies
 inner join d on classification_synonymies.group_id=d.group_id
 where referenced_relation='taxonomy'

 ),
f as
(select id, unnest(string_to_array(path, '/')) as tmp_parent from taxonomy)
, g
as

 (select distinct f.id	   
	   from f inner join
	   e on
	   tmp_parent::int=record_id
  and tmp_parent!=''
	   )
	   

 
 select distinct id from (select distinct id from c
 union select distinct id from g) h
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno_children_public(record_id integer) OWNER TO darwin2;

--
-- TOC entry 1045 (class 1255 OID 467262426)
-- Name: fct_rmca_taxo_get_syno_children_public_batch(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno_children_public_batch(param character varying) RETURNS TABLE(taxon_ref integer)
    LANGUAGE sql
    AS $_$

with  a as(
select string_to_array($1,'|') as x
),
b as
(
select replace(unnest(x),'/','') as rec from a)

select  fct_rmca_taxo_get_syno_children(rec::int) from  b
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno_children_public_batch(param character varying) OWNER TO darwin2;

--
-- TOC entry 335 (class 1255 OID 467261018)
-- Name: fct_rmca_taxo_get_syno_direct_children(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno_direct_children(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with a as 
(
select group_id, record_id from classification_synonymies
where referenced_relation='taxonomy'
and record_id=$1
),
b as 
(select classification_synonymies.record_id from classification_synonymies
 inner join a on classification_synonymies.group_id=a.group_id
 where referenced_relation='taxonomy'
 union select $1
 ),
 c as (select id	   
	   from taxonomy ,
	   b where
	   (path||'/'||id::varchar||'/' like '%/'||b.record_id::varchar||'/'
	   or id=$1)
	  
	  
 ),d
 as (
 select group_id, record_id from classification_synonymies , c
where referenced_relation='taxonomy'
and record_id=c.id
 ),
 e as
 (
 select classification_synonymies.record_id from classification_synonymies
 inner join d on classification_synonymies.group_id=d.group_id
 where referenced_relation='taxonomy'

 )

 
 select distinct id from (select distinct id from c
 union select distinct record_id from e) h
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno_direct_children(record_id integer) OWNER TO darwin2;

--
-- TOC entry 316 (class 1255 OID 467262423)
-- Name: fct_rmca_taxo_get_syno_no_children(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fct_rmca_taxo_get_syno_no_children(record_id integer) RETURNS TABLE(record_id integer)
    LANGUAGE sql
    AS $_$
with a as 
(
select group_id, record_id from classification_synonymies
where referenced_relation='taxonomy'
and record_id=$1
),
b as 
(select classification_synonymies.record_id from classification_synonymies
 inner join a on classification_synonymies.group_id=a.group_id
 where referenced_relation='taxonomy'
 union select $1
 ),
 c as (select id	   
	   from taxonomy ,
	   b where
	   (path||'/'||id::varchar||'/' like '%/'||b.record_id::varchar||'/%'
	   or id=$1)
	  
	  
 ),d
 as (
 select group_id, record_id from classification_synonymies , c
where referenced_relation='taxonomy'
and record_id=c.id
 ),
 e as
 (
 select classification_synonymies.record_id from classification_synonymies
 inner join d on classification_synonymies.group_id=d.group_id
 where referenced_relation='taxonomy'

 ),
f as
(select id, unnest(string_to_array(path, '/')) as tmp_parent from taxonomy)
, g
as

 (select distinct f.id	   
	   from f inner join
	   e on
	   tmp_parent::int=record_id
  and tmp_parent!=''
	   )
	   

 
 select distinct id from (select distinct id from c
 union select distinct id from g) h
 
$_$;


ALTER FUNCTION darwin2.fct_rmca_taxo_get_syno_no_children(record_id integer) OWNER TO darwin2;

--
-- TOC entry 1079 (class 1255 OID 467103082)
-- Name: fulltoindex(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fulltoindex(to_indexed character varying) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
BEGIN
   return fulltoindex(to_indexed, false);
END;
$$;


ALTER FUNCTION darwin2.fulltoindex(to_indexed character varying) OWNER TO darwin2;

--
-- TOC entry 1080 (class 1255 OID 467103083)
-- Name: fulltoindex(character varying, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.fulltoindex(to_indexed character varying, keep_space boolean) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
DECLARE
    temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := to_indexed;
    temp_string := translate(temp_string, 'âãäåāăąÁÂÃÄÅĀĂĄ', 'aaaaaaaaaaaaaaa');
    temp_string := translate(temp_string, 'èééêëēĕėęěĒĔĖĘĚ', 'eeeeeeeeeeeeeee');
    temp_string := translate(temp_string, 'ìíîïìĩīĭÌÍÎÏÌĨĪĬ', 'iiiiiiiiiiiiiiii');
    temp_string := translate(temp_string, 'óôõöōŏőÒÓÔÕÖŌŎŐ', 'ooooooooooooooo');
    temp_string := translate(temp_string, 'ùúûüũūŭůÙÚÛÜŨŪŬŮ', 'uuuuuuuuuuuuuuuu');
    temp_string := REPLACE(temp_string, 'Œ', 'oe');
    temp_string := REPLACE(temp_string, 'Ӕ', 'ae');
    temp_string := REPLACE(temp_string, 'œ', 'oe');
    temp_string := REPLACE(temp_string, 'æ', 'ae');
    temp_string := REPLACE(temp_string, 'ë', 'e');
    temp_string := REPLACE(temp_string, 'ï', 'i');
    temp_string := REPLACE(temp_string, 'ö', 'o');
    temp_string := REPLACE(temp_string, 'ü', 'u');
--     temp_string := REPLACE(temp_string, E'\'', '');
--     temp_string := REPLACE(temp_string, '"', '');
    temp_string := REPLACE(temp_string, 'ñ', 'n');
    temp_string := REPLACE(temp_string,chr(946),'b');
    temp_string := TRANSLATE(temp_string,'Ð','d');
    temp_string := TRANSLATE(temp_string,'ó','o');
    temp_string := TRANSLATE(temp_string,'ę','e');
    temp_string := TRANSLATE(temp_string,'ā','a');
    temp_string := TRANSLATE(temp_string,'ē','e');
    temp_string := TRANSLATE(temp_string,'ī','i');
    temp_string := TRANSLATE(temp_string,'ō','o');
    temp_string := TRANSLATE(temp_string,'ū','u');
    temp_string := TRANSLATE(temp_string,'ş','s');
    temp_string := TRANSLATE(temp_string,'Ş','s');
--     temp_string := TRANSLATE(temp_string,'†','');
--     temp_string := TRANSLATE(temp_string,chr(52914),'');
--ftheeten 2015 02 15

--ftheeten 2017 01 22
temp_string := TRANSLATE(temp_string,'-',' ');
temp_string := TRANSLATE(temp_string,'''',' ');
    -- FROM 160 to 255 ASCII
    temp_string := TRANSLATE(temp_string, ' ¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ',
      '  cL YS sCa  -R     Zu .z   EeY?AAAAAAACEEEEIIII NOOOOOxOUUUUYTBaaaaaaaceeeeiiii nooooo/ouuuuyty');
    --Remove ALL none alphanumerical char
    if keep_space= false then
    temp_string := lower(regexp_replace(temp_string,'[^[:alnum:]]','', 'g'));
    else
        temp_string := lower(regexp_replace(temp_string,'[^[:alnum:]\s]','', 'g'));
	temp_string :=regexp_replace(temp_string,'(\s{2,})',' ', 'g');
	
    end if;
    return temp_string;
END;
$$;


ALTER FUNCTION darwin2.fulltoindex(to_indexed character varying, keep_space boolean) OWNER TO darwin2;

--
-- TOC entry 1046 (class 1255 OID 467103084)
-- Name: getspecificparentforlevel(character varying, character varying, character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION darwin2.getspecificparentforlevel(referenced_relation character varying, path character varying, level_searched character varying) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
  response template_classifications.name%TYPE := '';
BEGIN
IF referenced_relation IS NOT NULL AND level_searched IS NOT NULL AND path IS NOT NULL THEN
  EXECUTE
  'SELECT name ' ||
  ' FROM '
  || quote_ident(lower(referenced_relation)) || ' cat '
  ' INNER JOIN catalogue_levels ON cat.level_ref = catalogue_levels.id '
  ' WHERE level_name = '
  || quote_literal(lower(level_searched)) ||
  '   AND cat.id IN (SELECT i_id::integer FROM regexp_split_to_table(' || quote_literal(path) || E', E''\/'') as i_id WHERE i_id != '''')'
  INTO response;
  RETURN response;
ELSE
	RETURN NULL;
END IF;
EXCEPTION
  WHEN OTHERS THEN
    RAISE WARNING 'Error in getSpecificParentForLevel: %', SQLERRM;
    RETURN response;

END;
$$;


ALTER FUNCTION darwin2.getspecificparentforlevel(referenced_relation character varying, path character varying, level_searched character varying) OWNER TO darwin2;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 212 (class 1259 OID 467103085)
-- Name: catalogue_levels; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.catalogue_levels (
    id integer,
    level_type character varying,
    level_name character varying,
    level_sys_name character varying,
    optional_level boolean,
    level_order integer
);


ALTER TABLE darwin2.catalogue_levels OWNER TO darwin2;

--
-- TOC entry 213 (class 1259 OID 467103091)
-- Name: catalogue_people; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.catalogue_people (
    referenced_relation character varying,
    record_id integer,
    id integer,
    people_type character varying,
    people_sub_type character varying,
    order_by integer,
    people_ref integer
);


ALTER TABLE darwin2.catalogue_people OWNER TO darwin2;

--
-- TOC entry 254 (class 1259 OID 467260984)
-- Name: template_table_record_ref; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.template_table_record_ref (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL
);


ALTER TABLE darwin2.template_table_record_ref OWNER TO darwin2;

--
-- TOC entry 4284 (class 0 OID 0)
-- Dependencies: 254
-- Name: TABLE template_table_record_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE darwin2.template_table_record_ref IS 'Template called to add referenced_relation and record_id fields';


--
-- TOC entry 4285 (class 0 OID 0)
-- Dependencies: 254
-- Name: COLUMN template_table_record_ref.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.template_table_record_ref.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 4286 (class 0 OID 0)
-- Dependencies: 254
-- Name: COLUMN template_table_record_ref.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.template_table_record_ref.record_id IS 'Id of record concerned';


--
-- TOC entry 256 (class 1259 OID 467260993)
-- Name: classification_synonymies; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.classification_synonymies (
    id integer NOT NULL,
    group_id integer NOT NULL,
    group_name character varying NOT NULL,
    is_basionym boolean DEFAULT false,
    order_by integer DEFAULT 0 NOT NULL,
    synonym_record_id integer,
    original_synonym boolean,
    import_ref integer,
    syn_date timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone NOT NULL,
    syn_date_mask integer DEFAULT 0 NOT NULL
)
INHERITS (darwin2.template_table_record_ref);


ALTER TABLE darwin2.classification_synonymies OWNER TO darwin2;

--
-- TOC entry 4288 (class 0 OID 0)
-- Dependencies: 256
-- Name: TABLE classification_synonymies; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE darwin2.classification_synonymies IS 'Table containing classification synonymies';


--
-- TOC entry 4289 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.referenced_relation IS 'Classification table concerned';


--
-- TOC entry 4290 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.record_id IS 'Id of record placed in group as a synonym';


--
-- TOC entry 4291 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.group_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.group_id IS 'Id given to group';


--
-- TOC entry 4292 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.group_name IS 'Name of group under which synonyms are placed';


--
-- TOC entry 4293 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.is_basionym; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.is_basionym IS 'If record is a basionym';


--
-- TOC entry 4294 (class 0 OID 0)
-- Dependencies: 256
-- Name: COLUMN classification_synonymies.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.order_by IS 'Order by used to qualify order amongst synonyms - used mainly for senio and junior synonyms';


--
-- TOC entry 255 (class 1259 OID 467260991)
-- Name: classification_synonymies_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE darwin2.classification_synonymies_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.classification_synonymies_id_seq OWNER TO darwin2;

--
-- TOC entry 4296 (class 0 OID 0)
-- Dependencies: 255
-- Name: classification_synonymies_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE darwin2.classification_synonymies_id_seq OWNED BY darwin2.classification_synonymies.id;


--
-- TOC entry 214 (class 1259 OID 467103097)
-- Name: codes; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.codes (
    referenced_relation character varying,
    record_id integer,
    id integer,
    code_category character varying,
    code_prefix character varying,
    code_prefix_separator character varying,
    code character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    full_code_indexed character varying,
    code_date timestamp without time zone,
    code_date_mask integer,
    code_num integer,
    code_num_bigint bigint,
    code_display text
);


ALTER TABLE darwin2.codes OWNER TO darwin2;

--
-- TOC entry 215 (class 1259 OID 467103103)
-- Name: collections; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.collections (
    id integer,
    collection_type character varying,
    code character varying,
    name character varying,
    name_indexed character varying,
    institution_ref integer,
    main_manager_ref integer,
    staff_ref integer,
    parent_ref integer,
    path character varying,
    code_auto_increment boolean,
    code_last_value integer,
    code_prefix character varying,
    code_prefix_separator character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    code_specimen_duplicate boolean,
    is_public boolean,
    code_mask character varying,
    loan_auto_increment boolean,
    loan_last_value integer,
    code_ai_inherit boolean,
    code_auto_increment_for_insert_only boolean,
    nagoya character varying,
    allow_duplicates boolean,
    code_full_path character varying,
    name_full_path character varying,
    name_indexed_full_path character varying
);


ALTER TABLE darwin2.collections OWNER TO darwin2;

--
-- TOC entry 216 (class 1259 OID 467103109)
-- Name: country_cleaning; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.country_cleaning (
    original_name character varying NOT NULL,
    replacement_value character varying
);


ALTER TABLE darwin2.country_cleaning OWNER TO darwin2;

--
-- TOC entry 217 (class 1259 OID 467103115)
-- Name: ext_links; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.ext_links (
    referenced_relation character varying,
    record_id integer,
    id integer,
    url character varying,
    comment text,
    comment_indexed text,
    category character varying,
    contributor character varying,
    disclaimer character varying,
    license character varying,
    display_order integer
);


ALTER TABLE darwin2.ext_links OWNER TO darwin2;

--
-- TOC entry 218 (class 1259 OID 467103124)
-- Name: flat_dict; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.flat_dict (
    id integer,
    referenced_relation character varying,
    dict_field character varying,
    dict_value character varying,
    dict_depend character varying
);


ALTER TABLE darwin2.flat_dict OWNER TO darwin2;

--
-- TOC entry 219 (class 1259 OID 467103130)
-- Name: gtu; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.gtu (
    id integer,
    code character varying,
    gtu_from_date_mask integer,
    gtu_from_date timestamp without time zone,
    gtu_to_date_mask integer,
    gtu_to_date timestamp without time zone,
    tag_values_indexed character varying[],
    latitude double precision,
    longitude double precision,
    lat_long_accuracy double precision,
    location point,
    elevation double precision,
    elevation_accuracy double precision,
    latitude_dms_degree integer,
    latitude_dms_minutes double precision,
    latitude_dms_seconds double precision,
    latitude_dms_direction integer,
    longitude_dms_degree integer,
    longitude_dms_minutes double precision,
    longitude_dms_seconds double precision,
    longitude_dms_direction integer,
    latitude_utm double precision,
    longitude_utm double precision,
    utm_zone character varying,
    coordinates_source character varying,
    elevation_unit character varying(4),
    gtu_creation_date timestamp without time zone,
    import_ref integer,
    iso3166 character varying,
    iso3166_subdivision character varying,
    wkt_str character varying,
    nagoya character varying,
    geom public.geometry
);


ALTER TABLE darwin2.gtu OWNER TO darwin2;

--
-- TOC entry 220 (class 1259 OID 467103136)
-- Name: identifications; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.identifications (
    referenced_relation character varying,
    record_id integer,
    id integer,
    notion_concerned character varying,
    notion_date timestamp without time zone,
    notion_date_mask integer,
    value_defined character varying,
    value_defined_indexed character varying,
    determination_status character varying,
    order_by integer
);


ALTER TABLE darwin2.identifications OWNER TO darwin2;

--
-- TOC entry 221 (class 1259 OID 467103142)
-- Name: v_collections_full_path_recursive; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_collections_full_path_recursive AS
 WITH RECURSIVE collections_path_recursive AS (
         SELECT collections.id,
            collections.collection_type,
            collections.code,
            collections.name,
            collections.name_indexed,
            collections.institution_ref,
            collections.main_manager_ref,
            collections.staff_ref,
            collections.parent_ref,
            collections.path,
            collections.code_auto_increment,
            collections.code_last_value,
            collections.code_prefix,
            collections.code_prefix_separator,
            collections.code_suffix,
            collections.code_suffix_separator,
            collections.code_specimen_duplicate,
            collections.is_public,
            collections.code_mask,
            collections.loan_auto_increment,
            collections.loan_last_value,
            collections.code_ai_inherit,
            collections.code_auto_increment_for_insert_only,
            collections.nagoya,
            collections.allow_duplicates,
            collections.code AS code_full_path,
            collections.name AS name_full_path,
            collections.name_indexed AS name_indexed_full_path
           FROM darwin2.collections
          WHERE (collections.parent_ref IS NULL)
        UNION ALL
         SELECT collections.id,
            collections.collection_type,
            collections.code,
            collections.name,
            collections.name_indexed,
            collections.institution_ref,
            collections.main_manager_ref,
            collections.staff_ref,
            collections.parent_ref,
            collections.path,
            collections.code_auto_increment,
            collections.code_last_value,
            collections.code_prefix,
            collections.code_prefix_separator,
            collections.code_suffix,
            collections.code_suffix_separator,
            collections.code_specimen_duplicate,
            collections.is_public,
            collections.code_mask,
            collections.loan_auto_increment,
            collections.loan_last_value,
            collections.code_ai_inherit,
            collections.code_auto_increment_for_insert_only,
            collections.nagoya,
            collections.allow_duplicates,
            (((collections_path_recursive_1.code_full_path)::text || '/'::text) || (collections.code)::text),
            (((collections_path_recursive_1.name_full_path)::text || '/'::text) || (collections.name)::text),
            (((collections_path_recursive_1.name_indexed_full_path)::text || '/'::text) || (collections.name_indexed)::text)
           FROM (darwin2.collections
             JOIN collections_path_recursive collections_path_recursive_1 ON ((collections.parent_ref = collections_path_recursive_1.id)))
        )
 SELECT collections_path_recursive.id,
    collections_path_recursive.collection_type,
    collections_path_recursive.code,
    collections_path_recursive.name,
    collections_path_recursive.name_indexed,
    collections_path_recursive.institution_ref,
    collections_path_recursive.main_manager_ref,
    collections_path_recursive.staff_ref,
    collections_path_recursive.parent_ref,
    collections_path_recursive.path,
    collections_path_recursive.code_auto_increment,
    collections_path_recursive.code_last_value,
    collections_path_recursive.code_prefix,
    collections_path_recursive.code_prefix_separator,
    collections_path_recursive.code_suffix,
    collections_path_recursive.code_suffix_separator,
    collections_path_recursive.code_specimen_duplicate,
    collections_path_recursive.is_public,
    collections_path_recursive.code_mask,
    collections_path_recursive.loan_auto_increment,
    collections_path_recursive.loan_last_value,
    collections_path_recursive.code_ai_inherit,
    collections_path_recursive.code_auto_increment_for_insert_only,
    collections_path_recursive.nagoya,
    collections_path_recursive.allow_duplicates,
    ((('/'::text || (collections_path_recursive.code_full_path)::text) || '/'::text))::character varying AS code_full_path,
    ((('/'::text || (collections_path_recursive.name_full_path)::text) || '/'::text))::character varying AS name_full_path,
    ((('/'::text || (collections_path_recursive.name_indexed_full_path)::text) || '/'::text))::character varying AS name_indexed_full_path
   FROM collections_path_recursive;


ALTER TABLE darwin2.v_collections_full_path_recursive OWNER TO darwin2;

--
-- TOC entry 222 (class 1259 OID 467103147)
-- Name: mv_collections_full_path_recursive; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_collections_full_path_recursive AS
 SELECT v_collections_full_path_recursive.id,
    v_collections_full_path_recursive.collection_type,
    v_collections_full_path_recursive.code,
    v_collections_full_path_recursive.name,
    v_collections_full_path_recursive.name_indexed,
    v_collections_full_path_recursive.institution_ref,
    v_collections_full_path_recursive.main_manager_ref,
    v_collections_full_path_recursive.staff_ref,
    v_collections_full_path_recursive.parent_ref,
    v_collections_full_path_recursive.path,
    v_collections_full_path_recursive.code_auto_increment,
    v_collections_full_path_recursive.code_last_value,
    v_collections_full_path_recursive.code_prefix,
    v_collections_full_path_recursive.code_prefix_separator,
    v_collections_full_path_recursive.code_suffix,
    v_collections_full_path_recursive.code_suffix_separator,
    v_collections_full_path_recursive.code_specimen_duplicate,
    v_collections_full_path_recursive.is_public,
    v_collections_full_path_recursive.code_mask,
    v_collections_full_path_recursive.loan_auto_increment,
    v_collections_full_path_recursive.loan_last_value,
    v_collections_full_path_recursive.code_ai_inherit,
    v_collections_full_path_recursive.code_auto_increment_for_insert_only,
    v_collections_full_path_recursive.nagoya,
    v_collections_full_path_recursive.allow_duplicates,
    v_collections_full_path_recursive.code_full_path,
    v_collections_full_path_recursive.name_full_path,
    v_collections_full_path_recursive.name_indexed_full_path
   FROM darwin2.v_collections_full_path_recursive
  WITH NO DATA;


ALTER TABLE darwin2.mv_collections_full_path_recursive OWNER TO darwin2;

--
-- TOC entry 223 (class 1259 OID 467103155)
-- Name: mv_collections_full_path_recursive_public; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_collections_full_path_recursive_public AS
 WITH a AS (
         SELECT v_collections_full_path_recursive.id,
            v_collections_full_path_recursive.collection_type,
            v_collections_full_path_recursive.code,
            v_collections_full_path_recursive.name,
            v_collections_full_path_recursive.name_indexed,
            v_collections_full_path_recursive.institution_ref,
            v_collections_full_path_recursive.main_manager_ref,
            v_collections_full_path_recursive.staff_ref,
            v_collections_full_path_recursive.parent_ref,
            v_collections_full_path_recursive.path,
            v_collections_full_path_recursive.code_auto_increment,
            v_collections_full_path_recursive.code_last_value,
            v_collections_full_path_recursive.code_prefix,
            v_collections_full_path_recursive.code_prefix_separator,
            v_collections_full_path_recursive.code_suffix,
            v_collections_full_path_recursive.code_suffix_separator,
            v_collections_full_path_recursive.code_specimen_duplicate,
            v_collections_full_path_recursive.is_public,
            v_collections_full_path_recursive.code_mask,
            v_collections_full_path_recursive.loan_auto_increment,
            v_collections_full_path_recursive.loan_last_value,
            v_collections_full_path_recursive.code_ai_inherit,
            v_collections_full_path_recursive.code_auto_increment_for_insert_only,
            v_collections_full_path_recursive.nagoya,
            v_collections_full_path_recursive.allow_duplicates,
            v_collections_full_path_recursive.code_full_path,
            v_collections_full_path_recursive.name_full_path,
            v_collections_full_path_recursive.name_indexed_full_path
           FROM darwin2.v_collections_full_path_recursive
          WHERE (v_collections_full_path_recursive.is_public = true)
        )
 SELECT a.id,
    a.collection_type,
    a.code,
    a.name,
    a.name_indexed,
    a.institution_ref,
    a.main_manager_ref,
    a.staff_ref,
    a.parent_ref,
    a.path,
    a.code_auto_increment,
    a.code_last_value,
    a.code_prefix,
    a.code_prefix_separator,
    a.code_suffix,
    a.code_suffix_separator,
    a.code_specimen_duplicate,
    a.is_public,
    a.code_mask,
    a.loan_auto_increment,
    a.loan_last_value,
    a.code_ai_inherit,
    a.code_auto_increment_for_insert_only,
    a.nagoya,
    a.allow_duplicates,
    a.code_full_path,
    a.name_full_path,
    a.name_indexed_full_path
   FROM a
UNION
 SELECT v_collections_full_path_recursive.id,
    v_collections_full_path_recursive.collection_type,
    v_collections_full_path_recursive.code,
    v_collections_full_path_recursive.name,
    v_collections_full_path_recursive.name_indexed,
    v_collections_full_path_recursive.institution_ref,
    v_collections_full_path_recursive.main_manager_ref,
    v_collections_full_path_recursive.staff_ref,
    v_collections_full_path_recursive.parent_ref,
    v_collections_full_path_recursive.path,
    v_collections_full_path_recursive.code_auto_increment,
    v_collections_full_path_recursive.code_last_value,
    v_collections_full_path_recursive.code_prefix,
    v_collections_full_path_recursive.code_prefix_separator,
    v_collections_full_path_recursive.code_suffix,
    v_collections_full_path_recursive.code_suffix_separator,
    v_collections_full_path_recursive.code_specimen_duplicate,
    v_collections_full_path_recursive.is_public,
    v_collections_full_path_recursive.code_mask,
    v_collections_full_path_recursive.loan_auto_increment,
    v_collections_full_path_recursive.loan_last_value,
    v_collections_full_path_recursive.code_ai_inherit,
    v_collections_full_path_recursive.code_auto_increment_for_insert_only,
    v_collections_full_path_recursive.nagoya,
    v_collections_full_path_recursive.allow_duplicates,
    v_collections_full_path_recursive.code_full_path,
    v_collections_full_path_recursive.name_full_path,
    v_collections_full_path_recursive.name_indexed_full_path
   FROM darwin2.v_collections_full_path_recursive
  WHERE ((v_collections_full_path_recursive.is_public = false) AND (EXISTS ( SELECT a.id
           FROM a
          WHERE ((a.path)::text ~~ (('%'::text || ((v_collections_full_path_recursive.id)::character varying)::text) || '%'::text)))))
  WITH NO DATA;


ALTER TABLE darwin2.mv_collections_full_path_recursive_public OWNER TO darwin2;

--
-- TOC entry 228 (class 1259 OID 467103218)
-- Name: people; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.people (
    is_physical boolean,
    sub_type character varying,
    formated_name character varying,
    formated_name_indexed character varying,
    formated_name_unique character varying,
    title character varying,
    family_name character varying,
    given_name character varying,
    additional_names character varying,
    birth_date_mask integer,
    birth_date date,
    gender character(1),
    id integer,
    end_date_mask integer,
    end_date date,
    activity_date_from_mask integer,
    activity_date_from date,
    activity_date_to_mask integer,
    activity_date_to date,
    name_formated_indexed character varying,
    import_ref integer
);


ALTER TABLE darwin2.people OWNER TO darwin2;

--
-- TOC entry 224 (class 1259 OID 467103163)
-- Name: specimens; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.specimens (
    id integer,
    collection_ref integer,
    expedition_ref integer,
    gtu_ref integer,
    taxon_ref integer,
    litho_ref integer,
    chrono_ref integer,
    lithology_ref integer,
    mineral_ref integer,
    acquisition_category character varying,
    acquisition_date_mask integer,
    acquisition_date date,
    station_visible boolean,
    ig_ref integer,
    type character varying,
    type_group character varying,
    type_search character varying,
    sex character varying,
    stage character varying,
    state character varying,
    social_status character varying,
    rock_form character varying,
    room character varying,
    shelf character varying,
    specimen_count_min integer,
    specimen_count_max integer,
    spec_ident_ids integer[],
    spec_coll_ids integer[],
    spec_don_sel_ids integer[],
    collection_type character varying,
    collection_code character varying,
    collection_name character varying,
    collection_is_public boolean,
    collection_parent_ref integer,
    collection_path character varying,
    expedition_name character varying,
    expedition_name_indexed character varying,
    gtu_code character varying,
    gtu_from_date_mask integer,
    gtu_from_date timestamp without time zone,
    gtu_to_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_tag_values_indexed character varying[],
    gtu_country_tag_value character varying,
    gtu_country_tag_indexed character varying[],
    gtu_province_tag_value character varying,
    gtu_province_tag_indexed character varying[],
    gtu_others_tag_value character varying,
    gtu_others_tag_indexed character varying[],
    gtu_elevation double precision,
    gtu_elevation_accuracy double precision,
    gtu_location point,
    taxon_name character varying,
    taxon_name_indexed character varying,
    taxon_level_ref integer,
    taxon_level_name character varying,
    taxon_status character varying,
    taxon_path character varying,
    taxon_parent_ref integer,
    taxon_extinct boolean,
    litho_name character varying,
    litho_name_indexed character varying,
    litho_level_ref integer,
    litho_level_name character varying,
    litho_status character varying,
    litho_local boolean,
    litho_color character varying,
    litho_path character varying,
    litho_parent_ref integer,
    chrono_name character varying,
    chrono_name_indexed character varying,
    chrono_level_ref integer,
    chrono_level_name character varying,
    chrono_status character varying,
    chrono_local boolean,
    chrono_color character varying,
    chrono_path character varying,
    chrono_parent_ref integer,
    lithology_name character varying,
    lithology_name_indexed character varying,
    lithology_level_ref integer,
    lithology_level_name character varying,
    lithology_status character varying,
    lithology_local boolean,
    lithology_color character varying,
    lithology_path character varying,
    lithology_parent_ref integer,
    mineral_name character varying,
    mineral_name_indexed character varying,
    mineral_level_ref integer,
    mineral_level_name character varying,
    mineral_status character varying,
    mineral_local boolean,
    mineral_color character varying,
    mineral_path character varying,
    mineral_parent_ref integer,
    ig_num character varying,
    ig_num_indexed character varying,
    ig_date_mask integer,
    ig_date date,
    specimen_count_males_min integer,
    specimen_count_males_max integer,
    specimen_count_females_min integer,
    specimen_count_females_max integer,
    specimen_count_juveniles_min integer,
    specimen_count_juveniles_max integer,
    main_code_indexed character varying,
    category character varying,
    institution_ref integer,
    building character varying,
    floor character varying,
    "row" character varying,
    col character varying,
    container character varying,
    sub_container character varying,
    container_type character varying,
    sub_container_type character varying,
    container_storage character varying,
    sub_container_storage character varying,
    surnumerary boolean,
    object_name text,
    object_name_indexed text,
    specimen_status character varying,
    valid_label boolean,
    label_created_on character varying,
    label_created_by character varying,
    specimen_creation_date timestamp without time zone,
    import_ref integer,
    gtu_iso3166 character varying,
    gtu_iso3166_subdivision character varying,
    nagoya character varying,
    uuid uuid,
    collection_name_full_path character varying,
    geom public.geometry,
    family character varying
);


ALTER TABLE darwin2.specimens OWNER TO darwin2;

--
-- TOC entry 231 (class 1259 OID 467103236)
-- Name: tags; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.tags (
    gtu_ref integer,
    group_ref integer,
    group_type character varying,
    sub_group_type character varying,
    tag character varying,
    tag_indexed character varying
);


ALTER TABLE darwin2.tags OWNER TO darwin2;

--
-- TOC entry 225 (class 1259 OID 467103169)
-- Name: taxonomy; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.taxonomy (
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    id integer,
    extinct boolean,
    sensitive_info_withheld boolean,
    is_reference_taxonomy boolean,
    metadata_ref integer,
    taxonomy_creation_date timestamp without time zone,
    import_ref integer,
    cites boolean
);


ALTER TABLE darwin2.taxonomy OWNER TO darwin2;

--
-- TOC entry 252 (class 1259 OID 467211760)
-- Name: mv_gbif_ichtyo; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_gbif_ichtyo AS
 SELECT DISTINCT specimens.id,
    ('https://darwinweb.africamuseum.be/object/'::text || specimens.uuid) AS public_url,
    ('https://darwinweb.africamuseum.be/object/'::text || (specimens.uuid)::text) AS guid,
    specimens.collection_ref,
    'VER-FIS'::text AS collection_code,
    collections.name AS collection_name,
    'https://ror.org/001805t51'::text AS collection_id,
    collections.path AS collection_path,
    (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text) AS cataloguenumber,
    (string_agg(DISTINCT (aux_code.code)::text, ';'::text ORDER BY (aux_code.code)::text))::character varying AS specimen_code,
    'PreservedSpecimen'::text AS basisofrecord,
    'https://ror.org/001805t51'::text AS institutionid,
    NULL::text AS iso_country_institution,
    NULL::text AS bibliographic_citation,
    'TO_FILL'::text AS license,
    'CONTACT_PERSON'::text AS email,
    COALESCE(NULLIF(btrim((specimens.type)::text), 'specimen'::text), ''::text) AS type,
    specimens.taxon_path,
    specimens.taxon_ref,
    specimens.taxon_name,
    darwin2.fct_rmca_sort_taxon_get_parent_level_text(specimens.taxon_ref, 34) AS family,
    specimens.gtu_iso3166 AS iso_country,
    specimens.gtu_country_tag_value AS country,
    btrim(replace(replace((specimens.gtu_others_tag_value)::text, (specimens.gtu_country_tag_value)::text, ''::text), 'Africa'::text, ''::text), ';'::text) AS location,
    (specimens.gtu_location[1])::character varying AS latitude,
    (specimens.gtu_location[0])::character varying AS longitude,
    gtu.lat_long_accuracy,
    specimens.spec_coll_ids AS collector_ids,
    ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
           FROM darwin2.people
          WHERE (people.id = ANY (specimens.spec_coll_ids))) AS collectors,
    specimens.spec_don_sel_ids AS donator_ids,
    ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
           FROM darwin2.people
          WHERE (people.id = ANY (specimens.spec_don_sel_ids))) AS donators,
    specimens.spec_ident_ids AS identifiers_ids,
    ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
           FROM darwin2.people
          WHERE (people.id = ANY (specimens.spec_ident_ids))) AS identifiers,
    darwin2.fct_mask_date_ymd_gbif(specimens.gtu_from_date, specimens.gtu_from_date_mask) AS gtu_from_date,
    darwin2.fct_mask_date_ymd_gbif(specimens.gtu_to_date, specimens.gtu_to_date_mask) AS gtu_to_date,
    replace(replace((NULLIF(darwin2.fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask), 'xxxx-xx-xx'::text) || COALESCE(('-'::text || NULLIF(darwin2.fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask), 'xxxx-xx-xx'::text)), ''::text)), 'xxxx'::text, ''::text), '-xx'::text, ''::text) AS eventdate,
    unnest(
        CASE
            WHEN (specimens.gtu_country_tag_indexed IS NOT NULL) THEN specimens.gtu_country_tag_indexed
            ELSE NULL::character varying[]
        END) AS country_unnest,
    ext_links_thumbnails.url AS urls_thumbnails,
    ext_links_thumbnails.category AS image_category_thumbnails,
    ext_links_thumbnails.contributor AS contributor_thumbnails,
    ext_links_thumbnails.disclaimer AS disclaimer_thumbnails,
    ext_links_thumbnails.license AS license_thumbnails,
    ext_links_thumbnails.display_order AS display_order_thumbnails,
    ext_links_image_links.url AS urls_image_links,
    ext_links_image_links.category AS image_category_image_links,
    ext_links_image_links.contributor AS contributor_image_links,
    ext_links_image_links.disclaimer AS disclaimer_image_links,
    ext_links_image_links.license AS license_image_links,
    ext_links_image_links.display_order AS display_order_image_links,
    ext_links_3d_snippets.url AS urls_3d_snippets,
    ext_links_3d_snippets.category AS image_category_3d_snippets,
    ext_links_3d_snippets.contributor AS contributor_3d_snippets,
    ext_links_3d_snippets.disclaimer AS disclaimer_3d_snippets,
    ext_links_3d_snippets.license AS license_3d_snippets,
    ext_links_3d_snippets.display_order AS display_order_3d_snippets,
    darwin2.fct_mask_date_ymd_gbif(identifications.notion_date, identifications.notion_date_mask) AS identification_date,
    NULL::text AS history,
    specimens.gtu_ref,
    specimens.specimen_count_min,
    specimens.specimen_count_males_min,
    specimens.specimen_count_females_min,
    specimens.specimen_count_juveniles_min,
        CASE
            WHEN (specimens.specimen_count_juveniles_min > 0) THEN 'juveniles'::text
            ELSE ''::text
        END AS stage,
        CASE
            WHEN ((specimens.specimen_count_males_min > 0) AND (specimens.specimen_count_females_min > 0)) THEN 'male_and_female'::text
            WHEN (specimens.specimen_count_males_min > 0) THEN 'male'::text
            WHEN (specimens.specimen_count_females_min > 0) THEN 'female'::text
            ELSE 'no_data'::text
        END AS sex,
    (('{donators:'''::text || ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
           FROM darwin2.people
          WHERE (people.id = ANY (specimens.spec_don_sel_ids)))) || '''}'::text) AS additional_data,
    specimens.uuid,
    'RMCA'::text AS institution_code
   FROM ((((((((((darwin2.specimens
     LEFT JOIN darwin2.collections ON ((specimens.collection_ref = collections.id)))
     LEFT JOIN darwin2.codes ON ((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text) AND (specimens.id = codes.record_id))))
     LEFT JOIN darwin2.codes aux_code ON ((((aux_code.referenced_relation)::text = 'specimens'::text) AND (lower((aux_code.code_category)::text) = 'additional id'::text) AND (specimens.id = aux_code.record_id))))
     LEFT JOIN darwin2.ext_links ext_links_thumbnails ON (((specimens.id = ext_links_thumbnails.record_id) AND ((ext_links_thumbnails.referenced_relation)::text = 'specimens'::text) AND ((ext_links_thumbnails.category)::text = 'thumbnail'::text))))
     LEFT JOIN darwin2.ext_links ext_links_image_links ON (((specimens.id = ext_links_image_links.record_id) AND ((ext_links_image_links.referenced_relation)::text = 'specimens'::text) AND ((ext_links_image_links.category)::text = 'image_link'::text))))
     LEFT JOIN darwin2.ext_links ext_links_3d_snippets ON (((specimens.id = ext_links_3d_snippets.record_id) AND ((ext_links_3d_snippets.referenced_relation)::text = 'specimens'::text) AND ((ext_links_3d_snippets.category)::text = 'html_3d_snippet'::text))))
     LEFT JOIN darwin2.identifications ON ((((identifications.referenced_relation)::text = 'specimens'::text) AND (specimens.id = identifications.record_id) AND ((identifications.notion_concerned)::text = 'taxonomy'::text))))
     LEFT JOIN darwin2.tags ON ((specimens.gtu_ref = tags.gtu_ref)))
     LEFT JOIN darwin2.gtu ON ((specimens.gtu_ref = gtu.id)))
     LEFT JOIN darwin2.taxonomy ON ((specimens.taxon_ref = taxonomy.id)))
  WHERE ((specimens.collection_ref = 6) AND (NOT COALESCE(taxonomy.sensitive_info_withheld, false)))
  GROUP BY specimens.id, specimens.uuid, collections.id, collections.path, collections.name, collections.code, (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text), specimens.taxon_path, specimens.taxon_ref, specimens.collection_ref, specimens.gtu_country_tag_indexed, specimens.gtu_country_tag_value, specimens.gtu_iso3166, (specimens.gtu_location[0])::character varying, (specimens.gtu_location[1])::character varying, specimens.spec_ident_ids, specimens.gtu_others_tag_indexed, specimens.gtu_others_tag_value, specimens.taxon_name, ext_links_thumbnails.url, ext_links_thumbnails.category, ext_links_thumbnails.contributor, ext_links_thumbnails.disclaimer, ext_links_thumbnails.license, ext_links_thumbnails.display_order, ext_links_image_links.url, ext_links_image_links.category, ext_links_image_links.contributor, ext_links_image_links.disclaimer, ext_links_image_links.license, ext_links_image_links.display_order, ext_links_3d_snippets.url, ext_links_3d_snippets.category, ext_links_3d_snippets.contributor, ext_links_3d_snippets.disclaimer, ext_links_3d_snippets.license, ext_links_3d_snippets.display_order, identifications.notion_date, identifications.notion_date_mask, specimens.gtu_ref, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.type, specimens.spec_coll_ids, specimens.spec_don_sel_ids, codes.full_code_indexed, gtu.lat_long_accuracy, specimens.specimen_count_min, specimens.specimen_count_males_min, specimens.specimen_count_females_min, specimens.specimen_count_juveniles_min
  WITH NO DATA;


ALTER TABLE darwin2.mv_gbif_ichtyo OWNER TO darwin2;

--
-- TOC entry 253 (class 1259 OID 467221324)
-- Name: mv_gbif_ichtyo_history; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_gbif_ichtyo_history AS
 WITH a AS (
         SELECT DISTINCT specimens.id,
            ('https://darwinweb.africamuseum.be/object/'::text || specimens.uuid) AS public_url,
            specimens.uuid,
            ('https://darwinweb.africamuseum.be/object/'::text || (specimens.uuid)::text) AS guid,
            specimens.collection_ref,
            collections.code AS collection_code,
            collections.name AS collection_name,
            collections.id AS collection_id,
            collections.path AS collection_path,
            (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text) AS cataloguenumber,
            (string_agg(DISTINCT (aux_code.code)::text, ';'::text ORDER BY (aux_code.code)::text))::character varying AS specimen_code_number,
            'PreservedSpecimen'::text AS basisofrecord,
            'RMCA'::text AS institutionid,
            'BE-RMCA'::text AS iso_country_institution,
            'Please cite the source database appropriatly'::text AS bibliographic_citation,
            ''::text AS license,
            ''::text AS email,
            specimens.type,
            specimens.taxon_path,
            specimens.taxon_ref,
            specimens.taxon_name,
            darwin2.fct_rmca_sort_taxon_get_parent_level_text(specimens.taxon_ref, 34) AS family,
            specimens.gtu_iso3166 AS iso_country,
            specimens.gtu_country_tag_value AS country,
            btrim(replace(replace((specimens.gtu_others_tag_value)::text, (specimens.gtu_country_tag_value)::text, ''::text), 'Africa'::text, ''::text), ';'::text) AS location,
            (specimens.gtu_location[1])::character varying AS latitude,
            (specimens.gtu_location[0])::character varying AS longitude,
            gtu.lat_long_accuracy,
            specimens.spec_coll_ids AS collector_ids,
            ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
                   FROM darwin2.people
                  WHERE (people.id = ANY (specimens.spec_coll_ids))) AS collectors,
            specimens.spec_don_sel_ids AS donator_ids,
            ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
                   FROM darwin2.people
                  WHERE (people.id = ANY (specimens.spec_don_sel_ids))) AS donators,
            specimens.spec_ident_ids AS identifiers_ids,
            ( SELECT string_agg(DISTINCT (people.formated_name)::text, ', '::text ORDER BY (people.formated_name)::text) AS string_agg
                   FROM darwin2.people
                  WHERE (people.id = ANY (specimens.spec_ident_ids))) AS identifiers,
            darwin2.fct_mask_date_ymd_gbif(specimens.gtu_from_date, specimens.gtu_from_date_mask) AS gtu_from_date,
            darwin2.fct_mask_date_ymd_gbif(specimens.gtu_to_date, specimens.gtu_to_date_mask) AS gtu_to_date,
            replace(replace((NULLIF(darwin2.fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask), 'xxxx-xx-xx'::text) || COALESCE(('-'::text || NULLIF(darwin2.fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask), 'xxxx-xx-xx'::text)), ''::text)), 'xxxx'::text, ''::text), '-xx'::text, ''::text) AS eventdate,
            unnest(
                CASE
                    WHEN (specimens.gtu_country_tag_indexed IS NOT NULL) THEN specimens.gtu_country_tag_indexed
                    ELSE NULL::character varying[]
                END) AS country_unnest,
            ext_links_thumbnails.url AS urls_thumbnails,
            ext_links_thumbnails.category AS image_category_thumbnails,
            ext_links_thumbnails.contributor AS contributor_thumbnails,
            ext_links_thumbnails.disclaimer AS disclaimer_thumbnails,
            ext_links_thumbnails.license AS license_thumbnails,
            ext_links_thumbnails.display_order AS display_order_thumbnails,
            ext_links_image_links.url AS urls_image_links,
            ext_links_image_links.category AS image_category_image_links,
            ext_links_image_links.contributor AS contributor_image_links,
            ext_links_image_links.disclaimer AS disclaimer_image_links,
            ext_links_image_links.license AS license_image_links,
            ext_links_image_links.display_order AS display_order_image_links,
            ext_links_3d_snippets.url AS urls_3d_snippets,
            ext_links_3d_snippets.category AS image_category_3d_snippets,
            ext_links_3d_snippets.contributor AS contributor_3d_snippets,
            ext_links_3d_snippets.disclaimer AS disclaimer_3d_snippets,
            ext_links_3d_snippets.license AS license_3d_snippets,
            ext_links_3d_snippets.display_order AS display_order_3d_snippets,
            darwin2.fct_mask_date_ymd_gbif(identifications.notion_date, identifications.notion_date_mask) AS identification_date,
            NULL::text AS history,
            specimens.gtu_ref,
            specimens.specimen_count_min,
            specimens.specimen_count_males_min,
            specimens.specimen_count_females_min,
            specimens.specimen_count_juveniles_min,
                CASE
                    WHEN (specimens.specimen_count_juveniles_min > 0) THEN 'juveniles'::text
                    ELSE ''::text
                END AS stage,
                CASE
                    WHEN ((specimens.specimen_count_males_min > 0) AND (specimens.specimen_count_females_min > 0)) THEN 'male_and_female'::text
                    WHEN (specimens.specimen_count_males_min > 0) THEN 'male'::text
                    WHEN (specimens.specimen_count_females_min > 0) THEN 'female'::text
                    ELSE 'no_data'::text
                END AS sex,
            specimens.valid_label,
            specimens.label_created_on,
            specimens.label_created_by,
            specimens.taxon_level_ref
           FROM ((((((((((darwin2.specimens
             LEFT JOIN darwin2.collections ON ((specimens.collection_ref = collections.id)))
             LEFT JOIN darwin2.codes ON ((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text) AND (specimens.id = codes.record_id))))
             LEFT JOIN darwin2.codes aux_code ON ((((aux_code.referenced_relation)::text = 'specimens'::text) AND (lower((aux_code.code_category)::text) = 'additional id'::text) AND (specimens.id = aux_code.record_id))))
             LEFT JOIN darwin2.ext_links ext_links_thumbnails ON (((specimens.id = ext_links_thumbnails.record_id) AND ((ext_links_thumbnails.referenced_relation)::text = 'specimens'::text) AND ((ext_links_thumbnails.category)::text = 'thumbnail'::text))))
             LEFT JOIN darwin2.ext_links ext_links_image_links ON (((specimens.id = ext_links_image_links.record_id) AND ((ext_links_image_links.referenced_relation)::text = 'specimens'::text) AND ((ext_links_image_links.category)::text = 'image_link'::text))))
             LEFT JOIN darwin2.ext_links ext_links_3d_snippets ON (((specimens.id = ext_links_3d_snippets.record_id) AND ((ext_links_3d_snippets.referenced_relation)::text = 'specimens'::text) AND ((ext_links_3d_snippets.category)::text = 'html_3d_snippet'::text))))
             LEFT JOIN darwin2.identifications ON ((((identifications.referenced_relation)::text = 'specimens'::text) AND (specimens.id = identifications.record_id) AND ((identifications.notion_concerned)::text = 'taxonomy'::text))))
             LEFT JOIN darwin2.tags ON ((specimens.gtu_ref = tags.gtu_ref)))
             LEFT JOIN darwin2.gtu ON ((specimens.gtu_ref = gtu.id)))
             LEFT JOIN darwin2.taxonomy ON ((specimens.taxon_ref = taxonomy.id)))
          WHERE ((specimens.collection_ref = 6) AND (NOT COALESCE(taxonomy.sensitive_info_withheld, false)))
          GROUP BY specimens.id, specimens.uuid, (specimens.uuid)::text, collections.id, collections.path, collections.name, collections.code, (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text), specimens.taxon_path, specimens.taxon_ref, specimens.collection_ref, specimens.gtu_country_tag_indexed, specimens.gtu_country_tag_value, specimens.gtu_iso3166, (specimens.gtu_location[0])::character varying, (specimens.gtu_location[1])::character varying, specimens.spec_ident_ids, specimens.gtu_others_tag_indexed, specimens.gtu_others_tag_value, specimens.taxon_name, ext_links_thumbnails.url, ext_links_thumbnails.category, ext_links_thumbnails.contributor, ext_links_thumbnails.disclaimer, ext_links_thumbnails.license, ext_links_thumbnails.display_order, ext_links_image_links.url, ext_links_image_links.category, ext_links_image_links.contributor, ext_links_image_links.disclaimer, ext_links_image_links.license, ext_links_image_links.display_order, ext_links_3d_snippets.url, ext_links_3d_snippets.category, ext_links_3d_snippets.contributor, ext_links_3d_snippets.disclaimer, ext_links_3d_snippets.license, ext_links_3d_snippets.display_order, identifications.notion_date, identifications.notion_date_mask, specimens.gtu_ref, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.type, specimens.spec_coll_ids, specimens.spec_don_sel_ids, codes.full_code_indexed, gtu.lat_long_accuracy, specimens.specimen_count_min, specimens.specimen_count_males_min, specimens.specimen_count_females_min, specimens.specimen_count_juveniles_min, specimens.valid_label, specimens.label_created_on, specimens.label_created_by, specimens.taxon_level_ref
        ), detect_no_valid AS (
         SELECT a.cataloguenumber,
            array_agg(a.id ORDER BY a.identification_date, a.id DESC) AS ids,
            count(a.id) AS cpt_ids,
            string_agg(((a.valid_label)::character varying)::text, ','::text ORDER BY a.identification_date, a.id DESC) AS str_valid
           FROM a
          GROUP BY a.cataloguenumber
        ), q_true AS (
         SELECT detect_no_valid.cataloguenumber,
            detect_no_valid.ids,
            detect_no_valid.cpt_ids,
            detect_no_valid.str_valid,
            detect_no_valid.ids[1] AS force_valid
           FROM detect_no_valid
          WHERE ((detect_no_valid.cpt_ids > 1) AND (detect_no_valid.str_valid !~~ '%true%'::text))
        ), valid AS (
         SELECT a.id,
            a.public_url,
            a.uuid,
            a.guid,
            a.collection_ref,
            a.collection_code,
            a.collection_name,
            a.collection_id,
            a.collection_path,
            a.cataloguenumber,
            a.specimen_code_number,
            a.basisofrecord,
            a.institutionid,
            a.iso_country_institution,
            a.bibliographic_citation,
            a.license,
            a.email,
            a.type,
            a.taxon_path,
            a.taxon_ref,
            a.taxon_name,
            a.family,
            a.iso_country,
            a.country,
            a.location,
            a.latitude,
            a.longitude,
            a.lat_long_accuracy,
            a.collector_ids,
            a.collectors,
            a.donator_ids,
            a.donators,
            a.identifiers_ids,
            a.identifiers,
            a.gtu_from_date,
            a.gtu_to_date,
            a.eventdate,
            a.country_unnest,
            a.urls_thumbnails,
            a.image_category_thumbnails,
            a.contributor_thumbnails,
            a.disclaimer_thumbnails,
            a.license_thumbnails,
            a.display_order_thumbnails,
            a.urls_image_links,
            a.image_category_image_links,
            a.contributor_image_links,
            a.disclaimer_image_links,
            a.license_image_links,
            a.display_order_image_links,
            a.urls_3d_snippets,
            a.image_category_3d_snippets,
            a.contributor_3d_snippets,
            a.disclaimer_3d_snippets,
            a.license_3d_snippets,
            a.display_order_3d_snippets,
            a.identification_date,
            a.history,
            a.gtu_ref,
            a.specimen_count_min,
            a.specimen_count_males_min,
            a.specimen_count_females_min,
            a.specimen_count_juveniles_min,
            a.stage,
            a.sex,
            a.valid_label,
            a.label_created_on,
            a.label_created_by,
            a.taxon_level_ref
           FROM (a
             LEFT JOIN q_true ON ((a.id = q_true.force_valid)))
          WHERE ((COALESCE(a.valid_label, false) = true) OR (q_true.force_valid IS NOT NULL))
        ), invalid AS (
         SELECT a.cataloguenumber,
            string_agg(((((a.identifiers || ':'::text) || (a.taxon_name)::text) || ' on '::text) || a.identification_date), ' - '::text) AS previous_identifications
           FROM a
          WHERE (COALESCE(a.valid_label, false) = false)
          GROUP BY a.cataloguenumber
        ), tot AS (
         SELECT valid.id,
            valid.public_url,
            valid.uuid,
            valid.guid,
            valid.collection_ref,
            valid.collection_code,
            valid.collection_name,
            valid.collection_id,
            valid.collection_path,
            valid.cataloguenumber,
            valid.specimen_code_number,
            valid.basisofrecord,
            valid.institutionid,
            valid.iso_country_institution,
            valid.bibliographic_citation,
            valid.license,
            valid.email,
            valid.type,
            valid.taxon_path,
            valid.taxon_ref,
            valid.taxon_name,
            valid.family,
            valid.iso_country,
            valid.country,
            valid.location,
            valid.latitude,
            valid.longitude,
            valid.lat_long_accuracy,
            valid.collector_ids,
            valid.collectors,
            valid.donator_ids,
            valid.donators,
            valid.identifiers_ids,
            valid.identifiers,
            valid.gtu_from_date,
            valid.gtu_to_date,
            valid.eventdate,
            valid.country_unnest,
            valid.urls_thumbnails,
            valid.image_category_thumbnails,
            valid.contributor_thumbnails,
            valid.disclaimer_thumbnails,
            valid.license_thumbnails,
            valid.display_order_thumbnails,
            valid.urls_image_links,
            valid.image_category_image_links,
            valid.contributor_image_links,
            valid.disclaimer_image_links,
            valid.license_image_links,
            valid.display_order_image_links,
            valid.urls_3d_snippets,
            valid.image_category_3d_snippets,
            valid.contributor_3d_snippets,
            valid.disclaimer_3d_snippets,
            valid.license_3d_snippets,
            valid.display_order_3d_snippets,
            valid.identification_date,
            valid.history,
            valid.gtu_ref,
            valid.specimen_count_min,
            valid.specimen_count_males_min,
            valid.specimen_count_females_min,
            valid.specimen_count_juveniles_min,
            valid.stage,
            valid.sex,
            valid.valid_label,
            valid.label_created_on,
            valid.label_created_by,
                CASE
                    WHEN (invalid.previous_identifications IS NULL) THEN (('{ ''donators'':'''::text || valid.donators) || '''}'::text)
                    ELSE (((('{ ''donators'':'''::text || valid.donators) || ', ''previous_identifications'':'''::text) || invalid.previous_identifications) || '''}'::text)
                END AS additional_data,
            valid.taxon_level_ref
           FROM (valid
             LEFT JOIN invalid ON ((valid.cataloguenumber = invalid.cataloguenumber)))
        ), all_r AS (
         SELECT tot.id,
            tot.public_url,
            tot.uuid,
            tot.guid,
            tot.collection_ref,
            tot.collection_code,
            tot.collection_name,
            tot.collection_id,
            tot.collection_path,
            tot.cataloguenumber,
            tot.specimen_code_number,
            tot.basisofrecord,
            tot.institutionid,
            tot.iso_country_institution,
            tot.bibliographic_citation,
            tot.license,
            tot.email,
            tot.type,
            tot.taxon_path,
            tot.taxon_ref,
            tot.taxon_name,
            tot.family,
            tot.iso_country,
            tot.country,
            tot.location,
            tot.latitude,
            tot.longitude,
            tot.lat_long_accuracy,
            tot.collector_ids,
            tot.collectors,
            tot.donator_ids,
            tot.donators,
            tot.identifiers_ids,
            tot.identifiers,
            tot.gtu_from_date,
            tot.gtu_to_date,
            tot.eventdate,
            tot.country_unnest,
            tot.urls_thumbnails,
            tot.image_category_thumbnails,
            tot.contributor_thumbnails,
            tot.disclaimer_thumbnails,
            tot.license_thumbnails,
            tot.display_order_thumbnails,
            tot.urls_image_links,
            tot.image_category_image_links,
            tot.contributor_image_links,
            tot.disclaimer_image_links,
            tot.license_image_links,
            tot.display_order_image_links,
            tot.urls_3d_snippets,
            tot.image_category_3d_snippets,
            tot.contributor_3d_snippets,
            tot.disclaimer_3d_snippets,
            tot.license_3d_snippets,
            tot.display_order_3d_snippets,
            tot.identification_date,
            tot.history,
            tot.gtu_ref,
            tot.specimen_count_min,
            tot.specimen_count_males_min,
            tot.specimen_count_females_min,
            tot.specimen_count_juveniles_min,
            tot.stage,
            tot.sex,
            tot.valid_label,
            tot.label_created_on,
            tot.label_created_by,
            tot.additional_data,
            tot.taxon_level_ref
           FROM tot
        UNION
         SELECT a.id,
            a.public_url,
            a.uuid,
            a.guid,
            a.collection_ref,
            'VER-FIS'::text AS collection_code,
            a.collection_name,
            a.collection_id,
            a.collection_path,
            a.cataloguenumber,
            a.specimen_code_number,
            a.basisofrecord,
            a.institutionid,
            a.iso_country_institution,
            a.bibliographic_citation,
            a.license,
            a.email,
            a.type,
            a.taxon_path,
            a.taxon_ref,
            a.taxon_name,
            a.family,
            a.iso_country,
            a.country,
            a.location,
            a.latitude,
            a.longitude,
            a.lat_long_accuracy,
            a.collector_ids,
            a.collectors,
            a.donator_ids,
            a.donators,
            a.identifiers_ids,
            a.identifiers,
            a.gtu_from_date,
            a.gtu_to_date,
            a.eventdate,
            a.country_unnest,
            a.urls_thumbnails,
            a.image_category_thumbnails,
            a.contributor_thumbnails,
            a.disclaimer_thumbnails,
            a.license_thumbnails,
            a.display_order_thumbnails,
            a.urls_image_links,
            a.image_category_image_links,
            a.contributor_image_links,
            a.disclaimer_image_links,
            a.license_image_links,
            a.display_order_image_links,
            a.urls_3d_snippets,
            a.image_category_3d_snippets,
            a.contributor_3d_snippets,
            a.disclaimer_3d_snippets,
            a.license_3d_snippets,
            a.display_order_3d_snippets,
            a.identification_date,
            a.history,
            a.gtu_ref,
            a.specimen_count_min,
            a.specimen_count_males_min,
            a.specimen_count_females_min,
            a.specimen_count_juveniles_min,
            a.stage,
            a.sex,
            a.valid_label,
            a.label_created_on,
            a.label_created_by,
                CASE
                    WHEN (invalid.previous_identifications IS NULL) THEN (((('{ ''donators'':'''::text || a.donators) || ''', ''current_identification_validity'':'''::text) || a.valid_label) || '''}'::text)
                    ELSE (((((('{ ''donators'':'''::text || a.donators) || ''', ''previous_identifications'':'''::text) || invalid.previous_identifications) || ''', ''current_identification_validity'':'::text) || a.valid_label) || '}'::text)
                END AS additional_data,
            a.taxon_level_ref
           FROM (a
             LEFT JOIN invalid ON ((a.cataloguenumber = invalid.cataloguenumber)))
          WHERE (NOT (a.cataloguenumber IN ( SELECT tot.cataloguenumber
                   FROM tot)))
        )
 SELECT all_r.id,
    all_r.public_url,
    all_r.uuid,
    all_r.guid,
    all_r.collection_ref,
    'VER-FIS'::text AS collection_code,
    all_r.collection_name,
    'https://ror.org/001805t51'::text AS collection_id,
    all_r.collection_path,
    all_r.cataloguenumber,
    all_r.specimen_code_number,
    'PreservedSpecimen'::text AS basisofrecord,
    'https://ror.org/001805t51'::text AS institutionid,
    NULL::text AS iso_country_institution,
    NULL::text AS bibliographic_citation,
    'TO_FILL'::text AS license,
    'CONTACT_PERSON'::text AS email,
    COALESCE(NULLIF(btrim((all_r.type)::text), 'specimen'::text), ''::text) AS type,
    all_r.taxon_path,
    all_r.taxon_ref,
    all_r.taxon_name,
    all_r.family,
    all_r.iso_country,
    all_r.country,
    all_r.location,
    all_r.latitude,
    all_r.longitude,
    all_r.lat_long_accuracy,
    all_r.collector_ids,
    all_r.collectors,
    all_r.donator_ids,
    all_r.donators,
    all_r.identifiers_ids,
    all_r.identifiers,
    all_r.gtu_from_date,
    all_r.gtu_to_date,
    all_r.eventdate,
    all_r.country_unnest,
    all_r.urls_thumbnails,
    all_r.image_category_thumbnails,
    all_r.contributor_thumbnails,
    all_r.disclaimer_thumbnails,
    all_r.license_thumbnails,
    all_r.display_order_thumbnails,
    all_r.urls_image_links,
    all_r.image_category_image_links,
    all_r.contributor_image_links,
    all_r.disclaimer_image_links,
    all_r.license_image_links,
    all_r.display_order_image_links,
    all_r.urls_3d_snippets,
    all_r.image_category_3d_snippets,
    all_r.contributor_3d_snippets,
    all_r.disclaimer_3d_snippets,
    all_r.license_3d_snippets,
    all_r.display_order_3d_snippets,
    all_r.identification_date,
    all_r.history,
    all_r.gtu_ref,
    all_r.specimen_count_min,
    all_r.specimen_count_males_min,
    all_r.specimen_count_females_min,
    all_r.specimen_count_juveniles_min,
    all_r.stage,
    NULLIF(all_r.sex, 'no_data'::text) AS sex,
    all_r.valid_label,
    all_r.label_created_on,
    all_r.label_created_by,
    all_r.additional_data,
    'RMCA'::text AS institution_code,
    darwin2.fct_rmca_sort_taxon_get_parent_level_text(all_r.taxon_ref, 28) AS "order",
    darwin2.fct_rmca_sort_taxon_get_parent_level_text(all_r.taxon_ref, 12) AS class,
    catalogue_levels.level_name
   FROM (all_r
     LEFT JOIN darwin2.catalogue_levels ON ((all_r.taxon_level_ref = catalogue_levels.id)))
  WITH NO DATA;


ALTER TABLE darwin2.mv_gbif_ichtyo_history OWNER TO darwin2;

--
-- TOC entry 257 (class 1259 OID 467271577)
-- Name: mv_gbif_ichtyo_history_no_dup; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_gbif_ichtyo_history_no_dup AS
 WITH a AS (
         SELECT mv_gbif_ichtyo_history.id,
            mv_gbif_ichtyo_history.public_url,
            mv_gbif_ichtyo_history.uuid,
            mv_gbif_ichtyo_history.guid,
            mv_gbif_ichtyo_history.collection_ref,
            mv_gbif_ichtyo_history.collection_code,
            mv_gbif_ichtyo_history.collection_name,
            mv_gbif_ichtyo_history.collection_id,
            mv_gbif_ichtyo_history.collection_path,
            mv_gbif_ichtyo_history.cataloguenumber,
            mv_gbif_ichtyo_history.specimen_code_number,
            mv_gbif_ichtyo_history.basisofrecord,
            mv_gbif_ichtyo_history.institutionid,
            mv_gbif_ichtyo_history.iso_country_institution,
            mv_gbif_ichtyo_history.bibliographic_citation,
            mv_gbif_ichtyo_history.license,
            mv_gbif_ichtyo_history.email,
            mv_gbif_ichtyo_history.type,
            mv_gbif_ichtyo_history.taxon_path,
            mv_gbif_ichtyo_history.taxon_ref,
            mv_gbif_ichtyo_history.taxon_name,
            mv_gbif_ichtyo_history.family,
            mv_gbif_ichtyo_history.iso_country,
            mv_gbif_ichtyo_history.country,
            mv_gbif_ichtyo_history.location,
            mv_gbif_ichtyo_history.latitude,
            mv_gbif_ichtyo_history.longitude,
            mv_gbif_ichtyo_history.lat_long_accuracy,
            mv_gbif_ichtyo_history.collector_ids,
            mv_gbif_ichtyo_history.collectors,
            mv_gbif_ichtyo_history.donator_ids,
            mv_gbif_ichtyo_history.donators,
            mv_gbif_ichtyo_history.identifiers_ids,
            mv_gbif_ichtyo_history.identifiers,
            mv_gbif_ichtyo_history.gtu_from_date,
            mv_gbif_ichtyo_history.gtu_to_date,
            mv_gbif_ichtyo_history.eventdate,
            mv_gbif_ichtyo_history.country_unnest,
            mv_gbif_ichtyo_history.urls_thumbnails,
            mv_gbif_ichtyo_history.image_category_thumbnails,
            mv_gbif_ichtyo_history.contributor_thumbnails,
            mv_gbif_ichtyo_history.disclaimer_thumbnails,
            mv_gbif_ichtyo_history.license_thumbnails,
            mv_gbif_ichtyo_history.display_order_thumbnails,
            mv_gbif_ichtyo_history.urls_image_links,
            mv_gbif_ichtyo_history.image_category_image_links,
            mv_gbif_ichtyo_history.contributor_image_links,
            mv_gbif_ichtyo_history.disclaimer_image_links,
            mv_gbif_ichtyo_history.license_image_links,
            mv_gbif_ichtyo_history.display_order_image_links,
            mv_gbif_ichtyo_history.urls_3d_snippets,
            mv_gbif_ichtyo_history.image_category_3d_snippets,
            mv_gbif_ichtyo_history.contributor_3d_snippets,
            mv_gbif_ichtyo_history.disclaimer_3d_snippets,
            mv_gbif_ichtyo_history.license_3d_snippets,
            mv_gbif_ichtyo_history.display_order_3d_snippets,
            mv_gbif_ichtyo_history.identification_date,
            mv_gbif_ichtyo_history.history,
            mv_gbif_ichtyo_history.gtu_ref,
            mv_gbif_ichtyo_history.specimen_count_min,
            mv_gbif_ichtyo_history.specimen_count_males_min,
            mv_gbif_ichtyo_history.specimen_count_females_min,
            mv_gbif_ichtyo_history.specimen_count_juveniles_min,
            mv_gbif_ichtyo_history.stage,
            mv_gbif_ichtyo_history.sex,
            mv_gbif_ichtyo_history.valid_label,
            mv_gbif_ichtyo_history.label_created_on,
            mv_gbif_ichtyo_history.label_created_by,
            mv_gbif_ichtyo_history.additional_data,
            mv_gbif_ichtyo_history.institution_code,
            mv_gbif_ichtyo_history."order",
            mv_gbif_ichtyo_history.class,
            mv_gbif_ichtyo_history.level_name,
            rank() OVER (PARTITION BY mv_gbif_ichtyo_history.uuid ORDER BY mv_gbif_ichtyo_history.identification_date DESC) AS rank
           FROM darwin2.mv_gbif_ichtyo_history
        )
 SELECT a.id,
    a.public_url,
    a.uuid,
    a.guid,
    a.collection_ref,
    a.collection_code,
    a.collection_name,
    a.collection_id,
    a.collection_path,
    a.cataloguenumber,
    a.specimen_code_number,
    a.basisofrecord,
    a.institutionid,
    a.iso_country_institution,
    a.bibliographic_citation,
    a.license,
    a.email,
    a.type,
    a.taxon_path,
    a.taxon_ref,
    a.taxon_name,
    a.family,
    a.iso_country,
    a.country,
    a.location,
    a.latitude,
    a.longitude,
    a.lat_long_accuracy,
    a.collector_ids,
    a.collectors,
    a.donator_ids,
    a.donators,
    a.identifiers_ids,
    a.identifiers,
    a.gtu_from_date,
    a.gtu_to_date,
    a.eventdate,
    a.country_unnest,
    a.urls_thumbnails,
    a.image_category_thumbnails,
    a.contributor_thumbnails,
    a.disclaimer_thumbnails,
    a.license_thumbnails,
    a.display_order_thumbnails,
    a.urls_image_links,
    a.image_category_image_links,
    a.contributor_image_links,
    a.disclaimer_image_links,
    a.license_image_links,
    a.display_order_image_links,
    a.urls_3d_snippets,
    a.image_category_3d_snippets,
    a.contributor_3d_snippets,
    a.disclaimer_3d_snippets,
    a.license_3d_snippets,
    a.display_order_3d_snippets,
    a.identification_date,
    a.history,
    a.gtu_ref,
    a.specimen_count_min,
    a.specimen_count_males_min,
    a.specimen_count_females_min,
    a.specimen_count_juveniles_min,
    a.stage,
    a.sex,
    a.valid_label,
    a.label_created_on,
    a.label_created_by,
    a.additional_data,
    a.institution_code,
    a."order",
    a.class,
    a.level_name,
    a.rank
   FROM a
  WHERE (a.rank = 1)
  WITH NO DATA;


ALTER TABLE darwin2.mv_gbif_ichtyo_history_no_dup OWNER TO darwin2;

--
-- TOC entry 250 (class 1259 OID 467180071)
-- Name: mv_gtu_stats; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_gtu_stats AS
 WITH a AS (
         SELECT v_collections_full_path_recursive.id,
            v_collections_full_path_recursive.collection_type,
            v_collections_full_path_recursive.code,
            v_collections_full_path_recursive.name,
            v_collections_full_path_recursive.name_indexed,
            v_collections_full_path_recursive.institution_ref,
            v_collections_full_path_recursive.main_manager_ref,
            v_collections_full_path_recursive.staff_ref,
            v_collections_full_path_recursive.parent_ref,
            v_collections_full_path_recursive.path,
            v_collections_full_path_recursive.code_auto_increment,
            v_collections_full_path_recursive.code_last_value,
            v_collections_full_path_recursive.code_prefix,
            v_collections_full_path_recursive.code_prefix_separator,
            v_collections_full_path_recursive.code_suffix,
            v_collections_full_path_recursive.code_suffix_separator,
            v_collections_full_path_recursive.code_specimen_duplicate,
            v_collections_full_path_recursive.is_public,
            v_collections_full_path_recursive.code_mask,
            v_collections_full_path_recursive.loan_auto_increment,
            v_collections_full_path_recursive.loan_last_value,
            v_collections_full_path_recursive.code_ai_inherit,
            v_collections_full_path_recursive.code_auto_increment_for_insert_only,
            v_collections_full_path_recursive.nagoya,
            v_collections_full_path_recursive.allow_duplicates,
            v_collections_full_path_recursive.code_full_path,
            v_collections_full_path_recursive.name_full_path,
            v_collections_full_path_recursive.name_indexed_full_path
           FROM darwin2.v_collections_full_path_recursive
        ), b AS (
         SELECT specimens.gtu_ref,
            specimens.collection_ref,
            a.name_full_path,
            count(*) AS nb_records,
            sum(specimens.specimen_count_min) AS specimen_count_min,
            sum(specimens.specimen_count_max) AS specimen_count_max,
            min(
                CASE
                    WHEN (specimens.gtu_from_date_mask = 0) THEN NULL::timestamp without time zone
                    ELSE specimens.gtu_from_date
                END) AS first_date_from,
            max(
                CASE
                    WHEN (specimens.gtu_from_date_mask = 0) THEN NULL::timestamp without time zone
                    ELSE specimens.gtu_from_date
                END) AS last_date_from,
            min(
                CASE
                    WHEN (specimens.gtu_to_date_mask = 0) THEN NULL::timestamp without time zone
                    ELSE specimens.gtu_to_date
                END) AS first_date_to,
            max(
                CASE
                    WHEN (specimens.gtu_to_date_mask = 0) THEN NULL::timestamp without time zone
                    ELSE specimens.gtu_to_date
                END) AS last_date_to
           FROM (darwin2.specimens
             LEFT JOIN a ON ((specimens.collection_ref = a.id)))
          GROUP BY specimens.gtu_ref, specimens.collection_ref, a.name_full_path
        )
 SELECT b.gtu_ref,
    b.collection_ref,
    b.name_full_path,
    b.nb_records,
    b.specimen_count_min,
    b.specimen_count_max,
    b.first_date_from,
    b.last_date_from,
    b.first_date_to,
    b.last_date_to
   FROM b
  WITH NO DATA;


ALTER TABLE darwin2.mv_gtu_stats OWNER TO darwin2;

--
-- TOC entry 251 (class 1259 OID 467180113)
-- Name: mv_gtu_tags_flat; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_gtu_tags_flat AS
 WITH gtu AS (
         SELECT gtu_1.id,
            gtu_1.code,
            gtu_1.gtu_from_date_mask,
            gtu_1.gtu_from_date,
            gtu_1.gtu_to_date_mask,
            gtu_1.gtu_to_date,
            gtu_1.tag_values_indexed,
            gtu_1.latitude,
            gtu_1.longitude,
            gtu_1.lat_long_accuracy,
            gtu_1.location,
            gtu_1.elevation,
            gtu_1.elevation_accuracy,
            gtu_1.latitude_dms_degree,
            gtu_1.latitude_dms_minutes,
            gtu_1.latitude_dms_seconds,
            gtu_1.latitude_dms_direction,
            gtu_1.longitude_dms_degree,
            gtu_1.longitude_dms_minutes,
            gtu_1.longitude_dms_seconds,
            gtu_1.longitude_dms_direction,
            gtu_1.latitude_utm,
            gtu_1.longitude_utm,
            gtu_1.utm_zone,
            gtu_1.coordinates_source,
            gtu_1.elevation_unit,
            gtu_1.gtu_creation_date,
            gtu_1.import_ref,
            gtu_1.iso3166,
            gtu_1.iso3166_subdivision,
            gtu_1.wkt_str,
            gtu_1.nagoya,
            gtu_1.geom
           FROM darwin2.gtu gtu_1
        ), country AS (
         SELECT tags.gtu_ref,
            tags.tag AS country
           FROM darwin2.tags
          WHERE (lower((tags.sub_group_type)::text) = 'country'::text)
        ), continent AS (
         SELECT tags.gtu_ref,
            tags.tag AS continent
           FROM darwin2.tags
          WHERE (lower((tags.sub_group_type)::text) = 'continent'::text)
        ), others_v AS (
         SELECT tags.gtu_ref,
            tags.sub_group_type,
            tags.tag
           FROM darwin2.tags
          WHERE ((lower((tags.sub_group_type)::text) <> 'continent'::text) AND (lower((tags.sub_group_type)::text) <> 'country'::text))
        )
 SELECT gtu.id,
    gtu.code AS station_number,
    continent.continent,
    country.country,
    others_v.tag,
    gtu.latitude,
    gtu.longitude,
    gtu.latitude_dms_degree,
    gtu.latitude_dms_minutes,
    gtu.latitude_dms_seconds,
        CASE
            WHEN (gtu.latitude_dms_degree IS NULL) THEN NULL::text
            WHEN (gtu.latitude_dms_direction < 0) THEN 'S'::text
            ELSE 'N'::text
        END AS latitude_dms_direction,
    gtu.longitude_dms_degree,
    gtu.longitude_dms_minutes,
    gtu.longitude_dms_seconds,
        CASE
            WHEN (gtu.longitude_dms_degree IS NULL) THEN NULL::text
            WHEN (gtu.longitude_dms_direction < 0) THEN 'W'::text
            ELSE 'E'::text
        END AS longitude_dms_direction
   FROM (((gtu
     LEFT JOIN continent ON ((gtu.id = continent.gtu_ref)))
     LEFT JOIN country ON ((gtu.id = country.gtu_ref)))
     LEFT JOIN others_v ON ((gtu.id = others_v.gtu_ref)))
  WITH NO DATA;


ALTER TABLE darwin2.mv_gtu_tags_flat OWNER TO darwin2;

--
-- TOC entry 248 (class 1259 OID 467132521)
-- Name: mv_search_public_specimen; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_search_public_specimen AS
 SELECT DISTINCT specimens.id,
    specimens.uuid,
    COALESCE(codes.code_prefix, ''::character varying) AS code_prefix,
    COALESCE(codes.code, ''::character varying) AS code,
    codes.code_num,
    COALESCE(NULLIF(codes.code_display, ''::text), (specimens.collection_code)::text) AS code_display,
    codes.full_code_indexed,
    specimens.taxon_path,
    specimens.taxon_ref,
    specimens.collection_ref,
    specimens.gtu_country_tag_indexed,
    NULLIF((specimens.gtu_country_tag_value)::text, ''::text) AS gtu_country_tag_value,
    specimens.gtu_others_tag_indexed AS localities_indexed,
    specimens.gtu_others_tag_value,
    specimens.family,
    specimens.taxon_name,
    specimens.sex,
    specimens.spec_coll_ids AS collector_ids,
    specimens.spec_don_sel_ids AS donator_ids,
    specimens.gtu_from_date,
    specimens.gtu_from_date_mask,
    specimens.gtu_to_date,
    specimens.gtu_to_date_mask,
    specimens.type AS coll_type,
    specimens.collection_path,
    specimens.specimen_count_min,
    specimens.gtu_location[1] AS latitude,
    specimens.gtu_location[0] AS longitude,
    NULLIF(NULLIF(darwin2.fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask), 'xxxx-xx-xx'::text), ''::text) AS formated_date,
    (ROW(COALESCE(codes.code_prefix, ''::character varying), COALESCE(codes.code, ''::character varying), codes.code_num))::character varying AS code_concat,
    specimens.geom,
    specimens.valid_label,
    specimens.gtu_others_tag_indexed
   FROM (((darwin2.specimens
     LEFT JOIN darwin2.codes ON ((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text) AND (specimens.id = codes.record_id))))
     JOIN darwin2.collections ON (((specimens.collection_ref = collections.id) AND (collections.is_public = true))))
     JOIN darwin2.taxonomy ON (((specimens.taxon_ref = taxonomy.id) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false))))
  WITH NO DATA;


ALTER TABLE darwin2.mv_search_public_specimen OWNER TO darwin2;

--
-- TOC entry 226 (class 1259 OID 467103183)
-- Name: src_mv_specimen_public; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.src_mv_specimen_public (
    uuid uuid,
    ids integer[],
    code_display text,
    taxon_paths character varying[],
    taxon_ref integer[],
    taxon_name character varying[],
    sex character varying,
    history_identification text[],
    gtu_country_tag_value character varying,
    gtu_others_tag_value character varying,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    fct_mask_date text,
    date_from_display text,
    date_to_display text,
    coll_type character varying,
    urls_thumbnails text,
    image_category_thumbnails text,
    contributor_thumbnails text,
    disclaimer_thumbnails text,
    license_thumbnails text,
    display_order_thumbnails text,
    urls_image_links text,
    image_category_image_links text,
    contributor_image_links text,
    disclaimer_image_links text,
    license_image_links text,
    display_order_image_links text,
    urls_3d_snippets text,
    image_category_3d_snippets text,
    contributor_3d_snippets text,
    disclaimer_3d_snippets text,
    license_3d_snippets text,
    display_order_3d_snippets text,
    longitude double precision,
    latitude double precision,
    collector_ids integer[],
    collectors character varying[],
    donator_ids integer[],
    donators character varying[],
    localities text[],
    family text,
    t_order text,
    class text,
    specimen_count_min integer,
    specimen_count_males_min integer,
    specimen_count_females_min integer,
    collection_code_full_path character varying,
    collection_name_full_path character varying
);


ALTER TABLE darwin2.src_mv_specimen_public OWNER TO darwin2;

--
-- TOC entry 227 (class 1259 OID 467103189)
-- Name: v_specimen_public; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_specimen_public AS
 SELECT src_mv_specimen_public.uuid,
    src_mv_specimen_public.ids,
    src_mv_specimen_public.code_display,
    src_mv_specimen_public.taxon_paths,
    src_mv_specimen_public.taxon_ref,
    src_mv_specimen_public.taxon_name,
    src_mv_specimen_public.sex,
    src_mv_specimen_public.history_identification,
    src_mv_specimen_public.gtu_country_tag_value,
    src_mv_specimen_public.gtu_others_tag_value,
    src_mv_specimen_public.gtu_from_date,
    src_mv_specimen_public.gtu_from_date_mask,
    src_mv_specimen_public.gtu_to_date,
    src_mv_specimen_public.gtu_to_date_mask,
    src_mv_specimen_public.fct_mask_date,
    src_mv_specimen_public.date_from_display,
    src_mv_specimen_public.date_to_display,
    src_mv_specimen_public.coll_type,
    src_mv_specimen_public.urls_thumbnails,
    src_mv_specimen_public.image_category_thumbnails,
    src_mv_specimen_public.contributor_thumbnails,
    src_mv_specimen_public.disclaimer_thumbnails,
    src_mv_specimen_public.license_thumbnails,
    src_mv_specimen_public.display_order_thumbnails,
    src_mv_specimen_public.urls_image_links,
    src_mv_specimen_public.image_category_image_links,
    src_mv_specimen_public.contributor_image_links,
    src_mv_specimen_public.disclaimer_image_links,
    src_mv_specimen_public.license_image_links,
    src_mv_specimen_public.display_order_image_links,
    src_mv_specimen_public.urls_3d_snippets,
    src_mv_specimen_public.image_category_3d_snippets,
    src_mv_specimen_public.contributor_3d_snippets,
    src_mv_specimen_public.disclaimer_3d_snippets,
    src_mv_specimen_public.license_3d_snippets,
    src_mv_specimen_public.display_order_3d_snippets,
    src_mv_specimen_public.longitude,
    src_mv_specimen_public.latitude,
    src_mv_specimen_public.collector_ids,
    src_mv_specimen_public.collectors,
    src_mv_specimen_public.donator_ids,
    src_mv_specimen_public.donators,
    src_mv_specimen_public.localities,
    src_mv_specimen_public.family,
    src_mv_specimen_public.t_order,
    src_mv_specimen_public.class,
    src_mv_specimen_public.specimen_count_min,
    src_mv_specimen_public.specimen_count_males_min,
    src_mv_specimen_public.specimen_count_females_min,
    src_mv_specimen_public.collection_code_full_path,
    src_mv_specimen_public.collection_name_full_path
   FROM darwin2.src_mv_specimen_public;


ALTER TABLE darwin2.v_specimen_public OWNER TO darwin2;

--
-- TOC entry 247 (class 1259 OID 467113628)
-- Name: mv_specimen_public; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_specimen_public AS
 WITH a AS (
         SELECT v_specimen_public.uuid,
            v_specimen_public.ids,
            v_specimen_public.code_display,
            v_specimen_public.taxon_paths,
            v_specimen_public.taxon_ref,
            v_specimen_public.taxon_name,
            v_specimen_public.sex,
            v_specimen_public.history_identification,
            unnest(string_to_array(COALESCE((v_specimen_public.gtu_country_tag_value)::text, ','::text), ';'::text)) AS tmp_country,
            v_specimen_public.gtu_others_tag_value,
            v_specimen_public.gtu_from_date,
            v_specimen_public.gtu_from_date_mask,
            v_specimen_public.gtu_to_date,
            v_specimen_public.gtu_to_date_mask,
            v_specimen_public.fct_mask_date,
            v_specimen_public.date_from_display,
            v_specimen_public.date_to_display,
            v_specimen_public.coll_type,
            v_specimen_public.urls_thumbnails,
            v_specimen_public.image_category_thumbnails,
            v_specimen_public.contributor_thumbnails,
            v_specimen_public.disclaimer_thumbnails,
            v_specimen_public.license_thumbnails,
            v_specimen_public.display_order_thumbnails,
            v_specimen_public.urls_image_links,
            v_specimen_public.image_category_image_links,
            v_specimen_public.contributor_image_links,
            v_specimen_public.disclaimer_image_links,
            v_specimen_public.license_image_links,
            v_specimen_public.display_order_image_links,
            v_specimen_public.urls_3d_snippets,
            v_specimen_public.image_category_3d_snippets,
            v_specimen_public.contributor_3d_snippets,
            v_specimen_public.disclaimer_3d_snippets,
            v_specimen_public.license_3d_snippets,
            v_specimen_public.display_order_3d_snippets,
            v_specimen_public.longitude,
            v_specimen_public.latitude,
            v_specimen_public.collector_ids,
            v_specimen_public.collectors,
            v_specimen_public.donator_ids,
            v_specimen_public.donators,
            v_specimen_public.localities,
            v_specimen_public.family,
            v_specimen_public.t_order,
            v_specimen_public.class,
            v_specimen_public.specimen_count_min,
            v_specimen_public.specimen_count_males_min,
            v_specimen_public.specimen_count_females_min,
            v_specimen_public.collection_code_full_path,
            v_specimen_public.collection_name_full_path
           FROM darwin2.v_specimen_public
        ), b AS (
         SELECT a.uuid,
            a.ids,
            a.code_display,
            a.taxon_paths,
            a.taxon_ref,
            a.taxon_name,
            a.sex,
            a.history_identification,
                CASE
                    WHEN (country_cleaning.replacement_value IS NOT NULL) THEN (country_cleaning.replacement_value)::text
                    ELSE a.tmp_country
                END AS tmp_country,
            a.gtu_others_tag_value,
            a.gtu_from_date,
            a.gtu_from_date_mask,
            a.gtu_to_date,
            a.gtu_to_date_mask,
            a.fct_mask_date,
            a.date_from_display,
            a.date_to_display,
            a.coll_type,
            a.urls_thumbnails,
            a.image_category_thumbnails,
            a.contributor_thumbnails,
            a.disclaimer_thumbnails,
            a.license_thumbnails,
            a.display_order_thumbnails,
            a.urls_image_links,
            a.image_category_image_links,
            a.contributor_image_links,
            a.disclaimer_image_links,
            a.license_image_links,
            a.display_order_image_links,
            a.urls_3d_snippets,
            a.image_category_3d_snippets,
            a.contributor_3d_snippets,
            a.disclaimer_3d_snippets,
            a.license_3d_snippets,
            a.display_order_3d_snippets,
            a.longitude,
            a.latitude,
            a.collector_ids,
            a.collectors,
            a.donator_ids,
            a.donators,
            a.localities,
            a.family,
            a.t_order,
            a.class,
            a.specimen_count_min,
            a.specimen_count_males_min,
            a.specimen_count_females_min,
            a.collection_code_full_path,
            a.collection_name_full_path
           FROM (a
             LEFT JOIN darwin2.country_cleaning ON ((a.tmp_country = (country_cleaning.original_name)::text)))
        )
 SELECT b.uuid,
    b.ids,
    b.code_display,
    b.taxon_paths,
    b.taxon_ref,
    b.taxon_name,
    b.sex,
    b.history_identification,
    string_agg(b.tmp_country, ';'::text) AS gtu_country_tag_value,
    b.gtu_others_tag_value,
    b.gtu_from_date,
    b.gtu_from_date_mask,
    b.gtu_to_date,
    b.gtu_to_date_mask,
    b.fct_mask_date,
    b.date_from_display,
    b.date_to_display,
    b.coll_type,
    b.urls_thumbnails,
    b.image_category_thumbnails,
    b.contributor_thumbnails,
    b.disclaimer_thumbnails,
    b.license_thumbnails,
    b.display_order_thumbnails,
    b.urls_image_links,
    b.image_category_image_links,
    b.contributor_image_links,
    b.disclaimer_image_links,
    b.license_image_links,
    b.display_order_image_links,
    b.urls_3d_snippets,
    b.image_category_3d_snippets,
    b.contributor_3d_snippets,
    b.disclaimer_3d_snippets,
    b.license_3d_snippets,
    b.display_order_3d_snippets,
    b.longitude,
    b.latitude,
    b.collector_ids,
    b.collectors,
    b.donator_ids,
    b.donators,
    b.localities,
    b.family,
    b.t_order,
    b.class,
    b.specimen_count_min,
    b.specimen_count_males_min,
    b.specimen_count_females_min,
    b.collection_code_full_path,
    b.collection_name_full_path
   FROM b
  GROUP BY b.uuid, b.ids, b.code_display, b.taxon_paths, b.taxon_ref, b.taxon_name, b.sex, b.history_identification, b.gtu_others_tag_value, b.gtu_from_date, b.gtu_from_date_mask, b.gtu_to_date, b.gtu_to_date_mask, b.fct_mask_date, b.date_from_display, b.date_to_display, b.coll_type, b.urls_thumbnails, b.image_category_thumbnails, b.contributor_thumbnails, b.disclaimer_thumbnails, b.license_thumbnails, b.display_order_thumbnails, b.urls_image_links, b.image_category_image_links, b.contributor_image_links, b.disclaimer_image_links, b.license_image_links, b.display_order_image_links, b.urls_3d_snippets, b.image_category_3d_snippets, b.contributor_3d_snippets, b.disclaimer_3d_snippets, b.license_3d_snippets, b.display_order_3d_snippets, b.longitude, b.latitude, b.collector_ids, b.collectors, b.donator_ids, b.donators, b.localities, b.family, b.t_order, b.class, b.specimen_count_min, b.specimen_count_males_min, b.specimen_count_females_min, b.collection_code_full_path, b.collection_name_full_path
  WITH NO DATA;


ALTER TABLE darwin2.mv_specimen_public OWNER TO darwin2;

--
-- TOC entry 230 (class 1259 OID 467103230)
-- Name: src_taxonomy; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.src_taxonomy (
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    id integer,
    extinct boolean,
    sensitive_info_withheld boolean,
    is_reference_taxonomy boolean,
    metadata_ref integer,
    taxonomy_creation_date timestamp without time zone,
    import_ref integer,
    cites boolean
);


ALTER TABLE darwin2.src_taxonomy OWNER TO darwin2;

--
-- TOC entry 244 (class 1259 OID 467106914)
-- Name: mv_taxa_in_specimens; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_taxa_in_specimens AS
 SELECT (a.taxon)::integer AS taxon,
    a.collection_ref,
    a.collection_path,
    (((a.collection_path)::text || ((a.collection_ref)::character varying)::text) || '/'::text) AS full_collection_path
   FROM (( SELECT unnest(string_to_array((((specimens.taxon_path)::text || ((specimens.taxon_ref)::character varying)::text) || '/'::text), '/'::text)) AS taxon,
            specimens.collection_ref,
            specimens.collection_path
           FROM (darwin2.specimens specimens
             JOIN darwin2.collections ON ((specimens.collection_ref = collections.id)))
          WHERE (collections.is_public = true)) a
     JOIN darwin2.src_taxonomy ON (((a.taxon)::integer = src_taxonomy.id)))
  WHERE ((a.taxon <> ''::text) AND (COALESCE(src_taxonomy.sensitive_info_withheld, false) = false))
  WITH NO DATA;


ALTER TABLE darwin2.mv_taxa_in_specimens OWNER TO darwin2;

--
-- TOC entry 245 (class 1259 OID 467106930)
-- Name: mv_taxonomy_by_collection; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_taxonomy_by_collection AS
 SELECT DISTINCT src_taxonomy.name,
    src_taxonomy.name_indexed,
    src_taxonomy.level_ref,
    src_taxonomy.status,
    src_taxonomy.local_naming,
    src_taxonomy.color,
    src_taxonomy.path,
    src_taxonomy.parent_ref,
    src_taxonomy.id,
    src_taxonomy.extinct,
    src_taxonomy.sensitive_info_withheld,
    src_taxonomy.is_reference_taxonomy,
    src_taxonomy.metadata_ref,
    src_taxonomy.taxonomy_creation_date,
    src_taxonomy.import_ref,
    src_taxonomy.cites,
    mv_taxa_in_specimens.collection_ref,
    mv_taxa_in_specimens.collection_path,
    mv_taxa_in_specimens.full_collection_path
   FROM (darwin2.src_taxonomy
     JOIN darwin2.mv_taxa_in_specimens ON ((src_taxonomy.id = mv_taxa_in_specimens.taxon)))
  WHERE (COALESCE(src_taxonomy.sensitive_info_withheld, false) = false)
  WITH NO DATA;


ALTER TABLE darwin2.mv_taxonomy_by_collection OWNER TO darwin2;

--
-- TOC entry 229 (class 1259 OID 467103224)
-- Name: specimens_stable_ids; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.specimens_stable_ids (
    id bigint,
    specimen_ref bigint,
    original_id bigint,
    uuid uuid,
    doi character varying,
    specimen_fk bigint
);


ALTER TABLE darwin2.specimens_stable_ids OWNER TO darwin2;

--
-- TOC entry 232 (class 1259 OID 467103242)
-- Name: template_classifications; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.template_classifications (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying DEFAULT 'valid'::character varying NOT NULL,
    local_naming boolean DEFAULT false NOT NULL,
    color character varying,
    path character varying DEFAULT '/'::character varying NOT NULL,
    parent_ref integer
);


ALTER TABLE darwin2.template_classifications OWNER TO darwin2;

--
-- TOC entry 233 (class 1259 OID 467103251)
-- Name: template_people; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.template_people (
    is_physical boolean DEFAULT true NOT NULL,
    sub_type character varying,
    formated_name character varying NOT NULL,
    formated_name_indexed character varying NOT NULL,
    formated_name_unique character varying NOT NULL,
    title character varying DEFAULT ''::character varying NOT NULL,
    family_name character varying NOT NULL,
    given_name character varying,
    additional_names character varying,
    birth_date_mask integer DEFAULT 0 NOT NULL,
    birth_date date DEFAULT '0001-01-01'::date NOT NULL,
    gender character(1),
    CONSTRAINT genders_chk CHECK ((gender = ANY (ARRAY['M'::bpchar, 'F'::bpchar])))
);


ALTER TABLE darwin2.template_people OWNER TO darwin2;

--
-- TOC entry 234 (class 1259 OID 467103262)
-- Name: tmv_collections_full_path_recursive_public; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.tmv_collections_full_path_recursive_public (
    id integer,
    collection_type character varying,
    code character varying,
    name character varying,
    name_indexed character varying,
    institution_ref integer,
    main_manager_ref integer,
    staff_ref integer,
    parent_ref integer,
    path character varying,
    code_auto_increment boolean,
    code_last_value integer,
    code_prefix character varying,
    code_prefix_separator character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    code_specimen_duplicate boolean,
    is_public boolean,
    code_mask character varying,
    loan_auto_increment boolean,
    loan_last_value integer,
    code_ai_inherit boolean,
    code_auto_increment_for_insert_only boolean,
    nagoya character varying,
    allow_duplicates boolean,
    code_full_path character varying,
    name_full_path character varying,
    name_indexed_full_path character varying
);


ALTER TABLE darwin2.tmv_collections_full_path_recursive_public OWNER TO darwin2;

--
-- TOC entry 235 (class 1259 OID 467103268)
-- Name: tmv_search_public_specimen; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.tmv_search_public_specimen (
    id integer,
    uuid uuid,
    code_prefix character varying,
    code character varying,
    code_num integer,
    code_display text,
    full_code_indexed character varying,
    taxon_path character varying,
    taxon_ref integer,
    collection_ref integer,
    gtu_country_tag_indexed character varying[],
    gtu_country_tag_value text,
    localities_indexed character varying[],
    gtu_others_tag_value character varying,
    family character varying,
    taxon_name character varying,
    sex character varying,
    collector_ids integer[],
    donator_ids integer[],
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    coll_type character varying,
    collection_path character varying,
    specimen_count_min integer,
    latitude double precision,
    longitude double precision,
    formated_date text,
    code_concat character varying,
    geom public.geometry
);


ALTER TABLE darwin2.tmv_search_public_specimen OWNER TO darwin2;

--
-- TOC entry 236 (class 1259 OID 467103274)
-- Name: tmv_specimen_public; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.tmv_specimen_public (
    uuid uuid,
    ids integer[],
    code_display text,
    taxon_paths character varying[],
    taxon_ref integer[],
    taxon_name character varying[],
    sex character varying,
    history_identification text[],
    gtu_country_tag_value text,
    gtu_others_tag_value character varying,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    fct_mask_date text,
    date_from_display text,
    date_to_display text,
    coll_type character varying,
    urls_thumbnails text,
    image_category_thumbnails text,
    contributor_thumbnails text,
    disclaimer_thumbnails text,
    license_thumbnails text,
    display_order_thumbnails text,
    urls_image_links text,
    image_category_image_links text,
    contributor_image_links text,
    disclaimer_image_links text,
    license_image_links text,
    display_order_image_links text,
    urls_3d_snippets text,
    image_category_3d_snippets text,
    contributor_3d_snippets text,
    disclaimer_3d_snippets text,
    license_3d_snippets text,
    display_order_3d_snippets text,
    longitude double precision,
    latitude double precision,
    collector_ids integer[],
    collectors character varying[],
    donator_ids integer[],
    donators character varying[],
    localities text[],
    family text,
    t_order text,
    class text,
    specimen_count_min integer,
    specimen_count_males_min integer,
    specimen_count_females_min integer,
    collection_code_full_path character varying,
    collection_name_full_path character varying
);


ALTER TABLE darwin2.tmv_specimen_public OWNER TO darwin2;

--
-- TOC entry 237 (class 1259 OID 467103280)
-- Name: tmv_taxonomy_by_collection; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.tmv_taxonomy_by_collection (
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    id integer,
    extinct boolean,
    sensitive_info_withheld boolean,
    is_reference_taxonomy boolean,
    metadata_ref integer,
    taxonomy_creation_date timestamp without time zone,
    import_ref integer,
    cites boolean,
    collection_ref integer,
    collection_path character varying,
    full_collection_path text
);


ALTER TABLE darwin2.tmv_taxonomy_by_collection OWNER TO darwin2;

--
-- TOC entry 238 (class 1259 OID 467103286)
-- Name: users; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.users (
    id integer NOT NULL,
    db_user_type smallint DEFAULT 1 NOT NULL,
    people_id integer,
    created_at timestamp without time zone DEFAULT now(),
    selected_lang character varying DEFAULT 'en'::character varying NOT NULL,
    user_ip character varying
)
INHERITS (darwin2.template_people);


ALTER TABLE darwin2.users OWNER TO darwin2;

--
-- TOC entry 239 (class 1259 OID 467103300)
-- Name: users_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE darwin2.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_id_seq OWNER TO darwin2;

--
-- TOC entry 4300 (class 0 OID 0)
-- Dependencies: 239
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE darwin2.users_id_seq OWNED BY darwin2.users.id;


--
-- TOC entry 240 (class 1259 OID 467103302)
-- Name: users_tracking; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.users_tracking (
    id integer NOT NULL,
    referenced_relation character varying,
    record_id integer,
    user_ref integer,
    action character varying,
    old_value public.hstore,
    new_value public.hstore,
    modification_date_time timestamp without time zone
);


ALTER TABLE darwin2.users_tracking OWNER TO darwin2;

--
-- TOC entry 246 (class 1259 OID 467113440)
-- Name: v_gbif_fishnet2_ichtyo_stats; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_gbif_fishnet2_ichtyo_stats AS
 WITH b AS (
         SELECT specimens.gtu_country_tag_value,
            specimens.id,
            specimens.type,
            specimens.specimen_count_min,
            darwin2.fct_rmca_sort_taxon_get_parent_level_text(specimens.taxon_ref, 28) AS orders
           FROM darwin2.specimens
          WHERE ((specimens.collection_ref = 6) AND (COALESCE(specimens.valid_label, true) = true))
        ), c AS (
         SELECT b.gtu_country_tag_value,
            count(*) AS count_all,
            sum(b.specimen_count_min) AS count_spec,
            string_agg(DISTINCT (b.type)::text, ';'::text) AS agg_spec,
            string_agg(DISTINCT (b.orders)::text, ';'::text) AS orders
           FROM b
          WHERE (((b.type)::text = 'specimen'::text) OR ((b.type)::text = ''::text))
          GROUP BY b.gtu_country_tag_value
        ), d AS (
         SELECT b.gtu_country_tag_value,
            count(*) AS count_type,
            sum(b.specimen_count_min) AS count_spec_type,
            string_agg(DISTINCT (b.type)::text, ';'::text) AS agg_types,
            string_agg(DISTINCT (b.orders)::text, '; '::text) AS type_orders
           FROM b
          WHERE (((b.type)::text <> 'specimen'::text) AND ((b.type)::text <> ''::text))
          GROUP BY b.gtu_country_tag_value
        )
 SELECT c.gtu_country_tag_value,
    sum(c.count_all) AS nb_records,
    sum(d.count_type) AS nb_record_types,
    sum(c.count_spec) AS nb_physical_specimes,
    sum(d.count_spec_type) AS nb_physical_type_specimens,
    d.agg_types,
    c.orders,
    d.type_orders
   FROM (c
     LEFT JOIN d ON (((c.gtu_country_tag_value)::text = (d.gtu_country_tag_value)::text)))
  GROUP BY c.gtu_country_tag_value, c.agg_spec, d.agg_types, c.orders, d.type_orders;


ALTER TABLE darwin2.v_gbif_fishnet2_ichtyo_stats OWNER TO darwin2;

--
-- TOC entry 241 (class 1259 OID 467103308)
-- Name: v_taxa_in_specimens; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_taxa_in_specimens AS
 SELECT (a.taxon)::integer AS taxon,
    a.collection_ref,
    a.collection_path,
    (((a.collection_path)::text || ((a.collection_ref)::character varying)::text) || '/'::text) AS full_collection_path
   FROM (( SELECT unnest(string_to_array((((specimens.taxon_path)::text || ((specimens.taxon_ref)::character varying)::text) || '/'::text), '/'::text)) AS taxon,
            specimens.collection_ref,
            specimens.collection_path
           FROM (darwin2.specimens specimens
             JOIN darwin2.collections ON ((specimens.collection_ref = collections.id)))
          WHERE (collections.is_public = true)) a
     JOIN darwin2.src_taxonomy taxonomy ON (((a.taxon)::integer = taxonomy.id)))
  WHERE ((a.taxon <> ''::text) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false));


ALTER TABLE darwin2.v_taxa_in_specimens OWNER TO darwin2;

--
-- TOC entry 242 (class 1259 OID 467103313)
-- Name: v_mv_taxonomy; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_taxonomy AS
 SELECT DISTINCT taxonomy.name,
    taxonomy.name_indexed,
    taxonomy.level_ref,
    taxonomy.status,
    taxonomy.local_naming,
    taxonomy.color,
    taxonomy.path,
    taxonomy.parent_ref,
    taxonomy.id,
    taxonomy.extinct,
    taxonomy.sensitive_info_withheld,
    taxonomy.is_reference_taxonomy,
    taxonomy.metadata_ref,
    taxonomy.taxonomy_creation_date,
    taxonomy.import_ref,
    taxonomy.cites
   FROM (darwin2.src_taxonomy taxonomy
     JOIN darwin2.v_taxa_in_specimens ON ((taxonomy.id = v_taxa_in_specimens.taxon)))
  WHERE (COALESCE(taxonomy.sensitive_info_withheld, false) = false);


ALTER TABLE darwin2.v_mv_taxonomy OWNER TO darwin2;

--
-- TOC entry 243 (class 1259 OID 467103318)
-- Name: v_rdf_view; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.v_rdf_view AS
 SELECT DISTINCT specimens.uuid,
    specimens.code_display AS "SpecimenID",
    ('http://darwinweb.africamuseum.be/darwin/rdf/'::text || specimens.code_display) AS "RefUri",
    ('http://darwinweb.africamuseum.be/'::text || specimens.code_display) AS "ObjectUri",
    btrim(((specimens.code_display || ' '::text) || array_to_string(array_agg(DISTINCT specimens.taxon_name), ', '::text))) AS "Title",
    btrim(((specimens.code_display || ' '::text) || array_to_string(array_agg(DISTINCT specimens.taxon_name), ', '::text))) AS "TitleDescription",
    btrim(array_to_string(( SELECT array_agg(people.formated_name) AS array_agg
           FROM darwin2.people
          WHERE (people.id = ANY (specimens.collector_ids))), ', '::text)) AS collector,
    (NULLIF(darwin2.fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask), 'xxxx-xx-xx'::text) || COALESCE(('-'::text || NULLIF(darwin2.fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask), 'xxxx-xx-xx'::text)))) AS "CollectionDate",
    ('http://darwinweb.africamuseum.be/'::text || specimens.code_display) AS "ObjectURI",
    specimens.modification_date_time AS modified,
    'specimens'::text AS "BaseOfRecord",
    'RMCA'::text AS "InstitutionCode",
    specimens.collection_name AS "CollectionName",
    specimens.code_display AS "CatalogNumber",
    darwin2.getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], 'family'::character varying) AS "Family",
    darwin2.getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], 'genus'::character varying) AS "Genus",
    darwin2.getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], '"species"'::character varying) AS "SpecificEpithet",
    specimens.taxon_name AS "ScientificName",
    NULL::character varying AS "HigherGeography",
    specimens.gtu_country_tag_value AS "Country",
    btrim(replace(replace((specimens.gtu_others_tag_value)::text, (specimens.gtu_country_tag_value)::text, ''::text), ';'::text, ''::text)) AS "Locality",
    specimens.urls_thumbnails AS "Image"
   FROM ( SELECT specimens_stable_ids.uuid,
            specimens_1.id,
            (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text) AS code_display,
            codes.full_code_indexed,
            specimens_1.taxon_path,
            specimens_1.taxon_ref,
            specimens_1.collection_ref,
            specimens_1.collection_name,
            specimens_1.gtu_country_tag_indexed,
            specimens_1.gtu_country_tag_value,
            specimens_1.gtu_others_tag_indexed AS localities_indexed,
            specimens_1.gtu_others_tag_value,
            specimens_1.taxon_name,
            specimens_1.spec_coll_ids AS collector_ids,
            specimens_1.spec_don_sel_ids AS donator_ids,
            specimens_1.gtu_from_date,
            specimens_1.gtu_from_date_mask,
            specimens_1.gtu_to_date,
            specimens_1.gtu_to_date_mask,
            specimens_1.type AS coll_type,
            unnest(
                CASE
                    WHEN (specimens_1.gtu_country_tag_indexed IS NOT NULL) THEN specimens_1.gtu_country_tag_indexed
                    ELSE NULL::character varying[]
                END) AS country_unnest,
            ext_links_thumbnails.url AS urls_thumbnails,
            ext_links_thumbnails.category AS image_category_thumbnails,
            ext_links_thumbnails.contributor AS contributor_thumbnails,
            ext_links_thumbnails.disclaimer AS disclaimer_thumbnails,
            ext_links_thumbnails.license AS license_thumbnails,
            ext_links_thumbnails.display_order AS display_order_thumbnails,
            ext_links_image_links.url AS urls_image_links,
            ext_links_image_links.category AS image_category_image_links,
            ext_links_image_links.contributor AS contributor_image_links,
            ext_links_image_links.disclaimer AS disclaimer_image_links,
            ext_links_image_links.license AS license_image_links,
            ext_links_image_links.display_order AS display_order_image_links,
            ext_links_3d_snippets.url AS urls_3d_snippets,
            ext_links_3d_snippets.category AS image_category_3d_snippets,
            ext_links_3d_snippets.contributor AS contributor_3d_snippets,
            ext_links_3d_snippets.disclaimer AS disclaimer_3d_snippets,
            ext_links_3d_snippets.license AS license_3d_snippets,
            ext_links_3d_snippets.display_order AS display_order_3d_snippets,
            specimens_1.gtu_location[0] AS latitude,
            specimens_1.gtu_location[1] AS longitude,
            identifications.notion_date AS identification_date,
            identifications.notion_date_mask AS identification_date_mask,
            (COALESCE((darwin2.fct_mask_date(identifications.notion_date, identifications.notion_date_mask) || ': '::text), ''::text) || (specimens_1.taxon_name)::text) AS history,
            specimens_1.gtu_ref,
            tags.group_type,
            tags.sub_group_type,
            tags.tag,
            (((((tags.group_type)::text || '-'::text) || (tags.sub_group_type)::text) || ':'::text) || (tags.tag)::text) AS tag_locality,
            users_tracking.modification_date_time
           FROM ((((((((darwin2.specimens specimens_1
             JOIN darwin2.specimens_stable_ids ON ((specimens_1.id = specimens_stable_ids.specimen_ref)))
             LEFT JOIN darwin2.codes ON ((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text) AND (specimens_1.id = codes.record_id))))
             LEFT JOIN darwin2.ext_links ext_links_thumbnails ON (((specimens_1.id = ext_links_thumbnails.record_id) AND ((ext_links_thumbnails.referenced_relation)::text = 'specimens'::text) AND ((ext_links_thumbnails.category)::text = 'thumbnail'::text))))
             LEFT JOIN darwin2.ext_links ext_links_image_links ON (((specimens_1.id = ext_links_image_links.record_id) AND ((ext_links_image_links.referenced_relation)::text = 'specimens'::text) AND ((ext_links_image_links.category)::text = 'image_link'::text))))
             LEFT JOIN darwin2.ext_links ext_links_3d_snippets ON (((specimens_1.id = ext_links_3d_snippets.record_id) AND ((ext_links_3d_snippets.referenced_relation)::text = 'specimens'::text) AND ((ext_links_3d_snippets.category)::text = 'html_3d_snippet'::text))))
             LEFT JOIN darwin2.identifications ON ((((identifications.referenced_relation)::text = 'specimens'::text) AND (specimens_1.id = identifications.record_id) AND ((identifications.notion_concerned)::text = 'taxonomy'::text))))
             LEFT JOIN darwin2.tags ON ((specimens_1.gtu_ref = tags.gtu_ref)))
             LEFT JOIN ( SELECT users_tracking_1.modification_date_time,
                    users_tracking_1.record_id,
                    users_tracking_1.referenced_relation
                   FROM darwin2.users_tracking users_tracking_1
                  ORDER BY users_tracking_1.id DESC
                 LIMIT 1) users_tracking ON (((specimens_1.id = users_tracking.record_id) AND ((users_tracking.referenced_relation)::text = 'specimens'::text))))
          ORDER BY tags.group_ref) specimens
  GROUP BY specimens.uuid, specimens.code_display, specimens.collection_name, specimens.gtu_country_tag_value, specimens.gtu_others_tag_value, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.coll_type, specimens.longitude, specimens.latitude, specimens.collector_ids, specimens.donator_ids, specimens.modification_date_time, specimens.urls_thumbnails, specimens.taxon_name
  WITH NO DATA;


ALTER TABLE darwin2.v_rdf_view OWNER TO darwin2;

--
-- TOC entry 249 (class 1259 OID 467179991)
-- Name: v_ws_geo_ref; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_ws_geo_ref AS
 WITH a AS (
         SELECT tags.gtu_ref
           FROM darwin2.tags
        ), country AS (
         SELECT a_1.gtu_ref,
            tags.tag AS country
           FROM (a a_1
             JOIN darwin2.tags ON ((a_1.gtu_ref = tags.gtu_ref)))
          WHERE (lower((tags.sub_group_type)::text) = 'country'::text)
        ), continent AS (
         SELECT a_1.gtu_ref,
            tags.tag AS continent
           FROM (a a_1
             JOIN darwin2.tags ON ((a_1.gtu_ref = tags.gtu_ref)))
          WHERE (lower((tags.sub_group_type)::text) = 'continent'::text)
        ), others_v AS (
         SELECT a_1.gtu_ref,
            tags.sub_group_type,
            tags.tag
           FROM (a a_1
             JOIN darwin2.tags ON ((a_1.gtu_ref = tags.gtu_ref)))
          WHERE ((lower((tags.sub_group_type)::text) <> 'continent'::text) AND (lower((tags.sub_group_type)::text) <> 'country'::text))
        ), specimens AS (
         WITH tmp AS (
                 SELECT a_1.gtu_ref,
                    specimens_1.collection_ref,
                    v_collections_full_path_recursive.name_full_path,
                    count(specimens_1.id) AS nb_records,
                    sum(specimens_1.specimen_count_min) AS specimen_count_min,
                    sum(specimens_1.specimen_count_max) AS specimen_count_max
                   FROM ((a a_1
                     LEFT JOIN darwin2.specimens specimens_1 ON ((a_1.gtu_ref = specimens_1.gtu_ref)))
                     LEFT JOIN darwin2.v_collections_full_path_recursive ON ((specimens_1.collection_ref = v_collections_full_path_recursive.id)))
                  GROUP BY a_1.gtu_ref, specimens_1.collection_ref, v_collections_full_path_recursive.name_full_path
                )
         SELECT tmp.gtu_ref,
            json_object_agg(COALESCE(tmp.name_full_path, ''::character varying), tmp.nb_records) AS nb_records,
            json_object_agg(COALESCE(tmp.name_full_path, ''::character varying), tmp.specimen_count_min) AS specimen_count_min,
            json_object_agg(COALESCE(tmp.name_full_path, ''::character varying), tmp.specimen_count_max) AS specimen_count_max
           FROM tmp
          GROUP BY tmp.gtu_ref
        )
 SELECT a.gtu_ref,
    gtu.code AS station_number,
    string_agg(DISTINCT (continent.continent)::text, '; '::text) AS continent,
    string_agg(DISTINCT (country.country)::text, '; '::text) AS country,
    string_agg(DISTINCT (((others_v.sub_group_type)::text || ':'::text) || (COALESCE(others_v.tag, ''::character varying))::text), '; '::text) AS tag,
    gtu.latitude,
    gtu.longitude,
    gtu.latitude_dms_degree,
    gtu.latitude_dms_minutes,
    gtu.latitude_dms_seconds,
        CASE
            WHEN (gtu.latitude_dms_degree IS NULL) THEN NULL::text
            WHEN (gtu.latitude_dms_direction < 0) THEN 'S'::text
            ELSE 'N'::text
        END AS latitude_dms_direction,
    gtu.longitude_dms_degree,
    gtu.longitude_dms_minutes,
    gtu.longitude_dms_seconds,
        CASE
            WHEN (gtu.longitude_dms_degree IS NULL) THEN NULL::text
            WHEN (gtu.longitude_dms_direction < 0) THEN 'W'::text
            ELSE 'E'::text
        END AS longitude_dms_direction,
    (specimens.nb_records)::character varying AS nb_records,
    (specimens.specimen_count_min)::character varying AS specimen_count_min,
    (specimens.specimen_count_max)::character varying AS specimen_count_max
   FROM (((((a
     LEFT JOIN continent ON ((a.gtu_ref = continent.gtu_ref)))
     LEFT JOIN country ON ((a.gtu_ref = country.gtu_ref)))
     LEFT JOIN others_v ON ((a.gtu_ref = others_v.gtu_ref)))
     LEFT JOIN darwin2.gtu ON ((a.gtu_ref = gtu.id)))
     LEFT JOIN specimens ON ((a.gtu_ref = specimens.gtu_ref)))
  GROUP BY a.gtu_ref, gtu.code, gtu.latitude, gtu.longitude, gtu.latitude_dms_degree, gtu.latitude_dms_minutes, gtu.latitude_dms_seconds, gtu.latitude_dms_direction, gtu.longitude_dms_degree, gtu.longitude_dms_minutes, gtu.longitude_dms_seconds, gtu.longitude_dms_direction, (specimens.nb_records)::character varying, (specimens.specimen_count_min)::character varying, (specimens.specimen_count_max)::character varying;


ALTER TABLE darwin2.v_ws_geo_ref OWNER TO darwin2;

--
-- TOC entry 4030 (class 2604 OID 467260996)
-- Name: classification_synonymies id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies ALTER COLUMN id SET DEFAULT nextval('darwin2.classification_synonymies_id_seq'::regclass);


--
-- TOC entry 4022 (class 2604 OID 467103326)
-- Name: users is_physical; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN is_physical SET DEFAULT true;


--
-- TOC entry 4023 (class 2604 OID 467103327)
-- Name: users title; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN title SET DEFAULT ''::character varying;


--
-- TOC entry 4024 (class 2604 OID 467103328)
-- Name: users birth_date_mask; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN birth_date_mask SET DEFAULT 0;


--
-- TOC entry 4025 (class 2604 OID 467103329)
-- Name: users birth_date; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN birth_date SET DEFAULT '0001-01-01'::date;


--
-- TOC entry 4026 (class 2604 OID 467103330)
-- Name: users id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN id SET DEFAULT nextval('darwin2.users_id_seq'::regclass);


--
-- TOC entry 4045 (class 2606 OID 467103332)
-- Name: country_cleaning pk_country_cleaning; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.country_cleaning
    ADD CONSTRAINT pk_country_cleaning PRIMARY KEY (original_name);


--
-- TOC entry 4117 (class 2606 OID 467261005)
-- Name: classification_synonymies pk_synonym_id; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies
    ADD CONSTRAINT pk_synonym_id PRIMARY KEY (id);


--
-- TOC entry 4111 (class 2606 OID 467103334)
-- Name: users_tracking pk_user_tracking; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users_tracking
    ADD CONSTRAINT pk_user_tracking PRIMARY KEY (id);


--
-- TOC entry 4104 (class 2606 OID 467103336)
-- Name: users pk_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users
    ADD CONSTRAINT pk_users PRIMARY KEY (id);


--
-- TOC entry 4119 (class 2606 OID 467261007)
-- Name: classification_synonymies unq_synonym; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies
    ADD CONSTRAINT unq_synonym UNIQUE (referenced_relation, record_id, group_id);


--
-- TOC entry 4106 (class 2606 OID 467103338)
-- Name: users unq_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users
    ADD CONSTRAINT unq_users UNIQUE (is_physical, gender, formated_name_unique, birth_date, birth_date_mask);


--
-- TOC entry 4040 (class 1259 OID 467103339)
-- Name: codes_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX codes_record_id_idx ON darwin2.codes USING btree (record_id);


--
-- TOC entry 4041 (class 1259 OID 467103340)
-- Name: codes_record_id_referenced_relation_code_category_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX codes_record_id_referenced_relation_code_category_idx ON darwin2.codes USING btree (record_id, referenced_relation, code_category);


--
-- TOC entry 4046 (class 1259 OID 467103341)
-- Name: ext_links_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX ext_links_record_id_idx ON darwin2.ext_links USING btree (record_id);


--
-- TOC entry 4047 (class 1259 OID 467103342)
-- Name: ext_links_record_id_referenced_relation_category_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX ext_links_record_id_referenced_relation_category_idx ON darwin2.ext_links USING btree (record_id, referenced_relation, category);


--
-- TOC entry 4052 (class 1259 OID 467103343)
-- Name: identifications_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX identifications_record_id_idx ON darwin2.identifications USING btree (record_id);


--
-- TOC entry 4053 (class 1259 OID 467103344)
-- Name: identifications_record_id_referenced_relation_notion_concer_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX identifications_record_id_referenced_relation_notion_concer_idx ON darwin2.identifications USING btree (record_id, referenced_relation, notion_concerned);


--
-- TOC entry 4113 (class 1259 OID 467261008)
-- Name: idx_classification_synonymies_grouping; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_grouping ON darwin2.classification_synonymies USING btree (group_id, is_basionym);


--
-- TOC entry 4114 (class 1259 OID 467261009)
-- Name: idx_classification_synonymies_order_by; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_order_by ON darwin2.classification_synonymies USING btree (group_name, order_by);


--
-- TOC entry 4115 (class 1259 OID 467261010)
-- Name: idx_classification_synonymies_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_referenced_record ON darwin2.classification_synonymies USING btree (referenced_relation, record_id, group_id);


--
-- TOC entry 4042 (class 1259 OID 467103345)
-- Name: idx_collections_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_collections_parent_ref ON darwin2.collections USING btree (parent_ref);


--
-- TOC entry 4054 (class 1259 OID 467103346)
-- Name: idx_darwin_flat_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_darwin_flat_gtu_code ON darwin2.specimens USING gin (gtu_code public.gin_trgm_ops);


--
-- TOC entry 4048 (class 1259 OID 467103347)
-- Name: idx_gin_gtu_tags_values; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_gtu_tags_values ON darwin2.gtu USING gin (tag_values_indexed);


--
-- TOC entry 4055 (class 1259 OID 467103348)
-- Name: idx_gin_specimens_gtu_country_tag_indexed_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_gtu_country_tag_indexed_indexed ON darwin2.specimens USING gin (gtu_country_tag_indexed);


--
-- TOC entry 4056 (class 1259 OID 467103349)
-- Name: idx_gin_specimens_gtu_tag_values_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_gtu_tag_values_indexed ON darwin2.specimens USING gin (gtu_tag_values_indexed);


--
-- TOC entry 4057 (class 1259 OID 467103350)
-- Name: idx_gin_specimens_spec_coll_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_coll_ids ON darwin2.specimens USING gin (spec_coll_ids);


--
-- TOC entry 4058 (class 1259 OID 467103351)
-- Name: idx_gin_specimens_spec_don_sel_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_don_sel_ids ON darwin2.specimens USING gin (spec_don_sel_ids);


--
-- TOC entry 4059 (class 1259 OID 467103352)
-- Name: idx_gin_specimens_spec_ident_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_ident_ids ON darwin2.specimens USING gin (spec_ident_ids);


--
-- TOC entry 4060 (class 1259 OID 467103353)
-- Name: idx_gin_trgm_specimens_expedition_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_expedition_name_indexed ON darwin2.specimens USING gin (expedition_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4061 (class 1259 OID 467103354)
-- Name: idx_gin_trgm_specimens_ig_num; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_ig_num ON darwin2.specimens USING gin (ig_num_indexed public.gin_trgm_ops);


--
-- TOC entry 4062 (class 1259 OID 467103355)
-- Name: idx_gin_trgm_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_taxon_name_indexed ON darwin2.specimens USING gin (taxon_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4063 (class 1259 OID 467103356)
-- Name: idx_gin_trgm_specimens_taxon_path; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_taxon_path ON darwin2.specimens USING gin (taxon_path public.gin_trgm_ops);


--
-- TOC entry 4095 (class 1259 OID 467103357)
-- Name: idx_gin_trgm_taxonomy_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_taxonomy_name_indexed ON darwin2.taxonomy USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 4096 (class 1259 OID 467103358)
-- Name: idx_gin_trgm_taxonomy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_taxonomy_naming ON darwin2.taxonomy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4064 (class 1259 OID 467103359)
-- Name: idx_gist_specimens_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gist_specimens_gtu_location ON darwin2.specimens USING gist (gtu_location);


--
-- TOC entry 4049 (class 1259 OID 467103360)
-- Name: idx_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gtu_code ON darwin2.gtu USING btree (code);


--
-- TOC entry 4050 (class 1259 OID 467103361)
-- Name: idx_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gtu_location ON darwin2.gtu USING gist (location);


--
-- TOC entry 4065 (class 1259 OID 467103362)
-- Name: idx_spec_family; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_spec_family ON darwin2.specimens USING btree (family);


--
-- TOC entry 4066 (class 1259 OID 467103363)
-- Name: idx_specimens_chrono_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_chrono_ref ON darwin2.specimens USING btree (chrono_ref) WHERE (chrono_ref <> 0);


--
-- TOC entry 4067 (class 1259 OID 467103364)
-- Name: idx_specimens_collection_is_public; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_collection_is_public ON darwin2.specimens USING btree (collection_is_public);


--
-- TOC entry 4068 (class 1259 OID 467103365)
-- Name: idx_specimens_collection_name; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_collection_name ON darwin2.specimens USING btree (collection_name);


--
-- TOC entry 4069 (class 1259 OID 467103366)
-- Name: idx_specimens_expedition_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_expedition_ref ON darwin2.specimens USING btree (expedition_ref) WHERE (expedition_ref <> 0);


--
-- TOC entry 4070 (class 1259 OID 467103367)
-- Name: idx_specimens_gtu_from_date; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_from_date ON darwin2.specimens USING btree (gtu_from_date);


--
-- TOC entry 4071 (class 1259 OID 467103368)
-- Name: idx_specimens_gtu_from_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_from_date_mask ON darwin2.specimens USING btree (gtu_from_date_mask);


--
-- TOC entry 4072 (class 1259 OID 467103369)
-- Name: idx_specimens_gtu_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_ref ON darwin2.specimens USING btree (gtu_ref) WHERE (gtu_ref <> 0);


--
-- TOC entry 4073 (class 1259 OID 467103370)
-- Name: idx_specimens_gtu_to_date; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_to_date ON darwin2.specimens USING btree (gtu_to_date);


--
-- TOC entry 4074 (class 1259 OID 467103371)
-- Name: idx_specimens_gtu_to_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_to_date_mask ON darwin2.specimens USING btree (gtu_to_date_mask);


--
-- TOC entry 4075 (class 1259 OID 467103372)
-- Name: idx_specimens_ig_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_ig_ref ON darwin2.specimens USING btree (ig_ref);


--
-- TOC entry 4076 (class 1259 OID 467103373)
-- Name: idx_specimens_litho_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_litho_ref ON darwin2.specimens USING btree (litho_ref) WHERE (litho_ref <> 0);


--
-- TOC entry 4077 (class 1259 OID 467103374)
-- Name: idx_specimens_lithology_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_lithology_ref ON darwin2.specimens USING btree (lithology_ref) WHERE (lithology_ref <> 0);


--
-- TOC entry 4078 (class 1259 OID 467103375)
-- Name: idx_specimens_main_code_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_main_code_indexed ON darwin2.specimens USING btree (main_code_indexed);


--
-- TOC entry 4079 (class 1259 OID 467103376)
-- Name: idx_specimens_mineral_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_mineral_ref ON darwin2.specimens USING btree (mineral_ref) WHERE (mineral_ref <> 0);


--
-- TOC entry 4080 (class 1259 OID 467103377)
-- Name: idx_specimens_rock_form; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_rock_form ON darwin2.specimens USING btree (rock_form);


--
-- TOC entry 4081 (class 1259 OID 467103378)
-- Name: idx_specimens_room; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_room ON darwin2.specimens USING btree (room) WHERE (NOT (room IS NULL));


--
-- TOC entry 4082 (class 1259 OID 467103379)
-- Name: idx_specimens_sex; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_sex ON darwin2.specimens USING btree (sex) WHERE ((sex)::text <> ALL (ARRAY[('undefined'::character varying)::text, ('unknown'::character varying)::text]));


--
-- TOC entry 4083 (class 1259 OID 467103380)
-- Name: idx_specimens_shelf; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_shelf ON darwin2.specimens USING btree (shelf) WHERE (NOT (shelf IS NULL));


--
-- TOC entry 4084 (class 1259 OID 467103381)
-- Name: idx_specimens_social_status; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_social_status ON darwin2.specimens USING btree (social_status) WHERE ((social_status)::text <> 'not applicable'::text);


--
-- TOC entry 4085 (class 1259 OID 467103382)
-- Name: idx_specimens_stage; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_stage ON darwin2.specimens USING btree (stage) WHERE ((stage)::text <> ALL (ARRAY[('undefined'::character varying)::text, ('unknown'::character varying)::text]));


--
-- TOC entry 4086 (class 1259 OID 467103383)
-- Name: idx_specimens_state; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_state ON darwin2.specimens USING btree (state) WHERE ((state)::text <> 'not applicable'::text);


--
-- TOC entry 4087 (class 1259 OID 467103384)
-- Name: idx_specimens_station_visible; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_station_visible ON darwin2.specimens USING btree (station_visible);


--
-- TOC entry 4088 (class 1259 OID 467103385)
-- Name: idx_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_taxon_name_indexed ON darwin2.specimens USING btree (taxon_name_indexed);


--
-- TOC entry 4089 (class 1259 OID 467103386)
-- Name: idx_specimens_taxon_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_taxon_ref ON darwin2.specimens USING btree (taxon_ref) WHERE (taxon_ref <> 0);


--
-- TOC entry 4090 (class 1259 OID 467103387)
-- Name: idx_specimens_type_search; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_type_search ON darwin2.specimens USING btree (type_search) WHERE ((type_search)::text <> 'specimen'::text);


--
-- TOC entry 4097 (class 1259 OID 467103388)
-- Name: idx_taxonomy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_level_ref ON darwin2.taxonomy USING btree (level_ref);


--
-- TOC entry 4098 (class 1259 OID 467103389)
-- Name: idx_taxonomy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_parent_ref ON darwin2.taxonomy USING btree (parent_ref);


--
-- TOC entry 4099 (class 1259 OID 467103390)
-- Name: idx_taxonomy_path; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_path ON darwin2.taxonomy USING btree (path text_pattern_ops);


--
-- TOC entry 4107 (class 1259 OID 467103391)
-- Name: idx_users_tracking_action; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_action ON darwin2.users_tracking USING btree (action);


--
-- TOC entry 4108 (class 1259 OID 467103392)
-- Name: idx_users_tracking_modification_date_time; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_modification_date_time ON darwin2.users_tracking USING btree (modification_date_time DESC);


--
-- TOC entry 4109 (class 1259 OID 467103393)
-- Name: idx_users_tracking_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_user_ref ON darwin2.users_tracking USING btree (user_ref);


--
-- TOC entry 4112 (class 1259 OID 467180121)
-- Name: mv_gtu_tags_flat_tag_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX mv_gtu_tags_flat_tag_idx ON darwin2.mv_gtu_tags_flat USING btree (tag);


--
-- TOC entry 4043 (class 1259 OID 467103394)
-- Name: pk_collections; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_collections ON darwin2.collections USING btree (id);


--
-- TOC entry 4051 (class 1259 OID 467103395)
-- Name: pk_gtu; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_gtu ON darwin2.gtu USING btree (id);


--
-- TOC entry 4091 (class 1259 OID 467103396)
-- Name: pk_specimens; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_specimens ON darwin2.specimens USING btree (id);


--
-- TOC entry 4100 (class 1259 OID 467103397)
-- Name: pk_taxonomy; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_taxonomy ON darwin2.taxonomy USING btree (id);


--
-- TOC entry 4092 (class 1259 OID 467103398)
-- Name: specimens_geom_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX specimens_geom_idx ON darwin2.specimens USING gist (geom);


--
-- TOC entry 4093 (class 1259 OID 467103399)
-- Name: specimens_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_id_idx ON darwin2.specimens USING btree (id);


--
-- TOC entry 4101 (class 1259 OID 467103400)
-- Name: specimens_stable_ids_specimen_ref_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX specimens_stable_ids_specimen_ref_idx ON darwin2.specimens_stable_ids USING btree (specimen_ref);


--
-- TOC entry 4102 (class 1259 OID 467103401)
-- Name: specimens_stable_ids_uuid_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_stable_ids_uuid_idx ON darwin2.specimens_stable_ids USING btree (uuid);


--
-- TOC entry 4094 (class 1259 OID 467103402)
-- Name: specimens_uuid_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_uuid_idx ON darwin2.specimens USING btree (uuid);


--
-- TOC entry 4120 (class 2620 OID 467260990)
-- Name: template_table_record_ref trg_chk_ref_record_template_table_record_ref; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_template_table_record_ref AFTER INSERT OR UPDATE ON darwin2.template_table_record_ref FOR EACH ROW EXECUTE FUNCTION darwin2.fct_chk_referencedrecord();


--
-- TOC entry 4275 (class 0 OID 0)
-- Dependencies: 10
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- TOC entry 4280 (class 0 OID 0)
-- Dependencies: 1084
-- Name: FUNCTION fct_rmca_flush_tables(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_flush_tables() TO darwin2;


--
-- TOC entry 4281 (class 0 OID 0)
-- Dependencies: 1081
-- Name: FUNCTION fct_rmca_refresh_materialized_view(); Type: ACL; Schema: darwin2; Owner: darwin2
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view() TO postgres;


--
-- TOC entry 4282 (class 0 OID 0)
-- Dependencies: 1082
-- Name: FUNCTION fct_rmca_refresh_materialized_view_and_consult_tables(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables() TO darwin2;


--
-- TOC entry 4283 (class 0 OID 0)
-- Dependencies: 1085
-- Name: FUNCTION fct_rmca_refresh_materialized_view_and_consult_tables_after_rep(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() TO darwin2;


--
-- TOC entry 4287 (class 0 OID 0)
-- Dependencies: 254
-- Name: TABLE template_table_record_ref; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.template_table_record_ref FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE darwin2.template_table_record_ref TO darwin2;


--
-- TOC entry 4295 (class 0 OID 0)
-- Dependencies: 256
-- Name: TABLE classification_synonymies; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.classification_synonymies FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE darwin2.classification_synonymies TO darwin2;


--
-- TOC entry 4297 (class 0 OID 0)
-- Dependencies: 216
-- Name: TABLE country_cleaning; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.country_cleaning FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.country_cleaning TO darwin2;


--
-- TOC entry 4298 (class 0 OID 0)
-- Dependencies: 227
-- Name: TABLE v_specimen_public; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.v_specimen_public FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.v_specimen_public TO darwin2;


--
-- TOC entry 4299 (class 0 OID 0)
-- Dependencies: 232
-- Name: TABLE template_classifications; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.template_classifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.template_classifications TO darwin2;


-- Completed on 2023-12-22 16:35:15

--
-- PostgreSQL database dump complete
--

