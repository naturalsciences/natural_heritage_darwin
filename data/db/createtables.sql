\set log_error_verbosity terse
create table template_people
       (
        is_physical boolean not null default true,
        sub_type varchar,
        formated_name varchar not null,
        formated_name_indexed varchar not null,
        formated_name_unique varchar not null,
        formated_name_ts tsvector not null,
        title varchar not null default '',
        family_name varchar not null,
        given_name varchar,
        additional_names varchar,
        birth_date_mask integer not null default 0,
        birth_date date not null default '01/01/0001',
        gender char(1),
	constraint genders_chk CHECK (gender in ('M', 'F'))
       );
comment on table template_people is 'Template table used to describe user/people tables';
comment on column template_people.is_physical is 'Type of user/person: physical or moral - true is physical, false is moral';
comment on column template_people.sub_type is 'Used for moral user/persons: precise nature - public institution, asbl, sprl, sa,...';
comment on column template_people.formated_name is 'Complete user/person formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';
comment on column template_people.formated_name_ts is 'tsvector form of formated_name field';
comment on column template_people.formated_name_indexed is 'Indexed form of formated_name field';
comment on column template_people.formated_name_unique is 'Indexed form of formated_name field (for unique index purpose)';
comment on column template_people.family_name is 'Family name for physical user/persons and Organisation name for moral user/persons';
comment on column template_people.title is 'Title of a physical user/person like Mr or Mrs or phd,...';
comment on column template_people.given_name is 'User/person''s given name - usually first name';
comment on column template_people.additional_names is 'Any additional names given to user/person';
comment on column template_people.birth_date_mask is 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column template_people.birth_date is 'Birth/Creation date composed';
comment on column template_people.gender is 'For physical user/persons give the gender: M or F';
create table template_people_languages
       (
        language_country varchar not null default 'en',
        mother boolean not null default true,
        preferred_language boolean not null default false
       );
comment on table template_people_languages is 'Template supporting users/people languages table definition';
comment on column template_people_languages.language_country is 'Reference of Language - language_country field of languages_countries table';
comment on column template_people_languages.mother is 'Flag telling if its mother language or not';
comment on column template_people_languages.preferred_language is 'Flag telling which language is preferred in communications';

create sequence people_id_seq;

create table people
       (
        id integer not null default nextval('people_id_seq'),
        db_people_type integer not null default 1,
        end_date_mask integer not null default 0,
        end_date date not null default '01/01/0001',
        activity_date_from_mask integer not null default 0,
        activity_date_from date not null default '01/01/0001',
        activity_date_to_mask integer not null default 0,
        activity_date_to date not null default '31/12/2038',
        constraint pk_people primary key (id),
        constraint unq_people unique (is_physical,gender, formated_name_unique, birth_date, birth_date_mask, end_date, end_date_mask)
       )
inherits (template_people);
comment on table people is 'All physical and moral persons used in the application are here stored';
comment on column people.id is 'Unique identifier of a person';
comment on column people.is_physical is 'Type of person: physical or moral - true is physical, false is moral';
comment on column people.sub_type is 'Used for moral persons: precise nature - public institution, asbl, sprl, sa,...';
comment on column people.formated_name is 'Complete person formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';
comment on column people.formated_name_ts is 'tsvector form of formated_name field';
comment on column people.formated_name_indexed is 'Indexed form of formated_name field';
comment on column people.title is 'Title of a physical user/person like Mr or Mrs or phd,...';
comment on column people.family_name is 'Family name for physical persons and Organisation name for moral persons';
comment on column people.given_name is 'User/person''s given name - usually first name';
comment on column people.additional_names is 'Any additional names given to person';
comment on column people.birth_date is 'Day of birth/creation';
comment on column people.birth_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column people.gender is 'For physical persons give the gender: M or F';
comment on column people.db_people_type is 'Sum of numbers in an arithmetic suite (1,2,4,8,...) that gives a unique number identifying people roles - each roles represented by one of the number in the arithmetic suite: 1 is contact, 2 is author, 4 is identifier, 8 is expert, 16 is collector, 32 preparator, 64 photographer...';
comment on column people.end_date is 'End date';
comment on column people.end_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column people.activity_date_from is 'person general activity period or person activity period in the organization referenced date from';
comment on column people.activity_date_from_mask is 'person general activity period or person activity period in the organization referenced date from mask';
comment on column people.activity_date_to is 'person general activity period or person activity period in the organization referenced date to';
comment on column people.activity_date_to_mask is 'person general activity period or person activity period in the organization referenced date to mask';


create sequence catalogue_relationships_id_seq;

create table catalogue_relationships
       (
        id integer not null default nextval('catalogue_relationships_id_seq'),
        referenced_relation varchar not null,
        record_id_1 integer not null,
        record_id_2 integer not null,
        relationship_type varchar not null default 'recombined from',
        constraint unq_catalogue_relationships unique (referenced_relation, relationship_type, record_id_1, record_id_2)
       );
comment on table catalogue_relationships is 'Stores the relationships between records of a table - current name, original combination, ...';
comment on column catalogue_relationships.referenced_relation is 'Reference of the table a relationship is defined for';
comment on column catalogue_relationships.record_id_1 is 'Identifier of record in relation with an other one (record_id_2)';
comment on column catalogue_relationships.record_id_2 is 'Identifier of record in relation with an other one (record_id_1)';
comment on column catalogue_relationships.relationship_type is 'Type of relation between record 1 and record 2 - current name, original combination, ...';
create table template_table_record_ref
       (
        referenced_relation varchar not null,
        record_id integer not null
       );
comment on table template_table_record_ref is 'Template called to add referenced_relation and record_id fields';
comment on column template_table_record_ref.referenced_relation is 'Reference-Name of table concerned';
comment on column template_table_record_ref.record_id is 'Id of record concerned';

create sequence catalogue_people_id_seq;

create table catalogue_people
       (
        id integer not null default nextval('catalogue_people_id_seq'),
        people_type varchar not null default 'author',
        people_sub_type varchar not null default '',
        order_by integer not null default 1,
        people_ref integer not null,
        constraint pk_catalogue_people primary key (id),
        constraint fk_people_list_person foreign key (people_ref) references people(id) on delete cascade,
        constraint unq_catalogue_people unique (referenced_relation, people_type, people_sub_type, record_id, people_ref)
       )
inherits (template_table_record_ref);
comment on table catalogue_people is 'List of people of catalogues units - Taxonomy, Chronostratigraphy,...';
comment on column catalogue_people.id is 'Unique identifier of record';
comment on column catalogue_people.referenced_relation is 'Identifier-Name of table the units come from';
comment on column catalogue_people.record_id is 'Identifier of record concerned in table concerned';
comment on column catalogue_people.people_type is 'Type of "people" associated to the catalogue unit: authors, collectors, defined,  ...';
comment on column catalogue_people.people_sub_type is 'Type of "people" associated to the catalogue unit: Main author, corrector, taking the sense from,...';
comment on column catalogue_people.people_ref is 'Reference of person concerned - id field of people table';
comment on column catalogue_people.order_by is 'Integer used to order the persons in a list';

create sequence catalogue_levels_id_seq;

create table catalogue_levels
       (
        id integer not null default nextval('catalogue_levels_id_seq'),
        level_type varchar not null,
        level_name varchar not null,
        level_sys_name varchar not null,
        optional_level boolean not null default false,
        constraint pk_catalogue_levels primary key (id),
        constraint unq_catalogue_levels unique (level_type, level_name)
       );
comment on table catalogue_levels is 'List of hierarchical units levels - organized by type of unit: taxonomy, chroostratigraphy,...';
comment on column catalogue_levels.id is 'Unique identifier of a hierarchical unit level';
comment on column catalogue_levels.level_type is 'Type of unit the levels is applicable to - contained in a predifined list: taxonomy, chronostratigraphy,...';
comment on column catalogue_levels.level_name is 'Name given to level concerned';
comment on column catalogue_levels.level_sys_name is 'Name given to level concerned in the system. i.e.: cohort zoology will be writen in system as cohort_zoology';
comment on column catalogue_levels.optional_level is 'Tells if the level is optional';
create table possible_upper_levels
       (
        level_ref integer not null,
        level_upper_ref integer not null,
        constraint unq_possible_upper_levels unique (level_ref, level_upper_ref),
        constraint fk_possible_upper_levels_catalogue_levels_01 foreign key (level_ref) references catalogue_levels(id) on delete cascade,
        constraint fk_possible_upper_levels_catalogue_levels_02 foreign key (level_upper_ref) references catalogue_levels(id)
       );
comment on table possible_upper_levels is 'For each level, list all the availble parent levels';
comment on column possible_upper_levels.level_ref is 'Reference of current level';
comment on column possible_upper_levels.level_upper_ref is 'Reference of authorized parent level';

create sequence comments_id_seq;

create table comments
       (
        id integer not null default nextval('comments_id_seq'),
        notion_concerned varchar not null,
        comment text not null,
        comment_ts tsvector not null,
        comment_language_full_text full_text_language,
        constraint pk_comments primary key (id),
        constraint unq_comments unique (referenced_relation, record_id, notion_concerned)
       )
       inherits (template_table_record_ref);
comment on table comments is 'Comments associated to a record of a given table (and maybe a given field) on a given subject';
comment on column comments.id is 'Unique identifier of a comment';
comment on column comments.referenced_relation is 'Reference-Name of table a comment is posted for';
comment on column comments.record_id is 'Identifier of the record concerned';
comment on column comments.notion_concerned is 'Notion concerned by comment';
comment on column comments.comment is 'Comment';
comment on column comments.comment_ts is 'tsvector form of comment field';
comment on column comments.comment_language_full_text is 'Corresponding language to the language/country reference recognized by full text search to_tsvector function';

create sequence ext_links_id_seq;

create table ext_links
       (
        id integer not null default nextval('ext_links_id_seq'),
        url varchar not null,
        comment text not null,
        comment_ts tsvector not null,
        comment_language_full_text full_text_language,
        constraint pk_ext_links primary key (id),
        constraint unq_ext_links unique (referenced_relation, record_id, url)
       )
       inherits (template_table_record_ref);
comment on table ext_links is 'External link possibly refereced for a specific relation';
comment on column ext_links.id is 'Unique identifier of a comment';
comment on column ext_links.referenced_relation is 'Reference-Name of table a comment is posted for';
comment on column ext_links.record_id is 'Identifier of the record concerned';
comment on column ext_links.url is 'External URL';
comment on column ext_links.comment is 'Comment';
comment on column ext_links.comment_ts is 'tsvector form of comment field';
comment on column ext_links.comment_language_full_text is 'Corresponding language to the language/country reference recognized by full text search to_tsvector function';


create sequence gtu_id_seq;

create table gtu
       (
        id integer not null default nextval('gtu_id_seq'),
        code varchar not null,
        parent_ref integer,
        gtu_from_date_mask integer not null default 0,
        gtu_from_date timestamp not null default '01/01/0001 00:00:00',
        gtu_to_date_mask integer not null default 0,
        gtu_to_date timestamp not null default '31/12/2038 00:00:00',
        path varchar not null default '/',
        tag_values_indexed varchar[],
        latitude float,
        longitude float,
        lat_long_accuracy float,
        location GEOGRAPHY(POLYGON,4326),
        elevation float,
        elevation_accuracy float,
        constraint pk_gtu primary key (id),
        constraint fk_gtu_gtu foreign key (parent_ref) references gtu(id) on delete cascade
       );
comment on table gtu is 'Location or sampling units - GeoTemporalUnits';
comment on column gtu.id is 'Unique identifier of a location or sampling unit';
comment on column gtu.code is 'Code given - for sampling units - takes id if none defined';
comment on column gtu.parent_ref is 'Recursive reference to a parent location-sampling unit - id field of gtu table itself';
comment on column gtu.gtu_from_date is 'composed from date of the GTU';
comment on column gtu.gtu_from_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column gtu.gtu_to_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column gtu.gtu_to_date is 'composed to date of the GTU';
comment on column gtu.path is 'When gtus are hierarchicaly ordered, give the parenty path';
comment on column gtu.tag_values_indexed is 'Array of all tags associated to gtu (indexed form)';
comment on column gtu.latitude is 'Latitude of the gtu';
comment on column gtu.longitude is 'longitude of the gtu';
comment on column gtu.lat_long_accuracy is 'Accuracy in meter of both lat & long';
comment on column gtu.elevation is 'Elevation from the level of the sea in meter';
comment on column gtu.elevation_accuracy is 'Accuracy in meter of the elevation';
--SELECT substring(AddGeometryColumn('gtu', 'location', 4326, 'POLYGON', 2) for 0);

create sequence tag_groups_id_seq;

create table tag_groups
       (
        id bigint not null default nextval('tag_groups_id_seq'),
        gtu_ref integer not null,
        group_name varchar not null,
        group_name_indexed varchar not null,
        sub_group_name varchar not null,
        sub_group_name_indexed varchar not null,
        color varchar not null default '#FFFFFF',
        tag_value varchar not null,
        constraint fk_tag_groups_gtu foreign key (gtu_ref) references gtu(id) on delete cascade,
        constraint pk_tag_groups primary key (id),
        constraint unq_tag_groups unique (gtu_ref, group_name_indexed, sub_group_name_indexed)
       );
