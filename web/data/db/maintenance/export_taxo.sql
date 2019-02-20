--- ON THE SERVER

/*

\COPY (select * from darwin2.taxonomy ) TO 'taxonomy.csv';
\COPY (select * from darwin2.catalogue_people WHERE referenced_relation='taxonomy' ) TO 'catalogue_people.csv';
\COPY (select * from darwin2.catalogue_relationships WHERE referenced_relation='taxonomy' ) TO 'catalogue_relationships.csv';
\COPY (select * from darwin2.classification_synonymies WHERE referenced_relation='taxonomy' ) TO 'classification_synonymies.csv';
\COPY (select * from darwin2.comments WHERE referenced_relation='taxonomy' ) TO 'comments.csv';
\COPY (select * from darwin2.properties WHERE referenced_relation='taxonomy' ) TO 'properties.csv';
\COPY (select * from darwin2.classification_keywords WHERE referenced_relation='taxonomy' ) TO 'classification_keywords.csv';
\COPY (select id,is_physical,sub_type,formated_name,formated_name_indexed,formated_name_unique,title,family_name,given_name,additional_names,birth_date_mask,birth_date,gender,end_date_mask,end_date,activity_date_from_mask,activity_date_from,activity_date_to_mask,activity_date_to,name_formated_indexed from people p where exists(select 1 from darwin2.catalogue_people where referenced_relation='taxonomy' and people_ref = p.id)) TO 'people.csv'

*/
-- ON THE COPY OF THE SERVER


/*

./install.sh install-db
data/db$ sudo -su postgres psql darwin2 -f maintenance/export_taxo.sql
cd ../..

# THEN
php symfony darwin:add-admin
*/

BEGIN;

SET search_path = darwin2, public, pg_catalog;

CREATE OR REPLACE FUNCTION fct_adm_disable_checks(tbl text[]) RETURNS boolean
AS $$
DECLARE
  res RECORD;
BEGIN
 CREATE TABLE disabled_constraints (
  id serial,
  relation text,
  qry text
 );
 FOR res IN select
    'ALTER TABLE "'||nspname||'"."'||relname||'" DROP CONSTRAINT "'||conname||'" ' as disable_query,
    'ALTER TABLE "'||nspname||'"."'||relname||'" ADD CONSTRAINT "'||conname||'" '|| pg_get_constraintdef(pg_constraint.oid)||';' as enable_query,
    relname as relation
    FROM pg_constraint
  INNER JOIN pg_class ON conrelid=pg_class.oid
  INNER JOIN pg_namespace ON pg_namespace.oid=pg_class.relnamespace
    WHERE relname = ANY (tbl)
    AND contype != 'p' --Primary
    ORDER BY CASE WHEN contype='f' THEN 0 ELSE 1 END,contype,nspname,relname,conname
  LOOP

    INSERT INTO disabled_constraints(relation,qry) VALUES(res.relation, res.enable_query);
    raise NOTICE 'SQL: %', res.disable_query;
    EXECUTE res.disable_query;
  END LOOP;
 RETURN TRUE;
END;
$$
language plpgsql;





CREATE OR REPLACE FUNCTION fct_adm_enable_checks() RETURNS boolean
AS $$
DECLARE
  rec RECORD;
BEGIN
 FOR rec IN select * from disabled_constraints LOOP
    EXECUTE rec.qry;
  END LOOP;

  DROP TABLE disabled_constraints;
 RETURN TRUE;
END;
$$
language plpgsql;





-- PostgreSQL database dump
--

-- Dumped from database version 9.1.11
-- Dumped by pg_dump version 9.3.2
-- Started on 2014-01-02 14:30:53 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;


--
-- TOC entry 3873 (class 0 OID 517175)
-- Dependencies: 404
-- Data for Name: taxonomy; Type: TABLE DATA; Schema: darwin2; Owner: darwin2
--

SET SESSION AUTHORIZATION DEFAULT;

SET SESSION session_replication_role = replica;

select fct_adm_disable_checks('{"taxonomy", "catalogue_people", "catalogue_relationships", "classification_synonymies", "comments", "properties", "classification_keywords"}');

-----

TRUNCATE darwin2.taxonomy CASCADE;

COPY darwin2.people(id,is_physical,sub_type,formated_name,formated_name_indexed,formated_name_unique,title,family_name,given_name,additional_names,birth_date_mask,birth_date,gender,end_date_mask,end_date,activity_date_from_mask,activity_date_from,activity_date_to_mask,activity_date_to,name_formated_indexed) FROM '/tmp/people.csv';
COPY darwin2.taxonomy FROM '/tmp/taxonomy.csv';
COPY darwin2.catalogue_people FROM '/tmp/catalogue_people.csv';
COPY darwin2.catalogue_relationships FROM '/tmp/catalogue_relationships.csv';
COPY darwin2.classification_synonymies FROM '/tmp/classification_synonymies.csv';
COPY darwin2.comments FROM '/tmp/comments.csv';
COPY darwin2.properties FROM '/tmp/properties.csv';
COPY darwin2.classification_keywords FROM '/tmp/classification_keywords.csv';


--
select fct_adm_enable_checks();

SET SESSION session_replication_role = origin;




DROP FUNCTION fct_adm_disable_checks(tbl text[]);
DROP FUNCTION fct_adm_enable_checks();


COMMIT;
