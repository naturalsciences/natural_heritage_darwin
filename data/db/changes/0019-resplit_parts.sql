
begin;

ALTER TABLE specimen_parts DISABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

CREATE TEMPORARY TABLE partsSplitFromAndTo ("start" boolean not null default true, "id" integer not null);

CREATE OR REPLACE FUNCTION convert_to_real(v_input varchar) RETURNS REAL IMMUTABLE
AS $$
DECLARE v_int_value REAL DEFAULT 0;
BEGIN
    BEGIN
        v_int_value := v_input::REAL;
    EXCEPTION WHEN OTHERS THEN
/*        RAISE NOTICE 'Invalid integer value: "%".  Returning NULL.', v_input;*/
        RETURN 0;
    END;
RETURN v_int_value;
END;
$$ LANGUAGE plpgsql;

DROP TYPE IF EXISTS recPartsDetail CASCADE;
CREATE TYPE recPartsDetail AS (
                                old_spec_id integer,
                                specimen_individual_ref integer,  
                                parts_id bigint, 
                                main_code varchar, 
                                rbins_code varchar, 
                                batch_main_code varchar, 
                                inventory_code varchar,
                                old_main_code varchar,
                                specimen_part varchar, 
                                building varchar, 
                                coalesced_building varchar, 
                                "floor" varchar, 
                                coalesced_floor varchar, 
                                room varchar, 
                                coalesced_room varchar,
                                "row" varchar, 
                                coalesced_row varchar,  
                                shelf varchar, 
                                coalesced_shelf varchar, 
                                container varchar, 
                                coalesced_container varchar, 
                                sub_container varchar, 
                                coalesced_sub_container varchar, 
                                container_and_sub_container_type varchar, 
                                container_type varchar, 
                                sub_container_type varchar,
                                container_and_sub_container_storage varchar, 
                                container_storage varchar, 
                                sub_container_storage varchar,
                                specimen_status varchar, 
                                part_count_min bigint,
                                specimen_part_count_min integer,
                                specimen_part_count_max integer,
                                complete boolean,
                                freshness_level varchar,
                                old_length_min varchar,
                                old_length_min_unified varchar,
                                old_length_max varchar,
                                old_length_max_unified varchar,
                                old_length_unit varchar,
                                old_height_min varchar,
                                old_height_min_unified varchar,
                                old_height_max varchar,
                                old_height_max_unified varchar,
                                old_height_unit varchar,
                                old_depth_min varchar,
                                old_depth_min_unified varchar,
                                old_depth_max varchar,
                                old_depth_max_unified varchar,
                                old_depth_unit varchar,
                                old_weight_min varchar,
                                old_weight_min_unified varchar,
                                old_weight_max varchar,
                                old_weight_max_unified varchar,
                                old_weight_unit varchar,
                                old_insurance_value numeric,
                                old_insurance_year smallint,
                                maintenance_people_ref integer,
                                maintenance_category varchar,
                                maintenance_action_observation varchar,
                                maintenance_modification_date_time timestamp,
                                maintenance_modification_date_mask integer
                              );

create or replace function decrementCount(IN partId specimen_parts.id%TYPE, IN decrementVal bigint) RETURNS boolean language plpgsql AS
$$
declare
  result boolean := true;
begin
  UPDATE specimen_parts
  SET specimen_part_count_min = case when specimen_part_count_min = 1 then specimen_part_count_min else specimen_part_count_min - decrementVal end,
      specimen_part_count_max = case when specimen_part_count_max = 1 then specimen_part_count_max else specimen_part_count_max - decrementVal end
  WHERE id = partId;
  return result;
exception when others then
  RAISE WARNING 'Error in decrementCount: %', SQLERRM;
  return false;
end;
$$;

create or replace function createCodes(IN new_part_id specimen_parts.id%TYPE, IN codeToSplit varchar) returns integer language plpgsql AS
$$
declare
  codes_prefix varchar;
  codes_prefix_separator varchar;
  code_to_insert varchar;
  codes_suffix_separator varchar;
  codes_suffix varchar;
  response integer := 0;
begin
  IF codeToSplit = 'A.193' THEN
    codes_prefix := 'A';
    codes_prefix_separator := '.';
    code_to_insert:= '193';
  ELSIF codeToSplit = 'A/G/26' THEN
    codes_prefix := 'A/G';
    codes_prefix_separator := '/';
    code_to_insert:= '26';
  ELSIF codeToSplit = 'A.G306' THEN
    codes_prefix := 'A';
    codes_prefix_separator := '.';
    code_to_insert:= 'G306';
  ELSIF codeToSplit = 'HIOL.1054' THEN
    codes_prefix := 'HOL';
    codes_prefix_separator := '.';
    code_to_insert:= '1054';
  ELSIF codeToSplit = 'IINV.62442' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '62442';
  ELSIF codeToSplit = 'IN16124' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '16124';
  ELSIF codeToSplit = 'IV.17792' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '17792';
  ELSIF codeToSplit = 'IV.84836' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    code_to_insert:= '84836';
  ELSIF codeToSplit = 'T1987' THEN
    codes_prefix := 'T';
    codes_prefix_separator := '.';
    code_to_insert:= '1987';
  ELSIF codeToSplit = 'V.78427' THEN
    codes_prefix := 'V';
    codes_prefix_separator := '.';
    code_to_insert:= '78427';
  ELSIF substr(codeToSplit, 1, 4) IN ('AST.', 'CRI.', 'HOL.', 'INV.', 'NIV.', 'OPH.', 'POP.') THEN
    codes_prefix := substr(codeToSplit, 1, 3);
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 4 THEN
      code_to_insert:= substr(codeToSplit, 5);
    END IF;
  ELSIF substr(codeToSplit, 1, 3) ='IN.' THEN
    codes_prefix := 'INV';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 3 THEN
      code_to_insert:= substr(codeToSplit, 4);
    END IF;
  ELSIF substr(codeToSplit, 1, 5) = 'HIST.' THEN
    codes_prefix := 'HIST';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 5 THEN
      code_to_insert:= substr(codeToSplit, 6);
    END IF;
  ELSIF substr(codeToSplit, 1, 6) = 'KREPS.' THEN
    codes_prefix := 'KREPS';
    codes_prefix_separator := '.';
    IF length(codeToSplit) > 6 THEN
      code_to_insert:= substr(codeToSplit, 7);
    END IF;
  ELSIF codeToSplit ~ E'^[PRT]\\s{1,2}\\d+\\s{1}\\w+\\W+\\w+$' THEN
    codes_prefix := substr(codeToSplit, 1, 1);
    codes_prefix_separator := ' ';
    code_to_insert:= trim(substring(trim(substr(codeToSplit, 2)) from E'^\\d+\\s{1}'));
    codes_suffix_separator := ' ';
    codes_suffix := substring(codeToSplit from E'\\w+\\W+\\w+$');
  ELSIF codeToSplit Like 'ALEX %' THEN
    code_to_insert:= codeToSplit;
  ELSE
    return response;
  END IF;
  INSERT INTO codes
  (referenced_relation, record_id, code_category, "code_prefix", "code_prefix_separator", "code", "code_suffix", "code_suffix_separator")
  VALUES
  ('specimen_parts', new_part_id, 'main', codes_prefix, codes_prefix_separator, code_to_insert, codes_suffix, codes_suffix_separator);
  GET DIAGNOSTICS response := ROW_COUNT;
  return response;
