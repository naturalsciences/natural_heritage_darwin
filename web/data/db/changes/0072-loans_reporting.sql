set search_path=darwin2,public;

BEGIN;

drop function if exists fct_report_loans_transporters (loan_id loans.id%TYPE);
drop function if exists fct_report_loans_transporters (loan_id loans.id%TYPE, transporter_side TEXT);
drop function if exists fct_report_loans_transporters (loan_id loans.id%TYPE, transporter_side TEXT, lang TEXT);
create or replace function fct_report_loans_transporters (loan_id loans.id%TYPE, transporter_side TEXT DEFAULT 'sender', lang TEXT DEFAULT 'en')
  returns
    table
    (
      transport_dispatched_by TEXT,
      transport_transporter_names TEXT,
      transport_track_ids TEXT
    )
AS
  $$
    with
    transporters as (
        select
          case
          when cp.people_type = 'sender' then
            case
              when $3 = 'fr' then
                'prêteur'
              when $3 = 'nl' then
                'lener'
              else
                'loaner'
            end
          else
            case
              when $3 = 'fr' then
                'emprunteur'
              when $3 = 'nl' then
                'lener'
              else
                'borrower'
            end
          end as transport_dispatched_by,
          p.formated_name as transport_transporter_name
        from loans inner join catalogue_people cp
                   on cp.referenced_relation = 'loans'
                      and cp.record_id = loans.id
                      and cp.people_type IN ('sender', 'receiver')
                      and people_sub_type::integer&64 != 0
                   inner join people p on cp.people_ref = p.id
        where loans.id = $1
          and case
                when $2 IN ('sender', 'loaner') then
                  cp.people_type = 'sender'
                when $2 IN ('receiver', 'borrower') then
                  cp.people_type = 'receiver'
              else
                  false
              end
        order by cp.people_type, cp.order_by
    )
    select distinct on (transport_dispatched_by)
      transport_dispatched_by,
      trim(array_to_string(array_agg(transport_transporter_name) OVER (PARTITION BY transport_dispatched_by), ', '), ', ') as transport_transporter_names,
      case
        when transport_dispatched_by = 'loaner' then
          (
            select trim(array_to_string(array_agg(lower_value), ', '), ', ') as tracking_id
            from properties
            where referenced_relation = 'loans'
              and record_id = $1
              and fullToIndex(property_type) = 'trackingid'
              and applies_to_indexed = 'sender'
            group by fullToIndex(property_type)
            limit 1
          )
        else
        (
          select trim(array_to_string(array_agg(lower_value), ', '), ', ') as tracking_id
          from properties
          where referenced_relation = 'loans'
                and record_id = $1
                and fullToIndex(property_type) = 'trackingid'
                and applies_to_indexed = 'receiver'
          group by fullToIndex(property_type)
          limit 1
        )
      end as transport_track_ids
    from transporters;
  $$
language SQL;

drop function if exists fct_report_loans_return_to (loan_id loans.id%TYPE);
drop function if exists fct_report_loans_return_to (loan_id loans.id%TYPE, lang TEXT);
create or replace function fct_report_loans_return_to (loan_id loans.id%TYPE, lang TEXT default 'en')
  returns
    TABLE
    (
      return_message TEXT
    )
AS
  $$
  with communications as
  (
      select entry, comm_type, tag
      from collection_maintenance
        inner join people on collection_maintenance.people_ref = people.id
        inner join people_comm on people.id = people_comm.person_user_ref
      where referenced_relation = 'loans'
            and record_id = $1
            and action_observation = 'approval'
            and strpos(tag, 'work') > 0
  )
  select
    case
      when $2 = 'fr' then
        'Veuillez retourner une copie de ce formulaire par FAX au '
      when $2 = 'nl' then
        'Stuur een kopie van dit formulier per fax naar '
      else
        'Return a copy of this form by FAX at '
    end ||
    coalesce((select trim(array_to_string(array_agg(entry), ', '), ', ') from communications where comm_type = 'phone/fax' and strpos(tag, 'fax') > 0), '+32(0)2.627.41.13.') ||
    coalesce((select
                case
                  when $2 = 'fr' then
                    E'\nou par email à '
                  when $2 = 'nl' then
                    E'\nof bij email naar '
                  else
                    E'\nor by email at '
                end
                || trim(array_to_string(array_agg(entry), ', '), ', ') from communications where comm_type = 'e-mail'
             ), ''
            ) as return_message
  $$
language sql;

