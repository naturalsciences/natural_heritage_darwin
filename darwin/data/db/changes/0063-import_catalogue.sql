begin;
set search_path=darwin2,public;

CREATE INDEX idx_staging_gtu_code ON staging (gtu_code) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_staging_gtu_code_fullToIndex ON staging (fullToIndex(gtu_code)) WHERE gtu_code IS NOT NULL;
CREATE INDEX idx_gtu_code_search_for_import ON gtu (position('import/' in code), COALESCE(latitude,0), COALESCE(longitude,0), COALESCE(fullToIndex(code), ''));

alter table imports alter column collection_ref drop not null ;
alter table imports alter column  updated_at set default now() ;

create table staging_catalogue
       (
        id serial,
        import_ref integer not null,
        name varchar not null,
        level_ref integer,
        parent_ref integer,
        catalogue_ref integer,
        constraint pk_staging_catalogue primary key (id),
        constraint fk_stg_catalogue_level_ref foreign key (level_ref) references catalogue_levels(id),
        constraint fk_stg_catalogue_import_ref foreign key (import_ref) references imports(id) on delete cascade,
        constraint fk_stg_catalogue_parent_ref foreign key (parent_ref) references staging_catalogue(id) on delete cascade
       );

CREATE OR REPLACE function fct_update_import() RETURNS trigger AS $$
BEGIN
  if OLD.state IS DISTINCT FROM NEW.state THEN
  UPDATE imports set updated_at= now() where id=NEW.id ;
  END IF ;
  return new ;
END;
$$ LANGUAGE plpgsql ;
CREATE TRIGGER trg_update_import AFTER UPDATE ON imports FOR EACH ROW EXECUTE PROCEDURE fct_update_import();

CREATE OR REPLACE FUNCTION fct_importer_catalogue(req_import_ref integer,referenced_relation text)  RETURNS boolean
AS $$
DECLARE
  parent_id integer := null;
  catalogue_id integer := null;
  all_line staging_catalogue ;
  result_nbr integer;
  error_msg text;
BEGIN    
  FOR all_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY id 
  LOOP     
    if all_line.parent_ref IS Null THEN  -- so this is the first catalogue, we have to attach it to an existant
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed like fullToIndex( $2 ) GROUP BY id ;'
        into result_nbr,catalogue_id
        USING all_line.level_ref, all_line.name ;
      IF result_nbr IS NULL THEN
        error_msg := 'Could not import this file, ' || all_line.name ||
        ' does not exist in DaRWIN and cannot be attached, correct your file or create this ' || quote_ident(referenced_relation) ||
        ' manually' ;
        EXECUTE 'Update imports set errors_in_import = $1,
          state=''error''
          WHERE id=$2'
        USING error_msg, req_import_ref ;
        RETURN true ;
      END IF ;
    ELSE -- else the direct parent is in the file, so we take the parent catalogue_ref from there
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed like fullToIndex( $2 ) 
        AND parent_ref IS NOT DISTINCT FROM $3 GROUP BY id ;'
        into result_nbr,catalogue_id
        USING all_line.level_ref, all_line.name, parent_id ;
    END IF ;
    IF result_nbr > 1 THEN
      error_msg := 'Could not import this file, ' || all_line.name || ' exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree';
      EXECUTE 'Update imports set errors_in_import = $1,
        state=''error''
        WHERE id=$2'
      USING error_msg, req_import_ref ;
      RETURN true ;
    END IF ;
    IF result_nbr IS NULL THEN -- target not found, let's create it
      EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) VALUES(DEFAULT,$1,$2,$3) returning id;'
        into catalogue_id 
        using all_line.name,all_line.level_ref,parent_id;
    END IF; 
    -- update the staging line to put the new or existing catalogue_ref
    update staging_catalogue set catalogue_ref=catalogue_id Where id = all_line.id ;
    parent_id := catalogue_id ;
  END LOOP;  
    EXECUTE 'update imports set state=''finished'',is_finished=true where id=$1'
    USING req_import_ref ;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
  tags_tag RECORD;
  update_count integer;
  tag_groups_line RECORD;