comment on table tag_groups is 'List of grouped tags';
comment on column tag_groups.id is 'Unique identifier of a grouped tag';
comment on column tag_groups.gtu_ref is 'Reference to a Gtu';
comment on column tag_groups.group_name is 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';
comment on column tag_groups.group_name_indexed is 'Indexed form of a group name';
comment on column tag_groups.sub_group_name is 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';
comment on column tag_groups.sub_group_name_indexed is 'Indexed form of a sub-group name';
comment on column tag_groups.color is 'Color associated to the group concerned';
comment on column tag_groups.tag_value is 'Ensemble of Tags';

create table tags
      (
        gtu_ref integer not null,
        group_ref integer not null,
        group_type varchar not null,
        sub_group_type varchar not null,
        tag varchar not null,
        tag_indexed varchar not null,
        constraint fk_tags_gtu foreign key (gtu_ref) references gtu(id) on delete cascade,
        constraint fk_tags_tag_groups foreign key (group_ref) references tag_groups(id) on delete cascade
      );

comment on table tags is 'List of calculated tags for a groups. This is only for query purpose (filled by triggers)';
comment on column tags.gtu_ref is 'Reference to a Gtu';
comment on column tags.group_ref is 'Reference of the Group name under which the tag is grouped';
comment on column tags.group_type is 'Indexed form of a group name';
comment on column tags.sub_group_type is 'Indexed form of a sub-group name';
comment on column tags.tag is 'The readable version of the tag';
comment on column tags.tag_indexed is 'The indexed version of the tag';


create sequence catalogue_properties_id_seq;

create table catalogue_properties
       (
        id integer not null default nextval('catalogue_properties_id_seq'),
        property_type varchar not null,
        property_sub_type varchar not null default '',
        property_sub_type_indexed varchar not null,
        property_qualifier varchar,
        property_qualifier_indexed varchar not null,
        date_from_mask integer not null default 0,
        date_from timestamp not null default '01/01/0001 00:00:00',
        date_to_mask integer not null default 0,
        date_to timestamp not null default '31/12/2038 00:00:00',
        property_unit varchar not null default '',
        property_accuracy_unit varchar not null default '',
        property_method varchar,
        property_method_indexed varchar not null,
        property_tool varchar,
        property_tool_indexed varchar not null,
        constraint pk_catalogue_properties primary key (id),
        constraint unq_catalogue_properties unique (referenced_relation, record_id, property_type, property_sub_type_indexed, property_qualifier_indexed, date_from, date_to, property_method_indexed, property_tool_indexed)
       )
inherits (template_table_record_ref);

comment on table catalogue_properties is 'All properties or all measurements describing an object in darwin are stored in this table';
comment on column catalogue_properties.referenced_relation is 'Identifier-Name of the table a property is defined for';
comment on column catalogue_properties.record_id is 'Identifier of record a property is defined for';
comment on column catalogue_properties.property_type is 'Type-Category of property - Latitude, Longitude, Ph, Height, Weight, Color, Temperature, Wind direction,...';
comment on column catalogue_properties.property_sub_type is 'Sub type or sub category of property: For Latitudes and Longitudes, precise which type of lat/long it is like Lambert 72, Lambert 92, UTM,...';
comment on column catalogue_properties.property_sub_type_indexed is 'Indexed form of Sub type of property - if subtype is null, takes a generic replacement value';
comment on column catalogue_properties.property_qualifier is 'Bring a complement of information to the property sub type. i.e.: if sub type is speed, qualifier can be wave speed, wind speed, light speed,...';
comment on column catalogue_properties.property_qualifier_indexed is 'Indexed form of property_qualifier field';
comment on column catalogue_properties.date_from is 'For a range of measurements, give the measurement start - if null, takes a generic replacement value';
comment on column catalogue_properties.date_from_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column catalogue_properties.date_to is 'For a range of measurements, give the measurement stop date/time - if null, takes a generic replacement value';
comment on column catalogue_properties.date_to_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column catalogue_properties.property_unit is 'Unit used for property value introduced';
comment on column catalogue_properties.property_method is 'Method used to collect property value';
comment on column catalogue_properties.property_method_indexed is 'Indexed version of property_method field - if null, takes a generic replacement value';
comment on column catalogue_properties.property_accuracy_unit is 'Unit used for accuracy value(s)';
comment on column catalogue_properties.property_tool is 'Tool used to collect property value';
comment on column catalogue_properties.property_tool_indexed is 'Indexed version of property_tool field - if null, takes a generic replacement value';

create sequence properties_values_id_seq;

create table properties_values
      (
        id integer not null default nextval('properties_values_id_seq'),
        property_ref integer not null,
        property_value varchar not null,
        property_value_unified varchar not null default '',
        property_accuracy real,
        property_accuracy_unified real,
        constraint pk_properties_values primary key (id),
        constraint fk_properties_values_properties foreign key (property_ref) references catalogue_properties(id) on delete cascade
      );
comment on table properties_values is 'All properties values seen in catalogue_properties';
comment on column properties_values.id is 'Unique identifier of a property value';
comment on column properties_values.property_value is 'Value for the property type and subtype selected';
comment on column properties_values.property_value_unified is 'Unified version of property_value -> means that the value is converted into a common unit allowing comparisons';
comment on column properties_values.property_accuracy is 'Accuracy of property measurement';
comment on column properties_values.property_accuracy_unified is 'Unified version of accuracy on property or sub property value -> means that the value is converted into a common unit allowing comparisons';

CREATE sequence identifications_id_seq;

create table identifications
       (
        id integer not null default nextval('identifications_id_seq'),
        notion_concerned varchar not null,
        notion_date timestamp not null default '0001-01-01 00:00:00'::timestamp,
        notion_date_mask integer not null default 0,
        value_defined varchar,
        value_defined_indexed varchar not null,
        value_defined_ts tsvector,
        determination_status varchar,
        order_by integer not null default 1,
        constraint pk_identifications primary key (id),
        constraint unq_identifications unique (referenced_relation, record_id, notion_concerned, notion_date, value_defined_indexed)
       )
inherits (template_table_record_ref);
comment on table identifications is 'History of identifications';
comment on column identifications.id is 'Unique identifier of an identification';
comment on column identifications.referenced_relation is 'Reference of table an identification is introduced for';
comment on column identifications.record_id is 'Id of record concerned by an identification entry';
comment on column identifications.notion_concerned is 'Type of entry: Identification on a specific concern';
comment on column identifications.notion_date is 'Date of identification or preparation';
comment on column identifications.notion_date_mask is 'Date/Time mask used for identification date fuzzyness';
comment on column identifications.value_defined is 'When making identification, stores the value resulting of this identification';
comment on column identifications.value_defined_ts is 'tsvector form of value_defined field';
comment on column identifications.value_defined_indexed is 'Indexed form of value_defined field';
comment on column identifications.determination_status is 'Status of identification - can either be a percentage of certainty or a code describing the identification step in the process';
comment on column identifications.order_by is 'Integer used to order the identifications when no date entered';

create sequence class_vernacular_names_id_seq;

create table class_vernacular_names
       (
        id integer not null default nextval('class_vernacular_names_id_seq'),
        community varchar not null,
        community_indexed varchar not null,
        constraint pk_class_vernacular_names primary key (id),
        constraint unq_class_vernacular_names unique (referenced_relation, record_id, community_indexed)
       )
inherits (template_table_record_ref);
comment on table class_vernacular_names is 'Contains the language communities a unit name translation is available for';
comment on column class_vernacular_names.id is 'Unique identifier of a language community vernacular name';
comment on column class_vernacular_names.referenced_relation is 'Reference of the unit table a vernacular name for a language community has to be defined - id field of table_list table';
comment on column class_vernacular_names.record_id is 'Identifier of record a vernacular name for a language community has to be defined';
comment on column class_vernacular_names.community is 'Language community, a unit translation is available for';

create sequence vernacular_names_id_seq;

create table vernacular_names
       (
        id integer not null default nextval('vernacular_names_id_seq'),
        vernacular_class_ref integer not null,
        name varchar not null,
        name_ts tsvector not null,
        name_indexed varchar not null,
        constraint unq_vernacular_names unique (vernacular_class_ref, name_indexed),
        constraint pk_vernacular_names primary key (id),
        constraint fk_vernacular_class_class_vernacular_names foreign key (vernacular_class_ref) references class_vernacular_names(id) on delete cascade
       );
comment on table vernacular_names is 'List of vernacular names for a given unit and a given language community';
comment on column vernacular_names.vernacular_class_ref is 'Identifier of a unit/language community entry - id field of class_vernacular_names table';
comment on column vernacular_names.name is 'Vernacular name';
comment on column vernacular_names.name_ts is 'tsvector version of name field';
comment on column vernacular_names.name_indexed is 'Indexed form of vernacular name';

create sequence expeditions_id_seq;

create table expeditions
       (
        id integer not null default nextval('expeditions_id_seq'),
        name varchar not null,
        name_ts tsvector not null,
        name_indexed varchar not null,
        name_language_full_text full_text_language,
        expedition_from_date_mask integer not null default 0,
        expedition_from_date date not null default '01/01/0001',
        expedition_to_date_mask integer not null default 0,
        expedition_to_date date not null default '31/12/2038',
        constraint pk_expeditions primary key (id),
        constraint unq_expeditions unique (name_indexed, expedition_from_date, expedition_from_date_mask, expedition_to_date, expedition_to_date_mask)
       );
comment on table expeditions is 'List of expeditions made to collect specimens';
comment on column expeditions.id is 'Unique identifier of an expedition';
comment on column expeditions.name is 'Expedition name';
comment on column expeditions.name_ts is 'tsvector version of name field';
comment on column expeditions.name_indexed is 'Indexed form of expedition name';
comment on column expeditions.name_language_full_text is 'Language associated to language/country reference used by full text search to_tsvector function';
comment on column expeditions.expedition_from_date_mask is 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column expeditions.expedition_from_date is 'Start date of the expedition';
comment on column expeditions.expedition_to_date is 'End date of the expedition';
comment on column expeditions.expedition_to_date_mask is  'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';

create sequence users_id_seq;

create table users
       (
        id integer not null default nextval('users_id_seq'),
        db_user_type smallint default 1 not null,
        people_id integer,
        last_seen timestamp,
        created_at timestamp default now(),
	selected_lang varchar not null default 'en',
        constraint pk_users primary key (id),
        constraint unq_users unique (is_physical, gender, formated_name_unique, birth_date, birth_date_mask),
        constraint fk_user_people_id foreign key (people_id) references people(id) on delete set NULL
       )
inherits (template_people);
comment on table users is 'List all application users';
comment on column users.id is 'Unique identifier of a user';
comment on column users.is_physical is 'Type of user: physical or moral - true is physical, false is moral';
comment on column users.sub_type is 'Used for moral users: precise nature - public institution, asbl, sprl, sa,...';
comment on column users.formated_name is 'Complete user formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';
comment on column users.db_user_type is 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';
comment on column users.people_id is 'Reference to a people if this user is also known as a people';
comment on column users.formated_name_ts is 'tsvector form of formated_name field';
comment on column users.formated_name_indexed is 'Indexed form of formated_name field';
comment on column users.formated_name_unique is 'Indexed form of formated_name field (for unique index use)';
comment on column users.family_name is 'Family name for physical users and Organisation name for moral users';
comment on column users.title is 'Title of a physical user/person like Mr or Mrs or phd,...';
comment on column users.given_name is 'User/user''s given name - usually first name';
comment on column users.additional_names is 'Any additional names given to user';
comment on column users.birth_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column users.birth_date is 'Birth/Creation date composed';
comment on column users.gender is 'For physical users give the gender: M or F';
comment on column users.selected_lang is 'Lang of the interface for the user en,fr,nl ,....';
comment on column users.last_seen is 'Last time the user has logged in.';

create sequence people_languages_id_seq;

create table people_languages
       (
        id integer not null default nextval('people_languages_id_seq'),
        people_ref integer not null,
        constraint pk_people_languages primary key (id),
        constraint unq_people_languages unique (people_ref, language_country),
        constraint fk_people_languages_people foreign key (people_ref) references people(id) on delete cascade
       )
inherits (template_people_languages);
comment on table people_languages is 'Languages spoken by a given person';
comment on column people_languages.people_ref is 'Reference of person - id field of people table';
comment on column people_languages.language_country is 'Reference of Language - language_country field of languages_countries table';
comment on column people_languages.mother is 'Flag telling if its mother language or not';
comment on column people_languages.preferred_language is 'Flag telling which language is preferred in communications';

create sequence users_languages_id_seq;
create table users_languages
       (
        id integer not null default nextval('users_languages_id_seq'),
        users_ref integer not null,
        constraint pk_users_languages primary key (id),
        constraint unq_users_languages unique (users_ref, language_country),
        constraint fk_users_languages_people foreign key (users_ref) references users(id) on delete cascade
       )
inherits (template_people_languages);
comment on table users_languages is 'Languages spoken by a given user';
comment on column users_languages.users_ref is 'Reference of user - id field of users table';
comment on column users_languages.language_country is 'Reference of Language - language_country field of languages_countries table';
comment on column users_languages.mother is 'Flag telling if its mother language or not';
comment on column users_languages.preferred_language is 'Flag telling which language is preferred in communications';

create sequence multimedia_id_seq;

