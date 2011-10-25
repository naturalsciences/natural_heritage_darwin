/*** BTree indexes for foreign keys in Darwin Flat table ***/

/*CREATE INDEX CONCURRENTLY idx_specimens_collection_type on specimens(collection_type);
CREATE INDEX CONCURRENTLY idx_specimens_collection_code on specimens(collection_code);
CREATE INDEX CONCURRENTLY idx_specimens_collection_institution_formated_name_indexed on specimens(collection_institution_formated_name_indexed);
CREATE INDEX CONCURRENTLY idx_specimens_collection_main_manager_formated_name_indexed on specimens(collection_main_manager_formated_name_indexed);
CREATE INDEX CONCURRENTLY idx_specimens_collection_path on specimens(collection_path);*/
CREATE INDEX CONCURRENTLY idx_specimens_station_visible on specimens(station_visible);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_code on specimens(gtu_code);
/*CREATE INDEX CONCURRENTLY idx_specimens_gtu_path on specimens(gtu_path);*/
CREATE INDEX CONCURRENTLY idx_specimens_gtu_from_date_mask on specimens(gtu_from_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_to_date_mask on specimens(gtu_to_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_to_date on specimens(gtu_to_date);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_from_date on specimens(gtu_from_date);
CREATE INDEX CONCURRENTLY idx_specimens_taxon_name_order_by on specimens(taxon_name_order_by);
/*CREATE INDEX CONCURRENTLY idx_specimens_host_taxon_name_order_by on specimens(host_taxon_name_order_by);*/
CREATE INDEX CONCURRENTLY idx_specimens_chrono_name_order_by on specimens(chrono_name_order_by);
CREATE INDEX CONCURRENTLY idx_specimens_litho_name_order_by on specimens(litho_name_order_by);
CREATE INDEX CONCURRENTLY idx_specimens_lithology_name_order_by on specimens(lithology_name_order_by);
CREATE INDEX CONCURRENTLY idx_specimens_mineral_name_order_by on specimens(mineral_name_order_by);
-- CREATE INDEX CONCURRENTLY idx_specimens_taxon_path on specimens(taxon_path text_pattern_ops);
/*
CREATE INDEX CONCURRENTLY idx_specimens_host_taxon_path on specimens(host_taxon_path);
CREATE INDEX CONCURRENTLY idx_specimens_chrono_path on specimens(chrono_path);
CREATE INDEX CONCURRENTLY idx_specimens_litho_path on specimens(litho_path);
CREATE INDEX CONCURRENTLY idx_specimens_lithology_path on specimens(lithology_path);
CREATE INDEX CONCURRENTLY idx_specimens_mineral_path on specimens(mineral_path);*/
CREATE INDEX CONCURRENTLY idx_specimens_taxon_extinct on specimens(taxon_extinct);
/*CREATE INDEX CONCURRENTLY idx_specimens_host_taxon_extinct on specimens(host_taxon_extinct);*/
CREATE INDEX CONCURRENTLY idx_specimens_ig_num on specimens(ig_num_indexed text_pattern_ops);
/*CREATE INDEX CONCURRENTLY idx_gin_specimens_collection_institution_formated_name_ts on specimens using gin(collection_institution_formated_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_specimens_collection_main_manager_formated_name_ts on specimens using gin(collection_main_manager_formated_name_ts);*/
CREATE INDEX CONCURRENTLY idx_gin_specimens_expedition_name_ts on specimens using gin(expedition_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_specimens_taxon_name_indexed on specimens using gin(taxon_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_chrono_name_indexed on specimens using gin(chrono_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_litho_name_indexed on specimens using gin(litho_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_lithology_name_indexed on specimens using gin(lithology_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_mineral_name_indexed on specimens using gin(mineral_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_gtu_tag_values_indexed on specimens using gin(gtu_tag_values_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_gtu_country_tag_indexed_indexed on specimens using gin(gtu_country_tag_indexed);
CREATE INDEX CONCURRENTLY idx_gist_specimens_gtu_location ON specimens USING GIST ( gtu_location );

CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_ident_ids on specimens using gin(spec_ident_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_coll_ids on specimens using gin(spec_coll_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_don_sel_ids on specimens using gin(spec_don_sel_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimen_indidivuals_ind_ident_ids on specimen_indidivuals using gin(ind_ident_ids);

/*** For Public search ***/
CREATE INDEX CONCURRENTLY idx_gin_specimens_gtu_country_tags on specimens using gin (getTagsIndexedAsArray(gtu_country_tag_value));
