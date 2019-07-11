begin;
set search_path=darwin2,public;

create table properties
       (
        id serial,
        property_type varchar not null,
        applies_to varchar not null default '',
        applies_to_indexed varchar not null,
        date_from_mask integer not null default 0,
        date_from timestamp not null default '01/01/0001 00:00:00',
        date_to_mask integer not null default 0,
        date_to timestamp not null default '31/12/2038 00:00:00',
        is_quantitative boolean not null default false,

        property_unit varchar not null default '',
        method varchar,
        method_indexed varchar not null,
        lower_value varchar not null,
        lower_value_unified float,
        upper_value  varchar not null,
        upper_value_unified float,
        property_accuracy varchar not null default '',

        constraint pk_properties primary key (id)
        )
inherits (template_table_record_ref);

comment on table properties is 'All properties or all measurements describing an object in darwin are stored in this table';
comment on column properties.referenced_relation is 'Identifier-Name of the table a property is defined for';
comment on column properties.record_id is 'Identifier of record a property is defined for';
comment on column properties.property_type is 'Type-Category of property - Latitude, Longitude, Ph, Height, Weight, Color, Temperature, Wind direction,...';
comment on column properties.applies_to is 'Depending on the use of the type, this can further specify the actual part measured. For example, a measurement of temperature may be a surface, air or sub-surface measurement.';
comment on column properties.applies_to_indexed is 'Indexed form of Sub type of property - if subtype is null, takes a generic replacement value';
comment on column properties.date_from is 'For a range of measurements, give the measurement start - if null, takes a generic replacement value';
comment on column properties.date_from_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column properties.date_to is 'For a range of measurements, give the measurement stop date/time - if null, takes a generic replacement value';
comment on column properties.date_to_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column properties.property_unit is 'Unit used for property value introduced';
comment on column properties.method is 'Method used to collect property value';
comment on column properties.method_indexed is 'Indexed version of property_method field - if null, takes a generic replacement value';
comment on column properties.lower_value is 'Lower value of Single Value';
comment on column properties.lower_value_unified is 'unified version of the value for comparison with other units';
comment on column properties.upper_value is 'upper value if in bound';
comment on column properties.upper_value_unified is 'unified version of the value for comparison with other units';
comment on column properties.property_accuracy is 'Accuracy of the values';


CREATE INDEX idx_properties_property_type on properties(property_type);
CREATE INDEX idx_properties_property_unit on properties(property_unit);
CREATE INDEX idx_properties_property_lower_value on properties(lower_value);
CREATE INDEX idx_properties_property_upper_value on properties(upper_value);

CREATE INDEX idx_properties_property_lower_value_unified on properties(lower_value_unified );
CREATE INDEX idx_properties_property_upper_value_unified  on properties(upper_value_unified );
CREATE INDEX idx_properties_referenced_record on properties(referenced_relation, record_id);

GRANT SELECT, INSERT, UPDATE, DELETE ON darwin2.properties TO cebmpad;
GRANT USAGE, SELECT ON SEQUENCE darwin2.properties_id_seq TO cebmpad;
GRANT SELECT ON properties TO d2viewer;
GRANT USAGE ON properties_id_seq TO d2viewer;

DROP FUNCTION IF EXISTS convert_to_unified (IN property varchar, IN property_unit varchar, IN property_type varchar);

\i  createfunctions.sql


/*
** Trigger aimed at calculating unified values
*/

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_unified_values();

/*** Add other triggers **/


CREATE TRIGGER trg_trk_log_table_properties AFTER INSERT OR UPDATE OR DELETE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_trk_log_table();

CREATE TRIGGER trg_chk_ref_record_properties AFTER INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_chk_ReferencedRecord();

CREATE TRIGGER fct_cpy_trg_ins_update_dict_properties AFTER INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE trg_ins_update_dict();

CREATE TRIGGER fct_cpy_trg_del_dict_properties AFTER DELETE OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE trg_del_dict();

CREATE TRIGGER trg_cpy_fullToIndex_properties BEFORE INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_fullToIndex();

-- DELETE PROPERTY w-o values
DELETE from catalogue_properties c where not exists (select 1 from properties_values where property_ref = c.id);

INSERT INTO properties(
            referenced_relation, record_id, property_type, applies_to,
            date_from_mask, date_from, date_to_mask,
            date_to, method, property_unit, property_accuracy,
            lower_value, upper_value) (

SELECT
referenced_relation,
record_id,

CASE WHEN coalesce(property_sub_type, '') = '' THEN property_type
  WHEN property_type='electronical measurement' and property_sub_type = 'weight' THEN 'electronical weight'
  ELSE property_sub_type END as prop_type,
COALESCE(property_qualifier,'') as applies_to,
date_from_mask,
date_from,
date_to_mask,
date_to,
CASE WHEN coalesce(property_method, '') = '' THEN property_tool ELSE property_method END as method,

CASE WHEN property_unit = 'unit' OR property_unit ='unit(s)' OR property_unit='' OR property_unit='units' THEN ''
ELSE COALESCE(property_unit,'') END as  property_unit,
COALESCE((select property_accuracy from properties_values where property_ref = c.id  limit 1)::text, '') as property_accuracy,

(select min(property_value) from properties_values where property_ref = c.id) as lower_value,
CASE WHEN (select count(*) from properties_values where property_ref = c.id) > 1 THEN
        (select max(property_value) from properties_values where property_ref = c.id)
ELSE '' END as upper_value


        from catalogue_properties c
  where property_type != 'category'

  );

drop table properties_values;
drop table catalogue_properties;
DELETE FROM flat_dict where  referenced_relation in ('catalogue_properties', 'properties_values', 'properties');

INSERT INTO flat_dict (dict_value, referenced_relation, dict_field, dict_depend)
(

  select property_type , 'properties' ,'property_type', referenced_relation
    FROM properties where property_type is not null
    GROUP BY property_type, referenced_relation

    UNION

      select applies_to , 'properties' ,'applies_to', property_type
    FROM properties where applies_to is not null
    GROUP BY applies_to, property_type

    UNION

      select property_unit , 'properties' ,'property_unit', property_type
    FROM properties where property_unit is not null
    GROUP BY property_unit, property_type
    );

COMMIT;
