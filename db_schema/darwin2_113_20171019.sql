--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.15
-- Dumped by pg_dump version 9.3.4
-- Started on 2017-10-19 18:14:09

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 7 (class 2615 OID 16388)
-- Name: darwin2; Type: SCHEMA; Schema: -; Owner: darwin2
--

CREATE SCHEMA darwin2;


ALTER SCHEMA darwin2 OWNER TO darwin2;

--
-- TOC entry 371 (class 3079 OID 11677)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 5180 (class 0 OID 0)
-- Dependencies: 371
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- TOC entry 376 (class 3079 OID 252393)
-- Name: fuzzystrmatch; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS fuzzystrmatch WITH SCHEMA darwin2;


--
-- TOC entry 5181 (class 0 OID 0)
-- Dependencies: 376
-- Name: EXTENSION fuzzystrmatch; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION fuzzystrmatch IS 'determine similarities and distance between strings';


--
-- TOC entry 373 (class 3079 OID 16470)
-- Name: hstore; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS hstore WITH SCHEMA public;


--
-- TOC entry 5182 (class 0 OID 0)
-- Dependencies: 373
-- Name: EXTENSION hstore; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION hstore IS 'data type for storing sets of (key, value) pairs';


--
-- TOC entry 374 (class 3079 OID 16423)
-- Name: pg_trgm; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pg_trgm WITH SCHEMA public;


--
-- TOC entry 5183 (class 0 OID 0)
-- Dependencies: 374
-- Name: EXTENSION pg_trgm; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pg_trgm IS 'text similarity measurement and index searching based on trigrams';


--
-- TOC entry 375 (class 3079 OID 16389)
-- Name: pgcrypto; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS pgcrypto WITH SCHEMA public;


--
-- TOC entry 5184 (class 0 OID 0)
-- Dependencies: 375
-- Name: EXTENSION pgcrypto; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION pgcrypto IS 'cryptographic functions';


--
-- TOC entry 372 (class 3079 OID 231160)
-- Name: postgis; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;


--
-- TOC entry 5185 (class 0 OID 0)
-- Dependencies: 372
-- Name: EXTENSION postgis; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION postgis IS 'PostGIS geometry, geography, and raster spatial types and functions';


SET search_path = darwin2, pg_catalog;

--
-- TOC entry 1715 (class 1255 OID 233122)
-- Name: array_sort(anyarray); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION array_sort(anyarray) RETURNS anyarray
    LANGUAGE sql
    AS $_$
SELECT ARRAY(SELECT unnest($1) ORDER BY 1)
$_$;


ALTER FUNCTION darwin2.array_sort(anyarray) OWNER TO darwin2;

--
-- TOC entry 1740 (class 1255 OID 232535)
-- Name: check_auto_increment_code_in_loan(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION check_auto_increment_code_in_loan() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE 
   col collections;
  loan RECORD;
  number text ;
  colcode varchar;

BEGIN
loan = NEW ;
SELECT parse[1], parse[3] into colcode,number FROM (SELECT regexp_matches(loan.name, '(.+)(_)([1-9]+)') as parse) AS a;
SELECT c.* INTO col FROM collections c JOIN specimens s ON s.collection_ref=c.id  WHERE TRIM(code)=TRIM(colcode);
  IF (col.id IS NOT NULL) AND isnumeric(number) THEN
    IF col.loan_auto_increment = TRUE  THEN 
      IF number::int > col.loan_last_value THEN
        UPDATE collections set loan_last_value = number::int WHERE id=col.id ;
      END IF;
    END IF;
  --ELSE
	--RAISE EXCEPTION 'COLLECTION NOT FOUND OR HAS NO SPECIMEN';
  END IF ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.check_auto_increment_code_in_loan() OWNER TO darwin2;

--
-- TOC entry 583 (class 1255 OID 18088)
-- Name: check_auto_increment_code_in_spec(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE 
  col collections;
  code RECORD;
  number integer ;
BEGIN
code = NEW ;
  IF code.referenced_relation = 'specimens' THEN
    SELECT c.* INTO col FROM collections c JOIN specimens s ON s.collection_ref=c.id WHERE s.id=code.record_id;  
    IF col.code_auto_increment = TRUE AND isnumeric(code.code) THEN 
      number := code.code::integer ;
      IF number > col.code_last_value THEN
        UPDATE collections set code_last_value = number WHERE id=col.id ;
      END IF;
    END IF;
  END IF ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.check_auto_increment_code_in_spec() OWNER TO darwin2;

--
-- TOC entry 576 (class 1255 OID 18081)
-- Name: chk_specimens_not_loaned(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION chk_specimens_not_loaned() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN

    IF exists( SELECT 1 FROM loan_items i INNER JOIN loan_status s on i.loan_ref = s.loan_ref
        WHERE s.is_last= true AND s.status != 'closed' AND i.specimen_ref = OLD.id ) THEN
      RAISE EXCEPTION 'The Part is currently used in an ongoing loan';
    END IF;
    RETURN OLD;
END;
$$;


ALTER FUNCTION darwin2.chk_specimens_not_loaned() OWNER TO darwin2;

--
-- TOC entry 525 (class 1255 OID 18020)
-- Name: concat(text[]); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION concat(VARIADIC text[]) RETURNS text
    LANGUAGE sql
    AS $_$
    SELECT array_to_string($1,'');
$_$;


ALTER FUNCTION darwin2.concat(VARIADIC text[]) OWNER TO darwin2;

--
-- TOC entry 534 (class 1255 OID 18035)
-- Name: convert_to_integer(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION convert_to_integer(v_input character varying) RETURNS integer
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE v_int_value INTEGER DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::INTEGER;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$;


ALTER FUNCTION darwin2.convert_to_integer(v_input character varying) OWNER TO darwin2;

--
-- TOC entry 500 (class 1255 OID 18036)
-- Name: convert_to_real(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION convert_to_real(v_input character varying) RETURNS real
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE v_int_value REAL DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::REAL;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$;


ALTER FUNCTION darwin2.convert_to_real(v_input character varying) OWNER TO darwin2;

--
-- TOC entry 521 (class 1255 OID 18015)
-- Name: convert_to_unified(character varying, character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION convert_to_unified(property character varying, property_unit character varying) RETURNS double precision
    LANGUAGE plpgsql
    AS $$
DECLARE
    r_val real :=0;
BEGIN
    IF property is NULL THEN
        RETURN NULL;
    END IF;

    BEGIN
      r_val := property::real;
    EXCEPTION WHEN SQLSTATE '22P02' THEN
      RETURN null;

      WHEN OTHERS THEN
        RETURN null;
    END;

    IF property_unit IN ('Kt', 'Beaufort', 'm/s') THEN
        RETURN fct_cpy_speed_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain') THEN
        RETURN fct_cpy_weight_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('m³', 'l', 'cm³', 'ml', 'mm³' ,'µl' , 'µm³' , 'km³', 'Ml' , 'hl') THEN
        RETURN fct_cpy_volume_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De') THEN
        RETURN fct_cpy_temperature_conversion(r_val, property_unit)::text;
    END IF;

    IF property_unit IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms') THEN
        RETURN fct_cpy_length_conversion(r_val, property_unit)::text;
    END IF;

    RETURN  property;

END;
$$;


ALTER FUNCTION darwin2.convert_to_unified(property character varying, property_unit character varying) OWNER TO darwin2;

--
-- TOC entry 523 (class 1255 OID 18017)
-- Name: convert_to_unified(character varying, character varying, character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION convert_to_unified(property character varying, property_unit character varying, property_type character varying) RETURNS double precision
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
    r_val real :=0;
BEGIN
    IF property is NULL THEN
        RETURN NULL;
    END IF;

    BEGIN
      r_val := property::real;
    EXCEPTION WHEN SQLSTATE '22P02' THEN
      RETURN null;
    END;

    IF property_type = 'speed' THEN
        RETURN fct_cpy_speed_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'weight' THEN
        RETURN fct_cpy_weight_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'volume' THEN
        RETURN fct_cpy_volume_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type = 'temperature' AND property_unit IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De') THEN
        RETURN fct_cpy_temperature_conversion(r_val, property_unit)::text;
    END IF;

    IF property_type IN ('length') AND property_unit IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms') THEN
        RETURN fct_cpy_length_conversion(r_val, property_unit)::text;
    END IF;

    RETURN  property;
END;
$$;


ALTER FUNCTION darwin2.convert_to_unified(property character varying, property_unit character varying, property_type character varying) OWNER TO darwin2;

--
-- TOC entry 550 (class 1255 OID 18051)
-- Name: fct_add_in_dict(text, text, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_add_in_dict(ref_relation text, ref_field text, old_value text, new_val text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  query_str varchar;
BEGIN
  IF new_val is NULL OR old_value IS NOT DISTINCT FROM new_val THEN
    RETURN TRUE;
  END IF;
    query_str := ' INSERT INTO flat_dict (referenced_relation, dict_field, dict_value)
    (
      SELECT ' || quote_literal(ref_relation) || ' , ' || quote_literal(ref_field) || ', ' || quote_literal(new_val) || ' WHERE NOT EXISTS
      (SELECT id FROM flat_dict WHERE
        referenced_relation = ' || quote_literal(ref_relation) || '
        AND dict_field = ' || quote_literal(ref_field) || '
        AND dict_value = ' || quote_literal(new_val) || ')
    );';
    execute query_str;
    RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_add_in_dict(ref_relation text, ref_field text, old_value text, new_val text) OWNER TO darwin2;

--
-- TOC entry 551 (class 1255 OID 18052)
-- Name: fct_add_in_dict_dept(text, text, text, text, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_add_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text, depending_old_value text, depending_new_value text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  query_str varchar;
  dpt_new_val varchar;
BEGIN
  IF new_val is NULL OR ( old_value IS NOT DISTINCT FROM new_val AND depending_old_value IS NOT DISTINCT FROM depending_new_value ) THEN
    RETURN TRUE;
  END IF;
  dpt_new_val := coalesce(depending_new_value,'');

    query_str := ' INSERT INTO flat_dict (referenced_relation, dict_field, dict_value, dict_depend)
    (
      SELECT ' || quote_literal(ref_relation) || ' , ' || quote_literal(ref_field) || ', ' || quote_literal(new_val) || ', '
        || quote_literal(dpt_new_val) || ' WHERE NOT EXISTS
      (SELECT id FROM flat_dict WHERE
        referenced_relation = ' || quote_literal(ref_relation) || '
        AND dict_field = ' || quote_literal(ref_field) || '
        AND dict_value = ' || quote_literal(new_val) || '
        AND dict_depend = ' || quote_literal(dpt_new_val) || '
      )
    );';
    --RAISE info 'hem %' ,  dpt_new_val;
    execute query_str;
    RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_add_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text, depending_old_value text, depending_new_value text) OWNER TO darwin2;

--
-- TOC entry 498 (class 1255 OID 17996)
-- Name: fct_array_find(anyarray, anyelement); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_array_find(in_array anyarray, elem anyelement, OUT item_order integer) RETURNS integer
    LANGUAGE sql IMMUTABLE
    AS $_$
    select s from generate_series(1,array_upper($1, 1)) as s where $1[s] = $2;
$_$;


ALTER FUNCTION darwin2.fct_array_find(in_array anyarray, elem anyelement, OUT item_order integer) OWNER TO darwin2;

--
-- TOC entry 499 (class 1255 OID 17997)
-- Name: fct_array_find(character varying, anyelement); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_array_find(in_array character varying, elem anyelement, OUT item_order integer) RETURNS integer
    LANGUAGE sql IMMUTABLE
    AS $_$
    select fct_array_find(string_to_array($1,','), $2::text);
$_$;


ALTER FUNCTION darwin2.fct_array_find(in_array character varying, elem anyelement, OUT item_order integer) OWNER TO darwin2;

--
-- TOC entry 577 (class 1255 OID 18082)
-- Name: fct_auto_insert_status_history(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_auto_insert_status_history() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
 user_id int;
BEGIN
    SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
    IF user_id = 0 THEN
      RETURN NEW;
    END IF;

    INSERT INTO loan_status
      (loan_ref, user_ref, status, modification_date_time, comment, is_last)
      VALUES
      (NEW.id, user_id, 'new', now(), '', true);

    INSERT INTO loan_rights
      (loan_ref, user_ref, has_encoding_right)
      VALUES
      (NEW.id, user_id, true);

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_auto_insert_status_history() OWNER TO darwin2;

--
-- TOC entry 581 (class 1255 OID 18086)
-- Name: fct_cast_to_real(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cast_to_real(element text) RETURNS real
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE r_val real;
BEGIN
    BEGIN
      r_val := element::real;
      return r_val;
    EXCEPTION WHEN SQLSTATE '22P02' THEN
      RETURN null;
    END;
END;
$$;


ALTER FUNCTION darwin2.fct_cast_to_real(element text) OWNER TO darwin2;

--
-- TOC entry 1750 (class 1255 OID 108352)
-- Name: fct_catalogue_import_keywords_update(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_catalogue_import_keywords_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  DECLARE
    booContinue BOOLEAN := FALSE;
    intDiag INTEGER;
  BEGIN
    IF TG_TABLE_NAME = 'staging_catalogue' THEN
      IF TG_OP IN ('INSERT', 'UPDATE') THEN
        IF COALESCE(NEW.catalogue_ref,0) != 0 AND COALESCE(NEW.level_ref,0) != 0 THEN
          UPDATE classification_keywords as mck
            SET
              referenced_relation = (
                SELECT level_type
                FROM catalogue_levels
                WHERE id = NEW.level_ref
              ),
              record_id = NEW.catalogue_ref
          WHERE mck.referenced_relation = TG_TABLE_NAME
            AND mck.record_id = NEW.id
            AND NOT EXISTS (
              SELECT 1
              FROM classification_keywords as sck
              WHERE sck.referenced_relation = (
                  SELECT level_type
                  FROM catalogue_levels
                  WHERE id = NEW.level_ref
                )
                AND sck.record_id = NEW.catalogue_ref
                AND sck.keyword_type = mck.keyword_type
                AND sck.keyword_indexed = mck.keyword_indexed
              );
        END IF;
        RETURN NEW;
      ELSE
        DELETE FROM classification_keywords
        WHERE referenced_relation = 'staging_catalogue'
              AND record_id = OLD.id;
        RETURN NULL;
      END IF;
    ELSEIF TG_TABLE_NAME = 'staging' THEN
      IF TG_OP IN ('INSERT', 'UPDATE') THEN
        IF COALESCE(NEW.taxon_ref,0) != 0 AND COALESCE(NEW.taxon_level_ref,0) != 0 THEN
          IF TG_OP = 'UPDATE' THEN
            IF COALESCE(NEW.taxon_ref,0) != COALESCE(OLD.taxon_ref,0) THEN
              booContinue := TRUE;
            END IF;
          ELSE
            booContinue := TRUE;
          END IF;
          IF booContinue = TRUE THEN
            UPDATE classification_keywords as mck
            SET
              referenced_relation = 'taxonomy',
              record_id = NEW.taxon_ref
            WHERE mck.referenced_relation = TG_TABLE_NAME
                  AND mck.record_id = NEW.id
                  AND mck.keyword_type IN (
                                            'GenusOrMonomial',
                                            'Subgenus',
                                            'SpeciesEpithet',
                                            'FirstEpiteth',
                                            'SubspeciesEpithet',
                                            'InfraspecificEpithet',
                                            'AuthorTeamAndYear',
                                            'AuthorTeam',
                                            'AuthorTeamOriginalAndYear',
                                            'AuthorTeamParenthesisAndYear',
                                            'SubgenusAuthorAndYear',
                                            'CultivarGroupName',
                                            'CultivarName',
                                            'Breed',
                                            'CombinationAuthorTeamAndYear',
                                            'NamedIndividual'
                                          )
                  AND NOT EXISTS (
                                  SELECT 1
                                  FROM classification_keywords as sck
                                  WHERE sck.referenced_relation = 'taxonomy'
                                        AND sck.record_id = NEW.taxon_ref
                                        AND sck.keyword_type = mck.keyword_type
                                        AND sck.keyword_indexed = mck.keyword_indexed
            );
          END IF;
        ELSEIF COALESCE(NEW.mineral_ref,0) != 0 AND COALESCE(NEW.mineral_level_ref,0) != 0 THEN
          IF TG_OP = 'UPDATE' THEN
            IF COALESCE(NEW.mineral_ref,0) != COALESCE(OLD.mineral_ref,0) THEN
              booContinue := TRUE;
            END IF;
          ELSE
            booContinue := TRUE;
          END IF;
          IF booContinue = TRUE THEN
            UPDATE classification_keywords as mck
            SET
              referenced_relation = 'mineralogy',
              record_id = NEW.mineral_ref
            WHERE mck.referenced_relation = TG_TABLE_NAME
                  AND mck.record_id = NEW.id
                  AND mck.keyword_type IN (
                                            'AuthorTeamAndYear',
                                            'AuthorTeam',
                                            'NamedIndividual'
                                          )
                  AND NOT EXISTS (
                                  SELECT 1
                                  FROM classification_keywords as sck
                                  WHERE sck.referenced_relation = 'mineralogy'
                                        AND sck.record_id = NEW.mineral_ref
                                        AND sck.keyword_type = mck.keyword_type
                                        AND sck.keyword_indexed = mck.keyword_indexed
            );
          END IF;
        END IF;
        RETURN NEW;
      ELSE
        DELETE FROM classification_keywords
        WHERE referenced_relation = 'staging'
              AND record_id = OLD.id;
        RETURN NULL;
      END IF;
    END IF;
  END;
$$;


ALTER FUNCTION darwin2.fct_catalogue_import_keywords_update() OWNER TO darwin2;

--
-- TOC entry 531 (class 1255 OID 18029)
-- Name: fct_chk_canupdatecollectionsrights(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_canupdatecollectionsrights() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  mgrName varchar;
  booContinue boolean := false;
BEGIN
  /*Check an unpromotion occurs by modifying db_user_type explicitely or implicitely by replacing a user by an other
    or moving a user from one collection to an other
  */
  IF (NEW.db_user_type < 4 AND OLD.db_user_type >=4) OR NEW.collection_ref IS DISTINCT FROM OLD.collection_ref OR NEW.user_ref IS DISTINCT FROM OLD.user_ref THEN
    SELECT formated_name INTO mgrName
    FROM collections INNER JOIN users ON users.id = collections.main_manager_ref
    WHERE collections.id = OLD.collection_ref
      AND main_manager_ref = OLD.user_ref;
    /*If user concerned still main manager of the collection, cannot be updated*/
    IF FOUND THEN
      RAISE EXCEPTION 'This manager (%) cannot be updated because he/she is still defined as a main manager for this collection', mgrName;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_canupdatecollectionsrights() OWNER TO darwin2;

--
-- TOC entry 1727 (class 1255 OID 18019)
-- Name: fct_chk_onceinpath(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_onceinpath(path character varying) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN

    PERFORM * FROM regexp_split_to_table(path, E'\/') as i_id WHERE i_id != '' GROUP BY i_id HAVING COUNT(*)>1;
    IF FOUND THEN
        RETURN FALSE;
    END IF;
    RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_onceinpath(path character varying) OWNER TO darwin2;

--
-- TOC entry 545 (class 1255 OID 18046)
-- Name: fct_chk_parentcollinstitution(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_parentcollinstitution() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  institutionRef integer;
  booContinue boolean := false;
BEGIN
  IF TG_OP = 'INSERT' THEN
    booContinue := true;
  ELSIF TG_OP = 'UPDATE' THEN
    IF NEW.institution_ref IS DISTINCT FROM OLD.institution_ref OR NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
      booContinue := true;
    END IF;
  END IF;
  IF booContinue THEN
    IF NEW.parent_ref IS NOT NULL THEN
      SELECT institution_ref INTO institutionRef FROM collections WHERE id = NEW.parent_ref;
      IF institutionRef != NEW.institution_ref THEN
        RAISE EXCEPTION 'You tried to insert or update a collection with an other institution than the one given for the parent collection';
      END IF;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_parentcollinstitution() OWNER TO darwin2;

--
-- TOC entry 493 (class 1255 OID 17988)
-- Name: fct_chk_peopleismoral(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_peopleismoral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists boolean;
BEGIN
   SELECT is_physical FROM people WHERE id=NEW.institution_ref into rec_exists;

   IF rec_exists = TRUE THEN
    RAISE EXCEPTION 'You cannot link a moral person as Institution';
   END IF;

   RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_peopleismoral() OWNER TO darwin2;

--
-- TOC entry 503 (class 1255 OID 17998)
-- Name: fct_chk_possible_upper_level(character varying, integer, integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_possible_upper_level(referenced_relation character varying, new_parent_ref integer, new_level_ref integer, new_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
response boolean;
BEGIN
  EXECUTE 'SELECT true WHERE EXISTS( SELECT * ' ||
          'from possible_upper_levels ' ||
          'where level_ref = ' || quote_literal(new_level_ref) ||
          '  and coalesce(level_upper_ref,0) = case when ' || quote_literal(coalesce(new_parent_ref,0)) || ' != '|| quote_literal(0) || ' then (select level_ref from ' || quote_ident(referenced_relation) || ' where id = ' || quote_literal(coalesce(new_parent_ref,0)) || ') else ' || quote_literal(coalesce(new_parent_ref,0)) || ' end' ||
          '                              )'
    INTO response;
  IF response IS NULL THEN
    RETURN FALSE;
  ELSE
    RETURN TRUE;
  END IF;

  RETURN FALSE;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_possible_upper_level(referenced_relation character varying, new_parent_ref integer, new_level_ref integer, new_id integer) OWNER TO darwin2;

--
-- TOC entry 505 (class 1255 OID 18001)
-- Name: fct_chk_referencedrecord(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_referencedrecord() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT 1 WHERE EXISTS ( SELECT id FROM ' || quote_ident(NEW.referenced_relation)  || ' WHERE id=' || quote_literal(NEW.record_id) || ')' INTO rec_exists;
  IF rec_exists IS NULL THEN
    RAISE EXCEPTION 'The referenced record does not exists % %',NEW.referenced_relation, NEW.record_id;
  END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_referencedrecord() OWNER TO darwin2;

--
-- TOC entry 506 (class 1255 OID 18002)
-- Name: fct_chk_referencedrecordrelationship(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_referencedrecordrelationship() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT count(id)  FROM ' || quote_ident(NEW.referenced_relation)  || ' WHERE id=' || quote_literal(NEW.record_id_1) ||  ' OR id=' || quote_literal(NEW.record_id_2) INTO rec_exists;

  IF rec_exists != 2 THEN
    RAISE EXCEPTION 'The referenced record does not exists';
  END IF;

  RETURN NEW;

END;
$$;


ALTER FUNCTION darwin2.fct_chk_referencedrecordrelationship() OWNER TO darwin2;

--
-- TOC entry 542 (class 1255 OID 18042)
-- Name: fct_chk_specimencollectionallowed(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_specimencollectionallowed() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  user_id integer;
  db_user_type_cpy smallint;
  col_ref integer;
BEGIN
  SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
  /*If no user id allows modification -> if we do a modif in SQL it should be possible*/
  IF user_id = 0 THEN
    IF TG_OP = 'DELETE' THEN
      RETURN OLD;
    END IF;
    RETURN NEW;
  END IF;

  IF user_id = -1 THEN
    RETURN NEW;
  END IF;
  /*If user_id <> 0, get db_user_type of user concerned*/
  SELECT db_user_type INTO db_user_type_cpy FROM users WHERE id = user_id;
  /*If admin allows whatever*/
  IF db_user_type_cpy = 8 THEN
    IF TG_OP = 'DELETE' THEN
      RETURN OLD;
    END IF;
    RETURN NEW;
  END IF;

  IF TG_TABLE_NAME = 'specimens' THEN
    IF TG_OP = 'INSERT' OR TG_OP = 'UPDATE' THEN
      IF NOT EXISTS (SELECT 1 FROM fct_search_authorized_encoding_collections (user_id) as r WHERE r = NEW.collection_ref) THEN
        RAISE EXCEPTION 'You don''t have the rights to insert into or update a specimen in this collection';
      END IF;
    ELSE /*Delete*/
      PERFORM true WHERE OLD.collection_ref::integer IN (SELECT * FROM fct_search_authorized_encoding_collections(user_id));
      IF NOT EXISTS (SELECT 1 FROM fct_search_authorized_encoding_collections (user_id) as r WHERE r = OLD.collection_ref) THEN
        RAISE EXCEPTION 'You don''t have the rights to delete a specimen from this collection';
      END IF;
    END IF;
  END IF;
  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_chk_specimencollectionallowed() OWNER TO darwin2;

--
-- TOC entry 504 (class 1255 OID 18000)
-- Name: fct_chk_upper_level_for_childrens(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_chk_upper_level_for_childrens() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_exists integer;
BEGIN

  EXECUTE 'SELECT count(id)  FROM ' || quote_ident(TG_TABLE_NAME::text) ||
    ' WHERE parent_ref=' || quote_literal(NEW.id) ||
    ' AND fct_chk_possible_upper_level('|| quote_literal(TG_TABLE_NAME::text) ||
    ', parent_ref, level_ref, id) = false ' INTO rec_exists;

  IF rec_exists > 0 THEN
    RAISE EXCEPTION 'Children of this record does not follow the level hierarchy';
  END IF;
  RETURN NEW;

END;
$$;


ALTER FUNCTION darwin2.fct_chk_upper_level_for_childrens() OWNER TO darwin2;

--
-- TOC entry 617 (class 1255 OID 108354)
-- Name: fct_clean_staging_catalogue(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_clean_staging_catalogue(importref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
  DECLARE
    recDistinctStagingCatalogue RECORD;
  BEGIN
    FOR recDistinctStagingCatalogue IN SELECT DISTINCT ON (level_ref, fullToIndex(name), name)
                                       id, import_ref, name, level_ref
                                       FROM
                                         (
                                           SELECT
                                             id,
                                             import_ref,
                                             name,
                                             level_ref
                                           FROM staging_catalogue
                                           WHERE import_ref = importRef
                                           ORDER BY level_ref, fullToIndex(name), id
                                         ) as subqry
    LOOP
      UPDATE staging_catalogue
      SET parent_ref = recDistinctStagingCatalogue.id
      WHERE
        import_ref = importRef
        AND parent_ref IN
            (
              SELECT id
              FROM staging_catalogue
              WHERE import_ref = importRef
                AND name = recDistinctStagingCatalogue.name
                AND level_ref = recDistinctStagingCatalogue.level_ref
                AND id != recDistinctStagingCatalogue.id
            );
      DELETE FROM staging_catalogue
      WHERE import_ref = importRef
            and name = recDistinctStagingCatalogue.name
            and level_ref = recDistinctStagingCatalogue.level_ref
            and id != recDistinctStagingCatalogue.id;
    END LOOP;
    RETURN TRUE;
  EXCEPTION
    WHEN OTHERS THEN
      RAISE WARNING 'Error:%', SQLERRM;
      RETURN FALSE;
  END;
$$;


ALTER FUNCTION darwin2.fct_clean_staging_catalogue(importref integer) OWNER TO darwin2;

--
-- TOC entry 556 (class 1255 OID 18058)
-- Name: fct_clear_identifiers_in_flat(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_clear_identifiers_in_flat() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;

  IF EXISTS(SELECT true FROM catalogue_people cp WHERE cp.record_id = OLD.id AND cp.referenced_relation = 'identifications') THEN
    -- There's NO identifier associated to this identification'
    UPDATE specimens SET spec_ident_ids = fct_remove_array_elem(spec_ident_ids,
      (
        select array_agg(people_ref) FROM catalogue_people p  INNER JOIN identifications i ON p.record_id = i.id AND i.id = OLD.id
        AND people_ref NOT in
          (
            SELECT people_ref from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications'
            AND p.people_type='identifier' where i.record_id=OLD.record_id AND i.referenced_relation=OLD.referenced_relation AND i.id != OLD.id
          )
      ))
      WHERE id = OLD.record_id;
  END IF;

  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN OLD;

END;
$$;


ALTER FUNCTION darwin2.fct_clear_identifiers_in_flat() OWNER TO darwin2;

--
-- TOC entry 496 (class 1255 OID 17993)
-- Name: fct_clear_referencedrecord(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_clear_referencedrecord() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF TG_OP ='UPDATE' THEN
    IF NEW.id != OLD.id THEN
      UPDATE template_table_record_ref SET record_id = NEW.id WHERE referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
    END IF;
  ELSE
    DELETE FROM template_table_record_ref where referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  END IF;
  RETURN OLD;
END;
$$;


ALTER FUNCTION darwin2.fct_clear_referencedrecord() OWNER TO darwin2;

--
-- TOC entry 495 (class 1255 OID 17992)
-- Name: fct_clr_specialstatus(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_clr_specialstatus() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  newType varchar := fullToIndex(NEW.type);
BEGIN

  -- IF Type not changed
  IF TG_OP = 'UPDATE' THEN
    IF fullToIndex(OLD.type) = newType THEN
      RETURN NEW;
    END IF;
  END IF;

  IF newType = 'specimen' THEN
    NEW.type_search := 'specimen';
    NEW.type_group := 'specimen';
  ELSIF newType = 'type' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'subtype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'allotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'allotype';
  ELSIF newType = 'cotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'syntype';
  ELSIF newType = 'genotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'holotype' THEN
    NEW.type_search := 'holotype';
    NEW.type_group := 'holotype';
  ELSIF newType = 'hypotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'hypotype';
  ELSIF newType = 'lectotype' THEN
    NEW.type_search := 'lectotype';
    NEW.type_group := 'lectotype';
  ELSIF newType = 'locotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'locotype';
  ELSIF newType = 'neallotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  ELSIF newType = 'neotype' THEN
    NEW.type_search := 'neotype';
    NEW.type_group := 'neotype';
  ELSIF newType = 'paralectotype' THEN
    NEW.type_search := 'paralectotype';
    NEW.type_group := 'paralectotype';
  ELSIF newType = 'paratype' THEN
    NEW.type_search := 'paratype';
    NEW.type_group := 'paratype';
  ELSIF newType = 'plastotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'plastotype';
  ELSIF newType = 'plesiotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'plesiotype';
  ELSIF newType = 'syntype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'syntype';
  ELSIF newType = 'topotype' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'topotype';
  ELSIF newType = 'typeinlitteris' THEN
    NEW.type_search := 'type';
    NEW.type_group := 'type in litteris';
  ELSE
    NEW.type_search := 'type';
    NEW.type_group := 'type';
  END IF;

  RETURN NEW;
EXCEPTION
  WHEN RAISE_EXCEPTION THEN
    return NULL;
END;
$$;


ALTER FUNCTION darwin2.fct_clr_specialstatus() OWNER TO darwin2;

--
-- TOC entry 579 (class 1255 OID 18084)
-- Name: fct_cpy_deleted_file(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_deleted_file() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  INSERT INTO multimedia_todelete (uri) VALUES (OLD.uri);
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_deleted_file() OWNER TO darwin2;

--
-- TOC entry 627 (class 1255 OID 18003)
-- Name: fct_cpy_formattedname(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_formattedname() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF TG_OP ='UPDATE' THEN
    IF NEW.family_name = OLD.family_name AND NEW.given_name = OLD.given_name AND NEW.title = OLD.title THEN
      RETURN NEW;
    END IF;
  END IF;

  IF NEW.is_physical THEN
  -- IF COALESCE(NEW.title, '') = '' THEN
  IF LENGTH(COALESCE(NEW.title, ''))=0 THEN
      NEW.formated_name := trim(COALESCE(NEW.family_name,'') || ' ' || COALESCE(NEW.given_name,''));
      --mrac 2015 07 16 test
      NEW.formated_name_indexed := trim(fulltoindex(COALESCE(NEW.family_name,''), true) || fulltoindex(' ' || COALESCE(NEW.given_name,''),true));
      NEW.formated_name_unique := TRIM(touniquestr(COALESCE(NEW.family_name,''), true) || touniquestr(' ' || COALESCE(NEW.given_name,''), true));      
    ELSE
      NEW.formated_name := TRIM(COALESCE(NEW.family_name,'') || ' ' || COALESCE(NEW.given_name,'') || ' (' || NEW.title || ')');
      --mrac 2015 07 16 test
      NEW.formated_name_indexed := TRIM(fulltoindex(COALESCE(NEW.family_name,''),true) || fulltoindex(COALESCE(NEW.given_name,'') || ' (' || NEW.title || ')', true));
       NEW.formated_name_unique := TRIM(touniquestr(COALESCE(NEW.family_name,'')) || touniquestr(COALESCE(NEW.given_name,'') || ' (' || NEW.title || ')', true));
    END IF;
  ELSE
    NEW.formated_name := NEW.family_name;
    --mrac test
    NEW.formated_name_indexed := TRIM(fullToIndex(NEW.formated_name, true));
    NEW.formated_name_unique := TRIM(toUniqueStr(NEW.formated_name, true));
  END IF;

  --NEW.formated_name_indexed := fullToIndex(NEW.formated_name);
  --NEW.formated_name_unique := toUniqueStr(NEW.formated_name);
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_formattedname() OWNER TO darwin2;

--
-- TOC entry 1723 (class 1255 OID 17991)
-- Name: fct_cpy_fulltoindex(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_fulltoindex() RETURNS trigger
    LANGUAGE plpgsql
    AS $_$
BEGIN
        IF TG_TABLE_NAME = 'properties' THEN
                NEW.applies_to_indexed := COALESCE(fullToIndex(NEW.applies_to),'');
                NEW.method_indexed := COALESCE(fullToIndex(NEW.method),'');
        ELSIF TG_TABLE_NAME = 'chronostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'collections' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'expeditions' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'bibliography' THEN
                NEW.title_indexed := fullToIndex(NEW.title);
        ELSIF TG_TABLE_NAME = 'identifications' THEN
                NEW.value_defined_indexed := COALESCE(fullToIndex(NEW.value_defined),'');
        ELSIF TG_TABLE_NAME = 'lithology' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'lithostratigraphy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'mineralogy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
                NEW.formule_indexed := fullToIndex(NEW.formule);
        ELSIF TG_TABLE_NAME = 'people' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.name_formated_indexed := fulltoindex(coalesce(NEW.given_name,'') || coalesce(NEW.family_name,''));
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'codes' THEN
                IF NEW.code ~ '^[0-9]+$' THEN
                    NEW.code_num := NEW.code;
                ELSE
                    NEW.code_num := null;
                END IF;
                NEW.full_code_indexed := fullToIndex(COALESCE(NEW.code_prefix,'') || COALESCE(NEW.code::text,'') || COALESCE(NEW.code_suffix,'') );
        ELSIF TG_TABLE_NAME = 'tag_groups' THEN
                NEW.group_name_indexed := fullToIndex(NEW.group_name);
                NEW.sub_group_name_indexed := fullToIndex(NEW.sub_group_name);
        ELSIF TG_TABLE_NAME = 'taxonomy' THEN
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'classification_keywords' THEN
                NEW.keyword_indexed := fullToIndex(NEW.keyword);
        ELSIF TG_TABLE_NAME = 'users' THEN
                NEW.formated_name_indexed := COALESCE(fullToIndex(NEW.formated_name),'');
                NEW.formated_name_unique := COALESCE(toUniqueStr(NEW.formated_name),'');
        ELSIF TG_TABLE_NAME = 'vernacular_names' THEN
                NEW.community_indexed := fullToIndex(NEW.community);
                NEW.name_indexed := fullToIndex(NEW.name);
        ELSIF TG_TABLE_NAME = 'igs' THEN
                NEW.ig_num_indexed := fullToIndex(NEW.ig_num);
        ELSIF TG_TABLE_NAME = 'collecting_methods' THEN
                NEW.method_indexed := fullToIndex(NEW.method);
        ELSIF TG_TABLE_NAME = 'collecting_tools' THEN
                NEW.tool_indexed := fullToIndex(NEW.tool);
        ELSIF TG_TABLE_NAME = 'loans' THEN
                NEW.search_indexed := fullToIndex(COALESCE(NEW.name,'') || COALESCE(NEW.description,''));
        ELSIF TG_TABLE_NAME = 'multimedia' THEN
                NEW.search_indexed := fullToIndex ( COALESCE(NEW.title,'') ||  COALESCE(NEW.description,'') || COALESCE(NEW.extracted_info,'') ) ;
        ELSIF TG_TABLE_NAME = 'comments' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
        ELSIF TG_TABLE_NAME = 'ext_links' THEN
                NEW.comment_indexed := fullToIndex(NEW.comment);
               --ftheeten 2016 08 08
        --ELSIF TG_TABLE_NAME = 'specimens' THEN
          --      NEW.object_name_indexed := fullToIndex(COALESCE(NEW.object_name,'') );
        ELSIF TG_TABLE_NAME = 'storage_parts' THEN
                NEW.object_name_indexed := fullToIndex(COALESCE(NEW.object_name,'') );
        END IF;
    
	RETURN NEW;
END;
$_$;


ALTER FUNCTION darwin2.fct_cpy_fulltoindex() OWNER TO darwin2;

--
-- TOC entry 630 (class 1255 OID 18027)
-- Name: fct_cpy_gtutags(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_gtutags() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  curs_entry refcursor;
  entry_row RECORD;
  seen_el varchar[];
BEGIN
  IF TG_OP != 'DELETE' THEN
    --OPEN curs_entry FOR SELECT distinct(fulltoIndex(tags)) as u_tag, trim(tags) as tags
    --                    FROM regexp_split_to_table(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value, ';') as tags
    --                    WHERE fulltoIndex(tags) != '';
    --ftheeten 2016 03 29
       OPEN curs_entry FOR SELECT distinct(fulltoIndex(tags,true)) as u_tag, trim(tags) as tags
                        FROM regexp_split_to_table(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value, ';') as tags
                        WHERE fulltoIndex(tags, true) != '';
    LOOP
      FETCH curs_entry INTO entry_row;
      EXIT WHEN NOT FOUND;

      seen_el := array_append(seen_el, entry_row.u_tag);

     IF EXISTS( SELECT 1 FROM tags
                WHERE gtu_ref = NEW.gtu_ref
                  AND group_ref = NEW.id
                  AND tag_indexed = entry_row.u_tag) THEN
        IF TG_OP = 'UPDATE' THEN
          IF OLD.sub_group_name != NEW.sub_group_name THEN
            UPDATE tags
            SET sub_group_type = NEW.sub_group_name
            WHERE group_ref = NEW.id;
          END IF;
        END IF;
        CONTINUE;
      ELSE
        INSERT INTO tags (gtu_ref, group_ref, tag_indexed, tag, group_type, sub_group_type )
        VALUES ( NEW.gtu_ref, NEW.id, entry_row.u_tag, entry_row.tags, NEW.group_name, NEW.sub_group_name);
      END IF;
    END LOOP;

    CLOSE curs_entry;

    UPDATE gtu
    SET tag_values_indexed = (SELECT array_agg(tags_list)
                              FROM (SELECT lineToTagRows(tag_agg) AS tags_list
                                    FROM (SELECT case when international_name != '' THEN international_name || ';' ELSE '' END || tag_value AS tag_agg
                                          FROM tag_groups
                                          WHERE id <> NEW.id
                                            AND gtu_ref = NEW.gtu_ref
                                          UNION
                                          SELECT case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value
                                         ) as tag_list_selection
                                   ) as tags_rows
                             )
    WHERE id = NEW.gtu_ref;

    DELETE FROM tags
           WHERE group_ref = NEW.id
              AND gtu_ref = NEW.gtu_ref
              AND fct_array_find(seen_el, tag_indexed ) IS NULL;
    RETURN NEW;
  ELSE
    UPDATE gtu
    SET tag_values_indexed = (SELECT array_agg(tags_list)
                              FROM (SELECT lineToTagRows(tag_agg) AS tags_list
                                    FROM (SELECT tag_value AS tag_agg
                                          FROM tag_groups
                                          WHERE id <> OLD.id
                                            AND gtu_ref = OLD.gtu_ref
                                         ) as tag_list_selection
                                   ) as tags_rows
                             )
    WHERE id = OLD.gtu_ref;
    RETURN NULL;
  END IF;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_gtutags() OWNER TO darwin2;

--
-- TOC entry 578 (class 1255 OID 18083)
-- Name: fct_cpy_ig_to_loan_items(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_ig_to_loan_items() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF OLD.ig_ref is distinct from NEW.ig_ref THEN
    UPDATE loan_items li SET ig_ref = NEW.ig_ref
    WHERE specimen_ref = NEW.ID
    AND li.ig_ref IS NOT DISTINCT FROM OLD.ig_ref;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_ig_to_loan_items() OWNER TO darwin2;

--
-- TOC entry 513 (class 1255 OID 18009)
-- Name: fct_cpy_length_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_length_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = 'dm' THEN
        ($1)*10^(-1)
      WHEN $2 = 'ft' THEN
        ($1)*3.048*10^(-1)
      WHEN $2 = 'P' THEN
        ($1)*3.24839385*10^(-1)
      WHEN $2 = 'yd' THEN
        ($1)*9.144*10^(-1)
      WHEN $2 = 'cm' THEN
        ($1)*10^(-2)
      WHEN $2 = 'in' THEN
        ($1)*2.54*10^(-2)
      WHEN $2 = 'mm' THEN
        ($1)*10^(-3)
      WHEN $2 = 'pica' THEN
        ($1)*4.233333*10^(-3)
      WHEN $2 = 'p' THEN
        ($1)*27.069949*10^(-3)
      WHEN $2 = 'mom' THEN
        ($1)*10^(-4)
      WHEN $2 IN ('pt', 'point') THEN
        ($1)*3.527778*10^(-4)
      WHEN $2 = 'mil' THEN
        ($1)*2.54*10^(-5)
      WHEN $2 IN ('µm', 'µ') THEN
        ($1)*10^(-6)
      WHEN $2 = 'twp' THEN
        ($1)*17.639*10^(-6)
      WHEN $2 = 'cal' THEN
        ($1)*254*10^(-6)
      WHEN $2 = 'nm' THEN
        ($1)*10^(-9)
      WHEN $2 = 'Å' THEN
        ($1)*10^(-10)
      WHEN $2 = 'pm' THEN
        ($1)*10^(-12)
      WHEN $2 IN ('fm', 'fermi') THEN
        ($1)*10^(-15)
      WHEN $2 = 'am' THEN
        ($1)*10^(-18)
      WHEN $2 = 'zm' THEN
        ($1)*10^(-21)
      WHEN $2 = 'ym' THEN
        ($1)*10^(-24)
      WHEN $2 IN ('brasse', 'vadem') THEN
        ($1)*1.8288
      WHEN $2 = 'fathom' THEN
        ($1)*1.828804
      WHEN $2 = 'rd' THEN
        ($1)*5.02921
      WHEN $2 = 'dam' THEN
        ($1)*10
      WHEN $2 = 'ch' THEN
        ($1)*20.11684
      WHEN $2 = 'arp' THEN
        ($1)*58.471089295
      WHEN $2 IN ('hm', 'K') THEN
        ($1)*10^2
      WHEN $2 = 'fur' THEN
        ($1)*201.168
      WHEN $2 = 'km' THEN
        ($1)*10^3
      WHEN $2 = 'mi' THEN
        ($1)*1.609344*10^3
      WHEN $2 = 'nautical mi' THEN
        ($1)*1.852*10^3
      WHEN $2 IN ('lieue', 'league') THEN
        ($1)*4.828032*10^3
      WHEN $2 = 'mam' THEN
        ($1)*10^4
      WHEN $2 = 'Mm' THEN
        ($1)*10^6
      WHEN $2 = 'Gm' THEN
        ($1)*10^9
      WHEN $2 = 'ua' THEN
        ($1)*1.495979*10^11
      WHEN $2 = 'Tm' THEN
        ($1)*10^12
      WHEN $2 = 'Pm' THEN
        ($1)*10^15
      WHEN $2 = 'pc' THEN
        ($1)*3.085678*10^16
      WHEN $2 IN ('ly', 'l.y.') THEN
        ($1)*9.4607304725808*10^15
      WHEN $2 = 'Em' THEN
        ($1)*10^18
      WHEN $2 = 'Zm' THEN
        ($1)*10^21
      WHEN $2 = 'Ym' THEN
        ($1)*10^24
      ELSE
        $1
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_length_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 580 (class 1255 OID 18085)
-- Name: fct_cpy_loan_history(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_loan_history(loan_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN

  -- LOAN
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'loans', hstore(l.*) from loans l where l.id = loan_id

    UNION

    select loan_id, 'catalogue_people', hstore(p.*) from catalogue_people p where
      (referenced_relation='loans'  AND record_id = loan_id) OR (referenced_relation='loan_items'  AND record_id in (select id from loan_items l where l.loan_ref = loan_id) )

    UNION

    select loan_id, 'properties', hstore(c.*) from properties c where
      (referenced_relation='loans'  AND record_id = loan_id) OR (referenced_relation='loan_items'  AND record_id in (select id from loan_items l where l.loan_ref = loan_id) )

  );


  --ITEMS
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'loan_items', hstore(l.*) from loan_items l where l.loan_ref = loan_id

    UNION

    select loan_id, 'specimens', hstore(sfl.*) from specimens sfl
      where sfl.id in (select specimen_ref from loan_items l where l.loan_ref = loan_id)
  );

  -- BOTH
  INSERT INTO loan_history (loan_ref, referenced_table, record_line)
  (
    select loan_id, 'people', hstore(p.*) from people p where id in (select (record_line->'people_ref')::int from loan_history where loan_ref = loan_id
      and referenced_table='catalogue_people' and modification_date_time = now())

    UNION

    select loan_id, 'people_addresses', hstore(p.*) from people_addresses p where person_user_ref in (select (record_line->'id')::int from loan_history where loan_ref = loan_id
      and referenced_table='people' and modification_date_time = now())
  );
  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_loan_history(loan_id integer) OWNER TO darwin2;

--
-- TOC entry 1747 (class 1255 OID 18043)
-- Name: fct_cpy_location(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_location() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  --NEW.location := POINT(NEW.latitude, NEW.longitude);
--ftheeten 2017 05 30
NEW.location := POINT(NEW.longitude, NEW.latitude);
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_location() OWNER TO darwin2;

--
-- TOC entry 507 (class 1255 OID 18004)
-- Name: fct_cpy_path(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_path() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
      IF TG_TABLE_NAME::text = 'collections' THEN

        IF NEW.id = 0 THEN
          NEW.parent_ref := null;
        END IF;
        IF NEW.parent_ref IS NULL THEN
          NEW.path :='/';
        ELSE
          EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
        END IF;
      ELSIF TG_TABLE_NAME::text = 'people_relationships' THEN
        SELECT path || NEW.person_1_ref || '/' INTO NEW.path
          FROM people_relationships
          WHERE person_2_ref=NEW.person_1_ref;
        IF NEW.path is NULL THEN
          NEW.path := '/' || NEW.person_1_ref || '/';
        END IF;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF TG_TABLE_NAME::text = 'collections' THEN

        IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
          IF NEW.parent_ref IS NULL THEN
            NEW.path := '/';
          ELSIF COALESCE(OLD.parent_ref,0) = COALESCE(NEW.parent_ref,0) THEN
            RETURN NEW;
          ELSE
            EXECUTE 'SELECT path || id || ' || quote_literal('/') ||' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;

          EXECUTE 'UPDATE ' || quote_ident(TG_TABLE_NAME::text) || ' SET path=replace(path, ' ||  quote_literal(OLD.path || OLD.id || '/') ||' , ' || quote_literal( NEW.path || OLD.id || '/') || ') ' ||
            ' WHERE path like ' || quote_literal(OLD.path || OLD.id || '/%');
        END IF;
      ELSE
        IF NEW.person_1_ref IS DISTINCT FROM OLD.person_1_ref OR NEW.person_2_ref IS DISTINCT FROM OLD.person_2_ref THEN
          SELECT path ||  NEW.person_1_ref || '/' INTO NEW.path FROM people_relationships WHERE person_2_ref=NEW.person_1_ref;

            IF NEW.path is NULL THEN
              NEW.path := '/' || NEW.person_1_ref || '/';
            END IF;
            -- AND UPDATE CHILDRENS
            UPDATE people_relationships SET path=replace(path, OLD.path, NEW.path) WHERE person_1_ref=OLD.person_2_ref;
        END IF;
      END IF;
    END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_path() OWNER TO darwin2;

--
-- TOC entry 508 (class 1255 OID 18005)
-- Name: fct_cpy_path_catalogs(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_path_catalogs() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    IF TG_OP = 'INSERT' AND (TG_TABLE_NAME::text = 'taxonomy' OR
          TG_TABLE_NAME::text = 'lithology' OR
          TG_TABLE_NAME::text = 'lithostratigraphy' OR
          TG_TABLE_NAME::text = 'mineralogy' OR
          TG_TABLE_NAME::text = 'chronostratigraphy') THEN

          IF NEW.parent_ref IS NULL THEN
            NEW.path ='/';
          ELSE
            EXECUTE 'SELECT path || id || ''/'' FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;
    ELSIF TG_OP = 'UPDATE' AND (TG_TABLE_NAME::text = 'taxonomy' OR
        TG_TABLE_NAME::text = 'lithology' OR
        TG_TABLE_NAME::text = 'lithostratigraphy' OR
        TG_TABLE_NAME::text = 'mineralogy' OR
        TG_TABLE_NAME::text = 'chronostratigraphy') THEN

        IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
          IF NEW.parent_ref IS NULL THEN
            NEW.path ='/';
          ELSIF OLD.parent_ref IS NOT DISTINCT FROM NEW.parent_ref THEN
            RETURN NEW;
          ELSE
            EXECUTE 'SELECT  path || id || ''/''  FROM ' || quote_ident(TG_TABLE_NAME::text) || ' WHERE id=' || quote_literal(NEW.parent_ref) INTO STRICT NEW.path;
          END IF;

          EXECUTE 'UPDATE ' || quote_ident(TG_TABLE_NAME::text) || ' SET path=replace(path, ' ||  quote_literal(OLD.path || OLD.id || '/') ||' , ' || quote_literal( NEW.path || OLD.id || '/') || ') ' ||
            ' WHERE path like ' || quote_literal(OLD.path || OLD.id || '/%');
        END IF;
--         RAISE INFO 'nothing diff';
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_path_catalogs() OWNER TO darwin2;

--
-- TOC entry 516 (class 1255 OID 18012)
-- Name: fct_cpy_speed_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_speed_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = 'Kt' THEN
        ($1)*0.51444444444444
      WHEN $2 = 'Beaufort' THEN
        CASE
          WHEN $1 = 0 THEN
            0.13888888888888
          WHEN $1 = 1 THEN
            3*0.27777777777778
          WHEN $1 = 2 THEN
            8*0.27777777777778
          WHEN $1 = 3 THEN
            15*0.27777777777778
          WHEN $1 = 4 THEN
            23.5*0.27777777777778
          WHEN $1 = 5 THEN
            33*0.27777777777778
          WHEN $1 = 6 THEN
            44*0.27777777777778
          WHEN $1 = 7 THEN
            55.5*0.27777777777778
          WHEN $1 = 8 THEN
            68*0.27777777777778
          WHEN $1 = 9 THEN
            81.5*0.27777777777778
          WHEN $1 = 10 THEN
            95.5*0.27777777777778
          WHEN $1 = 11 THEN
            110*0.27777777777778
          ELSE
            120*0.27777777777778
        END
      ELSE
        CASE
          WHEN strpos($2, '/') > 0 THEN
            fct_cpy_length_conversion($1, substr($2, 0, strpos($2, '/')))/fct_cpy_time_conversion(1, substr($2, strpos($2, '/')+1))
          ELSE
            $1
        END
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_speed_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 514 (class 1255 OID 18010)
-- Name: fct_cpy_temperature_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_temperature_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = '°C' THEN
        ($1)+273.15
      WHEN $2 = '°F' THEN
        (($1)+459.67)/1.8
      WHEN $2 = '°Ra' THEN
        ($1)/1.8
      WHEN $2 in ('°Ré', '°r') THEN
        (($1)*5/4)+273.15
      WHEN $2 = '°N' THEN
        (($1)+273.15)*0.33
      WHEN $2 = '°Rø' THEN
        (((($1)-7.5)*40)/21)+273.15
      WHEN $2 = '°De' THEN
        373.15-(($1)*2/3)
      ELSE
        $1
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_temperature_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 515 (class 1255 OID 18011)
-- Name: fct_cpy_time_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_time_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = 'ns' THEN
        ($1)*10^(-9)
      WHEN $2 = 'shake' THEN
        ($1)*10^(-8)
      WHEN $2 = 'µs' THEN
        ($1)*10^(-6)
      WHEN $2 = 'ms' THEN
        ($1)*10^(-3)
      WHEN $2 = 'cs' THEN
        ($1)*10^(-2)
      WHEN $2 = 't' THEN
        ($1)/60
      WHEN $2 = 'ds' THEN
        ($1)*10^(-1)
      WHEN $2 = 'min' THEN
        60*($1)
      WHEN $2 = 'h' THEN
        3600*($1)
      WHEN $2 IN ('d', 'j') THEN
        86400*($1)
      WHEN $2 IN ('y', 'year') THEN
        ($1)*3.1536*10^7
      ELSE
        $1
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_time_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 524 (class 1255 OID 18018)
-- Name: fct_cpy_unified_values(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_unified_values() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  property_line properties%ROWTYPE;
BEGIN
  NEW.lower_value_unified = convert_to_unified(NEW.lower_value, NEW.property_unit, NEW.property_type);
  NEW.upper_value_unified = convert_to_unified(CASE WHEN NEW.upper_value = '' THEN NEW.lower_value ELSE NEW.upper_value END, NEW.property_unit, NEW.property_type);
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_unified_values() OWNER TO darwin2;

--
-- TOC entry 530 (class 1255 OID 18028)
-- Name: fct_cpy_updatecollectionrights(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_updatecollectionrights() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	db_user_type_val integer ;
BEGIN
  IF TG_OP = 'INSERT' THEN
    INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
    (SELECT NEW.id as coll_ref, NEW.main_manager_ref as mgr_ref, 4 as user_type
     UNION
     SELECT NEW.id as coll_ref, user_ref as mgr_ref, db_user_type as user_type
     FROM collections_rights
     WHERE collection_ref = NEW.parent_ref
       AND db_user_type = 4
    );
  ELSIF TG_OP = 'UPDATE' THEN
    IF NEW.main_manager_ref IS DISTINCT FROM OLD.main_manager_ref THEN
      SELECT db_user_type INTO db_user_type_val FROM collections_rights WHERE collection_ref = NEW.id AND user_ref = NEW.main_manager_ref;
      IF FOUND AND db_user_type_val is distinct from 4 THEN
        UPDATE collections_rights
        SET db_user_type = 4
        WHERE collection_ref = NEW.id
          AND user_ref = NEW.main_manager_ref;
      ELSE
        INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
        VALUES(NEW.id,NEW.main_manager_ref,4);
      END IF;
    END IF;
    IF NEW.parent_ref IS DISTINCT FROM OLD.parent_ref THEN
      INSERT INTO collections_rights (collection_ref, user_ref, db_user_type)
      (
        SELECT NEW.id, user_ref, db_user_type
        FROM collections_rights
        WHERE collection_ref = NEW.parent_ref
          AND db_user_type = 4
          AND user_ref NOT IN
            (
              SELECT user_ref
              FROM collections_rights
              WHERE collection_ref = NEW.id
            )
      );
    END IF;
  END IF;

  RETURN NEW;

EXCEPTION
  WHEN OTHERS THEN
    RAISE NOTICE 'An error occured: %', SQLERRM;
    RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_updatecollectionrights() OWNER TO darwin2;

--
-- TOC entry 546 (class 1255 OID 18047)
-- Name: fct_cpy_updatecollinstitutioncascade(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_updatecollinstitutioncascade() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF NEW.institution_ref IS DISTINCT FROM OLD.institution_ref THEN
    UPDATE collections
    SET institution_ref = NEW.institution_ref
    WHERE id != NEW.id
      AND parent_ref = NEW.id;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_updatecollinstitutioncascade() OWNER TO darwin2;

--
-- TOC entry 544 (class 1255 OID 18045)
-- Name: fct_cpy_updatemywidgetscoll(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_updatemywidgetscoll() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  booContinue boolean := false;
BEGIN
  IF TG_TABLE_NAME = 'collections_rights' THEN
    IF TG_OP = 'DELETE' THEN
      booContinue := true;
    ELSE
      IF OLD.collection_ref IS DISTINCT FROM NEW.collection_ref OR OLD.user_ref IS DISTINCT FROM NEW.user_ref THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      /*!!! Whats done is only removing the old collection reference from list of collections set in widgets !!!
        !!! We considered the add of widgets available for someone in a collection still be a manual action !!!
      */
      UPDATE my_widgets
      SET collections = regexp_replace(collections, E'\,' || OLD.collection_ref || E'\,', E'\,', 'g')
      WHERE user_ref = OLD.user_ref
        AND collections ~ (E'\,' || OLD.collection_ref || E'\,');
    END IF;
  END IF;
  IF TG_OP = 'DELETE' THEN
    RETURN OLD;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_updatemywidgetscoll() OWNER TO darwin2;

--
-- TOC entry 532 (class 1255 OID 18030)
-- Name: fct_cpy_updateuserrights(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_updateuserrights() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  db_user_type_val integer ;
  booCollFound boolean;
  booContinue boolean;
BEGIN
  /*When updating main manager ref -> impact potentially db_user_type
    of new user chosen as manager
  */
  IF TG_TABLE_NAME = 'collections' THEN
    /*We take in count only an update
      An insertion as it's creating an entry in collections_rights will trigger this current trigger again ;)
    */
    IF TG_OP = 'UPDATE' THEN
      IF NEW.main_manager_ref IS DISTINCT FROM OLD.main_manager_ref THEN
        UPDATE users
        SET db_user_type = 4
        WHERE id = NEW.main_manager_ref
          AND db_user_type < 4;
      END IF;
    END IF;
  ELSE -- trigger on collections_rights table
    IF TG_OP = 'INSERT' THEN
      /*If user is promoted by inserting her/him
        with a higher db_user_type than she/he is -> promote her/him
      */
      UPDATE users
      SET db_user_type = NEW.db_user_type
      WHERE id = NEW.user_ref
        AND db_user_type < NEW.db_user_type;
    END IF;
    IF TG_OP = 'UPDATE' THEN
      /*First case: replacing a user by an other*/
      IF NEW.user_ref IS DISTINCT FROM OLD.user_ref THEN
        /*Update the user db_user_type chosen as the new one as if it would be an insertion*/
        UPDATE users
        SET db_user_type = NEW.db_user_type
        WHERE id = NEW.user_ref
          AND db_user_type < NEW.db_user_type;
        /*Un promote the user replaced if necessary*/
        UPDATE users
          SET db_user_type = subq.db_user_type_max
          FROM (
                SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
                FROM collections_rights
                WHERE user_ref = OLD.user_ref
              ) subq
          WHERE id = OLD.user_ref
            AND db_user_type != 8;
      END IF;
      IF NEW.db_user_type IS DISTINCT FROM OLD.db_user_type THEN
        /* Promotion */
        IF NEW.db_user_type > OLD.db_user_type THEN
          UPDATE users
          SET db_user_type = NEW.db_user_type
          WHERE id = NEW.user_ref
            AND db_user_type < NEW.db_user_type;
        /* Unpromotion */
        ELSE
          UPDATE users
          SET db_user_type = subq.db_user_type_max
          FROM (
                SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
                FROM collections_rights
                WHERE user_ref = NEW.user_ref
              ) subq
          WHERE id = NEW.user_ref
            AND db_user_type != 8;
        END IF;
      END IF;
    END IF;
    IF TG_OP = 'DELETE' THEN
      IF OLD.db_user_type >=4 THEN
        SELECT true
        INTO booCollFound
        FROM collections
        WHERE id = OLD.collection_ref
          AND main_manager_ref = OLD.user_ref;
        IF FOUND THEN
          RAISE EXCEPTION 'You try to delete a manager who is still defined as a main manager of the current collection';
        END IF;
      END IF;
      UPDATE users
      SET db_user_type = subq.db_user_type_max
      FROM (
            SELECT COALESCE(MAX(db_user_type),1) as db_user_type_max
            FROM collections_rights
            WHERE user_ref = OLD.user_ref
           ) subq
      WHERE id = OLD.user_ref
        AND db_user_type != 8;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_cpy_updateuserrights() OWNER TO darwin2;

--
-- TOC entry 517 (class 1255 OID 18013)
-- Name: fct_cpy_volume_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_volume_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = 'l' THEN
        ($1)*10^(-3)
      WHEN $2 = 'cm³' OR $2 = 'ml' THEN
        ($1)*10^(-6)
      WHEN $2 = 'mm³' OR $2 = 'µl' THEN
        ($1)*10^(-9)
      WHEN $2 = 'µm³' THEN
        ($1)*10^(-18)
      WHEN $2 = 'km³' THEN
        ($1)*10^(9)
      WHEN $2 = 'Ml' THEN
        ($1)*10^(3)
      WHEN $2 = 'hl' THEN
        ($1)*10
      ELSE
        $1
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_volume_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 518 (class 1255 OID 18014)
-- Name: fct_cpy_weight_conversion(real, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_cpy_weight_conversion(property real, property_unit text) RETURNS real
    LANGUAGE sql STABLE
    AS $_$
  SELECT  CASE
      WHEN $2 = 'hg' THEN
        ($1)*10^(2)
      WHEN $2 = 'kg' THEN
        ($1)*10^(3)
      WHEN $2 = 'Mg' OR $2 = 'ton' THEN
        ($1)*10^(6)
      WHEN $2 = 'dg' THEN
        ($1)*10^(-1)
      WHEN $2 = 'cg' THEN
        ($1)*10^(-2)
      WHEN $2 = 'mg' THEN
        ($1)*10^(-3)
      WHEN $2 = 'lb' OR $2 = 'lbs' OR $2 = 'pound' THEN
        ($1)*453.59237
      WHEN $2 = 'ounce' THEN
        ($1)*28.349523125
      WHEN $2 = 'grain' THEN
        ($1)*6.479891*10^(-2)
      ELSE
        $1
    END::real;
$_$;


ALTER FUNCTION darwin2.fct_cpy_weight_conversion(property real, property_unit text) OWNER TO darwin2;

--
-- TOC entry 552 (class 1255 OID 18053)
-- Name: fct_del_in_dict(text, text, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_del_in_dict(ref_relation text, ref_field text, old_value text, new_val text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  result boolean;
  query_str text;
BEGIN
  IF old_value IS null OR old_value IS NOT DISTINCT FROM new_val THEN
    RETURN TRUE;
  END IF;
  query_str := ' SELECT EXISTS( SELECT 1 from ' || quote_ident(ref_relation) || ' where ' || quote_ident(ref_field) || ' = ' || quote_literal(old_value) || ');';
  execute query_str into result;

  IF result = false THEN
    DELETE FROM flat_dict where
          referenced_relation = ref_relation
          AND dict_field = ref_field
          AND dict_value = old_value;
  END IF;
  RETURN TRUE;
END;
$$;


ALTER FUNCTION darwin2.fct_del_in_dict(ref_relation text, ref_field text, old_value text, new_val text) OWNER TO darwin2;

--
-- TOC entry 553 (class 1255 OID 18054)
-- Name: fct_del_in_dict_dept(text, text, text, text, text, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_del_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text, depending_old_value text, depending_new_value text, depending_field text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  result boolean;
  query_str text;
BEGIN
  IF old_value is NULL OR ( old_value IS NOT DISTINCT FROM new_val AND depending_old_value IS NOT DISTINCT FROM depending_new_value ) THEN
    RETURN TRUE;
  END IF;
  query_str := ' SELECT EXISTS( SELECT id from ' || quote_ident(ref_relation) || ' where ' || quote_ident(ref_field) || ' = ' || quote_literal(old_value)
  || ' AND ' || quote_ident(depending_field) || ' = ' || quote_literal(depending_old_value) || ' );';
  execute query_str into result;

  IF result = false THEN
    DELETE FROM flat_dict where
          referenced_relation = ref_relation
          AND dict_field = ref_field
          AND dict_value = old_value
          AND dict_depend = depending_old_value;
  END IF;
  RETURN TRUE;
END;
$$;


ALTER FUNCTION darwin2.fct_del_in_dict_dept(ref_relation text, ref_field text, old_value text, new_val text, depending_old_value text, depending_new_value text, depending_field text) OWNER TO darwin2;

--
-- TOC entry 497 (class 1255 OID 17995)
-- Name: fct_explode_array(anyarray); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_explode_array(in_array anyarray) RETURNS SETOF anyelement
    LANGUAGE sql IMMUTABLE
    AS $_$
    select ($1)[s] from generate_series(1,array_upper($1, 1)) as s;
$_$;


ALTER FUNCTION darwin2.fct_explode_array(in_array anyarray) OWNER TO darwin2;

--
-- TOC entry 543 (class 1255 OID 18044)
-- Name: fct_filter_encodable_row(character varying, character varying, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_filter_encodable_row(ids character varying, col_name character varying, user_id integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $$
DECLARE
  rec_id integer;
BEGIN
    IF col_name = 'spec_ref' THEN
      FOR rec_id IN SELECT id FROM specimens WHERE id in (select X::int from regexp_split_to_table(ids, ',' ) as X)
            AND collection_ref in (select X FROM fct_search_authorized_encoding_collections(user_id) as X)
      LOOP
        return next rec_id;
      END LOOP;
    END IF;

END;
$$;


ALTER FUNCTION darwin2.fct_filter_encodable_row(ids character varying, col_name character varying, user_id integer) OWNER TO darwin2;

--
-- TOC entry 549 (class 1255 OID 18050)
-- Name: fct_find_tax_level(text, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_find_tax_level(tax_path text, searched_level integer) RETURNS integer
    LANGUAGE sql STABLE
    AS $_$
   SELECT id FROM taxonomy where  level_ref = $2 and id in (select i::int from regexp_split_to_table($1, E'\/') as i where i != '');
$_$;


ALTER FUNCTION darwin2.fct_find_tax_level(tax_path text, searched_level integer) OWNER TO darwin2;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 281 (class 1259 OID 17690)
-- Name: staging; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging (
    id integer NOT NULL,
    import_ref integer NOT NULL,
    create_taxon boolean DEFAULT false NOT NULL,
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
    status public.hstore DEFAULT ''::public.hstore NOT NULL,
    to_import boolean DEFAULT false,
    object_name text,
    part_count_males_min integer,
    part_count_males_max integer,
    part_count_females_min integer,
    part_count_females_max integer,
    part_count_juveniles_min integer,
    part_count_juveniles_max integer
);


ALTER TABLE darwin2.staging OWNER TO darwin2;

--
-- TOC entry 558 (class 1255 OID 18060)
-- Name: fct_imp_checker_catalogue(staging, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_catalogue(line staging, catalogue_table text, prefix text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  result_nbr integer :=0;
  ref_record RECORD;
  rec_id integer := null;
  line_store hstore;
  field_name text;
  field_level_name text;
  test text;
  ref refcursor;
BEGIN
    line_store := hstore(line);
    field_name := prefix || '_name';
    field_name := line_store->field_name;
    field_level_name := prefix || '_level_name';
    field_level_name := coalesce(line_store->field_level_name,'');

    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name = ' || quote_literal( field_name) || ' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!

      PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
      RETURN true;
    END IF;

    IF result_nbr >= 2 THEN
      UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
      RETURN true;
    END IF;

    CLOSE ref;

  /*** Then CHECK fuzzy name ***/

  result_nbr := 0;
  IF catalogue_table = 'mineralogy' THEN
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed like fullToIndex(' || quote_literal( field_name) || ') AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  ELSE
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed like fullToIndex(' || quote_literal( field_name) || ') || ''%'' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  END IF ;

  IF result_nbr = 1 THEN -- It's Ok!
    PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
    RETURN true;
  END IF;

  IF result_nbr >= 2 THEN
    UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
    RETURN true;
  END IF;

  IF result_nbr = 0 THEN
    UPDATE staging SET status = (status || (prefix => 'not_found')) where id=line.id;
    RETURN true;
  END IF;

  CLOSE ref;
  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_catalogue(line staging, catalogue_table text, prefix text) OWNER TO darwin2;

--
-- TOC entry 563 (class 1255 OID 18062)
-- Name: fct_imp_checker_catalogues_parents(staging, integer, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_catalogues_parents(line staging, rec_id integer, catalogue_table text, prefix text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  result_nbr integer :=0;
  row_record record;
  lvl_name varchar;
  lvl_value varchar;
  rec_parents hstore;
  line_store hstore;
  field_name text;
BEGIN
  line_store := hstore(line);
  field_name := prefix || '_parents';
  rec_parents := line_store->field_name;

  IF rec_parents is not null AND rec_parents != ''::hstore  AND rec_id is not null THEN
    EXECUTE 'select * from '|| quote_ident(catalogue_table) || ' where id = ' || rec_id into row_record ;

    FOR lvl_name in SELECT s FROM fct_explode_array(akeys(rec_parents)) as s
    LOOP
      lvl_value := rec_parents->lvl_name;
      EXECUTE 'SELECT count(*) from ' || quote_ident(catalogue_table) || ' t
        INNER JOIN catalogue_levels c on t.level_ref = c.id
        WHERE level_sys_name = ' || quote_literal(lvl_name) || ' AND
          name_indexed like fullToIndex( ' || quote_literal(lvl_value) || '  ) || ''%''
          AND ' || quote_literal(row_record.path) || 'like t.path || t.id || ''/%'' ' INTO result_nbr;
      IF result_nbr = 0 THEN
        EXECUTE 'UPDATE staging SET status = (status || ('|| quote_literal(prefix) || ' => ''bad_hierarchy'')), ' || prefix || '_ref = null where id=' || line.id;
        RETURN TRUE;
      END IF;
    END LOOP;
  END IF;

  EXECUTE 'UPDATE staging SET status = delete(status, ' || quote_literal(prefix) ||'), ' || prefix|| '_ref = ' || rec_id || ' where id=' || line.id;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_catalogues_parents(line staging, rec_id integer, catalogue_table text, prefix text) OWNER TO darwin2;

--
-- TOC entry 566 (class 1255 OID 18065)
-- Name: fct_imp_checker_expeditions(staging, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_expeditions(line staging, import boolean DEFAULT false) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_rec integer :=0;
BEGIN
  IF line.expedition_name is null OR line.expedition_name ='' OR line.expedition_ref is not null THEN
    RETURN true;
  END IF;

  select id into ref_rec from expeditions where name_indexed = fulltoindex(line.expedition_name) and
    expedition_from_date = COALESCE(line.expedition_from_date,'01/01/0001') AND
    expedition_to_date = COALESCE(line.expedition_to_date,'31/12/2038');
  IF NOT FOUND THEN
      IF import THEN
        INSERT INTO expeditions (name, expedition_from_date, expedition_to_date, expedition_from_date_mask,expedition_to_date_mask)
        VALUES (
          line.expedition_name, COALESCE(line.expedition_from_date,'01/01/0001'),
          COALESCE(line.expedition_to_date,'31/12/2038'), COALESCE(line.expedition_from_date_mask,0),
          COALESCE(line.expedition_to_date_mask,0)
        )
        RETURNING id INTO line.expedition_ref;

        ref_rec := line.expedition_ref;
        PERFORM fct_imp_checker_staging_info(line, 'expeditions');
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'expedition'), expedition_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_expeditions(line staging, import boolean) OWNER TO darwin2;

--
-- TOC entry 1717 (class 1255 OID 233127)
-- Name: fct_imp_checker_gtu(staging, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_gtu(line staging, import boolean DEFAULT false) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
BEGIN
  IF line.gtu_ref is not null THEN
    RETURN true;
  END IF;
  IF (line.gtu_code is null OR line.gtu_code  = '') AND (line.gtu_from_date is null OR line.gtu_code  = '') AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
    RETURN true;
  END IF;

    select id into ref_rec from gtu g where
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      --ftheeten 2016 07 11
      --gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      --gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038') AND
      fullToIndex(code) = fullToIndex(line.gtu_code)

      AND id != 0 LIMIT 1;



  IF NOT FOUND THEN
      IF import THEN
        INSERT into gtu
          (code, latitude, longitude, lat_long_accuracy, elevation, elevation_accuracy)
        VALUES
          (COALESCE(line.gtu_code,'import/'|| line.import_ref || '/' || line.id ), 
          --ftheeten 2016 07 1
          --COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          --COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038'), 
          line.gtu_latitude, line.gtu_longitude, line.gtu_lat_long_accuracy, line.gtu_elevation, line.gtu_elevation_accuracy)
        RETURNING id INTO line.gtu_ref;
        ref_rec := line.gtu_ref;
        FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
            Values(ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value );
        --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN unique_violation THEN
            RAISE EXCEPTION 'An error occured: %', SQLERRM;
        END ;
        END LOOP ;
        PERFORM fct_imp_checker_staging_info(line, 'gtu');
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_gtu(line staging, import boolean) OWNER TO darwin2;

--
-- TOC entry 1716 (class 1255 OID 18066)
-- Name: fct_imp_checker_gtu_20160713(staging, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_gtu_20160713(line staging, import boolean DEFAULT false) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
BEGIN
  IF line.gtu_ref is not null THEN
    RETURN true;
  END IF;
  IF (line.gtu_code is null OR line.gtu_code  = '') AND (line.gtu_from_date is null OR line.gtu_code  = '') AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
    RETURN true;
  END IF;

    select id into ref_rec from gtu g where
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038') AND
      fullToIndex(code) = fullToIndex(line.gtu_code)

      AND id != 0 LIMIT 1;



  IF NOT FOUND THEN
      IF import THEN
        INSERT into gtu
          (code, gtu_from_date_mask, gtu_from_date,gtu_to_date_mask, gtu_to_date, latitude, longitude, lat_long_accuracy, elevation, elevation_accuracy)
        VALUES
          (COALESCE(line.gtu_code,'import/'|| line.import_ref || '/' || line.id ), COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038')
          , line.gtu_latitude, line.gtu_longitude, line.gtu_lat_long_accuracy, line.gtu_elevation, line.gtu_elevation_accuracy)
        RETURNING id INTO line.gtu_ref;
        ref_rec := line.gtu_ref;
        FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
            Values(ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value );
        --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN unique_violation THEN
            RAISE EXCEPTION 'An error occured: %', SQLERRM;
        END ;
        END LOOP ;
        PERFORM fct_imp_checker_staging_info(line, 'gtu');
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_gtu_20160713(line staging, import boolean) OWNER TO darwin2;

--
-- TOC entry 565 (class 1255 OID 18064)
-- Name: fct_imp_checker_igs(staging, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_igs(line staging, import boolean DEFAULT false) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_rec integer :=0;
BEGIN
  IF line.ig_num is null OR  line.ig_num = '' OR line.ig_ref is not null THEN
    RETURN true;
  END IF;

  select id into ref_rec from igs where ig_num = line.ig_num ;
  IF NOT FOUND THEN
    IF import THEN
        INSERT INTO igs (ig_num, ig_date_mask, ig_date)
        VALUES (line.ig_num,  COALESCE(line.ig_date_mask,line.ig_date_mask,'0'), COALESCE(line.ig_date,'01/01/0001'))
        RETURNING id INTO line.ig_ref;

        ref_rec := line.ig_ref;
        PERFORM fct_imp_checker_staging_info(line, 'igs');
    ELSE
    --UPDATE staging SET status = (status || ('igs' => 'not_found')), ig_ref = null where id=line.id;
      RETURN TRUE;
    END IF;
  END IF;

  UPDATE staging SET status = delete(status,'igs'), ig_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_igs(line staging, import boolean) OWNER TO darwin2;

--
-- TOC entry 559 (class 1255 OID 18061)
-- Name: fct_imp_checker_manager(staging); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_manager(line staging) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
BEGIN

  IF line.taxon_name IS NOT NULL AND line.taxon_name is distinct from '' AND line.taxon_ref is null THEN
    PERFORM fct_imp_create_catalogues_and_parents(line, 'taxonomy','taxon');
    PERFORM fct_imp_checker_catalogue(line,'taxonomy','taxon');
  END IF;

  IF line.chrono_name IS NOT NULL AND line.chrono_name is distinct from '' AND line.chrono_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'chronostratigraphy','chrono');
  END IF;

  IF line.lithology_name IS NOT NULL AND line.lithology_name is distinct from '' AND line.lithology_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithology','lithology');
  END IF;

  IF line.mineral_name IS NOT NULL AND line.mineral_name is distinct from '' AND line.mineral_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'mineralogy','mineral');
  END IF;

  IF line.litho_name IS NOT NULL AND line.litho_name is distinct from '' AND line.litho_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithostratigraphy','litho');
  END IF;



  PERFORM fct_imp_checker_igs(line);
  PERFORM fct_imp_checker_expeditions(line);
  PERFORM fct_imp_checker_gtu(line);
  PERFORM fct_imp_checker_people(line);
  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_manager(line staging) OWNER TO darwin2;

--
-- TOC entry 570 (class 1255 OID 18069)
-- Name: fct_imp_checker_people(staging); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_people(line staging) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_record integer :=0;
  cnt integer :=-1;
  p_name text;
  merge_status integer :=1;
  ident_line RECORD;
  people_line RECORD ;
BEGIN


  --  Donators and collectors

  FOR people_line IN select * from staging_people WHERE referenced_relation ='staging' AND record_id = line.id
  LOOP
    IF people_line.people_ref is not null THEN
      continue;
    END IF;
    SELECT fct_look_for_people(people_line.formated_name) into ref_record;
    CASE ref_record
      WHEN -1,0 THEN merge_status := -1 ;
      --WHEN 0 THEN merge_status := 0;
      ELSE
        UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
    END CASE;
  END LOOP;
  IF merge_status = 1 THEN
    UPDATE staging SET status = delete(status,'people') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('people' => 'people')) where id= line.id;
  END IF;

  -- Identifiers

  merge_status := 1 ;
  FOR ident_line in select * from identifications where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='identifications' AND record_id = ident_line.id
    LOOP
      IF people_line.people_ref is not null THEN
        continue;
      END IF;
      SELECT fct_look_for_people(people_line.formated_name) into ref_record;
      CASE ref_record
        WHEN -1,0 THEN merge_status := -1 ;
        --WHEN 0 THEN merge_status := 0;
        ELSE
          UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
      END CASE;
    END LOOP;
  END LOOP;

  IF merge_status = 1 THEN
    UPDATE staging SET status = delete(status,'identifiers') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('identifiers' => 'people')) where id= line.id;
  END IF;

  -- Sequencers

  merge_status := 1 ;
  FOR ident_line in select * from collection_maintenance where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='collection_maintenance' AND record_id = ident_line.id
    LOOP
      IF people_line.people_ref is not null THEN
        continue;
      END IF;
      SELECT fct_look_for_people(people_line.formated_name) into ref_record;
      CASE ref_record
        WHEN -1,0 THEN merge_status := -1 ;
        --WHEN 0 THEN merge_status := 0;
        ELSE
          UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
      END CASE;
    END LOOP;
  END LOOP;

  IF merge_status = 1 THEN
    UPDATE staging SET status = delete(status,'operator') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('operator' => 'people')) where id= line.id;
  END IF;

  /**********
  * Institution
  **********/
  IF line.institution_name IS NOT NULL and line.institution_name  != '' AND line.institution_ref is null THEN
    SELECT fct_look_for_institution(line.institution_name) into ref_record ;
  CASE ref_record
  WHEN -1 THEN
    UPDATE staging SET status = (status || ('institution' => 'too_much')) where id= line.id;
  WHEN 0 THEN
    UPDATE staging SET status = (status || ('institution' => 'not_found')) where id= line.id;
  ELSE
    UPDATE staging SET status = delete(status,'institution'), institution_ref = ref_record where id=line.id;
      END CASE;
  END IF;

  /**********
  * Institution in staging_relationship
  **********/
  FOR ident_line in select * from staging_relationship where record_id = line.id
  LOOP
    IF ident_line.institution_name IS NOT NULL and ident_line.institution_name  != '' AND ident_line.institution_ref is null AND ident_line.institution_name  != 'Not defined' THEN
      SELECT fct_look_for_institution(ident_line.institution_name) into ref_record;
      CASE ref_record
      WHEN -1 THEN
        UPDATE staging SET status = (status || ('institution_relationship' => 'too_much')) where id= line.id;
      WHEN 0 THEN
        UPDATE staging SET status = (status || ('institution_relationship' => 'not_found')) where id= line.id;
        ELSE
          UPDATE staging_relationship SET institution_ref = ref_record WHERE id=ident_line.id ;
          UPDATE staging SET status = delete(status,'institution_relationship') where id=line.id;
      END CASE;
    END IF;
  END LOOP;

  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_people(line staging) OWNER TO darwin2;

--
-- TOC entry 574 (class 1255 OID 18075)
-- Name: fct_imp_checker_staging_info(staging, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_staging_info(line staging, st_type text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  info_line staging_info ;
  record_line RECORD ;
BEGIN

  FOR info_line IN select * from staging_info i WHERE i.staging_ref = line.id AND i.referenced_relation = st_type
  LOOP
    BEGIN
    CASE info_line.referenced_relation
      WHEN 'gtu' THEN
        IF line.gtu_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='gtu', record_id=line.gtu_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'taxonomy' THEN
        IF line.taxon_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='taxonomy', record_id=line.taxon_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'expeditions' THEN
        IF line.expedition_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='expeditions', record_id=line.expedition_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'lithostratigraphy' THEN
        IF line.litho_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='lithostratigraphy', record_id=line.litho_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'lithology' THEN
        IF line.lithology_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='lithology', record_id=line.lithology_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'chronostratigraphy' THEN
        IF line.chrono_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='chronostratigraphy', record_id=line.chrono_ref where id=record_line.id ;
          END LOOP ;
        END IF;
      WHEN 'mineralogy' THEN
        IF line.mineral_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='mineralogy', record_id=line.mineral_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'igs' THEN
        IF line.ig_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='igs', record_id=line.ig_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      ELSE continue ;
      END CASE ;
      EXCEPTION WHEN unique_violation THEN
        RAISE NOTICE 'An error occured: %', SQLERRM;
      END ;
  END LOOP;
  DELETE FROM staging_info WHERE staging_ref = line.id ;
  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_staging_info(line staging, st_type text) OWNER TO darwin2;

--
-- TOC entry 575 (class 1255 OID 18076)
-- Name: fct_imp_checker_staging_relationship(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_checker_staging_relationship() RETURNS integer[]
    LANGUAGE plpgsql
    AS $$
DECLARE
  relation_line RECORD ;
  specimen_ref INTEGER ;
  id_array integer ARRAY ;
BEGIN

  FOR relation_line IN select sr.*, s.spec_ref from staging_relationship sr, staging s WHERE sr.record_id = s.id AND s.spec_ref IS NOT NULL
  LOOP
    IF relation_line.staging_related_ref IS NOT NULL THEN
      SELECT spec_ref INTO specimen_ref FROM staging where id=relation_line.staging_related_ref ;
      IF specimen_ref IS NULL THEN
        id_array := array_append(id_array, relation_line.record_id);
        continue ;
      ELSE
        INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, specimen_related_ref, institution_ref)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.spec_ref, relation_line.relationship_type, unit_type, specimen_ref, institution_ref
        from staging_relationship where id=relation_line.id AND staging_related_ref=relation_line.staging_related_ref;
      END IF;
    ELSE
    INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, institution_ref,taxon_ref, mineral_ref, source_name,
    source_id, quantity, unit)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.spec_ref, relation_line.relationship_type, unit_type, institution_ref,
        taxon_ref, mineral_ref, source_name, source_id, quantity, unit
        from staging_relationship where id=relation_line.id ;
    END IF ;
    DELETE FROM staging_relationship WHERE id = relation_line.id ;
  END LOOP;
  RETURN id_array;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_checker_staging_relationship() OWNER TO darwin2;

--
-- TOC entry 564 (class 1255 OID 18063)
-- Name: fct_imp_create_catalogues_and_parents(staging, text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_imp_create_catalogues_and_parents(line staging, catalogue_table text, prefix text) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
DECLARE
  result_nbr integer :=0;
  row_record record;
  lvl_name varchar;
  lvl_value varchar;
  lvl_id integer;

  old_parent_id integer;
  parent_id integer;
  rec_parents hstore;
  line_store hstore;
  field_name1 text;
  field_name2 text;

  tmp text;
BEGIN
  line_store := hstore(line);
  field_name1 := prefix || '_parents';
  rec_parents := line_store->field_name1;

  IF line.create_taxon AND rec_parents is not null AND rec_parents != ''::hstore  THEN
    BEGIN
      field_name2 := prefix || '_name';
      field_name1 := prefix || '_level_name';

      IF line_store->field_name2 != '' THEN
        rec_parents = rec_parents || hstore(line_store->field_name1, line_store->field_name2);
      END IF;

      FOR row_record in SELECT s.key as lvl_name, s.value as lvl_value, l.id as lvl_id
        FROM each(rec_parents) as s LEFT JOIN catalogue_levels l on s.key = l.level_sys_name
        ORDER BY l.level_order ASC
      LOOP
        old_parent_id := parent_id;
        EXECUTE 'SELECT count(*), min(t.id) as id from ' || quote_ident(catalogue_table) || ' t
          INNER JOIN catalogue_levels c on t.level_ref = c.id
          WHERE level_sys_name = ' || quote_literal(row_record.lvl_name) || ' AND
            name_indexed like fullToIndex( ' || quote_literal(row_record.lvl_value) || '  ) || ''%'' '
          INTO result_nbr, parent_id;

        IF result_nbr = 0 THEN
          IF old_parent_id IS NULL THEN
            RAISE EXCEPTION 'Unable to create taxon with no common parents';
          END IF;
          EXECUTE 'INSERT INTO ' || quote_ident(catalogue_table) || '  (name, level_ref, parent_ref) VALUES
            (' || quote_literal(row_record.lvl_value) || ', ' ||
            quote_literal(row_record.lvl_id) ||', '|| quote_literal(old_parent_id) ||') returning ID' into parent_id ;

          -- We are at the last level
          IF lvl_name = line_store->field_name1 THEN
            PERFORM fct_imp_checker_staging_info(line, 'taxonomy');
          END IF;
        END IF;
      END LOOP;

    EXCEPTION WHEN OTHERS THEN
      UPDATE staging set create_taxon = false where id = line.id;
      RETURN TRUE;
    END;
  END IF;
  RETURN true;
END;
$$;


ALTER FUNCTION darwin2.fct_imp_create_catalogues_and_parents(line staging, catalogue_table text, prefix text) OWNER TO darwin2;

--
-- TOC entry 1718 (class 1255 OID 233125)
-- Name: fct_importer_abcd(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_abcd(req_import_ref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
  --ftheeten 2016 08 26
  array_categories varchar array;
  array_specimen_parts varchar array;
  --array_complete varchar array;
  --array_institution_refs integer array;
  array_buildings varchar array;
  array_floors varchar array;
  array_rooms varchar array;
  array_rows varchar array;
  array_cols varchar array;
  array_shelves varchar array;
  array_containers varchar array;
  array_sub_containers varchar array;
  array_containers_type varchar array;
  array_sub_containers_type varchar array;
  array_containers_storage varchar array;
  array_sub_containers_storage varchar array;
    --array_surnumerary varchar array;
        array_statuses varchar array;
            array_object_name varchar array;
  process_multiple_storage boolean;
  array_lengths integer array;
  max_length integer;
  --
  --debug varchar;
BEGIN
  max_length:=null;
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  --BEGIN LOOP 1
  LOOP
    --BEGIN TRANSACTION 1
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    --BEGIN IF 1
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id,  collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          --these 2 fields not handled in staging ftheeten 2016 08 26 
         -- complete,surnumerary,
          specimen_count_min, specimen_count_max
		--ftheeten 2016 06 22
		,specimen_count_males_min, specimen_count_males_max,specimen_count_females_min, specimen_count_females_max
        --ftheeten 2016 12 08
        ,gtu_from_date_mask,
       gtu_from_date,
       gtu_to_date_mask,
       gtu_to_date
          )
      VALUES (rec_id,  all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'),
        --COALESCE(line.complete,true),COALESCE(line.surnumerary,false),
        COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1)
        --ftheeten 2016 06 22
        ,COALESCE(line.part_count_males_min,0),COALESCE(line.part_count_males_max,0),COALESCE(line.part_count_females_min,0),COALESCE(line.part_count_females_max,0)
        --ftheeten 2016 12 08
        , COALESCE(line.gtu_from_date_mask,0),
          COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0),
          COALESCE(line.gtu_to_date, '31/12/2038')
      );
      --BEGIN LOOP 2
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
       --END LOOP 2
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      --BEGIN IF 3
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      --END IF 3
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;
      --BEGIN LOOP 3
      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      --END LOOP 3
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    --END IF 1
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    --ftheeten to do handle array of storage
    process_multiple_storage:=false;
      
    array_categories :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.category),''), '(^\[|\]$)','','g'),'(\|)');
    array_specimen_parts :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.part),''), '(^\[|\]$)','','g'),'(\|)');
     -- array_institution_refs :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.institution_ref),''), '(^\[|\]$)','','g'),'(\|)');
    array_buildings :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.building),''), '(^\[|\]$)','','g'),'(\|)');
    array_floors :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.floor),''), '(^\[|\]$)','','g'),'(\|)');
    array_rooms :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.room),''), '(^\[|\]$)','','g'),'(\|)');
    array_rows :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.row),''), '(^\[|\]$)','','g'),'(\|)');
    array_cols :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.col),''), '(^\[|\]$)','','g'),'(\|)');
    array_shelves :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.shelf),''), '(^\[|\]$)','','g'),'(\|)');
    array_containers :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.container),''), '(^\[|\]$)','','g'),'(\|)');
    array_sub_containers :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.sub_container),''), '(^\[|\]$)','','g'),'(\|)');
    array_containers_type :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.container_type),''), '(^\[|\]$)','','g'),'(\|)');
    array_sub_containers_type :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.sub_container_type),''), '(^\[|\]$)','','g'),'(\|)');
    array_containers_storage :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.container_storage),''), '(^\[|\]$)','','g'),'(\|)');
    array_sub_containers_storage :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.sub_container_storage),''), '(^\[|\]$)','','g'),'(\|)');
    array_statuses :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.specimen_status),''), '(^\[|\]$)','','g'),'(\|)');
    array_object_name :=regexp_split_to_array(regexp_replace(NULLIF(trim(line.object_name),''), '(^\[|\]$)','','g'),'(\|)');
                
    ----work
    array_lengths:=ARRAY[]::INT[];
    array_lengths:=array_lengths||ARRAY_LENGTH( array_categories,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_specimen_parts ,1);
    --array_lengths:=array_lengths||ARRAY_LENGTH(array_institution_refs ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_buildings,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_floors,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_rooms,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_rows ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_cols ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_shelves ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_containers ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_sub_containers,1);
    array_lengths:= array_lengths||ARRAY_LENGTH(array_containers_type ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_sub_containers_type ,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_containers_storage,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_sub_containers_storage,1);
    array_lengths:=array_lengths||ARRAY_LENGTH( array_statuses,1);
    array_lengths:=array_lengths||ARRAY_LENGTH(array_object_name ,1);
     --max_length:=MAX(UNNEST(array_lengths));    
    max_length:=MAX(x) FROM UNNEST(array_lengths) x;
    /*
        debug:=', '||COALESCE(ARRAY_LENGTH( array_categories,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_specimen_parts ,1)::varchar,'');
    --array_lengths:=array_lengths||ARRAY_LENGTH(array_institution_refs ,1);
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_buildings,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_floors,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_rooms,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_rows ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_cols ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_shelves ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_containers ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_sub_containers,1)::varchar,'');
    debug:= debug||', '||COALESCE(ARRAY_LENGTH(array_containers_type ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_sub_containers_type ,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_containers_storage,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_sub_containers_storage,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH( array_statuses,1)::varchar,'');
    debug:=debug||', '||COALESCE(ARRAY_LENGTH(array_object_name ,1)::varchar,'');
    debug:=debug||' x'||','||(COALESCE(ARRAY_LENGTH( array_categories,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_specimen_parts ,1),max_length))::varchar||
    ','||(COALESCE(ARRAY_LENGTH( array_buildings,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_floors ,1),max_length))::varchar||
    ','||(COALESCE(ARRAY_LENGTH( array_rooms,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_rows ,1),max_length))::varchar||
    ','||(COALESCE(ARRAY_LENGTH( array_cols,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_shelves ,1),max_length))::varchar||
    ','||(COALESCE(ARRAY_LENGTH( array_containers,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_sub_containers ,1),max_length))::varchar||
    ','||(COALESCE(ARRAY_LENGTH( array_containers_type,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_sub_containers_type ,1),max_length))::varchar||
     ','||(COALESCE(ARRAY_LENGTH( array_containers_storage,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_sub_containers_storage ,1),max_length))::varchar||
     ','||(COALESCE(ARRAY_LENGTH( array_statuses,1),max_length)::varchar||',' ||COALESCE(ARRAY_LENGTH(array_object_name ,1),max_length))::varchar;
    */
    --IF 2
    IF 
    (COALESCE(ARRAY_LENGTH( array_categories,1),max_length)=COALESCE(ARRAY_LENGTH(array_specimen_parts ,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH( array_buildings,1),max_length)= COALESCE(ARRAY_LENGTH( array_floors,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH( array_rooms,1),max_length)=COALESCE(ARRAY_LENGTH(array_rows ,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH(array_cols ,1),max_length)=COALESCE(ARRAY_LENGTH(array_shelves ,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH(array_containers ,1),max_length)=COALESCE(ARRAY_LENGTH( array_sub_containers,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH(array_containers_type ,1),max_length)=COALESCE(ARRAY_LENGTH(array_sub_containers_type ,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH( array_containers_storage,1),max_length)=COALESCE(ARRAY_LENGTH( array_sub_containers_storage,1),max_length))
    =
    (COALESCE(ARRAY_LENGTH( array_statuses,1),max_length)= COALESCE(ARRAY_LENGTH(array_object_name ,1),max_length))
    THEN
        process_multiple_storage:=true;
        --BEGIN LOOP 3
        FOR i in 1..max_length LOOP
            INSERT INTO darwin2.storage_parts(
                            category, 
                            specimen_ref, 
                            specimen_part, 
                            institution_ref, 
                            building, 
                            floor, 
                            room, 
                            "row", 
                            col, 
                            shelf, 
                            container, 
                            sub_container, 
                            container_type, 
                            sub_container_type, 
                            container_storage, 
                            sub_container_storage, 
                            surnumerary, 
                            object_name, 
                            specimen_status, 
                            complete
                            --, debug
                            )
                    VALUES ( 
                            COALESCE(array_categories[i],'physical'), 
                            rec_id,
                             COALESCE(array_specimen_parts[i],'specimen'), 
                            line.institution_ref, 
                            array_buildings[i], 
                            array_floors[i], 
                            array_rooms[i], 
                            array_rows[i], 
                            array_cols[i], 
                            array_shelves[i], 
                            array_containers[i], 
                            array_sub_containers[i], 
                            COALESCE(array_containers_type[i],'container'), 
                            COALESCE(array_sub_containers_type[i],'container'), 
                            COALESCE(array_containers_storage[i],''), 
                            COALESCE(array_sub_containers_storage[i],''), 
                            COALESCE(line.surnumerary,false), 
                            array_object_name[i], 
                            COALESCE(array_statuses[i],''), 
                            COALESCE(line.complete,true)--,
                            --debug
                            );
        --END LOOP 3
        END LOOP;
    --END IF 2
    END IF;
    --IF 3  
    IF  process_multiple_storage=false THEN
            INSERT INTO darwin2.storage_parts(
                             category, 
                             specimen_ref, 
                             specimen_part, 
                             institution_ref, 
                             building, 
                            floor, 
                            room, 
                            "row", 
                            col, 
                            shelf, 
                            container, 
                            sub_container, 
                            container_type, 
                            sub_container_type, 
                            container_storage, 
                            sub_container_storage, 
                            surnumerary, 
                            object_name, 
                            specimen_status, 
                            complete--,
                            --debug
                            )
                    VALUES ( 
                            COALESCE(line.category,'physical'), 
                            rec_id,
                             COALESCE(line.part,'specimen'), 
                            line.institution_ref, 
                            line.building, 
                            line.floor, 
                            line.room, 
                            line.row, 
                            line.col, 
                            line.shelf, 
                            line.container, 
                            line.sub_container, 
                            COALESCE(line.container_type,'container'), 
                            COALESCE(line.sub_container_type,'container'), 
                            COALESCE(line.container_storage,''), 
                            COALESCE(line.sub_container_storage,''), 
                            COALESCE(line.surnumerary,false), 
                            line.object_name, 
                            COALESCE(line.specimen_status,''), 
                            COALESCE(line.complete,true)--,
                           -- debug
                            );
    --END IF 3
    END IF;
    --END TRANSACTION 1
    END;
   --END LOOP 1
  END LOOP;
  SELECT fct_imp_checker_staging_relationship() into id_to_keep ;
  --BEGIN IF 4
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  --END IF 4
    --BEGIN IF 5
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
    --END IF 5
  RETURN true;
END;
$_$;


ALTER FUNCTION darwin2.fct_importer_abcd(req_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 1712 (class 1255 OID 232547)
-- Name: fct_importer_abcd_bck20160622(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_abcd_bck20160622(req_import_ref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
          container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name)
      VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
        line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
        COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
        COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
      );
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$_$;


ALTER FUNCTION darwin2.fct_importer_abcd_bck20160622(req_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 1713 (class 1255 OID 18070)
-- Name: fct_importer_abcd_bck20160713(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_abcd_bck20160713(req_import_ref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
          container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name
		--ftheeten 2016 06 22
		,specimen_count_males_min, specimen_count_males_max,specimen_count_females_min, specimen_count_females_max
          )
      VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
        line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
        COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
        COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
        --ftheeten 2016 06 22
        ,COALESCE(line.part_count_males_min,0),COALESCE(line.part_count_males_max,0),COALESCE(line.part_count_females_min,0),COALESCE(line.part_count_females_max,0)
      );
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$_$;


ALTER FUNCTION darwin2.fct_importer_abcd_bck20160713(req_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 1726 (class 1255 OID 250123)
-- Name: fct_importer_abcd_bck20160826(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_abcd_bck20160826(req_import_ref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
          container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name
		--ftheeten 2016 06 22
		,specimen_count_males_min, specimen_count_males_max,specimen_count_females_min, specimen_count_females_max,
		--ftheeten 2016 07 11
        gtu_from_date_mask,
        gtu_from_date,
        gtu_to_date_mask,
        gtu_to_date
          )
      VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
        line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
        COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
        COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
        --ftheeten 2016 06 22
        ,COALESCE(line.part_count_males_min,0),COALESCE(line.part_count_males_max,0),COALESCE(line.part_count_females_min,0),COALESCE(line.part_count_females_max,0),    --ftheeten 2016 07 11
        
        COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038')
      );
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$_$;


ALTER FUNCTION darwin2.fct_importer_abcd_bck20160826(req_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 1725 (class 1255 OID 250543)
-- Name: fct_importer_abcd_bck20160928_no_storage_part(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_abcd_bck20160928_no_storage_part(req_import_ref integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
          (
            SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
                  'taxonomy',
                  taxon_ref,
                  keyword_type,
                  "keyword"
            FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                                     AND staging.id = ckmain.record_id
                         INNER JOIN imports as i ON i.id = staging.import_ref
            WHERE import_ref = req_import_ref
              AND to_import=true
              AND status = ''::hstore
              AND i.is_finished =  FALSE
              AND NOT EXISTS (
                              SELECT 1
                              FROM classification_keywords
                              WHERE referenced_relation = 'taxonomy'
                                AND record_id = staging.taxon_ref
                                AND keyword_type = ckmain.keyword_type
                                AND keyword_indexed = ckmain.keyword_indexed
              )
          );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    IF line.spec_ref IS NULL THEN
      INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
          acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
          specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
          container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name
		--ftheeten 2016 06 22
		,specimen_count_males_min, specimen_count_males_max,specimen_count_females_min, specimen_count_females_max,
		--ftheeten 2016 07 11
        gtu_from_date_mask,
        gtu_from_date,
        gtu_to_date_mask,
        gtu_to_date
          )
      VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
        COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
        COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
        COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
        line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
        COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
        COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
        --ftheeten 2016 06 22
        ,COALESCE(line.part_count_males_min,0),COALESCE(line.part_count_males_max,0),COALESCE(line.part_count_females_min,0),COALESCE(line.part_count_females_max,0),    --ftheeten 2016 07 11
        
        COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038')
      );
      FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
      LOOP
        SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
        DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      END LOOP;

      SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
      IF code_count = 0 THEN
        PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
      ELSE
        UPDATE codes SET referenced_relation = 'specimens', 
                         record_id = rec_id, 
                         code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
                         code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
                         code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
                         code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
        WHERE referenced_relation = 'staging'
          AND record_id = line.id
          AND code_category = 'main';
      END IF;

      UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
      -- Import identifiers whitch identification have been updated to specimen
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref 
        FROM staging_people s, identifications i 
        WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
      DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
      -- Import collecting_methods
      INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
        SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref 
        FROM staging_collecting_methods 
        WHERE staging_ref = line.id;
      
      DELETE FROM staging_collecting_methods where staging_ref = line.id;
      UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

      FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
      LOOP
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    END IF ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$_$;


ALTER FUNCTION darwin2.fct_importer_abcd_bck20160928_no_storage_part(req_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 618 (class 1255 OID 108356)
-- Name: fct_importer_catalogue(integer, text, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_importer_catalogue(req_import_ref integer, referenced_relation text, exclude_invalid_entries boolean DEFAULT false) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$
  DECLARE
    staging_catalogue_line staging_catalogue;
    where_clause_complement_1 text := ' ';
    where_clause_complement_2 text := ' ';
    where_clause_complement_3 text := ' ';
    where_clause_complement_3_bis text := ' ';
    where_clause_complement_4 text := ' ';
    where_clause_complement_5 text := ' ';
    where_clause_exclude_invalid text := ' ';
    recCatalogue RECORD;
    parent_path template_classifications.path%TYPE;
    parentRef staging_catalogue.parent_ref%TYPE;
    parent_level catalogue_levels.id%TYPE;
    catalogueRef staging_catalogue.catalogue_ref%TYPE;
    levelRef staging_catalogue.level_ref%TYPE;
    error_msg TEXT := '';
    children_move_forward BOOLEAN := FALSE;
    level_naming TEXT;
    tempSQL TEXT;
  BEGIN
    -- Browse all staging_catalogue lines
    FOR staging_catalogue_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY level_ref, fullToIndex(name)
    LOOP
      IF trim(touniquestr(staging_catalogue_line.name)) = '' THEN
        RAISE EXCEPTION E'Case 0, Could not import this file, % is not a valid name.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
      END IF;
      SELECT parent_ref, catalogue_ref, level_ref INTO parentRef, catalogueRef, levelRef FROM staging_catalogue WHERE id = staging_catalogue_line.id;
      IF catalogueRef IS NULL THEN
        -- Check if we're at a top taxonomic entry in the template/staging_catalogue line
        IF parentRef IS NULL THEN
          -- If top entry, we have not parent defined and we therefore have no other filtering criteria
          where_clause_complement_1 := ' ';
          where_clause_complement_2 := ' ';
          where_clause_complement_3 := ' ';
          where_clause_complement_3_bis := ' ';
        ELSE
          -- If a child entry, we've got to use the informations from the already matched or created parent
          where_clause_complement_1 := '  AND tax.parent_ref = ' || parentRef || ' ';
          where_clause_complement_2 := '  AND tax.parent_ref != ' || parentRef || ' ';
          -- Select the path from parent catalogue unit
          EXECUTE 'SELECT path, level_ref FROM ' || quote_ident(referenced_relation) || ' WHERE id = $1'
          INTO parent_path, parent_level
          USING parentRef;
          where_clause_complement_3 := '  AND position (' || quote_literal(parent_path) || ' IN tax.path) = 1 ';
          where_clause_complement_3_bis := '  AND (select t2.level_ref from ' || quote_ident(referenced_relation) || ' as t2 where t2.id = tax.parent_ref) > ' || parent_level || ' ';
        END IF;
        where_clause_complement_4 := '  AND left(substring(tax.name from length(trim(' ||
                                     quote_literal(staging_catalogue_line.name) || '))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        where_clause_complement_5 := '  AND left(substring(' || quote_literal(staging_catalogue_line.name) ||
                                     ' from length(trim(tax.name))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        -- Set the invalid where clause if asked
        IF exclude_invalid_entries = TRUE THEN
          where_clause_exclude_invalid := '  AND tax.status != ' || quote_literal('invalid') || ' ';
        END IF;
        -- Check a perfect match entry
        -- Take care here, a limit 1 has been set, we only kept the EXIT in case the limit would be accidently removed
        FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                    'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                    'WHERE tax.level_ref = $1 ' ||
                                    '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                    where_clause_exclude_invalid ||
                                    where_clause_complement_1 ||
                                    'LIMIT 1;'
        USING staging_catalogue_line.level_ref, staging_catalogue_line.name
        LOOP
          -- If more than one entry found, we set an error...
          IF recCatalogue.total_count > 1 THEN
            RAISE EXCEPTION E'Case 1, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
          END IF;
          EXIT;
        END LOOP;
        -- No perfect match occured with the same parent (if it applies - doesn't apply for top taxonomic entry in template)
        IF NOT FOUND THEN
          -- For this step, as it depends upon the existence of a parent, we test well we are on that case
          -- It concerns a perfect match with parents differents but with a path common
          -- That means, if only one entry exists, that they are the same but with a more detailed hierarchy in the
          -- already existing entry
          IF parentRef IS NOT NULL THEN
            FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                        'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                        'WHERE tax.level_ref = $1 ' ||
                                        '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                        where_clause_exclude_invalid ||
                                        where_clause_complement_2 ||
                                        where_clause_complement_3 ||
                                        where_clause_complement_3_bis ||
                                        'LIMIT 1;'
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If for this kind of perfect match with different parent but kind of same path start, we get multiple
              -- possibilities, then fail
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 2, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- If it gave no result, we've got to move forward and try the next option
            IF NOT FOUND THEN
              children_move_forward := TRUE;
            END IF;
          END IF;
          IF parentRef IS NULL OR children_move_forward = TRUE THEN
            -- This next option try a fuzzy match, with, if it's a child entry in the template, a verification that
            -- the parent specified in the template and the path of the potential corresponding entry in catalogue
            -- have a common path...
            tempSQL := 'SELECT COUNT(id) OVER () as total_count, * ' ||
                       'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                       'WHERE tax.level_ref = $1 ' ||
                       '  AND tax.name_indexed LIKE fullToIndex( $2 ) || ' || quote_literal('%') ||
                       where_clause_exclude_invalid ||
                       where_clause_complement_3 ||
                       where_clause_complement_4;
            IF parentRef IS NOT NULL THEN
              tempSQL := tempSQL || where_clause_complement_1;
            END IF;
            tempSQL := tempSQL || 'LIMIT 1;';
            FOR recCatalogue IN EXECUTE tempSQL
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If we're on the case of a top entry in the template, we cannot afford the problem of multiple entries
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 3, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- Last chance is to try to find if the entry in DaRWIN shouldn't be completed
            -- This entry should be "alone" of its kind - check the NOT EXIST clause
            IF NOT FOUND THEN
              FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                          'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                          'WHERE tax.level_ref = $1 ' ||
                                          '  AND position(tax.name_indexed IN fullToIndex( $2 )) = 1 ' ||
                                          where_clause_exclude_invalid ||
                                          '  AND NOT EXISTS (SELECT 1 ' ||
                                          '                  FROM ' || quote_ident(referenced_relation) || ' as stax ' ||
                                          '                  WHERE stax.id != tax.id ' ||
                                          '                  AND stax.level_ref = tax.level_ref ' ||
                                          '                  AND stax.path = tax.path ' ||
                                          '                  AND stax.name_indexed LIKE tax.name_indexed || ' || quote_literal('%') ||
                                          '                  LIMIT 1 ' ||
                                          '                 ) ' ||
                                          where_clause_complement_3 ||
                                          where_clause_complement_5 ||
                                          'LIMIT 1;'
              USING staging_catalogue_line.level_ref, staging_catalogue_line.name
              LOOP
                IF recCatalogue.total_count > 1 THEN
                  RAISE EXCEPTION E'Case 4, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
                ELSE
                  -- If only one entry is found, we can replace the name of this entry
                  EXECUTE 'UPDATE ' || quote_ident(referenced_relation) || ' ' ||
                          'SET name = ' || quote_literal(staging_catalogue_line.name) || ' ' ||
                          'WHERE id = ' || recCatalogue.id || ';';
                END IF;
                EXIT;
              END LOOP;
              IF NOT FOUND THEN
                IF parentRef IS NOT NULL THEN
                  EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) ' ||
                          'VALUES(DEFAULT,$1,$2,$3) ' ||
                          'RETURNING *;'
                  INTO recCatalogue
                  USING staging_catalogue_line.name,staging_catalogue_line.level_ref,parentRef;
                -- tell to update the staging line to set the catalogue_ref with the id found
                ELSE
                  SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
                  RAISE EXCEPTION 'Could not import this file, % (level %) does not exist in DaRWIN and cannot be attached, correct your file or create this % manually', staging_catalogue_line.name,  level_naming, quote_ident(referenced_relation);
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        -- update the staging line to set the catalogue_ref with the id found
        -- update the staging children lines
        WITH staging_catalogue_updated(updated_id/*, catalogue_ref_updated*/) AS (
          UPDATE staging_catalogue as sc
          SET catalogue_ref = recCatalogue.id
          WHERE sc.import_ref = staging_catalogue_line.import_ref
                AND sc.name = staging_catalogue_line.name
                AND sc.level_ref = staging_catalogue_line.level_ref
          RETURNING id
        )
        UPDATE staging_catalogue as msc
        SET parent_ref = recCatalogue.id,
          parent_updated = TRUE
        WHERE msc.import_ref = staging_catalogue_line.import_ref
              AND msc.parent_ref IN (
          SELECT updated_id FROM staging_catalogue_updated
        )
              AND parent_updated = FALSE;
      END IF;
      children_move_forward := FALSE;
    END LOOP;
    RETURN TRUE;
    EXCEPTION WHEN OTHERS THEN
    IF SQLERRM = 'This record does not follow the level hierarchy' THEN
      SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
      RAISE EXCEPTION E'Could not import this file, % (level %) does not follow the accepted level hierarchy in DaRWIN an cannot be attached nor created.\nPlease correct your file.\nStaging Catalogue Line: %', staging_catalogue_line.name,  level_naming, staging_catalogue_line.id;
    ELSE
      RAISE EXCEPTION '%', SQLERRM;
    END IF;
  END;
  $_$;


ALTER FUNCTION darwin2.fct_importer_catalogue(req_import_ref integer, referenced_relation text, exclude_invalid_entries boolean) OWNER TO darwin2;

--
-- TOC entry 554 (class 1255 OID 18079)
-- Name: fct_informative_reset_last_flag(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_informative_reset_last_flag() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE informative_workflow
    SET is_last = true
    WHERE referenced_relation = OLD.referenced_relation
      AND record_id = OLD.record_id
      AND id = (select id from informative_workflow
        WHERE referenced_relation = OLD.referenced_relation AND record_id = OLD.record_id ORDER BY modification_date_time desc LIMIT 1)
    ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_informative_reset_last_flag() OWNER TO darwin2;

--
-- TOC entry 560 (class 1255 OID 18068)
-- Name: fct_look_for_institution(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_look_for_institution(fullname text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_record integer :=0;
  result_nbr integer;

BEGIN
    result_nbr := 0;
    FOR ref_record IN SELECT id from people p
      WHERE is_physical = false  AND
      ( formated_name_indexed like fulltoindex(fullname) || '%' OR fulltoindex(additional_names) =  fulltoindex(fullname) )
      LIMIT 2
    LOOP
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      return ref_record;
    END IF;

    IF result_nbr >= 2 THEN
      return -1 ;-- To Much
      continue;
    END IF;
  RETURN 0;
END;
$$;


ALTER FUNCTION darwin2.fct_look_for_institution(fullname text) OWNER TO darwin2;

--
-- TOC entry 585 (class 1255 OID 18067)
-- Name: fct_look_for_people(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_look_for_people(fullname text) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
  ref_record integer :=0;
  result_nbr integer;
  searched_name text;
BEGIN
    result_nbr := 0;
     --modif ftheeten 20140630
     --modif ftheeten 20160226
     searched_name := fulltoindex(fullname,true);--|| '%'  ;
    --searched_name := fulltoindex(fullname);--|| '%'  ;
    --searched_name := fulltoindex(fullname)|| '%'  ;
    FOR ref_record IN SELECT id from people p
      WHERE
        formated_name_indexed like searched_name
        OR  name_formated_indexed like searched_name LIMIT 2
    LOOP
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!
      return ref_record;
    END IF;

    IF result_nbr >= 2 THEN
      return -1 ;-- To Much
      continue;
    END IF;
  RETURN 0;
END;
$$;


ALTER FUNCTION darwin2.fct_look_for_people(fullname text) OWNER TO darwin2;

--
-- TOC entry 511 (class 1255 OID 18077)
-- Name: fct_mask_date(timestamp without time zone, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_mask_date(date_fld timestamp without time zone, mask_fld integer) RETURNS text
    LANGUAGE sql IMMUTABLE
    AS $_$

  SELECT
CASE WHEN ($2 & 32)!=0 THEN date_part('year',$1)::text ELSE 'xxxx' END || '-' ||
CASE WHEN ($2 & 16)!=0 THEN date_part('month',$1)::text ELSE 'xx' END || '-' ||
CASE WHEN ($2 & 8)!=0 THEN date_part('day',$1)::text ELSE 'xx' END;
$_$;


ALTER FUNCTION darwin2.fct_mask_date(date_fld timestamp without time zone, mask_fld integer) OWNER TO darwin2;

--
-- TOC entry 527 (class 1255 OID 18022)
-- Name: fct_nbr_in_relation(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_nbr_in_relation() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  nbr integer = 0 ;
BEGIN
  SELECT count(record_id_2) INTO nbr FROM catalogue_relationships WHERE
      relationship_type = NEW.relationship_type
      AND record_id_1 = NEW.record_id_1
      AND referenced_relation = NEW.referenced_relation;

  IF NEW.relationship_type = 'current_name' THEN
    IF TG_OP = 'INSERT' THEN
      IF nbr > 0 THEN
	RAISE EXCEPTION 'Maximum number of renamed item reach';
      END IF;
    ELSE
      IF nbr > 1 THEN
	RAISE EXCEPTION 'Maximum number of renamed item reach';
      END IF;
    END IF;
  ELSEIF NEW.relationship_type = 'recombined from' THEN
    IF TG_OP = 'INSERT' THEN
      IF nbr > 1 THEN
	RAISE EXCEPTION 'Maximum number of recombined item reach';
      END IF;
    ELSE
      IF nbr > 2 THEN
	RAISE EXCEPTION 'Maximum number of recombined item reach';
      END IF;
    END IF;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_nbr_in_relation() OWNER TO darwin2;

--
-- TOC entry 519 (class 1255 OID 18023)
-- Name: fct_nbr_in_synonym(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_nbr_in_synonym() RETURNS trigger
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
-- TOC entry 1707 (class 1255 OID 17994)
-- Name: fct_remove_array_elem(anyarray, anyarray); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_remove_array_elem(in_array anyarray, elem anyarray, OUT out_array anyarray) RETURNS anyarray
    LANGUAGE plpgsql IMMUTABLE
    AS $$
BEGIN
	SELECT array(select s FROM fct_explode_array (in_array)  as s WHERE NOT elem @> ARRAY[s]) INTO out_array;
END;
$$;


ALTER FUNCTION darwin2.fct_remove_array_elem(in_array anyarray, elem anyarray, OUT out_array anyarray) OWNER TO darwin2;

--
-- TOC entry 535 (class 1255 OID 18078)
-- Name: fct_remove_last_flag(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_remove_last_flag() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE informative_workflow
    SET is_last = false
    WHERE referenced_relation = NEW.referenced_relation
      AND record_id = NEW.record_id;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_remove_last_flag() OWNER TO darwin2;

--
-- TOC entry 561 (class 1255 OID 18080)
-- Name: fct_remove_last_flag_loan(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_remove_last_flag_loan() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE loan_status
    SET is_last = false
    WHERE loan_ref = NEW.loan_ref;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_remove_last_flag_loan() OWNER TO darwin2;

--
-- TOC entry 1728 (class 1255 OID 251485)
-- Name: fct_remove_null_array_elem(anyarray); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_remove_null_array_elem(in_array anyarray, OUT out_array anyarray) RETURNS anyarray
    LANGUAGE plpgsql IMMUTABLE
    AS $$
BEGIN
	SELECT array(select s FROM fct_explode_array (in_array)  as s WHERE  s is not null) INTO out_array;
END;
$$;


ALTER FUNCTION darwin2.fct_remove_null_array_elem(in_array anyarray, OUT out_array anyarray) OWNER TO darwin2;

--
-- TOC entry 586 (class 1255 OID 230883)
-- Name: fct_rmca_check_people_before_delete(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_check_people_before_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN            
        IF EXISTS (SELECT 1 FROM catalogue_people WHERE people_ref=OLD.ID)
OR
	EXISTS (SELECT 1 FROM specimens WHERE OLD.ID = ANY(spec_ident_ids) OR OLD.ID =ANY(spec_coll_ids) OR OLD.ID = ANY (spec_don_sel_ids))


         THEN
            RAISE EXCEPTION 'cannot delete this people, still related to record';
         ELSE 
		RETURN OLD;
        END IF;

END;
$$;


ALTER FUNCTION darwin2.fct_rmca_check_people_before_delete() OWNER TO darwin2;

--
-- TOC entry 1708 (class 1255 OID 232532)
-- Name: fct_rmca_dynamic_saved_search(integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_dynamic_saved_search(id_query integer, id_user integer) RETURNS SETOF integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
	sql varchar;

	where_part varchar;
	param_part varchar;
	param_part_array varchar[];
	elem varchar;
	
BEGIN

	SELECT  query_where, query_parameters  into   where_part, param_part FROM my_saved_searches WHERE id=id_query AND user_ref= id_user LIMIT 1;
	
	
	param_part_array=regexp_split_to_array(param_part, ';(?=|)'); 
	
	FOR i in 2..array_length( param_part_array,1)	LOOP
		elem:=regexp_replace(trim(param_part_array[i]), '^(\|)','');
		elem:=regexp_replace(trim(elem), '(\|)$','');
		
		where_part:=regexp_replace(where_part, '(\?)',''''||elem||'''');
		
	END LOOP;

	 RETURN QUERY EXECUTE 'SELECT s.id '||where_part;
END
$_$;


ALTER FUNCTION darwin2.fct_rmca_dynamic_saved_search(id_query integer, id_user integer) OWNER TO darwin2;

--
-- TOC entry 1721 (class 1255 OID 250889)
-- Name: fct_rmca_get_people_name_related_to_specimen(integer, character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_get_people_name_related_to_specimen(integer, character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$
DECLARE
 returned varchar;
BEGIN
returned:=string_agg(formated_name, ', ' ) FROM  catalogue_people c
   
   LEFT JOIN people p
   ON c.people_ref=p.id
   WHERE c.record_id=$1
   AND c.referenced_relation='specimens' and c.people_type=$2;
   RETURN returned;
   END;
$_$;


ALTER FUNCTION darwin2.fct_rmca_get_people_name_related_to_specimen(integer, character varying) OWNER TO darwin2;

--
-- TOC entry 240 (class 1259 OID 17205)
-- Name: template_classifications; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE template_classifications (
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
-- TOC entry 5187 (class 0 OID 0)
-- Dependencies: 240
-- Name: TABLE template_classifications; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE template_classifications IS 'Template table used to construct every common data in each classifications tables (taxonomy, chronostratigraphy, lithostratigraphy,...)';


--
-- TOC entry 5188 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.name IS 'Classification unit name';


--
-- TOC entry 5189 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.name_indexed IS 'Indexed form of name field for ordering';


--
-- TOC entry 5190 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5191 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5192 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.local_naming; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.local_naming IS 'Flag telling the appelation is local or internationally recognized';


--
-- TOC entry 5193 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.color; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.color IS 'Hexadecimal value of color associated to the unit';


--
-- TOC entry 5194 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5195 (class 0 OID 0)
-- Dependencies: 240
-- Name: COLUMN template_classifications.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_classifications.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 247 (class 1259 OID 17245)
-- Name: taxonomy; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE taxonomy (
    id integer NOT NULL,
    extinct boolean DEFAULT false NOT NULL,
    sensitive_info_withheld boolean,
    CONSTRAINT fct_chk_onceinpath_taxonomy CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
)
INHERITS (template_classifications);


ALTER TABLE darwin2.taxonomy OWNER TO darwin2;

--
-- TOC entry 5197 (class 0 OID 0)
-- Dependencies: 247
-- Name: TABLE taxonomy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE taxonomy IS 'Taxonomic classification table';


--
-- TOC entry 5198 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.name IS 'Classification unit name';


--
-- TOC entry 5199 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.name_indexed IS 'Indexed form of name field';


--
-- TOC entry 5200 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5201 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5202 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5203 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 5204 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.id IS 'Unique identifier of a classification unit';


--
-- TOC entry 5205 (class 0 OID 0)
-- Dependencies: 247
-- Name: COLUMN taxonomy.extinct; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN taxonomy.extinct IS 'Tells if taxonomy is extinct or not';


--
-- TOC entry 320 (class 1259 OID 108219)
-- Name: v_rmca_path_parent_children; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_path_parent_children AS
SELECT b.id AS parent_id, ((((b.path)::text || b.id) || '/'::text))::character varying AS parent_path, a.id AS child_id, ((((a.path)::text || (a.id)::text) || '/'::text))::character varying AS child_path FROM (taxonomy a JOIN taxonomy b ON (((((((a.path)::text || (a.id)::text) || '/'::text))::character varying)::text ~~ (((((b.path)::text || (b.id)::text) || '/%'::text))::character varying)::text)));


ALTER TABLE darwin2.v_rmca_path_parent_children OWNER TO darwin2;

--
-- TOC entry 592 (class 1255 OID 108226)
-- Name: fct_rmca_path_parent_children_by_coll(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_path_parent_children_by_coll(integer) RETURNS SETOF v_rmca_path_parent_children
    LANGUAGE sql
    AS $_$
SELECt * FROM v_rmca_path_parent_children
where child_id in (SELECT taxon_ref from specimens where collection_ref=$1);
$_$;


ALTER FUNCTION darwin2.fct_rmca_path_parent_children_by_coll(integer) OWNER TO darwin2;

--
-- TOC entry 596 (class 1255 OID 108256)
-- Name: fct_rmca_path_parent_children_by_coll_alpha_specimens_count(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_path_parent_children_by_coll_alpha_specimens_count(integer) RETURNS TABLE(parent_id integer, parent_path character varying, parent_name character varying, parent_level character varying, parent_level_order integer, parent_alpha_path character varying, count_children_names_in_collection bigint, count_specimens_identified bigint)
    LANGUAGE sql
    AS $_$
SELECT distinct parent_id, parent_path, 
parent_name, parent_level, parent_level_order,  parent_alpha_path, count(distinct child_id)-1::bigint, (SELECT count(distinct id) FROM specimens where collection_ref=$1 and taxon_ref=a.parent_id ) FROM fct_rmca_path_parent_children_by_coll_extended_alpha( $1) a

--WHERE parent_id<>child_id

GROUP BY parent_id, parent_path, 
parent_name, parent_level, parent_level_order,  parent_alpha_path
order by parent_level_order, parent_alpha_path

$_$;


ALTER FUNCTION darwin2.fct_rmca_path_parent_children_by_coll_alpha_specimens_count(integer) OWNER TO darwin2;

--
-- TOC entry 595 (class 1255 OID 108264)
-- Name: fct_rmca_path_parent_children_by_coll_alpha_specimens_count_lev(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_path_parent_children_by_coll_alpha_specimens_count_lev(integer) RETURNS TABLE(parent_level character varying, parent_level_order integer, nb_names bigint, count_specimens_identified bigint)
    LANGUAGE sql
    AS $_$
SELECT distinct parent_level, parent_level_order, count($1)::bigint as nb_names, sum(count_specimens_identified)::bigint as count_specimens_identified FROM  fct_rmca_path_parent_children_by_coll_alpha_specimens_count(
    $1
)
GROUP BY parent_level, parent_level_order
 order by parent_level_order;

$_$;


ALTER FUNCTION darwin2.fct_rmca_path_parent_children_by_coll_alpha_specimens_count_lev(integer) OWNER TO darwin2;

--
-- TOC entry 187 (class 1259 OID 16667)
-- Name: catalogue_levels; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE catalogue_levels (
    id integer NOT NULL,
    level_type character varying NOT NULL,
    level_name character varying NOT NULL,
    level_sys_name character varying NOT NULL,
    optional_level boolean DEFAULT false NOT NULL,
    level_order integer DEFAULT 999 NOT NULL
);


ALTER TABLE darwin2.catalogue_levels OWNER TO darwin2;

--
-- TOC entry 5207 (class 0 OID 0)
-- Dependencies: 187
-- Name: TABLE catalogue_levels; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE catalogue_levels IS 'List of hierarchical units levels - organized by type of unit: taxonomy, chroostratigraphy,...';


--
-- TOC entry 5208 (class 0 OID 0)
-- Dependencies: 187
-- Name: COLUMN catalogue_levels.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_levels.id IS 'Unique identifier of a hierarchical unit level';


--
-- TOC entry 5209 (class 0 OID 0)
-- Dependencies: 187
-- Name: COLUMN catalogue_levels.level_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_levels.level_type IS 'Type of unit the levels is applicable to - contained in a predifined list: taxonomy, chronostratigraphy,...';


--
-- TOC entry 5210 (class 0 OID 0)
-- Dependencies: 187
-- Name: COLUMN catalogue_levels.level_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_levels.level_name IS 'Name given to level concerned';


--
-- TOC entry 5211 (class 0 OID 0)
-- Dependencies: 187
-- Name: COLUMN catalogue_levels.level_sys_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_levels.level_sys_name IS 'Name given to level concerned in the system. i.e.: cohort zoology will be writen in system as cohort_zoology';


--
-- TOC entry 5212 (class 0 OID 0)
-- Dependencies: 187
-- Name: COLUMN catalogue_levels.optional_level; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_levels.optional_level IS 'Tells if the level is optional';


--
-- TOC entry 321 (class 1259 OID 108227)
-- Name: v_rmca_path_parent_children_extended_taxonomy; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_path_parent_children_extended_taxonomy AS
SELECT b.id AS parent_id, ((((b.path)::text || b.id) || '/'::text))::character varying AS parent_path, b.name AS parent_name, d.level_name AS parent_level, d.level_order AS parent_level_order, a.id AS child_id, ((((a.path)::text || (a.id)::text) || '/'::text))::character varying AS child_path, a.name AS child_name, a.level_ref, c.level_name AS child_level, c.level_order AS child_level_order FROM (((taxonomy a JOIN taxonomy b ON (((((((a.path)::text || (a.id)::text) || '/'::text))::character varying)::text ~~ (((((b.path)::text || (b.id)::text) || '/%'::text))::character varying)::text))) JOIN catalogue_levels c ON ((a.level_ref = c.id))) JOIN catalogue_levels d ON ((b.level_ref = d.id)));


ALTER TABLE darwin2.v_rmca_path_parent_children_extended_taxonomy OWNER TO darwin2;

--
-- TOC entry 593 (class 1255 OID 108232)
-- Name: fct_rmca_path_parent_children_by_coll_extended(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_path_parent_children_by_coll_extended(integer) RETURNS SETOF v_rmca_path_parent_children_extended_taxonomy
    LANGUAGE sql
    AS $_$
SELECt * FROM v_rmca_path_parent_children_extended_taxonomy
where child_id in (SELECT taxon_ref from specimens where collection_ref=$1);
$_$;


ALTER FUNCTION darwin2.fct_rmca_path_parent_children_by_coll_extended(integer) OWNER TO darwin2;

--
-- TOC entry 591 (class 1255 OID 66682)
-- Name: fct_rmca_sort_taxon_path_alphabetically(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_sort_taxon_path_alphabetically(path character varying) RETURNS character varying
    LANGUAGE plpgsql
    AS $$
DECLARE
 returned varchar;
 arr varchar[];
 path_elem varchar;
 tmp varchar;
BEGIN
	returned ='';
	--arr:=	 regexp_matches(trim(path), '([^/]+)' , 'g');
	arr:=regexp_split_to_array(path, '/');
      FOR path_elem IN SELECT unnest(arr)
      LOOP
		IF isnumeric(path_elem) THEN
			SELECT name_indexed INTO tmp FROM taxonomy where id=path_elem::int;
			returned=returned||'/'||tmp;
		END IF;
      END LOOP;

	return returned||'/';
END;

$$;


ALTER FUNCTION darwin2.fct_rmca_sort_taxon_path_alphabetically(path character varying) OWNER TO darwin2;

--
-- TOC entry 322 (class 1259 OID 108234)
-- Name: v_rmca_path_parent_children_extended_taxonomy_alpha; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_path_parent_children_extended_taxonomy_alpha AS
SELECT b.id AS parent_id, ((((b.path)::text || b.id) || '/'::text))::character varying AS parent_path, fct_rmca_sort_taxon_path_alphabetically(((((b.path)::text || b.id) || '/'::text))::character varying) AS parent_alpha_path, b.name AS parent_name, d.level_name AS parent_level, d.level_order AS parent_level_order, a.id AS child_id, ((((a.path)::text || (a.id)::text) || '/'::text))::character varying AS child_path, a.name AS child_name, a.level_ref, c.level_name AS child_level, c.level_order AS child_level_order FROM (((taxonomy a JOIN taxonomy b ON (((((((a.path)::text || (a.id)::text) || '/'::text))::character varying)::text ~~ (((((b.path)::text || (b.id)::text) || '/%'::text))::character varying)::text))) JOIN catalogue_levels c ON ((a.level_ref = c.id))) JOIN catalogue_levels d ON ((b.level_ref = d.id)));


ALTER TABLE darwin2.v_rmca_path_parent_children_extended_taxonomy_alpha OWNER TO darwin2;

--
-- TOC entry 594 (class 1255 OID 108239)
-- Name: fct_rmca_path_parent_children_by_coll_extended_alpha(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_path_parent_children_by_coll_extended_alpha(integer) RETURNS SETOF v_rmca_path_parent_children_extended_taxonomy_alpha
    LANGUAGE sql
    AS $_$
SELECt * FROM v_rmca_path_parent_children_extended_taxonomy_alpha
where child_id in (SELECT taxon_ref from specimens where collection_ref=$1);
$_$;


ALTER FUNCTION darwin2.fct_rmca_path_parent_children_by_coll_extended_alpha(integer) OWNER TO darwin2;

--
-- TOC entry 1745 (class 1255 OID 251471)
-- Name: fct_rmca_report_invertebrates_rtf(integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_report_invertebrates_rtf(integer, integer) RETURNS TABLE(country_tag_value character varying, taxon_name character varying, aggregated_str character varying)
    LANGUAGE sql
    AS $_$
SELECT 


specimens.gtu_country_tag_value, specimens.taxon_name, 
string_agg(btrim(regexp_replace(((((((
COALESCE
(

COALESCE(specimens.specimen_count_males_min::text || '♂'::text, ''::text) || 
(' '::text || specimens.specimen_count_females_min::text|| '♀'::text||':') ,'')|| 
 (COALESCE(' '::text || 
 replace(
 regexp_replace(
array_to_string(filter_2_arrays_by_key(tags, group_types, '{administrative area, administrative, area}'),', ') 
 
 , ';AFRICA'::text, ''::text, 
 'i'::text)
 , ';'::text, ' '::text), ''::text)))
  || 
 COALESCE((', '::text || gtu.elevation::character varying::text) || COALESCE(gtu.elevation_unit, 'm'::character varying)::text, ''::text)) || 
        CASE
            WHEN upper(gtu.coordinates_source::text) = 'DD'::text THEN COALESCE(((((', '::text || ABS(gtu.latitude)::text) || 
            CASE
                WHEN gtu.latitude < 0::double precision THEN 'S'::text
                ELSE 'N'::text
            END) || ' '::text) || ABS(gtu.longitude)::text) || 
            CASE
                WHEN gtu.longitude < 0::double precision THEN 'W'::text
                ELSE 'E'::text
            END, ''::text)
            WHEN upper(gtu.coordinates_source::text) = 'DMS'::text THEN 
 

            --'coord_placeholder'
            ', '||COALESCE(gtu.latitude_dms_degree::text||'°','')||COALESCE(gtu.latitude_dms_minutes::text||'''','')||COALESCE(gtu.latitude_dms_seconds::text||'"','')
            ||CASE
                WHEN gtu.latitude_dms_direction >= 1 THEN 'N'::text
                WHEN gtu.latitude_dms_direction <= (-1) THEN 'S'::text
                ELSE ''
            END
            ||
            ', '||COALESCE(gtu.longitude_dms_degree::text||'°','')||COALESCE(gtu.longitude_dms_minutes::text||'''','')||COALESCE(gtu.longitude_dms_seconds::text||'"','')
            ||CASE
                WHEN gtu.longitude_dms_direction >= 1 THEN 'E'::text
                WHEN gtu.longitude_dms_direction <= (-1) THEN '>'::text
                ELSE ''
            END
            
            WHEN upper(gtu.coordinates_source::text) = 'UTM'::text THEN COALESCE(((((', '::text || gtu.latitude_utm::text) || ' '::text) || gtu.longitude_utm::text) || ' '::text) || gtu.utm_zone::text, ''::text)
            ELSE 

             --'coord_placeholder DMS by default'
            ', '||COALESCE(gtu.latitude_dms_degree::text||'°','')||COALESCE(gtu.latitude_dms_minutes::text||'''','')||COALESCE(gtu.latitude_dms_seconds::text||'"','')
            ||CASE
                WHEN gtu.latitude_dms_direction >= 1 THEN 'N'::text
                WHEN gtu.latitude_dms_direction <= (-1) THEN 'S'::text
                ELSE ''
            END
            ||
            ', '||COALESCE(gtu.longitude_dms_degree::text||'°','')||COALESCE(gtu.longitude_dms_minutes::text||'''','')||COALESCE(gtu.longitude_dms_seconds::text||'"','')
            ||CASE
                WHEN gtu.longitude_dms_direction >= 1 THEN 'E'::text
                WHEN gtu.longitude_dms_direction <= (-1) THEN '>'::text
                ELSE ''
            END
        END) 

--date
	||
	COALESCE(', '||fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask)
	||CASE
			WHEN specimens.gtu_to_date_mask>0 THEN
			' to '||fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask)
			ELSE
			''
			END
	 , '')
	 ||COALESCE(', '||array_to_string(filter_2_arrays_by_key(tags, group_types, '{administrative area, administrative, area}', true),', '),'')
--tags loc
	
        || COALESCE(', '::text || fct_rmca_get_people_name_related_to_specimen(specimens.id, 'collector'::character varying)::text, ''::text)) || ' ('::text) || COALESCE((((COALESCE(c2.code_prefix, ''::character varying)::text || 
        COALESCE(c2.code_prefix_separator, ''::character varying)::text) || 
        COALESCE(c2.code, ''::character varying)::text) || COALESCE(c2.code_suffix_separator, ''::character varying)::text) || COALESCE(c2.code_suffix, ''::character varying)::text, ''::text)) || ')'::text


        , '\s{2,}'::text, ' '::text)), '; '::text) 
        AS string_agg
   FROM specimens
   LEFT JOIN gtu ON specimens.gtu_ref = gtu.id
   LEFT JOIN catalogue_people c ON specimens.id = c.record_id AND c.referenced_relation::text = 'specimens'::text AND c.people_type::text = 'collector'::text
   LEFT JOIN people p ON c.people_ref = p.id
   LEFT JOIN codes c2 ON c2.referenced_relation::text = 'specimens'::text AND c2.code_category::text = 'main'::text AND specimens.id = c2.record_id 
   LEFT JOIN (SELECT array_agg(tag order by group_ref) tags, array_agg(group_type order by group_ref) group_types, gtu_ref FROM tags GROUP BY gtu_ref   ) tags ON gtu.id=tags.gtu_ref
  WHERE (specimens.id IN ( SELECT fct_rmca_dynamic_saved_search($2, $1) AS fct_rmca_dynamic_saved_search))
  GROUP BY specimens.gtu_country_tag_value, specimens.taxon_name;
$_$;


ALTER FUNCTION darwin2.fct_rmca_report_invertebrates_rtf(integer, integer) OWNER TO darwin2;

--
-- TOC entry 1709 (class 1255 OID 232533)
-- Name: fct_rmca_sort_taxon_get_parent_level(integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_sort_taxon_get_parent_level(id_taxon integer, id_level integer) RETURNS integer
    LANGUAGE plpgsql
    AS $$
DECLARE
 returned int;
 arr varchar[];
 path_elem varchar;
 tmp_level int;
BEGIN
	returned:=-1;
	arr:= regexp_split_to_array((SELECt path FROM taxonomy where id=id_taxon),'/');
      FOR path_elem IN SELECT unnest(arr)
      LOOP
		SELECT level_ref INTO tmp_level FROM taxonomy WHERE id= COALESCE(NULLIF(path_elem,''),'-1')::int;
		IF tmp_level=id_level THEN
			RETURN path_elem::int;
		END if;
      END LOOP;

	return returned;
END;

$$;


ALTER FUNCTION darwin2.fct_rmca_sort_taxon_get_parent_level(id_taxon integer, id_level integer) OWNER TO darwin2;

--
-- TOC entry 1710 (class 1255 OID 232534)
-- Name: fct_rmca_sort_taxon_get_parent_level_text(integer, integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_sort_taxon_get_parent_level_text(id_taxon integer, id_level integer) RETURNS character varying
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
	arr:= regexp_split_to_array((SELECt path FROM taxonomy where id=id_taxon),'/');
      FOR path_elem IN SELECT unnest(arr)
      LOOP
		SELECT level_ref,name INTO tmp_level,tmp_name FROM taxonomy WHERE id= COALESCE(NULLIF(path_elem,''),'-1')::int;
		IF tmp_level=id_level THEN
			RETURN tmp_name;
		END if;
      END LOOP;

	return returned;
END;

$$;


ALTER FUNCTION darwin2.fct_rmca_sort_taxon_get_parent_level_text(id_taxon integer, id_level integer) OWNER TO darwin2;

--
-- TOC entry 1748 (class 1255 OID 713255)
-- Name: fct_rmca_statistics_collection_count(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_statistics_collection_count(integer) RETURNS TABLE(collection_ref integer, counter_category text, items character varying, count_items bigint)
    LANGUAGE sql
    AS $_$
SELECT DISTINCT  $1 as collection_id, 'type_count'::text AS counter_category, coalesce(specimens.type,'others') AS items, count(specimens.id) AS count_items
           FROM specimens
            where collection_path||collection_ref::varchar||'/' LIKE '%/'||$1::varchar||'/%'

          GROUP BY  specimens.type
         UNION
         SELECT DISTINCT  $1 as collection_id, 'image_count'::text AS counter_category, coalesce(ext_links.category,'others') AS items, count(ext_links.id) AS count_items
         
           FROM ext_links
           
      JOIN specimens ON ext_links.record_id = specimens.id AND ext_links.referenced_relation::text = 'specimens'::text
      where collection_path||collection_ref::varchar||'/' LIKE '%/'||$1::varchar||'/%'
     GROUP BY  ext_links.category;

$_$;


ALTER FUNCTION darwin2.fct_rmca_statistics_collection_count(integer) OWNER TO darwin2;

--
-- TOC entry 1751 (class 1255 OID 715988)
-- Name: fct_rmca_update_child_of_taxon_protected(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_rmca_update_child_of_taxon_protected() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
	text_path varchar;
	text_to_replace varchar;
BEGIN
    IF TG_OP = 'UPDATE'  THEN

		IF NEW.sensitive_info_withheld <> OLD.sensitive_info_withheld THEN
			   
			UPDATE taxonomy SET sensitive_info_withheld=NEW.sensitive_info_withheld WHERE parent_ref=NEW.ID OR path LIKE '%/'||NEW.id::varchar||'/%' ;
		END IF;
        END IF;

 
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_rmca_update_child_of_taxon_protected() OWNER TO darwin2;

--
-- TOC entry 539 (class 1255 OID 18040)
-- Name: fct_search_authorized_encoding_collections(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_search_authorized_encoding_collections(user_id integer) RETURNS SETOF integer
    LANGUAGE sql STABLE
    AS $_$
    select collection_ref from collections_rights where user_ref = $1 and db_user_type >= 2;
$_$;


ALTER FUNCTION darwin2.fct_search_authorized_encoding_collections(user_id integer) OWNER TO darwin2;

--
-- TOC entry 540 (class 1255 OID 18041)
-- Name: fct_search_authorized_view_collections(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_search_authorized_view_collections(user_id integer) RETURNS SETOF integer
    LANGUAGE sql STABLE
    AS $_$
    select collection_ref from collections_rights where user_ref = $1

    UNION

    select id as collection_ref from collections where is_public = true;
$_$;


ALTER FUNCTION darwin2.fct_search_authorized_view_collections(user_id integer) OWNER TO darwin2;

--
-- TOC entry 538 (class 1255 OID 18039)
-- Name: fct_search_methods(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_search_methods(str_ids character varying) RETURNS SETOF integer
    LANGUAGE sql STABLE
    AS $_$
    select distinct(specimen_ref) from specimen_collecting_methods where collecting_method_ref in (select X::int from regexp_split_to_table($1,',') as X);
$_$;


ALTER FUNCTION darwin2.fct_search_methods(str_ids character varying) OWNER TO darwin2;

--
-- TOC entry 537 (class 1255 OID 18038)
-- Name: fct_search_tools(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_search_tools(str_ids character varying) RETURNS SETOF integer
    LANGUAGE sql STABLE
    AS $_$
    select distinct(specimen_ref) from specimen_collecting_tools where collecting_tool_ref in (select X::int from regexp_split_to_table($1,',') as X);
$_$;


ALTER FUNCTION darwin2.fct_search_tools(str_ids character varying) OWNER TO darwin2;

--
-- TOC entry 536 (class 1255 OID 18037)
-- Name: fct_searchcodes(character varying[]); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_searchcodes(VARIADIC character varying[]) RETURNS SETOF integer
    LANGUAGE plpgsql STABLE
    AS $_$
DECLARE
  sqlString varchar := E'select record_id from codes';
  sqlWhere varchar := '';
  code_part varchar;
  code_from varchar;
  code_to varchar;
  code_category varchar;
  relation varchar;
  word varchar;
BEGIN
  FOR i in 1 .. array_upper( $1, 1 ) BY 5 LOOP
    code_category := $1[i];
    code_part := $1[i+1];
    code_from := $1[i+2];
    code_to := $1[i+3];
    relation := $1[i+4] ;

    IF relation IS DISTINCT FROM '' AND i = 1 THEN
      sqlString := sqlString || ' where referenced_relation=' || quote_literal(relation) ;
    ELSIF i = 1 THEN
      sqlString := sqlString || E' where referenced_relation=\'specimens\''  ;
    END IF ;

    sqlWhere := sqlWhere || ' (code_category = ' || quote_literal(code_category) ;

    IF code_from ~ '^[0-9]+$' and code_to ~ '^[0-9]+$' THEN
      sqlWhere := sqlWhere || ' AND code_num BETWEEN ' || quote_literal(code_from) || ' AND ' || quote_literal(code_to) ;
    END IF;

    IF code_part != '' THEN
      sqlWhere := sqlWhere || ' AND (';
      FOR word IN (SELECT words FROM regexp_split_to_table(code_part, E'\\s+') as words) LOOP
        sqlWhere := sqlWhere || E' full_code_indexed like \'%\' || fullToIndex(' || quote_literal(word) || E') || \'%\' OR';
      END LOOP;
      sqlWhere := substr(sqlWhere,0,length(sqlWhere)-2) || ')';
    END IF;

    sqlWhere := sqlWhere || ') OR ';

  END LOOP;

  sqlString := sqlString || ' AND (' || substr(sqlWhere,0, length(sqlWhere)-2) || ')';
  RAISE INFO 'Sql : %',sqlString ;
  RETURN QUERY EXECUTE sqlString;
END;
$_$;


ALTER FUNCTION darwin2.fct_searchcodes(VARIADIC character varying[]) OWNER TO darwin2;

--
-- TOC entry 510 (class 1255 OID 18007)
-- Name: fct_set_user(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_set_user(userid integer) RETURNS void
    LANGUAGE sql
    AS $_$
  select set_config('darwin.userid', $1::varchar, false) ;
  select CASE WHEN get_setting('application_name') ~ ' uid:\d+'
    THEN set_config('application_name', regexp_replace(get_setting('application_name') ,'uid:\d+',  'uid:' || $1::varchar), false)
    ELSE set_config('application_name', get_setting('application_name')  || ' uid:' || $1::varchar, false)
    END;
  update users_login_infos set last_seen = now() where user_ref = $1  and login_type='local';
$_$;


ALTER FUNCTION darwin2.fct_set_user(userid integer) OWNER TO darwin2;

--
-- TOC entry 512 (class 1255 OID 17999)
-- Name: fct_trg_chk_possible_upper_level(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_trg_chk_possible_upper_level() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
BEGIN

  IF fct_chk_possible_upper_level(TG_TABLE_NAME::text, NEW.parent_ref, NEW.level_ref, NEW.id) = false THEN
    RAISE EXCEPTION 'This record does not follow the level hierarchy';
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_trg_chk_possible_upper_level() OWNER TO darwin2;

--
-- TOC entry 1746 (class 1255 OID 18008)
-- Name: fct_trk_log_table(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_trk_log_table() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  user_id integer;
  track_level integer;
  track_fields integer;
  trk_id bigint;
  tbl_row RECORD;
  new_val varchar;
  old_val varchar;
BEGIN
  SELECT COALESCE(get_setting('darwin.track_level'),'10')::integer INTO track_level;
  IF track_level = 0 THEN --NO Tracking
    RETURN NEW;
  ELSIF track_level = 1 THEN -- Track Only Main tables
    IF TG_TABLE_NAME::text NOT IN ('specimens', 'taxonomy', 'chronostratigraphy', 'lithostratigraphy',
      'mineralogy', 'lithology', 'people') THEN
      RETURN NEW;
    END IF;
  END IF;

  SELECT COALESCE(get_setting('darwin.userid'),'0')::integer INTO user_id;
  IF user_id = 0 OR  user_id = -1 THEN
    RETURN NEW;
  END IF;

  IF TG_OP = 'INSERT' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'insert', now(), hstore(NEW)) RETURNING id into trk_id;
  ELSEIF TG_OP = 'UPDATE' THEN

    IF ROW(NEW.*) IS DISTINCT FROM ROW(OLD.*) THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value, old_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'update', now(), hstore(NEW), hstore(OLD)) RETURNING id into trk_id;
    ELSE
      RAISE info 'unnecessary update on table "%" and id "%"', TG_TABLE_NAME::text, NEW.id;
    END IF;

  ELSEIF TG_OP = 'DELETE' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, old_value)
      VALUES (TG_TABLE_NAME::text, OLD.id, user_id, 'delete', now(), hstore(OLD));
  END IF;

  RETURN NULL;
END;
$$;


ALTER FUNCTION darwin2.fct_trk_log_table() OWNER TO darwin2;

--
-- TOC entry 547 (class 1255 OID 18048)
-- Name: fct_unpromotion_impact_prefs(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_unpromotion_impact_prefs() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  saved_search_row RECORD;
BEGIN
  IF NEW.db_user_type IS DISTINCT FROM OLD.db_user_type AND NEW.db_user_type = 1 THEN
    UPDATE preferences
    SET pref_value = subq.fields_available
    FROM (select array_to_string(array(select fields_list
                                       from regexp_split_to_table((SELECT pref_value
                                                                   FROM preferences
                                                                   WHERE user_ref = NEW.id
                                                                     AND pref_key = 'search_cols_specimen'
                                                                   LIMIT 1
                                                                  ), E'\\|') as fields_list
                                       where fields_list not in ('institution_ref', 'building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                      ),'|'
                                ) as fields_available
         ) subq
    WHERE user_ref = NEW.id
      AND pref_key = 'search_cols_specimen';
    FOR saved_search_row IN SELECT id, visible_fields_in_result FROM my_saved_searches WHERE user_ref = NEW.id LOOP
      UPDATE my_saved_searches
      SET visible_fields_in_result = subq.fields_available
      FROM (select array_to_string(array(select fields_list
                                         from regexp_split_to_table(saved_search_row.visible_fields_in_result, E'\\|') as fields_list
                                         where fields_list not in ('institution_ref','building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                        ),'|'
                                  ) as fields_available
          ) subq
      WHERE id = saved_search_row.id;
    END LOOP;
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_unpromotion_impact_prefs() OWNER TO darwin2;

--
-- TOC entry 572 (class 1255 OID 18073)
-- Name: fct_upd_institution_staging_relationship(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_upd_institution_staging_relationship() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  import_id integer;
  line RECORD ;
BEGIN
 IF get_setting('darwin.upd_people_ref') is null OR  get_setting('darwin.upd_people_ref') = '' THEN
    PERFORM set_config('darwin.upd_people_ref', 'ok', true);
    select s.import_ref INTO import_id FROM staging s, staging_relationship sr WHERE sr.id=OLD.id AND sr.record_id = s.id ;
    UPDATE staging_relationship SET institution_ref = NEW.institution_ref WHERE id IN (
      SELECT sr.id from staging_relationship sr, staging s WHERE sr.institution_name = OLD.institution_name AND s.import_ref = import_id AND
      sr.record_id = s.id
    );
    FOR line IN SELECT s.* FROM staging s, staging_relationship sr WHERE s.id=sr.record_id AND sr.institution_ref = NEW.institution_ref
    LOOP
      UPDATE staging SET status = delete(status,'institution_relationship') where id=line.id;
    END LOOP ;
    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_upd_institution_staging_relationship() OWNER TO darwin2;

--
-- TOC entry 555 (class 1255 OID 18057)
-- Name: fct_upd_people_in_flat(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_upd_people_in_flat() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  spec_row RECORD;
  ident RECORD;
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;


  IF TG_OP = 'DELETE' THEN
    IF OLD.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = fct_remove_array_elem(spec_coll_ids,ARRAY[OLD.people_ref])
        WHERE id  = OLD.record_id;
    ELSIF OLD.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = fct_remove_array_elem(spec_don_sel_ids,ARRAY[OLD.people_ref])
        WHERE id  = OLD.record_id;
    ELSIF OLD.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = OLD.record_id;
      IF NOT FOUND Then
        PERFORM set_config('darwin.userid', tmp_user, false) ;
        RETURN OLD;
      END IF;

      UPDATE specimens s SET spec_ident_ids = fct_remove_array_elem(spec_ident_ids,ARRAY[OLD.people_ref])
        WHERE id  = ident.record_id
            AND NOT exists (
              SELECT true FROM catalogue_people cp INNER JOIN identifications i ON cp.record_id = i.id AND cp.referenced_relation = 'identifications'
                WHERE i.record_id = ident.id AND people_ref = OLD.people_ref AND i.referenced_relation = 'specimens'
            );
    END IF;

  ELSIF TG_OP = 'INSERT' THEN --- INSERT

    IF NEW.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = array_append(spec_coll_ids,NEW.people_ref)
        WHERE id  = NEW.record_id and NOT (spec_coll_ids && ARRAY[ NEW.people_ref::integer ]);
    ELSIF NEW.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = array_append(spec_don_sel_ids,NEW.people_ref)
        WHERE id  = NEW.record_id  and NOT (spec_don_sel_ids && ARRAY[ NEW.people_ref::integer ]);
    ELSIF NEW.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = NEW.record_id;

      UPDATE specimens s SET spec_ident_ids = array_append(spec_ident_ids,NEW.people_ref)
          WHERE id  = ident.record_id and NOT (spec_ident_ids && ARRAY[ NEW.people_ref::integer ]);
    END IF;

  ELSIF OLD.people_ref != NEW.people_ref THEN --UPDATE

    IF NEW.people_type = 'collector' THEN
      UPDATE specimens s SET spec_coll_ids = array_append(fct_remove_array_elem(spec_coll_ids ,ARRAY[OLD.people_ref]),NEW.people_ref::integer)
        WHERE id  = NEW.record_id;
    ELSIF NEW.people_type = 'donator' THEN
      UPDATE specimens s SET spec_don_sel_ids = array_append(fct_remove_array_elem(spec_don_sel_ids ,ARRAY[OLD.people_ref]),NEW.people_ref::integer)
        WHERE id  = NEW.record_id;

    ELSIF NEW.people_type = 'identifier' THEN
      SELECT * into ident FROM identifications where id = NEW.record_id;

        SELECT id, spec_ident_ids INTO spec_row FROM specimens WHERE id = ident.record_id;

        IF NOT exists (SELECT 1 from identifications i INNER JOIN catalogue_people c ON c.record_id = i.id AND c.referenced_relation = 'identifications'
          WHERE i.record_id = spec_row.id AND people_ref = OLD.people_ref AND i.referenced_relation = 'specimens' AND c.id != OLD.id
        ) THEN
          spec_row.spec_ident_ids := fct_remove_array_elem(spec_row.spec_ident_ids ,ARRAY[OLD.people_ref]);
        END IF;

        IF NOT spec_row.spec_ident_ids && ARRAY[ NEW.people_ref::integer ] THEN
          spec_row.spec_ident_ids := array_append(spec_row.spec_ident_ids ,NEW.people_ref);
        END IF;

        UPDATE specimens SET spec_ident_ids = spec_row.spec_ident_ids WHERE id = spec_row.id;
    END IF;
    --else  raise info 'ooh';
  END IF;

  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_upd_people_in_flat() OWNER TO darwin2;

--
-- TOC entry 571 (class 1255 OID 18072)
-- Name: fct_upd_people_staging_fields(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_upd_people_staging_fields() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  import_id integer;
BEGIN
 IF get_setting('darwin.upd_people_ref') is null OR  get_setting('darwin.upd_people_ref') = '' THEN
    PERFORM set_config('darwin.upd_people_ref', 'ok', true);
    IF OLD.referenced_relation = 'staging' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp WHERE sp.id=OLD.id AND sp.record_id = s.id ;
    ELSEIF OLD.referenced_relation = 'identifications' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp, identifications i WHERE sp.id=OLD.id
      AND sp.record_id = i.id AND i.record_id = s.id ;
    ELSE
      select s.import_ref INTO import_id FROM staging s, staging_people sp, collection_maintenance c WHERE sp.id=OLD.id
      AND sp.record_id = c.id AND c.record_id = s.id ;
    END IF;

    UPDATE staging_people SET people_ref = NEW.people_ref WHERE id IN (
      SELECT sp.id from staging_people sp, identifications i, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND i.record_id = s.id AND sp.referenced_relation = 'identifications' AND sp.record_id = i.id
      UNION
      SELECT sp.id from staging_people sp, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id AND
      sp.record_id = s.id AND sp.referenced_relation = 'staging'
      UNION
      SELECT sp.id from staging_people sp, collection_maintenance c, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND c.record_id = s.id AND sp.referenced_relation = 'collection_maintenance' AND sp.record_id = c.id
    );
    -- update status field, if all error people are corrected, statut 'people', 'operator' or 'identifiers' will be removed
    PERFORM fct_imp_checker_people(s.*) FROM staging s WHERE import_ref = import_id AND (status::hstore ? 'people' OR status::hstore ? 'identifiers'  OR status::hstore ? 'operator') ;
    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_upd_people_staging_fields() OWNER TO darwin2;

--
-- TOC entry 573 (class 1255 OID 18074)
-- Name: fct_upd_staging_fields(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_upd_staging_fields() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF get_setting('darwin.upd_imp_ref') is null OR  get_setting('darwin.upd_imp_ref') = '' THEN
    PERFORM set_config('darwin.upd_imp_ref', 'ok', true);
    IF OLD.taxon_ref IS DISTINCT FROM NEW.taxon_ref AND  NEW.taxon_ref is not null THEN
        SELECT t.id ,t.name, t.level_ref , cl.level_sys_name, t.status, t.extinct
        INTO NEW.taxon_ref,NEW.taxon_name, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status, NEW.taxon_extinct
        FROM taxonomy t, catalogue_levels cl
        WHERE cl.id=t.level_ref AND t.id = NEW.taxon_ref;

        UPDATE staging set taxon_ref=NEW.taxon_ref, taxon_name = new.taxon_name, taxon_level_ref=new.taxon_level_ref,
          taxon_level_name=new.taxon_level_name, taxon_status=new.taxon_status, taxon_extinct=new.taxon_extinct,
          status = delete(status,'taxon')

        WHERE
          taxon_name  IS NOT DISTINCT FROM  old.taxon_name AND  taxon_level_ref IS NOT DISTINCT FROM old.taxon_level_ref AND
          taxon_level_name IS NOT DISTINCT FROM old.taxon_level_name AND  taxon_status IS NOT DISTINCT FROM old.taxon_status
          AND  taxon_extinct IS NOT DISTINCT FROM old.taxon_extinct
          AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'taxon');
    END IF;

    IF OLD.chrono_ref IS DISTINCT FROM NEW.chrono_ref  AND  NEW.chrono_ref is not null THEN
      SELECT c.id, c.name, c.level_ref, cl.level_name, c.status, c.local_naming, c.color, c.upper_bound, c.lower_bound
        INTO NEW.chrono_ref, NEW.chrono_name, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status, NEW.chrono_local, NEW.chrono_color, NEW.chrono_upper_bound, NEW.chrono_lower_bound
        FROM chronostratigraphy c, catalogue_levels cl
        WHERE cl.id=c.level_ref AND c.id = NEW.chrono_ref ;

        UPDATE staging set chrono_ref=NEW.chrono_ref, chrono_name = NEW.chrono_name, chrono_level_ref=NEW.chrono_level_ref, chrono_level_name=NEW.chrono_level_name, chrono_status=NEW.chrono_status,
        chrono_local=NEW.chrono_local, chrono_color=NEW.chrono_color, chrono_upper_bound=NEW.chrono_upper_bound, chrono_lower_bound=NEW.chrono_lower_bound,
        status = delete(status,'chrono')

        WHERE
        chrono_name  IS NOT DISTINCT FROM  OLD.chrono_name AND  chrono_level_ref IS NOT DISTINCT FROM OLD.chrono_level_ref AND
        chrono_level_name IS NOT DISTINCT FROM OLD.chrono_level_name AND  chrono_status IS NOT DISTINCT FROM OLD.chrono_status AND
        chrono_local IS NOT DISTINCT FROM OLD.chrono_local AND  chrono_color IS NOT DISTINCT FROM OLD.chrono_color AND
        chrono_upper_bound IS NOT DISTINCT FROM OLD.chrono_upper_bound AND  chrono_lower_bound IS NOT DISTINCT FROM OLD.chrono_lower_bound
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'chrono');

    END IF;

    IF OLD.litho_ref IS DISTINCT FROM NEW.litho_ref  AND  NEW.litho_ref is not null  THEN
      SELECT l.id,l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.litho_ref, NEW.litho_name, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status, NEW.litho_local, NEW.litho_color
      FROM lithostratigraphy l, catalogue_levels cl
      WHERE cl.id=l.level_ref AND l.id = NEW.litho_ref ;

      UPDATE staging set
        litho_ref=NEW.litho_ref, litho_name=NEW.litho_name, litho_level_ref=NEW.litho_level_ref, litho_level_name=NEW.litho_level_name,
        litho_status=NEW.litho_status, litho_local=NEW.litho_local, litho_color=NEW.litho_color,
        status = delete(status,'litho')

      WHERE
        litho_name IS NOT DISTINCT FROM  OLD.litho_name AND litho_level_ref IS NOT DISTINCT FROM  OLD.litho_level_ref AND
        litho_level_name IS NOT DISTINCT FROM  OLD.litho_level_name AND
        litho_status IS NOT DISTINCT FROM  OLD.litho_status AND litho_local IS NOT DISTINCT FROM  OLD.litho_local AND litho_color IS NOT DISTINCT FROM OLD.litho_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'litho');

    END IF;


    IF OLD.lithology_ref IS DISTINCT FROM NEW.lithology_ref  AND  NEW.lithology_ref is not null THEN
      SELECT l.id, l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.lithology_ref, NEW.lithology_name, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status, NEW.lithology_local, NEW.lithology_color
      FROM lithology l, catalogue_levels cl
      WHERE cl.id=l.level_ref AND l.id = NEW.lithology_ref ;

      UPDATE staging set
        lithology_ref=NEW.lithology_ref, lithology_name=NEW.lithology_name, lithology_level_ref=NEW.lithology_level_ref,
        lithology_level_name=NEW.lithology_level_name, lithology_status=NEW.lithology_status, lithology_local=NEW.lithology_local,
        lithology_color=NEW.lithology_color,
        status = delete(status,'lithology')

      WHERE
        lithology_name IS NOT DISTINCT FROM OLD.lithology_name AND  lithology_level_ref IS NOT DISTINCT FROM OLD.lithology_level_ref AND
        lithology_level_name IS NOT DISTINCT FROM OLD.lithology_level_name AND  lithology_status IS NOT DISTINCT FROM OLD.lithology_status AND  lithology_local IS NOT DISTINCT FROM OLD.lithology_local AND
        lithology_color IS NOT DISTINCT FROM OLD.lithology_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'lithology');

    END IF;


    IF OLD.mineral_ref IS DISTINCT FROM NEW.mineral_ref  AND  NEW.mineral_ref is not null THEN
      SELECT m.id, m.name, m.level_ref, cl.level_name, m.status, m.local_naming, m.color, m.path
      INTO NEW.mineral_ref, NEW.mineral_name, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status, NEW.mineral_local, NEW.mineral_color, NEW.mineral_path
      FROM mineralogy m, catalogue_levels cl
      WHERE cl.id=m.level_ref AND m.id = NEW.mineral_ref ;

      UPDATE staging set
        mineral_ref=NEW.mineral_ref, mineral_name=NEW.mineral_name, mineral_level_ref=NEW.mineral_level_ref,
        mineral_level_name=NEW.mineral_level_name, mineral_status=NEW.mineral_status, mineral_local=NEW.mineral_local,
        mineral_color=NEW.mineral_color, mineral_path=NEW.mineral_path,
        status = delete(status,'mineral')

      WHERE
        mineral_name IS NOT DISTINCT FROM OLD.mineral_name AND  mineral_level_ref IS NOT DISTINCT FROM OLD.mineral_level_ref AND
        mineral_level_name IS NOT DISTINCT FROM OLD.mineral_level_name AND  mineral_status IS NOT DISTINCT FROM OLD.mineral_status AND  mineral_local IS NOT DISTINCT FROM OLD.mineral_local AND
        mineral_color IS NOT DISTINCT FROM OLD.mineral_color AND  mineral_path IS NOT DISTINCT FROM OLD.mineral_path
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'mineral');

    END IF;

    IF OLD.expedition_ref IS DISTINCT FROM NEW.expedition_ref  AND  NEW.expedition_ref is not null THEN
      SELECT id, "name", expedition_from_date, expedition_to_date, expedition_from_date_mask , expedition_to_date_mask
      INTO NEW.expedition_ref, NEW.expedition_name, NEW.expedition_from_date, NEW.expedition_to_date, NEW.expedition_from_date_mask , NEW.expedition_to_date_mask
      FROM expeditions
      WHERE id = NEW.expedition_ref ;

      UPDATE staging set
        expedition_ref=NEW.expedition_ref, expedition_name=NEW.expedition_name, expedition_from_date=NEW.expedition_from_date,
        expedition_to_date=NEW.expedition_to_date, expedition_from_date_mask=NEW.expedition_from_date_mask , expedition_to_date_mask=NEW.expedition_to_date_mask
      WHERE
        expedition_name IS NOT DISTINCT FROM OLD.expedition_name AND  expedition_from_date IS NOT DISTINCT FROM OLD.expedition_from_date AND
        expedition_to_date IS NOT DISTINCT FROM OLD.expedition_to_date AND  expedition_from_date_mask IS NOT DISTINCT FROM OLD.expedition_from_date_mask  AND
        expedition_to_date_mask IS NOT DISTINCT FROM OLD.expedition_to_date_mask
        AND import_ref = NEW.import_ref;

    END IF;

    IF OLD.institution_ref IS DISTINCT FROM NEW.institution_ref  AND  NEW.institution_ref is not null THEN
      SELECT formated_name INTO NEW.institution_name FROM people WHERE id = NEW.institution_ref ;

      UPDATE staging set institution_ref = NEW.institution_ref, institution_name=NEW.institution_name,
        status = delete(status,'institution')
        WHERE
        institution_name IS NOT DISTINCT FROM OLD.institution_name
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'institution');

    END IF;

    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_upd_staging_fields() OWNER TO darwin2;

--
-- TOC entry 1752 (class 1255 OID 18034)
-- Name: fct_update_specimen_flat(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_update_specimen_flat() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  cnt integer;
  old_val specimens%ROWTYPE;
  new_val specimens%ROWTYPE;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      old_val = OLD;
      new_val = NEW;
    ELSE --INSERT
      new_val = NEW;
    END IF;

    IF old_val.taxon_ref IS DISTINCT FROM new_val.taxon_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, path, parent_ref, extinct
        INTO NEW.taxon_name, NEW.taxon_name_indexed, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status,
          NEW.taxon_path, NEW.taxon_parent_ref, NEW.taxon_extinct
        FROM taxonomy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.taxon_ref;
    END IF;

    IF old_val.chrono_ref IS DISTINCT FROM new_val.chrono_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
      INTO NEW.chrono_name, NEW.chrono_name_indexed, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status,
          NEW.chrono_local, NEW.chrono_color, NEW.chrono_path, NEW.chrono_parent_ref
        FROM chronostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.chrono_ref;
    END IF;

    IF old_val.litho_ref IS DISTINCT FROM new_val.litho_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.litho_name, NEW.litho_name_indexed, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status,
          NEW.litho_local, NEW.litho_color, NEW.litho_path, NEW.litho_parent_ref
        FROM lithostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.litho_ref;
    END IF;

    IF old_val.lithology_ref IS DISTINCT FROM new_val.lithology_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.lithology_name, NEW.lithology_name_indexed, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status,
          NEW.lithology_local, NEW.lithology_color, NEW.lithology_path, NEW.lithology_parent_ref
        FROM lithology c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.lithology_ref;
    END IF;

    IF old_val.mineral_ref IS DISTINCT FROM new_val.mineral_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.mineral_name, NEW.mineral_name_indexed, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status,
          NEW.mineral_local, NEW.mineral_color, NEW.mineral_path, NEW.mineral_parent_ref
        FROM mineralogy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.mineral_ref;
    END IF;


    IF old_val.expedition_ref IS DISTINCT FROM new_val.expedition_ref THEN
      SELECT  name, name_indexed
        INTO NEW.expedition_name, NEW.expedition_name_indexed
        FROM expeditions c
        WHERE c.id = new_val.expedition_ref;
    END IF;

    IF old_val.collection_ref IS DISTINCT FROM new_val.collection_ref THEN
      SELECT collection_type, code, name, is_public, parent_ref, path
        INTO NEW.collection_type, NEW.collection_code, NEW.collection_name, NEW.collection_is_public,
          NEW.collection_parent_ref, NEW.collection_path
        FROM collections c
        WHERE c.id = new_val.collection_ref;
    END IF;

    IF old_val.ig_ref IS DISTINCT FROM new_val.ig_ref THEN
      SELECT  ig_num, ig_num_indexed, ig_date, ig_date_mask
        INTO NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask
        FROM igs c
        WHERE c.id = new_val.ig_ref;
    END IF;

    IF old_val.gtu_ref IS DISTINCT FROM new_val.gtu_ref THEN
  
        --ftheeten 2016 07 07
        SELECT  code,
         elevation, elevation_accuracy,
         tag_values_indexed, location,

         taggr_countries.tag_value, lineToTagArray(taggr_countries.tag_value),
         taggr_provinces.tag_value, lineToTagArray(taggr_provinces.tag_value),
         (select array_to_string(array(select tag from tags where gtu_ref = c.id and LOWER(sub_group_type) not in ('country', 'province')), ';')) as other_gtu_values,
         (select array(select distinct fullToIndex(tag) from tags where gtu_ref = c.id and LOWER(sub_group_type) not in ('country', 'province'))) as other_gtu_values_array

        INTO NEW.gtu_code,
         NEW.gtu_elevation, NEW.gtu_elevation_accuracy, NEW.gtu_tag_values_indexed, NEW.gtu_location,
         NEW.gtu_country_tag_value, NEW.gtu_country_tag_indexed, NEW.gtu_province_tag_value,
         NEW.gtu_province_tag_indexed, NEW.gtu_others_tag_value, NEW.gtu_others_tag_indexed
        FROM gtu c
          LEFT JOIN tag_groups taggr_countries ON c.id = taggr_countries.gtu_ref AND taggr_countries.group_name_indexed = 'administrativearea' AND taggr_countries.sub_group_name_indexed = 'country'
          LEFT JOIN tag_groups taggr_provinces ON c.id = taggr_provinces.gtu_ref AND taggr_provinces.group_name_indexed = 'administrativearea' AND taggr_provinces.sub_group_name_indexed = 'province'
        WHERE c.id = new_val.gtu_ref;
    END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_update_specimen_flat() OWNER TO darwin2;

--
-- TOC entry 1714 (class 1255 OID 233116)
-- Name: fct_update_specimen_flat_bck20160713(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_update_specimen_flat_bck20160713() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  cnt integer;
  old_val specimens%ROWTYPE;
  new_val specimens%ROWTYPE;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      old_val = OLD;
      new_val = NEW;
    ELSE --INSERT
      new_val = NEW;
    END IF;

    IF old_val.taxon_ref IS DISTINCT FROM new_val.taxon_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, path, parent_ref, extinct
        INTO NEW.taxon_name, NEW.taxon_name_indexed, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status,
          NEW.taxon_path, NEW.taxon_parent_ref, NEW.taxon_extinct
        FROM taxonomy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.taxon_ref;
    END IF;

    IF old_val.chrono_ref IS DISTINCT FROM new_val.chrono_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
      INTO NEW.chrono_name, NEW.chrono_name_indexed, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status,
          NEW.chrono_local, NEW.chrono_color, NEW.chrono_path, NEW.chrono_parent_ref
        FROM chronostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.chrono_ref;
    END IF;

    IF old_val.litho_ref IS DISTINCT FROM new_val.litho_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.litho_name, NEW.litho_name_indexed, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status,
          NEW.litho_local, NEW.litho_color, NEW.litho_path, NEW.litho_parent_ref
        FROM lithostratigraphy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.litho_ref;
    END IF;

    IF old_val.lithology_ref IS DISTINCT FROM new_val.lithology_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.lithology_name, NEW.lithology_name_indexed, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status,
          NEW.lithology_local, NEW.lithology_color, NEW.lithology_path, NEW.lithology_parent_ref
        FROM lithology c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.lithology_ref;
    END IF;

    IF old_val.mineral_ref IS DISTINCT FROM new_val.mineral_ref THEN
      SELECT  name, name_indexed, level_ref, level_name, status, local_naming, color, path, parent_ref
        INTO NEW.mineral_name, NEW.mineral_name_indexed, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status,
          NEW.mineral_local, NEW.mineral_color, NEW.mineral_path, NEW.mineral_parent_ref
        FROM mineralogy c
        INNER JOIN catalogue_levels l on c.level_ref = l.id
        WHERE c.id = new_val.mineral_ref;
    END IF;


    IF old_val.expedition_ref IS DISTINCT FROM new_val.expedition_ref THEN
      SELECT  name, name_indexed
        INTO NEW.expedition_name, NEW.expedition_name_indexed
        FROM expeditions c
        WHERE c.id = new_val.expedition_ref;
    END IF;

    IF old_val.collection_ref IS DISTINCT FROM new_val.collection_ref THEN
      SELECT collection_type, code, name, is_public, parent_ref, path
        INTO NEW.collection_type, NEW.collection_code, NEW.collection_name, NEW.collection_is_public,
          NEW.collection_parent_ref, NEW.collection_path
        FROM collections c
        WHERE c.id = new_val.collection_ref;
    END IF;

    IF old_val.ig_ref IS DISTINCT FROM new_val.ig_ref THEN
      SELECT  ig_num, ig_num_indexed, ig_date, ig_date_mask
        INTO NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask
        FROM igs c
        WHERE c.id = new_val.ig_ref;
    END IF;

    IF old_val.gtu_ref IS DISTINCT FROM new_val.gtu_ref THEN
      SELECT  code, gtu_from_date, gtu_from_date_mask,
         gtu_to_date, gtu_to_date_mask,
         elevation, elevation_accuracy,
         tag_values_indexed, location,

         taggr_countries.tag_value, lineToTagArray(taggr_countries.tag_value),
         taggr_provinces.tag_value, lineToTagArray(taggr_provinces.tag_value),
         (select array_to_string(array(select tag from tags where gtu_ref = c.id and sub_group_type not in ('country', 'province')), ';')) as other_gtu_values,
         (select array(select distinct fullToIndex(tag) from tags where gtu_ref = c.id and sub_group_type not in ('country', 'province'))) as other_gtu_values_array

        INTO NEW.gtu_code, NEW.gtu_from_date, NEW.gtu_from_date_mask, NEW.gtu_to_date, NEW.gtu_to_date_mask,
         NEW.gtu_elevation, NEW.gtu_elevation_accuracy, NEW.gtu_tag_values_indexed, NEW.gtu_location,
         NEW.gtu_country_tag_value, NEW.gtu_country_tag_indexed, NEW.gtu_province_tag_value,
         NEW.gtu_province_tag_indexed, NEW.gtu_others_tag_value, NEW.gtu_others_tag_indexed
        FROM gtu c
          LEFT JOIN tag_groups taggr_countries ON c.id = taggr_countries.gtu_ref AND taggr_countries.group_name_indexed = 'administrativearea' AND taggr_countries.sub_group_name_indexed = 'country'
          LEFT JOIN tag_groups taggr_provinces ON c.id = taggr_provinces.gtu_ref AND taggr_provinces.group_name_indexed = 'administrativearea' AND taggr_provinces.sub_group_name_indexed = 'province'
        WHERE c.id = new_val.gtu_ref;
    END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_update_specimen_flat_bck20160713() OWNER TO darwin2;

--
-- TOC entry 541 (class 1255 OID 18032)
-- Name: fct_update_specimens_flat_related(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_update_specimens_flat_related() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  indCount INTEGER := 0;
  indType BOOLEAN := false;
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;

  IF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'expeditions' THEN
    IF NEW.name_indexed IS DISTINCT FROM OLD.name_indexed THEN
      UPDATE specimens
      SET (expedition_name, expedition_name_indexed) =
          (NEW.name, NEW.name_indexed)
      WHERE expedition_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'collections' THEN
    IF OLD.collection_type IS DISTINCT FROM NEW.collection_type
    OR OLD.code IS DISTINCT FROM NEW.code
    OR OLD.name IS DISTINCT FROM NEW.name
    OR OLD.is_public IS DISTINCT FROM NEW.is_public
    OR OLD.path IS DISTINCT FROM NEW.path
    THEN
      UPDATE specimens
      SET (collection_type, collection_code, collection_name, collection_is_public,
          collection_parent_ref, collection_path
          ) =
          (NEW.collection_type, NEW.code, NEW.name, NEW.is_public,
           NEW.parent_ref, NEW.path
          )
      WHERE collection_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'gtu' THEN
   /* UPDATE specimens
    SET (gtu_code, gtu_from_date, gtu_from_date_mask,
         gtu_to_date, gtu_to_date_mask,
         gtu_elevation, gtu_elevation_accuracy,
         gtu_tag_values_indexed, gtu_location
        ) =
        (NEW.code, NEW.gtu_from_date, NEW.gtu_from_date_mask,
         NEW.gtu_to_date, NEW.gtu_to_date_mask,
         NEW.elevation, NEW.elevation_accuracy,
         NEW.tag_values_indexed, NEW.location
        )
    WHERE gtu_ref = NEW.id;*/
    --ftheeten 206 017 07
    UPDATE specimens
    SET (gtu_code,
         gtu_elevation, gtu_elevation_accuracy,
         gtu_tag_values_indexed, gtu_location
        ) =
        (NEW.code, 
         NEW.elevation, NEW.elevation_accuracy,
         NEW.tag_values_indexed, NEW.location
        )
    WHERE gtu_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'igs' THEN
    IF NEW.ig_num_indexed IS DISTINCT FROM OLD.ig_num_indexed OR NEW.ig_date IS DISTINCT FROM OLD.ig_date THEN
      UPDATE specimens
      SET (ig_num, ig_num_indexed, ig_date, ig_date_mask) =
          (NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask)
      WHERE ig_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'taxonomy' THEN
    UPDATE specimens
    SET (taxon_name, taxon_name_indexed,
         taxon_level_ref, taxon_level_name,
         taxon_status, taxon_path, taxon_parent_ref, taxon_extinct
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status, NEW.path, NEW.parent_ref, NEW.extinct
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE taxon_ref = NEW.id;

  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'chronostratigraphy' THEN
    UPDATE specimens
    SET (chrono_name, chrono_name_indexed,
         chrono_level_ref, chrono_level_name,
         chrono_status,
         chrono_local, chrono_color,
         chrono_path, chrono_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE chrono_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithostratigraphy' THEN
    UPDATE specimens
    SET (litho_name, litho_name_indexed,
         litho_level_ref, litho_level_name,
         litho_status,
         litho_local, litho_color,
         litho_path, litho_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE litho_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithology' THEN
    UPDATE specimens
    SET (lithology_name, lithology_name_indexed,
         lithology_level_ref, lithology_level_name,
         lithology_status,
         lithology_local, lithology_color,
         lithology_path, lithology_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE lithology_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'mineralogy' THEN
    UPDATE specimens
    SET (mineral_name, mineral_name_indexed,
         mineral_level_ref, mineral_level_name,
         mineral_status,
         mineral_local, mineral_color,
         mineral_path, mineral_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE mineral_ref = NEW.id;

  ELSIF TG_TABLE_NAME = 'tag_groups' THEN
    IF TG_OP = 'INSERT' THEN
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
      /* ftheeten 2017 04 03 handle space in specimens
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;*/
         UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag, true) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' AND NEW.sub_group_name_indexed != 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' AND NEW.sub_group_name_indexed != 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END ||NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
      /* ftheeten 2017  04 03 handle spaces in specimens gtu
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;*/
         UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag,true) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'DELETE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSE
        /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        /* ftheeten 2017 04 03 handle space in specimens gtu
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = OLD.gtu_ref;*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag,true) from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = OLD.gtu_ref;
      END IF;
    END IF;
  END IF;
  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_update_specimens_flat_related() OWNER TO darwin2;

--
-- TOC entry 1711 (class 1255 OID 233113)
-- Name: fct_update_specimens_flat_related_bck20160713(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fct_update_specimens_flat_related_bck20160713() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  indCount INTEGER := 0;
  indType BOOLEAN := false;
  tmp_user text;
BEGIN
 SELECT COALESCE(get_setting('darwin.userid'),'0') INTO tmp_user;
  PERFORM set_config('darwin.userid', '-1', false) ;

  IF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'expeditions' THEN
    IF NEW.name_indexed IS DISTINCT FROM OLD.name_indexed THEN
      UPDATE specimens
      SET (expedition_name, expedition_name_indexed) =
          (NEW.name, NEW.name_indexed)
      WHERE expedition_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'collections' THEN
    IF OLD.collection_type IS DISTINCT FROM NEW.collection_type
    OR OLD.code IS DISTINCT FROM NEW.code
    OR OLD.name IS DISTINCT FROM NEW.name
    OR OLD.is_public IS DISTINCT FROM NEW.is_public
    OR OLD.path IS DISTINCT FROM NEW.path
    THEN
      UPDATE specimens
      SET (collection_type, collection_code, collection_name, collection_is_public,
          collection_parent_ref, collection_path
          ) =
          (NEW.collection_type, NEW.code, NEW.name, NEW.is_public,
           NEW.parent_ref, NEW.path
          )
      WHERE collection_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'gtu' THEN
    UPDATE specimens
    SET (gtu_code, gtu_from_date, gtu_from_date_mask,
         gtu_to_date, gtu_to_date_mask,
         gtu_elevation, gtu_elevation_accuracy,
         gtu_tag_values_indexed, gtu_location
        ) =
        (NEW.code, NEW.gtu_from_date, NEW.gtu_from_date_mask,
         NEW.gtu_to_date, NEW.gtu_to_date_mask,
         NEW.elevation, NEW.elevation_accuracy,
         NEW.tag_values_indexed, NEW.location
        )
    WHERE gtu_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'igs' THEN
    IF NEW.ig_num_indexed IS DISTINCT FROM OLD.ig_num_indexed OR NEW.ig_date IS DISTINCT FROM OLD.ig_date THEN
      UPDATE specimens
      SET (ig_num, ig_num_indexed, ig_date, ig_date_mask) =
          (NEW.ig_num, NEW.ig_num_indexed, NEW.ig_date, NEW.ig_date_mask)
      WHERE ig_ref = NEW.id;
    END IF;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'taxonomy' THEN
    UPDATE specimens
    SET (taxon_name, taxon_name_indexed,
         taxon_level_ref, taxon_level_name,
         taxon_status, taxon_path, taxon_parent_ref, taxon_extinct
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status, NEW.path, NEW.parent_ref, NEW.extinct
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE taxon_ref = NEW.id;

  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'chronostratigraphy' THEN
    UPDATE specimens
    SET (chrono_name, chrono_name_indexed,
         chrono_level_ref, chrono_level_name,
         chrono_status,
         chrono_local, chrono_color,
         chrono_path, chrono_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE chrono_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithostratigraphy' THEN
    UPDATE specimens
    SET (litho_name, litho_name_indexed,
         litho_level_ref, litho_level_name,
         litho_status,
         litho_local, litho_color,
         litho_path, litho_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE litho_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'lithology' THEN
    UPDATE specimens
    SET (lithology_name, lithology_name_indexed,
         lithology_level_ref, lithology_level_name,
         lithology_status,
         lithology_local, lithology_color,
         lithology_path, lithology_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE lithology_ref = NEW.id;
  ELSIF TG_OP = 'UPDATE' AND TG_TABLE_NAME = 'mineralogy' THEN
    UPDATE specimens
    SET (mineral_name, mineral_name_indexed,
         mineral_level_ref, mineral_level_name,
         mineral_status,
         mineral_local, mineral_color,
         mineral_path, mineral_parent_ref
        ) =
        (NEW.name, NEW.name_indexed,
         NEW.level_ref, subq.level_name,
         NEW.status,
         NEW.local_naming, NEW.color,
         NEW.path, NEW.parent_ref
        )
        FROM
        (SELECT level_name
         FROM catalogue_levels
         WHERE id = NEW.level_ref
        ) subq
    WHERE mineral_ref = NEW.id;

  ELSIF TG_TABLE_NAME = 'tag_groups' THEN
    IF TG_OP = 'INSERT' THEN
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'UPDATE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' AND NEW.sub_group_name_indexed != 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' AND NEW.sub_group_name_indexed != 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_country_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END ||NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      ELSIF NEW.group_name_indexed = 'administrativearea' AND NEW.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value,
            gtu_province_tag_indexed = lineToTagArray(case when NEW.international_name != '' THEN NEW.international_name || ';' ELSE '' END || NEW.tag_value)
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
      IF NEW.sub_group_name_indexed NOT IN ('country','province') THEN
      /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = NEW.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = NEW.gtu_ref;
      END IF;
    ELSIF TG_OP = 'DELETE' THEN
      IF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'country' THEN
        UPDATE specimens
        SET gtu_country_tag_value = NULL,
            gtu_country_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSIF OLD.group_name_indexed = 'administrativearea' AND OLD.sub_group_name_indexed = 'province' THEN
        UPDATE specimens
        SET gtu_province_tag_value = NULL,
            gtu_province_tag_indexed = NULL
        WHERE gtu_ref = OLD.gtu_ref;
      ELSE
        /*Trigger trg_cpy_gtutags_taggroups has already occured and values from tags table should be correct... but really need a check !*/
        UPDATE specimens
        SET gtu_others_tag_value = (select array_to_string(array(select tag from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')), ';')),
            gtu_others_tag_indexed = (select array(select distinct fullToIndex(tag) from tags where gtu_ref = OLD.gtu_ref and sub_group_type not in ('country', 'province')))
        WHERE gtu_ref = OLD.gtu_ref;
      END IF;
    END IF;
  END IF;
  PERFORM set_config('darwin.userid', tmp_user, false) ;
  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.fct_update_specimens_flat_related_bck20160713() OWNER TO darwin2;

--
-- TOC entry 1742 (class 1255 OID 251060)
-- Name: filter_2_arrays_by_key(character varying[], character varying[], character varying[], boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION filter_2_arrays_by_key(p_array_values character varying[], p_array_keys character varying[], p_array_selected_keys character varying[], exclude_mode boolean DEFAULT false) RETURNS character varying[]
    LANGUAGE plpgsql
    AS $$
DECLARE
	returned varchar[];
	i integer;
	
BEGIN 
	
	IF ARRAY_LENGTH(p_array_values ,1)=ARRAY_LENGTH(p_array_keys ,1)  THEN
		FOR i IN 1 .. ARRAY_UPPER(p_array_values,1 )
		LOOP
			IF exclude_mode =false THEN
				IF p_array_keys[i] = any(p_array_selected_keys) THEN
					returned:=returned||p_array_values[i];
				END IF;
			ELSE
				IF p_array_keys[i] <> all(p_array_selected_keys) THEN
					returned:=returned||p_array_values[i];
				END IF;
			END IF;
		END LOOP;
	END IF;
	RETURN returned;

END;
$$;


ALTER FUNCTION darwin2.filter_2_arrays_by_key(p_array_values character varying[], p_array_keys character varying[], p_array_selected_keys character varying[], exclude_mode boolean) OWNER TO darwin2;

--
-- TOC entry 597 (class 1255 OID 17989)
-- Name: fulltoindex(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fulltoindex(to_indexed character varying) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
BEGIN
   return fulltoindex(to_indexed, false);
END;
$$;


ALTER FUNCTION darwin2.fulltoindex(to_indexed character varying) OWNER TO darwin2;

--
-- TOC entry 619 (class 1255 OID 108322)
-- Name: fulltoindex(character varying, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fulltoindex(to_indexed character varying, keep_space boolean) RETURNS character varying
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
-- TOC entry 1743 (class 1255 OID 410663)
-- Name: fulltoindex_array(character varying[]); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fulltoindex_array(to_indexed character varying[]) RETURNS character varying[]
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
BEGIN
   return fulltoindex_array(to_indexed, false);
END;
$$;


ALTER FUNCTION darwin2.fulltoindex_array(to_indexed character varying[]) OWNER TO darwin2;

--
-- TOC entry 1741 (class 1255 OID 410627)
-- Name: fulltoindex_array(character varying[], boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION fulltoindex_array(to_indexed character varying[], keep_space boolean) RETURNS character varying[]
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $$
DECLARE
    temp_string varchar[];
    i int;
BEGIN
	temp_string:='{}';
	FOR i IN 1 .. array_upper(to_indexed, 1)
	LOOP
		if to_indexed[i] <> '' then
		temp_string:=temp_string||fulltoindex(to_indexed[i], keep_space);
		end if;
	END LOOP;
    return temp_string;
END;
$$;


ALTER FUNCTION darwin2.fulltoindex_array(to_indexed character varying[], keep_space boolean) OWNER TO darwin2;

--
-- TOC entry 557 (class 1255 OID 18059)
-- Name: get_import_row(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION get_import_row() RETURNS integer
    LANGUAGE sql SECURITY DEFINER
    AS $$

UPDATE imports SET state = 'loading' FROM (
  SELECT * FROM (
    SELECT  * FROM imports i1 WHERE i1.state = 'to_be_loaded' ORDER BY i1.created_at asc, id asc OFFSET 0 --thats important
  ) i2
  WHERE pg_try_advisory_lock('imports'::regclass::integer, i2.id)
  LIMIT 1
) i3
WHERE imports.id = i3.id RETURNING i3.id;
$$;


ALTER FUNCTION darwin2.get_import_row() OWNER TO darwin2;

--
-- TOC entry 509 (class 1255 OID 18006)
-- Name: get_setting(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION get_setting(param text, OUT value text) RETURNS text
    LANGUAGE plpgsql STABLE STRICT
    AS $$BEGIN
  SELECT current_setting(param) INTO value;
  EXCEPTION
  WHEN UNDEFINED_OBJECT THEN
    value := NULL;
END;$$;


ALTER FUNCTION darwin2.get_setting(param text, OUT value text) OWNER TO darwin2;

--
-- TOC entry 548 (class 1255 OID 18049)
-- Name: getspecificparentforlevel(character varying, character varying, character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION getspecificparentforlevel(referenced_relation character varying, path character varying, level_searched character varying) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE
    AS $$
DECLARE
  response template_classifications.name%TYPE := '';
BEGIN
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
EXCEPTION
  WHEN OTHERS THEN
    RAISE WARNING 'Error in getSpecificParentForLevel: %', SQLERRM;
    RETURN response;
END;
$$;


ALTER FUNCTION darwin2.getspecificparentforlevel(referenced_relation character varying, path character varying, level_searched character varying) OWNER TO darwin2;

--
-- TOC entry 533 (class 1255 OID 18031)
-- Name: gettagsindexedasarray(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION gettagsindexedasarray(taglist character varying) RETURNS character varying[]
    LANGUAGE sql IMMUTABLE
    AS $_$
  SELECT array_agg(tags) FROM (SELECT lineToTagRows($1) as tags) as subQuery;
$_$;


ALTER FUNCTION darwin2.gettagsindexedasarray(taglist character varying) OWNER TO darwin2;

--
-- TOC entry 522 (class 1255 OID 18016)
-- Name: is_property_unit_in_group(text, text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION is_property_unit_in_group(searched_unit text, property_unit text) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$

  SELECT CASE
  WHEN $1 IN ('Kt', 'Beaufort', 'm/s')
    AND  $2  IN ('Kt', 'Beaufort', 'm/s')
    THEN TRUE
  WHEN $1 IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    AND  $2  IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    THEN TRUE

  WHEN $1 IN ('m³', 'l', 'cm³', 'ml', 'mm³' ,'µl' , 'µm³' , 'km³', 'Ml' , 'hl')
    AND  $2  IN ( 'g', 'hg', 'kg', 'ton', 'dg', 'cg', 'mg', 'lb', 'lbs', 'pound' , 'ounce' , 'grain')
    THEN TRUE

  WHEN $1 IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De')
    AND  $2  IN ('K', '°C', '°F', '°Ra', '°Re', '°r', '°N', '°Rø', '°De')
    THEN TRUE

  WHEN $1 IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms')
    AND  $2  IN ('m', 'dm', 'cm', 'mm', 'µm', 'nm', 'pm', 'fm', 'am', 'zm', 'ym', 'am', 'dam', 'hm', 'km', 'Mm', 'Gm', 'Tm', 'Pm', 'Em', 'Zm', 'Ym', 'mam', 'mom', 'Å', 'ua', 'ch', 'fathom', 'fermi', 'ft', 'in', 'K', 'l.y.', 'ly', 'µ', 'mil', 'mi', 'nautical mi', 'pc', 'point', 'pt', 'pica', 'rd', 'yd', 'arp', 'lieue', 'league', 'cal', 'twp', 'p', 'P', 'fur', 'brasse', 'vadem', 'fms')
    THEN TRUE
  ELSE FALSE END;
$_$;


ALTER FUNCTION darwin2.is_property_unit_in_group(searched_unit text, property_unit text) OWNER TO darwin2;

--
-- TOC entry 584 (class 1255 OID 18089)
-- Name: isnumeric(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION isnumeric(text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE
    AS $_$
DECLARE x NUMERIC;
BEGIN
    x = $1::NUMERIC;
    RETURN TRUE;
EXCEPTION WHEN others THEN
    RETURN FALSE;
END;
$_$;


ALTER FUNCTION darwin2.isnumeric(text) OWNER TO darwin2;

--
-- TOC entry 529 (class 1255 OID 18026)
-- Name: linetotagarray(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION linetotagarray(line text) RETURNS character varying[]
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
select array_agg(tags_list) FROM (SELECT lineToTagRows($1) AS tags_list ) as x;
$_$;


ALTER FUNCTION darwin2.linetotagarray(line text) OWNER TO darwin2;

--
-- TOC entry 520 (class 1255 OID 18024)
-- Name: linetotagrows(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION linetotagrows(line text) RETURNS SETOF character varying
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT distinct(fulltoIndex(tags)) FROM regexp_split_to_table($1, ';') as tags WHERE fulltoIndex(tags) != '' ;
$_$;


ALTER FUNCTION darwin2.linetotagrows(line text) OWNER TO darwin2;

--
-- TOC entry 528 (class 1255 OID 18025)
-- Name: linetotagrowsformatconserved(text); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION linetotagrowsformatconserved(line text) RETURNS SETOF character varying
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT distinct on (fulltoIndex(tags)) tags FROM regexp_split_to_table($1, ';') as tags WHERE fulltoIndex(tags) != '' ;
$_$;


ALTER FUNCTION darwin2.linetotagrowsformatconserved(line text) OWNER TO darwin2;

--
-- TOC entry 582 (class 1255 OID 18087)
-- Name: point_equal(point, point); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION point_equal(point, point) RETURNS boolean
    LANGUAGE sql IMMUTABLE
    AS $_$SELECT
CASE WHEN $1[0] = $2[0] AND $1[1] = $2[1] THEN true
ELSE false END;$_$;


ALTER FUNCTION darwin2.point_equal(point, point) OWNER TO darwin2;

--
-- TOC entry 629 (class 1255 OID 108374)
-- Name: rmca_create_links_between_labels(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION rmca_create_links_between_labels(p_coll_ref integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$
BEGIN 



INSERT INTO specimens_relationships
(specimen_ref, relationship_type, unit_type, specimen_related_ref, unit)
SELECT   bsp.id, 'other_identification', 'specimens', csp.id, '%'
FROM specimens a
  INNER JOIN 
	codes b
	    ON a.id=b.record_id
	      AND b.referenced_relation='specimens'
	        and a.collection_ref=p_coll_ref 
	        and b.code_category='main'
	        
    INNER JOIN 
	codes c
	    ON c.code similar to regexp_replace(b.code, '\_id\_[a-z]','', 'g')||'_id_[a-z]'
	      AND c.referenced_relation='specimens'
	         
	        and c.code_category='main'
                and b.id<>c.id
     INNER JOIN specimens bsp
     ON bsp.id=b.record_id
      and bsp.collection_ref=p_coll_ref 
     INNER JOIN specimens csp
     ON csp.id=c.record_id   
     and
     csp.collection_ref=p_coll_ref    
where b.code similar to '%\_id\_[a-z]';

/* effacer le diff du code */

UPDATE codes SET code=code_without_label_diff

FROM (

SELECT b.id, b.code, regexp_replace(b.code,  '\_id\_[a-z]$', '', 'g') as code_without_label_diff FROM specimens a
  INNER JOIN codes b
	    ON a.id=b.record_id
	      AND b.referenced_relation='specimens'
	        and a.collection_ref=p_coll_ref and code_category='main'
	         where code similar to '%\_id\_[a-z]') foo
	         where foo.id=codes.id


;

END;
$_$;


ALTER FUNCTION darwin2.rmca_create_links_between_labels(p_coll_ref integer) OWNER TO darwin2;

--
-- TOC entry 628 (class 1255 OID 108372)
-- Name: rmca_create_missing_people_in_staging(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION rmca_create_missing_people_in_staging(p_import_ref integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
DECLARE
	curs1 record;
	tmpid int;
	
BEGIN 
	DROP TABLE if EXISTs tmp_people_import_rmca;
	CREATE TEMPORARY TABLE tmp_people_import_rmca(pk int, name varchar);
	RAISE NOTICE 'Different peoples %', (SELECT COUNT(DISTINCT formated_name) from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref);
	RAISE NOTICE 'linked specimens to be imported %', (SELECT COUNT(formated_name) from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref);
	FOR curs1 IN SELECT DISTINCT formated_name from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref 
		/*UNION
		SELECT distinct formated_name from staging a
		inner join codes b
		on b.referenced_relation='staging'
		and a.id=b.record_id
		INNER JOIN identifications c
		ON c.record_id=a.id
		AND c.referenced_relation='staging'
		INNER JOIN
		staging_people d
		ON d.referenced_relation='identifications'
		AND c.id=d.record_id
		and people_ref is null
		where a.to_import='f' 

		and import_ref=p_import_ref
		*/
		

		LOOP
		
		RAISE NOTICE '%', curs1.formated_name;
		RAISE NOTICE 'people with this name %', (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) );
		RAISE NOTICE 'people split %',  (SELECT regexp_split_to_array(curs1.formated_name, ' '));
		IF  (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) )=0 THEN
            INSERT INTO people (family_name) VALUES (curs1.formated_name) RETURNING id INTO tmpid;
            INSERT INTO tmp_people_import_rmca (pk, name) VALUES(tmpid, curs1.formated_name);
		ELSE
           INSERT INTO tmp_people_import_rmca (pk, name) SELECT id, family_name FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) LIMIT 1;
        END IF;
		
	END LOOP;
	DELETE FROM tmp_people_import_rmca;

	RAISE NOTICE  'GO identifications';
	UPDATE staging_people SET people_ref=tmp_people_import_rmca.pk FROM (SELECT pk, name FROM tmp_people_import_rmca ) AS tmp_people_import_rmca WHERE staging_people.formated_name=tmp_people_import_rmca.name 
	and referenced_relation='staging'
		
		and people_ref is null
		and record_id IN (SELECT id FROM staging WHERE import_ref=p_import_ref AND to_import='f' )
		
		;




		FOR curs1 IN 
		SELECT distinct formated_name from staging a
		inner join codes b
		on b.referenced_relation='staging'
		and a.id=b.record_id
		INNER JOIN identifications c
		ON c.record_id=a.id
		AND c.referenced_relation='staging'
		INNER JOIN
		staging_people d
		ON d.referenced_relation='identifications'
		AND c.id=d.record_id
		and people_ref is null
		where a.to_import='f' 

		and import_ref=p_import_ref
		
		

		LOOP
		
		RAISE NOTICE '%', curs1.formated_name;
		RAISE NOTICE 'people ident with this name %', (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) );
		RAISE NOTICE 'people ident split %',  (SELECT regexp_split_to_array(curs1.formated_name, ' '));
		IF  (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) )=0 THEN
            RAISE NOTICE 'INSERT %', curs1.formated_name;
                INSERT INTO people (family_name) VALUES (curs1.formated_name) RETURNING id INTO tmpid;
                INSERT INTO tmp_people_import_rmca (pk, name) VALUES(tmpid, curs1.formated_name);
		ELSE    
            INSERT INTO tmp_people_import_rmca (pk, name) SELECT id, family_name FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) LIMIT 1;
        END IF;
		
	END LOOP;
		UPDATE staging_people SET people_ref=tmp_people_import_rmca_alias.id FROM (SELECT id, family_name FROM people) AS tmp_people_import_rmca_alias 
		WHERE formated_name=tmp_people_import_rmca_alias.family_name 
		--and referenced_relation='identifications'
		
		and people_ref is null
		/*and record_id IN (SELECT c.id FROM identifications c 
			INNER join staging a ON 
			 c.referenced_relation='staging' AND c.record_id=a.id
			 WHERE import_ref=p_import_ref AND a.to_import='f' )
		*/	 
		
		;
	DROP TABLE  tmp_people_import_rmca;

END;
$$;


ALTER FUNCTION darwin2.rmca_create_missing_people_in_staging(p_import_ref integer) OWNER TO darwin2;

--
-- TOC entry 1749 (class 1255 OID 108373)
-- Name: rmca_delete_specimens_from_collection(integer); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION rmca_delete_specimens_from_collection(p_coll_ref integer) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN 


ALTER table properties disable trigger user; 
ALTER table comments disable trigger user; 
RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM properties);
DELETE FROM properties WHERE record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) AND referenced_relation= 'specimens';
RAISE NOTICE 'after delete properties (specimens) %', (SELECT count(*) FROM properties);


RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE FROM comments WHERE record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) AND referenced_relation='specimens';
RAISE NOTICE 'afet delete comments (specimens) %', (SELECT count(*) FROM comments);


RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM properties);
DELETE FROM properties WHERE record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) AND referenced_relation= 'staging';
RAISE NOTICE 'after delete properties (staging) %', (SELECT count(*) FROM properties);

RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE FROM comments WHERE record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) AND referenced_relation= 'staging';
RAISE NOTICE 'after delete comments (staging) %', (SELECT count(*) FROM comments);

RAISE NOTICE 'before delete properties %', (SELECT count(*) FROM comments);
DELETE
  FROM properties where referenced_relation ='staging_info' and record_id in (SELECT id FROM staging_info WHERE staging_ref IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) )  AND referenced_relation= 'staging_info';
RAISE NOTICE 'after delete properties (staging_info) %', (SELECT count(*) FROM properties);

RAISE NOTICE 'before delete comments %', (SELECT count(*) FROM comments);
DELETE
  FROM comments where referenced_relation ='staging_info' and record_id in (SELECT id FROM staging_info WHERE staging_ref IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) )  AND referenced_relation= 'staging_info';
RAISE NOTICE 'after  delete comments (staging_info) %', (SELECT count(*) FROM comments);

ALTER table properties enable trigger user; 
ALTER table comments enable trigger user; 

RAISE NOTICE 'before delete tags %', (SELECT count(*) FROM tags);
DELETE FROM tags WHERE gtu_ref IN (SELECT id FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) );
RAISE NOTICE 'after delete tags %', (SELECT count(*) FROM tags);
/*
RAISE NOTICE 'before delete tag_groups %', (SELECT count(*) FROM tag_groups);
DELETE FROM tag_groups WHERE gtu_ref IN (SELECT id FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) );
RAISE NOTICE 'after delete tag_groups %', (SELECT count(*) FROM tag_groups);
*/


RAISE NOTICE 'before delete staging_info %', (SELECT count(*) FROM staging_info);
DELETE FROM staging_info WHERE staging_ref IN (SELECT distinct gtu_ref FROM staging   WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'after delete staging_info %', (SELECT count(*) FROM staging_info);

ALTER TABLE identifications DISABLE TRIGGER user ;

RAISE NOTICE 'before delete identifications (specimens) %', (SELECT count(*) FROM identifications);
DELETE FROM identifications WHERE referenced_relation='specimens' AND record_id IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref);

RAISE NOTICE 'after delete identifications (specimens) %', (SELECT count(*) FROM identifications);

ALTER TABLE identifications ENABLE  TRIGGER user;

RAISE NOTICE 'before delete identifications (staging) %', (SELECT count(*) FROM identifications);
DELETE FROM identifications WHERE referenced_relation='staging' AND record_id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'atfer delete identifications (staging) %', (SELECT count(*) FROM identifications);


--DELETE FROM taxonomy WHERE id IN (SELECT taxon)

--DELETE FROM gtu WHERE id in (SELECT gtu_ref FROM specimens WHERE collection_ref=p_coll_ref) ;

--DELETE FROM igs WHERE id in (SELECT if_ref FROM specimens WHERE collection_ref=p_coll_ref) ;

ALTER TABLE specimens DISABLE TRIGGER trg_chk_specimencollectionallowed;

RAISE NOTICE 'update specimens nullify FKs';
UPDATE specimens SET gtu_ref=NULL, taxon_ref=NULL, ig_ref=NULL WHERE collection_ref=p_coll_ref;

ALTER TABLE specimens ENABLE TRIGGER trg_chk_specimencollectionallowed;

RAISE NOTICE 'update staging nullify FKs';
UPDATE staging SET gtu_ref=NULL, taxon_ref=NULL, ig_ref=NULL WHERE id IN (SELECT id FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref)) ;

/*
ALTER TABLE taxonomy DISABLE TRIGGER user;
RAISE NOTICE 'before delete taxonomy %', (SELECT count(*) FROM taxonomy);
DELETE FROM taxonomy WHERE coalesce(id,-1) NOT in (SELECT coalesce(taxon_ref,-2) FROM specimens) AND coalesce(id,-1) not in (SELECT coalesce(parent_ref, -2) FROM taxonomy) and coalesce(id,-1) NOT in (SELECT coalesce(record_id,-2) FROM classification_synonymies);--AND id NOT in (SELECT taxon_ref FROM staging);
RAISE NOTICE 'after delete taxonomy %', (SELECT count(*) FROM taxonomy);
ALTER TABLE taxonomy ENABLE TRIGGER user;
*/

RAISE NOTICE 'before delete gtu %', (SELECT count(*) FROM gtu);
--DELETE FROM gtu WHERE id NOT in (SELECT gtu_ref FROM specimens) AND id NOT in (SELECT gtu_ref FROM staging);
RAISE NOTICE 'ater delete gtu %', (SELECT count(*) FROM gtu);


RAISE NOTICE 'before delete igs %', (SELECT count(*) FROM igs);
DELETE FROM igs WHERE id NOT in (SELECT ig_ref FROM specimens) AND id NOT in (SELECT ig_ref FROM staging);

RAISE NOTICE 'after delete igs %', (SELECT count(*) FROM igs);

RAISE NOTICE 'before delete specimens_relationshipes %', (SELECT count(*) FROM specimens_relationships);
DELETE FROM specimens_relationships WHERE unit_type='specimens' AND (specimen_ref IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref) OR specimen_related_ref IN (SELECT id FROM specimens WHERE collection_ref=p_coll_ref));
RAISE NOTICE 'after delete specimens_relationshipes %', (SELECT count(*) FROM specimens_relationships);

RAISE NOTICE 'before delete staging %', (SELECT count(*) FROM staging);
DELETE FROM staging WHERE import_ref IN (SELECT id FROM imports WHERE collection_ref=p_coll_ref);
RAISE NOTICE 'after  delete staging %', (SELECT count(*) FROM staging);


ALTER TABLE storage_parts DISABLE TRIGGER user;
RAISE NOTICE 'before delete storage_parts %', (SELECT count(*) FROM storage_parts);
DELETE FROM storage_parts where specimen_ref in (select id from specimens WHERE collection_ref=p_coll_ref);
RAISE NOTICE 'after delete storage_parts %', (SELECT count(*) FROM storage_parts);
--DELETE FROM collections WHERE id=p_coll_ref;
ALTER TABLE storage_parts ENABLE TRIGGER user;

ALTER TABLE specimens DISABLE TRIGGER user;
RAISE NOTICE 'before delete specimens %', (SELECT count(*) FROM specimens);
DELETE FROM specimens WHERE collection_ref=p_coll_ref;
RAISE NOTICE 'after delete specimens %', (SELECT count(*) FROM specimens);
--DELETE FROM collections WHERE id=p_coll_ref;
ALTER TABLE specimens ENABLE TRIGGER user;

END;
$$;


ALTER FUNCTION darwin2.rmca_delete_specimens_from_collection(p_coll_ref integer) OWNER TO darwin2;

--
-- TOC entry 526 (class 1255 OID 18021)
-- Name: sha1(bytea); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION sha1(bytea) RETURNS character varying
    LANGUAGE plpgsql
    AS $_$
BEGIN
        RETURN ENCODE(DIGEST($1, 'sha1'), 'hex');
END;
$_$;


ALTER FUNCTION darwin2.sha1(bytea) OWNER TO darwin2;

--
-- TOC entry 494 (class 1255 OID 17990)
-- Name: touniquestr(character varying); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION touniquestr(to_indexed character varying) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
    temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := to_indexed;
    temp_string := TRANSLATE(temp_string, E'  ¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_-','');
     --Remove ALL none alphanumerical char like # or '
    temp_string := lower(temp_string);
    return substring(temp_string from 0 for 40);
END;
$_$;


ALTER FUNCTION darwin2.touniquestr(to_indexed character varying) OWNER TO darwin2;

--
-- TOC entry 616 (class 1255 OID 108323)
-- Name: touniquestr(character varying, boolean); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION touniquestr(to_indexed character varying, keep_space boolean) RETURNS character varying
    LANGUAGE plpgsql IMMUTABLE STRICT
    AS $_$
DECLARE
    temp_string varchar;
BEGIN
    -- Investigate https://launchpad.net/postgresql-unaccent
    temp_string := to_indexed;
    if keep_space =false then
    temp_string := TRANSLATE(temp_string, E'  ¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_-','');
        --ftheeten 2016 02 29
     temp_string :=regexp_replace(temp_string,'(\s{2,})',' ', 'g');
    else
    --temp_string := TRANSLATE(temp_string, E' ¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_-','');
   --ftheeten 2016 02 29 
        temp_string :=regexp_replace(temp_string,'-',' ', 'g');
        temp_string :=regexp_replace(temp_string,'''',' ', 'g');
temp_string := TRANSLATE(temp_string, E'¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿×&|@"\'#(§^!{})°$*][£µ`%+=~/.,?;:\\<>ł€¶ŧ←↓→«»¢“”_','');
    end if;
     --Remove ALL none alphanumerical char like # or '
    temp_string := lower(temp_string);
    --ftheeten 2016 02 29
     temp_string :=regexp_replace(temp_string,'(\s{2,})',' ', 'g');
    return substring(temp_string from 0 for 40);
END;

$_$;


ALTER FUNCTION darwin2.touniquestr(to_indexed character varying, keep_space boolean) OWNER TO darwin2;

--
-- TOC entry 1724 (class 1255 OID 18055)
-- Name: trg_del_dict(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION trg_del_dict() RETURNS trigger
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
	--ftheeten 2016 08 24
      --PERFORM fct_del_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      --PERFORM fct_del_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      ---PERFORM fct_del_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      --PERFORM fct_del_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      --PERFORM fct_del_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      --PERFORM fct_del_in_dict('specimens','col', oldfield.col, newfield.col);
      --PERFORM fct_del_in_dict('specimens','row', oldfield.row, newfield.row);
      --PERFORM fct_del_in_dict('specimens','room', oldfield.room, newfield.room);
      --PERFORM fct_del_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      --PERFORM fct_del_in_dict('specimens','building', oldfield.building, newfield.building);

      --PERFORM fct_del_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
      --  oldfield.container_type, newfield.container_type, 'container_type' );
      --PERFORM fct_del_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
      --  oldfield.sub_container_type, newfield.sub_container_type, 'sub_container_type' );
     --ftheeten 2016 09 28
     ELSIF TG_TABLE_NAME = 'storage_parts' THEN

      PERFORM fct_del_in_dict('storage_parts','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_del_in_dict('storage_parts','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_del_in_dict('storage_parts','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_del_in_dict('storage_parts','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_del_in_dict('storage_parts','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_del_in_dict('storage_parts','col', oldfield.col, newfield.col);
      PERFORM fct_del_in_dict('storage_parts','row', oldfield.row, newfield.row);
      PERFORM fct_del_in_dict('storage_parts','room', oldfield.room, newfield.room);
      PERFORM fct_del_in_dict('storage_parts','floor', oldfield.floor, newfield.floor);
      PERFORM fct_del_in_dict('storage_parts','building', oldfield.building, newfield.building);

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


ALTER FUNCTION darwin2.trg_del_dict() OWNER TO darwin2;

--
-- TOC entry 1744 (class 1255 OID 18056)
-- Name: trg_ins_update_dict(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION trg_ins_update_dict() RETURNS trigger
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

      --PERFORM fct_add_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      --PERFORM fct_add_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      --PERFORM fct_add_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      --PERFORM fct_add_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      --PERFORM fct_add_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      --PERFORM fct_add_in_dict('specimens','col', oldfield.col, newfield.col);
      --PERFORM fct_add_in_dict('specimens','row', oldfield.row, newfield.row);
      --PERFORM fct_add_in_dict('specimens','room', oldfield.room, newfield.room);
      --PERFORM fct_add_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      --PERFORM fct_add_in_dict('specimens','building', oldfield.building, newfield.building);

      --PERFORM fct_add_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
     --   oldfield.container_type, newfield.container_type);
     -- PERFORM fct_add_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
     --   oldfield.sub_container_type, newfield.sub_container_type);

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

--rmca 201608 24

     ELSIF TG_TABLE_NAME = 'storage_parts' THEN
        PERFORM fct_add_in_dict('storage_parts','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict('storage_parts','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_add_in_dict('storage_parts','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_add_in_dict('storage_parts','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_add_in_dict('storage_parts','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_add_in_dict('storage_parts','col', oldfield.col, newfield.col);
      PERFORM fct_add_in_dict('storage_parts','row', oldfield.row, newfield.row);
      PERFORM fct_add_in_dict('storage_parts','room', oldfield.room, newfield.room);
      PERFORM fct_add_in_dict('storage_parts','floor', oldfield.floor, newfield.floor);
      PERFORM fct_add_in_dict('storage_parts','building', oldfield.building, newfield.building);

      PERFORM fct_add_in_dict_dept('storage_parts','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict_dept('storage_parts','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type);


    END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.trg_ins_update_dict() OWNER TO darwin2;

--
-- TOC entry 1739 (class 1255 OID 250668)
-- Name: trg_rmca_delete_specimen_storage(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION trg_rmca_delete_specimen_storage() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  oldfield RECORD;

BEGIN

    IF TG_OP = 'DELETE' AND TG_TABLE_NAME = 'specimens' THEN
    
        DELETE FROM storage_parts WHERE specimen_ref=OLD.id;
    END IF;
	RETURN old;
END;
$$;


ALTER FUNCTION darwin2.trg_rmca_delete_specimen_storage() OWNER TO darwin2;

--
-- TOC entry 1722 (class 1255 OID 250575)
-- Name: trg_rmca_issue_storage_insert(); Type: FUNCTION; Schema: darwin2; Owner: darwin2
--

CREATE FUNCTION trg_rmca_issue_storage_insert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'INSERT' OR  TG_OP = 'UPDATE' THEN
	 --oldfield = OLD;
         newfield = NEW;
	 IF TG_TABLE_NAME = 'storage_parts' THEN
		IF newfield.institution_ref is NULL THEN
			--RETURN NULL;
		END IF;
	 END IF;

    END IF;
     IF  TG_OP = 'UPDATE' THEN
	oldfield = OLD;
         newfield = NEW;
	 IF TG_TABLE_NAME = 'storage_parts' THEN
		IF newfield.institution_ref is NULL THEN
			--RETURN OLD;
		END IF;
	 END IF;

    END IF;

  RETURN NEW;
END;
$$;


ALTER FUNCTION darwin2.trg_rmca_issue_storage_insert() OWNER TO darwin2;

SET search_path = public, pg_catalog;

--
-- TOC entry 1719 (class 1255 OID 250439)
-- Name: trg_del_dict(); Type: FUNCTION; Schema: public; Owner: darwin2
--

CREATE FUNCTION trg_del_dict() RETURNS trigger
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
-- TOC entry 1720 (class 1255 OID 250440)
-- Name: trg_ins_update_dict(); Type: FUNCTION; Schema: public; Owner: darwin2
--

CREATE FUNCTION trg_ins_update_dict() RETURNS trigger
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

SET search_path = darwin2, pg_catalog;

--
-- TOC entry 3391 (class 2617 OID 18090)
-- Name: =; Type: OPERATOR; Schema: darwin2; Owner: darwin2
--

CREATE OPERATOR = (
    PROCEDURE = point_equal,
    LEFTARG = point,
    RIGHTARG = point
);


ALTER OPERATOR darwin2.= (point, point) OWNER TO darwin2;

--
-- TOC entry 305 (class 1259 OID 17954)
-- Name: bibliography; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE bibliography (
    id integer NOT NULL,
    title character varying NOT NULL,
    title_indexed character varying NOT NULL,
    type character varying NOT NULL,
    abstract character varying DEFAULT ''::character varying NOT NULL,
    year integer
);


ALTER TABLE darwin2.bibliography OWNER TO darwin2;

--
-- TOC entry 5214 (class 0 OID 0)
-- Dependencies: 305
-- Name: TABLE bibliography; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE bibliography IS 'List of expeditions made to collect specimens';


--
-- TOC entry 5215 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.id IS 'Unique identifier';


--
-- TOC entry 5216 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.title; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.title IS 'bibliography title';


--
-- TOC entry 5217 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.title_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.title_indexed IS 'Indexed form of title';


--
-- TOC entry 5218 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.type IS 'bibliography type : article, book, booklet';


--
-- TOC entry 5219 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.abstract; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.abstract IS 'optional abstract of the bibliography';


--
-- TOC entry 5220 (class 0 OID 0)
-- Dependencies: 305
-- Name: COLUMN bibliography.year; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN bibliography.year IS 'The year of publication (or, if unpublished, the year of creation)';


--
-- TOC entry 304 (class 1259 OID 17952)
-- Name: bibliography_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE bibliography_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.bibliography_id_seq OWNER TO darwin2;

--
-- TOC entry 5222 (class 0 OID 0)
-- Dependencies: 304
-- Name: bibliography_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE bibliography_id_seq OWNED BY bibliography.id;


--
-- TOC entry 183 (class 1259 OID 16638)
-- Name: template_table_record_ref; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE template_table_record_ref (
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL
);


ALTER TABLE darwin2.template_table_record_ref OWNER TO darwin2;

--
-- TOC entry 5223 (class 0 OID 0)
-- Dependencies: 183
-- Name: TABLE template_table_record_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE template_table_record_ref IS 'Template called to add referenced_relation and record_id fields';


--
-- TOC entry 5224 (class 0 OID 0)
-- Dependencies: 183
-- Name: COLUMN template_table_record_ref.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_table_record_ref.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5225 (class 0 OID 0)
-- Dependencies: 183
-- Name: COLUMN template_table_record_ref.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_table_record_ref.record_id IS 'Id of record concerned';


--
-- TOC entry 307 (class 1259 OID 17968)
-- Name: catalogue_bibliography; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE catalogue_bibliography (
    id integer NOT NULL,
    bibliography_ref integer NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.catalogue_bibliography OWNER TO darwin2;

--
-- TOC entry 5227 (class 0 OID 0)
-- Dependencies: 307
-- Name: TABLE catalogue_bibliography; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE catalogue_bibliography IS 'List of people of catalogues units - Taxonomy, Chronostratigraphy,...';


--
-- TOC entry 5228 (class 0 OID 0)
-- Dependencies: 307
-- Name: COLUMN catalogue_bibliography.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_bibliography.referenced_relation IS 'Identifier-Name of table the units come from';


--
-- TOC entry 5229 (class 0 OID 0)
-- Dependencies: 307
-- Name: COLUMN catalogue_bibliography.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_bibliography.record_id IS 'Identifier of record concerned in table concerned';


--
-- TOC entry 5230 (class 0 OID 0)
-- Dependencies: 307
-- Name: COLUMN catalogue_bibliography.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_bibliography.id IS 'Unique identifier of record';


--
-- TOC entry 5231 (class 0 OID 0)
-- Dependencies: 307
-- Name: COLUMN catalogue_bibliography.bibliography_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_bibliography.bibliography_ref IS 'Reference of the biblio concerned - id field of people table';


--
-- TOC entry 306 (class 1259 OID 17966)
-- Name: catalogue_bibliography_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE catalogue_bibliography_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.catalogue_bibliography_id_seq OWNER TO darwin2;

--
-- TOC entry 5233 (class 0 OID 0)
-- Dependencies: 306
-- Name: catalogue_bibliography_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE catalogue_bibliography_id_seq OWNED BY catalogue_bibliography.id;


--
-- TOC entry 186 (class 1259 OID 16665)
-- Name: catalogue_levels_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE catalogue_levels_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.catalogue_levels_id_seq OWNER TO darwin2;

--
-- TOC entry 5234 (class 0 OID 0)
-- Dependencies: 186
-- Name: catalogue_levels_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE catalogue_levels_id_seq OWNED BY catalogue_levels.id;


--
-- TOC entry 185 (class 1259 OID 16646)
-- Name: catalogue_people; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE catalogue_people (
    id integer NOT NULL,
    people_type character varying DEFAULT 'author'::character varying NOT NULL,
    people_sub_type character varying DEFAULT ''::character varying NOT NULL,
    order_by integer DEFAULT 1 NOT NULL,
    people_ref integer NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.catalogue_people OWNER TO darwin2;

--
-- TOC entry 5235 (class 0 OID 0)
-- Dependencies: 185
-- Name: TABLE catalogue_people; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE catalogue_people IS 'List of people of catalogues units - Taxonomy, Chronostratigraphy,...';


--
-- TOC entry 5236 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.referenced_relation IS 'Identifier-Name of table the units come from';


--
-- TOC entry 5237 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.record_id IS 'Identifier of record concerned in table concerned';


--
-- TOC entry 5238 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.id IS 'Unique identifier of record';


--
-- TOC entry 5239 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.people_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.people_type IS 'Type of "people" associated to the catalogue unit: authors, collectors, defined,  ...';


--
-- TOC entry 5240 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.people_sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.people_sub_type IS 'Type of "people" associated to the catalogue unit: Main author, corrector, taking the sense from,...';


--
-- TOC entry 5241 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.order_by IS 'Integer used to order the persons in a list';


--
-- TOC entry 5242 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN catalogue_people.people_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_people.people_ref IS 'Reference of person concerned - id field of people table';


--
-- TOC entry 184 (class 1259 OID 16644)
-- Name: catalogue_people_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE catalogue_people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.catalogue_people_id_seq OWNER TO darwin2;

--
-- TOC entry 5244 (class 0 OID 0)
-- Dependencies: 184
-- Name: catalogue_people_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE catalogue_people_id_seq OWNED BY catalogue_people.id;


--
-- TOC entry 182 (class 1259 OID 16626)
-- Name: catalogue_relationships; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE catalogue_relationships (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    record_id_1 integer NOT NULL,
    record_id_2 integer NOT NULL,
    relationship_type character varying DEFAULT 'recombined from'::character varying NOT NULL,
    CONSTRAINT chk_not_related_to_self CHECK ((record_id_1 <> record_id_2))
);


ALTER TABLE darwin2.catalogue_relationships OWNER TO darwin2;

--
-- TOC entry 5245 (class 0 OID 0)
-- Dependencies: 182
-- Name: TABLE catalogue_relationships; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE catalogue_relationships IS 'Stores the relationships between records of a table - current name, original combination, ...';


--
-- TOC entry 5246 (class 0 OID 0)
-- Dependencies: 182
-- Name: COLUMN catalogue_relationships.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_relationships.referenced_relation IS 'Reference of the table a relationship is defined for';


--
-- TOC entry 5247 (class 0 OID 0)
-- Dependencies: 182
-- Name: COLUMN catalogue_relationships.record_id_1; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_relationships.record_id_1 IS 'Identifier of record in relation with an other one (record_id_2)';


--
-- TOC entry 5248 (class 0 OID 0)
-- Dependencies: 182
-- Name: COLUMN catalogue_relationships.record_id_2; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_relationships.record_id_2 IS 'Identifier of record in relation with an other one (record_id_1)';


--
-- TOC entry 5249 (class 0 OID 0)
-- Dependencies: 182
-- Name: COLUMN catalogue_relationships.relationship_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN catalogue_relationships.relationship_type IS 'Type of relation between record 1 and record 2 - current name, original combination, ...';


--
-- TOC entry 181 (class 1259 OID 16624)
-- Name: catalogue_relationships_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE catalogue_relationships_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.catalogue_relationships_id_seq OWNER TO darwin2;

--
-- TOC entry 5251 (class 0 OID 0)
-- Dependencies: 181
-- Name: catalogue_relationships_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE catalogue_relationships_id_seq OWNED BY catalogue_relationships.id;


--
-- TOC entry 249 (class 1259 OID 17272)
-- Name: chronostratigraphy; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE chronostratigraphy (
    id integer NOT NULL,
    lower_bound numeric(10,3),
    upper_bound numeric(10,3),
    CONSTRAINT fct_chk_onceinpath_chronostratigraphy CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
)
INHERITS (template_classifications);


ALTER TABLE darwin2.chronostratigraphy OWNER TO darwin2;

--
-- TOC entry 5252 (class 0 OID 0)
-- Dependencies: 249
-- Name: TABLE chronostratigraphy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE chronostratigraphy IS 'List of chronostratigraphic units';


--
-- TOC entry 5253 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.name IS 'Classification unit name';


--
-- TOC entry 5254 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.name_indexed IS 'Indexed form of name field';


--
-- TOC entry 5255 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5256 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5257 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5258 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 5259 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.id IS 'Unique identifier of a classification unit';


--
-- TOC entry 5260 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.lower_bound; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.lower_bound IS 'Lower age boundary in years';


--
-- TOC entry 5261 (class 0 OID 0)
-- Dependencies: 249
-- Name: COLUMN chronostratigraphy.upper_bound; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN chronostratigraphy.upper_bound IS 'Upper age boundary in years';


--
-- TOC entry 248 (class 1259 OID 17270)
-- Name: chronostratigraphy_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE chronostratigraphy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.chronostratigraphy_id_seq OWNER TO darwin2;

--
-- TOC entry 5263 (class 0 OID 0)
-- Dependencies: 248
-- Name: chronostratigraphy_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE chronostratigraphy_id_seq OWNED BY chronostratigraphy.id;


--
-- TOC entry 242 (class 1259 OID 17216)
-- Name: classification_keywords; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE classification_keywords (
    id integer NOT NULL,
    keyword_type character varying DEFAULT 'name'::character varying NOT NULL,
    keyword character varying NOT NULL,
    keyword_indexed character varying NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.classification_keywords OWNER TO darwin2;

--
-- TOC entry 5264 (class 0 OID 0)
-- Dependencies: 242
-- Name: TABLE classification_keywords; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE classification_keywords IS 'Help user to tag-label each part of full name in classifications';


--
-- TOC entry 5265 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN classification_keywords.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_keywords.referenced_relation IS 'Name of classifification table: taxonomy, lithology,...';


--
-- TOC entry 5266 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN classification_keywords.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_keywords.record_id IS 'Id of record concerned';


--
-- TOC entry 5267 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN classification_keywords.keyword_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_keywords.keyword_type IS 'Keyword type: name, year, authoritative keyword,...';


--
-- TOC entry 5268 (class 0 OID 0)
-- Dependencies: 242
-- Name: COLUMN classification_keywords.keyword; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_keywords.keyword IS 'Keyword';


--
-- TOC entry 241 (class 1259 OID 17214)
-- Name: classification_keywords_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE classification_keywords_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.classification_keywords_id_seq OWNER TO darwin2;

--
-- TOC entry 5270 (class 0 OID 0)
-- Dependencies: 241
-- Name: classification_keywords_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE classification_keywords_id_seq OWNED BY classification_keywords.id;


--
-- TOC entry 245 (class 1259 OID 17230)
-- Name: classification_synonymies; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE classification_synonymies (
    id integer NOT NULL,
    group_id integer NOT NULL,
    group_name character varying NOT NULL,
    is_basionym boolean DEFAULT false,
    order_by integer DEFAULT 0 NOT NULL,
    synonym_record_id integer
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.classification_synonymies OWNER TO darwin2;

--
-- TOC entry 5271 (class 0 OID 0)
-- Dependencies: 245
-- Name: TABLE classification_synonymies; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE classification_synonymies IS 'Table containing classification synonymies';


--
-- TOC entry 5272 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.referenced_relation IS 'Classification table concerned';


--
-- TOC entry 5273 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.record_id IS 'Id of record placed in group as a synonym';


--
-- TOC entry 5274 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.group_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.group_id IS 'Id given to group';


--
-- TOC entry 5275 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.group_name IS 'Name of group under which synonyms are placed';


--
-- TOC entry 5276 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.is_basionym; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.is_basionym IS 'If record is a basionym';


--
-- TOC entry 5277 (class 0 OID 0)
-- Dependencies: 245
-- Name: COLUMN classification_synonymies.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN classification_synonymies.order_by IS 'Order by used to qualify order amongst synonyms - used mainly for senio and junior synonyms';


--
-- TOC entry 243 (class 1259 OID 17226)
-- Name: classification_synonymies_group_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE classification_synonymies_group_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.classification_synonymies_group_id_seq OWNER TO darwin2;

--
-- TOC entry 244 (class 1259 OID 17228)
-- Name: classification_synonymies_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE classification_synonymies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.classification_synonymies_id_seq OWNER TO darwin2;

--
-- TOC entry 5279 (class 0 OID 0)
-- Dependencies: 244
-- Name: classification_synonymies_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE classification_synonymies_id_seq OWNED BY classification_synonymies.id;


--
-- TOC entry 261 (class 1259 OID 17482)
-- Name: codes; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE codes (
    id integer NOT NULL,
    code_category character varying DEFAULT 'main'::character varying NOT NULL,
    code_prefix character varying,
    code_prefix_separator character varying,
    code character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    full_code_indexed character varying NOT NULL,
    code_date timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone NOT NULL,
    code_date_mask integer DEFAULT 0 NOT NULL,
    code_num integer DEFAULT 0
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.codes OWNER TO darwin2;

--
-- TOC entry 5280 (class 0 OID 0)
-- Dependencies: 261
-- Name: TABLE codes; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE codes IS 'Template used to construct the specimen codes tables';


--
-- TOC entry 5281 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.referenced_relation IS 'Reference name of table concerned';


--
-- TOC entry 5282 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.record_id IS 'Identifier of record concerned';


--
-- TOC entry 5283 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.id IS 'Unique identifier of a code';


--
-- TOC entry 5284 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_category; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_category IS 'Category of code: main, secondary, temporary,...';


--
-- TOC entry 5285 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_prefix; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_prefix IS 'Code prefix - entire code if all alpha, begining character part if code is made of characters and numeric parts';


--
-- TOC entry 5286 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_prefix_separator; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_prefix_separator IS 'Separtor used between code core and code prefix';


--
-- TOC entry 5287 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code IS 'Numerical part of code - but not forced: if users want to use it as alphanumerical code - possible too';


--
-- TOC entry 5288 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_suffix; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_suffix IS 'For codes made of characters and numerical parts, this field stores the last alpha part of code';


--
-- TOC entry 5289 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_suffix_separator; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_suffix_separator IS 'Separtor used between code core and code suffix';


--
-- TOC entry 5290 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.full_code_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.full_code_indexed IS 'Full code composition by code_prefix, code and code suffix concatenation and indexed for unique check purpose';


--
-- TOC entry 5291 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_date IS 'Date of code creation (fuzzy date)';


--
-- TOC entry 5292 (class 0 OID 0)
-- Dependencies: 261
-- Name: COLUMN codes.code_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN codes.code_date_mask IS 'Mask used for code date';


--
-- TOC entry 260 (class 1259 OID 17480)
-- Name: codes_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE codes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.codes_id_seq OWNER TO darwin2;

--
-- TOC entry 5294 (class 0 OID 0)
-- Dependencies: 260
-- Name: codes_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE codes_id_seq OWNED BY codes.id;


--
-- TOC entry 271 (class 1259 OID 17601)
-- Name: collecting_methods; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE collecting_methods (
    id integer NOT NULL,
    method character varying NOT NULL,
    method_indexed character varying NOT NULL,
    CONSTRAINT chk_collecting_methods_method CHECK (((method)::text <> ''::text))
);


ALTER TABLE darwin2.collecting_methods OWNER TO darwin2;

--
-- TOC entry 5295 (class 0 OID 0)
-- Dependencies: 271
-- Name: TABLE collecting_methods; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE collecting_methods IS 'List of all available collecting methods';


--
-- TOC entry 5296 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN collecting_methods.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_methods.id IS 'Unique identifier of a collecting method';


--
-- TOC entry 5297 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN collecting_methods.method; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_methods.method IS 'Method used';


--
-- TOC entry 5298 (class 0 OID 0)
-- Dependencies: 271
-- Name: COLUMN collecting_methods.method_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_methods.method_indexed IS 'Indexed form of method used - for ordering and filtering purposes';


--
-- TOC entry 270 (class 1259 OID 17599)
-- Name: collecting_methods_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE collecting_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.collecting_methods_id_seq OWNER TO darwin2;

--
-- TOC entry 5300 (class 0 OID 0)
-- Dependencies: 270
-- Name: collecting_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE collecting_methods_id_seq OWNED BY collecting_methods.id;


--
-- TOC entry 267 (class 1259 OID 17567)
-- Name: collecting_tools; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE collecting_tools (
    id integer NOT NULL,
    tool character varying NOT NULL,
    tool_indexed character varying NOT NULL,
    CONSTRAINT chk_collecting_tools_tool CHECK (((tool)::text <> ''::text))
);


ALTER TABLE darwin2.collecting_tools OWNER TO darwin2;

--
-- TOC entry 5301 (class 0 OID 0)
-- Dependencies: 267
-- Name: TABLE collecting_tools; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE collecting_tools IS 'List of all available collecting tools';


--
-- TOC entry 5302 (class 0 OID 0)
-- Dependencies: 267
-- Name: COLUMN collecting_tools.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_tools.id IS 'Unique identifier of a collecting tool';


--
-- TOC entry 5303 (class 0 OID 0)
-- Dependencies: 267
-- Name: COLUMN collecting_tools.tool; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_tools.tool IS 'Tool used';


--
-- TOC entry 5304 (class 0 OID 0)
-- Dependencies: 267
-- Name: COLUMN collecting_tools.tool_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collecting_tools.tool_indexed IS 'Indexed form of tool used - for ordering and filtering purposes';


--
-- TOC entry 266 (class 1259 OID 17565)
-- Name: collecting_tools_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE collecting_tools_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.collecting_tools_id_seq OWNER TO darwin2;

--
-- TOC entry 5306 (class 0 OID 0)
-- Dependencies: 266
-- Name: collecting_tools_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE collecting_tools_id_seq OWNED BY collecting_tools.id;


--
-- TOC entry 235 (class 1259 OID 17132)
-- Name: collection_maintenance; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE collection_maintenance (
    id integer NOT NULL,
    people_ref integer,
    category character varying DEFAULT 'action'::character varying NOT NULL,
    action_observation character varying NOT NULL,
    description character varying,
    description_indexed text,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL,
    modification_date_mask integer DEFAULT 0 NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.collection_maintenance OWNER TO darwin2;

--
-- TOC entry 5307 (class 0 OID 0)
-- Dependencies: 235
-- Name: TABLE collection_maintenance; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE collection_maintenance IS 'History of specimen maintenance';


--
-- TOC entry 5308 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.referenced_relation IS 'Reference of table a maintenance entry has been created for';


--
-- TOC entry 5309 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.record_id IS 'ID of record a maintenance entry has been created for';


--
-- TOC entry 5310 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.id IS 'Unique identifier of a specimen maintenance';


--
-- TOC entry 5311 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.people_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.people_ref IS 'Reference of person having done an action or an observation';


--
-- TOC entry 5312 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.category; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.category IS 'Action or Observation';


--
-- TOC entry 5313 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.action_observation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.action_observation IS 'Action or observation done';


--
-- TOC entry 5314 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.description; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.description IS 'Complementary description';


--
-- TOC entry 5315 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.description_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.description_indexed IS 'indexed form of description field';


--
-- TOC entry 5316 (class 0 OID 0)
-- Dependencies: 235
-- Name: COLUMN collection_maintenance.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collection_maintenance.modification_date_time IS 'Last update date/time';


--
-- TOC entry 234 (class 1259 OID 17130)
-- Name: collection_maintenance_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE collection_maintenance_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.collection_maintenance_id_seq OWNER TO darwin2;

--
-- TOC entry 5318 (class 0 OID 0)
-- Dependencies: 234
-- Name: collection_maintenance_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE collection_maintenance_id_seq OWNED BY collection_maintenance.id;


--
-- TOC entry 227 (class 1259 OID 17032)
-- Name: collections; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE collections (
    id integer NOT NULL,
    collection_type character varying DEFAULT 'mix'::character varying NOT NULL,
    code character varying NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL,
    institution_ref integer NOT NULL,
    main_manager_ref integer NOT NULL,
    staff_ref integer,
    parent_ref integer,
    path character varying NOT NULL,
    code_auto_increment boolean DEFAULT false NOT NULL,
    code_last_value integer DEFAULT 0 NOT NULL,
    code_prefix character varying,
    code_prefix_separator character varying,
    code_suffix character varying,
    code_suffix_separator character varying,
    code_specimen_duplicate boolean DEFAULT false NOT NULL,
    is_public boolean DEFAULT true NOT NULL,
    code_mask character varying,
    loan_auto_increment boolean DEFAULT true NOT NULL,
    loan_last_value integer DEFAULT 0 NOT NULL,
    CONSTRAINT chk_main_manager_ref CHECK ((main_manager_ref > 0)),
    CONSTRAINT fct_chk_onceinpath_collections CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
);


ALTER TABLE darwin2.collections OWNER TO darwin2;

--
-- TOC entry 5319 (class 0 OID 0)
-- Dependencies: 227
-- Name: TABLE collections; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE collections IS 'List of all collections encoded in DaRWIN 2';


--
-- TOC entry 5320 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.id IS 'Unique identifier of a collection';


--
-- TOC entry 5321 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.collection_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.collection_type IS 'Type of collection: physical for a collection of only physical objects, observations for a collection of only observations, mix for any kind of entry catalogued in collection';


--
-- TOC entry 5322 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code IS 'Code given to collection';


--
-- TOC entry 5323 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.name IS 'Collection name';


--
-- TOC entry 5324 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.name_indexed IS 'Collection name indexed';


--
-- TOC entry 5325 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.institution_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.institution_ref IS 'Reference of institution current collection belongs to - id field of people table';


--
-- TOC entry 5326 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.main_manager_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.main_manager_ref IS 'Reference of collection main manager - id field of users table';


--
-- TOC entry 5327 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.staff_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.staff_ref IS 'Reference of staff member, scientist responsible - id field of users table';


--
-- TOC entry 5328 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.parent_ref IS 'Recursive reference to collection table itself to represent collection parenty/hierarchy';


--
-- TOC entry 5329 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.path IS 'Descriptive path for collection hierarchy, each level separated by a /';


--
-- TOC entry 5330 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_auto_increment; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_auto_increment IS 'Flag telling if the numerical part of a code has to be incremented or not';


--
-- TOC entry 5331 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_last_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_last_value IS 'Value of the last numeric code given in this collection when auto increment is/was activated';


--
-- TOC entry 5332 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_prefix; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_prefix IS 'Default code prefix to be used for specimens encoded in this collection';


--
-- TOC entry 5333 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_prefix_separator; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_prefix_separator IS 'Character chain used to separate code prefix from code core';


--
-- TOC entry 5334 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_suffix; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_suffix IS 'Default code suffix to be used for specimens encoded in this collection';


--
-- TOC entry 5335 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_suffix_separator; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_suffix_separator IS 'Character chain used to separate code suffix from code core';


--
-- TOC entry 5336 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.code_specimen_duplicate; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.code_specimen_duplicate IS 'Flag telling if the whole specimen code has to be copied when you do a duplicate';


--
-- TOC entry 5337 (class 0 OID 0)
-- Dependencies: 227
-- Name: COLUMN collections.is_public; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections.is_public IS 'Flag telling if the collection can be found in the public search';


--
-- TOC entry 226 (class 1259 OID 17030)
-- Name: collections_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE collections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.collections_id_seq OWNER TO darwin2;

--
-- TOC entry 5339 (class 0 OID 0)
-- Dependencies: 226
-- Name: collections_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE collections_id_seq OWNED BY collections.id;


--
-- TOC entry 229 (class 1259 OID 17071)
-- Name: collections_rights; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE collections_rights (
    id integer NOT NULL,
    db_user_type smallint DEFAULT 1 NOT NULL,
    collection_ref integer DEFAULT 0 NOT NULL,
    user_ref integer DEFAULT 0 NOT NULL
);


ALTER TABLE darwin2.collections_rights OWNER TO darwin2;

--
-- TOC entry 5340 (class 0 OID 0)
-- Dependencies: 229
-- Name: TABLE collections_rights; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE collections_rights IS 'List of rights of given users on given collections';


--
-- TOC entry 5341 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN collections_rights.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections_rights.id IS 'Unique identifier for collection rights';


--
-- TOC entry 5342 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN collections_rights.db_user_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections_rights.db_user_type IS 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';


--
-- TOC entry 5343 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN collections_rights.collection_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections_rights.collection_ref IS 'Reference of collection concerned - id field of collections table';


--
-- TOC entry 5344 (class 0 OID 0)
-- Dependencies: 229
-- Name: COLUMN collections_rights.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN collections_rights.user_ref IS 'Reference of user - id field of users table';


--
-- TOC entry 228 (class 1259 OID 17069)
-- Name: collections_rights_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE collections_rights_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.collections_rights_id_seq OWNER TO darwin2;

--
-- TOC entry 5346 (class 0 OID 0)
-- Dependencies: 228
-- Name: collections_rights_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE collections_rights_id_seq OWNED BY collections_rights.id;


--
-- TOC entry 190 (class 1259 OID 16697)
-- Name: comments; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE comments (
    id integer NOT NULL,
    notion_concerned character varying NOT NULL,
    comment text NOT NULL,
    comment_indexed text NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.comments OWNER TO darwin2;

--
-- TOC entry 5347 (class 0 OID 0)
-- Dependencies: 190
-- Name: TABLE comments; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE comments IS 'Comments associated to a record of a given table (and maybe a given field) on a given subject';


--
-- TOC entry 5348 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.referenced_relation IS 'Reference-Name of table a comment is posted for';


--
-- TOC entry 5349 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.record_id IS 'Identifier of the record concerned';


--
-- TOC entry 5350 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.id IS 'Unique identifier of a comment';


--
-- TOC entry 5351 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.notion_concerned; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.notion_concerned IS 'Notion concerned by comment';


--
-- TOC entry 5352 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.comment; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.comment IS 'Comment';


--
-- TOC entry 5353 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN comments.comment_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN comments.comment_indexed IS 'indexed form of comment field';


--
-- TOC entry 189 (class 1259 OID 16695)
-- Name: comments_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.comments_id_seq OWNER TO darwin2;

--
-- TOC entry 5355 (class 0 OID 0)
-- Dependencies: 189
-- Name: comments_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE comments_id_seq OWNED BY comments.id;


--
-- TOC entry 356 (class 1259 OID 712769)
-- Name: coordonnees_derivees_ichtyo_20170221; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE coordonnees_derivees_ichtyo_20170221 (
    specimenid character varying
);


ALTER TABLE darwin2.coordonnees_derivees_ichtyo_20170221 OWNER TO darwin2;

--
-- TOC entry 308 (class 1259 OID 17984)
-- Name: db_version; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE db_version (
    id integer NOT NULL,
    update_at timestamp without time zone DEFAULT now()
);


ALTER TABLE darwin2.db_version OWNER TO darwin2;

--
-- TOC entry 5356 (class 0 OID 0)
-- Dependencies: 308
-- Name: TABLE db_version; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE db_version IS 'Table holding the database version and update date';


--
-- TOC entry 205 (class 1259 OID 16821)
-- Name: expeditions; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE expeditions (
    id integer NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL,
    expedition_from_date_mask integer DEFAULT 0 NOT NULL,
    expedition_from_date date DEFAULT '0001-01-01'::date NOT NULL,
    expedition_to_date_mask integer DEFAULT 0 NOT NULL,
    expedition_to_date date DEFAULT '2038-12-31'::date NOT NULL
);


ALTER TABLE darwin2.expeditions OWNER TO darwin2;

--
-- TOC entry 5357 (class 0 OID 0)
-- Dependencies: 205
-- Name: TABLE expeditions; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE expeditions IS 'List of expeditions made to collect specimens';


--
-- TOC entry 5358 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.id IS 'Unique identifier of an expedition';


--
-- TOC entry 5359 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.name IS 'Expedition name';


--
-- TOC entry 5360 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.name_indexed IS 'Indexed form of expedition name';


--
-- TOC entry 5361 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.expedition_from_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.expedition_from_date_mask IS 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5362 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.expedition_from_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.expedition_from_date IS 'Start date of the expedition';


--
-- TOC entry 5363 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.expedition_to_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.expedition_to_date_mask IS 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5364 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN expeditions.expedition_to_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN expeditions.expedition_to_date IS 'End date of the expedition';


--
-- TOC entry 204 (class 1259 OID 16819)
-- Name: expeditions_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE expeditions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.expeditions_id_seq OWNER TO darwin2;

--
-- TOC entry 5366 (class 0 OID 0)
-- Dependencies: 204
-- Name: expeditions_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE expeditions_id_seq OWNED BY expeditions.id;


--
-- TOC entry 192 (class 1259 OID 16708)
-- Name: ext_links; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE ext_links (
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
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.ext_links OWNER TO darwin2;

--
-- TOC entry 5367 (class 0 OID 0)
-- Dependencies: 192
-- Name: TABLE ext_links; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE ext_links IS 'External link possibly refereced for a specific relation';


--
-- TOC entry 5368 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.referenced_relation IS 'Reference-Name of table a comment is posted for';


--
-- TOC entry 5369 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.record_id IS 'Identifier of the record concerned';


--
-- TOC entry 5370 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.id IS 'Unique identifier of a comment';


--
-- TOC entry 5371 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.url; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.url IS 'External URL';


--
-- TOC entry 5372 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.comment; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.comment IS 'Comment';


--
-- TOC entry 5373 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN ext_links.comment_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN ext_links.comment_indexed IS 'indexed form of comment field';


--
-- TOC entry 191 (class 1259 OID 16706)
-- Name: ext_links_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE ext_links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.ext_links_id_seq OWNER TO darwin2;

--
-- TOC entry 5375 (class 0 OID 0)
-- Dependencies: 191
-- Name: ext_links_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE ext_links_id_seq OWNED BY ext_links.id;


--
-- TOC entry 277 (class 1259 OID 17651)
-- Name: flat_dict; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE flat_dict (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    dict_field character varying NOT NULL,
    dict_value character varying NOT NULL,
    dict_depend character varying DEFAULT ''::character varying NOT NULL
);


ALTER TABLE darwin2.flat_dict OWNER TO darwin2;

--
-- TOC entry 5376 (class 0 OID 0)
-- Dependencies: 277
-- Name: TABLE flat_dict; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE flat_dict IS 'Flat table compiling all small distinct values for a faster search like types, code prefixes ,...';


--
-- TOC entry 5377 (class 0 OID 0)
-- Dependencies: 277
-- Name: COLUMN flat_dict.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN flat_dict.referenced_relation IS 'The table where the value come from';


--
-- TOC entry 5378 (class 0 OID 0)
-- Dependencies: 277
-- Name: COLUMN flat_dict.dict_field; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN flat_dict.dict_field IS 'the field name of where the value come from';


--
-- TOC entry 5379 (class 0 OID 0)
-- Dependencies: 277
-- Name: COLUMN flat_dict.dict_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN flat_dict.dict_value IS 'the distinct value';


--
-- TOC entry 276 (class 1259 OID 17649)
-- Name: flat_dict_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE flat_dict_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.flat_dict_id_seq OWNER TO darwin2;

--
-- TOC entry 5381 (class 0 OID 0)
-- Dependencies: 276
-- Name: flat_dict_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE flat_dict_id_seq OWNED BY flat_dict.id;


--
-- TOC entry 194 (class 1259 OID 16721)
-- Name: gtu; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE gtu (
    id integer NOT NULL,
    code character varying DEFAULT ''::character varying NOT NULL,
    gtu_from_date_mask integer DEFAULT 0,
    gtu_from_date timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone,
    gtu_to_date_mask integer DEFAULT 0,
    gtu_to_date timestamp without time zone DEFAULT '2038-12-31 00:00:00'::timestamp without time zone,
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
    elevation_unit character varying(4)
);


ALTER TABLE darwin2.gtu OWNER TO darwin2;

--
-- TOC entry 5383 (class 0 OID 0)
-- Dependencies: 194
-- Name: TABLE gtu; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE gtu IS 'Location or sampling units - GeoTemporalUnits';


--
-- TOC entry 5384 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.id IS 'Unique identifier of a location or sampling unit';


--
-- TOC entry 5385 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.code IS 'Code given - for sampling units - takes id if none defined';


--
-- TOC entry 5386 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.gtu_from_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.gtu_from_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5387 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.gtu_from_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.gtu_from_date IS 'composed from date of the GTU';


--
-- TOC entry 5388 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.gtu_to_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.gtu_to_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5389 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.gtu_to_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.gtu_to_date IS 'composed to date of the GTU';


--
-- TOC entry 5390 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.tag_values_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.tag_values_indexed IS 'Array of all tags associated to gtu (indexed form)';


--
-- TOC entry 5391 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.latitude; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.latitude IS 'Latitude of the gtu';


--
-- TOC entry 5392 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.longitude; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.longitude IS 'longitude of the gtu';


--
-- TOC entry 5393 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.lat_long_accuracy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.lat_long_accuracy IS 'Accuracy in meter of both lat & long';


--
-- TOC entry 5394 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.elevation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.elevation IS 'Elevation from the level of the sea in meter';


--
-- TOC entry 5395 (class 0 OID 0)
-- Dependencies: 194
-- Name: COLUMN gtu.elevation_accuracy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN gtu.elevation_accuracy IS 'Accuracy in meter of the elevation';


--
-- TOC entry 367 (class 1259 OID 715327)
-- Name: gtu_bck20170530; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE gtu_bck20170530 (
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
    elevation_unit character varying(4)
);


ALTER TABLE darwin2.gtu_bck20170530 OWNER TO darwin2;

--
-- TOC entry 193 (class 1259 OID 16719)
-- Name: gtu_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE gtu_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.gtu_id_seq OWNER TO darwin2;

--
-- TOC entry 5397 (class 0 OID 0)
-- Dependencies: 193
-- Name: gtu_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE gtu_id_seq OWNED BY gtu.id;


--
-- TOC entry 201 (class 1259 OID 16792)
-- Name: identifications; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE identifications (
    id integer NOT NULL,
    notion_concerned character varying NOT NULL,
    notion_date timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone NOT NULL,
    notion_date_mask integer DEFAULT 0 NOT NULL,
    value_defined character varying,
    value_defined_indexed character varying NOT NULL,
    determination_status character varying,
    order_by integer DEFAULT 1 NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.identifications OWNER TO darwin2;

--
-- TOC entry 5398 (class 0 OID 0)
-- Dependencies: 201
-- Name: TABLE identifications; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE identifications IS 'History of identifications';


--
-- TOC entry 5399 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.referenced_relation IS 'Reference of table an identification is introduced for';


--
-- TOC entry 5400 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.record_id IS 'Id of record concerned by an identification entry';


--
-- TOC entry 5401 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.id IS 'Unique identifier of an identification';


--
-- TOC entry 5402 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.notion_concerned; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.notion_concerned IS 'Type of entry: Identification on a specific concern';


--
-- TOC entry 5403 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.notion_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.notion_date IS 'Date of identification or preparation';


--
-- TOC entry 5404 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.notion_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.notion_date_mask IS 'Date/Time mask used for identification date fuzzyness';


--
-- TOC entry 5405 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.value_defined; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.value_defined IS 'When making identification, stores the value resulting of this identification';


--
-- TOC entry 5406 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.value_defined_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.value_defined_indexed IS 'Indexed form of value_defined field';


--
-- TOC entry 5407 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.determination_status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.determination_status IS 'Status of identification - can either be a percentage of certainty or a code describing the identification step in the process';


--
-- TOC entry 5408 (class 0 OID 0)
-- Dependencies: 201
-- Name: COLUMN identifications.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN identifications.order_by IS 'Integer used to order the identifications when no date entered';


--
-- TOC entry 200 (class 1259 OID 16790)
-- Name: identifications_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE identifications_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.identifications_id_seq OWNER TO darwin2;

--
-- TOC entry 5410 (class 0 OID 0)
-- Dependencies: 200
-- Name: identifications_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE identifications_id_seq OWNED BY identifications.id;


--
-- TOC entry 257 (class 1259 OID 17377)
-- Name: igs; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE igs (
    id integer NOT NULL,
    ig_num character varying NOT NULL,
    ig_num_indexed character varying NOT NULL,
    ig_date_mask integer DEFAULT 0 NOT NULL,
    ig_date date DEFAULT '0001-01-01'::date NOT NULL
);


ALTER TABLE darwin2.igs OWNER TO darwin2;

--
-- TOC entry 5411 (class 0 OID 0)
-- Dependencies: 257
-- Name: TABLE igs; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE igs IS 'Inventory table - register all ig (inventory general) numbers given in RBINS';


--
-- TOC entry 5412 (class 0 OID 0)
-- Dependencies: 257
-- Name: COLUMN igs.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN igs.id IS 'Unique identifier of an ig reference';


--
-- TOC entry 5413 (class 0 OID 0)
-- Dependencies: 257
-- Name: COLUMN igs.ig_num; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN igs.ig_num IS 'IG number';


--
-- TOC entry 5414 (class 0 OID 0)
-- Dependencies: 257
-- Name: COLUMN igs.ig_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN igs.ig_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5415 (class 0 OID 0)
-- Dependencies: 257
-- Name: COLUMN igs.ig_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN igs.ig_date IS 'Date of ig number creation';


--
-- TOC entry 256 (class 1259 OID 17375)
-- Name: igs_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE igs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.igs_id_seq OWNER TO darwin2;

--
-- TOC entry 5417 (class 0 OID 0)
-- Dependencies: 256
-- Name: igs_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE igs_id_seq OWNED BY igs.id;


--
-- TOC entry 279 (class 1259 OID 17665)
-- Name: imports; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE imports (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    format character varying NOT NULL,
    collection_ref integer,
    filename character varying NOT NULL,
    state character varying DEFAULT ''::character varying NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone,
    initial_count integer DEFAULT 0 NOT NULL,
    is_finished boolean DEFAULT false NOT NULL,
    errors_in_import text,
    template_version text,
    exclude_invalid_entries boolean DEFAULT false NOT NULL
);


ALTER TABLE darwin2.imports OWNER TO darwin2;

--
-- TOC entry 5418 (class 0 OID 0)
-- Dependencies: 279
-- Name: TABLE imports; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE imports IS 'Table used to check the state of the date coming from an uploaded file';


--
-- TOC entry 5419 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.user_ref IS 'The referenced user id';


--
-- TOC entry 5420 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.format; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.format IS 'The import template to use for the imported file';


--
-- TOC entry 5421 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.collection_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.collection_ref IS 'The collection associated';


--
-- TOC entry 5422 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.filename; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.filename IS 'The filename of the file to proceed';


--
-- TOC entry 5423 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.state; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.state IS 'the state of the processing the file';


--
-- TOC entry 5424 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.created_at; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.created_at IS 'Creation of the file';


--
-- TOC entry 5425 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.updated_at; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.updated_at IS 'When the data has been modified lately';


--
-- TOC entry 5426 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.initial_count; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.initial_count IS 'Number of rows of staging when the import was created';


--
-- TOC entry 5427 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.is_finished; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.is_finished IS 'Boolean to mark if the import is finished or still need some operations';


--
-- TOC entry 5428 (class 0 OID 0)
-- Dependencies: 279
-- Name: COLUMN imports.exclude_invalid_entries; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN imports.exclude_invalid_entries IS 'Tell if, for this import, match should exclude the invalid units';


--
-- TOC entry 278 (class 1259 OID 17663)
-- Name: imports_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE imports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.imports_id_seq OWNER TO darwin2;

--
-- TOC entry 5430 (class 0 OID 0)
-- Dependencies: 278
-- Name: imports_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE imports_id_seq OWNED BY imports.id;


--
-- TOC entry 231 (class 1259 OID 17094)
-- Name: informative_workflow; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE informative_workflow (
    id integer NOT NULL,
    user_ref integer,
    formated_name character varying DEFAULT 'anonymous'::character varying NOT NULL,
    status character varying DEFAULT 'suggestion'::character varying NOT NULL,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL,
    is_last boolean DEFAULT true NOT NULL,
    comment character varying NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.informative_workflow OWNER TO darwin2;

--
-- TOC entry 5431 (class 0 OID 0)
-- Dependencies: 231
-- Name: TABLE informative_workflow; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE informative_workflow IS 'Workflow information for each record encoded';


--
-- TOC entry 5432 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5433 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.record_id IS 'ID of record a workflow is defined for';


--
-- TOC entry 5434 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.user_ref IS 'Reference of user - id field of users table';


--
-- TOC entry 5435 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.formated_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.formated_name IS 'used to allow non registered user to add a workflow';


--
-- TOC entry 5436 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.status IS 'Record status number: to correct, to be corrected or published ';


--
-- TOC entry 5437 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.modification_date_time IS 'Date and time of status change - last date/time is used as actual status, but helps also to keep an history of status change';


--
-- TOC entry 5438 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.is_last; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.is_last IS 'a flag witch allow us to know if the workflow for this referenced_relation/record id is the latest';


--
-- TOC entry 5439 (class 0 OID 0)
-- Dependencies: 231
-- Name: COLUMN informative_workflow.comment; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN informative_workflow.comment IS 'Complementary comments';


--
-- TOC entry 230 (class 1259 OID 17092)
-- Name: informative_workflow_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE informative_workflow_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.informative_workflow_id_seq OWNER TO darwin2;

--
-- TOC entry 5441 (class 0 OID 0)
-- Dependencies: 230
-- Name: informative_workflow_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE informative_workflow_id_seq OWNED BY informative_workflow.id;


--
-- TOC entry 263 (class 1259 OID 17499)
-- Name: insurances; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE insurances (
    id integer NOT NULL,
    insurance_value numeric(16,2) NOT NULL,
    insurance_currency character varying DEFAULT '€'::character varying NOT NULL,
    date_from_mask integer DEFAULT 0 NOT NULL,
    date_from date DEFAULT '0001-01-01'::date NOT NULL,
    date_to_mask integer DEFAULT 0 NOT NULL,
    date_to date DEFAULT '2038-12-31'::date NOT NULL,
    insurer_ref integer,
    contact_ref integer,
    CONSTRAINT chk_chk_insurances CHECK ((insurance_value > (0)::numeric))
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.insurances OWNER TO darwin2;

--
-- TOC entry 5443 (class 0 OID 0)
-- Dependencies: 263
-- Name: TABLE insurances; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE insurances IS 'List of insurances values for given specimen or the loan';


--
-- TOC entry 5444 (class 0 OID 0)
-- Dependencies: 263
-- Name: COLUMN insurances.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN insurances.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5445 (class 0 OID 0)
-- Dependencies: 263
-- Name: COLUMN insurances.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN insurances.record_id IS 'Identifier of record concerned';


--
-- TOC entry 5446 (class 0 OID 0)
-- Dependencies: 263
-- Name: COLUMN insurances.insurance_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN insurances.insurance_value IS 'Insurance value';


--
-- TOC entry 5447 (class 0 OID 0)
-- Dependencies: 263
-- Name: COLUMN insurances.insurance_currency; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN insurances.insurance_currency IS 'Currency used with insurance value';


--
-- TOC entry 5448 (class 0 OID 0)
-- Dependencies: 263
-- Name: COLUMN insurances.insurer_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN insurances.insurer_ref IS 'Reference of the insurance firm an insurance have been subscripted at';


--
-- TOC entry 262 (class 1259 OID 17497)
-- Name: insurances_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE insurances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.insurances_id_seq OWNER TO darwin2;

--
-- TOC entry 5450 (class 0 OID 0)
-- Dependencies: 262
-- Name: insurances_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE insurances_id_seq OWNED BY insurances.id;


--
-- TOC entry 255 (class 1259 OID 17351)
-- Name: lithology; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE lithology (
    id integer NOT NULL,
    CONSTRAINT fct_chk_onceinpath_lithology CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
)
INHERITS (template_classifications);


ALTER TABLE darwin2.lithology OWNER TO darwin2;

--
-- TOC entry 5451 (class 0 OID 0)
-- Dependencies: 255
-- Name: TABLE lithology; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE lithology IS 'List of lithologic units';


--
-- TOC entry 5452 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.name IS 'Classification unit name';


--
-- TOC entry 5453 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.name_indexed IS 'Indexed form of name field';


--
-- TOC entry 5454 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5455 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5456 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5457 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 5458 (class 0 OID 0)
-- Dependencies: 255
-- Name: COLUMN lithology.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithology.id IS 'Unique identifier of a classification unit';


--
-- TOC entry 254 (class 1259 OID 17349)
-- Name: lithology_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE lithology_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.lithology_id_seq OWNER TO darwin2;

--
-- TOC entry 5460 (class 0 OID 0)
-- Dependencies: 254
-- Name: lithology_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE lithology_id_seq OWNED BY lithology.id;


--
-- TOC entry 251 (class 1259 OID 17298)
-- Name: lithostratigraphy; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE lithostratigraphy (
    id integer NOT NULL,
    CONSTRAINT fct_chk_onceinpath_lithostratigraphy CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
)
INHERITS (template_classifications);


ALTER TABLE darwin2.lithostratigraphy OWNER TO darwin2;

--
-- TOC entry 5461 (class 0 OID 0)
-- Dependencies: 251
-- Name: TABLE lithostratigraphy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE lithostratigraphy IS 'List of lithostratigraphic units';


--
-- TOC entry 5462 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.name IS 'Classification unit name';


--
-- TOC entry 5463 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.name_indexed IS 'Indexed form of name field';


--
-- TOC entry 5464 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5465 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5466 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5467 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 5468 (class 0 OID 0)
-- Dependencies: 251
-- Name: COLUMN lithostratigraphy.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN lithostratigraphy.id IS 'Unique identifier of a classification unit';


--
-- TOC entry 250 (class 1259 OID 17296)
-- Name: lithostratigraphy_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE lithostratigraphy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.lithostratigraphy_id_seq OWNER TO darwin2;

--
-- TOC entry 5470 (class 0 OID 0)
-- Dependencies: 250
-- Name: lithostratigraphy_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE lithostratigraphy_id_seq OWNED BY lithostratigraphy.id;


--
-- TOC entry 301 (class 1259 OID 17926)
-- Name: loan_history; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE loan_history (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    referenced_table text NOT NULL,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL,
    record_line public.hstore
);


ALTER TABLE darwin2.loan_history OWNER TO darwin2;

--
-- TOC entry 5471 (class 0 OID 0)
-- Dependencies: 301
-- Name: TABLE loan_history; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE loan_history IS 'Table is a snapshot of an entire loan and related informations at a certain time';


--
-- TOC entry 5472 (class 0 OID 0)
-- Dependencies: 301
-- Name: COLUMN loan_history.loan_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_history.loan_ref IS 'Mandatory Reference to a loan';


--
-- TOC entry 5473 (class 0 OID 0)
-- Dependencies: 301
-- Name: COLUMN loan_history.referenced_table; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_history.referenced_table IS 'Mandatory Reference to the table refereced';


--
-- TOC entry 5474 (class 0 OID 0)
-- Dependencies: 301
-- Name: COLUMN loan_history.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_history.modification_date_time IS 'date of the modification';


--
-- TOC entry 5475 (class 0 OID 0)
-- Dependencies: 301
-- Name: COLUMN loan_history.record_line; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_history.record_line IS 'hstore containing the whole line of referenced_table';


--
-- TOC entry 300 (class 1259 OID 17924)
-- Name: loan_history_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE loan_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.loan_history_id_seq OWNER TO darwin2;

--
-- TOC entry 5477 (class 0 OID 0)
-- Dependencies: 300
-- Name: loan_history_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE loan_history_id_seq OWNED BY loan_history.id;


--
-- TOC entry 295 (class 1259 OID 17851)
-- Name: loan_items; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE loan_items (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    ig_ref integer,
    from_date date,
    to_date date,
    specimen_ref integer,
    details character varying DEFAULT ''::character varying
);


ALTER TABLE darwin2.loan_items OWNER TO darwin2;

--
-- TOC entry 5478 (class 0 OID 0)
-- Dependencies: 295
-- Name: TABLE loan_items; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE loan_items IS 'Table holding an item of a loan. It may be a part from darwin or only an generic item';


--
-- TOC entry 5479 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.id IS 'Unique identifier of record';


--
-- TOC entry 5480 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.loan_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.loan_ref IS 'Mandatory Reference to a loan';


--
-- TOC entry 5481 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.ig_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.ig_ref IS 'Optional ref to an IG stored in the igs table';


--
-- TOC entry 5482 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.from_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.from_date IS 'Date when the item was sended';


--
-- TOC entry 5483 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.to_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.to_date IS 'Date when the item was recieved back';


--
-- TOC entry 5484 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.specimen_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.specimen_ref IS 'Optional reference to a Darwin Part';


--
-- TOC entry 5485 (class 0 OID 0)
-- Dependencies: 295
-- Name: COLUMN loan_items.details; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_items.details IS 'Textual details describing the item';


--
-- TOC entry 294 (class 1259 OID 17849)
-- Name: loan_items_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE loan_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.loan_items_id_seq OWNER TO darwin2;

--
-- TOC entry 5487 (class 0 OID 0)
-- Dependencies: 294
-- Name: loan_items_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE loan_items_id_seq OWNED BY loan_items.id;


--
-- TOC entry 297 (class 1259 OID 17880)
-- Name: loan_rights; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE loan_rights (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    user_ref integer NOT NULL,
    has_encoding_right boolean DEFAULT false NOT NULL
);


ALTER TABLE darwin2.loan_rights OWNER TO darwin2;

--
-- TOC entry 5488 (class 0 OID 0)
-- Dependencies: 297
-- Name: TABLE loan_rights; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE loan_rights IS 'Table describing rights into an entire loan (if user is in the table he has at least viewing rights)';


--
-- TOC entry 5489 (class 0 OID 0)
-- Dependencies: 297
-- Name: COLUMN loan_rights.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_rights.id IS 'Unique identifier of record';


--
-- TOC entry 5490 (class 0 OID 0)
-- Dependencies: 297
-- Name: COLUMN loan_rights.loan_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_rights.loan_ref IS 'Mandatory Reference to a loan';


--
-- TOC entry 5491 (class 0 OID 0)
-- Dependencies: 297
-- Name: COLUMN loan_rights.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_rights.user_ref IS 'Mandatory Reference to a user';


--
-- TOC entry 5492 (class 0 OID 0)
-- Dependencies: 297
-- Name: COLUMN loan_rights.has_encoding_right; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_rights.has_encoding_right IS 'Bool saying if the user can edit a loan';


--
-- TOC entry 296 (class 1259 OID 17878)
-- Name: loan_rights_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE loan_rights_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.loan_rights_id_seq OWNER TO darwin2;

--
-- TOC entry 5494 (class 0 OID 0)
-- Dependencies: 296
-- Name: loan_rights_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE loan_rights_id_seq OWNED BY loan_rights.id;


--
-- TOC entry 299 (class 1259 OID 17901)
-- Name: loan_status; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE loan_status (
    id integer NOT NULL,
    loan_ref integer NOT NULL,
    user_ref integer NOT NULL,
    status character varying DEFAULT 'new'::character varying NOT NULL,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL,
    comment character varying DEFAULT ''::character varying NOT NULL,
    is_last boolean DEFAULT true NOT NULL
);


ALTER TABLE darwin2.loan_status OWNER TO darwin2;

--
-- TOC entry 5495 (class 0 OID 0)
-- Dependencies: 299
-- Name: TABLE loan_status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE loan_status IS 'Table describing various states of a loan';


--
-- TOC entry 5496 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.id IS 'Unique identifier of record';


--
-- TOC entry 5497 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.loan_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.loan_ref IS 'Mandatory Reference to a loan';


--
-- TOC entry 5498 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.user_ref IS 'Mandatory Reference to a user';


--
-- TOC entry 5499 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.status IS 'Current status of the loan in a list (new, closed, running, ...)';


--
-- TOC entry 5500 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.modification_date_time IS 'date of the modification';


--
-- TOC entry 5501 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.comment; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.comment IS 'comment of the status modification';


--
-- TOC entry 5502 (class 0 OID 0)
-- Dependencies: 299
-- Name: COLUMN loan_status.is_last; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loan_status.is_last IS 'flag telling which line is the current line';


--
-- TOC entry 298 (class 1259 OID 17899)
-- Name: loan_status_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE loan_status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.loan_status_id_seq OWNER TO darwin2;

--
-- TOC entry 5504 (class 0 OID 0)
-- Dependencies: 298
-- Name: loan_status_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE loan_status_id_seq OWNED BY loan_status.id;


--
-- TOC entry 293 (class 1259 OID 17838)
-- Name: loans; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE loans (
    id integer NOT NULL,
    name character varying DEFAULT ''::character varying NOT NULL,
    description character varying DEFAULT ''::character varying NOT NULL,
    search_indexed text NOT NULL,
    from_date date,
    to_date date,
    extended_to_date date,
    collection_ref integer
);


ALTER TABLE darwin2.loans OWNER TO darwin2;

--
-- TOC entry 5505 (class 0 OID 0)
-- Dependencies: 293
-- Name: TABLE loans; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE loans IS 'Table holding an entire loan made of multiple loan items may also be linked to other table as comment, properties , ...';


--
-- TOC entry 5506 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.id IS 'Unique identifier of record';


--
-- TOC entry 5507 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.name IS 'Global name of the loan. May be a sort of code of other naming scheme';


--
-- TOC entry 5508 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.description; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.description IS 'Description of the meaning of the loan';


--
-- TOC entry 5509 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.search_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.search_indexed IS 'indexed getting Description and title of the loan';


--
-- TOC entry 5510 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.from_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.from_date IS 'Date of the start of the loan';


--
-- TOC entry 5511 (class 0 OID 0)
-- Dependencies: 293
-- Name: COLUMN loans.to_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN loans.to_date IS 'Planned date of the end of the loan';


--
-- TOC entry 292 (class 1259 OID 17836)
-- Name: loans_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE loans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.loans_id_seq OWNER TO darwin2;

--
-- TOC entry 5513 (class 0 OID 0)
-- Dependencies: 292
-- Name: loans_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE loans_id_seq OWNED BY loans.id;


--
-- TOC entry 253 (class 1259 OID 17324)
-- Name: mineralogy; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE mineralogy (
    id integer NOT NULL,
    code character varying NOT NULL,
    classification character varying DEFAULT 'strunz'::character varying NOT NULL,
    formule character varying,
    formule_indexed character varying,
    cristal_system character varying,
    CONSTRAINT fct_chk_onceinpath_mineralogy CHECK (fct_chk_onceinpath(((((COALESCE(path, ''::character varying))::text || '/'::text) || id))::character varying))
)
INHERITS (template_classifications);


ALTER TABLE darwin2.mineralogy OWNER TO darwin2;

--
-- TOC entry 5514 (class 0 OID 0)
-- Dependencies: 253
-- Name: TABLE mineralogy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE mineralogy IS 'List of mineralogic units';


--
-- TOC entry 5515 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.name IS 'Classification unit name';


--
-- TOC entry 5516 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.name_indexed IS 'Indexed form of name field';


--
-- TOC entry 5517 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.level_ref IS 'Reference of classification level the unit is encoded in';


--
-- TOC entry 5518 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.status IS 'Validitiy status: valid, invalid, in discussion';


--
-- TOC entry 5519 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.path IS 'Hierarchy path (/ for root)';


--
-- TOC entry 5520 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.parent_ref IS 'Id of parent - id field from table itself';


--
-- TOC entry 5521 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.id IS 'Unique identifier of a classification unit';


--
-- TOC entry 5522 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.code IS 'Classification code given to mineral - in classification chosen - Strunz by default';


--
-- TOC entry 5523 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.classification; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.classification IS 'Classification system used to describe mineral: strunz, dana,...';


--
-- TOC entry 5524 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.formule; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.formule IS 'Chemical formulation';


--
-- TOC entry 5525 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.formule_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.formule_indexed IS 'Indexed form of foumule field';


--
-- TOC entry 5526 (class 0 OID 0)
-- Dependencies: 253
-- Name: COLUMN mineralogy.cristal_system; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN mineralogy.cristal_system IS 'Cristal system defining the mineral structure: isometric, hexagonal,...';


--
-- TOC entry 252 (class 1259 OID 17322)
-- Name: mineralogy_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE mineralogy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.mineralogy_id_seq OWNER TO darwin2;

--
-- TOC entry 5528 (class 0 OID 0)
-- Dependencies: 252
-- Name: mineralogy_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE mineralogy_id_seq OWNED BY mineralogy.id;


--
-- TOC entry 211 (class 1259 OID 16885)
-- Name: multimedia; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE multimedia (
    id integer NOT NULL,
    is_digital boolean DEFAULT true NOT NULL,
    type character varying DEFAULT 'image'::character varying NOT NULL,
    sub_type character varying,
    title character varying NOT NULL,
    description character varying DEFAULT ''::character varying NOT NULL,
    uri character varying,
    filename character varying,
    search_indexed text NOT NULL,
    creation_date date DEFAULT '0001-01-01'::date NOT NULL,
    creation_date_mask integer DEFAULT 0 NOT NULL,
    mime_type character varying NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    publishable boolean DEFAULT true NOT NULL,
    extracted_info text
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.multimedia OWNER TO darwin2;

--
-- TOC entry 5529 (class 0 OID 0)
-- Dependencies: 211
-- Name: TABLE multimedia; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE multimedia IS 'Stores all multimedia objects encoded in DaRWIN 2.0';


--
-- TOC entry 5530 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5531 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.record_id IS 'Identifier of record concerned';


--
-- TOC entry 5532 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.id IS 'Unique identifier of a multimedia object';


--
-- TOC entry 5533 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.is_digital; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.is_digital IS 'Flag telling if the object is digital (true) or physical (false)';


--
-- TOC entry 5534 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.type IS 'Main multimedia object type: image, sound, video,...';


--
-- TOC entry 5535 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.sub_type IS 'Characterization of object type: article, publication in serie, book, glass plate,...';


--
-- TOC entry 5536 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.title; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.title IS 'Title of the multimedia object';


--
-- TOC entry 5537 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.description; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.description IS 'Description of the current object';


--
-- TOC entry 5538 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.uri; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.uri IS 'URI of object if digital';


--
-- TOC entry 5539 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.filename; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.filename IS 'The original name of the saved file';


--
-- TOC entry 5540 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.search_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.search_indexed IS 'indexed form of title and description fields together';


--
-- TOC entry 5541 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.creation_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.creation_date IS 'Object creation date';


--
-- TOC entry 5542 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.creation_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.creation_date_mask IS 'Mask used for object creation date display';


--
-- TOC entry 5543 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.mime_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.mime_type IS 'Mime/Type of the linked digital object';


--
-- TOC entry 5544 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.visible; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.visible IS 'Flag telling if the related file has been chosen to be publically visible or not';


--
-- TOC entry 5545 (class 0 OID 0)
-- Dependencies: 211
-- Name: COLUMN multimedia.publishable; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia.publishable IS 'Flag telling if the related file has been chosen as a prefered item for publication - Would be for example used for preselection of media published for Open Up project';


--
-- TOC entry 210 (class 1259 OID 16883)
-- Name: multimedia_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE multimedia_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.multimedia_id_seq OWNER TO darwin2;

--
-- TOC entry 5547 (class 0 OID 0)
-- Dependencies: 210
-- Name: multimedia_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE multimedia_id_seq OWNED BY multimedia.id;


--
-- TOC entry 303 (class 1259 OID 17943)
-- Name: multimedia_todelete; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE multimedia_todelete (
    id integer NOT NULL,
    uri text
);


ALTER TABLE darwin2.multimedia_todelete OWNER TO darwin2;

--
-- TOC entry 5548 (class 0 OID 0)
-- Dependencies: 303
-- Name: TABLE multimedia_todelete; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE multimedia_todelete IS 'Table here to save deleted multimedia files waiting for a deletion on the disk';


--
-- TOC entry 5549 (class 0 OID 0)
-- Dependencies: 303
-- Name: COLUMN multimedia_todelete.uri; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN multimedia_todelete.uri IS 'URI of the file to delete';


--
-- TOC entry 302 (class 1259 OID 17941)
-- Name: multimedia_todelete_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE multimedia_todelete_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.multimedia_todelete_id_seq OWNER TO darwin2;

--
-- TOC entry 5550 (class 0 OID 0)
-- Dependencies: 302
-- Name: multimedia_todelete_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE multimedia_todelete_id_seq OWNED BY multimedia_todelete.id;


--
-- TOC entry 237 (class 1259 OID 17151)
-- Name: my_saved_searches; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE my_saved_searches (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    name character varying DEFAULT 'default'::character varying NOT NULL,
    search_criterias character varying NOT NULL,
    favorite boolean DEFAULT false NOT NULL,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL,
    visible_fields_in_result character varying NOT NULL,
    is_only_id boolean DEFAULT false NOT NULL,
    subject character varying DEFAULT 'specimen'::character varying NOT NULL,
    query_where character varying,
    query_parameters character varying
);


ALTER TABLE darwin2.my_saved_searches OWNER TO darwin2;

--
-- TOC entry 5551 (class 0 OID 0)
-- Dependencies: 237
-- Name: TABLE my_saved_searches; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE my_saved_searches IS 'Stores user''s saved searches but also (by default) the last search done';


--
-- TOC entry 5552 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.user_ref IS 'Reference of user having saved a search';


--
-- TOC entry 5553 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.name IS 'Name given by user to his/her saved search';


--
-- TOC entry 5554 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.search_criterias; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.search_criterias IS 'String field containing the serialization of search criterias';


--
-- TOC entry 5555 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.favorite; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.favorite IS 'Flag telling if saved search concerned is one of the favorites or not';


--
-- TOC entry 5556 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.modification_date_time IS 'Last modification or entry date and time';


--
-- TOC entry 5557 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.visible_fields_in_result; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.visible_fields_in_result IS 'Array of fields that were set visible in the result table at the time the search was saved';


--
-- TOC entry 5558 (class 0 OID 0)
-- Dependencies: 237
-- Name: COLUMN my_saved_searches.is_only_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_saved_searches.is_only_id IS 'Tell if the search only contains saved specimen (ids) or it is a normal saved search';


--
-- TOC entry 236 (class 1259 OID 17149)
-- Name: my_saved_searches_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE my_saved_searches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.my_saved_searches_id_seq OWNER TO darwin2;

--
-- TOC entry 5560 (class 0 OID 0)
-- Dependencies: 236
-- Name: my_saved_searches_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE my_saved_searches_id_seq OWNED BY my_saved_searches.id;


--
-- TOC entry 239 (class 1259 OID 17174)
-- Name: my_widgets; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE my_widgets (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    category character varying DEFAULT 'board_widget'::character varying NOT NULL,
    group_name character varying NOT NULL,
    order_by smallint DEFAULT 1 NOT NULL,
    col_num smallint DEFAULT 1 NOT NULL,
    mandatory boolean DEFAULT false NOT NULL,
    visible boolean DEFAULT true NOT NULL,
    opened boolean DEFAULT true NOT NULL,
    color character varying DEFAULT '#5BAABD'::character varying NOT NULL,
    is_available boolean DEFAULT false NOT NULL,
    icon_ref integer,
    title_perso character varying(32),
    collections character varying DEFAULT ','::character varying NOT NULL,
    all_public boolean DEFAULT false NOT NULL
);


ALTER TABLE darwin2.my_widgets OWNER TO darwin2;

--
-- TOC entry 5561 (class 0 OID 0)
-- Dependencies: 239
-- Name: TABLE my_widgets; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE my_widgets IS 'Stores user''s preferences for customizable page elements - widgets mainly';


--
-- TOC entry 5562 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.user_ref IS 'Reference of user concerned - id field of users table';


--
-- TOC entry 5563 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.category; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.category IS 'Customizable page element category: board widget, encoding widget,...';


--
-- TOC entry 5564 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.group_name IS 'Customizable page element name';


--
-- TOC entry 5565 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.order_by IS 'Absolute order by between page element name';


--
-- TOC entry 5566 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.col_num; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.col_num IS 'Column number - tells in which column the page element concerned is';


--
-- TOC entry 5567 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.mandatory; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.mandatory IS 'Flag telling if the page element can be closed or not';


--
-- TOC entry 5568 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.visible; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.visible IS 'Flag telling if the page element is on the board or in the widget chooser';


--
-- TOC entry 5569 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.opened; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.opened IS 'Flag telling if the page element is opened by default or not';


--
-- TOC entry 5570 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.color; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.color IS 'Color given to page element by user';


--
-- TOC entry 5571 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.is_available; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.is_available IS 'Flag telling if the widget can be used or not';


--
-- TOC entry 5572 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.icon_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.icon_ref IS 'Reference of multimedia icon to be used before page element title';


--
-- TOC entry 5573 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.title_perso; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.title_perso IS 'Page element title given by user';


--
-- TOC entry 5574 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.collections; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.collections IS 'list of collections which user_ref has rights to see';


--
-- TOC entry 5575 (class 0 OID 0)
-- Dependencies: 239
-- Name: COLUMN my_widgets.all_public; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN my_widgets.all_public IS 'Set to determine if the widget available for a registered user by default or not';


--
-- TOC entry 238 (class 1259 OID 17172)
-- Name: my_widgets_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE my_widgets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.my_widgets_id_seq OWNER TO darwin2;

--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 238
-- Name: my_widgets_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE my_widgets_id_seq OWNED BY my_widgets.id;


--
-- TOC entry 178 (class 1259 OID 16588)
-- Name: template_people; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE template_people (
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
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 178
-- Name: TABLE template_people; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE template_people IS 'Template table used to describe user/people tables';


--
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.is_physical; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.is_physical IS 'Type of user/person: physical or moral - true is physical, false is moral';


--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.sub_type IS 'Used for moral user/persons: precise nature - public institution, asbl, sprl, sa,...';


--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.formated_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.formated_name IS 'Complete user/person formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';


--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.formated_name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.formated_name_indexed IS 'Indexed form of formated_name field';


--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.formated_name_unique; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.formated_name_unique IS 'Indexed form of formated_name field (for unique index purpose)';


--
-- TOC entry 5585 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.title; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.title IS 'Title of a physical user/person like Mr or Mrs or phd,...';


--
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.family_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.family_name IS 'Family name for physical user/persons and Organisation name for moral user/persons';


--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.given_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.given_name IS 'User/person''s given name - usually first name';


--
-- TOC entry 5588 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.additional_names; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.additional_names IS 'Any additional names given to user/person';


--
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.birth_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.birth_date_mask IS 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.birth_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.birth_date IS 'Birth/Creation date composed';


--
-- TOC entry 5591 (class 0 OID 0)
-- Dependencies: 178
-- Name: COLUMN template_people.gender; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people.gender IS 'For physical user/persons give the gender: M or F';


--
-- TOC entry 180 (class 1259 OID 16601)
-- Name: people; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people (
    id integer NOT NULL,
    end_date_mask integer DEFAULT 0 NOT NULL,
    end_date date DEFAULT '2038-12-31'::date NOT NULL,
    activity_date_from_mask integer DEFAULT 0 NOT NULL,
    activity_date_from date DEFAULT '0001-01-01'::date NOT NULL,
    activity_date_to_mask integer DEFAULT 0 NOT NULL,
    activity_date_to date DEFAULT '2038-12-31'::date NOT NULL,
    name_formated_indexed character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_people);


ALTER TABLE darwin2.people OWNER TO darwin2;

--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 180
-- Name: TABLE people; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE people IS 'All physical and moral persons used in the application are here stored';


--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.is_physical; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.is_physical IS 'Type of person: physical or moral - true is physical, false is moral';


--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.sub_type IS 'Used for moral persons: precise nature - public institution, asbl, sprl, sa,...';


--
-- TOC entry 5596 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.formated_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.formated_name IS 'Complete person formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';


--
-- TOC entry 5597 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.formated_name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.formated_name_indexed IS 'Indexed form of formated_name field';


--
-- TOC entry 5598 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.title; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.title IS 'Title of a physical user/person like Mr or Mrs or phd,...';


--
-- TOC entry 5599 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.family_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.family_name IS 'Family name for physical persons and Organisation name for moral persons';


--
-- TOC entry 5600 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.given_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.given_name IS 'User/person''s given name - usually first name';


--
-- TOC entry 5601 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.additional_names; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.additional_names IS 'Any additional names given to person';


--
-- TOC entry 5602 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.birth_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.birth_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5603 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.birth_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.birth_date IS 'Day of birth/creation';


--
-- TOC entry 5604 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.gender; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.gender IS 'For physical persons give the gender: M or F';


--
-- TOC entry 5605 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.id IS 'Unique identifier of a person';


--
-- TOC entry 5606 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.end_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.end_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5607 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.end_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.end_date IS 'End date';


--
-- TOC entry 5608 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.activity_date_from_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.activity_date_from_mask IS 'person general activity period or person activity period in the organization referenced date from mask';


--
-- TOC entry 5609 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.activity_date_from; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.activity_date_from IS 'person general activity period or person activity period in the organization referenced date from';


--
-- TOC entry 5610 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.activity_date_to_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.activity_date_to_mask IS 'person general activity period or person activity period in the organization referenced date to mask';


--
-- TOC entry 5611 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.activity_date_to; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.activity_date_to IS 'person general activity period or person activity period in the organization referenced date to';


--
-- TOC entry 5612 (class 0 OID 0)
-- Dependencies: 180
-- Name: COLUMN people.name_formated_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people.name_formated_indexed IS 'The indexed form of given_name and family_name (the inverse of formated_name_indexed for searching)';


--
-- TOC entry 213 (class 1259 OID 16907)
-- Name: template_people_users_addr_common; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE template_people_users_addr_common (
    po_box character varying,
    extended_address character varying,
    locality character varying NOT NULL,
    region character varying,
    zip_code character varying,
    country character varying NOT NULL
);


ALTER TABLE darwin2.template_people_users_addr_common OWNER TO darwin2;

--
-- TOC entry 5614 (class 0 OID 0)
-- Dependencies: 213
-- Name: TABLE template_people_users_addr_common; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE template_people_users_addr_common IS 'Template table used to construct addresses tables for people/users';


--
-- TOC entry 5615 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.po_box; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.po_box IS 'PO Box';


--
-- TOC entry 5616 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.extended_address; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.extended_address IS 'Address extension: State, Special post zip code characters,...';


--
-- TOC entry 5617 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.locality; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.locality IS 'Locality';


--
-- TOC entry 5618 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.region; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.region IS 'Region';


--
-- TOC entry 5619 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.zip_code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.zip_code IS 'zip code';


--
-- TOC entry 5620 (class 0 OID 0)
-- Dependencies: 213
-- Name: COLUMN template_people_users_addr_common.country; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_addr_common.country IS 'Country';


--
-- TOC entry 212 (class 1259 OID 16901)
-- Name: template_people_users_comm_common; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE template_people_users_comm_common (
    person_user_ref integer NOT NULL,
    entry character varying NOT NULL
);


ALTER TABLE darwin2.template_people_users_comm_common OWNER TO darwin2;

--
-- TOC entry 5622 (class 0 OID 0)
-- Dependencies: 212
-- Name: TABLE template_people_users_comm_common; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE template_people_users_comm_common IS 'Template table used to construct people communication tables (tel and e-mail)';


--
-- TOC entry 5623 (class 0 OID 0)
-- Dependencies: 212
-- Name: COLUMN template_people_users_comm_common.person_user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_comm_common.person_user_ref IS 'Reference of person/user - id field of people/users table';


--
-- TOC entry 5624 (class 0 OID 0)
-- Dependencies: 212
-- Name: COLUMN template_people_users_comm_common.entry; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN template_people_users_comm_common.entry IS 'Communication entry';


--
-- TOC entry 219 (class 1259 OID 16959)
-- Name: people_addresses; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people_addresses (
    id integer NOT NULL,
    tag character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_people_users_comm_common, template_people_users_addr_common);


ALTER TABLE darwin2.people_addresses OWNER TO darwin2;

--
-- TOC entry 5626 (class 0 OID 0)
-- Dependencies: 219
-- Name: TABLE people_addresses; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE people_addresses IS 'People addresses';


--
-- TOC entry 5627 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.person_user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.person_user_ref IS 'Reference of the person concerned - id field of people table';


--
-- TOC entry 5628 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.entry; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.entry IS 'Street address';


--
-- TOC entry 5629 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.po_box; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.po_box IS 'PO Box';


--
-- TOC entry 5630 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.extended_address; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.extended_address IS 'Address extension: State, zip code suffix,...';


--
-- TOC entry 5631 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.locality; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.locality IS 'Locality';


--
-- TOC entry 5632 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.region; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.region IS 'Region';


--
-- TOC entry 5633 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.zip_code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.zip_code IS 'Zip code';


--
-- TOC entry 5634 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.country; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.country IS 'Country';


--
-- TOC entry 5635 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.id IS 'Unique identifier of a person address';


--
-- TOC entry 5636 (class 0 OID 0)
-- Dependencies: 219
-- Name: COLUMN people_addresses.tag; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_addresses.tag IS 'List of descriptive tags: home, work,...';


--
-- TOC entry 218 (class 1259 OID 16957)
-- Name: people_addresses_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE people_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.people_addresses_id_seq OWNER TO darwin2;

--
-- TOC entry 5638 (class 0 OID 0)
-- Dependencies: 218
-- Name: people_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE people_addresses_id_seq OWNED BY people_addresses.id;


--
-- TOC entry 217 (class 1259 OID 16941)
-- Name: people_comm; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people_comm (
    id integer NOT NULL,
    comm_type character varying DEFAULT 'phone/fax'::character varying NOT NULL,
    tag character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_people_users_comm_common);


ALTER TABLE darwin2.people_comm OWNER TO darwin2;

--
-- TOC entry 5639 (class 0 OID 0)
-- Dependencies: 217
-- Name: TABLE people_comm; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE people_comm IS 'People phones and e-mails';


--
-- TOC entry 5640 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN people_comm.person_user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_comm.person_user_ref IS 'Reference of person - id field of people table';


--
-- TOC entry 5641 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN people_comm.entry; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_comm.entry IS 'Communication entry';


--
-- TOC entry 5642 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN people_comm.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_comm.id IS 'Unique identifier of a person communication mean entry';


--
-- TOC entry 5643 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN people_comm.comm_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_comm.comm_type IS 'Type of communication table concerned: address, phone or e-mail';


--
-- TOC entry 5644 (class 0 OID 0)
-- Dependencies: 217
-- Name: COLUMN people_comm.tag; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_comm.tag IS 'List of descriptive tags separated by , : internet, tel, fax, pager, public, private,...';


--
-- TOC entry 216 (class 1259 OID 16939)
-- Name: people_comm_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE people_comm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.people_comm_id_seq OWNER TO darwin2;

--
-- TOC entry 5646 (class 0 OID 0)
-- Dependencies: 216
-- Name: people_comm_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE people_comm_id_seq OWNED BY people_comm.id;


--
-- TOC entry 179 (class 1259 OID 16599)
-- Name: people_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.people_id_seq OWNER TO darwin2;

--
-- TOC entry 5647 (class 0 OID 0)
-- Dependencies: 179
-- Name: people_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE people_id_seq OWNED BY people.id;


--
-- TOC entry 209 (class 1259 OID 16864)
-- Name: people_languages; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people_languages (
    id integer NOT NULL,
    language_country character varying DEFAULT 'en'::character varying NOT NULL,
    mother boolean DEFAULT true NOT NULL,
    preferred_language boolean DEFAULT false NOT NULL,
    people_ref integer NOT NULL
);


ALTER TABLE darwin2.people_languages OWNER TO darwin2;

--
-- TOC entry 5648 (class 0 OID 0)
-- Dependencies: 209
-- Name: TABLE people_languages; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE people_languages IS 'Languages spoken by a given person';


--
-- TOC entry 5649 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN people_languages.language_country; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_languages.language_country IS 'Reference of Language - language_country field of languages_countries table';


--
-- TOC entry 5650 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN people_languages.mother; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_languages.mother IS 'Flag telling if its mother language or not';


--
-- TOC entry 5651 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN people_languages.preferred_language; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_languages.preferred_language IS 'Flag telling which language is preferred in communications';


--
-- TOC entry 5652 (class 0 OID 0)
-- Dependencies: 209
-- Name: COLUMN people_languages.people_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_languages.people_ref IS 'Reference of person - id field of people table';


--
-- TOC entry 208 (class 1259 OID 16862)
-- Name: people_languages_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE people_languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.people_languages_id_seq OWNER TO darwin2;

--
-- TOC entry 5654 (class 0 OID 0)
-- Dependencies: 208
-- Name: people_languages_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE people_languages_id_seq OWNED BY people_languages.id;


--
-- TOC entry 215 (class 1259 OID 16915)
-- Name: people_relationships; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people_relationships (
    id integer NOT NULL,
    person_user_role character varying,
    relationship_type character varying DEFAULT 'belongs to'::character varying NOT NULL,
    person_1_ref integer NOT NULL,
    person_2_ref integer NOT NULL,
    path character varying,
    activity_date_from_mask integer DEFAULT 0 NOT NULL,
    activity_date_from date DEFAULT '0001-01-01'::date NOT NULL,
    activity_date_to_mask integer DEFAULT 0 NOT NULL,
    activity_date_to date DEFAULT '2038-12-31'::date NOT NULL
);


ALTER TABLE darwin2.people_relationships OWNER TO darwin2;

--
-- TOC entry 5655 (class 0 OID 0)
-- Dependencies: 215
-- Name: TABLE people_relationships; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE people_relationships IS 'Relationships between people - mainly between physical person and moral person: relationship of dependancy';


--
-- TOC entry 5656 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.person_user_role; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.person_user_role IS 'Person role in the organization referenced';


--
-- TOC entry 5657 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.relationship_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.relationship_type IS 'Type of relationship between two persons: belongs to, is department of, is section of, works for,...';


--
-- TOC entry 5658 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.person_1_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.person_1_ref IS 'Reference of person to be puted in relationship with an other - id field of people table';


--
-- TOC entry 5659 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.person_2_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.person_2_ref IS 'Reference of person puted the person puted in relationship with is dependant of - id field of people table';


--
-- TOC entry 5660 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.path; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.path IS 'Hierarchical path of the organization structure';


--
-- TOC entry 5661 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.activity_date_from_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.activity_date_from_mask IS 'person activity period or person activity period in the organization referenced date from mask';


--
-- TOC entry 5662 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.activity_date_from; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.activity_date_from IS 'person activity period or person activity period in the organization referenced date from';


--
-- TOC entry 5663 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.activity_date_to_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.activity_date_to_mask IS 'person activity period or person activity period in the organization referenced date to mask';


--
-- TOC entry 5664 (class 0 OID 0)
-- Dependencies: 215
-- Name: COLUMN people_relationships.activity_date_to; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN people_relationships.activity_date_to IS 'person activity period or person activity period in the organization referenced date to';


--
-- TOC entry 214 (class 1259 OID 16913)
-- Name: people_relationships_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE people_relationships_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.people_relationships_id_seq OWNER TO darwin2;

--
-- TOC entry 5666 (class 0 OID 0)
-- Dependencies: 214
-- Name: people_relationships_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE people_relationships_id_seq OWNED BY people_relationships.id;


--
-- TOC entry 370 (class 1259 OID 715939)
-- Name: people_transpo; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE people_transpo (
    all_elements integer[],
    to_be_replaced integer[],
    replacement integer
);


ALTER TABLE darwin2.people_transpo OWNER TO darwin2;

--
-- TOC entry 188 (class 1259 OID 16680)
-- Name: possible_upper_levels; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE possible_upper_levels (
    level_ref integer NOT NULL,
    level_upper_ref integer
);


ALTER TABLE darwin2.possible_upper_levels OWNER TO darwin2;

--
-- TOC entry 5667 (class 0 OID 0)
-- Dependencies: 188
-- Name: TABLE possible_upper_levels; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE possible_upper_levels IS 'For each level, list all the availble parent levels';


--
-- TOC entry 5668 (class 0 OID 0)
-- Dependencies: 188
-- Name: COLUMN possible_upper_levels.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN possible_upper_levels.level_ref IS 'Reference of current level';


--
-- TOC entry 5669 (class 0 OID 0)
-- Dependencies: 188
-- Name: COLUMN possible_upper_levels.level_upper_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN possible_upper_levels.level_upper_ref IS 'Reference of authorized parent level';


--
-- TOC entry 275 (class 1259 OID 17635)
-- Name: preferences; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE preferences (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    pref_key character varying NOT NULL,
    pref_value character varying NOT NULL
);


ALTER TABLE darwin2.preferences OWNER TO darwin2;

--
-- TOC entry 5671 (class 0 OID 0)
-- Dependencies: 275
-- Name: TABLE preferences; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE preferences IS 'Table to handle users preferences';


--
-- TOC entry 5672 (class 0 OID 0)
-- Dependencies: 275
-- Name: COLUMN preferences.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN preferences.user_ref IS 'The referenced user id';


--
-- TOC entry 5673 (class 0 OID 0)
-- Dependencies: 275
-- Name: COLUMN preferences.pref_key; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN preferences.pref_key IS 'The classification key of the preference. eg: color';


--
-- TOC entry 5674 (class 0 OID 0)
-- Dependencies: 275
-- Name: COLUMN preferences.pref_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN preferences.pref_value IS 'The value of the preference for this user eg: red';


--
-- TOC entry 274 (class 1259 OID 17633)
-- Name: preferences_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.preferences_id_seq OWNER TO darwin2;

--
-- TOC entry 5676 (class 0 OID 0)
-- Dependencies: 274
-- Name: preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE preferences_id_seq OWNED BY preferences.id;


--
-- TOC entry 199 (class 1259 OID 16773)
-- Name: properties; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE properties (
    id integer NOT NULL,
    property_type character varying NOT NULL,
    applies_to character varying DEFAULT ''::character varying NOT NULL,
    applies_to_indexed character varying NOT NULL,
    date_from_mask integer DEFAULT 0 NOT NULL,
    date_from timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone NOT NULL,
    date_to_mask integer DEFAULT 0 NOT NULL,
    date_to timestamp without time zone DEFAULT '2038-12-31 00:00:00'::timestamp without time zone NOT NULL,
    is_quantitative boolean DEFAULT false NOT NULL,
    property_unit character varying DEFAULT ''::character varying NOT NULL,
    method character varying,
    method_indexed character varying NOT NULL,
    lower_value character varying NOT NULL,
    lower_value_unified double precision,
    upper_value character varying NOT NULL,
    upper_value_unified double precision,
    property_accuracy character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.properties OWNER TO darwin2;

--
-- TOC entry 5678 (class 0 OID 0)
-- Dependencies: 199
-- Name: TABLE properties; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE properties IS 'All properties or all measurements describing an object in darwin are stored in this table';


--
-- TOC entry 5679 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.referenced_relation IS 'Identifier-Name of the table a property is defined for';


--
-- TOC entry 5680 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.record_id IS 'Identifier of record a property is defined for';


--
-- TOC entry 5681 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.property_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.property_type IS 'Type-Category of property - Latitude, Longitude, Ph, Height, Weight, Color, Temperature, Wind direction,...';


--
-- TOC entry 5682 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.applies_to; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.applies_to IS 'Depending on the use of the type, this can further specify the actual part measured. For example, a measurement of temperature may be a surface, air or sub-surface measurement.';


--
-- TOC entry 5683 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.applies_to_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.applies_to_indexed IS 'Indexed form of Sub type of property - if subtype is null, takes a generic replacement value';


--
-- TOC entry 5684 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.date_from_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.date_from_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5685 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.date_from; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.date_from IS 'For a range of measurements, give the measurement start - if null, takes a generic replacement value';


--
-- TOC entry 5686 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.date_to_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.date_to_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5687 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.date_to; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.date_to IS 'For a range of measurements, give the measurement stop date/time - if null, takes a generic replacement value';


--
-- TOC entry 5688 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.property_unit; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.property_unit IS 'Unit used for property value introduced';


--
-- TOC entry 5689 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.method; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.method IS 'Method used to collect property value';


--
-- TOC entry 5690 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.method_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.method_indexed IS 'Indexed version of property_method field - if null, takes a generic replacement value';


--
-- TOC entry 5691 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.lower_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.lower_value IS 'Lower value of Single Value';


--
-- TOC entry 5692 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.lower_value_unified; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.lower_value_unified IS 'unified version of the value for comparison with other units';


--
-- TOC entry 5693 (class 0 OID 0)
-- Dependencies: 199
-- Name: COLUMN properties.property_accuracy; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN properties.property_accuracy IS 'Accuracy of the values';


--
-- TOC entry 198 (class 1259 OID 16771)
-- Name: properties_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE properties_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.properties_id_seq OWNER TO darwin2;

--
-- TOC entry 5695 (class 0 OID 0)
-- Dependencies: 198
-- Name: properties_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE properties_id_seq OWNED BY properties.id;


--
-- TOC entry 273 (class 1259 OID 17615)
-- Name: specimen_collecting_methods; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE specimen_collecting_methods (
    id integer NOT NULL,
    specimen_ref integer NOT NULL,
    collecting_method_ref integer NOT NULL
);


ALTER TABLE darwin2.specimen_collecting_methods OWNER TO darwin2;

--
-- TOC entry 5697 (class 0 OID 0)
-- Dependencies: 273
-- Name: TABLE specimen_collecting_methods; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE specimen_collecting_methods IS 'Association of collecting methods with specimens';


--
-- TOC entry 5698 (class 0 OID 0)
-- Dependencies: 273
-- Name: COLUMN specimen_collecting_methods.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_methods.id IS 'Unique identifier of an association';


--
-- TOC entry 5699 (class 0 OID 0)
-- Dependencies: 273
-- Name: COLUMN specimen_collecting_methods.specimen_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_methods.specimen_ref IS 'Identifier of a specimen - comes from specimens table (id field)';


--
-- TOC entry 5700 (class 0 OID 0)
-- Dependencies: 273
-- Name: COLUMN specimen_collecting_methods.collecting_method_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_methods.collecting_method_ref IS 'Identifier of a collecting method - comes from collecting_methods table (id field)';


--
-- TOC entry 272 (class 1259 OID 17613)
-- Name: specimen_collecting_methods_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE specimen_collecting_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.specimen_collecting_methods_id_seq OWNER TO darwin2;

--
-- TOC entry 5702 (class 0 OID 0)
-- Dependencies: 272
-- Name: specimen_collecting_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE specimen_collecting_methods_id_seq OWNED BY specimen_collecting_methods.id;


--
-- TOC entry 269 (class 1259 OID 17581)
-- Name: specimen_collecting_tools; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE specimen_collecting_tools (
    id integer NOT NULL,
    specimen_ref integer NOT NULL,
    collecting_tool_ref integer NOT NULL
);


ALTER TABLE darwin2.specimen_collecting_tools OWNER TO darwin2;

--
-- TOC entry 5703 (class 0 OID 0)
-- Dependencies: 269
-- Name: TABLE specimen_collecting_tools; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE specimen_collecting_tools IS 'Association of collecting tools with specimens';


--
-- TOC entry 5704 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN specimen_collecting_tools.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_tools.id IS 'Unique identifier of an association';


--
-- TOC entry 5705 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN specimen_collecting_tools.specimen_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_tools.specimen_ref IS 'Identifier of a specimen - comes from specimens table (id field)';


--
-- TOC entry 5706 (class 0 OID 0)
-- Dependencies: 269
-- Name: COLUMN specimen_collecting_tools.collecting_tool_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimen_collecting_tools.collecting_tool_ref IS 'Identifier of a collecting tool - comes from collecting_tools table (id field)';


--
-- TOC entry 268 (class 1259 OID 17579)
-- Name: specimen_collecting_tools_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE specimen_collecting_tools_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.specimen_collecting_tools_id_seq OWNER TO darwin2;

--
-- TOC entry 5708 (class 0 OID 0)
-- Dependencies: 268
-- Name: specimen_collecting_tools_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE specimen_collecting_tools_id_seq OWNED BY specimen_collecting_tools.id;


--
-- TOC entry 259 (class 1259 OID 17392)
-- Name: specimens; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE specimens (
    id integer NOT NULL,
    collection_ref integer NOT NULL,
    expedition_ref integer,
    gtu_ref integer,
    taxon_ref integer,
    litho_ref integer,
    chrono_ref integer,
    lithology_ref integer,
    mineral_ref integer,
    acquisition_category character varying DEFAULT ''::character varying NOT NULL,
    acquisition_date_mask integer DEFAULT 0 NOT NULL,
    acquisition_date date DEFAULT '0001-01-01'::date NOT NULL,
    station_visible boolean DEFAULT true NOT NULL,
    ig_ref integer,
    type character varying DEFAULT 'specimen'::character varying NOT NULL,
    type_group character varying DEFAULT 'specimen'::character varying NOT NULL,
    type_search character varying DEFAULT 'specimen'::character varying NOT NULL,
    sex character varying DEFAULT 'undefined'::character varying NOT NULL,
    stage character varying DEFAULT 'undefined'::character varying NOT NULL,
    state character varying DEFAULT 'not applicable'::character varying NOT NULL,
    social_status character varying DEFAULT 'not applicable'::character varying NOT NULL,
    rock_form character varying DEFAULT 'not applicable'::character varying NOT NULL,
    room character varying,
    shelf character varying,
    specimen_count_min integer DEFAULT 1 NOT NULL,
    specimen_count_max integer DEFAULT 1 NOT NULL,
    spec_ident_ids integer[] DEFAULT '{}'::integer[] NOT NULL,
    spec_coll_ids integer[] DEFAULT '{}'::integer[] NOT NULL,
    spec_don_sel_ids integer[] DEFAULT '{}'::integer[] NOT NULL,
    collection_type character varying,
    collection_code character varying,
    collection_name character varying,
    collection_is_public boolean,
    collection_parent_ref integer,
    collection_path character varying,
    expedition_name character varying,
    expedition_name_indexed character varying,
    gtu_code character varying,
    gtu_from_date_mask integer DEFAULT 0,
    gtu_from_date timestamp without time zone DEFAULT '0001-01-01 00:00:00'::timestamp without time zone,
    gtu_to_date_mask integer DEFAULT 0,
    gtu_to_date timestamp without time zone DEFAULT '2038-12-31 00:00:00'::timestamp without time zone,
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
    category character varying DEFAULT 'physical'::character varying,
    institution_ref integer,
    building character varying,
    floor character varying,
    "row" character varying,
    col character varying,
    container character varying,
    sub_container character varying,
    container_type character varying DEFAULT 'container'::character varying,
    sub_container_type character varying DEFAULT 'container'::character varying,
    container_storage character varying DEFAULT 'dry'::character varying,
    sub_container_storage character varying DEFAULT 'dry'::character varying,
    surnumerary boolean DEFAULT false,
    object_name text,
    object_name_indexed text DEFAULT ''::text,
    specimen_status character varying DEFAULT 'good state'::character varying,
    valid_label boolean,
    label_created_on character varying,
    label_created_by character varying,
    CONSTRAINT chk_chk_specimen_part_min CHECK ((specimen_count_min >= 0)),
    CONSTRAINT chk_chk_specimen_parts_minmax CHECK ((specimen_count_min <= specimen_count_max))
);


ALTER TABLE darwin2.specimens OWNER TO darwin2;

--
-- TOC entry 5709 (class 0 OID 0)
-- Dependencies: 259
-- Name: TABLE specimens; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE specimens IS 'Specimens or batch of specimens stored in collection';


--
-- TOC entry 5710 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.id IS 'Unique identifier of a specimen or batch of specimens';


--
-- TOC entry 5711 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.collection_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.collection_ref IS 'Reference of collection the specimen is grouped under - id field of collections table';


--
-- TOC entry 5712 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.expedition_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.expedition_ref IS 'When acquisition category is expedition, contains the reference of the expedition having conducted to the current specimen capture - id field of expeditions table';


--
-- TOC entry 5713 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.gtu_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.gtu_ref IS 'Reference of the sampling location the specimen is coming from - id field of gtu table';


--
-- TOC entry 5714 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.taxon_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.taxon_ref IS 'When encoding a ''living'' specimen, contains the reference of the taxon unit defining the specimen - id field of taxonomy table';


--
-- TOC entry 5715 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.litho_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.litho_ref IS 'When encoding a rock, mineral or paleontologic specimen, contains the reference of lithostratigraphic unit the specimen have been found into - id field of lithostratigraphy table';


--
-- TOC entry 5716 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.chrono_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.chrono_ref IS 'When encoding a rock, mineral or paleontologic specimen, contains the reference of chronostratigraphic unit the specimen have been found into - id field of chronostratigraphy table';


--
-- TOC entry 5717 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.lithology_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.lithology_ref IS 'Reference of a rock classification unit associated to the specimen encoded - id field of lithology table';


--
-- TOC entry 5718 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.mineral_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.mineral_ref IS 'Reference of a mineral classification unit associated to the specimen encoded - id field of mineralogy table';


--
-- TOC entry 5719 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.acquisition_category; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.acquisition_category IS 'Describe how the specimen was collected: expedition, donation,...';


--
-- TOC entry 5720 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.acquisition_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.acquisition_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5721 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.acquisition_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.acquisition_date IS 'Date Composed (if possible) of the acquisition';


--
-- TOC entry 5722 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.station_visible; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.station_visible IS 'Flag telling if the sampling location can be visible or must be hidden for the specimen encoded';


--
-- TOC entry 5723 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.ig_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.ig_ref IS 'Reference of ig number this specimen has been associated to';


--
-- TOC entry 5724 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.type IS 'Special status given to specimen: holotype, paratype,...';


--
-- TOC entry 5725 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.type_group; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.type_group IS 'For some special status, a common appelation is used - ie: topotype and cotype are joined into a common appelation of syntype';


--
-- TOC entry 5726 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.type_search; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.type_search IS 'On the interface, the separation in all special status is not suggested for non official appelations. For instance, an unified grouping name is provided: type for non official appelation,...';


--
-- TOC entry 5727 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.sex; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.sex IS 'sex: male , female,...';


--
-- TOC entry 5728 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.stage; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.stage IS 'stage: adult, juvenile,...';


--
-- TOC entry 5729 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.state; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.state IS 'state - a sex complement: ovigerous, pregnant,...';


--
-- TOC entry 5730 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.social_status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.social_status IS 'For social specimens, give the social status/role of the specimen in colony';


--
-- TOC entry 5731 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.rock_form; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.rock_form IS 'For rock specimens, a descriptive form can be given: polygonous,...';


--
-- TOC entry 5732 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.room; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.room IS 'Room the specimen is stored in';


--
-- TOC entry 5733 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.shelf; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.shelf IS 'Shelf the specimen is stored in';


--
-- TOC entry 5734 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.specimen_count_min; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.specimen_count_min IS 'Minimum number of specimens';


--
-- TOC entry 5735 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.specimen_count_max; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.specimen_count_max IS 'Maximum number of specimens';


--
-- TOC entry 5736 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.gtu_from_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.gtu_from_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5737 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.gtu_from_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.gtu_from_date IS 'composed from date of the GTU';


--
-- TOC entry 5738 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.gtu_to_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.gtu_to_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';


--
-- TOC entry 5739 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.gtu_to_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.gtu_to_date IS 'composed to date of the GTU';


--
-- TOC entry 5740 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.category; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.category IS 'Type of specimen encoded: a physical object stored in collections, an observation, a figurate specimen,...';


--
-- TOC entry 5741 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.building; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.building IS 'Building the specimen is stored in';


--
-- TOC entry 5742 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.floor; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.floor IS 'Floor the specimen is stored in';


--
-- TOC entry 5743 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens."row"; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens."row" IS 'Row the specimen is stored in';


--
-- TOC entry 5744 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.container; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.container IS 'Container the specimen is stored in';


--
-- TOC entry 5745 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.sub_container; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.sub_container IS 'Sub-Container the specimen is stored in';


--
-- TOC entry 5746 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.container_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.container_type IS 'Type of container: box, plateau-caisse,...';


--
-- TOC entry 5747 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.sub_container_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.sub_container_type IS 'Type of sub-container: slide, needle,...';


--
-- TOC entry 5748 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.container_storage; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.container_storage IS 'Conservative medium used: formol, alcohool, dry,...';


--
-- TOC entry 5749 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.sub_container_storage; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.sub_container_storage IS 'Conservative medium used: formol, alcohool, dry,...';


--
-- TOC entry 5750 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.surnumerary; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.surnumerary IS 'Tells if this specimen has been added after first inventory';


--
-- TOC entry 5751 (class 0 OID 0)
-- Dependencies: 259
-- Name: COLUMN specimens.specimen_status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens.specimen_status IS 'Specimen status: good state, lost, damaged,...';


--
-- TOC entry 363 (class 1259 OID 715092)
-- Name: specimens_detect_wrong_countries; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE specimens_detect_wrong_countries (
    id integer NOT NULL,
    gtu_country_tag_indexed character varying[],
    geom public.geometry
);


ALTER TABLE darwin2.specimens_detect_wrong_countries OWNER TO darwin2;

--
-- TOC entry 258 (class 1259 OID 17390)
-- Name: specimens_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE specimens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.specimens_id_seq OWNER TO darwin2;

--
-- TOC entry 5753 (class 0 OID 0)
-- Dependencies: 258
-- Name: specimens_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE specimens_id_seq OWNED BY specimens.id;


--
-- TOC entry 265 (class 1259 OID 17528)
-- Name: specimens_relationships; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE specimens_relationships (
    id integer NOT NULL,
    specimen_ref integer NOT NULL,
    relationship_type character varying DEFAULT 'host'::character varying NOT NULL,
    unit_type character varying DEFAULT 'specimens'::character varying NOT NULL,
    specimen_related_ref integer,
    taxon_ref integer,
    mineral_ref integer,
    institution_ref integer,
    source_name text,
    source_id text,
    quantity numeric(16,2),
    unit character varying DEFAULT '%'::character varying
);


ALTER TABLE darwin2.specimens_relationships OWNER TO darwin2;

--
-- TOC entry 5754 (class 0 OID 0)
-- Dependencies: 265
-- Name: TABLE specimens_relationships; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE specimens_relationships IS 'List all the objects/specimens related the current specimen';


--
-- TOC entry 5755 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.specimen_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.specimen_ref IS 'Reference of specimen concerned - id field of specimens table';


--
-- TOC entry 5756 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.relationship_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.relationship_type IS 'Type of relationship: host, part of, related to, ...';


--
-- TOC entry 5757 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.unit_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.unit_type IS 'Type of the related unit : spec, taxo or mineralo';


--
-- TOC entry 5758 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.taxon_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.taxon_ref IS 'Reference of the related specimen';


--
-- TOC entry 5759 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.mineral_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.mineral_ref IS 'Reference of related mineral';


--
-- TOC entry 5760 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.institution_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.institution_ref IS 'External Specimen related institution';


--
-- TOC entry 5761 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.source_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.source_name IS 'External Specimen related  source DB';


--
-- TOC entry 5762 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.source_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.source_id IS 'External Specimen related id in the source';


--
-- TOC entry 5763 (class 0 OID 0)
-- Dependencies: 265
-- Name: COLUMN specimens_relationships.quantity; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN specimens_relationships.quantity IS 'Quantity of accompanying mineral';


--
-- TOC entry 264 (class 1259 OID 17526)
-- Name: specimens_relationships_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE specimens_relationships_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.specimens_relationships_id_seq OWNER TO darwin2;

--
-- TOC entry 5765 (class 0 OID 0)
-- Dependencies: 264
-- Name: specimens_relationships_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE specimens_relationships_id_seq OWNED BY specimens_relationships.id;


--
-- TOC entry 345 (class 1259 OID 250515)
-- Name: storage_parts; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE storage_parts (
    id integer NOT NULL,
    category character varying DEFAULT 'physical'::character varying NOT NULL,
    specimen_ref integer NOT NULL,
    specimen_part character varying DEFAULT 'specimen'::character varying NOT NULL,
    institution_ref integer,
    building character varying,
    floor character varying,
    room character varying,
    "row" character varying,
    col character varying,
    shelf character varying,
    container character varying,
    sub_container character varying,
    container_type character varying DEFAULT 'container'::character varying NOT NULL,
    sub_container_type character varying DEFAULT 'container'::character varying NOT NULL,
    container_storage character varying DEFAULT 'dry'::character varying NOT NULL,
    sub_container_storage character varying DEFAULT 'dry'::character varying NOT NULL,
    surnumerary boolean DEFAULT false NOT NULL,
    object_name text,
    object_name_indexed text DEFAULT ''::text NOT NULL,
    specimen_status character varying DEFAULT 'good state'::character varying NOT NULL,
    complete boolean DEFAULT true NOT NULL
);


ALTER TABLE darwin2.storage_parts OWNER TO darwin2;

--
-- TOC entry 5767 (class 0 OID 0)
-- Dependencies: 345
-- Name: COLUMN storage_parts.specimen_status; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN storage_parts.specimen_status IS 'Specimen status: good state, lost, damaged,...';


--
-- TOC entry 5768 (class 0 OID 0)
-- Dependencies: 345
-- Name: COLUMN storage_parts.complete; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN storage_parts.complete IS 'Flag telling if specimen is complete or not';


--
-- TOC entry 346 (class 1259 OID 251005)
-- Name: taxonomy_synonymy_status; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW taxonomy_synonymy_status AS
SELECT a.group_id, a.specimen_ids, a.status, a.count_by_status, (SELECT count(*) AS count FROM classification_synonymies WHERE (a.group_id = classification_synonymies.group_id)) AS count_all FROM (SELECT a.group_id, array_agg(a.record_id) AS specimen_ids, taxonomy.status, count(a.id) AS count_by_status FROM (classification_synonymies a JOIN taxonomy ON ((a.record_id = taxonomy.id))) GROUP BY a.group_id, taxonomy.status) a GROUP BY a.group_id, a.status, a.count_by_status, a.specimen_ids ORDER BY a.group_id;


ALTER TABLE darwin2.taxonomy_synonymy_status OWNER TO darwin2;

--
-- TOC entry 360 (class 1259 OID 714818)
-- Name: specimens_storage_parts_view; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW specimens_storage_parts_view AS
SELECT specimens.id, specimens.collection_ref, specimens.expedition_ref, specimens.gtu_ref, specimens.taxon_ref, specimens.litho_ref, specimens.chrono_ref, specimens.lithology_ref, specimens.mineral_ref, specimens.acquisition_category, specimens.acquisition_date_mask, specimens.acquisition_date, specimens.station_visible, specimens.ig_ref, specimens.type, specimens.type_group, specimens.type_search, specimens.sex, specimens.stage, specimens.state, specimens.social_status, specimens.rock_form, specimens.specimen_count_min, specimens.specimen_count_max, specimens.spec_ident_ids, specimens.spec_coll_ids, specimens.spec_don_sel_ids, specimens.collection_type, specimens.collection_code, specimens.collection_name, specimens.collection_is_public, specimens.collection_parent_ref, specimens.collection_path, specimens.expedition_name, specimens.expedition_name_indexed, specimens.gtu_code, specimens.gtu_from_date_mask, specimens.gtu_from_date, specimens.gtu_to_date_mask, specimens.gtu_to_date, specimens.gtu_tag_values_indexed, specimens.gtu_country_tag_value, specimens.gtu_country_tag_indexed, specimens.gtu_province_tag_value, specimens.gtu_province_tag_indexed, specimens.gtu_others_tag_value, specimens.gtu_others_tag_indexed, specimens.gtu_elevation, specimens.gtu_elevation_accuracy, specimens.gtu_location, specimens.taxon_name, specimens.taxon_name_indexed, specimens.taxon_level_ref, specimens.taxon_level_name, specimens.taxon_status, specimens.taxon_path, specimens.taxon_parent_ref, specimens.taxon_extinct, specimens.litho_name, specimens.litho_name_indexed, specimens.litho_level_ref, specimens.litho_level_name, specimens.litho_status, specimens.litho_local, specimens.litho_color, specimens.litho_path, specimens.litho_parent_ref, specimens.chrono_name, specimens.chrono_name_indexed, specimens.chrono_level_ref, specimens.chrono_level_name, specimens.chrono_status, specimens.chrono_local, specimens.chrono_color, specimens.chrono_path, specimens.chrono_parent_ref, specimens.lithology_name, specimens.lithology_name_indexed, specimens.lithology_level_ref, specimens.lithology_level_name, specimens.lithology_status, specimens.lithology_local, specimens.lithology_color, specimens.lithology_path, specimens.lithology_parent_ref, specimens.mineral_name, specimens.mineral_name_indexed, specimens.mineral_level_ref, specimens.mineral_level_name, specimens.mineral_status, specimens.mineral_local, specimens.mineral_color, specimens.mineral_path, specimens.mineral_parent_ref, specimens.ig_num, specimens.ig_num_indexed, specimens.ig_date_mask, specimens.ig_date, specimens.specimen_count_males_min, specimens.specimen_count_males_max, specimens.specimen_count_females_min, specimens.specimen_count_females_max, specimens.specimen_count_juveniles_max, specimens.specimen_count_juveniles_min, specimens.main_code_indexed, p.category, p.specimen_ref, p.specimen_part, p.institution_ref, p.building, p.floor, p.room, p."row", p.col, p.shelf, p.container, p.sub_container, p.container_type, p.sub_container_type, p.container_storage, p.sub_container_storage, p.surnumerary, p.object_name, p.object_name_indexed, p.specimen_status, p.complete, taxonomy_synonymy_status.group_id AS synonymy_group_id, taxonomy_synonymy_status.status AS synonymy_status, taxonomy_synonymy_status.count_by_status AS count_by_synonymy_status, taxonomy_synonymy_status.count_all AS synonymy_count_all_in_group, specimens.valid_label, specimens.label_created_on, specimens.label_created_by FROM ((specimens LEFT JOIN storage_parts p ON ((specimens.id = p.specimen_ref))) LEFT JOIN (SELECT unnest(taxonomy_synonymy_status.specimen_ids) AS join_id, taxonomy_synonymy_status.group_id, taxonomy_synonymy_status.specimen_ids, taxonomy_synonymy_status.status, taxonomy_synonymy_status.count_by_status, taxonomy_synonymy_status.count_all FROM taxonomy_synonymy_status) taxonomy_synonymy_status ON ((specimens.taxon_ref = taxonomy_synonymy_status.join_id)));


ALTER TABLE darwin2.specimens_storage_parts_view OWNER TO darwin2;

--
-- TOC entry 326 (class 1259 OID 108327)
-- Name: staging_catalogue; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_catalogue (
    id integer NOT NULL,
    import_ref integer NOT NULL,
    name character varying NOT NULL,
    level_ref integer,
    parent_ref integer,
    catalogue_ref integer,
    parent_updated boolean DEFAULT false
);


ALTER TABLE darwin2.staging_catalogue OWNER TO darwin2;

--
-- TOC entry 5769 (class 0 OID 0)
-- Dependencies: 326
-- Name: TABLE staging_catalogue; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE staging_catalogue IS 'Stores the catalogues hierarchy to be imported';


--
-- TOC entry 5770 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.id IS 'Unique identifier of a to be imported catalogue unit entry';


--
-- TOC entry 5771 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.import_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.import_ref IS 'Reference of import concerned - from table imports';


--
-- TOC entry 5772 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.name IS 'Name of unit to be imported/checked';


--
-- TOC entry 5773 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.level_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.level_ref IS 'Level of unit to be imported/checked';


--
-- TOC entry 5774 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.parent_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.parent_ref IS 'ID of parent the unit is attached to. Right after the load of xml, it refers recursively to an entry in the same staging_catalogue table. During the import it is replaced by id of the parent from the concerned catalogue table.';


--
-- TOC entry 5775 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.catalogue_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.catalogue_ref IS 'ID of unit in concerned catalogue table - set during import process';


--
-- TOC entry 5776 (class 0 OID 0)
-- Dependencies: 326
-- Name: COLUMN staging_catalogue.parent_updated; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_catalogue.parent_updated IS 'During the catalogue import process, tells if the parent ref has already been updated with one catalogue entry or not';


--
-- TOC entry 325 (class 1259 OID 108325)
-- Name: staging_catalogue_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_catalogue_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_catalogue_id_seq OWNER TO darwin2;

--
-- TOC entry 5777 (class 0 OID 0)
-- Dependencies: 325
-- Name: staging_catalogue_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_catalogue_id_seq OWNED BY staging_catalogue.id;


--
-- TOC entry 287 (class 1259 OID 17783)
-- Name: staging_collecting_methods; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_collecting_methods (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    collecting_method_ref integer NOT NULL
);


ALTER TABLE darwin2.staging_collecting_methods OWNER TO darwin2;

--
-- TOC entry 5778 (class 0 OID 0)
-- Dependencies: 287
-- Name: TABLE staging_collecting_methods; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE staging_collecting_methods IS 'Association of collecting methods with Staging';


--
-- TOC entry 5779 (class 0 OID 0)
-- Dependencies: 287
-- Name: COLUMN staging_collecting_methods.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_collecting_methods.id IS 'Unique identifier of an association';


--
-- TOC entry 5780 (class 0 OID 0)
-- Dependencies: 287
-- Name: COLUMN staging_collecting_methods.staging_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_collecting_methods.staging_ref IS 'Identifier of a specimen - comes from staging table (id field)';


--
-- TOC entry 5781 (class 0 OID 0)
-- Dependencies: 287
-- Name: COLUMN staging_collecting_methods.collecting_method_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_collecting_methods.collecting_method_ref IS 'Identifier of a collecting method - comes from collecting_methods table (id field)';


--
-- TOC entry 286 (class 1259 OID 17781)
-- Name: staging_collecting_methods_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_collecting_methods_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_collecting_methods_id_seq OWNER TO darwin2;

--
-- TOC entry 5782 (class 0 OID 0)
-- Dependencies: 286
-- Name: staging_collecting_methods_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_collecting_methods_id_seq OWNED BY staging_collecting_methods.id;


--
-- TOC entry 280 (class 1259 OID 17688)
-- Name: staging_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_id_seq OWNER TO darwin2;

--
-- TOC entry 5783 (class 0 OID 0)
-- Dependencies: 280
-- Name: staging_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_id_seq OWNED BY staging.id;


--
-- TOC entry 283 (class 1259 OID 17734)
-- Name: staging_info; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_info (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    referenced_relation character varying NOT NULL
);


ALTER TABLE darwin2.staging_info OWNER TO darwin2;

--
-- TOC entry 5784 (class 0 OID 0)
-- Dependencies: 283
-- Name: TABLE staging_info; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE staging_info IS 'used to make association between catalogue informations and staging eg taxon properties';


--
-- TOC entry 5785 (class 0 OID 0)
-- Dependencies: 283
-- Name: COLUMN staging_info.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_info.id IS 'Unique identifier of a grouped tag';


--
-- TOC entry 5786 (class 0 OID 0)
-- Dependencies: 283
-- Name: COLUMN staging_info.staging_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_info.staging_ref IS 'Ref of a staging record';


--
-- TOC entry 5787 (class 0 OID 0)
-- Dependencies: 283
-- Name: COLUMN staging_info.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_info.referenced_relation IS 'catalogue where associating the info';


--
-- TOC entry 282 (class 1259 OID 17732)
-- Name: staging_info_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_info_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_info_id_seq OWNER TO darwin2;

--
-- TOC entry 5788 (class 0 OID 0)
-- Dependencies: 282
-- Name: staging_info_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_info_id_seq OWNED BY staging_info.id;


--
-- TOC entry 291 (class 1259 OID 17819)
-- Name: staging_people; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_people (
    id integer NOT NULL,
    people_type character varying DEFAULT 'author'::character varying NOT NULL,
    people_sub_type character varying DEFAULT ''::character varying NOT NULL,
    order_by integer DEFAULT 1 NOT NULL,
    people_ref integer,
    formated_name character varying
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.staging_people OWNER TO darwin2;

--
-- TOC entry 5789 (class 0 OID 0)
-- Dependencies: 291
-- Name: TABLE staging_people; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE staging_people IS 'List of people of staging units';


--
-- TOC entry 5790 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.referenced_relation IS 'Identifier-Name of table the units come from';


--
-- TOC entry 5791 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.record_id IS 'Identifier of record concerned in table concerned';


--
-- TOC entry 5792 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.id IS 'Unique identifier of record';


--
-- TOC entry 5793 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.people_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.people_type IS 'Type of "people" associated to the staging unit: authors, collectors, defined,  ...';


--
-- TOC entry 5794 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.people_sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.people_sub_type IS 'Type of "people" associated to the staging unit: Main author, corrector, taking the sense from,...';


--
-- TOC entry 5795 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.order_by; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.order_by IS 'Integer used to order the persons in a list';


--
-- TOC entry 5796 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.people_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.people_ref IS 'Reference of person concerned - id field of people table';


--
-- TOC entry 5797 (class 0 OID 0)
-- Dependencies: 291
-- Name: COLUMN staging_people.formated_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_people.formated_name IS 'full name of the people';


--
-- TOC entry 290 (class 1259 OID 17817)
-- Name: staging_people_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_people_id_seq OWNER TO darwin2;

--
-- TOC entry 5799 (class 0 OID 0)
-- Dependencies: 290
-- Name: staging_people_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_people_id_seq OWNED BY staging_people.id;


--
-- TOC entry 285 (class 1259 OID 17750)
-- Name: staging_relationship; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_relationship (
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
    unit character varying DEFAULT '%'::character varying,
    unit_type character varying DEFAULT 'specimens'::character varying NOT NULL
);


ALTER TABLE darwin2.staging_relationship OWNER TO darwin2;

--
-- TOC entry 5800 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.record_id IS 'id of the orignial record';


--
-- TOC entry 5801 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.referenced_relation IS 'where to find the record_id, referenced_relation is always staging but this field uis mandatory for addRelated php function';


--
-- TOC entry 5802 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.relationship_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.relationship_type IS 'relation type (eg. host, parent, part of)';


--
-- TOC entry 5803 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.staging_related_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.staging_related_ref IS 'the record id associated, this record id must be found in the same import file';


--
-- TOC entry 5804 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.taxon_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.taxon_ref IS 'Reference of the related specimen';


--
-- TOC entry 5805 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.mineral_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.mineral_ref IS 'Reference of related mineral';


--
-- TOC entry 5806 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.institution_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.institution_ref IS 'the institution id associated to this relationship';


--
-- TOC entry 5807 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.institution_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.institution_name IS 'the institution name associated to this relationship, used to add to darwin institution if it dont exist';


--
-- TOC entry 5808 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.source_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.source_name IS 'External Specimen related  source DB';


--
-- TOC entry 5809 (class 0 OID 0)
-- Dependencies: 285
-- Name: COLUMN staging_relationship.source_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_relationship.source_id IS 'External Specimen related id in the source';


--
-- TOC entry 284 (class 1259 OID 17748)
-- Name: staging_relationship_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_relationship_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_relationship_id_seq OWNER TO darwin2;

--
-- TOC entry 5810 (class 0 OID 0)
-- Dependencies: 284
-- Name: staging_relationship_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_relationship_id_seq OWNED BY staging_relationship.id;


--
-- TOC entry 289 (class 1259 OID 17803)
-- Name: staging_tag_groups; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE staging_tag_groups (
    id integer NOT NULL,
    staging_ref integer NOT NULL,
    group_name character varying NOT NULL,
    sub_group_name character varying NOT NULL,
    tag_value character varying NOT NULL
);


ALTER TABLE darwin2.staging_tag_groups OWNER TO darwin2;

--
-- TOC entry 5811 (class 0 OID 0)
-- Dependencies: 289
-- Name: TABLE staging_tag_groups; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE staging_tag_groups IS 'List of grouped tags for an imported row (copy of tag group)';


--
-- TOC entry 5812 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN staging_tag_groups.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_tag_groups.id IS 'Unique identifier of a grouped tag';


--
-- TOC entry 5813 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN staging_tag_groups.staging_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_tag_groups.staging_ref IS 'Ref of an imported line';


--
-- TOC entry 5814 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN staging_tag_groups.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_tag_groups.group_name IS 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';


--
-- TOC entry 5815 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN staging_tag_groups.sub_group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_tag_groups.sub_group_name IS 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';


--
-- TOC entry 5816 (class 0 OID 0)
-- Dependencies: 289
-- Name: COLUMN staging_tag_groups.tag_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN staging_tag_groups.tag_value IS 'Ensemble of Tags';


--
-- TOC entry 288 (class 1259 OID 17801)
-- Name: staging_tag_groups_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE staging_tag_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.staging_tag_groups_id_seq OWNER TO darwin2;

--
-- TOC entry 5818 (class 0 OID 0)
-- Dependencies: 288
-- Name: staging_tag_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE staging_tag_groups_id_seq OWNED BY staging_tag_groups.id;


--
-- TOC entry 344 (class 1259 OID 250513)
-- Name: storage_parts_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE storage_parts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.storage_parts_id_seq OWNER TO darwin2;

--
-- TOC entry 5819 (class 0 OID 0)
-- Dependencies: 344
-- Name: storage_parts_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE storage_parts_id_seq OWNED BY storage_parts.id;


--
-- TOC entry 353 (class 1259 OID 448478)
-- Name: t_abcd_mammalogie_correctif_date_acquision; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE t_abcd_mammalogie_correctif_date_acquision (
    specimenid character varying,
    acquisitionday text,
    acquisitionmonth text,
    acquisitionyear text
);


ALTER TABLE darwin2.t_abcd_mammalogie_correctif_date_acquision OWNER TO darwin2;

--
-- TOC entry 196 (class 1259 OID 16737)
-- Name: tag_groups; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE tag_groups (
    id integer NOT NULL,
    gtu_ref integer NOT NULL,
    group_name character varying NOT NULL,
    group_name_indexed character varying NOT NULL,
    sub_group_name character varying NOT NULL,
    sub_group_name_indexed character varying NOT NULL,
    international_name character varying DEFAULT ''::character varying NOT NULL,
    color character varying DEFAULT '#FFFFFF'::character varying NOT NULL,
    tag_value character varying NOT NULL
);


ALTER TABLE darwin2.tag_groups OWNER TO darwin2;

--
-- TOC entry 5820 (class 0 OID 0)
-- Dependencies: 196
-- Name: TABLE tag_groups; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE tag_groups IS 'List of grouped tags';


--
-- TOC entry 5821 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.id IS 'Unique identifier of a grouped tag';


--
-- TOC entry 5822 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.gtu_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.gtu_ref IS 'Reference to a Gtu';


--
-- TOC entry 5823 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.group_name IS 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';


--
-- TOC entry 5824 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.group_name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.group_name_indexed IS 'Indexed form of a group name';


--
-- TOC entry 5825 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.sub_group_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.sub_group_name IS 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';


--
-- TOC entry 5826 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.sub_group_name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.sub_group_name_indexed IS 'Indexed form of a sub-group name';


--
-- TOC entry 5827 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.international_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.international_name IS 'The international(english) name of the place / ocean / country';


--
-- TOC entry 5828 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.color; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.color IS 'Color associated to the group concerned';


--
-- TOC entry 5829 (class 0 OID 0)
-- Dependencies: 196
-- Name: COLUMN tag_groups.tag_value; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tag_groups.tag_value IS 'Ensemble of Tags';


--
-- TOC entry 195 (class 1259 OID 16735)
-- Name: tag_groups_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE tag_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.tag_groups_id_seq OWNER TO darwin2;

--
-- TOC entry 5831 (class 0 OID 0)
-- Dependencies: 195
-- Name: tag_groups_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE tag_groups_id_seq OWNED BY tag_groups.id;


--
-- TOC entry 197 (class 1259 OID 16755)
-- Name: tags; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE tags (
    gtu_ref integer NOT NULL,
    group_ref integer NOT NULL,
    group_type character varying NOT NULL,
    sub_group_type character varying NOT NULL,
    tag character varying NOT NULL,
    tag_indexed character varying NOT NULL
);


ALTER TABLE darwin2.tags OWNER TO darwin2;

--
-- TOC entry 5832 (class 0 OID 0)
-- Dependencies: 197
-- Name: TABLE tags; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE tags IS 'List of calculated tags for a groups. This is only for query purpose (filled by triggers)';


--
-- TOC entry 5833 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.gtu_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.gtu_ref IS 'Reference to a Gtu';


--
-- TOC entry 5834 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.group_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.group_ref IS 'Reference of the Group name under which the tag is grouped';


--
-- TOC entry 5835 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.group_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.group_type IS 'Indexed form of a group name';


--
-- TOC entry 5836 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.sub_group_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.sub_group_type IS 'Indexed form of a sub-group name';


--
-- TOC entry 5837 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.tag; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.tag IS 'The readable version of the tag';


--
-- TOC entry 5838 (class 0 OID 0)
-- Dependencies: 197
-- Name: COLUMN tags.tag_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN tags.tag_indexed IS 'The indexed version of the tag';


--
-- TOC entry 355 (class 1259 OID 694149)
-- Name: taxonomy_bck20170124; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE taxonomy_bck20170124 (
    name character varying,
    name_indexed character varying,
    level_ref integer,
    status character varying,
    local_naming boolean,
    color character varying,
    path character varying,
    parent_ref integer,
    id integer,
    extinct boolean
);


ALTER TABLE darwin2.taxonomy_bck20170124 OWNER TO darwin2;

--
-- TOC entry 246 (class 1259 OID 17243)
-- Name: taxonomy_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE taxonomy_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.taxonomy_id_seq OWNER TO darwin2;

--
-- TOC entry 5840 (class 0 OID 0)
-- Dependencies: 246
-- Name: taxonomy_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE taxonomy_id_seq OWNED BY taxonomy.id;


--
-- TOC entry 207 (class 1259 OID 16838)
-- Name: users; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    db_user_type smallint DEFAULT 1 NOT NULL,
    people_id integer,
    created_at timestamp without time zone DEFAULT now(),
    selected_lang character varying DEFAULT 'en'::character varying NOT NULL
)
INHERITS (template_people);


ALTER TABLE darwin2.users OWNER TO darwin2;

--
-- TOC entry 5841 (class 0 OID 0)
-- Dependencies: 207
-- Name: TABLE users; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE users IS 'List all application users';


--
-- TOC entry 5842 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.is_physical; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.is_physical IS 'Type of user: physical or moral - true is physical, false is moral';


--
-- TOC entry 5843 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.sub_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.sub_type IS 'Used for moral users: precise nature - public institution, asbl, sprl, sa,...';


--
-- TOC entry 5844 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.formated_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.formated_name IS 'Complete user formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';


--
-- TOC entry 5845 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.formated_name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.formated_name_indexed IS 'Indexed form of formated_name field';


--
-- TOC entry 5846 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.formated_name_unique; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.formated_name_unique IS 'Indexed form of formated_name field (for unique index use)';


--
-- TOC entry 5847 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.title; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.title IS 'Title of a physical user/person like Mr or Mrs or phd,...';


--
-- TOC entry 5848 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.family_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.family_name IS 'Family name for physical users and Organisation name for moral users';


--
-- TOC entry 5849 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.given_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.given_name IS 'User/user''s given name - usually first name';


--
-- TOC entry 5850 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.additional_names; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.additional_names IS 'Any additional names given to user';


--
-- TOC entry 5851 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.birth_date_mask; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.birth_date_mask IS 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';


--
-- TOC entry 5852 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.birth_date; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.birth_date IS 'Birth/Creation date composed';


--
-- TOC entry 5853 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.gender; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.gender IS 'For physical users give the gender: M or F';


--
-- TOC entry 5854 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.id IS 'Unique identifier of a user';


--
-- TOC entry 5855 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.db_user_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.db_user_type IS 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';


--
-- TOC entry 5856 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.people_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.people_id IS 'Reference to a people if this user is also known as a people';


--
-- TOC entry 5857 (class 0 OID 0)
-- Dependencies: 207
-- Name: COLUMN users.selected_lang; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users.selected_lang IS 'Lang of the interface for the user en,fr,nl ,....';


--
-- TOC entry 223 (class 1259 OID 16994)
-- Name: users_addresses; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE users_addresses (
    id integer NOT NULL,
    person_user_role character varying,
    organization_unit character varying,
    tag character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_people_users_comm_common, template_people_users_addr_common);


ALTER TABLE darwin2.users_addresses OWNER TO darwin2;

--
-- TOC entry 5859 (class 0 OID 0)
-- Dependencies: 223
-- Name: TABLE users_addresses; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE users_addresses IS 'Users addresses';


--
-- TOC entry 5860 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.person_user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.person_user_ref IS 'Reference of the user concerned - id field of users table';


--
-- TOC entry 5861 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.entry; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.entry IS 'Street address';


--
-- TOC entry 5862 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.po_box; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.po_box IS 'PO Box';


--
-- TOC entry 5863 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.extended_address; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.extended_address IS 'Address extension: State, zip code suffix,...';


--
-- TOC entry 5864 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.locality; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.locality IS 'Locality';


--
-- TOC entry 5865 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.region; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.region IS 'Region';


--
-- TOC entry 5866 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.zip_code; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.zip_code IS 'Zip code';


--
-- TOC entry 5867 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.country; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.country IS 'Country';


--
-- TOC entry 5868 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.id IS 'Unique identifier of a user address';


--
-- TOC entry 5869 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.person_user_role; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.person_user_role IS 'User role in the organization referenced';


--
-- TOC entry 5870 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.organization_unit; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.organization_unit IS 'When a physical user is in relationship with a moral one, indicates the department or unit the user is related to';


--
-- TOC entry 5871 (class 0 OID 0)
-- Dependencies: 223
-- Name: COLUMN users_addresses.tag; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_addresses.tag IS 'List of descriptive tags: home, work,...';


--
-- TOC entry 222 (class 1259 OID 16992)
-- Name: users_addresses_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE users_addresses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_addresses_id_seq OWNER TO darwin2;

--
-- TOC entry 5873 (class 0 OID 0)
-- Dependencies: 222
-- Name: users_addresses_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE users_addresses_id_seq OWNED BY users_addresses.id;


--
-- TOC entry 221 (class 1259 OID 16976)
-- Name: users_comm; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE users_comm (
    id integer NOT NULL,
    comm_type character varying DEFAULT 'phone/fax'::character varying NOT NULL,
    tag character varying DEFAULT ''::character varying NOT NULL
)
INHERITS (template_people_users_comm_common);


ALTER TABLE darwin2.users_comm OWNER TO darwin2;

--
-- TOC entry 5875 (class 0 OID 0)
-- Dependencies: 221
-- Name: TABLE users_comm; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE users_comm IS 'Users phones and e-mails';


--
-- TOC entry 5876 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN users_comm.person_user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_comm.person_user_ref IS 'Reference of user - id field of user table';


--
-- TOC entry 5877 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN users_comm.entry; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_comm.entry IS 'Communication entry';


--
-- TOC entry 5878 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN users_comm.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_comm.id IS 'Unique identifier of a users communication mean entry';


--
-- TOC entry 5879 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN users_comm.comm_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_comm.comm_type IS 'Type of communication table concerned: address, phone or e-mail';


--
-- TOC entry 5880 (class 0 OID 0)
-- Dependencies: 221
-- Name: COLUMN users_comm.tag; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_comm.tag IS 'List of descriptive tags: internet, tel, fax, pager, public, private,...';


--
-- TOC entry 220 (class 1259 OID 16974)
-- Name: users_comm_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE users_comm_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_comm_id_seq OWNER TO darwin2;

--
-- TOC entry 5882 (class 0 OID 0)
-- Dependencies: 220
-- Name: users_comm_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE users_comm_id_seq OWNED BY users_comm.id;


--
-- TOC entry 206 (class 1259 OID 16836)
-- Name: users_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_id_seq OWNER TO darwin2;

--
-- TOC entry 5884 (class 0 OID 0)
-- Dependencies: 206
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 225 (class 1259 OID 17011)
-- Name: users_login_infos; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE users_login_infos (
    id integer NOT NULL,
    user_ref integer NOT NULL,
    login_type character varying DEFAULT 'local'::character varying NOT NULL,
    user_name character varying,
    password character varying,
    login_system character varying,
    renew_hash character varying,
    last_seen timestamp without time zone
);


ALTER TABLE darwin2.users_login_infos OWNER TO darwin2;

--
-- TOC entry 5886 (class 0 OID 0)
-- Dependencies: 225
-- Name: TABLE users_login_infos; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE users_login_infos IS 'Contains the login/password informations of DaRWIN 2 users';


--
-- TOC entry 5887 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.user_ref IS 'Identifier of user - id field of users table';


--
-- TOC entry 5888 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.login_type; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.login_type IS 'Type of identification system';


--
-- TOC entry 5889 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.user_name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.user_name IS 'For some system (local, ldap, kerberos,...) provides the username (encrypted form)';


--
-- TOC entry 5890 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.password; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.password IS 'For some system (local, ldap, kerberos,...) provides the password (encrypted form)';


--
-- TOC entry 5891 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.login_system; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.login_system IS 'For some system (shibbolet, openID,...) provides the user id';


--
-- TOC entry 5892 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.renew_hash; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.renew_hash IS 'Hashed key defined when asking to renew a password';


--
-- TOC entry 5893 (class 0 OID 0)
-- Dependencies: 225
-- Name: COLUMN users_login_infos.last_seen; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_login_infos.last_seen IS 'Last time the user has logged in.';


--
-- TOC entry 224 (class 1259 OID 17009)
-- Name: users_login_infos_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE users_login_infos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_login_infos_id_seq OWNER TO darwin2;

--
-- TOC entry 5895 (class 0 OID 0)
-- Dependencies: 224
-- Name: users_login_infos_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE users_login_infos_id_seq OWNED BY users_login_infos.id;


--
-- TOC entry 233 (class 1259 OID 17114)
-- Name: users_tracking; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE users_tracking (
    id integer NOT NULL,
    referenced_relation character varying NOT NULL,
    record_id integer NOT NULL,
    user_ref integer NOT NULL,
    action character varying DEFAULT 'insert'::character varying NOT NULL,
    old_value public.hstore,
    new_value public.hstore,
    modification_date_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE darwin2.users_tracking OWNER TO darwin2;

--
-- TOC entry 5897 (class 0 OID 0)
-- Dependencies: 233
-- Name: TABLE users_tracking; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE users_tracking IS 'Tracking of users actions on tables';


--
-- TOC entry 5898 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.id IS 'Unique identifier of a table track entry';


--
-- TOC entry 5899 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.referenced_relation IS 'Reference-Name of table concerned';


--
-- TOC entry 5900 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.record_id IS 'ID of record concerned';


--
-- TOC entry 5901 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.user_ref; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.user_ref IS 'Reference of user having made an action - id field of users table';


--
-- TOC entry 5902 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.action; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.action IS 'Action done on table record: insert, update, delete';


--
-- TOC entry 5903 (class 0 OID 0)
-- Dependencies: 233
-- Name: COLUMN users_tracking.modification_date_time; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN users_tracking.modification_date_time IS 'Track date and time';


--
-- TOC entry 232 (class 1259 OID 17112)
-- Name: users_tracking_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE users_tracking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.users_tracking_id_seq OWNER TO darwin2;

--
-- TOC entry 5905 (class 0 OID 0)
-- Dependencies: 232
-- Name: users_tracking_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE users_tracking_id_seq OWNED BY users_tracking.id;


--
-- TOC entry 359 (class 1259 OID 713247)
-- Name: v_collection_statistics; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_collection_statistics AS
SELECT DISTINCT ((specimens.collection_path)::text || ((specimens.collection_ref)::character varying)::text) AS collection_path, 'type_count'::text AS counter_category, specimens.type AS items, count(specimens.id) AS count_items FROM specimens GROUP BY specimens.collection_ref, specimens.type, specimens.collection_path UNION SELECT DISTINCT ((specimens.collection_path)::text || ((specimens.collection_ref)::character varying)::text) AS collection_path, 'image_count'::text AS counter_category, ext_links.category AS items, count(ext_links.id) AS count_items FROM (ext_links JOIN specimens ON (((ext_links.record_id = specimens.id) AND ((ext_links.referenced_relation)::text = 'specimens'::text)))) GROUP BY specimens.collection_ref, ext_links.category, specimens.collection_path;


ALTER TABLE darwin2.v_collection_statistics OWNER TO darwin2;

--
-- TOC entry 311 (class 1259 OID 66555)
-- Name: v_rmca_get_lower_by_higher; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_get_lower_by_higher AS
SELECT parent.id AS parent_id, parent.name AS higher_name, parent.level_ref AS parent_level_ref, rank_parent.level_name AS parent_level_name, child.id AS child_id, child.name AS lower_name, child.level_ref AS child_level_ref, rank_child.level_name AS child_level_name, (child.level_ref - parent.level_ref) AS diff, child.path AS child_path FROM (((taxonomy parent JOIN taxonomy child ON ((((child.path)::text ~~ (('%/'::text || (parent.id)::text) || '/%'::text)) OR (parent.id = child.id)))) JOIN catalogue_levels rank_parent ON ((rank_parent.id = parent.level_ref))) JOIN catalogue_levels rank_child ON ((rank_child.id = child.level_ref))) ORDER BY child.path;


ALTER TABLE darwin2.v_rmca_get_lower_by_higher OWNER TO darwin2;

--
-- TOC entry 312 (class 1259 OID 66604)
-- Name: v_rmca_count_specimen_by_higher; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_count_specimen_by_higher AS
SELECT c.parent_id, c.parent_level_name, c.parent_level_ref, c.higher_name, c.child_id, c.child_level_name, c.child_level_ref, c.lower_name, a.collection_ref, count(a.id) AS count_all, (((c.child_path)::text || (c.child_id)::text) || '/'::text) AS full_path, (SELECT count(specimens.id) AS count FROM specimens WHERE (specimens.taxon_ref = c.child_id)) AS count_direct FROM ((specimens a JOIN taxonomy b ON ((a.taxon_ref = b.id))) JOIN v_rmca_get_lower_by_higher c ON (((((b.path)::text || (b.id)::text) || '/'::text) ~~ (((c.child_path)::text || (c.child_id)::text) || '/%'::text)))) GROUP BY c.parent_id, c.parent_level_name, c.child_level_name, c.higher_name, c.child_id, c.lower_name, a.collection_ref, c.diff, c.parent_level_ref, c.child_level_ref, c.child_path ORDER BY c.higher_name, c.diff, c.lower_name;


ALTER TABLE darwin2.v_rmca_count_specimen_by_higher OWNER TO darwin2;

--
-- TOC entry 314 (class 1259 OID 66684)
-- Name: v_count_by_families_genus_species; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_count_by_families_genus_species AS
(SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.lower_name AS family, NULL::character varying AS genus, NULL::text AS species_or_lower, v_rmca_count_specimen_by_higher.count_all AS count_all_family, v_rmca_count_specimen_by_higher.count_direct AS count_direct_family, NULL::bigint AS count_all_genus, NULL::bigint AS count_direct_genus, NULL::text AS count_all_species, NULL::text AS count_direct_species, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE ((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref = 34)) UNION SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.higher_name AS family, v_rmca_count_specimen_by_higher.lower_name AS genus, NULL::text AS species_or_lower, NULL::bigint AS count_all_family, NULL::bigint AS count_direct_family, v_rmca_count_specimen_by_higher.count_all AS count_all_genus, v_rmca_count_specimen_by_higher.count_direct AS count_direct_genus, NULL::text AS count_all_species, NULL::text AS count_direct_species, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE (((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref > 34)) AND (v_rmca_count_specimen_by_higher.child_level_ref <= 41))) UNION SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.higher_name AS family, NULL::character varying AS genus, v_rmca_count_specimen_by_higher.lower_name AS species_or_lower, NULL::bigint AS count_all_family, NULL::bigint AS count_direct_family, NULL::bigint AS count_all_genus, NULL::bigint AS count_direct_genus, (v_rmca_count_specimen_by_higher.count_all)::text AS count_all_species, (v_rmca_count_specimen_by_higher.count_direct)::text AS count_direct_species, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE ((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref > 41));


ALTER TABLE darwin2.v_count_by_families_genus_species OWNER TO darwin2;

--
-- TOC entry 316 (class 1259 OID 108107)
-- Name: v_count_by_families_genus_species_subspecies; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_count_by_families_genus_species_subspecies AS
((SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.lower_name AS family, NULL::character varying AS genus, NULL::text AS species_or_lower, v_rmca_count_specimen_by_higher.count_all AS count_all_family, v_rmca_count_specimen_by_higher.count_direct AS count_direct_family, NULL::bigint AS count_all_genus, NULL::bigint AS count_direct_genus, NULL::text AS count_all_species, NULL::text AS count_direct_species, NULL::text AS count_all_subspecies, NULL::text AS count_direct_subspecies, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE ((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref = 34)) UNION SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.higher_name AS family, v_rmca_count_specimen_by_higher.lower_name AS genus, NULL::text AS species_or_lower, NULL::bigint AS count_all_family, NULL::bigint AS count_direct_family, v_rmca_count_specimen_by_higher.count_all AS count_all_genus, v_rmca_count_specimen_by_higher.count_direct AS count_direct_genus, NULL::text AS count_all_species, NULL::text AS count_direct_species, NULL::text AS count_all_subspecies, NULL::text AS count_direct_subspecies, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE (((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref > 34)) AND (v_rmca_count_specimen_by_higher.child_level_ref <= 41))) UNION SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.higher_name AS family, v_rmca_count_specimen_by_higher.lower_name AS genus, NULL::text AS species_or_lower, NULL::bigint AS count_all_family, NULL::bigint AS count_direct_family, NULL::bigint AS count_all_genus, NULL::bigint AS count_direct_genus, (v_rmca_count_specimen_by_higher.count_all)::text AS count_all_species, (v_rmca_count_specimen_by_higher.count_direct)::text AS count_direct_species, NULL::text AS count_all_subspecies, NULL::text AS count_direct_subspecies, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE (((v_rmca_count_specimen_by_higher.parent_level_ref = 34) AND (v_rmca_count_specimen_by_higher.child_level_ref > 41)) AND (v_rmca_count_specimen_by_higher.child_level_ref <= 48))) UNION SELECT v_rmca_count_specimen_by_higher.child_level_name AS level, v_rmca_count_specimen_by_higher.higher_name AS family, NULL::character varying AS genus, v_rmca_count_specimen_by_higher.lower_name AS species_or_lower, NULL::bigint AS count_all_family, NULL::bigint AS count_direct_family, NULL::bigint AS count_all_genus, NULL::bigint AS count_direct_genus, NULL::text AS count_all_species, NULL::text AS count_direct_species, (v_rmca_count_specimen_by_higher.count_all)::text AS count_all_subspecies, (v_rmca_count_specimen_by_higher.count_direct)::text AS count_direct_subspecies, v_rmca_count_specimen_by_higher.full_path, fct_rmca_sort_taxon_path_alphabetically((v_rmca_count_specimen_by_higher.full_path)::character varying) AS full_path_alpha, v_rmca_count_specimen_by_higher.collection_ref FROM v_rmca_count_specimen_by_higher WHERE (v_rmca_count_specimen_by_higher.parent_level_ref > 48);


ALTER TABLE darwin2.v_count_by_families_genus_species_subspecies OWNER TO darwin2;

--
-- TOC entry 357 (class 1259 OID 713188)
-- Name: v_darwin_public; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_darwin_public AS
SELECT DISTINCT array_agg(DISTINCT specimens.id) AS ids, specimens.collection_name, specimens.code_display, array_agg(DISTINCT specimens.taxon_path) AS taxon_paths, array_agg(DISTINCT specimens.taxon_ref) AS taxon_ref, array_agg(DISTINCT specimens.taxon_name) AS taxon_name, array_agg(DISTINCT specimens.history) AS history_identification, specimens.gtu_country_tag_value, specimens.gtu_others_tag_value, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask) AS fct_mask_date, fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask) AS date_from_display, fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask) AS date_to_display, specimens.coll_type, string_agg(DISTINCT (specimens.urls_thumbnails)::text, '|'::text) AS urls_thumbnails, string_agg(DISTINCT (specimens.image_category_thumbnails)::text, '|'::text) AS image_category_thumbnails, string_agg(DISTINCT (specimens.contributor_thumbnails)::text, '|'::text) AS contributor_thumbnails, string_agg(DISTINCT (specimens.disclaimer_thumbnails)::text, '|'::text) AS disclaimer_thumbnails, string_agg(DISTINCT (specimens.license_thumbnails)::text, '|'::text) AS license_order_thumbnails, string_agg(DISTINCT ((specimens.display_order_thumbnails)::character varying)::text, '|'::text) AS display_order_thumbnails, string_agg(DISTINCT (specimens.urls_image_links)::text, '|'::text) AS urls_image_links, string_agg(DISTINCT (specimens.image_category_image_links)::text, '|'::text) AS image_category_image_links, string_agg(DISTINCT (specimens.contributor_image_links)::text, '|'::text) AS contributor_image_links, string_agg(DISTINCT (specimens.disclaimer_image_links)::text, '|'::text) AS disclaimer_image_links, string_agg(DISTINCT (specimens.license_image_links)::text, '|'::text) AS license_image_links, string_agg(DISTINCT ((specimens.display_order_image_links)::character varying)::text, '|'::text) AS display_order_image_links, string_agg(DISTINCT (specimens.urls_3d_snippets)::text, '|'::text) AS urls_3d_snippets, string_agg(DISTINCT (specimens.image_category_3d_snippets)::text, '|'::text) AS image_category_3d_snippets, string_agg(DISTINCT (specimens.contributor_3d_snippets)::text, '|'::text) AS contributor_3d_snippets, string_agg(DISTINCT (specimens.disclaimer_3d_snippets)::text, '|'::text) AS disclaimer_3d_snippets, string_agg(DISTINCT (specimens.license_3d_snippets)::text, '|'::text) AS license_3d_snippets, string_agg(DISTINCT ((specimens.display_order_3d_snippets)::character varying)::text, '|'::text) AS display_order_3d_snippets, specimens.longitude, specimens.latitude, count(*) OVER () AS full_count, specimens.collector_ids, (SELECT array_agg(people.formated_name) AS array_agg FROM people WHERE (people.id = ANY (specimens.collector_ids))) AS collectors, specimens.donator_ids, (SELECT array_agg(people.formated_name) AS array_agg FROM people WHERE (people.id = ANY (specimens.donator_ids))) AS donators, array_agg(DISTINCT (('\"'::text || specimens.tag_locality) || '\"'::text)) AS localities, specimens.modification_date_time FROM (SELECT specimens.id, (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text) AS code_display, codes.full_code_indexed, specimens.taxon_path, specimens.taxon_ref, specimens.collection_ref, specimens.collection_name, specimens.gtu_country_tag_indexed, specimens.gtu_country_tag_value, specimens.gtu_others_tag_indexed AS localities_indexed, specimens.gtu_others_tag_value, specimens.taxon_name, specimens.spec_coll_ids AS collector_ids, specimens.spec_don_sel_ids AS donator_ids, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.type AS coll_type, CASE WHEN (specimens.gtu_country_tag_indexed IS NOT NULL) THEN unnest(specimens.gtu_country_tag_indexed) ELSE NULL::character varying END AS country_unnest, ext_links_thumbnails.url AS urls_thumbnails, ext_links_thumbnails.category AS image_category_thumbnails, ext_links_thumbnails.contributor AS contributor_thumbnails, ext_links_thumbnails.disclaimer AS disclaimer_thumbnails, ext_links_thumbnails.license AS license_thumbnails, ext_links_thumbnails.display_order AS display_order_thumbnails, ext_links_image_links.url AS urls_image_links, ext_links_image_links.category AS image_category_image_links, ext_links_image_links.contributor AS contributor_image_links, ext_links_image_links.disclaimer AS disclaimer_image_links, ext_links_image_links.license AS license_image_links, ext_links_image_links.display_order AS display_order_image_links, ext_links_3d_snippets.url AS urls_3d_snippets, ext_links_3d_snippets.category AS image_category_3d_snippets, ext_links_3d_snippets.contributor AS contributor_3d_snippets, ext_links_3d_snippets.disclaimer AS disclaimer_3d_snippets, ext_links_3d_snippets.license AS license_3d_snippets, ext_links_3d_snippets.display_order AS display_order_3d_snippets, specimens.gtu_location[0] AS latitude, specimens.gtu_location[1] AS longitude, identifications.notion_date AS identification_date, identifications.notion_date_mask AS identification_date_mask, (COALESCE((fct_mask_date(identifications.notion_date, identifications.notion_date_mask) || ': '::text), ''::text) || (specimens.taxon_name)::text) AS history, specimens.gtu_ref, tags.group_type, tags.sub_group_type, tags.tag, (((((tags.group_type)::text || '-'::text) || (tags.sub_group_type)::text) || ':'::text) || (tags.tag)::text) AS tag_locality, users_tracking.modification_date_time FROM (((((((specimens LEFT JOIN codes ON (((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text)) AND (specimens.id = codes.record_id)))) LEFT JOIN ext_links ext_links_thumbnails ON ((((specimens.id = ext_links_thumbnails.record_id) AND ((ext_links_thumbnails.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_thumbnails.category)::text = 'thumbnail'::text)))) LEFT JOIN ext_links ext_links_image_links ON ((((specimens.id = ext_links_image_links.record_id) AND ((ext_links_image_links.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_image_links.category)::text = 'image_link'::text)))) LEFT JOIN ext_links ext_links_3d_snippets ON ((((specimens.id = ext_links_3d_snippets.record_id) AND ((ext_links_3d_snippets.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_3d_snippets.category)::text = 'html_3d_snippet'::text)))) LEFT JOIN identifications ON (((((identifications.referenced_relation)::text = 'specimens'::text) AND (specimens.id = identifications.record_id)) AND ((identifications.notion_concerned)::text = 'taxonomy'::text)))) LEFT JOIN tags ON ((specimens.gtu_ref = tags.gtu_ref))) LEFT JOIN (SELECT users_tracking.modification_date_time, users_tracking.record_id, users_tracking.referenced_relation FROM users_tracking ORDER BY users_tracking.id DESC LIMIT 1) users_tracking ON (((specimens.id = users_tracking.record_id) AND ((users_tracking.referenced_relation)::text = 'specimens'::text)))) ORDER BY tags.group_ref) specimens GROUP BY specimens.code_display, specimens.collection_name, specimens.gtu_country_tag_value, specimens.gtu_others_tag_value, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.coll_type, specimens.longitude, specimens.latitude, specimens.collector_ids, specimens.donator_ids, specimens.modification_date_time;


ALTER TABLE darwin2.v_darwin_public OWNER TO darwin2;

--
-- TOC entry 361 (class 1259 OID 714872)
-- Name: vmap0_world_boundaries; Type: TABLE; Schema: darwin2; Owner: postgres; Tablespace: 
--

CREATE TABLE vmap0_world_boundaries (
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
    the_geom public.geometry,
    CONSTRAINT enforce_srid_the_geom CHECK ((public.st_srid(the_geom) = 4326))
);


ALTER TABLE darwin2.vmap0_world_boundaries OWNER TO postgres;

--
-- TOC entry 364 (class 1259 OID 715103)
-- Name: v_diagnose_country_from_coord; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_diagnose_country_from_coord AS
SELECT specimens.id, specimens.gtu_location, vmap0_world_boundaries.na2_descri, specimens.gtu_country_tag_indexed FROM (specimens LEFT JOIN vmap0_world_boundaries ON (public.st_within(public.st_setsrid(public.st_makepoint(specimens.gtu_location[0], specimens.gtu_location[1]), 4326), public.st_setsrid(vmap0_world_boundaries.the_geom, 4326)))) WHERE ((specimens.gtu_location)::public.geometry IS NOT NULL);


ALTER TABLE darwin2.v_diagnose_country_from_coord OWNER TO darwin2;

--
-- TOC entry 365 (class 1259 OID 715115)
-- Name: vmap0_world_boundaries_enveloppe; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE vmap0_world_boundaries_enveloppe (
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
);


ALTER TABLE darwin2.vmap0_world_boundaries_enveloppe OWNER TO darwin2;

--
-- TOC entry 366 (class 1259 OID 715322)
-- Name: v_diagnose_fast_country_outlier_tmp; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_diagnose_fast_country_outlier_tmp AS
SELECT specimens.id, vmap0_world_boundaries_enveloppe.na2_descri, vmap0_world_boundaries_enveloppe.bounding_box, (specimens.gtu_country_tag_indexed)::character varying AS gtu_country_tag_indexed, specimens.collection_ref FROM (specimens LEFT JOIN vmap0_world_boundaries_enveloppe ON ((public.st_within(public.st_setsrid(public.st_makepoint(specimens.gtu_location[0], specimens.gtu_location[1]), 4326), vmap0_world_boundaries_enveloppe.bounding_box) OR (vmap0_world_boundaries_enveloppe.na2_descri IS NULL)))) WHERE (((((vmap0_world_boundaries_enveloppe.na2_descri)::text <> 'Fiji'::text) AND ((vmap0_world_boundaries_enveloppe.na2_descri)::text <> 'Kiribati'::text)) AND ((vmap0_world_boundaries_enveloppe.na2_descri)::text <> 'New Zealand'::text)) AND ((vmap0_world_boundaries_enveloppe.na2_descri)::text <> 'United States'::text));


ALTER TABLE darwin2.v_diagnose_fast_country_outlier_tmp OWNER TO darwin2;

--
-- TOC entry 368 (class 1259 OID 715357)
-- Name: v_diagnose_fast_country_outlier; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_diagnose_fast_country_outlier AS
SELECT specimens.id, a.na2_descri, a.bounding_box, (specimens.gtu_country_tag_indexed)::character varying AS gtu_country_tag_indexed, specimens.collection_ref FROM (specimens LEFT JOIN (SELECT v_diagnose_fast_country_outlier_tmp.id, v_diagnose_fast_country_outlier_tmp.na2_descri, v_diagnose_fast_country_outlier_tmp.bounding_box, v_diagnose_fast_country_outlier_tmp.gtu_country_tag_indexed, v_diagnose_fast_country_outlier_tmp.collection_ref FROM v_diagnose_fast_country_outlier_tmp) a ON ((specimens.id = a.id))) WHERE (specimens.gtu_location IS NOT NULL);


ALTER TABLE darwin2.v_diagnose_fast_country_outlier OWNER TO darwin2;

--
-- TOC entry 327 (class 1259 OID 231053)
-- Name: v_loan_details_for_pentaho; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE v_loan_details_for_pentaho (
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
);


ALTER TABLE darwin2.v_loan_details_for_pentaho OWNER TO darwin2;

--
-- TOC entry 328 (class 1259 OID 231058)
-- Name: v_loans_for_pentaho; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_loans_for_pentaho AS
SELECT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, (SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'Loan at your request'::text)) LIMIT 1) AS loan_at_your_request, (SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'in_exchange'::text)) LIMIT 1) AS in_exchange FROM loans a;


ALTER TABLE darwin2.v_loans_for_pentaho OWNER TO darwin2;

--
-- TOC entry 352 (class 1259 OID 252633)
-- Name: v_loans_pentaho_general; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_loans_pentaho_general AS
SELECT DISTINCT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'loan_at_your_request'::text)) LIMIT 1), 'no'::character varying) AS loan_at_your_request, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'in_exchange'::text)) LIMIT 1), 'no'::character varying) AS in_exchange, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'loan_for_identification_our_request'::text)) LIMIT 1), 'no'::character varying) AS loan_for_identification_our_request, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'return_of_material_sent_for_id'::text)) LIMIT 1), 'no'::character varying) AS return_of_material_sent_for_id, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'return_of_borrowed_material'::text)) LIMIT 1), 'no'::character varying) AS return_of_borrowed_material, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'gift'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'gift'::text)) LIMIT 1), 'no'::character varying) AS gift, COALESCE((SELECT CASE lower((properties.property_type)::text) WHEN 'sent_by_surface'::text THEN 'Sent by surface'::text WHEN 'sent_by_airmail'::text THEN 'Sent by airmail'::text ELSE NULL::text END AS "case" FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((lower((properties.property_type)::text) = 'sent_by_surface'::text) OR (lower((properties.property_type)::text) = 'sent_by_airmail'::text))) LIMIT 1), (''::character varying)::text) AS shipping_type, transporter.formated_name AS transporter, to_char(i.modification_date_time, 'YYYY-MM-DD'::text) AS registration_date, ((j.insurance_value || ' '::text) || (j.insurance_currency)::text) AS insurance, COALESCE((SELECT properties.lower_value FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'packages_count'::text)) LIMIT 1), '1'::character varying) AS packages_count, COALESCE((SELECT (((properties.lower_value)::text || ' '::text) || (properties.property_unit)::text) FROM properties WHERE ((((properties.referenced_relation)::text = 'loans'::text) AND (properties.record_id = a.id)) AND ((properties.property_type)::text = 'weight'::text)) LIMIT 1), ''::text) AS weight FROM (((((((((loans a LEFT JOIN collection_maintenance b ON ((((a.id = b.record_id) AND ((b.referenced_relation)::text = 'loans'::text)) AND (((b.action_observation)::text = 'sent_by_surface'::text) OR ((b.action_observation)::text = 'sent_by_airmail'::text))))) LEFT JOIN catalogue_people c ON ((((a.id = c.record_id) AND ((c.referenced_relation)::text = 'loans'::text)) AND ((c.people_type)::text = 'receiver'::text)))) LEFT JOIN people d ON ((d.id = c.people_ref))) LEFT JOIN catalogue_people e ON ((((a.id = e.record_id) AND ((e.referenced_relation)::text = 'loans'::text)) AND ((e.people_type)::text = 'sender'::text)))) LEFT JOIN people f ON ((f.id = e.people_ref))) LEFT JOIN catalogue_people g ON (((((a.id = g.record_id) AND ((g.referenced_relation)::text = 'loans'::text)) AND ((g.people_type)::text = 'receiver'::text)) AND ((((((g.people_sub_type)::integer)::bit(8) & B'01000000'::"bit"))::integer)::boolean = true)))) LEFT JOIN people transporter ON ((g.people_ref = transporter.id))) LEFT JOIN loan_status i ON (((a.id = i.loan_ref) AND ((i.status)::text = 'new'::text)))) LEFT JOIN insurances j ON (((a.id = j.record_id) AND ((j.referenced_relation)::text = 'loans'::text))));


ALTER TABLE darwin2.v_loans_pentaho_general OWNER TO darwin2;

--
-- TOC entry 354 (class 1259 OID 691290)
-- Name: v_loans_pentaho_receivers; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE v_loans_pentaho_receivers (
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
    country_institution text,
    sender_id integer,
    sender character varying
);


ALTER TABLE darwin2.v_loans_pentaho_receivers OWNER TO darwin2;

--
-- TOC entry 358 (class 1259 OID 713193)
-- Name: v_rdf_view; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rdf_view AS
SELECT DISTINCT specimens.code_display AS "SpecimenID", ('http://darwinweb.africamuseum.be/darwin/rdf/'::text || specimens.code_display) AS "RefUri", ('http://darwinweb.africamuseum.be/'::text || specimens.code_display) AS "ObjectUri", btrim(((specimens.code_display || ' '::text) || array_to_string(array_agg(DISTINCT specimens.taxon_name), ', '::text))) AS "Title", btrim(((specimens.code_display || ' '::text) || array_to_string(array_agg(DISTINCT specimens.taxon_name), ', '::text))) AS "TitleDescription", btrim(array_to_string((SELECT array_agg(people.formated_name) AS array_agg FROM people WHERE (people.id = ANY (specimens.collector_ids))), ', '::text)) AS collector, (NULLIF(fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask), 'xxxx-xx-xx'::text) || COALESCE(('-'::text || NULLIF(fct_mask_date(specimens.gtu_to_date, specimens.gtu_to_date_mask), 'xxxx-xx-xx'::text)))) AS "CollectionDate", ('http://darwinweb.africamuseum.be/'::text || specimens.code_display) AS "ObjectURI", specimens.modification_date_time AS modified, 'specimens'::text AS "BaseOfRecord", 'RMCA'::text AS "InstitutionCode", specimens.collection_name AS "CollectionName", specimens.code_display AS "CatalogNumber", getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], 'family'::character varying) AS "Family", getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], 'genus'::character varying) AS "Genus", getspecificparentforlevel('taxonomy'::character varying, (array_agg(DISTINCT specimens.taxon_path))[1], '"species"'::character varying) AS "SpecificEpithet", NULL::character varying AS "HigherGeography", specimens.gtu_country_tag_value AS "Country", btrim(replace(replace((specimens.gtu_others_tag_value)::text, (specimens.gtu_country_tag_value)::text, ''::text), ';'::text, ''::text)) AS "Locality", specimens.urls_thumbnails AS "Image" FROM (SELECT specimens.id, (((((COALESCE(codes.code_prefix, ''::character varying))::text || (COALESCE(codes.code_prefix_separator, ''::character varying))::text) || (COALESCE(codes.code, ''::character varying))::text) || (COALESCE(codes.code_suffix_separator, ''::character varying))::text) || (COALESCE(codes.code_suffix, ''::character varying))::text) AS code_display, codes.full_code_indexed, specimens.taxon_path, specimens.taxon_ref, specimens.collection_ref, specimens.collection_name, specimens.gtu_country_tag_indexed, specimens.gtu_country_tag_value, specimens.gtu_others_tag_indexed AS localities_indexed, specimens.gtu_others_tag_value, specimens.taxon_name, specimens.spec_coll_ids AS collector_ids, specimens.spec_don_sel_ids AS donator_ids, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.type AS coll_type, CASE WHEN (specimens.gtu_country_tag_indexed IS NOT NULL) THEN unnest(specimens.gtu_country_tag_indexed) ELSE NULL::character varying END AS country_unnest, ext_links_thumbnails.url AS urls_thumbnails, ext_links_thumbnails.category AS image_category_thumbnails, ext_links_thumbnails.contributor AS contributor_thumbnails, ext_links_thumbnails.disclaimer AS disclaimer_thumbnails, ext_links_thumbnails.license AS license_thumbnails, ext_links_thumbnails.display_order AS display_order_thumbnails, ext_links_image_links.url AS urls_image_links, ext_links_image_links.category AS image_category_image_links, ext_links_image_links.contributor AS contributor_image_links, ext_links_image_links.disclaimer AS disclaimer_image_links, ext_links_image_links.license AS license_image_links, ext_links_image_links.display_order AS display_order_image_links, ext_links_3d_snippets.url AS urls_3d_snippets, ext_links_3d_snippets.category AS image_category_3d_snippets, ext_links_3d_snippets.contributor AS contributor_3d_snippets, ext_links_3d_snippets.disclaimer AS disclaimer_3d_snippets, ext_links_3d_snippets.license AS license_3d_snippets, ext_links_3d_snippets.display_order AS display_order_3d_snippets, specimens.gtu_location[0] AS latitude, specimens.gtu_location[1] AS longitude, identifications.notion_date AS identification_date, identifications.notion_date_mask AS identification_date_mask, (COALESCE((fct_mask_date(identifications.notion_date, identifications.notion_date_mask) || ': '::text), ''::text) || (specimens.taxon_name)::text) AS history, specimens.gtu_ref, tags.group_type, tags.sub_group_type, tags.tag, (((((tags.group_type)::text || '-'::text) || (tags.sub_group_type)::text) || ':'::text) || (tags.tag)::text) AS tag_locality, users_tracking.modification_date_time FROM (((((((specimens LEFT JOIN codes ON (((((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text)) AND (specimens.id = codes.record_id)))) LEFT JOIN ext_links ext_links_thumbnails ON ((((specimens.id = ext_links_thumbnails.record_id) AND ((ext_links_thumbnails.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_thumbnails.category)::text = 'thumbnail'::text)))) LEFT JOIN ext_links ext_links_image_links ON ((((specimens.id = ext_links_image_links.record_id) AND ((ext_links_image_links.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_image_links.category)::text = 'image_link'::text)))) LEFT JOIN ext_links ext_links_3d_snippets ON ((((specimens.id = ext_links_3d_snippets.record_id) AND ((ext_links_3d_snippets.referenced_relation)::text = 'specimens'::text)) AND ((ext_links_3d_snippets.category)::text = 'html_3d_snippet'::text)))) LEFT JOIN identifications ON (((((identifications.referenced_relation)::text = 'specimens'::text) AND (specimens.id = identifications.record_id)) AND ((identifications.notion_concerned)::text = 'taxonomy'::text)))) LEFT JOIN tags ON ((specimens.gtu_ref = tags.gtu_ref))) LEFT JOIN (SELECT users_tracking.modification_date_time, users_tracking.record_id, users_tracking.referenced_relation FROM users_tracking ORDER BY users_tracking.id DESC LIMIT 1) users_tracking ON (((specimens.id = users_tracking.record_id) AND ((users_tracking.referenced_relation)::text = 'specimens'::text)))) ORDER BY tags.group_ref) specimens WHERE ((specimens.full_code_indexed)::text ~~ '%100%'::text) GROUP BY specimens.code_display, specimens.collection_name, specimens.gtu_country_tag_value, specimens.gtu_others_tag_value, specimens.gtu_from_date, specimens.gtu_from_date_mask, specimens.gtu_to_date, specimens.gtu_to_date_mask, specimens.coll_type, specimens.longitude, specimens.latitude, specimens.collector_ids, specimens.donator_ids, specimens.modification_date_time, specimens.urls_thumbnails;


ALTER TABLE darwin2.v_rdf_view OWNER TO darwin2;

--
-- TOC entry 348 (class 1259 OID 251477)
-- Name: v_rmca_count_ichtyology_by_number; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_count_ichtyology_by_number AS
SELECT a.referenced_relation, a.record_id, a.id, a.code_category, a.code_prefix, a.code_prefix_separator, a.code, a.code_suffix, a.code_suffix_separator, a.full_code_indexed, a.code_date, a.code_date_mask, a.code_num, a.upper_count, a.lower_count, ((a.upper_count - a.lower_count) + 1) AS counter FROM (SELECT codes.referenced_relation, codes.record_id, codes.id, codes.code_category, codes.code_prefix, codes.code_prefix_separator, codes.code, codes.code_suffix, codes.code_suffix_separator, codes.full_code_indexed, codes.code_date, codes.code_date_mask, codes.code_num, ((regexp_matches((codes.code)::text, '.(\d+)$'::text))[1])::integer AS upper_count, ((regexp_matches((codes.code)::text, '.(\d+)(-|$)'::text))[1])::integer AS lower_count FROM codes WHERE (((codes.referenced_relation)::text = 'specimens'::text) AND ((codes.code_category)::text = 'main'::text))) a;


ALTER TABLE darwin2.v_rmca_count_ichtyology_by_number OWNER TO darwin2;

--
-- TOC entry 309 (class 1259 OID 66535)
-- Name: v_rmca_get_genus_by_families; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_get_genus_by_families AS
SELECT parent.id AS parent_id, parent.name AS family, parent.level_ref AS parent_level_ref, rank_parent.level_name AS parent_level_name, child.id AS child_id, child.name AS family_or_genus, child.level_ref AS child_level_ref, rank_child.level_name AS child_level_name, (child.level_ref - parent.level_ref) AS diff, child.path AS child_path FROM (((taxonomy parent JOIN taxonomy child ON ((((child.path)::text ~~ (('%/'::text || (parent.id)::text) || '/%'::text)) OR (parent.id = child.id)))) JOIN catalogue_levels rank_parent ON ((rank_parent.id = parent.level_ref))) JOIN catalogue_levels rank_child ON ((rank_child.id = child.level_ref))) WHERE ((parent.level_ref = 34) AND (child.level_ref <= 41)) ORDER BY parent.name, (child.level_ref - parent.level_ref), child.name;


ALTER TABLE darwin2.v_rmca_get_genus_by_families OWNER TO darwin2;

--
-- TOC entry 310 (class 1259 OID 66550)
-- Name: v_rmca_count_specimen_by_families_genus; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_count_specimen_by_families_genus AS
SELECT c.family, c.child_id, c.family_or_genus, a.collection_ref, count(a.id) AS count_all, (SELECT count(specimens.id) AS count FROM specimens WHERE (specimens.taxon_ref = c.child_id)) AS count_direct FROM ((specimens a JOIN taxonomy b ON ((a.taxon_ref = b.id))) JOIN v_rmca_get_genus_by_families c ON (((((b.path)::text || (b.id)::text) || '/'::text) ~~ (((c.child_path)::text || (c.child_id)::text) || '/%'::text)))) GROUP BY c.family, c.child_id, c.family_or_genus, a.collection_ref, c.diff ORDER BY c.family, c.diff, c.family_or_genus;


ALTER TABLE darwin2.v_rmca_count_specimen_by_families_genus OWNER TO darwin2;

--
-- TOC entry 324 (class 1259 OID 108265)
-- Name: v_rmca_export_staging_info; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_export_staging_info AS
SELECT DISTINCT b.code, a.taxon_name, a.status, a.import_ref, c.collection_ref FROM ((staging a JOIN codes b ON ((((a.id = b.record_id) AND ((b.referenced_relation)::text = 'staging'::text)) AND ((b.code_category)::text = 'main'::text)))) LEFT JOIN imports c ON ((a.import_ref = c.collection_ref)));


ALTER TABLE darwin2.v_rmca_export_staging_info OWNER TO darwin2;

--
-- TOC entry 315 (class 1259 OID 108097)
-- Name: v_rmca_get_higher_by_lower; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_get_higher_by_lower AS
SELECT parent.id AS parent_id, parent.name AS higher_name, parent.level_ref AS parent_level_ref, rank_parent.level_name AS parent_level_name, child.id AS child_id, child.name AS lower_name, child.level_ref AS child_level_ref, rank_child.level_name AS child_level_name, (child.level_ref - parent.level_ref) AS diff, (((parent.path)::text || ((parent.id)::character varying)::text) || '/'::text) AS parent_path FROM (((taxonomy parent JOIN taxonomy child ON ((((child.path)::text ~~ (('%/'::text || (parent.id)::text) || '/%'::text)) OR (parent.id = child.id)))) JOIN catalogue_levels rank_parent ON ((rank_parent.id = parent.level_ref))) JOIN catalogue_levels rank_child ON ((rank_child.id = child.level_ref))) ORDER BY child.path;


ALTER TABLE darwin2.v_rmca_get_higher_by_lower OWNER TO darwin2;

--
-- TOC entry 347 (class 1259 OID 251045)
-- Name: v_rmca_gtu_tags_administraive_and_ecology; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_gtu_tags_administraive_and_ecology AS
SELECT DISTINCT t.gtu_ref, (((SELECT array_agg(t2.tag) AS array_agg FROM tags t2 WHERE ((t2.gtu_ref = t.gtu_ref) AND ((t2.group_type)::text = 'administrative'::text))) || (SELECT array_agg(t2.tag) AS array_agg FROM tags t2 WHERE ((t2.gtu_ref = t.gtu_ref) AND ((t2.group_type)::text = 'administrative area'::text)))) || (SELECT array_agg(t2.tag) AS array_agg FROM tags t2 WHERE ((t2.gtu_ref = t.gtu_ref) AND ((t2.group_type)::text = 'area'::text)))) AS administraive_tags, (SELECT array_agg(t2.tag) AS array_agg FROM tags t2 WHERE ((((t2.gtu_ref = t.gtu_ref) AND ((t2.group_type)::text <> 'administrative'::text)) AND ((t2.group_type)::text <> 'administrative area'::text)) AND ((t2.group_type)::text <> 'area'::text))) AS non_administrative_tags FROM tags t;


ALTER TABLE darwin2.v_rmca_gtu_tags_administraive_and_ecology OWNER TO darwin2;

--
-- TOC entry 313 (class 1259 OID 66636)
-- Name: v_rmca_higher_than_familiy_in_collection; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_higher_than_familiy_in_collection AS
SELECT count(*) AS count, a.collection_ref FROM (specimens a JOIN taxonomy b ON ((a.taxon_ref = b.id))) WHERE (b.level_ref < 34) GROUP BY a.collection_ref;


ALTER TABLE darwin2.v_rmca_higher_than_familiy_in_collection OWNER TO darwin2;

--
-- TOC entry 323 (class 1259 OID 108240)
-- Name: v_rmca_path_parent_children_extended_taxonomy_alpha_count_child; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_path_parent_children_extended_taxonomy_alpha_count_child AS
SELECT b.id AS parent_id, ((((b.path)::text || b.id) || '/'::text))::character varying AS parent_path, fct_rmca_sort_taxon_path_alphabetically(((((b.path)::text || b.id) || '/'::text))::character varying) AS parent_alpha_path, b.name AS parent_name, d.level_name AS parent_level, d.level_order AS parent_level_order, a.id AS child_id, ((((a.path)::text || (a.id)::text) || '/'::text))::character varying AS child_path, a.name AS child_name, a.level_ref, c.level_name AS child_level, c.level_order AS child_level_order FROM (((taxonomy a JOIN taxonomy b ON (((((((a.path)::text || (a.id)::text) || '/'::text))::character varying)::text ~~ (((((b.path)::text || (b.id)::text) || '/%'::text))::character varying)::text))) JOIN catalogue_levels c ON ((a.level_ref = c.id))) JOIN catalogue_levels d ON ((b.level_ref = d.id)));


ALTER TABLE darwin2.v_rmca_path_parent_children_extended_taxonomy_alpha_count_child OWNER TO darwin2;

--
-- TOC entry 351 (class 1259 OID 251545)
-- Name: v_rmca_report_ig_ichtyo_1_main; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_report_ig_ichtyo_1_main AS
SELECT DISTINCT igs.id, igs.ig_num, fct_mask_date((igs.ig_date)::timestamp without time zone, igs.ig_date_mask) AS date_donation, p.formated_name AS donateur, fct_remove_null_array_elem((array_agg(DISTINCT p2.formated_name) || array_agg(DISTINCT expeditions.name))) AS collectors_array, sum(v_rmca_count_ichtyology_by_number.counter) AS sum, specimens.collection_name FROM ((((((((igs LEFT JOIN specimens ON ((igs.id = specimens.ig_ref))) LEFT JOIN v_rmca_count_ichtyology_by_number ON ((specimens.id = v_rmca_count_ichtyology_by_number.record_id))) LEFT JOIN catalogue_people c ON ((((specimens.id = c.record_id) AND ((c.referenced_relation)::text = 'specimens'::text)) AND ((c.people_type)::text = 'donator'::text)))) LEFT JOIN people p ON ((c.people_ref = p.id))) LEFT JOIN catalogue_people c2 ON ((((specimens.id = c2.record_id) AND ((c2.referenced_relation)::text = 'specimens'::text)) AND ((c2.people_type)::text = 'collector'::text)))) LEFT JOIN people p2 ON ((c2.people_ref = p2.id))) LEFT JOIN expeditions ON ((specimens.expedition_ref = expeditions.id))) LEFT JOIN collections ON ((specimens.collection_ref = collections.id))) GROUP BY igs.id, igs.ig_num, igs.ig_date, igs.ig_date_mask, p.formated_name, specimens.collection_name;


ALTER TABLE darwin2.v_rmca_report_ig_ichtyo_1_main OWNER TO darwin2;

--
-- TOC entry 350 (class 1259 OID 251540)
-- Name: v_rmca_report_ig_ichtyo_2_localities; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE v_rmca_report_ig_ichtyo_2_localities (
    id integer,
    country character varying,
    id_gtu integer,
    locality text,
    coordinates_text text,
    date_min text,
    date_max text,
    collections_numbers text,
    sum bigint
);


ALTER TABLE darwin2.v_rmca_report_ig_ichtyo_2_localities OWNER TO darwin2;

--
-- TOC entry 349 (class 1259 OID 251535)
-- Name: v_rmca_report_ig_ichtyo_3_taxo; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_report_ig_ichtyo_3_taxo AS
SELECT DISTINCT igs.id, specimens.taxon_name, string_agg(DISTINCT (storage_parts.specimen_part)::text, ','::text) AS parts, sum(v_rmca_count_ichtyology_by_number.counter) AS counter, string_agg(replace((v_rmca_count_ichtyology_by_number.code)::text, ((igs.ig_num)::text || '.'::text), ''::text), ' '::text) AS codes FROM (((igs LEFT JOIN specimens ON ((igs.id = specimens.ig_ref))) LEFT JOIN v_rmca_count_ichtyology_by_number ON ((specimens.id = v_rmca_count_ichtyology_by_number.record_id))) LEFT JOIN storage_parts ON ((specimens.id = storage_parts.specimen_ref))) GROUP BY igs.id, specimens.taxon_name;


ALTER TABLE darwin2.v_rmca_report_ig_ichtyo_3_taxo OWNER TO darwin2;

--
-- TOC entry 317 (class 1259 OID 108206)
-- Name: v_rmca_split_path; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_split_path AS
SELECT taxonomy.id AS child_name_id, taxonomy.path, (regexp_matches((taxonomy.path)::text, '([0-9]+)/'::text, 'g'::text))[1] AS regexp_matches FROM taxonomy;


ALTER TABLE darwin2.v_rmca_split_path OWNER TO darwin2;

--
-- TOC entry 318 (class 1259 OID 108210)
-- Name: v_rmca_split_path_extended; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_split_path_extended AS
SELECT DISTINCT b.child_name_id, c.name, c.id, c.path, c.level_ref, d.level_name, d.level_order FROM ((v_rmca_split_path b JOIN taxonomy c ON ((b.child_name_id = c.id))) JOIN catalogue_levels d ON ((c.level_ref = d.id)));


ALTER TABLE darwin2.v_rmca_split_path_extended OWNER TO darwin2;

--
-- TOC entry 319 (class 1259 OID 108214)
-- Name: v_rmca_split_path_extended_alpha_path; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_rmca_split_path_extended_alpha_path AS
SELECT DISTINCT b.child_name_id, c.name, c.id, c.path, c.level_ref, d.level_name, d.level_order, (((c.path)::text || (c.id)::text) || '/'::text) AS full_path, fct_rmca_sort_taxon_path_alphabetically(((((c.path)::text || (c.id)::text) || '/'::text))::character varying) AS alpha_path FROM ((v_rmca_split_path b JOIN taxonomy c ON ((b.child_name_id = c.id))) JOIN catalogue_levels d ON ((c.level_ref = d.id)));


ALTER TABLE darwin2.v_rmca_split_path_extended_alpha_path OWNER TO darwin2;

--
-- TOC entry 369 (class 1259 OID 715399)
-- Name: v_taxonomical_statistics_callard; Type: VIEW; Schema: darwin2; Owner: darwin2
--

CREATE VIEW v_taxonomical_statistics_callard AS
SELECT DISTINCT specimens.collection_ref, specimens.taxon_single_ref, taxonomy.name, taxonomy.name_indexed, taxonomy.level_ref, taxonomy.status, taxonomy.local_naming, taxonomy.color, taxonomy.path, taxonomy.parent_ref, taxonomy.id AS taxon, taxonomy.extinct, catalogue_levels.id AS level_id, catalogue_levels.level_type, catalogue_levels.level_name, catalogue_levels.level_sys_name, catalogue_levels.optional_level, catalogue_levels.level_order FROM (((SELECT specimens.collection_ref, regexp_split_to_table(((((specimens.taxon_path)::text || '/'::text) || ((specimens.taxon_ref)::character varying)::text) || '/'::text), '/'::text) AS taxon_single_ref FROM specimens) specimens LEFT JOIN taxonomy ON ((((specimens.taxon_single_ref)::character varying)::text = ((taxonomy.id)::character varying)::text))) LEFT JOIN catalogue_levels ON ((taxonomy.level_ref = catalogue_levels.id)));


ALTER TABLE darwin2.v_taxonomical_statistics_callard OWNER TO darwin2;

--
-- TOC entry 203 (class 1259 OID 16808)
-- Name: vernacular_names; Type: TABLE; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE TABLE vernacular_names (
    id integer NOT NULL,
    community character varying NOT NULL,
    community_indexed character varying NOT NULL,
    name character varying NOT NULL,
    name_indexed character varying NOT NULL
)
INHERITS (template_table_record_ref);


ALTER TABLE darwin2.vernacular_names OWNER TO darwin2;

--
-- TOC entry 5906 (class 0 OID 0)
-- Dependencies: 203
-- Name: TABLE vernacular_names; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON TABLE vernacular_names IS 'List of vernacular names for a given unit and a given language community';


--
-- TOC entry 5907 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.referenced_relation; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.referenced_relation IS 'Reference of the unit table a vernacular name for a language community has to be defined - id field of table_list table';


--
-- TOC entry 5908 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.record_id; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.record_id IS 'Identifier of record a vernacular name for a language community has to be defined';


--
-- TOC entry 5909 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.community; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.community IS 'Language community, a unit translation is available for';


--
-- TOC entry 5910 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.community_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.community_indexed IS 'indexed version of the language community';


--
-- TOC entry 5911 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.name; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.name IS 'Vernacular name';


--
-- TOC entry 5912 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN vernacular_names.name_indexed; Type: COMMENT; Schema: darwin2; Owner: darwin2
--

COMMENT ON COLUMN vernacular_names.name_indexed IS 'Indexed form of vernacular name';


--
-- TOC entry 202 (class 1259 OID 16806)
-- Name: vernacular_names_id_seq; Type: SEQUENCE; Schema: darwin2; Owner: darwin2
--

CREATE SEQUENCE vernacular_names_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.vernacular_names_id_seq OWNER TO darwin2;

--
-- TOC entry 5914 (class 0 OID 0)
-- Dependencies: 202
-- Name: vernacular_names_id_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: darwin2
--

ALTER SEQUENCE vernacular_names_id_seq OWNED BY vernacular_names.id;


--
-- TOC entry 362 (class 1259 OID 714879)
-- Name: vmap0_world_boundaries_gid_seq; Type: SEQUENCE; Schema: darwin2; Owner: postgres
--

CREATE SEQUENCE vmap0_world_boundaries_gid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE darwin2.vmap0_world_boundaries_gid_seq OWNER TO postgres;

--
-- TOC entry 5915 (class 0 OID 0)
-- Dependencies: 362
-- Name: vmap0_world_boundaries_gid_seq; Type: SEQUENCE OWNED BY; Schema: darwin2; Owner: postgres
--

ALTER SEQUENCE vmap0_world_boundaries_gid_seq OWNED BY vmap0_world_boundaries.gid;


--
-- TOC entry 4347 (class 2604 OID 17957)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY bibliography ALTER COLUMN id SET DEFAULT nextval('bibliography_id_seq'::regclass);


--
-- TOC entry 4349 (class 2604 OID 17971)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_bibliography ALTER COLUMN id SET DEFAULT nextval('catalogue_bibliography_id_seq'::regclass);


--
-- TOC entry 4102 (class 2604 OID 16670)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_levels ALTER COLUMN id SET DEFAULT nextval('catalogue_levels_id_seq'::regclass);


--
-- TOC entry 4098 (class 2604 OID 16649)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_people ALTER COLUMN id SET DEFAULT nextval('catalogue_people_id_seq'::regclass);


--
-- TOC entry 4095 (class 2604 OID 16629)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_relationships ALTER COLUMN id SET DEFAULT nextval('catalogue_relationships_id_seq'::regclass);


--
-- TOC entry 4231 (class 2604 OID 17275)
-- Name: status; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy ALTER COLUMN status SET DEFAULT 'valid'::character varying;


--
-- TOC entry 4232 (class 2604 OID 17276)
-- Name: local_naming; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy ALTER COLUMN local_naming SET DEFAULT false;


--
-- TOC entry 4233 (class 2604 OID 17277)
-- Name: path; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy ALTER COLUMN path SET DEFAULT '/'::character varying;


--
-- TOC entry 4234 (class 2604 OID 17278)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy ALTER COLUMN id SET DEFAULT nextval('chronostratigraphy_id_seq'::regclass);


--
-- TOC entry 4220 (class 2604 OID 17219)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY classification_keywords ALTER COLUMN id SET DEFAULT nextval('classification_keywords_id_seq'::regclass);


--
-- TOC entry 4223 (class 2604 OID 17233)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY classification_synonymies ALTER COLUMN id SET DEFAULT nextval('classification_synonymies_id_seq'::regclass);


--
-- TOC entry 4287 (class 2604 OID 17485)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY codes ALTER COLUMN id SET DEFAULT nextval('codes_id_seq'::regclass);


--
-- TOC entry 4306 (class 2604 OID 17604)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collecting_methods ALTER COLUMN id SET DEFAULT nextval('collecting_methods_id_seq'::regclass);


--
-- TOC entry 4303 (class 2604 OID 17570)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collecting_tools ALTER COLUMN id SET DEFAULT nextval('collecting_tools_id_seq'::regclass);


--
-- TOC entry 4196 (class 2604 OID 17135)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collection_maintenance ALTER COLUMN id SET DEFAULT nextval('collection_maintenance_id_seq'::regclass);


--
-- TOC entry 4174 (class 2604 OID 17035)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections ALTER COLUMN id SET DEFAULT nextval('collections_id_seq'::regclass);


--
-- TOC entry 4184 (class 2604 OID 17074)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections_rights ALTER COLUMN id SET DEFAULT nextval('collections_rights_id_seq'::regclass);


--
-- TOC entry 4105 (class 2604 OID 16700)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY comments ALTER COLUMN id SET DEFAULT nextval('comments_id_seq'::regclass);


--
-- TOC entry 4130 (class 2604 OID 16824)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY expeditions ALTER COLUMN id SET DEFAULT nextval('expeditions_id_seq'::regclass);


--
-- TOC entry 4106 (class 2604 OID 16711)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY ext_links ALTER COLUMN id SET DEFAULT nextval('ext_links_id_seq'::regclass);


--
-- TOC entry 4310 (class 2604 OID 17654)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY flat_dict ALTER COLUMN id SET DEFAULT nextval('flat_dict_id_seq'::regclass);


--
-- TOC entry 4107 (class 2604 OID 16724)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY gtu ALTER COLUMN id SET DEFAULT nextval('gtu_id_seq'::regclass);


--
-- TOC entry 4125 (class 2604 OID 16795)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY identifications ALTER COLUMN id SET DEFAULT nextval('identifications_id_seq'::regclass);


--
-- TOC entry 4252 (class 2604 OID 17380)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY igs ALTER COLUMN id SET DEFAULT nextval('igs_id_seq'::regclass);


--
-- TOC entry 4312 (class 2604 OID 17668)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY imports ALTER COLUMN id SET DEFAULT nextval('imports_id_seq'::regclass);


--
-- TOC entry 4188 (class 2604 OID 17097)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY informative_workflow ALTER COLUMN id SET DEFAULT nextval('informative_workflow_id_seq'::regclass);


--
-- TOC entry 4292 (class 2604 OID 17502)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY insurances ALTER COLUMN id SET DEFAULT nextval('insurances_id_seq'::regclass);


--
-- TOC entry 4247 (class 2604 OID 17354)
-- Name: status; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology ALTER COLUMN status SET DEFAULT 'valid'::character varying;


--
-- TOC entry 4248 (class 2604 OID 17355)
-- Name: local_naming; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology ALTER COLUMN local_naming SET DEFAULT false;


--
-- TOC entry 4249 (class 2604 OID 17356)
-- Name: path; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology ALTER COLUMN path SET DEFAULT '/'::character varying;


--
-- TOC entry 4250 (class 2604 OID 17357)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology ALTER COLUMN id SET DEFAULT nextval('lithology_id_seq'::regclass);


--
-- TOC entry 4236 (class 2604 OID 17301)
-- Name: status; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy ALTER COLUMN status SET DEFAULT 'valid'::character varying;


--
-- TOC entry 4237 (class 2604 OID 17302)
-- Name: local_naming; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy ALTER COLUMN local_naming SET DEFAULT false;


--
-- TOC entry 4238 (class 2604 OID 17303)
-- Name: path; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy ALTER COLUMN path SET DEFAULT '/'::character varying;


--
-- TOC entry 4239 (class 2604 OID 17304)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy ALTER COLUMN id SET DEFAULT nextval('lithostratigraphy_id_seq'::regclass);


--
-- TOC entry 4344 (class 2604 OID 17929)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_history ALTER COLUMN id SET DEFAULT nextval('loan_history_id_seq'::regclass);


--
-- TOC entry 4335 (class 2604 OID 17854)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_items ALTER COLUMN id SET DEFAULT nextval('loan_items_id_seq'::regclass);


--
-- TOC entry 4337 (class 2604 OID 17883)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_rights ALTER COLUMN id SET DEFAULT nextval('loan_rights_id_seq'::regclass);


--
-- TOC entry 4339 (class 2604 OID 17904)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_status ALTER COLUMN id SET DEFAULT nextval('loan_status_id_seq'::regclass);


--
-- TOC entry 4332 (class 2604 OID 17841)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loans ALTER COLUMN id SET DEFAULT nextval('loans_id_seq'::regclass);


--
-- TOC entry 4241 (class 2604 OID 17327)
-- Name: status; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy ALTER COLUMN status SET DEFAULT 'valid'::character varying;


--
-- TOC entry 4242 (class 2604 OID 17328)
-- Name: local_naming; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy ALTER COLUMN local_naming SET DEFAULT false;


--
-- TOC entry 4243 (class 2604 OID 17329)
-- Name: path; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy ALTER COLUMN path SET DEFAULT '/'::character varying;


--
-- TOC entry 4244 (class 2604 OID 17330)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy ALTER COLUMN id SET DEFAULT nextval('mineralogy_id_seq'::regclass);


--
-- TOC entry 4148 (class 2604 OID 16888)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY multimedia ALTER COLUMN id SET DEFAULT nextval('multimedia_id_seq'::regclass);


--
-- TOC entry 4346 (class 2604 OID 17946)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY multimedia_todelete ALTER COLUMN id SET DEFAULT nextval('multimedia_todelete_id_seq'::regclass);


--
-- TOC entry 4200 (class 2604 OID 17154)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY my_saved_searches ALTER COLUMN id SET DEFAULT nextval('my_saved_searches_id_seq'::regclass);


--
-- TOC entry 4206 (class 2604 OID 17177)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY my_widgets ALTER COLUMN id SET DEFAULT nextval('my_widgets_id_seq'::regclass);


--
-- TOC entry 4082 (class 2604 OID 16604)
-- Name: is_physical; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people ALTER COLUMN is_physical SET DEFAULT true;


--
-- TOC entry 4083 (class 2604 OID 16605)
-- Name: title; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people ALTER COLUMN title SET DEFAULT ''::character varying;


--
-- TOC entry 4084 (class 2604 OID 16606)
-- Name: birth_date_mask; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people ALTER COLUMN birth_date_mask SET DEFAULT 0;


--
-- TOC entry 4085 (class 2604 OID 16607)
-- Name: birth_date; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people ALTER COLUMN birth_date SET DEFAULT '0001-01-01'::date;


--
-- TOC entry 4086 (class 2604 OID 16609)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people ALTER COLUMN id SET DEFAULT nextval('people_id_seq'::regclass);


--
-- TOC entry 4165 (class 2604 OID 16962)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_addresses ALTER COLUMN id SET DEFAULT nextval('people_addresses_id_seq'::regclass);


--
-- TOC entry 4162 (class 2604 OID 16944)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_comm ALTER COLUMN id SET DEFAULT nextval('people_comm_id_seq'::regclass);


--
-- TOC entry 4144 (class 2604 OID 16867)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_languages ALTER COLUMN id SET DEFAULT nextval('people_languages_id_seq'::regclass);


--
-- TOC entry 4156 (class 2604 OID 16918)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_relationships ALTER COLUMN id SET DEFAULT nextval('people_relationships_id_seq'::regclass);


--
-- TOC entry 4309 (class 2604 OID 17638)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY preferences ALTER COLUMN id SET DEFAULT nextval('preferences_id_seq'::regclass);


--
-- TOC entry 4116 (class 2604 OID 16776)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY properties ALTER COLUMN id SET DEFAULT nextval('properties_id_seq'::regclass);


--
-- TOC entry 4308 (class 2604 OID 17618)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_methods ALTER COLUMN id SET DEFAULT nextval('specimen_collecting_methods_id_seq'::regclass);


--
-- TOC entry 4305 (class 2604 OID 17584)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_tools ALTER COLUMN id SET DEFAULT nextval('specimen_collecting_tools_id_seq'::regclass);


--
-- TOC entry 4255 (class 2604 OID 17395)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens ALTER COLUMN id SET DEFAULT nextval('specimens_id_seq'::regclass);


--
-- TOC entry 4299 (class 2604 OID 17531)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships ALTER COLUMN id SET DEFAULT nextval('specimens_relationships_id_seq'::regclass);


--
-- TOC entry 4318 (class 2604 OID 17693)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging ALTER COLUMN id SET DEFAULT nextval('staging_id_seq'::regclass);


--
-- TOC entry 4351 (class 2604 OID 108330)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_catalogue ALTER COLUMN id SET DEFAULT nextval('staging_catalogue_id_seq'::regclass);


--
-- TOC entry 4326 (class 2604 OID 17786)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_collecting_methods ALTER COLUMN id SET DEFAULT nextval('staging_collecting_methods_id_seq'::regclass);


--
-- TOC entry 4322 (class 2604 OID 17737)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_info ALTER COLUMN id SET DEFAULT nextval('staging_info_id_seq'::regclass);


--
-- TOC entry 4328 (class 2604 OID 17822)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_people ALTER COLUMN id SET DEFAULT nextval('staging_people_id_seq'::regclass);


--
-- TOC entry 4323 (class 2604 OID 17753)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_relationship ALTER COLUMN id SET DEFAULT nextval('staging_relationship_id_seq'::regclass);


--
-- TOC entry 4327 (class 2604 OID 17806)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_tag_groups ALTER COLUMN id SET DEFAULT nextval('staging_tag_groups_id_seq'::regclass);


--
-- TOC entry 4354 (class 2604 OID 250518)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY storage_parts ALTER COLUMN id SET DEFAULT nextval('storage_parts_id_seq'::regclass);


--
-- TOC entry 4113 (class 2604 OID 16740)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY tag_groups ALTER COLUMN id SET DEFAULT nextval('tag_groups_id_seq'::regclass);


--
-- TOC entry 4225 (class 2604 OID 17248)
-- Name: status; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy ALTER COLUMN status SET DEFAULT 'valid'::character varying;


--
-- TOC entry 4226 (class 2604 OID 17249)
-- Name: local_naming; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy ALTER COLUMN local_naming SET DEFAULT false;


--
-- TOC entry 4227 (class 2604 OID 17250)
-- Name: path; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy ALTER COLUMN path SET DEFAULT '/'::character varying;


--
-- TOC entry 4228 (class 2604 OID 17251)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy ALTER COLUMN id SET DEFAULT nextval('taxonomy_id_seq'::regclass);


--
-- TOC entry 4135 (class 2604 OID 16841)
-- Name: is_physical; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users ALTER COLUMN is_physical SET DEFAULT true;


--
-- TOC entry 4136 (class 2604 OID 16842)
-- Name: title; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users ALTER COLUMN title SET DEFAULT ''::character varying;


--
-- TOC entry 4137 (class 2604 OID 16843)
-- Name: birth_date_mask; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users ALTER COLUMN birth_date_mask SET DEFAULT 0;


--
-- TOC entry 4138 (class 2604 OID 16844)
-- Name: birth_date; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users ALTER COLUMN birth_date SET DEFAULT '0001-01-01'::date;


--
-- TOC entry 4139 (class 2604 OID 16846)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 4170 (class 2604 OID 16997)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_addresses ALTER COLUMN id SET DEFAULT nextval('users_addresses_id_seq'::regclass);


--
-- TOC entry 4167 (class 2604 OID 16979)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_comm ALTER COLUMN id SET DEFAULT nextval('users_comm_id_seq'::regclass);


--
-- TOC entry 4172 (class 2604 OID 17014)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_login_infos ALTER COLUMN id SET DEFAULT nextval('users_login_infos_id_seq'::regclass);


--
-- TOC entry 4193 (class 2604 OID 17117)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_tracking ALTER COLUMN id SET DEFAULT nextval('users_tracking_id_seq'::regclass);


--
-- TOC entry 4129 (class 2604 OID 16811)
-- Name: id; Type: DEFAULT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY vernacular_names ALTER COLUMN id SET DEFAULT nextval('vernacular_names_id_seq'::regclass);


--
-- TOC entry 4365 (class 2604 OID 714881)
-- Name: gid; Type: DEFAULT; Schema: darwin2; Owner: postgres
--

ALTER TABLE ONLY vmap0_world_boundaries ALTER COLUMN gid SET DEFAULT nextval('vmap0_world_boundaries_gid_seq'::regclass);


--
-- TOC entry 4727 (class 2606 OID 17963)
-- Name: pk_bibliography; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY bibliography
    ADD CONSTRAINT pk_bibliography PRIMARY KEY (id);


--
-- TOC entry 4732 (class 2606 OID 17976)
-- Name: pk_catalogue_bibliography; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_bibliography
    ADD CONSTRAINT pk_catalogue_bibliography PRIMARY KEY (id);


--
-- TOC entry 4389 (class 2606 OID 16677)
-- Name: pk_catalogue_levels; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_levels
    ADD CONSTRAINT pk_catalogue_levels PRIMARY KEY (id);


--
-- TOC entry 4385 (class 2606 OID 16657)
-- Name: pk_catalogue_people; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_people
    ADD CONSTRAINT pk_catalogue_people PRIMARY KEY (id);


--
-- TOC entry 4376 (class 2606 OID 16635)
-- Name: pk_catalogue_relationships; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_relationships
    ADD CONSTRAINT pk_catalogue_relationships PRIMARY KEY (id);


--
-- TOC entry 4559 (class 2606 OID 17283)
-- Name: pk_chronostratigraphy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY chronostratigraphy
    ADD CONSTRAINT pk_chronostratigraphy PRIMARY KEY (id);


--
-- TOC entry 4536 (class 2606 OID 17225)
-- Name: pk_classification_keywords_id; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY classification_keywords
    ADD CONSTRAINT pk_classification_keywords_id PRIMARY KEY (id);


--
-- TOC entry 4637 (class 2606 OID 17494)
-- Name: pk_codes; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY codes
    ADD CONSTRAINT pk_codes PRIMARY KEY (id);


--
-- TOC entry 4667 (class 2606 OID 17610)
-- Name: pk_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collecting_methods
    ADD CONSTRAINT pk_collecting_methods PRIMARY KEY (id);


--
-- TOC entry 4656 (class 2606 OID 17576)
-- Name: pk_collecting_tools; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collecting_tools
    ADD CONSTRAINT pk_collecting_tools PRIMARY KEY (id);


--
-- TOC entry 4524 (class 2606 OID 17143)
-- Name: pk_collection_maintenance; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collection_maintenance
    ADD CONSTRAINT pk_collection_maintenance PRIMARY KEY (id);


--
-- TOC entry 4502 (class 2606 OID 17046)
-- Name: pk_collections; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT pk_collections PRIMARY KEY (id);


--
-- TOC entry 4508 (class 2606 OID 17079)
-- Name: pk_collections_right; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collections_rights
    ADD CONSTRAINT pk_collections_right PRIMARY KEY (id);


--
-- TOC entry 4400 (class 2606 OID 16705)
-- Name: pk_comments; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT pk_comments PRIMARY KEY (id);


--
-- TOC entry 4455 (class 2606 OID 16833)
-- Name: pk_expeditions; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY expeditions
    ADD CONSTRAINT pk_expeditions PRIMARY KEY (id);


--
-- TOC entry 4405 (class 2606 OID 16716)
-- Name: pk_ext_links; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY ext_links
    ADD CONSTRAINT pk_ext_links PRIMARY KEY (id);


--
-- TOC entry 4678 (class 2606 OID 17660)
-- Name: pk_flat_dict; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY flat_dict
    ADD CONSTRAINT pk_flat_dict PRIMARY KEY (id);


--
-- TOC entry 4412 (class 2606 OID 16734)
-- Name: pk_gtu; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY gtu
    ADD CONSTRAINT pk_gtu PRIMARY KEY (id);


--
-- TOC entry 4441 (class 2606 OID 16803)
-- Name: pk_identifications; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY identifications
    ADD CONSTRAINT pk_identifications PRIMARY KEY (id);


--
-- TOC entry 4592 (class 2606 OID 17387)
-- Name: pk_igs; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY igs
    ADD CONSTRAINT pk_igs PRIMARY KEY (id);


--
-- TOC entry 4683 (class 2606 OID 17677)
-- Name: pk_import; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY imports
    ADD CONSTRAINT pk_import PRIMARY KEY (id);


--
-- TOC entry 4514 (class 2606 OID 17106)
-- Name: pk_informative_workflow; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY informative_workflow
    ADD CONSTRAINT pk_informative_workflow PRIMARY KEY (id);


--
-- TOC entry 4644 (class 2606 OID 17513)
-- Name: pk_insurances; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY insurances
    ADD CONSTRAINT pk_insurances PRIMARY KEY (id);


--
-- TOC entry 4586 (class 2606 OID 17362)
-- Name: pk_lithology; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY lithology
    ADD CONSTRAINT pk_lithology PRIMARY KEY (id);


--
-- TOC entry 4567 (class 2606 OID 17309)
-- Name: pk_lithostratigraphy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY lithostratigraphy
    ADD CONSTRAINT pk_lithostratigraphy PRIMARY KEY (id);


--
-- TOC entry 4721 (class 2606 OID 17935)
-- Name: pk_loan_history; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_history
    ADD CONSTRAINT pk_loan_history PRIMARY KEY (id);


--
-- TOC entry 4706 (class 2606 OID 17860)
-- Name: pk_loan_items; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_items
    ADD CONSTRAINT pk_loan_items PRIMARY KEY (id);


--
-- TOC entry 4712 (class 2606 OID 17886)
-- Name: pk_loan_rights; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_rights
    ADD CONSTRAINT pk_loan_rights PRIMARY KEY (id);


--
-- TOC entry 4719 (class 2606 OID 17913)
-- Name: pk_loan_status; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_status
    ADD CONSTRAINT pk_loan_status PRIMARY KEY (id);


--
-- TOC entry 4701 (class 2606 OID 17848)
-- Name: pk_loans; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loans
    ADD CONSTRAINT pk_loans PRIMARY KEY (id);


--
-- TOC entry 4577 (class 2606 OID 17336)
-- Name: pk_mineralogy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY mineralogy
    ADD CONSTRAINT pk_mineralogy PRIMARY KEY (id);


--
-- TOC entry 4472 (class 2606 OID 16900)
-- Name: pk_multimedia; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY multimedia
    ADD CONSTRAINT pk_multimedia PRIMARY KEY (id);


--
-- TOC entry 4723 (class 2606 OID 17951)
-- Name: pk_multimedia_todelete; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY multimedia_todelete
    ADD CONSTRAINT pk_multimedia_todelete PRIMARY KEY (id);


--
-- TOC entry 4526 (class 2606 OID 17164)
-- Name: pk_my_saved_searches; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY my_saved_searches
    ADD CONSTRAINT pk_my_saved_searches PRIMARY KEY (id);


--
-- TOC entry 4531 (class 2606 OID 17192)
-- Name: pk_my_widgets; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY my_widgets
    ADD CONSTRAINT pk_my_widgets PRIMARY KEY (id);


--
-- TOC entry 4371 (class 2606 OID 16621)
-- Name: pk_people; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people
    ADD CONSTRAINT pk_people PRIMARY KEY (id);


--
-- TOC entry 4484 (class 2606 OID 16968)
-- Name: pk_people_addresses; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people_addresses
    ADD CONSTRAINT pk_people_addresses PRIMARY KEY (id);


--
-- TOC entry 4480 (class 2606 OID 16951)
-- Name: pk_people_comm; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people_comm
    ADD CONSTRAINT pk_people_comm PRIMARY KEY (id);


--
-- TOC entry 4466 (class 2606 OID 16875)
-- Name: pk_people_languages; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people_languages
    ADD CONSTRAINT pk_people_languages PRIMARY KEY (id);


--
-- TOC entry 4476 (class 2606 OID 16928)
-- Name: pk_people_relationships; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people_relationships
    ADD CONSTRAINT pk_people_relationships PRIMARY KEY (id);


--
-- TOC entry 4676 (class 2606 OID 17643)
-- Name: pk_preferences; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY preferences
    ADD CONSTRAINT pk_preferences PRIMARY KEY (id);


--
-- TOC entry 4436 (class 2606 OID 16789)
-- Name: pk_properties; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY properties
    ADD CONSTRAINT pk_properties PRIMARY KEY (id);


--
-- TOC entry 4402 (class 2606 OID 229259)
-- Name: pk_rmca_unique_comment_for_insertion; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY comments
    ADD CONSTRAINT pk_rmca_unique_comment_for_insertion UNIQUE (referenced_relation, record_id, notion_concerned, comment);


--
-- TOC entry 4672 (class 2606 OID 17620)
-- Name: pk_specimen_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimen_collecting_methods
    ADD CONSTRAINT pk_specimen_collecting_methods PRIMARY KEY (id);


--
-- TOC entry 4661 (class 2606 OID 17586)
-- Name: pk_specimen_collecting_tools; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimen_collecting_tools
    ADD CONSTRAINT pk_specimen_collecting_tools PRIMARY KEY (id);


--
-- TOC entry 4743 (class 2606 OID 250533)
-- Name: pk_specimen_paths; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY storage_parts
    ADD CONSTRAINT pk_specimen_paths PRIMARY KEY (id);


--
-- TOC entry 4631 (class 2606 OID 17429)
-- Name: pk_specimens; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT pk_specimens PRIMARY KEY (id);


--
-- TOC entry 4652 (class 2606 OID 17539)
-- Name: pk_specimens_relationships; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT pk_specimens_relationships PRIMARY KEY (id);


--
-- TOC entry 4686 (class 2606 OID 17701)
-- Name: pk_staging; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT pk_staging PRIMARY KEY (id);


--
-- TOC entry 4741 (class 2606 OID 108336)
-- Name: pk_staging_catalogue; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_catalogue
    ADD CONSTRAINT pk_staging_catalogue PRIMARY KEY (id);


--
-- TOC entry 4692 (class 2606 OID 17788)
-- Name: pk_staging_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_collecting_methods
    ADD CONSTRAINT pk_staging_collecting_methods PRIMARY KEY (id);


--
-- TOC entry 4688 (class 2606 OID 17742)
-- Name: pk_staging_info; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_info
    ADD CONSTRAINT pk_staging_info PRIMARY KEY (id);


--
-- TOC entry 4699 (class 2606 OID 17830)
-- Name: pk_staging_people; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_people
    ADD CONSTRAINT pk_staging_people PRIMARY KEY (id);


--
-- TOC entry 4690 (class 2606 OID 17760)
-- Name: pk_staging_relationship; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_relationship
    ADD CONSTRAINT pk_staging_relationship PRIMARY KEY (id);


--
-- TOC entry 4696 (class 2606 OID 17811)
-- Name: pk_staging_tag_groups; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_tag_groups
    ADD CONSTRAINT pk_staging_tag_groups PRIMARY KEY (id);


--
-- TOC entry 4541 (class 2606 OID 17240)
-- Name: pk_synonym_id; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY classification_synonymies
    ADD CONSTRAINT pk_synonym_id PRIMARY KEY (id);


--
-- TOC entry 4417 (class 2606 OID 16747)
-- Name: pk_tag_groups; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY tag_groups
    ADD CONSTRAINT pk_tag_groups PRIMARY KEY (id);


--
-- TOC entry 4427 (class 2606 OID 239285)
-- Name: pk_tags_for_replication_rmca; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY tags
    ADD CONSTRAINT pk_tags_for_replication_rmca PRIMARY KEY (gtu_ref, group_ref, group_type, sub_group_type, tag, tag_indexed);


--
-- TOC entry 4550 (class 2606 OID 17257)
-- Name: pk_taxonomy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY taxonomy
    ADD CONSTRAINT pk_taxonomy PRIMARY KEY (id);


--
-- TOC entry 4460 (class 2606 OID 16854)
-- Name: pk_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT pk_users PRIMARY KEY (id);


--
-- TOC entry 4492 (class 2606 OID 17003)
-- Name: pk_users_addresses; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_addresses
    ADD CONSTRAINT pk_users_addresses PRIMARY KEY (id);


--
-- TOC entry 4488 (class 2606 OID 16986)
-- Name: pk_users_comm; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_comm
    ADD CONSTRAINT pk_users_comm PRIMARY KEY (id);


--
-- TOC entry 4494 (class 2606 OID 17020)
-- Name: pk_users_login_infos; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_login_infos
    ADD CONSTRAINT pk_users_login_infos PRIMARY KEY (id);


--
-- TOC entry 4519 (class 2606 OID 17124)
-- Name: pk_users_tracking_pk; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_tracking
    ADD CONSTRAINT pk_users_tracking_pk PRIMARY KEY (id);


--
-- TOC entry 4448 (class 2606 OID 16816)
-- Name: pk_vernacular_names; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY vernacular_names
    ADD CONSTRAINT pk_vernacular_names PRIMARY KEY (id);


--
-- TOC entry 4753 (class 2606 OID 715101)
-- Name: specimens_wrong_countries_pk; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimens_detect_wrong_countries
    ADD CONSTRAINT specimens_wrong_countries_pk PRIMARY KEY (id);


--
-- TOC entry 4729 (class 2606 OID 17965)
-- Name: unq_bibliography; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY bibliography
    ADD CONSTRAINT unq_bibliography UNIQUE (title_indexed, type, year);


--
-- TOC entry 4734 (class 2606 OID 17978)
-- Name: unq_catalogue_bibliography; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_bibliography
    ADD CONSTRAINT unq_catalogue_bibliography UNIQUE (referenced_relation, record_id, bibliography_ref);


--
-- TOC entry 4391 (class 2606 OID 16679)
-- Name: unq_catalogue_levels; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_levels
    ADD CONSTRAINT unq_catalogue_levels UNIQUE (level_type, level_name);


--
-- TOC entry 4387 (class 2606 OID 16659)
-- Name: unq_catalogue_people; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_people
    ADD CONSTRAINT unq_catalogue_people UNIQUE (referenced_relation, people_type, people_sub_type, record_id, people_ref);


--
-- TOC entry 4378 (class 2606 OID 16637)
-- Name: unq_catalogue_relationships; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY catalogue_relationships
    ADD CONSTRAINT unq_catalogue_relationships UNIQUE (referenced_relation, relationship_type, record_id_1, record_id_2);


--
-- TOC entry 4561 (class 2606 OID 17285)
-- Name: unq_chronostratigraphy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY chronostratigraphy
    ADD CONSTRAINT unq_chronostratigraphy UNIQUE (path, name_indexed, level_ref);


--
-- TOC entry 4639 (class 2606 OID 17496)
-- Name: unq_codes; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY codes
    ADD CONSTRAINT unq_codes UNIQUE (referenced_relation, record_id, full_code_indexed, code_category);


--
-- TOC entry 4669 (class 2606 OID 17612)
-- Name: unq_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collecting_methods
    ADD CONSTRAINT unq_collecting_methods UNIQUE (method_indexed);


--
-- TOC entry 4658 (class 2606 OID 17578)
-- Name: unq_collecting_tools; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collecting_tools
    ADD CONSTRAINT unq_collecting_tools UNIQUE (tool_indexed);


--
-- TOC entry 4504 (class 2606 OID 17048)
-- Name: unq_collections; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT unq_collections UNIQUE (institution_ref, path, code);


--
-- TOC entry 4510 (class 2606 OID 17081)
-- Name: unq_collections_rights; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY collections_rights
    ADD CONSTRAINT unq_collections_rights UNIQUE (collection_ref, user_ref);


--
-- TOC entry 4457 (class 2606 OID 16835)
-- Name: unq_expeditions; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY expeditions
    ADD CONSTRAINT unq_expeditions UNIQUE (name_indexed, expedition_from_date, expedition_from_date_mask, expedition_to_date, expedition_to_date_mask);


--
-- TOC entry 4407 (class 2606 OID 16718)
-- Name: unq_ext_links; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY ext_links
    ADD CONSTRAINT unq_ext_links UNIQUE (referenced_relation, record_id, url);


--
-- TOC entry 4680 (class 2606 OID 17662)
-- Name: unq_flat_dict; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY flat_dict
    ADD CONSTRAINT unq_flat_dict UNIQUE (dict_value, dict_field, referenced_relation, dict_depend);


--
-- TOC entry 4443 (class 2606 OID 16805)
-- Name: unq_identifications; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY identifications
    ADD CONSTRAINT unq_identifications UNIQUE (referenced_relation, record_id, notion_concerned, notion_date, value_defined_indexed);


--
-- TOC entry 4594 (class 2606 OID 17389)
-- Name: unq_igs; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY igs
    ADD CONSTRAINT unq_igs UNIQUE (ig_num);


--
-- TOC entry 4646 (class 2606 OID 17515)
-- Name: unq_insurances; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY insurances
    ADD CONSTRAINT unq_insurances UNIQUE (referenced_relation, record_id, date_from, date_to, insurer_ref);


--
-- TOC entry 4588 (class 2606 OID 17364)
-- Name: unq_lithology; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY lithology
    ADD CONSTRAINT unq_lithology UNIQUE (path, name_indexed, level_ref);


--
-- TOC entry 4569 (class 2606 OID 17311)
-- Name: unq_lithostratigraphy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY lithostratigraphy
    ADD CONSTRAINT unq_lithostratigraphy UNIQUE (path, name_indexed, level_ref);


--
-- TOC entry 4708 (class 2606 OID 17862)
-- Name: unq_loan_items; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_items
    ADD CONSTRAINT unq_loan_items UNIQUE (loan_ref, specimen_ref);


--
-- TOC entry 4714 (class 2606 OID 17888)
-- Name: unq_loan_rights; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY loan_rights
    ADD CONSTRAINT unq_loan_rights UNIQUE (loan_ref, user_ref);


--
-- TOC entry 4579 (class 2606 OID 17338)
-- Name: unq_mineralogy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY mineralogy
    ADD CONSTRAINT unq_mineralogy UNIQUE (path, name_indexed, level_ref, code);


--
-- TOC entry 4528 (class 2606 OID 17166)
-- Name: unq_my_saved_searches; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY my_saved_searches
    ADD CONSTRAINT unq_my_saved_searches UNIQUE (user_ref, name);


--
-- TOC entry 4533 (class 2606 OID 17194)
-- Name: unq_my_widgets; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY my_widgets
    ADD CONSTRAINT unq_my_widgets UNIQUE (user_ref, category, group_name);


--
-- TOC entry 4373 (class 2606 OID 16623)
-- Name: unq_people; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people
    ADD CONSTRAINT unq_people UNIQUE (is_physical, gender, formated_name_unique, birth_date, birth_date_mask, end_date, end_date_mask);


--
-- TOC entry 4468 (class 2606 OID 16877)
-- Name: unq_people_languages; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY people_languages
    ADD CONSTRAINT unq_people_languages UNIQUE (people_ref, language_country);


--
-- TOC entry 4394 (class 2606 OID 16684)
-- Name: unq_possible_upper_levels; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY possible_upper_levels
    ADD CONSTRAINT unq_possible_upper_levels UNIQUE (level_ref, level_upper_ref);


--
-- TOC entry 4674 (class 2606 OID 17622)
-- Name: unq_specimen_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimen_collecting_methods
    ADD CONSTRAINT unq_specimen_collecting_methods UNIQUE (specimen_ref, collecting_method_ref);


--
-- TOC entry 4663 (class 2606 OID 17588)
-- Name: unq_specimen_collecting_tools; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY specimen_collecting_tools
    ADD CONSTRAINT unq_specimen_collecting_tools UNIQUE (specimen_ref, collecting_tool_ref);


--
-- TOC entry 4694 (class 2606 OID 17790)
-- Name: unq_staging_collecting_methods; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY staging_collecting_methods
    ADD CONSTRAINT unq_staging_collecting_methods UNIQUE (staging_ref, collecting_method_ref);


--
-- TOC entry 4543 (class 2606 OID 17242)
-- Name: unq_synonym; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY classification_synonymies
    ADD CONSTRAINT unq_synonym UNIQUE (referenced_relation, record_id, group_id);


--
-- TOC entry 4419 (class 2606 OID 16749)
-- Name: unq_tag_groups; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY tag_groups
    ADD CONSTRAINT unq_tag_groups UNIQUE (gtu_ref, group_name_indexed, sub_group_name_indexed);


--
-- TOC entry 4552 (class 2606 OID 17259)
-- Name: unq_taxonomy; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY taxonomy
    ADD CONSTRAINT unq_taxonomy UNIQUE (path, name_indexed, level_ref);


--
-- TOC entry 4462 (class 2606 OID 16856)
-- Name: unq_users; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT unq_users UNIQUE (is_physical, gender, formated_name_unique, birth_date, birth_date_mask);


--
-- TOC entry 4496 (class 2606 OID 17022)
-- Name: unq_users_login_infos; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_login_infos
    ADD CONSTRAINT unq_users_login_infos UNIQUE (user_ref, login_type);


--
-- TOC entry 4498 (class 2606 OID 17024)
-- Name: unq_users_login_infos_user_name; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY users_login_infos
    ADD CONSTRAINT unq_users_login_infos_user_name UNIQUE (user_name, login_type);


--
-- TOC entry 4450 (class 2606 OID 16818)
-- Name: unq_vernacular_names; Type: CONSTRAINT; Schema: darwin2; Owner: darwin2; Tablespace: 
--

ALTER TABLE ONLY vernacular_names
    ADD CONSTRAINT unq_vernacular_names UNIQUE (referenced_relation, record_id, community_indexed, name_indexed);


--
-- TOC entry 4750 (class 2606 OID 715090)
-- Name: vmap0_world_boundaries_pkey; Type: CONSTRAINT; Schema: darwin2; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY vmap0_world_boundaries
    ADD CONSTRAINT vmap0_world_boundaries_pkey PRIMARY KEY (gid);


--
-- TOC entry 4724 (class 1259 OID 18428)
-- Name: idx_bibliography_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_bibliography_type ON bibliography USING btree (type);


--
-- TOC entry 4730 (class 1259 OID 18427)
-- Name: idx_catalogue_bibliography_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_bibliography_referenced_record ON catalogue_bibliography USING btree (referenced_relation, record_id);


--
-- TOC entry 4379 (class 1259 OID 18328)
-- Name: idx_catalogue_people_people_order_by; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_people_people_order_by ON catalogue_people USING btree (order_by);


--
-- TOC entry 4380 (class 1259 OID 18329)
-- Name: idx_catalogue_people_people_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_people_people_ref ON catalogue_people USING btree (people_ref);


--
-- TOC entry 4381 (class 1259 OID 18327)
-- Name: idx_catalogue_people_people_sub_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_people_people_sub_type ON catalogue_people USING btree (people_sub_type);


--
-- TOC entry 4382 (class 1259 OID 18326)
-- Name: idx_catalogue_people_people_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_people_people_type ON catalogue_people USING btree (people_type);


--
-- TOC entry 4383 (class 1259 OID 18330)
-- Name: idx_catalogue_people_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_people_referenced_record ON catalogue_people USING btree (referenced_relation, record_id);


--
-- TOC entry 4374 (class 1259 OID 18338)
-- Name: idx_catalogue_relationships_relations; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_catalogue_relationships_relations ON catalogue_relationships USING btree (referenced_relation, record_id_1, relationship_type);


--
-- TOC entry 4553 (class 1259 OID 18294)
-- Name: idx_chronostratigraphy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_chronostratigraphy_level_ref ON chronostratigraphy USING btree (level_ref);


--
-- TOC entry 4554 (class 1259 OID 18339)
-- Name: idx_chronostratigraphy_lower_bound; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_chronostratigraphy_lower_bound ON chronostratigraphy USING btree ((COALESCE(lower_bound, ((-4600))::numeric)));


--
-- TOC entry 4555 (class 1259 OID 18295)
-- Name: idx_chronostratigraphy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_chronostratigraphy_parent_ref ON chronostratigraphy USING btree (parent_ref);


--
-- TOC entry 4556 (class 1259 OID 18340)
-- Name: idx_chronostratigraphy_upper_bound; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_chronostratigraphy_upper_bound ON chronostratigraphy USING btree ((COALESCE(upper_bound, (1)::numeric)));


--
-- TOC entry 4534 (class 1259 OID 18341)
-- Name: idx_classification_keywords_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_classification_keywords_referenced_record ON classification_keywords USING btree (referenced_relation, record_id);


--
-- TOC entry 4537 (class 1259 OID 18342)
-- Name: idx_classification_synonymies_grouping; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_classification_synonymies_grouping ON classification_synonymies USING btree (group_id, is_basionym);


--
-- TOC entry 4538 (class 1259 OID 18343)
-- Name: idx_classification_synonymies_order_by; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_classification_synonymies_order_by ON classification_synonymies USING btree (group_name, order_by);


--
-- TOC entry 4539 (class 1259 OID 18344)
-- Name: idx_classification_synonymies_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_classification_synonymies_referenced_record ON classification_synonymies USING btree (referenced_relation, record_id, group_id);


--
-- TOC entry 4632 (class 1259 OID 18349)
-- Name: idx_codes_code_num; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_codes_code_num ON codes USING btree (code_num) WHERE (NOT (code_num IS NULL));


--
-- TOC entry 4633 (class 1259 OID 18350)
-- Name: idx_codes_full_code_indexed_btree; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_codes_full_code_indexed_btree ON codes USING btree (full_code_indexed);


--
-- TOC entry 4634 (class 1259 OID 18351)
-- Name: idx_codes_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_codes_referenced_record ON codes USING btree (referenced_relation, record_id);


--
-- TOC entry 4664 (class 1259 OID 18352)
-- Name: idx_collecting_methods_method_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collecting_methods_method_indexed ON collecting_methods USING btree (method_indexed);


--
-- TOC entry 4653 (class 1259 OID 18353)
-- Name: idx_collecting_tools_tool_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collecting_tools_tool_indexed ON collecting_tools USING btree (tool_indexed);


--
-- TOC entry 4520 (class 1259 OID 18354)
-- Name: idx_collection_maintenance_action; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collection_maintenance_action ON collection_maintenance USING btree (action_observation);


--
-- TOC entry 4521 (class 1259 OID 18355)
-- Name: idx_collection_maintenance_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collection_maintenance_referenced_record ON collection_maintenance USING btree (referenced_relation, record_id);


--
-- TOC entry 4522 (class 1259 OID 18291)
-- Name: idx_collection_maintenance_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collection_maintenance_user_ref ON collection_maintenance USING btree (people_ref);


--
-- TOC entry 4499 (class 1259 OID 18287)
-- Name: idx_collections_main_manager_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collections_main_manager_ref ON collections USING btree (main_manager_ref);


--
-- TOC entry 4500 (class 1259 OID 18288)
-- Name: idx_collections_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collections_parent_ref ON collections USING btree (parent_ref);


--
-- TOC entry 4505 (class 1259 OID 18290)
-- Name: idx_collections_rights_db_user_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collections_rights_db_user_type ON collections_rights USING btree (db_user_type);


--
-- TOC entry 4506 (class 1259 OID 18289)
-- Name: idx_collections_rights_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_collections_rights_user_ref ON collections_rights USING btree (user_ref);


--
-- TOC entry 4395 (class 1259 OID 18356)
-- Name: idx_comments_notion_concerned; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_comments_notion_concerned ON comments USING btree (notion_concerned);


--
-- TOC entry 4396 (class 1259 OID 18357)
-- Name: idx_comments_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_comments_referenced_record ON comments USING btree (referenced_relation, record_id);


--
-- TOC entry 4595 (class 1259 OID 18440)
-- Name: idx_darwin_flat_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_darwin_flat_gtu_code ON specimens USING gin (gtu_code public.gin_trgm_ops);


--
-- TOC entry 4451 (class 1259 OID 18413)
-- Name: idx_expeditions_expedition_from_date; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_expeditions_expedition_from_date ON expeditions USING btree (expedition_from_date, expedition_from_date_mask);


--
-- TOC entry 4452 (class 1259 OID 18414)
-- Name: idx_expeditions_expedition_to_date; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_expeditions_expedition_to_date ON expeditions USING btree (expedition_to_date, expedition_to_date_mask);


--
-- TOC entry 4403 (class 1259 OID 18424)
-- Name: idx_ext_links_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_ext_links_referenced_record ON ext_links USING btree (referenced_relation, record_id);


--
-- TOC entry 4408 (class 1259 OID 18408)
-- Name: idx_gin_gtu_tags_values; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_gtu_tags_values ON gtu USING gin (tag_values_indexed);


--
-- TOC entry 4469 (class 1259 OID 18407)
-- Name: idx_gin_multimedia_search_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_multimedia_search_indexed ON multimedia USING gin (search_indexed public.gin_trgm_ops);


--
-- TOC entry 4596 (class 1259 OID 18436)
-- Name: idx_gin_specimens_gtu_country_tag_indexed_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_specimens_gtu_country_tag_indexed_indexed ON specimens USING gin (gtu_country_tag_indexed);


--
-- TOC entry 4597 (class 1259 OID 18435)
-- Name: idx_gin_specimens_gtu_tag_values_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_specimens_gtu_tag_values_indexed ON specimens USING gin (gtu_tag_values_indexed);


--
-- TOC entry 4598 (class 1259 OID 18453)
-- Name: idx_gin_specimens_spec_coll_ids; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_specimens_spec_coll_ids ON specimens USING gin (spec_coll_ids);


--
-- TOC entry 4599 (class 1259 OID 18454)
-- Name: idx_gin_specimens_spec_don_sel_ids; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_specimens_spec_don_sel_ids ON specimens USING gin (spec_don_sel_ids);


--
-- TOC entry 4600 (class 1259 OID 18452)
-- Name: idx_gin_specimens_spec_ident_ids; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_specimens_spec_ident_ids ON specimens USING gin (spec_ident_ids);


--
-- TOC entry 4725 (class 1259 OID 18406)
-- Name: idx_gin_trgm_bibliography_title; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_bibliography_title ON bibliography USING gist (title_indexed public.gist_trgm_ops);


--
-- TOC entry 4557 (class 1259 OID 18402)
-- Name: idx_gin_trgm_chronostratigraphy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_chronostratigraphy_naming ON chronostratigraphy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4397 (class 1259 OID 18397)
-- Name: idx_gin_trgm_comments_comment; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_comments_comment ON comments USING gin (comment public.gin_trgm_ops);


--
-- TOC entry 4398 (class 1259 OID 18429)
-- Name: idx_gin_trgm_comments_comment_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_comments_comment_indexed ON comments USING gin (comment_indexed public.gin_trgm_ops);


--
-- TOC entry 4453 (class 1259 OID 18398)
-- Name: idx_gin_trgm_expeditions_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_expeditions_name ON expeditions USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4580 (class 1259 OID 18430)
-- Name: idx_gin_trgm_lithology_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_lithology_name_indexed ON lithology USING btree (name_indexed);


--
-- TOC entry 4581 (class 1259 OID 18405)
-- Name: idx_gin_trgm_lithology_naming; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_lithology_naming ON lithology USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4562 (class 1259 OID 18403)
-- Name: idx_gin_trgm_lithostratigraphy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_lithostratigraphy_naming ON lithostratigraphy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4570 (class 1259 OID 18404)
-- Name: idx_gin_trgm_mineralogy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_mineralogy_naming ON mineralogy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4367 (class 1259 OID 18399)
-- Name: idx_gin_trgm_people_formated_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_people_formated_name ON people USING gin (formated_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4601 (class 1259 OID 18448)
-- Name: idx_gin_trgm_specimens_expedition_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_specimens_expedition_name_indexed ON specimens USING gin (expedition_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4602 (class 1259 OID 18451)
-- Name: idx_gin_trgm_specimens_ig_num; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_specimens_ig_num ON specimens USING gin (ig_num_indexed public.gin_trgm_ops);


--
-- TOC entry 4603 (class 1259 OID 18449)
-- Name: idx_gin_trgm_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_specimens_taxon_name_indexed ON specimens USING gin (taxon_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4604 (class 1259 OID 18450)
-- Name: idx_gin_trgm_specimens_taxon_path; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_specimens_taxon_path ON specimens USING gin (taxon_path public.gin_trgm_ops);


--
-- TOC entry 4544 (class 1259 OID 18431)
-- Name: idx_gin_trgm_taxonomy_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_taxonomy_name_indexed ON taxonomy USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 4545 (class 1259 OID 18401)
-- Name: idx_gin_trgm_taxonomy_naming; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_taxonomy_naming ON taxonomy USING gin (name_indexed public.gin_trgm_ops);


--
-- TOC entry 4458 (class 1259 OID 18400)
-- Name: idx_gin_trgm_users_formated_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gin_trgm_users_formated_name ON users USING gin (formated_name_indexed public.gin_trgm_ops);


--
-- TOC entry 4605 (class 1259 OID 18437)
-- Name: idx_gist_specimens_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gist_specimens_gtu_location ON specimens USING gist (gtu_location);


--
-- TOC entry 4409 (class 1259 OID 18278)
-- Name: idx_gtu_code; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gtu_code ON gtu USING btree (code);


--
-- TOC entry 4410 (class 1259 OID 18279)
-- Name: idx_gtu_location; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_gtu_location ON gtu USING gist (location);


--
-- TOC entry 4437 (class 1259 OID 18360)
-- Name: idx_identifications_determination_status; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_identifications_determination_status ON identifications USING btree (determination_status) WHERE ((determination_status)::text <> ''::text);


--
-- TOC entry 4438 (class 1259 OID 18358)
-- Name: idx_identifications_notion_concerned; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_identifications_notion_concerned ON identifications USING btree (notion_concerned);


--
-- TOC entry 4439 (class 1259 OID 18359)
-- Name: idx_identifications_order_by; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_identifications_order_by ON identifications USING btree (order_by);


--
-- TOC entry 4589 (class 1259 OID 18412)
-- Name: idx_igs_ig_date; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_igs_ig_date ON igs USING btree (ig_date, ig_date_mask);


--
-- TOC entry 4590 (class 1259 OID 18361)
-- Name: idx_igs_ig_num_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_igs_ig_num_indexed ON igs USING btree (ig_num_indexed text_pattern_ops);


--
-- TOC entry 4681 (class 1259 OID 18323)
-- Name: idx_imports_collection_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_imports_collection_ref ON imports USING btree (collection_ref);


--
-- TOC entry 4511 (class 1259 OID 18425)
-- Name: idx_informative_workflow_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_informative_workflow_referenced_record ON informative_workflow USING btree (referenced_relation, record_id);


--
-- TOC entry 4512 (class 1259 OID 18396)
-- Name: idx_informative_workflow_user_status; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_informative_workflow_user_status ON informative_workflow USING btree (user_ref, status);


--
-- TOC entry 4640 (class 1259 OID 18317)
-- Name: idx_insurances_contact_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_insurances_contact_ref ON insurances USING btree (contact_ref);


--
-- TOC entry 4641 (class 1259 OID 18362)
-- Name: idx_insurances_insurance_currency; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_insurances_insurance_currency ON insurances USING btree (insurance_currency);


--
-- TOC entry 4642 (class 1259 OID 18316)
-- Name: idx_insurances_insurer_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_insurances_insurer_ref ON insurances USING btree (insurer_ref);


--
-- TOC entry 4582 (class 1259 OID 18300)
-- Name: idx_lithology_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithology_level_ref ON lithology USING btree (level_ref);


--
-- TOC entry 4583 (class 1259 OID 18432)
-- Name: idx_lithology_name_order_by_txt_op; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithology_name_order_by_txt_op ON lithology USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 4584 (class 1259 OID 18301)
-- Name: idx_lithology_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithology_parent_ref ON lithology USING btree (parent_ref);


--
-- TOC entry 4563 (class 1259 OID 18296)
-- Name: idx_lithostratigraphy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithostratigraphy_level_ref ON lithostratigraphy USING btree (level_ref);


--
-- TOC entry 4564 (class 1259 OID 18433)
-- Name: idx_lithostratigraphy_name_order_by_txt_op; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithostratigraphy_name_order_by_txt_op ON lithostratigraphy USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 4565 (class 1259 OID 18297)
-- Name: idx_lithostratigraphy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_lithostratigraphy_parent_ref ON lithostratigraphy USING btree (parent_ref);


--
-- TOC entry 4702 (class 1259 OID 18417)
-- Name: idx_loan_items_ig_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_items_ig_ref ON loan_items USING btree (ig_ref);


--
-- TOC entry 4703 (class 1259 OID 18416)
-- Name: idx_loan_items_loan_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_items_loan_ref ON loan_items USING btree (loan_ref);


--
-- TOC entry 4704 (class 1259 OID 18418)
-- Name: idx_loan_items_part_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_items_part_ref ON loan_items USING btree (specimen_ref);


--
-- TOC entry 4709 (class 1259 OID 18419)
-- Name: idx_loan_rights_ig_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_rights_ig_ref ON loan_rights USING btree (loan_ref);


--
-- TOC entry 4710 (class 1259 OID 18420)
-- Name: idx_loan_rights_part_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_rights_part_ref ON loan_rights USING btree (user_ref);


--
-- TOC entry 4715 (class 1259 OID 18422)
-- Name: idx_loan_status_loan_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_status_loan_ref ON loan_status USING btree (loan_ref);


--
-- TOC entry 4716 (class 1259 OID 18423)
-- Name: idx_loan_status_loan_ref_is_last; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_status_loan_ref_is_last ON loan_status USING btree (loan_ref, is_last);


--
-- TOC entry 4717 (class 1259 OID 18421)
-- Name: idx_loan_status_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_loan_status_user_ref ON loan_status USING btree (user_ref);


--
-- TOC entry 4665 (class 1259 OID 18411)
-- Name: idx_method_trgm; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_method_trgm ON collecting_methods USING gin (method public.gin_trgm_ops);


--
-- TOC entry 4571 (class 1259 OID 18363)
-- Name: idx_mineralogy_code; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_mineralogy_code ON mineralogy USING btree (upper((code)::text));


--
-- TOC entry 4572 (class 1259 OID 18364)
-- Name: idx_mineralogy_cristal_system; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_mineralogy_cristal_system ON mineralogy USING btree (cristal_system) WHERE ((cristal_system)::text <> ''::text);


--
-- TOC entry 4573 (class 1259 OID 18298)
-- Name: idx_mineralogy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_mineralogy_level_ref ON mineralogy USING btree (level_ref);


--
-- TOC entry 4574 (class 1259 OID 18434)
-- Name: idx_mineralogy_name_order_by_txt_op; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_mineralogy_name_order_by_txt_op ON mineralogy USING btree (name_indexed text_pattern_ops);


--
-- TOC entry 4575 (class 1259 OID 18299)
-- Name: idx_mineralogy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_mineralogy_parent_ref ON mineralogy USING btree (parent_ref);


--
-- TOC entry 4470 (class 1259 OID 18426)
-- Name: idx_multimedia_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_multimedia_referenced_record ON multimedia USING btree (referenced_relation, record_id);


--
-- TOC entry 4529 (class 1259 OID 18365)
-- Name: idx_my_widgets_user_category; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_my_widgets_user_category ON my_widgets USING btree (user_ref, category, group_name);


--
-- TOC entry 4481 (class 1259 OID 18368)
-- Name: idx_people_addresses_country; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_addresses_country ON people_addresses USING btree (country);


--
-- TOC entry 4482 (class 1259 OID 18284)
-- Name: idx_people_addresses_person_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_addresses_person_user_ref ON people_addresses USING btree (person_user_ref);


--
-- TOC entry 4477 (class 1259 OID 18369)
-- Name: idx_people_comm_comm_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_comm_comm_type ON people_comm USING btree (comm_type);


--
-- TOC entry 4478 (class 1259 OID 18283)
-- Name: idx_people_comm_person_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_comm_person_user_ref ON people_comm USING btree (person_user_ref);


--
-- TOC entry 4368 (class 1259 OID 18367)
-- Name: idx_people_family_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_family_name ON people USING btree (family_name);


--
-- TOC entry 4463 (class 1259 OID 18370)
-- Name: idx_people_languages_language_country; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_languages_language_country ON people_languages USING btree (language_country);


--
-- TOC entry 4464 (class 1259 OID 18280)
-- Name: idx_people_languages_people_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_languages_people_ref ON people_languages USING btree (people_ref);


--
-- TOC entry 4473 (class 1259 OID 18281)
-- Name: idx_people_relationships_person_1_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_relationships_person_1_ref ON people_relationships USING btree (person_1_ref);


--
-- TOC entry 4474 (class 1259 OID 18282)
-- Name: idx_people_relationships_person_2_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_relationships_person_2_ref ON people_relationships USING btree (person_2_ref);


--
-- TOC entry 4369 (class 1259 OID 18366)
-- Name: idx_people_sub_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_people_sub_type ON people USING btree (sub_type) WHERE (NOT (sub_type IS NULL));


--
-- TOC entry 4392 (class 1259 OID 18277)
-- Name: idx_possible_upper_levels_level_upper_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_possible_upper_levels_level_upper_ref ON possible_upper_levels USING btree (level_upper_ref);


--
-- TOC entry 4428 (class 1259 OID 18333)
-- Name: idx_properties_property_lower_value; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_lower_value ON properties USING btree (lower_value);


--
-- TOC entry 4429 (class 1259 OID 18336)
-- Name: idx_properties_property_lower_value_unified; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_lower_value_unified ON properties USING btree (lower_value_unified);


--
-- TOC entry 4430 (class 1259 OID 18331)
-- Name: idx_properties_property_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_type ON properties USING btree (property_type);


--
-- TOC entry 4431 (class 1259 OID 18332)
-- Name: idx_properties_property_unit; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_unit ON properties USING btree (property_unit);


--
-- TOC entry 4432 (class 1259 OID 18334)
-- Name: idx_properties_property_upper_value; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_upper_value ON properties USING btree (upper_value);


--
-- TOC entry 4433 (class 1259 OID 18337)
-- Name: idx_properties_property_upper_value_unified; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_property_upper_value_unified ON properties USING btree (upper_value_unified);


--
-- TOC entry 4434 (class 1259 OID 18335)
-- Name: idx_properties_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_properties_referenced_record ON properties USING btree (referenced_relation, record_id);


--
-- TOC entry 4635 (class 1259 OID 229266)
-- Name: idx_rmca_codes; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_rmca_codes ON codes USING btree (code);


--
-- TOC entry 4670 (class 1259 OID 18314)
-- Name: idx_specimen_collecting_methods_method_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimen_collecting_methods_method_ref ON specimen_collecting_methods USING btree (collecting_method_ref);


--
-- TOC entry 4659 (class 1259 OID 18315)
-- Name: idx_specimen_collecting_tools_tool_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimen_collecting_tools_tool_ref ON specimen_collecting_tools USING btree (collecting_tool_ref);


--
-- TOC entry 4606 (class 1259 OID 18306)
-- Name: idx_specimens_chrono_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_chrono_ref ON specimens USING btree (chrono_ref) WHERE (chrono_ref <> 0);


--
-- TOC entry 4607 (class 1259 OID 18446)
-- Name: idx_specimens_collection_is_public; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_collection_is_public ON specimens USING btree (collection_is_public);


--
-- TOC entry 4608 (class 1259 OID 18447)
-- Name: idx_specimens_collection_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_collection_name ON specimens USING btree (collection_name);


--
-- TOC entry 4609 (class 1259 OID 18302)
-- Name: idx_specimens_expedition_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_expedition_ref ON specimens USING btree (expedition_ref) WHERE (expedition_ref <> 0);


--
-- TOC entry 4610 (class 1259 OID 18444)
-- Name: idx_specimens_gtu_from_date; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_gtu_from_date ON specimens USING btree (gtu_from_date);


--
-- TOC entry 4611 (class 1259 OID 18441)
-- Name: idx_specimens_gtu_from_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_gtu_from_date_mask ON specimens USING btree (gtu_from_date_mask);


--
-- TOC entry 4612 (class 1259 OID 18303)
-- Name: idx_specimens_gtu_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_gtu_ref ON specimens USING btree (gtu_ref) WHERE (gtu_ref <> 0);


--
-- TOC entry 4613 (class 1259 OID 18443)
-- Name: idx_specimens_gtu_to_date; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_gtu_to_date ON specimens USING btree (gtu_to_date);


--
-- TOC entry 4614 (class 1259 OID 18442)
-- Name: idx_specimens_gtu_to_date_mask; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_gtu_to_date_mask ON specimens USING btree (gtu_to_date_mask);


--
-- TOC entry 4615 (class 1259 OID 18318)
-- Name: idx_specimens_ig_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_ig_ref ON specimens USING btree (ig_ref);


--
-- TOC entry 4616 (class 1259 OID 18305)
-- Name: idx_specimens_litho_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_litho_ref ON specimens USING btree (litho_ref) WHERE (litho_ref <> 0);


--
-- TOC entry 4617 (class 1259 OID 18307)
-- Name: idx_specimens_lithology_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_lithology_ref ON specimens USING btree (lithology_ref) WHERE (lithology_ref <> 0);


--
-- TOC entry 4618 (class 1259 OID 18308)
-- Name: idx_specimens_mineral_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_mineral_ref ON specimens USING btree (mineral_ref) WHERE (mineral_ref <> 0);


--
-- TOC entry 4647 (class 1259 OID 18311)
-- Name: idx_specimens_relationships_mineral_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_relationships_mineral_ref ON specimens_relationships USING btree (mineral_ref);


--
-- TOC entry 4648 (class 1259 OID 18312)
-- Name: idx_specimens_relationships_specimen_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_relationships_specimen_ref ON specimens_relationships USING btree (specimen_ref);


--
-- TOC entry 4649 (class 1259 OID 18313)
-- Name: idx_specimens_relationships_specimen_related_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_relationships_specimen_related_ref ON specimens_relationships USING btree (specimen_related_ref);


--
-- TOC entry 4650 (class 1259 OID 18310)
-- Name: idx_specimens_relationships_taxon_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_relationships_taxon_ref ON specimens_relationships USING btree (taxon_ref);


--
-- TOC entry 4619 (class 1259 OID 18376)
-- Name: idx_specimens_rock_form; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_rock_form ON specimens USING btree (rock_form);


--
-- TOC entry 4620 (class 1259 OID 18378)
-- Name: idx_specimens_room; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_room ON specimens USING btree (room) WHERE (NOT (room IS NULL));


--
-- TOC entry 4621 (class 1259 OID 18372)
-- Name: idx_specimens_sex; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_sex ON specimens USING btree (sex) WHERE ((sex)::text <> ALL ((ARRAY['undefined'::character varying, 'unknown'::character varying])::text[]));


--
-- TOC entry 4622 (class 1259 OID 18380)
-- Name: idx_specimens_shelf; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_shelf ON specimens USING btree (shelf) WHERE (NOT (shelf IS NULL));


--
-- TOC entry 4623 (class 1259 OID 18375)
-- Name: idx_specimens_social_status; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_social_status ON specimens USING btree (social_status) WHERE ((social_status)::text <> 'not applicable'::text);


--
-- TOC entry 4624 (class 1259 OID 18373)
-- Name: idx_specimens_stage; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_stage ON specimens USING btree (stage) WHERE ((stage)::text <> ALL ((ARRAY['undefined'::character varying, 'unknown'::character varying])::text[]));


--
-- TOC entry 4625 (class 1259 OID 18374)
-- Name: idx_specimens_state; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_state ON specimens USING btree (state) WHERE ((state)::text <> 'not applicable'::text);


--
-- TOC entry 4626 (class 1259 OID 18439)
-- Name: idx_specimens_station_visible; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_station_visible ON specimens USING btree (station_visible);


--
-- TOC entry 4627 (class 1259 OID 18445)
-- Name: idx_specimens_taxon_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_taxon_name_indexed ON specimens USING btree (taxon_name_indexed);


--
-- TOC entry 4628 (class 1259 OID 18304)
-- Name: idx_specimens_taxon_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_taxon_ref ON specimens USING btree (taxon_ref) WHERE (taxon_ref <> 0);


--
-- TOC entry 4629 (class 1259 OID 18371)
-- Name: idx_specimens_type_search; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_specimens_type_search ON specimens USING btree (type_search) WHERE ((type_search)::text <> 'specimen'::text);


--
-- TOC entry 4735 (class 1259 OID 108347)
-- Name: idx_staging_catalogue; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_catalogue ON staging_catalogue USING btree (level_ref, fulltoindex(name));


--
-- TOC entry 4736 (class 1259 OID 108348)
-- Name: idx_staging_catalogue_catalogue_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_catalogue_catalogue_ref ON staging_catalogue USING btree (import_ref, parent_ref) WHERE (catalogue_ref IS NOT NULL);


--
-- TOC entry 4737 (class 1259 OID 108349)
-- Name: idx_staging_catalogue_filter; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_catalogue_filter ON staging_catalogue USING btree (import_ref, name, level_ref);


--
-- TOC entry 4738 (class 1259 OID 108350)
-- Name: idx_staging_catalogue_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_catalogue_parent_ref ON staging_catalogue USING btree (parent_ref) WHERE (parent_ref IS NOT NULL);


--
-- TOC entry 4739 (class 1259 OID 108351)
-- Name: idx_staging_catalogue_parent_updated; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_catalogue_parent_updated ON staging_catalogue USING btree (parent_updated);


--
-- TOC entry 4684 (class 1259 OID 18324)
-- Name: idx_staging_import_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_import_ref ON staging USING btree (import_ref);


--
-- TOC entry 4697 (class 1259 OID 18325)
-- Name: idx_staging_people_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_staging_people_record ON staging_people USING btree (record_id, referenced_relation);


--
-- TOC entry 4413 (class 1259 OID 18388)
-- Name: idx_tag_groups_group_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tag_groups_group_name_indexed ON tag_groups USING btree (group_name_indexed);


--
-- TOC entry 4414 (class 1259 OID 18389)
-- Name: idx_tag_groups_group_name_indexed_txt_op; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tag_groups_group_name_indexed_txt_op ON tag_groups USING btree (group_name_indexed text_pattern_ops);


--
-- TOC entry 4415 (class 1259 OID 18390)
-- Name: idx_tag_groups_sub_group_name; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tag_groups_sub_group_name ON tag_groups USING btree (sub_group_name);


--
-- TOC entry 4420 (class 1259 OID 18320)
-- Name: idx_tags_group_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_group_ref ON tags USING btree (group_ref);


--
-- TOC entry 4421 (class 1259 OID 18392)
-- Name: idx_tags_group_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_group_type ON tags USING btree (group_type);


--
-- TOC entry 4422 (class 1259 OID 18319)
-- Name: idx_tags_gtu_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_gtu_ref ON tags USING btree (gtu_ref);


--
-- TOC entry 4423 (class 1259 OID 18393)
-- Name: idx_tags_sub_group_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_sub_group_type ON tags USING btree (sub_group_type);


--
-- TOC entry 4424 (class 1259 OID 18391)
-- Name: idx_tags_tag_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_tag_indexed ON tags USING btree (tag_indexed);


--
-- TOC entry 4425 (class 1259 OID 18409)
-- Name: idx_tags_trgm; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tags_trgm ON tags USING gin (tag public.gin_trgm_ops);


--
-- TOC entry 4546 (class 1259 OID 18292)
-- Name: idx_taxonomy_level_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_taxonomy_level_ref ON taxonomy USING btree (level_ref);


--
-- TOC entry 4547 (class 1259 OID 18293)
-- Name: idx_taxonomy_parent_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_taxonomy_parent_ref ON taxonomy USING btree (parent_ref);


--
-- TOC entry 4548 (class 1259 OID 18387)
-- Name: idx_taxonomy_path; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_taxonomy_path ON taxonomy USING btree (path text_pattern_ops);


--
-- TOC entry 4654 (class 1259 OID 18410)
-- Name: idx_tool_trgm; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_tool_trgm ON collecting_tools USING gin (tool public.gin_trgm_ops);


--
-- TOC entry 4489 (class 1259 OID 18394)
-- Name: idx_users_addresses_country; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_addresses_country ON users_addresses USING btree (country);


--
-- TOC entry 4490 (class 1259 OID 18286)
-- Name: idx_users_addresses_person_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_addresses_person_user_ref ON users_addresses USING btree (person_user_ref);


--
-- TOC entry 4485 (class 1259 OID 18395)
-- Name: idx_users_comm_comm_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_comm_comm_type ON users_comm USING btree (comm_type);


--
-- TOC entry 4486 (class 1259 OID 18285)
-- Name: idx_users_comm_person_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_comm_person_user_ref ON users_comm USING btree (person_user_ref);


--
-- TOC entry 4515 (class 1259 OID 18322)
-- Name: idx_users_tracking_action; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_tracking_action ON users_tracking USING btree (action);


--
-- TOC entry 4516 (class 1259 OID 18415)
-- Name: idx_users_tracking_modification_date_time; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_tracking_modification_date_time ON users_tracking USING btree (modification_date_time DESC);


--
-- TOC entry 4517 (class 1259 OID 18321)
-- Name: idx_users_tracking_user_ref; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_users_tracking_user_ref ON users_tracking USING btree (user_ref);


--
-- TOC entry 4444 (class 1259 OID 18345)
-- Name: idx_vernacular_names_community_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_vernacular_names_community_indexed ON vernacular_names USING btree (community_indexed);


--
-- TOC entry 4445 (class 1259 OID 18346)
-- Name: idx_vernacular_names_name_indexed; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_vernacular_names_name_indexed ON vernacular_names USING btree (name_indexed);


--
-- TOC entry 4446 (class 1259 OID 18347)
-- Name: idx_vernacular_names_referenced_record; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX idx_vernacular_names_referenced_record ON vernacular_names USING btree (referenced_relation, record_id);


--
-- TOC entry 4751 (class 1259 OID 715102)
-- Name: specimens_detect_wrong_countrie_gix; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX specimens_detect_wrong_countrie_gix ON specimens_detect_wrong_countries USING gist (geom);


--
-- TOC entry 4744 (class 1259 OID 693845)
-- Name: storage_institution_idex; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX storage_institution_idex ON storage_parts USING btree (institution_ref);


--
-- TOC entry 4745 (class 1259 OID 693846)
-- Name: storage_object_idx; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX storage_object_idx ON storage_parts USING btree (object_name);


--
-- TOC entry 4746 (class 1259 OID 250539)
-- Name: storage_parts_id_idx; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX storage_parts_id_idx ON storage_parts USING btree (id);


--
-- TOC entry 4747 (class 1259 OID 250540)
-- Name: storage_parts_specimen_ref_idx; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX storage_parts_specimen_ref_idx ON storage_parts USING btree (specimen_ref);


--
-- TOC entry 4748 (class 1259 OID 693847)
-- Name: storage_type; Type: INDEX; Schema: darwin2; Owner: darwin2; Tablespace: 
--

CREATE INDEX storage_type ON storage_parts USING btree (specimen_status);


--
-- TOC entry 5147 (class 2618 OID 231056)
-- Name: _RETURN; Type: RULE; Schema: darwin2; Owner: darwin2
--

CREATE RULE "_RETURN" AS ON SELECT TO v_loan_details_for_pentaho DO INSTEAD SELECT a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, array_agg(d.lower_value) AS array_agg, btrim((((COALESCE(((b.specimen_count_males_min)::text || ' M '::text), ''::text) || COALESCE(((b.specimen_count_females_min)::text || ' F '::text), ''::text)) || COALESCE(((b.specimen_count_juveniles_min)::text || ' Juv '::text), ''::text)) || COALESCE(('Tot.:'::text || (b.specimen_count_min)::text), ''::text))) AS detail_loan, b.type, f.category, f.specimen_part, f.specimen_status, btrim(((((COALESCE(f.category, ''::character varying))::text || COALESCE((', '::text || NULLIF(replace((f.specimen_part)::text, 'specimen'::text, ''::text), ''::text)), ''::text)) || COALESCE((', '::text || NULLIF((f.specimen_status)::text, ''::text)), ''::text)) || COALESCE((', '::text || NULLIF(replace((b.type)::text, 'specimen'::text, ''::text), ''::text)), ''::text))) AS loan_remarks, e.id AS loan_id, e.name AS loan_name, b.collection_ref FROM (((((loan_items a JOIN specimens b ON ((a.specimen_ref = b.id))) JOIN codes c ON ((((b.id = c.record_id) AND ((c.referenced_relation)::text = 'specimens'::text)) AND ((c.code_category)::text = 'main'::text)))) LEFT JOIN properties d ON (((b.id = d.record_id) AND ((d.referenced_relation)::text = 'specimens'::text)))) JOIN loans e ON ((a.loan_ref = e.id))) LEFT JOIN storage_parts f ON ((b.id = f.specimen_ref))) GROUP BY a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, b.type, f.category, f.specimen_part, f.specimen_status, e.id, b.collection_ref, b.specimen_count_males_min, b.specimen_count_females_min, b.specimen_count_juveniles_min, b.specimen_count_min;


--
-- TOC entry 5160 (class 2618 OID 251543)
-- Name: _RETURN; Type: RULE; Schema: darwin2; Owner: darwin2
--

CREATE RULE "_RETURN" AS ON SELECT TO v_rmca_report_ig_ichtyo_2_localities DO INSTEAD SELECT igs.id, specimens.gtu_country_tag_value AS country, gtu.id AS id_gtu, btrim(regexp_replace(replace(array_to_string(tags.tags, (', '::character varying)::text), (specimens.gtu_country_tag_value)::text, (''::character varying)::text), ('^,'::character varying)::text, (''::character varying)::text)) AS locality, CASE WHEN (upper((gtu.coordinates_source)::text) = ('DD'::character varying)::text) THEN COALESCE(((((((abs(round((gtu.latitude)::numeric, 5)))::character varying)::text || (CASE WHEN (gtu.latitude < (0)::double precision) THEN 'S'::character varying ELSE 'N'::character varying END)::text) || (' '::character varying)::text) || ((abs(round((gtu.longitude)::numeric, 5)))::character varying)::text) || (CASE WHEN (gtu.longitude < (0)::double precision) THEN 'W'::character varying ELSE 'E'::character varying END)::text), (''::character varying)::text) WHEN (upper((gtu.coordinates_source)::text) = ('DMS'::character varying)::text) THEN (((((((COALESCE((((gtu.latitude_dms_degree)::character varying)::text || ('°'::character varying)::text), (''::character varying)::text) || COALESCE(((((gtu.latitude_dms_minutes)::numeric)::character varying)::text || (''''::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.latitude_dms_seconds)::numeric)::character varying)::text || ('"'::character varying)::text), (''::character varying)::text)) || (CASE WHEN (gtu.latitude_dms_direction >= 1) THEN 'N'::character varying WHEN (gtu.latitude_dms_direction <= (-1)) THEN 'S'::character varying ELSE ''::character varying END)::text) || COALESCE((((', '::character varying)::text || ((gtu.longitude_dms_degree)::character varying)::text) || ('°'::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.longitude_dms_minutes)::numeric)::character varying)::text || (''''::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.longitude_dms_seconds)::numeric)::character varying)::text || ('"'::character varying)::text), (''::character varying)::text)) || (CASE WHEN (gtu.longitude_dms_direction >= 1) THEN 'E'::character varying WHEN (gtu.longitude_dms_direction <= (-1)) THEN '>'::character varying ELSE ''::character varying END)::text) WHEN (upper((gtu.coordinates_source)::text) = ('UTM'::character varying)::text) THEN COALESCE(((((((', '::character varying)::text || ((gtu.latitude_utm)::character varying)::text) || (' '::character varying)::text) || ((gtu.longitude_utm)::character varying)::text) || (' '::character varying)::text) || (gtu.utm_zone)::text), (''::character varying)::text) ELSE (((((((COALESCE((((gtu.latitude_dms_degree)::character varying)::text || ('°'::character varying)::text), (''::character varying)::text) || COALESCE(((((gtu.latitude_dms_minutes)::numeric)::character varying)::text || (''''::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.latitude_dms_seconds)::numeric)::character varying)::text || ('"'::character varying)::text), (''::character varying)::text)) || (CASE WHEN (gtu.latitude_dms_direction >= 1) THEN 'N'::character varying WHEN (gtu.latitude_dms_direction <= (-1)) THEN 'S'::character varying ELSE ''::character varying END)::text) || COALESCE((((', '::character varying)::text || ((gtu.longitude_dms_degree)::character varying)::text) || ('°'::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.longitude_dms_minutes)::numeric)::character varying)::text || (''''::character varying)::text), (''::character varying)::text)) || COALESCE(((((gtu.longitude_dms_seconds)::numeric)::character varying)::text || ('"'::character varying)::text), (''::character varying)::text)) || (CASE WHEN (gtu.longitude_dms_direction >= 1) THEN 'E'::character varying WHEN (gtu.longitude_dms_direction <= (-1)) THEN '>'::character varying ELSE ''::character varying END)::text) END AS coordinates_text, min(fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask)) AS date_min, NULLIF(max(fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask)), min(fct_mask_date(specimens.gtu_from_date, specimens.gtu_from_date_mask))) AS date_max, string_agg(replace((v_rmca_count_ichtyology_by_number.code)::text, ((igs.ig_num)::text || ('.'::character varying)::text), (''::character varying)::text), (' '::character varying)::text) AS collections_numbers, sum(v_rmca_count_ichtyology_by_number.counter) AS sum FROM ((((igs LEFT JOIN specimens ON ((igs.id = specimens.ig_ref))) LEFT JOIN v_rmca_count_ichtyology_by_number ON ((specimens.id = v_rmca_count_ichtyology_by_number.record_id))) LEFT JOIN gtu ON ((specimens.gtu_ref = gtu.id))) LEFT JOIN (SELECT array_agg(tags.tag ORDER BY tags.group_ref) AS tags, array_agg(tags.group_type ORDER BY tags.group_ref) AS group_types, tags.gtu_ref FROM tags GROUP BY tags.gtu_ref) tags ON ((gtu.id = tags.gtu_ref))) GROUP BY igs.id, specimens.gtu_country_tag_value, gtu.id, tags.tags, tags.group_types ORDER BY specimens.gtu_country_tag_value, tags.tags, tags.group_types;


--
-- TOC entry 5163 (class 2618 OID 691293)
-- Name: _RETURN; Type: RULE; Schema: darwin2; Owner: darwin2
--

CREATE RULE "_RETURN" AS ON SELECT TO v_loans_pentaho_receivers DO INSTEAD SELECT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, string_agg(((c.id)::character varying)::text, ', '::text ORDER BY c.id) AS receiver_id, string_agg((c.formated_name)::text, ', '::text ORDER BY c.id) AS receiver, string_agg(COALESCE((g.formated_name)::text, (h.entry)::text), ', '::text ORDER BY c.id) AS institution_receiver, btrim(NULLIF(string_agg(btrim((((((COALESCE(''::text, (NULLIF((h.entry)::text, ''::text) || ', '::text), ''::text) || COALESCE((NULLIF((h.extended_address)::text, ''::text) || ', '::text), ''::text)) || COALESCE((NULLIF((h.locality)::text, ''::text) || ', '::text), ''::text)) || COALESCE((NULLIF((h.po_box)::text, ''::text) || ', '::text), ''::text)) || COALESCE((NULLIF((h.region)::text, ''::text) || ', '::text), ''::text)) || COALESCE((NULLIF((h.zip_code)::text, ''::text) || ', '::text), ''::text))), ', '::text ORDER BY c.id), ', '::text), ','::text) AS address_institution, btrim(NULLIF(string_agg(COALESCE(NULLIF((h.country)::text, ''::text), ''::text), ', '::text ORDER BY c.id), ', '::text), ','::text) AS country_institution, e.id AS sender_id, e.formated_name AS sender FROM (((((((loans a LEFT JOIN catalogue_people b ON ((((a.id = b.record_id) AND ((b.referenced_relation)::text = 'loans'::text)) AND ((b.people_type)::text = 'receiver'::text)))) LEFT JOIN people c ON ((c.id = b.people_ref))) LEFT JOIN catalogue_people d ON ((((a.id = d.record_id) AND ((d.referenced_relation)::text = 'loans'::text)) AND ((d.people_type)::text = 'sender'::text)))) LEFT JOIN people e ON ((e.id = d.people_ref))) LEFT JOIN people_relationships f ON (((((f.relationship_type)::text = 'works for'::text) OR ((f.relationship_type)::text = 'belongs to'::text)) AND (c.id = f.person_2_ref)))) LEFT JOIN people g ON ((f.person_1_ref = g.id))) LEFT JOIN people_addresses h ON ((COALESCE(g.id, c.id) = h.person_user_ref))) GROUP BY a.id, e.id;


--
-- TOC entry 4917 (class 2620 OID 18233)
-- Name: fct_chk_peopleismoral_collections; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_chk_peopleismoral_collections AFTER INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_chk_peopleismoral();


--
-- TOC entry 4992 (class 2620 OID 18248)
-- Name: fct_cpy_trg_del_dict_codes; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_codes AFTER DELETE OR UPDATE ON codes FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4929 (class 2620 OID 18249)
-- Name: fct_cpy_trg_del_dict_collection_maintenance; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_collection_maintenance AFTER DELETE OR UPDATE ON collection_maintenance FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4876 (class 2620 OID 18250)
-- Name: fct_cpy_trg_del_dict_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_identifications AFTER DELETE OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4998 (class 2620 OID 18253)
-- Name: fct_cpy_trg_del_dict_insurances; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_insurances AFTER DELETE OR UPDATE ON insurances FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 5020 (class 2620 OID 18259)
-- Name: fct_cpy_trg_del_dict_loan_status; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_loan_status AFTER DELETE OR UPDATE ON loan_status FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4965 (class 2620 OID 18254)
-- Name: fct_cpy_trg_del_dict_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_mineralogy AFTER DELETE OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4842 (class 2620 OID 18251)
-- Name: fct_cpy_trg_del_dict_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_people AFTER DELETE OR UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4905 (class 2620 OID 18252)
-- Name: fct_cpy_trg_del_dict_people_addresses; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_people_addresses AFTER DELETE OR UPDATE ON people_addresses FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4870 (class 2620 OID 18260)
-- Name: fct_cpy_trg_del_dict_properties; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_properties AFTER DELETE OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4978 (class 2620 OID 18255)
-- Name: fct_cpy_trg_del_dict_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_specimens AFTER DELETE OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 5002 (class 2620 OID 18256)
-- Name: fct_cpy_trg_del_dict_specimens_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_specimens_relationships AFTER DELETE OR UPDATE ON specimens_relationships FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 5027 (class 2620 OID 693880)
-- Name: fct_cpy_trg_del_dict_storage_parts; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_storage_parts AFTER DELETE OR UPDATE ON storage_parts FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4868 (class 2620 OID 18261)
-- Name: fct_cpy_trg_del_dict_tag_groups; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_tag_groups AFTER DELETE OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4895 (class 2620 OID 18257)
-- Name: fct_cpy_trg_del_dict_users; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_users AFTER DELETE OR UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4907 (class 2620 OID 18258)
-- Name: fct_cpy_trg_del_dict_users_addresses; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_del_dict_users_addresses AFTER DELETE OR UPDATE ON users_addresses FOR EACH ROW EXECUTE PROCEDURE trg_del_dict();


--
-- TOC entry 4993 (class 2620 OID 18234)
-- Name: fct_cpy_trg_ins_update_dict_codes; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_codes AFTER INSERT OR UPDATE ON codes FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4928 (class 2620 OID 18235)
-- Name: fct_cpy_trg_ins_update_dict_collection_maintenance; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_collection_maintenance AFTER INSERT OR UPDATE ON collection_maintenance FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4877 (class 2620 OID 18236)
-- Name: fct_cpy_trg_ins_update_dict_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_identifications AFTER INSERT OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4997 (class 2620 OID 18239)
-- Name: fct_cpy_trg_ins_update_dict_insurances; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_insurances AFTER INSERT OR UPDATE ON insurances FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 5019 (class 2620 OID 18245)
-- Name: fct_cpy_trg_ins_update_dict_loan_status; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_loan_status AFTER INSERT OR UPDATE ON loan_status FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4964 (class 2620 OID 18240)
-- Name: fct_cpy_trg_ins_update_dict_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_mineralogy AFTER INSERT OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4843 (class 2620 OID 18237)
-- Name: fct_cpy_trg_ins_update_dict_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_people AFTER INSERT OR UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4904 (class 2620 OID 18238)
-- Name: fct_cpy_trg_ins_update_dict_people_addresses; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_people_addresses AFTER INSERT OR UPDATE ON people_addresses FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4871 (class 2620 OID 18246)
-- Name: fct_cpy_trg_ins_update_dict_properties; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_properties AFTER INSERT OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4979 (class 2620 OID 18241)
-- Name: fct_cpy_trg_ins_update_dict_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens AFTER INSERT OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 5001 (class 2620 OID 18242)
-- Name: fct_cpy_trg_ins_update_dict_specimens_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens_relationships AFTER INSERT OR UPDATE ON specimens_relationships FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 5026 (class 2620 OID 250541)
-- Name: fct_cpy_trg_ins_update_dict_storage_parts; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_storage_parts AFTER INSERT OR UPDATE ON storage_parts FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4869 (class 2620 OID 18247)
-- Name: fct_cpy_trg_ins_update_dict_tag_groups; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_tag_groups AFTER INSERT OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4894 (class 2620 OID 18243)
-- Name: fct_cpy_trg_ins_update_dict_users; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_users AFTER INSERT OR UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 4906 (class 2620 OID 18244)
-- Name: fct_cpy_trg_ins_update_dict_users_addresses; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER fct_cpy_trg_ins_update_dict_users_addresses AFTER INSERT OR UPDATE ON users_addresses FOR EACH ROW EXECUTE PROCEDURE trg_ins_update_dict();


--
-- TOC entry 5016 (class 2620 OID 18264)
-- Name: trg_add_status_history; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_add_status_history AFTER INSERT ON loans FOR EACH ROW EXECUTE PROCEDURE fct_auto_insert_status_history();


--
-- TOC entry 5025 (class 2620 OID 108353)
-- Name: trg_catalogue_import_keywords_update; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_catalogue_import_keywords_update AFTER INSERT OR DELETE OR UPDATE ON staging_catalogue FOR EACH ROW EXECUTE PROCEDURE fct_catalogue_import_keywords_update();


--
-- TOC entry 4918 (class 2620 OID 18138)
-- Name: trg_chk_canupdatecollectionsrights; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_canupdatecollectionsrights BEFORE UPDATE ON collections_rights FOR EACH ROW EXECUTE PROCEDURE fct_chk_canupdatecollectionsrights();


--
-- TOC entry 4922 (class 2620 OID 18226)
-- Name: trg_chk_is_last_informative_workflow; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_is_last_informative_workflow BEFORE INSERT ON informative_workflow FOR EACH ROW EXECUTE PROCEDURE fct_remove_last_flag();


--
-- TOC entry 5021 (class 2620 OID 18263)
-- Name: trg_chk_is_last_loan_status; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_is_last_loan_status BEFORE INSERT ON loan_status FOR EACH ROW EXECUTE PROCEDURE fct_remove_last_flag_loan();


--
-- TOC entry 4912 (class 2620 OID 18143)
-- Name: trg_chk_parentcollinstitution; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_parentcollinstitution BEFORE INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_chk_parentcollinstitution();


--
-- TOC entry 4949 (class 2620 OID 18228)
-- Name: trg_chk_possible_upper_level_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_possible_upper_level_chronostratigraphy AFTER INSERT OR UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();


--
-- TOC entry 4972 (class 2620 OID 18231)
-- Name: trg_chk_possible_upper_level_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_possible_upper_level_lithology AFTER INSERT OR UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();


--
-- TOC entry 4956 (class 2620 OID 18229)
-- Name: trg_chk_possible_upper_level_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_possible_upper_level_lithostratigraphy AFTER INSERT OR UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();


--
-- TOC entry 4963 (class 2620 OID 18230)
-- Name: trg_chk_possible_upper_level_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_possible_upper_level_mineralogy AFTER INSERT OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();


--
-- TOC entry 4935 (class 2620 OID 251765)
-- Name: trg_chk_possible_upper_level_taxonomy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_possible_upper_level_taxonomy AFTER INSERT OR UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_trg_chk_possible_upper_level();


--
-- TOC entry 4990 (class 2620 OID 18222)
-- Name: trg_chk_ref_record_catalogue_codes; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_catalogue_codes AFTER INSERT OR UPDATE ON codes FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4852 (class 2620 OID 18213)
-- Name: trg_chk_ref_record_catalogue_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_catalogue_people AFTER INSERT OR UPDATE ON catalogue_people FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4934 (class 2620 OID 18221)
-- Name: trg_chk_ref_record_classification_synonymies; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_classification_synonymies AFTER INSERT OR UPDATE ON classification_synonymies FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4927 (class 2620 OID 18219)
-- Name: trg_chk_ref_record_collection_maintenance; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_collection_maintenance AFTER INSERT OR UPDATE ON collection_maintenance FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4854 (class 2620 OID 18214)
-- Name: trg_chk_ref_record_comments; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_comments AFTER INSERT OR UPDATE ON comments FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4859 (class 2620 OID 18215)
-- Name: trg_chk_ref_record_ext_links; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_ext_links AFTER INSERT OR UPDATE ON ext_links FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4878 (class 2620 OID 18217)
-- Name: trg_chk_ref_record_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_identifications AFTER INSERT OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4923 (class 2620 OID 18225)
-- Name: trg_chk_ref_record_informative_workflow; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_informative_workflow AFTER INSERT OR UPDATE ON informative_workflow FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4996 (class 2620 OID 18223)
-- Name: trg_chk_ref_record_insurances; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_insurances AFTER INSERT OR UPDATE ON insurances FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4872 (class 2620 OID 18216)
-- Name: trg_chk_ref_record_properties; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_properties AFTER INSERT OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4850 (class 2620 OID 18224)
-- Name: trg_chk_ref_record_relationship_catalogue_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_relationship_catalogue_relationships AFTER INSERT OR UPDATE ON catalogue_relationships FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecordrelationship();


--
-- TOC entry 4851 (class 2620 OID 18220)
-- Name: trg_chk_ref_record_template_table_record_ref; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_template_table_record_ref AFTER INSERT OR UPDATE ON template_table_record_ref FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4886 (class 2620 OID 18218)
-- Name: trg_chk_ref_record_vernacular_names; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_ref_record_vernacular_names AFTER INSERT OR UPDATE ON vernacular_names FOR EACH ROW EXECUTE PROCEDURE fct_chk_referencedrecord();


--
-- TOC entry 4980 (class 2620 OID 18142)
-- Name: trg_chk_specimencollectionallowed; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_specimencollectionallowed BEFORE INSERT OR DELETE OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_chk_specimencollectionallowed();


--
-- TOC entry 4981 (class 2620 OID 18265)
-- Name: trg_chk_specimens_not_loaned; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_specimens_not_loaned BEFORE DELETE ON specimens FOR EACH ROW EXECUTE PROCEDURE chk_specimens_not_loaned();


--
-- TOC entry 4946 (class 2620 OID 18152)
-- Name: trg_chk_upper_level_for_childrens_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_upper_level_for_childrens_chronostratigraphy AFTER UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();


--
-- TOC entry 4969 (class 2620 OID 18154)
-- Name: trg_chk_upper_level_for_childrens_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_upper_level_for_childrens_lithology AFTER UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();


--
-- TOC entry 4953 (class 2620 OID 18156)
-- Name: trg_chk_upper_level_for_childrens_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_upper_level_for_childrens_lithostratigraphy AFTER UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();


--
-- TOC entry 4960 (class 2620 OID 18158)
-- Name: trg_chk_upper_level_for_childrens_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_upper_level_for_childrens_mineralogy AFTER UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();


--
-- TOC entry 4936 (class 2620 OID 18160)
-- Name: trg_chk_upper_level_for_childrens_taxonomy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_chk_upper_level_for_childrens_taxonomy AFTER UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_chk_upper_level_for_childrens();


--
-- TOC entry 4879 (class 2620 OID 18116)
-- Name: trg_clr_identifiers_in_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_identifiers_in_flat BEFORE DELETE ON identifications FOR EACH ROW EXECUTE PROCEDURE fct_clear_identifiers_in_flat();


--
-- TOC entry 5023 (class 2620 OID 18126)
-- Name: trg_clr_referencerecord_bibliography; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_bibliography AFTER DELETE OR UPDATE ON bibliography FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4944 (class 2620 OID 18129)
-- Name: trg_clr_referencerecord_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_chronostratigraphy AFTER DELETE OR UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4909 (class 2620 OID 18125)
-- Name: trg_clr_referencerecord_collections; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_collections AFTER DELETE OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4888 (class 2620 OID 18120)
-- Name: trg_clr_referencerecord_expeditions; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_expeditions AFTER DELETE OR UPDATE ON expeditions FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4860 (class 2620 OID 18115)
-- Name: trg_clr_referencerecord_gtu; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_gtu AFTER DELETE OR UPDATE ON gtu FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4880 (class 2620 OID 18117)
-- Name: trg_clr_referencerecord_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_identifications AFTER DELETE OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4974 (class 2620 OID 18124)
-- Name: trg_clr_referencerecord_igs; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_igs AFTER DELETE OR UPDATE ON igs FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4994 (class 2620 OID 18118)
-- Name: trg_clr_referencerecord_insurances; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_insurances AFTER DELETE OR UPDATE ON insurances FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4967 (class 2620 OID 18132)
-- Name: trg_clr_referencerecord_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_lithology AFTER DELETE OR UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4951 (class 2620 OID 18130)
-- Name: trg_clr_referencerecord_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_lithostratigraphy AFTER DELETE OR UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 5018 (class 2620 OID 18136)
-- Name: trg_clr_referencerecord_loan_items; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_loan_items AFTER DELETE OR UPDATE ON loan_items FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 5015 (class 2620 OID 18135)
-- Name: trg_clr_referencerecord_loans; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_loans AFTER DELETE OR UPDATE ON loans FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4958 (class 2620 OID 18131)
-- Name: trg_clr_referencerecord_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_mineralogy AFTER DELETE OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4897 (class 2620 OID 18123)
-- Name: trg_clr_referencerecord_multimedia; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_multimedia AFTER DELETE OR UPDATE ON multimedia FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4925 (class 2620 OID 18127)
-- Name: trg_clr_referencerecord_mysavedsearches; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_mysavedsearches AFTER DELETE OR UPDATE ON collection_maintenance FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4844 (class 2620 OID 18121)
-- Name: trg_clr_referencerecord_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_people AFTER DELETE OR UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4982 (class 2620 OID 18133)
-- Name: trg_clr_referencerecord_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_specimens AFTER DELETE OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4999 (class 2620 OID 18134)
-- Name: trg_clr_referencerecord_specimens_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_specimens_relationships AFTER DELETE OR UPDATE ON specimens_relationships FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 5009 (class 2620 OID 18114)
-- Name: trg_clr_referencerecord_staging; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_staging AFTER DELETE OR UPDATE ON staging FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 5011 (class 2620 OID 18268)
-- Name: trg_clr_referencerecord_staging_info; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_staging_info AFTER DELETE OR UPDATE ON staging_info FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4937 (class 2620 OID 18128)
-- Name: trg_clr_referencerecord_taxa; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_taxa AFTER DELETE OR UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4891 (class 2620 OID 18122)
-- Name: trg_clr_referencerecord_users; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_users AFTER DELETE OR UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4884 (class 2620 OID 18119)
-- Name: trg_clr_referencerecord_vernacularnames; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_referencerecord_vernacularnames AFTER DELETE OR UPDATE ON vernacular_names FOR EACH ROW EXECUTE PROCEDURE fct_clear_referencedrecord();


--
-- TOC entry 4983 (class 2620 OID 18211)
-- Name: trg_clr_specialstatus_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_clr_specialstatus_specimens BEFORE INSERT OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_clr_specialstatus();


--
-- TOC entry 4899 (class 2620 OID 18267)
-- Name: trg_cpy_deleted_file; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_deleted_file AFTER DELETE ON multimedia FOR EACH ROW EXECUTE PROCEDURE fct_cpy_deleted_file();


--
-- TOC entry 4845 (class 2620 OID 18145)
-- Name: trg_cpy_formattedname; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_formattedname BEFORE INSERT OR UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE fct_cpy_formattedname();


--
-- TOC entry 4892 (class 2620 OID 18146)
-- Name: trg_cpy_formattedname; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_formattedname BEFORE INSERT OR UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE fct_cpy_formattedname();


--
-- TOC entry 5022 (class 2620 OID 18111)
-- Name: trg_cpy_fulltoindex_bibliography; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_bibliography BEFORE INSERT OR UPDATE ON bibliography FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4943 (class 2620 OID 18094)
-- Name: trg_cpy_fulltoindex_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_chronostratigraphy BEFORE INSERT OR UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4930 (class 2620 OID 18105)
-- Name: trg_cpy_fulltoindex_classification_keywords; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_classification_keywords BEFORE INSERT OR UPDATE ON classification_keywords FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4991 (class 2620 OID 18101)
-- Name: trg_cpy_fulltoindex_codes; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_codes BEFORE INSERT OR UPDATE ON codes FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 5006 (class 2620 OID 18108)
-- Name: trg_cpy_fulltoindex_collecting_methods; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_collecting_methods BEFORE INSERT OR UPDATE ON collecting_methods FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 5003 (class 2620 OID 18109)
-- Name: trg_cpy_fulltoindex_collecting_tools; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_collecting_tools BEFORE INSERT OR UPDATE ON collecting_tools FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4908 (class 2620 OID 18104)
-- Name: trg_cpy_fulltoindex_collection; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_collection BEFORE INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4855 (class 2620 OID 18092)
-- Name: trg_cpy_fulltoindex_comments; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_comments BEFORE INSERT OR UPDATE ON comments FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4887 (class 2620 OID 18095)
-- Name: trg_cpy_fulltoindex_expeditions; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_expeditions BEFORE INSERT OR UPDATE ON expeditions FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4857 (class 2620 OID 18091)
-- Name: trg_cpy_fulltoindex_ext_links; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_ext_links BEFORE INSERT OR UPDATE ON ext_links FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4881 (class 2620 OID 18096)
-- Name: trg_cpy_fulltoindex_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_identifications BEFORE INSERT OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4973 (class 2620 OID 18107)
-- Name: trg_cpy_fulltoindex_igs; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_igs BEFORE INSERT OR UPDATE ON igs FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4966 (class 2620 OID 18097)
-- Name: trg_cpy_fulltoindex_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_lithology BEFORE INSERT OR UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4950 (class 2620 OID 18098)
-- Name: trg_cpy_fulltoindex_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_lithostratigraphy BEFORE INSERT OR UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 5014 (class 2620 OID 18110)
-- Name: trg_cpy_fulltoindex_loans; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_loans BEFORE INSERT OR UPDATE ON loans FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4957 (class 2620 OID 18099)
-- Name: trg_cpy_fulltoindex_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_mineralogy BEFORE INSERT OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4896 (class 2620 OID 18100)
-- Name: trg_cpy_fulltoindex_multimedia; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_multimedia BEFORE INSERT OR UPDATE ON multimedia FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4873 (class 2620 OID 18093)
-- Name: trg_cpy_fulltoindex_properties; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_properties BEFORE INSERT OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4984 (class 2620 OID 18112)
-- Name: trg_cpy_fulltoindex_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_specimens BEFORE INSERT OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 5028 (class 2620 OID 250542)
-- Name: trg_cpy_fulltoindex_storage_parts; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_storage_parts BEFORE INSERT OR UPDATE ON storage_parts FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4864 (class 2620 OID 18102)
-- Name: trg_cpy_fulltoindex_taggroups; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_taggroups BEFORE INSERT OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4938 (class 2620 OID 18103)
-- Name: trg_cpy_fulltoindex_taxa; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_taxa BEFORE INSERT OR UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4883 (class 2620 OID 18106)
-- Name: trg_cpy_fulltoindex_vernacularnames; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_fulltoindex_vernacularnames BEFORE INSERT OR UPDATE ON vernacular_names FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


--
-- TOC entry 4865 (class 2620 OID 18113)
-- Name: trg_cpy_gtutags_taggroups; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_gtutags_taggroups AFTER INSERT OR DELETE OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE fct_cpy_gtutags();


--
-- TOC entry 4985 (class 2620 OID 18266)
-- Name: trg_cpy_ig_to_loan_items; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_ig_to_loan_items AFTER UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_cpy_ig_to_loan_items();


--
-- TOC entry 4861 (class 2620 OID 18197)
-- Name: trg_cpy_location; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_location BEFORE INSERT OR UPDATE ON gtu FOR EACH ROW EXECUTE PROCEDURE fct_cpy_location();


--
-- TOC entry 4945 (class 2620 OID 18151)
-- Name: trg_cpy_path_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_chronostratigraphy BEFORE INSERT OR UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path_catalogs();


--
-- TOC entry 4914 (class 2620 OID 18147)
-- Name: trg_cpy_path_collections; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_collections BEFORE INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path();


--
-- TOC entry 4968 (class 2620 OID 18153)
-- Name: trg_cpy_path_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_lithology BEFORE INSERT OR UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path_catalogs();


--
-- TOC entry 4952 (class 2620 OID 18155)
-- Name: trg_cpy_path_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_lithostratigraphy BEFORE INSERT OR UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path_catalogs();


--
-- TOC entry 4959 (class 2620 OID 18157)
-- Name: trg_cpy_path_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_mineralogy BEFORE INSERT OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path_catalogs();


--
-- TOC entry 4900 (class 2620 OID 18148)
-- Name: trg_cpy_path_peoplerelationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_peoplerelationships BEFORE INSERT OR UPDATE ON people_relationships FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path();


--
-- TOC entry 4939 (class 2620 OID 18159)
-- Name: trg_cpy_path_taxonomy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_path_taxonomy BEFORE INSERT OR UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_cpy_path_catalogs();


--
-- TOC entry 4874 (class 2620 OID 18196)
-- Name: trg_cpy_unified_values; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE fct_cpy_unified_values();


--
-- TOC entry 4910 (class 2620 OID 18137)
-- Name: trg_cpy_updatecollectionrights; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_updatecollectionrights AFTER INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updatecollectionrights();


--
-- TOC entry 4913 (class 2620 OID 18144)
-- Name: trg_cpy_updatecollinstitutioncascade; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_updatecollinstitutioncascade AFTER UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updatecollinstitutioncascade();


--
-- TOC entry 4920 (class 2620 OID 18141)
-- Name: trg_cpy_updatemywidgetscollrights; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_updatemywidgetscollrights AFTER DELETE OR UPDATE ON collections_rights FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updatemywidgetscoll();


--
-- TOC entry 4919 (class 2620 OID 18139)
-- Name: trg_cpy_updateuserrights; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_updateuserrights AFTER INSERT OR DELETE OR UPDATE ON collections_rights FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updateuserrights();


--
-- TOC entry 4911 (class 2620 OID 18140)
-- Name: trg_cpy_updateuserrightscollections; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_cpy_updateuserrightscollections AFTER INSERT OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_cpy_updateuserrights();


--
-- TOC entry 4942 (class 2620 OID 715989)
-- Name: trg_fct_rmca_update_child_of_taxon_protected; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_fct_rmca_update_child_of_taxon_protected BEFORE UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_rmca_update_child_of_taxon_protected();


--
-- TOC entry 4989 (class 2620 OID 18187)
-- Name: trg_insert_auto_code; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_insert_auto_code BEFORE INSERT ON codes FOR EACH ROW EXECUTE PROCEDURE check_auto_increment_code_in_spec();


--
-- TOC entry 5017 (class 2620 OID 232536)
-- Name: trg_insert_auto_code_loan; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_insert_auto_code_loan BEFORE INSERT ON loans FOR EACH ROW EXECUTE PROCEDURE check_auto_increment_code_in_loan();


--
-- TOC entry 4849 (class 2620 OID 18198)
-- Name: trg_nbr_in_relation; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_nbr_in_relation BEFORE INSERT OR UPDATE ON catalogue_relationships FOR EACH ROW EXECUTE PROCEDURE fct_nbr_in_relation();


--
-- TOC entry 4933 (class 2620 OID 18199)
-- Name: trg_nbr_in_synonym; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_nbr_in_synonym AFTER INSERT OR UPDATE ON classification_synonymies FOR EACH ROW EXECUTE PROCEDURE fct_nbr_in_synonym();


--
-- TOC entry 4924 (class 2620 OID 18227)
-- Name: trg_reset_last_flag_informative_workflow; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_reset_last_flag_informative_workflow AFTER DELETE ON informative_workflow FOR EACH ROW EXECUTE PROCEDURE fct_informative_reset_last_flag();


--
-- TOC entry 4847 (class 2620 OID 230885)
-- Name: trg_rmca_check_people_before_delete; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_rmca_check_people_before_delete BEFORE DELETE ON people FOR EACH ROW EXECUTE PROCEDURE fct_rmca_check_people_before_delete();


--
-- TOC entry 4977 (class 2620 OID 250669)
-- Name: trg_rmca_delete_specimen_storage_enable; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_rmca_delete_specimen_storage_enable BEFORE DELETE ON specimens FOR EACH ROW EXECUTE PROCEDURE trg_rmca_delete_specimen_storage();


--
-- TOC entry 5029 (class 2620 OID 715825)
-- Name: trg_rmca_trk_log_table_storage_parts; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_rmca_trk_log_table_storage_parts AFTER INSERT OR DELETE OR UPDATE ON storage_parts FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5024 (class 2620 OID 18181)
-- Name: trg_trk_log_table_bibliography; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_bibliography AFTER INSERT OR DELETE OR UPDATE ON bibliography FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4848 (class 2620 OID 18161)
-- Name: trg_trk_log_table_catalogue_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_catalogue_relationships AFTER INSERT OR DELETE OR UPDATE ON catalogue_relationships FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4947 (class 2620 OID 18191)
-- Name: trg_trk_log_table_chronostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_chronostratigraphy AFTER INSERT OR DELETE OR UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4931 (class 2620 OID 18162)
-- Name: trg_trk_log_table_classification_keywords; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_classification_keywords AFTER INSERT OR DELETE OR UPDATE ON classification_keywords FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4932 (class 2620 OID 18163)
-- Name: trg_trk_log_table_classification_synonymies; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_classification_synonymies AFTER INSERT OR DELETE OR UPDATE ON classification_synonymies FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4988 (class 2620 OID 18186)
-- Name: trg_trk_log_table_codes; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_codes AFTER INSERT OR DELETE OR UPDATE ON codes FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5007 (class 2620 OID 18174)
-- Name: trg_trk_log_table_collecting_methods; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_collecting_methods AFTER INSERT OR DELETE OR UPDATE ON collecting_methods FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5004 (class 2620 OID 18172)
-- Name: trg_trk_log_table_collecting_tools; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_collecting_tools AFTER INSERT OR DELETE OR UPDATE ON collecting_tools FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4926 (class 2620 OID 18184)
-- Name: trg_trk_log_table_collection_maintenance; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_collection_maintenance AFTER INSERT OR DELETE OR UPDATE ON collection_maintenance FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4915 (class 2620 OID 18183)
-- Name: trg_trk_log_table_collections; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_collections AFTER INSERT OR DELETE OR UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4921 (class 2620 OID 18170)
-- Name: trg_trk_log_table_collections_rights; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_collections_rights AFTER INSERT OR DELETE OR UPDATE ON collections_rights FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4856 (class 2620 OID 18176)
-- Name: trg_trk_log_table_comments; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_comments AFTER INSERT OR DELETE OR UPDATE ON comments FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4889 (class 2620 OID 18180)
-- Name: trg_trk_log_table_expeditions; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_expeditions AFTER INSERT OR DELETE OR UPDATE ON expeditions FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4858 (class 2620 OID 18177)
-- Name: trg_trk_log_table_ext_links; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_ext_links AFTER INSERT OR DELETE OR UPDATE ON ext_links FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4862 (class 2620 OID 18178)
-- Name: trg_trk_log_table_gtu; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_gtu AFTER INSERT OR DELETE OR UPDATE ON gtu FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4882 (class 2620 OID 18165)
-- Name: trg_trk_log_table_identifications; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_identifications AFTER INSERT OR DELETE OR UPDATE ON identifications FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4975 (class 2620 OID 18185)
-- Name: trg_trk_log_table_igs; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_igs AFTER INSERT OR DELETE OR UPDATE ON igs FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4995 (class 2620 OID 18188)
-- Name: trg_trk_log_table_insurances; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_insurances AFTER INSERT OR DELETE OR UPDATE ON insurances FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4970 (class 2620 OID 18194)
-- Name: trg_trk_log_table_lithology; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_lithology AFTER INSERT OR DELETE OR UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4954 (class 2620 OID 18192)
-- Name: trg_trk_log_table_lithostratigraphy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_lithostratigraphy AFTER INSERT OR DELETE OR UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4961 (class 2620 OID 18193)
-- Name: trg_trk_log_table_mineralogy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_mineralogy AFTER INSERT OR DELETE OR UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4898 (class 2620 OID 18182)
-- Name: trg_trk_log_table_multimedia; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_multimedia AFTER INSERT OR DELETE OR UPDATE ON multimedia FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4846 (class 2620 OID 18195)
-- Name: trg_trk_log_table_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_people AFTER INSERT OR DELETE OR UPDATE ON people FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4903 (class 2620 OID 18169)
-- Name: trg_trk_log_table_people_addresses; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_people_addresses AFTER INSERT OR DELETE OR UPDATE ON people_addresses FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4902 (class 2620 OID 18168)
-- Name: trg_trk_log_table_people_comm; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_people_comm AFTER INSERT OR DELETE OR UPDATE ON people_comm FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4901 (class 2620 OID 18167)
-- Name: trg_trk_log_table_people_relationships; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_people_relationships AFTER INSERT OR DELETE OR UPDATE ON people_relationships FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4875 (class 2620 OID 18164)
-- Name: trg_trk_log_table_properties; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_properties AFTER INSERT OR DELETE OR UPDATE ON properties FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5008 (class 2620 OID 18175)
-- Name: trg_trk_log_table_specimen_collecting_methods; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_specimen_collecting_methods AFTER INSERT OR DELETE OR UPDATE ON specimen_collecting_methods FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5005 (class 2620 OID 18173)
-- Name: trg_trk_log_table_specimen_collecting_tools; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_specimen_collecting_tools AFTER INSERT OR DELETE OR UPDATE ON specimen_collecting_tools FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4986 (class 2620 OID 18189)
-- Name: trg_trk_log_table_specimens; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_specimens AFTER INSERT OR DELETE OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 5000 (class 2620 OID 18171)
-- Name: trg_trk_log_table_specimens_relationship; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_specimens_relationship AFTER INSERT OR DELETE OR UPDATE ON specimens_relationships FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4866 (class 2620 OID 18179)
-- Name: trg_trk_log_table_tag_groups; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_tag_groups AFTER INSERT OR DELETE OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4940 (class 2620 OID 18190)
-- Name: trg_trk_log_table_taxonomy; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_taxonomy AFTER INSERT OR DELETE OR UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4885 (class 2620 OID 18166)
-- Name: trg_trk_log_table_vernacular_names; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_trk_log_table_vernacular_names AFTER INSERT OR DELETE OR UPDATE ON vernacular_names FOR EACH ROW EXECUTE PROCEDURE fct_trk_log_table();


--
-- TOC entry 4893 (class 2620 OID 18212)
-- Name: trg_unpromotion_remove_cols; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_unpromotion_remove_cols AFTER UPDATE ON users FOR EACH ROW EXECUTE PROCEDURE fct_unpromotion_impact_prefs();


--
-- TOC entry 5010 (class 2620 OID 18149)
-- Name: trg_upd_fields_staging; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_upd_fields_staging BEFORE UPDATE ON staging FOR EACH ROW EXECUTE PROCEDURE fct_upd_staging_fields();


--
-- TOC entry 5012 (class 2620 OID 18269)
-- Name: trg_upd_institution_staging_relationship; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_upd_institution_staging_relationship AFTER UPDATE ON staging_relationship FOR EACH ROW EXECUTE PROCEDURE fct_upd_institution_staging_relationship();


--
-- TOC entry 4853 (class 2620 OID 18262)
-- Name: trg_upd_people_in_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_upd_people_in_flat AFTER INSERT OR DELETE OR UPDATE ON catalogue_people FOR EACH ROW EXECUTE PROCEDURE fct_upd_people_in_flat();


--
-- TOC entry 5013 (class 2620 OID 18150)
-- Name: trg_upd_people_ref_staging_people; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_upd_people_ref_staging_people AFTER UPDATE ON staging_people FOR EACH ROW EXECUTE PROCEDURE fct_upd_people_staging_fields();


--
-- TOC entry 4948 (class 2620 OID 18206)
-- Name: trg_update_chronostratigraphy_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_chronostratigraphy_darwin_flat AFTER UPDATE ON chronostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4916 (class 2620 OID 18201)
-- Name: trg_update_collections_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_collections_darwin_flat AFTER UPDATE ON collections FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4890 (class 2620 OID 18200)
-- Name: trg_update_expeditions_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_expeditions_darwin_flat AFTER UPDATE ON expeditions FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4863 (class 2620 OID 18202)
-- Name: trg_update_gtu_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_gtu_darwin_flat AFTER UPDATE ON gtu FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4976 (class 2620 OID 18204)
-- Name: trg_update_igs_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_igs_darwin_flat AFTER UPDATE ON igs FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4971 (class 2620 OID 18208)
-- Name: trg_update_lithology_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_lithology_darwin_flat AFTER UPDATE ON lithology FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4955 (class 2620 OID 18207)
-- Name: trg_update_lithostratigraphy_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_lithostratigraphy_darwin_flat AFTER UPDATE ON lithostratigraphy FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4962 (class 2620 OID 18209)
-- Name: trg_update_mineralogy_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_mineralogy_darwin_flat AFTER UPDATE ON mineralogy FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4987 (class 2620 OID 18210)
-- Name: trg_update_specimens_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_specimens_darwin_flat BEFORE INSERT OR UPDATE ON specimens FOR EACH ROW EXECUTE PROCEDURE fct_update_specimen_flat();


--
-- TOC entry 4867 (class 2620 OID 18203)
-- Name: trg_update_tag_groups_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_tag_groups_darwin_flat AFTER INSERT OR DELETE OR UPDATE ON tag_groups FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4941 (class 2620 OID 18205)
-- Name: trg_update_taxonomy_darwin_flat; Type: TRIGGER; Schema: darwin2; Owner: darwin2
--

CREATE TRIGGER trg_update_taxonomy_darwin_flat AFTER UPDATE ON taxonomy FOR EACH ROW EXECUTE PROCEDURE fct_update_specimens_flat_related();


--
-- TOC entry 4838 (class 2606 OID 17979)
-- Name: fk_bibliography; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_bibliography
    ADD CONSTRAINT fk_bibliography FOREIGN KEY (bibliography_ref) REFERENCES bibliography(id) ON DELETE CASCADE;


--
-- TOC entry 4783 (class 2606 OID 17286)
-- Name: fk_chronostratigraphy_catalogue_levels; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy
    ADD CONSTRAINT fk_chronostratigraphy_catalogue_levels FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4784 (class 2606 OID 17291)
-- Name: fk_chronostratigraphy_parent_ref_chronostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY chronostratigraphy
    ADD CONSTRAINT fk_chronostratigraphy_parent_ref_chronostratigraphy FOREIGN KEY (parent_ref) REFERENCES chronostratigraphy(id) ON DELETE CASCADE;


--
-- TOC entry 4777 (class 2606 OID 17144)
-- Name: fk_collection_maintenance_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collection_maintenance
    ADD CONSTRAINT fk_collection_maintenance_users FOREIGN KEY (people_ref) REFERENCES people(id);


--
-- TOC entry 4770 (class 2606 OID 17054)
-- Name: fk_collections_collections; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT fk_collections_collections FOREIGN KEY (parent_ref) REFERENCES collections(id) ON DELETE CASCADE;


--
-- TOC entry 4769 (class 2606 OID 17049)
-- Name: fk_collections_institutions; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT fk_collections_institutions FOREIGN KEY (institution_ref) REFERENCES people(id);


--
-- TOC entry 4774 (class 2606 OID 17087)
-- Name: fk_collections_rights_collections; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections_rights
    ADD CONSTRAINT fk_collections_rights_collections FOREIGN KEY (collection_ref) REFERENCES collections(id) ON DELETE CASCADE;


--
-- TOC entry 4773 (class 2606 OID 17082)
-- Name: fk_collections_rights_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections_rights
    ADD CONSTRAINT fk_collections_rights_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4772 (class 2606 OID 17064)
-- Name: fk_collections_staff; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT fk_collections_staff FOREIGN KEY (staff_ref) REFERENCES users(id) ON DELETE SET NULL;


--
-- TOC entry 4771 (class 2606 OID 17059)
-- Name: fk_collections_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY collections
    ADD CONSTRAINT fk_collections_users FOREIGN KEY (main_manager_ref) REFERENCES users(id);


--
-- TOC entry 4812 (class 2606 OID 17678)
-- Name: fk_imports_collections; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY imports
    ADD CONSTRAINT fk_imports_collections FOREIGN KEY (collection_ref) REFERENCES collections(id) ON DELETE CASCADE;


--
-- TOC entry 4813 (class 2606 OID 17683)
-- Name: fk_imports_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY imports
    ADD CONSTRAINT fk_imports_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4775 (class 2606 OID 17107)
-- Name: fk_informative_workflow_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY informative_workflow
    ADD CONSTRAINT fk_informative_workflow_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4801 (class 2606 OID 17521)
-- Name: fk_insurances_contact; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY insurances
    ADD CONSTRAINT fk_insurances_contact FOREIGN KEY (contact_ref) REFERENCES people(id) ON DELETE SET NULL;


--
-- TOC entry 4800 (class 2606 OID 17516)
-- Name: fk_insurances_people; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY insurances
    ADD CONSTRAINT fk_insurances_people FOREIGN KEY (insurer_ref) REFERENCES people(id) ON DELETE SET NULL;


--
-- TOC entry 4790 (class 2606 OID 17370)
-- Name: fk_lithology_catalogue_levels; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology
    ADD CONSTRAINT fk_lithology_catalogue_levels FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4789 (class 2606 OID 17365)
-- Name: fk_lithology_parent_ref_lithology; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithology
    ADD CONSTRAINT fk_lithology_parent_ref_lithology FOREIGN KEY (parent_ref) REFERENCES lithology(id) ON DELETE CASCADE;


--
-- TOC entry 4785 (class 2606 OID 17312)
-- Name: fk_lithostratigraphy_catalogue_levels; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy
    ADD CONSTRAINT fk_lithostratigraphy_catalogue_levels FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4786 (class 2606 OID 17317)
-- Name: fk_lithostratigraphy_parent_ref_lithostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY lithostratigraphy
    ADD CONSTRAINT fk_lithostratigraphy_parent_ref_lithostratigraphy FOREIGN KEY (parent_ref) REFERENCES lithostratigraphy(id) ON DELETE CASCADE;


--
-- TOC entry 4837 (class 2606 OID 17936)
-- Name: fk_loan_history_loan_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_history
    ADD CONSTRAINT fk_loan_history_loan_ref FOREIGN KEY (loan_ref) REFERENCES loans(id) ON DELETE CASCADE;


--
-- TOC entry 4830 (class 2606 OID 17863)
-- Name: fk_loan_items_ig; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_items
    ADD CONSTRAINT fk_loan_items_ig FOREIGN KEY (ig_ref) REFERENCES igs(id);


--
-- TOC entry 4831 (class 2606 OID 17868)
-- Name: fk_loan_items_loan_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_items
    ADD CONSTRAINT fk_loan_items_loan_ref FOREIGN KEY (loan_ref) REFERENCES loans(id);


--
-- TOC entry 4832 (class 2606 OID 17873)
-- Name: fk_loan_items_specimen_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_items
    ADD CONSTRAINT fk_loan_items_specimen_ref FOREIGN KEY (specimen_ref) REFERENCES specimens(id) ON DELETE SET NULL;


--
-- TOC entry 4833 (class 2606 OID 17889)
-- Name: fk_loan_rights_loan_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_rights
    ADD CONSTRAINT fk_loan_rights_loan_ref FOREIGN KEY (loan_ref) REFERENCES loans(id) ON DELETE CASCADE;


--
-- TOC entry 4834 (class 2606 OID 17894)
-- Name: fk_loan_rights_user_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_rights
    ADD CONSTRAINT fk_loan_rights_user_ref FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4835 (class 2606 OID 17914)
-- Name: fk_loan_status_loan_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_status
    ADD CONSTRAINT fk_loan_status_loan_ref FOREIGN KEY (loan_ref) REFERENCES loans(id) ON DELETE CASCADE;


--
-- TOC entry 4836 (class 2606 OID 17919)
-- Name: fk_loan_status_user_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loan_status
    ADD CONSTRAINT fk_loan_status_user_ref FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4829 (class 2606 OID 252534)
-- Name: fk_loans_collections; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY loans
    ADD CONSTRAINT fk_loans_collections FOREIGN KEY (collection_ref) REFERENCES collections(id);


--
-- TOC entry 4787 (class 2606 OID 17339)
-- Name: fk_mineralogy_catalogue_levels; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy
    ADD CONSTRAINT fk_mineralogy_catalogue_levels FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4788 (class 2606 OID 17344)
-- Name: fk_mineralogy_parent_ref_mineralogy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY mineralogy
    ADD CONSTRAINT fk_mineralogy_parent_ref_mineralogy FOREIGN KEY (parent_ref) REFERENCES mineralogy(id) ON DELETE CASCADE;


--
-- TOC entry 4778 (class 2606 OID 17167)
-- Name: fk_my_saved_searches_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY my_saved_searches
    ADD CONSTRAINT fk_my_saved_searches_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4780 (class 2606 OID 17200)
-- Name: fk_my_widgets_multimedia; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY my_widgets
    ADD CONSTRAINT fk_my_widgets_multimedia FOREIGN KEY (icon_ref) REFERENCES multimedia(id);


--
-- TOC entry 4779 (class 2606 OID 17195)
-- Name: fk_my_widgets_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY my_widgets
    ADD CONSTRAINT fk_my_widgets_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4841 (class 2606 OID 250534)
-- Name: fk_part_specimens; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY storage_parts
    ADD CONSTRAINT fk_part_specimens FOREIGN KEY (specimen_ref) REFERENCES specimens(id);


--
-- TOC entry 4765 (class 2606 OID 16969)
-- Name: fk_people_addresses_people; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_addresses
    ADD CONSTRAINT fk_people_addresses_people FOREIGN KEY (person_user_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4764 (class 2606 OID 16952)
-- Name: fk_people_comm_people; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_comm
    ADD CONSTRAINT fk_people_comm_people FOREIGN KEY (person_user_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4761 (class 2606 OID 16878)
-- Name: fk_people_languages_people; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_languages
    ADD CONSTRAINT fk_people_languages_people FOREIGN KEY (people_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4754 (class 2606 OID 16660)
-- Name: fk_people_list_person; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY catalogue_people
    ADD CONSTRAINT fk_people_list_person FOREIGN KEY (people_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4762 (class 2606 OID 16929)
-- Name: fk_people_relationships_people_01; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_relationships
    ADD CONSTRAINT fk_people_relationships_people_01 FOREIGN KEY (person_1_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4763 (class 2606 OID 16934)
-- Name: fk_people_relationships_people_02; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY people_relationships
    ADD CONSTRAINT fk_people_relationships_people_02 FOREIGN KEY (person_2_ref) REFERENCES people(id);


--
-- TOC entry 4755 (class 2606 OID 16685)
-- Name: fk_possible_upper_levels_catalogue_levels_01; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY possible_upper_levels
    ADD CONSTRAINT fk_possible_upper_levels_catalogue_levels_01 FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id) ON DELETE CASCADE;


--
-- TOC entry 4756 (class 2606 OID 16690)
-- Name: fk_possible_upper_levels_catalogue_levels_02; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY possible_upper_levels
    ADD CONSTRAINT fk_possible_upper_levels_catalogue_levels_02 FOREIGN KEY (level_upper_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4821 (class 2606 OID 17761)
-- Name: fk_record_id; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_relationship
    ADD CONSTRAINT fk_record_id FOREIGN KEY (record_id) REFERENCES staging(id) ON DELETE CASCADE;


--
-- TOC entry 4810 (class 2606 OID 17628)
-- Name: fk_specimen_collecting_methods_method; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_methods
    ADD CONSTRAINT fk_specimen_collecting_methods_method FOREIGN KEY (collecting_method_ref) REFERENCES collecting_methods(id) ON DELETE CASCADE;


--
-- TOC entry 4809 (class 2606 OID 17623)
-- Name: fk_specimen_collecting_methods_specimen; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_methods
    ADD CONSTRAINT fk_specimen_collecting_methods_specimen FOREIGN KEY (specimen_ref) REFERENCES specimens(id) ON DELETE CASCADE;


--
-- TOC entry 4807 (class 2606 OID 17589)
-- Name: fk_specimen_collecting_tools_specimen; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_tools
    ADD CONSTRAINT fk_specimen_collecting_tools_specimen FOREIGN KEY (specimen_ref) REFERENCES specimens(id) ON DELETE CASCADE;


--
-- TOC entry 4808 (class 2606 OID 17594)
-- Name: fk_specimen_collecting_tools_tool; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimen_collecting_tools
    ADD CONSTRAINT fk_specimen_collecting_tools_tool FOREIGN KEY (collecting_tool_ref) REFERENCES collecting_tools(id) ON DELETE CASCADE;


--
-- TOC entry 4798 (class 2606 OID 17465)
-- Name: fk_specimens_chronostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_chronostratigraphy FOREIGN KEY (chrono_ref) REFERENCES chronostratigraphy(id);


--
-- TOC entry 4793 (class 2606 OID 17440)
-- Name: fk_specimens_collections; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_collections FOREIGN KEY (collection_ref) REFERENCES collections(id);


--
-- TOC entry 4791 (class 2606 OID 17430)
-- Name: fk_specimens_expeditions; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_expeditions FOREIGN KEY (expedition_ref) REFERENCES expeditions(id);


--
-- TOC entry 4792 (class 2606 OID 17435)
-- Name: fk_specimens_gtu; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_gtu FOREIGN KEY (gtu_ref) REFERENCES gtu(id);


--
-- TOC entry 4799 (class 2606 OID 17470)
-- Name: fk_specimens_igs; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_igs FOREIGN KEY (ig_ref) REFERENCES igs(id);


--
-- TOC entry 4796 (class 2606 OID 17455)
-- Name: fk_specimens_lithology; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_lithology FOREIGN KEY (lithology_ref) REFERENCES lithology(id);


--
-- TOC entry 4795 (class 2606 OID 17450)
-- Name: fk_specimens_lithostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_lithostratigraphy FOREIGN KEY (litho_ref) REFERENCES lithostratigraphy(id);


--
-- TOC entry 4797 (class 2606 OID 17460)
-- Name: fk_specimens_mineralogy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_mineralogy FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id);


--
-- TOC entry 4806 (class 2606 OID 17560)
-- Name: fk_specimens_relationships_institution; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT fk_specimens_relationships_institution FOREIGN KEY (institution_ref) REFERENCES people(id);


--
-- TOC entry 4823 (class 2606 OID 17771)
-- Name: fk_specimens_relationships_institution; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_relationship
    ADD CONSTRAINT fk_specimens_relationships_institution FOREIGN KEY (institution_ref) REFERENCES people(id);


--
-- TOC entry 4804 (class 2606 OID 17550)
-- Name: fk_specimens_relationships_mineralogy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT fk_specimens_relationships_mineralogy FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id);


--
-- TOC entry 4822 (class 2606 OID 17766)
-- Name: fk_specimens_relationships_mineralogy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_relationship
    ADD CONSTRAINT fk_specimens_relationships_mineralogy FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id);


--
-- TOC entry 4802 (class 2606 OID 17540)
-- Name: fk_specimens_relationships_specimens; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT fk_specimens_relationships_specimens FOREIGN KEY (specimen_ref) REFERENCES specimens(id) ON DELETE CASCADE;


--
-- TOC entry 4803 (class 2606 OID 17545)
-- Name: fk_specimens_relationships_specimens_related; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT fk_specimens_relationships_specimens_related FOREIGN KEY (specimen_related_ref) REFERENCES specimens(id) ON DELETE CASCADE;


--
-- TOC entry 4805 (class 2606 OID 17555)
-- Name: fk_specimens_relationships_taxonomy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens_relationships
    ADD CONSTRAINT fk_specimens_relationships_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id);


--
-- TOC entry 4824 (class 2606 OID 17776)
-- Name: fk_specimens_relationships_taxonomy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_relationship
    ADD CONSTRAINT fk_specimens_relationships_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id);


--
-- TOC entry 4794 (class 2606 OID 17445)
-- Name: fk_specimens_taxonomy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY specimens
    ADD CONSTRAINT fk_specimens_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id);


--
-- TOC entry 4816 (class 2606 OID 17712)
-- Name: fk_staging_chronostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_chronostratigraphy FOREIGN KEY (chrono_ref) REFERENCES chronostratigraphy(id) ON DELETE SET NULL;


--
-- TOC entry 4826 (class 2606 OID 17796)
-- Name: fk_staging_collecting_methods_method; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_collecting_methods
    ADD CONSTRAINT fk_staging_collecting_methods_method FOREIGN KEY (collecting_method_ref) REFERENCES collecting_methods(id) ON DELETE CASCADE;


--
-- TOC entry 4825 (class 2606 OID 17791)
-- Name: fk_staging_collecting_methods_staging; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_collecting_methods
    ADD CONSTRAINT fk_staging_collecting_methods_staging FOREIGN KEY (staging_ref) REFERENCES staging(id) ON DELETE CASCADE;


--
-- TOC entry 4814 (class 2606 OID 17702)
-- Name: fk_staging_import; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_import FOREIGN KEY (import_ref) REFERENCES imports(id) ON DELETE CASCADE;


--
-- TOC entry 4818 (class 2606 OID 17722)
-- Name: fk_staging_lithology; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_lithology FOREIGN KEY (lithology_ref) REFERENCES lithology(id) ON DELETE SET NULL;


--
-- TOC entry 4817 (class 2606 OID 17717)
-- Name: fk_staging_lithostratigraphy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_lithostratigraphy FOREIGN KEY (litho_ref) REFERENCES lithostratigraphy(id) ON DELETE SET NULL;


--
-- TOC entry 4819 (class 2606 OID 17727)
-- Name: fk_staging_mineralogy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_mineralogy FOREIGN KEY (mineral_ref) REFERENCES mineralogy(id) ON DELETE SET NULL;


--
-- TOC entry 4828 (class 2606 OID 17831)
-- Name: fk_staging_people_list_person; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_people
    ADD CONSTRAINT fk_staging_people_list_person FOREIGN KEY (people_ref) REFERENCES people(id) ON DELETE CASCADE;


--
-- TOC entry 4820 (class 2606 OID 17743)
-- Name: fk_staging_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_info
    ADD CONSTRAINT fk_staging_ref FOREIGN KEY (staging_ref) REFERENCES staging(id) ON DELETE CASCADE;


--
-- TOC entry 4827 (class 2606 OID 17812)
-- Name: fk_staging_tag_groups; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_tag_groups
    ADD CONSTRAINT fk_staging_tag_groups FOREIGN KEY (staging_ref) REFERENCES staging(id) ON DELETE CASCADE;


--
-- TOC entry 4815 (class 2606 OID 17707)
-- Name: fk_staging_taxonomy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging
    ADD CONSTRAINT fk_staging_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy(id) ON DELETE SET NULL;


--
-- TOC entry 4839 (class 2606 OID 108337)
-- Name: fk_stg_catalogue_import_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_catalogue
    ADD CONSTRAINT fk_stg_catalogue_import_ref FOREIGN KEY (import_ref) REFERENCES imports(id) ON DELETE CASCADE;


--
-- TOC entry 4840 (class 2606 OID 108342)
-- Name: fk_stg_catalogue_level_ref; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY staging_catalogue
    ADD CONSTRAINT fk_stg_catalogue_level_ref FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4757 (class 2606 OID 16750)
-- Name: fk_tag_groups_gtu; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY tag_groups
    ADD CONSTRAINT fk_tag_groups_gtu FOREIGN KEY (gtu_ref) REFERENCES gtu(id) ON DELETE CASCADE;


--
-- TOC entry 4758 (class 2606 OID 16761)
-- Name: fk_tags_gtu; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY tags
    ADD CONSTRAINT fk_tags_gtu FOREIGN KEY (gtu_ref) REFERENCES gtu(id) ON DELETE CASCADE;


--
-- TOC entry 4759 (class 2606 OID 16766)
-- Name: fk_tags_tag_groups; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY tags
    ADD CONSTRAINT fk_tags_tag_groups FOREIGN KEY (group_ref) REFERENCES tag_groups(id) ON DELETE CASCADE;


--
-- TOC entry 4781 (class 2606 OID 17260)
-- Name: fk_taxonomy_level_ref_catalogue_levels; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy
    ADD CONSTRAINT fk_taxonomy_level_ref_catalogue_levels FOREIGN KEY (level_ref) REFERENCES catalogue_levels(id);


--
-- TOC entry 4782 (class 2606 OID 17265)
-- Name: fk_taxonomy_parent_ref_taxonomy; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY taxonomy
    ADD CONSTRAINT fk_taxonomy_parent_ref_taxonomy FOREIGN KEY (parent_ref) REFERENCES taxonomy(id) ON DELETE CASCADE;


--
-- TOC entry 4760 (class 2606 OID 16857)
-- Name: fk_user_people_id; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users
    ADD CONSTRAINT fk_user_people_id FOREIGN KEY (people_id) REFERENCES people(id) ON DELETE SET NULL;


--
-- TOC entry 4767 (class 2606 OID 17004)
-- Name: fk_users_addresses_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_addresses
    ADD CONSTRAINT fk_users_addresses_users FOREIGN KEY (person_user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4766 (class 2606 OID 16987)
-- Name: fk_users_comm_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_comm
    ADD CONSTRAINT fk_users_comm_users FOREIGN KEY (person_user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4768 (class 2606 OID 17025)
-- Name: fk_users_login_infos_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_login_infos
    ADD CONSTRAINT fk_users_login_infos_users FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4811 (class 2606 OID 17644)
-- Name: fk_users_preferences; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY preferences
    ADD CONSTRAINT fk_users_preferences FOREIGN KEY (user_ref) REFERENCES users(id) ON DELETE CASCADE;


--
-- TOC entry 4776 (class 2606 OID 17125)
-- Name: fk_users_tracking_users; Type: FK CONSTRAINT; Schema: darwin2; Owner: darwin2
--

ALTER TABLE ONLY users_tracking
    ADD CONSTRAINT fk_users_tracking_users FOREIGN KEY (user_ref) REFERENCES users(id);


--
-- TOC entry 5177 (class 0 OID 0)
-- Dependencies: 7
-- Name: darwin2; Type: ACL; Schema: -; Owner: darwin2
--

REVOKE ALL ON SCHEMA darwin2 FROM PUBLIC;
REVOKE ALL ON SCHEMA darwin2 FROM darwin2;
GRANT ALL ON SCHEMA darwin2 TO darwin2;
GRANT USAGE ON SCHEMA darwin2 TO d2viewer;


--
-- TOC entry 5179 (class 0 OID 0)
-- Dependencies: 5
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- TOC entry 5186 (class 0 OID 0)
-- Dependencies: 281
-- Name: staging; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE staging FROM PUBLIC;
REVOKE ALL ON TABLE staging FROM darwin2;
GRANT ALL ON TABLE staging TO darwin2;
GRANT SELECT ON TABLE staging TO d2viewer;


--
-- TOC entry 5196 (class 0 OID 0)
-- Dependencies: 240
-- Name: template_classifications; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE template_classifications FROM PUBLIC;
REVOKE ALL ON TABLE template_classifications FROM darwin2;
GRANT ALL ON TABLE template_classifications TO darwin2;
GRANT SELECT ON TABLE template_classifications TO d2viewer;


--
-- TOC entry 5206 (class 0 OID 0)
-- Dependencies: 247
-- Name: taxonomy; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE taxonomy FROM PUBLIC;
REVOKE ALL ON TABLE taxonomy FROM darwin2;
GRANT ALL ON TABLE taxonomy TO darwin2;
GRANT SELECT ON TABLE taxonomy TO d2viewer;


--
-- TOC entry 5213 (class 0 OID 0)
-- Dependencies: 187
-- Name: catalogue_levels; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE catalogue_levels FROM PUBLIC;
REVOKE ALL ON TABLE catalogue_levels FROM darwin2;
GRANT ALL ON TABLE catalogue_levels TO darwin2;
GRANT SELECT ON TABLE catalogue_levels TO d2viewer;


--
-- TOC entry 5221 (class 0 OID 0)
-- Dependencies: 305
-- Name: bibliography; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE bibliography FROM PUBLIC;
REVOKE ALL ON TABLE bibliography FROM darwin2;
GRANT ALL ON TABLE bibliography TO darwin2;
GRANT SELECT ON TABLE bibliography TO d2viewer;


--
-- TOC entry 5226 (class 0 OID 0)
-- Dependencies: 183
-- Name: template_table_record_ref; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE template_table_record_ref FROM PUBLIC;
REVOKE ALL ON TABLE template_table_record_ref FROM darwin2;
GRANT ALL ON TABLE template_table_record_ref TO darwin2;
GRANT SELECT ON TABLE template_table_record_ref TO d2viewer;


--
-- TOC entry 5232 (class 0 OID 0)
-- Dependencies: 307
-- Name: catalogue_bibliography; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE catalogue_bibliography FROM PUBLIC;
REVOKE ALL ON TABLE catalogue_bibliography FROM darwin2;
GRANT ALL ON TABLE catalogue_bibliography TO darwin2;
GRANT SELECT ON TABLE catalogue_bibliography TO d2viewer;


--
-- TOC entry 5243 (class 0 OID 0)
-- Dependencies: 185
-- Name: catalogue_people; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE catalogue_people FROM PUBLIC;
REVOKE ALL ON TABLE catalogue_people FROM darwin2;
GRANT ALL ON TABLE catalogue_people TO darwin2;
GRANT SELECT ON TABLE catalogue_people TO d2viewer;


--
-- TOC entry 5250 (class 0 OID 0)
-- Dependencies: 182
-- Name: catalogue_relationships; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE catalogue_relationships FROM PUBLIC;
REVOKE ALL ON TABLE catalogue_relationships FROM darwin2;
GRANT ALL ON TABLE catalogue_relationships TO darwin2;
GRANT SELECT ON TABLE catalogue_relationships TO d2viewer;


--
-- TOC entry 5262 (class 0 OID 0)
-- Dependencies: 249
-- Name: chronostratigraphy; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE chronostratigraphy FROM PUBLIC;
REVOKE ALL ON TABLE chronostratigraphy FROM darwin2;
GRANT ALL ON TABLE chronostratigraphy TO darwin2;
GRANT SELECT ON TABLE chronostratigraphy TO d2viewer;


--
-- TOC entry 5269 (class 0 OID 0)
-- Dependencies: 242
-- Name: classification_keywords; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE classification_keywords FROM PUBLIC;
REVOKE ALL ON TABLE classification_keywords FROM darwin2;
GRANT ALL ON TABLE classification_keywords TO darwin2;
GRANT SELECT ON TABLE classification_keywords TO d2viewer;


--
-- TOC entry 5278 (class 0 OID 0)
-- Dependencies: 245
-- Name: classification_synonymies; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE classification_synonymies FROM PUBLIC;
REVOKE ALL ON TABLE classification_synonymies FROM darwin2;
GRANT ALL ON TABLE classification_synonymies TO darwin2;
GRANT SELECT ON TABLE classification_synonymies TO d2viewer;


--
-- TOC entry 5293 (class 0 OID 0)
-- Dependencies: 261
-- Name: codes; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE codes FROM PUBLIC;
REVOKE ALL ON TABLE codes FROM darwin2;
GRANT ALL ON TABLE codes TO darwin2;
GRANT SELECT ON TABLE codes TO d2viewer;


--
-- TOC entry 5299 (class 0 OID 0)
-- Dependencies: 271
-- Name: collecting_methods; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE collecting_methods FROM PUBLIC;
REVOKE ALL ON TABLE collecting_methods FROM darwin2;
GRANT ALL ON TABLE collecting_methods TO darwin2;
GRANT SELECT ON TABLE collecting_methods TO d2viewer;


--
-- TOC entry 5305 (class 0 OID 0)
-- Dependencies: 267
-- Name: collecting_tools; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE collecting_tools FROM PUBLIC;
REVOKE ALL ON TABLE collecting_tools FROM darwin2;
GRANT ALL ON TABLE collecting_tools TO darwin2;
GRANT SELECT ON TABLE collecting_tools TO d2viewer;


--
-- TOC entry 5317 (class 0 OID 0)
-- Dependencies: 235
-- Name: collection_maintenance; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE collection_maintenance FROM PUBLIC;
REVOKE ALL ON TABLE collection_maintenance FROM darwin2;
GRANT ALL ON TABLE collection_maintenance TO darwin2;
GRANT SELECT ON TABLE collection_maintenance TO d2viewer;


--
-- TOC entry 5338 (class 0 OID 0)
-- Dependencies: 227
-- Name: collections; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE collections FROM PUBLIC;
REVOKE ALL ON TABLE collections FROM darwin2;
GRANT ALL ON TABLE collections TO darwin2;
GRANT SELECT ON TABLE collections TO d2viewer;


--
-- TOC entry 5345 (class 0 OID 0)
-- Dependencies: 229
-- Name: collections_rights; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE collections_rights FROM PUBLIC;
REVOKE ALL ON TABLE collections_rights FROM darwin2;
GRANT ALL ON TABLE collections_rights TO darwin2;
GRANT SELECT ON TABLE collections_rights TO d2viewer;


--
-- TOC entry 5354 (class 0 OID 0)
-- Dependencies: 190
-- Name: comments; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE comments FROM PUBLIC;
REVOKE ALL ON TABLE comments FROM darwin2;
GRANT ALL ON TABLE comments TO darwin2;
GRANT SELECT ON TABLE comments TO d2viewer;


--
-- TOC entry 5365 (class 0 OID 0)
-- Dependencies: 205
-- Name: expeditions; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE expeditions FROM PUBLIC;
REVOKE ALL ON TABLE expeditions FROM darwin2;
GRANT ALL ON TABLE expeditions TO darwin2;
GRANT SELECT ON TABLE expeditions TO d2viewer;


--
-- TOC entry 5374 (class 0 OID 0)
-- Dependencies: 192
-- Name: ext_links; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE ext_links FROM PUBLIC;
REVOKE ALL ON TABLE ext_links FROM darwin2;
GRANT ALL ON TABLE ext_links TO darwin2;
GRANT SELECT ON TABLE ext_links TO d2viewer;


--
-- TOC entry 5380 (class 0 OID 0)
-- Dependencies: 277
-- Name: flat_dict; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE flat_dict FROM PUBLIC;
REVOKE ALL ON TABLE flat_dict FROM darwin2;
GRANT ALL ON TABLE flat_dict TO darwin2;
GRANT SELECT,INSERT ON TABLE flat_dict TO d2viewer;


--
-- TOC entry 5382 (class 0 OID 0)
-- Dependencies: 276
-- Name: flat_dict_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE flat_dict_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE flat_dict_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE flat_dict_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE flat_dict_id_seq TO d2viewer;


--
-- TOC entry 5396 (class 0 OID 0)
-- Dependencies: 194
-- Name: gtu; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE gtu FROM PUBLIC;
REVOKE ALL ON TABLE gtu FROM darwin2;
GRANT ALL ON TABLE gtu TO darwin2;
GRANT SELECT ON TABLE gtu TO d2viewer;


--
-- TOC entry 5409 (class 0 OID 0)
-- Dependencies: 201
-- Name: identifications; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE identifications FROM PUBLIC;
REVOKE ALL ON TABLE identifications FROM darwin2;
GRANT ALL ON TABLE identifications TO darwin2;
GRANT SELECT ON TABLE identifications TO d2viewer;


--
-- TOC entry 5416 (class 0 OID 0)
-- Dependencies: 257
-- Name: igs; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE igs FROM PUBLIC;
REVOKE ALL ON TABLE igs FROM darwin2;
GRANT ALL ON TABLE igs TO darwin2;
GRANT SELECT ON TABLE igs TO d2viewer;


--
-- TOC entry 5429 (class 0 OID 0)
-- Dependencies: 279
-- Name: imports; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE imports FROM PUBLIC;
REVOKE ALL ON TABLE imports FROM darwin2;
GRANT ALL ON TABLE imports TO darwin2;
GRANT SELECT ON TABLE imports TO d2viewer;


--
-- TOC entry 5440 (class 0 OID 0)
-- Dependencies: 231
-- Name: informative_workflow; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE informative_workflow FROM PUBLIC;
REVOKE ALL ON TABLE informative_workflow FROM darwin2;
GRANT ALL ON TABLE informative_workflow TO darwin2;
GRANT SELECT,INSERT ON TABLE informative_workflow TO d2viewer;


--
-- TOC entry 5442 (class 0 OID 0)
-- Dependencies: 230
-- Name: informative_workflow_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE informative_workflow_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE informative_workflow_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE informative_workflow_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE informative_workflow_id_seq TO d2viewer;


--
-- TOC entry 5449 (class 0 OID 0)
-- Dependencies: 263
-- Name: insurances; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE insurances FROM PUBLIC;
REVOKE ALL ON TABLE insurances FROM darwin2;
GRANT ALL ON TABLE insurances TO darwin2;
GRANT SELECT ON TABLE insurances TO d2viewer;


--
-- TOC entry 5459 (class 0 OID 0)
-- Dependencies: 255
-- Name: lithology; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE lithology FROM PUBLIC;
REVOKE ALL ON TABLE lithology FROM darwin2;
GRANT ALL ON TABLE lithology TO darwin2;
GRANT SELECT ON TABLE lithology TO d2viewer;


--
-- TOC entry 5469 (class 0 OID 0)
-- Dependencies: 251
-- Name: lithostratigraphy; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE lithostratigraphy FROM PUBLIC;
REVOKE ALL ON TABLE lithostratigraphy FROM darwin2;
GRANT ALL ON TABLE lithostratigraphy TO darwin2;
GRANT SELECT ON TABLE lithostratigraphy TO d2viewer;


--
-- TOC entry 5476 (class 0 OID 0)
-- Dependencies: 301
-- Name: loan_history; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE loan_history FROM PUBLIC;
REVOKE ALL ON TABLE loan_history FROM darwin2;
GRANT ALL ON TABLE loan_history TO darwin2;
GRANT SELECT ON TABLE loan_history TO d2viewer;


--
-- TOC entry 5486 (class 0 OID 0)
-- Dependencies: 295
-- Name: loan_items; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE loan_items FROM PUBLIC;
REVOKE ALL ON TABLE loan_items FROM darwin2;
GRANT ALL ON TABLE loan_items TO darwin2;
GRANT SELECT ON TABLE loan_items TO d2viewer;


--
-- TOC entry 5493 (class 0 OID 0)
-- Dependencies: 297
-- Name: loan_rights; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE loan_rights FROM PUBLIC;
REVOKE ALL ON TABLE loan_rights FROM darwin2;
GRANT ALL ON TABLE loan_rights TO darwin2;
GRANT SELECT ON TABLE loan_rights TO d2viewer;


--
-- TOC entry 5503 (class 0 OID 0)
-- Dependencies: 299
-- Name: loan_status; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE loan_status FROM PUBLIC;
REVOKE ALL ON TABLE loan_status FROM darwin2;
GRANT ALL ON TABLE loan_status TO darwin2;
GRANT SELECT ON TABLE loan_status TO d2viewer;


--
-- TOC entry 5512 (class 0 OID 0)
-- Dependencies: 293
-- Name: loans; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE loans FROM PUBLIC;
REVOKE ALL ON TABLE loans FROM darwin2;
GRANT ALL ON TABLE loans TO darwin2;
GRANT SELECT ON TABLE loans TO d2viewer;


--
-- TOC entry 5527 (class 0 OID 0)
-- Dependencies: 253
-- Name: mineralogy; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE mineralogy FROM PUBLIC;
REVOKE ALL ON TABLE mineralogy FROM darwin2;
GRANT ALL ON TABLE mineralogy TO darwin2;
GRANT SELECT ON TABLE mineralogy TO d2viewer;


--
-- TOC entry 5546 (class 0 OID 0)
-- Dependencies: 211
-- Name: multimedia; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE multimedia FROM PUBLIC;
REVOKE ALL ON TABLE multimedia FROM darwin2;
GRANT ALL ON TABLE multimedia TO darwin2;
GRANT SELECT ON TABLE multimedia TO d2viewer;


--
-- TOC entry 5559 (class 0 OID 0)
-- Dependencies: 237
-- Name: my_saved_searches; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE my_saved_searches FROM PUBLIC;
REVOKE ALL ON TABLE my_saved_searches FROM darwin2;
GRANT ALL ON TABLE my_saved_searches TO darwin2;
GRANT SELECT ON TABLE my_saved_searches TO d2viewer;


--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 239
-- Name: my_widgets; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE my_widgets FROM PUBLIC;
REVOKE ALL ON TABLE my_widgets FROM darwin2;
GRANT ALL ON TABLE my_widgets TO darwin2;
GRANT SELECT,INSERT ON TABLE my_widgets TO d2viewer;


--
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 238
-- Name: my_widgets_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE my_widgets_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE my_widgets_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE my_widgets_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE my_widgets_id_seq TO d2viewer;


--
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 178
-- Name: template_people; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE template_people FROM PUBLIC;
REVOKE ALL ON TABLE template_people FROM darwin2;
GRANT ALL ON TABLE template_people TO darwin2;
GRANT SELECT ON TABLE template_people TO d2viewer;


--
-- TOC entry 5613 (class 0 OID 0)
-- Dependencies: 180
-- Name: people; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE people FROM PUBLIC;
REVOKE ALL ON TABLE people FROM darwin2;
GRANT ALL ON TABLE people TO darwin2;
GRANT SELECT ON TABLE people TO d2viewer;


--
-- TOC entry 5621 (class 0 OID 0)
-- Dependencies: 213
-- Name: template_people_users_addr_common; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE template_people_users_addr_common FROM PUBLIC;
REVOKE ALL ON TABLE template_people_users_addr_common FROM darwin2;
GRANT ALL ON TABLE template_people_users_addr_common TO darwin2;
GRANT SELECT ON TABLE template_people_users_addr_common TO d2viewer;


--
-- TOC entry 5625 (class 0 OID 0)
-- Dependencies: 212
-- Name: template_people_users_comm_common; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE template_people_users_comm_common FROM PUBLIC;
REVOKE ALL ON TABLE template_people_users_comm_common FROM darwin2;
GRANT ALL ON TABLE template_people_users_comm_common TO darwin2;
GRANT SELECT ON TABLE template_people_users_comm_common TO d2viewer;


--
-- TOC entry 5637 (class 0 OID 0)
-- Dependencies: 219
-- Name: people_addresses; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE people_addresses FROM PUBLIC;
REVOKE ALL ON TABLE people_addresses FROM darwin2;
GRANT ALL ON TABLE people_addresses TO darwin2;
GRANT SELECT ON TABLE people_addresses TO d2viewer;


--
-- TOC entry 5645 (class 0 OID 0)
-- Dependencies: 217
-- Name: people_comm; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE people_comm FROM PUBLIC;
REVOKE ALL ON TABLE people_comm FROM darwin2;
GRANT ALL ON TABLE people_comm TO darwin2;
GRANT SELECT ON TABLE people_comm TO d2viewer;


--
-- TOC entry 5653 (class 0 OID 0)
-- Dependencies: 209
-- Name: people_languages; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE people_languages FROM PUBLIC;
REVOKE ALL ON TABLE people_languages FROM darwin2;
GRANT ALL ON TABLE people_languages TO darwin2;
GRANT SELECT ON TABLE people_languages TO d2viewer;


--
-- TOC entry 5665 (class 0 OID 0)
-- Dependencies: 215
-- Name: people_relationships; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE people_relationships FROM PUBLIC;
REVOKE ALL ON TABLE people_relationships FROM darwin2;
GRANT ALL ON TABLE people_relationships TO darwin2;
GRANT SELECT ON TABLE people_relationships TO d2viewer;


--
-- TOC entry 5670 (class 0 OID 0)
-- Dependencies: 188
-- Name: possible_upper_levels; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE possible_upper_levels FROM PUBLIC;
REVOKE ALL ON TABLE possible_upper_levels FROM darwin2;
GRANT ALL ON TABLE possible_upper_levels TO darwin2;
GRANT SELECT ON TABLE possible_upper_levels TO d2viewer;


--
-- TOC entry 5675 (class 0 OID 0)
-- Dependencies: 275
-- Name: preferences; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE preferences FROM PUBLIC;
REVOKE ALL ON TABLE preferences FROM darwin2;
GRANT ALL ON TABLE preferences TO darwin2;
GRANT SELECT,INSERT ON TABLE preferences TO d2viewer;


--
-- TOC entry 5677 (class 0 OID 0)
-- Dependencies: 274
-- Name: preferences_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE preferences_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE preferences_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE preferences_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE preferences_id_seq TO d2viewer;


--
-- TOC entry 5694 (class 0 OID 0)
-- Dependencies: 199
-- Name: properties; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE properties FROM PUBLIC;
REVOKE ALL ON TABLE properties FROM darwin2;
GRANT ALL ON TABLE properties TO darwin2;
GRANT SELECT ON TABLE properties TO d2viewer;


--
-- TOC entry 5696 (class 0 OID 0)
-- Dependencies: 198
-- Name: properties_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE properties_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE properties_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE properties_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE properties_id_seq TO d2viewer;


--
-- TOC entry 5701 (class 0 OID 0)
-- Dependencies: 273
-- Name: specimen_collecting_methods; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE specimen_collecting_methods FROM PUBLIC;
REVOKE ALL ON TABLE specimen_collecting_methods FROM darwin2;
GRANT ALL ON TABLE specimen_collecting_methods TO darwin2;
GRANT SELECT ON TABLE specimen_collecting_methods TO d2viewer;


--
-- TOC entry 5707 (class 0 OID 0)
-- Dependencies: 269
-- Name: specimen_collecting_tools; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE specimen_collecting_tools FROM PUBLIC;
REVOKE ALL ON TABLE specimen_collecting_tools FROM darwin2;
GRANT ALL ON TABLE specimen_collecting_tools TO darwin2;
GRANT SELECT ON TABLE specimen_collecting_tools TO d2viewer;


--
-- TOC entry 5752 (class 0 OID 0)
-- Dependencies: 259
-- Name: specimens; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE specimens FROM PUBLIC;
REVOKE ALL ON TABLE specimens FROM darwin2;
GRANT ALL ON TABLE specimens TO darwin2;
GRANT SELECT ON TABLE specimens TO d2viewer;


--
-- TOC entry 5764 (class 0 OID 0)
-- Dependencies: 265
-- Name: specimens_relationships; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE specimens_relationships FROM PUBLIC;
REVOKE ALL ON TABLE specimens_relationships FROM darwin2;
GRANT ALL ON TABLE specimens_relationships TO darwin2;
GRANT SELECT ON TABLE specimens_relationships TO d2viewer;


--
-- TOC entry 5766 (class 0 OID 0)
-- Dependencies: 264
-- Name: specimens_relationships_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE specimens_relationships_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE specimens_relationships_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE specimens_relationships_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE specimens_relationships_id_seq TO d2viewer;


--
-- TOC entry 5798 (class 0 OID 0)
-- Dependencies: 291
-- Name: staging_people; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE staging_people FROM PUBLIC;
REVOKE ALL ON TABLE staging_people FROM darwin2;
GRANT ALL ON TABLE staging_people TO darwin2;
GRANT SELECT ON TABLE staging_people TO d2viewer;


--
-- TOC entry 5817 (class 0 OID 0)
-- Dependencies: 289
-- Name: staging_tag_groups; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE staging_tag_groups FROM PUBLIC;
REVOKE ALL ON TABLE staging_tag_groups FROM darwin2;
GRANT ALL ON TABLE staging_tag_groups TO darwin2;
GRANT SELECT ON TABLE staging_tag_groups TO d2viewer;


--
-- TOC entry 5830 (class 0 OID 0)
-- Dependencies: 196
-- Name: tag_groups; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE tag_groups FROM PUBLIC;
REVOKE ALL ON TABLE tag_groups FROM darwin2;
GRANT ALL ON TABLE tag_groups TO darwin2;
GRANT SELECT ON TABLE tag_groups TO d2viewer;


--
-- TOC entry 5839 (class 0 OID 0)
-- Dependencies: 197
-- Name: tags; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE tags FROM PUBLIC;
REVOKE ALL ON TABLE tags FROM darwin2;
GRANT ALL ON TABLE tags TO darwin2;
GRANT SELECT ON TABLE tags TO d2viewer;


--
-- TOC entry 5858 (class 0 OID 0)
-- Dependencies: 207
-- Name: users; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE users FROM PUBLIC;
REVOKE ALL ON TABLE users FROM darwin2;
GRANT ALL ON TABLE users TO darwin2;
GRANT SELECT,INSERT ON TABLE users TO d2viewer;


--
-- TOC entry 5872 (class 0 OID 0)
-- Dependencies: 223
-- Name: users_addresses; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE users_addresses FROM PUBLIC;
REVOKE ALL ON TABLE users_addresses FROM darwin2;
GRANT ALL ON TABLE users_addresses TO darwin2;
GRANT SELECT,INSERT ON TABLE users_addresses TO d2viewer;


--
-- TOC entry 5874 (class 0 OID 0)
-- Dependencies: 222
-- Name: users_addresses_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE users_addresses_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE users_addresses_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE users_addresses_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE users_addresses_id_seq TO d2viewer;


--
-- TOC entry 5881 (class 0 OID 0)
-- Dependencies: 221
-- Name: users_comm; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE users_comm FROM PUBLIC;
REVOKE ALL ON TABLE users_comm FROM darwin2;
GRANT ALL ON TABLE users_comm TO darwin2;
GRANT SELECT,INSERT ON TABLE users_comm TO d2viewer;


--
-- TOC entry 5883 (class 0 OID 0)
-- Dependencies: 220
-- Name: users_comm_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE users_comm_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE users_comm_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE users_comm_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE users_comm_id_seq TO d2viewer;


--
-- TOC entry 5885 (class 0 OID 0)
-- Dependencies: 206
-- Name: users_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE users_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE users_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE users_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE users_id_seq TO d2viewer;


--
-- TOC entry 5894 (class 0 OID 0)
-- Dependencies: 225
-- Name: users_login_infos; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE users_login_infos FROM PUBLIC;
REVOKE ALL ON TABLE users_login_infos FROM darwin2;
GRANT ALL ON TABLE users_login_infos TO darwin2;
GRANT SELECT,INSERT ON TABLE users_login_infos TO d2viewer;


--
-- TOC entry 5896 (class 0 OID 0)
-- Dependencies: 224
-- Name: users_login_infos_id_seq; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON SEQUENCE users_login_infos_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE users_login_infos_id_seq FROM darwin2;
GRANT ALL ON SEQUENCE users_login_infos_id_seq TO darwin2;
GRANT USAGE ON SEQUENCE users_login_infos_id_seq TO d2viewer;


--
-- TOC entry 5904 (class 0 OID 0)
-- Dependencies: 233
-- Name: users_tracking; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE users_tracking FROM PUBLIC;
REVOKE ALL ON TABLE users_tracking FROM darwin2;
GRANT ALL ON TABLE users_tracking TO darwin2;
GRANT SELECT ON TABLE users_tracking TO d2viewer;


--
-- TOC entry 5913 (class 0 OID 0)
-- Dependencies: 203
-- Name: vernacular_names; Type: ACL; Schema: darwin2; Owner: darwin2
--

REVOKE ALL ON TABLE vernacular_names FROM PUBLIC;
REVOKE ALL ON TABLE vernacular_names FROM darwin2;
GRANT ALL ON TABLE vernacular_names TO darwin2;
GRANT SELECT ON TABLE vernacular_names TO d2viewer;


-- Completed on 2017-10-19 18:14:12

--
-- PostgreSQL database dump complete
--

