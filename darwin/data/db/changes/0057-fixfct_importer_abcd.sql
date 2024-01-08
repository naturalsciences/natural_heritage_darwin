begin;
set search_path=darwin2,public;


CREATE OR REPLACE FUNCTION fct_importer_abcd(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
--   collection int;
BEGIN
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);
      PERFORM fct_imp_checker_staging_info(staging_line) ;
      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

    rec_id := nextval('specimens_id_seq');
    INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
        acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
        specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
        container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name)
    VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
      line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
      COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
      COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
      COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
      line.floor, line.room, line.row, line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
      COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,'dry'),COALESCE(line.sub_container_storage,'dry'),
      COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,'good state'),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,2), line.object_name
    );
    FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
    LOOP
      SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
      UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
      DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
    END LOOP;
    UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
    -- Import identifiers whitch identification have been updated to specimen
    INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
    SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
    DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
    -- Import collecting_methods
    INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
    SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref FROM staging_collecting_methods WHERE staging_ref = line.id;
    DELETE FROM staging_collecting_methods where staging_ref = line.id;
    UPDATE staging set spec_ref=rec_id WHERE id=all_line.id ;

    FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
    LOOP
      INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
      VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
    END LOOP;
    DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
    id_to_delete = array_append(id_to_delete,all_line.id) ;
    END;
  END LOOP;
  select fct_imp_checker_staging_relationship() into id_to_keep ;
  IF id_to_keep IS NOT NULL THEN
    DELETE from staging where (id = ANY (id_to_delete)) AND NOT (id = ANY (id_to_keep)) ;
  else
    DELETE from staging where (id = ANY (id_to_delete)) ;
  END IF ;
  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

COMMIT;
