set search_path=darwin2,public;

BEGIN;

CREATE OR REPLACE FUNCTION fct_filter_encodable_row(ids varchar, col_name varchar, user_id integer) RETURNS SETOF integer
AS $$
with user_right as (
    select db_user_type
    from users
    where id = $3
)
select id
from specimens
where id in (select X::int from regexp_split_to_table($1, ',' ) as X)
      and case
          when (select db_user_type from user_right) = 8 then
            TRUE
          else
            collection_ref in (select X FROM fct_search_authorized_encoding_collections($3) as X)
          end
      and $2 = 'spec_ref';
$$ LANGUAGE sql;

COMMIT;
