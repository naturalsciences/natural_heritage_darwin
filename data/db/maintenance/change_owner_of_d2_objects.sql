\t
\o set_darwin2_obj_to_owner.sql
select sqlcmd 
from 
     (select 'ALTER DOMAIN darwin2.' || domain_name || ' OWNER TO darwin2;' as sqlcmd, 
             1 as orderby 
      from information_schema.domains 
      where domain_schema = 'darwin2'
      UNION
      select DISTINCT 'ALTER FUNCTION darwin2.' || x.proname || ' (' || array_to_string(darwin2.array_accum(y.typname), ',') || ') OWNER TO darwin2;' as sqlcmd, 
            2 as orderby 
      from (select oid as prooid, proname, unnest(proargtypes) as datatype from pg_proc 
            where pronamespace = (select oid from pg_namespace where nspname = 'darwin2')
          ) as x 
          left join 
          (select oid as typoid, case when typname = 'staging' then 'darwin2.staging' else typname end from pg_type
          ) as y on x.datatype = y.typoid 
      where proname not in ('dummy_first', 'array_accum')
        /*and proname = 'fct_chk_possible_upper_level'*/
      group by x.prooid, x.proname
      UNION 
      select 'ALTER TABLE darwin2.' || tablename || ' OWNER TO darwin2;' as sqlcmd, 
             3 as orderby 
      from pg_tables 
      where schemaname = 'darwin2'
      UNION 
      select 'ALTER SEQUENCE darwin2.' || relname || ' OWNER TO darwin2;' as sqlcmd, 
             4 as orderby 
      from pg_class 
      where relkind = 'S' 
        and relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = 'darwin2') 
      UNION
      select distinct 'ALTER FUNCTION darwin2.' || trim(trim(action_statement, 'EXECUTE PROCEDURE '), 'darwin2.') || ' OWNER TO darwin2;' as sqlcmd, 
             5 as orderby 
      from information_schema.triggers 
      where trigger_schema = 'darwin2'
     ) as x order by orderby;
\o
\i set_darwin2_obj_to_owner.sql
\! rm set_darwin2_obj_to_owner.sql