exception
  when others then
    RAISE WARNING 'Error in createCodes: %', SQLERRM;
    return -1;
end;
$$;

create or replace function createNewPart(IN part_id specimen_parts.id%TYPE, IN recPartsDetails recPartsDetail) RETURNS specimen_parts.id%TYPE language plpgsql AS
$$
DECLARE  
  new_part_id specimen_parts.id%TYPE;
  code_count integer;
BEGIN
  INSERT INTO specimen_parts
  (parent_ref, path, specimen_individual_ref, specimen_part, complete,
    building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage, 
    surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref
  )
  (
    SELECT parent_ref, path, specimen_individual_ref, specimen_part, complete, 
          building, "floor", room, "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage,
          surnumerary, specimen_status, recPartsDetails.part_count_min, recPartsDetails.part_count_min, institution_ref
    FROM specimen_parts 
    WHERE id = part_id
  )
  RETURNING id INTO new_part_id;
  INSERT INTO partsSplitFromAndTo (id, "start")
  (SELECT new_part_id, false WHERE NOT EXISTS (SELECT 1 FROM partsSplitFromAndTo WHERE NOT "start" ));
  if decrementCount(part_id, recPartsDetails.part_count_min) then
  end if;
  SELECT count(full_code_order_by) INTO code_count
  FROM codes
  WHERE referenced_relation = 'specimen_parts'
    AND code_category = 'main'
    AND record_id = part_id;
  IF code_count > 1 THEN
    UPDATE codes
    SET record_id = new_part_id
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND code_category = 'main'
      AND full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code);
    GET DIAGNOSTICS code_count = ROW_COUNT;
    IF code_count = 0 THEN
      IF createCodes (new_part_id, recPartsDetails.main_code) < 0 THEN
        return -1;
      END IF;
    END IF;
  ELSIF code_count = 1 THEN
    INSERT INTO codes
    (referenced_relation, record_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask)
    (SELECT 'specimen_parts', new_part_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask
     FROM codes
     WHERE referenced_relation = 'specimen_parts'
       AND record_id = part_id
       AND code_category = 'main'
       AND full_code_order_by IN (recPartsDetails.main_code, recPartsDetails.rbins_code, recPartsDetails.inventory_code, recPartsDetails.batch_main_code)
    );
    GET DIAGNOSTICS code_count = ROW_COUNT;
    IF code_count = 0 THEN
      IF createCodes (new_part_id, recPartsDetails.old_main_code) < 0 THEN
        return -1;
      END IF;
    END IF;
  ELSE
    IF createCodes (new_part_id, recPartsDetails.old_main_code) < 0 THEN
      return -1;
    END IF;
  END IF;
  /* Comments are not treated: only two are in identifiable corresponding old parts, but do not need to be splitted*/
  return new_part_id;
EXCEPTION
  WHEN OTHERS THEN
    RAISE WARNING 'Error in createNewPart: %', SQLERRM;
    return -1;
END;
$$;

create or replace function moveOrCreateProp (IN part_id specimen_parts.id%TYPE, IN new_part_id specimen_parts.id%TYPE, IN recPartsDetails recPartsDetail, IN recFirstPartsDetails recPartsDetail) returns integer language plpgsql AS
$$
declare
  prop_count integer;
  update_count integer;
  cat_prop_id integer;
  booUpdate boolean := false;
  booContinue boolean := false;
begin
  /*Freshness Level*/
  IF coalesce(recPartsDetails.freshness_level, '') != '' THEN
    SELECT COUNT(*)
    INTO prop_count
    FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
    WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level;
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type)
    (
      SELECT 'specimen_parts', new_part_id, 'part state', 'freshness level'
      WHERE NOT EXISTS (SELECT 1
                        FROM catalogue_properties
                        WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
                       )
    );
    IF prop_count >= 1 THEN
      IF prop_count > 1 OR (prop_count = 1 AND recPartsDetails.freshness_level != recFirstPartsDetails.freshness_level) THEN
        booUpdate := true;
      ELSIF prop_count = 1 AND recPartsDetails.freshness_level = recFirstPartsDetails.freshness_level THEN
        booContinue := true;
      END IF;
      IF booUpdate THEN
        UPDATE properties_values
        SET property_ref = (SELECT id
                            FROM catalogue_properties
                            WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
                          )
        WHERE property_ref = (SELECT DISTINCT catalogue_properties.id
                              FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
                              WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level
                              LIMIT 1
                            )
          AND property_value_unified = recPartsDetails.freshness_level
          AND id IN (SELECT properties_values.id
                     FROM catalogue_properties INNER JOIN properties_values ON catalogue_properties.id = properties_values.property_ref
                     WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'part state' AND property_sub_type = 'freshness level' AND property_value = recPartsDetails.freshness_level
                     LIMIT 1
                    );
        GET DIAGNOSTICS update_count = ROW_COUNT;
        IF update_count = 0 THEN
          booContinue := true;
        END IF;
      END IF;
    ELSE
      booContinue := true;
    END IF;
    IF booContinue THEN
      INSERT INTO properties_values (property_ref, property_value)
      (SELECT id, recPartsDetails.freshness_level
        FROM catalogue_properties
        WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'part state' AND property_sub_type = 'freshness level'
      );
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Weight level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'weight'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id THEN
    IF coalesce(recPartsDetails.old_weight_min, '') != '' OR coalesce(recPartsDetails.old_weight_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'weight', recPartsDetails.old_weight_unit, recPartsDetails.old_weight_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'weight'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_weight_min, '') = coalesce(recPartsDetails.old_weight_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_weight_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'weight'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_weight_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_weight_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'weight'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_weight_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_weight_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'weight'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'weight'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'weight'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Length level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') NOT IN ('height', 'depth')
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') NOT IN ('height','depth') LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') NOT IN ('height','depth') LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_length_min, '') != '' OR coalesce(recPartsDetails.old_length_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', recPartsDetails.old_length_unit, recPartsDetails.old_length_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') NOT IN ('height','depth')
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_length_min, '') = coalesce(recPartsDetails.old_length_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_length_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') NOT IN ('height','depth')
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_length_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_length_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') NOT IN ('height','depth')
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_length_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_length_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') NOT IN ('height','depth')
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') NOT IN ('height','depth')
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') NOT IN ('height','depth')
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Height level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') = 'height'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'height' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'height' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_height_min, '') != '' OR coalesce(recPartsDetails.old_height_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', 'height', recPartsDetails.old_height_unit, recPartsDetails.old_height_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') = 'height'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_height_min, '') = coalesce(recPartsDetails.old_height_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_height_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') = 'height'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_height_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_height_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'height'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_height_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_height_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'height'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'height'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'height'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Depth level*/
  SELECT
  NOT EXISTS (SELECT 1
              FROM users_tracking as ut
              WHERE (ut.referenced_relation = 'catalogue_properties'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.old_value -> 'record_id' = part_id::varchar
                           AND ut.old_value -> 'property_type' = 'physical measurement'
                           AND ut.old_value -> 'property_sub_type' = 'length'
                           AND coalesce(ut.old_value -> 'property_qualifier' , '') = 'depth'
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'referenced_relation' = 'specimen_parts'
                           AND ut.new_value -> 'record_id' = part_id::varchar
                          )
                         )
                    )
                 OR (ut.referenced_relation = 'properties_values'
                     AND ((ut.action IN ('update', 'delete')
                           AND ut.old_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'depth' LIMIT 1)
                          )
                          OR
                          (ut.action IN ('insert')
                           AND ut.new_value -> 'property_ref' = (SELECT id::varchar FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier,'') = 'depth' LIMIT 1)
                          )
                         )
                    )
             )
  INTO booContinue;
