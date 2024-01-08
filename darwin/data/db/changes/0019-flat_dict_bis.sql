set search_path=darwin2, public;

alter table flat_dict add column dict_depend varchar not null default '';
alter table flat_dict drop constraint unq_flat_dict;
alter table flat_dict add constraint unq_flat_dict unique (dict_value, dict_field, referenced_relation, dict_depend);

\i ../createfunctions.sql

CREATE TRIGGER fct_cpy_trg_del_dict_catalogue_properties AFTER DELETE OR UPDATE
        ON catalogue_properties FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_tag_groups AFTER DELETE OR UPDATE
        ON tag_groups FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_catalogue_properties AFTER INSERT OR UPDATE
        ON catalogue_properties FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_tag_groups AFTER INSERT OR UPDATE
        ON tag_groups FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();


INSERT INTO flat_dict (dict_value, referenced_relation, dict_field, dict_depend)
 (
  select property_type , 'catalogue_properties' ,'property_type', referenced_relation
    FROM catalogue_properties where property_type is not null
    GROUP BY property_type, referenced_relation

  UNION 

  select property_sub_type , 'catalogue_properties' ,'property_sub_type', property_type
    FROM catalogue_properties where property_sub_type is not null
    GROUP BY property_sub_type, property_type

  UNION 

  select property_qualifier , 'catalogue_properties' ,'property_qualifier', property_sub_type
    FROM catalogue_properties where property_qualifier is not null
    GROUP BY property_qualifier, property_sub_type

  UNION 

  select property_unit , 'catalogue_properties' ,'property_unit', property_type
    FROM catalogue_properties where property_unit is not null
    GROUP BY property_unit, property_type

  UNION 

  select property_accuracy_unit , 'catalogue_properties' ,'property_accuracy_unit', property_type
    FROM catalogue_properties where property_accuracy_unit is not null
    GROUP BY property_accuracy_unit, property_type


  UNION 

  select container_storage , 'specimen_parts' ,'container_storage', container_type
    FROM specimen_parts where container_storage is not null
    GROUP BY container_storage, container_type

  UNION 

  select sub_container_storage , 'specimen_parts' ,'sub_container_storage', sub_container_type
    FROM specimen_parts where sub_container_storage is not null
    GROUP BY sub_container_storage, sub_container_type

  UNION

  select sub_group_name , 'tag_groups' ,'sub_group_name', group_name
    FROM tag_groups where sub_group_name is not null
    GROUP BY sub_group_name, group_name
  );