create table multimedia
       (
        id integer not null default nextval('multimedia_id_seq'),
        is_digital boolean not null default true,
        type varchar not null default 'image',
        sub_type varchar,
        title varchar not null,
        title_indexed varchar not null,
        subject varchar not null default '/',
        coverage coverages not null default 'temporal',
        apercu_path varchar,
        copyright varchar,
        license varchar,
        uri varchar,
        descriptive_ts tsvector not null,
        descriptive_language_full_text full_text_language,
        creation_date date not null default '01/01/0001',
        creation_date_mask integer not null default 0,
        publication_date_from date not null default '01/01/0001',
        publication_date_from_mask integer not null default 0,
        publication_date_to date not null default '31/12/2038',
        publication_date_to_mask integer not null default 0,
        parent_ref integer,
        path varchar not null default '/',
        mime_type varchar,
        constraint pk_multimedia primary key (id),
	constraint fk_multimedia_parent_ref_multimedia foreign key (parent_ref) references multimedia(id) on delete cascade
       );
comment on table multimedia is 'Stores all multimedia objects encoded in DaRWIN 2.0';
comment on column multimedia.id is 'Unique identifier of a multimedia object';
comment on column multimedia.is_digital is 'Flag telling if the object is digital (true) or physical (false)';
comment on column multimedia.type is 'Main multimedia object type: image, sound, video,...';
comment on column multimedia.sub_type is 'Characterization of object type: article, publication in serie, book, glass plate,...';
comment on column multimedia.title is 'Object title';
comment on column multimedia.title_indexed is 'Indexed form of title field';
comment on column multimedia.subject is 'Multimedia object subject (as required by Dublin Core...)';
comment on column multimedia.coverage is 'Coverage of multimedia object: spatial or temporal (as required by Dublin Core...)';
comment on column multimedia.apercu_path is 'URI path to the thumbnail illustrating the object';
comment on column multimedia.copyright is 'Copyright notice';
comment on column multimedia.license is 'License notice';
comment on column multimedia.uri is 'URI of object if digital';
comment on column multimedia.creation_date is 'Object creation date';
comment on column multimedia.creation_date_mask is 'Mask used for object creation date display';
comment on column multimedia.publication_date_from is 'Object publication date from';
comment on column multimedia.publication_date_from_mask is 'Mask used for object publication begining date display';
comment on column multimedia.publication_date_to is 'Object publication date to';
comment on column multimedia.publication_date_to_mask is 'Mask used for object publication end date display';
comment on column multimedia.descriptive_ts is 'tsvector form of title and subject fields together';
comment on column multimedia.descriptive_language_full_text is 'Language used for descriptive_ts tsvector field composition';
comment on column multimedia.parent_ref is 'Reference of a parent multimedia. Such as an Article of a publication';
comment on column multimedia.path is 'Path of parent of the object (automaticaly filled)';
comment on column multimedia.mime_type is 'Mime/Type of the linked digital object';
create table template_people_users_comm_common
       (
        person_user_ref integer not null,
        entry varchar not null
       );
comment on table template_people_users_comm_common is 'Template table used to construct people communication tables (tel and e-mail)';
comment on column template_people_users_comm_common.person_user_ref is 'Reference of person/user - id field of people/users table';
comment on column template_people_users_comm_common.entry is 'Communication entry';
create table template_people_users_rel_common
       (
        person_user_role varchar
       );
comment on table template_people_users_rel_common is 'Template table used to propagate three field in different tables depending it''s people or user dedicated';
comment on column template_people_users_rel_common.person_user_role is 'Role the person/user have in the moral person he depends of';
create table template_people_users_addr_common
       (
        po_box varchar,
        extended_address varchar,
        locality varchar not null,
        region varchar,
        zip_code varchar,
        country varchar not null,
        address_parts_ts tsvector not null
       );
comment on table template_people_users_addr_common is 'Template table used to construct addresses tables for people/users';
comment on column template_people_users_addr_common.po_box is 'PO Box';
comment on column template_people_users_addr_common.extended_address is 'Address extension: State, Special post zip code characters,...';
comment on column template_people_users_addr_common.locality is 'Locality';
comment on column template_people_users_addr_common.region is 'Region';
comment on column template_people_users_addr_common.zip_code is 'zip code';
comment on column template_people_users_addr_common.country is 'Country';
comment on column template_people_users_addr_common.address_parts_ts is 'tsvector field containing vectorized form of all addresses fields: country, region, locality, extended address,...';

create sequence people_relationships_id_seq;

create table people_relationships
       (
        id integer not null default nextval('people_relationships_id_seq'),
        relationship_type varchar not null default 'belongs to',
        person_1_ref integer not null,
        person_2_ref integer not null,
        path varchar,
        activity_date_from_mask integer not null default 0,
        activity_date_from date not null default '01/01/0001',
        activity_date_to_mask integer not null default 0,
        activity_date_to date not null default '31/12/2038',
        constraint pk_people_relationships primary key (id),
        constraint fk_people_relationships_people_01 foreign key (person_1_ref) references people(id) on delete cascade,
        constraint fk_people_relationships_people_02 foreign key (person_2_ref) references people(id)
       )

inherits (template_people_users_rel_common);
comment on table people_relationships is 'Relationships between people - mainly between physical person and moral person: relationship of dependancy';
comment on column people_relationships.relationship_type is 'Type of relationship between two persons: belongs to, is department of, is section of, works for,...';
comment on column people_relationships.person_1_ref is 'Reference of person to be puted in relationship with an other - id field of people table';
comment on column people_relationships.person_2_ref is 'Reference of person puted the person puted in relationship with is dependant of - id field of people table';
comment on column people_relationships.person_user_role is 'Person role in the organization referenced';
comment on column people_relationships.activity_date_from is 'person activity period or person activity period in the organization referenced date from';
comment on column people_relationships.activity_date_from_mask is 'person activity period or person activity period in the organization referenced date from mask';
comment on column people_relationships.activity_date_to is 'person activity period or person activity period in the organization referenced date to';
comment on column people_relationships.activity_date_to_mask is 'person activity period or person activity period in the organization referenced date to mask';
comment on column people_relationships.path is 'Hierarchical path of the organization structure';

create sequence people_comm_id_seq;

create table people_comm
       (
        id integer not null default nextval('people_comm_id_seq'),
        comm_type varchar default 'phone/fax' not null,
        tag varchar not null default '',
        constraint pk_people_comm primary key (id),
        constraint fk_people_comm_people foreign key (person_user_ref) references people(id) on delete cascade
       )
inherits (template_people_users_comm_common);
comment on table people_comm is 'People phones and e-mails';
comment on column people_comm.id is 'Unique identifier of a person communication mean entry';
comment on column people_comm.person_user_ref is 'Reference of person - id field of people table';
comment on column people_comm.comm_type is 'Type of communication table concerned: phone or e-mail';
comment on column people_comm.entry is 'Communication entry';
comment on column people_comm.comm_type is 'Type of communication table concerned: address, phone or e-mail';
comment on column people_comm.tag is 'List of descriptive tags separated by , : internet, tel, fax, pager, public, private,...';

create sequence people_addresses_id_seq;

create table people_addresses
       (
        id integer not null default nextval('people_addresses_id_seq'),
        tag varchar not null default '',
        constraint pk_people_addresses primary key (id),
        constraint fk_people_addresses_people foreign key (person_user_ref) references people(id) on delete cascade
       )
inherits (template_people_users_comm_common, template_people_users_addr_common);
comment on table people_addresses is 'People addresses';
comment on column people_addresses.address_parts_ts is 'tsvector column used to search an address part';
comment on column people_addresses.id is 'Unique identifier of a person address';
comment on column people_addresses.person_user_ref is 'Reference of the person concerned - id field of people table';
comment on column people_addresses.po_box is 'PO Box';
comment on column people_addresses.extended_address is 'Address extension: State, zip code suffix,...';
comment on column people_addresses.entry is 'Street address';
comment on column people_addresses.locality is 'Locality';
comment on column people_addresses.country is 'Country';
comment on column people_addresses.region is 'Region';
comment on column people_addresses.zip_code is 'Zip code';
comment on column people_addresses.tag is 'List of descriptive tags: home, work,...';

create sequence users_comm_id_seq;

create table users_comm
       (
        id integer not null default nextval('users_comm_id_seq'),
        comm_type varchar not null default 'phone/fax',
        tag varchar not null default '',
        constraint pk_users_comm primary key (id),
        constraint fk_users_comm_users foreign key (person_user_ref) references users(id) on delete cascade
       )
inherits (template_people_users_comm_common);
comment on table users_comm is 'Users phones and e-mails';
comment on column users_comm.id is 'Unique identifier of a users communication mean entry';
comment on column users_comm.person_user_ref is 'Reference of user - id field of user table';
comment on column users_comm.comm_type is 'Type of communication table concerned: phone or e-mail';
comment on column users_comm.entry is 'Communication entry';
comment on column users_comm.comm_type is 'Type of communication table concerned: address, phone or e-mail';
comment on column users_comm.tag is 'List of descriptive tags: internet, tel, fax, pager, public, private,...';

create sequence users_addresses_id_seq;

create table users_addresses
       (
        id integer not null default nextval('users_addresses_id_seq'),
        organization_unit varchar,
        tag varchar not null default '',
        constraint pk_users_addresses primary key (id),
        constraint fk_users_addresses_users foreign key (person_user_ref) references users(id) on delete cascade
       )
inherits (template_people_users_rel_common, template_people_users_comm_common, template_people_users_addr_common);
comment on table users_addresses is 'Users addresses';
comment on column users_addresses.address_parts_ts is 'tsvector column used to search an address part';
comment on column users_addresses.id is 'Unique identifier of a user address';
comment on column users_addresses.person_user_ref is 'Reference of the user concerned - id field of users table';
comment on column users_addresses.po_box is 'PO Box';
comment on column users_addresses.extended_address is 'Address extension: State, zip code suffix,...';
comment on column users_addresses.entry is 'Street address';
comment on column users_addresses.locality is 'Locality';
comment on column users_addresses.country is 'Country';
comment on column users_addresses.region is 'Region';
comment on column users_addresses.zip_code is 'Zip code';
comment on column users_addresses.organization_unit is 'When a physical user is in relationship with a moral one, indicates the department or unit the user is related to';
comment on column users_addresses.person_user_role is 'User role in the organization referenced';
comment on column users_addresses.tag is 'List of descriptive tags: home, work,...';


create sequence users_login_info_id_seq;

create table users_login_infos
       (
        id integer not null default nextval('users_login_info_id_seq'),
        user_ref integer not null,
        login_type varchar not null default 'local',
        user_name varchar,
        password varchar,
        login_system varchar,
        renew_hash varchar,
        constraint pk_users_login_infos primary key (id),
        constraint unq_users_login_infos unique (user_ref, login_type),
        constraint unq_users_login_infos_user_name unique (user_name, login_type),
        constraint fk_users_login_infos_users foreign key (user_ref) references users(id) on delete cascade
       );
comment on table users_login_infos is 'Contains the login/password informations of DaRWIN 2 users';
comment on column users_login_infos.user_ref is 'Identifier of user - id field of users table';
comment on column users_login_infos.login_type is 'Type of identification system';
comment on column users_login_infos.user_name is 'For some system (local, ldap, kerberos,...) provides the username (encrypted form)';
comment on column users_login_infos.password is 'For some system (local, ldap, kerberos,...) provides the password (encrypted form)';
comment on column users_login_infos.login_system is 'For some system (shibbolet, openID,...) provides the user id';
comment on column users_login_infos.renew_hash is 'Hashed key defined when asking to renew a password';

create table template_people_users_multimedia
       (
        person_user_ref integer not null,
        object_ref integer not null,
        category varchar not null default 'avatar',
        constraint fk_template_people_users_multimedia foreign key (object_ref) references multimedia(id) on delete cascade
       );
comment on table template_people_users_multimedia is 'Template table used to construct people/users associated multimedia table';
comment on column template_people_users_multimedia.person_user_ref is 'Reference of person/user - id field of people/users table';
comment on column template_people_users_multimedia.object_ref is 'Reference of multimedia object - id field of multimedia table';
comment on column template_people_users_multimedia.category is 'Category qualifying the multimedia object use for this person';
create table people_multimedia
       (
        constraint unq_people_multimedia unique (person_user_ref, object_ref, category),
        constraint fk_people_multimedia_people foreign key (person_user_ref) references people(id) on delete cascade
       )
inherits (template_people_users_multimedia);
comment on table people_multimedia is 'Multimedia objects linked to persons';
comment on column people_multimedia.person_user_ref is 'Reference of person concerned - id field of people table';
comment on column people_multimedia.object_ref is 'Reference of multimedia object associated - id field of multimedia table';
comment on column people_multimedia.category is 'Object catégory: avatar, spelled name,...';
create table users_multimedia
       (
        constraint unq_users_multimedia unique (person_user_ref, object_ref, category),
        constraint fk_users_multimedia_users foreign key (person_user_ref) references users(id) on delete cascade
       )
inherits (template_people_users_multimedia);
comment on table users_multimedia is 'Multimedia objects linked to users';
comment on column users_multimedia.person_user_ref is 'Reference of user concerned - id field of users table';
comment on column users_multimedia.object_ref is 'Reference of multimedia object associated - id field of multimedia table';
comment on column users_multimedia.category is 'Object catégory: avatar, spelled name,...';

create sequence collections_id_seq;

