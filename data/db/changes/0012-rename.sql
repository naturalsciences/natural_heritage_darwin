BEGIN;

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
    BEGIN 
      INSERT INTO classification_synonymies
        (referenced_relation, record_id, group_id, group_name, is_basionym, order_by)
      VALUES (rec.referenced_relation, rec.record_id_1, grp_id, 'rename', false, 0);
    EXCEPTION WHEN SQLSTATE 'P0001' THEN
      BEGIN
        SELECT group_id INTO grp_id from classification_synonymies where 
          referenced_relation = rec.referenced_relation AND record_id=rec.record_id_1 AND  group_name = 'rename';
        
        INSERT INTO classification_synonymies
          (referenced_relation, record_id, group_id, group_name, is_basionym, order_by)
          VALUES (rec.referenced_relation, rec.record_id_1, grp_id, 'rename', false, 0);
      EXCEPTION WHEN unique_violation THEN
      --NOOP
      END;
    END;

    -- Try the second one
    BEGIN 
      INSERT INTO classification_synonymies
        (referenced_relation, record_id, group_id, group_name, is_basionym, order_by)
      VALUES (rec.referenced_relation, rec.record_id_2, grp_id, 'rename', true, 1);
    EXCEPTION WHEN SQLSTATE 'P0001' THEN
      --NOOP
    WHEN unique_violation THEN
      --NOOP
    END;
  END LOOP;
  return true;
END;
$$ LANGUAGE plpgsql;

SELECT migrate_current_name();

alter table catalogue_relationships disable trigger trg_trk_log_table_catalogue_relationships;

delete from catalogue_relationships where relationship_type = 'current_name';

alter table catalogue_relationships enable trigger trg_trk_log_table_catalogue_relationships;

drop function migrate_current_name();

commit;

