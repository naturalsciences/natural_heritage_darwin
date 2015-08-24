set search_path=darwin2,public;

BEGIN;

drop function if exists fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT);
create or replace function fct_report_loans_addresses (loan_id loans.id%TYPE, target_copy TEXT)
  returns
    table
    (
    people_name text,
    institution_name text,
    address text
    )
AS
  $$
  with
  people_infos as
  (
    select regexp_replace(p.formated_name, '\s+', ' ', 'g') as formated_name,
          regexp_replace(pp.formated_name, '\s+', ' ', 'g') as institution_name,
          case
          when (ppa.entry is not null
                AND trim(ppa.entry) != ''
                AND ppa.locality is not null
                AND trim(ppa.locality) != ''
                AND ppa.country is not null
                AND trim(ppa.country) != ''
          ) then
            ppa.entry ||
            case when (ppa.po_box is not null AND trim(ppa.po_box) != '') then
              ', ' || ppa.po_box
            else
              ''
            end ||
            case when (ppa.extended_address is not null AND trim(ppa.extended_address) != '') then
              E'\n' || ppa.extended_address
            else
              ''
            end ||
            case when (ppa.zip_code is not null AND trim(ppa.zip_code) != '') then
              E'\n' || ppa.zip_code || ' ' || ppa.locality ||
              case when (ppa.region is not null and trim(ppa.region) != '') then
                ' - ' || ppa.region
              else
                ''
              end
            else
              E'\n' || ppa.locality ||
              case when (ppa.region is not null and trim(ppa.region) != '') then
                ' - ' || ppa.region
              else
                ''
              end
            end ||
            E'\n' || ppa.country
          when (pa.entry is not null
                     AND trim(pa.entry) != ''
                     AND pa.locality is not null
                     AND trim(pa.locality) != ''
                     AND pa.country is not null
                     AND trim(pa.country) != ''
          ) then
            pa.entry ||
            case when (pa.po_box is not null AND trim(pa.po_box) != '') then
              ', ' || pa.po_box
            else
              ''
            end ||
            case when (pa.extended_address is not null AND trim(pa.extended_address) != '') then
              E'\n' || pa.extended_address
            else
              ''
            end ||
            case when (pa.zip_code is not null AND trim(pa.zip_code) != '') then
              E'\n' || pa.zip_code || ' ' || pa.locality ||
              case when (pa.region is not null and trim(pa.region) != '') then
                ' - ' || pa.region
              else
                ''
              end
            else
              E'\n' || pa.locality ||
              case when (pa.region is not null and trim(pa.region) != '') then
                ' - ' || pa.region
              else
                ''
              end
            end ||
            E'\n' || pa.country
          else
            null
          end::text as address
    from catalogue_people cp inner join people p on cp.people_ref = p.id
                            left join people_addresses pa on p.id = pa.person_user_ref and strpos(pa.tag, 'work') > 0
                            left join (
                                        people_relationships pr
                                        left join
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
          case when (pa.entry is not null
                     AND trim(pa.entry) != ''
                     AND pa.locality is not null
                     AND trim(pa.locality) != ''
                     AND pa.country is not null
                     AND trim(pa.country) != ''
                    ) then
              pa.entry ||
              case when (pa.po_box is not null AND trim(pa.po_box) != '') then
                  ', ' || pa.po_box
              else
                  ''
              end ||
              case when (pa.extended_address is not null AND trim(pa.extended_address) != '') then
                  E'\n' || pa.extended_address
              else
                  ''
              end ||
              case when (pa.zip_code is not null AND trim(pa.zip_code) != '') then
                  E'\n' || pa.zip_code || ' ' || pa.locality ||
                  case when (pa.region is not null and trim(pa.region) != '') then
                    ' - ' || pa.region
                  else
                    ''
                  end
              else
                  E'\n' || pa.locality ||
                  case when (pa.region is not null and trim(pa.region) != '') then
                  ' - ' || pa.region
                  else
                  ''
                  end
              end ||
              E'\n' || pa.country
          else
              null
          end as address
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
    coalesce(
        (select name from institution_address),
        (select institution_name from people_infos where institution_name is not null limit 1)
    ) as institution_name,
    coalesce(
        (select address from institution_address),
        (select address from people_infos where address is not null limit 1)
    ) as address
$$
language sql;

drop function if exists fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, with_addr boolean);
create or replace function fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, with_addr boolean default false)
  returns TABLE (
    target_copy text,
    loan_id loans.id%TYPE,
    loan_name loans.name%TYPE,
    loan_description loans.description%TYPE,
    loan_from_date loans.from_date%TYPE,
    loan_to_date loans.to_date%TYPE,
    loan_extended_to_date loans.extended_to_date%TYPE,
    loan_receiver_name text,
    loan_receiver_institution_name text,
    loan_receiver_address text,
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
       case
        when $5 then
          (select people_name from fct_report_loans_addresses($1,vals.val))::text
        else
          ''::text
       end as loan_receiver_name,
       case
        when $5 then
          (select institution_name from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_institution_name,
       case
        when $5 then
          (select address from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_address,
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

COMMIT;