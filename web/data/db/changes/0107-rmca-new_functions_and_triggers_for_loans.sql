--Add fields:-------------------------------------------------------------
  ALTER TABLE loans ADD COLUMN address_receiver character varying;
  ALTER TABLE loans ADD COLUMN institution_receiver character varying;
  ALTER TABLE loans ADD COLUMN country_receiver character varying;
  ALTER TABLE loans ADD COLUMN zip_receiver character varying;
  ALTER TABLE loans ADD COLUMN city_receiver character varying(50);

--In trg_del_dict(), add code for loan:-----------------------------------
ELSIF TG_TABLE_NAME = 'loans' THEN
  PERFORM fct_del_in_dict('loans','country_receiver', oldfield.country_receiver, newfield.country_receiver);

--create trigger on table loans:-----------------------------------------
CREATE TRIGGER fct_cpy_trg_del_dict_people_addresses
  AFTER UPDATE OR DELETE
  ON loans
  FOR EACH ROW
  EXECUTE PROCEDURE trg_del_dict();

--In trg_ins_update_dict(), add code for loan:---------------------------
 ELSIF TG_TABLE_NAME = 'loans' THEN
      PERFORM fct_add_in_dict('loans','country_receiver', oldfield.country_receiver, newfield.country_receiver);

--create trigger on table loans:------------------------------------------
CREATE TRIGGER fct_cpy_trg_ins_update_dict_people_addresses
  AFTER INSERT OR UPDATE
  ON loans
  FOR EACH ROW
  EXECUTE PROCEDURE trg_ins_update_dict();  

--create new function:------------------------------------------------------
CREATE OR REPLACE FUNCTION trg_rmca_ins_update_people_and_address()
  RETURNS trigger AS
$BODY$
DECLARE
  newfield RECORD; 
  idres integer;
  
BEGIN
    newfield = NEW;
    IF TG_TABLE_NAME = 'loans' THEN
      idres = fct_rmca_add_in_people('f',newfield.institution_receiver);  
      RAISE NOTICE 'id=(%)', idres;
      PERFORM fct_rmca_add_in_people_address(idres,newfield.address_receiver,newfield.zip_receiver,newfield.city_receiver,newfield.country_receiver);  
    END IF;
  RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION trg_rmca_ins_update_people_and_address()
  OWNER TO darwin2;

--create trigger on table loans:------------------------------------------
CREATE TRIGGER fct_rmca_cpy_trg_ins_update_people_instit
  AFTER UPDATE
  ON loans
  FOR EACH ROW
  EXECUTE PROCEDURE trg_rmca_ins_update_people_and_address();

--create new function:------------------------------------------------------
CREATE OR REPLACE FUNCTION fct_rmca_add_in_people(ref_isphysical boolean, ref_name text)
  RETURNS integer AS
$BODY$
DECLARE
  query_str varchar;
  query_str2 varchar;
  ref_name2 varchar;
  idres integer;
  pos integer;

BEGIN
  IF ref_name is NULL THEN
    RETURN 0;
  END IF;
  --ref_name = "Musée National d'Histoire Naturelle Paris§§§4044";
  pos = position('§§§' in ref_name);
  IF pos > 0 THEN
	ref_name2 = substring(ref_name from 1 for pos-1);
  ELSE
	ref_name2 = ref_name;
  END IF;
  
  query_str := 
    
    ' INSERT INTO people (is_physical, formated_name, family_name)
    (
      SELECT ' || quote_literal(ref_isphysical) || ' , ' || quote_literal(ref_name2) || ' , ' || quote_literal(ref_name2)|| ' WHERE NOT EXISTS
      (SELECT id FROM people WHERE
        is_physical = ' || quote_literal(ref_isphysical) || '
        AND formated_name = ' || quote_literal(ref_name2) || '
        AND family_name = ' || quote_literal(ref_name2) || ')
    );'

    ;
    RAISE NOTICE 'query_str=(%)', query_str;
    EXECUTE query_str;

    idres := (SELECT id FROM people WHERE formated_name = ref_name2);
    RETURN idres;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fct_rmca_add_in_people(boolean, text)
  OWNER TO darwin2;
