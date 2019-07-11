/*** BTree indexes for foreign keys in Darwin Flat table ***/

CREATE INDEX CONCURRENTLY idx_gin_specimens_gtu_tag_values_indexed on specimens using gin(gtu_tag_values_indexed);
CREATE INDEX CONCURRENTLY idx_gin_specimens_gtu_country_tag_indexed_indexed on specimens using gin(gtu_country_tag_indexed);
CREATE INDEX CONCURRENTLY idx_gist_specimens_gtu_location ON specimens USING GIST ( gtu_location );

CREATE INDEX CONCURRENTLY idx_specimens_category on specimens(category);

/*** BTree indexes for search purposes in Darwin flat table ***/
CREATE INDEX CONCURRENTLY idx_specimens_station_visible on specimens(station_visible);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_code ON specimens USING gin
  (gtu_code gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_from_date_mask on specimens(gtu_from_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_to_date_mask on specimens(gtu_to_date_mask);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_to_date on specimens(gtu_to_date);
CREATE INDEX CONCURRENTLY idx_specimens_gtu_from_date on specimens(gtu_from_date);
CREATE INDEX CONCURRENTLY idx_specimens_taxon_name_indexed on specimens(taxon_name_indexed);

CREATE INDEX CONCURRENTLY idx_specimens_collection_is_public on specimens(collection_is_public);
CREATE INDEX CONCURRENTLY idx_specimens_collection_name on specimens(collection_name);

CREATE INDEX CONCURRENTLY idx_gin_trgm_specimens_expedition_name_indexed on specimens using gin(expedition_name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_specimens_taxon_name_indexed on specimens using gin(taxon_name_indexed gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_specimens_taxon_path on specimens using gin(taxon_path gin_trgm_ops);
CREATE INDEX CONCURRENTLY idx_gin_trgm_specimens_ig_num ON specimens USING gin (ig_num_indexed gin_trgm_ops);

CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_ident_ids on specimens using gin(spec_ident_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_coll_ids on specimens using gin(spec_coll_ids);
CREATE INDEX CONCURRENTLY idx_gin_specimens_spec_don_sel_ids on specimens using gin(spec_don_sel_ids);
