begin;
set search_path=darwin2,public;

alter table catalogue_levels add column lvl_order integer not null default 999;
update catalogue_levels set lvl_order = id;
update catalogue_levels set lvl_order = id+1 where id > 63 ;

update catalogue_levels set lvl_order = 64 where id = 85 and level_type='lithostratigraphy';

commit;