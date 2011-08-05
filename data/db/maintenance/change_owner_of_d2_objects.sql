\t
\o set_darwin2_obj_to_owner.sql
select sqlcmd 
from 
     (select 'ALTER TABLE darwin2.' || tablename || ' OWNER TO darwin2;' as sqlcmd, 
             1 as orderby 
      from pg_tables 
      where schemaname = 'darwin2'
      UNION 
      select 'ALTER SEQUENCE darwin2.' || relname || ' OWNER TO darwin2;' as sqlcmd, 
             3 as orderby 
      from pg_class 
      where relkind = 'S' 
        and relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = 'darwin2') 
      UNION 
      (select 'ALTER FUNCTION darwin2.' || proname || ' (' || array_to_string(array_agg(case when typname = 'staging' then 'darwin2.staging' else typname end), ',') || ') OWNER TO darwin2;' as sqlcmd, 
              0 as orderby 
       from (select oid, proname, unnest(proargtypes) as datatype from pg_proc where pronamespace = (select oid from pg_namespace where nspname = 'darwin2')) as x left join (select oid as typoid, typname from pg_type) as y on x.datatype = y.typoid 
       group by x.oid, x.proname) UNION (select DISTINCT 'ALTER FUNCTION ' || trim(action_statement, 'EXECUTE PROCEDURE ') || ' OWNER TO darwin2;' as sqlcmd, 0 as orderby FROM information_schema.triggers where trigger_schema = 'darwin2')) as x order by orderby;