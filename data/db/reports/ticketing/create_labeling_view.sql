create or replace view "public"."labeling" as
select id as unique_id,
       df.collection_ref as collection,
       df.collection_name as collection_name,
       df.collection_path as collection_path,
       case when coalesce(df.specimen_part,'') in ('specimen', 'animal', 'undefined', 'unknown', '') then '' else df.specimen_part end as part_item,
       case when df.sex in ('undefined', 'unknown', 'not stated', 'non applicable') then '' else df.sex || case when df.state = 'not applicable' then '' else ' ' || df.state end end as part_sex_state,
       case when df.type = 'specimen' then '' else df.type end as part_type,
       case when df.stage in ('undefined', 'unknown', 'not stated') then '' else df.stage end as part_stage,
       case when df.sub_container is null then case when coalesce(df.container_storage, '') in ('unknown', '/', '') then '' else df.container_storage end else '' end as part_container_storage,
       case when coalesce(df.sub_container_storage, '') in ('unknown', '/', '') then '' else coalesce(df.sub_container_storage, '') end as part_sub_container_storage,
       array[fullToIndex(df.specimen_part)] as part,
       array[fullToIndex(df.type)] as type,
       df.sex as sex,
       df.stage as stage,
       CAST(array_to_string(labeling_code_for_indexation(df.id), ';') AS varchar) as code,
       (select code_num from codes where referenced_relation = 'specimens' and record_id = df.id and code_category = 'main' and coalesce(upper(code_prefix),'') != 'RBINS' and code_num is not null limit 1) as code_num,
       labeling_code_for_indexation(df.id) as code_array,
       df.taxon_ref as taxon_ref,
       df.taxon_name as taxon_name,
       df.taxon_name_indexed as taxon_name_indexed,
       df.taxon_path as taxon_path,
       (select phyl.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxphyls
                  where taxphyls.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as phyl on y.id = phyl.id and phyl.level_ref = 4
       )::varchar as phyl,
       (select clas.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxclass
                  where taxclass.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as clas on y.id = clas.id and clas.level_ref = 12
       )::varchar as clas,
       (select ordo.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxord
                  where taxord.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as ordo on y.id = ordo.id and ordo.level_ref = 28
       )::varchar as ordo,
       (select fam.name
        from (select x.id::integer as id from
                 (select regexp_split_to_table(path, '/') as id
                  from taxonomy as taxfam
                  where taxfam.id = df.taxon_ref
                 ) as x
              where x.id != ''
             ) as y
             inner join taxonomy as fam on y.id = fam.id and fam.level_ref = 34
       )::varchar as family,
       (select array_to_string(array_agg(ct.name), ' - ')
        from taxonomy as ct inner join classification_synonymies as cs on cs.referenced_relation = 'taxonomy' and cs.record_id = ct.id and is_basionym = true
        where group_id = (select group_id
                          from classification_synonymies
                          where referenced_relation = 'taxonomy' and record_id = df.taxon_ref and group_name = 'rename'
                         )
       )::varchar as current_name,
       case when df.acquisition_category is not null and trim(df.acquisition_category) !='' then df.acquisition_category else '' end as acquisition_category,
       df.gtu_ref as gtu_ref,
       df.gtu_country_tag_value::varchar as countries,
       df.gtu_country_tag_indexed as countries_array,
       df.gtu_province_tag_value::varchar as provinces,
       df.gtu_province_tag_indexed as provinces_array,
       df.gtu_others_tag_value::varchar as location,
       df.gtu_others_tag_indexed as location_array,
       case when trim(df.gtu_code) in ('', '/', '0', '0/') then '' else trim(df.gtu_code) end as location_code,
       case when df.gtu_from_date_mask >= 32 then to_char(df.gtu_from_date, 'DD/MM/YYYY') else '' end || case when df.gtu_to_date_mask >= 32 then ' - ' || to_char(df.gtu_to_date, 'DD/MM/YYYY') else '' end as gtu_date,
       case when df.gtu_location is not null then trunc(df.gtu_location[0]::numeric, 6) || '/' || trunc(df.gtu_location[1]::numeric, 6) else '' end as lat_long,
       case when df.gtu_elevation is not null then trunc(df.gtu_elevation::numeric,2) || ' m' || case when df.gtu_elevation_accuracy is not null then ' +- ' || trunc(df.gtu_elevation_accuracy::numeric,2) || ' m' else '' end else '' end as elevation,
       (select case when length(regexp_replace(coll, '[^,]+', '', 'g')) > 2
                    then substr(coll, 1, strpos(coll, ',')-1) || ' & al.'
                    else coll
                end
        from
          (select array_to_string(array_agg(people_list), ', ') as coll
              from (select distinct trim(family_name) as people_list, cp.order_by
                    from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id
                    where cp.people_type = 'collector'
                      and cp.referenced_relation = 'specimens'
                      and cp.record_id = df.id
                      and peo.family_name NOT IN ('Unknown', '/')
                    order by cp.order_by
                  ) as x
          ) as y
        )::varchar as collectors,
       (select case when length(regexp_replace(identi, '[^,]+', '', 'g')) > 2
                    then substr(identi, 1, strpos(identi, ',')-1) || ' & al.'
                    else identi
                end || identi_year
        from
        (select array_to_string(array_agg(people_list), ', ') as identi, case when max(ident_date) is not null then ', ' || max(ident_date) else '' end as identi_year
          from (select distinct trim(family_name) as people_list, cp.order_by, case when ident.notion_date_mask != 0 then extract(year from ident.notion_date) else null::double precision end as ident_date
                from (catalogue_people as cp inner join people as peo on cp.people_ref = peo.id) inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier'
                where ident.referenced_relation = 'specimens'
                  and ident.record_id = df.id
                  and peo.family_name NOT IN ('Unknown', '/')
                  and ident.notion_date = (select max(notion_date)
                                          from identifications as idt
                                          where idt.referenced_relation = ident.referenced_relation
                                            and idt.record_id = ident.record_id
                                          )
                order by cp.order_by
              ) as x
         ) as y
       )::varchar as identifiers,
       ''::varchar as part_identifiers,
       (select case when length(regexp_replace(coll, '[^,]+', '', 'g')) > 2
                    then substr(coll, 1, strpos(coll, ',')-1) || ' & al.'
                    else coll
                end
        from
          (select array_to_string(array_agg(people_list), ', ') as coll
              from (select distinct trim(family_name) as people_list, cp.order_by
                    from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id
                    where cp.people_type = 'donator'
                      and cp.referenced_relation = 'specimens'
                      and cp.record_id = df.id
                      and peo.family_name NOT IN ('Unknown', '/')
                    order by cp.order_by
                  ) as x
          ) as y
        )::varchar as donators,
       coalesce(df.ig_num, '-') as ig_num,
       df.ig_num_indexed as ig_num_indexed,
       convert_to_integer(coalesce(ig_num, '-')) as ig_numeric,
       case when df.specimen_count_min <> df.specimen_count_max and df.specimen_count_min is not null and df.specimen_count_max is not null then df.specimen_count_min || ' - ' || df.specimen_count_max else case when df.specimen_count_min is not null then df.specimen_count_min::text else '' end end as specimen_number,
       df.specimen_count_max as specimen_number_max,
       df.room as part_room,
       df.row as part_row,
       df.shelf as part_shelf,
       df.container as part_container,
       df.sub_container as part_sub_container,
       case when exists(select 1 from comments where referenced_relation = 'specimens' and record_id = df.id) then 'Y' else 'N' end as comments
from specimens as df
;

ALTER VIEW "public"."labeling" OWNER TO darwin2;
GRANT SELECT ON "public"."labeling" TO d2viewer;
