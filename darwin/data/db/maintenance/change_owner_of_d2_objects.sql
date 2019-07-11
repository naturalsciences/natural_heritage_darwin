\prompt 'Please provide the schema concerned: ' scheman
\set schemaname '\'' :scheman '\''
\prompt 'Please provide the new owner: ' ownern
\set ownername '\'' :ownern '\''

\t
\o set_darwin2_obj_to_owner.sql
select sqlcmd 
from 
     (select 'ALTER DOMAIN ' || :schemaname || '.' || domain_name || ' OWNER TO ' || :ownername || ';' as sqlcmd, 
             1 as orderby 
      from information_schema.domains 
      where domain_schema = :schemaname
      UNION
      select DISTINCT 'ALTER FUNCTION ' || :schemaname || '.' || min(x.proname) || ' (' || array_to_string(array_agg(y.typname), ',') || ') OWNER TO ' || :ownername || ';' as sqlcmd,
            2 as orderby 
      from (select oid as prooid, proname, unnest(proargtypes) as datatype from pg_proc 
            where pronamespace = (select oid from pg_namespace where nspname = :schemaname)
          ) as x 
          left join 
          (select oid as typoid, case when typname = 'staging' then :schemaname || '.staging' else typname end from pg_type
          ) as y on x.datatype = y.typoid 
      where proname not in ('dummy_first')
      group by x.prooid
      UNION
      select DISTINCT 'ALTER FUNCTION ' || :schemaname || '.' || min(x.proname) || ' () OWNER TO ' || :ownername || ';' as sqlcmd,
            2 as orderby 
      from (select oid as prooid, proname from pg_proc 
            where pronamespace = (select oid from pg_namespace where nspname = :schemaname)
              and proargtypes = ''
          ) as x 
      where proname not in ('dummy_first')
      group by x.prooid
      UNION 
      select 'ALTER TABLE ' || :schemaname || '.' || tablename || ' OWNER TO ' || :ownername || ';' as sqlcmd, 
             3 as orderby 
      from pg_tables 
      where schemaname = :schemaname
      UNION 
      select 'ALTER SEQUENCE ' || :schemaname || '.' || relname || ' OWNER TO ' || :ownername || ';' as sqlcmd, 
             4 as orderby 
      from pg_class 
      where relkind = 'S' 
        and relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = :schemaname) 
      UNION
      select distinct 'ALTER FUNCTION ' || :schemaname || '.' || trim(trim(action_statement, 'EXECUTE PROCEDURE '), :schemaname || '.') || ' OWNER TO ' || :ownername || ';' as sqlcmd, 
             5 as orderby 
      from information_schema.triggers 
      where trigger_schema = :schemaname
        and position('tsvector_update_trigger' in action_statement) = 0
     ) as x order by orderby;
\o
\i set_darwin2_obj_to_owner.sql
\! rm set_darwin2_obj_to_owner.sql
