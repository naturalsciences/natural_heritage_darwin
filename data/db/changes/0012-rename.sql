delete from my_widgets where group_name = 'relationRename';

CREATE FUNCTION migrate_current_name() RETURNS boolean
AS $$
DECLARE
  rec RECORD;
  grp_id integer;
BEGIN
  FOR rec in select referenced_relation, record_id_1, record_id_2 from catalogue_relationships where relationship_type = 'current_name'
  LOOP
    SELECT nextval('classification_synonymies_group_id_seq') into grp_id;
    INSERT INTO classification_synonymies
            (referenced_relation, record_id, group_id, group_name, is_basionym, order_by)
    VALUES (rec.referenced_relation, rec.record_id_1, grp_id, 'rename', false, 0),
      (rec.referenced_relation, rec.record_id_2, grp_id, 'rename', true, 1)
    ;
  END LOOP;
END;
$$ LANGUAGE plpgsql;

SELECT migrate_current_name();

delete from catalogue_relationships where relationship_type = 'current_name';
drop function migrate_current_name;

