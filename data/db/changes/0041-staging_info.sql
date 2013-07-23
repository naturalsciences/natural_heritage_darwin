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
  taxon_ref integer, -- Reference of the related specimen
  mineral_ref integer, -- Reference of related mineral
  institution_ref integer,
  institution_name text,
  source_name text,
  source_id text,
  quantity numeric(16,2),
  unit character varying DEFAULT '%'::character varying,
  unit_type character varying NOT NULL DEFAULT 'specimens'::character varying,

  CONSTRAINT pk_staging_relationship PRIMARY KEY (id),
  CONSTRAINT fk_record_id FOREIGN KEY (record_id)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT fk_specimens_relationships_mineralogy FOREIGN KEY (mineral_ref)
      REFERENCES mineralogy (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_specimens_relationships_institution FOREIGN KEY (institution_ref)
      REFERENCES people (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_specimens_relationships_taxonomy FOREIGN KEY (taxon_ref)
      REFERENCES taxonomy (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
COMMENT ON COLUMN staging_relationship.record_id IS 'id of the orignial record';
COMMENT ON COLUMN staging_relationship.referenced_relation IS 'where to find the record_id, referenced_relation is always staging but this field uis mandatory for addRelated php function';
COMMENT ON COLUMN staging_relationship.relationship_type IS 'relation type (eg. host, parent, part of)';
COMMENT ON COLUMN staging_relationship.staging_related_ref IS 'the record id associated, this record id must be found in the same import file';
COMMENT ON COLUMN specimens_relationships.taxon_ref IS 'Reference of the related specimen';
COMMENT ON COLUMN specimens_relationships.mineral_ref IS 'Reference of related mineral';
COMMENT ON COLUMN staging_relationship.institution_ref IS 'the institution id associated to this relationship';
COMMENT ON COLUMN staging_relationship.institution_name IS 'the institution name associated to this relationship, used to add to darwin institution if it dont exist';
COMMENT ON COLUMN staging_relationship.source_name IS 'External Specimen related  source DB';
COMMENT ON COLUMN staging_relationship.source_id IS 'External Specimen related id in the source';
COMMENT ON COLUMN specimens_relationships.quantity IS 'Quantity of accompanying mineral';

ALTER TABLE staging_relationship
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_relationship TO darwin2;
GRANT SELECT ON TABLE staging_relationship TO d2viewer;

create table staging_collecting_methods
  (
    id serial,
    staging_ref integer not null,
    collecting_method_ref integer not null,
    constraint pk_staging_collecting_methods primary key (id),
    constraint unq_staging_collecting_methods unique (staging_ref, collecting_method_ref),
    constraint fk_staging_collecting_methods_staging foreign key (staging_ref) references staging (id) on delete cascade,
    constraint fk_staging_collecting_methods_method foreign key (collecting_method_ref) references collecting_methods (id) on delete cascade
  );

comment on table staging_collecting_methods is 'Association of collecting methods with staging';
comment on column staging_collecting_methods.id is 'Unique identifier of an association';
comment on column staging_collecting_methods.staging_ref is 'Identifier of a specimen - comes from staging table (id field)';
comment on column staging_collecting_methods.collecting_method_ref is 'Identifier of a collecting method - comes from collecting_methods table (id field)';
ALTER TABLE staging_collecting_methods
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_collecting_methods TO darwin2;
GRANT SELECT ON TABLE staging_collecting_methods TO d2viewer;

ALTER TABLE staging DROP COLUMN part_status ;
ALTER TABLE staging DROP COLUMN parent_ref ;
ALTER TABLE staging DROP COLUMN path ;
ALTER TABLE staging DROP COLUMN level ;
ALTER TABLE staging add column mineral_classification text ;
alter table collection_maintenance alter column people_ref drop not null ;
ALTER TABLE imports add column errors_in_import text ;

DROP trigger trg_cpy_path_staging on staging ;

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




Drop function fct_importer_dna(integer);

CREATE OR REPLACE FUNCTION fct_importer_abcd(req_import_ref integer)  RETURNS boolean
AS $$
DECLARE
  rec_id integer;
--   people_id integer;
  all_line RECORD ;
  line staging;
  people_line RECORD;
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
        specimen_part, complete, institution_ref, building, floor, room, row, shelf, container, sub_container,container_type, sub_container_type,
        container_storage, sub_container_storage, surnumerary, specimen_status, specimen_count_min, specimen_count_max)
    VALUES (rec_id, COALESCE(line.category,'physical') , all_line.collection_ref, line.expedition_ref, line.gtu_ref, line.taxon_ref, line.litho_ref, line.chrono_ref,
      line.lithology_ref, line.mineral_ref, COALESCE(line.acquisition_category,''), COALESCE(line.acquisition_date_mask,0), COALESCE(line.acquisition_date,'01/01/0001'),
      COALESCE(line.station_visible,true),  line.ig_ref, COALESCE(line.individual_type,'specimen'), COALESCE(line.individual_sex,'undefined'),
      COALESCE(line.individual_stage,'undefined'), COALESCE(line.individual_state,'not applicable'),COALESCE(line.individual_social_status,'not applicable'),
      COALESCE(line.individual_rock_form,'not applicable'), COALESCE(line.part,'specimen'), COALESCE(line.complete,true), line.institution_ref, line.building,
      line.floor, line.room, line.row, line.shelf, line.container, line.sub_container,COALESCE(line.container_type,'container'), 
      COALESCE(line.sub_container_type, 'container'), COALESCE(line.container_storage,'dry'),COALESCE(line.sub_container_storage,'dry'),
      COALESCE(line.surnumerary,false), COALESCE(line.specimen_status,'good state'),COALESCE(line.part_count_min,1), COALESCE(line.part_count_max,2)
    );
    UPDATE template_table_record_ref SET referenced_relation ='specimens', record_id = rec_id where referenced_relation ='staging' and record_id = line.id;
    -- Import identifiers whitch identification have been updated to specimen
    INSERT INTO catalogue_people(id, referenced_relation, record_id, people_type, people_sub_type, order_by, people_ref)
    SELECT nextval('catalogue_people_id_seq'), s.referenced_relation, s.record_id, s.people_type, s.people_sub_type, s.order_by, s.people_ref FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ;
    DELETE FROM staging_people where id in (SELECT s.id FROM staging_people s, identifications i WHERE i.id = s.record_id AND s.referenced_relation = 'identifications' AND i.record_id = rec_id AND i.referenced_relation = 'specimens' ) ;
    -- Import collecting_methods
    INSERT INTO specimen_collecting_methods(id, specimen_ref, collecting_method_ref)
    SELECT nextval('specimen_collecting_methods_id_seq'), rec_id, collecting_method_ref FROM staging_collecting_methods WHERE staging_ref = line.id;
    DELETE FROM staging_collecting_methods where staging_ref = line.id;
    UPDATE staging set spec_ref=rec_id WHERE id=line.id ;

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


GRANT ALL ON TABLE staging TO darwin2;
GRANT SELECT ON TABLE staging TO d2viewer;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info(line staging) RETURNS boolean
AS $$
DECLARE
  info_line staging_info ;
BEGIN

  FOR info_line IN select * from staging_info WHERE staging_ref = line.id
  LOOP
    BEGIN
    CASE info_line.referenced_relation 
      WHEN 'gtu' THEN
        IF line.gtu_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='gtu', record_id=line.gtu_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'taxonomy' THEN
        IF line.taxon_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='taxonomy', record_id=line.taxon_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'expeditions' THEN
        IF line.expedition_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='expeditions', record_id=line.expedition_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'lithostratigraphy' THEN
        IF line.litho_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='lithostratigraphy', record_id=line.litho_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'lithology' THEN
        IF line.lithology_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='lithology', record_id=line.lithology_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'chronostratigraphy' THEN
        IF line.chrono_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='chronostratigraphy', record_id=line.chrono_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'mineralogy' THEN
        IF line.mineral_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='mineralogy', record_id=line.mineral_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      WHEN 'igs' THEN
        IF line.ig_ref IS NOT NULL THEN
          UPDATE template_table_record_ref set referenced_relation='igs', record_id=line.ig_ref where referenced_relation='staging_info' and record_id=info_line.id ;
        END IF;
      ELSE continue ;
      END CASE ;
      EXCEPTION WHEN unique_violation THEN
        RAISE NOTICE 'An error occured: %', SQLERRM;
      END ;
  END LOOP;
  DELETE FROM staging_info WHERE staging_ref = line.id ;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

drop function fct_imp_checker_staging_relationship()  ;
CREATE OR REPLACE FUNCTION fct_imp_checker_staging_relationship() RETURNS integer ARRAY
AS $$
DECLARE
  relation_line RECORD ;
  specimen_ref INTEGER ;
  id_array integer ARRAY ;
BEGIN

  FOR relation_line IN select sr.* from staging_relationship sr, staging s WHERE sr.record_id = s.id AND s.spec_ref IS NOT NULL
  LOOP
    IF relation_line.staging_related_ref IS NOT NULL THEN
      SELECT spec_ref INTO specimen_ref FROM staging where id=relation_line.staging_related_ref ;
      IF specimen_ref IS NULL THEN 
        id_array = array_append(id_array, s.id);
        continue ;
      ELSE
        INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, specimen_related_ref, institution_ref)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.staging_related_ref, 'parent', unit_type, specimen_ref, institution_ref
        from staging_relationship where record_id=relation_line.id AND staging_related_ref=relation_line.staging_related_ref;
      END IF;
    ELSE
    INSERT INTO specimens_relationships(id, specimen_ref, relationship_type, unit_type, institution_ref,taxon_ref, mineral_ref, source_name,
    source_id, quantity, unit)
        SELECT nextval('specimens_relationships_id_seq'), relation_line.staging_related_ref, 'parent', unit_type, specimen_ref, institution_ref,
        taxon_ref, mineral_ref, source_name, source_id, quantity, unit
        from staging_relationship where record_id=relation_line.id ;
    END IF ;
    DELETE FROM staging_relationship WHERE id = relation_line.id ;
  END LOOP;
  RETURN id_array;
END;
$$ LANGUAGE plpgsql;

COMMIT;