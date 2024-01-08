begin;
set search_path=darwin2,public;

CREATE INDEX  idx_gin_trgm_comments_comment on comments  using gin ("comment" gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_vernacular_names_name on vernacular_names using gin(name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_expeditions_name on expeditions using gin(name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_people_formated_name on people using gin(formated_name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_users_formated_name on users using gin(formated_name_indexed gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_taxonomy_naming on taxonomy using gin(name_order_by gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_chronostratigraphy_naming on chronostratigraphy using gin(name_order_by gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_lithostratigraphy_naming on lithostratigraphy using gin(name_order_by gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_mineralogy_naming on mineralogy using gin(name_order_by gin_trgm_ops);
CREATE INDEX  idx_gin_trgm_lithology_naming on lithology using gin(name_order_by gin_trgm_ops);


/*** For Flat ***/
CREATE INDEX idx_gin_trgm_specimens_flat_expedition_name_ts on specimens_flat using gin(expedition_name_indexed gin_trgm_ops);
CREATE INDEX idx_gin_trgm_specimens_flat_taxon_name_indexed on specimens_flat using gin(taxon_name_order_by gin_trgm_ops);
CREATE INDEX idx_gin_trgm_specimens_flat_taxon_path on specimens_flat using gin(taxon_path gin_trgm_ops);
CREATE INDEX idx_gin_trgm_specimens_flat_ig_num ON specimens_flat USING gin (ig_num_indexed gin_trgm_ops);

commit;