--create new function:------------------------------------------------------
CREATE OR REPLACE FUNCTION fct_rmca_add_in_people_address(ref_user integer, address_ref text, zip_ref text, city_ref text, country_ref text)
  RETURNS boolean AS
$BODY$
DECLARE
  query_str varchar;

BEGIN
  IF ref_user is NULL THEN
    RETURN true;
  END IF;
    query_str := 
    
    ' INSERT INTO people_addresses (person_user_ref, entry, zip_code, locality, country)
    (
      SELECT ' || quote_literal(ref_user) || ' , ' || quote_literal(address_ref) || ' , ' || quote_literal(zip_ref) || ' , ' || quote_literal(city_ref) || ' , ' || quote_literal(country_ref)|| ' WHERE NOT EXISTS
      (SELECT id FROM people_addresses WHERE
        person_user_ref = ' || quote_literal(ref_user) || '
        AND entry = ' || quote_literal(address_ref) || ')
    );'

    ;
    EXECUTE query_str;

    RETURN true;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fct_rmca_add_in_people_address(integer, text, text, text, text)
  OWNER TO darwin2;


--create new function:------------------------------------------------------
CREATE OR REPLACE FUNCTION fct_rmca_instit_address_from_loan_actor(IN id integer)
  RETURNS TABLE(id_instit text, instit text, entry text, zip text, locality text, address text, country text) AS
$BODY$

SELECT 
  (p.formated_name || '§§§' || pr.person_1_ref) AS Id_instit, p.formated_name, COALESCE(a.entry::text),COALESCE(a.zip_code::text), COALESCE(a.locality::text), COALESCE(a.entry::text) || ' ' ||  COALESCE(a.zip_code::text) || ' ' || COALESCE(a.locality::text)  AS address,  COALESCE(a.country::text)
FROM 
  people_relationships pr
LEFT JOIN people p
	ON p.id = pr.person_1_ref
LEFT JOIN people_addresses a
	ON pr.person_1_ref = a.person_user_ref
WHERE 
  pr.person_2_ref = $1 AND
  (pr.relationship_type = 'works for' OR 
  pr.relationship_type = 'belongs to')
  
$BODY$
  LANGUAGE sql VOLATILE
  COST 100
  ROWS 1000;
ALTER FUNCTION fct_rmca_instit_address_from_loan_actor(integer)
  OWNER TO darwin2;

--change view for pdf-------------------------------------------------------
-- View: v_loans_pentaho_receivers

-- DROP VIEW v_loans_pentaho_receivers;