BEGIN
  IF import THEN
    /* If gtu_ref already defined, that means that check was already 
       made for the line and there's no need to reassociate it 
    */
    IF line.gtu_ref is not null THEN
      RETURN true;
    END IF;
    /* If no code is given, not even from date and not even tags (tag_groups here), 
       that means there's not enough information to associate a gtu 
    */
    IF (line.gtu_code is null OR COALESCE(fullToIndex(line.gtu_code),'')  = '') AND (line.gtu_from_date is null) AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
      RETURN true;
    END IF;
    /* Otherwise, we should try to associate a gtu_ref */
    select substr.id into ref_rec from (
      /* This part try to select gtu id for line.gtu_code NULL or line.gtu_code = '' making the comparison on all the
         other fields ensuring uniqueness (latitude, longitude, from_date and to_date)
         The criteria position('import/' in code) > 0 filter also on the already imported gtu without code only
      */
      select id from gtu g where
        position('import/' in code) > 0 AND
        COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
        COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
        COALESCE(fullToIndex(line.gtu_code), '') = '' AND
        gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
        gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038')
      /* if we're not in the case of already imported gtu without code,
         we've got to find a gtu that correspond to the criterias of the current line
      */
      union
      select id from gtu g where
        position('import/' in code) = 0 AND
        COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
        COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
        COALESCE(fullToIndex(code),'') = COALESCE(fullToIndex(line.gtu_code),'') AND
        gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
        gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038')
        LIMIT 1
      ) as substr
      WHERE substr.id != 0 LIMIT 1;

    /* If no corresponding gtu found and we've chosen to import... insert the new gtu */
    IF NOT FOUND THEN
      INSERT into gtu
        (code, 
         gtu_from_date_mask, 
         gtu_from_date,
         gtu_to_date_mask, 
         gtu_to_date, 
         latitude, 
         longitude, 
         lat_long_accuracy, 
         elevation, 
         elevation_accuracy
        )
      VALUES
      (
        CASE COALESCE(fullToIndex(line.gtu_code),'') WHEN '' THEN 'import/'|| line.import_ref || '/' || line.id ELSE line.gtu_code END, 
        COALESCE(line.gtu_from_date_mask,0), 
        COALESCE(line.gtu_from_date, '01/01/0001'),
        COALESCE(line.gtu_to_date_mask,0), 
        COALESCE(line.gtu_to_date, '31/12/2038'),
        line.gtu_latitude, 
        line.gtu_longitude, 
        line.gtu_lat_long_accuracy, 
        line.gtu_elevation, 
        line.gtu_elevation_accuracy
      )
      RETURNING id INTO line.gtu_ref;
      /* The new id is returned in line.gtu_ref and stored in ref_rec so it can be used further on */
      ref_rec := line.gtu_ref;
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
          SELECT ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value;
          --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN OTHERS THEN
            RAISE NOTICE 'Error in fct_imp_checker_gtu (case non existing gtu): %', SQLERRM;
            -- nothing
        END ;
      END LOOP ;
    ELSE
      /* Define gtu_ref of the line object, so it can be used afterwards in the perform to bring correctly
         the additional comments and additional properties
      */
      line.gtu_ref = ref_rec;
      /* ELSE ADDED HERE TO CHECK IF THE TAGS (and the staging infos) OF THE EXISTING GTU EXISTS TOO */
      /* This case happens when a gtu that correspond to info entered in staging has been found */
      /* Browse all tags to try importing them one by one and associate them with the newly created gtu */
      FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        /* We split all the tags entered by ; as it's the case in the interface */
        FOR tags_tag IN SELECT trim(regexp_split_to_table(tags.tag_value, E';+')) as value LOOP
          BEGIN
            /* We use an upsert here.
               Ideally, we should use locking, but we consider it's isolated.
             */
            UPDATE tag_groups 
            SET tag_value = tag_value || ';' || tags.tag_value 
            WHERE gtu_ref = ref_rec
              AND group_name_indexed = fullToIndex(tags.group_name)
              AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
              AND fullToIndex(tags_tag.value) NOT IN (SELECT fullToIndex(regexp_split_to_table(tag_value, E';+')));
            GET DIAGNOSTICS update_count = ROW_COUNT;
            IF update_count = 0 THEN
              INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
              SELECT ref_rec,tags.group_name, tags.sub_group_name, tags_tag.value
              WHERE NOT EXISTS (SELECT id 
                                FROM tag_groups 
                                WHERE gtu_ref = ref_rec
                                  AND group_name_indexed = fullToIndex(tags.group_name)
                                  AND sub_group_name_indexed = fullToIndex(tags.sub_group_name)
                                LIMIT 1
                               );
            END IF;
          --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
            EXCEPTION WHEN OTHERS THEN
              RAISE NOTICE 'Error in fct_imp_checker_gtu (case from existing gtu): %', SQLERRM;
              RAISE NOTICE 'gtu_ref is %', ref_rec;
              RAISE NOTICE 'group name is %', tags.group_name;
              RAISE NOTICE 'subgroup name is %', tags.sub_group_name;
              RAISE NOTICE 'tag value is %', tags_tag.value;
              -- nothing
          END ;
        END LOOP;
      END LOOP ;
    END IF;
    /* Execute (perform = execute without any output) the update of reference_relation 
       for the current staging line and for the gtu type of relationship.
       Referenced relation currently named 'staging_info' is replaced by gtu
       and record_id currently set to line.id (staging id) is replaced by line.gtu_ref (id of the new gtu created)
    */
    PERFORM fct_imp_checker_staging_info(line, 'gtu');

    /* Associate the gtu_ref in the staging and erase in hstore status the gtu tag signaling gtu has still to be treated */
    UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  END IF;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

