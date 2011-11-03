begin;

/* Creation of temporary table with list of duplicated specimens and eventual duplicated individuals beneath
   With chosen specimen and chosen individual to merge to
   Thanks to B! for this...
*/
create temporary table spec_ind_dupli as
(
select specimen_ids, 
       specimen_merge_into, 
       array_agg(ind.id) as individual_ids, 
       dummy_first(ind.id) as individual_merge_into
 from specimen_individuals as ind, 
     (select array_agg(id) as specimen_ids, dummy_first(id) as specimen_merge_into
      from specimens 
      group by collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref, host_taxon_ref, acquisition_category, acquisition_date, coalesce(ig_ref,0)
      having count(*) != 1
     ) as sp
where array[specimen_ref] && specimen_ids
group by specimen_ids, specimen_merge_into, type, sex, state, stage, social_status, rock_form
order by specimen_ids, specimen_merge_into, dummy_first(ind.id) 
);


/* Function that will update for all individuals to be grouped, the parts beneath */
create or replace function updateParts() returns integer language plpgsql as $$
declare 
  recInds RECORD;
  recPartsUpdt RECORD;
  iCounter INTEGER := 0;
  rowsUpdated INTEGER := 0;
begin
  FOR recInds IN SELECT * FROM spec_ind_dupli LOOP
    update specimen_parts
    set specimen_individual_ref = recInds.individual_merge_into
    where array[specimen_individual_ref] && recInds.individual_ids; 
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    rowsUpdated := rowsUpdated + iCounter;
    RAISE NOTICE 'We have updated so far % records.', rowsUpdated;
  END LOOP;
  return rowsUpdated;
exception when others then
  RAISE WARNING 'Error: %', SQLERRM;
  rollback;
  return 0;
end;
$$;

/* Function updating peripheral data for either specimens or specimen_individuals
   It will select from array of duplicated specimens or individuals the ones to be merged and the one to be merged into.
   Move from the ones to be merged to the one to be merged into all the peripheral data (catalogue_people, catalogue_properties,...) that
   wouldn't produce unique constraint violation...
*/
create or replace function updatePeriphData(in table_concerned varchar) returns boolean language plpgsql as $$
declare
  recConcerned RECORD;
  iCounter INTEGER := 0;