--   RAISE NOTICE '< No User tracking for this part ?: %', booContinue;
  IF booContinue AND recPartsDetails.old_spec_id != recFirstPartsDetails.old_spec_id  THEN
    IF coalesce(recPartsDetails.old_depth_min, '') != '' OR coalesce(recPartsDetails.old_depth_max, '') != '' THEN
      INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
      (
        SELECT 'specimen_parts', new_part_id, 'physical measurement', 'length', 'depth', recPartsDetails.old_depth_unit, recPartsDetails.old_depth_unit
        WHERE NOT EXISTS (SELECT 1
                          FROM catalogue_properties
                          WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND property_type = 'physical measurement' AND property_sub_type = 'length' AND coalesce(property_qualifier, '') = 'depth'
                        )
      );
--       GET DIAGNOSTICS prop_count = ROW_COUNT;
--       RAISE NOTICE '<< Catalogue prop inserted: %', prop_count;
      IF coalesce(recPartsDetails.old_depth_min, '') = coalesce(recPartsDetails.old_depth_max, '') THEN
--         RAISE NOTICE '<<< Min and Max the same';
        INSERT INTO properties_values (property_ref, property_value)
        (SELECT id, recPartsDetails.old_depth_min
          FROM catalogue_properties
          WHERE referenced_relation = 'specimen_parts'
            AND record_id = new_part_id
            AND property_type = 'physical measurement'
            AND property_sub_type = 'length'
            AND coalesce(property_qualifier, '') = 'depth'
        );
--         GET DIAGNOSTICS prop_count = ROW_COUNT;
--         RAISE NOTICE '<< Catalogue prop value inserted: %', prop_count;
      ELSE
--         RAISE NOTICE '<<< Min and Max Not the same';
        IF coalesce(recPartsDetails.old_depth_min, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_depth_min
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'depth'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value min inserted: %', prop_count;
        END IF;
        IF coalesce(recPartsDetails.old_depth_max, '') != '' THEN
          INSERT INTO properties_values (property_ref, property_value)
          (SELECT id, recPartsDetails.old_depth_max
            FROM catalogue_properties
            WHERE referenced_relation = 'specimen_parts'
              AND record_id = new_part_id
              AND property_type = 'physical measurement'
              AND property_sub_type = 'length'
              AND coalesce(property_qualifier, '') = 'depth'
          );
--           GET DIAGNOSTICS prop_count = ROW_COUNT;
--           RAISE NOTICE '<< Catalogue prop value max inserted: %', prop_count;
        END IF;
      END IF;
    END IF;
  ELSE
    INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit)
    (SELECT referenced_relation, new_part_id, property_type, property_sub_type, property_qualifier, property_method, property_tool, property_unit, property_accuracy_unit
      FROM catalogue_properties
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'depth'
    )
    RETURNING id INTO cat_prop_id;
    INSERT INTO properties_values (property_ref, property_value, property_accuracy)
    (SELECT DISTINCT cat_prop_id, property_value, property_accuracy
      FROM catalogue_properties as cp INNER JOIN properties_values as pv ON cp.id = pv.property_ref
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND property_type = 'physical measurement'
        AND property_sub_type = 'length'
        AND coalesce(property_qualifier, '') = 'depth'
    );
--     GET DIAGNOSTICS prop_count = ROW_COUNT;
--     RAISE NOTICE '< All in one insertion: %', prop_count;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Insurances*/
  IF recPartsDetails.old_insurance_value > 0 THEN
    IF coalesce(recPartsDetails.old_spec_id = recFirstPartsDetails.old_spec_id) AND coalesce(recPartsDetails.old_insurance_value,0) != 0 THEN
      booUpdate := true;
    ELSE
      SELECT COUNT(*) INTO prop_count
      FROM insurances
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
        AND 1 < (SELECT COUNT(*) FROM insurances WHERE referenced_relation = 'specimen_parts' AND record_id = part_id);
      IF prop_count = 1 THEN
        booUpdate := true;
      ELSE
        booContinue := true;
      END IF;
    END IF;
    IF booUpdate THEN
      UPDATE insurances
      SET record_id = new_part_id
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0);
      GET DIAGNOSTICS update_count = ROW_COUNT;
      IF update_count = 0 THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_year)
      (SELECT 'specimen_parts', new_part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0) WHERE NOT EXISTS (SELECT 1 FROM insurances WHERE referenced_relation = 'specimen_parts' AND record_id = new_part_id AND insurance_year = coalesce(recPartsDetails.old_insurance_year,0)));
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  /*Maintenances*/
  IF recPartsDetails.maintenance_people_ref > 0 AND recPartsDetails.maintenance_people_ref IS NOT NULL THEN
    IF coalesce(recPartsDetails.old_spec_id = recFirstPartsDetails.old_spec_id) THEN
      booUpdate := true;
    ELSE
      SELECT COUNT(*) INTO prop_count
      FROM collection_maintenance
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND "category" = recPartsDetails.maintenance_category
        AND action_observation = recPartsDetails.maintenance_action_observation
        AND people_ref = recPartsDetails.maintenance_people_ref
        AND modification_date_time = recPartsDetails.maintenance_modification_date_time
        AND 1 < (SELECT COUNT(*)
                 FROM collection_maintenance
                 WHERE referenced_relation = 'specimen_parts'
                   AND record_id = part_id
                   AND "category" = recPartsDetails.maintenance_category
                   AND action_observation = recPartsDetails.maintenance_action_observation
                   AND people_ref = recPartsDetails.maintenance_people_ref
                   AND modification_date_time = recPartsDetails.maintenance_modification_date_time
                );
      IF prop_count = 1 THEN
        booUpdate := true;
      ELSE
        booContinue := true;
      END IF;
    END IF;
    IF booUpdate THEN
      UPDATE collection_maintenance
      SET record_id = new_part_id,
          modification_date_mask = recPartsDetails.maintenance_modification_date_mask
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = part_id
        AND "category" = recPartsDetails.maintenance_category
        AND action_observation = recPartsDetails.maintenance_action_observation
        AND people_ref = recPartsDetails.maintenance_people_ref
        AND modification_date_time = recPartsDetails.maintenance_modification_date_time;
      GET DIAGNOSTICS update_count = ROW_COUNT;
      IF update_count = 0 THEN
        booContinue := true;
      END IF;
    END IF;
    IF booContinue THEN
      INSERT INTO collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, modification_date_time, modification_date_mask)
      (SELECT 'specimen_parts', new_part_id, recPartsDetails.maintenance_people_ref, recPartsDetails.maintenance_category, recPartsDetails.maintenance_action_observation, recPartsDetails.maintenance_modification_date_time, recPartsDetails.maintenance_modification_date_mask
       WHERE NOT EXISTS
             (SELECT 1
              FROM collection_maintenance
              WHERE referenced_relation = 'specimen_parts'
                AND record_id = new_part_id
                AND "category" = recPartsDetails.maintenance_category
                AND action_observation = recPartsDetails.maintenance_action_observation
                AND people_ref = recPartsDetails.maintenance_people_ref
                AND modification_date_time = recPartsDetails.maintenance_modification_date_time
             )
      );
    END IF;
  END IF;
  booContinue := false;
  booUpdate := false;
  return 1;
