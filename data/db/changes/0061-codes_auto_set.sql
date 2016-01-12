BEGIN;
set search_path=darwin2,public;

CREATE OR REPLACE FUNCTION check_auto_increment_code_in_spec() RETURNS trigger 
AS $$
DECLARE 
  col collections%ROWTYPE;
  number integer ;
BEGIN
  IF TG_OP != 'DELETE' THEN
    IF NEW.referenced_relation = 'specimens' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=NEW.record_id;
      IF FOUND THEN
        IF NEW.code_category = 'main' THEN
          IF isnumeric(NEW.code) THEN 
            number := NEW.code::integer ;
            IF number > col.code_last_value THEN
              UPDATE collections set code_last_value = number WHERE id=col.id ;
            END IF;
          ELSE
            UPDATE collections 
            SET code_last_value = (SELECT max(code_num)
                                   FROM codes inner join specimens
                                     ON codes.referenced_relation = 'specimens'
                                     AND codes.record_id = specimens.id
                                   WHERE codes.code_category = 'main'
                                     AND specimens.collection_ref = col.id
                                     AND codes.code_num IS NOT NULL
                                  )
            WHERE id = col.id
              AND EXISTS (SELECT 1
                          FROM codes inner join specimens
                            ON codes.referenced_relation = 'specimens'
                            AND codes.record_id = specimens.id
                          WHERE codes.code_category = 'main'
                            AND specimens.collection_ref = col.id
                            AND codes.code_num IS NOT NULL
                          LIMIT 1
                         );
            IF NOT FOUND THEN
              UPDATE collections
              SET code_last_value = DEFAULT
              WHERE id=col.id;
            END IF;
          END IF;
        ELSEIF TG_OP = 'UPDATE' THEN
          IF OLD.code_category = 'main' THEN
            IF isnumeric(OLD.code) THEN 
              number := OLD.code::integer ;
              IF number = col.code_last_value THEN
                UPDATE collections 
                SET code_last_value = (SELECT max(code_num)
                                       FROM codes inner join specimens
                                         ON codes.referenced_relation = 'specimens'
                                         AND codes.record_id = specimens.id
                                       WHERE codes.code_category = 'main'
                                         AND specimens.collection_ref = col.id
                                         AND codes.code_num IS NOT NULL
                                      )
                WHERE id = col.id
                  AND EXISTS (SELECT 1
                              FROM codes inner join specimens
                                ON codes.referenced_relation = 'specimens'
                                AND codes.record_id = specimens.id
                              WHERE codes.code_category = 'main'
                                AND specimens.collection_ref = col.id
                                AND codes.code_num IS NOT NULL
                              LIMIT 1
                             );
                IF NOT FOUND THEN
                  UPDATE collections
                  SET code_last_value = DEFAULT
                  WHERE id=col.id;
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
      END IF;
    END IF ;
    RETURN NEW;
  ELSE
    IF OLD.referenced_relation = 'specimens' AND OLD.code_category = 'main' THEN
      SELECT c.* INTO col FROM collections c INNER JOIN specimens s ON s.collection_ref=c.id WHERE s.id=OLD.record_id; 
      IF FOUND AND isnumeric(OLD.code) THEN 
        UPDATE collections 
        SET code_last_value = (SELECT max(code_num)
                               FROM codes INNER JOIN specimens 
                                 ON  codes.referenced_relation = 'specimens'
                                 AND codes.record_id = specimens.id 
                               WHERE codes.code_category = 'main'
                                 AND specimens.collection_ref = col.id
                                 AND codes.code_num IS NOT NULL
                              )
        WHERE id=col.id
          AND EXISTS (SELECT 1
                      FROM codes inner join specimens
                        ON codes.referenced_relation = 'specimens'
                        AND codes.record_id = specimens.id
                      WHERE codes.code_category = 'main'
                        AND specimens.collection_ref = col.id
                        AND codes.code_num IS NOT NULL
                      LIMIT 1
                     );
        IF NOT FOUND THEN
          UPDATE collections
          SET code_last_value = DEFAULT
          WHERE id=col.id;
        END IF;
      END IF;
    END IF ;
    RETURN OLD;
  END IF;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_insert_auto_code 
    ON codes;    

