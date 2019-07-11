set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_importer_catalogue(req_import_ref integer,referenced_relation text,exclude_invalid_entries boolean default false) RETURNS BOOLEAN
LANGUAGE plpgsql
AS
  $$
  DECLARE
    staging_catalogue_line staging_catalogue;
    where_clause_complement_1 text := ' ';
    where_clause_complement_2 text := ' ';
    where_clause_complement_3 text := ' ';
    where_clause_complement_3_bis text := ' ';
    where_clause_complement_4 text := ' ';
    where_clause_complement_5 text := ' ';
    where_clause_exclude_invalid text := ' ';
    recCatalogue RECORD;
    parent_path template_classifications.path%TYPE;
    parentRef staging_catalogue.parent_ref%TYPE;
    parent_level catalogue_levels.id%TYPE;
    catalogueRef staging_catalogue.catalogue_ref%TYPE;
    levelRef staging_catalogue.level_ref%TYPE;
    error_msg TEXT := '';
    children_move_forward BOOLEAN := FALSE;
    level_naming TEXT;
    tempSQL TEXT;
  BEGIN
    -- Browse all staging_catalogue lines
    FOR staging_catalogue_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY level_ref, fullToIndex(name)
    LOOP
      IF trim(touniquestr(staging_catalogue_line.name)) = '' THEN
        RAISE EXCEPTION E'Case 0, Could not import this file, % is not a valid name.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
      END IF;
      SELECT parent_ref, catalogue_ref, level_ref INTO parentRef, catalogueRef, levelRef FROM staging_catalogue WHERE id = staging_catalogue_line.id;
      IF catalogueRef IS NULL THEN
        -- Check if we're at a top taxonomic entry in the template/staging_catalogue line
        IF parentRef IS NULL THEN
          -- If top entry, we have not parent defined and we therefore have no other filtering criteria
          where_clause_complement_1 := ' ';
          where_clause_complement_2 := ' ';
          where_clause_complement_3 := ' ';
          where_clause_complement_3_bis := ' ';
        ELSE
          -- If a child entry, we've got to use the informations from the already matched or created parent
          where_clause_complement_1 := '  AND tax.parent_ref = ' || parentRef || ' ';
          where_clause_complement_2 := '  AND tax.parent_ref != ' || parentRef || ' ';
          -- Select the path from parent catalogue unit
          EXECUTE 'SELECT path, level_ref FROM ' || quote_ident(referenced_relation) || ' WHERE id = $1'
          INTO parent_path, parent_level
          USING parentRef;
          where_clause_complement_3 := '  AND position (' || quote_literal(parent_path) || ' IN tax.path) = 1 ';
          where_clause_complement_3_bis := '  AND (select t2.level_ref from ' || quote_ident(referenced_relation) || ' as t2 where t2.id = tax.parent_ref) > ' || parent_level || ' ';
        END IF;
        where_clause_complement_4 := '  AND left(substring(tax.name from length(trim(' ||
                                     quote_literal(staging_catalogue_line.name) || '))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        where_clause_complement_5 := '  AND left(substring(' || quote_literal(staging_catalogue_line.name) ||
                                     ' from length(trim(tax.name))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        -- Set the invalid where clause if asked
        IF exclude_invalid_entries = TRUE THEN
          where_clause_exclude_invalid := '  AND tax.status != ' || quote_literal('invalid') || ' ';
        END IF;
        -- Check a perfect match entry
        -- Take care here, a limit 1 has been set, we only kept the EXIT in case the limit would be accidently removed
        FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                    'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                    'WHERE tax.level_ref = $1 ' ||
                                    '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                    where_clause_exclude_invalid ||
                                    where_clause_complement_1 ||
                                    'LIMIT 1;'
        USING staging_catalogue_line.level_ref, staging_catalogue_line.name
        LOOP
          -- If more than one entry found, we set an error...
          IF recCatalogue.total_count > 1 THEN
            RAISE EXCEPTION E'Case 1, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
          END IF;
          EXIT;
        END LOOP;
        -- No perfect match occured with the same parent (if it applies - doesn't apply for top taxonomic entry in template)
        IF NOT FOUND THEN
          -- For this step, as it depends upon the existence of a parent, we test well we are on that case
          -- It concerns a perfect match with parents differents but with a path common
          -- That means, if only one entry exists, that they are the same but with a more detailed hierarchy in the
          -- already existing entry
          IF parentRef IS NOT NULL THEN
            FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                        'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                        'WHERE tax.level_ref = $1 ' ||
                                        '  AND tax.name_indexed = fullToIndex( $2 ) ' ||
                                        where_clause_exclude_invalid ||
                                        where_clause_complement_2 ||
                                        where_clause_complement_3 ||
                                        where_clause_complement_3_bis ||
                                        'LIMIT 1;'
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If for this kind of perfect match with different parent but kind of same path start, we get multiple
              -- possibilities, then fail
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 2, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- If it gave no result, we've got to move forward and try the next option
            IF NOT FOUND THEN
              children_move_forward := TRUE;
            END IF;
          END IF;
          IF parentRef IS NULL OR children_move_forward = TRUE THEN
            -- This next option try a fuzzy match, with, if it's a child entry in the template, a verification that
            -- the parent specified in the template and the path of the potential corresponding entry in catalogue
            -- have a common path...
            tempSQL := 'SELECT COUNT(id) OVER () as total_count, * ' ||
                       'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                       'WHERE tax.level_ref = $1 ' ||
                       '  AND tax.name_indexed LIKE fullToIndex( $2 ) || ' || quote_literal('%') ||
                       where_clause_exclude_invalid ||
                       where_clause_complement_3 ||
                       where_clause_complement_4;
            IF parentRef IS NOT NULL THEN
              tempSQL := tempSQL || where_clause_complement_1;
            END IF;
            tempSQL := tempSQL || 'LIMIT 1;';
            FOR recCatalogue IN EXECUTE tempSQL
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If we're on the case of a top entry in the template, we cannot afford the problem of multiple entries
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION E'Case 3, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
              END IF;
              EXIT;
            END LOOP;
            -- Last chance is to try to find if the entry in DaRWIN shouldn't be completed
            -- This entry should be "alone" of its kind - check the NOT EXIST clause
            IF NOT FOUND THEN
              FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                          'FROM ' || quote_ident(referenced_relation) || ' as tax ' ||
                                          'WHERE tax.level_ref = $1 ' ||
                                          '  AND position(tax.name_indexed IN fullToIndex( $2 )) = 1 ' ||
                                          where_clause_exclude_invalid ||
                                          '  AND NOT EXISTS (SELECT 1 ' ||
                                          '                  FROM ' || quote_ident(referenced_relation) || ' as stax ' ||
                                          '                  WHERE stax.id != tax.id ' ||
                                          '                  AND stax.level_ref = tax.level_ref ' ||
                                          '                  AND stax.path = tax.path ' ||
                                          '                  AND stax.name_indexed LIKE tax.name_indexed || ' || quote_literal('%') ||
                                          '                  LIMIT 1 ' ||
                                          '                 ) ' ||
                                          where_clause_complement_3 ||
                                          where_clause_complement_5 ||
                                          'LIMIT 1;'
              USING staging_catalogue_line.level_ref, staging_catalogue_line.name
              LOOP
                IF recCatalogue.total_count > 1 THEN
                  RAISE EXCEPTION E'Case 4, Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: %', staging_catalogue_line.name, staging_catalogue_line.id;
                ELSE
                  -- If only one entry is found, we can replace the name of this entry
                  EXECUTE 'UPDATE ' || quote_ident(referenced_relation) || ' ' ||
                          'SET name = ' || quote_literal(staging_catalogue_line.name) || ' ' ||
                          'WHERE id = ' || recCatalogue.id || ';';
                END IF;
                EXIT;
              END LOOP;
              IF NOT FOUND THEN
                IF parentRef IS NOT NULL THEN
                  EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) ' ||
                          'VALUES(DEFAULT,$1,$2,$3) ' ||
                          'RETURNING *;'
                  INTO recCatalogue
                  USING staging_catalogue_line.name,staging_catalogue_line.level_ref,parentRef;
                -- tell to update the staging line to set the catalogue_ref with the id found
                ELSE
                  SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
                  RAISE EXCEPTION 'Could not import this file, % (level %) does not exist in DaRWIN and cannot be attached, correct your file or create this % manually', staging_catalogue_line.name,  level_naming, quote_ident(referenced_relation);
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        -- update the staging line to set the catalogue_ref with the id found
        -- update the staging children lines
        WITH staging_catalogue_updated(updated_id/*, catalogue_ref_updated*/) AS (
          UPDATE staging_catalogue as sc
          SET catalogue_ref = recCatalogue.id
          WHERE sc.import_ref = staging_catalogue_line.import_ref
                AND sc.name = staging_catalogue_line.name
                AND sc.level_ref = staging_catalogue_line.level_ref
          RETURNING id
        )
        UPDATE staging_catalogue as msc
        SET parent_ref = recCatalogue.id,
          parent_updated = TRUE
        WHERE msc.import_ref = staging_catalogue_line.import_ref
              AND msc.parent_ref IN (
          SELECT updated_id FROM staging_catalogue_updated
        )
              AND parent_updated = FALSE;
      END IF;
      children_move_forward := FALSE;
    END LOOP;
    RETURN TRUE;
    EXCEPTION WHEN OTHERS THEN
    IF SQLERRM = 'This record does not follow the level hierarchy' THEN
      SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
      RAISE EXCEPTION E'Could not import this file, % (level %) does not follow the accepted level hierarchy in DaRWIN an cannot be attached nor created.\nPlease correct your file.\nStaging Catalogue Line: %', staging_catalogue_line.name,  level_naming, staging_catalogue_line.id;
    ELSE
      RAISE EXCEPTION '%', SQLERRM;
    END IF;
  END;
  $$;


COMMIT;
