begin;
set search_path=darwin2,public;
drop view labeling;

DROP TRIGGER IF EXISTS trg_cpy_updateSpecHostImpact ON specimens;


CREATE OR REPLACE FUNCTION trg_del_dict() RETURNS TRIGGER
AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --DELETE
      oldfield = OLD;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into newfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_del_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_del_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_del_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_del_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_del_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_del_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_del_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_del_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_del_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_del_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_del_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_del_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_del_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_del_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_del_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_del_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_del_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_del_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_del_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_del_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_del_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_del_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_del_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_del_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_del_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_del_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_del_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type, 'container_type' );
      PERFORM fct_del_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type, 'sub_container_type' );

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_del_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_del_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_del_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_del_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_del_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'catalogue_properties' THEN

      PERFORM fct_del_in_dict_dept('catalogue_properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation, 'referenced_relation' );
      PERFORM fct_del_in_dict_dept('catalogue_properties','property_sub_type', oldfield.property_sub_type, newfield.property_sub_type,
        oldfield.property_type, newfield.property_type, 'property_type' );
      PERFORM fct_del_in_dict_dept('catalogue_properties','property_qualifier', oldfield.property_qualifier, newfield.property_qualifier,
        oldfield.property_sub_type, newfield.property_sub_type, 'property_sub_type' );
      PERFORM fct_del_in_dict_dept('catalogue_properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type, 'property_type' );
      PERFORM fct_del_in_dict_dept('catalogue_properties','property_accuracy_unit', oldfield.property_accuracy_unit, newfield.property_accuracy_unit,
        oldfield.property_type, newfield.property_type, 'property_type' );

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_del_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name, 'group_name' );
  END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION trg_ins_update_dict() RETURNS TRIGGER
AS $$
DECLARE
  oldfield RECORD;
  newfield RECORD;
BEGIN

    IF TG_OP = 'UPDATE' THEN
      oldfield = OLD;
      newfield = NEW;
    ELSE --INSERT
      newfield = NEW;
      execute 'select * from ' || TG_TABLE_NAME::text || ' where id = -15 ' into oldfield;
    END IF;
    IF TG_TABLE_NAME = 'codes' THEN
      PERFORM fct_add_in_dict('codes','code_prefix_separator', oldfield.code_prefix_separator, newfield.code_prefix_separator);
      PERFORM fct_add_in_dict('codes','code_suffix_separator', oldfield.code_suffix_separator, newfield.code_suffix_separator);
    ELSIF TG_TABLE_NAME = 'collection_maintenance' THEN
      PERFORM fct_add_in_dict('collection_maintenance','action_observation', oldfield.action_observation, newfield.action_observation);
    ELSIF TG_TABLE_NAME = 'identifications' THEN
      PERFORM fct_add_in_dict('identifications','determination_status', oldfield.determination_status, newfield.determination_status);
    ELSIF TG_TABLE_NAME = 'people' THEN
      PERFORM fct_add_in_dict('people','sub_type', oldfield.sub_type, newfield.sub_type);
      PERFORM fct_add_in_dict('people','title', oldfield.title, newfield.title);
    ELSIF TG_TABLE_NAME = 'people_addresses' THEN
      PERFORM fct_add_in_dict('people_addresses','country', oldfield.country, newfield.country);
    ELSIF TG_TABLE_NAME = 'insurances' THEN
      PERFORM fct_add_in_dict('insurances','insurance_currency', oldfield.insurance_currency, newfield.insurance_currency);
    ELSIF TG_TABLE_NAME = 'mineralogy' THEN
      PERFORM fct_add_in_dict('mineralogy','cristal_system', oldfield.cristal_system, newfield.cristal_system);
    ELSIF TG_TABLE_NAME = 'specimens' THEN
      PERFORM fct_add_in_dict('specimens','type', oldfield.type, newfield.type);
      PERFORM fct_add_in_dict('specimens','type_group', oldfield.type_group, newfield.type_group);
      PERFORM fct_add_in_dict('specimens','type_search', oldfield.type_search, newfield.type_search);
      PERFORM fct_add_in_dict('specimens','sex', oldfield.sex, newfield.sex);
      PERFORM fct_add_in_dict('specimens','state', oldfield.state, newfield.state);
      PERFORM fct_add_in_dict('specimens','stage', oldfield.stage, newfield.stage);
      PERFORM fct_add_in_dict('specimens','social_status', oldfield.social_status, newfield.social_status);
      PERFORM fct_add_in_dict('specimens','rock_form', oldfield.rock_form, newfield.rock_form);

      PERFORM fct_add_in_dict('specimens','container_type', oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict('specimens','sub_container_type', oldfield.sub_container_type, newfield.sub_container_type);
      PERFORM fct_add_in_dict('specimens','specimen_part', oldfield.specimen_part, newfield.specimen_part);
      PERFORM fct_add_in_dict('specimens','specimen_status', oldfield.specimen_status, newfield.specimen_status);

      PERFORM fct_add_in_dict('specimens','shelf', oldfield.shelf, newfield.shelf);
      PERFORM fct_add_in_dict('specimens','row', oldfield.row, newfield.row);
      PERFORM fct_add_in_dict('specimens','room', oldfield.room, newfield.room);
      PERFORM fct_add_in_dict('specimens','floor', oldfield.floor, newfield.floor);
      PERFORM fct_add_in_dict('specimens','building', oldfield.building, newfield.building);

      PERFORM fct_add_in_dict_dept('specimens','container_storage', oldfield.container_storage, newfield.container_storage,
        oldfield.container_type, newfield.container_type);
      PERFORM fct_add_in_dict_dept('specimens','sub_container_storage', oldfield.sub_container_storage, newfield.sub_container_storage,
        oldfield.sub_container_type, newfield.sub_container_type);

    ELSIF TG_TABLE_NAME = 'specimens_relationships' THEN
      PERFORM fct_add_in_dict('specimens_relationships','relationship_type', oldfield.relationship_type, newfield.relationship_type);
    ELSIF TG_TABLE_NAME = 'users' THEN
      PERFORM fct_add_in_dict('users','title', oldfield.title, newfield.title);
      PERFORM fct_add_in_dict('users','sub_type', oldfield.sub_type, newfield.sub_type);
    ELSIF TG_TABLE_NAME = 'users_addresses' THEN
      PERFORM fct_add_in_dict('users_addresses','country', oldfield.country, newfield.country);

    ELSIF TG_TABLE_NAME = 'loan_status' THEN
      PERFORM fct_add_in_dict('loan_status','status', oldfield.status, newfield.status);

    ELSIF TG_TABLE_NAME = 'catalogue_properties' THEN

      PERFORM fct_add_in_dict_dept('catalogue_properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation);
      PERFORM fct_add_in_dict_dept('catalogue_properties','property_sub_type', oldfield.property_sub_type, newfield.property_sub_type,
        oldfield.property_type, newfield.property_type);
      PERFORM fct_add_in_dict_dept('catalogue_properties','property_qualifier', oldfield.property_qualifier, newfield.property_qualifier,
        oldfield.property_sub_type, newfield.property_sub_type);
      PERFORM fct_add_in_dict_dept('catalogue_properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type);
      PERFORM fct_add_in_dict_dept('catalogue_properties','property_accuracy_unit', oldfield.property_accuracy_unit, newfield.property_accuracy_unit,
        oldfield.property_type, newfield.property_type );

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_add_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name);

    END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;



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


--Accomp taxo
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

 GRANT SELECT, INSERT, UPDATE, DELETE ON specimens_relationships TO cebmpad;
 GRANT USAGE, SELECT ON SEQUENCE darwin2.specimens_relationships_id_seq TO cebmpad;

 GRANT SELECT ON specimens_relationships TO d2viewer;
 GRANT USAGE ON specimens_relationships_id_seq TO d2viewer;

 delete from flat_dict where dict_field = 'host_relationship';

DROP TRIGGER trg_cpy_updatehosts ON specimens;
DROP FUNCTION fct_cpy_updatehosts();

\i reports/ticketing/labeling.sql
COMMIT;
