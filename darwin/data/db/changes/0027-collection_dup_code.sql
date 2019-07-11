SET search_path = darwin2, public;

alter table collections add column code_specimen_duplicate boolean not null default false;
comment on column collections.code_specimen_duplicate is 'Flag telling if the whole specimen code has to be copied when you do a duplicate';