CREATE OR REPLACE VIEW v_loans_pentaho_receivers AS 
 SELECT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, string_agg(c.id::character varying::text, ', '::text ORDER BY c.id) AS receiver_id, string_agg(c.formated_name::text, ', '::text ORDER BY c.id) AS receiver, 
        CASE
            WHEN COALESCE(btrim(a.institution_receiver::text), ''::text) = ''::text THEN btrim(string_agg(COALESCE(g.formated_name::text, h.entry::text), ', '::text ORDER BY c.id))::character varying::text
            ELSE 
            CASE "position"(a.institution_receiver::text, '§§§'::text)
                WHEN 0 THEN a.institution_receiver::text
                ELSE "substring"(a.institution_receiver::text, 1, "position"(a.institution_receiver::text, '§§§'::text) - 1)
            END
        END AS institution_receiver, 
        CASE
            WHEN COALESCE(btrim(a.address_receiver::text), ''::text) = ''::text THEN btrim(btrim(NULLIF(string_agg(btrim(((((COALESCE(NULLIF(h.entry::text, ''::text) || ', '::text, ''::text) || COALESCE(NULLIF(h.extended_address::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.locality::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.po_box::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.region::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.zip_code::text, ''::text) || ', '::text, ''::text)), ', '::text ORDER BY c.id), ', '::text), ','::text))
            ELSE btrim(btrim(NULLIF(string_agg(btrim((COALESCE(NULLIF(a.address_receiver::text, ''::text) || ', '::text, ''::text) || COALESCE(NULLIF(a.zip_receiver::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(a.city_receiver::text, ''::text) || ', '::text, ''::text)), ', '::text ORDER BY c.id), ', '::text), ','::text))
        END AS address_institution, 
        CASE
            WHEN COALESCE(btrim(a.country_receiver::text), ''::text) = ''::text THEN btrim(NULLIF(string_agg(COALESCE(NULLIF(h.country::text, ''::text), ''::text), ', '::text ORDER BY c.id), ', '::text), ','::text)::character varying
            ELSE a.country_receiver
        END AS country_institution, e.id AS sender_id, e.formated_name AS sender, string_agg(i.entry::text, ', '::text) AS receiver_email, string_agg(j.entry::text, ', '::text) AS receiver_tel, string_agg(f2.person_user_role::text, ', '::text) AS contact_sender_role
   FROM loans a
   LEFT JOIN catalogue_people b ON a.id = b.record_id AND b.referenced_relation::text = 'loans'::text AND b.people_type::text = 'receiver'::text AND b.order_by = (( SELECT min(cp.order_by) AS min
      FROM catalogue_people cp
     WHERE cp.record_id = b.record_id AND cp.referenced_relation::text = 'loans'::text AND cp.people_type::text = 'receiver'::text))
   LEFT JOIN people c ON c.id = b.people_ref
   LEFT JOIN catalogue_people d ON a.id = d.record_id AND d.referenced_relation::text = 'loans'::text AND d.people_type::text = 'sender'::text
   LEFT JOIN people e ON e.id = d.people_ref
   LEFT JOIN people_relationships f ON (f.relationship_type::text = 'works for'::text OR f.relationship_type::text = 'belongs to'::text) AND c.id = f.person_2_ref
   LEFT JOIN v_loan_detail_role_person f1 ON f1.referenced_relation::text = 'loans'::text AND f1.people_type::text = 'sender'::text AND f1.record_id = a.id AND f1.contact = true AND f1.people_ref = d.people_ref
   LEFT JOIN people_relationships f2 ON (f2.relationship_type::text = 'works for'::text OR f2.relationship_type::text = 'belongs to'::text) AND f2.person_2_ref = f1.people_ref
   LEFT JOIN people g ON f.person_1_ref = g.id
   LEFT JOIN people_addresses h ON COALESCE(g.id, c.id) = h.person_user_ref
   LEFT JOIN people_comm i ON i.person_user_ref = b.people_ref AND i.comm_type::text = 'e-mail'::text
   LEFT JOIN people_comm j ON j.person_user_ref = b.people_ref AND j.comm_type::text = 'phone/fax'::text
  GROUP BY a.id, e.id;

ALTER TABLE v_loans_pentaho_receivers
  OWNER TO darwin2;

----------------------------------------------
-- View: v_loans_pentaho_general

-- DROP VIEW v_loans_pentaho_general;

CREATE OR REPLACE VIEW v_loans_pentaho_general AS 
 SELECT DISTINCT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, 

	COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'loan_at_your_request'::text
         LIMIT 1), 'no'::character varying) AS loan_at_your_request, 

         COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'gift'::text
         LIMIT 1), 'no'::character varying) AS gift,


         COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'in_exchange'::text
         LIMIT 1), 'no'::character varying) AS in_exchange, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'loan_for_identification_our_request'::text
         LIMIT 1), 'no'::character varying) AS loan_for_identification_our_request, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'return_of_material_sent_for_id'::text
         LIMIT 1), 'no'::character varying) AS return_of_material_sent_for_id, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'return_of_borrowed_material'::text
         LIMIT 1), 'no'::character varying) AS return_of_borrowed_material, 
         

         COALESCE(( SELECT 
                CASE lower(properties.property_type::text)
                    WHEN 'sent_by_surface'::text THEN 'Sent by surface'::text
                    WHEN 'sent_by_airmail'::text THEN 'Sent by airmail'::text
                    ELSE NULL::text
                END AS "case"
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND (lower(properties.property_type::text) = 'sent_by_surface'::text OR lower(properties.property_type::text) = 'sent_by_airmail'::text)
         LIMIT 1), ''::character varying::text) AS shipping_type, transporter.formated_name AS transporter, to_char(i.modification_date_time, 'YYYY-MM-DD'::text) AS registration_date, (j.insurance_value || ' '::text) || j.insurance_currency::text AS insurance, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'packages_count'::text
         LIMIT 1), '1'::character varying) AS packages_count, COALESCE(( SELECT (properties.lower_value::text || ' '::text) || properties.property_unit::text
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'weight'::text
         LIMIT 1), ''::text) AS weight
   FROM loans a
   LEFT JOIN collection_maintenance b ON a.id = b.record_id AND b.referenced_relation::text = 'loans'::text AND (b.action_observation::text = 'sent_by_surface'::text OR b.action_observation::text = 'sent_by_airmail'::text)
   LEFT JOIN catalogue_people c ON a.id = c.record_id AND c.referenced_relation::text = 'loans'::text AND c.people_type::text = 'receiver'::text
   LEFT JOIN people d ON d.id = c.people_ref
   LEFT JOIN catalogue_people e ON a.id = e.record_id AND e.referenced_relation::text = 'loans'::text AND e.people_type::text = 'sender'::text
   LEFT JOIN people f ON f.id = e.people_ref
   LEFT JOIN catalogue_people g ON a.id = g.record_id AND g.referenced_relation::text = 'loans'::text AND g.people_type::text = 'receiver'::text AND (g.people_sub_type::integer::bit(8) & B'01000000'::"bit")::integer::boolean = true
   LEFT JOIN people transporter ON g.people_ref = transporter.id
   LEFT JOIN loan_status i ON a.id = i.loan_ref AND i.status::text = 'new'::text
   LEFT JOIN insurances j ON a.id = j.record_id AND j.referenced_relation::text = 'loans'::text
  WHERE i.modification_date_time = (( SELECT min(loan_status.modification_date_time) AS min
   FROM loan_status
  WHERE loan_status.loan_ref = i.loan_ref AND loan_status.status::text = 'new'::text))

  ;