create table collections
       (
        id integer not null default nextval('collections_id_seq'),
        collection_type varchar not null default 'mix',
        code varchar not null,
        name varchar not null,
        name_indexed varchar not null,
        institution_ref integer not null,
        main_manager_ref integer not null,
        parent_ref integer,
        path varchar not null,
        code_auto_increment boolean not null default false,
        code_last_value integer not null default 0,
        code_prefix varchar,
        code_prefix_separator varchar,
        code_suffix varchar,
        code_suffix_separator varchar,
        code_part_code_auto_copy boolean not null default false,
        is_public boolean not null default true,
        constraint pk_collections primary key (id),
        constraint fk_collections_institutions foreign key (institution_ref) references people(id),
        constraint fk_collections_collections foreign key (parent_ref) references collections(id) on delete cascade,
        constraint fk_collections_users foreign key (main_manager_ref) references users(id),
        constraint chk_main_manager_ref check (main_manager_ref > 0),
        constraint unq_collections unique (institution_ref, path, code)
       );
comment on table collections is 'List of all collections encoded in DaRWIN 2';
comment on column collections.id is 'Unique identifier of a collection';
comment on column collections.collection_type is 'Type of collection: physical for a collection of only physical objects, observations for a collection of only observations, mix for any kind of entry catalogued in collection';
comment on column collections.code is 'Code given to collection';
comment on column collections.name is 'Collection name';
comment on column collections.name_indexed is 'Collection name indexed';
comment on column collections.institution_ref is 'Reference of institution current collection belongs to - id field of people table';
comment on column collections.parent_ref is 'Recursive reference to collection table itself to represent collection parenty/hierarchy';
comment on column collections.path is 'Descriptive path for collection hierarchy, each level separated by a /';
comment on column collections.main_manager_ref is 'Reference of collection main manager - id field of users table';
comment on column collections.code_auto_increment is 'Flag telling if the numerical part of a code has to be incremented or not';
comment on column collections.code_last_value is 'Value of the last numeric code given in this collection when auto increment is/was activated';
comment on column collections.code_prefix is 'Default code prefix to be used for specimens encoded in this collection';
comment on column collections.code_prefix_separator is 'Character chain used to separate code prefix from code core';
comment on column collections.code_suffix is 'Default code suffix to be used for specimens encoded in this collection';
comment on column collections.code_suffix_separator is 'Character chain used to separate code suffix from code core';
comment on column collections.code_part_code_auto_copy is 'Flag telling if the whole specimen code has to be copied for a part, when inserting a new one';
comment on column collections.is_public is 'Flag telling if the collection can be found in the public search';
create table template_collections_users
       (
        collection_ref integer not null default 0,
        user_ref integer not null default 0
       );
comment on table template_collections_users is 'Template table used to construct collections rights tables';
comment on column template_collections_users.collection_ref is 'Reference of collection concerned - id field of collections table';
comment on column template_collections_users.user_ref is 'Reference of user - id field of users table';

create sequence collections_rights_id_seq;

create table collections_rights
       (
        id integer not null default nextval('collections_rights_id_seq'),
        db_user_type smallint not null default 1,
        constraint pk_collections_right primary key (id),
        constraint fk_collections_rights_users foreign key (user_ref) references users(id) on delete cascade,
        constraint fk_collections_rights_collections foreign key (collection_ref) references collections(id) on delete cascade,
        constraint unq_collections_rights unique (collection_ref, user_ref)
       )
inherits (template_collections_users);
comment on table collections_rights is 'List of rights of given users on given collections';
comment on column collections_rights.id is 'Unique identifier for collection rights';
comment on column collections_rights.collection_ref is 'Reference of collection concerned - id field of collections table';
comment on column collections_rights.user_ref is 'Reference of user - id field of users table';
comment on column collections_rights.db_user_type is 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';


-- create sequence users_coll_rights_asked_id_seq;
--
-- create table users_coll_rights_asked
--        (
--         id integer not null default nextval('users_coll_rights_asked_id_seq'),
--         field_group_name varchar not null,
--         db_user_type smallint not null,
--         searchable boolean not null default true,
--         visible boolean not null default true,
--         motivation varchar not null,
--         asking_date_time update_date_time,
--         with_sub_collections boolean not null default true,
--         constraint pk_users_coll_rights_asked primary key (id),
--         constraint unq_users_coll_rights_asked unique (collection_ref, user_ref, field_group_name, db_user_type),
--         constraint fk_users_coll_rights_asked_collections foreign key (collection_ref) references collections(id) on delete cascade,
--         constraint fk_users_coll_rights_asked_users foreign key (user_ref) references users(id) on delete cascade
--        )
-- inherits (template_collections_users);
-- comment on table users_coll_rights_asked is 'List all rights asked by a registered user or encoder to collection managers';
-- comment on column users_coll_rights_asked.collection_ref is 'Reference of collection concerned - id field of collections table';
-- comment on column users_coll_rights_asked.user_ref is 'Reference of user - id field of users table';
-- comment on column users_coll_rights_asked.field_group_name is 'Group of fields name';
-- comment on column users_coll_rights_asked.db_user_type is 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';
-- comment on column users_coll_rights_asked.searchable is 'Flag telling if the field group is searchable - meaning these fields will appear as search criterias in the search form';
-- comment on column users_coll_rights_asked.visible is 'Flag telling if the field group is visible - meaning these fields will be displayable in the result table';
-- comment on column users_coll_rights_asked.motivation is 'Motivation given by asker';
-- comment on column users_coll_rights_asked.asking_date_time is 'Telling when right ask was done';
-- comment on column users_coll_rights_asked.with_sub_collections is 'Rights are asked on a single collection or on this collection with all the sub-collections included ?';
--

create sequence users_workflow_id_seq;

create table users_workflow
       (
        id integer not null default nextval('users_workflow_id_seq'),
        user_ref integer not null,
        status varchar not null default 'to check',
        modification_date_time update_date_time,
        comment varchar,
        constraint pk_users_workflow primary key (id),
        constraint fk_users_workflow_users foreign key (user_ref) references users(id) on delete cascade
       )
inherits (template_table_record_ref);
comment on table users_workflow is 'Workflow information for each record encoded';
comment on column users_workflow.user_ref is 'Reference of user - id field of users table';
comment on column users_workflow.referenced_relation is 'Reference-Name of table concerned';
comment on column users_workflow.record_id is 'ID of record a workflow is defined for';
comment on column users_workflow.status is 'Record status: to correct, to be corrected or published';
comment on column users_workflow.modification_date_time is 'Date and time of status change - last date/time is used as actual status, but helps also to keep an history of status change';
comment on column users_workflow.comment is 'Complementary comments';

create sequence users_tracking_id_seq;

create table users_tracking
       (
        id bigint not null default nextval('users_tracking_id_seq'),
        referenced_relation varchar not null,
        record_id integer not null,
        user_ref integer not null,
        action varchar not null default 'insert',
        old_value hstore,
        new_value hstore,
        modification_date_time update_date_time,
        constraint pk_users_tracking_pk primary key (id),
        constraint fk_users_tracking_users foreign key (user_ref) references users(id)
       );
comment on table users_tracking is 'Tracking of users actions on tables';
comment on column users_tracking.referenced_relation is 'Reference-Name of table concerned';
comment on column users_tracking.record_id is 'ID of record concerned';
comment on column users_tracking.id is 'Unique identifier of a table track entry';
comment on column users_tracking.user_ref is 'Reference of user having made an action - id field of users table';
comment on column users_tracking.action is 'Action done on table record: insert, update, delete';
comment on column users_tracking.modification_date_time is 'Track date and time';

create sequence collection_maintenance_id_seq;

create table collection_maintenance
       (
        id integer not null default nextval('collection_maintenance_id_seq'),
        people_ref integer not null,
        category varchar not null default 'action',
        action_observation varchar not null,
        description varchar,
        description_ts tsvector,
        modification_date_time update_date_time,
        modification_date_mask int not null default '0',
        constraint pk_collection_maintenance primary key (id),
        constraint fk_collection_maintenance_users foreign key (people_ref) references people(id)
       )
inherits (template_table_record_ref);
comment on table collection_maintenance is 'History of specimen maintenance';
comment on column collection_maintenance.id is 'Unique identifier of a specimen maintenance';
comment on column collection_maintenance.referenced_relation is 'Reference of table a maintenance entry has been created for';
comment on column collection_maintenance.record_id is 'ID of record a maintenance entry has been created for';
comment on column collection_maintenance.people_ref is 'Reference of person having done an action or an observation';
comment on column collection_maintenance.category is 'Action or Observation';
comment on column collection_maintenance.action_observation is 'Action or observation done';
comment on column collection_maintenance.description is 'Complementary description';
comment on column collection_maintenance.description_ts is 'tsvector form of description field';
comment on column collection_maintenance.modification_date_time is 'Last update date/time';

create sequence my_saved_searches_id_seq;
create table my_saved_searches
       (
        id integer not null default nextval('my_saved_searches_id_seq'),
        user_ref integer not null,
        name varchar not null default 'default',
        search_criterias varchar not null,
        favorite boolean not null default false,
        modification_date_time update_date_time,
        visible_fields_in_result varchar not null,
	is_only_id boolean not null default false,
	subject varchar not null default 'specimen',
        constraint pk_my_saved_searches primary key (id),
        constraint unq_my_saved_searches unique (user_ref, name),
        constraint fk_my_saved_searches_users foreign key (user_ref) references users(id) on delete cascade
       );
comment on table my_saved_searches is 'Stores user''s saved searches but also (by default) the last search done';
comment on column my_saved_searches.user_ref is 'Reference of user having saved a search';
comment on column my_saved_searches.name is 'Name given by user to his/her saved search';
comment on column my_saved_searches.search_criterias is 'String field containing the serialization of search criterias';
comment on column my_saved_searches.favorite is 'Flag telling if saved search concerned is one of the favorites or not';
comment on column my_saved_searches.modification_date_time is 'Last modification or entry date and time';
comment on column my_saved_searches.visible_fields_in_result is 'Array of fields that were set visible in the result table at the time the search was saved';
comment on column my_saved_searches.is_only_id is 'Tell if the search only contains saved specimen (ids) or it is a normal saved search';

create sequence my_widgets_id_seq;

create table my_widgets
       (
        id integer not null default nextval('my_widgets_id_seq'),
        user_ref integer not null,
        category varchar not null default 'board_widget',
        group_name varchar not null,
        order_by smallint not null default 1,
        col_num smallint not null default 1,
        mandatory boolean not null default false,
        visible boolean not null default true,
        opened boolean not null default true,
        color varchar not null default '#5BAABD',
        is_available boolean not null default false,
        icon_ref integer,
        title_perso varchar(32),
        collections varchar not null default ',',
        all_public boolean not null default false,
        constraint pk_my_widgets primary key (id),
        constraint unq_my_widgets unique (user_ref, category, group_name),
        constraint fk_my_widgets_users foreign key (user_ref) references users(id) on delete cascade,
        constraint fk_my_widgets_multimedia foreign key (icon_ref) references multimedia(id)
       );
comment on table my_widgets is 'Stores user''s preferences for customizable page elements - widgets mainly';
comment on column my_widgets.user_ref is 'Reference of user concerned - id field of users table';
comment on column my_widgets.category is 'Customizable page element category: board widget, encoding widget,...';
comment on column my_widgets.group_name is 'Customizable page element name';
comment on column my_widgets.order_by is 'Absolute order by between page element name';
comment on column my_widgets.col_num is 'Column number - tells in which column the page element concerned is';
comment on column my_widgets.mandatory is 'Flag telling if the page element can be closed or not';
comment on column my_widgets.visible is 'Flag telling if the page element is on the board or in the widget chooser';
comment on column my_widgets.opened is 'Flag telling if the page element is opened by default or not';
comment on column my_widgets.color is 'Color given to page element by user';
comment on column my_widgets.is_available is 'Flag telling if the widget can be used or not';
comment on column my_widgets.icon_ref is 'Reference of multimedia icon to be used before page element title';
comment on column my_widgets.title_perso is 'Page element title given by user';
comment on column my_widgets.collections is 'list of collections whitch user_ref has rights to see';
comment on column my_widgets.all_public is 'Set to determine if the widget is public by default or not';

create table template_classifications
       (
        name varchar not null,
        name_indexed tsvector not null,
        name_order_by varchar,
        level_ref integer not null,
        status varchar not null default 'valid',
        local_naming boolean not null default false,
        color varchar,
        path varchar not null default '/',
        parent_ref integer not null default 0
       );
comment on table template_classifications is 'Template table used to construct every common data in each classifications tables (taxonomy, chronostratigraphy, lithostratigraphy,...)';
comment on column template_classifications.name is 'Classification unit name';
comment on column template_classifications.name_indexed is 'TS Vector Indexed form of name field';
comment on column template_classifications.name_order_by is 'Indexed form of name field for ordering';
comment on column template_classifications.level_ref is 'Reference of classification level the unit is encoded in';
comment on column template_classifications.status is 'Validitiy status: valid, invalid, in discussion';
comment on column template_classifications.local_naming is 'Flag telling the appelation is local or internationally recognized';
comment on column template_classifications.color is 'Hexadecimal value of color associated to the unit';
comment on column template_classifications.path is 'Hierarchy path (/ for root)';
comment on column template_classifications.parent_ref is 'Id of parent - id field from table itself';

create sequence classification_keywords_id_seq;

create table classification_keywords
  (
    id integer not null default nextval('classification_keywords_id_seq'),
    keyword_type varchar not null default 'name',
    keyword varchar not null,
    keyword_indexed varchar not null,
    constraint pk_classification_keywords_id primary key (id)
  )
