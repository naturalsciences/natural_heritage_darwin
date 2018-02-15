-- View: v_loans_pentaho_general

-- DROP VIEW v_loans_pentaho_general;

CREATE OR REPLACE VIEW v_loans_pentaho_general AS 
 SELECT DISTINCT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'loans'::text AND properties.record_id = a.id AND properties.property_type::text = 'loan_at_your_request'::text
         LIMIT 1), 'no'::character varying) AS loan_at_your_request, COALESCE(( SELECT properties.lower_value
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
         LIMIT 1), 'no'::character varying) AS return_of_borrowed_material, COALESCE(( SELECT properties.lower_value
           FROM properties
          WHERE properties.referenced_relation::text = 'gift'::text AND properties.record_id = a.id AND properties.property_type::text = 'gift'::text
         LIMIT 1), 'no'::character varying) AS gift, COALESCE(( SELECT 
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
   --ftheeten 2017 11 30
   WHERE i.modification_date_time = (( SELECT min(loan_status.modification_date_time) AS min
										FROM loan_status
										WHERE loan_status.loan_ref = i.loan_ref AND loan_status.status::text = 'new'::text));

ALTER TABLE v_loans_pentaho_general
  OWNER TO darwin2;


CREATE UNIQUE INDEX loan_status_loan_ref_user_ref_status_idx
  ON loan_status
  USING btree
  (loan_ref , user_ref , status COLLATE pg_catalog."default" )
  WHERE status::text = 'new'::text OR status::text = 'rejected'::text;
COMMENT ON INDEX loan_status_loan_ref_user_ref_status_idx
  IS 'added by rmca (ftheeten and jmherpers 2017 11 29)
';

