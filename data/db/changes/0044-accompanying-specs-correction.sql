begin;
set search_path=darwin2,public;

ALTER TABLE specimens_accompanying drop constraint if exists fk_specimens_accompanying_specimens;
ALTER TABLE specimens_accompanying add constraint "fk_specimens_accompanying_specimens" foreign key (specimen_ref) references specimens(id) on delete cascade;

COMMIT;