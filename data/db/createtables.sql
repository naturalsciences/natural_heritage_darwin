\set log_error_verbosity terse
create table template_people
       (
        is_physical boolean not null default true,
        sub_type varchar,
        formated_name varchar not null,
        formated_name_indexed varchar not null,
        formated_name_unique varchar not null,
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
comment on column template_people.formated_name_indexed is 'Indexed form of formated_name field';
comment on column template_people.formated_name_unique is 'Indexed form of formated_name field (for unique index purpose)';
comment on column template_people.family_name is 'Family name for physical user/persons and Organisation name for moral user/persons';
comment on column template_people.title is 'Title of a physical user/person like Mr or Mrs or phd,...';
comment on column template_people.given_name is 'User/person''s given name - usually first name';
comment on column template_people.additional_names is 'Any additional names given to user/person';
comment on column template_people.birth_date_mask is 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column template_people.birth_date is 'Birth/Creation date composed';
comment on column template_people.gender is 'For physical user/persons give the gender: M or F';

create table people
       (
        id serial,
        end_date_mask integer not null default 0,
        end_date date not null default '31/12/2038',
        activity_date_from_mask integer not null default 0,
        activity_date_from date not null default '01/01/0001',
        activity_date_to_mask integer not null default 0,
        activity_date_to date not null default '31/12/2038',
        name_formated_indexed varchar not null default '',
        constraint pk_people primary key (id),
        constraint unq_people unique (is_physical,gender, formated_name_unique, birth_date, birth_date_mask, end_date, end_date_mask)
       )
inherits (template_people);
comment on table people is 'All physical and moral persons used in the application are here stored';
comment on column people.id is 'Unique identifier of a person';
comment on column people.is_physical is 'Type of person: physical or moral - true is physical, false is moral';
comment on column people.sub_type is 'Used for moral persons: precise nature - public institution, asbl, sprl, sa,...';
comment on column people.formated_name is 'Complete person formated name (with honorific mention, prefixes, suffixes,...) - By default composed with family_name and given_name fields, but can be modified by hand';
comment on column people.formated_name_indexed is 'Indexed form of formated_name field';
comment on column people.name_formated_indexed is 'The indexed form of given_name and family_name (the inverse of formated_name_indexed for searching)';
comment on column people.title is 'Title of a physical user/person like Mr or Mrs or phd,...';
comment on column people.family_name is 'Family name for physical persons and Organisation name for moral persons';
comment on column people.given_name is 'User/person''s given name - usually first name';
comment on column people.additional_names is 'Any additional names given to person';
comment on column people.birth_date is 'Day of birth/creation';
comment on column people.birth_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column people.gender is 'For physical persons give the gender: M or F';
comment on column people.end_date is 'End date';
comment on column people.end_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column people.activity_date_from is 'person general activity period or person activity period in the organization referenced date from';
comment on column people.activity_date_from_mask is 'person general activity period or person activity period in the organization referenced date from mask';
comment on column people.activity_date_to is 'person general activity period or person activity period in the organization referenced date to';
comment on column people.activity_date_to_mask is 'person general activity period or person activity period in the organization referenced date to mask';

create table catalogue_relationships
       (
        id serial,
        referenced_relation varchar not null,
        record_id_1 integer not null,
        record_id_2 integer not null,
        relationship_type varchar not null default 'recombined from',
        constraint pk_catalogue_relationships primary key (id),
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

create table catalogue_people
       (
        id serial,
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

create table catalogue_levels
       (
        id serial,
        level_type varchar not null,
        level_name varchar not null,
        level_sys_name varchar not null,
        optional_level boolean not null default false,
        level_order integer not null default 999,
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
        level_upper_ref integer,
        constraint unq_possible_upper_levels unique (level_ref, level_upper_ref),
        constraint fk_possible_upper_levels_catalogue_levels_01 foreign key (level_ref) references catalogue_levels(id) on delete cascade,
        constraint fk_possible_upper_levels_catalogue_levels_02 foreign key (level_upper_ref) references catalogue_levels(id)
       );
comment on table possible_upper_levels is 'For each level, list all the availble parent levels';
comment on column possible_upper_levels.level_ref is 'Reference of current level';
comment on column possible_upper_levels.level_upper_ref is 'Reference of authorized parent level';

create table comments
       (
        id serial,
        notion_concerned varchar not null,
        comment text not null,
        comment_indexed text not null,
        constraint pk_comments primary key (id)
       )
       inherits (template_table_record_ref);
comment on table comments is 'Comments associated to a record of a given table (and maybe a given field) on a given subject';
comment on column comments.id is 'Unique identifier of a comment';
comment on column comments.referenced_relation is 'Reference-Name of table a comment is posted for';
comment on column comments.record_id is 'Identifier of the record concerned';
comment on column comments.notion_concerned is 'Notion concerned by comment';
comment on column comments.comment is 'Comment';
comment on column comments.comment_indexed is 'indexed form of comment field';

create table ext_links
       (
        id serial,
        url varchar not null,
        comment text not null,
        comment_indexed text not null,
        type text not null default 'ext',
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
comment on column ext_links.comment_indexed is 'indexed form of comment field';
comment on column ext_links.type IS 'Sort of external link given';

create table gtu
       (
        id serial,
        code varchar not null default '',
        gtu_from_date_mask integer not null default 0,
        gtu_from_date timestamp not null default '01/01/0001 00:00:00',
        gtu_to_date_mask integer not null default 0,
        gtu_to_date timestamp not null default '31/12/2038 00:00:00',
        tag_values_indexed varchar[],
        latitude float,
        longitude float,
        lat_long_accuracy float,
        location POINT,
        elevation float,
        elevation_accuracy float,
        constraint pk_gtu primary key (id)
       );
comment on table gtu is 'Location or sampling units - GeoTemporalUnits';
comment on column gtu.id is 'Unique identifier of a location or sampling unit';
comment on column gtu.code is 'Code given - for sampling units - takes id if none defined';
comment on column gtu.gtu_from_date is 'composed from date of the GTU';
comment on column gtu.gtu_from_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column gtu.gtu_to_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day, 4 for hour, 2 for minutes, 1 for seconds';
comment on column gtu.gtu_to_date is 'composed to date of the GTU';
comment on column gtu.tag_values_indexed is 'Array of all tags associated to gtu (indexed form)';
comment on column gtu.latitude is 'Latitude of the gtu';
comment on column gtu.longitude is 'longitude of the gtu';
comment on column gtu.lat_long_accuracy is 'Accuracy in meter of both lat & long';
comment on column gtu.elevation is 'Elevation from the level of the sea in meter';
comment on column gtu.elevation_accuracy is 'Accuracy in meter of the elevation';

create table tag_groups
       (
        id serial,
        gtu_ref integer not null,
        group_name varchar not null,
        group_name_indexed varchar not null,
        sub_group_name varchar not null,
        sub_group_name_indexed varchar not null,
        international_name varchar not null default '',
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
comment on column tag_groups.international_name is 'The international(english) name of the place / ocean / country';

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
comment on column properties.property_accuracy is 'Accuracy of the values';


create table identifications
       (
        id serial,
        notion_concerned varchar not null,
        notion_date timestamp not null default '0001-01-01 00:00:00'::timestamp,
        notion_date_mask integer not null default 0,
        value_defined varchar,
        value_defined_indexed varchar not null,
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
comment on column identifications.value_defined_indexed is 'Indexed form of value_defined field';
comment on column identifications.determination_status is 'Status of identification - can either be a percentage of certainty or a code describing the identification step in the process';
comment on column identifications.order_by is 'Integer used to order the identifications when no date entered';

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


create table expeditions
       (
        id serial,
        name varchar not null,
        name_indexed varchar not null,
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
comment on column expeditions.name_indexed is 'Indexed form of expedition name';
comment on column expeditions.expedition_from_date_mask is 'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column expeditions.expedition_from_date is 'Start date of the expedition';
comment on column expeditions.expedition_to_date is 'End date of the expedition';
comment on column expeditions.expedition_to_date_mask is  'Contains the Mask flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';

create table users
       (
        id serial,
        db_user_type smallint default 1 not null,
        people_id integer,
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

create table people_languages
       (
        id serial,
        language_country varchar not null default 'en',
        mother boolean not null default true,
        preferred_language boolean not null default false,
        people_ref integer not null,
        constraint pk_people_languages primary key (id),
        constraint unq_people_languages unique (people_ref, language_country),
        constraint fk_people_languages_people foreign key (people_ref) references people(id) on delete cascade
       );
comment on table people_languages is 'Languages spoken by a given person';
comment on column people_languages.people_ref is 'Reference of person - id field of people table';
comment on column people_languages.language_country is 'Reference of Language - language_country field of languages_countries table';
comment on column people_languages.mother is 'Flag telling if its mother language or not';
comment on column people_languages.preferred_language is 'Flag telling which language is preferred in communications';

create table multimedia
       (
        id serial,
        is_digital boolean not null default true,
        type varchar not null default 'image',
        sub_type varchar,
        title varchar not null,
        description varchar not null default '',
        uri varchar,
        filename varchar,
        search_indexed text not null,
        creation_date date not null default '0001-01-01'::date,
        creation_date_mask integer not null default 0,
        mime_type varchar not null,
        visible boolean not null default true,
        publishable boolean not null default true,
        extracted_info text,
        constraint pk_multimedia primary key (id)
      )
      inherits (template_table_record_ref);

comment on table multimedia is 'Stores all multimedia objects encoded in DaRWIN 2.0';
comment on column multimedia.referenced_relation is 'Reference-Name of table concerned';
comment on column multimedia.record_id is 'Identifier of record concerned';
comment on column multimedia.id is 'Unique identifier of a multimedia object';
comment on column multimedia.is_digital is 'Flag telling if the object is digital (true) or physical (false)';
comment on column multimedia.type is 'Main multimedia object type: image, sound, video,...';
comment on column multimedia.sub_type is 'Characterization of object type: article, publication in serie, book, glass plate,...';
comment on column multimedia.title is 'Title of the multimedia object';
comment on column multimedia.description is 'Description of the current object';
comment on column multimedia.uri is 'URI of object if digital';
comment on column multimedia.filename is 'The original name of the saved file';
comment on column multimedia.creation_date is 'Object creation date';
comment on column multimedia.creation_date_mask is 'Mask used for object creation date display';
comment on column multimedia.search_indexed is 'indexed form of title and description fields together';
comment on column multimedia.mime_type is 'Mime/Type of the linked digital object';
comment on column multimedia.visible is 'Flag telling if the related file has been chosen to be publically visible or not';
comment on column multimedia.publishable is 'Flag telling if the related file has been chosen as a prefered item for publication - Would be for example used for preselection of media published for Open Up project';

create table template_people_users_comm_common
       (
        person_user_ref integer not null,
        entry varchar not null
       );
comment on table template_people_users_comm_common is 'Template table used to construct people communication tables (tel and e-mail)';
comment on column template_people_users_comm_common.person_user_ref is 'Reference of person/user - id field of people/users table';
comment on column template_people_users_comm_common.entry is 'Communication entry';

create table template_people_users_addr_common
       (
        po_box varchar,
        extended_address varchar,
        locality varchar not null,
        region varchar,
        zip_code varchar,
        country varchar not null
       );
comment on table template_people_users_addr_common is 'Template table used to construct addresses tables for people/users';
comment on column template_people_users_addr_common.po_box is 'PO Box';
comment on column template_people_users_addr_common.extended_address is 'Address extension: State, Special post zip code characters,...';
comment on column template_people_users_addr_common.locality is 'Locality';
comment on column template_people_users_addr_common.region is 'Region';
comment on column template_people_users_addr_common.zip_code is 'zip code';
comment on column template_people_users_addr_common.country is 'Country';

create table people_relationships
       (
        id serial,
        person_user_role varchar,
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
       );
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

create table people_comm
       (
        id serial,
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

create table people_addresses
       (
        id serial,
        tag varchar not null default '',
        constraint pk_people_addresses primary key (id),
        constraint fk_people_addresses_people foreign key (person_user_ref) references people(id) on delete cascade
       )
inherits (template_people_users_comm_common, template_people_users_addr_common);
comment on table people_addresses is 'People addresses';
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

create table users_comm
       (
        id serial,
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

create table users_addresses
       (
        id serial,
        person_user_role varchar,
        organization_unit varchar,
        tag varchar not null default '',
        constraint pk_users_addresses primary key (id),
        constraint fk_users_addresses_users foreign key (person_user_ref) references users(id) on delete cascade
       )
inherits (template_people_users_comm_common, template_people_users_addr_common);
comment on table users_addresses is 'Users addresses';
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


create table users_login_infos
       (
        id serial,
        user_ref integer not null,
        login_type varchar not null default 'local',
        user_name varchar,
        password varchar,
        login_system varchar,
        renew_hash varchar,
        last_seen timestamp,
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
comment on column users_login_infos.last_seen is 'Last time the user has logged in.';

create table collections
       (
        id serial,
        collection_type varchar not null default 'mix',
        code varchar not null,
        name varchar not null,
        name_indexed varchar not null,
        institution_ref integer not null,
        main_manager_ref integer not null,
        staff_ref integer,
        parent_ref integer,
        path varchar not null,
        code_auto_increment boolean not null default false,
        code_auto_increment_for_insert_only boolean not null default true,
        code_last_value bigint not null default 0,
        code_prefix varchar,
        code_prefix_separator varchar,
        code_suffix varchar,
        code_suffix_separator varchar,
        code_specimen_duplicate boolean not null default false,
        is_public boolean not null default true,
        constraint pk_collections primary key (id),
        constraint fk_collections_institutions foreign key (institution_ref) references people(id),
        constraint fk_collections_collections foreign key (parent_ref) references collections(id) on delete cascade,
        constraint fk_collections_users foreign key (main_manager_ref) references users(id),
        constraint fk_collections_staff foreign key (staff_ref) references users(id) on delete set null,
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
comment on column collections.staff_ref is 'Reference of staff member, scientist responsible - id field of users table';
comment on column collections.code_auto_increment is 'Flag telling if the numerical part of a code has to be incremented or not';
comment on column collections.code_auto_increment_for_insert_only is 'Flag telling if the autoincremented code insertion has to be done only after insertion of specimens or also after updates of specimens';
comment on column collections.code_last_value is 'Value of the last numeric code given in this collection when auto increment is/was activated';
comment on column collections.code_prefix is 'Default code prefix to be used for specimens encoded in this collection';
comment on column collections.code_prefix_separator is 'Character chain used to separate code prefix from code core';
comment on column collections.code_suffix is 'Default code suffix to be used for specimens encoded in this collection';
comment on column collections.code_suffix_separator is 'Character chain used to separate code suffix from code core';
comment on column collections.code_specimen_duplicate is 'Flag telling if the whole specimen code has to be copied when you do a duplicate';
comment on column collections.is_public is 'Flag telling if the collection can be found in the public search';

create table collections_rights
       (
        id serial,
        db_user_type smallint not null default 1,
        collection_ref integer not null default 0,
        user_ref integer not null default 0,
        constraint pk_collections_right primary key (id),
        constraint fk_collections_rights_users foreign key (user_ref) references users(id) on delete cascade,
        constraint fk_collections_rights_collections foreign key (collection_ref) references collections(id) on delete cascade,
        constraint unq_collections_rights unique (collection_ref, user_ref)
       );

comment on table collections_rights is 'List of rights of given users on given collections';
comment on column collections_rights.id is 'Unique identifier for collection rights';
comment on column collections_rights.collection_ref is 'Reference of collection concerned - id field of collections table';
comment on column collections_rights.user_ref is 'Reference of user - id field of users table';
comment on column collections_rights.db_user_type is 'Integer is representing a role: 1 for registered user, 2 for encoder, 4 for collection manager, 8 for system admin,...';

create table informative_workflow
       (
        id serial,
        user_ref integer,
        formated_name varchar not null default 'anonymous',
        status varchar not null default 'suggestion',
        modification_date_time timestamp default now() not null,
        is_last boolean not null default true,
        comment varchar not null ,
        constraint pk_informative_workflow primary key (id),
        constraint fk_informative_workflow_users foreign key (user_ref) references users(id) ON DELETE CASCADE
       )
inherits (template_table_record_ref);
comment on table informative_workflow is 'Workflow information for each record encoded';
comment on column informative_workflow.user_ref is 'Reference of user - id field of users table';
comment on column informative_workflow.formated_name is 'used to allow non registered user to add a workflow' ;
comment on column informative_workflow.referenced_relation is 'Reference-Name of table concerned';
comment on column informative_workflow.record_id is 'ID of record a workflow is defined for';
comment on column informative_workflow.status is 'Record status number: to correct, to be corrected or published ';
comment on column informative_workflow.modification_date_time is 'Date and time of status change - last date/time is used as actual status, but helps also to keep an history of status change';
comment on column informative_workflow.comment is 'Complementary comments';
COMMENT on COLUMN informative_workflow.is_last is 'a flag witch allow us to know if the workflow for this referenced_relation/record id is the latest' ;

create table users_tracking
       (
        id serial,
        referenced_relation varchar not null,
        record_id integer not null,
        user_ref integer not null,
        action varchar not null default 'insert',
        old_value hstore,
        new_value hstore,
        modification_date_time timestamp default now() not null,
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

create table collection_maintenance
       (
        id serial,
        people_ref integer,
        category varchar not null default 'action',
        action_observation varchar not null,
        description varchar,
        description_indexed text,
        modification_date_time timestamp default now() not null,
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
comment on column collection_maintenance.description_indexed is 'indexed form of description field';
comment on column collection_maintenance.modification_date_time is 'Last update date/time';

create table my_saved_searches
       (
        id serial,
        user_ref integer not null,
        name varchar not null default 'default',
        search_criterias varchar not null,
        favorite boolean not null default false,
        modification_date_time timestamp default now() not null,
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

create table my_widgets
       (
        id serial,
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
comment on column my_widgets.collections is 'list of collections which user_ref has rights to see';
comment on column my_widgets.all_public is 'Set to determine if the widget available for a registered user by default or not';

create table template_classifications
       (
        name varchar not null,
        name_indexed varchar,
        level_ref integer not null,
        status varchar not null default 'valid',
        local_naming boolean not null default false,
        color varchar,
        path varchar not null default '/',
        parent_ref integer
       );
comment on table template_classifications is 'Template table used to construct every common data in each classifications tables (taxonomy, chronostratigraphy, lithostratigraphy,...)';
comment on column template_classifications.name is 'Classification unit name';
comment on column template_classifications.name_indexed is 'Indexed form of name field for ordering';
comment on column template_classifications.level_ref is 'Reference of classification level the unit is encoded in';
comment on column template_classifications.status is 'Validitiy status: valid, invalid, in discussion';
comment on column template_classifications.local_naming is 'Flag telling the appelation is local or internationally recognized';
comment on column template_classifications.color is 'Hexadecimal value of color associated to the unit';
comment on column template_classifications.path is 'Hierarchy path (/ for root)';
comment on column template_classifications.parent_ref is 'Id of parent - id field from table itself';

create table classification_keywords
  (
    id serial,
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

create sequence classification_synonymies_group_id_seq;

create table classification_synonymies
  (
    id serial,
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

create table taxonomy
       (
        id serial,
        extinct boolean default false not null,
        constraint pk_taxonomy primary key (id),
        constraint unq_taxonomy unique (path, name_indexed, level_ref),
        constraint fk_taxonomy_level_ref_catalogue_levels foreign key (level_ref) references catalogue_levels(id),
        constraint fk_taxonomy_parent_ref_taxonomy foreign key (parent_ref) references taxonomy(id) on delete cascade
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

create table chronostratigraphy
       (
        id serial,
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

create table lithostratigraphy
       (
        id serial,
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

create table mineralogy
       (
        id serial,
        code varchar not null,
        classification varchar not null default 'strunz',
        formule varchar,
        formule_indexed varchar,
        cristal_system varchar,
        constraint pk_mineralogy primary key (id),
        constraint unq_mineralogy unique (path, name_indexed, level_ref, code),
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

create table lithology
       (
        id serial,
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

create table igs
       (
         id serial,
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

create table specimens
       (
        id serial,
        category varchar not null default 'physical',
        collection_ref integer not null,
        expedition_ref integer,
        gtu_ref integer,
        taxon_ref integer,
        litho_ref integer,
        chrono_ref integer,
        lithology_ref integer,
        mineral_ref integer,
        acquisition_category varchar not null default '',
        acquisition_date_mask integer not null default 0,
        acquisition_date date not null default '01/01/0001',
        station_visible boolean not null default true,
        ig_ref integer,

        type varchar not null default 'specimen',
        type_group varchar not null default 'specimen',
        type_search varchar not null default 'specimen',
        sex varchar not null default 'undefined',
        stage varchar not null default 'undefined',
        state varchar not null default 'not applicable',
        social_status varchar not null default 'not applicable',
        rock_form varchar not null default 'not applicable',


        specimen_part varchar not null default 'specimen',
        complete boolean not null default true,
        institution_ref integer,
        building varchar,
        floor varchar,
        room varchar,
        row varchar,
        col varchar,
        shelf varchar,
        container varchar,
        sub_container varchar,
        container_type varchar not null default 'container',
        sub_container_type varchar not null default 'container',
        container_storage varchar not null default 'dry',
        sub_container_storage varchar not null default 'dry',
        surnumerary boolean not null default false,
        specimen_status varchar not null default 'good state',
        specimen_count_min integer not null default 1,
        specimen_count_max integer not null default 1,
        object_name text,
        object_name_indexed text not null default '',


    spec_ident_ids integer[] not null default '{}',
    spec_coll_ids integer[] not null default '{}',
    spec_don_sel_ids integer[] not null default '{}',
    collection_type varchar,
    collection_code varchar,
    collection_name varchar,
    collection_is_public boolean,
    collection_parent_ref integer,
    collection_path varchar,
    expedition_name varchar,
    expedition_name_indexed varchar,

    gtu_code varchar,
    gtu_from_date_mask integer,
    gtu_from_date timestamp,
    gtu_to_date_mask integer,
    gtu_to_date timestamp,
    gtu_tag_values_indexed varchar[],
    gtu_country_tag_value varchar,
    gtu_country_tag_indexed varchar[],
    gtu_province_tag_value varchar,
    gtu_province_tag_indexed varchar[],
    gtu_others_tag_value varchar,
    gtu_others_tag_indexed varchar[],
    gtu_elevation double precision,
    gtu_elevation_accuracy double precision,
    gtu_location POINT,

    taxon_name varchar,
    taxon_name_indexed varchar,
    taxon_level_ref integer,
    taxon_level_name varchar,
    taxon_status varchar,
    taxon_path varchar,
    taxon_parent_ref integer,
    taxon_extinct boolean,

    litho_name varchar,
    litho_name_indexed varchar,
    litho_level_ref integer,
    litho_level_name varchar,
    litho_status varchar,
    litho_local boolean,
    litho_color varchar,
    litho_path varchar,
    litho_parent_ref integer,

    chrono_name varchar,
    chrono_name_indexed varchar,
    chrono_level_ref integer,
    chrono_level_name varchar,
    chrono_status varchar,
    chrono_local boolean,
    chrono_color varchar,
    chrono_path varchar,
    chrono_parent_ref integer,

    lithology_name varchar,
    lithology_name_indexed varchar,
    lithology_level_ref integer,
    lithology_level_name varchar,
    lithology_status varchar,
    lithology_local boolean,
    lithology_color varchar,
    lithology_path varchar,
    lithology_parent_ref integer,

    mineral_name varchar,
    mineral_name_indexed varchar,
    mineral_level_ref integer,
    mineral_level_name varchar,
    mineral_status varchar,
    mineral_local boolean,
    mineral_color varchar,
    mineral_path varchar,
    mineral_parent_ref integer,

    ig_num varchar,
    ig_num_indexed varchar,
    ig_date_mask integer,
    ig_date date,

        constraint pk_specimens primary key (id),
        constraint fk_specimens_expeditions foreign key (expedition_ref) references expeditions(id),
        constraint fk_specimens_gtu foreign key (gtu_ref) references gtu(id),
        constraint fk_specimens_collections foreign key (collection_ref) references collections(id),
        constraint fk_specimens_taxonomy foreign key (taxon_ref) references taxonomy(id),
        constraint fk_specimens_lithostratigraphy foreign key (litho_ref) references lithostratigraphy(id),
        constraint fk_specimens_lithology foreign key (lithology_ref) references lithology(id),
        constraint fk_specimens_mineralogy foreign key (mineral_ref) references mineralogy(id),
        constraint fk_specimens_chronostratigraphy foreign key (chrono_ref) references chronostratigraphy(id),
        constraint fk_specimens_igs foreign key (ig_ref) references igs(id),

        constraint fk_specimen_institutions foreign key (institution_ref) references people(id) ON DELETE no action,
        constraint chk_chk_specimen_parts_minmax check (specimen_count_min <= specimen_count_max),
        constraint chk_chk_specimen_part_min check (specimen_count_min >= 0)
       );



comment on table specimens is 'Specimens or batch of specimens stored in collection';
comment on column specimens.id is 'Unique identifier of a specimen or batch of specimens';
comment on column specimens.collection_ref is 'Reference of collection the specimen is grouped under - id field of collections table';
comment on column specimens.expedition_ref is 'When acquisition category is expedition, contains the reference of the expedition having conducted to the current specimen capture - id field of expeditions table';
comment on column specimens.gtu_ref is 'Reference of the sampling location the specimen is coming from - id field of gtu table';
comment on column specimens.litho_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of lithostratigraphic unit the specimen have been found into - id field of lithostratigraphy table';
comment on column specimens.chrono_ref is 'When encoding a rock, mineral or paleontologic specimen, contains the reference of chronostratigraphic unit the specimen have been found into - id field of chronostratigraphy table';
comment on column specimens.taxon_ref is 'When encoding a ''living'' specimen, contains the reference of the taxon unit defining the specimen - id field of taxonomy table';
comment on column specimens.acquisition_category is 'Describe how the specimen was collected: expedition, donation,...';
comment on column specimens.acquisition_date_mask is 'Mask Flag to know wich part of the date is effectively known: 32 for year, 16 for month and 8 for day';
comment on column specimens.acquisition_date is 'Date Composed (if possible) of the acquisition';
comment on column specimens.station_visible is 'Flag telling if the sampling location can be visible or must be hidden for the specimen encoded';
comment on column specimens.lithology_ref is 'Reference of a rock classification unit associated to the specimen encoded - id field of lithology table';
comment on column specimens.mineral_ref is 'Reference of a mineral classification unit associated to the specimen encoded - id field of mineralogy table';
comment on column specimens.ig_ref is 'Reference of ig number this specimen has been associated to';
comment on column specimens.category is 'Type of specimen encoded: a physical object stored in collections, an observation, a figurate specimen,...';

comment on column specimens.type is 'Special status given to specimen: holotype, paratype,...';
comment on column specimens.type_group is 'For some special status, a common appelation is used - ie: topotype and cotype are joined into a common appelation of syntype';
comment on column specimens.type_search is 'On the interface, the separation in all special status is not suggested for non official appelations. For instance, an unified grouping name is provided: type for non official appelation,...';
comment on column specimens.sex is 'sex: male , female,...';
comment on column specimens.stage is 'stage: adult, juvenile,...';
comment on column specimens.state is 'state - a sex complement: ovigerous, pregnant,...';
comment on column specimens.social_status is 'For social specimens, give the social status/role of the specimen in colony';
comment on column specimens.rock_form is 'For rock specimens, a descriptive form can be given: polygonous,...';

comment on column specimens.specimen_part is 'Description of the part stored in conservatory: the whole specimen or a given precise part such as skelleton, head, fur,...';
comment on column specimens.building is 'Building the specimen is stored in';
comment on column specimens.floor is 'Floor the specimen is stored in';
comment on column specimens.room is 'Room the specimen is stored in';
comment on column specimens.row is 'Row the specimen is stored in';
comment on column specimens.shelf is 'Shelf the specimen is stored in';
comment on column specimens.container is 'Container the specimen is stored in';
comment on column specimens.sub_container is 'Sub-Container the specimen is stored in';
comment on column specimens.container_type is 'Type of container: box, plateau-caisse,...';
comment on column specimens.sub_container_type is 'Type of sub-container: slide, needle,...';
comment on column specimens.container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column specimens.sub_container_storage is 'Conservative medium used: formol, alcohool, dry,...';
comment on column specimens.surnumerary is 'Tells if this specimen has been added after first inventory';
comment on column specimens.specimen_status is 'Specimen status: good state, lost, damaged,...';
comment on column specimens.specimen_count_min is 'Minimum number of specimens';
comment on column specimens.specimen_count_max is 'Maximum number of specimens';
comment on column specimens.complete is 'Flag telling if specimen is complete or not';


create table codes
       (
        id serial,
        code_category varchar not null default 'main',
        code_prefix varchar,
        code_prefix_separator varchar,
        code varchar,
        code_suffix varchar,
        code_suffix_separator varchar,
        full_code_indexed varchar not null,
        code_date timestamp not null default '0001-01-01 00:00:00',
        code_date_mask integer not null default 0,
        code_num bigint default 0,
        constraint pk_codes primary key (id),
        constraint unq_codes unique (referenced_relation, record_id, full_code_indexed,code_category)
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
comment on column codes.full_code_indexed is 'Full code composition by code_prefix, code and code suffix concatenation and indexed for unique check purpose';
comment on column codes.code_date is 'Date of code creation (fuzzy date)';
comment on column codes.code_date_mask is 'Mask used for code date';
comment on column codes.referenced_relation is 'Reference name of table concerned';
comment on column codes.record_id is 'Identifier of record concerned';

create table insurances
       (
        id serial,
        insurance_value numeric(16,2) not null,
        insurance_currency varchar not null default '€',
        date_from_mask integer not null default 0,
        date_from date not null default '01/01/0001',
        date_to_mask integer not null default 0,
        date_to date not null default '31/12/2038',
        insurer_ref integer,
        contact_ref integer,
        constraint pk_insurances primary key (id),
        constraint unq_insurances unique (referenced_relation, record_id, date_from, date_to, insurer_ref),
        constraint fk_insurances_people foreign key (insurer_ref) references people(id) on delete set null,
        constraint fk_insurances_contact foreign key (contact_ref) references people(id) on delete set null,
        constraint chk_chk_insurances check (insurance_value > 0)
       )
       inherits (template_table_record_ref);
comment on table insurances is 'List of insurances values for given specimen or the loan';
comment on column insurances.referenced_relation is 'Reference-Name of table concerned';
comment on column insurances.record_id is 'Identifier of record concerned';
comment on column insurances.insurance_currency is 'Currency used with insurance value';
comment on column insurances.insurance_value is 'Insurance value';
comment on column insurances.insurer_ref is 'Reference of the insurance firm an insurance have been subscripted at';

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

comment on column specimens_relationships.relationship_type is 'Type of relationship: host, part of, related to, ...';
comment on column specimens_relationships.unit_type is 'Type of the related unit : spec, taxo or mineralo';
comment on column specimens_relationships.quantity is 'Quantity of accompanying mineral';
comment on column specimens_relationships.institution_ref is 'External Specimen related institution';
comment on column specimens_relationships.source_name is 'External Specimen related  source DB';
comment on column specimens_relationships.source_id is 'External Specimen related id in the source';

create table collecting_tools
       (
        id serial,
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

create table specimen_collecting_tools
  (
    id serial,
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

create table collecting_methods
       (
        id serial,
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

create table specimen_collecting_methods
  (
    id serial,
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

create table preferences
  (
    id serial,
    user_ref integer not null,
    pref_key varchar not null,
    pref_value varchar not null,
    constraint pk_preferences primary key (id),
    constraint fk_users_preferences foreign key (user_ref) references users(id) on delete cascade
  );

comment on table preferences is 'Table to handle users preferences';
comment on column preferences.user_ref is 'The referenced user id';
comment on column preferences.pref_key is 'The classification key of the preference. eg: color';
comment on column preferences.pref_value is 'The value of the preference for this user eg: red';

create table flat_dict
(
  id serial,
  referenced_relation varchar not null,
  dict_field varchar not null,
  dict_value varchar not null,
  dict_depend varchar not null default '',
  constraint unq_flat_dict unique (dict_value, dict_field, referenced_relation, dict_depend),
  constraint pk_flat_dict primary key (id)
);


comment on table flat_dict is 'Flat table compiling all small distinct values for a faster search like types, code prefixes ,...';
comment on column flat_dict.referenced_relation is 'The table where the value come from';
comment on column flat_dict.dict_field is 'the field name of where the value come from';
comment on column flat_dict.dict_value is 'the distinct value';

create table imports
  (
    id serial,
    user_ref integer not null,
    format varchar not null,
    collection_ref integer,
    filename varchar not null,
    state varchar not null default '',
    created_at timestamp not null default now(),
    updated_at timestamp default now(),
    initial_count integer not null default 0,
    is_finished boolean  not null default false,
    errors_in_import text,
    template_version text,
    exclude_invalid_entries boolean not null default false,
    constraint pk_import primary key (id) ,
    constraint fk_imports_collections foreign key (collection_ref) references collections(id) on update no action on delete cascade,
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
comment on column imports.errors_in_import is 'Contains the error encountered while trying to import data from template';
comment on column imports.template_version is 'Contains the template version (when applicable)';
comment on column imports.exclude_invalid_entries is 'Tell if, for this import, match should exclude the invalid units';

create table staging
  (
    id serial,
    import_ref integer not null,
    create_taxon boolean not null default false,
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
    mineral_classification varchar,
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
    part varchar,
    part_status varchar,
    institution_ref integer,
    institution_name varchar,
    building varchar,
    floor varchar,
    room varchar,
    row varchar,
    col varchar,
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
    object_name text,
    constraint pk_staging primary key (id),
    constraint fk_staging_import foreign key (import_ref) references imports(id) on delete cascade,
    constraint fk_staging_taxonomy foreign key (taxon_ref) references taxonomy(id) on delete set NULL,
    constraint fk_staging_chronostratigraphy foreign key (chrono_ref) references chronostratigraphy(id) on delete set NULL,
    constraint fk_staging_lithostratigraphy foreign key (litho_ref) references lithostratigraphy(id) on delete set NULL,
    constraint fk_staging_lithology foreign key (lithology_ref) references lithology(id) on delete set NULL,
    constraint fk_staging_mineralogy foreign key (mineral_ref) references mineralogy(id) on delete set NULL
  );

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
comment on table staging_info is 'used to make association between catalogue informations and staging eg taxon properties';
comment on column staging_info.id is 'Unique identifier of a grouped tag';
comment on column staging_info.staging_ref is 'Ref of a staging record';
comment on column staging_info.referenced_relation is 'catalogue where associating the info' ;

CREATE TABLE staging_relationship
(
  id serial NOT NULL,
  record_id integer NOT NULL,
  referenced_relation character varying NOT NULL,
  relationship_type character varying,
  staging_related_ref integer,
  taxon_ref integer, -- Reference of the related specimen
  mineral_ref integer, -- Reference of related mineral
  institution_ref integer,
  institution_name text,
  source_name text,
  source_id text,
  quantity numeric(16,2),
  unit character varying DEFAULT '%'::character varying,
  unit_type character varying NOT NULL DEFAULT 'specimens'::character varying,

  CONSTRAINT pk_staging_relationship PRIMARY KEY (id),
  CONSTRAINT fk_record_id FOREIGN KEY (record_id)
      REFERENCES staging (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE,
  CONSTRAINT fk_specimens_relationships_mineralogy FOREIGN KEY (mineral_ref)
      REFERENCES mineralogy (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_specimens_relationships_institution FOREIGN KEY (institution_ref)
      REFERENCES people (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_specimens_relationships_taxonomy FOREIGN KEY (taxon_ref)
      REFERENCES taxonomy (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
COMMENT ON COLUMN staging_relationship.record_id IS 'id of the orignial record';
COMMENT ON COLUMN staging_relationship.referenced_relation IS 'where to find the record_id, referenced_relation is always staging but this field uis mandatory for addRelated php function';
COMMENT ON COLUMN staging_relationship.relationship_type IS 'relation type (eg. host, parent, part of)';
COMMENT ON COLUMN staging_relationship.staging_related_ref IS 'the record id associated, this record id must be found in the same import file';
COMMENT ON COLUMN staging_relationship.taxon_ref IS 'Reference of the related specimen';
COMMENT ON COLUMN staging_relationship.mineral_ref IS 'Reference of related mineral';
COMMENT ON COLUMN staging_relationship.institution_ref IS 'the institution id associated to this relationship';
COMMENT ON COLUMN staging_relationship.institution_name IS 'the institution name associated to this relationship, used to add to darwin institution if it dont exist';
COMMENT ON COLUMN staging_relationship.source_name IS 'External Specimen related  source DB';
COMMENT ON COLUMN staging_relationship.source_id IS 'External Specimen related id in the source';
COMMENT ON COLUMN specimens_relationships.quantity IS 'Quantity of accompanying mineral';

create table staging_collecting_methods
  (
    id serial,
    staging_ref integer not null,
    collecting_method_ref integer not null,
    constraint pk_staging_collecting_methods primary key (id),
    constraint unq_staging_collecting_methods unique (staging_ref, collecting_method_ref),
    constraint fk_staging_collecting_methods_staging foreign key (staging_ref) references staging (id) on delete cascade,
    constraint fk_staging_collecting_methods_method foreign key (collecting_method_ref) references collecting_methods (id) on delete cascade
  );

comment on table staging_collecting_methods is 'Association of collecting methods with Staging';
comment on column staging_collecting_methods.id is 'Unique identifier of an association';
comment on column staging_collecting_methods.staging_ref is 'Identifier of a specimen - comes from staging table (id field)';
comment on column staging_collecting_methods.collecting_method_ref is 'Identifier of a collecting method - comes from collecting_methods table (id field)';

create table  staging_tag_groups
       (
        id serial,
        staging_ref integer not null,
        group_name varchar not null,
        sub_group_name varchar not null,
        tag_value varchar not null,
        constraint pk_staging_tag_groups primary key (id),
        CONSTRAINT fk_staging_tag_groups FOREIGN KEY (staging_ref) REFERENCES staging (id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE CASCADE
       );

comment on table staging_tag_groups is 'List of grouped tags for an imported row (copy of tag group)';
comment on column staging_tag_groups.id is 'Unique identifier of a grouped tag';
comment on column staging_tag_groups.staging_ref is 'Ref of an imported line';
comment on column staging_tag_groups.group_name is 'Group name under which the tag is grouped: Administrative area, Topographic structure,...';
comment on column staging_tag_groups.sub_group_name is 'Sub-Group name under which the tag is grouped: Country, River, Mountain,...';
comment on column staging_tag_groups.tag_value is 'Ensemble of Tags';

create table staging_people
       (
        id serial,
        people_type varchar not null default 'author',
        people_sub_type varchar not null default '',
        order_by integer not null default 1,
        people_ref integer,
        formated_name varchar,
        constraint pk_staging_people primary key (id),
        constraint fk_staging_people_list_person foreign key (people_ref) references people(id) on delete cascade
       )
inherits (template_table_record_ref);
comment on table staging_people is 'List of people of staging units';
comment on column staging_people.id is 'Unique identifier of record';
comment on column staging_people.referenced_relation is 'Identifier-Name of table the units come from';
comment on column staging_people.record_id is 'Identifier of record concerned in table concerned';
comment on column staging_people.people_type is 'Type of "people" associated to the staging unit: authors, collectors, defined,  ...';
comment on column staging_people.people_sub_type is 'Type of "people" associated to the staging unit: Main author, corrector, taking the sense from,...';
comment on column staging_people.people_ref is 'Reference of person concerned - id field of people table';
comment on column staging_people.order_by is 'Integer used to order the persons in a list';
comment on column staging_people.formated_name is 'full name of the people';


create table loans
    (
      id serial,
      name varchar not null default '',
      description varchar not null default '',
      search_indexed text not null,
      from_date date,
      to_date date,
      extended_to_date date,
      constraint pk_loans primary key (id)
    );

comment on table loans is 'Table holding an entire loan made of multiple loan items may also be linked to other table as comment, properties , ...';

comment on column loans.id is 'Unique identifier of record';
comment on column loans.name is 'Global name of the loan. May be a sort of code of other naming scheme';
comment on column loans.description is 'Description of the meaning of the loan';
comment on column loans.search_indexed is 'indexed getting Description and title of the loan';
comment on column loans.from_date  is 'Date of the start of the loan';
comment on column loans.to_date  is 'Planned date of the end of the loan';

create table loan_items (
  id serial,
  loan_ref integer not null,
  ig_ref integer,
  from_date date,
  to_date date,
  specimen_ref integer,
  details varchar default '',
  constraint pk_loan_items primary key (id),
  constraint fk_loan_items_ig foreign key (ig_ref) references igs(id),
  constraint fk_loan_items_loan_ref foreign key (loan_ref) references loans(id) on delete CASCADE,
  constraint fk_loan_items_specimen_ref foreign key (specimen_ref) references specimens(id) on delete set null,
  constraint unq_loan_items unique(loan_ref, specimen_ref)
);

comment on table loan_items is 'Table holding an item of a loan. It may be a part from darwin or only an generic item';

comment on column loan_items.id is 'Unique identifier of record';
comment on column loan_items.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_items.from_date is 'Date when the item was sended';
comment on column loan_items.to_date is 'Date when the item was recieved back';
comment on column loan_items.ig_ref is 'Optional ref to an IG stored in the igs table';
comment on column loan_items.specimen_ref is 'Optional reference to a Darwin Part';
comment on column loan_items.details is 'Textual details describing the item';

create table loan_rights (
  id serial,
  loan_ref integer not null,
  user_ref integer not null,
  has_encoding_right boolean not null default false,

  constraint pk_loan_rights primary key (id),
  constraint fk_loan_rights_loan_ref foreign key (loan_ref) references loans(id) on delete cascade,
  constraint fk_loan_rights_user_ref foreign key (user_ref) references users(id) on delete cascade,
  constraint unq_loan_rights unique (loan_ref, user_ref)
);


comment on table loan_rights is 'Table describing rights into an entire loan (if user is in the table he has at least viewing rights)';

comment on column loan_rights.id is 'Unique identifier of record';
comment on column loan_rights.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_rights.user_ref is 'Mandatory Reference to a user';
comment on column loan_rights.has_encoding_right is 'Bool saying if the user can edit a loan';

create table loan_status (
  id serial,
  loan_ref integer not null,
  user_ref integer not null,
  status varchar not null default 'new',
  modification_date_time timestamp default now() not null,
  comment varchar not null default '',
  is_last boolean not null default true,
  constraint pk_loan_status primary key (id),
  constraint fk_loan_status_loan_ref foreign key (loan_ref) references loans(id) on delete cascade,
  constraint fk_loan_status_user_ref foreign key (user_ref) references users(id) on delete cascade

);

comment on table loan_status is 'Table describing various states of a loan';

comment on column loan_status.id is 'Unique identifier of record';
comment on column loan_status.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_status.user_ref is 'Mandatory Reference to a user';
comment on column loan_status.status is 'Current status of the loan in a list (new, closed, running, ...)';
comment on column loan_status.modification_date_time is 'date of the modification';
comment on column loan_status.comment is 'comment of the status modification';
comment on column loan_status.is_last is 'flag telling which line is the current line';

create table loan_history (
  id serial,
  loan_ref integer not null,
  referenced_table text not null,
  modification_date_time timestamp default now() not null,
  record_line hstore,
  constraint pk_loan_history primary key (id),
  constraint fk_loan_history_loan_ref foreign key (loan_ref) references loans(id) on delete cascade
);

comment on table loan_history is 'Table is a snapshot of an entire loan and related informations at a certain time';

comment on column loan_history.loan_ref is 'Mandatory Reference to a loan';
comment on column loan_history.referenced_table is 'Mandatory Reference to the table refereced';
comment on column loan_history.modification_date_time is 'date of the modification';
comment on column loan_history.record_line is 'hstore containing the whole line of referenced_table';

create table multimedia_todelete (
  id serial,
  uri text,
  constraint pk_multimedia_todelete primary key (id)
);

comment on table multimedia_todelete is 'Table here to save deleted multimedia files waiting for a deletion on the disk';
comment on column multimedia_todelete.uri is 'URI of the file to delete';


CREATE TABLE bibliography (
  id serial,
  title varchar not null,
  title_indexed varchar not null,
  type varchar not null,
  abstract varchar not null default '',
  year integer,
  constraint pk_bibliography primary key (id),
  constraint unq_bibliography unique (title_indexed, type, year)
);
comment on table bibliography is 'List of expeditions made to collect specimens';
comment on column bibliography.id is 'Unique identifier';
comment on column bibliography.title is 'bibliography title';
comment on column bibliography.title_indexed is 'Indexed form of title';
comment on column bibliography.type is 'bibliography type : article, book, booklet';
comment on column bibliography.abstract is 'optional abstract of the bibliography';
comment on column bibliography.year is 'The year of publication (or, if unpublished, the year of creation)';


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



create table db_version (
 id integer not null,
 update_at timestamp default now()

);

comment on table db_version is 'Table holding the database version and update date';

create table staging_catalogue
  (
  id serial,
  import_ref integer not null,
  name varchar not null,
  level_ref integer,
  parent_ref integer,
  catalogue_ref integer,
  parent_updated boolean default false,
  constraint pk_staging_catalogue primary key (id),
  constraint fk_stg_catalogue_level_ref foreign key (level_ref) references catalogue_levels(id),
  constraint fk_stg_catalogue_import_ref foreign key (import_ref) references imports(id) on delete cascade
  );

comment on table staging_catalogue is 'Stores the catalogues hierarchy to be imported';
comment on column staging_catalogue.id is 'Unique identifier of a to be imported catalogue unit entry';
comment on column staging_catalogue.import_ref is 'Reference of import concerned - from table imports';
comment on column staging_catalogue.name is 'Name of unit to be imported/checked';
comment on column staging_catalogue.level_ref is 'Level of unit to be imported/checked';
comment on column staging_catalogue.parent_ref is 'ID of parent the unit is attached to. Right after the load of xml, it refers recursively to an entry in the same staging_catalogue table. During the import it is replaced by id of the parent from the concerned catalogue table.';
comment on column staging_catalogue.catalogue_ref is 'ID of unit in concerned catalogue table - set during import process';
comment on column staging_catalogue.parent_updated is 'During the catalogue import process, tells if the parent ref has already been updated with one catalogue entry or not';

create table reports
 (
    id serial,
    user_ref integer not null,
    name varchar not null,
    uri varchar,
    lang char(2) not null,
    format varchar not null default 'csv',
    comment varchar,
    parameters hstore,
    CONSTRAINT pk_reports PRIMARY KEY (id),
    CONSTRAINT fk_reports_users FOREIGN KEY (user_ref)
    REFERENCES users (id) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE CASCADE
  );
comment on table reports is 'Table to handle users reports asking';
comment on column reports.user_ref is 'The referenced user id';
comment on column reports.name is 'The report name';
comment on column reports.uri is 'The path where the report file is stored, if uri is not null then the report has already been launched';
comment on column reports.lang is 'The lang asked for this report';
comment on column reports.format is 'The file type of the report file, generaly csv or xls';
comment on column reports.comment is 'A comment to add to the report, just in case.';
comment on column reports.parameters is 'if the report requires some information (such as collection_ref), they are here';
