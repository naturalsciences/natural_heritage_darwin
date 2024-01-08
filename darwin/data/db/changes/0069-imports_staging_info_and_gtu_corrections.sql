set search_path=darwin2,public;

BEGIN;

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
         fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
         fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask) AND
         COALESCE(elevation,0) = COALESCE(line.gtu_elevation,0)
         /* if we're not in the case of already imported gtu without code,
            we've got to find a gtu that correspond to the criterias of the current line
         */
       union
       select id from gtu g where
         position('import/' in code) = 0 AND
         COALESCE(latitude,0) = COALESCE(line.gtu_latitude,0) AND
         COALESCE(longitude,0) = COALESCE(line.gtu_longitude,0) AND
         COALESCE(fullToIndex(code),'') = COALESCE(fullToIndex(line.gtu_code),'') AND
         fct_mask_date(gtu_from_date,gtu_from_date_mask) = fct_mask_date(COALESCE(line.gtu_from_date, '01/01/0001')::timestamp,line.gtu_from_date_mask) AND
         fct_mask_date(gtu_to_date,gtu_to_date_mask) = fct_mask_date(COALESCE(line.gtu_to_date, '31/12/2038')::timestamp,line.gtu_to_date_mask) AND
         COALESCE(elevation,0) = COALESCE(line.gtu_elevation,0)
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
          EXCEPTION WHEN OTHERS THEN
            RAISE NOTICE 'Error in fct_imp_checker_gtu (case non existing gtu): %', SQLERRM;
            /* Do nothing and continue */
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
            EXCEPTION WHEN OTHERS THEN
              RAISE NOTICE 'Error in fct_imp_checker_gtu (case from existing gtu): %', SQLERRM;
              RAISE NOTICE 'gtu_ref is %', ref_rec;
              RAISE NOTICE 'group name is %', tags.group_name;
              RAISE NOTICE 'subg  roup name is %', tags.sub_group_name;
              RAISE NOTICE 'tag value is %', tags_tag.value;
              /* Do nothing here */
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

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_comments (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                  targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                  new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                  new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE comments as mc
SET referenced_relation = $3, record_id = $4
WHERE mc.referenced_relation = $1
      AND record_id = $2
      AND NOT EXISTS(SELECT 1
                     FROM comments AS sc
                     WHERE sc.referenced_relation = $3
                           AND sc.record_id = $4
                           AND sc.notion_concerned = mc.notion_concerned
                           AND sc.comment_indexed = mc.comment_indexed
                     LIMIT 1
);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_properties (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE properties as mp
SET referenced_relation = $3, record_id = $4
WHERE mp.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM properties AS sp
                 WHERE sp.referenced_relation = $3
                       AND sp.record_id = $4
                       AND sp.property_type = mp.property_type
                       AND sp.applies_to = mp.applies_to
                       AND sp.date_from_mask = mp.date_from_mask
                       AND sp.date_from = mp.date_from
                       AND sp.date_to_mask = mp.date_to_mask
                       AND sp.date_to = mp.date_to
                       AND sp.is_quantitative = mp.is_quantitative
                       AND sp.property_unit = mp.property_unit
                       AND sp.method_indexed = mp.method_indexed
                       AND sp.lower_value = mp.lower_value
                       AND sp.upper_value = mp.upper_value
                       AND sp.property_accuracy = mp.property_accuracy
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_ext_links (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                   targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                   new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                   new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE ext_links as mel
SET referenced_relation = $3, record_id = $4
WHERE mel.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM ext_links AS sel
                 WHERE sel.referenced_relation = $3
                       AND sel.record_id = $4
                       AND sel.url = mel.url
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_multimedia (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE multimedia as mm
SET referenced_relation = $3, record_id = $4
WHERE mm.referenced_relation = $1
  AND record_id = $2
  AND NOT EXISTS(SELECT 1
                 FROM multimedia AS sm
                 WHERE sm.referenced_relation = $3
                       AND sm.record_id = $4
                       AND sm.mime_type = mm.mime_type
                       AND sm.search_indexed = mm.search_indexed
                );
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info_insurances (targeted_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    targeted_record_id template_table_record_ref.record_id%TYPE,
                                                                    new_referenced_relation template_table_record_ref.referenced_relation%TYPE,
                                                                    new_record_id template_table_record_ref.record_id%TYPE
) RETURNS VOID
AS $$
UPDATE insurances as mi
SET referenced_relation = $3, record_id = $4
WHERE mi.referenced_relation = $1
      AND record_id = $2
      AND NOT EXISTS(SELECT 1
                     FROM insurances AS si
                     WHERE si.referenced_relation = $3
                           AND si.record_id = $4
                           AND si.insurance_value = mi.insurance_value
                           AND si.insurance_currency = mi.insurance_currency
                           AND si.date_from_mask = mi.date_from_mask
                           AND si.date_from = mi.date_from
                           AND si.date_to_mask = mi.date_to_mask
                           AND si.date_to = mi.date_to
                           AND COALESCE(si.insurer_ref,0) = COALESCE(mi.insurer_ref,0)
);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_imp_checker_staging_info(line staging, st_type text) RETURNS boolean
AS $$
DECLARE
  info_line staging_info ;
  record_line RECORD ;

BEGIN

  FOR info_line IN select * from staging_info WHERE staging_ref = line.id AND referenced_relation = st_type
  LOOP
      CASE info_line.referenced_relation
      WHEN 'gtu' THEN
        IF line.gtu_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.gtu_ref);

        END IF;
      WHEN 'taxonomy' THEN
        IF line.taxon_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.taxon_ref);

        END IF;
      WHEN 'expeditions' THEN
        IF line.expedition_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);

          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.expedition_ref);

        END IF;
      WHEN 'lithostratigraphy' THEN
        IF line.litho_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.litho_ref);

        END IF;
      WHEN 'lithology' THEN
        IF line.lithology_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.lithology_ref);

        END IF;
      WHEN 'chronostratigraphy' THEN
        IF line.chrono_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.chrono_ref);

        END IF;
      WHEN 'mineralogy' THEN
        IF line.mineral_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);

          PERFORM fct_imp_checker_staging_info_properties('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);
          PERFORM fct_imp_checker_staging_info_multimedia('staging_info', info_line.id, info_line.referenced_relation, line.mineral_ref);

        END IF;
      WHEN 'igs' THEN
        IF line.ig_ref IS NOT NULL THEN

          PERFORM fct_imp_checker_staging_info_comments('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);
          PERFORM fct_imp_checker_staging_info_ext_links('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);

          PERFORM fct_imp_checker_staging_info_insurances('staging_info', info_line.id, info_line.referenced_relation, line.ig_ref);

        END IF;
      ELSE continue ;
      END CASE ;
  END LOOP;
  DELETE FROM staging_info WHERE staging_ref = line.id ;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

COMMIT;
