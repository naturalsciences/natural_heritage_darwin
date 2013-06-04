begin;
set search_path=darwin2,public;
\i  createfunctions.sql

/***
* Move Codes from Spec to parts
**/
CREATE OR REPLACE FUNCTION move_code_onepart() RETURNS boolean
AS $$
DECLARE
  tmp RECORD;
BEGIN

   -- Move Codes in spec that have 1 parts OR 
   RAISE INFO 'Start moving Code/* from spec with 1 part';
   
   FOR tmp IN SELECT *, p.id as part_id, s.id as spec_id from 
    specimens s
    INNER JOIN specimen_individuals i on s.id = i.specimen_ref
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref

    where  (select count(*) from specimen_individuals i
      inner join specimen_parts p on i.id = p.specimen_individual_ref
      where i.specimen_ref = s.id) = 1
    LOOP
      BEGIN
        UPDATE template_table_record_ref set referenced_relation='specimen_parts', record_id = tmp.part_id
          WHERE  referenced_relation ='specimens' AND record_id=tmp.spec_id;
      EXCEPTION
        when unique_violation then
         --IIIK Nothing
      END;

   END LOOP;


   RAISE INFO 'Start moving Code from spec with 1 code';
   -- Move Codes in spec that have 1 code
   FOR tmp IN SELECT c1.*, p.id as p_id  FROM codes  c1
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
        VALUES('specimen_parts', tmp.p_id, tmp.code_category, tmp.code_prefix, 
            tmp.code_prefix_separator, tmp.code, tmp.code_suffix, tmp.code_suffix_separator, 
            tmp.full_code_indexed, tmp.full_code_order_by, tmp.code_date, tmp.code_date_mask, 
            tmp.code_num);

      EXCEPTION
        when unique_violation then
--          DELETE FROM codes  where id = tmp.code_id;
      END;
   END LOOP;
   
   --- Move suspicious Rbins Codes
   --- Remove Code that are already once in parts
   --- Remove ALL
   RAISE INFO 'Remove codes';
   DELETE FROM codes c1 where c1.referenced_relation ='specimens' AND 
        (select count(*) from codes c2 where c1.record_id = c2.record_id and c1.referenced_relation ='specimens') = 1;
   delete from codes c where referenced_relation ='specimens';
   


    RAISE INFO 'Copy Comments from spec to all parts';
       FOR tmp IN SELECT c1.*, p.id as p_id  FROM comments  c1
    INNER JOIN specimens s on s.id = c1.record_id AND c1.referenced_relation ='specimens'
    INNER JOIN specimen_individuals i on s.id = i.specimen_ref
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
      LOOP
      BEGIN
      
      INSERT INTO comments(
            referenced_relation, record_id, notion_concerned, code_prefix, 
            comment_indexed)
        VALUES('specimen_parts', tmp.p_id, tmp.notion_concerned, tmp.comment, 
            tmp.comment_indexed);

      EXCEPTION
        when unique_violation then
          --pass
      END;
    END LOOP;
    DELETE FROM Comments c1 where c1.referenced_relation ='specimens';

    RAISE INFO 'Copy Comments from indiv to all parts';
       FOR tmp IN SELECT c1.*, p.id as p_id  FROM comments  c1
    INNER JOIN specimen_individuals i on c1.record_id=i.id AND c1.referenced_relation ='specimen_individuals'
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
      LOOP
      BEGIN
      
      INSERT INTO comments(
            referenced_relation, record_id, notion_concerned, code_prefix, 
            comment_indexed)
        VALUES('specimen_parts', tmp.p_id, tmp.notion_concerned, tmp.comment, 
            tmp.comment_indexed);

      EXCEPTION
        when unique_violation then
          --pass
      END;
    END LOOP;
    DELETE FROM Comments c1 where c1.referenced_relation ='specimen_individuals';

   RETURN TRUE;
END;
$$
language plpgsql;


select move_code_onepart();



--- Move Comments (Indiv + spec) ==> part?

--- Move Ext links  (Indiv + spec) => part
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