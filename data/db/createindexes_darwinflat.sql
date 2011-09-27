/*** BTree indexes for foreign keys in Darwin Flat table ***/

CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref on darwin_flat(spec_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_ref on darwin_flat(collection_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_parent_ref on darwin_flat(collection_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_expedition_ref on darwin_flat(expedition_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_ref on darwin_flat(gtu_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_parent_ref on darwin_flat(gtu_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_ref on darwin_flat(taxon_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_parent_ref on darwin_flat(taxon_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_level_ref on darwin_flat(taxon_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_ref on darwin_flat(host_taxon_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_parent_ref on darwin_flat(host_taxon_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_level_ref on darwin_flat(host_taxon_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_chrono_ref on darwin_flat(chrono_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_chrono_parent_ref on darwin_flat(chrono_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_chrono_level_ref on darwin_flat(chrono_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_litho_ref on darwin_flat(litho_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_litho_parent_ref on darwin_flat(litho_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_litho_level_ref on darwin_flat(litho_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_lithology_ref on darwin_flat(lithology_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_lithology_parent_ref on darwin_flat(lithology_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_lithology_level_ref on darwin_flat(lithology_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_mineral_ref on darwin_flat(mineral_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_mineral_parent_ref on darwin_flat(mineral_parent_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_mineral_level_ref on darwin_flat(mineral_level_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_ig_ref on darwin_flat(ig_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref on darwin_flat(individual_ref);
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref on darwin_flat(part_ref);



/*** BTree indexes for search purposes in Darwin flat table ***/

CREATE INDEX CONCURRENTLY idx_darwin_flat_category on darwin_flat(category);
/*CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_type on darwin_flat(collection_type);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_code on darwin_flat(collection_code);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_institution_formated_name_indexed on darwin_flat(collection_institution_formated_name_indexed);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_main_manager_formated_name_indexed on darwin_flat(collection_main_manager_formated_name_indexed);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_path on darwin_flat(collection_path);*/
CREATE INDEX CONCURRENTLY idx_darwin_flat_station_visible on darwin_flat(station_visible);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_code on darwin_flat(gtu_code);
/*CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_path on darwin_flat(gtu_path);*/
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_from_date_mask on darwin_flat(gtu_from_date_mask);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_to_date_mask on darwin_flat(gtu_to_date_mask);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_to_date on darwin_flat(gtu_to_date);
CREATE INDEX CONCURRENTLY idx_darwin_flat_gtu_from_date on darwin_flat(gtu_from_date);
CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_name_order_by on darwin_flat(taxon_name_order_by);
/*CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_name_order_by on darwin_flat(host_taxon_name_order_by);*/
CREATE INDEX CONCURRENTLY idx_darwin_flat_chrono_name_order_by on darwin_flat(chrono_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_litho_name_order_by on darwin_flat(litho_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_lithology_name_order_by on darwin_flat(lithology_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_mineral_name_order_by on darwin_flat(mineral_name_order_by);
-- CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_path on darwin_flat(taxon_path text_pattern_ops);
/*
CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_path on darwin_flat(host_taxon_path);
CREATE INDEX CONCURRENTLY idx_darwin_flat_chrono_path on darwin_flat(chrono_path);
CREATE INDEX CONCURRENTLY idx_darwin_flat_litho_path on darwin_flat(litho_path);
CREATE INDEX CONCURRENTLY idx_darwin_flat_lithology_path on darwin_flat(lithology_path);
CREATE INDEX CONCURRENTLY idx_darwin_flat_mineral_path on darwin_flat(mineral_path);*/
CREATE INDEX CONCURRENTLY idx_darwin_flat_taxon_extinct on darwin_flat(taxon_extinct);
/*CREATE INDEX CONCURRENTLY idx_darwin_flat_host_taxon_extinct on darwin_flat(host_taxon_extinct);*/
CREATE INDEX CONCURRENTLY idx_darwin_flat_ig_num on darwin_flat(ig_num_indexed text_pattern_ops);
CREATE INDEX CONCURRENTLY idx_darwin_flat_acquisition_category on darwin_flat(acquisition_category);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_count_min on darwin_flat(individual_count_min);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_count_max on darwin_flat(individual_count_max);
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_count_min on darwin_flat(part_count_min);
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_count_max on darwin_flat(part_count_max);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_type_group on darwin_flat(individual_type_group);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_type_search on darwin_flat(individual_type_search);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_sex on darwin_flat(individual_sex);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_state on darwin_flat(individual_state);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_stage on darwin_flat(individual_stage);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_social_status on darwin_flat(individual_social_status);
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_rock_form on darwin_flat(individual_rock_form);
CREATE INDEX CONCURRENTLY idx_darwin_flat_part on darwin_flat(part);
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_status on darwin_flat(part_status);
CREATE INDEX CONCURRENTLY idx_darwin_flat_building on darwin_flat(building);
CREATE INDEX CONCURRENTLY idx_darwin_flat_floor on darwin_flat(floor);
CREATE INDEX CONCURRENTLY idx_darwin_flat_room on darwin_flat(room);
CREATE INDEX CONCURRENTLY idx_darwin_flat_row on darwin_flat(row);
CREATE INDEX CONCURRENTLY idx_darwin_flat_shelf on darwin_flat(shelf);
CREATE INDEX CONCURRENTLY idx_darwin_flat_container on darwin_flat(container);
CREATE INDEX CONCURRENTLY idx_darwin_flat_sub_container on darwin_flat(sub_container);
CREATE INDEX CONCURRENTLY idx_darwin_flat_container_type on darwin_flat(container_type);
CREATE INDEX CONCURRENTLY idx_darwin_flat_sub_container_type on darwin_flat(sub_container_type);
CREATE INDEX CONCURRENTLY idx_darwin_flat_container_storage on darwin_flat(container_storage);
CREATE INDEX CONCURRENTLY idx_darwin_flat_sub_container_storage on darwin_flat(sub_container_storage);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_is_public on darwin_flat(collection_is_public);
CREATE INDEX CONCURRENTLY idx_darwin_flat_collection_name on darwin_flat(collection_name, spec_ref);

/*** Indexes created for the f***ing necessary group by when searching in darwin_flat ***/
/**** For specimen search ****/
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_category on darwin_flat(spec_ref, category);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_coll_name on darwin_flat(spec_ref, collection_name);
-- CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_taxon_name on darwin_flat(spec_ref, taxon_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_chrono_name on darwin_flat(spec_ref, chrono_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_litho_name on darwin_flat(spec_ref, litho_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_lithology_name on darwin_flat(spec_ref, lithology_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_mineral_name on darwin_flat(spec_ref, mineral_name_order_by);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_expedition_name on darwin_flat(spec_ref, expedition_name_indexed);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_with_types on darwin_flat(spec_ref, with_types);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_with_individuals on darwin_flat(spec_ref, with_individuals);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_ref_with_parts on darwin_flat(spec_ref, with_parts);
CREATE INDEX CONCURRENTLY idx_darwin_flat_spec_host_specimens on darwin_flat(host_specimen_ref);
/**** For individual search ****/
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_category on darwin_flat(individual_ref, category) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_coll_name on darwin_flat(individual_ref, collection_name) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_taxon_name on darwin_flat(individual_ref, taxon_name_order_by) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_chrono_name on darwin_flat(individual_ref, chrono_name_order_by) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_litho_name on darwin_flat(individual_ref, litho_name_order_by) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_lithology_name on darwin_flat(individual_ref, lithology_name_order_by) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_mineral_name on darwin_flat(individual_ref, mineral_name_order_by) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_expedition_name on darwin_flat(individual_ref, expedition_name_indexed) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_type on darwin_flat(individual_ref, individual_type) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_type_group on darwin_flat(individual_ref, individual_type_group) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_type_search on darwin_flat(individual_ref, individual_type_search) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_sex on darwin_flat(individual_ref, individual_sex) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_state on darwin_flat(individual_ref, individual_state) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_stage on darwin_flat(individual_ref, individual_stage) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_social_status on darwin_flat(individual_ref, individual_social_status) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_rock_form on darwin_flat(individual_ref, individual_rock_form) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_individual_count_max on darwin_flat(individual_ref, individual_count_max) where not individual_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_individual_ref_with_parts on darwin_flat(individual_ref, with_parts) where not individual_ref is null;

/**** For part search ****/
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_category on darwin_flat(part_ref, category) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_coll_name on darwin_flat(part_ref, collection_name) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_taxon_name on darwin_flat(part_ref, taxon_name_order_by) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_chrono_name on darwin_flat(part_ref, chrono_name_order_by) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_litho_name on darwin_flat(part_ref, litho_name_order_by) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_lithology_name on darwin_flat(part_ref, lithology_name_order_by) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_mineral_name on darwin_flat(part_ref, mineral_name_order_by) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_expedition_name on darwin_flat(part_ref, expedition_name_indexed) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_type on darwin_flat(part_ref, individual_type) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_type_group on darwin_flat(part_ref, individual_type_group) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_type_search on darwin_flat(part_ref, individual_type_search) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_sex on darwin_flat(part_ref, individual_sex) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_state on darwin_flat(part_ref, individual_state) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_stage on darwin_flat(part_ref, individual_stage) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_social_status on darwin_flat(part_ref, individual_social_status) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_rock_form on darwin_flat(part_ref, individual_rock_form) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_individual_count_max on darwin_flat(part_ref, individual_count_max) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_part on darwin_flat(part_ref, part) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_part_status on darwin_flat(part_ref, part_status) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_building on darwin_flat(part_ref, building) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_floor on darwin_flat(part_ref, "floor") where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_room on darwin_flat(part_ref, room) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_row on darwin_flat(part_ref, "row") where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_container_type on darwin_flat(part_ref, container_type) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_container_storage on darwin_flat(part_ref, container_storage) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_sub_container_type on darwin_flat(part_ref, sub_container_type) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_container on darwin_flat(part_ref, container) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_sub_container_storage on darwin_flat(part_ref, sub_container_storage) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_sub_container on darwin_flat(part_ref, sub_container) where not part_ref is null;
CREATE INDEX CONCURRENTLY idx_darwin_flat_part_ref_part_count_max on darwin_flat(part_ref, part_count_max) where not part_ref is null;


/*CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_collection_institution_formated_name_ts on darwin_flat using gin(collection_institution_formated_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_collection_main_manager_formated_name_ts on darwin_flat using gin(collection_main_manager_formated_name_ts);*/
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_expedition_name_ts on darwin_flat using gin(expedition_name_ts);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_taxon_name_indexed on darwin_flat using gin(taxon_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_chrono_name_indexed on darwin_flat using gin(chrono_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_litho_name_indexed on darwin_flat using gin(litho_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_lithology_name_indexed on darwin_flat using gin(lithology_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_mineral_name_indexed on darwin_flat using gin(mineral_name_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_gtu_tag_values_indexed on darwin_flat using gin(gtu_tag_values_indexed);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_gtu_country_tag_indexed_indexed on darwin_flat using gin(gtu_country_tag_indexed);
CREATE INDEX CONCURRENTLY idx_gist_darwin_flat_gtu_location ON darwin_flat USING GIST ( gtu_location );

CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_spec_ident_ids on darwin_flat using gin(spec_ident_ids);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_spec_coll_ids on darwin_flat using gin(spec_coll_ids);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_spec_don_sel_ids on darwin_flat using gin(spec_don_sel_ids);
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_ind_ident_ids on darwin_flat using gin(ind_ident_ids);

/*** For Public search ***/
CREATE INDEX CONCURRENTLY idx_gin_darwin_flat_gtu_country_tags on darwin_flat using gin (getTagsIndexedAsArray(gtu_country_tag_value));
