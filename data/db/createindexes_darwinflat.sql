/*** BTree indexes for foreign keys in Darwin Flat table ***/

CREATE INDEX CONCURRENTLY idx_specimens_flat_spec_ref on specimens_flat(specimen_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_collection_ref on specimens_flat(collection_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_expedition_ref on specimens_flat(expedition_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_gtu_ref on specimens_flat(gtu_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_taxon_ref on specimens_flat(taxon_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_ig_ref on specimens_flat(ig_ref);

CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_expedition_name_ts on specimens_flat using gin(expedition_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_taxon_name_indexed on specimens_flat using gin(taxon_name_indexed);
CREATE INDEX CONCURRENTLY idx_specimens_flat_taxon_path on specimens_flat(taxon_path text_pattern_ops);

CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_gtu_tag_values_indexed on specimens_flat using gin(gtu_tag_values_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_gtu_country_tag_indexed_indexed on specimens_flat using gin(gtu_country_tag_indexed);
CREATE INDEX CONCURRENTLY idx_gist_specimens_flat_gtu_location ON specimens_flat USING GIST ( gtu_location );
CREATE INDEX CONCURRENTLY idx_specimens_flat_host_specimen_ref on specimens_flat(host_specimen_ref);
CREATE INDEX CONCURRENTLY idx_specimens_flat_host_taxon_ref on specimens_flat(host_taxon_ref);

CREATE INDEX CONCURRENTLY idx_specimens_flat_category on specimens_flat(category);

/*** BTree indexes for search purposes in Darwin flat table ***/
CREATE INDEX CONCURRENTLY idx_specimens_flat_station_visible on specimens_flat(station_visible);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_code ON specimens_flat USING gin
  (gtu_code gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_specimens_flat_gtu_from_date_mask on specimens_flat(gtu_from_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_flat_gtu_to_date_mask on specimens_flat(gtu_to_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_flat_gtu_to_date on specimens_flat(gtu_to_date);
CREATE INDEX CONCURRENTLY idx_specimens_flat_gtu_from_date on specimens_flat(gtu_from_date);
CREATE INDEX CONCURRENTLY idx_specimens_flat_taxon_name_order_by on specimens_flat(taxon_name_order_by);

CREATE INDEX CONCURRENTLY idx_specimens_flat_ig_num on specimens_flat(ig_num_indexed text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_specimens_flat_collection_is_public on specimens_flat(collection_is_public);
CREATE INDEX CONCURRENTLY idx_specimens_flat_collection_name on specimens_flat(collection_name, specimen_ref);

/*CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_collection_institution_formated_name_ts on specimens_flat using gin(collection_institution_formated_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_collection_main_manager_formated_name_ts on specimens_flat using gin(collection_main_manager_formated_name_ts);*/

CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_spec_ident_ids on specimens_flat using gin(spec_ident_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_spec_coll_ids on specimens_flat using gin(spec_coll_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_flat_spec_don_sel_ids on specimens_flat using gin(spec_don_sel_ids);
