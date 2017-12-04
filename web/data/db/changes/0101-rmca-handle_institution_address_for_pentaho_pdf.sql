CREATE OR REPLACE VIEW v_loans_pentaho_receivers AS 
 SELECT a.id, a.name, a.description, a.search_indexed, a.from_date, a.to_date, a.extended_to_date, string_agg(c.id::character varying::text, ', '::text ORDER BY c.id) AS receiver_id, 
 
 string_agg(c.formated_name::text, ', '::text ORDER BY c.id) AS receiver, 
 
 
btrim(string_agg(COALESCE(g.formated_name::text, h.entry::text), ', '::text ORDER BY c.id)) AS institution_receiver, 

btrim(btrim(NULLIF(string_agg(btrim(((((COALESCE( NULLIF(h.entry::text, ''::text) || ', '::text, ''::text) || COALESCE(NULLIF(h.extended_address::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.locality::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.po_box::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.region::text, ''::text) || ', '::text, ''::text)) || COALESCE(NULLIF(h.zip_code::text, ''::text) || ', '::text, ''::text)), ', '::text ORDER BY c.id), ', '::text), ','::text)) AS address_institution
 
 
 
 , btrim(NULLIF(string_agg(COALESCE(NULLIF(h.country::text, ''::text), ''::text), ', '::text ORDER BY c.id), ', '::text), ','::text) AS country_institution, e.id AS sender_id, e.formated_name AS sender
   FROM loans a
   LEFT JOIN catalogue_people b ON a.id = b.record_id AND b.referenced_relation::text = 'loans'::text AND b.people_type::text = 'receiver'::text
   LEFT JOIN people c ON c.id = b.people_ref
   LEFT JOIN catalogue_people d ON a.id = d.record_id AND d.referenced_relation::text = 'loans'::text AND d.people_type::text = 'sender'::text
   LEFT JOIN people e ON e.id = d.people_ref
   LEFT JOIN people_relationships f ON (f.relationship_type::text = 'works for'::text OR f.relationship_type::text = 'belongs to'::text) AND c.id = f.person_2_ref
   LEFT JOIN people g ON f.person_1_ref = g.id
   LEFT JOIN people_addresses h ON COALESCE(g.id, c.id) = h.person_user_ref
  GROUP BY a.id, e.id;
