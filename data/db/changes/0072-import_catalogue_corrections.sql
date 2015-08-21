set search_path=darwin2,public;

BEGIN;

drop function if exists fct_report_loan_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text);
create or replace function fct_report_loan_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text)
  returns TABLE (
    target_copy text,
    loan_id loans.id%TYPE,
    loan_name loans.name%TYPE,
    loan_description loans.description%TYPE,
    loan_from_date loans.from_date%TYPE,
    loan_to_date loans.to_date%TYPE,
    loan_extended_to_date loans.extended_to_date%TYPE,
    loan_items_id loan_items.id%TYPE,
    loan_items_ig_ref loan_items.ig_ref%TYPE,
    loan_items_from_date loan_items.from_date%TYPE,
    loan_items_to_date loan_items.to_date%TYPE,
    loan_items_specimen_ref loan_items.specimen_ref%TYPE,
    loan_items_details loan_items.details%TYPE
  )
AS
  $$
select vals.val as target_copy,
       loans.id,
       loans.name,
       loans.description,
       loans.from_date,
       loans.to_date,
       loans.extended_to_date,
       loan_items.id,
       loan_items.ig_ref,
       loan_items.from_date,
       loan_items.to_date,
       loan_items.specimen_ref,
       loan_items.details
from ( select unnest(array_vals.val) as val, generate_series(1,array_vals.val_index) as val_index
       from (select case when exists ( select 1
                                       from catalogue_people
                                       where referenced_relation = 'loans'
                                         and record_id = $1
                                         and people_type = 'receiver'
                                         and people_sub_type::integer&2 != 0
                                       limit 1
                                     ) then
                      string_to_array(trim($2,'[]'), ', ')
                    else
                      string_to_array(trim($3,'[]'), ', ')
                    end as val,
                    case when exists ( select 1
                                       from catalogue_people
                                       where referenced_relation = 'loans'
                                         and record_id = $1
                                         and people_type = 'receiver'
                                         and people_sub_type::integer&2 != 0
                                       limit 1
                                     ) then
                      array_length(string_to_array(trim($2,'[]'), ', '),1)
                    else
                      array_length(string_to_array(trim($3,'[]'), ', '),1)
                    end as val_index
            ) as array_vals
     ) as vals,
loans
inner join loan_items on loans.id = loan_items.loan_ref
where loans.id = $1
  and exists(select 1
             from catalogue_people
             where referenced_relation = 'loans'
               and record_id = $1
               and people_type = 'receiver'
               and people_sub_type::integer&4 != 0
             limit 1
            )
  and vals.val IN ( select unnest(string_to_array(trim($4,'[]'), ', ')) )
order by vals.val_index,loans.id;
$$
language sql;

drop function if exists fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT);
create or replace function fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT)
  returns
    table
    (
    people_name text,
    institution_name text
    )
AS
  $$
  with
  people_infos as
  (
    select regexp_replace(p.formated_name, '\s+', ' ', 'g') as formated_name,
          ppa.entry,
          ppa.po_box,
          ppa.extended_address,
          ppa.locality,
          ppa.region,
          ppa.zip_code,
          ppa.country,
          ppa.tag
    from catalogue_people cp inner join people p on cp.people_ref = p.id
                            left join people_addresses pa on p.id = pa.person_user_ref and strpos(pa.tag, 'work') > 0
                            left join (
                                        people_relationships pr
                                        inner join
                                        people pp on pr.person_2_ref = pp.id and NOT pp.is_physical
                                        left join people_addresses ppa on pp.id = ppa.person_user_ref
                                      ) on pr.person_1_ref = p.id and pr.relationship_type IN ('works for', 'belongs to')
    where referenced_relation = 'loans'
      and record_id = $1
      and people_type = 'receiver'
      and case when $2 = 'Responsible copy' then
            people_sub_type::integer&2 != 0
          else
            people_sub_type::integer&4 != 0
          end
      and p.is_physical
    order by order_by,(strpos(pa.tag, 'work') > 0)
  ),
  institution_address as
  (
    select p.formated_name::text as name,
          pa.entry,
          pa.po_box,
          pa.extended_address,
          pa.locality,
          pa.region,
          pa.zip_code,
          pa.country,
          pa.tag
    from catalogue_people cp inner join people p on cp.people_ref = p.id
                            left join people_addresses pa on p.id = pa.person_user_ref
    where referenced_relation = 'loans'
      and record_id = $1
      and people_type = 'receiver'
      and case when $2 = 'Responsible copy' then
            people_sub_type::integer&2 != 0
          else
            people_sub_type::integer&4 != 0
          end
      and NOT p.is_physical
    order by order_by
    limit 1
  )
  select
    array_to_string(array(select distinct on (formated_name) formated_name from people_infos),', ') as people_name,
    coalesce((select institution_address.name from institution_address),
            (select entry from people_infos where entry is not null limit 1)
            ) as institution_name
$$
language sql;

COMMIT;