CREATE TRIGGER trg_insert_auto_code AFTER INSERT OR UPDATE OR DELETE ON codes FOR EACH ROW
EXECUTE PROCEDURE check_auto_increment_code_in_spec() ;

CREATE OR REPLACE FUNCTION update_collections_code_last_val() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = NEW.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = NEW.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = NEW.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val
    ON specimens;

CREATE TRIGGER trg_update_collections_code_last_val AFTER UPDATE OF collection_ref ON specimens FOR EACH ROW
EXECUTE PROCEDURE update_collections_code_last_val();


ALTER TABLE collections ADD COLUMN code_auto_increment_for_insert_only BOOLEAN NOT NULL DEFAULT TRUE;
COMMENT ON COLUMN collections.code_auto_increment_for_insert_only IS 'Flag telling if the autoincremented code insertion has to be done only after insertion of specimens or also after updates of specimens';

UPDATE collections SET code_auto_increment_for_insert_only = DEFAULT;

CREATE OR REPLACE FUNCTION fct_after_save_add_code(IN collectionId collections.id%TYPE, IN specimenId specimens.id%TYPE) RETURNS integer
AS $$
DECLARE
  col collections%ROWTYPE;
BEGIN
  SELECT c.* INTO col FROM collections c WHERE c.id = collectionId;
  IF FOUND THEN
    IF col.code_auto_increment = TRUE THEN
      INSERT INTO codes (referenced_relation, record_id, code_prefix, code_prefix_separator, code, code_suffix_separator, code_suffix)
      SELECT 'specimens', specimenId, col.code_prefix, col.code_prefix_separator, (col.code_last_value+1)::varchar, col.code_suffix_separator, col.code_suffix
      WHERE NOT EXISTS (SELECT 1 
                        FROM codes 
                        WHERE referenced_relation = 'specimens'
                          AND record_id = specimenId
                          AND code_category = 'main'
                          AND code_num IS NOT NULL
                        LIMIT 1
                       );
    END IF;
  END IF;
  RETURN 0;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION update_collections_code_last_val_after_spec_del() RETURNS trigger 
AS $$
BEGIN
  UPDATE collections 
  SET code_last_value = (SELECT max(code_num) 
                         FROM codes 
                         INNER JOIN specimens 
                           ON codes.referenced_relation = 'specimens' 
                           AND codes.record_id = specimens.id
                           AND codes.code_category = 'main'
                         WHERE specimens.collection_ref = OLD.collection_ref
                           AND specimens.id != OLD.id
                           AND codes.code_num IS NOT NULL
                        )
  WHERE id = OLD.collection_ref
    AND EXISTS (SELECT 1
                FROM codes inner join specimens
                  ON codes.referenced_relation = 'specimens'
                  AND codes.record_id = specimens.id
                WHERE codes.code_category = 'main'
                  AND specimens.collection_ref = OLD.collection_ref
                  AND specimens.id != OLD.id
                  AND codes.code_num IS NOT NULL
                LIMIT 1
               );
  IF NOT FOUND THEN
    UPDATE collections
    SET code_last_value = DEFAULT
    WHERE id = OLD.collection_ref;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_update_collections_code_last_val_after_spec_del
    ON specimens;

CREATE TRIGGER trg_update_collections_code_last_val_after_spec_del AFTER DELETE ON specimens FOR EACH ROW
EXECUTE PROCEDURE update_collections_code_last_val_after_spec_del();

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
  collection collections%ROWTYPE;
  code_count integer;
BEGIN
  SELECT * INTO collection FROM collections WHERE id = (SELECT collection_ref FROM imports WHERE id = req_import_ref AND is_finished = FALSE LIMIT 1);
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
      -- I know it's dumb but....
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

update collections co set code_last_value = (select coalesce((select max(coalesce(code_num, 0)) from codes inner join specimens on codes.referenced_relation = 'specimens' and codes.record_id = specimens.id and code_category = 'main' and specimens.collection_ref = co.id),0));

COMMIT ;