exception
  when others then
    raise warning 'Error in moveOrCreateProp: %', SQLERRM;
    return -1;
end;
$$;

create or replace function createProperties (IN part_id specimen_parts.id%TYPE, recPartsDetails recPartsDetail) returns integer language plpgsql AS
$$
declare
  cat_prop_id integer := 0;
  prop_id integer := 0;
  prop_count integer;
  booContinue boolean := true;
begin
  /*Freshness level*/
  IF coalesce(recPartsDetails.freshness_level,'') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'part state'
      AND property_sub_type = 'freshness level'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'part state'
                          AND old_value -> 'property_sub_type' = 'freshness level'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type)
        (
          select 'specimen_parts', part_id, 'part state', 'freshness level'
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.freshness_level
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Insurances*/
  IF coalesce(recPartsDetails.old_insurance_value,0) != 0 THEN
    SELECT NOT EXISTS ( select 1
                        from users_tracking
                        where referenced_relation = 'insurances'
                          and (action in ('delete', 'update')
                                and old_value -> 'referenced_relation' = 'specimen_parts'
                                and old_value -> 'record_id' = part_id::varchar
                                and old_value -> 'insurance_year' = coalesce(recPartsDetails.old_insurance_year,0)::varchar
                              )
                          or (action in ('insert')
                                and new_value -> 'referenced_relation' = 'specimen_parts'
                                and new_value -> 'record_id' = part_id::varchar
                                and new_value -> 'insurance_year' = coalesce(recPartsDetails.old_insurance_year,0)::varchar
                              )
                      )
    INTO booContinue;
    IF booContinue THEN
      update insurances
      set insurance_value = recPartsDetails.old_insurance_value
      where referenced_relation = 'specimen_parts'
        and record_id = part_id
        and insurance_year = coalesce(recPartsDetails.old_insurance_year,0);
      GET DIAGNOSTICS prop_count = ROW_COUNT;
      IF prop_count = 0 THEN
        insert into insurances (referenced_relation, record_id, insurance_value, insurance_year)
        (
          select 'specimen_parts', part_id, recPartsDetails.old_insurance_value, coalesce(recPartsDetails.old_insurance_year,0)
          where not exists (
                              select 1
                              from insurances
                              where referenced_relation = 'specimen_parts'
                                and record_id = part_id
                                and insurance_year = coalesce(recPartsDetails.old_insurance_year,0)
                           )
        );
      END IF;
    END IF;
  END IF;
  /*Maintenances*/
  IF recPartsDetails.maintenance_people_ref IS NOT NULL THEN
    insert into collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, modification_date_time, modification_date_mask)
    (
      select 'specimen_parts', part_id, recPartsDetails.maintenance_people_ref, recPartsDetails.maintenance_category, recPartsDetails.maintenance_action_observation, recPartsDetails.maintenance_modification_date_time, recPartsDetails.maintenance_modification_date_mask
      where not exists (
                          select 1
                          from collection_maintenance
                          where referenced_relation = 'specimen_parts'
                            and record_id = part_id
                            and people_ref = recPartsDetails.maintenance_people_ref
                            and "category" = recPartsDetails.maintenance_category
                            and action_observation = recPartsDetails.maintenance_action_observation
                            and modification_date_time = recPartsDetails.maintenance_modification_date_time
                            and modification_date_mask = recPartsDetails.maintenance_modification_date_mask
                       )
        and not exists (
                          select 1
                          from users_tracking
                          where referenced_relation = 'collection_maintenance'
                            and (action in ('delete', 'update')
                                  and old_value -> 'referenced_relation' = 'specimen_parts'
                                  and old_value -> 'record_id' = part_id::varchar
                                  and old_value -> 'people_ref' = recPartsDetails.maintenance_people_ref::varchar
                                  and old_value -> 'category' = recPartsDetails.maintenance_category::varchar
                                  and old_value -> 'action_observation' = recPartsDetails.maintenance_action_observation::varchar
                                  and old_value -> 'modification_date_time' = recPartsDetails.maintenance_modification_date_time::varchar
                                  and old_value -> 'modification_date_mask' = recPartsDetails.maintenance_modification_date_mask::varchar
                                )
                            or (action in ('delete', 'update')
                                  and new_value -> 'referenced_relation' = 'specimen_parts'
                                  and new_value -> 'record_id' = part_id::varchar
                                  and new_value -> 'people_ref' = recPartsDetails.maintenance_people_ref::varchar
                                  and new_value -> 'category' = recPartsDetails.maintenance_category::varchar
                                  and new_value -> 'action_observation' = recPartsDetails.maintenance_action_observation::varchar
                                  and new_value -> 'modification_date_time' = recPartsDetails.maintenance_modification_date_time::varchar
                                  and new_value -> 'modification_date_mask' = recPartsDetails.maintenance_modification_date_mask::varchar
                               )
                       )
        and exists (select 1 from people where id = recPartsDetails.maintenance_people_ref)
    );
  END IF;
  /*Length level*/
  IF coalesce(recPartsDetails.old_length_min, '') != '' OR coalesce(recPartsDetails.old_length_max, '') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier NOT IN ('height','depth')
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' NOT IN ('height','depth')
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', '', recPartsDetails.old_length_unit, recPartsDetails.old_length_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_length_min
            where coalesce(recPartsDetails.old_length_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_length_max
            where coalesce(recPartsDetails.old_length_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Height level*/
  IF coalesce(recPartsDetails.old_height_min, '') != '' OR coalesce(recPartsDetails.old_height_max, '') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier = 'height'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' = 'height'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', 'height', recPartsDetails.old_height_unit, recPartsDetails.old_height_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_height_min
            where coalesce(recPartsDetails.old_height_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_height_max
            where coalesce(recPartsDetails.old_height_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Depth level*/
  IF coalesce(recPartsDetails.old_depth_min, '') != '' OR coalesce(recPartsDetails.old_depth_max, '') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'length'
      AND property_qualifier = 'depth'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'length'
                          AND old_value -> 'property_qualifier' = 'depth'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'length', 'depth', recPartsDetails.old_depth_unit, recPartsDetails.old_depth_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_depth_min
            where coalesce(recPartsDetails.old_depth_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_depth_max
            where coalesce(recPartsDetails.old_depth_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  /*Weight level*/
  IF coalesce(recPartsDetails.old_weight_min, '') != '' OR coalesce(recPartsDetails.old_weight_max, '') != '' THEN
    SELECT id INTO cat_prop_id
    FROM catalogue_properties as cp
    WHERE referenced_relation = 'specimen_parts'
      AND record_id = part_id
      AND property_type = 'physical measurement'
      AND property_sub_type = 'weight'
      AND coalesce(property_method, '') = ''
      AND coalesce(property_tool, '') = ''
      AND date_from = '0001-01-01 00:00:00'
      AND date_to = '2038-12-31 00:00:00'
      AND NOT EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'catalogue_properties' AND record_id = cp.id AND action IN ('insert','update'));
    IF coalesce(cat_prop_id, 0) = 0 THEN
      SELECT NOT EXISTS(SELECT 1
                        FROM users_tracking
                        WHERE referenced_relation = 'catalogue_properties'
                          AND action = 'delete'
                          AND old_value -> 'referenced_relation' = 'specimen_parts'
                          AND old_value -> 'record_id' = part_id::varchar
                          AND old_value -> 'property_type' = 'physical measurement'
                          AND old_value -> 'property_sub_type' = 'weight'
                          AND coalesce(old_value -> 'property_method', '') = ''
                          AND coalesce(old_value -> 'property_tool', '') = ''
                          AND old_value -> 'date_from' = '0001-01-01 00:00:00'
                          AND old_value -> 'date_to' = '2038-12-31 00:00:00'
                       ) INTO booContinue;
    END IF;
    IF booContinue THEN
      SELECT NOT EXISTS ( SELECT 1
                          FROM properties_values as pv
                          WHERE pv.property_ref = cat_prop_id
                            AND EXISTS (SELECT 1 FROM users_tracking WHERE referenced_relation = 'properties_values' AND record_id = pv.id AND action IN ('insert','update'))
                        )
        AND NOT EXISTS ( SELECT 1
                         FROM users_tracking
                         WHERE referenced_relation = 'properties_values'
                           AND action = 'delete'
                           AND coalesce(old_value -> 'property_ref', '') = (coalesce(cat_prop_id,0))::varchar
                       )
      INTO booContinue;
      IF booContinue THEN
        DELETE FROM catalogue_properties
        WHERE id = cat_prop_id;
        insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, property_unit, property_accuracy_unit)
        (
          select 'specimen_parts', part_id, 'physical measurement', 'weight', '', recPartsDetails.old_weight_unit, recPartsDetails.old_weight_unit
        )
        returning id INTO cat_prop_id;
        IF cat_prop_id != 0 THEN
          insert into properties_values (property_ref, property_value)
          (
            select cat_prop_id, recPartsDetails.old_weight_min
            where coalesce(recPartsDetails.old_weight_min, '') != ''
            union
            select cat_prop_id, recPartsDetails.old_weight_max
            where coalesce(recPartsDetails.old_weight_max, '') != ''
          );
        END IF;
      END IF;
    END IF;
    cat_prop_id := 0;
    booContinue := true;
  END IF;
  return 1;
exception
  when others then
    RAISE WARNING 'Error in createProperties: %', SQLERRM;
    return -1;
end;
$$;

drop function if exists resplit_parts();
create or replace function resplit_parts () returns boolean language plpgsql as
$$
declare
  recPartsDetails recPartsDetail;
  recFirstPart recPartsDetail;
  part_id specimen_parts.id%TYPE := 0;
  new_part_id specimen_parts.id%TYPE;
  code_count integer;
  recActualCodes varchar[];
  recTransferedCodes varchar[];
  recProperties RECORD;
  recInsurances RECORD;
  spec_part_count_min integer;
  spec_part_count_max integer;
begin
  INSERT INTO partsSplitFromAndTo (id)
  (
    SELECT max(specimen_parts.id)
    FROM darwin1.tbl_specimen_groups
          inner join darwin1.id_refs
          on sgr_id_ctn = old_id and system = 'individuals'
          inner join (
                        darwin1.tbl_rooms
                        inner join (
                                    darwin1.tbl_building_floors
                                    inner join darwin1.tbl_buildings
                                    on bui_id_ctn = bfl_building_nr
                                  )
                        on bfl_id_ctn = rom_building_floor_nr
                      )
          on sgr_room_nr = rom_id_ctn
          inner join
          specimen_parts
          on  specimen_individual_ref = new_id
              and specimen_part = lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation'))
              and coalesce(specimen_parts.building,'') =
                  case
                    when sgr_room_nr = 0 then ''
                    else bui_name
                  end::varchar
              and coalesce(specimen_parts.floor,'') =
                  case
                    when sgr_room_nr = 0 then ''
                    else bfl_floor
                  end::varchar
              and coalesce(specimen_parts.room, '') =
                  case
                    when sgr_room_nr = 0 then ''
                    else rom_code
                  end::varchar
              and coalesce(specimen_parts.row, '') = coalesce(sgr_row, '')
              and coalesce(specimen_parts.shelf, '') = coalesce(sgr_shelf, '')
              and coalesce(specimen_parts.container, '') = coalesce(sgr_container, '')
              and coalesce(specimen_parts.sub_container, '') = coalesce(sgr_subcontainer, '')
              and complete =
                  case
                    when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true
                    else false
                  end    
  );
  FOR recPartsDetails IN select sgr_id_ctn as old_spec_id, specimen_individual_ref,  specimen_parts.id as parts_id, fullToIndex(sgr_code) as main_code,
                                fullToIndex(bat_unique_rbins_code) as rbins_code, fullToIndex(bat_code) as batch_main_code, fullToIndex(bat_inventory_code) as inventory_code, sgr_code as old_main_code,
                                specimen_part, building, coalesce(building, '') as coalesced_building, 
                                "floor", coalesce("floor", '') as coalesced_floor, 
                                room, coalesce("room", '') as coalesced_room, 
                                specimen_parts.row as row, coalesce("row", '') as coalesced_row,  
                                specimen_parts.shelf as shelf, coalesce("shelf", '') as coalesced_shelf, 
                                specimen_parts.container as container, coalesce("container", '') as coalesced_container, 
                                specimen_parts.sub_container as sub_container, coalesce("sub_container", '') as coalesced_sub_container, 
                                lower(cty_type_short_descr) as container_and_sub_container_type, specimen_parts.container_type as container_type, specimen_parts.sub_container_type as sub_container_type,
                                lower(sto_storage) as container_and_sub_container_storage, specimen_parts.container_storage as container_storage, specimen_parts.sub_container_storage as sub_container_storage,
                                specimen_status, 
                                case when sgr_number_in_group < 1 or sgr_number_in_group is null then 1 else sgr_number_in_group end as part_count_min,
                                specimen_part_count_min,
                                specimen_part_count_max,
                                case 
                                  when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                                  else false 
                                end as complete,
                                case when sfl_description = 'Undefined' then '' else coalesce(sfl_description,'') end as freshness_level,
                                sgr_length_min::varchar as old_length_min,
                                convert_to_unified(sgr_length_min::varchar, case when length_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(length_unit.uni_unit,'m') END, 'length') as length_min_unified,
                                sgr_length_max::varchar as old_length_max,
                                convert_to_unified(sgr_length_max::varchar, case when length_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(length_unit.uni_unit,'m') END, 'length') as length_max_unified,
                                case when length_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(length_unit.uni_unit,'m') END as old_length_unit,
                                sgr_height_min::varchar as old_height_min,
                                convert_to_unified(sgr_height_min::varchar, case when height_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(height_unit.uni_unit,'m') END, 'length') as height_min_unified,
                                sgr_height_max::varchar as old_height_max,
                                convert_to_unified(sgr_height_max::varchar, case when height_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(height_unit.uni_unit,'m') END, 'length') as height_max_unified,
                                case when height_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(height_unit.uni_unit,'m') END as old_height_unit,
                                sgr_depth_min::varchar as old_depth_min,
                                convert_to_unified(sgr_depth_min::varchar, case when depth_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(depth_unit.uni_unit,'m') END, 'length') as depth_min_unified,
                                sgr_depth_max::varchar as old_depth_max,
                                convert_to_unified(sgr_depth_max::varchar, case when depth_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(depth_unit.uni_unit,'m') END, 'length') as depth_max_unified,
                                case when depth_unit.uni_unit = 'Undef.' THEN 'm' ELSE coalesce(depth_unit.uni_unit,'m') END as old_depth_unit,
                                sgr_weight_min::varchar as old_weight_min,
                                convert_to_unified(sgr_weight_min::varchar, case when weight_unit.uni_unit = 'Undef.' THEN 'g' ELSE coalesce(weight_unit.uni_unit,'g') END, 'weight') as weight_min_unified,
                                sgr_weight_max::varchar as old_weight_max,
                                convert_to_unified(sgr_weight_max::varchar, case when weight_unit.uni_unit = 'Undef.' THEN 'g' ELSE coalesce(weight_unit.uni_unit,'g') END, 'weight') as weight_max_unified,
                                case when weight_unit.uni_unit = 'Undef.' THEN 'g' ELSE coalesce(weight_unit.uni_unit,'g') END as old_weight_unit,
                                bat_value as old_insurance_value,
                                bat_value_year as old_insurance_year,
                                case when exists(select 1 from people where id = sgr_preparator_nr) then case when sgr_preparator_nr = 0 then null else sgr_preparator_nr end else null::integer end as maintenance_people_ref,
                                'action' as maintenance_category,
                                'preparation' as maintenance_action_observation,
                                (
                                    ('01/' ||
                                    CASE WHEN sgr_preparation_month IS NULL THEN '01' ELSE sgr_preparation_month::varchar END
                                    || '/' ||
                                    CASE WHEN sgr_preparation_year IS NULL THEN '0001' ELSE sgr_preparation_year::varchar END
                                    )::timestamp
                                    + (CASE WHEN sgr_preparation_day IS NULL OR sgr_preparation_day = 0 THEN '0' ELSE (sgr_preparation_day::integer - 1)::varchar END || ' days')::interval
                                )::timestamp as maintenance_modification_date_time,
                                CASE WHEN sgr_preparation_day IS NOT NULL THEN 8 ELSE 0 END
                                +
                                CASE WHEN sgr_preparation_month IS NOT NULL THEN 16 ELSE 0 END
                                +
                                CASE WHEN sgr_preparation_year IS NOT NULL THEN 32 ELSE 0 END as maintenance_modification_date_mask
                          from darwin1.tbl_specimen_groups
                          inner join darwin1.id_refs 
                          on sgr_id_ctn = old_id and system = 'individuals'
                          inner join (
                                        darwin1.tbl_rooms
                                        inner join (
                                                    darwin1.tbl_building_floors
                                                    inner join darwin1.tbl_buildings 
                                                    on bui_id_ctn = bfl_building_nr
                                                  )
                                        on bfl_id_ctn = rom_building_floor_nr
                                      )
                          on sgr_room_nr = rom_id_ctn
                          inner join darwin1.tbl_specimen_status 
                          on sst_id_ctn = sgr_status_nr
                          inner join darwin1.tbl_storage on sto_id_ctn = sgr_storage_nr
                          inner join darwin1.tbl_container_types on cty_id_ctn = sgr_container_type_nr
                          inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                          inner join darwin1.tbl_spec_freshness_levels on sfl_level = sgr_freshness_level_nr
                          inner join darwin1.tbl_units as length_unit on sgr_length_min_uni_nr = length_unit.uni_id_ctn
                          inner join darwin1.tbl_units as height_unit on sgr_height_min_uni_nr = height_unit.uni_id_ctn
                          inner join darwin1.tbl_units as depth_unit on sgr_depth_min_uni_nr = depth_unit.uni_id_ctn
                          inner join darwin1.tbl_units as weight_unit on sgr_weight_min_uni_nr = weight_unit.uni_id_ctn
                          inner join darwin1.tbl_units as vol_unit on sgr_vol_min_uni_nr = vol_unit.uni_id_ctn
                          inner join 
                          specimen_parts
                          on  specimen_individual_ref = new_id
                              and specimen_part = lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation'))
                              and coalesce(specimen_parts.building,'') = 
                                  case 
                                    when sgr_room_nr = 0 then '' 
                                    else bui_name 
                                  end::varchar
                              and coalesce(specimen_parts.floor,'') = 
                                  case 
                                    when sgr_room_nr = 0 then '' 
                                    else bfl_floor 
                                  end::varchar
                              and coalesce(specimen_parts.room, '') = 
                                  case 
                                    when sgr_room_nr = 0 then '' 
                                    else rom_code 
                                  end::varchar
                              and coalesce(specimen_parts.row, '') = coalesce(sgr_row, '')
                              and coalesce(specimen_parts.shelf, '') = coalesce(sgr_shelf, '')
                              and coalesce(specimen_parts.container, '') = coalesce(sgr_container, '')
                              and coalesce(specimen_parts.sub_container, '') = coalesce(sgr_subcontainer, '')
                              and complete = 
                                  case 
                                    when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                                    else false 
                                  end
/*                          where sgr_preparator_nr is not null and sgr_preparator_nr != 0*/
                          /*where bat_value is not null *//*and specimen_parts.id = 594237*/
                          /*where bat_collection_id_nr between 1 and 8*/
                                /*where bat_collection_id_nr = 133*/
                          /*where (sgr_weight_min is not null or sgr_weight_max is not null *//*and sgr_length_min != sgr_length_max*//*)*/
--                             where specimen_parts.id in (585835, 585836)
                            /*and specimen_parts.id in (585835, 585836)*/
--                           where sfl_description is not null and sfl_description != 'Undefined'
                                /*exists (select 1 from comments where comment is not null and referenced_relation = 'specimen_parts' and record_id = specimen_parts.id limit 1)*/
                          order by new_id desc, specimen_part, main_code 
--                           limit 50
  LOOP
    IF part_id != recPartsDetails.parts_id THEN
--       RAISE NOTICE 'Next part infos: %', recPartsDetails;
      recFirstPart := recPartsDetails;
      part_id := recPartsDetails.parts_id;
      IF recPartsDetails.specimen_part_count_min = 0 AND recPartsDetails.part_count_min > 0 THEN
        IF recPartsDetails.specimen_part_count_max = recPartsDetails.specimen_part_count_min THEN
          UPDATE specimen_parts
          SET specimen_part_count_min = recPartsDetails.part_count_min,
              specimen_part_count_max = recPartsDetails.part_count_min
          WHERE id = part_id;
        ELSIF recPartsDetails.specimen_part_count_max >= recPartsDetails.part_count_min THEN
          UPDATE specimen_parts
          SET specimen_part_count_min = recPartsDetails.part_count_min
          WHERE id = part_id;
        END IF;
      END IF;
      SELECT count(full_code_order_by) INTO code_count
      FROM codes
      where referenced_relation = 'specimen_parts'
        and record_id = part_id
        and code_category = 'main';
      IF code_count != 0 then
        select count(full_code)
        into code_count
        from (
              select full_code_order_by as full_code
              from codes
              where referenced_relation = 'specimen_parts'
                and code_category = 'main'
                and record_id = part_id
              except
              (
                select fullToIndex(sgr_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
                inner join (
                              darwin1.tbl_rooms
                              inner join (
                                          darwin1.tbl_building_floors
                                          inner join darwin1.tbl_buildings 
                                          on bui_id_ctn = bfl_building_nr
                                        )
                              on bfl_id_ctn = rom_building_floor_nr
                            )
                on sgr_room_nr = rom_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
                inner join (
                              darwin1.tbl_rooms
                              inner join (
                                          darwin1.tbl_building_floors
                                          inner join darwin1.tbl_buildings 
                                          on bui_id_ctn = bfl_building_nr
                                        )
                              on bfl_id_ctn = rom_building_floor_nr
                            )
                on sgr_room_nr = rom_id_ctn
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_unique_rbins_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
                inner join (
                              darwin1.tbl_rooms
                              inner join (
                                          darwin1.tbl_building_floors
                                          inner join darwin1.tbl_buildings 
                                          on bui_id_ctn = bfl_building_nr
                                        )
                              on bfl_id_ctn = rom_building_floor_nr
                            )
                on sgr_room_nr = rom_id_ctn
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
                union
                select fullToIndex(bat_inventory_code) as full_code
                from darwin1.tbl_specimen_groups 
                inner join darwin1.id_refs on sgr_id_ctn = old_id and system = 'individuals'
                inner join (
                              darwin1.tbl_rooms
                              inner join (
                                          darwin1.tbl_building_floors
                                          inner join darwin1.tbl_buildings 
                                          on bui_id_ctn = bfl_building_nr
                                        )
                              on bfl_id_ctn = rom_building_floor_nr
                            )
                on sgr_room_nr = rom_id_ctn
                inner join darwin1.tbl_batches on sgr_batch_nr = bat_id_ctn
                where new_id = recPartsDetails.specimen_individual_ref
                  and lower(replace(replace(replace(pit_item, 'Anat.', 'anatomic'), 'Microsc. prep.', 'microscopic preparation'), 'Microsc.prep.', 'microscopic preparation')) = recPartsDetails.specimen_part
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bui_name 
                      end::varchar = recPartsDetails.coalesced_building
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else bfl_floor 
                      end::varchar = recPartsDetails.coalesced_floor
                  and case 
                        when sgr_room_nr = 0 then '' 
                        else rom_code 
                      end::varchar = recPartsDetails.coalesced_room
                  and coalesce(sgr_row, '') = recPartsDetails.coalesced_row
                  and coalesce(sgr_shelf, '') = recPartsDetails.coalesced_shelf
                  and coalesce(sgr_container, '') = recPartsDetails.coalesced_container
                  and coalesce(sgr_subcontainer, '') = recPartsDetails.coalesced_sub_container
                  and case 
                        when sgr_item_concerned_nr in (20, 95, 136, 217, 218, 236, 336) then true 
                        else false 
                      end = recPartsDetails.complete
              ) 
            ) as x;
        IF code_count > 0 THEN
--           RAISE NOTICE '+ Need of new part creation';
          SELECT createNewPart(part_id, recPartsDetails) INTO new_part_id;
          IF new_part_id < 0 THEN
            return false;
          END IF;
--           select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id;
--           select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')) into recTransferedCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_part_id;
--           RAISE NOTICE '++ Actual codes: %, Transfered codes: %', recActualCodes, recTransferedCodes;
--           SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = part_id;
--           RAISE NOTICE '++Actual count min and max: % and %', spec_part_count_min, spec_part_count_max;
--           SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = new_part_id;
--           RAISE NOTICE '++Transfered count min and max: % and %', spec_part_count_min, spec_part_count_max;
--           SELECT array_agg(property_value/*_unified*/)
--           INTO recProperties
--           FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Properties before transfert: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Insurances before transfert: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Maintenance before transfert: %', recProperties;
          IF moveOrCreateProp(part_id, new_part_id, recPartsDetails, recFirstPart) < 0 THEN
            return false;
          END IF;
--           SELECT array_agg(property_value/*_unified*/)
--           INTO recProperties
--           FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_part_id;
--           RAISE NOTICE '+++ Properties after transfert for new part: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_part_id;
--           RAISE NOTICE '+++ Insurances after transfert for new part: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = new_part_id;
--           RAISE NOTICE '+++ Maintenance after transfert for new part: %', recProperties;
        ELSE
--           RAISE NOTICE '+ Need of properties reCheck at least !';
--           select array_agg(property_value/*_unified*/)
--           into recProperties
--           from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--           where referenced_relation = 'specimen_parts' and record_id = part_id;
--           RAISE NOTICE '+++ Properties before creation: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Insurances before creation: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Maintenance before creation: %', recProperties;
          IF createProperties (part_id, recPartsDetails) < 0 THEN
            return false;
          END IF;
--           select array_agg(property_value/*_unified*/)
--           into recProperties
--           from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--           where referenced_relation = 'specimen_parts' and record_id = part_id;
--           RAISE NOTICE '+++ Actual properties: %', recProperties;
--           SELECT array_agg(insurance_value), array_agg(insurance_year)
--           INTO recInsurances
--           FROM insurances
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Actual Insurances: %', recInsurances;
--           SELECT array_agg(people_ref)
--           INTO recProperties
--           FROM collection_maintenance
--           WHERE referenced_relation = 'specimen_parts'
--             AND record_id = part_id;
--           RAISE NOTICE '+++ Maintenance after creation: %', recProperties;
        END IF;
      ELSE
--         RAISE NOTICE '+ New code creation for next part';
        IF createCodes (part_id, recPartsDetails.old_main_code) < 0 THEN
          return false;
        END IF;
--         select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id;
--         RAISE NOTICE '++ Actual codes: %', recActualCodes;
--         select array_agg(property_value/*_unified*/)
--         into recProperties
--         from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--         where referenced_relation = 'specimen_parts' and record_id = part_id;
--         RAISE NOTICE '+++ Properties before creation: %', recProperties;
--         SELECT array_agg(insurance_value), array_agg(insurance_year)
--         INTO recInsurances
--         FROM insurances
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Insurances before creation: %', recInsurances;
--         SELECT array_agg(people_ref)
--         INTO recProperties
--         FROM collection_maintenance
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Maintenance before transfert: %', recProperties;
        IF createProperties (part_id, recPartsDetails) < 0 THEN
          return false;
        END IF;
--         select array_agg(property_value/*_unified*/)
--         into recProperties
--         from catalogue_properties inner join properties_values on catalogue_properties.id = properties_values.property_ref
--         where referenced_relation = 'specimen_parts' and record_id = part_id;
--         RAISE NOTICE '+++ Actual properties: %', recProperties;
--         SELECT array_agg(insurance_value), array_agg(insurance_year)
--         INTO recInsurances
--         FROM insurances
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Actual Insurances: %', recInsurances;
--         SELECT array_agg(people_ref)
--         INTO recProperties
--         FROM collection_maintenance
--         WHERE referenced_relation = 'specimen_parts'
--           AND record_id = part_id;
--         RAISE NOTICE '+++ Actual Maintenance: %', recProperties;
      END IF;
    ELSE
--       RAISE NOTICE '- Same part id infos: %', recPartsDetails;
      SELECT createNewPart(part_id, recPartsDetails) INTO new_part_id;
      IF new_part_id < 0 THEN
        return false;
      END IF;
--       select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')) into recActualCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = part_id;
--       select array_agg(coalesce(code_prefix, '') || case when code_prefix is null then '' else coalesce(code_prefix_separator, ' ') end || coalesce(code, '') || case when code_suffix is null then '' else coalesce(code_suffix_separator, ' ') end || coalesce(code_suffix, '')) into recTransferedCodes from codes where code_category = 'main' and referenced_relation = 'specimen_parts' and record_id = new_part_id;
--       RAISE NOTICE '-- Actual codes: %, Transfered codes: %', recActualCodes, recTransferedCodes;
--       SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = part_id;
--       RAISE NOTICE '++Actual count min and max: % and %', spec_part_count_min, spec_part_count_max;
--       SELECT specimen_part_count_min, specimen_part_count_max INTO spec_part_count_min, spec_part_count_max FROM specimen_parts WHERE id = new_part_id;
--       RAISE NOTICE '++Transfered count min and max: % and %', spec_part_count_min, spec_part_count_max;
--       SELECT array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Properties before transfert: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Insurances before transfert: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Maintenance before transfert: %', recProperties;
      IF moveOrCreateProp(part_id, new_part_id, recPartsDetails, recFirstPart) < 0 THEN
        return false;
      END IF;
--       SELECT array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Properties after transfert: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Insurances after transfert: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = part_id;
--       RAISE NOTICE '+++ Maintenance after transfert: %', recProperties;
--       SELECT array_agg(property_value/*_unified*/)
--       INTO recProperties
--       FROM catalogue_properties inner join properties_values on catalogue_properties.id = property_ref
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Properties after transfert for new part: %', recProperties;
--       SELECT array_agg(insurance_value), array_agg(insurance_year)
--       INTO recInsurances
--       FROM insurances
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Insurances after transfert for new part: %', recInsurances;
--       SELECT array_agg(people_ref)
--       INTO recProperties
--       FROM collection_maintenance
--       WHERE referenced_relation = 'specimen_parts'
--         AND record_id = new_part_id;
--       RAISE NOTICE '+++ Maintenance after transfert for new part: %', recProperties;
    END IF;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'Error in main function: %', SQLERRM;
end;
$$;

create or replace function split_parts() returns boolean language plpgsql
AS
$$
declare
  response boolean;
  recPartsAfter RECORD;
  recPartsAfterCodes RECORD;
  recPartsAfterDiverse RECORD;
  partsAfterCodeId codes.id%TYPE := 0;
  newPartId specimen_parts.id%TYPE;
  newDiverseId catalogue_properties.id%TYPE;
begin
  select resplit_parts() INTO response;
  IF NOT response THEN
    ROLLBACK;
  END IF;
  FOR recPartsAfter IN
    SELECT id
    FROM specimen_parts
    WHERE id > (select id from partsSplitFromAndTo where "start") and id < (select id from partsSplitFromAndTo where not "start")
      AND 1 < (select count(*) from codes where referenced_relation = 'specimen_parts' and record_id = specimen_parts.id)
  LOOP
    RAISE NOTICE 'Part id is: %', recPartsAfter.id;
    FOR recPartsAfterCodes IN
      SELECT id
      FROM codes
      WHERE referenced_relation = 'specimen_parts'
        AND record_id = recPartsAfter.id
        AND code_category = 'main'
    LOOP
      IF partsAfterCodeId != recPartsAfterCodes.id THEN
        partsAfterCodeId := recPartsAfterCodes.id;
      ELSE
        INSERT INTO specimen_parts (parent_ref, path, specimen_individual_ref, specimen_part, "complete", building, "floor", "room", "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref)
        (SELECT parent_ref, path, specimen_individual_ref, specimen_part, "complete", building, "floor", "room", "row", shelf, "container", sub_container, container_type, sub_container_type, container_storage, sub_container_storage, surnumerary, specimen_status, specimen_part_count_min, specimen_part_count_max, institution_ref FROM specimen_parts WHERE id = recPartsAfter.id)
        RETURNING id INTO newPartId;
        UPDATE codes
        SET record_id = newPartId
        WHERE id = recPartsAfterCodes.id;
        INSERT INTO codes (referenced_relation, record_id, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask)
        (SELECT 'specimen_parts', newPartId, code_category, code_prefix, code_prefix_separator, code, code_suffix, code_suffix_separator, code_date, code_date_mask FROM codes WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id AND code_category != 'main');
        INSERT INTO comments (referenced_relation, record_id, notion_concerned, "comment")
        (SELECT 'specimen_parts', newPartId, notion_concerned, "comment" FROM comments WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id);
        INSERT INTO insurances (referenced_relation, record_id, insurance_value, insurance_currency, insurance_year, insurer_ref)
        (SELECT 'specimen_parts', newPartId, insurance_value, insurance_currency, insurance_year, insurer_ref FROM insurances WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id);
        INSERT INTO collection_maintenance (referenced_relation, record_id, people_ref, "category", action_observation, description, modification_date_time, modification_date_mask)
        (SELECT 'specimen_parts', newPartId, people_ref, "category", action_observation, description, modification_date_time, modification_date_mask FROM collection_maintenance WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id);
        FOR recPartsAfterDiverse IN
        SELECT id FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id
        LOOP
          INSERT INTO catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, date_from_mask, date_from, date_to_mask, date_to, property_unit, property_accuracy_unit, property_method, property_tool)
          (SELECT 'specimen_parts', newPartId, property_type, property_sub_type, property_qualifier, date_from_mask, date_from, date_to_mask, date_to, property_unit, property_accuracy_unit, property_method, property_tool FROM catalogue_properties WHERE referenced_relation = 'specimen_parts' AND record_id = recPartsAfter.id)
          RETURNING id INTO newDiverseId;
          INSERT INTO properties_values (property_ref, property_value, property_accuracy)
          (SELECT newDiverseId, property_value, property_accuracy FROM properties_values WHERE property_ref = recPartsAfterDiverse.id);
        END LOOP;
      END IF;
    END LOOP;
  END LOOP;
  return response;
exception
  when others then
    RAISE WARNING 'Error in split_parts: %', SQLERRM;
    rollback;
end;
$$;

SELECT split_parts();

DROP TYPE IF EXISTS recPartsDetail CASCADE;

ALTER TABLE specimen_parts ENABLE TRIGGER trg_cpy_specimensmaincode_specimenpartcode;

rollback;