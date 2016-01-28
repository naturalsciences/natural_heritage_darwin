set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_importer_catalogue(req_import_ref integer,referenced_relation text) RETURNS BOOLEAN
  LANGUAGE plpgsql
  AS
  $$
    DECLARE
      staging_catalogue_line staging_catalogue;
      where_clause_complement_1 text := ' ';
      where_clause_complement_2 text := ' ';
      where_clause_complement_3 text := ' ';
      where_clause_complement_4 text := ' ';
      where_clause_complement_5 text := ' ';
      recCatalogue RECORD;
      recParent RECORD;
      error_msg TEXT := '';
      children_move_forward BOOLEAN := FALSE;
      insert_from_template BOOLEAN := FALSE;
      level_naming TEXT;
    BEGIN
      -- Browse all staging_catalogue lines
      FOR staging_catalogue_line IN SELECT * from staging_catalogue WHERE import_ref = req_import_ref ORDER BY id
      LOOP
        -- Check if we're at a top taxonomic entry in the template/staging_catalogue line
        IF staging_catalogue_line.parent_ref IS NULL THEN
          -- If top entry, we have not parent defined and we therefore have no other filtering criteria
          where_clause_complement_1 := ' ';
          where_clause_complement_2 := ' ';
          where_clause_complement_3 := ' ';
        ELSE
          -- If a child entry, we've got to use the informations from the already matched or created parent
          where_clause_complement_1 := '  AND parent_ref = ' || recParent.id || ' ';
          where_clause_complement_2 := '  AND parent_ref != ' || recParent.id || ' ';
          where_clause_complement_3 := '  AND position (' || quote_literal(recParent.path) || ' IN path) = 1 ';
        END IF;
        where_clause_complement_4 := '  AND left(substring(name from length(trim(' ||
                                     quote_literal(staging_catalogue_line.name) || '))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        where_clause_complement_5 := '  AND left(substring(' || quote_literal(staging_catalogue_line.name) ||
                                     ' from length(trim(name))+1),1) IN (' ||
                                     quote_literal(' ') || ', ' || quote_literal(',') || ') ';
        -- Check a perfect match entry
        -- Take care here, a limit 1 has been set, we only kept the EXIT in case the limit would be accidently removed
        FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                    'FROM ' || quote_ident(referenced_relation) || ' ' ||
                                    'WHERE level_ref = $1 ' ||
                                    '  AND name_indexed = fullToIndex( $2 ) ' ||
                                    '  AND status != ' || quote_literal('invalid') || ' ' ||
                                    where_clause_complement_1 ||
                                    'LIMIT 1;'
                            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
        LOOP
          -- If more than one entry found, we set an error...
          IF recCatalogue.total_count > 1 THEN
            RAISE EXCEPTION 'Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree', staging_catalogue_line.name;
          ELSE
            insert_from_template := TRUE;
          END IF;
          EXIT;
        END LOOP;
        -- No perfect match occured with the same parent (if it applies - doesn't apply for top taxonomic entry in template)
        IF NOT FOUND THEN
          -- For this step, as it depends upon the existence of a parent, we test well we are on that case
          -- It concerns a perfect match with parents differents but with a path common
          -- That means, if only one entry exists, that they are the same but with a more detailed hierarchy in the
          -- already existing entry
          IF staging_catalogue_line.parent_ref IS NOT NULL THEN
            FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                        'FROM ' || quote_ident(referenced_relation) || ' ' ||
                                        'WHERE level_ref = $1 ' ||
                                        '  AND name_indexed = fullToIndex( $2 ) ' ||
                                        '  AND status != ' || quote_literal('invalid') || ' ' ||
                                        where_clause_complement_2 ||
                                        where_clause_complement_3 ||
                                        'LIMIT 1;'
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If for this kind of perfect match with different parent but kind of same path start, we get multiple
              -- possibilities, then fail
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION 'Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree', staging_catalogue_line.name;
              ELSE
                insert_from_template := TRUE;
              END IF;
              EXIT;
            END LOOP;
            -- If it gave no result, we've got to move forward and try the next option
            IF NOT FOUND THEN
              children_move_forward := TRUE;
            END IF;
          END IF;
          IF staging_catalogue_line.parent_ref IS NULL OR children_move_forward = TRUE THEN
            -- This next option try a fuzzy match, with, if it's a child entry in the template, a verification that
            -- the parent specified in the template and the path of the potential corresponding entry in catalogue
            -- have a common path...
            FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                        'FROM ' || quote_ident(referenced_relation) || ' ' ||
                                        'WHERE level_ref = $1 ' ||
                                        '  AND name_indexed LIKE fullToIndex( $2 ) || ' || quote_literal('%') ||
                                        '  AND status != ' || quote_literal('invalid') || ' ' ||
                                        where_clause_complement_3 ||
                                        where_clause_complement_4 ||
                                        'LIMIT 1;'
            USING staging_catalogue_line.level_ref, staging_catalogue_line.name
            LOOP
              -- If we're on the case of a top entry in the template, we cannot afford the problem of multiple entries
              IF recCatalogue.total_count > 1 THEN
                RAISE EXCEPTION 'Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree', staging_catalogue_line.name;
              ELSE
                insert_from_template := TRUE;
              END IF;
              EXIT;
            END LOOP;
            -- Last chance is to try to find if the entry in DaRWIN shouldn't be completed
            IF NOT FOUND THEN
              FOR recCatalogue IN EXECUTE 'SELECT COUNT(id) OVER () as total_count, * ' ||
                                          'FROM ' || quote_ident(referenced_relation) || ' ' ||
                                          'WHERE level_ref = $1 ' ||
                                          '  AND position(name_indexed IN fullToIndex( $2 )) = 1 ' ||
                                          '  AND status != ' || quote_literal('invalid') || ' ' ||
                                          where_clause_complement_3 ||
                                          where_clause_complement_5 ||
                                          'LIMIT 1;'
              USING staging_catalogue_line.level_ref, staging_catalogue_line.name
              LOOP
                IF recCatalogue.total_count > 1 THEN
                  RAISE EXCEPTION 'Could not import this file, % exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree', staging_catalogue_line.name;
                ELSE
                  -- If only one entry is found, we can replace the name of this entry
                  EXECUTE 'UPDATE ' || quote_ident(referenced_relation) || ' ' ||
                          'SET name = ' || quote_literal(staging_catalogue_line.name) || ' ' ||
                          'WHERE id = ' || recCatalogue.id || ';';
                  insert_from_template := TRUE;
                END IF;
                EXIT;
              END LOOP;
              IF NOT FOUND THEN
                IF staging_catalogue_line.parent_ref IS NOT NULL THEN
                  EXECUTE 'INSERT INTO ' || quote_ident(referenced_relation) || '(id,name,level_ref,parent_ref) ' ||
                          'VALUES(DEFAULT,$1,$2,$3) ' ||
                          'RETURNING *;'
                  INTO recCatalogue
                  USING staging_catalogue_line.name,staging_catalogue_line.level_ref,recParent.id;
                  -- tell to update the staging line to set the catalogue_ref with the id found
                  insert_from_template := TRUE;
                ELSE
                  SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
                  RAISE EXCEPTION 'Could not import this file, % (level %) does not exist in DaRWIN and cannot be attached, correct your file or create this % manually', staging_catalogue_line.name,  level_naming, quote_ident(referenced_relation);
                END IF;
              END IF;
            END IF;
          END IF;
        END IF;
        IF insert_from_template = TRUE THEN
          -- update the staging line to set the catalogue_ref with the id found
          UPDATE staging_catalogue
          SET catalogue_ref = recCatalogue.id
          WHERE id = staging_catalogue_line.id ;
        END IF;
        recParent := recCatalogue;
        insert_from_template := FALSE;
        children_move_forward := FALSE;
      END LOOP;
      RETURN TRUE;
    EXCEPTION WHEN OTHERS THEN
      IF SQLERRM = 'This record does not follow the level hierarchy' THEN
        SELECT level_name INTO level_naming FROM catalogue_levels WHERE id = staging_catalogue_line.level_ref;
        RAISE EXCEPTION 'Could not import this file, % (level %) does not follow the accepted level hierarchy in DaRWIN an cannot be attached nor created. Please correct your file.', staging_catalogue_line.name,  level_naming;
      ELSE
        RAISE EXCEPTION '%', SQLERRM;
      END IF;
    END;
  $$;

COMMIT;
