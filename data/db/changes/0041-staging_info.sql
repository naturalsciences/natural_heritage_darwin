begin;
set search_path=darwin2,public;
CREATE TABLE staging_info
(
  id serial NOT NULL,
  staging_ref integer NOT NULL,
  referenced_relation character varying NOT NULL,

  CONSTRAINT pk_staging_info PRIMARY KEY (id),
  CONSTRAINT fk_staging_ref FOREIGN KEY (staging_ref)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
ALTER TABLE staging_info
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_info TO darwin2;
GRANT SELECT ON TABLE staging_info TO d2viewer;

CREATE TABLE staging_relationship
(
  id serial NOT NULL,
  record_id integer NOT NULL,
  referenced_relation character varying NOT NULL,
  relationship_type character varying,
  ref_id integer NOT NULL,

  CONSTRAINT pk_staging_relationship PRIMARY KEY (id)
  CONSTRAINT fk_record_id FOREIGN KEY (record_id)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
COMMENT ON COLUMN staging_relationship.record_id IS 'id of the orignial record';
COMMENT ON COLUMN staging_relationship.referenced_relation IS 'where to find the record_id, referenced_relation is always staging but this field uis mandatory for addRelated php function';
COMMENT ON COLUMN staging_relationship.relationship_type IS 'relation type (eg. host, parent, part of)';
COMMENT ON COLUMN staging_relationship.ref_id IS 'the record id associated';
ALTER TABLE staging_relationship
  OWNER TO darwin2;
GRANT ALL ON TABLE staging_relationship TO darwin2;
GRANT SELECT ON TABLE staging_relationship TO d2viewer;

ALTER TABLE staging DROP COLUMN part_status ;

CREATE TRIGGER trg_clr_referenceRecord_staging_info AFTER DELETE OR UPDATE
  ON staging_info FOR EACH ROW
  EXECUTE PROCEDURE fct_clear_referencedRecord();

COMMIT;

