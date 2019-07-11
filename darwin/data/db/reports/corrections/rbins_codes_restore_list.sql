/* SQL code to get the whole list of RBINS codes to be reassociated to Asteroidea specimens where not RBINS code exists */
/* As you'll see, there are some cases where it's very difficult to know what to associate back (more than one RBINS code by AST entry) */
/* Rem.: Entries have been excluded and should be treated appart because of more than one entry (specimen associated) for the same AST code in the previous version of codes table:
 * The sql is available in data/db/reports/corrections/usefull_sqls_to_identify_codes_changes_before_one_specimens_table.sql 
 * This represent 18 entries for the following AST codes: AST317, AST4, AST71, AST72, AST81, AST82, AST83, AST91, AST92
 */
select preselection.*, 
       codes_2012.full_code_order_by as c2012_full_code_order_by,
       codes_2012.code_prefix as c2012_code_prefix,
       codes_2012.code_prefix_separator as c2012_code_prefix_separator,
       codes_2012.code as c2012_code,
       codes_2012.code_suffix_separator as c2012_code_suffix_separator,
       codes_2012.code_suffix as c2012_code_suffix
from
(select ac.*,
        c2012.record_id as c2012_recid,
        c2012.referenced_relation as c2012_refrel
 from codes as ac
      inner join 
      (select * from
       zzz_code_d2012 as cd2012
       where cd2012.referenced_relation = 'specimens'
         and cd2012.code_prefix = 'AST'
         and exists (select 1 from zzz_code_d2012 as cc2012 where cc2012.referenced_relation = 'specimens' and cc2012.record_id = cd2012.record_id and code_prefix = 'RBINS' limit 1)
         and id not in (select c.id from zzz_code_d2012 as c where referenced_relation = 'specimens' and code_prefix = 'AST' and 1 < (select count(cc.*) from zzz_code_d2012 as cc where cc.referenced_relation = 'specimens' and cc.code_prefix = 'AST' and cc.full_code_order_by = c.full_code_order_by))
      ) as c2012 on ac.full_code_indexed = c2012.full_code_order_by
 where ac.referenced_relation = 'specimens' and ac.code_prefix = 'AST'
   and not exists (select 1 from codes as acc where acc.referenced_relation = 'specimens' and acc.record_id = ac.record_id and acc.code_prefix = 'RBINS' limit 1)
 order by ac.full_code_indexed
) as preselection
inner join zzz_code_d2012 as codes_2012 
   on codes_2012.record_id = preselection.c2012_recid
  and codes_2012.referenced_relation = preselection.c2012_refrel
  and codes_2012.code_prefix = 'RBINS'
order by preselection.full_code_indexed;

/* Same as previous but for the unique RBINS codes only */

with prefeed as
(
select preselection.*, 
       codes_2012.full_code_order_by as c2012_full_code_order_by,
       codes_2012.code_prefix as c2012_code_prefix,
       codes_2012.code_prefix_separator as c2012_code_prefix_separator,
       codes_2012.code as c2012_code,
       codes_2012.code_suffix_separator as c2012_code_suffix_separator,
       codes_2012.code_suffix as c2012_code_suffix
from
(select ac.*,
        c2012.record_id as c2012_recid,
        c2012.referenced_relation as c2012_refrel
 from codes as ac
      inner join 
      (select * from
       zzz_code_d2012 as cd2012
       where cd2012.referenced_relation = 'specimens'
         and cd2012.code_prefix = 'AST'
         and exists (select 1 from zzz_code_d2012 as cc2012 where cc2012.referenced_relation = 'specimens' and cc2012.record_id = cd2012.record_id and code_prefix = 'RBINS' limit 1)
         and id not in (select c.id from zzz_code_d2012 as c where referenced_relation = 'specimens' and code_prefix = 'AST' and 1 < (select count(cc.*) from zzz_code_d2012 as cc where cc.referenced_relation = 'specimens' and cc.code_prefix = 'AST' and cc.full_code_order_by = c.full_code_order_by))
      ) as c2012 on ac.full_code_indexed = c2012.full_code_order_by
 where ac.referenced_relation = 'specimens' and ac.code_prefix = 'AST'
   and not exists (select 1 from codes as acc where acc.referenced_relation = 'specimens' and acc.record_id = ac.record_id and acc.code_prefix = 'RBINS' limit 1)
 order by ac.full_code_indexed
) as preselection
inner join zzz_code_d2012 as codes_2012 
   on codes_2012.record_id = preselection.c2012_recid
  and codes_2012.referenced_relation = preselection.c2012_refrel
  and codes_2012.code_prefix = 'RBINS'
order by preselection.full_code_indexed
)
select * from prefeed as pf1 where 1 = (select count(*) from prefeed as pf2 where pf1.id = pf2.id);

/* Same as previous but for the more than one RBINS codes only */

with prefeed as
(
select preselection.*, 
       codes_2012.full_code_order_by as c2012_full_code_order_by,
       codes_2012.code_prefix as c2012_code_prefix,
       codes_2012.code_prefix_separator as c2012_code_prefix_separator,
       codes_2012.code as c2012_code,
       codes_2012.code_suffix_separator as c2012_code_suffix_separator,
       codes_2012.code_suffix as c2012_code_suffix
from
(select ac.*,
        c2012.record_id as c2012_recid,
        c2012.referenced_relation as c2012_refrel
 from codes as ac
      inner join 
      (select * from
       zzz_code_d2012 as cd2012
       where cd2012.referenced_relation = 'specimens'
         and cd2012.code_prefix = 'AST'
         and exists (select 1 from zzz_code_d2012 as cc2012 where cc2012.referenced_relation = 'specimens' and cc2012.record_id = cd2012.record_id and code_prefix = 'RBINS' limit 1)
         and id not in (select c.id from zzz_code_d2012 as c where referenced_relation = 'specimens' and code_prefix = 'AST' and 1 < (select count(cc.*) from zzz_code_d2012 as cc where cc.referenced_relation = 'specimens' and cc.code_prefix = 'AST' and cc.full_code_order_by = c.full_code_order_by))
      ) as c2012 on ac.full_code_indexed = c2012.full_code_order_by
 where ac.referenced_relation = 'specimens' and ac.code_prefix = 'AST'
   and not exists (select 1 from codes as acc where acc.referenced_relation = 'specimens' and acc.record_id = ac.record_id and acc.code_prefix = 'RBINS' limit 1)
 order by ac.full_code_indexed
) as preselection
inner join zzz_code_d2012 as codes_2012 
   on codes_2012.record_id = preselection.c2012_recid
  and codes_2012.referenced_relation = preselection.c2012_refrel
  and codes_2012.code_prefix = 'RBINS'
order by preselection.full_code_indexed
)
select * from prefeed as pf1 where 1 < (select count(*) from prefeed as pf2 where pf1.id = pf2.id);