inherits (template_table_record_ref);

comment on table classification_keywords is 'Help user to tag-label each part of full name in classifications';
comment on column classification_keywords.referenced_relation is 'Name of classifification table: taxonomy, lithology,...';
comment on column classification_keywords.record_id is 'Id of record concerned';
comment on column classification_keywords.keyword_type is 'Keyword type: name, year, authoritative keyword,...';
comment on column classification_keywords.keyword is 'Keyword';

create sequence classification_synonymies_id_seq;
create sequence classification_synonymies_group_id_seq;
create table classification_synonymies
	(
    id integer not null default nextval('classification_synonymies_id_seq'),
    group_id integer not null,
    group_name varchar not null,
    is_basionym boolean DEFAULT false,
    order_by integer not null default 0,
    constraint unq_synonym unique (referenced_relation, record_id, group_id),
    constraint pk_synonym_id primary key (id)
	)
inherits (template_table_record_ref);

comment on table classification_synonymies is 'Table containing classification synonymies';
comment on column classification_synonymies.referenced_relation is 'Classification table concerned';
comment on column classification_synonymies.record_id is 'Id of record placed in group as a synonym';
comment on column classification_synonymies.group_name is 'Name of group under which synonyms are placed';
comment on column classification_synonymies.group_id is 'Id given to group';
comment on column classification_synonymies.order_by is 'Order by used to qualify order amongst synonyms - used mainly for senio and junior synonyms';
comment on column classification_synonymies.is_basionym is 'If record is a basionym';

create sequence taxonomy_id_seq;

create table taxonomy
       (
        id integer not null default nextval('taxonomy_id_seq'),
        extinct boolean default false not null,
        constraint pk_taxonomy primary key (id),
        constraint unq_taxonomy unique (path, name_indexed, level_ref),
        constraint fk_taxonomy_level_ref_catalogue_levels foreign key (level_ref) references catalogue_levels(id),
        constraint fk_taxonomy_parent_ref_taxonomy foreign key (parent_ref) references taxonomy(id)
       )
inherits (template_classifications);
comment on table taxonomy is 'Taxonomic classification table';
comment on column taxonomy.id is 'Unique identifier of a classification unit';
comment on column taxonomy.name is 'Classification unit name';
comment on column taxonomy.name_indexed is 'Indexed form of name field';
comment on column taxonomy.level_ref is 'Reference of classification level the unit is encoded in';
comment on column taxonomy.status is 'Validitiy status: valid, invalid, in discussion';
comment on column taxonomy.extinct is 'Tells if taxonomy is extinct or not';
comment on column taxonomy.path is 'Hierarchy path (/ for root)';
comment on column taxonomy.parent_ref is 'Id of parent - id field from table itself';

create sequence chronostratigraphy_id_seq;

create table chronostratigraphy
       (
        id integer not null default nextval('chronostratigraphy_id_seq'),
        lower_bound numeric(10,3),
        upper_bound numeric(10,3),
       constraint pk_chronostratigraphy primary key (id),
       constraint unq_chronostratigraphy unique (path, name_indexed, level_ref),
       constraint fk_chronostratigraphy_catalogue_levels foreign key (level_ref) references catalogue_levels(id),
       constraint fk_chronostratigraphy_parent_ref_chronostratigraphy foreign key (parent_ref) references chronostratigraphy(id) on delete cascade
       )
inherits (template_classifications);
comment on table chronostratigraphy is 'List of chronostratigraphic units';
comment on column chronostratigraphy.id is 'Unique identifier of a classification unit';
comment on column chronostratigraphy.name is 'Classification unit name';
comment on column chronostratigraphy.name_indexed is 'Indexed form of name field';
comment on column chronostratigraphy.level_ref is 'Reference of classification level the unit is encoded in';
comment on column chronostratigraphy.status is 'Validitiy status: valid, invalid, in discussion';
comment on column chronostratigraphy.lower_bound is 'Lower age boundary in years';
comment on column chronostratigraphy.upper_bound is 'Upper age boundary in years';
comment on column chronostratigraphy.path is 'Hierarchy path (/ for root)';
comment on column chronostratigraphy.parent_ref is 'Id of parent - id field from table itself';

create sequence lithostratigraphy_id_seq;

create table lithostratigraphy
       (
        id integer not null default nextval('lithostratigraphy_id_seq'),
        constraint pk_lithostratigraphy primary key (id),
        constraint unq_lithostratigraphy unique (path, name_indexed, level_ref),
        constraint fk_lithostratigraphy_catalogue_levels foreign key (level_ref) references catalogue_levels(id),
        constraint fk_lithostratigraphy_parent_ref_lithostratigraphy foreign key (parent_ref) references lithostratigraphy(id) on delete cascade
       )
inherits (template_classifications);
comment on table lithostratigraphy is 'List of lithostratigraphic units';
comment on column lithostratigraphy.id is 'Unique identifier of a classification unit';
comment on column lithostratigraphy.name is 'Classification unit name';
comment on column lithostratigraphy.name_indexed is 'Indexed form of name field';
comment on column lithostratigraphy.level_ref is 'Reference of classification level the unit is encoded in';
comment on column lithostratigraphy.status is 'Validitiy status: valid, invalid, in discussion';
comment on column lithostratigraphy.path is 'Hierarchy path (/ for root)';
comment on column lithostratigraphy.parent_ref is 'Id of parent - id field from table itself';

create sequence mineralogy_id_seq;

create table mineralogy
       (
        id integer not null default nextval('mineralogy_id_seq'),
        code varchar not null,
        classification varchar not null default 'strunz',
        formule varchar,
        formule_indexed varchar,
        cristal_system varchar,
        constraint pk_mineralogy primary key (id),
        constraint unq_mineralogy unique (path, name_indexed, level_ref),
        constraint fk_mineralogy_catalogue_levels foreign key (level_ref) references catalogue_levels(id),
        constraint fk_mineralogy_parent_ref_mineralogy foreign key (parent_ref) references mineralogy(id) on delete cascade
       )
inherits (template_classifications);
comment on table mineralogy is 'List of mineralogic units';
comment on column mineralogy.id is 'Unique identifier of a classification unit';
comment on column mineralogy.name is 'Classification unit name';
comment on column mineralogy.name_indexed is 'Indexed form of name field';
comment on column mineralogy.level_ref is 'Reference of classification level the unit is encoded in';
comment on column mineralogy.status is 'Validitiy status: valid, invalid, in discussion';
comment on column mineralogy.code is 'Classification code given to mineral - in classification chosen - Strunz by default';
comment on column mineralogy.classification is 'Classification system used to describe mineral: strunz, dana,...';
comment on column mineralogy.formule is 'Chemical formulation';
comment on column mineralogy.formule_indexed is 'Indexed form of foumule field';
comment on column mineralogy.cristal_system is 'Cristal system defining the mineral structure: isometric, hexagonal,...';
comment on column mineralogy.path is 'Hierarchy path (/ for root)';
comment on column mineralogy.parent_ref is 'Id of parent - id field from table itself';

create sequence lithology_id_seq;

create table lithology
       (
        id integer not null default nextval('lithology_id_seq'),
        constraint pk_lithology primary key (id),
        constraint unq_lithology unique (path, name_indexed, level_ref),
        constraint fk_lithology_parent_ref_lithology foreign key (parent_ref) references lithology(id) on delete cascade,
        constraint fk_lithology_catalogue_levels foreign key (level_ref) references catalogue_levels(id)
       )
inherits (template_classifications);
comment on table lithology is 'List of lithologic units';
comment on column lithology.id is 'Unique identifier of a classification unit';
comment on column lithology.name is 'Classification unit name';
comment on column lithology.name_indexed is 'Indexed form of name field';
comment on column lithology.level_ref is 'Reference of classification level the unit is encoded in';
comment on column lithology.status is 'Validitiy status: valid, invalid, in discussion';
comment on column lithology.path is 'Hierarchy path (/ for root)';
comment on column lithology.parent_ref is 'Id of parent - id field from table itself';

create sequence habitats_id_seq;

create table habitats
       (
        id integer not null default nextval('habitats_id_seq'),
        code varchar not null,
        code_indexed varchar not null default '/',
        description varchar not null,
        description_ts tsvector not null,
        description_language_full_text full_text_language,
        habitat_system varchar not null default 'eunis',
	parent_ref integer,
        path varchar not null default '/',
        constraint pk_habitats primary key (id),
	constraint fk_habitats_parent_ref foreign key (parent_ref) references habitats(id) on delete cascade,
        constraint unq_habitats unique (path, code_indexed, habitat_system)
       );
comment on table habitats is 'Habitats classifications';
comment on column habitats.id is 'Unique identifier of a habitat';
comment on column habitats.code is 'Code given to this habitat in the classification encoded';
comment on column habitats.code_indexed is 'Indexed form of code field';
comment on column habitats.description is 'General description of the habitat';
comment on column habitats.description_ts is 'Indexed form of description field ready to be used with to_tsvector full text search function';
comment on column habitats.description_language_full_text is 'Language used to compose the description_ts tsvector field';
comment on column habitats.habitat_system is 'System used to describe habitat encoded';
comment on column habitats.parent_ref is 'Reference of parent habitat';
comment on column habitats.path is 'Hierarchy path (/ for root)';
create table multimedia_keywords
       (
        object_ref integer not null,
        keyword varchar not null,
        keyword_indexed varchar not null,
        constraint unq_multimedia_keywords unique (object_ref, keyword_indexed),
        constraint fk_multimedia_keywords_multimedia foreign key (object_ref) references multimedia(id) on delete cascade
       );
comment on table multimedia_keywords is 'List of keywords associated to a multimedia object - encoded in the keywords field on the interface';
comment on column multimedia_keywords.object_ref is 'Reference of multimedia object concerned';
comment on column multimedia_keywords.keyword is 'Keyword associated';
comment on column multimedia_keywords.keyword_indexed is 'Indexed form of keyword field';
create table soortenregister
       (
        taxa_ref integer not null default 0,
        gtu_ref integer not null default 0,
        habitat_ref integer not null default 0,
        date_from date,
        date_to date,
        constraint fk_soortenregister_taxonomy foreign key (taxa_ref) references taxonomy(id) on delete cascade,
        constraint fk_soortenregister_gtu foreign key (gtu_ref) references gtu(id) on delete cascade,
        constraint fk_soortenregister_habitats foreign key (habitat_ref) references habitats(id) on delete cascade
       );
comment on table soortenregister is 'Species register table - Indicates the presence of a certain species in a certain habitat at a given place from time to time';
comment on column soortenregister.taxa_ref is 'Reference of taxon concerned - id field of taxonomy table';
comment on column soortenregister.gtu_ref is 'Reference of gtu concerned - id field of gtu table';
comment on column soortenregister.habitat_ref is 'Reference of habitat concerned - id field of habitats table';
comment on column soortenregister.date_from is 'From date association definition';
comment on column soortenregister.date_to is 'To date association definition';

create sequence igs_id_seq;

create table igs
       (
         id integer default nextval('igs_id_seq'),
         ig_num varchar not null,
         ig_num_indexed varchar not null,
         ig_date_mask integer not null default 0,
         ig_date date not null default '01/01/0001',
         constraint pk_igs primary key (id),
         constraint unq_igs unique (ig_num)
       );

comment on table igs is 'Inventory table - register all ig (inventory general) numbers given in RBINS';
comment on column igs.id is 'Unique identifier of an ig reference';
comment on column igs.ig_num is 'IG number';
comment on column igs.ig_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column igs.ig_date is 'Date of ig number creation';

create sequence specimens_id_seq;

create table specimens
       (
        id integer not null default nextval('specimens_id_seq'),
        category varchar not null default 'physical',
        collection_ref integer not null default 0,
        expedition_ref integer not null default 0,
        gtu_ref integer not null default 0,
        taxon_ref integer not null default 0,
        litho_ref integer not null default 0,
        chrono_ref integer not null default 0,
        lithology_ref integer not null default 0,
        mineral_ref integer not null default 0,
        host_taxon_ref integer not null default 0,
        host_specimen_ref integer,
        host_relationship varchar,
        acquisition_category varchar not null default '',
        acquisition_date_mask integer not null default 0,
        acquisition_date date not null default '01/01/0001',
        station_visible boolean not null default true,
        multimedia_visible boolean not null default true,
        ig_ref integer,
        constraint pk_specimens primary key (id),
        constraint unq_specimens unique (collection_ref, expedition_ref, gtu_ref, taxon_ref, litho_ref, chrono_ref, lithology_ref, mineral_ref, host_taxon_ref, acquisition_category, acquisition_date, ig_ref),
        constraint fk_specimens_expeditions foreign key (expedition_ref) references expeditions(id),
        constraint fk_specimens_gtu foreign key (gtu_ref) references gtu(id),
        constraint fk_specimens_collections foreign key (collection_ref) references collections(id),
        constraint fk_specimens_taxonomy foreign key (taxon_ref) references taxonomy(id),
        constraint fk_specimens_lithostratigraphy foreign key (litho_ref) references lithostratigraphy(id),
        constraint fk_specimens_lithology foreign key (lithology_ref) references lithology(id),
        constraint fk_specimens_mineralogy foreign key (mineral_ref) references mineralogy(id),
        constraint fk_specimens_chronostratigraphy foreign key (chrono_ref) references chronostratigraphy(id),
        constraint fk_specimens_host_taxonomy foreign key (host_taxon_ref) references taxonomy(id),
        constraint fk_specimens_host_specimen foreign key (host_specimen_ref) references specimens(id) on delete set null,
        constraint fk_specimens_igs foreign key (ig_ref) references igs(id)
       );
