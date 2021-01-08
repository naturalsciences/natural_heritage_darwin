set search_path  to darwin2,postgres;
----FIELD TO CREATE FOR RMCA----
----DOCTRINE MUST BE ADAPTED
ALTER TABLE darwin2.specimens ADD COLUMN valid_label boolean;
ALTER TABLE darwin2.specimens ADD COLUMN label_created_on character varying;
ALTER TABLE darwin2.specimens ADD COLUMN label_created_by character varying;
ALTER TABLE darwin2.specimens ADD COLUMN gtu_iso3166 character varying;
ALTER TABLE darwin2.specimens ADD COLUMN gtu_iso3166_subdivision character varying;
---------------------------
truncate  darwin2.taxonomy cascade ;
/*
NOTICE:  truncate cascades to table "staging"
NOTICE:  truncate cascades to table "specimens"
NOTICE:  truncate cascades to table "specimens_relationships"
NOTICE:  truncate cascades to table "staging_relationship"
NOTICE:  truncate cascades to table "taxonomy_authority"
NOTICE:  truncate cascades to table "temporal_information"
NOTICE:  truncate cascades to table "loan_items"
NOTICE:  truncate cascades to table "specimen_collecting_methods"
NOTICE:  truncate cascades to table "specimen_collecting_tools"
NOTICE:  truncate cascades to table "specimens_stable_ids"
NOTICE:  truncate cascades to table "staging_collecting_methods"
NOTICE:  truncate cascades to table "staging_info"
NOTICE:  truncate cascades to table "staging_tag_groups
*/
truncate  darwin2.taxonomy_metadata cascade ;
truncate  darwin2.gtu cascade ;

/*
NOTICE:  truncate cascades to table "staging"
NOTICE:  truncate cascades to table "specimens"
NOTICE:  truncate cascades to table "specimens_relationships"
NOTICE:  truncate cascades to table "staging_relationship"
NOTICE:  truncate cascades to table "taxonomy_authority"
NOTICE:  truncate cascades to table "temporal_information"
NOTICE:  truncate cascades to table "loan_items"
NOTICE:  truncate cascades to table "specimen_collecting_methods"
NOTICE:  truncate cascades to table "specimen_collecting_tools"
NOTICE:  truncate cascades to table "specimens_stable_ids"
NOTICE:  truncate cascades to table "staging_collecting_methods"
NOTICE:  truncate cascades to table "staging_info"
NOTICE:  truncate cascades to table "staging_tag_groups"
NOTICE:  truncate cascades to table "staging"
NOTICE:  truncate cascades to table "temporal_information"
NOTICE:  truncate cascades to table "tags"
NOTICE:  truncate cascades to table "specimens"
NOTICE:  truncate cascades to table "tag_groups"
NOTICE:  truncate cascades to table "staging_gtu"
NOTICE:  truncate cascades to table "loan_items"
NOTICE:  truncate cascades to table "specimen_collecting_methods"
NOTICE:  truncate cascades to table "specimen_collecting_tools"
NOTICE:  truncate cascades to table "specimens_relationships"
NOTICE:  truncate cascades to table "specimens_stable_ids"
NOTICE:  truncate cascades to table "staging_collecting_methods"
NOTICE:  truncate cascades to table "staging_info"
NOTICE:  truncate cascades to table "staging_relationship"
NOTICE:  truncate cascades to table "staging_tag_groups"
TRUNCATE TABLE

Query returned successfully in 2 secs 266 msec.
*/

--1
--TAXONOMY
alter table darwin2.taxonomy disable trigger all;
insert into darwin2.taxonomy 
	(name, --1
	 name_indexed, --2
	 level_ref, --3
	 status, --4
	 local_naming, --5 
	 color, --6
	 path, --7
	 parent_ref, --8 
	 metadata_ref, --9
	 id, --10
	 extinct, --11 
	 taxonomy_creation_date, --12 
	 import_ref, --13
	 sensitive_info_withheld, --14
	 is_reference_taxonomy, --15
	 cites) --16
	
	select name,  --1
	name_indexed, --2
	level_ref, --3
	status, --4 
	local_naming, --5
	color, --6 
	path, --7 
	parent_ref, --8
	metadata_ref, --9 
	id, --10 
	extinct, --11
	taxonomy_creation_date, --12 
	import_ref, --13 
	sensitive_info_withheld, --14
	is_reference_taxonomy, --15 
	cites --16
	 from darwin2_wrapper_prod_rmca.taxonomy;
alter table darwin2.taxonomy enable trigger all;

--2
--TAXONOMY_METADATA
alter table darwin2.taxonomy_metadata disable trigger all;

insert into darwin2.taxonomy_metadata
	SELECT * from darwin2_wrapper_prod_rmca.taxonomy_metadata;

