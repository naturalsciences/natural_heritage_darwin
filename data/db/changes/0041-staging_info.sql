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
GRANT ALL ON TABLE staging TO darwin2;
GRANT SELECT ON TABLE staging TO d2viewer;
COMMIT;

