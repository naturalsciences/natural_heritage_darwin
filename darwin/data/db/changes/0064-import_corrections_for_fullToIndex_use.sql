begin;
set search_path=darwin2,public;

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
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed = fullToIndex( $2 ) GROUP BY id ;'
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
      EXECUTE 'select count(*),id from '|| quote_ident(referenced_relation)||' where level_ref = $1 AND name_indexed = fullToIndex( $2 ) 
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


CREATE OR REPLACE FUNCTION fct_imp_checker_catalogue(line staging, catalogue_table text, prefix text)  RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  ref_record RECORD;
  rec_id integer := null;
  line_store hstore;
  field_name text;
  field_level_name text;
  test text;
  ref refcursor;
BEGIN
    line_store := hstore(line);
    field_name := prefix || '_name';
    field_name := line_store->field_name;
    field_level_name := prefix || '_level_name';
    field_level_name := coalesce(line_store->field_level_name,'');

    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name = ' || quote_literal( field_name) || ' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;

    IF result_nbr = 1 THEN -- It's Ok!

      PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
      RETURN true;
    END IF;

    IF result_nbr >= 2 THEN
      UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
      RETURN true;
    END IF;

    CLOSE ref;

  /*** Then CHECK fuzzy name ***/

  result_nbr := 0;
  IF catalogue_table = 'mineralogy' THEN
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed like fullToIndex(' || quote_literal( field_name) || ') || ''%'' AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  ELSE
    OPEN ref FOR EXECUTE 'SELECT * FROM ' || catalogue_table || ' t
    INNER JOIN catalogue_levels c on t.level_ref = c.id
    WHERE name_indexed = fullToIndex(' || quote_literal( field_name) || ') AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
    LIMIT 2';
    LOOP
      FETCH ref INTO ref_record;
      IF  NOT FOUND THEN
        EXIT;  -- exit loop
      END IF;

      rec_id := ref_record.id;
      result_nbr := result_nbr +1;
    END LOOP;
  END IF ;

  IF result_nbr = 1 THEN -- It's Ok!
    PERFORM fct_imp_checker_catalogues_parents(line,rec_id, catalogue_table, prefix);
    RETURN true;
  END IF;

  IF result_nbr >= 2 THEN
    UPDATE staging SET status = (status || (prefix => 'too_much')) where id= line.id;
    RETURN true;
  END IF;

  IF result_nbr = 0 THEN
    UPDATE staging SET status = (status || (prefix => 'not_found')) where id=line.id;
    RETURN true;
  END IF;

  CLOSE ref;
  RETURN true;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION get_import_row() RETURNS integer AS $$

UPDATE imports SET state = 'aloaded' FROM (
  SELECT * FROM (
    SELECT  * FROM imports i1 WHERE i1.state = 'to_be_loaded' ORDER BY i1.created_at asc, id asc OFFSET 0 --thats important
  ) i2
  WHERE pg_try_advisory_lock('imports'::regclass::integer, i2.id)
  LIMIT 1
) i3
WHERE imports.id = i3.id RETURNING i3.id;
$$
LANGUAGE sql SECURITY DEFINER;


commit;