comment on table specimens is 'Specimens or batch of specimens stored in collection';
comment on column specimens.id is 'Unique identifier of a specimen or batch of specimens';
comment on column specimens.collection_ref is 'Reference of collection the specimen is grouped under - id field of collections table';
comment on column specimens.expedition_ref is 'When acquisition category is expedition, contains the reference of the expedition having conducted to the current specimen capture - id field of expeditions table';
comment on column specimens.gtu_ref is 'Reference of the sampling location the specimen is coming from - id field of gtu table';
comment on column specimens.litho_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of lithostratigraphic unit the specimen have been found into - id field of lithostratigraphy table';
comment on column specimens.chrono_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of chronostratigraphic unit the specimen have been found into - id field of chronostratigraphy table';
comment on column specimens.taxon_ref is 'When encoding a ''living'' specimen, contains the reference of the taxon unit defining the specimen - id field of taxonomy table';
comment on column specimens.host_relationship is 'When current specimen encoded is in a host relationship with an other specimen or taxon, this field contains the type of relationship between them: symbiosis, parasitism, saprophytism,...';
comment on column specimens.host_specimen_ref is 'When current specimen encoded is in a host relationship with an other specimen, this field contains reference of the host specimen - recursive reference';
comment on column specimens.acquisition_category is 'Describe how the specimen was collected: expedition, donation,...';
comment on column specimens.acquisition_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column specimens.acquisition_date is 'Date Composed (if possible) of the acquisition';
comment on column specimens.multimedia_visible is 'Flag telling if the multimedia attached to this specimen can be visible or not';
comment on column specimens.station_visible is 'Flag telling if the sampling location can be visible or must be hidden for the specimen encoded';
comment on column specimens.lithology_ref is 'Reference of a rock classification unit associated to the specimen encoded - id field of lithology table';
comment on column specimens.mineral_ref is 'Reference of a mineral classification unit associated to the specimen encoded - id field of mineralogy table';
comment on column specimens.host_taxon_ref is 'Reference of taxon definition defining the host which holds the current specimen - id field of taxonomy table';
comment on column specimens.ig_ref is 'Reference of ig number this specimen has been associated to';
comment on column specimens.category is 'Type of specimen encoded: a physical object stored in collections, an observation, a figurate specimen,...';

create sequence codes_id_seq;

create table codes
       (
        id integer not null default nextval('codes_id_seq'),
        code_category varchar not null default 'main',
        code_prefix varchar,
        code_prefix_separator varchar,
        code varchar,
        code_suffix varchar,
        code_suffix_separator varchar,
        full_code_indexed tsvector not null,
        full_code_order_by varchar not null,
        code_date timestamp not null default '0001-01-01 00:00:00',
        code_date_mask integer not null default 0,
        code_num integer default 0,
        constraint pk_codes primary key (id),
        constraint unq_codes unique (referenced_relation, record_id, full_code_order_by,code_category)
       )
inherits (template_table_record_ref);

comment on table codes is 'Template used to construct the specimen codes tables';
comment on column codes.id is 'Unique identifier of a code';
comment on column codes.code_category is 'Category of code: main, secondary, temporary,...';
comment on column codes.code_prefix is 'Code prefix - entire code if all alpha, begining character part if code is made of characters and numeric parts';
comment on column codes.code_prefix_separator is 'Separtor used between code core and code prefix';
comment on column codes.code is 'Numerical part of code - but not forced: if users want to use it as alphanumerical code - possible too';
comment on column codes.code_suffix is 'For codes made of characters and numerical parts, this field stores the last alpha part of code';
comment on column codes.code_suffix_separator is 'Separtor used between code core and code suffix';
comment on column codes.full_code_indexed is 'ts_vector code composition coming from all code parts - used for searching specimen codes';
comment on column codes.full_code_order_by is 'Full code composition by code_prefix, code and code suffix concatenation and indexed for unique check purpose';
comment on column codes.code_date is 'Date of code creation (fuzzy date)';
comment on column codes.code_date_mask is 'Mask used for code date';
comment on column codes.referenced_relation is 'Reference name of table concerned';
comment on column codes.record_id is 'Identifier of record concerned';

create sequence specimen_individuals_id_seq;

create table specimen_individuals
       (
        id integer not null default nextval('specimen_individuals_id_seq'),
        specimen_ref integer not null,
        type varchar not null default 'specimen',
        type_group varchar not null default 'specimen',
        type_search varchar not null default 'specimen',
        sex varchar not null default 'undefined',
        stage varchar not null default 'undefined',
        state varchar not null default 'not applicable',
        social_status varchar not null default 'not applicable',
        rock_form varchar not null default 'not applicable',
        specimen_individuals_count_min integer not null default 1,
        specimen_individuals_count_max integer not null default 1,
        with_parts boolean not null default false,
        constraint pk_specimen_individuals primary key (id),
        constraint unq_specimen_individuals unique (specimen_ref, type, sex, stage, state, social_status, rock_form),
        constraint fk_specimen_individuals_specimens foreign key (specimen_ref) references specimens(id) on delete cascade,
        constraint chk_chk_specimen_individuals_minmax check (specimen_individuals_count_min <= specimen_individuals_count_max),
        constraint chk_chk_specimens_individuals_min check (specimen_individuals_count_min >= 0)
       );
comment on table specimen_individuals is 'Stores characterized individudals from a specimen batch';
comment on column specimen_individuals.id is 'Unique identifier of a specimen individual';
comment on column specimen_individuals.specimen_ref is 'Reference of a specimen batch the individual(s) is/are extracted from';
comment on column specimen_individuals.type is 'Special status given to individual(s): holotype, paratype,...';
comment on column specimen_individuals.type_group is 'For some special status, a common appelation is used - ie: topotype and cotype are joined into a common appelation of syntype';
comment on column specimen_individuals.type_search is 'On the interface, the separation in all special status is not suggested for non official appelations. For instance, an unified grouping name is provided: type for non official appelation,...';
comment on column specimen_individuals.sex is 'Individual sex: male , female,...';
comment on column specimen_individuals.stage is 'Individual stage: adult, juvenile,...';
comment on column specimen_individuals.state is 'Individual state - a sex complement: ovigerous, pregnant,...';
comment on column specimen_individuals.social_status is 'For social specimens, give the social status/role of individual in colony';
comment on column specimen_individuals.rock_form is 'For rock specimens/individuals, a descriptive form can be given: polygonous,...';
comment on column specimen_individuals.specimen_individuals_count_min is 'Minimum number of individuals';
comment on column specimen_individuals.specimen_individuals_count_max is 'Maximum number of individuals';
comment on column specimen_individuals.with_parts is 'Flag telling if they are parts for current individual - Triggerly composed';

create sequence specimen_parts_id_seq;

create table specimen_parts
       (
        id integer not null default nextval('specimen_parts_id_seq'),
        parent_ref integer,
        path varchar not null default '/',
        specimen_individual_ref integer not null,
        specimen_part varchar not null default 'specimen',
        complete boolean not null default true,
        institution_ref integer,
        building varchar,
        floor varchar,
        room varchar,
        row varchar,
        shelf varchar,
        container varchar,
        sub_container varchar,
        container_type varchar not null default 'container',
        sub_container_type varchar not null default 'container',
        container_storage varchar not null default 'dry',
        sub_container_storage varchar not null default 'dry',
        surnumerary boolean not null default false,
        specimen_status varchar not null default 'good state',
        specimen_part_count_min integer not null default 1,
        specimen_part_count_max integer not null default 1,
        constraint pk_specimen_parts primary key (id),
        constraint fk_specimen_parts_specimen_individuals foreign key (specimen_individual_ref) references specimen_individuals(id) on delete cascade,
        constraint fk_specimen_parts_parent_ref foreign key (parent_ref) references specimen_parts(id) on delete cascade,
        constraint fk_specimen_parts_institutions foreign key (institution_ref) references people(id) ON DELETE no action,
        constraint chk_chk_specimen_parts_minmax check (specimen_part_count_min <= specimen_part_count_max),
        constraint chk_chk_specimen_part_min check (specimen_part_count_min >= 0)
       );

comment on table specimen_parts is 'List of individuals or parts of individuals stored in conservatories';
comment on column specimen_parts.id is 'Unique identifier of a specimen part/individual';
comment on column specimen_parts.specimen_individual_ref is 'Reference of corresponding characterized specimen';
comment on column specimen_parts.specimen_part is 'Description of the part stored in conservatory: the whole specimen or a given precise part such as skelleton, head, fur,...';
comment on column specimen_parts.building is 'Building the part/individual is stored in';
comment on column specimen_parts.floor is 'Floor the part/individual is stored in';
comment on column specimen_parts.room is 'Room the part/individual is stored in';
comment on column specimen_parts.row is 'Row the part/individual is stored in';
comment on column specimen_parts.shelf is 'Shelf the part/individual is stored in';
comment on column specimen_parts.container is 'Container the part/individual is stored in';
comment on column specimen_parts.sub_container is 'Sub-Container the part/individual is stored in';
comment on column specimen_parts.container_type is 'Type of container: box, plateau-caisse,...';
comment on column specimen_parts.sub_container_type is 'Type of sub-container: slide, needle,...';
comment on column specimen_parts.container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column specimen_parts.sub_container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column specimen_parts.surnumerary is 'Tells if this part/individual has been added after first inventory';
comment on column specimen_parts.specimen_status is 'Specimen status: good state, lost, damaged,...';
comment on column specimen_parts.specimen_part_count_min is 'Minimum number of parts/individuals';
comment on column specimen_parts.specimen_part_count_max is 'Maximum number of parts/individuals';
comment on column specimen_parts.complete is 'Flag telling if part/specimen is complete or not';

create sequence insurances_id_seq;

create table insurances
       (
        id integer not null default nextval('insurances_id_seq'),
        insurance_value numeric(16,2) not null,
        insurance_currency varchar not null default '€',
        insurance_year smallint not null default 0,
        insurer_ref integer,
        constraint pk_insurances primary key (id),
        constraint unq_specimen_parts_insurances unique (referenced_relation, record_id, insurance_year),
        constraint fk_specimen_parts_insurances_people foreign key (insurer_ref) references people(id) on delete set null,
        constraint chk_chk_specimen_parts_insurances check (insurance_value > 0)
       )
       inherits (template_table_record_ref);
comment on table insurances is 'List of insurances values for given specimen parts/individuals';
comment on column insurances.referenced_relation is 'Reference-Name of table concerned';
comment on column insurances.record_id is 'Identifier of record concerned';
comment on column insurances.insurance_currency is 'Currency used with insurance value';
comment on column insurances.insurance_year is 'Reference year for insurance subscription';
comment on column insurances.insurance_value is 'Insurance value';
comment on column insurances.insurer_ref is 'Reference of the insurance firm an insurance have been subscripted at';

create sequence associated_multimedia_id_seq;

create table associated_multimedia
       (
        id integer not null default nextval('associated_multimedia_id_seq'),
        multimedia_ref integer not null,
        constraint pk_associated_multimedia primary key (id),
        constraint unq_associated_multimedia unique (multimedia_ref, referenced_relation, record_id),
        constraint fk_associated_multimedia_multimedia foreign key (multimedia_ref) references multimedia(id) on delete cascade
       )
       inherits (template_table_record_ref);
comment on table associated_multimedia is 'List of all associated multimedia to an element of DaRWIN 2 application: specimen, catalogue unit';
comment on column associated_multimedia.id is 'Unique identifier of a multimedia association';
comment on column associated_multimedia.referenced_relation is 'Reference-Name of table concerned';
comment on column associated_multimedia.record_id is 'Identifier of record concerned';
comment on column associated_multimedia.multimedia_ref is 'Reference of multimedia object concerned - id field of multimedia table';

create sequence specimens_accompanying_id_seq;

create table specimens_accompanying
       (
        id integer not null default nextval('specimens_accompanying_id_seq'),
        accompanying_type varchar not null default 'biological',
        specimen_ref integer not null,
        taxon_ref integer not null default 0,
        mineral_ref integer not null default 0,
        form varchar not null default 'isolated',
        quantity numeric(16,2),
        unit varchar default '%',
        constraint pk_specimens_accompanying primary key (id),
        constraint unq_specimens_accompanying unique (specimen_ref, taxon_ref, mineral_ref),
        constraint fk_specimens_accompanying_specimens foreign key (specimen_ref) references specimens(id) on delete cascade,
        constraint fk_specimens_accompanying_mineralogy foreign key (mineral_ref) references mineralogy(id),
        constraint fk_specimens_accompanying_taxonomy foreign key (taxon_ref) references taxonomy(id)
       );
comment on table specimens_accompanying is 'List all the objects/specimens accompanying the current specimen';
comment on column specimens_accompanying.specimen_ref is 'Reference of specimen concerned - id field of specimens table';
comment on column specimens_accompanying.mineral_ref is 'Reference of accompanying mineral (if it''s an inhert unit accompanying - id field of mineralogy table';
comment on column specimens_accompanying.accompanying_type is 'Type of accompanying specimen: biological or mineral';
comment on column specimens_accompanying.quantity is 'Quantity of accompanying specimens';
comment on column specimens_accompanying.unit is 'Unit used for quantity of accompanying specimen presence';
comment on column specimens_accompanying.taxon_ref is 'Reference of the accompanying taxon (if it''s a biological unit accompanying) - id field of taxonomy table';
comment on column specimens_accompanying.form is 'Form of accompanying specimen presence: colony, aggregate, isolated,...';

