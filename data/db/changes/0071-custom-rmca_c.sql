CREATE OR REPLACE FUNCTION rmca_create_links_between_labels(p_coll_ref integer)
  RETURNS void AS
$BODY$
BEGIN 



INSERT INTO specimens_relationships
(specimen_ref, relationship_type, unit_type, specimen_related_ref, unit)
SELECT  old_identification_id, 'other_identification', 'specimens', new_identification_id, '%'
FROM (
SELECT a.id as old_identification_id, b.code, 
(SELECT aa.id  FROM specimens aa
  INNER JOIN codes ba
	    ON aa.id=ba.record_id
	      AND ba.referenced_relation='specimens'
	        and aa.collection_ref=10 and code_category='main'
  inner  JOIN properties ca
	ON aa.id=ca.record_id
	  AND ca.referenced_relation='specimens'
	 AND ca.property_type='label_created_on'

  where ba.code similar to regexp_replace(b.code, '\_id\_[a-z]','', 'g')||'%' 

  order by ca.lower_value::int asc limit 1
  ) as new_identification_id
 FROM specimens a
  INNER JOIN codes b
	    ON a.id=b.record_id
	      AND b.referenced_relation='specimens'
	        and a.collection_ref=p_coll_ref and code_category='main'
	         where code similar to '%\_id\_[b-z]'

) as foo;

/* effacer le diff du code */

UPDATE codes SET code=code_without_label_diff

FROM (

SELECT b.id, b.code, regexp_replace(b.code,  '\_id\_[a-z]$', '', 'g') as code_without_label_diff FROM specimens a
  INNER JOIN codes b
	    ON a.id=b.record_id
	      AND b.referenced_relation='specimens'
	        and a.collection_ref=p_coll_ref and code_category='main'
	         where code similar to '%\_id\_[a-z]') foo
	         where foo.id=codes.id


;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION rmca_create_links_between_labels(integer)
  OWNER TO darwin2;
