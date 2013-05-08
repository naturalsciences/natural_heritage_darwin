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


DROP TABLE words;

commit;