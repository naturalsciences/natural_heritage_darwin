begin;
set search_path=darwin2,public;
CREATE TABLE staging_info
(
  id serial NOT NULL,
  staging_ref integer NOT NULL,
  referenced_relation character varying NOT NULL,

  CONSTRAINT pk_staging_info PRIMARY KEY (id),
  CONSTRAINT fk_staging_ref FOREIGN KEY (staging_ref)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
ALTER TABLE staging_info
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_info TO darwin2;
GRANT SELECT ON TABLE staging_info TO d2viewer;

CREATE TABLE staging_relationship
(
  id serial NOT NULL,
  record_id integer NOT NULL,
  referenced_relation character varying NOT NULL,
  relationship_type character varying,
  staging_related_ref integer,
  institution_ref integer,
  institution_name text,
  source_name text,
  source_id text,

  CONSTRAINT pk_staging_relationship PRIMARY KEY (id),
  CONSTRAINT fk_record_id FOREIGN KEY (record_id)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
COMMENT ON COLUMN staging_relationship.record_id IS 'id of the orignial record';
COMMENT ON COLUMN staging_relationship.referenced_relation IS 'where to find the record_id, referenced_relation is always staging but this field uis mandatory for addRelated php function';
COMMENT ON COLUMN staging_relationship.relationship_type IS 'relation type (eg. host, parent, part of)';
COMMENT ON COLUMN staging_relationship.staging_related_ref IS 'the record id associated, this record id must be found in the same import file';
COMMENT ON COLUMN staging_relationship.institution_ref IS 'the institution id associated to this relationship';
COMMENT ON COLUMN staging_relationship.institution_name IS 'the institution name associated to this relationship, used to add to darwin institution if it dont exist';
COMMENT ON COLUMN staging_relationship.source_name IS 'External Specimen related  source DB';
COMMENT ON COLUMN staging_relationship.source_id IS 'External Specimen related id in the source';
ALTER TABLE staging_relationship
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_relationship TO darwin2;
GRANT SELECT ON TABLE staging_relationship TO d2viewer;

ALTER TABLE staging DROP COLUMN part_status ;
ALTER TABLE staging add column mineral_classification text ;
alter table collection_maintenance alter column people_ref drop not null ;
ALTER TABLE imports add column errors_in_import text ;


CREATE TRIGGER trg_clr_referenceRecord_staging_info AFTER DELETE OR UPDATE
  ON staging_info FOR EACH ROW
  EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE OR REPLACE FUNCTION fct_imp_checker_manager(line staging)  RETURNS boolean
AS $$
BEGIN
  IF line.taxon_name IS NOT NULL AND line.taxon_name is distinct from '' AND line.taxon_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'taxonomy','taxon');
  END IF;
  
  IF line.chrono_name IS NOT NULL AND line.chrono_name is not null AND line.chrono_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'chronostratigraphy','chrono');
  END IF;
  
  IF line.lithology_name IS NOT NULL AND line.lithology_name is distinct from '' AND line.lithology_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithology','lithology');
  END IF;

  IF line.mineral_name IS NOT NULL AND line.mineral_name is distinct from '' AND line.mineral_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'mineralogy','mineral');
  END IF;

  IF line.litho_name IS NOT NULL AND line.litho_name is distinct from '' AND line.litho_ref is null THEN
    PERFORM fct_imp_checker_catalogue(line,'lithostratigraphy','litho');
  END IF;



  PERFORM fct_imp_checker_igs(line);
  PERFORM fct_imp_checker_expeditions(line);
  PERFORM fct_imp_checker_gtu(line);
  PERFORM fct_imp_checker_people(line);
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_upd_people_staging_fields() RETURNS TRIGGER
AS $$
DECLARE
  import_id integer;
