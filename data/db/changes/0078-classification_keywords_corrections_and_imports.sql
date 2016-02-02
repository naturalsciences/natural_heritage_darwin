set search_path=darwin2,public;

BEGIN;

insert into classification_keywords (referenced_relation, record_id, keyword_type, keyword)
  (
    select distinct on (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
      'taxonomy',
      taxon_ref,
      keyword_type,
      "keyword"
    from specimens inner join classification_keywords as ckmain on ckmain.referenced_relation = 'specimens'
                                                               and specimens.id = ckmain.record_id
    where not exists (
        select 1
        from classification_keywords
        where referenced_relation = 'taxonomy'
          and record_id = specimens.taxon_ref
          and keyword_type = ckmain.keyword_type
          and keyword_indexed = ckmain.keyword_indexed
    )
  );
delete from classification_keywords where referenced_relation = 'specimens';

CREATE OR REPLACE FUNCTION fct_importer_abcd(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  userid integer;
  rec_id integer;
  people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
  maintenance_line collection_maintenance;
  staging_line staging;
  id_to_delete integer ARRAY;
  id_to_keep integer ARRAY ;
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  select user_ref into userid from imports where id=req_import_ref ;
  PERFORM set_config('darwin.userid',userid::varchar, false) ;
  INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword)
    (
      SELECT DISTINCT ON (referenced_relation, taxon_ref, keyword_type, keyword_indexed)
        'taxonomy',
        taxon_ref,
        keyword_type,
        "keyword"
      FROM staging INNER JOIN classification_keywords as ckmain ON ckmain.referenced_relation = 'staging'
                                                               AND staging.id = ckmain.record_id
                   INNER JOIN imports as i ON i.id = staging.import_ref
      WHERE import_ref = req_import_ref
        AND to_import=true
        AND status = ''::hstore
        AND i.is_finished =  FALSE
        AND NOT EXISTS (
          SELECT 1
          FROM classification_keywords
          WHERE referenced_relation = 'taxonomy'
            AND record_id = staging.taxon_ref
            AND keyword_type = ckmain.keyword_type
            AND keyword_indexed = ckmain.keyword_indexed
        )
  );
  EXECUTE 'DELETE FROM classification_keywords
           WHERE referenced_relation = ''staging''
             AND record_id IN (
                                SELECT s.id
                                FROM staging s INNER JOIN imports i ON  s.import_ref = i.id
                                WHERE import_ref = $1
                                  AND to_import=true
                                  AND status = ''''::hstore
                                  AND i.is_finished =  FALSE
                             )'
  USING req_import_ref;
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
  WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
      -- @ToDo: We need to correct this to avoid reselecting from the staging table !!!
      select * into staging_line from staging where id = all_line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);

      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=all_line.id;

      rec_id := nextval('specimens_id_seq');
      IF line.spec_ref IS NULL THEN
        INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
                               acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref, type, sex, stage, state, social_status, rock_form,
                               specimen_part, complete, institution_ref, building, floor, room, row, col, shelf, container, sub_container,container_type, sub_container_type,
                               container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max, object_name)
        VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
                        line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
                                                                                                      COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
                                                                                                      COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
                                                                                                      COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
                                                                                                                                                            line.floor, line.room, line.row,  line.col, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'),
                                                                                                                                                                                                                                    COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,''),COALESCE(line.sub_container_storage,''),
                                                                                                                                                                                                                                    COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,''),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,line.part_count_min,1), line.object_name
        );
        FOR maintenance_line IN SELECT * from collection_maintenance where referenced_relation = 'staging' AND record_id=line.id
        LOOP
          SELECT people_ref into people_id FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
          UPDATE collection_maintenance set people_ref=people_id where id=maintenance_line.id ;
          DELETE FROM staging_people where referenced_relation='collection_maintenance' AND record_id=maintenance_line.id ;
        END LOOP;

        SELECT COUNT(*) INTO code_count FROM codes WHERE referenced_relation = 'staging' AND record_id = line.id AND code_category = 'main' AND code IS NOT NULL;
        IF code_count = 0 THEN
          PERFORM fct_after_save_add_code(all_line.collection_ref, rec_id);
        ELSE
          UPDATE codes SET referenced_relation = 'specimens',
            record_id = rec_id,
            code_prefix = CASE WHEN code_prefix IS NULL THEN collection.code_prefix ELSE code_prefix END,
            code_prefix_separator = CASE WHEN code_prefix_separator IS NULL THEN collection.code_prefix_separator ELSE code_prefix_separator END,
            code_suffix = CASE WHEN code_suffix IS NULL THEN collection.code_suffix ELSE code_suffix END,
            code_suffix_separator = CASE WHEN code_suffix_separator IS NULL THEN collection.code_suffix_separator ELSE code_suffix_separator END
          WHERE referenced_relation = 'staging'
                AND record_id = line.id
                AND code_category = 'main';
        END IF;

        UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
        --UPDATE collection_maintenance SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
        -- Import identifiers whitch identification have been updated to specimen
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
          SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref
          FROM staging_people s, identifications i
          WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
        DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
        -- Import collecting_methods
        INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
          SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref
          FROM staging_collecting_methods
          WHERE staging_ref = line.id;

        DELETE FROM staging_collecting_methods where staging_ref = line.id;
        UPDATE staging set spec_ref=rec_id WHERE id=all_line.id;

        FOR people_line IN SELECT * from staging_people WHERE referenced_relation = 'specimens'
        LOOP
          INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
          VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
        END LOOP;
        DELETE FROM staging_people WHERE referenced_relation = 'specimens' ;
      END IF ;
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

COMMIT ;
