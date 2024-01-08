set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_listing_taxonomy (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
                 "domain" TEXT,
                 "kingdom" TEXT,
                 "super_phylum" TEXT,
                 "phylum" TEXT,
                 "sub_phylum" TEXT,
                 "infra_phylum" TEXT,
                 "super_cohort_botany" TEXT,
                 "cohort_botany" TEXT,
                 "sub_cohort_botany" TEXT,
                 "infra_cohort_botany" TEXT,
                 "super_class" TEXT,
                 "class" TEXT,
                 "sub_class" TEXT,
                 "infra_class" TEXT,
                 "super_division" TEXT,
                 "division" TEXT,
                 "sub_division" TEXT,
                 "infra_division" TEXT,
                 "super_legion" TEXT,
                 "legion" TEXT,
                 "sub_legion" TEXT,
                 "infra_legion" TEXT,
                 "super_cohort_zoology" TEXT,
                 "cohort_zoology" TEXT,
                 "sub_cohort_zoology" TEXT,
                 "infra_cohort_zoology" TEXT,
                 "super_order" TEXT,
                 "order" TEXT,
                 "sub_order" TEXT,
                 "infra_order" TEXT,
                 "section_zoology" TEXT,
                 "sub_section_zoology" TEXT,
                 "super_family" TEXT,
                 "family" TEXT,
                 "sub_family" TEXT,
                 "infra_family" TEXT,
                 "super_tribe" TEXT,
                 "tribe" TEXT,
                 "sub_tribe" TEXT,
                 "infra_tribe" TEXT,
                 "genus" TEXT,
                 "sub_genus" TEXT,
                 "section_botany" TEXT,
                 "sub_section_botany" TEXT,
                 "serie" TEXT,
                 "sub_serie" TEXT,
                 "super_species" TEXT,
                 "species" TEXT,
                 "sub_species" TEXT,
                 "variety" TEXT,
                 "sub_variety" TEXT,
                 "form" TEXT,
                 "sub_form" TEXT,
                 "abberans" TEXT
                )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of taxon ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    taxon_id INTEGER;
    recTaxonomic_levels RECORD;
  BEGIN
    -- First, test that there's at least one taxon to search the hierarchy from
    IF array_length(taxon_ids, 1) > 0 THEN
      -- Compose the list of taxon ids as comma separated string
      where_first_list_of_ids := array_to_string(taxon_ids, ',');
      -- Loop through these taxon ids to compose the second part of the sql where clause
      FOREACH taxon_id IN ARRAY taxon_ids LOOP
        where_second_sql := 'or strpos	(tax.path, (select ssstax.path || ssstax.id
                                                    from taxonomy ssstax
                                                    where ssstax.id = ' || taxon_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where tax.id in (select sstax.id
                                          from taxonomy sstax
                                          where sstax.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((tax.path || tax.id), tax.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all taxonomic levels and for each of them, include a select clause
      FOR recTaxonomic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'taxonomy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select subtax.name
                             from taxonomy as subtax inner join (select unnest(string_to_array(substring(tax.path || tax.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as taxids
                             on subtax.id = taxids.id::integer
                             where taxids.id != ' || CHR(39) || CHR(39) || '
                               and subtax.level_ref = ' || recTaxonomic_levels.id || '
                            )::text as "' || recTaxonomic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from taxonomy as tax left join specimens on tax.id = specimens.taxon_ref ';
      order_by_sql_part := 'order by (tax.path || tax.id), tax.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_zoology (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "domain" TEXT,
  "kingdom" TEXT,
  "super_phylum" TEXT,
  "phylum" TEXT,
  "sub_phylum" TEXT,
  "infra_phylum" TEXT,
  "super_class" TEXT,
  "class" TEXT,
  "sub_class" TEXT,
  "infra_class" TEXT,
  "super_division" TEXT,
  "division" TEXT,
  "sub_division" TEXT,
  "infra_division" TEXT,
  "super_legion" TEXT,
  "legion" TEXT,
  "sub_legion" TEXT,
  "infra_legion" TEXT,
  "super_cohort_zoology" TEXT,
  "cohort_zoology" TEXT,
  "sub_cohort_zoology" TEXT,
  "infra_cohort_zoology" TEXT,
  "super_order" TEXT,
  "order" TEXT,
  "sub_order" TEXT,
  "infra_order" TEXT,
  "section_zoology" TEXT,
  "sub_section_zoology" TEXT,
  "super_family" TEXT,
  "family" TEXT,
  "sub_family" TEXT,
  "infra_family" TEXT,
  "super_tribe" TEXT,
  "tribe" TEXT,
  "sub_tribe" TEXT,
  "infra_tribe" TEXT,
  "genus" TEXT,
  "sub_genus" TEXT,
  "serie" TEXT,
  "sub_serie" TEXT,
  "super_species" TEXT,
  "species" TEXT,
  "sub_species" TEXT,
  "variety" TEXT,
  "sub_variety" TEXT,
  "form" TEXT,
  "sub_form" TEXT,
  "abberans" TEXT
  )
AS $$
  SELECT "referenced_by_at_least_one_specimen","domain","kingdom","super_phylum","phylum","sub_phylum","infra_phylum","super_class","class","sub_class","infra_class","super_division","division","sub_division","infra_division","super_legion","legion","sub_legion","infra_legion","super_cohort_zoology","cohort_zoology","sub_cohort_zoology","infra_cohort_zoology","super_order","order","sub_order","infra_order","section_zoology","sub_section_zoology","super_family","family","sub_family","infra_family","super_tribe","tribe","sub_tribe","infra_tribe","genus","sub_genus","serie","sub_serie","super_species","species","sub_species","variety","sub_variety","form","sub_form","abberans" from fct_listing_taxonomy($1, variadic $2);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_listing_botany (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "domain" TEXT,
  "kingdom" TEXT,
  "super_phylum" TEXT,
  "phylum" TEXT,
  "sub_phylum" TEXT,
  "infra_phylum" TEXT,
  "super_cohort_botany" TEXT,
  "cohort_botany" TEXT,
  "sub_cohort_botany" TEXT,
  "infra_cohort_botany" TEXT,
  "super_class" TEXT,
  "class" TEXT,
  "sub_class" TEXT,
  "infra_class" TEXT,
  "super_division" TEXT,
  "division" TEXT,
  "sub_division" TEXT,
  "infra_division" TEXT,
  "super_legion" TEXT,
  "legion" TEXT,
  "sub_legion" TEXT,
  "infra_legion" TEXT,
  "super_order" TEXT,
  "order" TEXT,
  "sub_order" TEXT,
  "infra_order" TEXT,
  "super_family" TEXT,
  "family" TEXT,
  "sub_family" TEXT,
  "infra_family" TEXT,
  "super_tribe" TEXT,
  "tribe" TEXT,
  "sub_tribe" TEXT,
  "infra_tribe" TEXT,
  "genus" TEXT,
  "sub_genus" TEXT,
  "section_botany" TEXT,
  "sub_section_botany" TEXT,
  "serie" TEXT,
  "sub_serie" TEXT,
  "super_species" TEXT,
  "species" TEXT,
  "sub_species" TEXT,
  "variety" TEXT,
  "sub_variety" TEXT,
  "form" TEXT,
  "sub_form" TEXT,
  "abberans" TEXT
  )
AS $$
  SELECT "referenced_by_at_least_one_specimen","domain","kingdom","super_phylum","phylum","sub_phylum","infra_phylum","super_cohort_botany","cohort_botany","sub_cohort_botany","infra_cohort_botany","super_class","class","sub_class","infra_class","super_division","division","sub_division","infra_division","super_legion","legion","sub_legion","infra_legion","super_order","order","sub_order","infra_order","super_family","family","sub_family","infra_family","super_tribe","tribe","sub_tribe","infra_tribe","genus","sub_genus","section_botany","sub_section_botany","serie","sub_serie","super_species","species","sub_species","variety","sub_variety","form","sub_form","abberans" from fct_listing_taxonomy($1, variadic $2);
$$ LANGUAGE SQL;

CREATE OR REPLACE FUNCTION fct_listing_chronostratigraphy (IN nbr_records INTEGER, VARIADIC chrono_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "eon" TEXT,
  "era" TEXT,
  "sub_era" TEXT,
  "system" TEXT,
  "serie" TEXT,
  "stage" TEXT,
  "sub_stage" TEXT,
  "sub_level_1" TEXT,
  "sub_level_2" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of chrono_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    chrono_unit_id INTEGER;
    recChronostratigraphic_levels RECORD;
  BEGIN
    -- First, test that there's at least one chrono_unit to search the hierarchy from
    IF array_length(chrono_unit_ids, 1) > 0 THEN
      -- Compose the list of chrono_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(chrono_unit_ids, ',');
      -- Loop through these chrono_unit ids to compose the second part of the sql where clause
      FOREACH chrono_unit_id IN ARRAY chrono_unit_ids LOOP
        where_second_sql := 'or strpos	(chronos.path, (select ssschronos.path || ssschronos.id
                                                    from chronostratigraphy ssschronos
                                                    where ssschronos.id = ' || chrono_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where chronos.id in (select sschronos.id
                                          from chronostratigraphy sschronos
                                          where sschronos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((chronos.path || chronos.id), chronos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all chronostratigraphic levels and for each of them, include a select clause
      FOR recChronostratigraphic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'chronostratigraphy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select subchronos.name
                             from chronostratigraphy as subchronos inner join (select unnest(string_to_array(substring(chronos.path || chronos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as chronosids
                             on subchronos.id = chronosids.id::integer
                             where chronosids.id != ' || CHR(39) || CHR(39) || '
                               and subchronos.level_ref = ' || recChronostratigraphic_levels.id || '
                            )::text as "' || recChronostratigraphic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from chronostratigraphy as chronos left join specimens on chronos.id = specimens.chrono_ref ';
      order_by_sql_part := 'order by (chronos.path || chronos.id), chronos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_lithostratigraphy (IN nbr_records INTEGER, VARIADIC litho_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "supergroup" TEXT,
  "group" TEXT,
  "formation" TEXT,
  "member" TEXT,
  "layer" TEXT,
  "sub_level_1" TEXT,
  "sub_level_2" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of litho_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    litho_unit_id INTEGER;
    reclithostratigraphic_levels RECORD;
  BEGIN
    -- First, test that there's at least one litho_unit to search the hierarchy from
    IF array_length(litho_unit_ids, 1) > 0 THEN
      -- Compose the list of litho_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(litho_unit_ids, ',');
      -- Loop through these litho_unit ids to compose the second part of the sql where clause
      FOREACH litho_unit_id IN ARRAY litho_unit_ids LOOP
        where_second_sql := 'or strpos	(lithos.path, (select ssslithos.path || ssslithos.id
                                                    from lithostratigraphy ssslithos
                                                    where ssslithos.id = ' || litho_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where lithos.id in (select sslithos.id
                                          from lithostratigraphy sslithos
                                          where sslithos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((lithos.path || lithos.id), lithos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all lithostratigraphic levels and for each of them, include a select clause
      FOR reclithostratigraphic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'lithostratigraphy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select sublithos.name
                             from lithostratigraphy as sublithos inner join (select unnest(string_to_array(substring(lithos.path || lithos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as lithosids
                             on sublithos.id = lithosids.id::integer
                             where lithosids.id != ' || CHR(39) || CHR(39) || '
                               and sublithos.level_ref = ' || reclithostratigraphic_levels.id || '
                            )::text as "' || reclithostratigraphic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from lithostratigraphy as lithos left join specimens on lithos.id = specimens.litho_ref ';
      order_by_sql_part := 'order by (lithos.path || lithos.id), lithos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_mineralogy (IN nbr_records INTEGER, VARIADIC mineralo_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "unit_class" TEXT,
  "unit_sub_class" TEXT,
  "unit_series" TEXT,
  "unit_variety" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of mineralo_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    mineralo_unit_id INTEGER;
    recmineralogic_levels RECORD;
  BEGIN
    -- First, test that there's at least one mineralo_unit to search the hierarchy from
    IF array_length(mineralo_unit_ids, 1) > 0 THEN
      -- Compose the list of mineralo_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(mineralo_unit_ids, ',');
      -- Loop through these mineralo_unit ids to compose the second part of the sql where clause
      FOREACH mineralo_unit_id IN ARRAY mineralo_unit_ids LOOP
        where_second_sql := 'or strpos	(mineralos.path, (select sssmineralos.path || sssmineralos.id
                                                    from mineralogy sssmineralos
                                                    where sssmineralos.id = ' || mineralo_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where mineralos.id in (select ssmineralos.id
                                          from mineralogy ssmineralos
                                          where ssmineralos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((mineralos.path || mineralos.id), mineralos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all mineralogic levels and for each of them, include a select clause
      FOR recmineralogic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'mineralogy' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select submineralos.name
                             from mineralogy as submineralos inner join (select unnest(string_to_array(substring(mineralos.path || mineralos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as mineralosids
                             on submineralos.id = mineralosids.id::integer
                             where mineralosids.id != ' || CHR(39) || CHR(39) || '
                               and submineralos.level_ref = ' || recmineralogic_levels.id || '
                            )::text as "' || recmineralogic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from mineralogy as mineralos left join specimens on mineralos.id = specimens.mineral_ref ';
      order_by_sql_part := 'order by (mineralos.path || mineralos.id), mineralos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_listing_lithology (IN nbr_records INTEGER, VARIADIC litholo_unit_ids INTEGER[])
  RETURNS TABLE ("referenced_by_at_least_one_specimen" INTEGER,
  "unit_main_group" TEXT,
  "unit_group" TEXT,
  "unit_sub_group" TEXT,
  "unit_rock" TEXT,
  "unit_main_class" TEXT,
  "unit_class" TEXT,
  "unit_clan" TEXT,
  "unit_category" TEXT
  )
AS $$
  DECLARE
    select_sql_part TEXT; -- Variable dedicated to store the select part of sql
    from_sql_part TEXT;
    where_sql_part TEXT;  -- Variable dedicated to store the where part of sql - this part being dynamically constructed
    where_first_list_of_ids TEXT; -- Will store the list of litholo_unit ids as string with comma as separation delimiter
    where_second_sql TEXT; -- Will store the second part of the where clause dynamically constructed
    order_by_sql_part TEXT;
    limit_sql_part TEXT DEFAULT '';
    litholo_unit_id INTEGER;
    reclithologic_levels RECORD;
  BEGIN
    -- First, test that there's at least one litholo_unit to search the hierarchy from
    IF array_length(litholo_unit_ids, 1) > 0 THEN
      -- Compose the list of litholo_unit ids as comma separated string
      where_first_list_of_ids := array_to_string(litholo_unit_ids, ',');
      -- Loop through these litholo_unit ids to compose the second part of the sql where clause
      FOREACH litholo_unit_id IN ARRAY litholo_unit_ids LOOP
        where_second_sql := 'or strpos	(litholos.path, (select ssslitholos.path || ssslitholos.id
                                                    from lithology ssslitholos
                                                    where ssslitholos.id = ' || litholo_unit_id || '
                                                   )
                                        ) != 0 ';
        where_sql_part := COALESCE(where_sql_part, '') || where_second_sql;
      END LOOP;
      where_sql_part := 'where litholos.id in (select sslitholos.id
                                          from lithology sslitholos
                                          where sslitholos.id in (' || where_first_list_of_ids || ')
                                         ) ' ||  where_sql_part;
      select_sql_part := 'select distinct on ((litholos.path || litholos.id), litholos.level_ref)
                          case
                            when specimens.id is null then 0
                            else 1
                          end as "referenced_by_at_least_one_specimen", ';
      -- Browse all lithologic levels and for each of them, include a select clause
      FOR reclithologic_levels IN SELECT id, level_sys_name FROM catalogue_levels WHERE level_type = 'lithology' ORDER BY level_order, id LOOP
        select_sql_part := select_sql_part ||
                           '(select sublitholos.name
                             from lithology as sublitholos inner join (select unnest(string_to_array(substring(litholos.path || litholos.id from 2), ' || CHR(39) || CHR(47) || CHR(39) || ')) as id) as litholosids
                             on sublitholos.id = litholosids.id::integer
                             where litholosids.id != ' || CHR(39) || CHR(39) || '
                               and sublitholos.level_ref = ' || reclithologic_levels.id || '
                            )::text as "' || reclithologic_levels.level_sys_name || '" ,';
      END LOOP;
      select_sql_part := substring(select_sql_part for (length(select_sql_part) - 1));
      from_sql_part := 'from lithology as litholos left join specimens on litholos.id = specimens.lithology_ref ';
      order_by_sql_part := 'order by (litholos.path || litholos.id), litholos.level_ref ';
      -- Get a limit part only if set
      IF nbr_records IS NOT NULL AND nbr_records != 0 THEN
        limit_sql_part := 'limit ' || nbr_records;
      END IF;
    END IF;
    RETURN QUERY EXECUTE select_sql_part || from_sql_part || where_sql_part || order_by_sql_part || limit_sql_part;
  EXCEPTION WHEN OTHERS THEN
    RAISE NOTICE 'Error is %', SQLERRM;
    RETURN;
  END;
$$ LANGUAGE plpgsql;

GRANT EXECUTE ON FUNCTION fct_listing_taxonomy (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_zoology (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_botany (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_chronostratigraphy (IN nbr_records INTEGER, VARIADIC chrono_unit_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_lithostratigraphy (IN nbr_records INTEGER, VARIADIC litho_unit_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_mineralogy (IN nbr_records INTEGER, VARIADIC mineralo_unit_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_lithology (IN nbr_records INTEGER, VARIADIC litholo_unit_ids INTEGER[]) TO d2viewer;
GRANT EXECUTE ON FUNCTION fct_listing_taxonomy (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_zoology (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_botany (IN nbr_records INTEGER, VARIADIC taxon_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_chronostratigraphy (IN nbr_records INTEGER, VARIADIC chrono_unit_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_lithostratigraphy (IN nbr_records INTEGER, VARIADIC litho_unit_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_mineralogy (IN nbr_records INTEGER, VARIADIC mineralo_unit_ids INTEGER[]) TO cebmpad;
GRANT EXECUTE ON FUNCTION fct_listing_lithology (IN nbr_records INTEGER, VARIADIC litholo_unit_ids INTEGER[]) TO cebmpad;

commit;
