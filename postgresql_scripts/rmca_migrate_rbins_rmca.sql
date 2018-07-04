-- Function: public.rmca_migrate_rbins_rmca()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca()
  RETURNS integer AS
$BODY$
    Declare returned int;
    declare same_tables varchar[];
    declare i integer;
    declare size_array_same int;
    declare fields_to_copy varchar[]; 
     text_var1 text;
  text_var2 text;
  text_var3 text;

  metadata_tmp integer;
    BEGIN
    --attention, the index esof specimens in the rbins are bound to some functions
    SET search_path TO darwin2;
      returned:=1;
     -- same_tables:='{"gtu"}';
     same_tables:='{"people",
"catalogue_relationships",
"catalogue_people",
"catalogue_levels",
"possible_upper_levels",
"tag_groups",
"tags",
"properties",
"comments",
"ext_links",
"people_addresses",
"people_languages",
"people_relationships",
"people_transpo",
"identifications",
"vernacular_names",
"expeditions",
"collections_rights",
"users_tracking",
"informative_workflow",
"classification_keywords",
"chronostratigraphy",
"lithostratigraphy",
"mineralogy",
"lithology",
"igs",
"collecting_methods",
"codes",
"insurances",
"specimens_relationships",
"specimen_collecting_methods",
"specimen_collecting_tools",
"flat_dict",
"staging_info",
"staging_people",
"staging_collecting_methods",
"multimedia",
"loan_items",
"loan_rights",
"loan_status",
"loan_history",
"bibliography",
"catalogue_bibliography",
"multimedia_todelete",
"users",
"gtu",
"db_version",
"staging_catalogue",
"staging",
"collection_maintenance",
"collections",
"collecting_tools",
"imports",
"loans",
"my_saved_searches",
"my_widgets",
"people_comm",
"preferences",
"users_addresses",
"staging_tag_groups",
"tags",
"users_comm",
"users_login_infos"
}';




size_array_same:=array_length(same_tables,1);
raise notice 'begin %', timeofday()::varchar;

ALTER TABLE darwin2.taxonomy DISABLE TRIGGER ALL;
DELETE from darwin2.taxonomy;
ALTER TABLE darwin2.taxonomy ENABLE TRIGGER ALL;

ALTER TABLE darwin2.classification_synonymies DISABLE TRIGGER ALL;
DELETE from darwin2.classification_synonymies;
ALTER TABLE darwin2.classification_synonymies ENABLE TRIGGER ALL;


ALTER TABLE darwin2.taxonomy_metadata DISABLE TRIGGER ALL;
DELETE FROM darwin2.taxonomy_metadata;
ALTER TABLE darwin2.taxonomy_metadata ENABLE TRIGGER ALL;
ALTER TABLE darwin2.specimens DISABLE TRIGGER ALL;
DELETE FROM darwin2.specimens;
ALTER TABLE darwin2.specimens ENABLE TRIGGER ALL;
ALTER TABLE darwin2.storage_parts DISABLE TRIGGER ALL;
DELETE FROM darwin2.storage_parts;
ALTER TABLE darwin2.storage_parts ENABLE TRIGGER ALL;

INSERT INTO public.count_migration VALUES ('begin_import', timeofday()::varchar,null);
FOR i in 1..size_array_same LOOP
	EXECUTE 'TRUNCATE darwin2.'||same_tables[i]||' CASCADE;';
END LOOP;

FOR i in 1..size_array_same LOOP
	RAISE NOTICE '%', same_tables[i];
	SELECT array_agg(column_name::varchar order by ordinal_position) into fields_to_copy
		FROM information_schema.columns
		WHERE table_schema = 'darwin2_rbins_data'
		AND table_name   = same_tables[i]		
		;
		RAISE NOTICE '%', fields_to_copy;
		if fields_to_copy is not null then
		BEGIN

                 

		 EXECUTE 'INSERT INTO public.count_migration VALUES (''darwin2_rbins_data.'||same_tables[i]||''',''before_copy'',(SELECT COUNT (*) FROM darwin2_rbins_data.'||same_tables[i]||'));';


		 RAISE NOTICE 'ALTER TABLE darwin2.% DISABLE TRIGGER ALL;
		INSERT INTO darwin2.% (%) SELECT % FROM darwin2_rbins_data.%;
		ALTER TABLE darwin2.% ENABLE TRIGGER ALL;', same_tables[i], same_tables[i], array_to_string(fields_to_copy,','), array_to_string(fields_to_copy,','), same_tables[i], same_tables[i];

		EXECUTE 'ALTER TABLE darwin2.'||same_tables[i]||' DISABLE TRIGGER ALL;
		
		INSERT INTO darwin2.'||same_tables[i]||' ('||array_to_string(fields_to_copy,',')||') SELECT '||array_to_string(fields_to_copy,',')||' FROM darwin2_rbins_data.'||same_tables[i]||';
		ALTER TABLE darwin2.'||same_tables[i]||' ENABLE TRIGGER ALL;' ;

				EXECUTE 'INSERT INTO public.count_migration VALUES (
''darwin2.'||same_tables[i]||''' ,
''after_copy'',
(SELECT COUNT (*) FROM darwin2.'||same_tables[i]||'));';
		
		EXCEPTION WHEN OTHERS then
		 GET STACKED DIAGNOSTICS text_var1 = MESSAGE_TEXT,
                          text_var2 = PG_EXCEPTION_DETAIL,
                          text_var3 = PG_EXCEPTION_HINT;
                          RAISE NOTICE '%',text_var1;
			RAISE NOTICE 'DIFF_IN_FIELD_FOR : %',same_tables[i];
		END;
		
		end if;
		
		
