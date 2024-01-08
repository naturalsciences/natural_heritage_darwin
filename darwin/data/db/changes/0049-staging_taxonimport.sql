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

      IF line_store->field_name2 != '' THEN
        rec_parents = rec_parents || hstore(line_store->field_name1, line_store->field_name2);
      END IF;

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


CREATE OR REPLACE FUNCTION fct_upd_staging_fields() RETURNS TRIGGER
AS $$
BEGIN
  IF get_setting('darwin.upd_imp_ref') is null OR  get_setting('darwin.upd_imp_ref') = '' THEN
    PERFORM set_config('darwin.upd_imp_ref', 'ok', true);
    IF OLD.taxon_ref IS DISTINCT FROM NEW.taxon_ref AND  NEW.taxon_ref is not null THEN
        SELECT t.id ,t.name, t.level_ref , cl.level_name, t.status, t.extinct
        INTO NEW.taxon_ref,NEW.taxon_name, NEW.taxon_level_ref, NEW.taxon_level_name, NEW.taxon_status, NEW.taxon_extinct
        FROM taxonomy t, catalogue_levels cl
        WHERE cl.id=t.level_ref AND t.id = NEW.taxon_ref;

        UPDATE staging set taxon_ref=NEW.taxon_ref, taxon_name = new.taxon_name, taxon_level_ref=new.taxon_level_ref,
          taxon_level_name=new.taxon_level_name, taxon_status=new.taxon_status, taxon_extinct=new.taxon_extinct,
          status = delete(status,'taxon')

        WHERE
          taxon_name  IS NOT DISTINCT FROM  old.taxon_name AND  taxon_level_ref IS NOT DISTINCT FROM old.taxon_level_ref AND
          taxon_level_name IS NOT DISTINCT FROM old.taxon_level_name AND  taxon_status IS NOT DISTINCT FROM old.taxon_status
          AND  taxon_extinct IS NOT DISTINCT FROM old.taxon_extinct
          AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'taxon');
    END IF;

    IF OLD.chrono_ref IS DISTINCT FROM NEW.chrono_ref  AND  NEW.chrono_ref is not null THEN
      SELECT c.id, c.name, c.level_ref, cl.level_name, c.status, c.local_naming, c.color, c.upper_bound, c.lower_bound
        INTO NEW.chrono_ref, NEW.chrono_name, NEW.chrono_level_ref, NEW.chrono_level_name, NEW.chrono_status, NEW.chrono_local, NEW.chrono_color, NEW.chrono_upper_bound, NEW.chrono_lower_bound
        FROM chronostratigraphy c, catalogue_levels cl
        WHERE cl.id=c.level_ref AND c.id = NEW.chrono_ref ;

        UPDATE staging set chrono_ref=NEW.chrono_ref, chrono_name = NEW.chrono_name, chrono_level_ref=NEW.chrono_level_ref, chrono_level_name=NEW.chrono_level_name, chrono_status=NEW.chrono_status,
        chrono_local=NEW.chrono_local, chrono_color=NEW.chrono_color, chrono_upper_bound=NEW.chrono_upper_bound, chrono_lower_bound=NEW.chrono_lower_bound,
        status = delete(status,'chrono')

        WHERE
        chrono_name  IS NOT DISTINCT FROM  OLD.chrono_name AND  chrono_level_ref IS NOT DISTINCT FROM OLD.chrono_level_ref AND
        chrono_level_name IS NOT DISTINCT FROM OLD.chrono_level_name AND  chrono_status IS NOT DISTINCT FROM OLD.chrono_status AND
        chrono_local IS NOT DISTINCT FROM OLD.chrono_local AND  chrono_color IS NOT DISTINCT FROM OLD.chrono_color AND
        chrono_upper_bound IS NOT DISTINCT FROM OLD.chrono_upper_bound AND  chrono_lower_bound IS NOT DISTINCT FROM OLD.chrono_lower_bound
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'chrono');

    END IF;

    IF OLD.litho_ref IS DISTINCT FROM NEW.litho_ref  AND  NEW.litho_ref is not null  THEN
      SELECT l.id,l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.litho_ref, NEW.litho_name, NEW.litho_level_ref, NEW.litho_level_name, NEW.litho_status, NEW.litho_local, NEW.litho_color
      FROM lithostratigraphy l, catalogue_levels cl
      WHERE cl.id=l.level_ref AND l.id = NEW.litho_ref ;

      UPDATE staging set
        litho_ref=NEW.litho_ref, litho_name=NEW.litho_name, litho_level_ref=NEW.litho_level_ref, litho_level_name=NEW.litho_level_name,
        litho_status=NEW.litho_status, litho_local=NEW.litho_local, litho_color=NEW.litho_color,
        status = delete(status,'litho')

      WHERE
        litho_name IS NOT DISTINCT FROM  OLD.litho_name AND litho_level_ref IS NOT DISTINCT FROM  OLD.litho_level_ref AND
        litho_level_name IS NOT DISTINCT FROM  OLD.litho_level_name AND
        litho_status IS NOT DISTINCT FROM  OLD.litho_status AND litho_local IS NOT DISTINCT FROM  OLD.litho_local AND litho_color IS NOT DISTINCT FROM OLD.litho_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'litho');

    END IF;


    IF OLD.lithology_ref IS DISTINCT FROM NEW.lithology_ref  AND  NEW.lithology_ref is not null THEN
      SELECT l.id, l.name, l.level_ref, cl.level_name, l.status, l.local_naming, l.color
      INTO NEW.lithology_ref, NEW.lithology_name, NEW.lithology_level_ref, NEW.lithology_level_name, NEW.lithology_status, NEW.lithology_local, NEW.lithology_color
      FROM lithology l, catalogue_levels cl
      WHERE cl.id=l.level_ref AND l.id = NEW.lithology_ref ;

      UPDATE staging set
        lithology_ref=NEW.lithology_ref, lithology_name=NEW.lithology_name, lithology_level_ref=NEW.lithology_level_ref,
        lithology_level_name=NEW.lithology_level_name, lithology_status=NEW.lithology_status, lithology_local=NEW.lithology_local,
        lithology_color=NEW.lithology_color,
        status = delete(status,'lithology')

      WHERE
        lithology_name IS NOT DISTINCT FROM OLD.lithology_name AND  lithology_level_ref IS NOT DISTINCT FROM OLD.lithology_level_ref AND
        lithology_level_name IS NOT DISTINCT FROM OLD.lithology_level_name AND  lithology_status IS NOT DISTINCT FROM OLD.lithology_status AND  lithology_local IS NOT DISTINCT FROM OLD.lithology_local AND
        lithology_color IS NOT DISTINCT FROM OLD.lithology_color
        AND import_ref = NEW.import_ref;
        NEW.status = delete(NEW.status,'lithology');

    END IF;


    IF OLD.mineral_ref IS DISTINCT FROM NEW.mineral_ref  AND  NEW.mineral_ref is not null THEN
      SELECT m.id, m.name, m.level_ref, cl.level_name, m.status, m.local_naming, m.color, m.path
      INTO NEW.mineral_ref, NEW.mineral_name, NEW.mineral_level_ref, NEW.mineral_level_name, NEW.mineral_status, NEW.mineral_local, NEW.mineral_color, NEW.mineral_path
      FROM mineralogy m, catalogue_levels cl
      WHERE cl.id=m.level_ref AND m.id = NEW.mineral_ref ;

      UPDATE staging set
        mineral_ref=NEW.mineral_ref, mineral_name=NEW.mineral_name, mineral_level_ref=NEW.mineral_level_ref,
        mineral_level_name=NEW.mineral_level_name, mineral_status=NEW.mineral_status, mineral_local=NEW.mineral_local,
        mineral_color=NEW.mineral_color, mineral_path=NEW.mineral_path,
        status = delete(status,'mineral')

      WHERE
        mineral_name IS NOT DISTINCT FROM OLD.mineral_name AND  mineral_level_ref IS NOT DISTINCT FROM OLD.mineral_level_ref AND
        mineral_level_name IS NOT DISTINCT FROM OLD.mineral_level_name AND  mineral_status IS NOT DISTINCT FROM OLD.mineral_status AND  mineral_local IS NOT DISTINCT FROM OLD.mineral_local AND
        mineral_color IS NOT DISTINCT FROM OLD.mineral_color AND  mineral_path IS NOT DISTINCT FROM OLD.mineral_path
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'mineral');

    END IF;

    IF OLD.expedition_ref IS DISTINCT FROM NEW.expedition_ref  AND  NEW.expedition_ref is not null THEN
      SELECT id, "name", expedition_from_date, expedition_to_date, expedition_from_date_mask , expedition_to_date_mask
      INTO NEW.expedition_ref, NEW.expedition_name, NEW.expedition_from_date, NEW.expedition_to_date, NEW.expedition_from_date_mask , NEW.expedition_to_date_mask
      FROM expeditions
      WHERE id = NEW.expedition_ref ;

      UPDATE staging set
        expedition_ref=NEW.expedition_ref, expedition_name=NEW.expedition_name, expedition_from_date=NEW.expedition_from_date,
        expedition_to_date=NEW.expedition_to_date, expedition_from_date_mask=NEW.expedition_from_date_mask , expedition_to_date_mask=NEW.expedition_to_date_mask
      WHERE
        expedition_name IS NOT DISTINCT FROM OLD.expedition_name AND  expedition_from_date IS NOT DISTINCT FROM OLD.expedition_from_date AND
        expedition_to_date IS NOT DISTINCT FROM OLD.expedition_to_date AND  expedition_from_date_mask IS NOT DISTINCT FROM OLD.expedition_from_date_mask  AND
        expedition_to_date_mask IS NOT DISTINCT FROM OLD.expedition_to_date_mask
        AND import_ref = NEW.import_ref;

    END IF;

    IF OLD.institution_ref IS DISTINCT FROM NEW.institution_ref  AND  NEW.institution_ref is not null THEN
      SELECT formated_name INTO NEW.institution_name FROM people WHERE id = NEW.institution_ref ;

      UPDATE staging set institution_ref = NEW.institution_ref, institution_name=NEW.institution_name,
        status = delete(status,'institution')
        WHERE
        institution_name IS NOT DISTINCT FROM OLD.institution_name
        AND import_ref = NEW.import_ref;

        NEW.status = delete(NEW.status,'institution');

    END IF;

    PERFORM set_config('darwin.upd_imp_ref', NULL, true);
  END IF;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


commit;
