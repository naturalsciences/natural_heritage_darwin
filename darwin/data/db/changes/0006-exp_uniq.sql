BEGIN;

CREATE OR REPLACE FUNCTION migrate_expeditions() RETURNS boolean
AS $$
DECLARE
 exp_arr integer[];
 exp_first_id integer;
 exp_id integer;
 tmp_id integer;
 
 cnt integer default 0;
 i integer default 0;
BEGIN
   select count (*) from
     (select 1 FROM expeditions GROUP BY name_indexed, expedition_from_date, expedition_from_date_mask, expedition_to_date, expedition_to_date_mask HAVING count(id)>1) as x
     into cnt;

   FOR exp_arr IN 
    (SELECT array_accum(id) FROM expeditions GROUP BY name_indexed, expedition_from_date, expedition_from_date_mask, expedition_to_date, expedition_to_date_mask HAVING count(id)>1 )
    LOOP
    -- ARRAY OF EXP dupped
    -- SELECT FIRST ID AND Remove it from array
    SELECT unnest(exp_arr) LIMIT 1 INTO exp_first_id;
    SELECT fct_remove_array_elem(exp_arr, exp_first_id) into exp_arr;

    FOR exp_id IN (select unnest(exp_arr) ) LOOP
      BEGIN
        UPDATE catalogue_people SET record_id = exp_first_id WHERE record_id = exp_id AND referenced_relation =  'expeditions';
      EXCEPTION WHEN unique_violation THEN
        FOR tmp_id in ( SELECT id FROM catalogue_people WHERE record_id = exp_id AND referenced_relation =  'expeditions') LOOP
          BEGIN 
            UPDATE catalogue_people SET record_id = exp_first_id WHERE id = tmp_id;
          EXCEPTION WHEN unique_violation THEN
            DELETE from catalogue_people WHERE id = tmp_id;
          END;
        END LOOP;
      END;
    END LOOP;
    
    --UPDATE catalogue_people SET record_id = exp_first_id WHERE record_id = ANY(exp_arr) AND referenced_relation =  'expeditions';
    UPDATE ext_links SET record_id = exp_first_id WHERE record_id = ANY(exp_arr) AND referenced_relation =  'expeditions'; --should be 0
    UPDATE comments SET record_id = exp_first_id WHERE record_id = ANY(exp_arr) AND referenced_relation =  'expeditions'; --should be 0

    -- MOVE SPECIMENS
    UPDATE specimens SET expedition_ref = exp_first_id WHERE expedition_ref = ANY(exp_arr);

    -- DELETE Expeditions
    DELETE FROM expeditions where id = ANY(exp_arr);
    RAISE INFO 'Done: % / %', i, cnt;
    i := i +1;
   END LOOP;
   RETURN TRUE;
END;
$$ LANGUAGE plpgsql;

SELECT migrate_expeditions();

ALTER TABLE expeditions add constraint unq_expeditions unique (name_indexed, expedition_from_date, expedition_from_date_mask, expedition_to_date, expedition_to_date_mask);

COMMIT;