ALTER TABLE v_loans_pentaho_general
  OWNER TO darwin2;
----------------------------------------------------------------------------
-- View: v_loan_details_for_pentaho (old version with text formatting  of counts in details)

-- DROP VIEW v_loan_details_for_pentaho;

CREATE OR REPLACE VIEW v_loan_details_for_pentaho AS 
 SELECT a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, array_agg(d.lower_value) AS array_agg, 

 btrim((COALESCE('Tot. nbr:'::text || b.specimen_count_min::text, ''::text) || 
        CASE to_number(COALESCE(b.specimen_count_males_min::text, '0'::text), '9G'::text) + to_number(COALESCE(b.specimen_count_females_min::text, '0'::text), '9G'::text) + to_number(COALESCE(b.specimen_count_juveniles_min::text, '0'::text), '9G'::text)
            WHEN 0 THEN ''::text
            ELSE 
            CASE char_length((COALESCE(b.specimen_count_males_min::text || 'M'::text, ''::text) || COALESCE(b.specimen_count_females_min::text || 'F'::text, ''::text)) || COALESCE(b.specimen_count_juveniles_min::text || ' Juv'::text, ''::text))
                WHEN 0 THEN ''::text
                ELSE ((('('::text || 
                CASE char_length(COALESCE(b.specimen_count_females_min::text || 'F'::text, ''::text) || COALESCE(b.specimen_count_juveniles_min::text || 'Juv'::text, ''::text))
                    WHEN 0 THEN COALESCE(b.specimen_count_males_min::text || 'M'::text, ''::text)
                    ELSE COALESCE(b.specimen_count_males_min::text || 'M + '::text, ''::text)
                END) || 
                CASE char_length(COALESCE(b.specimen_count_juveniles_min::text || 'Juv'::text, ''::text))
                    WHEN 0 THEN COALESCE(b.specimen_count_females_min::text || 'F'::text, ''::text)
                    ELSE COALESCE(b.specimen_count_females_min::text || 'F + '::text, ''::text)
                END) || COALESCE(b.specimen_count_juveniles_min::text || 'Juv'::text, ''::text)) || ')'::text
            END
        END) || COALESCE(chr(10) || a.details::text)) AS detail_loan, 

        b.type, f.category, f.specimen_part, f.specimen_status, btrim(((COALESCE(f.category, ''::character varying)::text || COALESCE(', '::text || NULLIF(replace(f.specimen_part::text, 'specimen'::text, ''::text), ''::text), ''::text)) || COALESCE(', '::text || NULLIF(f.specimen_status::text, ''::text), ''::text)) || COALESCE(', '::text || NULLIF(replace(b.type::text, 'specimen'::text, ''::text), ''::text), ''::text)) AS loan_remarks, e.id AS loan_id, e.name AS loan_name, b.collection_ref
   FROM loan_items a
   JOIN specimens b ON a.specimen_ref = b.id
   JOIN codes c ON b.id = c.record_id AND c.referenced_relation::text = 'specimens'::text AND c.code_category::text = 'main'::text
   LEFT JOIN properties d ON b.id = d.record_id AND d.referenced_relation::text = 'specimens'::text
   JOIN loans e ON a.loan_ref = e.id
   LEFT JOIN storage_parts f ON b.id = f.specimen_ref
  GROUP BY a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, b.type, f.category, f.specimen_part, f.specimen_status, e.id, b.collection_ref, b.specimen_count_males_min, b.specimen_count_females_min, b.specimen_count_juveniles_min, b.specimen_count_min;

