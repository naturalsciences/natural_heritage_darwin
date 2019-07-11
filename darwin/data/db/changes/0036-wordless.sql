begin;
set search_path=darwin2,public;

\i  createfunctions.sql
SET SESSION session_replication_role = replica;

UPDATE template_table_record_ref r set referenced_relation = 'old_multimedia' where referenced_relation='multimedia' and not exists (select 1 from multimedia t where t.id = r.record_id);
SET SESSION session_replication_role = origin;


delete from template_table_record_ref r where referenced_relation = 'staging' AND not exists (select 1 from staging t where t.id = r.record_id);


/***
* INDEXES
*/
DROP INDEX IF EXISTS idx_gist_comments_comment_ts;
DROP INDEX IF EXISTS idx_gist_vernacular_names_name_ts;
DROP INDEX IF EXISTS idx_gist_expeditions_name_ts;
DROP INDEX IF EXISTS idx_gin_people_formated_name_ts;
DROP INDEX IF EXISTS idx_gin_users_formated_name_ts;
DROP INDEX IF EXISTS idx_gin_people_addresses_address_parts_ts;
DROP INDEX IF EXISTS idx_gin_users_addresses_address_parts_ts;
DROP INDEX IF EXISTS idx_gin_taxonomy_naming;
DROP INDEX IF EXISTS idx_gin_chronostratigraphy_naming;
DROP INDEX IF EXISTS idx_gin_lithostratigraphy_naming;
DROP INDEX IF EXISTS idx_gin_mineralogy_naming;
DROP INDEX IF EXISTS idx_gin_lithology_naming;



DROP INDEX IF EXISTS idx_gin_specimens_flat_expedition_name_ts;
DROP INDEX IF EXISTS idx_gin_specimens_flat_taxon_name_indexed;
DROP INDEX IF EXISTS idx_specimens_flat_taxon_path;
DROP INDEX IF EXISTS idx_specimens_flat_ig_num;

DROP VIEW if exists "public"."labeling";
DROP VIEW darwin_flat;

DROP TRIGGER trg_cpy_tofulltext_bibliography ON bibliography;
DROP TRIGGER trg_cpy_tofulltext_collectionmaintenance ON collection_maintenance;
DROP TRIGGER trg_cpy_tofulltext_comments ON comments;
DROP TRIGGER trg_cpy_tofulltext_expeditions ON expeditions;
DROP TRIGGER trg_cpy_tofulltext_ext_links ON ext_links;
DROP TRIGGER trg_cpy_tofulltext_identifications ON identifications;
DROP TRIGGER trg_cpy_tofulltext_multimedia ON multimedia;
DROP TRIGGER trg_cpy_tofulltext_peopleaddresses ON people_addresses;
DROP TRIGGER trg_cpy_tofulltext_usersaddresses ON users_addresses;
DROP TRIGGER trg_cpy_tofulltext_vernacularnames ON vernacular_names;


DROP FUNCTION darwin2.fct_cpy_word(character varying, character varying, tsvector);
DROP FUNCTION fct_trg_word() CASCADE;
DROP FUNCTION ts_stat(tsvector, OUT word text, OUT ndoc integer, OUT nentry integer);
DROP TABLE words;

ALTER TABLE template_people DROP COLUMN formated_name_ts;
ALTER TABLE template_classifications DROP COLUMN name_indexed;
ALTER TABLE template_classifications RENAME COLUMN  name_order_by TO name_indexed;


ALTER TABLE codes DROP COLUMN full_code_indexed;
ALTER TABLE codes RENAME COLUMN  full_code_order_by TO full_code_indexed;

ALTER TABLE identifications DROP COLUMN  value_defined_ts;
ALTER TABLE loans DROP COLUMN  description_ts;
ALTER TABLE template_people_users_addr_common DROP COLUMN address_parts_ts;
ALTER TABLE vernacular_names DROP COLUMN name_ts;

ALTER TABLE specimens_flat DROP COLUMN expedition_name_ts;
ALTER TABLE specimens_flat DROP COLUMN taxon_name_indexed;
ALTER TABLE specimens_flat DROP COLUMN chrono_name_indexed;
ALTER TABLE specimens_flat DROP COLUMN litho_name_indexed;
ALTER TABLE specimens_flat DROP COLUMN lithology_name_indexed;
ALTER TABLE specimens_flat DROP COLUMN mineral_name_indexed;
ALTER TABLE specimens_flat DROP COLUMN host_taxon_name_indexed;

