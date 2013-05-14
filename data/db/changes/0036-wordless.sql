begin;
set search_path=darwin2;

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


ALTER TABLE template_people DROP COLUMN formated_name_ts;
ALTER TABLE template_classifications DROP COLUMN name_indexed;
ALTER TABLE template_classifications RENAME COLUMN  name_order_by TO name_indexed;

ALTER TABLE code DROP COLUMN full_code_indexed;
ALTER TABLE code RENAME COLUMN  full_code_order_by TO full_code_indexed;

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
ALTER TABLE ONLY bibliography
    DROP CONSTRAINT unq_bibliography;
ALTER TABLE ONLY bibliography
    ADD CONSTRAINT unq_bibliography UNIQUE (title_indexed, type);

ALTER TABLE collection_maintenance DROP COLUMN description_ts;
ALTER TABLE collection_maintenance ADD COLUMN description_indexed text;
-- No need to update it isn't indexed nor searched

ALTER TABLE comments DROP COLUMN comment_ts;
ALTER TABLE comments ADD COLUMN comment_indexed text;
UPDATE comments SET comment_indexed = fulltoindex(comment);
ALTER TABLE comments ALTER COLUMN comment_indexed SET NOT NULL;
CREATE TRIGGER trg_cpy_fulltoindex_comments BEFORE INSERT OR UPDATE ON comments FOR EACH ROW EXECUTE PROCEDURE fct_cpy_fulltoindex();


ALTER TABLE multimedia DROP COLUMN comment_ts;
ALTER TABLE multimedia ADD COLUMN comment_indexed text;
UPDATE multimedia SET comment_indexed = fullToIndex ( COALESCE(title,'') ||  COALESCE(NEW.description,'') );
ALTER TABLE multimedia ALTER COLUMN comment_indexed SET NOT NULL;

ALTER TABLE expedition DROP COLUMN name_ts;
ALTER TABLE expedition ADD COLUMN name_indexed text;
UPDATE expedition SET name_indexed = fulltoindex(name);
ALTER TABLE expedition ALTER COLUMN name_indexed SET NOT NULL;

ALTER TABLE ext_links DROP COLUMN comment_ts;
ALTER TABLE ext_links ADD COLUMN comment_indexed text;
UPDATE ext_links SET comment_indexed = fulltoindex(comment);
ALTER TABLE ext_links ALTER COLUMN comment_indexed SET NOT NULL;

DROP FUNCTION darwin2.fct_cpy_word(character varying, character varying, tsvector);
DROP FUNCTION fct_trg_word();
DROP FUNCTION ts_stat(tsvector, OUT word text, OUT ndoc integer, OUT nentry integer);
DROP TABLE words;


commit;