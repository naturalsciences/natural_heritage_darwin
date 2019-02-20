begin;
set search_path=darwin2,public;
ALTER TABLE staging add column col text ;
ALTER TABLE specimens add column col text ;

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
      PERFORM fct_del_in_dict('specimens','col', oldfield.col, newfield.col);
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

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_del_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation, 'referenced_relation' );
      PERFORM fct_del_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type, 'property_type' );
      PERFORM fct_del_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type, 'property_type' );

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_del_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name, 'group_name' );
  END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION fct_cpy_updateCollInstitutionCascade() RETURNS TRIGGER
language plpgsql
AS
$$
BEGIN
  IF NEW.institution_ref IS DISTINCT FROM OLD.institution_ref THEN
    UPDATE collections
    SET institution_ref = NEW.institution_ref
    WHERE id != NEW.id
      AND parent_ref = NEW.id;
  END IF;
  RETURN NEW;
END;
$$;

/*When unpromoting a user we have to remove the non availble columns from preferences*/
CREATE OR REPLACE FUNCTION fct_unpromotion_impact_prefs() RETURNS TRIGGER
language plpgSQL
AS
$$
DECLARE
  saved_search_row RECORD;
BEGIN
  IF NEW.db_user_type IS DISTINCT FROM OLD.db_user_type AND NEW.db_user_type = 1 THEN
    UPDATE preferences
    SET pref_value = subq.fields_available
    FROM (select array_to_string(array(select fields_list
                                       from regexp_split_to_table((SELECT pref_value
                                                                   FROM preferences
                                                                   WHERE user_ref = NEW.id
                                                                     AND pref_key = 'search_cols_specimen'
                                                                   LIMIT 1
                                                                  ), E'\\|') as fields_list
                                       where fields_list not in ('institution_ref', 'building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                      ),'|'
                                ) as fields_available
         ) subq
    WHERE user_ref = NEW.id
      AND pref_key = 'search_cols_specimen';
    FOR saved_search_row IN SELECT id, visible_fields_in_result FROM my_saved_searches WHERE user_ref = NEW.id LOOP
      UPDATE my_saved_searches
      SET visible_fields_in_result = subq.fields_available
      FROM (select array_to_string(array(select fields_list
                                         from regexp_split_to_table(saved_search_row.visible_fields_in_result, E'\\|') as fields_list
                                         where fields_list not in ('institution_ref','building', 'floor', 'room', 'row', 'shelf', 'col', 'container', 'container_type', 'container_storage', 'sub_container', 'sub_container_type', 'sub_container_storage')
                                        ),'|'
                                  ) as fields_available
          ) subq
      WHERE id = saved_search_row.id;
    END LOOP;
  END IF;
  RETURN NEW;
END;
$$;

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
      PERFORM fct_add_in_dict('specimens','col', oldfield.col, newfield.col);
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

    ELSIF TG_TABLE_NAME = 'properties' THEN

      PERFORM fct_add_in_dict_dept('properties','property_type', oldfield.property_type, newfield.property_type,
        oldfield.referenced_relation, newfield.referenced_relation);
      PERFORM fct_add_in_dict_dept('properties','applies_to', oldfield.applies_to, newfield.applies_to,
        oldfield.property_type, newfield.property_type);
      PERFORM fct_add_in_dict_dept('properties','property_unit', oldfield.property_unit, newfield.property_unit,
        oldfield.property_type, newfield.property_type);

    ELSIF TG_TABLE_NAME = 'tag_groups' THEN
      PERFORM fct_add_in_dict_dept('tag_groups','sub_group_name', oldfield.sub_group_name, newfield.sub_group_name,
        oldfield.group_name, newfield.group_name);

    END IF;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

COMMIT;
