begin;
set search_path=darwin2,public;
drop view labeling;
create table specimens_relationships
       (
        id serial,
        specimen_ref integer not null,
        relationship_type varchar not null default 'host',
        unit_type varchar not null default 'specimens',
        specimen_related_ref integer,
        taxon_ref integer,
        mineral_ref integer,

        institution_ref integer,
        source_name text,
        source_id text,

        quantity numeric(16,2),
        unit varchar default '%',
        constraint pk_specimens_relationships primary key (id),
        constraint fk_specimens_relationships_specimens foreign key (specimen_ref) references specimens(id) on delete cascade,
        constraint fk_specimens_relationships_specimens_related foreign key (specimen_related_ref) references specimens(id) on delete cascade,
        constraint fk_specimens_relationships_mineralogy foreign key (mineral_ref) references mineralogy(id),
        constraint fk_specimens_relationships_taxonomy foreign key (taxon_ref) references taxonomy(id),
        constraint fk_specimens_relationships_institution foreign key (institution_ref) references people(id)

       );
comment on table specimens_relationships is 'List all the objects/specimens related the current specimen';
comment on column specimens_relationships.specimen_ref is 'Reference of specimen concerned - id field of specimens table';
comment on column specimens_relationships.mineral_ref is 'Reference of related mineral';
comment on column specimens_relationships.taxon_ref is 'Reference of the related taxon ';
comment on column specimens_relationships.taxon_ref is 'Reference of the related specimen';

comment on column specimens_relationships.relationship_type is 'Type of relationship';
comment on column specimens_relationships.unit_type is 'Type of the related unit : spec, taxo or mineralo';
comment on column specimens_relationships.quantity is 'Quantity of related mineral';
comment on column specimens_relationships.institution_ref is 'External Specimen related institution';
comment on column specimens_relationships.source_name is 'External Specimen related  source DB';
comment on column specimens_relationships.source_id is 'External Specimen related id in the source';

CREATE TRIGGER trg_clr_referenceRecord_specimens_relationships AFTER DELETE OR UPDATE
        ON specimens_relationships FOR EACH ROW
        EXECUTE PROCEDURE fct_clear_referencedRecord();

CREATE TRIGGER trg_trk_log_table_specimens_relationship AFTER INSERT OR UPDATE OR DELETE
        ON specimens_relationships FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_specimens_relationships AFTER INSERT OR UPDATE
        ON specimens_relationships FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_specimens_relationships AFTER DELETE  OR UPDATE
        ON specimens_relationships FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

--host spec
insert into specimens_relationships(
 specimen_ref,
 relationship_type,
 unit_type,
 specimen_related_ref)
 (
  select id, CASE WHEN host_relationship='koekoeksei in nest' THEN 'cuckoo eggs in the nest' ELSE 'host' END, 'specimens', host_specimen_ref from specimens where host_specimen_ref is not null
 );
 
--HOST taxon
insert into specimens_relationships(
 specimen_ref,
 relationship_type,
 unit_type,
 taxon_ref)
 (
  select id, 'host', 'taxonomy', host_taxon_ref from specimens where host_specimen_ref is null and host_taxon_ref is not null
 );

 
--Accomp mineralo
insert into specimens_relationships(
 specimen_ref,
 relationship_type,
 unit_type,
 mineral_ref,
 quantity,
 unit
 )
 (
  select specimen_ref, 'combination', 'mineralogy', mineral_ref , quantity , unit from specimens_accompanying where accompanying_type='mineral'
 );
 
 
--Accomp mineralo
insert into specimens_relationships(
 specimen_ref,
 relationship_type,
 unit_type,
 taxon_ref)
 (
  select specimen_ref, 'host', 'taxonomy', taxon_ref from specimens_accompanying where accompanying_type='biological'
 );
 
CREATE INDEX idx_specimens_relationships_taxon_ref on specimens_relationships(taxon_ref);
CREATE INDEX idx_specimens_relationships_mineral_ref on specimens_relationships(mineral_ref);
CREATE INDEX idx_specimens_relationships_specimen_ref on specimens_relationships(specimen_ref);
CREATE INDEX idx_specimens_relationships_specimen_related_ref on specimens_relationships(specimen_related_ref);
 
 drop table specimens_accompanying;
 alter table specimens drop column host_specimen_ref;
 alter table specimens drop column host_taxon_ref;
 alter table specimens drop column host_relationship;
 
alter table specimens drop column host_taxon_name;
alter table specimens drop column host_taxon_name_indexed ;
alter table specimens drop column host_taxon_level_ref ;
alter table specimens drop column host_taxon_level_name ;
alter table specimens drop column host_taxon_status ;
alter table specimens drop column host_taxon_path ;
alter table specimens drop column host_taxon_parent_ref ;
alter table specimens drop column host_taxon_extinct ;

    
 alter table staging drop column host_specimen_ref;
 alter table staging drop column host_taxon_ref;
 alter table staging drop column host_relationship;
 
 delete from flat_dict where dict_field = 'host_relationship';
 
\i reports/ticketing/labeling.sql
COMMIT;