END LOOP;
BEGIN

 INSERT INTO public.count_migration VALUES ('end_import', timeofday()::varchar,null);
raise notice 'end %', timeofday()::varchar;

--taxonomy_metadata
RAISE NOTICE 'taxonomy_metadata %', timeofday()::varchar;
ALTER TABLE darwin2.taxonomy_metadata DISABLE TRIGGER ALL; 
--DELETE FROM darwin2.taxonomy_metadata;
INSERT INTO darwin2.taxonomy_metadata(
            creation_date, creation_date_mask, import_ref, taxonomy_name, 
            definition, is_reference_taxonomy, source)
    VALUES (now() ,
            56, null, 'RBINS_GENERAL', 'RBINS general taxonomy in April 2018' , true, 'Legacy from Darwin (as to 2018)');
ALTER TABLE darwin2.taxonomy_metadata ENABLE TRIGGER ALL; 


--taxonomy
RAISE NOTICE 'taxonomy %', timeofday()::varchar;
SELECT max(id) INTO metadata_tmp FROM darwin2.taxonomy_metadata;
ALTER TABLE darwin2.taxonomy DISABLE TRIGGER ALL; 
--DELETE FROM darwin2.taxonomy;
INSERT INTO darwin2.taxonomy(
            name, name_indexed, level_ref, status, local_naming, color, path, 
            parent_ref, id, extinct,  
            metadata_ref, taxonomy_creation_date, import_ref)
    SELECT name, name_indexed, level_ref, status, local_naming, color, path, 
            parent_ref, id, extinct,  
            metadata_tmp, taxonomy_creation_date, import_ref FROM  darwin2_rbins_data.taxonomy;

ALTER TABLE darwin2.taxonomy ENABLE TRIGGER ALL; 

--taxonomy synonymies
RAISE NOTICE 'syonymies %', timeofday()::varchar;
ALTER TABLE darwin2.classification_synonymies DISABLE TRIGGER ALL; 
--DELETE FROM darwin2.classification_synonymies;
INSERT INTO darwin2.classification_synonymies(
             referenced_relation, record_id, id, group_id, group_name, is_basionym, 
            order_by)
    SELECT  referenced_relation, record_id, id, group_id, group_name, is_basionym, 
            order_by FROM  darwin2_rbins_data.classification_synonymies;

ALTER TABLE darwin2.classification_synonymies ENABLE TRIGGER ALL; 


--specimens