BEGIN
 IF get_setting('darwin.upd_people_ref') is null OR  get_setting('darwin.upd_people_ref') = '' THEN
    PERFORM set_config('darwin.upd_people_ref', 'ok', true);
    IF OLD.referenced_relation = 'staging' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp WHERE sp.id=OLD.id AND sp.record_id = s.id ;
    ELSEIF OLD.referenced_relation = 'identifications' THEN
      select s.import_ref INTO import_id FROM staging s, staging_people sp, identifications i WHERE sp.id=OLD.id 
      AND sp.record_id = i.id AND i.record_id = s.id ;
    ELSE
      select s.import_ref INTO import_id FROM staging s, staging_people sp, collection_maintenance c WHERE sp.id=OLD.id 
      AND sp.record_id = c.id AND c.record_id = s.id ;
    END IF;

    UPDATE staging_people SET people_ref = NEW.people_ref WHERE id IN (
      SELECT sp.id from staging_people sp, identifications i, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND i.record_id = s.id AND sp.referenced_relation = 'identifications' AND sp.record_id = i.id 
      UNION
      SELECT sp.id from staging_people sp, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id AND
      sp.record_id = s.id AND sp.referenced_relation = 'staging'
      UNION
      SELECT sp.id from staging_people sp, collection_maintenance c, staging s WHERE formated_name = OLD.formated_name AND s.import_ref = import_id
      AND c.record_id = s.id AND sp.referenced_relation = 'collection_maintenance' AND sp.record_id = c.id 
    ); 
    -- update status field, if all error people are corrected, statut 'people', 'operator' or 'identifiers' will be removed
    PERFORM fct_imp_checker_people(s.*) FROM staging s WHERE import_ref = import_id AND (status::hstore ? 'people' OR status::hstore ? 'identifiers'  OR status::hstore ? 'operator') ;
    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_people(line staging) RETURNS boolean
AS $$
DECLARE
  ref_record integer :=0;
  cnt integer :=-1;
  p_name text;
  merge_status integer :=1;
  ident_line RECORD;
  people_line RECORD ;
BEGIN


  --  Donators and collectors

  FOR people_line IN select * from staging_people WHERE referenced_relation ='staging' AND record_id = line.id 
  LOOP
    IF people_line.people_ref is not null THEN
      continue;
    END IF;
    SELECT fct_look_for_people(people_line.formated_name) into ref_record;
    CASE ref_record
      WHEN -1,0 THEN merge_status := -1 ;
      --WHEN 0 THEN merge_status := 0;
      ELSE
        UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
    END CASE;
  END LOOP;
  IF merge_status = 1 THEN 
    UPDATE staging SET status = delete(status,'people') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('people' => 'people')) where id= line.id;  
  END IF;
  
  -- Indentifiers
   
  merge_status := 1 ; 
  FOR ident_line in select * from identifications where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='identifications' AND record_id = ident_line.id 
    LOOP
      IF people_line.people_ref is not null THEN
        continue;
      END IF;
      SELECT fct_look_for_people(people_line.formated_name) into ref_record;
      CASE ref_record
        WHEN -1,0 THEN merge_status := -1 ;
        --WHEN 0 THEN merge_status := 0;
        ELSE
          UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
      END CASE;
    END LOOP;
  END LOOP;

  IF merge_status = 1 THEN 
    UPDATE staging SET status = delete(status,'identifiers') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('identifiers' => 'people')) where id= line.id;  
  END IF;
  
  -- Sequencers
   
  merge_status := 1 ; 
  FOR ident_line in select * from collection_maintenance where referenced_relation ='staging' AND  record_id = line.id
  LOOP
    FOR people_line IN select * from staging_people WHERE referenced_relation ='collection_maintenance' AND record_id = ident_line.id
    LOOP
      IF people_line.people_ref is not null THEN
        continue;
      END IF;
      SELECT fct_look_for_people(people_line.formated_name) into ref_record;
      CASE ref_record
        WHEN -1,0 THEN merge_status := -1 ;
        --WHEN 0 THEN merge_status := 0;
        ELSE
          UPDATE staging_people SET people_ref = ref_record WHERE id=people_line.id ;
      END CASE;
    END LOOP;
  END LOOP;

  IF merge_status = 1 THEN 
    UPDATE staging SET status = delete(status,'operator') where id=line.id;
  ELSE
    UPDATE staging SET status = (status || ('operator' => 'people')) where id= line.id;  
  END IF;

  /**********
  * Institution
  **********/
  IF line.institution_name IS NOT NULL and line.institution_name  != '' AND line.institution_ref is null THEN
    SELECT fct_look_for_institution(line.institution_name) into ref_record ;
      CASE ref_record
  WHEN -1 THEN 
    UPDATE staging SET status = (status || ('institution' => 'too_much')) where id= line.id;  
  WHEN 0 THEN
    UPDATE staging SET status = (status || ('institution' => 'not_found')) where id= line.id;  
  ELSE
    UPDATE staging SET status = delete(status,'institution'), institution_ref = ref_record where id=line.id;
      END CASE;
  END IF;

  /**********
  * Institution in staging_relationship
  **********
  FOR r_line IN select * from staging_relationship WHERE referenced_relation ='staging' AND record_id = line.id
    LOOP
      IF r_line.institution_name IS NOT NULL and r_line.institution_name  != '' AND r_line.institution_ref is null THEN
        SELECT fct_look_for_institution(r_line.institution_name) into ref_record ;
          CASE ref_record
      WHEN -1 THEN 
        UPDATE staging SET status = (status || ('institution_relationship' => 'too_much')) where id= line.id;  
      WHEN 0 THEN
        UPDATE staging SET status = (status || ('institution_relationship' => 'not_found')) where id= line.id;  
      ELSE
        UPDATE staging SET status = delete(status,'institution_relationship'), institution_ref = ref_record where id=line.id;
          END CASE;
    END IF;
*/
  RETURN true;
