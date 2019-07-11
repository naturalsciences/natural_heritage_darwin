-- Function: rmca_create_links_between_labels(integer)

-- DROP FUNCTION rmca_create_links_between_labels(integer);

CREATE OR REPLACE FUNCTION rmca_create_links_between_labels(p_coll_ref integer)
  RETURNS void AS
$BODY$
BEGIN 



INSERT INTO specimens_relationships
(specimen_ref, relationship_type, unit_type, specimen_related_ref, unit)
SELECT   bsp.id, 'other_identification', 'specimens', csp.id, '%'
FROM specimens a
  INNER JOIN 
	codes b
	    ON a.id=b.record_id
	      AND b.referenced_relation='specimens'
	        and a.collection_ref=p_coll_ref 
	        and b.code_category='main'
	        
    INNER JOIN 
	codes c
	    ON c.code similar to regexp_replace(b.code, '\_id\_[a-z]','', 'g')||'_id_[a-z]'
	      AND c.referenced_relation='specimens'
	         
	        and c.code_category='main'
                and b.id<>c.id
     INNER JOIN specimens bsp
     ON bsp.id=b.record_id
      and bsp.collection_ref=p_coll_ref 
     INNER JOIN specimens csp
     ON csp.id=c.record_id   
     and
     csp.collection_ref=p_coll_ref    
where b.code similar to '%\_id\_[a-z]';


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

