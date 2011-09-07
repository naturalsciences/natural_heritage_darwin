select df.id as id, 
       df.part as item,
       df.coding as lenglet_code,
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
       case when df.part_count_max <> df.part_count_max then df.part_count_min::varchar || ' - ' || df.part_count_max::varchar else part_count_min::varchar end as specimen_number
from (select df.*, trim(coalesce(code_prefix, '') || coalesce(code_prefix_separator, '') || coalesce(code, '') || coalesce(code_suffix_separator, '') || coalesce(code_suffix, ''))::varchar as coding 
      from darwin_flat as df left join codes on referenced_relation = 'specimen_parts' and record_id = df.part_ref and code_category = 'main' and coalesce(code_prefix,'') != 'RBINS' 
      where collection_ref in (1, 5, 6, 7, 8, 9, 10, 176, 181) 
        and part_ref is not null
     ) as df
where exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'country' and case when coalesce('?InvitePays', '') = '' then true else string_to_array(trim(lower(translate('België;', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name = 'province' and case when coalesce('?InviteProvince', '') = '' then true else string_to_array(trim(lower(translate('Liège;', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and exists (select 1 from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name not in ('country', 'province') and case when coalesce('?InviteLocalisation', '') = '' then true else string_to_array(trim(lower(translate('Liège', ' ,/\#', ';;;;;')), ';'),';') && string_to_array(trim(lower(translate(tag_value, ' ,/\#', ';;;;;')), ';'), ';') end limit 1)
  and (df.ig_num in (select trim(regexp_split_to_table('9296 ; 9732 ;9945; 10285; 11858 ; 11899;15718;15957', ';')))
       or
       case 
         when convert_to_integer('9296 ; 9732 ;9945; 10285; 11858 ; 11899;15718;15957') != 0 and convert_to_integer('9300') != 0 then
           convert_to_integer(df.ig_num) Between convert_to_integer('9296 ; 9732 ;9945; 10285; 11858 ; 11899;15718;15957') and convert_to_integer('9300')
         else
           false
       end 
      );