alter table darwin2.taxonomy_metadata enable trigger all;

--3
--SPECIMENS


--3.a specimenn without parts
alter table darwin2.specimens disable trigger all;

insert into darwin2.specimens
(
	id,
	category,
	collection_ref,
	expedition_ref,
	gtu_ref,
	taxon_ref,
	litho_ref,
	chrono_ref,
	lithology_ref,
	mineral_ref,
	acquisition_category,
	acquisition_date_mask,
	acquisition_date,
	station_visible,
	ig_ref,
	type,
	type_group,
	type_search,
	sex,
	stage,
	state,
	social_status,
	rock_form,
	specimen_part,
	complete,
	institution_ref,
	building,
	floor,
	room,
	row,
	col,
	shelf,
	container,
	sub_container,
	container_type,
	sub_container_type,
	container_storage,
	sub_container_storage,
	surnumerary,
	specimen_status,
	specimen_count_min,
	specimen_count_max,
	object_name,
	object_name_indexed,
	spec_ident_ids,
	spec_coll_ids,
	spec_don_sel_ids,
	collection_type,
	collection_code,
	collection_name,
	collection_is_public,
	collection_parent_ref,
	collection_path,
	expedition_name,
	expedition_name_indexed,
	gtu_code,
	gtu_from_date_mask,
	gtu_from_date,
	gtu_to_date_mask,
	gtu_to_date,
	gtu_tag_values_indexed,
	gtu_country_tag_value,
	gtu_country_tag_indexed,
	gtu_province_tag_value,
	gtu_province_tag_indexed,
	gtu_others_tag_value,
	gtu_others_tag_indexed,
	gtu_elevation,
	gtu_elevation_accuracy,
	gtu_location,
	taxon_name,
	taxon_name_indexed,
	taxon_level_ref,
	taxon_level_name,
	taxon_status,
	taxon_path,
	taxon_parent_ref,
	taxon_extinct,
	litho_name,
	litho_name_indexed,
	litho_level_ref,
	litho_level_name,
	litho_status,
	litho_local,
	litho_color,
	litho_path,
	litho_parent_ref,
	chrono_name,
	chrono_name_indexed,
	chrono_level_ref,
	chrono_level_name,
	chrono_status,
	chrono_local,
	chrono_color,
	chrono_path,
	chrono_parent_ref,
	lithology_name,
	lithology_name_indexed,
	lithology_level_ref,
	lithology_level_name,
	lithology_status,
	lithology_local,
	lithology_color,
	lithology_path,
	lithology_parent_ref,
	mineral_name,
	mineral_name_indexed,
	mineral_level_ref,
	mineral_level_name,
	mineral_status,
	mineral_local,
	mineral_color,
	mineral_path,
	mineral_parent_ref,
	ig_num,
	ig_num_indexed,
	ig_date_mask,
	ig_date,
	specimen_count_males_min,
	specimen_count_males_max,
	specimen_count_females_min,
	specimen_count_females_max,
	specimen_count_juveniles_min,
	specimen_count_juveniles_max,
	main_code_indexed,
	valid_label,
	label_created_on,
	label_created_by,
	specimen_creation_date,
	import_ref,
	gtu_iso3166,
	gtu_iso3166_subdivision,
	nagoya
)
	SELECT 
		a.id,
		COALESCE(b.category, a.category, 'physical'),
		collection_ref,
		expedition_ref,
		gtu_ref,
		taxon_ref,
		litho_ref,
		chrono_ref,
		lithology_ref,
		mineral_ref,
		acquisition_category,
		acquisition_date_mask,
		acquisition_date,
		station_visible,
		ig_ref,
		type,
		type_group,
		type_search,
		sex,
		stage,
		state,
		social_status,
		rock_form,
		COALESCE(b.specimen_part,'specimen'),
		COALESCE(b.complete,true),
		COALESCE(b.institution_ref, a.institution_ref),
		COALESCE(b.building, a.building),
		COALESCE(b.floor, a.floor),
		COALESCE(b.room, a.room),
		COALESCE(b.row, a.row),
		COALESCE(b.col, a.col),
		COALESCE(b.shelf, a.shelf),
		COALESCE(b.container, a.container),
		COALESCE(b.sub_container, a.sub_container),
		COALESCE(b.container_type, a.container_type, 'container'),
		COALESCE(b.sub_container_type, a.sub_container_type, 'container'),
		COALESCE(b.container_storage, a.container_storage,'dry'),
		COALESCE(b.sub_container_storage, a.sub_container_storage,'dry'),
		COALESCE(b.surnumerary, a.surnumerary,false),
		COALESCE(b.specimen_status, a.specimen_status, 'good state'),
		specimen_count_min,
		specimen_count_max,
		COALESCE(b.object_name, a.object_name),
		COALESCE(b.object_name_indexed, a.object_name_indexed,''),
		spec_ident_ids,
		spec_coll_ids,
		spec_don_sel_ids,
		collection_type,
		collection_code,
		collection_name,
		collection_is_public,
		collection_parent_ref,
		collection_path,
		expedition_name,
		expedition_name_indexed,
		gtu_code,
		gtu_from_date_mask,
		gtu_from_date,
		gtu_to_date_mask,
		gtu_to_date,
		gtu_tag_values_indexed,
		gtu_country_tag_value,
		gtu_country_tag_indexed,
		gtu_province_tag_value,
		gtu_province_tag_indexed,
		gtu_others_tag_value,
		gtu_others_tag_indexed,
		gtu_elevation,
		gtu_elevation_accuracy,
		gtu_location,
		taxon_name,
		taxon_name_indexed,
		taxon_level_ref,
		taxon_level_name,
		taxon_status,
		taxon_path,
		taxon_parent_ref,
		taxon_extinct,
		litho_name,
		litho_name_indexed,
		litho_level_ref,
		litho_level_name,
		litho_status,
		litho_local,
		litho_color,
		litho_path,
		litho_parent_ref,
		chrono_name,
		chrono_name_indexed,
		chrono_level_ref,
		chrono_level_name,
		chrono_status,
		chrono_local,
		chrono_color,
		chrono_path,
		chrono_parent_ref,
		lithology_name,
		lithology_name_indexed,
		lithology_level_ref,
		lithology_level_name,
		lithology_status,
		lithology_local,
		lithology_color,
		lithology_path,
		lithology_parent_ref,
		mineral_name,
		mineral_name_indexed,
		mineral_level_ref,
		mineral_level_name,
		mineral_status,
		mineral_local,
		mineral_color,
		mineral_path,
		mineral_parent_ref,
		ig_num,
		ig_num_indexed,
		ig_date_mask,
		ig_date,
		specimen_count_males_min,
		specimen_count_males_max,
		specimen_count_females_min,
		specimen_count_females_max,
		specimen_count_juveniles_min,
		specimen_count_juveniles_max,
		main_code_indexed,
		valid_label,
		label_created_on,
		label_created_by,
		specimen_creation_date,
		import_ref,
		gtu_iso3166,
		gtu_iso3166_subdivision,
		nagoya

	from darwin2_wrapper_prod_rmca.specimens a
	LEFT OUTER JOIN darwin2_wrapper_prod_rmca.storage_parts b
	ON
	a.id=
	b.specimen_ref WHERE b.id IS NULL