drop function if exists fct_report_loans_maintenances (loan_id loans.id%TYPE, maintenance_type TEXT);
create or replace function fct_report_loans_maintenances (loan_id loans.id%TYPE, maintenance_type TEXT)
  returns
    table
    (
        maintenance_date TEXT,
        maintenance_people TEXT,
        maintenance_people_functions TEXT
    )
AS
  $$
    with maintenance_people as (
        SELECT
          DISTINCT ON (maintenance_date, formated_name)
          CASE
          WHEN modification_date_time IN ('0001-01-01' :: TIMESTAMP, '2038-12-31' :: TIMESTAMP)
            THEN
              NULL
          ELSE
            TO_CHAR(modification_date_time, 'DD/MM/YYYY')
          END::TEXT                                      AS maintenance_date,
          regexp_replace(formated_name, '\s+', ' ', 'g') AS formated_name,
          case
            when person_user_role = '' then
              '*'
            else
              person_user_role
          end::text AS people_function
        FROM
          collection_maintenance
          INNER JOIN people
            ON collection_maintenance.people_ref = people.id
          LEFT JOIN people_relationships pr
            ON people.id = pr.person_2_ref
               AND pr.relationship_type IN ('works for', 'belongs to')
        WHERE collection_maintenance.referenced_relation = 'loans'
          AND collection_maintenance.record_id = $1
          AND collection_maintenance.action_observation = $2
        ORDER BY
          maintenance_date DESC,
          formated_name,
          pr.activity_date_to DESC,
          pr.activity_date_from DESC,
          case when person_user_role = '' then 'zzz' else person_user_role end::TEXT
    )
    select distinct on (maintenance_date)
      maintenance_date,
      trim(array_to_string(array_agg(formated_name) OVER (PARTITION BY maintenance_date), ', '), ', ') as maintenance_people,
      case
        when trim(array_to_string(array_agg(people_function) OVER (PARTITION BY maintenance_date), ', '), ', ') = '*' then
          null
        else
          trim(array_to_string(array_agg(people_function) OVER (PARTITION BY maintenance_date), ', '), ', ')
      end as maintenance_people
    from maintenance_people
    order by maintenance_date desc;
  $$
language sql;

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
                                        inner join
                                        people pp on pr.person_1_ref = pp.id and NOT pp.is_physical
                                        inner join people_addresses ppa on pp.id = ppa.person_user_ref
                                       ) on pr.person_2_ref = p.id and pr.relationship_type IN ('works for', 'belongs to')
    where referenced_relation = 'loans'
      and record_id = $1
      and people_type = 'receiver'
      and case when $2 IN ('Responsible copy', 'Copie responsable', 'Verantwoordelijk copie') then
            people_sub_type::integer&2 != 0
          else
            people_sub_type::integer&4 != 0
          end
      and p.is_physical
    order by order_by,(strpos(pa.tag, 'work') > 0),pr.activity_date_from desc
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
      and case when $2 IN ('Responsible copy', 'Copie responsable', 'Verantwoordelijk copie') then
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
drop function if exists fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, targeted_catalogues text, with_addr boolean);
drop function if exists fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, targeted_catalogues text, with_addr boolean, lang text);
create or replace function fct_report_loans_forms (loan_id integer, full_target_list text, short_target_list text, selected_target_list text, targeted_catalogues text, with_addr boolean default false, lang text default 'en')
  returns TABLE (
    target_copy TEXT,
    loan_id loans.id%TYPE,
    loan_name loans.name%TYPE,
    loan_description loans.description%TYPE,
    loan_purposes TEXT,
    loan_conditions TEXT,
    loan_reception_conditions TEXT,
    loan_return_conditions TEXT,
    loan_from_date TEXT,
    loan_to_date TEXT,
    loan_extended_to_date TEXT,
    loan_receiver_name text,
    loan_receiver_institution_name text,
    loan_receiver_address text,
    loan_items_id TEXT,
    loan_items_name loan_items.details%TYPE,
    loan_items_description comments.comment%TYPE,
    loan_items_value insurances.insurance_value%TYPE,
    loan_phantom_id TEXT,
    loan_rbins_phantom_id TEXT
  )
AS
  $$