END;
$$ LANGUAGE plpgsql;




Drop function fct_importer_dna;

CREATE OR REPLACE FUNCTION fct_importer_abcd(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  prev_levels hstore default '';
  rec_id integer;
  people_id integer;
  line RECORD;
  s_line RECORD;
  people_line RECORD;
  staging_line staging;
  old_level int;
BEGIN
  FOR line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id 
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    /************
    *
    *  DON'T FORGET TO MAKE A CHECK !
    *
     ***/
    BEGIN
      --Import Specimen

      -- I know it's dumb but....
      select * into staging_line from staging where id = line.id;
      PERFORM fct_imp_checker_igs(staging_line, true);
      PERFORM fct_imp_checker_expeditions(staging_line, true);
      PERFORM fct_imp_checker_gtu(staging_line, true);
      --RE SELECT WITH UPDATE
      select * into line from staging s INNER JOIN imports i on  s.import_ref = i.id where s.id=line.id;

      BEGIN
  IF line.spec_ref is NULL THEN
    rec_id := nextval('specimens_id_seq');
    INSERT INTO specimens (id, category, collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref,
        host_taxon_ref, host_specimen_ref, host_relationship, acquisition_category, acquisition_date_mask, acquisition_date, station_visible, ig_ref)
    VALUES (rec_id, COALESCE(line.category,'physical') , line.collection_ref, line.expedition_ref, line.gtu_ref,
      line.taxon_ref, line.litho_ref, line.chrono_ref,
      line.lithology_ref, line.mineral_ref, line.host_taxon_ref,
      line.host_specimen_ref, line.host_relationship, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0),
      COALESCE(line.acquisition_date,'01/01/0001'), COALESCE(line.station_visible,true),  line.ig_ref
    );
    UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
    -- Import identifiers whitch identification have been updated to specimen
    INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
    SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
    DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;    
  ELSE
    rec_id = line.spec_ref;
  END IF;
  prev_levels := prev_levels || ('specimen' => rec_id::text);
      EXCEPTION WHEN unique_violation THEN
  SELECT id INTO rec_id FROM specimens WHERE
    category = COALESCE(line.category,'physical')
    AND collection_ref=line.collection_ref
    AND expedition_ref= line.expedition_ref
    AND gtu_ref= line.gtu_ref
    AND taxon_ref= line.taxon_ref
    AND litho_ref = line.litho_ref
    AND chrono_ref = line.chrono_ref
    AND lithology_ref = line.lithology_ref
    AND mineral_ref = line.mineral_ref
    AND host_taxon_ref = line.host_taxon_ref
    AND host_specimen_ref = line.host_specimen_ref
    AND host_relationship = line.host_relationship
    AND acquisition_category = COALESCE(line.acquisition_category,'')
    AND acquisition_date_mask = COALESCE(line.acquisition_date_mask,0)
    AND acquisition_date = COALESCE(line.acquisition_date,'01/01/0001')
    AND station_visible = COALESCE(line.station_visible,true)
    AND ig_ref = line.ig_ref; 
  UPDATE staging SET status=(status || ('duplicate' => rec_id::text)) , to_import=false WHERE id = (prev_levels->'specimen')::integer;
  UPDATE staging SET to_import=false where path like '/' || line.id || '/%';
  CONTINUE;
    END;
      --Import lower levels
      FOR s_line IN  SELECT * from staging s where path like '/' || line.id || '/%' ORDER BY path || s.id
      LOOP
        IF s_line.level = 'individual' THEN
          rec_id := nextval('specimen_individuals_id_seq');
          INSERT INTO specimen_individuals (id, specimen_ref, type, sex, stage, state, social_status, rock_form, specimen_individuals_count_min, specimen_individuals_count_max)
          VALUES (
            rec_id,(prev_levels->'specimen')::integer, COALESCE(s_line.individual_type,'specimen'), 
            COALESCE(s_line.individual_sex,'undefined'), COALESCE( s_line.individual_state,'not applicable'),
            COALESCE(s_line.individual_stage,'undefined'), COALESCE(s_line.individual_social_status,'not applicable'),
            COALESCE(s_line.individual_rock_form,'not applicable'),
            COALESCE(s_line.individual_count_min,'1'), COALESCE(s_line.individual_count_max,'1')
          );       
          UPDATE template_table_record_ref SET referenced_relation ='specimen_individuals' , record_id = rec_id where referenced_relation ='staging' and record_id = s_line.id;
           -- Import identifiers whitch identification have been updated to specimen
          INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
          SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimen_individuals' ;
          DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimen_individuals') ;                 
          prev_levels := (prev_levels || ('individual' => rec_id::text));

        ELSIF lower(s_line.level) in ('specimen part','tissue part','dna part') THEN /*** @TODO:CHECK THIS!!**/
          rec_id := nextval('specimen_parts_id_seq');
          IF  lower(s_line.level) = 'specimen part' THEN
            old_level := null;
          ELSIF lower(s_line.level) = 'tissue part' THEN
            old_level :=  prev_levels->'specimen part';
          ELSIF lower(s_line.level) = 'dna part' THEN
            old_level :=  prev_levels->'tissue part';
          END IF;
          ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
          INSERT INTO specimen_parts (id, parent_ref, specimen_individual_ref, specimen_part, complete, institution_ref, building, floor, room, row, shelf,
            container, sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status,
              specimen_part_count_min, specimen_part_count_max)
          VALUES (
            rec_id, old_level, (prev_levels->'individual')::integer,
            COALESCE(s_line.part,'specimen'), COALESCE(s_line.complete,true),
            s_line.institution_ref, s_line.building ,s_line.floor, s_line.room, s_line.row, s_line.shelf,
            s_line.container, s_line.sub_container,
            COALESCE(s_line.container_type,'container'),  COALESCE(s_line.sub_container_type, 'container'), 
            COALESCE(s_line.container_storage,'dry'),  COALESCE(s_line.sub_container_storage,'dry'),
            COALESCE(s_line.surnumerary,false),  COALESCE(s_line.specimen_status,'good state'), 
            COALESCE(s_line.part_count_min,1),  COALESCE(s_line.part_count_max,2)
          );
          UPDATE template_table_record_ref SET referenced_relation ='specimen_parts' , record_id = rec_id where referenced_relation ='staging' and record_id = s_line.id;

          prev_levels := (prev_levels || (s_line.level => rec_id::text));

          ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;
        END IF; 
      END LOOP;
      -- Import staging people into catalogue people
      FOR people_line IN SELECT * from staging_people WHERE referenced_relation in ('specimens','specimen_individuals','specimen_parts') 
      LOOP     
        INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
        VALUES(nextval('catalogue_people_id_seq'),people_line.referenced_relation, people_line.record_id, people_line.people_type, people_line.people_sub_type, people_line.order_by, people_line.people_ref) ;
      END LOOP;
      DELETE FROM staging_people WHERE referenced_relation in ('specimens','specimen_individuals','specimen_parts') ;

      DELETE from staging where path like '/' || line.id || '/%' OR  id = line.id;
    EXCEPTION WHEN unique_violation THEN
      RAISE info 'Error uniq_violation: %', SQLERRM;
      UPDATE staging SET status=(status || ('duplicate' => '0')) , to_import=false WHERE id = (prev_levels->'specimen')::integer;
      UPDATE staging SET to_import=false where path like '/' || line.id || '/%';

    END;
  END LOOP;

  IF EXISTS( select id FROM  staging WHERE import_ref = req_import_ref) THEN
    UPDATE imports set state = 'pending' where id = req_import_ref;
  ELSE
    UPDATE imports set state = 'finished', is_finished = true where id = req_import_ref;
  END IF;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

COMMIT;