create sequence collecting_tools_id_seq;

create table collecting_tools
       (
        id integer not null default nextval('collecting_tools_id_seq'),
        tool varchar not null,
        tool_indexed varchar not null,
        constraint pk_collecting_tools primary key (id),
        constraint unq_collecting_tools unique (tool_indexed),
        constraint chk_collecting_tools_tool check (tool <> '')
       );
comment on table collecting_tools is 'List of all available collecting tools';
comment on column collecting_tools.id is 'Unique identifier of a collecting tool';
comment on column collecting_tools.tool is 'Tool used';
comment on column collecting_tools.tool_indexed is 'Indexed form of tool used - for ordering and filtering purposes';

create sequence specimen_collecting_tools_id_seq;

create table specimen_collecting_tools
  (
    id integer not null default nextval('specimen_collecting_tools_id_seq'),
    specimen_ref integer not null,
    collecting_tool_ref integer not null,
    constraint pk_specimen_collecting_tools primary key (id),
    constraint unq_specimen_collecting_tools unique (specimen_ref, collecting_tool_ref),
    constraint fk_specimen_collecting_tools_specimen foreign key (specimen_ref) references specimens (id) on delete cascade,
    constraint fk_specimen_collecting_tools_tool foreign key (collecting_tool_ref) references collecting_tools (id) on delete cascade
  );

comment on table specimen_collecting_tools is 'Association of collecting tools with specimens';
comment on column specimen_collecting_tools.id is 'Unique identifier of an association';
comment on column specimen_collecting_tools.specimen_ref is 'Identifier of a specimen - comes from specimens table (id field)';
comment on column specimen_collecting_tools.collecting_tool_ref is 'Identifier of a collecting tool - comes from collecting_tools table (id field)';

create sequence collecting_methods_id_seq;

create table collecting_methods
       (
        id integer not null default nextval('collecting_methods_id_seq'),
        method varchar not null,
        method_indexed varchar not null,
        constraint pk_collecting_methods primary key (id),
        constraint unq_collecting_methods unique (method_indexed),
        constraint chk_collecting_methods_method check (method <> '')
       );
comment on table collecting_methods is 'List of all available collecting methods';
comment on column collecting_methods.id is 'Unique identifier of a collecting method';
comment on column collecting_methods.method is 'Method used';
comment on column collecting_methods.method_indexed is 'Indexed form of method used - for ordering and filtering purposes';

create sequence specimen_collecting_methods_id_seq;

create table specimen_collecting_methods
  (
    id integer not null default nextval('specimen_collecting_methods_id_seq'),
    specimen_ref integer not null,
    collecting_method_ref integer not null,
    constraint pk_specimen_collecting_methods primary key (id),
    constraint unq_specimen_collecting_methods unique (specimen_ref, collecting_method_ref),
    constraint fk_specimen_collecting_methods_specimen foreign key (specimen_ref) references specimens (id) on delete cascade,
    constraint fk_specimen_collecting_methods_method foreign key (collecting_method_ref) references collecting_methods (id) on delete cascade
  );

comment on table specimen_collecting_methods is 'Association of collecting methods with specimens';
comment on column specimen_collecting_methods.id is 'Unique identifier of an association';
comment on column specimen_collecting_methods.specimen_ref is 'Identifier of a specimen - comes from specimens table (id field)';
comment on column specimen_collecting_methods.collecting_method_ref is 'Identifier of a collecting method - comes from collecting_methods table (id field)';

create table words
  (
    referenced_relation varchar,
    field_name varchar,
    word varchar,
    constraint uniq_words unique (referenced_relation, field_name, word)
  );
comment on table words is 'List all trigram used with pg_trgm to match similarities';
comment on column words.referenced_relation is 'Reference of table concerned';
comment on column words.field_name is 'Reference of field in the table';
comment on column words.word is 'word founded';

create sequence preferences_id_seq;

create table preferences
  (
    id integer not null default nextval('preferences_id_seq'),
    user_ref integer not null,
    pref_key varchar not null,
    pref_value varchar not null,
    constraint fk_users_preferences foreign key (user_ref) references users(id) on delete cascade
  );

comment on table preferences is 'Table to handle users preferences';
comment on column preferences.user_ref is 'The referenced user id';
comment on column preferences.pref_key is 'The classification key of the preference. eg: color';
comment on column preferences.pref_value is 'The value of the preference for this user eg: red';

create sequence darwin_flat_id_seq;

create table darwin_flat
  (
    id integer not null default nextval('darwin_flat_id_seq'),
    spec_ref integer not null,
    category varchar,
    collection_ref integer not null default 0,
    collection_type varchar,
    collection_code varchar,
    collection_name varchar,
    collection_is_public boolean not null default true,
    collection_parent_ref integer default 0,
    collection_path varchar,
    expedition_ref integer not null default 0,
    expedition_name varchar,
    expedition_name_ts tsvector,
    expedition_name_indexed varchar,
    station_visible boolean,
    gtu_ref integer not null default 0,
    gtu_code varchar,
    gtu_parent_ref integer default 0,
    gtu_path varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_tag_values_indexed varchar[],
    gtu_country_tag_value varchar,
    gtu_country_tag_indexed varchar[],
    gtu_location GEOGRAPHY(POLYGON,4326),
    taxon_ref integer not null default 0,
    taxon_name varchar,
    taxon_name_indexed tsvector,
    taxon_name_order_by varchar,
    taxon_level_ref integer not null default 0,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_path varchar,
    taxon_parent_ref integer default 0,
    taxon_extinct boolean,
    litho_ref integer not null default 0,
    litho_name varchar,
    litho_name_indexed tsvector,
    litho_name_order_by varchar,
    litho_level_ref integer not null default 0,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean not null default false,
    litho_color varchar,
    litho_path varchar,
    litho_parent_ref integer default 0,
    chrono_ref integer not null default 0,
    chrono_name varchar,
    chrono_name_indexed tsvector,
    chrono_name_order_by varchar,
    chrono_level_ref integer not null default 0,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean not null default false,
    chrono_color varchar,
    chrono_path varchar,
    chrono_parent_ref integer default 0,
    lithology_ref integer not null default 0,
    lithology_name varchar,
    lithology_name_indexed tsvector,
    lithology_name_order_by varchar,
    lithology_level_ref integer not null default 0,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean not null default false,
    lithology_color varchar,
    lithology_path varchar,
    lithology_parent_ref integer default 0,
    mineral_ref integer not null default 0,
    mineral_name varchar,
    mineral_name_indexed tsvector,
    mineral_name_order_by varchar,
    mineral_level_ref integer not null default 0,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean not null default false,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parent_ref integer default 0,
    host_taxon_ref integer not null default 0,
    host_relationship varchar,
    host_taxon_name varchar,
    host_taxon_name_indexed tsvector,
    host_taxon_name_order_by varchar,
    host_taxon_level_ref integer not null default 0,
    host_taxon_level_name varchar,
    host_taxon_status varchar,
    host_taxon_path varchar,
    host_taxon_parent_ref integer default 0,
    host_taxon_extinct boolean,
    host_specimen_ref integer,
    ig_ref integer,
    ig_num varchar,
    ig_num_indexed varchar,
    ig_date_mask integer,
    ig_date date,
    acquisition_category varchar,
    acquisition_date_mask integer,
    acquisition_date date,
    with_types boolean not null default false,
    with_individuals boolean not null default false,
    individual_ref integer,
    individual_type varchar not null default 'specimen',
    individual_type_group varchar not null default 'specimen',
    individual_type_search varchar not null default 'specimen',
    individual_sex  varchar not null default 'undefined',
    individual_state varchar not null default 'not applicable',
    individual_stage varchar not null default 'undefined',
    individual_social_status varchar not null default 'not applicable',
    individual_rock_form varchar not null default 'not applicable',
    individual_count_min integer,
    individual_count_max integer,
    with_parts boolean not null default false,
    part_ref integer,
    part varchar,
    part_status varchar,
    institution_ref integer,
    building varchar,
    floor varchar,
    room varchar,
    row varchar,
    shelf varchar,
    container_type varchar,
    container_storage varchar,
    container varchar,
    sub_container_type varchar,
    sub_container_storage varchar,
    sub_container varchar,
    part_count_min integer,
    part_count_max integer,
    specimen_status varchar,
    complete boolean,
    surnumerary boolean,
    spec_ident_ids integer[],
    ind_ident_ids integer[],
    spec_coll_ids integer[],
    spec_don_sel_ids integer[],
    CONSTRAINT pk_darwin_flat PRIMARY KEY (id),
    CONSTRAINT fk_darwin_flat_spec_ref FOREIGN KEY (spec_ref) REFERENCES specimens (id) ON DELETE CASCADE,
    CONSTRAINT fk_darwin_flat_host_specimen_ref FOREIGN KEY (host_specimen_ref) REFERENCES specimens (id) ON DELETE SET DEFAULT
  );
--SELECT substring(AddGeometryColumn('darwin_flat', 'gtu_location', 4326, 'POLYGON', 2) for 0);

