set search_path=darwin2,public;

BEGIN;

update staging set taxon_ref = null
where taxon_ref in
(select distinct taxon_ref from staging s left join taxonomy t on s.taxon_ref = t.id where t.id is null and taxon_ref is not null);

ALTER TABLE staging ADD CONSTRAINT fk_staging_taxonomy FOREIGN KEY (taxon_ref) REFERENCES taxonomy (id) ON DELETE SET NULL;

commit;

BEGIN;

CREATE OR REPLACE FUNCTION fct_trk_log_table() RETURNS TRIGGER
AS $$
DECLARE
  user_id integer;
  track_level integer;
  track_fields integer;
  trk_id bigint;
  tbl_row RECORD;
  new_val varchar;
  old_val varchar;
  returnedRow RECORD;
BEGIN
  IF TG_OP IN ('INSERT', 'UPDATE') THEN
    returnedRow := NEW;
  ELSE
    returnedRow := OLD;
  END IF;
  SELECT COALESCE(CASE WHEN get_setting('darwin.track_level') = '' THEN NULL ELSE get_setting('darwin.track_level') END,'10')::integer INTO track_level;
  IF track_level = 0 THEN --NO Tracking
    RETURN returnedRow;
  ELSIF track_level = 1 THEN -- Track Only Main tables
    IF TG_TABLE_NAME::text NOT IN ('specimens', 'taxonomy', 'chronostratigraphy', 'lithostratigraphy',
      'mineralogy', 'lithology', 'people') THEN
      RETURN returnedRow;
    END IF;
  END IF;

  SELECT COALESCE(CASE WHEN get_setting('darwin.userid') = '' THEN NULL ELSE get_setting('darwin.userid') END,'0')::integer INTO user_id;
  IF user_id = 0 OR  user_id = -1 THEN
    RETURN returnedRow;
  END IF;

  IF TG_OP = 'INSERT' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'insert', now(), hstore(NEW)) RETURNING id into trk_id;
  ELSEIF TG_OP = 'UPDATE' THEN

    IF ROW(NEW.*) IS DISTINCT FROM ROW(OLD.*) THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, new_value, old_value)
        VALUES (TG_TABLE_NAME::text, NEW.id, user_id, 'update', now(), hstore(NEW), hstore(OLD)) RETURNING id into trk_id;
    ELSE
      RAISE info 'unnecessary update on table "%" and id "%"', TG_TABLE_NAME::text, NEW.id;
    END IF;

  ELSEIF TG_OP = 'DELETE' THEN
    INSERT INTO users_tracking (referenced_relation, record_id, user_ref, action, modification_date_time, old_value)
      VALUES (TG_TABLE_NAME::text, OLD.id, user_id, 'delete', now(), hstore(OLD));
  END IF;

  RETURN NULL;
END;
$$
LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION fct_clear_referencedRecord() RETURNS TRIGGER
AS $$
BEGIN
  IF TG_OP ='DELETE' THEN
    DELETE FROM template_table_record_ref where referenced_relation = TG_TABLE_NAME AND record_id = OLD.id;
  END IF;
  RETURN NULL;
END;
$$ LANGUAGE plpgsql;

commit;
