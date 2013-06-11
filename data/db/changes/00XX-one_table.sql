begin;
set search_path=darwin2,public;
\i  createfunctions.sql

/***
* Move Codes from Spec to parts
**/
CREATE OR REPLACE FUNCTION move_refs() RETURNS boolean
AS $$
DECLARE
  tmp RECORD;
  tmp2 RECORD;
  source_ref INTEGER;
BEGIN
   RAISE INFO 'Insert missing indiv';
    INSERT INTO specimen_individuals(specimen_ref)
    (
      SELECT id from specimens s
        where not exists( select 1 from specimen_individuals i where i.specimen_ref = s.id)
    );

    RAISE INFO 'Insert missing parts';
    INSERT INTO specimen_parts(specimen_individual_ref)
    (
      SELECT id from specimen_individuals i
      where not exists( select 1 from specimen_parts p where i.id = p.specimen_individual_ref)
    );

   RAISE INFO 'Start moving template_ref from spec with 1 part';
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


   RAISE INFO 'Start moving template_ref from ind with 1 part';
   FOR tmp IN SELECT *, p.id as part_id, i.id as ind_id from 
    specimen_individuals i
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref

    where  (select count(*) from specimen_parts p
      where i.specimen_ref = s.id) = 1
    LOOP
      BEGIN
        UPDATE template_table_record_ref set referenced_relation='specimen_parts', record_id = tmp.part_id
          WHERE  referenced_relation ='specimen_individuals' AND record_id=tmp.spec_id;
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


