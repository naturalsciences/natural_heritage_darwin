drop table if exists zzz_col_stats;
create temporary table if not exists zzz_col_stats (table_name varchar, 
                                                    column_name varchar, 
                                                    collection varchar, 
                                                    row_count integer, 
                                                    general_description varchar, 
                                                    default_values_count integer default 0, 
                                                    default_values_percentage float default 0, 
                                                    null_values_count integer default 0, 
                                                    null_values_percentage float default 0, 
                                                    empty_values_count integer default 0, 
                                                    empty_values_percentage float default 0, 
                                                    other_values_count integer default 0, 
                                                    other_values_percentage float default 0
                                                   );
create temporary table if not exists zzz_top_collections (id integer, name varchar);

create or replace function stat_columns_usage() returns boolean language plpgsql as $$
declare
  table_columns RECORD;
  previousTableName varchar := '';
  tableName varchar;
  rowCount numeric;
  rowCountCopy numeric;
  rowDefaultCount numeric;
  rowNullCount numeric;
  rowEmptyCount numeric;
  rowOthersCount numeric;
begin
  TRUNCATE TABLE zzz_col_stats;
  TRUNCATE TABLE zzz_top_collections;
  INSERT INTO zzz_top_collections (select id, name from collections where (Array_upper(String_to_array(path,'/'),1) - 1) = 1 order by id);
  FOR table_columns IN (select table_name, column_name, column_default, is_nullable 
                        from information_schema.columns
                        where table_schema = 'darwin2' 
                          and table_name not in ('darwin_flat', 'flat_dict', 'migrated_parts', 'multimedia_todelete', 'my_saved_searches', 'my_widgets', 'old_multimedia', 'preferences', 'possible_upper_levels', 'catalogue_levels', 'specimens_flat', 'staging', 'staging_people', 'staging_tag_groups', 'tags', 'tag_groups', 'template_classifications', 'template_collections_users', 'template_people', 'template_people_languages', 'template_people_users_addr_common', 'template_people_users_comm_common', 'template_people_users_rel_common', 'template_table_record_ref', 'temp_mineralogy', 'users_login_infos', 'users_tracking', 'words', 'zzz_to_correct') 
                          and column_name not in ('id', 'referenced_relation', 'record_id')
                          and column_name not like '%_ts'
                          and column_name not like '%_indexed'
                          and column_name not like '%_ref'
                          and column_name not like '%_mask'
                        order by table_name, ordinal_position)
  LOOP
    IF table_columns.table_name != previousTableName THEN
      EXECUTE 'SELECT COUNT(*) from ' || table_columns.table_name INTO rowCount USING table_columns.table_name;
    END IF;
    IF COALESCE(table_columns.column_default, '') != '' THEN
      EXECUTE 'SELECT Count(*) FROM ' || table_columns.table_name || ' WHERE Trim(' || table_columns.column_name || '::varchar) = $1' INTO rowDefaultCount USING table_columns.column_default;
      EXECUTE 'SELECT Count(*) FROM ' || table_columns.table_name || ' WHERE ' || table_columns.column_name || '::varchar IS NOT NULL AND Trim(Coalesce(' || table_columns.column_name || '::varchar,' || CHR(39) || CHR(39) || ')) != ' || CHR(39) || CHR(39) || ' AND Trim(Coalesce(' || table_columns.column_name || '::varchar,' || CHR(39) || CHR(39) || ')) != $1' INTO rowOthersCount USING table_columns.column_default;
    ELSE
      rowDefaultCount := 0;
      EXECUTE 'SELECT Count(*) FROM ' || table_columns.table_name || ' WHERE ' || table_columns.column_name || '::varchar IS NOT NULL AND Trim(Coalesce(' || table_columns.column_name || '::varchar,' || CHR(39) || CHR(39) || ')) != ' || CHR(39) || CHR(39) INTO rowOthersCount USING table_columns.column_default;
    END IF;
    EXECUTE 'SELECT Count(*) FROM ' || table_columns.table_name || ' WHERE Trim(' || table_columns.column_name || '::varchar) = ' || CHR(39) || CHR(39) INTO rowEmptyCount;
    EXECUTE 'SELECT Count(*) FROM ' || table_columns.table_name || ' WHERE ' || table_columns.column_name || '::varchar IS NULL' INTO rowNullCount;
    rowCountCopy := rowCount;
    IF rowCount = 0 THEN
      rowCountCopy := 1;
    END IF;
    INSERT INTO zzz_col_stats (table_name, column_name, row_count, default_values_count, default_values_percentage, null_values_count, null_values_percentage, empty_values_count, empty_values_percentage, other_values_count, other_values_percentage)
    VALUES (table_columns.table_name, table_columns.column_name, rowCount, rowDefaultCount, Round(rowDefaultCount/rowCountCopy,2), rowNullCount, Round(rowNullCount/rowCountCopy,2), rowEmptyCount, Round(rowEmptyCount/rowCountCopy,2), rowOthersCount, Round(rowOthersCount/rowCountCopy,2));
    rowDefaultCount := 0;
    rowNullCount := 0;
    rowEmptyCount := 0;
    rowOthersCount := 0;
    previousTableName = table_columns.table_name;
  END LOOP;
  RETURN TRUE;
end;
$$;

select * from stat_columns_usage();

SELECT * FROM zzz_col_stats order by other_values_percentage desc, row_count desc, table_name;
