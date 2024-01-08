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

    select id into ref_rec from gtu g where
      COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
      COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
      gtu_from_date = COALESCE(line.gtu_from_date, '01/01/0001') AND
      gtu_to_date = COALESCE(line.gtu_to_date, '31/12/2038') AND
      fullToIndex(code) = fullToIndex(line.gtu_code)

      AND id != 0 LIMIT 1;



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



COMMIT ;
