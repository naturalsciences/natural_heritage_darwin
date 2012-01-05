drop view "public"."lenglet_tickets";

create or replace view "public"."lenglet_tickets" as
select df.part_ref as unique_id, 
       trim(case when df.part = 'specimen' then '' else coalesce(df.part,'') end 
            || 
            case when df.individual_sex in ('undefined', 'unknown', 'not stated', 'non applicable') then '' else ', ' || df.individual_sex || case when df.individual_state = 'not applicable' then '' else df.individual_state end end 
            || 
            case when df.individual_type = 'specimen' then '' else ', ' || df.individual_type end 
            || 
            case when df.individual_stage in ('undefined', 'unknown', 'not stated') then '' else ', ' || df.individual_stage end 
            || 
            case when coalesce(df.container_storage, '') in ('unknown', '/', '') then '' || case when coalesce(df.sub_container_storage, '') in ('unknown', '/', '')  then '' else ', ' || df.sub_container_storage end else ', ' || df.container_storage || case when coalesce(df.sub_container_storage, '') in ('unknown', '/', '') or df.sub_container_storage = df.container_storage then '' else ' - ' || df.sub_container_storage end end
           ) as item,
       (select array_to_string(array_agg(coding), '; ')
        from (select trim(coalesce(code_prefix, '') || coalesce(code_prefix_separator, '') || coalesce(code, '') || coalesce(code_suffix_separator, '') || coalesce(code_suffix, ''))::varchar as coding
              from codes
              where referenced_relation = 'specimen_parts'
                and record_id = df.part_ref
                and code_category = 'main'
                and code_prefix != 'RBINS'
             ) as x
       )::varchar as lenglet_code,
       df.taxon_name as taxa_name,
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
       (select ct.name 
        from taxonomy as ct inner join classification_synonymies as cs on cs.referenced_relation = 'taxonomy' and cs.record_id = ct.id and is_basionym = true
        where group_id = (select group_id 
                          from classification_synonymies 
                          where referenced_relation = 'taxonomy' and record_id = df.taxon_ref and group_name = 'rename'
                         )
       )::varchar as current_name,
       df.acquisition_category as acquisition_category,
       (select array_to_string(array_agg(tags_list), ';') 
        from (select trim(regexp_split_to_table(tag_value, ';')) as tags_list 
              from tag_groups as tg 
              where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'country'
             ) as x
       ) as countries,
       (select array_to_string(array_agg(tags_list), ';') 
        from (select trim(regexp_split_to_table(tag_value, ';')) as tags_list 
              from tag_groups as tg 
              where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'province'
             ) as x
       ) as provinces,
       (select array_to_string(array_agg(tags_list), ';') 
        from (select trim(regexp_split_to_table(tag_value, ';')) as tags_list 
              from tag_groups as tg 
              where tg.gtu_ref = df.gtu_ref and tg.sub_group_name not in ('country', 'province')
             ) as x
       )::varchar as location,
       (select array_to_string(array_agg(people_list), ' - ') 
        from (select trim(formated_name) as people_list 
              from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id 
              where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and cp.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as collectors,
       (select array_to_string(array_agg(people_list), ' - ') 
        from (select trim(formated_name) as people_list 
              from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id 
              where cp.people_type = 'donator' and cp.referenced_relation = 'specimens' and cp.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as donator,
       (select array_to_string(array_agg(people_list), ' - ') 
        from (select trim(formated_name) as people_list 
              from (catalogue_people as cp inner join people as peo on cp.people_ref = peo.id) inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier' 
              where ident.referenced_relation = 'specimens' and ident.record_id = df.spec_ref order by cp.order_by
             ) as x
       )::varchar as identifiers,
       df.ig_num as ig_num,
       case when df.part_count_min <> df.part_count_max then df.part_count_min::varchar || ' - ' || df.part_count_max::varchar else df.part_count_min::varchar end as specimen_number,
       df.gtu_ref as gtu_ref
from darwin_flat as df inner join gtu on df.gtu_ref = gtu.id
where (collection_ref = 1 or collection_path like '/1/%')
  and part_ref is not null;

ALTER VIEW "public"."lenglet_tickets" OWNER TO darwin2;
GRANT SELECT ON "public"."lenglet_tickets" TO d2viewer;

select *
from
"public"."lenglet_tickets"
where exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'country' and case when coalesce('?InvitePays', '') = '' then true else string_to_array(trim(lower(translate('?InvitePays', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'province' and case when coalesce('?InviteProvince', '') = '' then true else string_to_array(trim(lower(translate('?InviteProvince', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name not in ('country', 'province') and case when coalesce('?InviteLocalisation', '') = '' then true else string_to_array(trim(lower(translate('?InviteLocalisation', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and case when coalesce('?InviteIGFrom', '') = '' and coalesce('?InviteIGTo', '') = '' then true
      else (df.ig_num in (select trim(regexp_split_to_table('?InviteIGFrom', ';'))) /* 9296 ; 9732 ;9945; 10285; 11858 ; 11899;15718;15957*/
            or
            case 
              when convert_to_integer('?InviteIGFrom') != 0 and convert_to_integer('?InviteIGTo') != 0 then
                convert_to_integer(df.ig_num) Between convert_to_integer('?InviteIGFrom') and convert_to_integer('?InviteIGTo')
              else
                false
            end 
           )
      end
limit 200
;