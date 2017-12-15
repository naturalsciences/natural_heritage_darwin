-- View: v_loans_pentaho_receivers

-- DROP VIEW v_loans_pentaho_receivers;

CREATE OR REPLACE VIEW v_loans_pentaho_receivers AS 
 SELECT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, string_agg(c.id::character varying::text, ', '::text ORDER BY c.id) AS receiver_id, string_agg(c.formated_name::text, ', '::text ORDER BY c.id) AS receiver, btrim(string_agg(COALESCE(g.formated_name::text, h.entry::text), ', '::text ORDER BY c.id)) AS institution_receiver, btrim(btrim(NULLIF(string_agg(btrim(((((COALESCE(NULLIF(h.entry::text, ''::text) || ', '::text, ''::text) || COALESCE(NULLIF(h.extended_address::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.locality::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.po_box::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.region::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.zip_code::text, ''::text) || ', '::text, ''::text)), ', '::text ORDER BY c.id), ', '::text), ','::text)) AS address_institution, btrim(NULLIF(string_agg(COALESCE(NULLIF(h.country::text, ''::text), ''::text), ', '::text ORDER BY c.id), ', '::text), ','::text) AS country_institution, e.id AS sender_id, e.formated_name AS sender, string_agg(i.entry::text, ', '::text) AS receiver_email, string_agg(j.entry::text, ', '::text) AS receiver_tel, string_agg(f2.person_user_role::text, ', '::text) AS contact_sender_role
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

