select name, new_items_2011, updated_items_2011, new_items_2012, updated_items_2012, new_types_2011, updated_types_2011, new_types_2012, updated_types_2012, new_specimens_2011, updated_specimens_2011, new_specimens_2012, updated_specimens_2012, new_species_2011, new_species_2012
from
(select 1 as level, col.name as name,/* col.path, col.id, (select array_agg(x) from (select id from collections where path like col.path || col.id || '/%' union select col.id) as x),*/
(select count(sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'insert'
 and ut.record_id = sp.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_items_2011,
(select count(distinct sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'update'
 and ut.record_id = sp.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_items_2011,
(select count(sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'insert'
 and ut.record_id = sp.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_items_2012,
(select count(distinct sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'update'
 and ut.record_id = sp.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_items_2012,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'insert'
 and ut.record_id = si.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_types_2011,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'update'
 and ut.record_id = si.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_types_2011,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'insert'
 and ut.record_id = si.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_types_2012,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'update'
 and ut.record_id = si.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_types_2012,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'insert'
 and ut.record_id = s.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_specimens_2011,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'update'
 and ut.record_id = s.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_specimens_2011,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'insert'
 and ut.record_id = s.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_specimens_2012,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'update'
 and ut.record_id = s.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as updated_specimens_2012,
(select count(distinct tax.id)
 from 
 (users_tracking as ut inner join taxonomy as tax
  on ut.referenced_relation = 'taxonomy'
  and ut.action = 'insert'
  and ut.record_id = tax.id
  and tax.level_ref > 47
  and extract(year from ut.modification_date_time) = 2011
 ) inner join 
 (specimens as s  inner join users_tracking as ust
  on ust.referenced_relation = 'specimens'
  and ust.action = 'insert'
  and ust.record_id = s.id
  and extract(year from ust.modification_date_time) = 2011
 ) on s.taxon_ref = tax.id
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_species_2011,
(select count(distinct tax.id)
 from 
 (users_tracking as ut inner join taxonomy as tax
  on ut.referenced_relation = 'taxonomy'
  and ut.action = 'insert'
  and ut.record_id = tax.id
  and tax.level_ref > 47
  and extract(year from ut.modification_date_time) = 2012
 ) inner join 
 (specimens as s  inner join users_tracking as ust
  on ust.referenced_relation = 'specimens'
  and ust.action = 'insert'
  and ust.record_id = s.id
  and extract(year from ust.modification_date_time) = 2012
 ) on s.taxon_ref = tax.id
 where s.collection_ref in (select id from collections where path like col.path || col.id || '/%' union select col.id)
) as new_species_2012
from collections as col
where col.path like '/2/%' 
  and length(regexp_replace(col.path,'\d','')) = 2
group by col.id, col.name, col.path
UNION
select 2 as level, usr.formated_name as name,
(select count(sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'insert'
 and ut.record_id = sp.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_items_2011,
(select count(distinct sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'update'
 and ut.record_id = sp.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_items_2011,
(select count(sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'insert'
 and ut.record_id = sp.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_items_2012,
(select count(distinct sp.id)
 from users_tracking as ut inner join 
   (specimen_parts as sp
    inner join
    (specimen_individuals as si
     inner join
     specimens as s on s.id = si.specimen_ref
    ) on si.id = sp.specimen_individual_ref
   )
 on ut.referenced_relation = 'specimen_parts'
 and ut.action = 'update'
 and ut.record_id = sp.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_items_2012,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'insert'
 and ut.record_id = si.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_types_2011,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'update'
 and ut.record_id = si.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_types_2011,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'insert'
 and ut.record_id = si.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_types_2012,
(select count(si.id)
 from users_tracking as ut inner join 
   (specimen_individuals as si
    inner join
    specimens as s on s.id = si.specimen_ref and si.type != 'specimen'
   )
 on ut.referenced_relation = 'specimen_individuals'
 and ut.action = 'update'
 and ut.record_id = si.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_types_2012,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'insert'
 and ut.record_id = s.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_specimens_2011,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'update'
 and ut.record_id = s.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2011
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_specimens_2011,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'insert'
 and ut.record_id = s.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_specimens_2012,
(select count(s.id)
 from users_tracking as ut inner join specimens as s
 on ut.referenced_relation = 'specimens'
 and ut.action = 'update'
 and ut.record_id = s.id
 and ut.user_ref = usr.id
 and extract(year from modification_date_time) = 2012
 where s.collection_ref in (select id from collections where path like '/2/%')
) as updated_specimens_2012,
(select count(distinct tax.id)
 from 
 (users_tracking as ut inner join taxonomy as tax
  on ut.referenced_relation = 'taxonomy'
  and ut.action = 'insert'
  and ut.record_id = tax.id
  and tax.level_ref > 47
  and extract(year from ut.modification_date_time) = 2011
 ) inner join 
 (specimens as s  inner join users_tracking as ust
  on ust.referenced_relation = 'specimens'
  and ust.action = 'insert'
  and ust.record_id = s.id
  and ust.user_ref = usr.id
  and extract(year from ust.modification_date_time) = 2011
 ) on s.taxon_ref = tax.id
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_species_2011,
(select count(distinct tax.id)
 from 
 (users_tracking as ut inner join taxonomy as tax
  on ut.referenced_relation = 'taxonomy'
  and ut.action = 'insert'
  and ut.record_id = tax.id
  and tax.level_ref > 47
  and extract(year from ut.modification_date_time) = 2012
 ) inner join 
 (specimens as s  inner join users_tracking as ust
  on ust.referenced_relation = 'specimens'
  and ust.action = 'insert'
  and ust.record_id = s.id
  and ust.user_ref = usr.id
  and extract(year from ust.modification_date_time) = 2012
 ) on s.taxon_ref = tax.id
 where s.collection_ref in (select id from collections where path like '/2/%')
) as new_species_2012
from users_tracking as utr inner join users as usr 
on utr.user_ref = usr.id 
and usr.id in (7884, 8201, 40915, 41805, 41806, 41811, 42349, 47157, 47424)
and utr.referenced_relation in ('specimens', 'specimen_individuals', 'specimen_parts')
and utr.action in ('insert', 'update')
and extract(year from utr.modification_date_time) in (2011, 2012)
/*from collections as col
where col.path like '/2/%' 
  and length(regexp_replace(col.path,'\d','')) = 2
group by col.id, col.name, col.path*/
) as x
order by x.level, x.name;

/*select * from collections where path like '/2/%' and length(regexp_replace(path,'\d','')) = 2 order by name;*/
/*select count(*) from specimens where collection_ref = 26;*/