comment on table darwin_flat is 'Flat table compiling all specimens data (catalogues data included - used for search purposes';
comment on column darwin_flat.id is 'Unique identifier of a darwin flat entry';
comment on column darwin_flat.spec_ref is 'Reference of specimen concerned';
comment on column darwin_flat.category is 'Specimen concerned category: physical, observation,...';
comment on column darwin_flat.collection_ref is 'Reference of collection the specimen concerned belongs to';
comment on column darwin_flat.collection_code is 'Collection code';
comment on column darwin_flat.collection_name is 'Collection name - i.e.: Vertebrates,...';
comment on column darwin_flat.collection_is_public is 'Flag telling if collection is public or not';
comment on column darwin_flat.collection_parent_ref is 'Reference of parent collection';
comment on column darwin_flat.collection_path is 'Hierarchical path of current collection';
comment on column darwin_flat.expedition_ref is 'Reference of expedition the current specimen was collected in';
comment on column darwin_flat.expedition_name is 'Name of the expedition';
comment on column darwin_flat.expedition_name_ts is 'Name of the expedition - ts vector form - for searches purposes';
comment on column darwin_flat.expedition_name_indexed is 'Name of the expedition - indexed form with fullToIndex - for ordering purposes';
comment on column darwin_flat.station_visible is 'Flag telling if the sampling location can be publically visible or not';
comment on column darwin_flat.gtu_ref is 'Sampling location referenced';
comment on column darwin_flat.gtu_code is 'Sampling location referenced code';
comment on column darwin_flat.gtu_parent_ref is 'Sampling location referenced parent';
comment on column darwin_flat.gtu_path is 'Sampling location hierarchical path';
comment on column darwin_flat.gtu_from_date_mask is 'Sampling location from date mask';
comment on column darwin_flat.gtu_from_date is 'Sampling location from date';
comment on column darwin_flat.gtu_to_date_mask is 'Sampling location to date mask';
comment on column darwin_flat.gtu_to_date is 'Sampling location to date';
comment on column darwin_flat.gtu_tag_values_indexed is 'Array of all the tags entered for this gtu - all the tags are of the indexed form with fullToIndex function';
comment on column darwin_flat.gtu_country_tag_value is 'List of "Administrative area-Country" tags associated to the sampling location referenced';
comment on column darwin_flat.gtu_country_tag_indexed is 'List of "Administrative area-Country" tags associated to the sampling location referenced  all the tags are of the indexed form with fullToIndex function';
comment on column darwin_flat.taxon_ref is 'Taxon unit referenced';
comment on column darwin_flat.taxon_name is 'Taxon unit referenced name';
comment on column darwin_flat.taxon_name_indexed is 'Taxon unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.taxon_name_order_by is 'Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.taxon_level_ref is 'Taxon unit referenced level';
comment on column darwin_flat.taxon_level_name is 'Taxon unit referenced level name';
comment on column darwin_flat.taxon_status is 'Taxon unit referenced status: valid, invalid,...';
comment on column darwin_flat.taxon_path is 'Taxon unit referenced hierarchical path';
comment on column darwin_flat.taxon_parent_ref is 'Taxon unit referenced parenty';
comment on column darwin_flat.taxon_extinct is 'Taxon unit referenced flag telling if the unit is extinct or not';
comment on column darwin_flat.chrono_ref is 'Chrono unit referenced';
comment on column darwin_flat.chrono_name is 'Chrono unit referenced name';
comment on column darwin_flat.chrono_name_indexed is 'Chrono unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.chrono_name_order_by is 'Chrono unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.chrono_level_ref is 'Chrono unit referenced level';
comment on column darwin_flat.chrono_level_name is 'Chrono unit referenced level name';
comment on column darwin_flat.chrono_status is 'Chrono unit referenced status: valid, invalid,...';
comment on column darwin_flat.chrono_local is 'Flag telling if the chrono unit name is a local appelation or not';
comment on column darwin_flat.chrono_color is 'Hexadecimal value of color associated to the chrono unit';
comment on column darwin_flat.chrono_path is 'Chrono unit referenced hierarchical path';
comment on column darwin_flat.chrono_parent_ref is 'Chrono unit referenced parenty';
comment on column darwin_flat.litho_ref is 'Litho unit referenced';
comment on column darwin_flat.litho_name is 'Litho unit referenced name';
comment on column darwin_flat.litho_name_indexed is 'Litho unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.litho_name_order_by is 'Litho unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.litho_level_ref is 'Litho unit referenced level';
comment on column darwin_flat.litho_level_name is 'Litho unit referenced level name';
comment on column darwin_flat.litho_status is 'Litho unit referenced status: valid, invalid,...';
comment on column darwin_flat.chrono_local is 'Flag telling if the litho unit name is a local appelation or not';
comment on column darwin_flat.chrono_color is 'Hexadecimal value of color associated to the litho unit';
comment on column darwin_flat.litho_path is 'Litho unit referenced hierarchical path';
comment on column darwin_flat.litho_parent_ref is 'Litho unit referenced parenty';
comment on column darwin_flat.lithology_ref is 'Lithology unit referenced';
comment on column darwin_flat.lithology_name is 'Lithology unit referenced name';
comment on column darwin_flat.lithology_name_indexed is 'Lithology unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.lithology_name_order_by is 'Lithology unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.lithology_level_ref is 'Lithology unit referenced level';
comment on column darwin_flat.lithology_level_name is 'Lithology unit referenced level name';
comment on column darwin_flat.lithology_status is 'Lithology unit referenced status: valid, invalid,...';
comment on column darwin_flat.chrono_local is 'Flag telling if the lithology unit name is a local appelation or not';
comment on column darwin_flat.chrono_color is 'Hexadecimal value of color associated to the lithology unit';
comment on column darwin_flat.lithology_path is 'Lithology unit referenced hierarchical path';
comment on column darwin_flat.lithology_parent_ref is 'Lithology unit referenced parenty';
comment on column darwin_flat.mineral_ref is 'Mineral unit referenced';
comment on column darwin_flat.mineral_name is 'Mineral unit referenced name';
comment on column darwin_flat.mineral_name_indexed is 'Mineral unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.mineral_name_order_by is 'Mineral unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.mineral_level_ref is 'Mineral unit referenced level';
comment on column darwin_flat.mineral_level_name is 'Mineral unit referenced level name';
comment on column darwin_flat.mineral_status is 'Mineral unit referenced status: valid, invalid,...';
comment on column darwin_flat.chrono_local is 'Flag telling if the mineral unit name is a local appelation or not';
comment on column darwin_flat.chrono_color is 'Hexadecimal value of color associated to the mineral unit';
comment on column darwin_flat.mineral_path is 'Mineral unit referenced hierarchical path';
comment on column darwin_flat.mineral_parent_ref is 'Mineral unit referenced parenty';
comment on column darwin_flat.host_taxon_ref is 'Host Taxon unit referenced';
comment on column darwin_flat.host_taxon_name is 'Host Taxon unit referenced name';
comment on column darwin_flat.host_taxon_name_indexed is 'Host Taxon unit referenced name - ts vector form - used for searches purposes';
comment on column darwin_flat.host_taxon_name_order_by is 'Host Taxon unit referenced name - indexed form with fullToIndex - used for ordering purposes';
comment on column darwin_flat.host_taxon_level_ref is 'Host Taxon unit referenced level';
comment on column darwin_flat.host_taxon_level_name is 'Host Taxon unit referenced level name';
comment on column darwin_flat.host_taxon_status is 'Host Taxon unit referenced status: valid, invalid,...';
comment on column darwin_flat.host_taxon_path is 'Host Taxon unit referenced hierarchical path';
comment on column darwin_flat.host_taxon_parent_ref is 'Host Taxon unit referenced parenty';
comment on column darwin_flat.host_taxon_extinct is 'Host Taxon unit referenced flag telling if the unit is extinct or not';
comment on column darwin_flat.ig_ref is 'General Inventory number (I.G. Num) referenced';
comment on column darwin_flat.ig_num is 'General Inventory number (I.G. Num) referenced - The number concerned';
comment on column darwin_flat.ig_num_indexed is 'General Inventory number (I.G. Num) referenced - The number concerned - indexed form composed with fullToIndex';
comment on column darwin_flat.ig_date_mask is 'General Inventory number (I.G. Num) referenced - Date of attribution mask';
comment on column darwin_flat.ig_date is 'General Inventory number (I.G. Num) referenced - Date of attribution';
comment on column darwin_flat.acquisition_category is 'Specimen acquisition category';
comment on column darwin_flat.acquisition_date_mask is 'Specimen acquisition date mask';
comment on column darwin_flat.acquisition_date is 'Specimen acquisition date';
comment on column darwin_flat.with_types is 'Flag telling if there are types for current specimen';
comment on column darwin_flat.with_individuals is 'Flag telling if there are individuals for current specimen';
comment on column darwin_flat.individual_ref is 'Reference of specimen individual - references to id of individual in specimen_individuals table - Null if nothing referenced';
comment on column darwin_flat.individual_type is 'Type';
comment on column darwin_flat.individual_type_group is 'Type group - Grouping of types appelations used for internal search';
comment on column darwin_flat.individual_type_search is 'Type search - Grouping of types appelations used for external searches';
comment on column darwin_flat.individual_sex  is 'Sex: Male, Female, Hermaphrodit,...';
comment on column darwin_flat.individual_state is 'Sex state if applicable: Ovigerous, Pregnant,...';
comment on column darwin_flat.individual_stage is 'Stage: Adult, Nymph, Larvae,...';
comment on column darwin_flat.individual_social_status is 'Social status if applicable: Worker, Queen, King, Fighter,...';
comment on column darwin_flat.individual_rock_form is 'Rock form if applicable: Cubic, Orthorhombic,...';
comment on column darwin_flat.individual_count_min is 'Minimum number of individuals';
comment on column darwin_flat.individual_count_max is 'Maximum number of individuals';
comment on column darwin_flat.with_parts is 'Flag telling if they are parts for the current individual';
comment on column darwin_flat.part_ref is 'Reference of part - coming from specimen_parts table (id field) - set to null if no references';
comment on column darwin_flat.part is 'Part name: wing, tail, toes,...';
comment on column darwin_flat.part_status is 'Part status: intact, lost, stolen,...';
comment on column darwin_flat.institution_ref is 'Institution (people) where the current part is stored';
comment on column darwin_flat.building is 'Building where the current part is stored';
comment on column darwin_flat.floor is 'Floor where the current part is stored';
comment on column darwin_flat.room is 'Room where the current part is stored';
comment on column darwin_flat.row is 'Row of the conservatory where the current part is stored';
comment on column darwin_flat.shelf is 'Shelf where the current part is stored';
comment on column darwin_flat.container_type is 'Container type: box, slide,...';
comment on column darwin_flat.container_storage is 'Container storage: dry, alcohool, formol,...';
comment on column darwin_flat.container is 'Container code';
comment on column darwin_flat.sub_container_type is 'Sub-Container type: box, slide,...';
comment on column darwin_flat.sub_container_storage is 'Sub-Container storage: dry, alcohool, formol,...';
comment on column darwin_flat.sub_container is 'Sub container code';
comment on column darwin_flat.part_count_min is 'Minimum number of parts stored';
comment on column darwin_flat.part_count_max is 'Maximum number of parts stored';
comment on column darwin_flat.specimen_status is 'Tells the status of part concerned: lost, damaged, good shape,...';
comment on column darwin_flat.complete is 'Flag telling if the specimen is complete or not';
comment on column darwin_flat.surnumerary is 'Tells if this part/individual has been added after first inventory';
comment on column darwin_flat.spec_ident_ids is 'Array of identifiers referenced in this specimen';
comment on column darwin_flat.ind_ident_ids is 'Array of identifiers referenced in this individual';
comment on column darwin_flat.spec_coll_ids is 'Array of collectors referenced in this specimen';
comment on column darwin_flat.spec_don_sel_ids is 'Array of donators or sellers referenced in this specimen';


create sequence flat_dict_id_seq;

create table flat_dict
(
  id integer not null default nextval('flat_dict_id_seq'),
  referenced_relation varchar not null,
  dict_field varchar not null,
  dict_value varchar not null,
  constraint unq_flat_dict unique (dict_value, dict_field, referenced_relation),
  constraint pk_flat_dict primary key (id)
);


comment on table flat_dict is 'Flat table compiling all small distinct values for a faster search like types, code prefixes ,...';
comment on column flat_dict.referenced_relation is 'The table where the value come from';
comment on column flat_dict.dict_field is 'the field name of where the value come from';
comment on column flat_dict.dict_value is 'the distinct value';

create sequence imports_id_seq;

create table imports
  (
    id integer not null default nextval('imports_id_seq'),
    user_ref integer not null,
    format varchar not null,
    collection_ref integer not null default 0,
    filename varchar not null,
    state varchar not null default '',
    created_at timestamp not null default now(),
    updated_at timestamp,
    initial_count integer not null default 0,
    is_finished boolean  not null default false,
    constraint pk_import primary key (id) ,
    constraint fk_imports_collections foreign key (collection_ref) references collections(id) on delete cascade,
    constraint fk_imports_users foreign key (user_ref) references users(id) on delete cascade
  );

comment on table imports is 'Table used to check the state of the date coming from an uploaded file';
comment on column imports.user_ref is 'The referenced user id';
comment on column imports.format is 'The import template to use for the imported file';
comment on column imports.filename is 'The filename of the file to proceed';
comment on column imports.collection_ref is 'The collection associated';
comment on column imports.state is 'the state of the processing the file';
comment on column imports.created_at is 'Creation of the file';
comment on column imports.updated_at is 'When the data has been modified lately';
comment on column imports.initial_count is 'Number of rows of staging when the import was created';
comment on column imports.is_finished is 'Boolean to mark if the import is finished or still need some operations';

create sequence staging_id_seq;

create table staging
  (
    id integer not null default nextval('staging_id_seq'),
    import_ref integer not null,
    parent_ref integer,
    path varchar,
    level varchar not null,
    spec_ref integer,
    category varchar,
    expedition_ref integer,
    expedition_name varchar,
    expedition_from_date date,
    expedition_from_date_mask integer,
    expedition_to_date date,
    expedition_to_date_mask integer,
    station_visible boolean,
    gtu_ref integer,
    gtu_code varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_latitude float,
    gtu_longitude float,
    gtu_lat_long_accuracy float,
    gtu_elevation float,
    gtu_elevation_accuracy float,
    taxon_ref integer,
    taxon_name varchar,
    taxon_level_ref integer,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_extinct boolean,
    taxon_parents hstore,
    litho_ref integer,
    litho_name varchar,
    litho_level_ref integer,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean,
    litho_color varchar,   
    litho_parents hstore,
    chrono_ref integer,
    chrono_name varchar,
    chrono_level_ref integer,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean,
    chrono_color varchar,
    chrono_upper_bound numeric(10,3),
    chrono_lower_bound numeric(10,3),
    chrono_parents hstore,
    lithology_ref integer,
    lithology_name varchar,
    lithology_level_ref integer,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean,
    lithology_color varchar,
    lithology_parents hstore,
    mineral_ref integer,
    mineral_name varchar,
    mineral_level_ref integer,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parents hstore,
    host_taxon_ref integer,
    host_relationship varchar,
    host_taxon_name varchar,
    host_taxon_level_ref integer,
    host_taxon_level_name varchar,
    host_taxon_status varchar,
    host_specimen_ref integer,
    ig_ref integer,
    ig_num varchar,
    ig_date_mask integer,
    ig_date date,
    acquisition_category varchar,
    acquisition_date_mask integer,
    acquisition_date date,

    individual_type varchar,
    individual_sex  varchar,
    individual_state varchar,
    individual_stage varchar,
    individual_social_status varchar,
    individual_rock_form varchar,
    individual_count_min integer,
    individual_count_max integer,
    collectors text[],
    donators text[],

    part varchar,
    part_status varchar,

    institution_ref integer,
    institution_name varchar,
    building varchar,
    floor varchar,
    room varchar,
    row varchar,
    shelf varchar,
    container_type varchar,
    container_storage varchar,
    container varchar,
    sub_container_type varchar,
    sub_container_storage varchar,
    sub_container varchar,
    part_count_min integer,
    part_count_max integer,
    specimen_status varchar,
    complete boolean,
    surnumerary boolean,
    status hstore not null default '',
    to_import boolean default false,
    constraint pk_staging primary key (id),
    constraint fk_staging_import foreign key (import_ref) references imports(id) on delete cascade,
    constraint fk_parent_ref foreign key (parent_ref) references staging(id) on delete cascade
  );

create sequence staging_tag_groups_id_seq;

create table  staging_tag_groups
       (
        id bigint not null default nextval('staging_tag_groups_id_seq'),
        staging_ref integer not null,
        group_name varchar not null,
        sub_group_name varchar not null,
        tag_value varchar not null,
        constraint pk_staging_tag_groups primary key (id)
       );

comment on table staging_tag_groups is 'List of grouped tags for an imported row (copy of tag group)';
comment on column staging_tag_groups.id is 'Unique identifier of a grouped tag';
comment on column staging_tag_groups.staging_ref is 'Ref of an imported line';
comment on column staging_tag_groups.group_name is 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';
comment on column staging_tag_groups.sub_group_name is 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';
comment on column staging_tag_groups.tag_value is 'Ensemble of Tags';