begin
  FOR recConcerned IN select * from (select fct_explode_array(case when table_concerned = 'specimens' then specimen_ids else individual_ids end) as old_id, case when table_concerned = 'specimens' then specimen_merge_into else individual_merge_into end as new_id from spec_ind_dupli) as subqry where old_id != new_id order by new_id LOOP
    /*Comments*/
    update comments
    set record_id = recConcerned.new_id
    where referenced_relation = table_concerned
      and record_id = recConcerned.old_id;
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% comments updated.', iCounter;
    /*External links*/
    insert into ext_links (referenced_relation, record_id, url)
    (select table_concerned, recConcerned.new_id, url
      from ext_links
      where referenced_relation = table_concerned
        and record_id = recConcerned.old_id
        and url not in (select url
                        from ext_links
                        where referenced_relation = table_concerned
                          and record_id = recConcerned.new_id
                      )
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% external links inserted.', iCounter;
    /*Insurances*/
    insert into insurances (referenced_relation, record_id, insurance_value, insurance_currency, insurance_year)
    (select table_concerned, recConcerned.new_id, insurance_value, insurance_currency, insurance_year
      from insurances
      where referenced_relation = table_concerned
        and record_id = recConcerned.old_id
        and insurance_year not in (select insurance_year
                                   from insurances
                                   where referenced_relation = table_concerned
                                     and record_id = recConcerned.new_id
                                  )
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% insurances inserted.', iCounter;
    /*Catalogue people*/
    insert into catalogue_people (referenced_relation, record_id, people_type, people_sub_type, people_ref)
    (select table_concerned, recConcerned.new_id, cp.people_type, cp.people_sub_type, cp.people_ref
     from catalogue_people as cp
     where cp.referenced_relation = table_concerned
       and cp.record_id = recConcerned.old_id
       and not exists (select 1 from catalogue_people as new_cp
                       where new_cp.referenced_relation = table_concerned
                         and new_cp.record_id = recConcerned.new_id
                         and new_cp.people_type = cp.people_type
                         and new_cp.people_sub_type = cp.people_sub_type
                         and new_cp.people_ref = cp.people_ref
                      )
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% people inserted.', iCounter;
    /*Identifications*/
    insert into identifications (referenced_relation, record_id, notion_concerned, notion_date, notion_date_mask, value_defined, determination_status, order_by)
    (select table_concerned, recConcerned.new_id, ident.notion_concerned, ident.notion_date, ident.notion_date_mask, ident.value_defined, ident.determination_status, ident.order_by
     from identifications as ident
     where ident.referenced_relation = table_concerned
       and ident.record_id = recConcerned.old_id
       and not exists (select 1 from identifications as new_ident
                       where new_ident.referenced_relation = table_concerned
                         and new_ident.record_id = recConcerned.new_id
                         and new_ident.notion_concerned = ident.notion_concerned
                         and new_ident.notion_date = ident.notion_date
                         and new_ident.value_defined_indexed = ident.value_defined_indexed
                      )
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% identifications inserted.', iCounter;
    insert into catalogue_people (referenced_relation, record_id, people_type, people_ref)
    (select 'identifications', ident.id, 'identifier', old_cp.people_ref
      from (identifications as ident inner join catalogue_people as cp on ident.referenced_relation = table_concerned and ident.record_id = recConcerned.new_id and cp.referenced_relation = 'identifications' and cp.record_id = ident.id) 
          inner join
          (identifications as old_ident inner join catalogue_people as old_cp on old_ident.referenced_relation = table_concerned and old_ident.record_id = recConcerned.old_id and old_cp.referenced_relation = 'identifications' and old_ident.id = old_cp.record_id)
          on ident.notion_concerned = old_ident.notion_concerned and ident.notion_date = old_ident.notion_date and ident.value_defined_indexed = old_ident.value_defined_indexed
      where cp.people_ref != old_cp.people_ref
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% identifiers inserted.', iCounter;
    /*Catalogue properties*/
    insert into catalogue_properties (referenced_relation, record_id, property_type, property_sub_type, property_qualifier, date_from, date_from_mask, date_to, date_to_mask, property_unit, property_accuracy_unit, property_method, property_tool)
    (
      select table_concerned, recConcerned.new_id, property_type, property_sub_type, property_qualifier, date_from, date_from_mask, date_to, date_to_mask, property_unit, property_accuracy_unit, property_method, property_tool 
      from catalogue_properties as cp
      where cp.referenced_relation = table_concerned
        and cp.record_id = recConcerned.old_id
        and not exists (select 1 from catalogue_properties as new_cp
                        where new_cp.referenced_relation = table_concerned
                          and new_cp.record_id = recConcerned.new_id
                          and new_cp.property_type = cp.property_type
                          and new_cp.property_sub_type_indexed = cp.property_sub_type_indexed
                          and new_cp.property_qualifier_indexed = cp.property_qualifier_indexed
                          and new_cp.date_from = cp.date_from
                          and new_cp.date_to = cp.date_to
                          and new_cp.property_method_indexed = cp.property_method_indexed
                          and new_cp.property_tool_indexed = cp.property_tool_indexed
                       )
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% properties inserted.', iCounter;
    insert into properties_values (property_ref, property_value, property_accuracy)
    (select cp.id, old_pv.property_value, old_pv.property_accuracy
      from (catalogue_properties as cp inner join properties_values as pv on cp.referenced_relation = table_concerned and cp.record_id = recConcerned.new_id and pv.property_ref = cp.id) 
          inner join
          (catalogue_properties as old_cp inner join properties_values as old_pv on old_cp.referenced_relation = table_concerned and old_cp.record_id = recConcerned.old_id and old_pv.property_ref = old_cp.id)
          on cp.property_type = old_cp.property_type and cp.property_sub_type_indexed = old_cp.property_sub_type_indexed and cp.property_qualifier_indexed = old_cp.property_qualifier_indexed and cp.date_from = old_cp.date_from and cp.date_to = old_cp.date_to and cp.property_method_indexed = old_cp.property_method_indexed and cp.property_tool_indexed = old_cp.property_tool_indexed 
      where pv.property_value_unified != old_pv.property_value_unified
    );
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    RAISE NOTICE '% property values inserted.', iCounter;
    IF table_concerned = 'specimens' THEN
      insert into specimen_collecting_methods (specimen_ref, collecting_method_ref)
      (select recConcerned.new_id, collecting_method_ref
       from specimen_collecting_methods as scm
       where specimen_ref = recConcerned.old_id
         and not exists (select 1 from specimen_collecting_methods as new_scm
                         where new_scm.specimen_ref = recConcerned.new_id
                           and new_scm.collecting_method_ref = scm.collecting_method_ref
                        )
      );
      GET DIAGNOSTICS iCounter = ROW_COUNT;
      RAISE NOTICE '% methods inserted.', iCounter;
      insert into specimen_collecting_tools (specimen_ref, collecting_tool_ref)
      (select recConcerned.new_id, collecting_tool_ref
       from specimen_collecting_tools as sct
       where specimen_ref = recConcerned.old_id
         and not exists (select 1 from specimen_collecting_tools as new_sct
                         where new_sct.specimen_ref = recConcerned.new_id
                           and new_sct.collecting_tool_ref = sct.collecting_tool_ref
                        )
      );
      GET DIAGNOSTICS iCounter = ROW_COUNT;
      RAISE NOTICE '% tools inserted.', iCounter;
      insert into specimens_accompanying (specimen_ref, accompanying_type, taxon_ref, mineral_ref, form, quantity, unit)
      (select recConcerned.new_id, accompanying_type, taxon_ref, mineral_ref, form, quantity, unit
       from specimens_accompanying as sa
       where specimen_ref = recConcerned.old_id
         and not exists (select 1 from specimens_accompanying as new_sa
                         where new_sa.specimen_ref = recConcerned.new_id
                           and new_sa.taxon_ref = sa.taxon_ref
                           and new_sa.mineral_ref = sa.mineral_ref
                        )
      );
      GET DIAGNOSTICS iCounter = ROW_COUNT;
      RAISE NOTICE '% accompanying specimens inserted.', iCounter;
    END IF;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
    return false;
end;
$$;

create or replace function indMergeCountMinMax () returns boolean language plpgsql as $$
declare
  recConcerned RECORD;
  count_min integer := 0;
  count_max integer := 0;
begin
  FOR recConcerned IN select individual_ids, individual_merge_into from spec_ind_dupli order by individual_merge_into LOOP
    update specimen_individuals
    set specimen_individuals_count_min = subqry.ind_min,
        specimen_individuals_count_max = subqry.ind_max 
    from (
          select min(specimen_individuals_count_min) as ind_min, max(specimen_individuals_count_max) as ind_max
          from specimen_individuals
          where array[id] && recConcerned.individual_ids
         ) as subqry
    where id = recConcerned.individual_merge_into
    returning specimen_individuals_count_min, specimen_individuals_count_max INTO count_min, count_max;
    RAISE NOTICE 'count min is now: %', count_min;
    RAISE NOTICE 'count max is now: %', count_max;
  END LOOP;
  return true;
exception
  when others then
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
    return false;
end;
$$;

create function eraseDupliInd () returns boolean language plpgsql as $$
declare
  rowsDeleted integer := 0;
begin
  select count(*) into rowsDeleted from (select distinct fct_explode_array(individual_ids) from spec_ind_dupli) as x;
  RAISE NOTICE '% individuals in total.', rowsDeleted;
  select count(distinct individual_merge_into) into rowsDeleted from spec_ind_dupli;
  RAISE NOTICE '% distinct individuals to merge into.', rowsDeleted;
  delete from specimen_individuals
  where id in (select x.old_id
              from (select distinct fct_explode_array(individual_ids) as old_id from spec_ind_dupli) as x
                    left join
                    (select distinct individual_merge_into as new_id from spec_ind_dupli) as y
                    on x.old_id = y.new_id
              where y.new_id is null
              );
  GET DIAGNOSTICS rowsDeleted = ROW_COUNT;
  RAISE NOTICE '% rows deleted.', rowsDeleted;
  return true;
exception
  when others then
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
    return false;
end;
$$;

create or replace function updateSpecIntoMerge() returns boolean language plpgsql as $$
declare
  recConcerned RECORD;
  iCounter integer := 0;
  rowsHSpecUpdated integer := 0;
  rowsHRelUpdated integer := 0;
  rowsStationVisibleUpdated integer := 0;
begin
  FOR recConcerned IN select * from (select fct_explode_array(specimen_ids) as old_id, specimen_merge_into as new_id from spec_ind_dupli) as subqry where old_id != new_id order by new_id LOOP
    update specimens
    set host_specimen_ref = (select host_specimen_ref 
                             from specimens
                             where id = recConcerned.old_id
                               and host_specimen_ref is not null
                            )
    where id = recConcerned.new_id
      and exists (select host_specimen_ref
                  from specimens
                  where id = recConcerned.old_id
                    and host_specimen_ref is not null
                 )
      and host_specimen_ref is null;
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    rowsHSpecUpdated := rowsHSpecUpdated + iCounter;
    update specimens
    set host_relationship = (select host_relationship 
                             from specimens
                             where id = recConcerned.old_id
                               and host_relationship is not null
                            )
    where id = recConcerned.new_id
      and exists (select host_relationship
                  from specimens
                  where id = recConcerned.old_id
                    and host_relationship is not null
                 )
      and host_relationship is null;
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    rowsHRelUpdated := rowsHRelUpdated + iCounter;
    update specimens
    set station_visible = (select station_visible 
                           from specimens
                           where id = recConcerned.old_id
                             and not station_visible
                          )
    where id = recConcerned.new_id
      and exists (select station_visible
                  from specimens
                  where id = recConcerned.old_id
                    and not station_visible
                 )
      and station_visible;
    GET DIAGNOSTICS iCounter = ROW_COUNT;
    rowsStationVisibleUpdated := rowsStationVisibleUpdated + iCounter;
  END LOOP;
  RAISE NOTICE '% host_specimen_ref in total updated', rowsHSpecUpdated;
  RAISE NOTICE '% host_relationships in total updated', rowsHRelUpdated;
  RAISE NOTICE '% station_visible in total updated', rowsStationVisibleUpdated;
  return true;
exception
  when others then
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
    return false;
end;
$$;

create function eraseDupliSpec () returns boolean language plpgsql as $$
declare
  rowsDeleted integer := 0;
begin
  select count(*) into rowsDeleted from (select distinct fct_explode_array(specimen_ids) from spec_ind_dupli) as x;
  RAISE NOTICE '% specimens in total.', rowsDeleted;
  select count(distinct specimen_merge_into) into rowsDeleted from spec_ind_dupli;
  RAISE NOTICE '% distinct specimens to merge into.', rowsDeleted;
  delete from specimens
  where id in (select x.old_id
              from (select distinct fct_explode_array(specimen_ids) as old_id from spec_ind_dupli) as x
                    left join
                    (select distinct specimen_merge_into as new_id from spec_ind_dupli) as y
                    on x.old_id = y.new_id
              where y.new_id is null
              );
  GET DIAGNOSTICS rowsDeleted = ROW_COUNT;
  RAISE NOTICE '% rows deleted.', rowsDeleted;
  return true;
exception
  when others then
    RAISE WARNING 'Error: %', SQLERRM;
    rollback;
    return false;
end;
$$;

select updateParts() as PartsUpdatedCount;
select updatePeriphData('specimen_individuals') as IndPeriphDataUpdated;
select updatePeriphData('specimens') as SpecPeriphDataUpdated;
select indMergeCountMinMax() as MergeIndividualsCountMinAndMax;
select eraseDupliInd() as IndividualsInDuplicationDeleted;
select updateSpecIntoMerge() as UpdateSpecimenCommonInfos;
select eraseDupliSpec() as SpecimensInDuplicationDeleted;

drop function if exists updateParts();
drop function if exists updatePeriphData(table_concerned varchar);
drop function if exists indMergeCountMinMax();
drop function if exists eraseDupliInd();
drop function if exists updateSpecIntoMerge();
drop function if exists eraseDupliSpec();

alter table specimens drop constraint unq_specimens CASCADE;
create unique index unq_specimens on specimens (collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref, host_taxon_ref, acquisition_category, acquisition_date, coalesce(ig_ref, 0));

rollback;
