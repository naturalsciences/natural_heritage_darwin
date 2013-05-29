begin;
set search_path=darwin2,public;
\i  createfunctions.sql

/***
* Move Codes from Spec to parts
**/
CREATE OR REPLACE FUNCTION move_code_onepart() RETURNS boolean
AS $$
DECLARE
  tmp_code RECORD;
BEGIN

   -- Move Codes in spec that have 1 parts OR 
   RAISE INFO 'Start moving Code from spec with 1 part';
   
   FOR tmp_code IN SELECT s.id as spec_id, p.id as part_id, c1.id as code_id FROM codes  c1
    INNER JOIN specimens s on s.id = c1.record_id AND c1.referenced_relation ='specimens'
    INNER JOIN specimen_individuals i on s.id = i.specimen_ref
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref

    where  (select count(*) from specimen_individuals i
      inner join specimen_parts p on i.id = p.specimen_individual_ref
      where i.specimen_ref = s.id) = 1
    LOOP
      
      BEGIN
        UPDATE codes set referenced_relation='specimen_parts', record_id = tmp_code.part_id WHERE id = tmp_code.code_id;
      EXCEPTION
        when unique_violation then
          DELETE FROM codes  where id = tmp_code.code_id;
      END;

   END LOOP;


   RAISE INFO 'Start moving Code from spec with 1 code';
   -- Move Codes in spec that have 1 code
   FOR tmp_code IN SELECT c1.*, p.id as p_id  FROM codes  c1
    INNER JOIN specimens s on s.id = c1.record_id AND c1.referenced_relation ='specimens'
    INNER JOIN specimen_individuals i on s.id = i.specimen_ref
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref

    where  (select count(*) from codes c2 where s.id = c2.record_id and c1.referenced_relation ='specimens') = 1
      LOOP
      BEGIN
      
      INSERT INTO codes(
            referenced_relation, record_id, code_category, code_prefix, 
            code_prefix_separator, code, code_suffix, code_suffix_separator, 
            full_code_indexed, full_code_order_by, code_date, code_date_mask, 
            code_num)
        VALUES('specimen_parts', tmp_code.p_id, tmp_code.code_category, tmp_code.code_prefix, 
            tmp_code.code_prefix_separator, tmp_code.code, tmp_code.code_suffix, tmp_code.code_suffix_separator, 
            tmp_code.full_code_indexed, tmp_code.full_code_order_by, tmp_code.code_date, tmp_code.code_date_mask, 
            tmp_code.code_num);

      EXCEPTION
        when unique_violation then
--          DELETE FROM codes  where id = tmp_code.code_id;
      END;
      
      DELETE FROM codes c1 where c1.referenced_relation ='specimens' AND 
        (select count(*) from codes c2 where s.id = c2.record_id and c1.referenced_relation ='specimens') = 1;

   END LOOP;
   
   
   --- Move suspicious Rbins Codes
   --- Remove Code that are already once in parts
   --- Remove ALL
   
   RETURN TRUE;
END;
$$
language plpgsql;


select move_code_onepart();



--- Move Comments (Indiv + spec)
--- Move Ext links  (Indiv + spec)
--- Move Properties  (Indiv + spec)
--- Merge Then move Indentifications (Indiv => Part)
--- REwrite ident to simple REF
--- REwrite code to simple REF ?
--- Cleanup Part auto copy in Collections
--- Move My saved Searches ==> chg subject
--- Move  my_widgets
--- Move Prefs
--- Move Flat Dict 
--- Watchout Biblio
--- Watchout catalogue people
--- Watchout Spec Host
--- Watchout Spec Accomp
--- Watchout Tools Method
--- Watchout Loans (should be ok)


/** Cleanup migration scripts ****/
drop function move_code_onepart();

rollback;