/**********/


   source_ref := -1;
   RAISE INFO 'Start moving template_ref from spec ';
   FOR tmp IN SELECT s.id as source_ref, p.id as p_id from specimens s
     INNER JOIN specimen_individuals i on s.id = i.specimen_ref
     INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
    LOOP

      INSERT INTO comments(referenced_relation, record_id, notion_concerned, code_prefix, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, notion_concerned, comment, comment_indexed
          FROM comments c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref  );


      INSERT INTO ext_links(referenced_relation, record_id, url, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, url, comment, comment_indexed
          FROM ext_links c WHERE c.referenced_relation='specimens' and record_id=tmp.source_ref
            AND not exists( 
              select 1 from ext_links c2 where  referenced_relation='specimens' and record_id=tmp.source_ref and c2.url=c.url
             ) 
         );

         
   FOR tmp2 IN SELECT * from catalogue_properties c where referenced_relation='specimens' and record_id=tmp.source_ref
   LOOP
   
      INSERT INTO catalogue_properties(referenced_relation, record_id, 
        property_type, property_sub_type, 
        property_sub_type_indexed, property_qualifier, property_qualifier_indexed, 
        date_from_mask, date_from, date_to_mask, date_to, property_unit, 
        property_accuracy_unit, property_method, property_method_indexed, 
        property_tool, property_tool_indexed
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.property_type, tmp2.property_sub_type,
          tmp2.property_sub_type_indexed, tmp2.property_qualifier, tmp2.property_qualifier_indexed, 
          tmp2.date_from_mask, tmp2.date_from, tmp2.date_to_mask, tmp2.date_to, tmp2.property_unit, 
          tmp2.property_accuracy_unit, tmp2.property_method, tmp2.property_method_indexed, 
          tmp2.property_tool, tmp2.property_tool_indexed
         );
      
      INSERT INTO properties_values(
          property_ref, property_value, property_value_unified, property_accuracy, 
          property_accuracy_unified)
        (
          SELECT currval('catalogue_properties_id_seq'), property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified from properties_values where property_ref = tmp2.id
        );
            
      --END LOOP;
   END LOOP;
   
   FOR tmp2 IN SELECT * from identifications c where referenced_relation='specimens' and record_id=tmp.source_ref
   LOOP

      INSERT INTO identifications (referenced_relation, record_id, 
        notion_concerned, notion_date, 
        notion_date_mask, value_defined, value_defined_indexed, determination_status, 
        order_by
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.notion_concerned, tmp2.notion_date, 
          tmp2.notion_date_mask, tmp2.value_defined, tmp2.value_defined_indexed, tmp2.determination_status, 
          tmp2.order_by
         );
      
        INSERT INTO catalogue_people(referenced_relation, record_id, people_type, people_sub_type, 
            order_by, people_ref
            )
          (SELECT currval('identifications_id_seq'), referenced_relation, record_id, people_type, people_sub_type, 
            order_by, people_ref
            from catalogue_people where record_id = tmp2.id and referenced_relation = 'identifications'
          );
            
   END LOOP;
 END LOOP;

  RAISE INFO 'Delete spec comments';
  DELETE FROM comments where referenced_relation='specimens';
  RAISE INFO 'Delete spec ext_links';
  DELETE FROM ext_links where referenced_relation='specimens';
  RAISE INFO 'Delete spec properties';
  DELETE FROM catalogue_properties where referenced_relation='specimens';
  RAISE INFO 'Delete spec ident';
  DELETE FROM identifications where referenced_relation='specimens';




   source_ref := -1;
   RAISE INFO 'Start moving template_ref from indiv ';
   FOR tmp IN SELECT i.id as source_ref, p.id as p_id from specimen_individuals
    INNER JOIN specimen_parts p on i.id = p.specimen_individual_ref
    LOOP

      INSERT INTO comments(referenced_relation, record_id, notion_concerned, code_prefix, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, notion_concerned, comment, comment_indexed
          FROM comments c WHERE c.referenced_relation='specimen_individuals' and record_id=tmp.source_ref  );


      INSERT INTO ext_links(referenced_relation, record_id, url, comment, comment_indexed) 
        (SELECT 'specimen_parts', tmp.p_id, url, comment, comment_indexed
          FROM ext_links c WHERE c.referenced_relation='specimen_individuals' and record_id=tmp.source_ref
            AND not exists( 
              select 1 from ext_links c2 where  referenced_relation='specimen_individuals' and record_id=tmp.source_ref and c2.url=c.url
             ) 
         );

         
   FOR tmp2 IN SELECT * from catalogue_properties c where referenced_relation='specimen_individuals' and record_id=tmp.source_ref
   LOOP

      INSERT INTO catalogue_properties(referenced_relation, record_id, 
        property_type, property_sub_type, 
        property_sub_type_indexed, property_qualifier, property_qualifier_indexed, 
        date_from_mask, date_from, date_to_mask, date_to, property_unit, 
        property_accuracy_unit, property_method, property_method_indexed, 
        property_tool, property_tool_indexed
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.property_type, tmp2.property_sub_type,
          tmp2.property_sub_type_indexed, tmp2.property_qualifier, tmp2.property_qualifier_indexed, 
          tmp2.date_from_mask, tmp2.date_from, tmp2.date_to_mask, tmp2.date_to, tmp2.property_unit, 
          tmp2.property_accuracy_unit, tmp2.property_method, tmp2.property_method_indexed, 
          tmp2.property_tool, tmp2.property_tool_indexed
         );
      
        INSERT INTO properties_values(
            property_ref, property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified)
        (
          SELECT currval('catalogue_properties_id_seq'), property_value, property_value_unified, property_accuracy, 
            property_accuracy_unified from properties_values where property_ref = tmp2.id
        );
            
   END LOOP;
   --END LOOP;

   FOR tmp2 IN SELECT * from identifications c where referenced_relation='specimen_individuals' and record_id=tmp.source_ref
   LOOP

      INSERT INTO identifications (referenced_relation, record_id, 
        notion_concerned, notion_date, 
        notion_date_mask, value_defined, value_defined_indexed, determination_status, 
        order_by
      ) 
        VALUES ('specimen_parts', tmp.p_id,
          tmp2.notion_concerned, tmp2.notion_date, 
          tmp2.notion_date_mask, tmp2.value_defined, tmp2.value_defined_indexed, tmp2.determination_status, 
          tmp2.order_by
         );
      
        INSERT INTO catalogue_people(referenced_relation, record_id, people_type, people_sub_type, 
            order_by, people_ref
            )
          (SELECT currval('identifications_id_seq'), referenced_relation, record_id, people_type, people_sub_type, 
            order_by, people_ref
            from catalogue_people where record_id = tmp2.id and referenced_relation = 'identifications'
          );
            
   END LOOP;
 END LOOP;
   
  RAISE INFO 'Delete ind comments';
  DELETE FROM comments where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind ext_links';
  DELETE FROM ext_links where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind properties';
  DELETE FROM catalogue_properties where referenced_relation='specimen_individuals';
  RAISE INFO 'Delete ind ident';
  DELETE FROM identifications where referenced_relation='specimen_individuals';
/*****/


   RETURN TRUE;
END;
$$
language plpgsql;

SET SESSION session_replication_role = replica;

select move_refs();

SET SESSION session_replication_role = origin;


--- Move Comments (Indiv + spec) ==> part?
--- Move Ext links  (Indiv + spec) => part
--- Move Properties  (Indiv + spec)
--- Merge Then move Indentifications (Indiv => Part)


--- Move Files

--- REwrite ident to simple REF ?
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
drop function move_refs();

rollback;