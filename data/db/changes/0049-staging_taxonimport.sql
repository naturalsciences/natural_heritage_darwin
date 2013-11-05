begin;
set search_path=darwin2,public;

alter table staging add column create_taxon boolean not null default false;


CREATE OR REPLACE FUNCTION fct_imp_create_catalogues_and_parents(line staging, catalogue_table text, prefix text) RETURNS boolean
AS $$
DECLARE
  result_nbr integer :=0;
  row_record record;
  lvl_name varchar;
  lvl_value varchar;
  lvl_id integer;

  old_parent_id integer;
  parent_id integer;
  rec_parents hstore;
  line_store hstore;
  field_name1 text;
  field_name2 text;

  tmp text;
BEGIN
  line_store := hstore(line);
  field_name1 := prefix || '_parents';
  rec_parents := line_store->field_name1;

  IF line.create_taxon AND rec_parents is not null AND rec_parents != ''::hstore  THEN
    BEGIN
      field_name2 := prefix || '_name';
      field_name1 := prefix || '_level_name';
      rec_parents = rec_parents || hstore(line_store->field_name1, line_store->field_name2);

      FOR row_record in SELECT s.key as lvl_name, s.value as lvl_value, l.id as lvl_id
        FROM each(rec_parents) as s LEFT JOIN catalogue_levels l on s.key = l.level_sys_name
        ORDER BY l.level_order ASC
      LOOP
        old_parent_id := parent_id;
        EXECUTE 'SELECT count(*), min(t.id) as id from ' || quote_ident(catalogue_table) || ' t
          INNER JOIN catalogue_levels c on t.level_ref = c.id
          WHERE level_sys_name = ' || quote_literal(row_record.lvl_name) || ' AND
            name_indexed like fullToIndex( ' || quote_literal(row_record.lvl_value) || '  ) || ''%'' '
          INTO result_nbr, parent_id;

        IF result_nbr = 0 THEN
          IF old_parent_id IS NULL THEN
            RAISE EXCEPTION 'Unable to create taxon with no common parents';
          END IF;
          EXECUTE 'INSERT INTO ' || quote_ident(catalogue_table) || '  (name, level_ref, parent_ref) VALUES
            (' || quote_literal(row_record.lvl_value) || ', ' ||
            quote_literal(row_record.lvl_id) ||', '|| quote_literal(old_parent_id) ||') returning ID' into parent_id ;

        END IF;
      END LOOP;

    EXCEPTION WHEN OTHERS THEN
      UPDATE staging set create_taxon = false where id = line.id;
      RETURN TRUE;
    END;
  END IF;
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

  IF line.chrono_name IS NOT NULL AND line.chrono_name is not null AND line.chrono_ref is null THEN
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



commit;