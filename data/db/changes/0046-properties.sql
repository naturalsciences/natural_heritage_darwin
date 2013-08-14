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
        lower_value_unified varchar not null,
        upper_value  varchar not null,
        upper_value_unified varchar not null,
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
comment on column properties.property_method is 'Method used to collect property value';
comment on column properties.property_method_indexed is 'Indexed version of property_method field - if null, takes a generic replacement value';
comment on column properties.lower_value is 'Lower value of Single Value';
comment on column properties.lower_value_unified is 'unified version of the value for comparison with other units';µ
comment on column properties.upper_value is 'upper value if in bound';
comment on column properties.upper_value_unified is 'unified version of the value for comparison with other units';
comment on column properties.property_accuracy is 'Accuracy of the values';


CREATE INDEX idx_properties_property_type on properties(property_type);
CREATE INDEX idx_properties_property_unit on properties(property_unit);
CREATE INDEX idx_properties_property_lower_value on properties(lower_value);
CREATE INDEX idx_properties_property_upper_value on properties(upper_value);

\i  createfunctions.sql


/*
** Trigger aimed at calculating unified values
*/

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_unified_values();

CREATE TRIGGER trg_cpy_unified_values BEFORE INSERT OR UPDATE
        ON properties FOR EACH ROW
        EXECUTE PROCEDURE fct_cpy_unified_values();