;

alter table darwin2.specimens enable trigger all;

--3.b specimens with only one part
alter table darwin2.specimens disable trigger all;
WITH UNIQUE_TEST AS
(
select count(id) as cpt_id, specimen_ref 
from darwin2_wrapper_prod_rmca.storage_parts
group by specimen_ref having count(id)=1)
insert into darwin2.specimens
(
	id,
	category,
	collection_ref,
	expedition_ref,
	gtu_ref,
	taxon_ref,
	litho_ref,
	chrono_ref,
	lithology_ref,
	mineral_ref,
	acquisition_category,
	acquisition_date_mask,
	acquisition_date,
	station_visible,
	ig_ref,
	type,
	type_group,
	type_search,
	sex,
	stage,
	state,
	social_status,
	rock_form,
	specimen_part,
	complete,
	institution_ref,
	building,
	floor,
	room,
	row,
	col,
	shelf,
	container,
	sub_container,
	container_type,
	sub_container_type,
	container_storage,
	sub_container_storage,
	surnumerary,
	specimen_status,
	specimen_count_min,
	specimen_count_max,
	object_name,
	object_name_indexed,
	spec_ident_ids,
	spec_coll_ids,
	spec_don_sel_ids,
	collection_type,
	collection_code,
	collection_name,
	collection_is_public,
	collection_parent_ref,
	collection_path,
	expedition_name,
	expedition_name_indexed,
	gtu_code,
	gtu_from_date_mask,
	gtu_from_date,
	gtu_to_date_mask,
	gtu_to_date,
	gtu_tag_values_indexed,
	gtu_country_tag_value,
	gtu_country_tag_indexed,
	gtu_province_tag_value,
	gtu_province_tag_indexed,
	gtu_others_tag_value,
	gtu_others_tag_indexed,
	gtu_elevation,
	gtu_elevation_accuracy,
	gtu_location,
	taxon_name,
	taxon_name_indexed,
	taxon_level_ref,
	taxon_level_name,
	taxon_status,
	taxon_path,
	taxon_parent_ref,
	taxon_extinct,
	litho_name,
	litho_name_indexed,
	litho_level_ref,
	litho_level_name,
	litho_status,
	litho_local,
	litho_color,
	litho_path,
	litho_parent_ref,
	chrono_name,
	chrono_name_indexed,
	chrono_level_ref,
	chrono_level_name,
	chrono_status,
	chrono_local,
	chrono_color,
	chrono_path,
	chrono_parent_ref,
	lithology_name,
	lithology_name_indexed,
	lithology_level_ref,
	lithology_level_name,
	lithology_status,
	lithology_local,
	lithology_color,
	lithology_path,
	lithology_parent_ref,
	mineral_name,
	mineral_name_indexed,
	mineral_level_ref,
	mineral_level_name,
	mineral_status,
	mineral_local,
	mineral_color,
	mineral_path,
	mineral_parent_ref,
	ig_num,
	ig_num_indexed,
	ig_date_mask,
	ig_date,
	specimen_count_males_min,
	specimen_count_males_max,
	specimen_count_females_min,
	specimen_count_females_max,
	specimen_count_juveniles_min,
	specimen_count_juveniles_max,
	main_code_indexed,
	valid_label,
	label_created_on,
	label_created_by,
	specimen_creation_date,
	import_ref,
	gtu_iso3166,
	gtu_iso3166_subdivision,
	nagoya
)
	SELECT 
		a.id,
		COALESCE(b.category, a.category, 'physical'),
		collection_ref,
		expedition_ref,
		gtu_ref,
		taxon_ref,
		litho_ref,
		chrono_ref,
		lithology_ref,
		mineral_ref,
		acquisition_category,
		acquisition_date_mask,
		acquisition_date,
		station_visible,
		ig_ref,
		type,
		type_group,
		type_search,
		sex,
		stage,
		state,
		social_status,
		rock_form,
		COALESCE(b.specimen_part,'specimen'),
		COALESCE(b.complete,true),
		COALESCE(b.institution_ref, a.institution_ref),
		COALESCE(b.building, a.building),
		COALESCE(b.floor, a.floor),
		COALESCE(b.room, a.room),
		COALESCE(b.row, a.row),
		COALESCE(b.col, a.col),
		COALESCE(b.shelf, a.shelf),
		COALESCE(b.container, a.container),
		COALESCE(b.sub_container, a.sub_container),
		COALESCE(b.container_type, a.container_type, 'container'),
		COALESCE(b.sub_container_type, a.sub_container_type, 'container'),
		COALESCE(b.container_storage, a.container_storage,'dry'),
		COALESCE(b.sub_container_storage, a.sub_container_storage,'dry'),
		COALESCE(b.surnumerary, a.surnumerary,false),
		COALESCE(b.specimen_status, a.specimen_status, 'good state'),
		specimen_count_min,
		specimen_count_max,
		COALESCE(b.object_name, a.object_name),
		COALESCE(b.object_name_indexed, a.object_name_indexed,''),
		spec_ident_ids,
		spec_coll_ids,
		spec_don_sel_ids,
		collection_type,
		collection_code,
		collection_name,
		collection_is_public,
		collection_parent_ref,
		collection_path,
		expedition_name,
		expedition_name_indexed,
		gtu_code,
		gtu_from_date_mask,
		gtu_from_date,
		gtu_to_date_mask,
		gtu_to_date,
		gtu_tag_values_indexed,
		gtu_country_tag_value,
		gtu_country_tag_indexed,
		gtu_province_tag_value,
		gtu_province_tag_indexed,
		gtu_others_tag_value,
		gtu_others_tag_indexed,
		gtu_elevation,
		gtu_elevation_accuracy,
		gtu_location,
		taxon_name,
		taxon_name_indexed,
		taxon_level_ref,
		taxon_level_name,
		taxon_status,
		taxon_path,
		taxon_parent_ref,
		taxon_extinct,
		litho_name,
		litho_name_indexed,
		litho_level_ref,
		litho_level_name,
		litho_status,
		litho_local,
		litho_color,
		litho_path,
		litho_parent_ref,
		chrono_name,
		chrono_name_indexed,
		chrono_level_ref,
		chrono_level_name,
		chrono_status,
		chrono_local,
		chrono_color,
		chrono_path,
		chrono_parent_ref,
		lithology_name,
		lithology_name_indexed,
		lithology_level_ref,
		lithology_level_name,
		lithology_status,
		lithology_local,
		lithology_color,
		lithology_path,
		lithology_parent_ref,
		mineral_name,
		mineral_name_indexed,
		mineral_level_ref,
		mineral_level_name,
		mineral_status,
		mineral_local,
		mineral_color,
		mineral_path,
		mineral_parent_ref,
		ig_num,
		ig_num_indexed,
		ig_date_mask,
		ig_date,
		specimen_count_males_min,
		specimen_count_males_max,
		specimen_count_females_min,
		specimen_count_females_max,
		specimen_count_juveniles_min,
		specimen_count_juveniles_max,
		main_code_indexed,
		valid_label,
		label_created_on,
		label_created_by,
		specimen_creation_date,
		import_ref,
		gtu_iso3166,
		gtu_iso3166_subdivision,
		nagoya

	from darwin2_wrapper_prod_rmca.specimens a
	LEFT OUTER JOIN darwin2_wrapper_prod_rmca.storage_parts b
	ON
	a.id=
	b.specimen_ref 
	INNER JOIN 
	UNIQUE_TEST ON
	a.id=UNIQUE_TEST.specimen_ref;