RAISE NOTICE 'specimens (!) %', timeofday()::varchar;
ALTER TABLE darwin2.specimens DISABLE TRIGGER ALL; 
--DELETE FROM darwin2.specimens;
INSERT INTO darwin2.specimens(
            id, --category, 
            collection_ref, expedition_ref, gtu_ref, taxon_ref, 
            litho_ref, chrono_ref, lithology_ref, mineral_ref, acquisition_category, 
            acquisition_date_mask, acquisition_date, station_visible, ig_ref, 
            type, type_group, type_search, sex, stage, state, social_status, 
            rock_form, 

            /*specimen_part, complete, institution_ref, building, 
            floor, room, "row", shelf, container, sub_container, container_type, 
            sub_container_type, container_storage, sub_container_storage, 
            surnumerary, specimen_status, */

            specimen_count_min, specimen_count_max, 
            --object_name, object_name_indexed, 

            spec_ident_ids, spec_coll_ids, 
            spec_don_sel_ids, collection_type, collection_code, collection_name, 
            collection_is_public, collection_parent_ref, collection_path, 
            expedition_name, expedition_name_indexed, 
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
            taxon_name, taxon_name_indexed, 
            taxon_level_ref, taxon_level_name, taxon_status, taxon_path, 
            taxon_parent_ref, taxon_extinct, litho_name, litho_name_indexed, 
            litho_level_ref, litho_level_name, litho_status, litho_local, 
            litho_color, litho_path, litho_parent_ref, chrono_name, chrono_name_indexed, 
            chrono_level_ref, chrono_level_name, chrono_status, chrono_local, 
            chrono_color, chrono_path, chrono_parent_ref, lithology_name, 
            lithology_name_indexed, lithology_level_ref, lithology_level_name, 
            lithology_status, lithology_local, lithology_color, lithology_path, 
            lithology_parent_ref, mineral_name, mineral_name_indexed, mineral_level_ref, 
            mineral_level_name, mineral_status, mineral_local, mineral_color, 
            mineral_path, mineral_parent_ref, ig_num, ig_num_indexed, ig_date_mask, 
            ig_date, col, --gtu_location, 
            specimen_creation_date, import_ref)
   

            SELECt DISTINCT

 id, --category, 
 collection_ref, expedition_ref, gtu_ref, taxon_ref, 
            litho_ref, chrono_ref, lithology_ref, mineral_ref, acquisition_category, 
            acquisition_date_mask, acquisition_date, station_visible, ig_ref, 
            type, type_group, type_search, sex, stage, state, social_status, 
            rock_form, 


            ---specimen_part, 
            --complete, 
            --institution_ref, 
            --building, 
            ---floor, 
            ---room, 
            --"row", 
            --shelf, 
            --container, 
            --sub_container, 
            ---container_type, 
            --sub_container_type, 
            --container_storage, sub_container_storage, 
            --surnumerary, s--pecimen_status, 
            specimen_count_min, specimen_count_max, 
            --object_name, object_name_indexed, 

            spec_ident_ids, spec_coll_ids, 
            spec_don_sel_ids, collection_type, collection_code, collection_name, 
            collection_is_public, collection_parent_ref, collection_path, 
            expedition_name, expedition_name_indexed, 
            gtu_code, 
            COALESCE(gtu_from_date_mask,0), 
            COALESCE(gtu_from_date,'0001-01-01'), 
            COALESCE(gtu_to_date_mask,0), 
            COALESCE(gtu_to_date,'0001-01-01'), 
            gtu_tag_values_indexed, 
            gtu_country_tag_value, 
            gtu_country_tag_indexed, 
            gtu_province_tag_value, 
            gtu_province_tag_indexed, 
            gtu_others_tag_value, 
            gtu_others_tag_indexed, 
            gtu_elevation, gtu_elevation_accuracy, taxon_name, taxon_name_indexed, 
            taxon_level_ref, taxon_level_name, taxon_status, taxon_path, 
            taxon_parent_ref, taxon_extinct, litho_name, litho_name_indexed, 
            litho_level_ref, litho_level_name, litho_status, litho_local, 
            litho_color, litho_path, litho_parent_ref, chrono_name, chrono_name_indexed, 
            chrono_level_ref, chrono_level_name, chrono_status, chrono_local, 
            chrono_color, chrono_path, chrono_parent_ref, lithology_name, 
            lithology_name_indexed, lithology_level_ref, lithology_level_name, 
            lithology_status, lithology_local, lithology_color, lithology_path, 
            lithology_parent_ref, mineral_name, mineral_name_indexed, mineral_level_ref, 
            mineral_level_name, mineral_status, mineral_local, mineral_color, 
            mineral_path, mineral_parent_ref, ig_num, ig_num_indexed, ig_date_mask, 
            ig_date, col, 
            --(SELECT gtu_location FROM darwin2.specimens s2 WHERE s2.id=specimens.id LIMIT 1), 
            specimen_creation_date, import_ref

            FROM 

            darwin2_rbins_data.specimens
            ;

            UPDATE darwin2.specimens SET gtu_location=s2.gtu_location
            FROM darwin2_rbins_data.specimens s2 WHERE specimens.id=s2.id;
ALTER TABLE darwin2.specimens ENABLE TRIGGER ALL; 

--storage parts
RAISE NOTICE 'storage_parts %', timeofday()::varchar;
ALTER TABLE darwin2.storage_parts DISABLE TRIGGER ALL; 
--DELETE FROM darwin2.storage_parts;

INSERT INTO darwin2.storage_parts(
             category, specimen_ref, specimen_part, institution_ref, building, 
            floor, room, "row", col, shelf, container, sub_container, container_type, 
            sub_container_type, container_storage, sub_container_storage, 
            surnumerary, object_name, object_name_indexed, specimen_status, 
            complete)
    SELECT
    DISTINCT    category, id, specimen_part, institution_ref, building, 
            floor, room, "row", col, shelf, container, sub_container, container_type, 
            sub_container_type, container_storage, sub_container_storage, 
            surnumerary, object_name, object_name_indexed, specimen_status, 
            complete FROM darwin2_rbins_data.specimens;

            ALTER TABLE darwin2.storage_parts ENABLE TRIGGER ALL;


            --normalize gtus 
            --RAISE NOTICE 'normalize_gtus %', timeofday()::varchar;
           --SELECT * FROM public.rmca_migrate_rbins_rmca_normalize_gtus();

   --align sequences

--RAISE NOTICE 'Sequences %', timeofday()::varchar;
--select * FROM public.rmca_migrate_rbins_rmca_align_seq();

EXCEPTION WHEN OTHERS then
		 GET STACKED DIAGNOSTICS text_var1 = MESSAGE_TEXT,
                          text_var2 = PG_EXCEPTION_DETAIL,
                          text_var3 = PG_EXCEPTION_HINT;
                          RAISE NOTICE '%',text_var1;
			RAISE NOTICE 'DIFF_IN_FIELD_FOR : %',same_tables[i];
		END;
      return returned;


      
    END;
    $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.rmca_migrate_rbins_rmca()
  OWNER TO postgres;