ALTER TABLE v_loan_details_for_pentaho
  OWNER TO darwin2;
--------------------------------------------------------------------------------------
-- View: v_loan_details_for_pentaho

-- DROP VIEW v_loan_details_for_pentaho;

CREATE OR REPLACE VIEW v_loan_details_for_pentaho AS 
 SELECT a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, array_agg(d.lower_value) AS array_agg, 
	regexp_replace(regexp_replace((((COALESCE(a.details::text || chr(10)) || a.specimen_count::text) || chr(10)) || a.specimen_part::text) || chr(10), '^\s+'::text, ''::text), '\s+$'::text, ''::text) AS detail_loan, 
	b.type, f.category, f.specimen_part, f.specimen_status, btrim(((COALESCE(f.category, ''::character varying)::text || COALESCE(', '::text || NULLIF(replace(f.specimen_part::text, 'specimen'::text, ''::text), ''::text), ''::text)) || COALESCE(', '::text || NULLIF(f.specimen_status::text, ''::text), ''::text)) || COALESCE(', '::text || NULLIF(replace(b.type::text, 'specimen'::text, ''::text), ''::text), ''::text)) AS loan_remarks, e.id AS loan_id, e.name AS loan_name, b.collection_ref
   FROM loan_items a
   JOIN specimens b ON a.specimen_ref = b.id
   JOIN codes c ON b.id = c.record_id AND c.referenced_relation::text = 'specimens'::text AND c.code_category::text = 'main'::text
   LEFT JOIN properties d ON b.id = d.record_id AND d.referenced_relation::text = 'specimens'::text
   JOIN loans e ON a.loan_ref = e.id
   LEFT JOIN storage_parts f ON b.id = f.specimen_ref
  WHERE a.loan_ref = 118
  GROUP BY a.id, a.loan_ref, a.ig_ref, a.from_date, a.to_date, a.specimen_ref, a.details, b.taxon_name, c.code, b.type, f.category, f.specimen_part, f.specimen_status, e.id, b.collection_ref, b.specimen_count_males_min, b.specimen_count_females_min, b.specimen_count_juveniles_min, b.specimen_count_min;

ALTER TABLE v_loan_details_for_pentaho
  OWNER TO darwin2;