alter table darwin2.specimens enable trigger all;

--"3.c SPECIMEN WITH SEVERAL PARTS

--4 adapt flat dict
alter table darwin2.flat_dict disable trigger all;
delete from darwin2.flat_dict;
insert into darwin2.flat_dict SELECT * FROM darwin2_wrapper_prod_rmca.flat_dict ;
with spec_rel as (select coalesce(dict_value,'')||
				  coalesce(dict_field,'')||
				  coalesce(dict_depend,'') as ctrl
				  from darwin2.flat_dict 
				  where  referenced_relation='specimens' ) 
UPDATE  darwin2.flat_dict SET referenced_relation='specimens' 
FROM spec_rel
WHERE 
coalesce(dict_value,'')||
				  coalesce(dict_field,'')||
				  coalesce(dict_depend,'') not in (select ctrl FROM spec_rel)
and
darwin2.flat_dict.referenced_relation='storage_parts' 
;
alter table darwin2.flat_dict enable trigger all;

insert into darwin2.flat_dict
(id, referenced_relation, dict_field, dict_value)
VALUES((select max(id)+1 FROM darwin2.flat_dict ),'specimens_relationships','relationship_type', 'part_of_specimen' );

--5 copy specimen relationships

alter table darwin2.specimens_relationships disable trigger all;
insert into darwin2.specimens_relationships
	SELECT * FROM darwin2_wrapper_prod_rmca.specimens_relationships 
	where  specimen_Ref is not null;
