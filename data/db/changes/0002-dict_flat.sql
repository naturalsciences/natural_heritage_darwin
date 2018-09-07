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


INSERT INTO flat_dict (dict_value, referenced_relation, dict_field)
 (
  select distinct code_prefix_separator , 'codes' ,'code_prefix_separator'
    FROM codes where code_prefix_separator is not null

  UNION

  select distinct code_suffix_separator , 'codes' ,'code_suffix_separator'
    FROM codes where code_suffix_separator is not null

  UNION

  select distinct action_observation , 'collection_maintenance' ,'action_observation'
    FROM collection_maintenance where action_observation is not null

  UNION

  select distinct determination_status , 'identifications' ,'determination_status'
    FROM identifications where determination_status is not null

  UNION

  select distinct sub_type , 'people' ,'sub_type'
    FROM people where sub_type is not null

  UNION

  select distinct title , 'people' ,'title'
    FROM people where title is not null

  UNION

  select distinct country , 'people_addresses' ,'country'
    FROM people_addresses where country is not null

  UNION

  select distinct insurance_currency , 'insurances' ,'insurance_currency'
    FROM insurances where insurance_currency is not null

  UNION

  select distinct cristal_system , 'mineralogy' ,'cristal_system'
    FROM mineralogy where cristal_system is not null  

  UNION

  select distinct "type" , 'specimen_individuals' ,'type'
    FROM specimen_individuals where "type" is not null  

  UNION

  select distinct type_group , 'specimen_individuals' ,'type_group'
    FROM specimen_individuals where type_group is not null  

  UNION
    
  select distinct type_search , 'specimen_individuals' ,'type_search'
    FROM specimen_individuals where type_search is not null  

  UNION

  select distinct sex , 'specimen_individuals' ,'sex'
    FROM specimen_individuals where sex is not null  

  UNION

  select distinct state , 'specimen_individuals' ,'state'
    FROM specimen_individuals where state is not null  

  UNION

  select distinct stage , 'specimen_individuals' ,'stage'
    FROM specimen_individuals where stage is not null  

  UNION

  select distinct social_status , 'specimen_individuals' ,'social_status'
    FROM specimen_individuals where social_status is not null  

  UNION

  select distinct rock_form , 'specimen_individuals' ,'rock_form'
    FROM specimen_individuals where rock_form is not null  

  UNION

  select distinct host_relationship , 'specimens' ,'host_relationship'
    FROM specimens where host_relationship is not null  

  UNION

  select distinct form , 'specimens_accompanying' ,'form'
    FROM specimens_accompanying where form is not null  

  UNION

  select distinct title , 'users' ,'title'
    FROM users where title is not null  

  UNION

  select distinct sub_type , 'users' ,'sub_type'
    FROM users where sub_type is not null  

  UNION

  select distinct country , 'users_addresses' ,'country'
    FROM users_addresses where country is not null  

  UNION

  select distinct container_type , 'specimen_parts' ,'container_type'
    FROM specimen_parts where container_type is not null  

  UNION

  select distinct sub_container_type , 'specimen_parts' ,'sub_container_type'
    FROM specimen_parts where sub_container_type is not null  

  UNION

  select distinct specimen_part , 'specimen_parts' ,'specimen_part'
    FROM specimen_parts where specimen_part is not null  

  UNION
  select distinct specimen_status , 'specimen_parts' ,'specimen_status'
    FROM specimen_parts where specimen_status is not null 

  UNION
  select distinct floor , 'specimen_parts' ,'floor'
    FROM specimen_parts where floor is not null  
  
  UNION 
   select distinct shelf , 'specimen_parts' ,'shelf'
    FROM specimen_parts where shelf is not null  

  UNION

  select distinct row  , 'specimen_parts' ,'row'
    FROM specimen_parts where row is not null  

  UNION

  select distinct room , 'specimen_parts' ,'room'
    FROM specimen_parts where room is not null  

  UNION
  select distinct building , 'specimen_parts' ,'building'
    FROM specimen_parts where building is not null  
 );

\i createfunctions.sql
\i createtriggers.sql
