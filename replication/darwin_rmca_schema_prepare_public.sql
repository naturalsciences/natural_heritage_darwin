--
-- PostgreSQL database dump
--

-- Dumped from database version 12.9 (Ubuntu 12.9-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 15.3

-- Started on 2023-12-22 16:34:14

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
-- TOC entry 14 (class 2615 OID 13523549)
-- Name: clean; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA clean;


ALTER SCHEMA clean OWNER TO darwin2;

--
-- TOC entry 15 (class 2615 OID 13523550)
-- Name: darwin2; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA darwin2;


ALTER SCHEMA darwin2 OWNER TO darwin2;

--
-- TOC entry 16 (class 2615 OID 13523551)
-- Name: drosera_import; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA drosera_import;


ALTER SCHEMA drosera_import OWNER TO darwin2;

--
-- TOC entry 17 (class 2615 OID 13523552)
-- Name: eod; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA eod;


ALTER SCHEMA eod OWNER TO darwin2;

--
-- TOC entry 18 (class 2615 OID 13523553)
-- Name: fdw_113; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA fdw_113;


ALTER SCHEMA fdw_113 OWNER TO darwin2;

--
-- TOC entry 19 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

-- *not* creating schema, since initdb creates it


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 8 (class 3079 OID 13523554)
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA public;


--
-- TOC entry 5340 (class 0 OID 0)
-- Dependencies: 8
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- TOC entry 7 (class 3079 OID 13523565)
-- Name: hstore; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA public;


--
-- TOC entry 5341 (class 0 OID 0)
-- Dependencies: 7
-- Name: EXTENSION hstore; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION hstore IS 'data type for storing sets of (key, value) pairs';


--
-- TOC entry 6 (class 3079 OID 13523690)
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- TOC entry 5342 (class 0 OID 0)
-- Dependencies: 6
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


--
-- TOC entry 5 (class 3079 OID 13523767)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 5343 (class 0 OID 0)
-- Dependencies: 5
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- TOC entry 4 (class 3079 OID 13523804)
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- TOC entry 5344 (class 0 OID 0)
-- Dependencies: 4
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry, geography, and raster spatial types and functions';


--
-- TOC entry 2 (class 3079 OID 13524806)
-- Name: postgres_fdw; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS postgres_fdw WITH SCHEMA darwin2;


--
-- TOC entry 5345 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION postgres_fdw; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgres_fdw IS 'foreign-data wrapper for remote PostgreSQL servers';


--
-- TOC entry 3 (class 3079 OID 13524810)
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- TOC entry 5346 (class 0 OID 0)
-- Dependencies: 3
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- TOC entry 1354 (class 1255 OID 26905213)
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
-- TOC entry 1341 (class 1255 OID 13524821)
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
-- TOC entry 1351 (class 1255 OID 26905211)
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
-- TOC entry 1352 (class 1255 OID 26905212)
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
-- TOC entry 1355 (class 1255 OID 16384919)
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
-- TOC entry 1350 (class 1255 OID 13524822)
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
-- TOC entry 1348 (class 1255 OID 13907817)
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
	with a as (select id from darwin2.src_taxonomy)
	INSERT INTO darwin2.classification_synonymies SELECT fdw_113.classification_synonymies.* FROM fdw_113.classification_synonymies, a
	where referenced_relation='taxonomy' and record_id=a.id ;
	insert into darwin2.users_tracking select * from darwin2.v_users_tracking_public_specimens;
	

return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables() OWNER TO postgres;

--
-- TOC entry 1353 (class 1255 OID 16172026)
-- Name: fct_rmca_refresh_materialized_view_and_consult_tables_after_rep(); Type: FUNCTION; Schema: darwin2; Owner: postgres
--

CREATE FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
begin
	
REFRESH MATERIALIZED VIEW darwin2.mv_collections_full_path_recursive_public;
INSERT into darwin2.taxonomy select * from darwin2.src_taxonomy;
REFRESH MATERIALIZED VIEW darwin2.mv_taxa_in_specimens;
REFRESH MATERIALIZED VIEW darwin2.mv_taxonomy_by_collection;
REFRESH MATERIALIZED VIEW darwin2.mv_search_public_specimen;
REFRESH MATERIALIZED VIEW darwin2.mv_specimen_public;
return true;
end;
$$;


ALTER FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() OWNER TO postgres;

--
-- TOC entry 1342 (class 1255 OID 13524823)
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
-- TOC entry 1357 (class 1255 OID 26957991)
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
-- TOC entry 1356 (class 1255 OID 26957992)
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
-- TOC entry 1343 (class 1255 OID 13524824)
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
-- TOC entry 1344 (class 1255 OID 13524825)
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
-- TOC entry 1345 (class 1255 OID 13524826)
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

--
-- TOC entry 1346 (class 1255 OID 13524827)
-- Name: rmca_migrate_rbins_rmca_align_seq(); Type: FUNCTION; Schema: public; Owner: darwin2
--

CREATE FUNCTION public.rmca_migrate_rbins_rmca_align_seq() RETURNS integer
    LANGUAGE plpgsql
    AS $$
    Declare returned int;
    rec_seq record;
 
   cur_seq  CURSOR FOR SELECT REPLACE(REPLACE(column_default,'nextval(''',''),'''::regclass)','') as seq_name, table_name, column_name from information_schema.columns where column_default like 'nextval%' and table_schema='darwin2' order by table_name;



  metadata_tmp integer;
    BEGIN
       set search_path='darwin2';
    returned:=-1;
    --attention, the index esof specimens in the rbins are bound to some functions
    SET search_path TO darwin2;
      OPEN cur_seq;
 
   LOOP
    -- fetch row into the film
      FETCH cur_seq INTO rec_seq;
    -- exit when no more row to fetch
      EXIT WHEN NOT FOUND;
	RAISE notice E'seq: % \t table:% \ t count', rec_seq.seq_name,rec_seq.table_name ;
       EXECUTE 'SELECT setval(''darwin2.'||rec_seq.seq_name||''', (SELECT MAX('||rec_seq.column_name||')+1 FROM darwin2.'||rec_seq.table_name||') , false);';
 
   END LOOP;
	returned:=0;
      return returned;


      
    END;
    $$;


ALTER FUNCTION public.rmca_migrate_rbins_rmca_align_seq() OWNER TO darwin2;

--
-- TOC entry 1347 (class 1255 OID 13524828)
-- Name: trg_del_dict(); Type: FUNCTION; Schema: public; Owner: darwin2
--

CREATE FUNCTION public.trg_del_dict() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --DELETE
      oldfield = OLD;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into newfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_del_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_del_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
            --ftheeten 2016 09 15
      IF oldfield.referenced_relation='specimens' AND oldfield.code_category='main' THEN
                UPDATE specimens SET main_code_indexed= NULL WHERE id=oldfield.record_id;
      END IF;
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_del_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_del_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_del_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_del_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_del_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_del_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_del_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_del_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_del_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_del_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_del_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_del_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_del_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_del_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_del_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_del_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_del_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_del_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_del_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_del_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_del_in_dict('specimens','col', oldfield.col, newfield.col);
      PERFORM fct_del_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_del_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_del_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_del_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_del_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type, 'container_type' );
      PERFORM fct_del_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type, 'sub_container_type' );

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_del_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_del_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_del_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_del_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_del_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_del_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation, 'referenced_relation' );
      PERFORM fct_del_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type, 'property_type' );
      PERFORM fct_del_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type, 'property_type' );

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_del_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name, 'group_name' );
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION public.trg_del_dict() OWNER TO darwin2;

--
-- TOC entry 1349 (class 1255 OID 13524829)
-- Name: trg_ins_update_dict(); Type: FUNCTION; Schema: public; Owner: darwin2
--

CREATE FUNCTION public.trg_ins_update_dict() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --INSERT
      newfield = NEW;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into oldfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_add_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_add_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
      --ftheeten 2016 09 15
      IF newfield.referenced_relation='specimens' AND newfield.code_category='main' THEN
                UPDATE specimens SET main_code_indexed= fullToIndex(COALESCE(newfield.code_prefix,'') || COALESCE(newfield.code::text,'') || COALESCE(newfield.code_suffix,'') ) WHERE id=newfield.record_id;
      END IF;
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_add_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_add_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_add_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_add_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_add_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_add_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_add_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_add_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_add_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_add_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_add_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_add_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_add_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_add_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_add_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_add_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_add_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_add_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_add_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_add_in_dict('specimens','col', oldfield.col, newfield.col);
      PERFORM fct_add_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_add_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_add_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_add_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_add_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type);

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_add_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_add_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_add_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_add_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_add_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_add_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation);
      PERFORM fct_add_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type);
      PERFORM fct_add_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type);

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_add_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name);

    END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION public.trg_ins_update_dict() OWNER TO darwin2;

--
-- TOC entry 3895 (class 1417 OID 13524830)
-- Name: fdw_113; Type: SERVER; Schema: -; Owner: darwin2
--

CREATE SERVER fdw_113 FOREIGN DATA WRAPPER postgres_fdw OPTIONS (
    dbname 'darwin2',
    host '172.16.11.113',
    port '5432'
);


ALTER SERVER fdw_113 OWNER TO darwin2;

--
-- TOC entry 5351 (class 0 OID 0)
-- Name: USER MAPPING darwin2 SERVER fdw_113; Type: USER MAPPING; Schema: -; Owner: darwin2
--

CREATE USER MAPPING FOR darwin2 SERVER fdw_113 OPTIONS (
    password 'phvisodu$ft',
    "user" 'darwin2'
);


--
-- TOC entry 5352 (class 0 OID 0)
-- Name: USER MAPPING postgres SERVER fdw_113; Type: USER MAPPING; Schema: -; Owner: darwin2
--

CREATE USER MAPPING FOR postgres SERVER fdw_113 OPTIONS (
    password 'fv30714$A',
    "user" 'postgres'
);


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 219 (class 1259 OID 13524833)
-- Name: bck_specimens_crustacae_20210319; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.bck_specimens_crustacae_20210319 (
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
    nagoya character varying
);


ALTER TABLE clean.bck_specimens_crustacae_20210319 OWNER TO darwin2;

--
-- TOC entry 220 (class 1259 OID 13524839)
-- Name: catalogue_people_bck2021; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.catalogue_people_bck2021 (
    referenced_relation character varying,
    record_id integer,
    id integer,
    people_type character varying,
    people_sub_type character varying,
    order_by integer,
    people_ref integer
);


ALTER TABLE clean.catalogue_people_bck2021 OWNER TO darwin2;

--
-- TOC entry 221 (class 1259 OID 13524845)
-- Name: crustacae_2021_clean_date; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.crustacae_2021_clean_date (
    unitid character varying,
    original_collection_date character varying,
    collectionstartday character varying,
    collectionstartmonth character varying,
    collectionstartyear character varying,
    collectionendday character varying,
    collectionendmonth character varying,
    collectionendyear character varying
);


ALTER TABLE clean.crustacae_2021_clean_date OWNER TO darwin2;

--
-- TOC entry 222 (class 1259 OID 13524851)
-- Name: ident_pb_20210125; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.ident_pb_20210125 (
    filename character varying,
    unitid character varying,
    people_role character varying,
    people_name character varying,
    people_fk integer,
    specimen_fk integer,
    id integer,
    specimen_fks integer[],
    imp_id integer,
    unnest_fk integer,
    cat_people_relation character varying,
    cat_people_record_id integer,
    cat_people_id integer,
    people_type character varying,
    people_sub_type character varying,
    people_ref integer,
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
    people_id integer,
    end_date_mask integer,
    end_date date,
    activity_date_from_mask integer,
    activity_date_from date,
    activity_date_to_mask integer,
    activity_date_to date,
    name_formated_indexed character varying,
    import_ref integer,
    ident_relation character varying,
    ident_record_id integer,
    ident_id integer,
    notion_concerned character varying,
    notion_date timestamp without time zone,
    notion_date_mask integer,
    value_defined character varying,
    value_defined_indexed character varying,
    determination_status character varying
);


ALTER TABLE clean.ident_pb_20210125 OWNER TO darwin2;

--
-- TOC entry 223 (class 1259 OID 13524857)
-- Name: identifications_bck2021; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.identifications_bck2021 (
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


ALTER TABLE clean.identifications_bck2021 OWNER TO darwin2;

--
-- TOC entry 224 (class 1259 OID 13524863)
-- Name: people_align_debug_bck2021; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.people_align_debug_bck2021 (
    filename character varying,
    unitid character varying,
    people_role character varying,
    people_name character varying,
    people_fk integer,
    specimen_fk integer
);


ALTER TABLE clean.people_align_debug_bck2021 OWNER TO darwin2;

--
-- TOC entry 225 (class 1259 OID 13524869)
-- Name: specimens_bck2021; Type: TABLE; Schema: clean; Owner: darwin2
--

CREATE TABLE clean.specimens_bck2021 (
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
    nagoya character varying
);


ALTER TABLE clean.specimens_bck2021 OWNER TO darwin2;

--
-- TOC entry 226 (class 1259 OID 13524875)
-- Name: v_crustacae_align_date_begin; Type: VIEW; Schema: clean; Owner: darwin2
--

CREATE VIEW clean.v_crustacae_align_date_begin AS
 SELECT 56 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionstartday IS NOT NULL) AND (crustacae_2021_clean_date.collectionstartmonth IS NOT NULL) AND (crustacae_2021_clean_date.collectionstartyear IS NOT NULL))
UNION
 SELECT 48 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionstartday IS NULL) AND (crustacae_2021_clean_date.collectionstartmonth IS NOT NULL) AND (crustacae_2021_clean_date.collectionstartyear IS NOT NULL))
UNION
 SELECT 32 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionstartday IS NULL) AND (crustacae_2021_clean_date.collectionstartmonth IS NULL) AND (crustacae_2021_clean_date.collectionstartyear IS NOT NULL));


ALTER TABLE clean.v_crustacae_align_date_begin OWNER TO darwin2;

--
-- TOC entry 227 (class 1259 OID 13524880)
-- Name: v_crustacae_align_date_end; Type: VIEW; Schema: clean; Owner: darwin2
--

CREATE VIEW clean.v_crustacae_align_date_end AS
 SELECT 56 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionendday IS NOT NULL) AND (crustacae_2021_clean_date.collectionendmonth IS NOT NULL) AND (crustacae_2021_clean_date.collectionendyear IS NOT NULL))
UNION
 SELECT 48 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionendday IS NULL) AND (crustacae_2021_clean_date.collectionendmonth IS NOT NULL) AND (crustacae_2021_clean_date.collectionendyear IS NOT NULL))
UNION
 SELECT 32 AS code,
    crustacae_2021_clean_date.unitid,
    crustacae_2021_clean_date.original_collection_date,
    crustacae_2021_clean_date.collectionstartday,
    crustacae_2021_clean_date.collectionstartmonth,
    crustacae_2021_clean_date.collectionstartyear,
    crustacae_2021_clean_date.collectionendday,
    crustacae_2021_clean_date.collectionendmonth,
    crustacae_2021_clean_date.collectionendyear
   FROM clean.crustacae_2021_clean_date
  WHERE ((crustacae_2021_clean_date.collectionendday IS NULL) AND (crustacae_2021_clean_date.collectionendmonth IS NULL) AND (crustacae_2021_clean_date.collectionendyear IS NOT NULL));


ALTER TABLE clean.v_crustacae_align_date_end OWNER TO darwin2;

--
-- TOC entry 460 (class 1259 OID 13526362)
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
-- TOC entry 461 (class 1259 OID 13526368)
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
-- TOC entry 495 (class 1259 OID 26905214)
-- Name: template_table_record_ref; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.template_table_record_ref (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL
);


ALTER TABLE darwin2.template_table_record_ref OWNER TO darwin2;

--
-- TOC entry 5353 (class 0 OID 0)
-- Dependencies: 495
-- Name: TABLE template_table_record_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE darwin2.template_table_record_ref IS 'Template called to add referenced_relation and record_id fields';


--
-- TOC entry 5354 (class 0 OID 0)
-- Dependencies: 495
-- Name: COLUMN template_table_record_ref.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.template_table_record_ref.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5355 (class 0 OID 0)
-- Dependencies: 495
-- Name: COLUMN template_table_record_ref.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.template_table_record_ref.record_id IS 'Id of record concerned';


--
-- TOC entry 497 (class 1259 OID 26905223)
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
-- TOC entry 5357 (class 0 OID 0)
-- Dependencies: 497
-- Name: TABLE classification_synonymies; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE darwin2.classification_synonymies IS 'Table containing classification synonymies';


--
-- TOC entry 5358 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.referenced_relation IS 'Classification table concerned';


--
-- TOC entry 5359 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.record_id IS 'Id of record placed in group as a synonym';


--
-- TOC entry 5360 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.group_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.group_id IS 'Id given to group';


--
-- TOC entry 5361 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.group_name IS 'Name of group under which synonyms are placed';


--
-- TOC entry 5362 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.is_basionym; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.is_basionym IS 'If record is a basionym';


--
-- TOC entry 5363 (class 0 OID 0)
-- Dependencies: 497
-- Name: COLUMN classification_synonymies.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN darwin2.classification_synonymies.order_by IS 'Order by used to qualify order amongst synonyms - used mainly for senio and junior synonyms';


--
-- TOC entry 496 (class 1259 OID 26905221)
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
-- TOC entry 5365 (class 0 OID 0)
-- Dependencies: 496
-- Name: classification_synonymies_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE darwin2.classification_synonymies_id_seq OWNED BY darwin2.classification_synonymies.id;


--
-- TOC entry 464 (class 1259 OID 13526392)
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
-- TOC entry 465 (class 1259 OID 13592001)
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
-- TOC entry 232 (class 1259 OID 13524921)
-- Name: country_cleaning; Type: TABLE; Schema: darwin2; Owner: darwin2
--

CREATE TABLE darwin2.country_cleaning (
    original_name character varying NOT NULL,
    replacement_value character varying
);


ALTER TABLE darwin2.country_cleaning OWNER TO darwin2;

--
-- TOC entry 470 (class 1259 OID 13907676)
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
-- TOC entry 237 (class 1259 OID 13524962)
-- Name: fgmv_rdf_view_2_ichtyo_taxo_mbisa; Type: FOREIGN TABLE; Schema: darwin2; Owner: darwin2
--

CREATE FOREIGN TABLE darwin2.fgmv_rdf_view_2_ichtyo_taxo_mbisa (
    uuid uuid,
    specimen_id text,
    ref_uri text,
    object_uri text,
    title text,
    title_description text,
    collector text,
    collection_date text,
    "ObjectURI" text,
    modified timestamp without time zone,
    base_ofrecord text,
    institution_code text,
    collection_name character varying,
    catalog_number text,
    family character varying,
    genus character varying,
    specific_epithet character varying,
    scientific_name character varying,
    higher_geography character varying,
    country character varying,
    locality text,
    image character varying,
    latitude double precision,
    longitude double precision,
    coll_type character varying,
    geom public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_rdf_view_2_ichtyo_taxo_mbisa'
);


ALTER FOREIGN TABLE darwin2.fgmv_rdf_view_2_ichtyo_taxo_mbisa OWNER TO darwin2;

--
-- TOC entry 471 (class 1259 OID 13907682)
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
-- TOC entry 472 (class 1259 OID 13907688)
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
-- TOC entry 473 (class 1259 OID 13907702)
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
-- TOC entry 488 (class 1259 OID 16014114)
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
-- TOC entry 489 (class 1259 OID 16014119)
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
-- TOC entry 491 (class 1259 OID 16014145)
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
-- TOC entry 469 (class 1259 OID 13802693)
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
-- TOC entry 463 (class 1259 OID 13526386)
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
-- TOC entry 477 (class 1259 OID 13907807)
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
    specimens.geom
   FROM (((darwin2.specimens
     LEFT JOIN darwin2.codes ON ((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text) AND (specimens.id = codes.record_id))))
     JOIN darwin2.collections ON (((specimens.collection_ref = collections.id) AND (collections.is_public = true))))
     JOIN darwin2.taxonomy ON (((specimens.taxon_ref = taxonomy.id) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false))))
  WITH NO DATA;


ALTER TABLE darwin2.mv_search_public_specimen OWNER TO darwin2;

--
-- TOC entry 494 (class 1259 OID 16014191)
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
-- TOC entry 243 (class 1259 OID 13525042)
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
-- TOC entry 244 (class 1259 OID 13525047)
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
            unnest(string_to_array((v_specimen_public.gtu_country_tag_value)::text, ';'::text)) AS tmp_country,
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
-- TOC entry 492 (class 1259 OID 16014153)
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
     JOIN darwin2.taxonomy ON (((a.taxon)::integer = taxonomy.id)))
  WHERE ((a.taxon <> ''::text) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false))
  WITH NO DATA;


ALTER TABLE darwin2.mv_taxa_in_specimens OWNER TO darwin2;

--
-- TOC entry 493 (class 1259 OID 16014161)
-- Name: mv_taxonomy_by_collection; Type: MATERIALIZED VIEW; Schema: darwin2; Owner: darwin2
--

CREATE MATERIALIZED VIEW darwin2.mv_taxonomy_by_collection AS
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
    taxonomy.cites,
    mv_taxa_in_specimens.collection_ref,
    mv_taxa_in_specimens.collection_path,
    mv_taxa_in_specimens.full_collection_path
   FROM (darwin2.taxonomy
     JOIN darwin2.mv_taxa_in_specimens ON ((taxonomy.id = mv_taxa_in_specimens.taxon)))
  WHERE (COALESCE(taxonomy.sensitive_info_withheld, false) = false)
  WITH NO DATA;


ALTER TABLE darwin2.mv_taxonomy_by_collection OWNER TO darwin2;

--
-- TOC entry 474 (class 1259 OID 13907708)
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
-- TOC entry 475 (class 1259 OID 13907714)
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
-- TOC entry 490 (class 1259 OID 16014135)
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
-- TOC entry 476 (class 1259 OID 13907724)
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
-- TOC entry 248 (class 1259 OID 13525093)
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
-- TOC entry 478 (class 1259 OID 15274899)
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
-- TOC entry 484 (class 1259 OID 15382060)
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
-- TOC entry 485 (class 1259 OID 15382066)
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
-- TOC entry 486 (class 1259 OID 15382073)
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
-- TOC entry 487 (class 1259 OID 15382205)
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
-- TOC entry 480 (class 1259 OID 15274912)
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
-- TOC entry 479 (class 1259 OID 15274910)
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
-- TOC entry 5371 (class 0 OID 0)
-- Dependencies: 479
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE darwin2.users_id_seq OWNED BY darwin2.users.id;


--
-- TOC entry 481 (class 1259 OID 15380755)
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
-- TOC entry 230 (class 1259 OID 13524905)
-- Name: collections; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.collections (
    id integer NOT NULL,
    collection_type character varying NOT NULL,
    code character varying NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL,
    institution_ref integer NOT NULL,
    main_manager_ref integer NOT NULL,
    staff_ref integer,
    parent_ref integer,
    path character varying NOT NULL,
    code_auto_increment boolean NOT NULL,
    code_last_value integer NOT NULL,
    code_prefix character varying,
    code_prefix_separator character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    code_specimen_duplicate boolean NOT NULL,
    is_public boolean NOT NULL,
    code_mask character varying,
    loan_auto_increment boolean NOT NULL,
    loan_last_value integer NOT NULL,
    code_ai_inherit boolean,
    code_auto_increment_for_insert_only boolean NOT NULL,
    nagoya character varying,
    allow_duplicates boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'collections'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN main_manager_ref OPTIONS (
    column_name 'main_manager_ref'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN staff_ref OPTIONS (
    column_name 'staff_ref'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_auto_increment OPTIONS (
    column_name 'code_auto_increment'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_last_value OPTIONS (
    column_name 'code_last_value'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_specimen_duplicate OPTIONS (
    column_name 'code_specimen_duplicate'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN is_public OPTIONS (
    column_name 'is_public'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_mask OPTIONS (
    column_name 'code_mask'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN loan_auto_increment OPTIONS (
    column_name 'loan_auto_increment'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN loan_last_value OPTIONS (
    column_name 'loan_last_value'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_ai_inherit OPTIONS (
    column_name 'code_ai_inherit'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN code_auto_increment_for_insert_only OPTIONS (
    column_name 'code_auto_increment_for_insert_only'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);
ALTER FOREIGN TABLE fdw_113.collections ALTER COLUMN allow_duplicates OPTIONS (
    column_name 'allow_duplicates'
);


ALTER FOREIGN TABLE fdw_113.collections OWNER TO darwin2;

--
-- TOC entry 231 (class 1259 OID 13524908)
-- Name: v_fdw113_collections_full_path_recursive; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_fdw113_collections_full_path_recursive AS
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
           FROM fdw_113.collections
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
           FROM (fdw_113.collections
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
    collections_path_recursive.code_full_path,
    collections_path_recursive.name_full_path,
    collections_path_recursive.name_indexed_full_path
   FROM collections_path_recursive;


ALTER TABLE darwin2.v_fdw113_collections_full_path_recursive OWNER TO darwin2;

--
-- TOC entry 228 (class 1259 OID 13524885)
-- Name: catalogue_levels; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.catalogue_levels (
    id integer NOT NULL,
    level_type character varying NOT NULL,
    level_name character varying NOT NULL,
    level_sys_name character varying NOT NULL,
    optional_level boolean NOT NULL,
    level_order integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'catalogue_levels'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN level_type OPTIONS (
    column_name 'level_type'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN level_sys_name OPTIONS (
    column_name 'level_sys_name'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN optional_level OPTIONS (
    column_name 'optional_level'
);
ALTER FOREIGN TABLE fdw_113.catalogue_levels ALTER COLUMN level_order OPTIONS (
    column_name 'level_order'
);


ALTER FOREIGN TABLE fdw_113.catalogue_levels OWNER TO darwin2;

--
-- TOC entry 447 (class 1259 OID 13526295)
-- Name: v_mv_catalogue_levels; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_catalogue_levels AS
 SELECT catalogue_levels.id,
    catalogue_levels.level_type,
    catalogue_levels.level_name,
    catalogue_levels.level_sys_name,
    catalogue_levels.optional_level,
    catalogue_levels.level_order
   FROM fdw_113.catalogue_levels;


ALTER TABLE darwin2.v_mv_catalogue_levels OWNER TO darwin2;

--
-- TOC entry 229 (class 1259 OID 13524895)
-- Name: catalogue_people; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.catalogue_people (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    people_type character varying NOT NULL,
    people_sub_type character varying NOT NULL,
    order_by integer NOT NULL,
    people_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'catalogue_people'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN people_type OPTIONS (
    column_name 'people_type'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN people_sub_type OPTIONS (
    column_name 'people_sub_type'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.catalogue_people ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);


ALTER FOREIGN TABLE fdw_113.catalogue_people OWNER TO darwin2;

--
-- TOC entry 448 (class 1259 OID 13526305)
-- Name: v_mv_catalogue_people; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_catalogue_people AS
 SELECT catalogue_people.referenced_relation,
    catalogue_people.record_id,
    catalogue_people.id,
    catalogue_people.people_type,
    catalogue_people.people_sub_type,
    catalogue_people.order_by,
    catalogue_people.people_ref
   FROM fdw_113.catalogue_people;


ALTER TABLE darwin2.v_mv_catalogue_people OWNER TO darwin2;

--
-- TOC entry 449 (class 1259 OID 13526309)
-- Name: v_mv_collections; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_collections AS
 SELECT v_fdw113_collections_full_path_recursive.id,
    v_fdw113_collections_full_path_recursive.collection_type,
    v_fdw113_collections_full_path_recursive.code,
    v_fdw113_collections_full_path_recursive.name,
    v_fdw113_collections_full_path_recursive.name_indexed,
    v_fdw113_collections_full_path_recursive.institution_ref,
    v_fdw113_collections_full_path_recursive.main_manager_ref,
    v_fdw113_collections_full_path_recursive.staff_ref,
    v_fdw113_collections_full_path_recursive.parent_ref,
    v_fdw113_collections_full_path_recursive.path,
    v_fdw113_collections_full_path_recursive.code_auto_increment,
    v_fdw113_collections_full_path_recursive.code_last_value,
    v_fdw113_collections_full_path_recursive.code_prefix,
    v_fdw113_collections_full_path_recursive.code_prefix_separator,
    v_fdw113_collections_full_path_recursive.code_suffix,
    v_fdw113_collections_full_path_recursive.code_suffix_separator,
    v_fdw113_collections_full_path_recursive.code_specimen_duplicate,
    v_fdw113_collections_full_path_recursive.is_public,
    v_fdw113_collections_full_path_recursive.code_mask,
    v_fdw113_collections_full_path_recursive.loan_auto_increment,
    v_fdw113_collections_full_path_recursive.loan_last_value,
    v_fdw113_collections_full_path_recursive.code_ai_inherit,
    v_fdw113_collections_full_path_recursive.code_auto_increment_for_insert_only,
    v_fdw113_collections_full_path_recursive.nagoya,
    v_fdw113_collections_full_path_recursive.allow_duplicates,
    v_fdw113_collections_full_path_recursive.code_full_path,
    v_fdw113_collections_full_path_recursive.name_full_path,
    v_fdw113_collections_full_path_recursive.name_indexed_full_path
   FROM darwin2.v_fdw113_collections_full_path_recursive
  WHERE (lower((v_fdw113_collections_full_path_recursive.name_full_path)::text) !~~ '%test%'::text);


ALTER TABLE darwin2.v_mv_collections OWNER TO darwin2;

--
-- TOC entry 233 (class 1259 OID 13524927)
-- Name: specimens; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimens (
    id integer NOT NULL,
    collection_ref integer NOT NULL,
    expedition_ref integer,
    gtu_ref integer,
    taxon_ref integer,
    litho_ref integer,
    chrono_ref integer,
    lithology_ref integer,
    mineral_ref integer,
    acquisition_category character varying NOT NULL,
    acquisition_date_mask integer NOT NULL,
    acquisition_date date NOT NULL,
    station_visible boolean NOT NULL,
    ig_ref integer,
    type character varying NOT NULL,
    type_group character varying NOT NULL,
    type_search character varying NOT NULL,
    sex character varying NOT NULL,
    stage character varying NOT NULL,
    state character varying NOT NULL,
    social_status character varying NOT NULL,
    rock_form character varying NOT NULL,
    room character varying,
    shelf character varying,
    specimen_count_min integer NOT NULL,
    specimen_count_max integer NOT NULL,
    spec_ident_ids integer[] NOT NULL,
    spec_coll_ids integer[] NOT NULL,
    spec_don_sel_ids integer[] NOT NULL,
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
    collection_name_full_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimens'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN expedition_ref OPTIONS (
    column_name 'expedition_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_ref OPTIONS (
    column_name 'litho_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_ref OPTIONS (
    column_name 'chrono_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_ref OPTIONS (
    column_name 'lithology_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN station_visible OPTIONS (
    column_name 'station_visible'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN type_group OPTIONS (
    column_name 'type_group'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN type_search OPTIONS (
    column_name 'type_search'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN rock_form OPTIONS (
    column_name 'rock_form'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN spec_ident_ids OPTIONS (
    column_name 'spec_ident_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN spec_coll_ids OPTIONS (
    column_name 'spec_coll_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN spec_don_sel_ids OPTIONS (
    column_name 'spec_don_sel_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_is_public OPTIONS (
    column_name 'collection_is_public'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_parent_ref OPTIONS (
    column_name 'collection_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN expedition_name_indexed OPTIONS (
    column_name 'expedition_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_tag_values_indexed OPTIONS (
    column_name 'gtu_tag_values_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_province_tag_indexed OPTIONS (
    column_name 'gtu_province_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_others_tag_indexed OPTIONS (
    column_name 'gtu_others_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_elevation_accuracy OPTIONS (
    column_name 'gtu_elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_location OPTIONS (
    column_name 'gtu_location'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_name_indexed OPTIONS (
    column_name 'taxon_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_status OPTIONS (
    column_name 'taxon_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_parent_ref OPTIONS (
    column_name 'taxon_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN taxon_extinct OPTIONS (
    column_name 'taxon_extinct'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_name OPTIONS (
    column_name 'litho_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_name_indexed OPTIONS (
    column_name 'litho_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_level_ref OPTIONS (
    column_name 'litho_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_level_name OPTIONS (
    column_name 'litho_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_status OPTIONS (
    column_name 'litho_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_local OPTIONS (
    column_name 'litho_local'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_color OPTIONS (
    column_name 'litho_color'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_path OPTIONS (
    column_name 'litho_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN litho_parent_ref OPTIONS (
    column_name 'litho_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_name OPTIONS (
    column_name 'chrono_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_name_indexed OPTIONS (
    column_name 'chrono_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_level_ref OPTIONS (
    column_name 'chrono_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_level_name OPTIONS (
    column_name 'chrono_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_status OPTIONS (
    column_name 'chrono_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_local OPTIONS (
    column_name 'chrono_local'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_color OPTIONS (
    column_name 'chrono_color'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_path OPTIONS (
    column_name 'chrono_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN chrono_parent_ref OPTIONS (
    column_name 'chrono_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_name OPTIONS (
    column_name 'lithology_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_name_indexed OPTIONS (
    column_name 'lithology_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_level_ref OPTIONS (
    column_name 'lithology_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_level_name OPTIONS (
    column_name 'lithology_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_status OPTIONS (
    column_name 'lithology_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_local OPTIONS (
    column_name 'lithology_local'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_color OPTIONS (
    column_name 'lithology_color'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_path OPTIONS (
    column_name 'lithology_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN lithology_parent_ref OPTIONS (
    column_name 'lithology_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_name OPTIONS (
    column_name 'mineral_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_name_indexed OPTIONS (
    column_name 'mineral_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_level_ref OPTIONS (
    column_name 'mineral_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_level_name OPTIONS (
    column_name 'mineral_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_status OPTIONS (
    column_name 'mineral_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_local OPTIONS (
    column_name 'mineral_local'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_color OPTIONS (
    column_name 'mineral_color'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_path OPTIONS (
    column_name 'mineral_path'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN mineral_parent_ref OPTIONS (
    column_name 'mineral_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN ig_num_indexed OPTIONS (
    column_name 'ig_num_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_males_min OPTIONS (
    column_name 'specimen_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_males_max OPTIONS (
    column_name 'specimen_count_males_max'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_females_min OPTIONS (
    column_name 'specimen_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_females_max OPTIONS (
    column_name 'specimen_count_females_max'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_juveniles_min OPTIONS (
    column_name 'specimen_count_juveniles_min'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_count_juveniles_max OPTIONS (
    column_name 'specimen_count_juveniles_max'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN main_code_indexed OPTIONS (
    column_name 'main_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN valid_label OPTIONS (
    column_name 'valid_label'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN label_created_on OPTIONS (
    column_name 'label_created_on'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN label_created_by OPTIONS (
    column_name 'label_created_by'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_iso3166 OPTIONS (
    column_name 'gtu_iso3166'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN gtu_iso3166_subdivision OPTIONS (
    column_name 'gtu_iso3166_subdivision'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.specimens ALTER COLUMN collection_name_full_path OPTIONS (
    column_name 'collection_name_full_path'
);


ALTER FOREIGN TABLE fdw_113.specimens OWNER TO darwin2;

--
-- TOC entry 234 (class 1259 OID 13524930)
-- Name: taxonomy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.taxonomy (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer,
    id integer NOT NULL,
    extinct boolean NOT NULL,
    sensitive_info_withheld boolean,
    is_reference_taxonomy boolean NOT NULL,
    metadata_ref integer NOT NULL,
    taxonomy_creation_date timestamp without time zone,
    import_ref integer,
    cites boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'taxonomy'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN extinct OPTIONS (
    column_name 'extinct'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN sensitive_info_withheld OPTIONS (
    column_name 'sensitive_info_withheld'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN is_reference_taxonomy OPTIONS (
    column_name 'is_reference_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN metadata_ref OPTIONS (
    column_name 'metadata_ref'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN taxonomy_creation_date OPTIONS (
    column_name 'taxonomy_creation_date'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.taxonomy ALTER COLUMN cites OPTIONS (
    column_name 'cites'
);


ALTER FOREIGN TABLE fdw_113.taxonomy OWNER TO darwin2;

--
-- TOC entry 450 (class 1259 OID 13526314)
-- Name: v_mv_specimens; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_specimens AS
 SELECT specimens.id,
    specimens.collection_ref,
    specimens.expedition_ref,
    specimens.gtu_ref,
    specimens.taxon_ref,
    specimens.litho_ref,
    specimens.chrono_ref,
    specimens.lithology_ref,
    specimens.mineral_ref,
    specimens.acquisition_category,
    specimens.acquisition_date_mask,
    specimens.acquisition_date,
    specimens.station_visible,
    specimens.ig_ref,
    specimens.type,
    specimens.type_group,
    specimens.type_search,
    specimens.sex,
    specimens.stage,
    specimens.state,
    specimens.social_status,
    specimens.rock_form,
    specimens.room,
    specimens.shelf,
    specimens.specimen_count_min,
    specimens.specimen_count_max,
    specimens.spec_ident_ids,
    specimens.spec_coll_ids,
    specimens.spec_don_sel_ids,
    specimens.collection_type,
    specimens.collection_code,
    specimens.collection_name,
    specimens.collection_is_public,
    specimens.collection_parent_ref,
    specimens.collection_path,
    specimens.expedition_name,
    specimens.expedition_name_indexed,
    specimens.gtu_code,
    specimens.gtu_from_date_mask,
    specimens.gtu_from_date,
    specimens.gtu_to_date_mask,
    specimens.gtu_to_date,
    specimens.gtu_tag_values_indexed,
        CASE
            WHEN (country_cleaning.replacement_value IS NOT NULL) THEN country_cleaning.replacement_value
            ELSE specimens.gtu_country_tag_value
        END AS gtu_country_tag_value,
        CASE
            WHEN (country_cleaning.replacement_value IS NOT NULL) THEN ((('{"'::text || (darwin2.fulltoindex(country_cleaning.replacement_value, false))::text) || '"}'::text))::character varying[]
            ELSE specimens.gtu_country_tag_indexed
        END AS gtu_country_tag_indexed,
    specimens.gtu_province_tag_value,
    specimens.gtu_province_tag_indexed,
    specimens.gtu_others_tag_value,
    specimens.gtu_others_tag_indexed,
    specimens.gtu_elevation,
    specimens.gtu_elevation_accuracy,
        CASE
            WHEN (taxonomy.sensitive_info_withheld = true) THEN NULL::point
            ELSE specimens.gtu_location
        END AS gtu_location,
    specimens.taxon_name,
    specimens.taxon_name_indexed,
    specimens.taxon_level_ref,
    specimens.taxon_level_name,
    specimens.taxon_status,
    specimens.taxon_path,
    specimens.taxon_parent_ref,
    specimens.taxon_extinct,
    specimens.litho_name,
    specimens.litho_name_indexed,
    specimens.litho_level_ref,
    specimens.litho_level_name,
    specimens.litho_status,
    specimens.litho_local,
    specimens.litho_color,
    specimens.litho_path,
    specimens.litho_parent_ref,
    specimens.chrono_name,
    specimens.chrono_name_indexed,
    specimens.chrono_level_ref,
    specimens.chrono_level_name,
    specimens.chrono_status,
    specimens.chrono_local,
    specimens.chrono_color,
    specimens.chrono_path,
    specimens.chrono_parent_ref,
    specimens.lithology_name,
    specimens.lithology_name_indexed,
    specimens.lithology_level_ref,
    specimens.lithology_level_name,
    specimens.lithology_status,
    specimens.lithology_local,
    specimens.lithology_color,
    specimens.lithology_path,
    specimens.lithology_parent_ref,
    specimens.mineral_name,
    specimens.mineral_name_indexed,
    specimens.mineral_level_ref,
    specimens.mineral_level_name,
    specimens.mineral_status,
    specimens.mineral_local,
    specimens.mineral_color,
    specimens.mineral_path,
    specimens.mineral_parent_ref,
    specimens.ig_num,
    specimens.ig_num_indexed,
    specimens.ig_date_mask,
    specimens.ig_date,
    specimens.specimen_count_males_min,
    specimens.specimen_count_males_max,
    specimens.specimen_count_females_min,
    specimens.specimen_count_females_max,
    specimens.specimen_count_juveniles_min,
    specimens.specimen_count_juveniles_max,
    specimens.main_code_indexed,
    specimens.category,
    specimens.institution_ref,
    specimens.building,
    specimens.floor,
    specimens."row",
    specimens.col,
    specimens.container,
    specimens.sub_container,
    specimens.container_type,
    specimens.sub_container_type,
    specimens.container_storage,
    specimens.sub_container_storage,
    specimens.surnumerary,
    specimens.object_name,
    specimens.object_name_indexed,
    specimens.specimen_status,
    specimens.valid_label,
    specimens.label_created_on,
    specimens.label_created_by,
    specimens.specimen_creation_date,
    specimens.import_ref,
    specimens.gtu_iso3166,
    specimens.gtu_iso3166_subdivision,
    specimens.nagoya,
    specimens.uuid,
    specimens.collection_name_full_path,
        CASE
            WHEN ((specimens.gtu_location[0] <> (0)::double precision) AND (specimens.gtu_location[1] <> (0)::double precision)) THEN public.st_setsrid(public.st_makepoint(specimens.gtu_location[0], specimens.gtu_location[1]), 4326)
            ELSE NULL::public.geometry
        END AS geom,
    darwin2.fct_rmca_sort_taxon_get_parent_level_text(specimens.taxon_ref, 34) AS family
   FROM (((fdw_113.specimens
     LEFT JOIN fdw_113.taxonomy ON ((specimens.taxon_ref = taxonomy.id)))
     JOIN darwin2.v_mv_collections ON ((specimens.collection_ref = v_mv_collections.id)))
     LEFT JOIN darwin2.country_cleaning ON (((specimens.gtu_country_tag_value)::text = (country_cleaning.original_name)::text)))
  WHERE ((v_mv_collections.is_public = true) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false));


ALTER TABLE darwin2.v_mv_specimens OWNER TO darwin2;

--
-- TOC entry 235 (class 1259 OID 13524941)
-- Name: codes; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.codes (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    code_category character varying NOT NULL,
    code_prefix character varying,
    code_prefix_separator character varying,
    code character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    full_code_indexed character varying NOT NULL,
    code_date timestamp without time zone NOT NULL,
    code_date_mask integer NOT NULL,
    code_num integer,
    code_num_bigint bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'codes'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_date_mask OPTIONS (
    column_name 'code_date_mask'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_num OPTIONS (
    column_name 'code_num'
);
ALTER FOREIGN TABLE fdw_113.codes ALTER COLUMN code_num_bigint OPTIONS (
    column_name 'code_num_bigint'
);


ALTER FOREIGN TABLE fdw_113.codes OWNER TO darwin2;

--
-- TOC entry 451 (class 1259 OID 13526319)
-- Name: v_mv_codes; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_codes AS
 SELECT codes.referenced_relation,
    codes.record_id,
    codes.id,
    codes.code_category,
    codes.code_prefix,
    codes.code_prefix_separator,
    codes.code,
    codes.code_suffix,
    codes.code_suffix_separator,
    codes.full_code_indexed,
    codes.code_date,
    codes.code_date_mask,
    codes.code_num,
    codes.code_num_bigint,
    COALESCE(NULLIF(btrim((((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text)), ''::text), (v_mv_specimens.collection_code)::text) AS code_display
   FROM (fdw_113.codes
     JOIN darwin2.v_mv_specimens ON ((codes.record_id = v_mv_specimens.id)))
  WHERE (((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text));


ALTER TABLE darwin2.v_mv_codes OWNER TO darwin2;

--
-- TOC entry 236 (class 1259 OID 13524952)
-- Name: ext_links; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.ext_links (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    url character varying NOT NULL,
    comment text,
    comment_indexed text,
    category character varying,
    contributor character varying,
    disclaimer character varying,
    license character varying,
    display_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'ext_links'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN url OPTIONS (
    column_name 'url'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN comment_indexed OPTIONS (
    column_name 'comment_indexed'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN contributor OPTIONS (
    column_name 'contributor'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN disclaimer OPTIONS (
    column_name 'disclaimer'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN license OPTIONS (
    column_name 'license'
);
ALTER FOREIGN TABLE fdw_113.ext_links ALTER COLUMN display_order OPTIONS (
    column_name 'display_order'
);


ALTER FOREIGN TABLE fdw_113.ext_links OWNER TO darwin2;

--
-- TOC entry 452 (class 1259 OID 13526324)
-- Name: v_mv_ext_links; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_ext_links AS
 SELECT ext_links.referenced_relation,
    ext_links.record_id,
    ext_links.id,
    ext_links.url,
    ext_links.comment,
    ext_links.comment_indexed,
    ext_links.category,
    ext_links.contributor,
    ext_links.disclaimer,
    ext_links.license,
    ext_links.display_order
   FROM fdw_113.ext_links;


ALTER TABLE darwin2.v_mv_ext_links OWNER TO darwin2;

--
-- TOC entry 238 (class 1259 OID 13524965)
-- Name: flat_dict; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.flat_dict (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    dict_field character varying NOT NULL,
    dict_value character varying NOT NULL,
    dict_depend character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'flat_dict'
);
ALTER FOREIGN TABLE fdw_113.flat_dict ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.flat_dict ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.flat_dict ALTER COLUMN dict_field OPTIONS (
    column_name 'dict_field'
);
ALTER FOREIGN TABLE fdw_113.flat_dict ALTER COLUMN dict_value OPTIONS (
    column_name 'dict_value'
);
ALTER FOREIGN TABLE fdw_113.flat_dict ALTER COLUMN dict_depend OPTIONS (
    column_name 'dict_depend'
);


ALTER FOREIGN TABLE fdw_113.flat_dict OWNER TO darwin2;

--
-- TOC entry 453 (class 1259 OID 13526328)
-- Name: v_mv_flat_dict; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_flat_dict AS
 SELECT flat_dict.id,
    flat_dict.referenced_relation,
    flat_dict.dict_field,
    flat_dict.dict_value,
    flat_dict.dict_depend
   FROM fdw_113.flat_dict;


ALTER TABLE darwin2.v_mv_flat_dict OWNER TO darwin2;

--
-- TOC entry 239 (class 1259 OID 13524975)
-- Name: gtu; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.gtu (
    id integer NOT NULL,
    code character varying NOT NULL,
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
    nagoya character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'gtu'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN tag_values_indexed OPTIONS (
    column_name 'tag_values_indexed'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN lat_long_accuracy OPTIONS (
    column_name 'lat_long_accuracy'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN location OPTIONS (
    column_name 'location'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN elevation OPTIONS (
    column_name 'elevation'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN elevation_accuracy OPTIONS (
    column_name 'elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude_dms_degree OPTIONS (
    column_name 'latitude_dms_degree'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude_dms_minutes OPTIONS (
    column_name 'latitude_dms_minutes'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude_dms_seconds OPTIONS (
    column_name 'latitude_dms_seconds'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude_dms_direction OPTIONS (
    column_name 'latitude_dms_direction'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude_dms_degree OPTIONS (
    column_name 'longitude_dms_degree'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude_dms_minutes OPTIONS (
    column_name 'longitude_dms_minutes'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude_dms_seconds OPTIONS (
    column_name 'longitude_dms_seconds'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude_dms_direction OPTIONS (
    column_name 'longitude_dms_direction'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN latitude_utm OPTIONS (
    column_name 'latitude_utm'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN longitude_utm OPTIONS (
    column_name 'longitude_utm'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN utm_zone OPTIONS (
    column_name 'utm_zone'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN coordinates_source OPTIONS (
    column_name 'coordinates_source'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN elevation_unit OPTIONS (
    column_name 'elevation_unit'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN gtu_creation_date OPTIONS (
    column_name 'gtu_creation_date'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN iso3166 OPTIONS (
    column_name 'iso3166'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN iso3166_subdivision OPTIONS (
    column_name 'iso3166_subdivision'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN wkt_str OPTIONS (
    column_name 'wkt_str'
);
ALTER FOREIGN TABLE fdw_113.gtu ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);


ALTER FOREIGN TABLE fdw_113.gtu OWNER TO darwin2;

--
-- TOC entry 454 (class 1259 OID 13526332)
-- Name: v_mv_gtu; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_gtu AS
 SELECT gtu.id,
    gtu.code,
    gtu.gtu_from_date_mask,
    gtu.gtu_from_date,
    gtu.gtu_to_date_mask,
    gtu.gtu_to_date,
    gtu.tag_values_indexed,
    gtu.latitude,
    gtu.longitude,
    gtu.lat_long_accuracy,
    gtu.location,
    gtu.elevation,
    gtu.elevation_accuracy,
    gtu.latitude_dms_degree,
    gtu.latitude_dms_minutes,
    gtu.latitude_dms_seconds,
    gtu.latitude_dms_direction,
    gtu.longitude_dms_degree,
    gtu.longitude_dms_minutes,
    gtu.longitude_dms_seconds,
    gtu.longitude_dms_direction,
    gtu.latitude_utm,
    gtu.longitude_utm,
    gtu.utm_zone,
    gtu.coordinates_source,
    gtu.elevation_unit,
    gtu.gtu_creation_date,
    gtu.import_ref,
    gtu.iso3166,
    gtu.iso3166_subdivision,
    gtu.wkt_str,
    gtu.nagoya,
    public.st_setsrid(public.st_makepoint(gtu.longitude, gtu.latitude), 4326) AS geom
   FROM fdw_113.gtu;


ALTER TABLE darwin2.v_mv_gtu OWNER TO darwin2;

--
-- TOC entry 240 (class 1259 OID 13524986)
-- Name: identifications; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.identifications (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    notion_concerned character varying NOT NULL,
    notion_date timestamp without time zone NOT NULL,
    notion_date_mask integer NOT NULL,
    value_defined character varying,
    value_defined_indexed character varying NOT NULL,
    determination_status character varying,
    order_by integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'identifications'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN notion_concerned OPTIONS (
    column_name 'notion_concerned'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN notion_date OPTIONS (
    column_name 'notion_date'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN notion_date_mask OPTIONS (
    column_name 'notion_date_mask'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN value_defined OPTIONS (
    column_name 'value_defined'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN value_defined_indexed OPTIONS (
    column_name 'value_defined_indexed'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN determination_status OPTIONS (
    column_name 'determination_status'
);
ALTER FOREIGN TABLE fdw_113.identifications ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);


ALTER FOREIGN TABLE fdw_113.identifications OWNER TO darwin2;

--
-- TOC entry 455 (class 1259 OID 13526337)
-- Name: v_mv_identifications; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_identifications AS
 SELECT identifications.referenced_relation,
    identifications.record_id,
    identifications.id,
    identifications.notion_concerned,
    identifications.notion_date,
    identifications.notion_date_mask,
    identifications.value_defined,
    identifications.value_defined_indexed,
    identifications.determination_status,
    identifications.order_by
   FROM fdw_113.identifications;


ALTER TABLE darwin2.v_mv_identifications OWNER TO darwin2;

--
-- TOC entry 245 (class 1259 OID 13525063)
-- Name: people; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people (
    is_physical boolean NOT NULL,
    sub_type character varying,
    formated_name character varying NOT NULL,
    formated_name_indexed character varying NOT NULL,
    formated_name_unique character varying NOT NULL,
    title character varying NOT NULL,
    family_name character varying NOT NULL,
    given_name character varying,
    additional_names character varying,
    birth_date_mask integer NOT NULL,
    birth_date date NOT NULL,
    gender character(1),
    id integer NOT NULL,
    end_date_mask integer NOT NULL,
    end_date date NOT NULL,
    activity_date_from_mask integer NOT NULL,
    activity_date_from date NOT NULL,
    activity_date_to_mask integer NOT NULL,
    activity_date_to date NOT NULL,
    name_formated_indexed character varying NOT NULL,
    import_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN is_physical OPTIONS (
    column_name 'is_physical'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN sub_type OPTIONS (
    column_name 'sub_type'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN formated_name_indexed OPTIONS (
    column_name 'formated_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN formated_name_unique OPTIONS (
    column_name 'formated_name_unique'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN family_name OPTIONS (
    column_name 'family_name'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN given_name OPTIONS (
    column_name 'given_name'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN additional_names OPTIONS (
    column_name 'additional_names'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN birth_date_mask OPTIONS (
    column_name 'birth_date_mask'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN birth_date OPTIONS (
    column_name 'birth_date'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN gender OPTIONS (
    column_name 'gender'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN end_date_mask OPTIONS (
    column_name 'end_date_mask'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN end_date OPTIONS (
    column_name 'end_date'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN activity_date_from_mask OPTIONS (
    column_name 'activity_date_from_mask'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN activity_date_from OPTIONS (
    column_name 'activity_date_from'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN activity_date_to_mask OPTIONS (
    column_name 'activity_date_to_mask'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN activity_date_to OPTIONS (
    column_name 'activity_date_to'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN name_formated_indexed OPTIONS (
    column_name 'name_formated_indexed'
);
ALTER FOREIGN TABLE fdw_113.people ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);


ALTER FOREIGN TABLE fdw_113.people OWNER TO darwin2;

--
-- TOC entry 456 (class 1259 OID 13526341)
-- Name: v_mv_people; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_people AS
 SELECT people.is_physical,
    people.sub_type,
    people.formated_name,
    people.formated_name_indexed,
    people.formated_name_unique,
    people.title,
    people.family_name,
    people.given_name,
    people.additional_names,
    people.birth_date_mask,
    people.birth_date,
    people.gender,
    people.id,
    people.end_date_mask,
    people.end_date,
    people.activity_date_from_mask,
    people.activity_date_from,
    people.activity_date_to_mask,
    people.activity_date_to,
    people.name_formated_indexed,
    people.import_ref
   FROM fdw_113.people;


ALTER TABLE darwin2.v_mv_people OWNER TO darwin2;

--
-- TOC entry 246 (class 1259 OID 13525073)
-- Name: specimens_stable_ids; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimens_stable_ids (
    id bigint NOT NULL,
    specimen_ref bigint NOT NULL,
    original_id bigint NOT NULL,
    uuid uuid,
    doi character varying,
    specimen_fk bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimens_stable_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN original_id OPTIONS (
    column_name 'original_id'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN doi OPTIONS (
    column_name 'doi'
);
ALTER FOREIGN TABLE fdw_113.specimens_stable_ids ALTER COLUMN specimen_fk OPTIONS (
    column_name 'specimen_fk'
);


ALTER FOREIGN TABLE fdw_113.specimens_stable_ids OWNER TO darwin2;

--
-- TOC entry 457 (class 1259 OID 13526345)
-- Name: v_mv_specimens_stable_ids; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_specimens_stable_ids AS
 SELECT specimens_stable_ids.id,
    specimens_stable_ids.specimen_ref,
    specimens_stable_ids.original_id,
    specimens_stable_ids.uuid,
    specimens_stable_ids.doi,
    specimens_stable_ids.specimen_fk
   FROM fdw_113.specimens_stable_ids;


ALTER TABLE darwin2.v_mv_specimens_stable_ids OWNER TO darwin2;

--
-- TOC entry 247 (class 1259 OID 13525083)
-- Name: tags; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tags (
    gtu_ref integer NOT NULL,
    group_ref integer NOT NULL,
    group_type character varying NOT NULL,
    sub_group_type character varying NOT NULL,
    tag character varying NOT NULL,
    tag_indexed character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tags'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN group_ref OPTIONS (
    column_name 'group_ref'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN group_type OPTIONS (
    column_name 'group_type'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN sub_group_type OPTIONS (
    column_name 'sub_group_type'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN tag OPTIONS (
    column_name 'tag'
);
ALTER FOREIGN TABLE fdw_113.tags ALTER COLUMN tag_indexed OPTIONS (
    column_name 'tag_indexed'
);


ALTER FOREIGN TABLE fdw_113.tags OWNER TO darwin2;

--
-- TOC entry 459 (class 1259 OID 13526354)
-- Name: v_mv_tags; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_mv_tags AS
 SELECT tags.gtu_ref,
    tags.group_ref,
    tags.group_type,
    tags.sub_group_type,
    tags.tag,
    tags.tag_indexed
   FROM fdw_113.tags;


ALTER TABLE darwin2.v_mv_tags OWNER TO darwin2;

--
-- TOC entry 462 (class 1259 OID 13526380)
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
     JOIN darwin2.taxonomy ON (((a.taxon)::integer = taxonomy.id)))
  WHERE ((a.taxon <> ''::text) AND (COALESCE(taxonomy.sensitive_info_withheld, false) = false));


ALTER TABLE darwin2.v_taxa_in_specimens OWNER TO darwin2;

--
-- TOC entry 458 (class 1259 OID 13526349)
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
-- TOC entry 483 (class 1259 OID 15380771)
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
-- TOC entry 249 (class 1259 OID 13525102)
-- Name: users_tracking; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users_tracking (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    user_ref integer NOT NULL,
    action character varying NOT NULL,
    old_value public.hstore,
    new_value public.hstore,
    modification_date_time timestamp without time zone NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users_tracking'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN action OPTIONS (
    column_name 'action'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN old_value OPTIONS (
    column_name 'old_value'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN new_value OPTIONS (
    column_name 'new_value'
);
ALTER FOREIGN TABLE fdw_113.users_tracking ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);


ALTER FOREIGN TABLE fdw_113.users_tracking OWNER TO darwin2;

--
-- TOC entry 482 (class 1259 OID 15380766)
-- Name: v_users_tracking_public_specimens; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW darwin2.v_users_tracking_public_specimens AS
 SELECT users_tracking.id,
    users_tracking.referenced_relation,
    users_tracking.record_id,
    users_tracking.user_ref,
    users_tracking.action,
    NULL::public.hstore AS old_value,
    NULL::public.hstore AS new_value,
    users_tracking.modification_date_time
   FROM fdw_113.users_tracking
  WHERE (((users_tracking.action)::text = 'insert'::text) AND ((users_tracking.referenced_relation)::text = 'specimens'::text))
UNION
 SELECT users_tracking.id,
    users_tracking.referenced_relation,
    users_tracking.record_id,
    users_tracking.user_ref,
    users_tracking.action,
    NULL::public.hstore AS old_value,
    NULL::public.hstore AS new_value,
    users_tracking.modification_date_time
   FROM fdw_113.users_tracking
  WHERE (((users_tracking.action)::text = 'update'::text) AND ((users_tracking.referenced_relation)::text = 'specimens'::text))
  GROUP BY users_tracking.id, users_tracking.referenced_relation, users_tracking.record_id, users_tracking.user_ref, users_tracking.action, users_tracking.modification_date_time
 HAVING (users_tracking.modification_date_time = max(users_tracking.modification_date_time));


ALTER TABLE darwin2.v_users_tracking_public_specimens OWNER TO darwin2;

--
-- TOC entry 250 (class 1259 OID 13525113)
-- Name: loans_content; Type: TABLE; Schema: drosera_import; Owner: darwin2
--

CREATE TABLE drosera_import.loans_content (
    pbram character varying,
    loan_code character varying,
    spec_code character varying,
    spec_code2 character varying,
    storage character varying,
    quantity character varying,
    part_code character varying,
    insurrance character varying,
    insurrance_value character varying,
    pick_up_species_name character varying,
    return_date character varying,
    out_flag_drosera character varying,
    spec_storage_1 character varying,
    spect_storage character varying,
    pbram2 character varying,
    code1 character varying,
    code2 character varying
);


ALTER TABLE drosera_import.loans_content OWNER TO darwin2;

--
-- TOC entry 251 (class 1259 OID 13525119)
-- Name: loans_main; Type: TABLE; Schema: drosera_import; Owner: darwin2
--

CREATE TABLE drosera_import.loans_main (
    collection character varying,
    nr_loan character varying,
    date character varying,
    expiration character varying,
    contact1 character varying,
    contact_adresse character varying,
    "out" character varying,
    shipment_or_destroyed character varying,
    code2 character varying,
    curator character varying,
    return_date character varying,
    code_2 character varying,
    code_3 character varying,
    counter_1 character varying,
    counter_2 character varying,
    counter_3 character varying,
    counter_4 character varying,
    counter_5 character varying,
    code_4 character varying
);


ALTER TABLE drosera_import.loans_main OWNER TO darwin2;

--
-- TOC entry 252 (class 1259 OID 13525125)
-- Name: mv_loans_content_matched; Type: TABLE; Schema: drosera_import; Owner: darwin2
--

CREATE TABLE drosera_import.mv_loans_content_matched (
    pbram character varying,
    loan_code character varying,
    spec_code character varying,
    spec_code2 character varying,
    storage character varying,
    quantity character varying,
    part_code character varying,
    insurrance character varying,
    insurrance_value character varying,
    pick_up_species_name character varying,
    return_date character varying,
    out_flag_drosera character varying,
    spec_storage_1 character varying,
    spect_storage character varying,
    pbram2 character varying,
    code1 character varying,
    code2 character varying,
    record_id integer
);


ALTER TABLE drosera_import.mv_loans_content_matched OWNER TO darwin2;

--
-- TOC entry 253 (class 1259 OID 13525131)
-- Name: stations_danny; Type: TABLE; Schema: drosera_import; Owner: darwin2
--

CREATE TABLE drosera_import.stations_danny (
    datasetname text,
    stationlist text,
    stationnumber text,
    exactsite text,
    countrygiven text,
    elevationgivenbycollector text,
    coordinatesgivenbycollector text,
    elevationinmeters text,
    coordinatestext text,
    coordinatesstatus text,
    coordinatesbycollector text,
    latitudedecimaldegrees text,
    longitudedecimaldegrees text,
    extent_m text,
    continent text,
    country text,
    state_province text,
    region_district text,
    municipality text
);


ALTER TABLE drosera_import.stations_danny OWNER TO darwin2;

--
-- TOC entry 254 (class 1259 OID 13525137)
-- Name: v_loan_content_new_number; Type: VIEW; Schema: drosera_import; Owner: darwin2
--

CREATE VIEW drosera_import.v_loan_content_new_number AS
 WITH a AS (
         SELECT regexp_replace(replace(tmp.new_code, '.'::text, '.P.'::text), '(^\d{4})(.+)'::text, '\1.\2'::text) AS padded_number,
            tmp.new_code,
            tmp.pbram,
            tmp.loan_code,
            tmp.spec_code,
            tmp.spec_code2,
            tmp.storage,
            tmp.quantity,
            tmp.part_code,
            tmp.insurrance,
            tmp.insurrance_value,
            tmp.pick_up_species_name,
            tmp.return_date,
            tmp.out_flag_drosera,
            tmp.spec_storage_1,
            tmp.spect_storage,
            tmp.pbram2,
            tmp.code1,
            tmp.code2
           FROM ( SELECT replace(replace((loans_content.spec_code)::text, 'A'::text, '200'::text), 'B'::text, '201'::text) AS new_code,
                    loans_content.pbram,
                    loans_content.loan_code,
                    loans_content.spec_code,
                    loans_content.spec_code2,
                    loans_content.storage,
                    loans_content.quantity,
                    loans_content.part_code,
                    loans_content.insurrance,
                    loans_content.insurrance_value,
                    loans_content.pick_up_species_name,
                    loans_content.return_date,
                    loans_content.out_flag_drosera,
                    loans_content.spec_storage_1,
                    loans_content.spect_storage,
                    loans_content.pbram2,
                    loans_content.code1,
                    loans_content.code2
                   FROM drosera_import.loans_content
                  WHERE (((loans_content.pbram)::text = 'P'::text) AND ((loans_content.spec_code)::text ~ '[A|B|0-9]\d{4}\.\d+'::text) AND (((loans_content.spec_code)::text ~~ 'A%'::text) OR ((loans_content.spec_code)::text ~~ 'B%'::text)))
                UNION
                 SELECT ('19'::text || (loans_content.spec_code)::text) AS new_code,
                    loans_content.pbram,
                    loans_content.loan_code,
                    loans_content.spec_code,
                    loans_content.spec_code2,
                    loans_content.storage,
                    loans_content.quantity,
                    loans_content.part_code,
                    loans_content.insurrance,
                    loans_content.insurrance_value,
                    loans_content.pick_up_species_name,
                    loans_content.return_date,
                    loans_content.out_flag_drosera,
                    loans_content.spec_storage_1,
                    loans_content.spect_storage,
                    loans_content.pbram2,
                    loans_content.code1,
                    loans_content.code2
                   FROM drosera_import.loans_content
                  WHERE (((loans_content.pbram)::text = 'P'::text) AND ((loans_content.spec_code)::text ~ '[A|B|0-9]\d{4}\.\d+'::text) AND ((loans_content.spec_code)::text !~~ 'A%'::text) AND ((loans_content.spec_code)::text !~~ 'B%'::text))) tmp
        )
 SELECT (regexp_matches(a.padded_number, '(.+P\.)(.+)'::text))[1] AS prefix,
    (regexp_matches(a.padded_number, '(.+P\.)(.+)'::text))[2] AS suffix,
    a.padded_number,
    a.new_code,
    a.pbram,
    a.loan_code,
    a.spec_code,
    a.spec_code2,
    a.storage,
    a.quantity,
    a.part_code,
    a.insurrance,
    a.insurrance_value,
    a.pick_up_species_name,
    a.return_date,
    a.out_flag_drosera,
    a.spec_storage_1,
    a.spect_storage,
    a.pbram2,
    a.code1,
    a.code2
   FROM a;


ALTER TABLE drosera_import.v_loan_content_new_number OWNER TO darwin2;

--
-- TOC entry 255 (class 1259 OID 13525142)
-- Name: mukweze_files; Type: TABLE; Schema: eod; Owner: darwin2
--

CREATE TABLE eod.mukweze_files (
    pk integer NOT NULL,
    file character varying,
    format character varying,
    mime character varying
);


ALTER TABLE eod.mukweze_files OWNER TO darwin2;

--
-- TOC entry 256 (class 1259 OID 13525148)
-- Name: mukweze_files_pk_seq; Type: SEQUENCE; Schema: eod; Owner: darwin2
--

CREATE SEQUENCE eod.mukweze_files_pk_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE eod.mukweze_files_pk_seq OWNER TO darwin2;

--
-- TOC entry 5387 (class 0 OID 0)
-- Dependencies: 256
-- Name: mukweze_files_pk_seq; Type: SEQUENCE OWNED BY; Schema: eod; Owner: darwin2
--

ALTER SEQUENCE eod.mukweze_files_pk_seq OWNED BY eod.mukweze_files.pk;


--
-- TOC entry 257 (class 1259 OID 13525150)
-- Name: mukweze_multimedia; Type: TABLE; Schema: eod; Owner: darwin2
--

CREATE TABLE eod.mukweze_multimedia (
    tagno character varying,
    fieldno character varying,
    cumlno character varying,
    datecollected character varying,
    daterecorded character varying,
    museumno character varying,
    weblink character varying,
    hyperlink character varying,
    fieldid character varying,
    speciesid character varying,
    typestatus character varying,
    sex character varying,
    standard_length character varying,
    specimentemperature character varying,
    eod character varying,
    spi character varying,
    dna_samples character varying,
    photo character varying,
    specimencomment character varying,
    family character varying,
    author character varying,
    hardware_used character varying,
    software character varying,
    recordist character varying,
    eodcomment character varying,
    specimenlocality character varying,
    bit_depth character varying,
    localityname character varying,
    localitydate character varying,
    latitude character varying,
    longitude character varying,
    elevation character varying,
    localityconductivity character varying,
    localitytemperature character varying,
    localityph character varying,
    localityoxygen character varying,
    country character varying,
    province character varying,
    basin character varying,
    river character varying,
    localitycomment character varying,
    gear character varying,
    timein character varying,
    timeout character varying,
    collectors character varying,
    metadatafilename character varying,
    catch_invetory character varying
);


ALTER TABLE eod.mukweze_multimedia OWNER TO darwin2;

--
-- TOC entry 258 (class 1259 OID 13525156)
-- Name: mukweze_specimens; Type: TABLE; Schema: eod; Owner: darwin2
--

CREATE TABLE eod.mukweze_specimens (
    code character varying,
    individual_code character varying,
    scientific_name character varying,
    author character varying,
    valid_identification character varying,
    valid_author character varying,
    family character varying,
    type character varying,
    count_min character varying,
    count_max character varying,
    determinators character varying,
    identification_year character varying,
    country character varying,
    exact_site character varying,
    ecology character varying,
    locality_full character varying,
    coordinates_source character varying,
    latitude_deci character varying,
    longitude_deci character varying,
    latitude_dms character varying,
    longitude_dms character varying,
    elevation character varying,
    collecting_year_from character varying,
    collecting_month_from character varying,
    collecting_day_from character varying,
    properties_locality character varying,
    collectors character varying,
    expedition character varying,
    amount_males character varying,
    amount_females character varying,
    amount_juveniles character varying,
    valid_label character varying,
    comments character varying,
    properties character varying,
    eod character varying
);


ALTER TABLE eod.mukweze_specimens OWNER TO darwin2;

--
-- TOC entry 259 (class 1259 OID 13525162)
-- Name: bibliography; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.bibliography (
    id integer NOT NULL,
    title character varying NOT NULL,
    title_indexed character varying NOT NULL,
    type character varying NOT NULL,
    abstract character varying NOT NULL,
    year integer,
    reference character varying,
    doi character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'bibliography'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN title_indexed OPTIONS (
    column_name 'title_indexed'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN abstract OPTIONS (
    column_name 'abstract'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN reference OPTIONS (
    column_name 'reference'
);
ALTER FOREIGN TABLE fdw_113.bibliography ALTER COLUMN doi OPTIONS (
    column_name 'doi'
);


ALTER FOREIGN TABLE fdw_113.bibliography OWNER TO darwin2;

--
-- TOC entry 260 (class 1259 OID 13525165)
-- Name: catalogue_bibliography; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.catalogue_bibliography (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    bibliography_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'catalogue_bibliography'
);
ALTER FOREIGN TABLE fdw_113.catalogue_bibliography ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.catalogue_bibliography ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_bibliography ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_bibliography ALTER COLUMN bibliography_ref OPTIONS (
    column_name 'bibliography_ref'
);


ALTER FOREIGN TABLE fdw_113.catalogue_bibliography OWNER TO darwin2;

--
-- TOC entry 261 (class 1259 OID 13525168)
-- Name: catalogue_relationships; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.catalogue_relationships (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    record_id_1 integer NOT NULL,
    record_id_2 integer NOT NULL,
    relationship_type character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'catalogue_relationships'
);
ALTER FOREIGN TABLE fdw_113.catalogue_relationships ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.catalogue_relationships ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.catalogue_relationships ALTER COLUMN record_id_1 OPTIONS (
    column_name 'record_id_1'
);
ALTER FOREIGN TABLE fdw_113.catalogue_relationships ALTER COLUMN record_id_2 OPTIONS (
    column_name 'record_id_2'
);
ALTER FOREIGN TABLE fdw_113.catalogue_relationships ALTER COLUMN relationship_type OPTIONS (
    column_name 'relationship_type'
);


ALTER FOREIGN TABLE fdw_113.catalogue_relationships OWNER TO darwin2;

--
-- TOC entry 262 (class 1259 OID 13525171)
-- Name: check_dates_danny; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.check_dates_danny (
    unitid character varying,
    accessionnumber character varying,
    acquisitiontype character varying,
    acquiredfrom character varying,
    acquisitionday character varying,
    acquisitionmonth character varying,
    acquisitionyear character varying,
    collectedby character varying,
    collectionstartday character varying,
    collectionstartmonth character varying,
    collectionstartyear character varying,
    collectionendday character varying,
    collectionendmonth character varying,
    collectionendyear character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'check_dates_danny'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN unitid OPTIONS (
    column_name 'unitid'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN accessionnumber OPTIONS (
    column_name 'accessionnumber'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN acquisitiontype OPTIONS (
    column_name 'acquisitiontype'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN acquiredfrom OPTIONS (
    column_name 'acquiredfrom'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN acquisitionday OPTIONS (
    column_name 'acquisitionday'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN acquisitionmonth OPTIONS (
    column_name 'acquisitionmonth'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN acquisitionyear OPTIONS (
    column_name 'acquisitionyear'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectedby OPTIONS (
    column_name 'collectedby'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionstartday OPTIONS (
    column_name 'collectionstartday'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionstartmonth OPTIONS (
    column_name 'collectionstartmonth'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionstartyear OPTIONS (
    column_name 'collectionstartyear'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionendday OPTIONS (
    column_name 'collectionendday'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionendmonth OPTIONS (
    column_name 'collectionendmonth'
);
ALTER FOREIGN TABLE fdw_113.check_dates_danny ALTER COLUMN collectionendyear OPTIONS (
    column_name 'collectionendyear'
);


ALTER FOREIGN TABLE fdw_113.check_dates_danny OWNER TO darwin2;

--
-- TOC entry 263 (class 1259 OID 13525174)
-- Name: chronostratigraphy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.chronostratigraphy (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer,
    id integer NOT NULL,
    lower_bound numeric(10,3),
    upper_bound numeric(10,3)
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'chronostratigraphy'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN lower_bound OPTIONS (
    column_name 'lower_bound'
);
ALTER FOREIGN TABLE fdw_113.chronostratigraphy ALTER COLUMN upper_bound OPTIONS (
    column_name 'upper_bound'
);


ALTER FOREIGN TABLE fdw_113.chronostratigraphy OWNER TO darwin2;

--
-- TOC entry 264 (class 1259 OID 13525177)
-- Name: classification_keywords; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.classification_keywords (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    keyword_type character varying NOT NULL,
    keyword character varying NOT NULL,
    keyword_indexed character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'classification_keywords'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN keyword_type OPTIONS (
    column_name 'keyword_type'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN keyword OPTIONS (
    column_name 'keyword'
);
ALTER FOREIGN TABLE fdw_113.classification_keywords ALTER COLUMN keyword_indexed OPTIONS (
    column_name 'keyword_indexed'
);


ALTER FOREIGN TABLE fdw_113.classification_keywords OWNER TO darwin2;

--
-- TOC entry 265 (class 1259 OID 13525180)
-- Name: classification_synonymies; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.classification_synonymies (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    group_id integer NOT NULL,
    group_name character varying NOT NULL,
    is_basionym boolean,
    order_by integer NOT NULL,
    synonym_record_id integer,
    original_synonym boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'classification_synonymies'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN group_id OPTIONS (
    column_name 'group_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN is_basionym OPTIONS (
    column_name 'is_basionym'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN synonym_record_id OPTIONS (
    column_name 'synonym_record_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies ALTER COLUMN original_synonym OPTIONS (
    column_name 'original_synonym'
);


ALTER FOREIGN TABLE fdw_113.classification_synonymies OWNER TO darwin2;

--
-- TOC entry 266 (class 1259 OID 13525183)
-- Name: classification_synonymies_history; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.classification_synonymies_history (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    group_id integer NOT NULL,
    group_name character varying NOT NULL,
    is_basionym boolean,
    order_by integer NOT NULL,
    synonym_record_id integer,
    modification_date_time timestamp without time zone,
    user_name character varying,
    taxon_name character varying,
    action character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'classification_synonymies_history'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN group_id OPTIONS (
    column_name 'group_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN is_basionym OPTIONS (
    column_name 'is_basionym'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN synonym_record_id OPTIONS (
    column_name 'synonym_record_id'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN user_name OPTIONS (
    column_name 'user_name'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.classification_synonymies_history ALTER COLUMN action OPTIONS (
    column_name 'action'
);


ALTER FOREIGN TABLE fdw_113.classification_synonymies_history OWNER TO darwin2;

--
-- TOC entry 267 (class 1259 OID 13525186)
-- Name: codes_tmp_duplicates; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.codes_tmp_duplicates (
    full_code_indexed character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'codes_tmp_duplicates'
);
ALTER FOREIGN TABLE fdw_113.codes_tmp_duplicates ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);


ALTER FOREIGN TABLE fdw_113.codes_tmp_duplicates OWNER TO darwin2;

--
-- TOC entry 268 (class 1259 OID 13525189)
-- Name: collecting_methods; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.collecting_methods (
    id integer NOT NULL,
    method character varying NOT NULL,
    method_indexed character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'collecting_methods'
);
ALTER FOREIGN TABLE fdw_113.collecting_methods ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.collecting_methods ALTER COLUMN method OPTIONS (
    column_name 'method'
);
ALTER FOREIGN TABLE fdw_113.collecting_methods ALTER COLUMN method_indexed OPTIONS (
    column_name 'method_indexed'
);


ALTER FOREIGN TABLE fdw_113.collecting_methods OWNER TO darwin2;

--
-- TOC entry 269 (class 1259 OID 13525192)
-- Name: collecting_tools; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.collecting_tools (
    id integer NOT NULL,
    tool character varying NOT NULL,
    tool_indexed character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'collecting_tools'
);
ALTER FOREIGN TABLE fdw_113.collecting_tools ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.collecting_tools ALTER COLUMN tool OPTIONS (
    column_name 'tool'
);
ALTER FOREIGN TABLE fdw_113.collecting_tools ALTER COLUMN tool_indexed OPTIONS (
    column_name 'tool_indexed'
);


ALTER FOREIGN TABLE fdw_113.collecting_tools OWNER TO darwin2;

--
-- TOC entry 270 (class 1259 OID 13525195)
-- Name: collection_maintenance; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.collection_maintenance (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    people_ref integer,
    category character varying NOT NULL,
    action_observation character varying NOT NULL,
    description character varying,
    description_indexed text,
    modification_date_time timestamp without time zone NOT NULL,
    modification_date_mask integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'collection_maintenance'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN action_observation OPTIONS (
    column_name 'action_observation'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN description_indexed OPTIONS (
    column_name 'description_indexed'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.collection_maintenance ALTER COLUMN modification_date_mask OPTIONS (
    column_name 'modification_date_mask'
);


ALTER FOREIGN TABLE fdw_113.collection_maintenance OWNER TO darwin2;

--
-- TOC entry 271 (class 1259 OID 13525198)
-- Name: collections_rights; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.collections_rights (
    id integer NOT NULL,
    db_user_type smallint NOT NULL,
    collection_ref integer NOT NULL,
    user_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'collections_rights'
);
ALTER FOREIGN TABLE fdw_113.collections_rights ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.collections_rights ALTER COLUMN db_user_type OPTIONS (
    column_name 'db_user_type'
);
ALTER FOREIGN TABLE fdw_113.collections_rights ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.collections_rights ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);


ALTER FOREIGN TABLE fdw_113.collections_rights OWNER TO darwin2;

--
-- TOC entry 272 (class 1259 OID 13525201)
-- Name: comments; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.comments (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    notion_concerned character varying NOT NULL,
    comment text NOT NULL,
    comment_indexed text NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'comments'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN notion_concerned OPTIONS (
    column_name 'notion_concerned'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);
ALTER FOREIGN TABLE fdw_113.comments ALTER COLUMN comment_indexed OPTIONS (
    column_name 'comment_indexed'
);


ALTER FOREIGN TABLE fdw_113.comments OWNER TO darwin2;

--
-- TOC entry 273 (class 1259 OID 13525204)
-- Name: db_version; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.db_version (
    id integer NOT NULL,
    update_at timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'db_version'
);
ALTER FOREIGN TABLE fdw_113.db_version ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.db_version ALTER COLUMN update_at OPTIONS (
    column_name 'update_at'
);


ALTER FOREIGN TABLE fdw_113.db_version OWNER TO darwin2;

--
-- TOC entry 274 (class 1259 OID 13525207)
-- Name: dissco_continents_to_countries; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.dissco_continents_to_countries (
    continent character varying,
    country_in_darwin character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'dissco_continents_to_countries'
);
ALTER FOREIGN TABLE fdw_113.dissco_continents_to_countries ALTER COLUMN continent OPTIONS (
    column_name 'continent'
);
ALTER FOREIGN TABLE fdw_113.dissco_continents_to_countries ALTER COLUMN country_in_darwin OPTIONS (
    column_name 'country_in_darwin'
);


ALTER FOREIGN TABLE fdw_113.dissco_continents_to_countries OWNER TO darwin2;

--
-- TOC entry 275 (class 1259 OID 13525210)
-- Name: domain_name; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.domain_name (
    fulltoindex character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'domain_name'
);
ALTER FOREIGN TABLE fdw_113.domain_name ALTER COLUMN fulltoindex OPTIONS (
    column_name 'fulltoindex'
);


ALTER FOREIGN TABLE fdw_113.domain_name OWNER TO darwin2;

--
-- TOC entry 276 (class 1259 OID 13525213)
-- Name: expeditions; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.expeditions (
    id integer NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL,
    expedition_from_date_mask integer NOT NULL,
    expedition_from_date date NOT NULL,
    expedition_to_date_mask integer NOT NULL,
    expedition_to_date date NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'expeditions'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN expedition_from_date_mask OPTIONS (
    column_name 'expedition_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN expedition_from_date OPTIONS (
    column_name 'expedition_from_date'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN expedition_to_date_mask OPTIONS (
    column_name 'expedition_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.expeditions ALTER COLUMN expedition_to_date OPTIONS (
    column_name 'expedition_to_date'
);


ALTER FOREIGN TABLE fdw_113.expeditions OWNER TO darwin2;

--
-- TOC entry 277 (class 1259 OID 13525216)
-- Name: fix_date_kin_feb2022; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.fix_date_kin_feb2022 (
    num character varying NOT NULL,
    identificationyear character varying,
    secondary_code character varying,
    coll_day_begin character varying,
    coll_month_begin character varying,
    coll_year_begin character varying,
    coll_day_end character varying,
    coll_month_end character varying,
    coll_year_end character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'fix_date_kin_feb2022'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN num OPTIONS (
    column_name 'num'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN identificationyear OPTIONS (
    column_name 'identificationyear'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN secondary_code OPTIONS (
    column_name 'secondary_code'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_day_begin OPTIONS (
    column_name 'coll_day_begin'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_month_begin OPTIONS (
    column_name 'coll_month_begin'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_year_begin OPTIONS (
    column_name 'coll_year_begin'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_day_end OPTIONS (
    column_name 'coll_day_end'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_month_end OPTIONS (
    column_name 'coll_month_end'
);
ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 ALTER COLUMN coll_year_end OPTIONS (
    column_name 'coll_year_end'
);


ALTER FOREIGN TABLE fdw_113.fix_date_kin_feb2022 OWNER TO darwin2;

--
-- TOC entry 278 (class 1259 OID 13525219)
-- Name: identifiers; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.identifiers (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    protocol character varying,
    value character varying,
    creation_date timestamp with time zone NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN protocol OPTIONS (
    column_name 'protocol'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN value OPTIONS (
    column_name 'value'
);
ALTER FOREIGN TABLE fdw_113.identifiers ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);


ALTER FOREIGN TABLE fdw_113.identifiers OWNER TO darwin2;

--
-- TOC entry 279 (class 1259 OID 13525222)
-- Name: igs; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.igs (
    id integer NOT NULL,
    ig_num character varying NOT NULL,
    ig_num_indexed character varying NOT NULL,
    ig_date_mask integer NOT NULL,
    ig_date date NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'igs'
);
ALTER FOREIGN TABLE fdw_113.igs ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.igs ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.igs ALTER COLUMN ig_num_indexed OPTIONS (
    column_name 'ig_num_indexed'
);
ALTER FOREIGN TABLE fdw_113.igs ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.igs ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);


ALTER FOREIGN TABLE fdw_113.igs OWNER TO darwin2;

--
-- TOC entry 280 (class 1259 OID 13525225)
-- Name: import_fruitfly_drybarcodes_20211006; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 (
    id character varying,
    _2didnumber character varying,
    cabinet character varying,
    drawer character varying,
    specimenid character varying,
    sex character varying,
    tubeidfrozencollection character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'import_fruitfly_drybarcodes_20211006'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN _2didnumber OPTIONS (
    column_name '_2didnumber'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN cabinet OPTIONS (
    column_name 'cabinet'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN drawer OPTIONS (
    column_name 'drawer'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN specimenid OPTIONS (
    column_name 'specimenid'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 ALTER COLUMN tubeidfrozencollection OPTIONS (
    column_name 'tubeidfrozencollection'
);


ALTER FOREIGN TABLE fdw_113.import_fruitfly_drybarcodes_20211006 OWNER TO darwin2;

--
-- TOC entry 281 (class 1259 OID 13525228)
-- Name: imports; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.imports (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    format character varying NOT NULL,
    collection_ref integer,
    filename character varying NOT NULL,
    state character varying NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    initial_count integer NOT NULL,
    is_finished boolean NOT NULL,
    errors_in_import text,
    template_version text,
    exclude_invalid_entries boolean NOT NULL,
    specimen_taxonomy_ref integer,
    taxonomy_name character varying,
    creation_date date,
    creation_date_mask integer,
    definition_taxonomy text,
    is_reference_taxonomy boolean,
    source_taxonomy character varying,
    url_website_taxonomy character varying,
    url_webservice_taxonomy character varying,
    working boolean,
    mime_type character varying,
    taxonomy_kingdom character varying,
    history_taxonomy public.hstore,
    merge_gtu boolean,
    add_collection_prefix boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'imports'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN format OPTIONS (
    column_name 'format'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN filename OPTIONS (
    column_name 'filename'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN created_at OPTIONS (
    column_name 'created_at'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN updated_at OPTIONS (
    column_name 'updated_at'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN initial_count OPTIONS (
    column_name 'initial_count'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN is_finished OPTIONS (
    column_name 'is_finished'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN errors_in_import OPTIONS (
    column_name 'errors_in_import'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN template_version OPTIONS (
    column_name 'template_version'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN exclude_invalid_entries OPTIONS (
    column_name 'exclude_invalid_entries'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN specimen_taxonomy_ref OPTIONS (
    column_name 'specimen_taxonomy_ref'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN taxonomy_name OPTIONS (
    column_name 'taxonomy_name'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN creation_date_mask OPTIONS (
    column_name 'creation_date_mask'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN definition_taxonomy OPTIONS (
    column_name 'definition_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN is_reference_taxonomy OPTIONS (
    column_name 'is_reference_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN source_taxonomy OPTIONS (
    column_name 'source_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN url_website_taxonomy OPTIONS (
    column_name 'url_website_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN url_webservice_taxonomy OPTIONS (
    column_name 'url_webservice_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN working OPTIONS (
    column_name 'working'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN mime_type OPTIONS (
    column_name 'mime_type'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN taxonomy_kingdom OPTIONS (
    column_name 'taxonomy_kingdom'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN history_taxonomy OPTIONS (
    column_name 'history_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN merge_gtu OPTIONS (
    column_name 'merge_gtu'
);
ALTER FOREIGN TABLE fdw_113.imports ALTER COLUMN add_collection_prefix OPTIONS (
    column_name 'add_collection_prefix'
);


ALTER FOREIGN TABLE fdw_113.imports OWNER TO darwin2;

--
-- TOC entry 282 (class 1259 OID 13525231)
-- Name: informative_workflow; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.informative_workflow (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    user_ref integer,
    formated_name character varying NOT NULL,
    status character varying NOT NULL,
    modification_date_time timestamp without time zone NOT NULL,
    is_last boolean NOT NULL,
    comment character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'informative_workflow'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN is_last OPTIONS (
    column_name 'is_last'
);
ALTER FOREIGN TABLE fdw_113.informative_workflow ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);


ALTER FOREIGN TABLE fdw_113.informative_workflow OWNER TO darwin2;

--
-- TOC entry 283 (class 1259 OID 13525234)
-- Name: insurances; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.insurances (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    insurance_value numeric(16,2) NOT NULL,
    insurance_currency character varying NOT NULL,
    date_from_mask integer NOT NULL,
    date_from date NOT NULL,
    date_to_mask integer NOT NULL,
    date_to date NOT NULL,
    insurer_ref integer,
    contact_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'insurances'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN insurance_value OPTIONS (
    column_name 'insurance_value'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN insurance_currency OPTIONS (
    column_name 'insurance_currency'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN date_from_mask OPTIONS (
    column_name 'date_from_mask'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN date_from OPTIONS (
    column_name 'date_from'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN date_to_mask OPTIONS (
    column_name 'date_to_mask'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN date_to OPTIONS (
    column_name 'date_to'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN insurer_ref OPTIONS (
    column_name 'insurer_ref'
);
ALTER FOREIGN TABLE fdw_113.insurances ALTER COLUMN contact_ref OPTIONS (
    column_name 'contact_ref'
);


ALTER FOREIGN TABLE fdw_113.insurances OWNER TO darwin2;

--
-- TOC entry 284 (class 1259 OID 13525237)
-- Name: lithology; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.lithology (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer,
    id integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'lithology'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.lithology ALTER COLUMN id OPTIONS (
    column_name 'id'
);


ALTER FOREIGN TABLE fdw_113.lithology OWNER TO darwin2;

--
-- TOC entry 285 (class 1259 OID 13525240)
-- Name: lithostratigraphy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.lithostratigraphy (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer,
    id integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'lithostratigraphy'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.lithostratigraphy ALTER COLUMN id OPTIONS (
    column_name 'id'
);


ALTER FOREIGN TABLE fdw_113.lithostratigraphy OWNER TO darwin2;

--
-- TOC entry 286 (class 1259 OID 13525243)
-- Name: loan_history; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.loan_history (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    referenced_table text NOT NULL,
    modification_date_time timestamp without time zone NOT NULL,
    record_line public.hstore
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'loan_history'
);
ALTER FOREIGN TABLE fdw_113.loan_history ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.loan_history ALTER COLUMN loan_ref OPTIONS (
    column_name 'loan_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_history ALTER COLUMN referenced_table OPTIONS (
    column_name 'referenced_table'
);
ALTER FOREIGN TABLE fdw_113.loan_history ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.loan_history ALTER COLUMN record_line OPTIONS (
    column_name 'record_line'
);


ALTER FOREIGN TABLE fdw_113.loan_history OWNER TO darwin2;

--
-- TOC entry 287 (class 1259 OID 13525246)
-- Name: loan_items; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.loan_items (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    ig_ref integer,
    from_date date,
    to_date date,
    specimen_ref integer NOT NULL,
    details character varying,
    specimen_count_tot integer,
    specimen_count_males integer,
    specimen_count_females integer,
    specimen_count_juveniles integer,
    specimen_part character varying,
    specimen_count character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'loan_items'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN loan_ref OPTIONS (
    column_name 'loan_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN details OPTIONS (
    column_name 'details'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_count_tot OPTIONS (
    column_name 'specimen_count_tot'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_count_males OPTIONS (
    column_name 'specimen_count_males'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_count_females OPTIONS (
    column_name 'specimen_count_females'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_count_juveniles OPTIONS (
    column_name 'specimen_count_juveniles'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.loan_items ALTER COLUMN specimen_count OPTIONS (
    column_name 'specimen_count'
);


ALTER FOREIGN TABLE fdw_113.loan_items OWNER TO darwin2;

--
-- TOC entry 288 (class 1259 OID 13525249)
-- Name: loan_rights; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.loan_rights (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    user_ref integer NOT NULL,
    has_encoding_right boolean NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'loan_rights'
);
ALTER FOREIGN TABLE fdw_113.loan_rights ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.loan_rights ALTER COLUMN loan_ref OPTIONS (
    column_name 'loan_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_rights ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_rights ALTER COLUMN has_encoding_right OPTIONS (
    column_name 'has_encoding_right'
);


ALTER FOREIGN TABLE fdw_113.loan_rights OWNER TO darwin2;

--
-- TOC entry 289 (class 1259 OID 13525252)
-- Name: loan_status; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.loan_status (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    user_ref integer NOT NULL,
    status character varying NOT NULL,
    modification_date_time timestamp without time zone NOT NULL,
    comment character varying NOT NULL,
    is_last boolean NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'loan_status'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN loan_ref OPTIONS (
    column_name 'loan_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);
ALTER FOREIGN TABLE fdw_113.loan_status ALTER COLUMN is_last OPTIONS (
    column_name 'is_last'
);


ALTER FOREIGN TABLE fdw_113.loan_status OWNER TO darwin2;

--
-- TOC entry 290 (class 1259 OID 13525255)
-- Name: loans; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.loans (
    id integer NOT NULL,
    name character varying NOT NULL,
    description character varying NOT NULL,
    search_indexed text NOT NULL,
    from_date date,
    to_date date,
    extended_to_date date,
    collection_ref integer,
    address_receiver character varying,
    institution_receiver character varying,
    country_receiver character varying,
    zip_receiver character varying,
    city_receiver character varying(50),
    collection_manager character varying,
    collection_manager_title character varying,
    collection_manager_mail character varying,
    non_cites boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'loans'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN search_indexed OPTIONS (
    column_name 'search_indexed'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN extended_to_date OPTIONS (
    column_name 'extended_to_date'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN address_receiver OPTIONS (
    column_name 'address_receiver'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN institution_receiver OPTIONS (
    column_name 'institution_receiver'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN country_receiver OPTIONS (
    column_name 'country_receiver'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN zip_receiver OPTIONS (
    column_name 'zip_receiver'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN city_receiver OPTIONS (
    column_name 'city_receiver'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN collection_manager OPTIONS (
    column_name 'collection_manager'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN collection_manager_title OPTIONS (
    column_name 'collection_manager_title'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN collection_manager_mail OPTIONS (
    column_name 'collection_manager_mail'
);
ALTER FOREIGN TABLE fdw_113.loans ALTER COLUMN non_cites OPTIONS (
    column_name 'non_cites'
);


ALTER FOREIGN TABLE fdw_113.loans OWNER TO darwin2;

--
-- TOC entry 291 (class 1259 OID 13525258)
-- Name: mineralogy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mineralogy (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer,
    id integer NOT NULL,
    code character varying NOT NULL,
    classification character varying NOT NULL,
    formule character varying,
    formule_indexed character varying,
    cristal_system character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mineralogy'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN classification OPTIONS (
    column_name 'classification'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN formule OPTIONS (
    column_name 'formule'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN formule_indexed OPTIONS (
    column_name 'formule_indexed'
);
ALTER FOREIGN TABLE fdw_113.mineralogy ALTER COLUMN cristal_system OPTIONS (
    column_name 'cristal_system'
);


ALTER FOREIGN TABLE fdw_113.mineralogy OWNER TO darwin2;

--
-- TOC entry 292 (class 1259 OID 13525261)
-- Name: multimedia; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.multimedia (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    is_digital boolean NOT NULL,
    type character varying NOT NULL,
    sub_type character varying,
    title character varying NOT NULL,
    description character varying NOT NULL,
    uri character varying,
    filename character varying,
    search_indexed text NOT NULL,
    creation_date date NOT NULL,
    creation_date_mask integer NOT NULL,
    mime_type character varying NOT NULL,
    visible boolean NOT NULL,
    publishable boolean NOT NULL,
    extracted_info text,
    technical_parameters character varying,
    internet_protocol character varying,
    field_observations character varying,
    external_uri character varying,
    import_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'multimedia'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN is_digital OPTIONS (
    column_name 'is_digital'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN sub_type OPTIONS (
    column_name 'sub_type'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN uri OPTIONS (
    column_name 'uri'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN filename OPTIONS (
    column_name 'filename'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN search_indexed OPTIONS (
    column_name 'search_indexed'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN creation_date_mask OPTIONS (
    column_name 'creation_date_mask'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN mime_type OPTIONS (
    column_name 'mime_type'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN visible OPTIONS (
    column_name 'visible'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN publishable OPTIONS (
    column_name 'publishable'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN extracted_info OPTIONS (
    column_name 'extracted_info'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN technical_parameters OPTIONS (
    column_name 'technical_parameters'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN internet_protocol OPTIONS (
    column_name 'internet_protocol'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN field_observations OPTIONS (
    column_name 'field_observations'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN external_uri OPTIONS (
    column_name 'external_uri'
);
ALTER FOREIGN TABLE fdw_113.multimedia ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);


ALTER FOREIGN TABLE fdw_113.multimedia OWNER TO darwin2;

--
-- TOC entry 293 (class 1259 OID 13525264)
-- Name: multimedia_todelete; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.multimedia_todelete (
    id integer NOT NULL,
    uri text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'multimedia_todelete'
);
ALTER FOREIGN TABLE fdw_113.multimedia_todelete ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.multimedia_todelete ALTER COLUMN uri OPTIONS (
    column_name 'uri'
);


ALTER FOREIGN TABLE fdw_113.multimedia_todelete OWNER TO darwin2;

--
-- TOC entry 294 (class 1259 OID 13525267)
-- Name: mv_mids_stat_larissa; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_mids_stat_larissa (
    collection_path_text text,
    main_collection text,
    sub_collection_1 text,
    sub_collection_2 text,
    collection_name character varying,
    mids_level integer,
    container_type character varying,
    container_storage character varying,
    count bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_mids_stat_larissa'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN collection_path_text OPTIONS (
    column_name 'collection_path_text'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN main_collection OPTIONS (
    column_name 'main_collection'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN sub_collection_1 OPTIONS (
    column_name 'sub_collection_1'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN sub_collection_2 OPTIONS (
    column_name 'sub_collection_2'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa ALTER COLUMN count OPTIONS (
    column_name 'count'
);


ALTER FOREIGN TABLE fdw_113.mv_mids_stat_larissa OWNER TO darwin2;

--
-- TOC entry 295 (class 1259 OID 13525270)
-- Name: mv_mids_stats_larissa_with_type_country; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country (
    collection_path_text text,
    collection_name character varying,
    main_collection text,
    sub_collection text,
    sub_collection_2 text,
    mids_level integer,
    type character varying,
    gtu_country_tag_value character varying,
    container_type character varying,
    container_storage character varying,
    count bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_mids_stats_larissa_with_type_country'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN collection_path_text OPTIONS (
    column_name 'collection_path_text'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN main_collection OPTIONS (
    column_name 'main_collection'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN sub_collection OPTIONS (
    column_name 'sub_collection'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN sub_collection_2 OPTIONS (
    column_name 'sub_collection_2'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country ALTER COLUMN count OPTIONS (
    column_name 'count'
);


ALTER FOREIGN TABLE fdw_113.mv_mids_stats_larissa_with_type_country OWNER TO darwin2;

--
-- TOC entry 242 (class 1259 OID 13525039)
-- Name: mv_specimen_public; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_specimen_public (
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
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_specimen_public'
);


ALTER FOREIGN TABLE fdw_113.mv_specimen_public OWNER TO darwin2;

--
-- TOC entry 296 (class 1259 OID 13525273)
-- Name: mv_specimens_mids; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_specimens_mids (
    id integer,
    main_code text,
    category character varying,
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
    specimen_part character varying,
    complete boolean,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    shelf character varying,
    container character varying,
    sub_container character varying,
    container_type character varying,
    sub_container_type character varying,
    container_storage character varying,
    sub_container_storage character varying,
    surnumerary boolean,
    specimen_status character varying,
    specimen_count_min integer,
    specimen_count_max integer,
    object_name text,
    object_name_indexed text,
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
    taxon_name character varying,
    taxon_name_indexed character varying,
    taxon_level_ref integer,
    taxon_level_name character varying,
    taxon_status character varying,
    taxon_path character varying,
    taxon_parent_ref integer,
    taxon_extinct boolean,
    litho_name character varying,
    family character varying,
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
    col character varying,
    gtu_location point,
    specimen_creation_date timestamp without time zone,
    import_ref integer,
    main_code_indexed character varying,
    specimen_count_males_min integer,
    specimen_count_males_max integer,
    specimen_count_females_min integer,
    specimen_count_females_max integer,
    specimen_count_juveniles_min integer,
    specimen_count_juveniles_max integer,
    nagoya character varying,
    uuid uuid,
    mids_level integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_specimens_mids'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN main_code OPTIONS (
    column_name 'main_code'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN expedition_ref OPTIONS (
    column_name 'expedition_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_ref OPTIONS (
    column_name 'litho_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_ref OPTIONS (
    column_name 'chrono_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_ref OPTIONS (
    column_name 'lithology_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN station_visible OPTIONS (
    column_name 'station_visible'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN type_group OPTIONS (
    column_name 'type_group'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN type_search OPTIONS (
    column_name 'type_search'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN rock_form OPTIONS (
    column_name 'rock_form'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN spec_ident_ids OPTIONS (
    column_name 'spec_ident_ids'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN spec_coll_ids OPTIONS (
    column_name 'spec_coll_ids'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN spec_don_sel_ids OPTIONS (
    column_name 'spec_don_sel_ids'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_is_public OPTIONS (
    column_name 'collection_is_public'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_parent_ref OPTIONS (
    column_name 'collection_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN expedition_name_indexed OPTIONS (
    column_name 'expedition_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_tag_values_indexed OPTIONS (
    column_name 'gtu_tag_values_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_province_tag_indexed OPTIONS (
    column_name 'gtu_province_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_others_tag_indexed OPTIONS (
    column_name 'gtu_others_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_elevation_accuracy OPTIONS (
    column_name 'gtu_elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_name_indexed OPTIONS (
    column_name 'taxon_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_status OPTIONS (
    column_name 'taxon_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_parent_ref OPTIONS (
    column_name 'taxon_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN taxon_extinct OPTIONS (
    column_name 'taxon_extinct'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_name OPTIONS (
    column_name 'litho_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_name_indexed OPTIONS (
    column_name 'litho_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_level_ref OPTIONS (
    column_name 'litho_level_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_level_name OPTIONS (
    column_name 'litho_level_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_status OPTIONS (
    column_name 'litho_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_local OPTIONS (
    column_name 'litho_local'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_color OPTIONS (
    column_name 'litho_color'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_path OPTIONS (
    column_name 'litho_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN litho_parent_ref OPTIONS (
    column_name 'litho_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_name OPTIONS (
    column_name 'chrono_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_name_indexed OPTIONS (
    column_name 'chrono_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_level_ref OPTIONS (
    column_name 'chrono_level_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_level_name OPTIONS (
    column_name 'chrono_level_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_status OPTIONS (
    column_name 'chrono_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_local OPTIONS (
    column_name 'chrono_local'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_color OPTIONS (
    column_name 'chrono_color'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_path OPTIONS (
    column_name 'chrono_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN chrono_parent_ref OPTIONS (
    column_name 'chrono_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_name OPTIONS (
    column_name 'lithology_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_name_indexed OPTIONS (
    column_name 'lithology_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_level_ref OPTIONS (
    column_name 'lithology_level_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_level_name OPTIONS (
    column_name 'lithology_level_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_status OPTIONS (
    column_name 'lithology_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_local OPTIONS (
    column_name 'lithology_local'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_color OPTIONS (
    column_name 'lithology_color'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_path OPTIONS (
    column_name 'lithology_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN lithology_parent_ref OPTIONS (
    column_name 'lithology_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_name OPTIONS (
    column_name 'mineral_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_name_indexed OPTIONS (
    column_name 'mineral_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_level_ref OPTIONS (
    column_name 'mineral_level_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_level_name OPTIONS (
    column_name 'mineral_level_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_status OPTIONS (
    column_name 'mineral_status'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_local OPTIONS (
    column_name 'mineral_local'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_color OPTIONS (
    column_name 'mineral_color'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_path OPTIONS (
    column_name 'mineral_path'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mineral_parent_ref OPTIONS (
    column_name 'mineral_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN ig_num_indexed OPTIONS (
    column_name 'ig_num_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN gtu_location OPTIONS (
    column_name 'gtu_location'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN main_code_indexed OPTIONS (
    column_name 'main_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_males_min OPTIONS (
    column_name 'specimen_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_males_max OPTIONS (
    column_name 'specimen_count_males_max'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_females_min OPTIONS (
    column_name 'specimen_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_females_max OPTIONS (
    column_name 'specimen_count_females_max'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_juveniles_min OPTIONS (
    column_name 'specimen_count_juveniles_min'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN specimen_count_juveniles_max OPTIONS (
    column_name 'specimen_count_juveniles_max'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);


ALTER FOREIGN TABLE fdw_113.mv_specimens_mids OWNER TO darwin2;

--
-- TOC entry 297 (class 1259 OID 13525276)
-- Name: mv_specimens_mids_simplified; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_specimens_mids_simplified (
    collection_name character varying,
    id integer,
    type character varying,
    family character varying,
    gtu_country_tag_value character varying,
    gtu_province_tag_value character varying,
    gtu_others_tag_value character varying,
    container_type character varying,
    container_storage character varying,
    mids_level integer,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_specimens_mids_simplified'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified OWNER TO darwin2;

--
-- TOC entry 298 (class 1259 OID 13525279)
-- Name: mv_specimens_mids_simplified_coll_hierarchy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy (
    collection_path_text text,
    collection_name character varying,
    id integer,
    type character varying,
    family character varying,
    gtu_country_tag_value character varying,
    gtu_province_tag_value character varying,
    gtu_others_tag_value character varying,
    container_type character varying,
    container_storage character varying,
    mids_level integer,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_specimens_mids_simplified_coll_hierarchy'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN collection_path_text OPTIONS (
    column_name 'collection_path_text'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy OWNER TO darwin2;

--
-- TOC entry 299 (class 1259 OID 13525282)
-- Name: mv_specimens_mids_simplified_coll_hierarchy_2; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 (
    collection_path_text text,
    collection_name character varying,
    main_collection text,
    sub_collection text,
    sub_collection_2 text,
    id integer,
    type character varying,
    family character varying,
    gtu_country_tag_value character varying,
    gtu_province_tag_value character varying,
    gtu_others_tag_value character varying,
    container_type character varying,
    container_storage character varying,
    mids_level integer,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'mv_specimens_mids_simplified_coll_hierarchy_2'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN collection_path_text OPTIONS (
    column_name 'collection_path_text'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN main_collection OPTIONS (
    column_name 'main_collection'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN sub_collection OPTIONS (
    column_name 'sub_collection'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN sub_collection_2 OPTIONS (
    column_name 'sub_collection_2'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 OWNER TO darwin2;

--
-- TOC entry 300 (class 1259 OID 13525285)
-- Name: my_saved_searches; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.my_saved_searches (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    name character varying NOT NULL,
    search_criterias character varying NOT NULL,
    favorite boolean NOT NULL,
    modification_date_time timestamp without time zone NOT NULL,
    visible_fields_in_result character varying NOT NULL,
    is_only_id boolean NOT NULL,
    subject character varying NOT NULL,
    query_where character varying,
    query_parameters character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'my_saved_searches'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN search_criterias OPTIONS (
    column_name 'search_criterias'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN favorite OPTIONS (
    column_name 'favorite'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN visible_fields_in_result OPTIONS (
    column_name 'visible_fields_in_result'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN is_only_id OPTIONS (
    column_name 'is_only_id'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN subject OPTIONS (
    column_name 'subject'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN query_where OPTIONS (
    column_name 'query_where'
);
ALTER FOREIGN TABLE fdw_113.my_saved_searches ALTER COLUMN query_parameters OPTIONS (
    column_name 'query_parameters'
);


ALTER FOREIGN TABLE fdw_113.my_saved_searches OWNER TO darwin2;

--
-- TOC entry 301 (class 1259 OID 13525288)
-- Name: my_widgets; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.my_widgets (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    category character varying NOT NULL,
    group_name character varying NOT NULL,
    order_by smallint NOT NULL,
    col_num smallint NOT NULL,
    mandatory boolean NOT NULL,
    visible boolean NOT NULL,
    opened boolean NOT NULL,
    color character varying NOT NULL,
    is_available boolean NOT NULL,
    icon_ref integer,
    title_perso character varying(32),
    collections character varying NOT NULL,
    all_public boolean NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'my_widgets'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN col_num OPTIONS (
    column_name 'col_num'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN mandatory OPTIONS (
    column_name 'mandatory'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN visible OPTIONS (
    column_name 'visible'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN opened OPTIONS (
    column_name 'opened'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN is_available OPTIONS (
    column_name 'is_available'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN icon_ref OPTIONS (
    column_name 'icon_ref'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN title_perso OPTIONS (
    column_name 'title_perso'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN collections OPTIONS (
    column_name 'collections'
);
ALTER FOREIGN TABLE fdw_113.my_widgets ALTER COLUMN all_public OPTIONS (
    column_name 'all_public'
);


ALTER FOREIGN TABLE fdw_113.my_widgets OWNER TO darwin2;

--
-- TOC entry 302 (class 1259 OID 13525291)
-- Name: my_widgets_rmca; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.my_widgets_rmca (
    id integer,
    user_ref integer,
    category character varying,
    group_name character varying,
    order_by smallint,
    col_num smallint,
    mandatory boolean,
    visible boolean,
    opened boolean,
    color character varying,
    is_available boolean,
    icon_ref integer,
    title_perso character varying(32),
    collections character varying,
    all_public boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'my_widgets_rmca'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN col_num OPTIONS (
    column_name 'col_num'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN mandatory OPTIONS (
    column_name 'mandatory'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN visible OPTIONS (
    column_name 'visible'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN opened OPTIONS (
    column_name 'opened'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN is_available OPTIONS (
    column_name 'is_available'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN icon_ref OPTIONS (
    column_name 'icon_ref'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN title_perso OPTIONS (
    column_name 'title_perso'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN collections OPTIONS (
    column_name 'collections'
);
ALTER FOREIGN TABLE fdw_113.my_widgets_rmca ALTER COLUMN all_public OPTIONS (
    column_name 'all_public'
);


ALTER FOREIGN TABLE fdw_113.my_widgets_rmca OWNER TO darwin2;

--
-- TOC entry 303 (class 1259 OID 13525294)
-- Name: people_addresses; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people_addresses (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL,
    po_box character varying,
    extended_address character varying,
    locality character varying NOT NULL,
    region character varying,
    zip_code character varying,
    country character varying NOT NULL,
    id integer NOT NULL,
    tag character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people_addresses'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN person_user_ref OPTIONS (
    column_name 'person_user_ref'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN entry OPTIONS (
    column_name 'entry'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN po_box OPTIONS (
    column_name 'po_box'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN extended_address OPTIONS (
    column_name 'extended_address'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN locality OPTIONS (
    column_name 'locality'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN region OPTIONS (
    column_name 'region'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN zip_code OPTIONS (
    column_name 'zip_code'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people_addresses ALTER COLUMN tag OPTIONS (
    column_name 'tag'
);


ALTER FOREIGN TABLE fdw_113.people_addresses OWNER TO darwin2;

--
-- TOC entry 304 (class 1259 OID 13525297)
-- Name: people_align_debug; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people_align_debug (
    filename character varying,
    unitid character varying,
    people_role character varying,
    people_name character varying,
    people_fk integer,
    specimen_fk integer,
    id integer NOT NULL,
    specimen_fks integer[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people_align_debug'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN filename OPTIONS (
    column_name 'filename'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN unitid OPTIONS (
    column_name 'unitid'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN people_role OPTIONS (
    column_name 'people_role'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN people_name OPTIONS (
    column_name 'people_name'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN people_fk OPTIONS (
    column_name 'people_fk'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN specimen_fk OPTIONS (
    column_name 'specimen_fk'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people_align_debug ALTER COLUMN specimen_fks OPTIONS (
    column_name 'specimen_fks'
);


ALTER FOREIGN TABLE fdw_113.people_align_debug OWNER TO darwin2;

--
-- TOC entry 305 (class 1259 OID 13525300)
-- Name: people_comm; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people_comm (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL,
    id integer NOT NULL,
    comm_type character varying NOT NULL,
    tag character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people_comm'
);
ALTER FOREIGN TABLE fdw_113.people_comm ALTER COLUMN person_user_ref OPTIONS (
    column_name 'person_user_ref'
);
ALTER FOREIGN TABLE fdw_113.people_comm ALTER COLUMN entry OPTIONS (
    column_name 'entry'
);
ALTER FOREIGN TABLE fdw_113.people_comm ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people_comm ALTER COLUMN comm_type OPTIONS (
    column_name 'comm_type'
);
ALTER FOREIGN TABLE fdw_113.people_comm ALTER COLUMN tag OPTIONS (
    column_name 'tag'
);


ALTER FOREIGN TABLE fdw_113.people_comm OWNER TO darwin2;

--
-- TOC entry 306 (class 1259 OID 13525303)
-- Name: people_languages; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people_languages (
    id integer NOT NULL,
    language_country character varying NOT NULL,
    mother boolean NOT NULL,
    preferred_language boolean NOT NULL,
    people_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people_languages'
);
ALTER FOREIGN TABLE fdw_113.people_languages ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people_languages ALTER COLUMN language_country OPTIONS (
    column_name 'language_country'
);
ALTER FOREIGN TABLE fdw_113.people_languages ALTER COLUMN mother OPTIONS (
    column_name 'mother'
);
ALTER FOREIGN TABLE fdw_113.people_languages ALTER COLUMN preferred_language OPTIONS (
    column_name 'preferred_language'
);
ALTER FOREIGN TABLE fdw_113.people_languages ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);


ALTER FOREIGN TABLE fdw_113.people_languages OWNER TO darwin2;

--
-- TOC entry 307 (class 1259 OID 13525306)
-- Name: people_relationships; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.people_relationships (
    id integer NOT NULL,
    person_user_role character varying,
    relationship_type character varying NOT NULL,
    person_1_ref integer NOT NULL,
    person_2_ref integer NOT NULL,
    path character varying,
    activity_date_from_mask integer NOT NULL,
    activity_date_from date NOT NULL,
    activity_date_to_mask integer NOT NULL,
    activity_date_to date NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'people_relationships'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN person_user_role OPTIONS (
    column_name 'person_user_role'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN relationship_type OPTIONS (
    column_name 'relationship_type'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN person_1_ref OPTIONS (
    column_name 'person_1_ref'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN person_2_ref OPTIONS (
    column_name 'person_2_ref'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN activity_date_from_mask OPTIONS (
    column_name 'activity_date_from_mask'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN activity_date_from OPTIONS (
    column_name 'activity_date_from'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN activity_date_to_mask OPTIONS (
    column_name 'activity_date_to_mask'
);
ALTER FOREIGN TABLE fdw_113.people_relationships ALTER COLUMN activity_date_to OPTIONS (
    column_name 'activity_date_to'
);


ALTER FOREIGN TABLE fdw_113.people_relationships OWNER TO darwin2;

--
-- TOC entry 308 (class 1259 OID 13525309)
-- Name: possible_upper_levels; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.possible_upper_levels (
    level_ref integer NOT NULL,
    level_upper_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'possible_upper_levels'
);
ALTER FOREIGN TABLE fdw_113.possible_upper_levels ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.possible_upper_levels ALTER COLUMN level_upper_ref OPTIONS (
    column_name 'level_upper_ref'
);


ALTER FOREIGN TABLE fdw_113.possible_upper_levels OWNER TO darwin2;

--
-- TOC entry 309 (class 1259 OID 13525312)
-- Name: preferences; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.preferences (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    pref_key character varying NOT NULL,
    pref_value character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'preferences'
);
ALTER FOREIGN TABLE fdw_113.preferences ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.preferences ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.preferences ALTER COLUMN pref_key OPTIONS (
    column_name 'pref_key'
);
ALTER FOREIGN TABLE fdw_113.preferences ALTER COLUMN pref_value OPTIONS (
    column_name 'pref_value'
);


ALTER FOREIGN TABLE fdw_113.preferences OWNER TO darwin2;

--
-- TOC entry 310 (class 1259 OID 13525315)
-- Name: properties; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.properties (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    property_type character varying NOT NULL,
    applies_to character varying NOT NULL,
    applies_to_indexed character varying NOT NULL,
    date_from_mask integer NOT NULL,
    date_from timestamp without time zone NOT NULL,
    date_to_mask integer NOT NULL,
    date_to timestamp without time zone NOT NULL,
    is_quantitative boolean NOT NULL,
    property_unit character varying NOT NULL,
    method character varying,
    method_indexed character varying NOT NULL,
    lower_value character varying NOT NULL,
    lower_value_unified double precision,
    upper_value character varying NOT NULL,
    upper_value_unified double precision,
    property_accuracy character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'properties'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN property_type OPTIONS (
    column_name 'property_type'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN applies_to OPTIONS (
    column_name 'applies_to'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN applies_to_indexed OPTIONS (
    column_name 'applies_to_indexed'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN date_from_mask OPTIONS (
    column_name 'date_from_mask'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN date_from OPTIONS (
    column_name 'date_from'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN date_to_mask OPTIONS (
    column_name 'date_to_mask'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN date_to OPTIONS (
    column_name 'date_to'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN is_quantitative OPTIONS (
    column_name 'is_quantitative'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN property_unit OPTIONS (
    column_name 'property_unit'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN method OPTIONS (
    column_name 'method'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN method_indexed OPTIONS (
    column_name 'method_indexed'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN lower_value OPTIONS (
    column_name 'lower_value'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN lower_value_unified OPTIONS (
    column_name 'lower_value_unified'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN upper_value OPTIONS (
    column_name 'upper_value'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN upper_value_unified OPTIONS (
    column_name 'upper_value_unified'
);
ALTER FOREIGN TABLE fdw_113.properties ALTER COLUMN property_accuracy OPTIONS (
    column_name 'property_accuracy'
);


ALTER FOREIGN TABLE fdw_113.properties OWNER TO darwin2;

--
-- TOC entry 311 (class 1259 OID 13525318)
-- Name: specimen_collecting_methods; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimen_collecting_methods (
    id integer NOT NULL,
    specimen_ref integer NOT NULL,
    collecting_method_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimen_collecting_methods'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_methods ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_methods ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_methods ALTER COLUMN collecting_method_ref OPTIONS (
    column_name 'collecting_method_ref'
);


ALTER FOREIGN TABLE fdw_113.specimen_collecting_methods OWNER TO darwin2;

--
-- TOC entry 312 (class 1259 OID 13525321)
-- Name: specimen_collecting_tools; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimen_collecting_tools (
    id integer NOT NULL,
    specimen_ref integer NOT NULL,
    collecting_tool_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimen_collecting_tools'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_tools ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_tools ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.specimen_collecting_tools ALTER COLUMN collecting_tool_ref OPTIONS (
    column_name 'collecting_tool_ref'
);


ALTER FOREIGN TABLE fdw_113.specimen_collecting_tools OWNER TO darwin2;

--
-- TOC entry 313 (class 1259 OID 13525324)
-- Name: specimens_detect_wrong_countries; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimens_detect_wrong_countries (
    id integer NOT NULL,
    gtu_country_tag_indexed character varying[],
    geom public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimens_detect_wrong_countries'
);
ALTER FOREIGN TABLE fdw_113.specimens_detect_wrong_countries ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimens_detect_wrong_countries ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_detect_wrong_countries ALTER COLUMN geom OPTIONS (
    column_name 'geom'
);


ALTER FOREIGN TABLE fdw_113.specimens_detect_wrong_countries OWNER TO darwin2;

--
-- TOC entry 314 (class 1259 OID 13525327)
-- Name: specimens_relationships; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimens_relationships (
    id integer NOT NULL,
    specimen_ref integer,
    relationship_type character varying,
    unit_type character varying,
    specimen_related_ref integer,
    taxon_ref integer,
    mineral_ref integer,
    institution_ref integer,
    source_name text,
    source_id text,
    quantity numeric(16,2),
    unit character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimens_relationships'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN relationship_type OPTIONS (
    column_name 'relationship_type'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN unit_type OPTIONS (
    column_name 'unit_type'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN specimen_related_ref OPTIONS (
    column_name 'specimen_related_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN source_name OPTIONS (
    column_name 'source_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN source_id OPTIONS (
    column_name 'source_id'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN quantity OPTIONS (
    column_name 'quantity'
);
ALTER FOREIGN TABLE fdw_113.specimens_relationships ALTER COLUMN unit OPTIONS (
    column_name 'unit'
);


ALTER FOREIGN TABLE fdw_113.specimens_relationships OWNER TO darwin2;

--
-- TOC entry 315 (class 1259 OID 13525330)
-- Name: specimens_storage_parts_view; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.specimens_storage_parts_view (
    id integer,
    uuid uuid,
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
    specimen_count_juveniles_max integer,
    specimen_count_juveniles_min integer,
    main_code_indexed character varying,
    specimen_creation_date timestamp without time zone,
    specimen_creation_date_tech text,
    category character varying,
    specimen_ref integer,
    specimen_part character varying,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
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
    complete boolean,
    synonymy_group_id integer,
    synonymy_status character varying,
    count_by_synonymy_status bigint,
    synonymy_count_all_in_group bigint,
    valid_label boolean,
    label_created_on character varying,
    label_created_by character varying,
    gtu_iso3166 character varying,
    gtu_iso3166_subdivision character varying,
    nagoya character varying,
    cites boolean,
    import_ref integer,
    determination_status text,
    collection_name_full_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'specimens_storage_parts_view'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN expedition_ref OPTIONS (
    column_name 'expedition_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_ref OPTIONS (
    column_name 'litho_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_ref OPTIONS (
    column_name 'chrono_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_ref OPTIONS (
    column_name 'lithology_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN station_visible OPTIONS (
    column_name 'station_visible'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN type_group OPTIONS (
    column_name 'type_group'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN type_search OPTIONS (
    column_name 'type_search'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN rock_form OPTIONS (
    column_name 'rock_form'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN spec_ident_ids OPTIONS (
    column_name 'spec_ident_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN spec_coll_ids OPTIONS (
    column_name 'spec_coll_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN spec_don_sel_ids OPTIONS (
    column_name 'spec_don_sel_ids'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_is_public OPTIONS (
    column_name 'collection_is_public'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_parent_ref OPTIONS (
    column_name 'collection_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN expedition_name_indexed OPTIONS (
    column_name 'expedition_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_tag_values_indexed OPTIONS (
    column_name 'gtu_tag_values_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_province_tag_indexed OPTIONS (
    column_name 'gtu_province_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_others_tag_indexed OPTIONS (
    column_name 'gtu_others_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_elevation_accuracy OPTIONS (
    column_name 'gtu_elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_location OPTIONS (
    column_name 'gtu_location'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_name_indexed OPTIONS (
    column_name 'taxon_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_status OPTIONS (
    column_name 'taxon_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_parent_ref OPTIONS (
    column_name 'taxon_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN taxon_extinct OPTIONS (
    column_name 'taxon_extinct'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_name OPTIONS (
    column_name 'litho_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_name_indexed OPTIONS (
    column_name 'litho_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_level_ref OPTIONS (
    column_name 'litho_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_level_name OPTIONS (
    column_name 'litho_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_status OPTIONS (
    column_name 'litho_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_local OPTIONS (
    column_name 'litho_local'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_color OPTIONS (
    column_name 'litho_color'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_path OPTIONS (
    column_name 'litho_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN litho_parent_ref OPTIONS (
    column_name 'litho_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_name OPTIONS (
    column_name 'chrono_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_name_indexed OPTIONS (
    column_name 'chrono_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_level_ref OPTIONS (
    column_name 'chrono_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_level_name OPTIONS (
    column_name 'chrono_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_status OPTIONS (
    column_name 'chrono_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_local OPTIONS (
    column_name 'chrono_local'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_color OPTIONS (
    column_name 'chrono_color'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_path OPTIONS (
    column_name 'chrono_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN chrono_parent_ref OPTIONS (
    column_name 'chrono_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_name OPTIONS (
    column_name 'lithology_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_name_indexed OPTIONS (
    column_name 'lithology_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_level_ref OPTIONS (
    column_name 'lithology_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_level_name OPTIONS (
    column_name 'lithology_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_status OPTIONS (
    column_name 'lithology_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_local OPTIONS (
    column_name 'lithology_local'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_color OPTIONS (
    column_name 'lithology_color'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_path OPTIONS (
    column_name 'lithology_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN lithology_parent_ref OPTIONS (
    column_name 'lithology_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_name OPTIONS (
    column_name 'mineral_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_name_indexed OPTIONS (
    column_name 'mineral_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_level_ref OPTIONS (
    column_name 'mineral_level_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_level_name OPTIONS (
    column_name 'mineral_level_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_status OPTIONS (
    column_name 'mineral_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_local OPTIONS (
    column_name 'mineral_local'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_color OPTIONS (
    column_name 'mineral_color'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_path OPTIONS (
    column_name 'mineral_path'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN mineral_parent_ref OPTIONS (
    column_name 'mineral_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN ig_num_indexed OPTIONS (
    column_name 'ig_num_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_males_min OPTIONS (
    column_name 'specimen_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_males_max OPTIONS (
    column_name 'specimen_count_males_max'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_females_min OPTIONS (
    column_name 'specimen_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_females_max OPTIONS (
    column_name 'specimen_count_females_max'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_juveniles_max OPTIONS (
    column_name 'specimen_count_juveniles_max'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_count_juveniles_min OPTIONS (
    column_name 'specimen_count_juveniles_min'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN main_code_indexed OPTIONS (
    column_name 'main_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_creation_date_tech OPTIONS (
    column_name 'specimen_creation_date_tech'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN synonymy_group_id OPTIONS (
    column_name 'synonymy_group_id'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN synonymy_status OPTIONS (
    column_name 'synonymy_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN count_by_synonymy_status OPTIONS (
    column_name 'count_by_synonymy_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN synonymy_count_all_in_group OPTIONS (
    column_name 'synonymy_count_all_in_group'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN valid_label OPTIONS (
    column_name 'valid_label'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN label_created_on OPTIONS (
    column_name 'label_created_on'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN label_created_by OPTIONS (
    column_name 'label_created_by'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_iso3166 OPTIONS (
    column_name 'gtu_iso3166'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN gtu_iso3166_subdivision OPTIONS (
    column_name 'gtu_iso3166_subdivision'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN cites OPTIONS (
    column_name 'cites'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN determination_status OPTIONS (
    column_name 'determination_status'
);
ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view ALTER COLUMN collection_name_full_path OPTIONS (
    column_name 'collection_name_full_path'
);


ALTER FOREIGN TABLE fdw_113.specimens_storage_parts_view OWNER TO darwin2;

--
-- TOC entry 316 (class 1259 OID 13525333)
-- Name: staging; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging (
    id integer NOT NULL,
    import_ref integer NOT NULL,
    create_taxon boolean NOT NULL,
    spec_ref integer,
    category character varying,
    expedition_ref integer,
    expedition_name character varying,
    expedition_from_date date,
    expedition_from_date_mask integer,
    expedition_to_date date,
    expedition_to_date_mask integer,
    station_visible boolean,
    gtu_ref integer,
    gtu_code character varying,
    gtu_from_date_mask integer,
    gtu_from_date timestamp without time zone,
    gtu_to_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_latitude double precision,
    gtu_longitude double precision,
    gtu_lat_long_accuracy double precision,
    gtu_elevation double precision,
    gtu_elevation_accuracy double precision,
    taxon_ref integer,
    taxon_name character varying,
    taxon_level_ref integer,
    taxon_level_name character varying,
    taxon_status character varying,
    taxon_extinct boolean,
    taxon_parents public.hstore,
    litho_ref integer,
    litho_name character varying,
    litho_level_ref integer,
    litho_level_name character varying,
    litho_status character varying,
    litho_local boolean,
    litho_color character varying,
    litho_parents public.hstore,
    chrono_ref integer,
    chrono_name character varying,
    chrono_level_ref integer,
    chrono_level_name character varying,
    chrono_status character varying,
    chrono_local boolean,
    chrono_color character varying,
    chrono_upper_bound numeric(10,3),
    chrono_lower_bound numeric(10,3),
    chrono_parents public.hstore,
    lithology_ref integer,
    lithology_name character varying,
    lithology_level_ref integer,
    lithology_level_name character varying,
    lithology_status character varying,
    lithology_local boolean,
    lithology_color character varying,
    lithology_parents public.hstore,
    mineral_ref integer,
    mineral_name character varying,
    mineral_level_ref integer,
    mineral_level_name character varying,
    mineral_status character varying,
    mineral_local boolean,
    mineral_color character varying,
    mineral_path character varying,
    mineral_parents public.hstore,
    mineral_classification character varying,
    ig_ref integer,
    ig_num character varying,
    ig_date_mask integer,
    ig_date date,
    acquisition_category character varying,
    acquisition_date_mask integer,
    acquisition_date date,
    individual_type character varying,
    individual_sex character varying,
    individual_state character varying,
    individual_stage character varying,
    individual_social_status character varying,
    individual_rock_form character varying,
    individual_count_min integer,
    individual_count_max integer,
    part character varying,
    part_status character varying,
    institution_ref integer,
    institution_name character varying,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
    container_type character varying,
    container_storage character varying,
    container character varying,
    sub_container_type character varying,
    sub_container_storage character varying,
    sub_container character varying,
    part_count_min integer,
    part_count_max integer,
    specimen_status character varying,
    complete boolean,
    surnumerary boolean,
    status public.hstore NOT NULL,
    to_import boolean,
    object_name text,
    part_count_males_min integer,
    part_count_males_max integer,
    part_count_females_min integer,
    part_count_females_max integer,
    part_count_juveniles_min integer,
    part_count_juveniles_max integer,
    specimen_taxonomy_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN create_taxon OPTIONS (
    column_name 'create_taxon'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN spec_ref OPTIONS (
    column_name 'spec_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_ref OPTIONS (
    column_name 'expedition_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_from_date OPTIONS (
    column_name 'expedition_from_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_from_date_mask OPTIONS (
    column_name 'expedition_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_to_date OPTIONS (
    column_name 'expedition_to_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN expedition_to_date_mask OPTIONS (
    column_name 'expedition_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN station_visible OPTIONS (
    column_name 'station_visible'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_latitude OPTIONS (
    column_name 'gtu_latitude'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_longitude OPTIONS (
    column_name 'gtu_longitude'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_lat_long_accuracy OPTIONS (
    column_name 'gtu_lat_long_accuracy'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN gtu_elevation_accuracy OPTIONS (
    column_name 'gtu_elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_status OPTIONS (
    column_name 'taxon_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_extinct OPTIONS (
    column_name 'taxon_extinct'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN taxon_parents OPTIONS (
    column_name 'taxon_parents'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_ref OPTIONS (
    column_name 'litho_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_name OPTIONS (
    column_name 'litho_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_level_ref OPTIONS (
    column_name 'litho_level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_level_name OPTIONS (
    column_name 'litho_level_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_status OPTIONS (
    column_name 'litho_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_local OPTIONS (
    column_name 'litho_local'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_color OPTIONS (
    column_name 'litho_color'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN litho_parents OPTIONS (
    column_name 'litho_parents'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_ref OPTIONS (
    column_name 'chrono_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_name OPTIONS (
    column_name 'chrono_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_level_ref OPTIONS (
    column_name 'chrono_level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_level_name OPTIONS (
    column_name 'chrono_level_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_status OPTIONS (
    column_name 'chrono_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_local OPTIONS (
    column_name 'chrono_local'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_color OPTIONS (
    column_name 'chrono_color'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_upper_bound OPTIONS (
    column_name 'chrono_upper_bound'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_lower_bound OPTIONS (
    column_name 'chrono_lower_bound'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN chrono_parents OPTIONS (
    column_name 'chrono_parents'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_ref OPTIONS (
    column_name 'lithology_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_name OPTIONS (
    column_name 'lithology_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_level_ref OPTIONS (
    column_name 'lithology_level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_level_name OPTIONS (
    column_name 'lithology_level_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_status OPTIONS (
    column_name 'lithology_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_local OPTIONS (
    column_name 'lithology_local'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_color OPTIONS (
    column_name 'lithology_color'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN lithology_parents OPTIONS (
    column_name 'lithology_parents'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_name OPTIONS (
    column_name 'mineral_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_level_ref OPTIONS (
    column_name 'mineral_level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_level_name OPTIONS (
    column_name 'mineral_level_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_status OPTIONS (
    column_name 'mineral_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_local OPTIONS (
    column_name 'mineral_local'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_color OPTIONS (
    column_name 'mineral_color'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_path OPTIONS (
    column_name 'mineral_path'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_parents OPTIONS (
    column_name 'mineral_parents'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN mineral_classification OPTIONS (
    column_name 'mineral_classification'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_type OPTIONS (
    column_name 'individual_type'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_sex OPTIONS (
    column_name 'individual_sex'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_state OPTIONS (
    column_name 'individual_state'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_stage OPTIONS (
    column_name 'individual_stage'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_social_status OPTIONS (
    column_name 'individual_social_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_rock_form OPTIONS (
    column_name 'individual_rock_form'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_count_min OPTIONS (
    column_name 'individual_count_min'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN individual_count_max OPTIONS (
    column_name 'individual_count_max'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part OPTIONS (
    column_name 'part'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_status OPTIONS (
    column_name 'part_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN institution_name OPTIONS (
    column_name 'institution_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_min OPTIONS (
    column_name 'part_count_min'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_max OPTIONS (
    column_name 'part_count_max'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN to_import OPTIONS (
    column_name 'to_import'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_males_min OPTIONS (
    column_name 'part_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_males_max OPTIONS (
    column_name 'part_count_males_max'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_females_min OPTIONS (
    column_name 'part_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_females_max OPTIONS (
    column_name 'part_count_females_max'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_juveniles_min OPTIONS (
    column_name 'part_count_juveniles_min'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN part_count_juveniles_max OPTIONS (
    column_name 'part_count_juveniles_max'
);
ALTER FOREIGN TABLE fdw_113.staging ALTER COLUMN specimen_taxonomy_ref OPTIONS (
    column_name 'specimen_taxonomy_ref'
);


ALTER FOREIGN TABLE fdw_113.staging OWNER TO darwin2;

--
-- TOC entry 317 (class 1259 OID 13525336)
-- Name: staging_catalogue; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_catalogue (
    id integer NOT NULL,
    import_ref integer NOT NULL,
    name character varying NOT NULL,
    level_ref integer,
    parent_ref integer,
    catalogue_ref integer,
    parent_updated boolean,
    is_reference_taxonomy boolean,
    source_taxonomy character varying,
    parent_ref_internal integer,
    hierarchical_conflict boolean,
    name_cluster integer,
    imported boolean NOT NULL,
    import_exception character varying,
    staging_hierarchy character varying,
    darwin_hierarchy character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_catalogue'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN catalogue_ref OPTIONS (
    column_name 'catalogue_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN parent_updated OPTIONS (
    column_name 'parent_updated'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN is_reference_taxonomy OPTIONS (
    column_name 'is_reference_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN source_taxonomy OPTIONS (
    column_name 'source_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN parent_ref_internal OPTIONS (
    column_name 'parent_ref_internal'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN hierarchical_conflict OPTIONS (
    column_name 'hierarchical_conflict'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN name_cluster OPTIONS (
    column_name 'name_cluster'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN imported OPTIONS (
    column_name 'imported'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN import_exception OPTIONS (
    column_name 'import_exception'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN staging_hierarchy OPTIONS (
    column_name 'staging_hierarchy'
);
ALTER FOREIGN TABLE fdw_113.staging_catalogue ALTER COLUMN darwin_hierarchy OPTIONS (
    column_name 'darwin_hierarchy'
);


ALTER FOREIGN TABLE fdw_113.staging_catalogue OWNER TO darwin2;

--
-- TOC entry 318 (class 1259 OID 13525339)
-- Name: staging_collecting_methods; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_collecting_methods (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    collecting_method_ref integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_collecting_methods'
);
ALTER FOREIGN TABLE fdw_113.staging_collecting_methods ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_collecting_methods ALTER COLUMN staging_ref OPTIONS (
    column_name 'staging_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_collecting_methods ALTER COLUMN collecting_method_ref OPTIONS (
    column_name 'collecting_method_ref'
);


ALTER FOREIGN TABLE fdw_113.staging_collecting_methods OWNER TO darwin2;

--
-- TOC entry 319 (class 1259 OID 13525342)
-- Name: staging_info; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_info (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    referenced_relation character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_info'
);
ALTER FOREIGN TABLE fdw_113.staging_info ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_info ALTER COLUMN staging_ref OPTIONS (
    column_name 'staging_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_info ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);


ALTER FOREIGN TABLE fdw_113.staging_info OWNER TO darwin2;

--
-- TOC entry 320 (class 1259 OID 13525345)
-- Name: staging_people; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_people (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    people_type character varying NOT NULL,
    people_sub_type character varying NOT NULL,
    order_by integer NOT NULL,
    people_ref integer,
    formated_name character varying,
    import_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_people'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN people_type OPTIONS (
    column_name 'people_type'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN people_sub_type OPTIONS (
    column_name 'people_sub_type'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.staging_people ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);


ALTER FOREIGN TABLE fdw_113.staging_people OWNER TO darwin2;

--
-- TOC entry 321 (class 1259 OID 13525348)
-- Name: staging_relationship; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_relationship (
    id integer NOT NULL,
    record_id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    relationship_type character varying,
    staging_related_ref integer,
    taxon_ref integer,
    mineral_ref integer,
    institution_ref integer,
    institution_name text,
    source_name text,
    source_id text,
    quantity numeric(16,2),
    unit character varying,
    unit_type character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_relationship'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN relationship_type OPTIONS (
    column_name 'relationship_type'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN staging_related_ref OPTIONS (
    column_name 'staging_related_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN institution_name OPTIONS (
    column_name 'institution_name'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN source_name OPTIONS (
    column_name 'source_name'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN source_id OPTIONS (
    column_name 'source_id'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN quantity OPTIONS (
    column_name 'quantity'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN unit OPTIONS (
    column_name 'unit'
);
ALTER FOREIGN TABLE fdw_113.staging_relationship ALTER COLUMN unit_type OPTIONS (
    column_name 'unit_type'
);


ALTER FOREIGN TABLE fdw_113.staging_relationship OWNER TO darwin2;

--
-- TOC entry 322 (class 1259 OID 13525351)
-- Name: staging_tag_groups; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.staging_tag_groups (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    group_name character varying NOT NULL,
    sub_group_name character varying NOT NULL,
    tag_value character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'staging_tag_groups'
);
ALTER FOREIGN TABLE fdw_113.staging_tag_groups ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.staging_tag_groups ALTER COLUMN staging_ref OPTIONS (
    column_name 'staging_ref'
);
ALTER FOREIGN TABLE fdw_113.staging_tag_groups ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.staging_tag_groups ALTER COLUMN sub_group_name OPTIONS (
    column_name 'sub_group_name'
);
ALTER FOREIGN TABLE fdw_113.staging_tag_groups ALTER COLUMN tag_value OPTIONS (
    column_name 'tag_value'
);


ALTER FOREIGN TABLE fdw_113.staging_tag_groups OWNER TO darwin2;

--
-- TOC entry 323 (class 1259 OID 13525354)
-- Name: storage_parts; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.storage_parts (
    id integer NOT NULL,
    category character varying NOT NULL,
    specimen_ref integer NOT NULL,
    specimen_part character varying NOT NULL,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
    container character varying,
    sub_container character varying,
    container_type character varying NOT NULL,
    sub_container_type character varying NOT NULL,
    container_storage character varying NOT NULL,
    sub_container_storage character varying NOT NULL,
    surnumerary boolean NOT NULL,
    object_name text,
    object_name_indexed text NOT NULL,
    specimen_status character varying NOT NULL,
    complete boolean NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'storage_parts'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.storage_parts ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);


ALTER FOREIGN TABLE fdw_113.storage_parts OWNER TO darwin2;

--
-- TOC entry 324 (class 1259 OID 13525357)
-- Name: storage_parts_bck_20220513; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.storage_parts_bck_20220513 (
    id integer NOT NULL,
    category character varying NOT NULL,
    specimen_ref integer NOT NULL,
    specimen_part character varying NOT NULL,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
    container character varying,
    sub_container character varying,
    container_type character varying NOT NULL,
    sub_container_type character varying NOT NULL,
    container_storage character varying NOT NULL,
    sub_container_storage character varying NOT NULL,
    surnumerary boolean NOT NULL,
    object_name text,
    object_name_indexed text NOT NULL,
    specimen_status character varying NOT NULL,
    complete boolean NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'storage_parts_bck_20220513'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);


ALTER FOREIGN TABLE fdw_113.storage_parts_bck_20220513 OWNER TO darwin2;

--
-- TOC entry 325 (class 1259 OID 13525360)
-- Name: storage_parts_fix_ichtyo; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo (
    id integer,
    category character varying,
    specimen_ref integer,
    specimen_part character varying,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
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
    complete boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'storage_parts_fix_ichtyo'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);


ALTER FOREIGN TABLE fdw_113.storage_parts_fix_ichtyo OWNER TO darwin2;

--
-- TOC entry 326 (class 1259 OID 13525363)
-- Name: storage_parts_ichtyo_missing20220517; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 (
    family character varying,
    genus character varying,
    genusupper character varying,
    species character varying,
    room_genus character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'storage_parts_ichtyo_missing20220517'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 ALTER COLUMN genusupper OPTIONS (
    column_name 'genusupper'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 ALTER COLUMN species OPTIONS (
    column_name 'species'
);
ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 ALTER COLUMN room_genus OPTIONS (
    column_name 'room_genus'
);


ALTER FOREIGN TABLE fdw_113.storage_parts_ichtyo_missing20220517 OWNER TO darwin2;

--
-- TOC entry 327 (class 1259 OID 13525366)
-- Name: t_compare_darwin_digit03_mysql; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql (
    pid integer NOT NULL,
    phylum character varying,
    class character varying,
    family character varying,
    genus character varying,
    species character varying,
    subspecies character varying,
    status character varying,
    number character varying,
    digitisation character varying,
    url character varying,
    sketchfab_snippet character varying,
    sketchfab_without_snippet character varying,
    contributor character varying,
    pic_path character varying,
    pic_display_order character varying,
    pic_image_file character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 't_compare_darwin_digit03_mysql'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN pid OPTIONS (
    column_name 'pid'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN phylum OPTIONS (
    column_name 'phylum'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN class OPTIONS (
    column_name 'class'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN species OPTIONS (
    column_name 'species'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN subspecies OPTIONS (
    column_name 'subspecies'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN number OPTIONS (
    column_name 'number'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN digitisation OPTIONS (
    column_name 'digitisation'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN url OPTIONS (
    column_name 'url'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN sketchfab_snippet OPTIONS (
    column_name 'sketchfab_snippet'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN sketchfab_without_snippet OPTIONS (
    column_name 'sketchfab_without_snippet'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN contributor OPTIONS (
    column_name 'contributor'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN pic_path OPTIONS (
    column_name 'pic_path'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN pic_display_order OPTIONS (
    column_name 'pic_display_order'
);
ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql ALTER COLUMN pic_image_file OPTIONS (
    column_name 'pic_image_file'
);


ALTER FOREIGN TABLE fdw_113.t_compare_darwin_digit03_mysql OWNER TO darwin2;

--
-- TOC entry 328 (class 1259 OID 13525369)
-- Name: t_darwin_ipt; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.t_darwin_ipt (
    ids text,
    guid text,
    collection_ref integer,
    collection_code character varying,
    collection_name character varying,
    collection_id integer,
    collection_path character varying,
    cataloguenumber text,
    basisofrecord text,
    institutionid text,
    iso_country_institution text,
    bibliographic_citation text,
    license text,
    email text,
    type character varying,
    taxon_path character varying,
    taxon_ref integer,
    taxon_name character varying,
    family character varying,
    iso_country character varying,
    country character varying,
    location text,
    latitude character varying,
    longitude character varying,
    lat_long_accuracy double precision,
    collector_ids integer[],
    collectors text,
    donator_ids integer[],
    donators text,
    identifiers_ids integer[],
    identifiers text,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    eventdate text,
    country_unnest character varying,
    urls_thumbnails character varying,
    image_category_thumbnails character varying,
    contributor_thumbnails character varying,
    disclaimer_thumbnails character varying,
    license_thumbnails character varying,
    display_order_thumbnails integer,
    urls_image_links character varying,
    image_category_image_links character varying,
    contributor_image_links character varying,
    disclaimer_image_links character varying,
    license_image_links character varying,
    display_order_image_links integer,
    urls_3d_snippets character varying,
    image_category_3d_snippets character varying,
    contributor_3d_snippets character varying,
    disclaimer_3d_snippets character varying,
    license_3d_snippets character varying,
    display_order_3d_snippets integer,
    identification_date text,
    history text,
    gtu_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 't_darwin_ipt'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN ids OPTIONS (
    column_name 'ids'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN guid OPTIONS (
    column_name 'guid'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collection_id OPTIONS (
    column_name 'collection_id'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN cataloguenumber OPTIONS (
    column_name 'cataloguenumber'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN basisofrecord OPTIONS (
    column_name 'basisofrecord'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN institutionid OPTIONS (
    column_name 'institutionid'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN iso_country_institution OPTIONS (
    column_name 'iso_country_institution'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN bibliographic_citation OPTIONS (
    column_name 'bibliographic_citation'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN license OPTIONS (
    column_name 'license'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN email OPTIONS (
    column_name 'email'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN iso_country OPTIONS (
    column_name 'iso_country'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN location OPTIONS (
    column_name 'location'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN lat_long_accuracy OPTIONS (
    column_name 'lat_long_accuracy'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN donator_ids OPTIONS (
    column_name 'donator_ids'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN identifiers_ids OPTIONS (
    column_name 'identifiers_ids'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN eventdate OPTIONS (
    column_name 'eventdate'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN country_unnest OPTIONS (
    column_name 'country_unnest'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN license_thumbnails OPTIONS (
    column_name 'license_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN identification_date OPTIONS (
    column_name 'identification_date'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN history OPTIONS (
    column_name 'history'
);
ALTER FOREIGN TABLE fdw_113.t_darwin_ipt ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);


ALTER FOREIGN TABLE fdw_113.t_darwin_ipt OWNER TO darwin2;

--
-- TOC entry 329 (class 1259 OID 13525372)
-- Name: tag_groups; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tag_groups (
    id integer NOT NULL,
    gtu_ref integer NOT NULL,
    group_name character varying NOT NULL,
    group_name_indexed character varying NOT NULL,
    sub_group_name character varying NOT NULL,
    sub_group_name_indexed character varying NOT NULL,
    international_name character varying NOT NULL,
    color character varying NOT NULL,
    tag_value character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tag_groups'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN group_name OPTIONS (
    column_name 'group_name'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN group_name_indexed OPTIONS (
    column_name 'group_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN sub_group_name OPTIONS (
    column_name 'sub_group_name'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN sub_group_name_indexed OPTIONS (
    column_name 'sub_group_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN international_name OPTIONS (
    column_name 'international_name'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.tag_groups ALTER COLUMN tag_value OPTIONS (
    column_name 'tag_value'
);


ALTER FOREIGN TABLE fdw_113.tag_groups OWNER TO darwin2;

--
-- TOC entry 330 (class 1259 OID 13525375)
-- Name: taxonomy_metadata; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.taxonomy_metadata (
    id integer NOT NULL,
    creation_date date NOT NULL,
    creation_date_mask integer,
    import_ref integer,
    taxonomy_name character varying NOT NULL,
    definition text,
    is_reference_taxonomy boolean NOT NULL,
    source character varying,
    url_website character varying,
    url_webservice character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'taxonomy_metadata'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN creation_date_mask OPTIONS (
    column_name 'creation_date_mask'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN taxonomy_name OPTIONS (
    column_name 'taxonomy_name'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN definition OPTIONS (
    column_name 'definition'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN is_reference_taxonomy OPTIONS (
    column_name 'is_reference_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN source OPTIONS (
    column_name 'source'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN url_website OPTIONS (
    column_name 'url_website'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_metadata ALTER COLUMN url_webservice OPTIONS (
    column_name 'url_webservice'
);


ALTER FOREIGN TABLE fdw_113.taxonomy_metadata OWNER TO darwin2;

--
-- TOC entry 331 (class 1259 OID 13525378)
-- Name: taxonomy_synonymy_status; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.taxonomy_synonymy_status (
    group_id integer,
    specimen_ids integer[],
    status character varying,
    count_by_status bigint,
    count_all bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'taxonomy_synonymy_status'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status ALTER COLUMN group_id OPTIONS (
    column_name 'group_id'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status ALTER COLUMN specimen_ids OPTIONS (
    column_name 'specimen_ids'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status ALTER COLUMN count_by_status OPTIONS (
    column_name 'count_by_status'
);
ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status ALTER COLUMN count_all OPTIONS (
    column_name 'count_all'
);


ALTER FOREIGN TABLE fdw_113.taxonomy_synonymy_status OWNER TO darwin2;

--
-- TOC entry 332 (class 1259 OID 13525381)
-- Name: template_classifications; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.template_classifications (
    name character varying NOT NULL,
    name_indexed character varying,
    level_ref integer NOT NULL,
    status character varying NOT NULL,
    local_naming boolean NOT NULL,
    color character varying,
    path character varying NOT NULL,
    parent_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'template_classifications'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.template_classifications ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);


ALTER FOREIGN TABLE fdw_113.template_classifications OWNER TO darwin2;

--
-- TOC entry 333 (class 1259 OID 13525384)
-- Name: template_people; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.template_people (
    is_physical boolean NOT NULL,
    sub_type character varying,
    formated_name character varying NOT NULL,
    formated_name_indexed character varying NOT NULL,
    formated_name_unique character varying NOT NULL,
    title character varying NOT NULL,
    family_name character varying NOT NULL,
    given_name character varying,
    additional_names character varying,
    birth_date_mask integer NOT NULL,
    birth_date date NOT NULL,
    gender character(1)
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'template_people'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN is_physical OPTIONS (
    column_name 'is_physical'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN sub_type OPTIONS (
    column_name 'sub_type'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN formated_name_indexed OPTIONS (
    column_name 'formated_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN formated_name_unique OPTIONS (
    column_name 'formated_name_unique'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN family_name OPTIONS (
    column_name 'family_name'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN given_name OPTIONS (
    column_name 'given_name'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN additional_names OPTIONS (
    column_name 'additional_names'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN birth_date_mask OPTIONS (
    column_name 'birth_date_mask'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN birth_date OPTIONS (
    column_name 'birth_date'
);
ALTER FOREIGN TABLE fdw_113.template_people ALTER COLUMN gender OPTIONS (
    column_name 'gender'
);


ALTER FOREIGN TABLE fdw_113.template_people OWNER TO darwin2;

--
-- TOC entry 334 (class 1259 OID 13525387)
-- Name: template_people_users_addr_common; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.template_people_users_addr_common (
    po_box character varying,
    extended_address character varying,
    locality character varying NOT NULL,
    region character varying,
    zip_code character varying,
    country character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'template_people_users_addr_common'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN po_box OPTIONS (
    column_name 'po_box'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN extended_address OPTIONS (
    column_name 'extended_address'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN locality OPTIONS (
    column_name 'locality'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN region OPTIONS (
    column_name 'region'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN zip_code OPTIONS (
    column_name 'zip_code'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common ALTER COLUMN country OPTIONS (
    column_name 'country'
);


ALTER FOREIGN TABLE fdw_113.template_people_users_addr_common OWNER TO darwin2;

--
-- TOC entry 335 (class 1259 OID 13525390)
-- Name: template_people_users_comm_common; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.template_people_users_comm_common (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'template_people_users_comm_common'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_comm_common ALTER COLUMN person_user_ref OPTIONS (
    column_name 'person_user_ref'
);
ALTER FOREIGN TABLE fdw_113.template_people_users_comm_common ALTER COLUMN entry OPTIONS (
    column_name 'entry'
);


ALTER FOREIGN TABLE fdw_113.template_people_users_comm_common OWNER TO darwin2;

--
-- TOC entry 336 (class 1259 OID 13525393)
-- Name: template_table_record_ref; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.template_table_record_ref (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'template_table_record_ref'
);
ALTER FOREIGN TABLE fdw_113.template_table_record_ref ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.template_table_record_ref ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);


ALTER FOREIGN TABLE fdw_113.template_table_record_ref OWNER TO darwin2;

--
-- TOC entry 337 (class 1259 OID 13525396)
-- Name: tmp_xylarium_img_links_2022; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 (
    unitid character varying,
    links character varying,
    legend character varying,
    tw_images character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tmp_xylarium_img_links_2022'
);
ALTER FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 ALTER COLUMN unitid OPTIONS (
    column_name 'unitid'
);
ALTER FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 ALTER COLUMN links OPTIONS (
    column_name 'links'
);
ALTER FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 ALTER COLUMN legend OPTIONS (
    column_name 'legend'
);
ALTER FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 ALTER COLUMN tw_images OPTIONS (
    column_name 'tw_images'
);


ALTER FOREIGN TABLE fdw_113.tmp_xylarium_img_links_2022 OWNER TO darwin2;

--
-- TOC entry 338 (class 1259 OID 13525399)
-- Name: tv_darwin_view_for_csv; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_darwin_view_for_csv (
    id text,
    collection_code character varying,
    code text,
    additional_codes text,
    ig_num character varying,
    taxon_name text,
    author text,
    full_scientific_name text,
    family text,
    type text,
    specimen_count_min integer,
    specimen_count_max integer,
    identifiers text,
    abbreviated_identifiers text,
    identification_year text,
    longitude double precision,
    latitude double precision,
    longitude_text character varying,
    latitude_text character varying,
    gtu_country_tag_value character varying,
    municipality text,
    region_district text,
    exact_site text,
    ecology text,
    gtu_others_tag_value character varying,
    gtu_code character varying,
    gtu_elevation double precision,
    collecting_year_from double precision,
    collecting_month_from double precision,
    collecting_day_from double precision,
    collecting_year_to double precision,
    collecting_month_to double precision,
    collecting_day_to double precision,
    properties_locality text,
    collectors text,
    abbreviated_collectors text,
    expedition_name character varying,
    donators text,
    abbreviated_donators text,
    acquisition_category character varying,
    acquisition_date text,
    sex character varying,
    stage character varying,
    state character varying,
    social_status character varying,
    specimen_part text,
    complete text,
    object_name text,
    specimen_status text,
    container_storage text,
    method text,
    tool text,
    comment text,
    properties_all text,
    specimen_creation_date timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_darwin_view_for_csv'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN additional_codes OPTIONS (
    column_name 'additional_codes'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN author OPTIONS (
    column_name 'author'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN full_scientific_name OPTIONS (
    column_name 'full_scientific_name'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN abbreviated_identifiers OPTIONS (
    column_name 'abbreviated_identifiers'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN identification_year OPTIONS (
    column_name 'identification_year'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN longitude_text OPTIONS (
    column_name 'longitude_text'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN latitude_text OPTIONS (
    column_name 'latitude_text'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN municipality OPTIONS (
    column_name 'municipality'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN region_district OPTIONS (
    column_name 'region_district'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN exact_site OPTIONS (
    column_name 'exact_site'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN ecology OPTIONS (
    column_name 'ecology'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_year_from OPTIONS (
    column_name 'collecting_year_from'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_month_from OPTIONS (
    column_name 'collecting_month_from'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_day_from OPTIONS (
    column_name 'collecting_day_from'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_year_to OPTIONS (
    column_name 'collecting_year_to'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_month_to OPTIONS (
    column_name 'collecting_month_to'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collecting_day_to OPTIONS (
    column_name 'collecting_day_to'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN properties_locality OPTIONS (
    column_name 'properties_locality'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN abbreviated_collectors OPTIONS (
    column_name 'abbreviated_collectors'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN abbreviated_donators OPTIONS (
    column_name 'abbreviated_donators'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN method OPTIONS (
    column_name 'method'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN tool OPTIONS (
    column_name 'tool'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN properties_all OPTIONS (
    column_name 'properties_all'
);
ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);


ALTER FOREIGN TABLE fdw_113.tv_darwin_view_for_csv OWNER TO darwin2;

--
-- TOC entry 339 (class 1259 OID 13525402)
-- Name: tv_rdf_view_2_ichtyo; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo (
    uuid uuid,
    id integer,
    code_display text,
    full_code_indexed character varying,
    taxon_path character varying,
    taxon_ref integer,
    collection_ref integer,
    collection_name character varying,
    gtu_country_tag_indexed character varying[],
    gtu_country_tag_value character varying,
    localities_indexed character varying[],
    gtu_others_tag_value character varying,
    taxon_name character varying,
    collector_ids integer[],
    collector_name text,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    coll_type character varying,
    country_unnest character varying,
    urls_thumbnails character varying,
    image_category_thumbnails character varying,
    contributor_thumbnails character varying,
    disclaimer_thumbnails character varying,
    license_thumbnails character varying,
    display_order_thumbnails integer,
    urls_image_links character varying,
    image_category_image_links character varying,
    contributor_image_links character varying,
    disclaimer_image_links character varying,
    license_image_links character varying,
    display_order_image_links integer,
    urls_3d_snippets character varying,
    image_category_3d_snippets character varying,
    contributor_3d_snippets character varying,
    disclaimer_3d_snippets character varying,
    license_3d_snippets character varying,
    display_order_3d_snippets integer,
    latitude double precision,
    longitude double precision,
    identification_date timestamp without time zone,
    identification_date_mask integer,
    history text,
    gtu_ref integer,
    "Country" character varying,
    "Locality" text,
    geom public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_rdf_view_2_ichtyo'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN code_display OPTIONS (
    column_name 'code_display'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN localities_indexed OPTIONS (
    column_name 'localities_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN collector_name OPTIONS (
    column_name 'collector_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN coll_type OPTIONS (
    column_name 'coll_type'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN country_unnest OPTIONS (
    column_name 'country_unnest'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN license_thumbnails OPTIONS (
    column_name 'license_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN identification_date OPTIONS (
    column_name 'identification_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN identification_date_mask OPTIONS (
    column_name 'identification_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN history OPTIONS (
    column_name 'history'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN "Country" OPTIONS (
    column_name 'Country'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN "Locality" OPTIONS (
    column_name 'Locality'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo ALTER COLUMN geom OPTIONS (
    column_name 'geom'
);


ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo OWNER TO darwin2;

--
-- TOC entry 340 (class 1259 OID 13525405)
-- Name: tv_rdf_view_2_ichtyo_taxo; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo (
    uuid uuid,
    id integer,
    code_display text,
    full_code_indexed character varying,
    taxon_path character varying,
    taxon_ref integer,
    collection_ref integer,
    collection_name character varying,
    gtu_country_tag_indexed character varying[],
    gtu_country_tag_value character varying,
    localities_indexed character varying[],
    gtu_others_tag_value character varying,
    taxon_name character varying,
    collector_ids integer[],
    collector_name text,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    coll_type character varying,
    country_unnest character varying,
    urls_thumbnails character varying,
    image_category_thumbnails character varying,
    contributor_thumbnails character varying,
    disclaimer_thumbnails character varying,
    license_thumbnails character varying,
    display_order_thumbnails integer,
    urls_image_links character varying,
    image_category_image_links character varying,
    contributor_image_links character varying,
    disclaimer_image_links character varying,
    license_image_links character varying,
    display_order_image_links integer,
    urls_3d_snippets character varying,
    image_category_3d_snippets character varying,
    contributor_3d_snippets character varying,
    disclaimer_3d_snippets character varying,
    license_3d_snippets character varying,
    display_order_3d_snippets integer,
    latitude double precision,
    longitude double precision,
    identification_date timestamp without time zone,
    identification_date_mask integer,
    history text,
    gtu_ref integer,
    "Country" character varying,
    "Locality" text,
    geom public.geometry,
    family character varying,
    genus character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_rdf_view_2_ichtyo_taxo'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN code_display OPTIONS (
    column_name 'code_display'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN localities_indexed OPTIONS (
    column_name 'localities_indexed'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN collector_name OPTIONS (
    column_name 'collector_name'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN coll_type OPTIONS (
    column_name 'coll_type'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN country_unnest OPTIONS (
    column_name 'country_unnest'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN license_thumbnails OPTIONS (
    column_name 'license_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN identification_date OPTIONS (
    column_name 'identification_date'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN identification_date_mask OPTIONS (
    column_name 'identification_date_mask'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN history OPTIONS (
    column_name 'history'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN "Country" OPTIONS (
    column_name 'Country'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN "Locality" OPTIONS (
    column_name 'Locality'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN geom OPTIONS (
    column_name 'geom'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);


ALTER FOREIGN TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo OWNER TO darwin2;

--
-- TOC entry 341 (class 1259 OID 13525408)
-- Name: tv_reporting_count_all_specimens_by_collection_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig (
    collection_name character varying,
    collection_path character varying,
    collection_ref integer,
    year double precision,
    specimen_creation_date timestamp without time zone,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint,
    ig_ref integer,
    ig_num character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_count_all_specimens_by_collection_year_ig'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig OWNER TO darwin2;

--
-- TOC entry 342 (class 1259 OID 13525411)
-- Name: tv_reporting_count_all_specimens_type_by_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig (
    collection_path character varying,
    collection_name character varying,
    collection_ref integer,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    specimen_creation_date timestamp without time zone,
    type text,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_count_all_specimens_type_by_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 343 (class 1259 OID 13525414)
-- Name: tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig (
    level_ref integer,
    level_name character varying,
    rank text,
    taxon text,
    year double precision,
    creation_date timestamp without time zone,
    ig_ref integer,
    ig_num character varying,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    countries character varying[],
    min_lon double precision,
    min_lat double precision,
    max_lon double precision,
    max_lat double precision
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN rank OPTIONS (
    column_name 'rank'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN countries OPTIONS (
    column_name 'countries'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN min_lon OPTIONS (
    column_name 'min_lon'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN min_lat OPTIONS (
    column_name 'min_lat'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN max_lon OPTIONS (
    column_name 'max_lon'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN max_lat OPTIONS (
    column_name 'max_lat'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 344 (class 1259 OID 13525417)
-- Name: tv_reporting_higher_taxa_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig (
    level_ref integer,
    level_name character varying,
    rank text,
    taxon text,
    year double precision,
    creation_date timestamp without time zone,
    ig_ref integer,
    ig_num character varying,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_higher_taxa_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN rank OPTIONS (
    column_name 'rank'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 345 (class 1259 OID 13525420)
-- Name: tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig (
    taxonomy_id integer,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    creation_date timestamp without time zone,
    level_ref integer,
    level_name character varying,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN taxonomy_id OPTIONS (
    column_name 'taxonomy_id'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 346 (class 1259 OID 13525423)
-- Name: tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal (
    taxonomy_id integer,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    creation_date timestamp without time zone,
    level_ref integer,
    level_name character varying,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN taxonomy_id OPTIONS (
    column_name 'taxonomy_id'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal OWNER TO darwin2;

--
-- TOC entry 347 (class 1259 OID 13525426)
-- Name: users; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users (
    is_physical boolean NOT NULL,
    sub_type character varying,
    formated_name character varying NOT NULL,
    formated_name_indexed character varying NOT NULL,
    formated_name_unique character varying NOT NULL,
    title character varying NOT NULL,
    family_name character varying NOT NULL,
    given_name character varying,
    additional_names character varying,
    birth_date_mask integer NOT NULL,
    birth_date date NOT NULL,
    gender character(1),
    id integer NOT NULL,
    db_user_type smallint NOT NULL,
    people_id integer,
    created_at timestamp without time zone,
    selected_lang character varying NOT NULL,
    user_ip character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN is_physical OPTIONS (
    column_name 'is_physical'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN sub_type OPTIONS (
    column_name 'sub_type'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN formated_name_indexed OPTIONS (
    column_name 'formated_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN formated_name_unique OPTIONS (
    column_name 'formated_name_unique'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN family_name OPTIONS (
    column_name 'family_name'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN given_name OPTIONS (
    column_name 'given_name'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN additional_names OPTIONS (
    column_name 'additional_names'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN birth_date_mask OPTIONS (
    column_name 'birth_date_mask'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN birth_date OPTIONS (
    column_name 'birth_date'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN gender OPTIONS (
    column_name 'gender'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN db_user_type OPTIONS (
    column_name 'db_user_type'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN people_id OPTIONS (
    column_name 'people_id'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN created_at OPTIONS (
    column_name 'created_at'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN selected_lang OPTIONS (
    column_name 'selected_lang'
);
ALTER FOREIGN TABLE fdw_113.users ALTER COLUMN user_ip OPTIONS (
    column_name 'user_ip'
);


ALTER FOREIGN TABLE fdw_113.users OWNER TO darwin2;

--
-- TOC entry 348 (class 1259 OID 13525429)
-- Name: users_addresses; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users_addresses (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL,
    po_box character varying,
    extended_address character varying,
    locality character varying NOT NULL,
    region character varying,
    zip_code character varying,
    country character varying NOT NULL,
    id integer NOT NULL,
    person_user_role character varying,
    organization_unit character varying,
    tag character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users_addresses'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN person_user_ref OPTIONS (
    column_name 'person_user_ref'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN entry OPTIONS (
    column_name 'entry'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN po_box OPTIONS (
    column_name 'po_box'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN extended_address OPTIONS (
    column_name 'extended_address'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN locality OPTIONS (
    column_name 'locality'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN region OPTIONS (
    column_name 'region'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN zip_code OPTIONS (
    column_name 'zip_code'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN person_user_role OPTIONS (
    column_name 'person_user_role'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN organization_unit OPTIONS (
    column_name 'organization_unit'
);
ALTER FOREIGN TABLE fdw_113.users_addresses ALTER COLUMN tag OPTIONS (
    column_name 'tag'
);


ALTER FOREIGN TABLE fdw_113.users_addresses OWNER TO darwin2;

--
-- TOC entry 349 (class 1259 OID 13525432)
-- Name: users_comm; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users_comm (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL,
    id integer NOT NULL,
    comm_type character varying NOT NULL,
    tag character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users_comm'
);
ALTER FOREIGN TABLE fdw_113.users_comm ALTER COLUMN person_user_ref OPTIONS (
    column_name 'person_user_ref'
);
ALTER FOREIGN TABLE fdw_113.users_comm ALTER COLUMN entry OPTIONS (
    column_name 'entry'
);
ALTER FOREIGN TABLE fdw_113.users_comm ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users_comm ALTER COLUMN comm_type OPTIONS (
    column_name 'comm_type'
);
ALTER FOREIGN TABLE fdw_113.users_comm ALTER COLUMN tag OPTIONS (
    column_name 'tag'
);


ALTER FOREIGN TABLE fdw_113.users_comm OWNER TO darwin2;

--
-- TOC entry 350 (class 1259 OID 13525435)
-- Name: users_login_infos; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users_login_infos (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    login_type character varying NOT NULL,
    user_name character varying,
    password character varying,
    login_system character varying,
    renew_hash character varying,
    last_seen timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users_login_infos'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN login_type OPTIONS (
    column_name 'login_type'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN user_name OPTIONS (
    column_name 'user_name'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN password OPTIONS (
    column_name 'password'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN login_system OPTIONS (
    column_name 'login_system'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN renew_hash OPTIONS (
    column_name 'renew_hash'
);
ALTER FOREIGN TABLE fdw_113.users_login_infos ALTER COLUMN last_seen OPTIONS (
    column_name 'last_seen'
);


ALTER FOREIGN TABLE fdw_113.users_login_infos OWNER TO darwin2;

--
-- TOC entry 351 (class 1259 OID 13525438)
-- Name: users_tracking_proppb; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.users_tracking_proppb (
    id integer,
    referenced_relation character varying,
    record_id integer,
    user_ref integer,
    action character varying,
    old_value public.hstore,
    new_value public.hstore,
    modification_date_time timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'users_tracking_proppb'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN action OPTIONS (
    column_name 'action'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN old_value OPTIONS (
    column_name 'old_value'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN new_value OPTIONS (
    column_name 'new_value'
);
ALTER FOREIGN TABLE fdw_113.users_tracking_proppb ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);


ALTER FOREIGN TABLE fdw_113.users_tracking_proppb OWNER TO darwin2;

--
-- TOC entry 352 (class 1259 OID 13525441)
-- Name: v_collection_statistics; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_collection_statistics (
    collection_path text,
    counter_category text,
    items character varying,
    count_items bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_collection_statistics'
);
ALTER FOREIGN TABLE fdw_113.v_collection_statistics ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_collection_statistics ALTER COLUMN counter_category OPTIONS (
    column_name 'counter_category'
);
ALTER FOREIGN TABLE fdw_113.v_collection_statistics ALTER COLUMN items OPTIONS (
    column_name 'items'
);
ALTER FOREIGN TABLE fdw_113.v_collection_statistics ALTER COLUMN count_items OPTIONS (
    column_name 'count_items'
);


ALTER FOREIGN TABLE fdw_113.v_collection_statistics OWNER TO darwin2;

--
-- TOC entry 241 (class 1259 OID 13524996)
-- Name: v_collections_full_path_recursive; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_collections_full_path_recursive (
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
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_collections_full_path_recursive'
);


ALTER FOREIGN TABLE fdw_113.v_collections_full_path_recursive OWNER TO darwin2;

--
-- TOC entry 353 (class 1259 OID 13525444)
-- Name: v_comment_loans; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_comment_loans (
    id integer,
    record_id integer,
    lower_value character varying,
    regexp_matches text[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_comment_loans'
);
ALTER FOREIGN TABLE fdw_113.v_comment_loans ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_comment_loans ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_comment_loans ALTER COLUMN lower_value OPTIONS (
    column_name 'lower_value'
);
ALTER FOREIGN TABLE fdw_113.v_comment_loans ALTER COLUMN regexp_matches OPTIONS (
    column_name 'regexp_matches'
);


ALTER FOREIGN TABLE fdw_113.v_comment_loans OWNER TO darwin2;

--
-- TOC entry 354 (class 1259 OID 13525447)
-- Name: v_control_identifications; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_control_identifications (
    linked_ref integer,
    linked_only integer[],
    group_all integer[],
    status_labels json
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_control_identifications'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications ALTER COLUMN linked_ref OPTIONS (
    column_name 'linked_ref'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications ALTER COLUMN linked_only OPTIONS (
    column_name 'linked_only'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications ALTER COLUMN group_all OPTIONS (
    column_name 'group_all'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications ALTER COLUMN status_labels OPTIONS (
    column_name 'status_labels'
);


ALTER FOREIGN TABLE fdw_113.v_control_identifications OWNER TO darwin2;

--
-- TOC entry 355 (class 1259 OID 13525450)
-- Name: v_control_identifications_several_true; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_control_identifications_several_true (
    linked_ref integer,
    linked_only integer[],
    group_all integer[],
    status_labels json,
    json_object_keys text,
    cardinality integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_control_identifications_several_true'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN linked_ref OPTIONS (
    column_name 'linked_ref'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN linked_only OPTIONS (
    column_name 'linked_only'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN group_all OPTIONS (
    column_name 'group_all'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN status_labels OPTIONS (
    column_name 'status_labels'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN json_object_keys OPTIONS (
    column_name 'json_object_keys'
);
ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true ALTER COLUMN cardinality OPTIONS (
    column_name 'cardinality'
);


ALTER FOREIGN TABLE fdw_113.v_control_identifications_several_true OWNER TO darwin2;

--
-- TOC entry 356 (class 1259 OID 13525453)
-- Name: v_count_by_families_genus_species; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_count_by_families_genus_species (
    level character varying,
    family character varying,
    genus character varying,
    species_or_lower text,
    count_all_family bigint,
    count_direct_family bigint,
    count_all_genus bigint,
    count_direct_genus bigint,
    count_all_species text,
    count_direct_species text,
    full_path text,
    full_path_alpha character varying,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_count_by_families_genus_species'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN level OPTIONS (
    column_name 'level'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN species_or_lower OPTIONS (
    column_name 'species_or_lower'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_all_family OPTIONS (
    column_name 'count_all_family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_direct_family OPTIONS (
    column_name 'count_direct_family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_all_genus OPTIONS (
    column_name 'count_all_genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_direct_genus OPTIONS (
    column_name 'count_direct_genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_all_species OPTIONS (
    column_name 'count_all_species'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN count_direct_species OPTIONS (
    column_name 'count_direct_species'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN full_path_alpha OPTIONS (
    column_name 'full_path_alpha'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species OWNER TO darwin2;

--
-- TOC entry 357 (class 1259 OID 13525456)
-- Name: v_count_by_families_genus_species_subspecies; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies (
    level character varying,
    family character varying,
    genus character varying,
    species_or_lower text,
    count_all_family bigint,
    count_direct_family bigint,
    count_all_genus bigint,
    count_direct_genus bigint,
    count_all_species text,
    count_direct_species text,
    count_all_subspecies text,
    count_direct_subspecies text,
    full_path text,
    full_path_alpha character varying,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_count_by_families_genus_species_subspecies'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN level OPTIONS (
    column_name 'level'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN species_or_lower OPTIONS (
    column_name 'species_or_lower'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_all_family OPTIONS (
    column_name 'count_all_family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_direct_family OPTIONS (
    column_name 'count_direct_family'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_all_genus OPTIONS (
    column_name 'count_all_genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_direct_genus OPTIONS (
    column_name 'count_direct_genus'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_all_species OPTIONS (
    column_name 'count_all_species'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_direct_species OPTIONS (
    column_name 'count_direct_species'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_all_subspecies OPTIONS (
    column_name 'count_all_subspecies'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN count_direct_subspecies OPTIONS (
    column_name 'count_direct_subspecies'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN full_path_alpha OPTIONS (
    column_name 'full_path_alpha'
);
ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_count_by_families_genus_species_subspecies OWNER TO darwin2;

--
-- TOC entry 358 (class 1259 OID 13525459)
-- Name: v_danny_2020_check_collection_date; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date (
    record_id integer,
    unitid character varying,
    acquisitionday character varying,
    acquisitionmonth character varying,
    acquisitionyear character varying,
    collectionstartday character varying,
    collectionstartmonth character varying,
    collectionstartyear character varying,
    collectionendday character varying,
    collectionendmonth character varying,
    collectionendyear character varying,
    from_year double precision,
    from_month double precision,
    from_day double precision,
    to_year double precision,
    to_month double precision,
    to_day double precision,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    acquisition_date date,
    acquisition_date_mask integer,
    gtu_from_date timestamp without time zone,
    dw_acquisitionyear double precision,
    dw_acquisitionmonth double precision,
    dw_acquisitionday double precision
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_danny_2020_check_collection_date'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN unitid OPTIONS (
    column_name 'unitid'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN acquisitionday OPTIONS (
    column_name 'acquisitionday'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN acquisitionmonth OPTIONS (
    column_name 'acquisitionmonth'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN acquisitionyear OPTIONS (
    column_name 'acquisitionyear'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionstartday OPTIONS (
    column_name 'collectionstartday'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionstartmonth OPTIONS (
    column_name 'collectionstartmonth'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionstartyear OPTIONS (
    column_name 'collectionstartyear'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionendday OPTIONS (
    column_name 'collectionendday'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionendmonth OPTIONS (
    column_name 'collectionendmonth'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN collectionendyear OPTIONS (
    column_name 'collectionendyear'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN from_year OPTIONS (
    column_name 'from_year'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN from_month OPTIONS (
    column_name 'from_month'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN from_day OPTIONS (
    column_name 'from_day'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN to_year OPTIONS (
    column_name 'to_year'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN to_month OPTIONS (
    column_name 'to_month'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN to_day OPTIONS (
    column_name 'to_day'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN dw_acquisitionyear OPTIONS (
    column_name 'dw_acquisitionyear'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN dw_acquisitionmonth OPTIONS (
    column_name 'dw_acquisitionmonth'
);
ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date ALTER COLUMN dw_acquisitionday OPTIONS (
    column_name 'dw_acquisitionday'
);


ALTER FOREIGN TABLE fdw_113.v_danny_2020_check_collection_date OWNER TO darwin2;

--
-- TOC entry 359 (class 1259 OID 13525462)
-- Name: v_darwin_ichtyo_history_of_reidentifications; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications (
    code character varying,
    main_code_indexed character varying,
    technical_ids integer[],
    technical_creation_dates timestamp without time zone[],
    identifications text[],
    valid_labels boolean[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_ichtyo_history_of_reidentifications'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN main_code_indexed OPTIONS (
    column_name 'main_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN technical_ids OPTIONS (
    column_name 'technical_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN technical_creation_dates OPTIONS (
    column_name 'technical_creation_dates'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN identifications OPTIONS (
    column_name 'identifications'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications ALTER COLUMN valid_labels OPTIONS (
    column_name 'valid_labels'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications OWNER TO darwin2;

--
-- TOC entry 360 (class 1259 OID 13525465)
-- Name: v_darwin_ipt; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_ipt (
    ids text,
    guid text,
    collection_ref integer,
    collection_code character varying,
    collection_name character varying,
    collection_id integer,
    collection_path character varying,
    cataloguenumber text,
    basisofrecord text,
    institutionid text,
    iso_country_institution text,
    bibliographic_citation text,
    license text,
    email text,
    type character varying,
    taxon_path character varying,
    taxon_ref integer,
    taxon_name character varying,
    family character varying,
    iso_country character varying,
    country character varying,
    location text,
    latitude character varying,
    longitude character varying,
    lat_long_accuracy double precision,
    collector_ids integer[],
    collectors text,
    donator_ids integer[],
    donators text,
    identifiers_ids integer[],
    identifiers text,
    gtu_from_date timestamp without time zone,
    gtu_from_date_mask integer,
    gtu_to_date timestamp without time zone,
    gtu_to_date_mask integer,
    eventdate text,
    country_unnest character varying,
    urls_thumbnails character varying,
    image_category_thumbnails character varying,
    contributor_thumbnails character varying,
    disclaimer_thumbnails character varying,
    license_thumbnails character varying,
    display_order_thumbnails integer,
    urls_image_links character varying,
    image_category_image_links character varying,
    contributor_image_links character varying,
    disclaimer_image_links character varying,
    license_image_links character varying,
    display_order_image_links integer,
    urls_3d_snippets character varying,
    image_category_3d_snippets character varying,
    contributor_3d_snippets character varying,
    disclaimer_3d_snippets character varying,
    license_3d_snippets character varying,
    display_order_3d_snippets integer,
    identification_date text,
    history text,
    gtu_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_ipt'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN ids OPTIONS (
    column_name 'ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN guid OPTIONS (
    column_name 'guid'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collection_id OPTIONS (
    column_name 'collection_id'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN cataloguenumber OPTIONS (
    column_name 'cataloguenumber'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN basisofrecord OPTIONS (
    column_name 'basisofrecord'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN institutionid OPTIONS (
    column_name 'institutionid'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN iso_country_institution OPTIONS (
    column_name 'iso_country_institution'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN bibliographic_citation OPTIONS (
    column_name 'bibliographic_citation'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN license OPTIONS (
    column_name 'license'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN email OPTIONS (
    column_name 'email'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN iso_country OPTIONS (
    column_name 'iso_country'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN location OPTIONS (
    column_name 'location'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN lat_long_accuracy OPTIONS (
    column_name 'lat_long_accuracy'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN donator_ids OPTIONS (
    column_name 'donator_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN identifiers_ids OPTIONS (
    column_name 'identifiers_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN eventdate OPTIONS (
    column_name 'eventdate'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN country_unnest OPTIONS (
    column_name 'country_unnest'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN license_thumbnails OPTIONS (
    column_name 'license_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN identification_date OPTIONS (
    column_name 'identification_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN history OPTIONS (
    column_name 'history'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_ipt OWNER TO darwin2;

--
-- TOC entry 361 (class 1259 OID 13525468)
-- Name: v_darwin_ipt_taxonomy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy (
    darwin_id integer,
    scientificname character varying,
    taxonrank character varying,
    verbatimtaxonrank character varying,
    kingdom character varying,
    phylum character varying,
    class character varying,
    "order" character varying,
    family character varying,
    genus character varying,
    higherclassification text,
    path character varying,
    full_path text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_ipt_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN darwin_id OPTIONS (
    column_name 'darwin_id'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN scientificname OPTIONS (
    column_name 'scientificname'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN taxonrank OPTIONS (
    column_name 'taxonrank'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN verbatimtaxonrank OPTIONS (
    column_name 'verbatimtaxonrank'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN kingdom OPTIONS (
    column_name 'kingdom'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN phylum OPTIONS (
    column_name 'phylum'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN class OPTIONS (
    column_name 'class'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN "order" OPTIONS (
    column_name 'order'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN higherclassification OPTIONS (
    column_name 'higherclassification'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy OWNER TO darwin2;

--
-- TOC entry 362 (class 1259 OID 13525471)
-- Name: v_darwin_ipt_taxonomy_vernacular; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular (
    darwin_id integer,
    scientificname character varying,
    taxonrank character varying,
    verbatimtaxonrank character varying,
    kingdom character varying,
    phylum character varying,
    class character varying,
    "order" character varying,
    family character varying,
    genus character varying,
    higherclassification text,
    path character varying,
    full_path text,
    community character varying,
    name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_ipt_taxonomy_vernacular'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN darwin_id OPTIONS (
    column_name 'darwin_id'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN scientificname OPTIONS (
    column_name 'scientificname'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN taxonrank OPTIONS (
    column_name 'taxonrank'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN verbatimtaxonrank OPTIONS (
    column_name 'verbatimtaxonrank'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN kingdom OPTIONS (
    column_name 'kingdom'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN phylum OPTIONS (
    column_name 'phylum'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN class OPTIONS (
    column_name 'class'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN "order" OPTIONS (
    column_name 'order'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN higherclassification OPTIONS (
    column_name 'higherclassification'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN community OPTIONS (
    column_name 'community'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular ALTER COLUMN name OPTIONS (
    column_name 'name'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular OWNER TO darwin2;

--
-- TOC entry 363 (class 1259 OID 13525474)
-- Name: v_darwin_public; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_public (
    ids integer[],
    collection_name character varying,
    code_display text,
    taxon_paths character varying[],
    taxon_ref integer[],
    taxon_name character varying[],
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
    license_order_thumbnails text,
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
    full_count bigint,
    collector_ids integer[],
    collectors character varying[],
    donator_ids integer[],
    donators character varying[],
    localities text[],
    modification_date_time timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_public'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN ids OPTIONS (
    column_name 'ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN code_display OPTIONS (
    column_name 'code_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN taxon_paths OPTIONS (
    column_name 'taxon_paths'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN history_identification OPTIONS (
    column_name 'history_identification'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN fct_mask_date OPTIONS (
    column_name 'fct_mask_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN date_from_display OPTIONS (
    column_name 'date_from_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN date_to_display OPTIONS (
    column_name 'date_to_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN coll_type OPTIONS (
    column_name 'coll_type'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN license_order_thumbnails OPTIONS (
    column_name 'license_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN full_count OPTIONS (
    column_name 'full_count'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN donator_ids OPTIONS (
    column_name 'donator_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN localities OPTIONS (
    column_name 'localities'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_public OWNER TO darwin2;

--
-- TOC entry 364 (class 1259 OID 13525477)
-- Name: v_darwin_public_gis; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_public_gis (
    ids integer[],
    collection_name character varying,
    code_display text,
    taxon_paths character varying[],
    taxon_ref integer[],
    taxon_name character varying[],
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
    license_order_thumbnails text,
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
    full_count bigint,
    collector_ids integer[],
    collectors character varying[],
    donator_ids integer[],
    donators character varying[],
    localities text[],
    modification_date_time timestamp without time zone,
    geom public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_public_gis'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN ids OPTIONS (
    column_name 'ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN code_display OPTIONS (
    column_name 'code_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN taxon_paths OPTIONS (
    column_name 'taxon_paths'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN history_identification OPTIONS (
    column_name 'history_identification'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN fct_mask_date OPTIONS (
    column_name 'fct_mask_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN date_from_display OPTIONS (
    column_name 'date_from_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN date_to_display OPTIONS (
    column_name 'date_to_display'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN coll_type OPTIONS (
    column_name 'coll_type'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN license_order_thumbnails OPTIONS (
    column_name 'license_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN full_count OPTIONS (
    column_name 'full_count'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN donator_ids OPTIONS (
    column_name 'donator_ids'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN localities OPTIONS (
    column_name 'localities'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis ALTER COLUMN geom OPTIONS (
    column_name 'geom'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_public_gis OWNER TO darwin2;

--
-- TOC entry 365 (class 1259 OID 13525480)
-- Name: v_darwin_view_for_csv; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_darwin_view_for_csv (
    id text,
    collection_code character varying,
    code text,
    additional_codes text,
    ig_num character varying,
    taxon_name text,
    author text,
    full_scientific_name text,
    family text,
    type text,
    specimen_count_min integer,
    specimen_count_max integer,
    identifiers text,
    abbreviated_identifiers text,
    identification_year text,
    longitude double precision,
    latitude double precision,
    longitude_text character varying,
    latitude_text character varying,
    gtu_country_tag_value character varying,
    municipality text,
    region_district text,
    exact_site text,
    ecology text,
    gtu_others_tag_value character varying,
    gtu_code character varying,
    gtu_elevation double precision,
    collecting_year_from double precision,
    collecting_month_from double precision,
    collecting_day_from double precision,
    collecting_year_to double precision,
    collecting_month_to double precision,
    collecting_day_to double precision,
    properties_locality text,
    collectors text,
    abbreviated_collectors text,
    expedition_name character varying,
    donators text,
    abbreviated_donators text,
    acquisition_category character varying,
    acquisition_date text,
    sex character varying,
    stage character varying,
    state character varying,
    social_status character varying,
    specimen_part text,
    complete text,
    object_name text,
    specimen_status text,
    container_storage text,
    method text,
    tool text,
    comment text,
    properties_all text,
    specimen_creation_date timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_darwin_view_for_csv'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN additional_codes OPTIONS (
    column_name 'additional_codes'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN author OPTIONS (
    column_name 'author'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN full_scientific_name OPTIONS (
    column_name 'full_scientific_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN abbreviated_identifiers OPTIONS (
    column_name 'abbreviated_identifiers'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN identification_year OPTIONS (
    column_name 'identification_year'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN longitude_text OPTIONS (
    column_name 'longitude_text'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN latitude_text OPTIONS (
    column_name 'latitude_text'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN municipality OPTIONS (
    column_name 'municipality'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN region_district OPTIONS (
    column_name 'region_district'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN exact_site OPTIONS (
    column_name 'exact_site'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN ecology OPTIONS (
    column_name 'ecology'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_year_from OPTIONS (
    column_name 'collecting_year_from'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_month_from OPTIONS (
    column_name 'collecting_month_from'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_day_from OPTIONS (
    column_name 'collecting_day_from'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_year_to OPTIONS (
    column_name 'collecting_year_to'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_month_to OPTIONS (
    column_name 'collecting_month_to'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collecting_day_to OPTIONS (
    column_name 'collecting_day_to'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN properties_locality OPTIONS (
    column_name 'properties_locality'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN abbreviated_collectors OPTIONS (
    column_name 'abbreviated_collectors'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN abbreviated_donators OPTIONS (
    column_name 'abbreviated_donators'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN method OPTIONS (
    column_name 'method'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN tool OPTIONS (
    column_name 'tool'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN comment OPTIONS (
    column_name 'comment'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN properties_all OPTIONS (
    column_name 'properties_all'
);
ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);


ALTER FOREIGN TABLE fdw_113.v_darwin_view_for_csv OWNER TO darwin2;

--
-- TOC entry 366 (class 1259 OID 13525483)
-- Name: v_detect_duplicates; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_detect_duplicates (
    collection_ref integer,
    collection_name character varying,
    code character varying,
    full_code_indexed character varying,
    cpt_spec_ids bigint,
    cpt_taxa character varying[],
    cpt_taxon bigint,
    arr_parts character varying[],
    imports integer[],
    collection_full_path text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_detect_duplicates'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN cpt_spec_ids OPTIONS (
    column_name 'cpt_spec_ids'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN cpt_taxa OPTIONS (
    column_name 'cpt_taxa'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN cpt_taxon OPTIONS (
    column_name 'cpt_taxon'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN arr_parts OPTIONS (
    column_name 'arr_parts'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN imports OPTIONS (
    column_name 'imports'
);
ALTER FOREIGN TABLE fdw_113.v_detect_duplicates ALTER COLUMN collection_full_path OPTIONS (
    column_name 'collection_full_path'
);


ALTER FOREIGN TABLE fdw_113.v_detect_duplicates OWNER TO darwin2;

--
-- TOC entry 367 (class 1259 OID 13525486)
-- Name: v_diagnose_country_from_coord; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_diagnose_country_from_coord (
    id integer,
    gtu_location point,
    na2_descri character varying(80),
    gtu_country_tag_indexed character varying[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_diagnose_country_from_coord'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_country_from_coord ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_country_from_coord ALTER COLUMN gtu_location OPTIONS (
    column_name 'gtu_location'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_country_from_coord ALTER COLUMN na2_descri OPTIONS (
    column_name 'na2_descri'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_country_from_coord ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);


ALTER FOREIGN TABLE fdw_113.v_diagnose_country_from_coord OWNER TO darwin2;

--
-- TOC entry 368 (class 1259 OID 13525489)
-- Name: v_diagnose_fast_country_outlier; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier (
    id integer,
    na2_descri character varying(80),
    bounding_box public.geometry,
    gtu_country_tag_indexed character varying,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_diagnose_fast_country_outlier'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier ALTER COLUMN na2_descri OPTIONS (
    column_name 'na2_descri'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier ALTER COLUMN bounding_box OPTIONS (
    column_name 'bounding_box'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier OWNER TO darwin2;

--
-- TOC entry 369 (class 1259 OID 13525492)
-- Name: v_diagnose_fast_country_outlier_tmp; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp (
    id integer,
    na2_descri character varying(80),
    bounding_box public.geometry,
    gtu_country_tag_indexed character varying,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_diagnose_fast_country_outlier_tmp'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp ALTER COLUMN na2_descri OPTIONS (
    column_name 'na2_descri'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp ALTER COLUMN bounding_box OPTIONS (
    column_name 'bounding_box'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_diagnose_fast_country_outlier_tmp OWNER TO darwin2;

--
-- TOC entry 370 (class 1259 OID 13525495)
-- Name: v_elephants_ivory_emmanuel; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel (
    comments text,
    specimen_code text,
    taxon character varying,
    acquisition_date date,
    collection_date timestamp without time zone,
    gtu_country_tag_value character varying,
    gtu_province_tag_value character varying,
    gtu_others_tag_value character varying,
    sex character varying,
    stage character varying,
    ig_num character varying,
    lat double precision,
    long double precision
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_elephants_ivory_emmanuel'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN comments OPTIONS (
    column_name 'comments'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN specimen_code OPTIONS (
    column_name 'specimen_code'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN collection_date OPTIONS (
    column_name 'collection_date'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN lat OPTIONS (
    column_name 'lat'
);
ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel ALTER COLUMN long OPTIONS (
    column_name 'long'
);


ALTER FOREIGN TABLE fdw_113.v_elephants_ivory_emmanuel OWNER TO darwin2;

--
-- TOC entry 371 (class 1259 OID 13525498)
-- Name: v_erik_verheyen_mammals_in_alcohol_2020; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 (
    taxon_group character varying,
    taxon_name character varying,
    family character varying,
    genus character varying,
    species character varying,
    container_type character varying,
    country text,
    loc_list text,
    date_min timestamp without time zone,
    date_max text,
    nb_records bigint,
    specimen_count_min bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_erik_verheyen_mammals_in_alcohol_2020'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN taxon_group OPTIONS (
    column_name 'taxon_group'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN species OPTIONS (
    column_name 'species'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN loc_list OPTIONS (
    column_name 'loc_list'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN date_min OPTIONS (
    column_name 'date_min'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN date_max OPTIONS (
    column_name 'date_max'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);


ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 OWNER TO darwin2;

--
-- TOC entry 372 (class 1259 OID 13525501)
-- Name: v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro (
    taxon_group character varying,
    family character varying,
    genus character varying,
    species character varying,
    container_type character varying,
    country text,
    loc_list text[],
    date_min timestamp without time zone,
    date_max text,
    nb_records numeric,
    specimen_count_min numeric
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN taxon_group OPTIONS (
    column_name 'taxon_group'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN species OPTIONS (
    column_name 'species'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN loc_list OPTIONS (
    column_name 'loc_list'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN date_min OPTIONS (
    column_name 'date_min'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN date_max OPTIONS (
    column_name 'date_max'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);


ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro OWNER TO darwin2;

--
-- TOC entry 373 (class 1259 OID 13525504)
-- Name: v_erik_verheyen_mammals_in_alcohol_2020_generic; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic (
    taxon_group character varying,
    container_type character varying,
    country text,
    array_agg text[],
    min timestamp without time zone,
    max text,
    nb_records numeric,
    specimen_count_min numeric
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_erik_verheyen_mammals_in_alcohol_2020_generic'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN taxon_group OPTIONS (
    column_name 'taxon_group'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN array_agg OPTIONS (
    column_name 'array_agg'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN min OPTIONS (
    column_name 'min'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN max OPTIONS (
    column_name 'max'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);


ALTER FOREIGN TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic OWNER TO darwin2;

--
-- TOC entry 374 (class 1259 OID 13525507)
-- Name: v_fix_property_shift; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_fix_property_shift (
    key_1 text[],
    key_2 text[],
    new_val public.hstore,
    old_val public.hstore,
    id integer,
    referenced_relation character varying,
    record_id integer,
    user_ref integer,
    action character varying,
    old_value public.hstore,
    new_value public.hstore,
    modification_date_time timestamp without time zone
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_fix_property_shift'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN key_1 OPTIONS (
    column_name 'key_1'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN key_2 OPTIONS (
    column_name 'key_2'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN new_val OPTIONS (
    column_name 'new_val'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN old_val OPTIONS (
    column_name 'old_val'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN action OPTIONS (
    column_name 'action'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN old_value OPTIONS (
    column_name 'old_value'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN new_value OPTIONS (
    column_name 'new_value'
);
ALTER FOREIGN TABLE fdw_113.v_fix_property_shift ALTER COLUMN modification_date_time OPTIONS (
    column_name 'modification_date_time'
);


ALTER FOREIGN TABLE fdw_113.v_fix_property_shift OWNER TO darwin2;

--
-- TOC entry 375 (class 1259 OID 13525510)
-- Name: v_gbif_snails_kin_tine; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine (
    id integer,
    uuid uuid,
    guid text,
    collection_ref integer,
    collection_code character varying,
    collection_name character varying,
    collection_id integer,
    collection_path character varying,
    cataloguenumber text,
    auxcode character varying,
    basisofrecord text,
    institutionid text,
    iso_country_institution text,
    bibliographic_citation text,
    licence text,
    email text,
    type character varying,
    taxon_path character varying,
    taxon_ref integer,
    taxon_name character varying,
    family character varying,
    iso_country character varying,
    country character varying,
    location text,
    latitude character varying,
    longitude character varying,
    lat_long_accuracy double precision,
    collector_ids integer[],
    collectors text,
    donator_ids integer[],
    donators text,
    identifiers_ids integer[],
    identifiers text,
    gtu_from_date text,
    gtu_to_date text,
    eventdate text,
    country_unnest character varying,
    urls_thumbnails character varying,
    image_category_thumbnails character varying,
    contributor_thumbnails character varying,
    disclaimer_thumbnails character varying,
    license_thumbnails character varying,
    display_order_thumbnails integer,
    urls_image_links character varying,
    image_category_image_links character varying,
    contributor_image_links character varying,
    disclaimer_image_links character varying,
    license_image_links character varying,
    display_order_image_links integer,
    urls_3d_snippets character varying,
    image_category_3d_snippets character varying,
    contributor_3d_snippets character varying,
    disclaimer_3d_snippets character varying,
    license_3d_snippets character varying,
    display_order_3d_snippets integer,
    identification_date text,
    history text,
    gtu_ref integer,
    specimen_count_min integer,
    specimen_count_males_min integer,
    specimen_count_females_min integer,
    specimen_count_juveniles_min integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_gbif_snails_kin_tine'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN guid OPTIONS (
    column_name 'guid'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collection_id OPTIONS (
    column_name 'collection_id'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN cataloguenumber OPTIONS (
    column_name 'cataloguenumber'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN auxcode OPTIONS (
    column_name 'auxcode'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN basisofrecord OPTIONS (
    column_name 'basisofrecord'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN institutionid OPTIONS (
    column_name 'institutionid'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN iso_country_institution OPTIONS (
    column_name 'iso_country_institution'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN bibliographic_citation OPTIONS (
    column_name 'bibliographic_citation'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN licence OPTIONS (
    column_name 'licence'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN email OPTIONS (
    column_name 'email'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN iso_country OPTIONS (
    column_name 'iso_country'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN location OPTIONS (
    column_name 'location'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN latitude OPTIONS (
    column_name 'latitude'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN longitude OPTIONS (
    column_name 'longitude'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN lat_long_accuracy OPTIONS (
    column_name 'lat_long_accuracy'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collector_ids OPTIONS (
    column_name 'collector_ids'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN donator_ids OPTIONS (
    column_name 'donator_ids'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN identifiers_ids OPTIONS (
    column_name 'identifiers_ids'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN eventdate OPTIONS (
    column_name 'eventdate'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN country_unnest OPTIONS (
    column_name 'country_unnest'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN urls_thumbnails OPTIONS (
    column_name 'urls_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN image_category_thumbnails OPTIONS (
    column_name 'image_category_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN contributor_thumbnails OPTIONS (
    column_name 'contributor_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN disclaimer_thumbnails OPTIONS (
    column_name 'disclaimer_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN license_thumbnails OPTIONS (
    column_name 'license_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN display_order_thumbnails OPTIONS (
    column_name 'display_order_thumbnails'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN urls_image_links OPTIONS (
    column_name 'urls_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN image_category_image_links OPTIONS (
    column_name 'image_category_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN contributor_image_links OPTIONS (
    column_name 'contributor_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN disclaimer_image_links OPTIONS (
    column_name 'disclaimer_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN license_image_links OPTIONS (
    column_name 'license_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN display_order_image_links OPTIONS (
    column_name 'display_order_image_links'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN urls_3d_snippets OPTIONS (
    column_name 'urls_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN image_category_3d_snippets OPTIONS (
    column_name 'image_category_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN contributor_3d_snippets OPTIONS (
    column_name 'contributor_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN disclaimer_3d_snippets OPTIONS (
    column_name 'disclaimer_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN license_3d_snippets OPTIONS (
    column_name 'license_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN display_order_3d_snippets OPTIONS (
    column_name 'display_order_3d_snippets'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN identification_date OPTIONS (
    column_name 'identification_date'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN history OPTIONS (
    column_name 'history'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN specimen_count_males_min OPTIONS (
    column_name 'specimen_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN specimen_count_females_min OPTIONS (
    column_name 'specimen_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine ALTER COLUMN specimen_count_juveniles_min OPTIONS (
    column_name 'specimen_count_juveniles_min'
);


ALTER FOREIGN TABLE fdw_113.v_gbif_snails_kin_tine OWNER TO darwin2;

--
-- TOC entry 376 (class 1259 OID 13525513)
-- Name: v_get_catalogue_numbers_by_collection; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection (
    code text,
    record_id integer,
    collection_name character varying,
    taxon_name character varying,
    full_collection_path text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_get_catalogue_numbers_by_collection'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection ALTER COLUMN full_collection_path OPTIONS (
    column_name 'full_collection_path'
);


ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection OWNER TO darwin2;

--
-- TOC entry 377 (class 1259 OID 13525516)
-- Name: v_get_catalogue_numbers_by_collection_taxonomy_with_parent; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent (
    code text,
    record_id integer,
    collection_name character varying,
    taxon_name character varying,
    full_collection_path text,
    family character varying,
    order_taxon character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_get_catalogue_numbers_by_collection_taxonomy_with_parent'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN full_collection_path OPTIONS (
    column_name 'full_collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent ALTER COLUMN order_taxon OPTIONS (
    column_name 'order_taxon'
);


ALTER FOREIGN TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent OWNER TO darwin2;

--
-- TOC entry 378 (class 1259 OID 13525519)
-- Name: v_ichtyology_series_fast; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_ichtyology_series_fast (
    prefix text,
    range integer[],
    split text[],
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
    code_num_bigint bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_ichtyology_series_fast'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN prefix OPTIONS (
    column_name 'prefix'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN range OPTIONS (
    column_name 'range'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN split OPTIONS (
    column_name 'split'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_date_mask OPTIONS (
    column_name 'code_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_num OPTIONS (
    column_name 'code_num'
);
ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast ALTER COLUMN code_num_bigint OPTIONS (
    column_name 'code_num_bigint'
);


ALTER FOREIGN TABLE fdw_113.v_ichtyology_series_fast OWNER TO darwin2;

--
-- TOC entry 379 (class 1259 OID 13525522)
-- Name: v_imports_filename_encoded; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_imports_filename_encoded (
    id integer,
    user_ref integer,
    format character varying,
    collection_ref integer,
    filename character varying,
    state character varying,
    created_at timestamp without time zone,
    updated_at timestamp without time zone,
    initial_count integer,
    is_finished boolean,
    errors_in_import text,
    template_version text,
    exclude_invalid_entries boolean,
    specimen_taxonomy_ref integer,
    taxonomy_name character varying,
    creation_date date,
    creation_date_mask integer,
    definition_taxonomy text,
    is_reference_taxonomy boolean,
    source_taxonomy character varying,
    url_website_taxonomy character varying,
    url_webservice_taxonomy character varying,
    working boolean,
    mime_type character varying,
    taxonomy_kingdom character varying,
    history_taxonomy public.hstore,
    merge_gtu boolean,
    filename_encoded text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_imports_filename_encoded'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN format OPTIONS (
    column_name 'format'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN filename OPTIONS (
    column_name 'filename'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN created_at OPTIONS (
    column_name 'created_at'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN updated_at OPTIONS (
    column_name 'updated_at'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN initial_count OPTIONS (
    column_name 'initial_count'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN is_finished OPTIONS (
    column_name 'is_finished'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN errors_in_import OPTIONS (
    column_name 'errors_in_import'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN template_version OPTIONS (
    column_name 'template_version'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN exclude_invalid_entries OPTIONS (
    column_name 'exclude_invalid_entries'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN specimen_taxonomy_ref OPTIONS (
    column_name 'specimen_taxonomy_ref'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN taxonomy_name OPTIONS (
    column_name 'taxonomy_name'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN creation_date_mask OPTIONS (
    column_name 'creation_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN definition_taxonomy OPTIONS (
    column_name 'definition_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN is_reference_taxonomy OPTIONS (
    column_name 'is_reference_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN source_taxonomy OPTIONS (
    column_name 'source_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN url_website_taxonomy OPTIONS (
    column_name 'url_website_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN url_webservice_taxonomy OPTIONS (
    column_name 'url_webservice_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN working OPTIONS (
    column_name 'working'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN mime_type OPTIONS (
    column_name 'mime_type'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN taxonomy_kingdom OPTIONS (
    column_name 'taxonomy_kingdom'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN history_taxonomy OPTIONS (
    column_name 'history_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN merge_gtu OPTIONS (
    column_name 'merge_gtu'
);
ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded ALTER COLUMN filename_encoded OPTIONS (
    column_name 'filename_encoded'
);


ALTER FOREIGN TABLE fdw_113.v_imports_filename_encoded OWNER TO darwin2;

--
-- TOC entry 380 (class 1259 OID 13525525)
-- Name: v_loan_detail_role_person; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loan_detail_role_person (
    referenced_relation character varying,
    record_id integer,
    id integer,
    people_type character varying,
    people_sub_type character varying,
    order_by integer,
    people_ref integer,
    other boolean,
    transporter boolean,
    attendant boolean,
    preparator boolean,
    checker boolean,
    contact boolean,
    role_responsible boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loan_detail_role_person'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN people_type OPTIONS (
    column_name 'people_type'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN people_sub_type OPTIONS (
    column_name 'people_sub_type'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN order_by OPTIONS (
    column_name 'order_by'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN other OPTIONS (
    column_name 'other'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN transporter OPTIONS (
    column_name 'transporter'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN attendant OPTIONS (
    column_name 'attendant'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN preparator OPTIONS (
    column_name 'preparator'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN checker OPTIONS (
    column_name 'checker'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN contact OPTIONS (
    column_name 'contact'
);
ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person ALTER COLUMN role_responsible OPTIONS (
    column_name 'role_responsible'
);


ALTER FOREIGN TABLE fdw_113.v_loan_detail_role_person OWNER TO darwin2;

--
-- TOC entry 381 (class 1259 OID 13525528)
-- Name: v_loan_details_for_pentaho; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loan_details_for_pentaho (
    id integer,
    loan_ref integer,
    ig_ref integer,
    from_date date,
    to_date date,
    specimen_ref integer,
    details character varying,
    taxon_name character varying,
    code character varying,
    array_agg character varying[],
    detail_loan text,
    type character varying,
    category character varying,
    specimen_part character varying,
    specimen_status character varying,
    loan_remarks text,
    loan_id integer,
    loan_name character varying,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loan_details_for_pentaho'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN loan_ref OPTIONS (
    column_name 'loan_ref'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN specimen_ref OPTIONS (
    column_name 'specimen_ref'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN details OPTIONS (
    column_name 'details'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN array_agg OPTIONS (
    column_name 'array_agg'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN detail_loan OPTIONS (
    column_name 'detail_loan'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN loan_remarks OPTIONS (
    column_name 'loan_remarks'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN loan_id OPTIONS (
    column_name 'loan_id'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN loan_name OPTIONS (
    column_name 'loan_name'
);
ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_loan_details_for_pentaho OWNER TO darwin2;

--
-- TOC entry 382 (class 1259 OID 13525531)
-- Name: v_loans_for_pentaho; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loans_for_pentaho (
    id integer,
    name character varying,
    description character varying,
    search_indexed text,
    from_date date,
    to_date date,
    extended_to_date date,
    loan_at_your_request character varying,
    in_exchange character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loans_for_pentaho'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN search_indexed OPTIONS (
    column_name 'search_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN extended_to_date OPTIONS (
    column_name 'extended_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN loan_at_your_request OPTIONS (
    column_name 'loan_at_your_request'
);
ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho ALTER COLUMN in_exchange OPTIONS (
    column_name 'in_exchange'
);


ALTER FOREIGN TABLE fdw_113.v_loans_for_pentaho OWNER TO darwin2;

--
-- TOC entry 383 (class 1259 OID 13525534)
-- Name: v_loans_pentaho_contact_person; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loans_pentaho_contact_person (
    people_group text,
    people_type character varying,
    record_id integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loans_pentaho_contact_person'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_contact_person ALTER COLUMN people_group OPTIONS (
    column_name 'people_group'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_contact_person ALTER COLUMN people_type OPTIONS (
    column_name 'people_type'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_contact_person ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);


ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_contact_person OWNER TO darwin2;

--
-- TOC entry 384 (class 1259 OID 13525537)
-- Name: v_loans_pentaho_general; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loans_pentaho_general (
    id integer,
    name character varying,
    description character varying,
    search_indexed text,
    from_date date,
    to_date date,
    extended_to_date date,
    loan_at_your_request character varying,
    gift character varying,
    in_exchange character varying,
    loan_for_identification_our_request character varying,
    return_of_material_sent_for_id character varying,
    return_of_borrowed_material character varying,
    shipping_type text,
    transporter character varying,
    registration_date text,
    insurance text,
    packages_count character varying,
    weight text,
    non_cites text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loans_pentaho_general'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN search_indexed OPTIONS (
    column_name 'search_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN extended_to_date OPTIONS (
    column_name 'extended_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN loan_at_your_request OPTIONS (
    column_name 'loan_at_your_request'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN gift OPTIONS (
    column_name 'gift'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN in_exchange OPTIONS (
    column_name 'in_exchange'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN loan_for_identification_our_request OPTIONS (
    column_name 'loan_for_identification_our_request'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN return_of_material_sent_for_id OPTIONS (
    column_name 'return_of_material_sent_for_id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN return_of_borrowed_material OPTIONS (
    column_name 'return_of_borrowed_material'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN shipping_type OPTIONS (
    column_name 'shipping_type'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN transporter OPTIONS (
    column_name 'transporter'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN registration_date OPTIONS (
    column_name 'registration_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN insurance OPTIONS (
    column_name 'insurance'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN packages_count OPTIONS (
    column_name 'packages_count'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN weight OPTIONS (
    column_name 'weight'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general ALTER COLUMN non_cites OPTIONS (
    column_name 'non_cites'
);


ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_general OWNER TO darwin2;

--
-- TOC entry 385 (class 1259 OID 13525540)
-- Name: v_loans_pentaho_receivers; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_loans_pentaho_receivers (
    id integer,
    name character varying,
    description character varying,
    search_indexed text,
    from_date date,
    to_date date,
    extended_to_date date,
    receiver_id text,
    receiver text,
    institution_receiver text,
    address_institution text,
    country_institution character varying,
    sender_id integer,
    sender character varying,
    receiver_email text,
    receiver_tel text,
    contact_sender_role text,
    collection_manager character varying,
    collection_manager_title character varying,
    collection_manager_mail character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_loans_pentaho_receivers'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN description OPTIONS (
    column_name 'description'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN search_indexed OPTIONS (
    column_name 'search_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN from_date OPTIONS (
    column_name 'from_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN to_date OPTIONS (
    column_name 'to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN extended_to_date OPTIONS (
    column_name 'extended_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN receiver_id OPTIONS (
    column_name 'receiver_id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN receiver OPTIONS (
    column_name 'receiver'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN institution_receiver OPTIONS (
    column_name 'institution_receiver'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN address_institution OPTIONS (
    column_name 'address_institution'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN country_institution OPTIONS (
    column_name 'country_institution'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN sender_id OPTIONS (
    column_name 'sender_id'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN sender OPTIONS (
    column_name 'sender'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN receiver_email OPTIONS (
    column_name 'receiver_email'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN receiver_tel OPTIONS (
    column_name 'receiver_tel'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN contact_sender_role OPTIONS (
    column_name 'contact_sender_role'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN collection_manager OPTIONS (
    column_name 'collection_manager'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN collection_manager_title OPTIONS (
    column_name 'collection_manager_title'
);
ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers ALTER COLUMN collection_manager_mail OPTIONS (
    column_name 'collection_manager_mail'
);


ALTER FOREIGN TABLE fdw_113.v_loans_pentaho_receivers OWNER TO darwin2;

--
-- TOC entry 386 (class 1259 OID 13525543)
-- Name: v_mbisa_correspondence_dw_number_eod_mukweze_2022; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 (
    id_technique integer,
    code character varying,
    code_terrain_mukweze text,
    code_terrain_darwin character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_mbisa_correspondence_dw_number_eod_mukweze_2022'
);
ALTER FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 ALTER COLUMN id_technique OPTIONS (
    column_name 'id_technique'
);
ALTER FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 ALTER COLUMN code_terrain_mukweze OPTIONS (
    column_name 'code_terrain_mukweze'
);
ALTER FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 ALTER COLUMN code_terrain_darwin OPTIONS (
    column_name 'code_terrain_darwin'
);


ALTER FOREIGN TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 OWNER TO darwin2;

--
-- TOC entry 387 (class 1259 OID 13525546)
-- Name: v_rdf_view; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rdf_view (
    uuid uuid,
    "SpecimenID" text,
    "RefUri" text,
    "ObjectUri" text,
    "Title" text,
    "TitleDescription" text,
    collector text,
    "CollectionDate" text,
    "ObjectURI" text,
    modified timestamp without time zone,
    "BaseOfRecord" text,
    "InstitutionCode" text,
    "CollectionName" character varying,
    "CatalogNumber" text,
    "Family" character varying,
    "Genus" character varying,
    "SpecificEpithet" character varying,
    "ScientificName" character varying,
    "HigherGeography" character varying,
    "Country" character varying,
    "Locality" text,
    "Image" character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rdf_view'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "SpecimenID" OPTIONS (
    column_name 'SpecimenID'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "RefUri" OPTIONS (
    column_name 'RefUri'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "ObjectUri" OPTIONS (
    column_name 'ObjectUri'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Title" OPTIONS (
    column_name 'Title'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "TitleDescription" OPTIONS (
    column_name 'TitleDescription'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN collector OPTIONS (
    column_name 'collector'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "CollectionDate" OPTIONS (
    column_name 'CollectionDate'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "ObjectURI" OPTIONS (
    column_name 'ObjectURI'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN modified OPTIONS (
    column_name 'modified'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "BaseOfRecord" OPTIONS (
    column_name 'BaseOfRecord'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "InstitutionCode" OPTIONS (
    column_name 'InstitutionCode'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "CollectionName" OPTIONS (
    column_name 'CollectionName'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "CatalogNumber" OPTIONS (
    column_name 'CatalogNumber'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Family" OPTIONS (
    column_name 'Family'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Genus" OPTIONS (
    column_name 'Genus'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "SpecificEpithet" OPTIONS (
    column_name 'SpecificEpithet'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "ScientificName" OPTIONS (
    column_name 'ScientificName'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "HigherGeography" OPTIONS (
    column_name 'HigherGeography'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Country" OPTIONS (
    column_name 'Country'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Locality" OPTIONS (
    column_name 'Locality'
);
ALTER FOREIGN TABLE fdw_113.v_rdf_view ALTER COLUMN "Image" OPTIONS (
    column_name 'Image'
);


ALTER FOREIGN TABLE fdw_113.v_rdf_view OWNER TO darwin2;

--
-- TOC entry 388 (class 1259 OID 13525549)
-- Name: v_report_group_taxon_full_path_per_insertion_year; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year (
    taxon_id text,
    year double precision,
    spec_ids integer[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_report_group_taxon_full_path_per_insertion_year'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year ALTER COLUMN taxon_id OPTIONS (
    column_name 'taxon_id'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year ALTER COLUMN spec_ids OPTIONS (
    column_name 'spec_ids'
);


ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year OWNER TO darwin2;

--
-- TOC entry 389 (class 1259 OID 13525552)
-- Name: v_report_group_taxon_full_path_per_insertion_year_all; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all (
    min_year double precision,
    collection_name character varying,
    taxon_level_name character varying,
    taxon_level_ref integer,
    count_taxa bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_report_group_taxon_full_path_per_insertion_year_all'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all ALTER COLUMN min_year OPTIONS (
    column_name 'min_year'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all ALTER COLUMN count_taxa OPTIONS (
    column_name 'count_taxa'
);


ALTER FOREIGN TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all OWNER TO darwin2;

--
-- TOC entry 390 (class 1259 OID 13525555)
-- Name: v_report_yearly_encoding_statistics_specimens; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_report_yearly_encoding_statistics_specimens (
    name character varying,
    year double precision,
    specimen_count bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_report_yearly_encoding_statistics_specimens'
);
ALTER FOREIGN TABLE fdw_113.v_report_yearly_encoding_statistics_specimens ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_report_yearly_encoding_statistics_specimens ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_report_yearly_encoding_statistics_specimens ALTER COLUMN specimen_count OPTIONS (
    column_name 'specimen_count'
);


ALTER FOREIGN TABLE fdw_113.v_report_yearly_encoding_statistics_specimens OWNER TO darwin2;

--
-- TOC entry 391 (class 1259 OID 13525558)
-- Name: v_reporting_count_all_specimens; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens (
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens OWNER TO darwin2;

--
-- TOC entry 392 (class 1259 OID 13525561)
-- Name: v_reporting_count_all_specimens_by_collection; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection (
    name character varying,
    collection_ref integer,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens_by_collection'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection OWNER TO darwin2;

--
-- TOC entry 393 (class 1259 OID 13525564)
-- Name: v_reporting_count_all_specimens_by_collection_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig (
    collection_name character varying,
    collection_path character varying,
    collection_ref integer,
    year double precision,
    specimen_creation_date timestamp without time zone,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint,
    ig_ref integer,
    ig_num character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens_by_collection_year_ig'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig OWNER TO darwin2;

--
-- TOC entry 394 (class 1259 OID 13525567)
-- Name: v_reporting_count_all_specimens_type; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type (
    type text,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens_type'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type OWNER TO darwin2;

--
-- TOC entry 395 (class 1259 OID 13525570)
-- Name: v_reporting_count_all_specimens_type_by_collection; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection (
    type text,
    collection_id integer,
    collection_name character varying,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens_type_by_collection'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN collection_id OPTIONS (
    column_name 'collection_id'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection OWNER TO darwin2;

--
-- TOC entry 396 (class 1259 OID 13525573)
-- Name: v_reporting_count_all_specimens_type_by_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig (
    collection_path character varying,
    collection_name character varying,
    collection_ref integer,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    specimen_creation_date timestamp without time zone,
    type text,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_count_all_specimens_type_by_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 397 (class 1259 OID 13525576)
-- Name: v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig (
    level_ref integer,
    level_name character varying,
    rank text,
    taxon text,
    year double precision,
    creation_date timestamp without time zone,
    ig_ref integer,
    ig_num character varying,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    countries character varying[],
    min_lon double precision,
    min_lat double precision,
    max_lon double precision,
    max_lat double precision
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN rank OPTIONS (
    column_name 'rank'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN countries OPTIONS (
    column_name 'countries'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN min_lon OPTIONS (
    column_name 'min_lon'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN min_lat OPTIONS (
    column_name 'min_lat'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN max_lon OPTIONS (
    column_name 'max_lon'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig ALTER COLUMN max_lat OPTIONS (
    column_name 'max_lat'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 398 (class 1259 OID 13525579)
-- Name: v_reporting_higher_taxa_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig (
    level_ref integer,
    level_name character varying,
    rank text,
    taxon text,
    year double precision,
    creation_date timestamp without time zone,
    ig_ref integer,
    ig_num character varying,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_higher_taxa_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN rank OPTIONS (
    column_name 'rank'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 399 (class 1259 OID 13525582)
-- Name: v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig (
    taxonomy_id integer,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    creation_date timestamp without time zone,
    level_ref integer,
    level_name character varying,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN taxonomy_id OPTIONS (
    column_name 'taxonomy_id'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig OWNER TO darwin2;

--
-- TOC entry 400 (class 1259 OID 13525585)
-- Name: v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall (
    taxonomy_id integer,
    collection_path character varying,
    collection_ref integer,
    collection_name character varying,
    ig_ref integer,
    ig_num character varying,
    year double precision,
    creation_date timestamp without time zone,
    level_ref integer,
    level_name character varying,
    nb_records bigint,
    specimen_count_min bigint,
    specimen_count_max bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN taxonomy_id OPTIONS (
    column_name 'taxonomy_id'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN year OPTIONS (
    column_name 'year'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN creation_date OPTIONS (
    column_name 'creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN nb_records OPTIONS (
    column_name 'nb_records'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall OWNER TO darwin2;

--
-- TOC entry 401 (class 1259 OID 13525588)
-- Name: v_reporting_taxonomy_general; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_taxonomy_general (
    level_name character varying,
    count bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_taxonomy_general'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_general ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_general ALTER COLUMN count OPTIONS (
    column_name 'count'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_general OWNER TO darwin2;

--
-- TOC entry 402 (class 1259 OID 13525591)
-- Name: v_reporting_taxonomy_in_specimen; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_reporting_taxonomy_in_specimen (
    taxon_level_name character varying,
    count bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_reporting_taxonomy_in_specimen'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_in_specimen ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_in_specimen ALTER COLUMN count OPTIONS (
    column_name 'count'
);


ALTER FOREIGN TABLE fdw_113.v_reporting_taxonomy_in_specimen OWNER TO darwin2;

--
-- TOC entry 403 (class 1259 OID 13525594)
-- Name: v_rmca_check_taxonomy_in_staging; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging (
    code character varying,
    status public.hstore,
    taxon_name character varying,
    taxon_level_name character varying,
    imported_taxonomy_in_source character varying,
    existing_taxonomy_in_darwin text,
    import_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_check_taxonomy_in_staging'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN imported_taxonomy_in_source OPTIONS (
    column_name 'imported_taxonomy_in_source'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN existing_taxonomy_in_darwin OPTIONS (
    column_name 'existing_taxonomy_in_darwin'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_check_taxonomy_in_staging OWNER TO darwin2;

--
-- TOC entry 404 (class 1259 OID 13525597)
-- Name: v_rmca_collections_path_as_text; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text (
    id integer,
    collection_type character varying,
    code character varying,
    name character varying,
    name_indexed character varying,
    institution_ref integer,
    main_manager_ref integer,
    parent_ref integer,
    path character varying,
    code_auto_increment boolean,
    code_last_value integer,
    code_prefix character varying,
    code_prefix_separator character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    is_public boolean,
    code_specimen_duplicate boolean,
    staff_ref integer,
    code_auto_increment_for_insert_only boolean,
    code_mask character varying,
    allow_duplicates boolean,
    collection_path_text text,
    collection_path_code text,
    collection_path_indexed text,
    collection_main_text text,
    collection_main_code text,
    collection_main_indexed text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_collections_path_as_text'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN main_manager_ref OPTIONS (
    column_name 'main_manager_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_auto_increment OPTIONS (
    column_name 'code_auto_increment'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_last_value OPTIONS (
    column_name 'code_last_value'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN is_public OPTIONS (
    column_name 'is_public'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_specimen_duplicate OPTIONS (
    column_name 'code_specimen_duplicate'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN staff_ref OPTIONS (
    column_name 'staff_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_auto_increment_for_insert_only OPTIONS (
    column_name 'code_auto_increment_for_insert_only'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN code_mask OPTIONS (
    column_name 'code_mask'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN allow_duplicates OPTIONS (
    column_name 'allow_duplicates'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_path_text OPTIONS (
    column_name 'collection_path_text'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_path_code OPTIONS (
    column_name 'collection_path_code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_path_indexed OPTIONS (
    column_name 'collection_path_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_main_text OPTIONS (
    column_name 'collection_main_text'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_main_code OPTIONS (
    column_name 'collection_main_code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text ALTER COLUMN collection_main_indexed OPTIONS (
    column_name 'collection_main_indexed'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_collections_path_as_text OWNER TO darwin2;

--
-- TOC entry 405 (class 1259 OID 13525600)
-- Name: v_rmca_count_ichtyology_by_number_full_good; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good (
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
    upper_count integer,
    lower_count integer,
    counter integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_count_ichtyology_by_number_full_good'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN upper_count OPTIONS (
    column_name 'upper_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN lower_count OPTIONS (
    column_name 'lower_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good ALTER COLUMN counter OPTIONS (
    column_name 'counter'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good OWNER TO darwin2;

--
-- TOC entry 406 (class 1259 OID 13525603)
-- Name: v_rmca_count_ichtyology_by_number_full_restrict_ichtyo; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo (
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
    upper_count integer,
    lower_count integer,
    counter integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_count_ichtyology_by_number_full_restrict_ichtyo'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN upper_count OPTIONS (
    column_name 'upper_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN lower_count OPTIONS (
    column_name 'lower_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo ALTER COLUMN counter OPTIONS (
    column_name 'counter'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo OWNER TO darwin2;

--
-- TOC entry 407 (class 1259 OID 13525606)
-- Name: v_rmca_count_specimen_by_families_genus; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus (
    family character varying,
    child_id integer,
    family_or_genus character varying,
    collection_ref integer,
    count_all bigint,
    count_direct bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_count_specimen_by_families_genus'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN family_or_genus OPTIONS (
    column_name 'family_or_genus'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN count_all OPTIONS (
    column_name 'count_all'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus ALTER COLUMN count_direct OPTIONS (
    column_name 'count_direct'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_families_genus OWNER TO darwin2;

--
-- TOC entry 408 (class 1259 OID 13525609)
-- Name: v_rmca_count_specimen_by_higher; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher (
    parent_id integer,
    parent_level_name character varying,
    parent_level_ref integer,
    higher_name character varying,
    child_id integer,
    child_level_name character varying,
    child_level_ref integer,
    lower_name character varying,
    collection_ref integer,
    count_all bigint,
    full_path text,
    count_direct bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_count_specimen_by_higher'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN parent_level_name OPTIONS (
    column_name 'parent_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN parent_level_ref OPTIONS (
    column_name 'parent_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN higher_name OPTIONS (
    column_name 'higher_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN child_level_name OPTIONS (
    column_name 'child_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN child_level_ref OPTIONS (
    column_name 'child_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN lower_name OPTIONS (
    column_name 'lower_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN count_all OPTIONS (
    column_name 'count_all'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher ALTER COLUMN count_direct OPTIONS (
    column_name 'count_direct'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_count_specimen_by_higher OWNER TO darwin2;

--
-- TOC entry 409 (class 1259 OID 13525612)
-- Name: v_rmca_count_vertebrates_drosera_by_number; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number (
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
    upper_count integer,
    lower_count integer,
    counter integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_count_vertebrates_drosera_by_number'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_date_mask OPTIONS (
    column_name 'code_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN code_num OPTIONS (
    column_name 'code_num'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN upper_count OPTIONS (
    column_name 'upper_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN lower_count OPTIONS (
    column_name 'lower_count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number ALTER COLUMN counter OPTIONS (
    column_name 'counter'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number OWNER TO darwin2;

--
-- TOC entry 410 (class 1259 OID 13525615)
-- Name: v_rmca_export_staging_info; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_export_staging_info (
    code character varying,
    taxon_name character varying,
    status public.hstore,
    import_ref integer,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_export_staging_info'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_export_staging_info OWNER TO darwin2;

--
-- TOC entry 411 (class 1259 OID 13525618)
-- Name: v_rmca_get_genus_by_families; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families (
    parent_id integer,
    family character varying,
    parent_level_ref integer,
    parent_level_name character varying,
    child_id integer,
    family_or_genus character varying,
    child_level_ref integer,
    child_level_name character varying,
    diff integer,
    child_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_get_genus_by_families'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN parent_level_ref OPTIONS (
    column_name 'parent_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN parent_level_name OPTIONS (
    column_name 'parent_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN family_or_genus OPTIONS (
    column_name 'family_or_genus'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN child_level_ref OPTIONS (
    column_name 'child_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN child_level_name OPTIONS (
    column_name 'child_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN diff OPTIONS (
    column_name 'diff'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_get_genus_by_families OWNER TO darwin2;

--
-- TOC entry 412 (class 1259 OID 13525621)
-- Name: v_rmca_get_higher_by_lower; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower (
    parent_id integer,
    higher_name character varying,
    parent_level_ref integer,
    parent_level_name character varying,
    child_id integer,
    lower_name character varying,
    child_level_ref integer,
    child_level_name character varying,
    diff integer,
    parent_path text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_get_higher_by_lower'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN higher_name OPTIONS (
    column_name 'higher_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN parent_level_ref OPTIONS (
    column_name 'parent_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN parent_level_name OPTIONS (
    column_name 'parent_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN lower_name OPTIONS (
    column_name 'lower_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN child_level_ref OPTIONS (
    column_name 'child_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN child_level_name OPTIONS (
    column_name 'child_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN diff OPTIONS (
    column_name 'diff'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower ALTER COLUMN parent_path OPTIONS (
    column_name 'parent_path'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_get_higher_by_lower OWNER TO darwin2;

--
-- TOC entry 413 (class 1259 OID 13525624)
-- Name: v_rmca_get_lower_by_higher; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher (
    parent_id integer,
    higher_name character varying,
    parent_level_ref integer,
    parent_level_name character varying,
    child_id integer,
    lower_name character varying,
    child_level_ref integer,
    child_level_name character varying,
    diff integer,
    child_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_get_lower_by_higher'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN higher_name OPTIONS (
    column_name 'higher_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN parent_level_ref OPTIONS (
    column_name 'parent_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN parent_level_name OPTIONS (
    column_name 'parent_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN lower_name OPTIONS (
    column_name 'lower_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN child_level_ref OPTIONS (
    column_name 'child_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN child_level_name OPTIONS (
    column_name 'child_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN diff OPTIONS (
    column_name 'diff'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_get_lower_by_higher OWNER TO darwin2;

--
-- TOC entry 414 (class 1259 OID 13525627)
-- Name: v_rmca_gtu_tags_administraive_and_ecology; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology (
    gtu_ref integer,
    administraive_tags character varying[],
    non_administrative_tags character varying[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_gtu_tags_administraive_and_ecology'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology ALTER COLUMN administraive_tags OPTIONS (
    column_name 'administraive_tags'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology ALTER COLUMN non_administrative_tags OPTIONS (
    column_name 'non_administrative_tags'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology OWNER TO darwin2;

--
-- TOC entry 415 (class 1259 OID 13525630)
-- Name: v_rmca_higher_than_familiy_in_collection; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_higher_than_familiy_in_collection (
    count bigint,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_higher_than_familiy_in_collection'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_higher_than_familiy_in_collection ALTER COLUMN count OPTIONS (
    column_name 'count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_higher_than_familiy_in_collection ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_higher_than_familiy_in_collection OWNER TO darwin2;

--
-- TOC entry 416 (class 1259 OID 13525633)
-- Name: v_rmca_ig_to_people; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_ig_to_people (
    ig_ref integer,
    ig_num character varying,
    people_ref integer,
    role text,
    collection_ref integer,
    collection_name character varying,
    formated_name character varying,
    formated_name_indexed character varying,
    formated_name_unique character varying,
    title character varying,
    family_name character varying,
    given_name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_ig_to_people'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN role OPTIONS (
    column_name 'role'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN formated_name_indexed OPTIONS (
    column_name 'formated_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN formated_name_unique OPTIONS (
    column_name 'formated_name_unique'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN family_name OPTIONS (
    column_name 'family_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people ALTER COLUMN given_name OPTIONS (
    column_name 'given_name'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people OWNER TO darwin2;

--
-- TOC entry 417 (class 1259 OID 13525636)
-- Name: v_rmca_ig_to_people_bics_report_2020; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 (
    ig_ref integer,
    ig_num character varying,
    people_ref integer,
    role text,
    collection_ref integer,
    collection_name character varying,
    formated_name character varying,
    formated_name_indexed character varying,
    formated_name_unique character varying,
    title character varying,
    family_name character varying,
    given_name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_ig_to_people_bics_report_2020'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN people_ref OPTIONS (
    column_name 'people_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN role OPTIONS (
    column_name 'role'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN formated_name_indexed OPTIONS (
    column_name 'formated_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN formated_name_unique OPTIONS (
    column_name 'formated_name_unique'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN title OPTIONS (
    column_name 'title'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN family_name OPTIONS (
    column_name 'family_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 ALTER COLUMN given_name OPTIONS (
    column_name 'given_name'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 OWNER TO darwin2;

--
-- TOC entry 418 (class 1259 OID 13525639)
-- Name: v_rmca_ig_to_people_bics_report_2020_specimens; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens (
    ig_num character varying,
    ig_ref integer,
    count bigint,
    phyisical bigint,
    people character varying[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_ig_to_people_bics_report_2020_specimens'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens ALTER COLUMN count OPTIONS (
    column_name 'count'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens ALTER COLUMN phyisical OPTIONS (
    column_name 'phyisical'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens ALTER COLUMN people OPTIONS (
    column_name 'people'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens OWNER TO darwin2;

--
-- TOC entry 419 (class 1259 OID 13525642)
-- Name: v_rmca_path_parent_children; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_path_parent_children (
    parent_id integer,
    parent_path character varying,
    child_id integer,
    child_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_path_parent_children'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children ALTER COLUMN parent_path OPTIONS (
    column_name 'parent_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children OWNER TO darwin2;

--
-- TOC entry 420 (class 1259 OID 13525645)
-- Name: v_rmca_path_parent_children_extended_taxonomy; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy (
    parent_id integer,
    parent_path character varying,
    parent_name character varying,
    parent_level character varying,
    parent_level_order integer,
    child_id integer,
    child_path character varying,
    child_name character varying,
    level_ref integer,
    child_level character varying,
    child_level_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_path_parent_children_extended_taxonomy'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN parent_path OPTIONS (
    column_name 'parent_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN parent_name OPTIONS (
    column_name 'parent_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN parent_level OPTIONS (
    column_name 'parent_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN parent_level_order OPTIONS (
    column_name 'parent_level_order'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN child_name OPTIONS (
    column_name 'child_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN child_level OPTIONS (
    column_name 'child_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy ALTER COLUMN child_level_order OPTIONS (
    column_name 'child_level_order'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy OWNER TO darwin2;

--
-- TOC entry 421 (class 1259 OID 13525648)
-- Name: v_rmca_path_parent_children_extended_taxonomy_alpha; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha (
    parent_id integer,
    parent_path character varying,
    parent_alpha_path character varying,
    parent_name character varying,
    parent_level character varying,
    parent_level_order integer,
    child_id integer,
    child_path character varying,
    child_name character varying,
    level_ref integer,
    child_level character varying,
    child_level_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_path_parent_children_extended_taxonomy_alpha'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_path OPTIONS (
    column_name 'parent_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_alpha_path OPTIONS (
    column_name 'parent_alpha_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_name OPTIONS (
    column_name 'parent_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_level OPTIONS (
    column_name 'parent_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN parent_level_order OPTIONS (
    column_name 'parent_level_order'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN child_name OPTIONS (
    column_name 'child_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN child_level OPTIONS (
    column_name 'child_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha ALTER COLUMN child_level_order OPTIONS (
    column_name 'child_level_order'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha OWNER TO darwin2;

--
-- TOC entry 422 (class 1259 OID 13525651)
-- Name: v_rmca_path_parent_children_extended_taxonomy_alpha_count_child; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child (
    parent_id integer,
    parent_path character varying,
    parent_alpha_path character varying,
    parent_name character varying,
    parent_level character varying,
    parent_level_order integer,
    child_id integer,
    child_path character varying,
    child_name character varying,
    level_ref integer,
    child_level character varying,
    child_level_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_path_parent_children_extended_taxonomy_alpha_count_child'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_id OPTIONS (
    column_name 'parent_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_path OPTIONS (
    column_name 'parent_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_alpha_path OPTIONS (
    column_name 'parent_alpha_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_name OPTIONS (
    column_name 'parent_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_level OPTIONS (
    column_name 'parent_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN parent_level_order OPTIONS (
    column_name 'parent_level_order'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN child_id OPTIONS (
    column_name 'child_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN child_path OPTIONS (
    column_name 'child_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN child_name OPTIONS (
    column_name 'child_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN child_level OPTIONS (
    column_name 'child_level'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child ALTER COLUMN child_level_order OPTIONS (
    column_name 'child_level_order'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child OWNER TO darwin2;

--
-- TOC entry 423 (class 1259 OID 13525654)
-- Name: v_rmca_preferences_with_usernames; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames (
    id integer,
    user_ref integer,
    formated_name character varying,
    pref_key character varying,
    pref_value character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_preferences_with_usernames'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames ALTER COLUMN user_ref OPTIONS (
    column_name 'user_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames ALTER COLUMN formated_name OPTIONS (
    column_name 'formated_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames ALTER COLUMN pref_key OPTIONS (
    column_name 'pref_key'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames ALTER COLUMN pref_value OPTIONS (
    column_name 'pref_value'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_preferences_with_usernames OWNER TO darwin2;

--
-- TOC entry 424 (class 1259 OID 13525657)
-- Name: v_rmca_report_ig_ichtyo_1_main; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main (
    id integer,
    ig_num character varying,
    date_donation text,
    donateur character varying,
    collectors_array character varying[],
    sum bigint,
    collection_name character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_report_ig_ichtyo_1_main'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN date_donation OPTIONS (
    column_name 'date_donation'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN donateur OPTIONS (
    column_name 'donateur'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN collectors_array OPTIONS (
    column_name 'collectors_array'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN sum OPTIONS (
    column_name 'sum'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main OWNER TO darwin2;

--
-- TOC entry 425 (class 1259 OID 13525660)
-- Name: v_rmca_report_ig_ichtyo_2_localities; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities (
    id integer,
    country character varying,
    id_gtu integer,
    locality text,
    coordinates_text text,
    date_min text,
    date_max text,
    collections_numbers text,
    sum bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_report_ig_ichtyo_2_localities'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN country OPTIONS (
    column_name 'country'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN id_gtu OPTIONS (
    column_name 'id_gtu'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN locality OPTIONS (
    column_name 'locality'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN coordinates_text OPTIONS (
    column_name 'coordinates_text'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN date_min OPTIONS (
    column_name 'date_min'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN date_max OPTIONS (
    column_name 'date_max'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN collections_numbers OPTIONS (
    column_name 'collections_numbers'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities ALTER COLUMN sum OPTIONS (
    column_name 'sum'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities OWNER TO darwin2;

--
-- TOC entry 426 (class 1259 OID 13525663)
-- Name: v_rmca_report_ig_ichtyo_3_taxo; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo (
    id integer,
    taxon_name character varying,
    parts text,
    counter bigint,
    codes text,
    valid_label boolean
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_report_ig_ichtyo_3_taxo'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN parts OPTIONS (
    column_name 'parts'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN counter OPTIONS (
    column_name 'counter'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN codes OPTIONS (
    column_name 'codes'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo ALTER COLUMN valid_label OPTIONS (
    column_name 'valid_label'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo OWNER TO darwin2;

--
-- TOC entry 427 (class 1259 OID 13525666)
-- Name: v_rmca_split_path; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_split_path (
    child_name_id integer,
    path character varying,
    regexp_matches text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_split_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path ALTER COLUMN child_name_id OPTIONS (
    column_name 'child_name_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path ALTER COLUMN regexp_matches OPTIONS (
    column_name 'regexp_matches'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_split_path OWNER TO darwin2;

--
-- TOC entry 428 (class 1259 OID 13525669)
-- Name: v_rmca_split_path_extended; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_split_path_extended (
    child_name_id integer,
    name character varying,
    id integer,
    path character varying,
    level_ref integer,
    level_name character varying,
    level_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_split_path_extended'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN child_name_id OPTIONS (
    column_name 'child_name_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended ALTER COLUMN level_order OPTIONS (
    column_name 'level_order'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended OWNER TO darwin2;

--
-- TOC entry 429 (class 1259 OID 13525672)
-- Name: v_rmca_split_path_extended_alpha_path; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path (
    child_name_id integer,
    name character varying,
    id integer,
    path character varying,
    level_ref integer,
    level_name character varying,
    level_order integer,
    full_path text,
    alpha_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_split_path_extended_alpha_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN child_name_id OPTIONS (
    column_name 'child_name_id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN level_order OPTIONS (
    column_name 'level_order'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path ALTER COLUMN alpha_path OPTIONS (
    column_name 'alpha_path'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_split_path_extended_alpha_path OWNER TO darwin2;

--
-- TOC entry 430 (class 1259 OID 13525675)
-- Name: v_rmca_taxo_detect_duplicate_hierarchies; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies (
    level_ref integer,
    level_name character varying,
    canonical_name character varying,
    nb_canonical_homonyms integer,
    names_list character varying[],
    ids integer[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_rmca_taxo_detect_duplicate_hierarchies'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN canonical_name OPTIONS (
    column_name 'canonical_name'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN nb_canonical_homonyms OPTIONS (
    column_name 'nb_canonical_homonyms'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN names_list OPTIONS (
    column_name 'names_list'
);
ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies ALTER COLUMN ids OPTIONS (
    column_name 'ids'
);


ALTER FOREIGN TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies OWNER TO darwin2;

--
-- TOC entry 431 (class 1259 OID 13525678)
-- Name: v_sophie_gryseels_2022; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 (
    taxo_group character varying,
    family character varying,
    container_type text,
    type character varying,
    part text,
    string_agg text,
    date_min timestamp without time zone,
    date_max text,
    records bigint,
    specimens bigint
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_sophie_gryseels_2022'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN taxo_group OPTIONS (
    column_name 'taxo_group'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN part OPTIONS (
    column_name 'part'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN string_agg OPTIONS (
    column_name 'string_agg'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN date_min OPTIONS (
    column_name 'date_min'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN date_max OPTIONS (
    column_name 'date_max'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN records OPTIONS (
    column_name 'records'
);
ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 ALTER COLUMN specimens OPTIONS (
    column_name 'specimens'
);


ALTER FOREIGN TABLE fdw_113.v_sophie_gryseels_2022 OWNER TO darwin2;

--
-- TOC entry 432 (class 1259 OID 13525681)
-- Name: v_specimens_isolate_taxa_in_path; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path (
    id integer,
    taxon_ref integer,
    path_elem text,
    taxon_path character varying,
    collection_ref integer,
    collection_path character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_specimens_isolate_taxa_in_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN path_elem OPTIONS (
    column_name 'path_elem'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);


ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path OWNER TO darwin2;

--
-- TOC entry 433 (class 1259 OID 13525684)
-- Name: v_specimens_isolate_taxa_in_path_with_metadata_ref; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref (
    id integer,
    taxon_ref integer,
    path_elem text,
    taxon_path character varying,
    collection_ref integer,
    collection_path character varying,
    taxonomy_metadata_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_specimens_isolate_taxa_in_path_with_metadata_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN path_elem OPTIONS (
    column_name 'path_elem'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref ALTER COLUMN taxonomy_metadata_ref OPTIONS (
    column_name 'taxonomy_metadata_ref'
);


ALTER FOREIGN TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref OWNER TO darwin2;

--
-- TOC entry 434 (class 1259 OID 13525687)
-- Name: v_specimens_mids; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_specimens_mids (
    id integer,
    main_code text,
    category character varying,
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
    specimen_part character varying,
    complete boolean,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    shelf character varying,
    container character varying,
    sub_container character varying,
    container_type character varying,
    sub_container_type character varying,
    container_storage character varying,
    sub_container_storage character varying,
    surnumerary boolean,
    specimen_status character varying,
    specimen_count_min integer,
    specimen_count_max integer,
    object_name text,
    object_name_indexed text,
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
    taxon_name character varying,
    taxon_name_indexed character varying,
    taxon_level_ref integer,
    taxon_level_name character varying,
    taxon_status character varying,
    taxon_path character varying,
    taxon_parent_ref integer,
    taxon_extinct boolean,
    litho_name character varying,
    family character varying,
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
    col character varying,
    gtu_location point,
    specimen_creation_date timestamp without time zone,
    import_ref integer,
    main_code_indexed character varying,
    specimen_count_males_min integer,
    specimen_count_males_max integer,
    specimen_count_females_min integer,
    specimen_count_females_max integer,
    specimen_count_juveniles_min integer,
    specimen_count_juveniles_max integer,
    nagoya character varying,
    uuid uuid,
    mids_level integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_specimens_mids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN main_code OPTIONS (
    column_name 'main_code'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN category OPTIONS (
    column_name 'category'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN expedition_ref OPTIONS (
    column_name 'expedition_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_ref OPTIONS (
    column_name 'gtu_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_ref OPTIONS (
    column_name 'taxon_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_ref OPTIONS (
    column_name 'litho_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_ref OPTIONS (
    column_name 'chrono_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_ref OPTIONS (
    column_name 'lithology_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_ref OPTIONS (
    column_name 'mineral_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN acquisition_category OPTIONS (
    column_name 'acquisition_category'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN acquisition_date_mask OPTIONS (
    column_name 'acquisition_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN acquisition_date OPTIONS (
    column_name 'acquisition_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN station_visible OPTIONS (
    column_name 'station_visible'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN ig_ref OPTIONS (
    column_name 'ig_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN type_group OPTIONS (
    column_name 'type_group'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN type_search OPTIONS (
    column_name 'type_search'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN sex OPTIONS (
    column_name 'sex'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN stage OPTIONS (
    column_name 'stage'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN state OPTIONS (
    column_name 'state'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN social_status OPTIONS (
    column_name 'social_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN rock_form OPTIONS (
    column_name 'rock_form'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_part OPTIONS (
    column_name 'specimen_part'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN complete OPTIONS (
    column_name 'complete'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN institution_ref OPTIONS (
    column_name 'institution_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN building OPTIONS (
    column_name 'building'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN floor OPTIONS (
    column_name 'floor'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN room OPTIONS (
    column_name 'room'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN "row" OPTIONS (
    column_name 'row'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN shelf OPTIONS (
    column_name 'shelf'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN container OPTIONS (
    column_name 'container'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN sub_container OPTIONS (
    column_name 'sub_container'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN sub_container_type OPTIONS (
    column_name 'sub_container_type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN sub_container_storage OPTIONS (
    column_name 'sub_container_storage'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN surnumerary OPTIONS (
    column_name 'surnumerary'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_status OPTIONS (
    column_name 'specimen_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_min OPTIONS (
    column_name 'specimen_count_min'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_max OPTIONS (
    column_name 'specimen_count_max'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN object_name OPTIONS (
    column_name 'object_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN object_name_indexed OPTIONS (
    column_name 'object_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN spec_ident_ids OPTIONS (
    column_name 'spec_ident_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN spec_coll_ids OPTIONS (
    column_name 'spec_coll_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN spec_don_sel_ids OPTIONS (
    column_name 'spec_don_sel_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_type OPTIONS (
    column_name 'collection_type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_code OPTIONS (
    column_name 'collection_code'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_is_public OPTIONS (
    column_name 'collection_is_public'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_parent_ref OPTIONS (
    column_name 'collection_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN collection_path OPTIONS (
    column_name 'collection_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN expedition_name OPTIONS (
    column_name 'expedition_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN expedition_name_indexed OPTIONS (
    column_name 'expedition_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_code OPTIONS (
    column_name 'gtu_code'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_from_date_mask OPTIONS (
    column_name 'gtu_from_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_from_date OPTIONS (
    column_name 'gtu_from_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_to_date_mask OPTIONS (
    column_name 'gtu_to_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_to_date OPTIONS (
    column_name 'gtu_to_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_tag_values_indexed OPTIONS (
    column_name 'gtu_tag_values_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_country_tag_indexed OPTIONS (
    column_name 'gtu_country_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_province_tag_indexed OPTIONS (
    column_name 'gtu_province_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_others_tag_indexed OPTIONS (
    column_name 'gtu_others_tag_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_elevation OPTIONS (
    column_name 'gtu_elevation'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_elevation_accuracy OPTIONS (
    column_name 'gtu_elevation_accuracy'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_name_indexed OPTIONS (
    column_name 'taxon_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_level_ref OPTIONS (
    column_name 'taxon_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_level_name OPTIONS (
    column_name 'taxon_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_status OPTIONS (
    column_name 'taxon_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_path OPTIONS (
    column_name 'taxon_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_parent_ref OPTIONS (
    column_name 'taxon_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN taxon_extinct OPTIONS (
    column_name 'taxon_extinct'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_name OPTIONS (
    column_name 'litho_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_name_indexed OPTIONS (
    column_name 'litho_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_level_ref OPTIONS (
    column_name 'litho_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_level_name OPTIONS (
    column_name 'litho_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_status OPTIONS (
    column_name 'litho_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_local OPTIONS (
    column_name 'litho_local'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_color OPTIONS (
    column_name 'litho_color'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_path OPTIONS (
    column_name 'litho_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN litho_parent_ref OPTIONS (
    column_name 'litho_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_name OPTIONS (
    column_name 'chrono_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_name_indexed OPTIONS (
    column_name 'chrono_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_level_ref OPTIONS (
    column_name 'chrono_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_level_name OPTIONS (
    column_name 'chrono_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_status OPTIONS (
    column_name 'chrono_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_local OPTIONS (
    column_name 'chrono_local'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_color OPTIONS (
    column_name 'chrono_color'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_path OPTIONS (
    column_name 'chrono_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN chrono_parent_ref OPTIONS (
    column_name 'chrono_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_name OPTIONS (
    column_name 'lithology_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_name_indexed OPTIONS (
    column_name 'lithology_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_level_ref OPTIONS (
    column_name 'lithology_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_level_name OPTIONS (
    column_name 'lithology_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_status OPTIONS (
    column_name 'lithology_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_local OPTIONS (
    column_name 'lithology_local'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_color OPTIONS (
    column_name 'lithology_color'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_path OPTIONS (
    column_name 'lithology_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN lithology_parent_ref OPTIONS (
    column_name 'lithology_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_name OPTIONS (
    column_name 'mineral_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_name_indexed OPTIONS (
    column_name 'mineral_name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_level_ref OPTIONS (
    column_name 'mineral_level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_level_name OPTIONS (
    column_name 'mineral_level_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_status OPTIONS (
    column_name 'mineral_status'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_local OPTIONS (
    column_name 'mineral_local'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_color OPTIONS (
    column_name 'mineral_color'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_path OPTIONS (
    column_name 'mineral_path'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mineral_parent_ref OPTIONS (
    column_name 'mineral_parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN ig_num_indexed OPTIONS (
    column_name 'ig_num_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN ig_date_mask OPTIONS (
    column_name 'ig_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN ig_date OPTIONS (
    column_name 'ig_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN col OPTIONS (
    column_name 'col'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN gtu_location OPTIONS (
    column_name 'gtu_location'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN main_code_indexed OPTIONS (
    column_name 'main_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_males_min OPTIONS (
    column_name 'specimen_count_males_min'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_males_max OPTIONS (
    column_name 'specimen_count_males_max'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_females_min OPTIONS (
    column_name 'specimen_count_females_min'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_females_max OPTIONS (
    column_name 'specimen_count_females_max'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_juveniles_min OPTIONS (
    column_name 'specimen_count_juveniles_min'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN specimen_count_juveniles_max OPTIONS (
    column_name 'specimen_count_juveniles_max'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN nagoya OPTIONS (
    column_name 'nagoya'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN uuid OPTIONS (
    column_name 'uuid'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);


ALTER FOREIGN TABLE fdw_113.v_specimens_mids OWNER TO darwin2;

--
-- TOC entry 435 (class 1259 OID 13525690)
-- Name: v_specimens_mids_simplified; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_specimens_mids_simplified (
    collection_name character varying,
    id integer,
    type character varying,
    family character varying,
    gtu_country_tag_value character varying,
    gtu_province_tag_value character varying,
    gtu_others_tag_value character varying,
    container_type character varying,
    container_storage character varying,
    mids_level integer,
    collection_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_specimens_mids_simplified'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN type OPTIONS (
    column_name 'type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN gtu_country_tag_value OPTIONS (
    column_name 'gtu_country_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN gtu_province_tag_value OPTIONS (
    column_name 'gtu_province_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN gtu_others_tag_value OPTIONS (
    column_name 'gtu_others_tag_value'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN container_type OPTIONS (
    column_name 'container_type'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN container_storage OPTIONS (
    column_name 'container_storage'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN mids_level OPTIONS (
    column_name 'mids_level'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);


ALTER FOREIGN TABLE fdw_113.v_specimens_mids_simplified OWNER TO darwin2;

--
-- TOC entry 436 (class 1259 OID 13525693)
-- Name: v_specimens_people_full_text; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_specimens_people_full_text (
    id integer,
    collection_ref integer,
    collection_name character varying,
    ig_num character varying,
    specimen_creation_date timestamp without time zone,
    spec_ident_ids integer[],
    identifiers character varying[],
    spec_coll_ids integer[],
    collectors character varying[],
    spec_don_sel_ids integer[],
    donators character varying[]
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_specimens_people_full_text'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN collection_name OPTIONS (
    column_name 'collection_name'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN ig_num OPTIONS (
    column_name 'ig_num'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN specimen_creation_date OPTIONS (
    column_name 'specimen_creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN spec_ident_ids OPTIONS (
    column_name 'spec_ident_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN identifiers OPTIONS (
    column_name 'identifiers'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN spec_coll_ids OPTIONS (
    column_name 'spec_coll_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN collectors OPTIONS (
    column_name 'collectors'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN spec_don_sel_ids OPTIONS (
    column_name 'spec_don_sel_ids'
);
ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text ALTER COLUMN donators OPTIONS (
    column_name 'donators'
);


ALTER FOREIGN TABLE fdw_113.v_specimens_people_full_text OWNER TO darwin2;

--
-- TOC entry 437 (class 1259 OID 13525696)
-- Name: v_staging_diagnose_rejects; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_staging_diagnose_rejects (
    code character varying,
    status public.hstore,
    id integer,
    taxon_name character varying,
    taxon_parents public.hstore,
    import_ref integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_staging_diagnose_rejects'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN taxon_name OPTIONS (
    column_name 'taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN taxon_parents OPTIONS (
    column_name 'taxon_parents'
);
ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects ALTER COLUMN import_ref OPTIONS (
    column_name 'import_ref'
);


ALTER FOREIGN TABLE fdw_113.v_staging_diagnose_rejects OWNER TO darwin2;

--
-- TOC entry 438 (class 1259 OID 13525699)
-- Name: v_t_compare_darwin_digit03_mysql; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql (
    pid integer,
    phylum character varying,
    class character varying,
    family character varying,
    genus character varying,
    species character varying,
    subspecies character varying,
    status character varying,
    number character varying,
    digitisation character varying,
    url character varying,
    sketchfab_snippet character varying,
    sketchfab_without_snippet character varying,
    contributor character varying,
    pic_path character varying,
    pic_display_order character varying,
    pic_image_file character varying,
    sp_corr text,
    ssp_corr text,
    full_sc_name text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_t_compare_darwin_digit03_mysql'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN pid OPTIONS (
    column_name 'pid'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN phylum OPTIONS (
    column_name 'phylum'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN class OPTIONS (
    column_name 'class'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN family OPTIONS (
    column_name 'family'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN genus OPTIONS (
    column_name 'genus'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN species OPTIONS (
    column_name 'species'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN subspecies OPTIONS (
    column_name 'subspecies'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN number OPTIONS (
    column_name 'number'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN digitisation OPTIONS (
    column_name 'digitisation'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN url OPTIONS (
    column_name 'url'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN sketchfab_snippet OPTIONS (
    column_name 'sketchfab_snippet'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN sketchfab_without_snippet OPTIONS (
    column_name 'sketchfab_without_snippet'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN contributor OPTIONS (
    column_name 'contributor'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN pic_path OPTIONS (
    column_name 'pic_path'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN pic_display_order OPTIONS (
    column_name 'pic_display_order'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN pic_image_file OPTIONS (
    column_name 'pic_image_file'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN sp_corr OPTIONS (
    column_name 'sp_corr'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN ssp_corr OPTIONS (
    column_name 'ssp_corr'
);
ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql ALTER COLUMN full_sc_name OPTIONS (
    column_name 'full_sc_name'
);


ALTER FOREIGN TABLE fdw_113.v_t_compare_darwin_digit03_mysql OWNER TO darwin2;

--
-- TOC entry 439 (class 1259 OID 13525702)
-- Name: v_taxonomical_statistics_callard; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard (
    collection_ref integer,
    taxon_single_ref text,
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    taxon integer,
    extinct boolean,
    level_id integer,
    level_type character varying,
    level_name character varying,
    level_sys_name character varying,
    optional_level boolean,
    level_order integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_taxonomical_statistics_callard'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN collection_ref OPTIONS (
    column_name 'collection_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN taxon_single_ref OPTIONS (
    column_name 'taxon_single_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN taxon OPTIONS (
    column_name 'taxon'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN extinct OPTIONS (
    column_name 'extinct'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_id OPTIONS (
    column_name 'level_id'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_type OPTIONS (
    column_name 'level_type'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_name OPTIONS (
    column_name 'level_name'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_sys_name OPTIONS (
    column_name 'level_sys_name'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN optional_level OPTIONS (
    column_name 'optional_level'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard ALTER COLUMN level_order OPTIONS (
    column_name 'level_order'
);


ALTER FOREIGN TABLE fdw_113.v_taxonomical_statistics_callard OWNER TO darwin2;

--
-- TOC entry 440 (class 1259 OID 13525705)
-- Name: v_taxonomy_split_author_fast; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast (
    id integer,
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    extinct boolean,
    sensitive_info_withheld boolean,
    metadata_ref integer,
    taxonomy_creation_date timestamp without time zone,
    name_no_author text,
    author text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_taxonomy_split_author_fast'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN level_ref OPTIONS (
    column_name 'level_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN status OPTIONS (
    column_name 'status'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN local_naming OPTIONS (
    column_name 'local_naming'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN color OPTIONS (
    column_name 'color'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN path OPTIONS (
    column_name 'path'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN parent_ref OPTIONS (
    column_name 'parent_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN extinct OPTIONS (
    column_name 'extinct'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN sensitive_info_withheld OPTIONS (
    column_name 'sensitive_info_withheld'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN metadata_ref OPTIONS (
    column_name 'metadata_ref'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN taxonomy_creation_date OPTIONS (
    column_name 'taxonomy_creation_date'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN name_no_author OPTIONS (
    column_name 'name_no_author'
);
ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast ALTER COLUMN author OPTIONS (
    column_name 'author'
);


ALTER FOREIGN TABLE fdw_113.v_taxonomy_split_author_fast OWNER TO darwin2;

--
-- TOC entry 441 (class 1259 OID 13525708)
-- Name: v_x_ray_drosera; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_x_ray_drosera (
    matched text,
    full_path character varying,
    file character varying,
    folder character varying,
    metadata_found character varying,
    object_desc character varying,
    file_num character varying[],
    object_num character varying[],
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
    darwin_taxon_name character varying,
    code_num_parts character varying[],
    nb_code_num_parts integer
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_x_ray_drosera'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN matched OPTIONS (
    column_name 'matched'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN file OPTIONS (
    column_name 'file'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN folder OPTIONS (
    column_name 'folder'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN metadata_found OPTIONS (
    column_name 'metadata_found'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN object_desc OPTIONS (
    column_name 'object_desc'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN file_num OPTIONS (
    column_name 'file_num'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN object_num OPTIONS (
    column_name 'object_num'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_category OPTIONS (
    column_name 'code_category'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_prefix OPTIONS (
    column_name 'code_prefix'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_prefix_separator OPTIONS (
    column_name 'code_prefix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code OPTIONS (
    column_name 'code'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_suffix OPTIONS (
    column_name 'code_suffix'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_suffix_separator OPTIONS (
    column_name 'code_suffix_separator'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN full_code_indexed OPTIONS (
    column_name 'full_code_indexed'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_date OPTIONS (
    column_name 'code_date'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_date_mask OPTIONS (
    column_name 'code_date_mask'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_num OPTIONS (
    column_name 'code_num'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_num_bigint OPTIONS (
    column_name 'code_num_bigint'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN darwin_taxon_name OPTIONS (
    column_name 'darwin_taxon_name'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN code_num_parts OPTIONS (
    column_name 'code_num_parts'
);
ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera ALTER COLUMN nb_code_num_parts OPTIONS (
    column_name 'nb_code_num_parts'
);


ALTER FOREIGN TABLE fdw_113.v_x_ray_drosera OWNER TO darwin2;

--
-- TOC entry 442 (class 1259 OID 13525711)
-- Name: v_xylarium_2022_image_link; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.v_xylarium_2022_image_link (
    unitid character varying,
    links text,
    legend text,
    file text
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'v_xylarium_2022_image_link'
);
ALTER FOREIGN TABLE fdw_113.v_xylarium_2022_image_link ALTER COLUMN unitid OPTIONS (
    column_name 'unitid'
);
ALTER FOREIGN TABLE fdw_113.v_xylarium_2022_image_link ALTER COLUMN links OPTIONS (
    column_name 'links'
);
ALTER FOREIGN TABLE fdw_113.v_xylarium_2022_image_link ALTER COLUMN legend OPTIONS (
    column_name 'legend'
);
ALTER FOREIGN TABLE fdw_113.v_xylarium_2022_image_link ALTER COLUMN file OPTIONS (
    column_name 'file'
);


ALTER FOREIGN TABLE fdw_113.v_xylarium_2022_image_link OWNER TO darwin2;

--
-- TOC entry 443 (class 1259 OID 13525714)
-- Name: vernacular_names; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.vernacular_names (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    id integer NOT NULL,
    community character varying NOT NULL,
    community_indexed character varying NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'vernacular_names'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN referenced_relation OPTIONS (
    column_name 'referenced_relation'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN record_id OPTIONS (
    column_name 'record_id'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN community OPTIONS (
    column_name 'community'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN community_indexed OPTIONS (
    column_name 'community_indexed'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN name OPTIONS (
    column_name 'name'
);
ALTER FOREIGN TABLE fdw_113.vernacular_names ALTER COLUMN name_indexed OPTIONS (
    column_name 'name_indexed'
);


ALTER FOREIGN TABLE fdw_113.vernacular_names OWNER TO darwin2;

--
-- TOC entry 444 (class 1259 OID 13525717)
-- Name: vmap0_world_boundaries; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.vmap0_world_boundaries (
    gid integer NOT NULL,
    id bigint,
    id2 double precision,
    f_code character varying(80),
    f_code_des character varying(80),
    nam character varying(80),
    na2 character varying(80),
    na2_descri character varying(80),
    na3 character varying(80),
    na3_descri character varying(80),
    tile_id bigint,
    fac_id double precision,
    id21 bigint,
    the_geom public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'vmap0_world_boundaries'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN gid OPTIONS (
    column_name 'gid'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN id2 OPTIONS (
    column_name 'id2'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN f_code OPTIONS (
    column_name 'f_code'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN f_code_des OPTIONS (
    column_name 'f_code_des'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN nam OPTIONS (
    column_name 'nam'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN na2 OPTIONS (
    column_name 'na2'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN na2_descri OPTIONS (
    column_name 'na2_descri'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN na3 OPTIONS (
    column_name 'na3'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN na3_descri OPTIONS (
    column_name 'na3_descri'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN tile_id OPTIONS (
    column_name 'tile_id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN fac_id OPTIONS (
    column_name 'fac_id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN id21 OPTIONS (
    column_name 'id21'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries ALTER COLUMN the_geom OPTIONS (
    column_name 'the_geom'
);


ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries OWNER TO darwin2;

--
-- TOC entry 445 (class 1259 OID 13525720)
-- Name: vmap0_world_boundaries_enveloppe; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe (
    gid integer,
    id bigint,
    id2 double precision,
    f_code character varying(80),
    f_code_des character varying(80),
    nam character varying(80),
    na2 character varying(80),
    na2_descri character varying(80),
    na3 character varying(80),
    na3_descri character varying(80),
    tile_id bigint,
    fac_id double precision,
    id21 bigint,
    the_geom public.geometry,
    bounding_box public.geometry
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'vmap0_world_boundaries_enveloppe'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN gid OPTIONS (
    column_name 'gid'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN id OPTIONS (
    column_name 'id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN id2 OPTIONS (
    column_name 'id2'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN f_code OPTIONS (
    column_name 'f_code'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN f_code_des OPTIONS (
    column_name 'f_code_des'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN nam OPTIONS (
    column_name 'nam'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN na2 OPTIONS (
    column_name 'na2'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN na2_descri OPTIONS (
    column_name 'na2_descri'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN na3 OPTIONS (
    column_name 'na3'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN na3_descri OPTIONS (
    column_name 'na3_descri'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN tile_id OPTIONS (
    column_name 'tile_id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN fac_id OPTIONS (
    column_name 'fac_id'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN id21 OPTIONS (
    column_name 'id21'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN the_geom OPTIONS (
    column_name 'the_geom'
);
ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe ALTER COLUMN bounding_box OPTIONS (
    column_name 'bounding_box'
);


ALTER FOREIGN TABLE fdw_113.vmap0_world_boundaries_enveloppe OWNER TO darwin2;

--
-- TOC entry 446 (class 1259 OID 13525723)
-- Name: x_ray_drosera; Type: FOREIGN TABLE; Schema: fdw_113; Owner: darwin2
--

CREATE FOREIGN TABLE fdw_113.x_ray_drosera (
    full_path character varying NOT NULL,
    file character varying,
    folder character varying,
    metadata_found character varying,
    object_desc character varying
)
SERVER fdw_113
OPTIONS (
    schema_name 'darwin2',
    table_name 'x_ray_drosera'
);
ALTER FOREIGN TABLE fdw_113.x_ray_drosera ALTER COLUMN full_path OPTIONS (
    column_name 'full_path'
);
ALTER FOREIGN TABLE fdw_113.x_ray_drosera ALTER COLUMN file OPTIONS (
    column_name 'file'
);
ALTER FOREIGN TABLE fdw_113.x_ray_drosera ALTER COLUMN folder OPTIONS (
    column_name 'folder'
);
ALTER FOREIGN TABLE fdw_113.x_ray_drosera ALTER COLUMN metadata_found OPTIONS (
    column_name 'metadata_found'
);
ALTER FOREIGN TABLE fdw_113.x_ray_drosera ALTER COLUMN object_desc OPTIONS (
    column_name 'object_desc'
);


ALTER FOREIGN TABLE fdw_113.x_ray_drosera OWNER TO darwin2;

--
-- TOC entry 466 (class 1259 OID 13592010)
-- Name: ext_links; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.ext_links (
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


ALTER TABLE public.ext_links OWNER TO postgres;

--
-- TOC entry 467 (class 1259 OID 13592016)
-- Name: flat_dict; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.flat_dict (
    id integer,
    referenced_relation character varying,
    dict_field character varying,
    dict_value character varying,
    dict_depend character varying
);


ALTER TABLE public.flat_dict OWNER TO postgres;

--
-- TOC entry 468 (class 1259 OID 13592024)
-- Name: gtu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.gtu (
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


ALTER TABLE public.gtu OWNER TO postgres;

--
-- TOC entry 5083 (class 2604 OID 26905226)
-- Name: classification_synonymies id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies ALTER COLUMN id SET DEFAULT nextval('darwin2.classification_synonymies_id_seq'::regclass);


--
-- TOC entry 5075 (class 2604 OID 15274915)
-- Name: users is_physical; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN is_physical SET DEFAULT true;


--
-- TOC entry 5076 (class 2604 OID 15274916)
-- Name: users title; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN title SET DEFAULT ''::character varying;


--
-- TOC entry 5077 (class 2604 OID 15274917)
-- Name: users birth_date_mask; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN birth_date_mask SET DEFAULT 0;


--
-- TOC entry 5078 (class 2604 OID 15274918)
-- Name: users birth_date; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN birth_date SET DEFAULT '0001-01-01'::date;


--
-- TOC entry 5079 (class 2604 OID 15274920)
-- Name: users id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users ALTER COLUMN id SET DEFAULT nextval('darwin2.users_id_seq'::regclass);


--
-- TOC entry 5070 (class 2604 OID 13525726)
-- Name: mukweze_files pk; Type: DEFAULT; Schema: eod; Owner: darwin2
--

ALTER TABLE ONLY eod.mukweze_files ALTER COLUMN pk SET DEFAULT nextval('eod.mukweze_files_pk_seq'::regclass);


--
-- TOC entry 5094 (class 2606 OID 13525728)
-- Name: country_cleaning pk_country_cleaning; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.country_cleaning
    ADD CONSTRAINT pk_country_cleaning PRIMARY KEY (original_name);


--
-- TOC entry 5171 (class 2606 OID 26905235)
-- Name: classification_synonymies pk_synonym_id; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies
    ADD CONSTRAINT pk_synonym_id PRIMARY KEY (id);


--
-- TOC entry 5166 (class 2606 OID 15380852)
-- Name: users_tracking pk_user_tracking; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users_tracking
    ADD CONSTRAINT pk_user_tracking PRIMARY KEY (id);


--
-- TOC entry 5159 (class 2606 OID 15274928)
-- Name: users pk_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users
    ADD CONSTRAINT pk_users PRIMARY KEY (id);


--
-- TOC entry 5173 (class 2606 OID 26905237)
-- Name: classification_synonymies unq_synonym; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.classification_synonymies
    ADD CONSTRAINT unq_synonym UNIQUE (referenced_relation, record_id, group_id);


--
-- TOC entry 5161 (class 2606 OID 15274930)
-- Name: users unq_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY darwin2.users
    ADD CONSTRAINT unq_users UNIQUE (is_physical, gender, formated_name_unique, birth_date, birth_date_mask);


--
-- TOC entry 5096 (class 2606 OID 13525730)
-- Name: mukweze_files mukweze_files_pkey; Type: CONSTRAINT; Schema: eod; Owner: darwin2
--

ALTER TABLE ONLY eod.mukweze_files
    ADD CONSTRAINT mukweze_files_pkey PRIMARY KEY (pk);


--
-- TOC entry 5103 (class 1259 OID 13907738)
-- Name: codes_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX codes_record_id_idx ON darwin2.codes USING btree (record_id);


--
-- TOC entry 5104 (class 1259 OID 13907739)
-- Name: codes_record_id_referenced_relation_code_category_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX codes_record_id_referenced_relation_code_category_idx ON darwin2.codes USING btree (record_id, referenced_relation, code_category);


--
-- TOC entry 5148 (class 1259 OID 13907742)
-- Name: ext_links_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX ext_links_record_id_idx ON darwin2.ext_links USING btree (record_id);


--
-- TOC entry 5149 (class 1259 OID 13907743)
-- Name: ext_links_record_id_referenced_relation_category_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX ext_links_record_id_referenced_relation_category_idx ON darwin2.ext_links USING btree (record_id, referenced_relation, category);


--
-- TOC entry 5154 (class 1259 OID 13907748)
-- Name: identifications_record_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX identifications_record_id_idx ON darwin2.identifications USING btree (record_id);


--
-- TOC entry 5155 (class 1259 OID 13907749)
-- Name: identifications_record_id_referenced_relation_notion_concer_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX identifications_record_id_referenced_relation_notion_concer_idx ON darwin2.identifications USING btree (record_id, referenced_relation, notion_concerned);


--
-- TOC entry 5167 (class 1259 OID 26905238)
-- Name: idx_classification_synonymies_grouping; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_grouping ON darwin2.classification_synonymies USING btree (group_id, is_basionym);


--
-- TOC entry 5168 (class 1259 OID 26905239)
-- Name: idx_classification_synonymies_order_by; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_order_by ON darwin2.classification_synonymies USING btree (group_name, order_by);


--
-- TOC entry 5169 (class 1259 OID 26905240)
-- Name: idx_classification_synonymies_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_classification_synonymies_referenced_record ON darwin2.classification_synonymies USING btree (referenced_relation, record_id, group_id);


--
-- TOC entry 5105 (class 1259 OID 13907740)
-- Name: idx_collections_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_collections_parent_ref ON darwin2.collections USING btree (parent_ref);


--
-- TOC entry 5107 (class 1259 OID 13907750)
-- Name: idx_darwin_flat_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_darwin_flat_gtu_code ON darwin2.specimens USING gin (gtu_code public.gin_trgm_ops);


--
-- TOC entry 5150 (class 1259 OID 13907744)
-- Name: idx_gin_gtu_tags_values; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_gtu_tags_values ON darwin2.gtu USING gin (tag_values_indexed);


--
-- TOC entry 5108 (class 1259 OID 13907751)
-- Name: idx_gin_specimens_gtu_country_tag_indexed_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_gtu_country_tag_indexed_indexed ON darwin2.specimens USING gin (gtu_country_tag_indexed);


--
-- TOC entry 5109 (class 1259 OID 13907752)
-- Name: idx_gin_specimens_gtu_tag_values_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_gtu_tag_values_indexed ON darwin2.specimens USING gin (gtu_tag_values_indexed);


--
-- TOC entry 5110 (class 1259 OID 13907753)
-- Name: idx_gin_specimens_spec_coll_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_coll_ids ON darwin2.specimens USING gin (spec_coll_ids);


--
-- TOC entry 5111 (class 1259 OID 13907754)
-- Name: idx_gin_specimens_spec_don_sel_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_don_sel_ids ON darwin2.specimens USING gin (spec_don_sel_ids);


--
-- TOC entry 5112 (class 1259 OID 13907755)
-- Name: idx_gin_specimens_spec_ident_ids; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_specimens_spec_ident_ids ON darwin2.specimens USING gin (spec_ident_ids);


--
-- TOC entry 5113 (class 1259 OID 13907756)
-- Name: idx_gin_trgm_specimens_expedition_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_expedition_name_indexed ON darwin2.specimens USING gin (expedition_name_indexed public.gin_trgm_ops);


--
-- TOC entry 5114 (class 1259 OID 13907757)
-- Name: idx_gin_trgm_specimens_ig_num; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_ig_num ON darwin2.specimens USING gin (ig_num_indexed public.gin_trgm_ops);


--
-- TOC entry 5115 (class 1259 OID 13907758)
-- Name: idx_gin_trgm_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_taxon_name_indexed ON darwin2.specimens USING gin (taxon_name_indexed public.gin_trgm_ops);


--
-- TOC entry 5116 (class 1259 OID 13907759)
-- Name: idx_gin_trgm_specimens_taxon_path; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_specimens_taxon_path ON darwin2.specimens USING gin (taxon_path public.gin_trgm_ops);


--
-- TOC entry 5097 (class 1259 OID 13907793)
-- Name: idx_gin_trgm_taxonomy_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_taxonomy_name_indexed ON darwin2.taxonomy USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 5098 (class 1259 OID 13907794)
-- Name: idx_gin_trgm_taxonomy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gin_trgm_taxonomy_naming ON darwin2.taxonomy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 5117 (class 1259 OID 13907760)
-- Name: idx_gist_specimens_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gist_specimens_gtu_location ON darwin2.specimens USING gist (gtu_location);


--
-- TOC entry 5151 (class 1259 OID 13907745)
-- Name: idx_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gtu_code ON darwin2.gtu USING btree (code);


--
-- TOC entry 5152 (class 1259 OID 13907746)
-- Name: idx_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_gtu_location ON darwin2.gtu USING gist (location);


--
-- TOC entry 5118 (class 1259 OID 13907761)
-- Name: idx_spec_family; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_spec_family ON darwin2.specimens USING btree (family);


--
-- TOC entry 5119 (class 1259 OID 13907762)
-- Name: idx_specimens_chrono_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_chrono_ref ON darwin2.specimens USING btree (chrono_ref) WHERE (chrono_ref <> 0);


--
-- TOC entry 5120 (class 1259 OID 13907763)
-- Name: idx_specimens_collection_is_public; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_collection_is_public ON darwin2.specimens USING btree (collection_is_public);


--
-- TOC entry 5121 (class 1259 OID 13907764)
-- Name: idx_specimens_collection_name; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_collection_name ON darwin2.specimens USING btree (collection_name);


--
-- TOC entry 5122 (class 1259 OID 13907765)
-- Name: idx_specimens_expedition_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_expedition_ref ON darwin2.specimens USING btree (expedition_ref) WHERE (expedition_ref <> 0);


--
-- TOC entry 5123 (class 1259 OID 13907766)
-- Name: idx_specimens_gtu_from_date; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_from_date ON darwin2.specimens USING btree (gtu_from_date);


--
-- TOC entry 5124 (class 1259 OID 13907767)
-- Name: idx_specimens_gtu_from_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_from_date_mask ON darwin2.specimens USING btree (gtu_from_date_mask);


--
-- TOC entry 5125 (class 1259 OID 13907768)
-- Name: idx_specimens_gtu_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_ref ON darwin2.specimens USING btree (gtu_ref) WHERE (gtu_ref <> 0);


--
-- TOC entry 5126 (class 1259 OID 13907769)
-- Name: idx_specimens_gtu_to_date; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_to_date ON darwin2.specimens USING btree (gtu_to_date);


--
-- TOC entry 5127 (class 1259 OID 13907770)
-- Name: idx_specimens_gtu_to_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_gtu_to_date_mask ON darwin2.specimens USING btree (gtu_to_date_mask);


--
-- TOC entry 5128 (class 1259 OID 13907771)
-- Name: idx_specimens_ig_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_ig_ref ON darwin2.specimens USING btree (ig_ref);


--
-- TOC entry 5129 (class 1259 OID 13907772)
-- Name: idx_specimens_litho_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_litho_ref ON darwin2.specimens USING btree (litho_ref) WHERE (litho_ref <> 0);


--
-- TOC entry 5130 (class 1259 OID 13907773)
-- Name: idx_specimens_lithology_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_lithology_ref ON darwin2.specimens USING btree (lithology_ref) WHERE (lithology_ref <> 0);


--
-- TOC entry 5131 (class 1259 OID 13907774)
-- Name: idx_specimens_main_code_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_main_code_indexed ON darwin2.specimens USING btree (main_code_indexed);


--
-- TOC entry 5132 (class 1259 OID 13907775)
-- Name: idx_specimens_mineral_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_mineral_ref ON darwin2.specimens USING btree (mineral_ref) WHERE (mineral_ref <> 0);


--
-- TOC entry 5133 (class 1259 OID 13907776)
-- Name: idx_specimens_rock_form; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_rock_form ON darwin2.specimens USING btree (rock_form);


--
-- TOC entry 5134 (class 1259 OID 13907777)
-- Name: idx_specimens_room; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_room ON darwin2.specimens USING btree (room) WHERE (NOT (room IS NULL));


--
-- TOC entry 5135 (class 1259 OID 13907778)
-- Name: idx_specimens_sex; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_sex ON darwin2.specimens USING btree (sex) WHERE ((sex)::text <> ALL (ARRAY[('undefined'::character varying)::text, ('unknown'::character varying)::text]));


--
-- TOC entry 5136 (class 1259 OID 13907779)
-- Name: idx_specimens_shelf; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_shelf ON darwin2.specimens USING btree (shelf) WHERE (NOT (shelf IS NULL));


--
-- TOC entry 5137 (class 1259 OID 13907780)
-- Name: idx_specimens_social_status; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_social_status ON darwin2.specimens USING btree (social_status) WHERE ((social_status)::text <> 'not applicable'::text);


--
-- TOC entry 5138 (class 1259 OID 13907781)
-- Name: idx_specimens_stage; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_stage ON darwin2.specimens USING btree (stage) WHERE ((stage)::text <> ALL (ARRAY[('undefined'::character varying)::text, ('unknown'::character varying)::text]));


--
-- TOC entry 5139 (class 1259 OID 13907782)
-- Name: idx_specimens_state; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_state ON darwin2.specimens USING btree (state) WHERE ((state)::text <> 'not applicable'::text);


--
-- TOC entry 5140 (class 1259 OID 13907783)
-- Name: idx_specimens_station_visible; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_station_visible ON darwin2.specimens USING btree (station_visible);


--
-- TOC entry 5141 (class 1259 OID 13907784)
-- Name: idx_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_taxon_name_indexed ON darwin2.specimens USING btree (taxon_name_indexed);


--
-- TOC entry 5142 (class 1259 OID 13907785)
-- Name: idx_specimens_taxon_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_taxon_ref ON darwin2.specimens USING btree (taxon_ref) WHERE (taxon_ref <> 0);


--
-- TOC entry 5143 (class 1259 OID 13907786)
-- Name: idx_specimens_type_search; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_specimens_type_search ON darwin2.specimens USING btree (type_search) WHERE ((type_search)::text <> 'specimen'::text);


--
-- TOC entry 5099 (class 1259 OID 13907795)
-- Name: idx_taxonomy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_level_ref ON darwin2.taxonomy USING btree (level_ref);


--
-- TOC entry 5100 (class 1259 OID 13907796)
-- Name: idx_taxonomy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_parent_ref ON darwin2.taxonomy USING btree (parent_ref);


--
-- TOC entry 5101 (class 1259 OID 13907797)
-- Name: idx_taxonomy_path; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_taxonomy_path ON darwin2.taxonomy USING btree (path text_pattern_ops);


--
-- TOC entry 5162 (class 1259 OID 15380920)
-- Name: idx_users_tracking_action; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_action ON darwin2.users_tracking USING btree (action);


--
-- TOC entry 5163 (class 1259 OID 15380921)
-- Name: idx_users_tracking_modification_date_time; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_modification_date_time ON darwin2.users_tracking USING btree (modification_date_time DESC);


--
-- TOC entry 5164 (class 1259 OID 15380922)
-- Name: idx_users_tracking_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX idx_users_tracking_user_ref ON darwin2.users_tracking USING btree (user_ref);


--
-- TOC entry 5106 (class 1259 OID 13907741)
-- Name: pk_collections; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_collections ON darwin2.collections USING btree (id);


--
-- TOC entry 5153 (class 1259 OID 13907747)
-- Name: pk_gtu; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_gtu ON darwin2.gtu USING btree (id);


--
-- TOC entry 5144 (class 1259 OID 13907787)
-- Name: pk_specimens; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_specimens ON darwin2.specimens USING btree (id);


--
-- TOC entry 5102 (class 1259 OID 13907798)
-- Name: pk_taxonomy; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX pk_taxonomy ON darwin2.taxonomy USING btree (id);


--
-- TOC entry 5145 (class 1259 OID 13907788)
-- Name: specimens_geom_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX specimens_geom_idx ON darwin2.specimens USING gist (geom);


--
-- TOC entry 5146 (class 1259 OID 13907789)
-- Name: specimens_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_id_idx ON darwin2.specimens USING btree (id);


--
-- TOC entry 5156 (class 1259 OID 13907791)
-- Name: specimens_stable_ids_specimen_ref_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE INDEX specimens_stable_ids_specimen_ref_idx ON darwin2.specimens_stable_ids USING btree (specimen_ref);


--
-- TOC entry 5157 (class 1259 OID 13907792)
-- Name: specimens_stable_ids_uuid_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_stable_ids_uuid_idx ON darwin2.specimens_stable_ids USING btree (uuid);


--
-- TOC entry 5147 (class 1259 OID 13907790)
-- Name: specimens_uuid_idx; Type: INDEX; Schema: darwin2; Owner: darwin2
--

CREATE UNIQUE INDEX specimens_uuid_idx ON darwin2.specimens USING btree (uuid);


--
-- TOC entry 5174 (class 2620 OID 26905220)
-- Name: template_table_record_ref trg_chk_ref_record_template_table_record_ref; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_template_table_record_ref AFTER INSERT OR UPDATE ON darwin2.template_table_record_ref FOR EACH ROW EXECUTE FUNCTION darwin2.fct_chk_referencedrecord();


--
-- TOC entry 5339 (class 0 OID 0)
-- Dependencies: 19
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE USAGE ON SCHEMA public FROM PUBLIC;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- TOC entry 5347 (class 0 OID 0)
-- Dependencies: 1355
-- Name: FUNCTION fct_rmca_flush_tables(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_flush_tables() TO darwin2;


--
-- TOC entry 5348 (class 0 OID 0)
-- Dependencies: 1350
-- Name: FUNCTION fct_rmca_refresh_materialized_view(); Type: ACL; Schema: darwin2; Owner: darwin2
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view() TO postgres;


--
-- TOC entry 5349 (class 0 OID 0)
-- Dependencies: 1348
-- Name: FUNCTION fct_rmca_refresh_materialized_view_and_consult_tables(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables() TO darwin2;


--
-- TOC entry 5350 (class 0 OID 0)
-- Dependencies: 1353
-- Name: FUNCTION fct_rmca_refresh_materialized_view_and_consult_tables_after_rep(); Type: ACL; Schema: darwin2; Owner: postgres
--

GRANT ALL ON FUNCTION darwin2.fct_rmca_refresh_materialized_view_and_consult_tables_after_rep() TO darwin2;


--
-- TOC entry 5356 (class 0 OID 0)
-- Dependencies: 495
-- Name: TABLE template_table_record_ref; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.template_table_record_ref FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE darwin2.template_table_record_ref TO darwin2;


--
-- TOC entry 5364 (class 0 OID 0)
-- Dependencies: 497
-- Name: TABLE classification_synonymies; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.classification_synonymies FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,DELETE,TRIGGER,UPDATE ON TABLE darwin2.classification_synonymies TO darwin2;


--
-- TOC entry 5366 (class 0 OID 0)
-- Dependencies: 232
-- Name: TABLE country_cleaning; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.country_cleaning FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.country_cleaning TO darwin2;


--
-- TOC entry 5367 (class 0 OID 0)
-- Dependencies: 237
-- Name: TABLE fgmv_rdf_view_2_ichtyo_taxo_mbisa; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.fgmv_rdf_view_2_ichtyo_taxo_mbisa FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.fgmv_rdf_view_2_ichtyo_taxo_mbisa TO darwin2;
GRANT ALL ON TABLE darwin2.fgmv_rdf_view_2_ichtyo_taxo_mbisa TO postgres;


--
-- TOC entry 5368 (class 0 OID 0)
-- Dependencies: 243
-- Name: TABLE v_specimen_public; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.v_specimen_public FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.v_specimen_public TO darwin2;


--
-- TOC entry 5369 (class 0 OID 0)
-- Dependencies: 244
-- Name: TABLE mv_specimen_public; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.mv_specimen_public FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.mv_specimen_public TO darwin2;


--
-- TOC entry 5370 (class 0 OID 0)
-- Dependencies: 248
-- Name: TABLE template_classifications; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.template_classifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.template_classifications TO darwin2;


--
-- TOC entry 5372 (class 0 OID 0)
-- Dependencies: 230
-- Name: TABLE collections; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.collections FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.collections TO darwin2;


--
-- TOC entry 5373 (class 0 OID 0)
-- Dependencies: 231
-- Name: TABLE v_fdw113_collections_full_path_recursive; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE darwin2.v_fdw113_collections_full_path_recursive FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE darwin2.v_fdw113_collections_full_path_recursive TO darwin2;


--
-- TOC entry 5374 (class 0 OID 0)
-- Dependencies: 228
-- Name: TABLE catalogue_levels; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.catalogue_levels FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.catalogue_levels TO darwin2;


--
-- TOC entry 5375 (class 0 OID 0)
-- Dependencies: 229
-- Name: TABLE catalogue_people; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.catalogue_people FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.catalogue_people TO darwin2;


--
-- TOC entry 5376 (class 0 OID 0)
-- Dependencies: 233
-- Name: TABLE specimens; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimens FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimens TO darwin2;


--
-- TOC entry 5377 (class 0 OID 0)
-- Dependencies: 234
-- Name: TABLE taxonomy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.taxonomy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.taxonomy TO darwin2;


--
-- TOC entry 5378 (class 0 OID 0)
-- Dependencies: 235
-- Name: TABLE codes; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.codes FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.codes TO darwin2;


--
-- TOC entry 5379 (class 0 OID 0)
-- Dependencies: 236
-- Name: TABLE ext_links; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.ext_links FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.ext_links TO darwin2;


--
-- TOC entry 5380 (class 0 OID 0)
-- Dependencies: 238
-- Name: TABLE flat_dict; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.flat_dict FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.flat_dict TO darwin2;


--
-- TOC entry 5381 (class 0 OID 0)
-- Dependencies: 239
-- Name: TABLE gtu; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.gtu FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.gtu TO darwin2;


--
-- TOC entry 5382 (class 0 OID 0)
-- Dependencies: 240
-- Name: TABLE identifications; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.identifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.identifications TO darwin2;


--
-- TOC entry 5383 (class 0 OID 0)
-- Dependencies: 245
-- Name: TABLE people; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people TO darwin2;


--
-- TOC entry 5384 (class 0 OID 0)
-- Dependencies: 246
-- Name: TABLE specimens_stable_ids; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimens_stable_ids FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimens_stable_ids TO darwin2;


--
-- TOC entry 5385 (class 0 OID 0)
-- Dependencies: 247
-- Name: TABLE tags; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tags FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tags TO darwin2;


--
-- TOC entry 5386 (class 0 OID 0)
-- Dependencies: 249
-- Name: TABLE users_tracking; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users_tracking FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users_tracking TO darwin2;


--
-- TOC entry 5388 (class 0 OID 0)
-- Dependencies: 259
-- Name: TABLE bibliography; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.bibliography FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.bibliography TO darwin2;


--
-- TOC entry 5389 (class 0 OID 0)
-- Dependencies: 260
-- Name: TABLE catalogue_bibliography; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.catalogue_bibliography FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.catalogue_bibliography TO darwin2;


--
-- TOC entry 5390 (class 0 OID 0)
-- Dependencies: 261
-- Name: TABLE catalogue_relationships; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.catalogue_relationships FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.catalogue_relationships TO darwin2;


--
-- TOC entry 5391 (class 0 OID 0)
-- Dependencies: 262
-- Name: TABLE check_dates_danny; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.check_dates_danny FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.check_dates_danny TO darwin2;


--
-- TOC entry 5392 (class 0 OID 0)
-- Dependencies: 263
-- Name: TABLE chronostratigraphy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.chronostratigraphy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.chronostratigraphy TO darwin2;


--
-- TOC entry 5393 (class 0 OID 0)
-- Dependencies: 264
-- Name: TABLE classification_keywords; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.classification_keywords FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.classification_keywords TO darwin2;


--
-- TOC entry 5394 (class 0 OID 0)
-- Dependencies: 265
-- Name: TABLE classification_synonymies; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.classification_synonymies FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.classification_synonymies TO darwin2;


--
-- TOC entry 5395 (class 0 OID 0)
-- Dependencies: 266
-- Name: TABLE classification_synonymies_history; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.classification_synonymies_history FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.classification_synonymies_history TO darwin2;


--
-- TOC entry 5396 (class 0 OID 0)
-- Dependencies: 267
-- Name: TABLE codes_tmp_duplicates; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.codes_tmp_duplicates FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.codes_tmp_duplicates TO darwin2;


--
-- TOC entry 5397 (class 0 OID 0)
-- Dependencies: 268
-- Name: TABLE collecting_methods; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.collecting_methods FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.collecting_methods TO darwin2;


--
-- TOC entry 5398 (class 0 OID 0)
-- Dependencies: 269
-- Name: TABLE collecting_tools; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.collecting_tools FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.collecting_tools TO darwin2;


--
-- TOC entry 5399 (class 0 OID 0)
-- Dependencies: 270
-- Name: TABLE collection_maintenance; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.collection_maintenance FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.collection_maintenance TO darwin2;


--
-- TOC entry 5400 (class 0 OID 0)
-- Dependencies: 271
-- Name: TABLE collections_rights; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.collections_rights FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.collections_rights TO darwin2;


--
-- TOC entry 5401 (class 0 OID 0)
-- Dependencies: 272
-- Name: TABLE comments; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.comments FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.comments TO darwin2;


--
-- TOC entry 5402 (class 0 OID 0)
-- Dependencies: 273
-- Name: TABLE db_version; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.db_version FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.db_version TO darwin2;


--
-- TOC entry 5403 (class 0 OID 0)
-- Dependencies: 274
-- Name: TABLE dissco_continents_to_countries; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.dissco_continents_to_countries FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.dissco_continents_to_countries TO darwin2;


--
-- TOC entry 5404 (class 0 OID 0)
-- Dependencies: 275
-- Name: TABLE domain_name; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.domain_name FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.domain_name TO darwin2;


--
-- TOC entry 5405 (class 0 OID 0)
-- Dependencies: 276
-- Name: TABLE expeditions; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.expeditions FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.expeditions TO darwin2;


--
-- TOC entry 5406 (class 0 OID 0)
-- Dependencies: 277
-- Name: TABLE fix_date_kin_feb2022; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.fix_date_kin_feb2022 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.fix_date_kin_feb2022 TO darwin2;


--
-- TOC entry 5407 (class 0 OID 0)
-- Dependencies: 278
-- Name: TABLE identifiers; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.identifiers FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.identifiers TO darwin2;


--
-- TOC entry 5408 (class 0 OID 0)
-- Dependencies: 279
-- Name: TABLE igs; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.igs FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.igs TO darwin2;


--
-- TOC entry 5409 (class 0 OID 0)
-- Dependencies: 280
-- Name: TABLE import_fruitfly_drybarcodes_20211006; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.import_fruitfly_drybarcodes_20211006 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.import_fruitfly_drybarcodes_20211006 TO darwin2;


--
-- TOC entry 5410 (class 0 OID 0)
-- Dependencies: 281
-- Name: TABLE imports; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.imports FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.imports TO darwin2;


--
-- TOC entry 5411 (class 0 OID 0)
-- Dependencies: 282
-- Name: TABLE informative_workflow; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.informative_workflow FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.informative_workflow TO darwin2;


--
-- TOC entry 5412 (class 0 OID 0)
-- Dependencies: 283
-- Name: TABLE insurances; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.insurances FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.insurances TO darwin2;


--
-- TOC entry 5413 (class 0 OID 0)
-- Dependencies: 284
-- Name: TABLE lithology; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.lithology FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.lithology TO darwin2;


--
-- TOC entry 5414 (class 0 OID 0)
-- Dependencies: 285
-- Name: TABLE lithostratigraphy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.lithostratigraphy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.lithostratigraphy TO darwin2;


--
-- TOC entry 5415 (class 0 OID 0)
-- Dependencies: 286
-- Name: TABLE loan_history; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.loan_history FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.loan_history TO darwin2;


--
-- TOC entry 5416 (class 0 OID 0)
-- Dependencies: 287
-- Name: TABLE loan_items; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.loan_items FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.loan_items TO darwin2;


--
-- TOC entry 5417 (class 0 OID 0)
-- Dependencies: 288
-- Name: TABLE loan_rights; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.loan_rights FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.loan_rights TO darwin2;


--
-- TOC entry 5418 (class 0 OID 0)
-- Dependencies: 289
-- Name: TABLE loan_status; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.loan_status FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.loan_status TO darwin2;


--
-- TOC entry 5419 (class 0 OID 0)
-- Dependencies: 290
-- Name: TABLE loans; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.loans FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.loans TO darwin2;


--
-- TOC entry 5420 (class 0 OID 0)
-- Dependencies: 291
-- Name: TABLE mineralogy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mineralogy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mineralogy TO darwin2;


--
-- TOC entry 5421 (class 0 OID 0)
-- Dependencies: 292
-- Name: TABLE multimedia; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.multimedia FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.multimedia TO darwin2;


--
-- TOC entry 5422 (class 0 OID 0)
-- Dependencies: 293
-- Name: TABLE multimedia_todelete; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.multimedia_todelete FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.multimedia_todelete TO darwin2;


--
-- TOC entry 5423 (class 0 OID 0)
-- Dependencies: 294
-- Name: TABLE mv_mids_stat_larissa; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_mids_stat_larissa FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_mids_stat_larissa TO darwin2;


--
-- TOC entry 5424 (class 0 OID 0)
-- Dependencies: 295
-- Name: TABLE mv_mids_stats_larissa_with_type_country; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_mids_stats_larissa_with_type_country FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_mids_stats_larissa_with_type_country TO darwin2;


--
-- TOC entry 5425 (class 0 OID 0)
-- Dependencies: 242
-- Name: TABLE mv_specimen_public; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_specimen_public FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_specimen_public TO darwin2;


--
-- TOC entry 5426 (class 0 OID 0)
-- Dependencies: 296
-- Name: TABLE mv_specimens_mids; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_specimens_mids FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_specimens_mids TO darwin2;


--
-- TOC entry 5427 (class 0 OID 0)
-- Dependencies: 297
-- Name: TABLE mv_specimens_mids_simplified; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_specimens_mids_simplified FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_specimens_mids_simplified TO darwin2;


--
-- TOC entry 5428 (class 0 OID 0)
-- Dependencies: 298
-- Name: TABLE mv_specimens_mids_simplified_coll_hierarchy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy TO darwin2;


--
-- TOC entry 5429 (class 0 OID 0)
-- Dependencies: 299
-- Name: TABLE mv_specimens_mids_simplified_coll_hierarchy_2; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.mv_specimens_mids_simplified_coll_hierarchy_2 TO darwin2;


--
-- TOC entry 5430 (class 0 OID 0)
-- Dependencies: 300
-- Name: TABLE my_saved_searches; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.my_saved_searches FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.my_saved_searches TO darwin2;


--
-- TOC entry 5431 (class 0 OID 0)
-- Dependencies: 301
-- Name: TABLE my_widgets; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.my_widgets FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.my_widgets TO darwin2;


--
-- TOC entry 5432 (class 0 OID 0)
-- Dependencies: 302
-- Name: TABLE my_widgets_rmca; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.my_widgets_rmca FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.my_widgets_rmca TO darwin2;


--
-- TOC entry 5433 (class 0 OID 0)
-- Dependencies: 303
-- Name: TABLE people_addresses; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people_addresses FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people_addresses TO darwin2;


--
-- TOC entry 5434 (class 0 OID 0)
-- Dependencies: 304
-- Name: TABLE people_align_debug; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people_align_debug FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people_align_debug TO darwin2;


--
-- TOC entry 5435 (class 0 OID 0)
-- Dependencies: 305
-- Name: TABLE people_comm; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people_comm FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people_comm TO darwin2;


--
-- TOC entry 5436 (class 0 OID 0)
-- Dependencies: 306
-- Name: TABLE people_languages; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people_languages FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people_languages TO darwin2;


--
-- TOC entry 5437 (class 0 OID 0)
-- Dependencies: 307
-- Name: TABLE people_relationships; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.people_relationships FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.people_relationships TO darwin2;


--
-- TOC entry 5438 (class 0 OID 0)
-- Dependencies: 308
-- Name: TABLE possible_upper_levels; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.possible_upper_levels FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.possible_upper_levels TO darwin2;


--
-- TOC entry 5439 (class 0 OID 0)
-- Dependencies: 309
-- Name: TABLE preferences; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.preferences FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.preferences TO darwin2;


--
-- TOC entry 5440 (class 0 OID 0)
-- Dependencies: 310
-- Name: TABLE properties; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.properties FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.properties TO darwin2;


--
-- TOC entry 5441 (class 0 OID 0)
-- Dependencies: 311
-- Name: TABLE specimen_collecting_methods; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimen_collecting_methods FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimen_collecting_methods TO darwin2;


--
-- TOC entry 5442 (class 0 OID 0)
-- Dependencies: 312
-- Name: TABLE specimen_collecting_tools; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimen_collecting_tools FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimen_collecting_tools TO darwin2;


--
-- TOC entry 5443 (class 0 OID 0)
-- Dependencies: 313
-- Name: TABLE specimens_detect_wrong_countries; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimens_detect_wrong_countries FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimens_detect_wrong_countries TO darwin2;


--
-- TOC entry 5444 (class 0 OID 0)
-- Dependencies: 314
-- Name: TABLE specimens_relationships; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimens_relationships FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimens_relationships TO darwin2;


--
-- TOC entry 5445 (class 0 OID 0)
-- Dependencies: 315
-- Name: TABLE specimens_storage_parts_view; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.specimens_storage_parts_view FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.specimens_storage_parts_view TO darwin2;


--
-- TOC entry 5446 (class 0 OID 0)
-- Dependencies: 316
-- Name: TABLE staging; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging TO darwin2;


--
-- TOC entry 5447 (class 0 OID 0)
-- Dependencies: 317
-- Name: TABLE staging_catalogue; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_catalogue FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_catalogue TO darwin2;


--
-- TOC entry 5448 (class 0 OID 0)
-- Dependencies: 318
-- Name: TABLE staging_collecting_methods; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_collecting_methods FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_collecting_methods TO darwin2;


--
-- TOC entry 5449 (class 0 OID 0)
-- Dependencies: 319
-- Name: TABLE staging_info; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_info FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_info TO darwin2;


--
-- TOC entry 5450 (class 0 OID 0)
-- Dependencies: 320
-- Name: TABLE staging_people; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_people FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_people TO darwin2;


--
-- TOC entry 5451 (class 0 OID 0)
-- Dependencies: 321
-- Name: TABLE staging_relationship; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_relationship FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_relationship TO darwin2;


--
-- TOC entry 5452 (class 0 OID 0)
-- Dependencies: 322
-- Name: TABLE staging_tag_groups; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.staging_tag_groups FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.staging_tag_groups TO darwin2;


--
-- TOC entry 5453 (class 0 OID 0)
-- Dependencies: 323
-- Name: TABLE storage_parts; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.storage_parts FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.storage_parts TO darwin2;


--
-- TOC entry 5454 (class 0 OID 0)
-- Dependencies: 324
-- Name: TABLE storage_parts_bck_20220513; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.storage_parts_bck_20220513 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.storage_parts_bck_20220513 TO darwin2;


--
-- TOC entry 5455 (class 0 OID 0)
-- Dependencies: 325
-- Name: TABLE storage_parts_fix_ichtyo; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.storage_parts_fix_ichtyo FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.storage_parts_fix_ichtyo TO darwin2;


--
-- TOC entry 5456 (class 0 OID 0)
-- Dependencies: 326
-- Name: TABLE storage_parts_ichtyo_missing20220517; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.storage_parts_ichtyo_missing20220517 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.storage_parts_ichtyo_missing20220517 TO darwin2;


--
-- TOC entry 5457 (class 0 OID 0)
-- Dependencies: 327
-- Name: TABLE t_compare_darwin_digit03_mysql; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.t_compare_darwin_digit03_mysql FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.t_compare_darwin_digit03_mysql TO darwin2;


--
-- TOC entry 5458 (class 0 OID 0)
-- Dependencies: 328
-- Name: TABLE t_darwin_ipt; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.t_darwin_ipt FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.t_darwin_ipt TO darwin2;


--
-- TOC entry 5459 (class 0 OID 0)
-- Dependencies: 329
-- Name: TABLE tag_groups; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tag_groups FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tag_groups TO darwin2;


--
-- TOC entry 5460 (class 0 OID 0)
-- Dependencies: 330
-- Name: TABLE taxonomy_metadata; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.taxonomy_metadata FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.taxonomy_metadata TO darwin2;


--
-- TOC entry 5461 (class 0 OID 0)
-- Dependencies: 331
-- Name: TABLE taxonomy_synonymy_status; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.taxonomy_synonymy_status FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.taxonomy_synonymy_status TO darwin2;


--
-- TOC entry 5462 (class 0 OID 0)
-- Dependencies: 332
-- Name: TABLE template_classifications; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.template_classifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.template_classifications TO darwin2;


--
-- TOC entry 5463 (class 0 OID 0)
-- Dependencies: 333
-- Name: TABLE template_people; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.template_people FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.template_people TO darwin2;


--
-- TOC entry 5464 (class 0 OID 0)
-- Dependencies: 334
-- Name: TABLE template_people_users_addr_common; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.template_people_users_addr_common FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.template_people_users_addr_common TO darwin2;


--
-- TOC entry 5465 (class 0 OID 0)
-- Dependencies: 335
-- Name: TABLE template_people_users_comm_common; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.template_people_users_comm_common FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.template_people_users_comm_common TO darwin2;


--
-- TOC entry 5466 (class 0 OID 0)
-- Dependencies: 336
-- Name: TABLE template_table_record_ref; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.template_table_record_ref FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.template_table_record_ref TO darwin2;


--
-- TOC entry 5467 (class 0 OID 0)
-- Dependencies: 337
-- Name: TABLE tmp_xylarium_img_links_2022; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tmp_xylarium_img_links_2022 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tmp_xylarium_img_links_2022 TO darwin2;


--
-- TOC entry 5468 (class 0 OID 0)
-- Dependencies: 338
-- Name: TABLE tv_darwin_view_for_csv; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_darwin_view_for_csv FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_darwin_view_for_csv TO darwin2;


--
-- TOC entry 5469 (class 0 OID 0)
-- Dependencies: 339
-- Name: TABLE tv_rdf_view_2_ichtyo; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_rdf_view_2_ichtyo FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_rdf_view_2_ichtyo TO darwin2;


--
-- TOC entry 5470 (class 0 OID 0)
-- Dependencies: 340
-- Name: TABLE tv_rdf_view_2_ichtyo_taxo; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_rdf_view_2_ichtyo_taxo TO darwin2;


--
-- TOC entry 5471 (class 0 OID 0)
-- Dependencies: 341
-- Name: TABLE tv_reporting_count_all_specimens_by_collection_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_count_all_specimens_by_collection_year_ig TO darwin2;


--
-- TOC entry 5472 (class 0 OID 0)
-- Dependencies: 342
-- Name: TABLE tv_reporting_count_all_specimens_type_by_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_count_all_specimens_type_by_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5473 (class 0 OID 0)
-- Dependencies: 343
-- Name: TABLE tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5474 (class 0 OID 0)
-- Dependencies: 344
-- Name: TABLE tv_reporting_higher_taxa_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_higher_taxa_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5475 (class 0 OID 0)
-- Dependencies: 345
-- Name: TABLE tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5476 (class 0 OID 0)
-- Dependencies: 346
-- Name: TABLE tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.tv_reporting_taxa_in_specimen_per_rank_collection_ref_year_igal TO darwin2;


--
-- TOC entry 5477 (class 0 OID 0)
-- Dependencies: 347
-- Name: TABLE users; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users TO darwin2;


--
-- TOC entry 5478 (class 0 OID 0)
-- Dependencies: 348
-- Name: TABLE users_addresses; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users_addresses FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users_addresses TO darwin2;


--
-- TOC entry 5479 (class 0 OID 0)
-- Dependencies: 349
-- Name: TABLE users_comm; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users_comm FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users_comm TO darwin2;


--
-- TOC entry 5480 (class 0 OID 0)
-- Dependencies: 350
-- Name: TABLE users_login_infos; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users_login_infos FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users_login_infos TO darwin2;


--
-- TOC entry 5481 (class 0 OID 0)
-- Dependencies: 351
-- Name: TABLE users_tracking_proppb; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.users_tracking_proppb FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.users_tracking_proppb TO darwin2;


--
-- TOC entry 5482 (class 0 OID 0)
-- Dependencies: 352
-- Name: TABLE v_collection_statistics; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_collection_statistics FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_collection_statistics TO darwin2;


--
-- TOC entry 5483 (class 0 OID 0)
-- Dependencies: 241
-- Name: TABLE v_collections_full_path_recursive; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_collections_full_path_recursive FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_collections_full_path_recursive TO darwin2;


--
-- TOC entry 5484 (class 0 OID 0)
-- Dependencies: 353
-- Name: TABLE v_comment_loans; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_comment_loans FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_comment_loans TO darwin2;


--
-- TOC entry 5485 (class 0 OID 0)
-- Dependencies: 354
-- Name: TABLE v_control_identifications; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_control_identifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_control_identifications TO darwin2;


--
-- TOC entry 5486 (class 0 OID 0)
-- Dependencies: 355
-- Name: TABLE v_control_identifications_several_true; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_control_identifications_several_true FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_control_identifications_several_true TO darwin2;


--
-- TOC entry 5487 (class 0 OID 0)
-- Dependencies: 356
-- Name: TABLE v_count_by_families_genus_species; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_count_by_families_genus_species FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_count_by_families_genus_species TO darwin2;


--
-- TOC entry 5488 (class 0 OID 0)
-- Dependencies: 357
-- Name: TABLE v_count_by_families_genus_species_subspecies; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_count_by_families_genus_species_subspecies FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_count_by_families_genus_species_subspecies TO darwin2;


--
-- TOC entry 5489 (class 0 OID 0)
-- Dependencies: 358
-- Name: TABLE v_danny_2020_check_collection_date; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_danny_2020_check_collection_date FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_danny_2020_check_collection_date TO darwin2;


--
-- TOC entry 5490 (class 0 OID 0)
-- Dependencies: 359
-- Name: TABLE v_darwin_ichtyo_history_of_reidentifications; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_ichtyo_history_of_reidentifications TO darwin2;


--
-- TOC entry 5491 (class 0 OID 0)
-- Dependencies: 360
-- Name: TABLE v_darwin_ipt; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_ipt FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_ipt TO darwin2;


--
-- TOC entry 5492 (class 0 OID 0)
-- Dependencies: 361
-- Name: TABLE v_darwin_ipt_taxonomy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_ipt_taxonomy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_ipt_taxonomy TO darwin2;


--
-- TOC entry 5493 (class 0 OID 0)
-- Dependencies: 362
-- Name: TABLE v_darwin_ipt_taxonomy_vernacular; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_ipt_taxonomy_vernacular TO darwin2;


--
-- TOC entry 5494 (class 0 OID 0)
-- Dependencies: 363
-- Name: TABLE v_darwin_public; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_public FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_public TO darwin2;


--
-- TOC entry 5495 (class 0 OID 0)
-- Dependencies: 364
-- Name: TABLE v_darwin_public_gis; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_public_gis FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_public_gis TO darwin2;


--
-- TOC entry 5496 (class 0 OID 0)
-- Dependencies: 365
-- Name: TABLE v_darwin_view_for_csv; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_darwin_view_for_csv FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_darwin_view_for_csv TO darwin2;


--
-- TOC entry 5497 (class 0 OID 0)
-- Dependencies: 366
-- Name: TABLE v_detect_duplicates; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_detect_duplicates FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_detect_duplicates TO darwin2;


--
-- TOC entry 5498 (class 0 OID 0)
-- Dependencies: 367
-- Name: TABLE v_diagnose_country_from_coord; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_diagnose_country_from_coord FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_diagnose_country_from_coord TO darwin2;


--
-- TOC entry 5499 (class 0 OID 0)
-- Dependencies: 368
-- Name: TABLE v_diagnose_fast_country_outlier; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_diagnose_fast_country_outlier FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_diagnose_fast_country_outlier TO darwin2;


--
-- TOC entry 5500 (class 0 OID 0)
-- Dependencies: 369
-- Name: TABLE v_diagnose_fast_country_outlier_tmp; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_diagnose_fast_country_outlier_tmp FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_diagnose_fast_country_outlier_tmp TO darwin2;


--
-- TOC entry 5501 (class 0 OID 0)
-- Dependencies: 370
-- Name: TABLE v_elephants_ivory_emmanuel; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_elephants_ivory_emmanuel FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_elephants_ivory_emmanuel TO darwin2;


--
-- TOC entry 5502 (class 0 OID 0)
-- Dependencies: 371
-- Name: TABLE v_erik_verheyen_mammals_in_alcohol_2020; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020 TO darwin2;


--
-- TOC entry 5503 (class 0 OID 0)
-- Dependencies: 372
-- Name: TABLE v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_detail_rodent_chiro TO darwin2;


--
-- TOC entry 5504 (class 0 OID 0)
-- Dependencies: 373
-- Name: TABLE v_erik_verheyen_mammals_in_alcohol_2020_generic; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_erik_verheyen_mammals_in_alcohol_2020_generic TO darwin2;


--
-- TOC entry 5505 (class 0 OID 0)
-- Dependencies: 374
-- Name: TABLE v_fix_property_shift; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_fix_property_shift FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_fix_property_shift TO darwin2;


--
-- TOC entry 5506 (class 0 OID 0)
-- Dependencies: 375
-- Name: TABLE v_gbif_snails_kin_tine; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_gbif_snails_kin_tine FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_gbif_snails_kin_tine TO darwin2;


--
-- TOC entry 5507 (class 0 OID 0)
-- Dependencies: 376
-- Name: TABLE v_get_catalogue_numbers_by_collection; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_get_catalogue_numbers_by_collection FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_get_catalogue_numbers_by_collection TO darwin2;


--
-- TOC entry 5508 (class 0 OID 0)
-- Dependencies: 377
-- Name: TABLE v_get_catalogue_numbers_by_collection_taxonomy_with_parent; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_get_catalogue_numbers_by_collection_taxonomy_with_parent TO darwin2;


--
-- TOC entry 5509 (class 0 OID 0)
-- Dependencies: 378
-- Name: TABLE v_ichtyology_series_fast; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_ichtyology_series_fast FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_ichtyology_series_fast TO darwin2;


--
-- TOC entry 5510 (class 0 OID 0)
-- Dependencies: 379
-- Name: TABLE v_imports_filename_encoded; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_imports_filename_encoded FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_imports_filename_encoded TO darwin2;


--
-- TOC entry 5511 (class 0 OID 0)
-- Dependencies: 380
-- Name: TABLE v_loan_detail_role_person; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loan_detail_role_person FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loan_detail_role_person TO darwin2;


--
-- TOC entry 5512 (class 0 OID 0)
-- Dependencies: 381
-- Name: TABLE v_loan_details_for_pentaho; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loan_details_for_pentaho FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loan_details_for_pentaho TO darwin2;


--
-- TOC entry 5513 (class 0 OID 0)
-- Dependencies: 382
-- Name: TABLE v_loans_for_pentaho; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loans_for_pentaho FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loans_for_pentaho TO darwin2;


--
-- TOC entry 5514 (class 0 OID 0)
-- Dependencies: 383
-- Name: TABLE v_loans_pentaho_contact_person; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loans_pentaho_contact_person FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loans_pentaho_contact_person TO darwin2;


--
-- TOC entry 5515 (class 0 OID 0)
-- Dependencies: 384
-- Name: TABLE v_loans_pentaho_general; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loans_pentaho_general FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loans_pentaho_general TO darwin2;


--
-- TOC entry 5516 (class 0 OID 0)
-- Dependencies: 385
-- Name: TABLE v_loans_pentaho_receivers; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_loans_pentaho_receivers FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_loans_pentaho_receivers TO darwin2;


--
-- TOC entry 5517 (class 0 OID 0)
-- Dependencies: 386
-- Name: TABLE v_mbisa_correspondence_dw_number_eod_mukweze_2022; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_mbisa_correspondence_dw_number_eod_mukweze_2022 TO darwin2;


--
-- TOC entry 5518 (class 0 OID 0)
-- Dependencies: 387
-- Name: TABLE v_rdf_view; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rdf_view FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rdf_view TO darwin2;


--
-- TOC entry 5519 (class 0 OID 0)
-- Dependencies: 388
-- Name: TABLE v_report_group_taxon_full_path_per_insertion_year; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year TO darwin2;


--
-- TOC entry 5520 (class 0 OID 0)
-- Dependencies: 389
-- Name: TABLE v_report_group_taxon_full_path_per_insertion_year_all; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_report_group_taxon_full_path_per_insertion_year_all TO darwin2;


--
-- TOC entry 5521 (class 0 OID 0)
-- Dependencies: 390
-- Name: TABLE v_report_yearly_encoding_statistics_specimens; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_report_yearly_encoding_statistics_specimens FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_report_yearly_encoding_statistics_specimens TO darwin2;


--
-- TOC entry 5522 (class 0 OID 0)
-- Dependencies: 391
-- Name: TABLE v_reporting_count_all_specimens; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens TO darwin2;


--
-- TOC entry 5523 (class 0 OID 0)
-- Dependencies: 392
-- Name: TABLE v_reporting_count_all_specimens_by_collection; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens_by_collection FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens_by_collection TO darwin2;


--
-- TOC entry 5524 (class 0 OID 0)
-- Dependencies: 393
-- Name: TABLE v_reporting_count_all_specimens_by_collection_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens_by_collection_year_ig TO darwin2;


--
-- TOC entry 5525 (class 0 OID 0)
-- Dependencies: 394
-- Name: TABLE v_reporting_count_all_specimens_type; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens_type FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens_type TO darwin2;


--
-- TOC entry 5526 (class 0 OID 0)
-- Dependencies: 395
-- Name: TABLE v_reporting_count_all_specimens_type_by_collection; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection TO darwin2;


--
-- TOC entry 5527 (class 0 OID 0)
-- Dependencies: 396
-- Name: TABLE v_reporting_count_all_specimens_type_by_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_count_all_specimens_type_by_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5528 (class 0 OID 0)
-- Dependencies: 397
-- Name: TABLE v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_higher_taxa_geo_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5529 (class 0 OID 0)
-- Dependencies: 398
-- Name: TABLE v_reporting_higher_taxa_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_higher_taxa_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5530 (class 0 OID 0)
-- Dependencies: 399
-- Name: TABLE v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_ig TO darwin2;


--
-- TOC entry 5531 (class 0 OID 0)
-- Dependencies: 400
-- Name: TABLE v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_taxa_in_specimen_per_rank_collection_ref_year_igall TO darwin2;


--
-- TOC entry 5532 (class 0 OID 0)
-- Dependencies: 401
-- Name: TABLE v_reporting_taxonomy_general; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_taxonomy_general FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_taxonomy_general TO darwin2;


--
-- TOC entry 5533 (class 0 OID 0)
-- Dependencies: 402
-- Name: TABLE v_reporting_taxonomy_in_specimen; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_reporting_taxonomy_in_specimen FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_reporting_taxonomy_in_specimen TO darwin2;


--
-- TOC entry 5534 (class 0 OID 0)
-- Dependencies: 403
-- Name: TABLE v_rmca_check_taxonomy_in_staging; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_check_taxonomy_in_staging FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_check_taxonomy_in_staging TO darwin2;


--
-- TOC entry 5535 (class 0 OID 0)
-- Dependencies: 404
-- Name: TABLE v_rmca_collections_path_as_text; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_collections_path_as_text FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_collections_path_as_text TO darwin2;


--
-- TOC entry 5536 (class 0 OID 0)
-- Dependencies: 405
-- Name: TABLE v_rmca_count_ichtyology_by_number_full_good; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_good TO darwin2;


--
-- TOC entry 5537 (class 0 OID 0)
-- Dependencies: 406
-- Name: TABLE v_rmca_count_ichtyology_by_number_full_restrict_ichtyo; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_count_ichtyology_by_number_full_restrict_ichtyo TO darwin2;


--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 407
-- Name: TABLE v_rmca_count_specimen_by_families_genus; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_count_specimen_by_families_genus FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_count_specimen_by_families_genus TO darwin2;


--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 408
-- Name: TABLE v_rmca_count_specimen_by_higher; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_count_specimen_by_higher FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_count_specimen_by_higher TO darwin2;


--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 409
-- Name: TABLE v_rmca_count_vertebrates_drosera_by_number; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_count_vertebrates_drosera_by_number TO darwin2;


--
-- TOC entry 5541 (class 0 OID 0)
-- Dependencies: 410
-- Name: TABLE v_rmca_export_staging_info; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_export_staging_info FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_export_staging_info TO darwin2;


--
-- TOC entry 5542 (class 0 OID 0)
-- Dependencies: 411
-- Name: TABLE v_rmca_get_genus_by_families; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_get_genus_by_families FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_get_genus_by_families TO darwin2;


--
-- TOC entry 5543 (class 0 OID 0)
-- Dependencies: 412
-- Name: TABLE v_rmca_get_higher_by_lower; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_get_higher_by_lower FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_get_higher_by_lower TO darwin2;


--
-- TOC entry 5544 (class 0 OID 0)
-- Dependencies: 413
-- Name: TABLE v_rmca_get_lower_by_higher; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_get_lower_by_higher FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_get_lower_by_higher TO darwin2;


--
-- TOC entry 5545 (class 0 OID 0)
-- Dependencies: 414
-- Name: TABLE v_rmca_gtu_tags_administraive_and_ecology; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_gtu_tags_administraive_and_ecology TO darwin2;


--
-- TOC entry 5546 (class 0 OID 0)
-- Dependencies: 415
-- Name: TABLE v_rmca_higher_than_familiy_in_collection; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_higher_than_familiy_in_collection FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_higher_than_familiy_in_collection TO darwin2;


--
-- TOC entry 5547 (class 0 OID 0)
-- Dependencies: 416
-- Name: TABLE v_rmca_ig_to_people; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_ig_to_people FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_ig_to_people TO darwin2;


--
-- TOC entry 5548 (class 0 OID 0)
-- Dependencies: 417
-- Name: TABLE v_rmca_ig_to_people_bics_report_2020; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020 TO darwin2;


--
-- TOC entry 5549 (class 0 OID 0)
-- Dependencies: 418
-- Name: TABLE v_rmca_ig_to_people_bics_report_2020_specimens; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_ig_to_people_bics_report_2020_specimens TO darwin2;


--
-- TOC entry 5550 (class 0 OID 0)
-- Dependencies: 419
-- Name: TABLE v_rmca_path_parent_children; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_path_parent_children FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_path_parent_children TO darwin2;


--
-- TOC entry 5551 (class 0 OID 0)
-- Dependencies: 420
-- Name: TABLE v_rmca_path_parent_children_extended_taxonomy; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy TO darwin2;


--
-- TOC entry 5552 (class 0 OID 0)
-- Dependencies: 421
-- Name: TABLE v_rmca_path_parent_children_extended_taxonomy_alpha; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha TO darwin2;


--
-- TOC entry 5553 (class 0 OID 0)
-- Dependencies: 422
-- Name: TABLE v_rmca_path_parent_children_extended_taxonomy_alpha_count_child; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child TO darwin2;


--
-- TOC entry 5554 (class 0 OID 0)
-- Dependencies: 423
-- Name: TABLE v_rmca_preferences_with_usernames; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_preferences_with_usernames FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_preferences_with_usernames TO darwin2;


--
-- TOC entry 5555 (class 0 OID 0)
-- Dependencies: 424
-- Name: TABLE v_rmca_report_ig_ichtyo_1_main; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_report_ig_ichtyo_1_main TO darwin2;


--
-- TOC entry 5556 (class 0 OID 0)
-- Dependencies: 425
-- Name: TABLE v_rmca_report_ig_ichtyo_2_localities; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_report_ig_ichtyo_2_localities TO darwin2;


--
-- TOC entry 5557 (class 0 OID 0)
-- Dependencies: 426
-- Name: TABLE v_rmca_report_ig_ichtyo_3_taxo; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_report_ig_ichtyo_3_taxo TO darwin2;


--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 427
-- Name: TABLE v_rmca_split_path; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_split_path FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_split_path TO darwin2;


--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 428
-- Name: TABLE v_rmca_split_path_extended; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_split_path_extended FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_split_path_extended TO darwin2;


--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 429
-- Name: TABLE v_rmca_split_path_extended_alpha_path; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_split_path_extended_alpha_path FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_split_path_extended_alpha_path TO darwin2;


--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 430
-- Name: TABLE v_rmca_taxo_detect_duplicate_hierarchies; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_rmca_taxo_detect_duplicate_hierarchies TO darwin2;


--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 431
-- Name: TABLE v_sophie_gryseels_2022; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_sophie_gryseels_2022 FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_sophie_gryseels_2022 TO darwin2;


--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 432
-- Name: TABLE v_specimens_isolate_taxa_in_path; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_specimens_isolate_taxa_in_path FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_specimens_isolate_taxa_in_path TO darwin2;


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 433
-- Name: TABLE v_specimens_isolate_taxa_in_path_with_metadata_ref; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_specimens_isolate_taxa_in_path_with_metadata_ref TO darwin2;


--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 434
-- Name: TABLE v_specimens_mids; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_specimens_mids FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_specimens_mids TO darwin2;


--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 435
-- Name: TABLE v_specimens_mids_simplified; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_specimens_mids_simplified FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_specimens_mids_simplified TO darwin2;


--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 436
-- Name: TABLE v_specimens_people_full_text; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_specimens_people_full_text FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_specimens_people_full_text TO darwin2;


--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 437
-- Name: TABLE v_staging_diagnose_rejects; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_staging_diagnose_rejects FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_staging_diagnose_rejects TO darwin2;


--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 438
-- Name: TABLE v_t_compare_darwin_digit03_mysql; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_t_compare_darwin_digit03_mysql FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_t_compare_darwin_digit03_mysql TO darwin2;


--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 439
-- Name: TABLE v_taxonomical_statistics_callard; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_taxonomical_statistics_callard FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_taxonomical_statistics_callard TO darwin2;


--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 440
-- Name: TABLE v_taxonomy_split_author_fast; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_taxonomy_split_author_fast FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_taxonomy_split_author_fast TO darwin2;


--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 441
-- Name: TABLE v_x_ray_drosera; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_x_ray_drosera FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_x_ray_drosera TO darwin2;


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 442
-- Name: TABLE v_xylarium_2022_image_link; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.v_xylarium_2022_image_link FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.v_xylarium_2022_image_link TO darwin2;


--
-- TOC entry 5574 (class 0 OID 0)
-- Dependencies: 443
-- Name: TABLE vernacular_names; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.vernacular_names FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.vernacular_names TO darwin2;


--
-- TOC entry 5575 (class 0 OID 0)
-- Dependencies: 444
-- Name: TABLE vmap0_world_boundaries; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.vmap0_world_boundaries FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.vmap0_world_boundaries TO darwin2;


--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 445
-- Name: TABLE vmap0_world_boundaries_enveloppe; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.vmap0_world_boundaries_enveloppe FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.vmap0_world_boundaries_enveloppe TO darwin2;


--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 446
-- Name: TABLE x_ray_drosera; Type: ACL; Schema: fdw_113; Owner: darwin2
--

REVOKE ALL ON TABLE fdw_113.x_ray_drosera FROM darwin2;
GRANT SELECT,INSERT,REFERENCES,TRIGGER,UPDATE ON TABLE fdw_113.x_ray_drosera TO darwin2;


-- Completed on 2023-12-22 16:34:16

--
-- PostgreSQL database dump complete
--