select vals.val as target_copy,
       loans.id,
       loans.name,
       loans.description,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'usage') as loan_purposes,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'state_observation') as loan_conditions,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'reception_state_observation') as loan_reception_conditions,
       (select array_to_string(array_agg(comment), E'\n') from comments where referenced_relation = 'loans' and record_id = $1 and notion_concerned = 'return_state_observation') as loan_return_conditions,
       to_char(loans.from_date,'DD/MM/YYYY'),
       to_char(loans.to_date,'DD/MM/YYYY'),
       to_char(loans.extended_to_date,'DD/MM/YYYY'),
       case
        when $6 then
          (select people_name from fct_report_loans_addresses($1,vals.val))::text
        else
          ''::text
       end as loan_receiver_name,
       case
        when $6 then
          (select institution_name from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_institution_name,
       case
        when $6 then
          (select address from fct_report_loans_addresses($1,vals.val))
        else
          ''::text
       end as loan_receiver_address,
       case
        when specimen_ref is null then
          coalesce (
              (
                select
                  case
                    when $7 = 'fr' then
                      'Codes temporaires: '
                    when $7 = 'nl' then
                      'Tijdelijke codes: '
                    else
                      'Temporary codes: '
                  end
                  ||
                  trim(
                       array_to_string(
                           array_agg(
                                       case
                                       when coalesce(code_prefix,'') != '' then
                                         code_prefix || coalesce(code_prefix_separator,'')
                                       else
                                         ''
                                       end ||
                                       coalesce(code,'') ||
                                       case
                                       when coalesce(code_suffix,'') != '' then
                                         coalesce(code_suffix_separator,'') || code_suffix
                                       else
                                         ''
                                       end
                                     ),
                           ', '
                       ),
                       ', '
                  )
                from codes
                where referenced_relation = 'loan_items'
                      and record_id = loan_items.id
                      and code_category = 'main'
                limit 3
              ), '')
        else
          'RBINS ID: ' || specimens.id  ||
          coalesce (
          (
            select E'\nCodes: ' || trim(array_to_string(array_agg(
              case
                when coalesce(code_prefix,'') != '' then
                  code_prefix || coalesce(code_prefix_separator,'')
                else
                  ''
              end ||
              coalesce(code,'') ||
              case
              when coalesce(code_suffix,'') != '' then
                coalesce(code_suffix_separator,'') || code_suffix
              else
                ''
              end
            ), ', '), ', ')
            from codes
            where referenced_relation = 'specimens'
              and record_id = specimens.id
              and code_category = 'main'
            limit 3
          ), '')
       end as loan_items_id,
       case
        when loan_items.specimen_ref is null then
          loan_items.details
        else
           trim(
             CASE
             WHEN 'taxonomy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(taxon_name, '') != ''
               THEN
                 taxon_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'chronostratigraphy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(chrono_name, '') != ''
               THEN
                 chrono_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'lithostratigraphy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(litho_name, '') != ''
               THEN
                 litho_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'lithology' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(lithology_name, '') != ''
               THEN
                 lithology_name || E'\n'
             ELSE
               E'\n'
             END ||
             CASE
             WHEN 'mineralogy' = ANY (string_to_array(trim($5, '[]'), ', ')) AND coalesce(mineral_name, '') != ''
               THEN
                 mineral_name || E'\n'
             ELSE
               E'\n'
             END
           ,E'\n')
        end::text as loan_items_name,
        coalesce
        (
           (
             select trim(array_to_string(array_agg(comment), E'\n'), E'\n')
             from comments
             where referenced_relation = 'loan_items'
               and record_id = loan_items.id
               and notion_concerned = 'description'
             limit 3
           )
          ,
           (
             select trim(array_to_string(array_agg(comment), E'\n'), E'\n')
             from comments
             where referenced_relation = 'specimens'
                   and record_id = loan_items.specimen_ref
                   and notion_concerned = 'description'
             limit 3
           )
        ) as loan_items_description,
        coalesce
       (
            (
              select insurance_value
              from insurances
              where referenced_relation = 'loan_items'
                and record_id = loan_items.id
                and insurance_currency = '€'
                order by date_to desc
              limit 1
            )
          ,
            (
              select insurance_value
              from insurances
              where referenced_relation = 'specimens'
                    and record_id = loan_items.specimen_ref
                    and insurance_currency = '€'
              order by date_to desc
              limit 1
            )
        ) as loan_items_value,
       case
        when vals.val IN ('RBINS copy', 'Copie RBINS', 'RBINS copie') then
         loan_items.id::text
        else
         trim(coalesce(to_char(loans.from_date,'YY/MM-'),'') || loans.name || '-' || row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id))
       end as loan_phantom_id,
       case
        when vals.val IN ('RBINS copy', 'Copie RBINS', 'RBINS copie') then
          case
            when $7 = 'fr' then
              'ID item prêté: '
            when $7 = 'nl' then
              'ID geleend item: '
            else
              'Loan item ID: '
          end
          ||  loan_items.id || E'\n' ||
          case
            when $7 = 'fr' then
              'ID Fantôme: '
            else
              'Phantom ID: '
          end
          || trim(coalesce(to_char(loans.from_date,'YY/MM-'),'') || loans.name || '-' || row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id))
        else
          null::text
       end as loan_rbins_phantom_id
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
left join specimens on loan_items.specimen_ref = specimens.id
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
order by vals.val_index,loans.id,row_number() over (PARTITION BY vals.val ORDER BY vals.val_index, loans.id, loan_items.id);
$$
language sql;

