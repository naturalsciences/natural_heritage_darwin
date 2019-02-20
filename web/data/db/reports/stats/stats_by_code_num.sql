with aves_codes as (
select *
from (
select record_id,
       coalesce(code_prefix,'') || coalesce(code_prefix_separator, '') || code || coalesce(code_suffix_separator, '') || coalesce(code_suffix, '') as code,
       case
         when code ~ '^\d+\D+$' then
           array_to_string(regexp_matches(code, '^(\d+)\D+', 'g'), ',')
         when code ~ '^\D+\d+$' then
           array_to_string(regexp_matches(code, '^\D+(\d+)', 'g'), ',')
         when code ~ '^\D+\d+\D+$' then
           array_to_string(regexp_matches(code, '^\D+(\d+)\D+', 'g'), ',')
       end ::bigint as code_num
from codes 
where referenced_relation = 'specimens' 
  and exists (select id from specimens where collection_ref = 6 and id = codes.record_id) 
  and code_num is null
  and (
	code ~ '^\d+\D+$'
	or
	code ~ '^\D+\d+$'
	or
	code ~ '^\D+\d+\D+$'
      )
union
select record_id,
       coalesce(code_prefix,'') || coalesce(code_prefix_separator, '') || code || coalesce(code_suffix_separator, '') || coalesce(code_suffix, '') as code,
       code_num
from codes
where referenced_relation = 'specimens' 
  and exists (select id from specimens where collection_ref = 6 and id = codes.record_id) 
  and code_num is not null
) as aves_codes
)
select aves_codes.code_num, count(specimens.*) as nbr_records, sum(specimen_count_min) as minimum_specimen_count, sum(specimen_count_max) as maximum_specimen_count
from specimens inner join aves_codes on id = record_id
group by aves_codes.code_num
order by aves_codes.code_num;
