select df.collection_code as Subcollectie, 
       df.taxon_name as Taxa,
       case when df.gtu_from_date_mask != 0 then case when df.gtu_to_date_mask != 0 then df.gtu_from_date::text || ' - ' || df.gtu_to_date::text else df.gtu_from_date::text end else null::text end as Vinddatum,
       (select array_to_string(array_agg(tags_list), ' - ') FROM (select trim(regexp_split_to_table(tag_value, ';')) as tags_list from tag_groups as tg where tg.gtu_ref = df.gtu_ref and tg.sub_group_name != 'country') as x) as Locatie,
       null as Toponiem,
       (select array_to_string(array_agg(people_list), ' - ') FROM (select trim(formated_name) as people_list from catalogue_people as cp inner join people as peo on cp.people_ref = peo.id where cp.people_type = 'collector' and cp.referenced_relation = 'specimens' and cp.record_id = df.spec_ref order by cp.order_by) as x) as Collectoren,
       (select array_to_string(array_agg(people_list), ' - ') FROM (select trim(formated_name) as people_list from (catalogue_people as cp inner join people as peo on cp.people_ref = peo.id) inner join identifications as ident on cp.record_id = ident.id and cp.referenced_relation = 'identifications' and cp.people_type = 'identifier' where ident.referenced_relation = 'specimens' and ident.record_id = df.spec_ref order by cp.order_by) as x) as Determinatoren,
       df.individual_sex as Geslacht,
       case when df.individual_stage = 'not applicable' then null else df.individual_stage end as Stadium,
       case when df.individual_social_status = 'not applicable' then null else df.individual_social_status end as Social_status,
       df.part as Item,
       df.room || '-' || df.row || '-' || df.shelf || '-' || df.container as Bewaarplaats
from darwin_flat as df
where df.collection_ref in (41, 101);
