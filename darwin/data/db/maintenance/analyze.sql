\t
\o analyze_darwin2_obj.sql

select 'VACUUM ANALYZE darwin2.' || tablename || ';'
from pg_tables
where schemaname = 'darwin2';

\o
\i analyze_darwin2_obj.sql
\! rm analyze_darwin2_obj.sql
