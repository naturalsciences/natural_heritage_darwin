begin;
set search_path=darwin2,public;

CREATE OR REPLACE FUNCTION fct_imp_checker_gtu(line staging, import boolean default false)  RETURNS boolean
AS $$
DECLARE
  ref_rec integer :=0;
  tags staging_tag_groups ;
BEGIN
  IF line.gtu_ref is not null THEN
    RETURN true;
  END IF;
  IF (line.gtu_code is null OR line.gtu_code  = '') AND (line.gtu_from_date is null OR line.gtu_code  = '') AND NOT EXISTS (select 1 from staging_tag_groups g where g.staging_ref = line.id ) THEN
    RETURN true;
  END IF;
  select substr.id into ref_rec from (
    select id from gtu g where
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038') AND
      position('import/' in code) > 0 AND
      line.gtu_code IS NULL
    union
    select id from gtu g where
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038') AND
      position('import/' in code) = 0 AND
      fullToIndex(code) IN (SELECT fullToIndex(stg.gtu_code)
                            FROM staging stg
                            WHERE COALESCE(stg.gtu_latitude,0) = COALESCE(line.gtu_latitude,0) AND
                                  COALESCE(stg.gtu_longitude,0) = COALESCE(line.gtu_longitude,0) AND
                                  COALESCE(stg.gtu_from_date, '01/01/0001') = COALESCE(line.gtu_from_date, '01/01/0001') AND
                                  COALESCE(stg.gtu_to_date, '31/12/2038') = COALESCE(line.gtu_to_date, '31/12/2038') AND
                                  stg.gtu_code IS NOT NULL
                            )
      ) as substr
      WHERE substr.id != 0 LIMIT 1;

      /* fullToIndex(code) = fullToIndex(line.gtu_code) */


  IF NOT FOUND THEN
      IF import THEN
        INSERT into gtu
          (code, gtu_from_date_mask, gtu_from_date,gtu_to_date_mask, gtu_to_date, latitude, longitude, lat_long_accuracy, elevation, elevation_accuracy)
        VALUES
          (COALESCE(line.gtu_code,'import/'|| line.import_ref || '/' || line.id ), COALESCE(line.gtu_from_date_mask,0), COALESCE(line.gtu_from_date, '01/01/0001'),
          COALESCE(line.gtu_to_date_mask,0), COALESCE(line.gtu_to_date, '31/12/2038')
          , line.gtu_latitude, line.gtu_longitude, line.gtu_lat_long_accuracy, line.gtu_elevation, line.gtu_elevation_accuracy)
        RETURNING id INTO line.gtu_ref;
        ref_rec := line.gtu_ref;
        FOR tags IN SELECT * FROM staging_tag_groups WHERE staging_ref = line.id LOOP
        BEGIN
          INSERT INTO tag_groups (gtu_ref, group_name, sub_group_name, tag_value)
            Values(ref_rec,tags.group_name, tags.sub_group_name, tags.tag_value );
        --  DELETE FROM staging_tag_groups WHERE staging_ref = line.id;
          EXCEPTION WHEN unique_violation THEN
            RAISE EXCEPTION 'An error occured: %', SQLERRM;
        END ;
        END LOOP ;
        PERFORM fct_imp_checker_staging_info(line, 'gtu');
      ELSE
        RETURN TRUE;
      END IF;
  END IF;

  UPDATE staging SET status = delete(status,'gtu'), gtu_ref = ref_rec where id=line.id;

  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info(line staging) RETURNS boolean
AS $$
DECLARE
  info_line staging_info ;
  record_line RECORD ;
BEGIN

  FOR info_line IN select * from staging_info WHERE staging_ref = line.id
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




COMMIT ;