alter table only loan_items drop constraint fk_loan_items_loan_ref;
alter table loan_items add constraint fk_loan_items_loan_ref FOREIGN KEY (loan_ref) REFERENCES loans ( id ) on DELETE cascade;

CREATE OR REPLACE FUNCTION fct_trk_log_table() RETURNS TRIGGER
AS $$
DECLARE
  user_id integer;
  track_level integer;
  track_fields integer;
  trk_id bigint;
  tbl_row RECORD;
  new_val varchar;
  old_val varchar;
  returnedRow RECORD;
BEGIN
  IF TG_OP IN ('INSERT', 'UPDATE') THEN
    returnedRow := NEW;
  ELSE
    returnedRow := OLD;
  END IF;
  SELECT COALESCE(CASE WHEN get_setting('darwin.track_level') = '' THEN NULL ELSE get_setting('darwin.track_level') END,'10')::integer INTO track_level;
  IF track_level = 0 THEN --NO Tracking
    RETURN returnedRow;
  ELSIF track_level = 1 THEN -- Track Only Main tables
    IF TG_TABLE_NAME::text NOT IN ('specimens', 'taxonomy', 'chronostratigraphy', 'lithostratigraphy',
      'mineralogy', 'lithology', 'people', 'loans', 'loan_items') THEN
      RETURN returnedRow;
    END IF;
  END IF;

  SELECT COALESCE(CASE WHEN get_setting('darwin.userid') = '' THEN NULL ELSE get_setting('darwin.userid') END,'0')::integer INTO user_id;
  IF user_id = 0 OR  user_id = -1 THEN
    RETURN returnedRow;
  END IF;

  IF TG_OP = 'INSERT' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'insert', now(), hstore(NEW)) RETURNING id into trk_id;
  ELSEIF TG_OP = 'UPDATE' THEN

    IF ROW(NEW.*) IS DISTINCT FROM ROW(OLD.*) THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value, old_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'update', now(), hstore(NEW), hstore(OLD)) RETURNING id into trk_id;
    ELSE
      RAISE info 'unnecessary update on table "%" and id "%"', TG_TABLE_NAME::text, NEW.id;
    END IF;

  ELSEIF TG_OP = 'DELETE' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, old_value)
      VALUES (TG_TABLE_NAME::text, OLD.id, user_id, 'delete', now(), hstore(OLD));
  END IF;

  RETURN NULL;
END;
$$
LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_trk_log_table_loans ON loans;
CREATE TRIGGER trg_trk_log_table_loans
AFTER INSERT OR UPDATE OR DELETE
ON loans
FOR EACH ROW
EXECUTE PROCEDURE fct_trk_log_table();

DROP TRIGGER IF EXISTS trg_trk_log_table_loan_items ON loan_items;
CREATE TRIGGER trg_trk_log_table_loan_items
AFTER INSERT OR UPDATE OR DELETE
ON loan_items
FOR EACH ROW
EXECUTE PROCEDURE fct_trk_log_table();

DROP TRIGGER IF EXISTS trg_trk_log_table_loan_status ON loan_status;
CREATE TRIGGER trg_trk_log_table_loan_status
AFTER INSERT OR UPDATE OR DELETE
ON loan_status
FOR EACH ROW
EXECUTE PROCEDURE fct_trk_log_table();

DROP TRIGGER IF EXISTS trg_trk_log_table_loan_rights ON loan_rights;
CREATE TRIGGER trg_trk_log_table_loan_rights
AFTER INSERT OR UPDATE OR DELETE
ON loan_rights
FOR EACH ROW
EXECUTE PROCEDURE fct_trk_log_table();

COMMIT;