DROP FUNCTION IF EXISTS fct_imp_checker_staging_info(staging);

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info(line staging, st_type text) RETURNS boolean
AS $$
DECLARE
  info_line staging_info ;
  record_line RECORD ;
BEGIN

  FOR info_line IN select * from staging_info WHERE staging_ref = line.id AND referenced_relation = st_type
  LOOP
    BEGIN
    CASE info_line.referenced_relation
      WHEN 'gtu' THEN
        IF line.gtu_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            DELETE FROM comments mc
            WHERE referenced_relation = 'staging_info'
              AND record_id=info_line.id
              AND EXISTS (SELECT 1
                          FROM comments cc
                          WHERE cc.referenced_relation = 'gtu'
                            AND cc.notion_concerned = mc.notion_concerned
                            AND cc.comment = mc.comment
                          LIMIT 1
                         );
            DELETE FROM properties mp
            WHERE referenced_relation = 'staging_info'
              AND record_id=info_line.id
              AND EXISTS (SELECT 1
                          FROM properties cp
                          WHERE cp.referenced_relation = 'gtu'
                            AND cp.property_type = mp.property_type
                            AND cp.applies_to = mp.applies_to
                            AND cp.date_from_mask = mp.date_from_mask
                            AND cp.date_from = mp.date_from
                            AND cp.date_to_mask = mp.date_to_mask
                            AND cp.date_to = mp.date_to
                            AND cp.is_quantitative = mp.is_quantitative
                            AND cp.property_unit = mp.property_unit
                            AND cp.method_indexed = mp.method_indexed
                            AND cp.lower_value = mp.lower_value
                            AND cp.upper_value = mp.upper_value
                            AND cp.property_accuracy = mp.property_accuracy
                          LIMIT 1
                         );
            UPDATE template_table_record_ref set referenced_relation='gtu', record_id=line.gtu_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'taxonomy' THEN
        IF line.taxon_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='taxonomy', record_id=line.taxon_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'expeditions' THEN
        IF line.expedition_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='expeditions', record_id=line.expedition_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'lithostratigraphy' THEN
        IF line.litho_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='lithostratigraphy', record_id=line.litho_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'lithology' THEN
        IF line.lithology_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='lithology', record_id=line.lithology_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'chronostratigraphy' THEN
        IF line.chrono_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='chronostratigraphy', record_id=line.chrono_ref where id=record_line.id ;
          END LOOP ;
        END IF;
      WHEN 'mineralogy' THEN
        IF line.mineral_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='mineralogy', record_id=line.mineral_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
        END IF;
      WHEN 'igs' THEN
        IF line.ig_ref IS NOT NULL THEN
          FOR record_line IN select * from template_table_record_ref where referenced_relation='staging_info' and record_id=info_line.id
          LOOP
            UPDATE template_table_record_ref set referenced_relation='igs', record_id=line.ig_ref where referenced_relation='staging_info' and record_id=info_line.id;
          END LOOP ;
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
  FOR all_line IN SELECT * from staging s INNER JOIN imports i on  s.import_ref = i.id
      WHERE import_ref = req_import_ref AND to_import=true and status = ''::hstore AND i.is_finished =  FALSE
  LOOP
    BEGIN
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


commit;
