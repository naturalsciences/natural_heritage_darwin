/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * These scripts require the existence of a code table imported from the time before one
 * specimens table
 * This import has been done by creating a new db from a backup and making a dblink to it
 * to create the zzz_code_d2012 from old codes table
 */

/* Selection of the count of Asteroidea codes that have at least one RBINS code counterpart */

/*select count(*) from zzz_code_d2012 as c2012
where referenced_relation ='specimens' and code_prefix = 'AST'
  and exists (select 1 from zzz_code_d2012 as cc2012 where cc2012.referenced_relation = 'specimens' and cc2012.record_id = c2012.record_id and code_prefix = 'RBINS' limit 1);*/

/* For Asteroidea, selection of codes that appears more than once in the actual single specimens table */

select c.* from codes as c where referenced_relation = 'specimens' and code_prefix = 'AST' and 1 < (select count(cc.*) from codes as cc where cc.referenced_relation = 'specimens' and cc.code_prefix = 'AST' and cc.full_code_indexed = c.full_code_indexed) order by full_code_indexed;

/* For Asteroidea, selection of codes that appeared more than once in the structure before moving to a single specimens table */

select c.* from zzz_code_d2012 as c where referenced_relation = 'specimens' and code_prefix = 'AST' and 1 < (select count(cc.*) from zzz_code_d2012 as cc where cc.referenced_relation = 'specimens' and cc.code_prefix = 'AST' and cc.full_code_order_by = c.full_code_order_by) order by full_code_order_by;
