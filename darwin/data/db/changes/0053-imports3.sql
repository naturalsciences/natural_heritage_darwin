begin;
set search_path=darwin2,public;

CREATE TRIGGER trg_cpy_fullToIndex_specimens BEFORE INSERT OR UPDATE
  ON specimens FOR EACH ROW
  EXECUTE PROCEDURE fct_cpy_fullToIndex();

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
    WHERE name_indexed like fullToIndex(' || quote_literal( field_name) || ') AND  level_sys_name = CASE WHEN ' || quote_literal(field_level_name) || ' = '''' THEN level_sys_name ELSE ' || quote_literal(field_level_name) || ' END
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

CREATE OR REPLACE FUNCTION fct_imp_checker_manager(line staging)  RETURNS boolean
AS $$
BEGIN

  IF line.taxon_name IS NOT NULL AND line.taxon_name is distinct from '' AND line.taxon_ref is null THEN
    PERFORM fct_imp_create_catalogues_and_parents(line, 'taxonomy','taxon');
    PERFORM fct_imp_checker_catalogue(line,'taxonomy','taxon');
  END IF;

  IF line.chrono_name IS NOT NULL AND line.chrono_name is distinct from '' AND line.chrono_ref is null THEN
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

COMMIT ;
