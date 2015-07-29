DROP FUNCTION rmca_create_missing_people_in_staging(integer);

CREATE OR REPLACE FUNCTION rmca_create_missing_people_in_staging(p_import_ref integer)
  RETURNS void AS
$BODY$
DECLARE
	curs1 record;
	tmpid int;
	
BEGIN 
	DROP TABLE if EXISTs tmp_people_import_rmca;
	CREATE TEMPORARY TABLE tmp_people_import_rmca(pk int, name varchar);
	RAISE NOTICE 'Different peoples %', (SELECT COUNT(DISTINCT formated_name) from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref);
	RAISE NOTICE 'linked specimens to be imported %', (SELECT COUNT(formated_name) from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref);
	FOR curs1 IN SELECT DISTINCT formated_name from staging a
		inner join codes b
		on referenced_relation='staging'
		and a.id=b.record_id
		inner join staging_people c
		ON
		c.record_id=a.id
		and c.referenced_relation='staging'
		where a.to_import='f' 
		and people_ref is null
		and import_ref=p_import_ref 
		/*UNION
		SELECT distinct formated_name from staging a
		inner join codes b
		on b.referenced_relation='staging'
		and a.id=b.record_id
		INNER JOIN identifications c
		ON c.record_id=a.id
		AND c.referenced_relation='staging'
		INNER JOIN
		staging_people d
		ON d.referenced_relation='identifications'
		AND c.id=d.record_id
		and people_ref is null
		where a.to_import='f' 

		and import_ref=p_import_ref
		*/
		

		LOOP
		
		RAISE NOTICE '%', curs1.formated_name;
		RAISE NOTICE 'people with this name %', (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) );
		RAISE NOTICE 'people split %',  (SELECT regexp_split_to_array(curs1.formated_name, ' '));
		IF  (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) )=0 THEN
		INSERT INTO people (family_name) VALUES (curs1.formated_name) RETURNING id INTO tmpid;
		INSERT INTO tmp_people_import_rmca (pk, name) VALUES(tmpid, curs1.formated_name);
		END IF;
		
	END LOOP;
	DELETE FROM tmp_people_import_rmca;

	RAISE NOTICE  'GO identifications';
	UPDATE staging_people SET people_ref=tmp_people_import_rmca.pk FROM (SELECT pk, name FROM tmp_people_import_rmca ) AS tmp_people_import_rmca WHERE staging_people.formated_name=tmp_people_import_rmca.name 
	and referenced_relation='staging'
		
		and people_ref is null
		and record_id IN (SELECT id FROM staging WHERE import_ref=p_import_ref AND to_import='f' )
		
		;




		FOR curs1 IN 
		SELECT distinct formated_name from staging a
		inner join codes b
		on b.referenced_relation='staging'
		and a.id=b.record_id
		INNER JOIN identifications c
		ON c.record_id=a.id
		AND c.referenced_relation='staging'
		INNER JOIN
		staging_people d
		ON d.referenced_relation='identifications'
		AND c.id=d.record_id
		and people_ref is null
		where a.to_import='f' 

		and import_ref=p_import_ref
		
		

		LOOP
		
		RAISE NOTICE '%', curs1.formated_name;
		RAISE NOTICE 'people ident with this name %', (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) );
		RAISE NOTICE 'people ident split %',  (SELECT regexp_split_to_array(curs1.formated_name, ' '));
		IF  (sELECt count(*) FROM people WHERE people.formated_name_indexed LIKE fulltoindex(curs1.formated_name, true) )=0 THEN
		RAISE NOTICE 'INSERT %', curs1.formated_name;
		INSERT INTO people (family_name) VALUES (curs1.formated_name) RETURNING id INTO tmpid;
		INSERT INTO tmp_people_import_rmca (pk, name) VALUES(tmpid, curs1.formated_name);
		END IF;
		
	END LOOP;
		UPDATE staging_people SET people_ref=tmp_people_import_rmca.id FROM (SELECT id, family_name FROM people) AS tmp_people_import_rmca 
		WHERE formated_name=tmp_people_import_rmca.family_name 
		--and referenced_relation='identifications'
		
		and people_ref is null
		/*and record_id IN (SELECT c.id FROM identifications c 
			INNER join staging a ON 
			 c.referenced_relation='staging' AND c.record_id=a.id
			 WHERE import_ref=p_import_ref AND a.to_import='f' )
		*/	 
		
		;
	DROP TABLE  tmp_people_import_rmca;

END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION rmca_create_missing_people_in_staging(integer)
  OWNER TO darwin2;