ALTER TABLE specimens_flat RENAME COLUMN taxon_name_order_by TO taxon_name_indexed;
ALTER TABLE specimens_flat RENAME COLUMN chrono_name_order_by TO chrono_name_indexed;
ALTER TABLE specimens_flat RENAME COLUMN litho_name_order_by TO litho_name_indexed;
ALTER TABLE specimens_flat RENAME COLUMN lithology_name_order_by TO lithology_name_indexed;
ALTER TABLE specimens_flat RENAME COLUMN mineral_name_order_by TO mineral_name_indexed;
ALTER TABLE specimens_flat RENAME COLUMN host_taxon_name_order_by TO host_taxon_name_indexed;

ALTER TABLE bibliography DROP COLUMN title_ts;

ALTER TABLE collection_maintenance DROP COLUMN description_ts;
ALTER TABLE collection_maintenance ADD COLUMN description_indexed text;
-- No need to update it isn't indexed nor searched

ALTER TABLE comments DROP COLUMN comment_ts;
ALTER TABLE comments ADD COLUMN comment_indexed text;

SET SESSION session_replication_role = replica;
UPDATE comments SET comment_indexed = fulltoindex(comment);
SET SESSION session_replication_role = origin;


ALTER TABLE comments ALTER COLUMN comment_indexed SET NOT NULL;
CREATE TRIGGER trg_cpy_fulltoindex_comments BEFORE INSERT OR UPDATE ON comments FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


ALTER TABLE multimedia DROP COLUMN search_ts;
ALTER TABLE multimedia ADD COLUMN search_indexed text;
UPDATE multimedia SET search_indexed = fullToIndex ( COALESCE(title,'') ||  COALESCE(description,'') );
ALTER TABLE multimedia ALTER COLUMN search_indexed SET NOT NULL;

ALTER TABLE expeditions DROP COLUMN name_ts;


ALTER TABLE ext_links DROP COLUMN comment_ts;
ALTER TABLE ext_links ADD COLUMN comment_indexed text;
UPDATE ext_links SET comment_indexed = fulltoindex(comment);
ALTER TABLE ext_links ALTER COLUMN comment_indexed SET NOT NULL;




-- ALTER TABLE bibliography ADD constraint unq_bibliography unique (title_indexed, type);
ALTER TABLE taxonomy ADD constraint unq_taxonomy unique (path, name_indexed, level_ref);
ALTER TABLE chronostratigraphy ADD constraint unq_chronostratigraphy unique (path, name_indexed, level_ref);
ALTER TABLE lithostratigraphy ADD constraint unq_lithostratigraphy unique (path, name_indexed, level_ref);
ALTER TABLE mineralogy ADD constraint unq_mineralogy unique (path, name_indexed, level_ref, code);
ALTER TABLE lithology ADD constraint unq_lithology unique (path, name_indexed, level_ref);


COMMENT ON COLUMN collection_maintenance.description_indexed IS 'indexed form of description field';
COMMENT ON COLUMN chronostratigraphy.name_indexed IS 'Indexed form of name field';
COMMENT ON COLUMN comments.comment_indexed IS 'indexed form of comment field';
COMMENT ON COLUMN ext_links.comment_indexed IS 'indexed form of comment field';
COMMENT ON COLUMN lithology.name_indexed IS 'Indexed form of name field';
COMMENT ON COLUMN lithostratigraphy.name_indexed IS 'Indexed form of name field';
COMMENT ON COLUMN loans.search_indexed IS 'indexed getting Description and title of the loan';
COMMENT ON COLUMN mineralogy.name_indexed IS 'Indexed form of name field';
COMMENT ON COLUMN multimedia.search_indexed IS 'indexed form of title and description fields together';
COMMENT ON COLUMN taxonomy.name_indexed IS 'Indexed form of name field';


ALTER INDEX idx_chronostratigraphy_name_order_by RENAME TO idx_gin_trgm_chronostratigraphy_name_indexed;
ALTER INDEX idx_gin_trgm_specimens_flat_expedition_name_ts RENAME TO idx_gin_trgm_specimens_flat_expedition_name_indexed;

ALTER INDEX idx_lithology_name_order_by RENAME TO idx_gin_trgm_lithology_name_indexed;
ALTER INDEX idx_lithostratigraphy_name_order_by RENAME TO idx_gin_trgm_lithostratigraphy_name_indexed;

ALTER INDEX idx_mineralogy_name_order_by RENAME TO idx_gin_trgm_mineralogy_name_indexed;
ALTER INDEX idx_specimens_flat_taxon_name_order_by RENAME TO idx_specimens_flat_name_indexed;

ALTER INDEX idx_taxonomy_name_order_by_txt_op RENAME TO idx_gin_trgm_taxonomy_name_indexed;


\i maintenance/recreate_flat_view.sql 
\i reports/ticketing/create_labeling_view.sql

CREATE INDEX idx_gin_multimedia_search_indexed ON multimedia USING gin (search_indexed public.gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_comments_comment_indexed on comments  using gin ("comment_indexed" gin_trgm_ops);

COMMIT;
--rollback;
