SET search_path = darwin2, public;

\i ../createfunctions.sql


CREATE TABLE bibliography (
  id serial,
  title varchar not null,
  title_ts tsvector not null,
  title_indexed varchar not null,
  type varchar not null,
  abstract varchar not null default '',
  year integer,
  constraint pk_bibliography primary key (id),
  constraint unq_bibliography unique (title_indexed, type)

);
comment on table bibliography is 'List of expeditions made to collect specimens';
comment on column bibliography.id is 'Unique identifier';
comment on column bibliography.title is 'bibliography title';
comment on column bibliography.title_ts is 'tsvector version of title field';
comment on column bibliography.title_indexed is 'Indexed form of title';
comment on column bibliography.type is 'bibliography type : article, book, booklet';
comment on column bibliography.abstract is 'optional abstract of the bibliography';
comment on column bibliography.year is 'The year of publication (or, if unpublished, the year of creation)';

ALTER TABLE bibliography OWNER TO darwin2;
GRANT SELECT ON bibliography TO d2viewer;

create table catalogue_bibliography
(
  id serial,
  bibliography_ref integer not null,
  constraint pk_catalogue_bibliography primary key (id),
  constraint fk_bibliography foreign key (bibliography_ref) references bibliography(id) on delete cascade,
  constraint unq_catalogue_bibliography unique (referenced_relation, record_id, bibliography_ref)
  )
inherits (template_table_record_ref);

comment on table catalogue_bibliography is 'List of people of catalogues units - Taxonomy, Chronostratigraphy,...';
comment on column catalogue_bibliography.id is 'Unique identifier of record';
comment on column catalogue_bibliography.referenced_relation is 'Identifier-Name of table the units come from';
comment on column catalogue_bibliography.record_id is 'Identifier of record concerned in table concerned';
comment on column catalogue_bibliography.bibliography_ref is 'Reference of the biblio concerned - id field of people table';

ALTER TABLE catalogue_bibliography OWNER TO darwin2;
GRANT SELECT ON catalogue_bibliography TO d2viewer;

CREATE TRIGGER trg_cpy_fullToIndex_bibliography BEFORE INSERT OR UPDATE
        ON bibliography FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_fullToIndex();

CREATE TRIGGER trg_clr_referenceRecord_bibliography AFTER DELETE
        ON bibliography FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_cpy_toFullText_bibliography BEFORE INSERT OR UPDATE
  ON bibliography FOR EACH ROW
  EXECUTE PROCEDURE tsvector_update_trigger(title_ts, 'pg_catalog.simple', title);

CREATE TRIGGER trg_trk_log_table_bibliography AFTER INSERT OR UPDATE OR DELETE
        ON bibliography FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_words_ts_cpy_bibliography BEFORE INSERT OR UPDATE
        ON bibliography FOR EACH ROW
        EXECUTE PROCEDURE fct_trg_word();
