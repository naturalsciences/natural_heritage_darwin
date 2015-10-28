select distinct users.formated_name as "User",
                ( 
                  select '/'||array_to_string(array_agg(sc.name),'/')||'/'||c.name
                  from collections as sc
                  inner join (select unnest(string_to_array(trim(c.path, '/'),'/'))::bigint as id) as scc on sc.id = scc.id
                ) as "Collection path",
                users_tracking.action as "Action",
                case when specimens.type = 'specimen' then 'non type' else 'type' end as "Type",
                count(*) over (
                                partition by users.formated_name,
                                             ( 
                                               select '/'||array_to_string(array_agg(sc.name),'/')||'/'||c.name
                                               from collections as sc
                                               inner join (select unnest(string_to_array(trim(c.path, '/'),'/'))::bigint as id) as scc on sc.id = scc.id
                                             ), 
                                             users_tracking.action
                              ) as "Count",
                count(*) over (
                                partition by users.formated_name,
                                             ( 
                                               select '/'||array_to_string(array_agg(sc.name),'/')||'/'||c.name
                                               from collections as sc
                                               inner join (select unnest(string_to_array(trim(c.path, '/'),'/'))::bigint as id) as scc on sc.id = scc.id
                                             ), 
                                             users_tracking.action,
                                             case when specimens.type = 'specimens' then 'non type' else 'type' end
                              ) as "Type Count"
from users
inner join users_tracking 
   on users.id = users_tracking.user_ref 
  and users_tracking.modification_date_time between '2014-01-01'::timestamp and '2014-12-31'::timestamp
  and users_tracking.referenced_relation = 'specimens'
inner join specimens 
   on users_tracking.record_id = specimens.id
inner join collections as c
   on c.id = specimens.collection_ref
where case
        when 0 = 0 then
          true
        else
          users.id = 41811
      end 
  and case 
        when  2 != 0 then
          c.id in (select id from collections where id = 2 or path like '%/'||2||'/%')
        else
          true
      end
  and users_tracking.action != 'delete'
order by "User", "Collection path", "Action"
     