alter table darwin2.specimens_relationships enable trigger all;

--ISSUE with about 30 missing specimens, to be further checks

--3.C.1
--COPY FIRST PIECE of cluster

alter table darwin2.specimens disable trigger all;
WITH SEVERAL_TEST AS
(
select count(id) as cpt_id, specimen_ref , 
	array_agg(id order by id) as array_ids
from darwin2_wrapper_prod_rmca.storage_parts
group by specimen_ref having count(id)>1)


insert into darwin2.specimens
(
	id,
	category,
	collection_ref,
	expedition_ref,
	gtu_ref,
	taxon_ref,
	litho_ref,
	chrono_ref,
	lithology_ref,
	mineral_ref,
	acquisition_category,
	acquisition_date_mask,
	acquisition_date,
	station_visible,
	ig_ref,
	type,
	type_group,
	type_search,
	sex,
	stage,
	state,
	social_status,
	rock_form,
	specimen_part,
	complete,
	institution_ref,
	building,
	floor,
	room,
	row,
	col,
	shelf,
	container,
	sub_container,
	container_type,
	sub_container_type,
	container_storage,
	sub_container_storage,
	surnumerary,
	specimen_status,
	specimen_count_min,
	specimen_count_max,
	object_name,
	object_name_indexed,
	spec_ident_ids,
	spec_coll_ids,
	spec_don_sel_ids,
	collection_type,
	collection_code,
	collection_name,
	collection_is_public,
	collection_parent_ref,
	collection_path,
	expedition_name,
	expedition_name_indexed,
	gtu_code,
	gtu_from_date_mask,
	gtu_from_date,
	gtu_to_date_mask,
	gtu_to_date,
	gtu_tag_values_indexed,
	gtu_country_tag_value,
	gtu_country_tag_indexed,
	gtu_province_tag_value,
	gtu_province_tag_indexed,
	gtu_others_tag_value,
	gtu_others_tag_indexed,
	gtu_elevation,
	gtu_elevation_accuracy,
	gtu_location,
	taxon_name,
	taxon_name_indexed,
	taxon_level_ref,
	taxon_level_name,
	taxon_status,
	taxon_path,
	taxon_parent_ref,
	taxon_extinct,
	litho_name,
	litho_name_indexed,
	litho_level_ref,
	litho_level_name,
	litho_status,
	litho_local,
	litho_color,
	litho_path,
	litho_parent_ref,
	chrono_name,
	chrono_name_indexed,
	chrono_level_ref,
	chrono_level_name,
	chrono_status,
	chrono_local,
	chrono_color,
	chrono_path,
	chrono_parent_ref,
	lithology_name,
	lithology_name_indexed,
	lithology_level_ref,
	lithology_level_name,
	lithology_status,
	lithology_local,
	lithology_color,
	lithology_path,
	lithology_parent_ref,
	mineral_name,
	mineral_name_indexed,
	mineral_level_ref,
	mineral_level_name,
	mineral_status,
	mineral_local,
	mineral_color,
	mineral_path,
	mineral_parent_ref,
	ig_num,
	ig_num_indexed,
	ig_date_mask,
	ig_date,
	specimen_count_males_min,
	specimen_count_males_max,
	specimen_count_females_min,
	specimen_count_females_max,
	specimen_count_juveniles_min,
	specimen_count_juveniles_max,
	main_code_indexed,
	valid_label,
	label_created_on,
	label_created_by,
	specimen_creation_date,
	import_ref,
	gtu_iso3166,
	gtu_iso3166_subdivision,
	nagoya
)
	SELECT 
		a.id,
		COALESCE(b.category, a.category, 'physical'),
		collection_ref,
		expedition_ref,
		gtu_ref,
		taxon_ref,
		litho_ref,
		chrono_ref,
		lithology_ref,
		mineral_ref,
		acquisition_category,
		acquisition_date_mask,
		acquisition_date,
		station_visible,
		ig_ref,
		type,
		type_group,
		type_search,
		sex,
		stage,
		state,
		social_status,
		rock_form,
		COALESCE(b.specimen_part,'specimen'),
		COALESCE(b.complete,true),
		COALESCE(b.institution_ref, a.institution_ref),
		COALESCE(b.building, a.building),
		COALESCE(b.floor, a.floor),
		COALESCE(b.room, a.room),
		COALESCE(b.row, a.row),
		COALESCE(b.col, a.col),
		COALESCE(b.shelf, a.shelf),
		COALESCE(b.container, a.container),
		COALESCE(b.sub_container, a.sub_container),
		COALESCE(b.container_type, a.container_type, 'container'),
		COALESCE(b.sub_container_type, a.sub_container_type, 'container'),
		COALESCE(b.container_storage, a.container_storage,'dry'),
		COALESCE(b.sub_container_storage, a.sub_container_storage,'dry'),
		COALESCE(b.surnumerary, a.surnumerary,false),
		COALESCE(b.specimen_status, a.specimen_status, 'good state'),
		specimen_count_min,
		specimen_count_max,
		COALESCE(b.object_name, a.object_name),
		COALESCE(b.object_name_indexed, a.object_name_indexed,''),
		spec_ident_ids,
		spec_coll_ids,
		spec_don_sel_ids,
		collection_type,
		collection_code,
		collection_name,
		collection_is_public,
		collection_parent_ref,
		collection_path,
		expedition_name,
		expedition_name_indexed,
		gtu_code,
		gtu_from_date_mask,
		gtu_from_date,
		gtu_to_date_mask,
		gtu_to_date,
		gtu_tag_values_indexed,
		gtu_country_tag_value,
		gtu_country_tag_indexed,
		gtu_province_tag_value,
		gtu_province_tag_indexed,
		gtu_others_tag_value,
		gtu_others_tag_indexed,
		gtu_elevation,
		gtu_elevation_accuracy,
		gtu_location,
		taxon_name,
		taxon_name_indexed,
		taxon_level_ref,
		taxon_level_name,
		taxon_status,
		taxon_path,
		taxon_parent_ref,
		taxon_extinct,
		litho_name,
		litho_name_indexed,
		litho_level_ref,
		litho_level_name,
		litho_status,
		litho_local,
		litho_color,
		litho_path,
		litho_parent_ref,
		chrono_name,
		chrono_name_indexed,
		chrono_level_ref,
		chrono_level_name,
		chrono_status,
		chrono_local,
		chrono_color,
		chrono_path,
		chrono_parent_ref,
		lithology_name,
		lithology_name_indexed,
		lithology_level_ref,
		lithology_level_name,
		lithology_status,
		lithology_local,
		lithology_color,
		lithology_path,
		lithology_parent_ref,
		mineral_name,
		mineral_name_indexed,
		mineral_level_ref,
		mineral_level_name,
		mineral_status,
		mineral_local,
		mineral_color,
		mineral_path,
		mineral_parent_ref,
		ig_num,
		ig_num_indexed,
		ig_date_mask,
		ig_date,
		specimen_count_males_min,
		specimen_count_males_max,
		specimen_count_females_min,
		specimen_count_females_max,
		specimen_count_juveniles_min,
		specimen_count_juveniles_max,
		main_code_indexed,
		valid_label,
		label_created_on,
		label_created_by,
		specimen_creation_date,
		import_ref,
		gtu_iso3166,
		gtu_iso3166_subdivision,
		nagoya

	from darwin2_wrapper_prod_rmca.specimens a
	INNER JOIN 
	SEVERAL_TEST ON
	a.id=SEVERAL_TEST.specimen_ref
	LEFT OUTER JOIN darwin2_wrapper_prod_rmca.storage_parts b
	ON
	SEVERAL_TEST.array_ids[1]=b.id
	
	;
