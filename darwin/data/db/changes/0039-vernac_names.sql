begin;
set search_path=darwin2,public;
\i  createfunctions.sql


create temporary table tmp_vern_names as 
  select referenced_relation, record_id, name, name_indexed, community ,community_indexed   from class_vernacular_names c
  inner join vernacular_names v ON c.id = v.vernacular_class_ref
  ;

DROP TABLE vernacular_names CASCADE ;
DROP TABLE class_vernacular_names CASCADE ;
DROP SEQUENCE IF EXISTS vernacular_names_id_seq;

create table vernacular_names
       (
        id serial,
        community varchar not null,
        community_indexed varchar not null,
        name varchar not null,
        name_indexed varchar not null,
        constraint unq_vernacular_names unique (referenced_relation, record_id, community_indexed, name_indexed),
        constraint pk_vernacular_names primary key (id)
       )
inherits (template_table_record_ref);

comment on table vernacular_names is 'List of vernacular names for a given unit and a given language community';
comment on column vernacular_names.community is 'Language community, a unit translation is available for';
comment on column vernacular_names.community_indexed is 'indexed version of the language community';
comment on column vernacular_names.name is 'Vernacular name';
comment on column vernacular_names.name_indexed is 'Indexed form of vernacular name';
comment on column vernacular_names.referenced_relation is 'Reference of the unit table a vernacular name for a language community has to be defined - id field of table_list table';
comment on column vernacular_names.record_id is 'Identifier of record a vernacular name for a language community has to be defined';


INSERT INTO vernacular_names (referenced_relation, record_id, name, name_indexed, community ,community_indexed)
  (
    select referenced_relation, record_id, name, name_indexed, community ,community_indexed from tmp_vern_names
  );



CREATE INDEX idx_vernacular_names_community_indexed on vernacular_names (community_indexed);
CREATE INDEX idx_vernacular_names_name_indexed on vernacular_names (name_indexed);
CREATE INDEX idx_vernacular_names_referenced_record on vernacular_names(referenced_relation, record_id);



CREATE TRIGGER trg_cpy_fullToIndex_vernacularnames BEFORE INSERT OR UPDATE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_clr_referenceRecord_vernacularnames AFTER DELETE OR UPDATE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_trk_log_table_vernacular_names AFTER INSERT OR UPDATE OR DELETE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_chk_ref_record_vernacular_names AFTER INSERT OR UPDATE
        ON vernacular_names FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

 GRANT SELECT ON vernacular_names TO d2viewer;
 GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.vernacular_names TO cebmpad;
 GRANT USAGE, SELECT ON SEQUENCE darwin2.vernacular_names_id_seq TO cebmpad;
 
 GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.vernacular_names TO darwin2;
 GRANT USAGE, SELECT ON SEQUENCE darwin2.vernacular_names_id_seq TO darwin2;
COMMIT;
