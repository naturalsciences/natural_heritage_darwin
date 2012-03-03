SET search_path = darwin2, public;

BEGIN;

DROP TABLE IF EXISTS darwin_flat CASCADE;

DROP SEQUENCE IF EXISTS darwin_flat_id_seq;

CREATE SEQUENCE darwin_flat_id_seq;

CREATE TABLE darwin_flat
(
  id bigint NOT NULL DEFAULT nextval('darwin_flat_id_seq'), -- Unique identifier of a darwin flat entry
  spec_ref integer NOT NULL, -- Reference of specimen concerned
  category character varying, -- Specimen concerned category: physical, observation,...
  collection_ref integer NOT NULL DEFAULT 0, -- Reference of collection the specimen concerned belongs to
  collection_type character varying, -- Type of collection: mixed, physical or observation
  collection_code character varying, -- Collection code
  collection_name character varying, -- Collection name - i.e.: Vertebrates,...
  collection_is_public boolean NOT NULL DEFAULT true, -- Flag telling if collection is public or not
  collection_parent_ref integer DEFAULT 0, -- Reference of parent collection
  collection_path character varying, -- Hierarchical path of current collection
  expedition_ref integer NOT NULL DEFAULT 0, -- Reference of expedition the current specimen was collected in
  expedition_name character varying, -- Name of the expedition
  expedition_name_ts tsvector, -- Name of the expedition - ts vector form - for searches purposes
  expedition_name_indexed character varying, -- Name of the expedition - indexed form with fullToIndex - for ordering purposes
  station_visible boolean, -- Flag telling if the sampling location can be publically visible or not
  gtu_ref integer NOT NULL DEFAULT 0, -- Sampling location referenced
  gtu_code character varying, -- Sampling location referenced code
  gtu_parent_ref integer DEFAULT 0, -- Sampling location referenced parent
  gtu_path character varying, -- Sampling location hierarchical path
  gtu_from_date_mask integer, -- Sampling location from date mask
  gtu_from_date timestamp without time zone, -- Sampling location from date
  gtu_to_date_mask integer, -- Sampling location to date mask
  gtu_to_date timestamp without time zone, -- Sampling location to date
  gtu_tag_values_indexed character varying[], -- Array of all the tags entered for this gtu - all the tags are of the indexed form with fullToIndex function
  gtu_country_tag_value character varying, -- List of "Administrative area-Country" tags associated to the sampling location referenced
  gtu_country_tag_indexed character varying[], -- List of "Administrative area-Country" tags associated to the sampling location referenced  all the tags are of the indexed form with fullToIndex function
  gtu_location geography(Polygon,4326), -- GTU location - postgis geography object
  taxon_ref integer NOT NULL DEFAULT 0, -- Taxon unit referenced
  taxon_name character varying, -- Taxon unit referenced name
  taxon_name_indexed tsvector, -- Taxon unit referenced name - ts vector form - used for searches purposes
  taxon_name_order_by character varying, -- Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes
  taxon_level_ref integer NOT NULL DEFAULT 0, -- Taxon unit referenced level
  taxon_level_name character varying, -- Taxon unit referenced level name
  taxon_status character varying, -- Taxon unit referenced status: valid, invalid,...
  taxon_path character varying, -- Taxon unit referenced hierarchical path
  taxon_parent_ref integer DEFAULT 0, -- Taxon unit referenced parenty
  taxon_extinct boolean, -- Taxon unit referenced flag telling if the unit is extinct or not
  litho_ref integer NOT NULL DEFAULT 0, -- Litho unit referenced
  litho_name character varying, -- Litho unit referenced name
  litho_name_indexed tsvector, -- Litho unit referenced name - ts vector form - used for searches purposes
  litho_name_order_by character varying, -- Litho unit referenced name - indexed form with fullToIndex - used for ordering purposes
  litho_level_ref integer NOT NULL DEFAULT 0, -- Litho unit referenced level
  litho_level_name character varying, -- Litho unit referenced level name
  litho_status character varying, -- Litho unit referenced status: valid, invalid,...
  litho_local boolean NOT NULL DEFAULT false,
  litho_color character varying,
  litho_path character varying, -- Litho unit referenced hierarchical path
  litho_parent_ref integer DEFAULT 0, -- Litho unit referenced parenty
  chrono_ref integer NOT NULL DEFAULT 0, -- Chrono unit referenced
  chrono_name character varying, -- Chrono unit referenced name
  chrono_name_indexed tsvector, -- Chrono unit referenced name - ts vector form - used for searches purposes
  chrono_name_order_by character varying, -- Chrono unit referenced name - indexed form with fullToIndex - used for ordering purposes
  chrono_level_ref integer NOT NULL DEFAULT 0, -- Chrono unit referenced level
  chrono_level_name character varying, -- Chrono unit referenced level name
  chrono_status character varying, -- Chrono unit referenced status: valid, invalid,...
  chrono_local boolean NOT NULL DEFAULT false, -- Flag telling if the mineral unit name is a local appelation or not
  chrono_color character varying, -- Hexadecimal value of color associated to the mineral unit
  chrono_path character varying, -- Chrono unit referenced hierarchical path
  chrono_parent_ref integer DEFAULT 0, -- Chrono unit referenced parenty
  lithology_ref integer NOT NULL DEFAULT 0, -- Lithology unit referenced
  lithology_name character varying, -- Lithology unit referenced name
  lithology_name_indexed tsvector, -- Lithology unit referenced name - ts vector form - used for searches purposes
  lithology_name_order_by character varying, -- Lithology unit referenced name - indexed form with fullToIndex - used for ordering purposes
  lithology_level_ref integer NOT NULL DEFAULT 0, -- Lithology unit referenced level
  lithology_level_name character varying, -- Lithology unit referenced level name
  lithology_status character varying, -- Lithology unit referenced status: valid, invalid,...
  lithology_local boolean NOT NULL DEFAULT false,
  lithology_color character varying,
  lithology_path character varying, -- Lithology unit referenced hierarchical path
  lithology_parent_ref integer DEFAULT 0, -- Lithology unit referenced parenty
  mineral_ref integer NOT NULL DEFAULT 0, -- Mineral unit referenced
  mineral_name character varying, -- Mineral unit referenced name
  mineral_name_indexed tsvector, -- Mineral unit referenced name - ts vector form - used for searches purposes
  mineral_name_order_by character varying, -- Mineral unit referenced name - indexed form with fullToIndex - used for ordering purposes
  mineral_level_ref integer NOT NULL DEFAULT 0, -- Mineral unit referenced level
  mineral_level_name character varying, -- Mineral unit referenced level name
  mineral_status character varying, -- Mineral unit referenced status: valid, invalid,...
  mineral_local boolean NOT NULL DEFAULT false,
  mineral_color character varying,
  mineral_path character varying, -- Mineral unit referenced hierarchical path
  mineral_parent_ref integer DEFAULT 0, -- Mineral unit referenced parenty
  host_taxon_ref integer NOT NULL DEFAULT 0, -- Host Taxon unit referenced
  host_relationship character varying,
  host_taxon_name character varying, -- Host Taxon unit referenced name
  host_taxon_name_indexed tsvector, -- Host Taxon unit referenced name - ts vector form - used for searches purposes
  host_taxon_name_order_by character varying, -- Host Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes
  host_taxon_level_ref integer NOT NULL DEFAULT 0, -- Host Taxon unit referenced level
  host_taxon_level_name character varying, -- Host Taxon unit referenced level name
  host_taxon_status character varying, -- Host Taxon unit referenced status: valid, invalid,...
  host_taxon_path character varying, -- Host Taxon unit referenced hierarchical path
  host_taxon_parent_ref integer DEFAULT 0, -- Host Taxon unit referenced parenty
  host_taxon_extinct boolean, -- Host Taxon unit referenced flag telling if the unit is extinct or not
  host_specimen_ref integer, -- Reference/Id of the host specimen
  ig_ref integer, -- General Inventory number (I.G. Num) referenced
  ig_num character varying, -- General Inventory number (I.G. Num) referenced - The number concerned
  ig_num_indexed character varying, -- General Inventory number (I.G. Num) referenced - The number concerned - indexed form composed with fullToIndex
  ig_date_mask integer, -- General Inventory number (I.G. Num) referenced - Date of attribution mask
  ig_date date, -- General Inventory number (I.G. Num) referenced - Date of attribution
  acquisition_category character varying, -- Specimen acquisition category
  acquisition_date_mask integer, -- Specimen acquisition date mask
  acquisition_date date, -- Specimen acquisition date
  with_types boolean NOT NULL DEFAULT false, -- Flag telling if there are types for current specimen
  with_individuals boolean NOT NULL DEFAULT false, -- Flag telling if there are individuals for current specimen
  individual_ref integer, -- Reference of specimen individual - references to id of individual in specimen_individuals table - Null if nothing referenced
  individual_type character varying NOT NULL DEFAULT 'specimen'::character varying, -- Type
  individual_type_group character varying NOT NULL DEFAULT 'specimen'::character varying, -- Type group - Grouping of types appelations used for internal search
  individual_type_search character varying NOT NULL DEFAULT 'specimen'::character varying, -- Type search - Grouping of types appelations used for external searches
  individual_sex character varying NOT NULL DEFAULT 'undefined'::character varying, -- Sex: Male, Female, Hermaphrodit,...
  individual_state character varying NOT NULL DEFAULT 'not applicable'::character varying, -- Sex state if applicable: Ovigerous, Pregnant,...
  individual_stage character varying NOT NULL DEFAULT 'undefined'::character varying, -- Stage: Adult, Nymph, Larvae,...
  individual_social_status character varying NOT NULL DEFAULT 'not applicable'::character varying, -- Social status if applicable: Worker, Queen, King, Fighter,...
  individual_rock_form character varying NOT NULL DEFAULT 'not applicable'::character varying, -- Rock form if applicable: Cubic, Orthorhombic,...
  individual_count_min integer, -- Minimum number of individuals
  individual_count_max integer, -- Maximum number of individuals
  with_parts boolean NOT NULL DEFAULT false, -- Flag telling if they are parts for the current individual
  part_ref integer, -- Reference of part - coming from specimen_parts table (id field) - set to null if no references
  part character varying, -- Part name: wing, tail, toes,...
  part_status character varying, -- Part status: intact, lost, stolen,...
  institution_ref integer, -- Institution (people) where the current part is stored
  building character varying, -- Building where the current part is stored
  floor character varying, -- Floor where the current part is stored
  room character varying, -- Room where the current part is stored
  "row" character varying, -- Row of the conservatory where the current part is stored
  shelf character varying, -- Shelf where the current part is stored
  container_type character varying, -- Container type: box, slide,...
  container_storage character varying, -- Container storage: dry, alcohool, formol,...
  container character varying, -- Container code
  sub_container_type character varying, -- Sub-Container type: box, slide,...
  sub_container_storage character varying, -- Sub-Container storage: dry, alcohool, formol,...
  sub_container character varying, -- Sub container code
  part_count_min integer, -- Minimum number of parts stored
  part_count_max integer, -- Maximum number of parts stored
  specimen_status character varying, -- Tells the status of part concerned: lost, damaged, good shape,...
  complete boolean, -- Flag telling if the specimen is complete or not
  surnumerary boolean, -- Tells if this part/individual has been added after first inventory
  spec_ident_ids integer[], -- Array of identifiers referenced in this specimen
  ind_ident_ids integer[], -- Array of identifiers referenced in this individual
  spec_coll_ids integer[], -- Array of collectors referenced in this specimen
  spec_don_sel_ids integer[], -- Array of donators or sellers referenced in this specimen
  CONSTRAINT pk_darwin_flat PRIMARY KEY (id),
  CONSTRAINT fk_darwin_flat_host_specimen_ref FOREIGN KEY (host_specimen_ref)
      REFERENCES specimens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET DEFAULT,
  CONSTRAINT fk_darwin_flat_spec_ref FOREIGN KEY (spec_ref)
      REFERENCES specimens (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
ALTER TABLE darwin_flat OWNER TO darwin2;
GRANT ALL ON TABLE darwin_flat TO darwin2;
GRANT SELECT ON TABLE darwin_flat TO d2viewer;
COMMENT ON TABLE darwin_flat IS 'Flat table compiling all specimens data (catalogues data included - used for search purposes';
COMMENT ON COLUMN darwin_flat.id IS 'Unique identifier of a darwin flat entry';
COMMENT ON COLUMN darwin_flat.spec_ref IS 'Reference of specimen concerned';
COMMENT ON COLUMN darwin_flat.category IS 'Specimen concerned category: physical, observation,...';
COMMENT ON COLUMN darwin_flat.collection_ref IS 'Reference of collection the specimen concerned belongs to';
COMMENT ON COLUMN darwin_flat.collection_code IS 'Collection code';
COMMENT ON COLUMN darwin_flat.collection_name IS 'Collection name - i.e.: Vertebrates,...';
COMMENT ON COLUMN darwin_flat.collection_is_public IS 'Flag telling if collection is public or not';
COMMENT ON COLUMN darwin_flat.collection_parent_ref IS 'Reference of parent collection';
COMMENT ON COLUMN darwin_flat.collection_path IS 'Hierarchical path of current collection';
COMMENT ON COLUMN darwin_flat.expedition_ref IS 'Reference of expedition the current specimen was collected in';
COMMENT ON COLUMN darwin_flat.expedition_name IS 'Name of the expedition';
COMMENT ON COLUMN darwin_flat.expedition_name_ts IS 'Name of the expedition - ts vector form - for searches purposes';
COMMENT ON COLUMN darwin_flat.expedition_name_indexed IS 'Name of the expedition - indexed form with fullToIndex - for ordering purposes';
COMMENT ON COLUMN darwin_flat.station_visible IS 'Flag telling if the sampling location can be publically visible or not';
COMMENT ON COLUMN darwin_flat.gtu_ref IS 'Sampling location referenced';
COMMENT ON COLUMN darwin_flat.gtu_code IS 'Sampling location referenced code';
COMMENT ON COLUMN darwin_flat.gtu_parent_ref IS 'Sampling location referenced parent';
COMMENT ON COLUMN darwin_flat.gtu_path IS 'Sampling location hierarchical path';
COMMENT ON COLUMN darwin_flat.gtu_from_date_mask IS 'Sampling location from date mask';
COMMENT ON COLUMN darwin_flat.gtu_from_date IS 'Sampling location from date';
COMMENT ON COLUMN darwin_flat.gtu_to_date_mask IS 'Sampling location to date mask';
COMMENT ON COLUMN darwin_flat.gtu_to_date IS 'Sampling location to date';
COMMENT ON COLUMN darwin_flat.gtu_tag_values_indexed IS 'Array of all the tags entered for this gtu - all the tags are of the indexed form with fullToIndex function';
COMMENT ON COLUMN darwin_flat.gtu_country_tag_value IS 'List of "Administrative area-Country" tags associated to the sampling location referenced';
COMMENT ON COLUMN darwin_flat.gtu_country_tag_indexed IS 'List of "Administrative area-Country" tags associated to the sampling location referenced  all the tags are of the indexed form with fullToIndex function';
COMMENT ON COLUMN darwin_flat.taxon_ref IS 'Taxon unit referenced';
COMMENT ON COLUMN darwin_flat.taxon_name IS 'Taxon unit referenced name';
COMMENT ON COLUMN darwin_flat.taxon_name_indexed IS 'Taxon unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.taxon_name_order_by IS 'Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.taxon_level_ref IS 'Taxon unit referenced level';
COMMENT ON COLUMN darwin_flat.taxon_level_name IS 'Taxon unit referenced level name';
COMMENT ON COLUMN darwin_flat.taxon_status IS 'Taxon unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.taxon_path IS 'Taxon unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.taxon_parent_ref IS 'Taxon unit referenced parenty';
COMMENT ON COLUMN darwin_flat.taxon_extinct IS 'Taxon unit referenced flag telling if the unit is extinct or not';
COMMENT ON COLUMN darwin_flat.litho_ref IS 'Litho unit referenced';
COMMENT ON COLUMN darwin_flat.litho_name IS 'Litho unit referenced name';
COMMENT ON COLUMN darwin_flat.litho_name_indexed IS 'Litho unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.litho_name_order_by IS 'Litho unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.litho_level_ref IS 'Litho unit referenced level';
COMMENT ON COLUMN darwin_flat.litho_level_name IS 'Litho unit referenced level name';
COMMENT ON COLUMN darwin_flat.litho_status IS 'Litho unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.litho_path IS 'Litho unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.litho_parent_ref IS 'Litho unit referenced parenty';
COMMENT ON COLUMN darwin_flat.chrono_ref IS 'Chrono unit referenced';
COMMENT ON COLUMN darwin_flat.chrono_name IS 'Chrono unit referenced name';
COMMENT ON COLUMN darwin_flat.chrono_name_indexed IS 'Chrono unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.chrono_name_order_by IS 'Chrono unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.chrono_level_ref IS 'Chrono unit referenced level';
COMMENT ON COLUMN darwin_flat.chrono_level_name IS 'Chrono unit referenced level name';
COMMENT ON COLUMN darwin_flat.chrono_status IS 'Chrono unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.chrono_local IS 'Flag telling if the mineral unit name is a local appelation or not';
COMMENT ON COLUMN darwin_flat.chrono_color IS 'Hexadecimal value of color associated to the mineral unit';
COMMENT ON COLUMN darwin_flat.chrono_path IS 'Chrono unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.chrono_parent_ref IS 'Chrono unit referenced parenty';
COMMENT ON COLUMN darwin_flat.lithology_ref IS 'Lithology unit referenced';
COMMENT ON COLUMN darwin_flat.lithology_name IS 'Lithology unit referenced name';
COMMENT ON COLUMN darwin_flat.lithology_name_indexed IS 'Lithology unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.lithology_name_order_by IS 'Lithology unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.lithology_level_ref IS 'Lithology unit referenced level';
COMMENT ON COLUMN darwin_flat.lithology_level_name IS 'Lithology unit referenced level name';
COMMENT ON COLUMN darwin_flat.lithology_status IS 'Lithology unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.lithology_path IS 'Lithology unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.lithology_parent_ref IS 'Lithology unit referenced parenty';
COMMENT ON COLUMN darwin_flat.mineral_ref IS 'Mineral unit referenced';
COMMENT ON COLUMN darwin_flat.mineral_name IS 'Mineral unit referenced name';
COMMENT ON COLUMN darwin_flat.mineral_name_indexed IS 'Mineral unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.mineral_name_order_by IS 'Mineral unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.mineral_level_ref IS 'Mineral unit referenced level';
COMMENT ON COLUMN darwin_flat.mineral_level_name IS 'Mineral unit referenced level name';
COMMENT ON COLUMN darwin_flat.mineral_status IS 'Mineral unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.mineral_path IS 'Mineral unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.mineral_parent_ref IS 'Mineral unit referenced parenty';
COMMENT ON COLUMN darwin_flat.host_taxon_ref IS 'Host Taxon unit referenced';
COMMENT ON COLUMN darwin_flat.host_taxon_name IS 'Host Taxon unit referenced name';
COMMENT ON COLUMN darwin_flat.host_taxon_name_indexed IS 'Host Taxon unit referenced name - ts vector form - used for searches purposes';
COMMENT ON COLUMN darwin_flat.host_taxon_name_order_by IS 'Host Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes';
COMMENT ON COLUMN darwin_flat.host_taxon_level_ref IS 'Host Taxon unit referenced level';
COMMENT ON COLUMN darwin_flat.host_taxon_level_name IS 'Host Taxon unit referenced level name';
COMMENT ON COLUMN darwin_flat.host_taxon_status IS 'Host Taxon unit referenced status: valid, invalid,...';
COMMENT ON COLUMN darwin_flat.host_taxon_path IS 'Host Taxon unit referenced hierarchical path';
COMMENT ON COLUMN darwin_flat.host_taxon_parent_ref IS 'Host Taxon unit referenced parenty';
COMMENT ON COLUMN darwin_flat.host_taxon_extinct IS 'Host Taxon unit referenced flag telling if the unit is extinct or not';
COMMENT ON COLUMN darwin_flat.host_specimen_ref IS 'Reference/Id of the host specimen';
COMMENT ON COLUMN darwin_flat.host_relationship IS 'Type of relationship between host and hosted item';
COMMENT ON COLUMN darwin_flat.ig_ref IS 'General Inventory number (I.G. Num) referenced';
COMMENT ON COLUMN darwin_flat.ig_num IS 'General Inventory number (I.G. Num) referenced - The number concerned';
COMMENT ON COLUMN darwin_flat.ig_num_indexed IS 'General Inventory number (I.G. Num) referenced - The number concerned - indexed form composed with fullToIndex';
COMMENT ON COLUMN darwin_flat.ig_date_mask IS 'General Inventory number (I.G. Num) referenced - Date of attribution mask';
COMMENT ON COLUMN darwin_flat.ig_date IS 'General Inventory number (I.G. Num) referenced - Date of attribution';
COMMENT ON COLUMN darwin_flat.acquisition_category IS 'Specimen acquisition category';
COMMENT ON COLUMN darwin_flat.acquisition_date_mask IS 'Specimen acquisition date mask';
COMMENT ON COLUMN darwin_flat.acquisition_date IS 'Specimen acquisition date';
COMMENT ON COLUMN darwin_flat.with_types IS 'Flag telling if there are types for current specimen';
COMMENT ON COLUMN darwin_flat.with_individuals IS 'Flag telling if there are individuals for current specimen';
COMMENT ON COLUMN darwin_flat.individual_ref IS 'Reference of specimen individual - references to id of individual in specimen_individuals table - Null if nothing referenced';
COMMENT ON COLUMN darwin_flat.individual_type IS 'Type';
COMMENT ON COLUMN darwin_flat.individual_type_group IS 'Type group - Grouping of types appelations used for internal search';
COMMENT ON COLUMN darwin_flat.individual_type_search IS 'Type search - Grouping of types appelations used for external searches';
COMMENT ON COLUMN darwin_flat.individual_sex IS 'Sex: Male, Female, Hermaphrodit,...';
COMMENT ON COLUMN darwin_flat.individual_state IS 'Sex state if applicable: Ovigerous, Pregnant,...';
COMMENT ON COLUMN darwin_flat.individual_stage IS 'Stage: Adult, Nymph, Larvae,...';
COMMENT ON COLUMN darwin_flat.individual_social_status IS 'Social status if applicable: Worker, Queen, King, Fighter,...';
COMMENT ON COLUMN darwin_flat.individual_rock_form IS 'Rock form if applicable: Cubic, Orthorhombic,...';
COMMENT ON COLUMN darwin_flat.individual_count_min IS 'Minimum number of individuals';
COMMENT ON COLUMN darwin_flat.individual_count_max IS 'Maximum number of individuals';
COMMENT ON COLUMN darwin_flat.with_parts IS 'Flag telling if they are parts for the current individual';
COMMENT ON COLUMN darwin_flat.part_ref IS 'Reference of part - coming from specimen_parts table (id field) - set to null if no references';
COMMENT ON COLUMN darwin_flat.part IS 'Part name: wing, tail, toes,...';
COMMENT ON COLUMN darwin_flat.part_status IS 'Part status: intact, lost, stolen,...';
COMMENT ON COLUMN darwin_flat.institution_ref IS 'Institution (people) where the current part is stored';
COMMENT ON COLUMN darwin_flat.building IS 'Building where the current part is stored';
COMMENT ON COLUMN darwin_flat.floor IS 'Floor where the current part is stored';
COMMENT ON COLUMN darwin_flat.room IS 'Room where the current part is stored';
COMMENT ON COLUMN darwin_flat."row" IS 'Row of the conservatory where the current part is stored';
COMMENT ON COLUMN darwin_flat.shelf IS 'Shelf where the current part is stored';
COMMENT ON COLUMN darwin_flat.container_type IS 'Container type: box, slide,...';
COMMENT ON COLUMN darwin_flat.container_storage IS 'Container storage: dry, alcohool, formol,...';
COMMENT ON COLUMN darwin_flat.container IS 'Container code';
COMMENT ON COLUMN darwin_flat.sub_container_type IS 'Sub-Container type: box, slide,...';
COMMENT ON COLUMN darwin_flat.sub_container_storage IS 'Sub-Container storage: dry, alcohool, formol,...';
COMMENT ON COLUMN darwin_flat.sub_container IS 'Sub container code';
COMMENT ON COLUMN darwin_flat.part_count_min IS 'Minimum number of parts stored';
COMMENT ON COLUMN darwin_flat.part_count_max IS 'Maximum number of parts stored';
COMMENT ON COLUMN darwin_flat.specimen_status IS 'Tells the status of part concerned: lost, damaged, good shape,...';
COMMENT ON COLUMN darwin_flat.complete IS 'Flag telling if the specimen is complete or not';
COMMENT ON COLUMN darwin_flat.surnumerary IS 'Tells if this part/individual has been added after first inventory';
COMMENT ON COLUMN darwin_flat.spec_ident_ids IS 'Array of identifiers referenced in this specimen';
COMMENT ON COLUMN darwin_flat.ind_ident_ids IS 'Array of identifiers referenced in this individual';
COMMENT ON COLUMN darwin_flat.spec_coll_ids IS 'Array of collectors referenced in this specimen';
COMMENT ON COLUMN darwin_flat.spec_don_sel_ids IS 'Array of donators or sellers referenced in this specimen';

INSERT INTO darwin_flat
(
    spec_ref,
    category,
    collection_ref,
    collection_type,
    collection_code,
    collection_name,
    collection_is_public,
    collection_parent_ref,
    collection_path,
    expedition_ref,
    expedition_name,
    expedition_name_ts,
    expedition_name_indexed,
    station_visible,
    gtu_ref,
    gtu_code,
    gtu_parent_ref,
    gtu_path,
    gtu_from_date_mask,
    gtu_from_date,
    gtu_to_date_mask,
    gtu_to_date,
    gtu_tag_values_indexed,
    gtu_country_tag_value,
    gtu_country_tag_indexed,
    gtu_location,
    spec_ident_ids,
    ind_ident_ids,
    spec_coll_ids,
    spec_don_sel_ids,
    taxon_ref,
    taxon_name,
    taxon_name_indexed,
    taxon_name_order_by,
    taxon_level_ref,
    taxon_level_name,
    taxon_status,
    taxon_path,
    taxon_parent_ref,
    taxon_extinct,
    litho_ref,
    litho_name,
    litho_name_indexed,
    litho_name_order_by,
    litho_level_ref,
    litho_level_name,
    litho_status,
    litho_local,
    litho_color,
    litho_path,
    litho_parent_ref,
    chrono_ref,
    chrono_name,
    chrono_name_indexed,
    chrono_name_order_by,
    chrono_level_ref,
    chrono_level_name,
    chrono_status,
    chrono_local,
    chrono_color,
    chrono_path,
    chrono_parent_ref,
    lithology_ref,
    lithology_name,
    lithology_name_indexed,
    lithology_name_order_by,
    lithology_level_ref,
    lithology_level_name,
    lithology_status,
    lithology_local,
    lithology_color,
    lithology_path,
    lithology_parent_ref,
    mineral_ref,
    mineral_name,
    mineral_name_indexed,
    mineral_name_order_by,
    mineral_level_ref,
    mineral_level_name,
    mineral_status,
    mineral_local,
    mineral_color,
    mineral_path,
    mineral_parent_ref,
    host_taxon_ref,
    host_relationship,
    host_taxon_name,
    host_taxon_name_indexed,
    host_taxon_name_order_by,
    host_taxon_level_ref,
    host_taxon_level_name,
    host_taxon_status,
    host_taxon_path,
    host_taxon_parent_ref,
    host_taxon_extinct,
    host_specimen_ref,
    ig_ref,
    ig_num,
    ig_num_indexed,
    ig_date_mask,
    ig_date,
    acquisition_category,
    acquisition_date_mask,
    acquisition_date,
    individual_ref,
    individual_type,
    individual_type_group,
    individual_type_search,
    individual_sex,
    individual_state,
    individual_stage,
    individual_social_status,
    individual_rock_form,
    individual_count_min,
    individual_count_max,
    part_ref,
    part,
    part_status,
    institution_ref,
    building,
    "floor",
    "room",
    "row",
    shelf,
    "container",
    sub_container,
    container_type,
    sub_container_type,
    container_storage,
    sub_container_storage,
    part_count_min,
    part_count_max,
    specimen_status,
    "complete",
    surnumerary
)
(SELECT
  spec.id,
  spec.category,
  spec.collection_ref,
  coll.collection_type,
  coll.code,
  coll.name,
  coll.is_public,
  coll.parent_ref,
  coll.path,
  spec.expedition_ref,
  exp.name,
  exp.name_ts,
  exp.name_indexed,
  spec.station_visible,
  spec.gtu_ref,
  gtu.code,
  gtu.parent_ref,
  gtu.path,
  gtu.gtu_from_date_mask,
  gtu.gtu_from_date,
  gtu.gtu_to_date_mask,
  gtu.gtu_to_date,
  gtu.tag_values_indexed,
  taggr.tag_value,
  (select lineToTagArray(tag_value) FROM tag_groups taggr WHERE gtu.id = taggr.gtu_ref AND taggr.group_name_indexed = 'administrativearea' AND taggr.sub_group_name_indexed = 'country'),
  gtu.location,
  ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimens' and p.people_type='identifier' and i.record_id = spec.id ),
  ( select array_accum(DISTINCT people_ref) from catalogue_people p INNER JOIN identifications i ON p.record_id = i.id AND p.referenced_relation = 'identifications' where i.referenced_relation='specimen_individuals' and p.people_type='identifier' and i.record_id = sInd.id ),
  ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='collector' and record_id = spec.id),
  ( select array_accum(DISTINCT people_ref) from catalogue_people where referenced_relation='specimens' and people_type='donator' and record_id = spec.id),
  spec.taxon_ref,
  taxon.name,
  taxon.name_indexed,
  taxon.name_order_by,
  taxon.level_ref,
  taxon_level.level_name,
  taxon.status,
  taxon.path,
  taxon.parent_ref,
  taxon.extinct,
  spec.litho_ref,
  litho.name,
  litho.name_indexed,
  litho.name_order_by,
  litho.level_ref,
  litho_level.level_name,
  litho.status,
  litho.local_naming,
  litho.color,
  litho.path,
  litho.parent_ref,
  spec.chrono_ref,
  chrono.name,
  chrono.name_indexed,
  chrono.name_order_by,
  chrono.level_ref,
  chrono_level.level_name,
  chrono.status,
  chrono.local_naming,
  chrono.color,
  chrono.path,
  chrono.parent_ref,
  spec.lithology_ref,
  lithology.name,
  lithology.name_indexed,
  lithology.name_order_by,
  lithology.level_ref,
  lithology_level.level_name,
  lithology.status,
  lithology.local_naming,
  lithology.color,
  lithology.path,
  lithology.parent_ref,
  spec.mineral_ref,
  mineral.name,
  mineral.name_indexed,
  mineral.name_order_by,
  mineral.level_ref,
  mineral_level.level_name,
  mineral.status,
  mineral.local_naming,
  mineral.color,
  mineral.path,
  mineral.parent_ref,
  spec.host_taxon_ref,
  spec.host_relationship,
  host_taxon.name,
  host_taxon.name_indexed,
  host_taxon.name_order_by,
  host_taxon.level_ref,
  host_taxon_level.level_name,
  host_taxon.status,
  host_taxon.path,
  host_taxon.parent_ref,
  host_taxon.extinct,
  spec.host_specimen_ref,
  spec.ig_ref,
  igs.ig_num,
  igs.ig_num_indexed,
  igs.ig_date_mask,
  igs.ig_date,
  spec.acquisition_category,
  spec.acquisition_date_mask,
  spec.acquisition_date,
  sInd.id,
  coalesce(sInd.type, 'specimen'),
  coalesce(sInd.type_group, 'specimen'),
  coalesce(sInd.type_search, 'specimen'),
  coalesce(sInd.sex, 'undefined'),
  coalesce(sInd.state, 'not applicable'),
  coalesce(sInd.stage, 'undefined'),
  coalesce(sInd.social_status, 'not applicable'),
  coalesce(sInd.rock_form, 'not applicable'),
  coalesce(sInd.specimen_individuals_count_min, 1),
  coalesce(sInd.specimen_individuals_count_max, 1),
  sPart.id,
  sPart.specimen_part,
  sPart.specimen_status,
  sPart.institution_ref,
  sPart.building,
  sPart.floor,
  sPart.room,
  sPart.row,
  sPart.shelf,
  sPart.container,
  sPart.sub_container,
  sPart.container_type,
  sPart.sub_container_type,
  sPart.container_storage,
  sPart.sub_container_storage,
  sPart.specimen_part_count_min,
  sPart.specimen_part_count_max,
  sPart.specimen_status,
  sPart.complete,
  sPart.surnumerary
FROM specimens spec
     LEFT JOIN igs ON spec.ig_ref = igs.id
     INNER JOIN collections coll ON spec.collection_ref = coll.id
     INNER JOIN expeditions exp ON spec.expedition_ref = exp.id
     INNER JOIN (gtu LEFT JOIN tag_groups taggr ON gtu.id = taggr.gtu_ref 
                                                AND taggr.group_name_indexed = 'administrativearea' 
                                                AND sub_group_name_indexed = 'country'
                ) ON spec.gtu_ref = gtu.id
     INNER JOIN (taxonomy taxon INNER JOIN catalogue_levels taxon_level ON taxon.level_ref = taxon_level.id
                ) ON spec.taxon_ref = taxon.id
     INNER JOIN (chronostratigraphy chrono INNER JOIN catalogue_levels chrono_level ON chrono.level_ref = chrono_level.id
                ) ON spec.chrono_ref = chrono.id
     INNER JOIN (lithostratigraphy litho INNER JOIN catalogue_levels litho_level ON litho.level_ref = litho_level.id
                ) ON spec.litho_ref = litho.id
     INNER JOIN (lithology INNER JOIN catalogue_levels lithology_level ON lithology.level_ref = lithology_level.id
                ) ON spec.lithology_ref = lithology.id
     INNER JOIN (mineralogy mineral INNER JOIN catalogue_levels mineral_level ON mineral.level_ref = mineral_level.id
                ) ON spec.mineral_ref = mineral.id
     INNER JOIN (taxonomy host_taxon INNER JOIN catalogue_levels host_taxon_level ON host_taxon.level_ref = host_taxon_level.id
                ) ON spec.host_taxon_ref = host_taxon.id
     LEFT JOIN (specimen_individuals sInd LEFT JOIN specimen_parts sPart ON sInd.id = sPart.specimen_individual_ref
               ) ON spec.id = sInd.specimen_ref
);

UPDATE darwin_flat
SET with_types = TRUE
WHERE spec_ref IN
(SELECT DISTINCT specimen_ref FROM specimen_individuals WHERE type_group <> 'specimen');

UPDATE darwin_flat
SET with_individuals = TRUE
WHERE individual_ref IS NOT NULL;

UPDATE darwin_flat
SET with_parts = TRUE
WHERE part_ref IS NOT NULL;

ALTER TABLE darwin_flat_id_seq
  OWNER TO darwin2;

ALTER TABLE darwin_flat OWNER TO darwin2;

GRANT ALL ON TABLE darwin_flat TO darwin2;
GRANT SELECT ON TABLE darwin_flat TO d2viewer;

select setval('darwin_flat_id_seq'::regclass, (select case when max(id) = 0 then 1 else max(id) end from only darwin2.darwin_flat));

commit;

\i ../createindexes_darwinflat.sql

\i ../reports/ticketing/labeling.sql