alter table darwin2.specimens enable trigger all;

--3.C.2 copy suite of cluster

alter table darwin2.specimens disable trigger all;
WITH SEVERAL_TEST AS
(
select count(id) as cpt_id, specimen_ref , 
	array_agg(id order by id) as array_ids
from darwin2_wrapper_prod_rmca.storage_parts
group by specimen_ref having count(id)>1)
,
B_ARRAY AS (
select *,array_ids[2:] as arr_copy, unnest(array_ids[2:]) as id_link
	from SEVERAL_TEST
	),
C_ARRAY AS
(SELECT darwin2_wrapper_prod_rmca.storage_parts.* , rank() over(order by id_link)+ 
 (SELECT MAX(id)+1 FROM darwin2.specimens) as new_specimen_id FROM B_ARRAY
INNER JOIN  darwin2_wrapper_prod_rmca.storage_parts
 ON id_link=darwin2_wrapper_prod_rmca.storage_parts.id
)
select * INTO darwin2.C_ARRAY_CPY from C_ARRAY ;
insert into darwin2.specimens
(
	id,
	category,
	collection_ref,
	expedition_ref,
	gtu_ref,
	taxon_ref,
	litho_ref,
	chrono_ref,
	lithology_ref,
	mineral_ref,
	acquisition_category,
	acquisition_date_mask,
	acquisition_date,
	station_visible,
	ig_ref,
	type,
	type_group,
	type_search,
	sex,
	stage,
	state,
	social_status,
	rock_form,
	specimen_part,
	complete,
	institution_ref,
	building,
	floor,
	room,
	row,
	col,
	shelf,
	container,
	sub_container,
	container_type,
	sub_container_type,
	container_storage,
	sub_container_storage,
	surnumerary,
	specimen_status,
	specimen_count_min,
	specimen_count_max,
	object_name,
	object_name_indexed,
	spec_ident_ids,
	spec_coll_ids,
	spec_don_sel_ids,
	collection_type,
	collection_code,
	collection_name,
	collection_is_public,
	collection_parent_ref,
	collection_path,
	expedition_name,
	expedition_name_indexed,
	gtu_code,
	gtu_from_date_mask,
	gtu_from_date,
	gtu_to_date_mask,
	gtu_to_date,
	gtu_tag_values_indexed,
	gtu_country_tag_value,
	gtu_country_tag_indexed,
	gtu_province_tag_value,
	gtu_province_tag_indexed,
	gtu_others_tag_value,
	gtu_others_tag_indexed,
	gtu_elevation,
	gtu_elevation_accuracy,
	gtu_location,
	taxon_name,
	taxon_name_indexed,
	taxon_level_ref,
	taxon_level_name,
	taxon_status,
	taxon_path,
	taxon_parent_ref,
	taxon_extinct,
	litho_name,
	litho_name_indexed,
	litho_level_ref,
	litho_level_name,
	litho_status,
	litho_local,
	litho_color,
	litho_path,
	litho_parent_ref,
	chrono_name,
	chrono_name_indexed,
	chrono_level_ref,
	chrono_level_name,
	chrono_status,
	chrono_local,
	chrono_color,
	chrono_path,
	chrono_parent_ref,
	lithology_name,
	lithology_name_indexed,
	lithology_level_ref,
	lithology_level_name,
	lithology_status,
	lithology_local,
	lithology_color,
	lithology_path,
	lithology_parent_ref,
	mineral_name,
	mineral_name_indexed,
	mineral_level_ref,
	mineral_level_name,
	mineral_status,
	mineral_local,
	mineral_color,
	mineral_path,
	mineral_parent_ref,
	ig_num,
	ig_num_indexed,
	ig_date_mask,
	ig_date,
	specimen_count_males_min,
	specimen_count_males_max,
	specimen_count_females_min,
	specimen_count_females_max,
	specimen_count_juveniles_min,
	specimen_count_juveniles_max,
	main_code_indexed,
	valid_label,
	label_created_on,
	label_created_by,
	specimen_creation_date,
	import_ref,
	gtu_iso3166,
	gtu_iso3166_subdivision,
	nagoya
)
	SELECT 
		new_specimen_id,
		COALESCE(b.category, a.category, 'physical'),
		collection_ref,
		expedition_ref,
		gtu_ref,
		taxon_ref,
		litho_ref,
		chrono_ref,
		lithology_ref,
		mineral_ref,
		acquisition_category,
		acquisition_date_mask,
		acquisition_date,
		station_visible,
		ig_ref,
		type,
		type_group,
		type_search,
		sex,
		stage,
		state,
		social_status,
		rock_form,
		COALESCE(b.specimen_part,'specimen'),
		COALESCE(b.complete,true),
		COALESCE(b.institution_ref, a.institution_ref),
		COALESCE(b.building, a.building),
		COALESCE(b.floor, a.floor),
		COALESCE(b.room, a.room),
		COALESCE(b.row, a.row),
		COALESCE(b.col, a.col),
		COALESCE(b.shelf, a.shelf),
		COALESCE(b.container, a.container),
		COALESCE(b.sub_container, a.sub_container),
		COALESCE(b.container_type, a.container_type, 'container'),
		COALESCE(b.sub_container_type, a.sub_container_type, 'container'),
		COALESCE(b.container_storage, a.container_storage,'dry'),
		COALESCE(b.sub_container_storage, a.sub_container_storage,'dry'),
		COALESCE(b.surnumerary, a.surnumerary,false),
		COALESCE(b.specimen_status, a.specimen_status, 'good state'),
		specimen_count_min,
		specimen_count_max,
		COALESCE(b.object_name, a.object_name),
		COALESCE(b.object_name_indexed, a.object_name_indexed,''),
		spec_ident_ids,
		spec_coll_ids,
		spec_don_sel_ids,
		collection_type,
		collection_code,
		collection_name,
		collection_is_public,
		collection_parent_ref,
		collection_path,
		expedition_name,
		expedition_name_indexed,
		gtu_code,
		gtu_from_date_mask,
		gtu_from_date,
		gtu_to_date_mask,
		gtu_to_date,
		gtu_tag_values_indexed,
		gtu_country_tag_value,
		gtu_country_tag_indexed,
		gtu_province_tag_value,
		gtu_province_tag_indexed,
		gtu_others_tag_value,
		gtu_others_tag_indexed,
		gtu_elevation,
		gtu_elevation_accuracy,
		gtu_location,
		taxon_name,
		taxon_name_indexed,
		taxon_level_ref,
		taxon_level_name,
		taxon_status,
		taxon_path,
		taxon_parent_ref,
		taxon_extinct,
		litho_name,
		litho_name_indexed,
		litho_level_ref,
		litho_level_name,
		litho_status,
		litho_local,
		litho_color,
		litho_path,
		litho_parent_ref,
		chrono_name,
		chrono_name_indexed,
		chrono_level_ref,
		chrono_level_name,
		chrono_status,
		chrono_local,
		chrono_color,
		chrono_path,
		chrono_parent_ref,
		lithology_name,
		lithology_name_indexed,
		lithology_level_ref,
		lithology_level_name,
		lithology_status,
		lithology_local,
		lithology_color,
		lithology_path,
		lithology_parent_ref,
		mineral_name,
		mineral_name_indexed,
		mineral_level_ref,
		mineral_level_name,
		mineral_status,
		mineral_local,
		mineral_color,
		mineral_path,
		mineral_parent_ref,
		ig_num,
		ig_num_indexed,
		ig_date_mask,
		ig_date,
		specimen_count_males_min,
		specimen_count_males_max,
		specimen_count_females_min,
		specimen_count_females_max,
		specimen_count_juveniles_min,
		specimen_count_juveniles_max,
		main_code_indexed,
		valid_label,
		label_created_on,
		label_created_by,
		specimen_creation_date,
		import_ref,
		gtu_iso3166,
		gtu_iso3166_subdivision,
		nagoya

	from darwin2_wrapper_prod_rmca.specimens a
	INNER JOIN 
	C_ARRAY_CPY b ON
	a.id=b.specimen_ref
	
	
	;


alter table darwin2.specimens enable trigger all;

----FINALLY CREATE RELATION BETWEEN PARTS
SELECT rmca_migrate_rbin_rmca_align_seq();
WITH array_cluster as
(
SELECT 
unnest(array_agg(new_specimen_id) 
over (partition by specimen_ref)||specimen_Ref::bigint) as spec,
array_agg(new_specimen_id) 
over (partition by specimen_ref)||specimen_Ref::bigint as related
, specimen_ref FROM darwin2.C_ARRAY_CPY
)
,
array_cluster_2
AS
(
SELECt *, array_remove(related, spec) others from array_cluster)
,
array_cluster_3
as
(SELECT spec, unnest(others) as parts from array_cluster_2)
INSERT INTO specimens_relationships 
( specimen_ref, relationship_type, unit_type, specimen_related_ref, unit)
select spec, 'part_of_specimen' , 'specimens',
parts, '%'
from array_cluster_3
