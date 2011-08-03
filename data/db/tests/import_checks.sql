\unset ECHO
\i unit_launch.sql
SELECT plan(61);

select diag('Test of staging check without levels');
update people set name_formated_indexed = fulltoindex(coalesce(given_name,'') || coalesce(family_name,''));

insert into imports (id, user_ref, format, filename, collection_ref) VALUES (1,1,'brol','brol.xml',2);
insert into staging (id,import_ref, "level",taxon_name) VALUES (12,1,'specimens','');

select is(true , (select fct_imp_checker_manager(s.*) from staging s));
select is(null , (select taxon_ref from staging s where id = 12));

insert into staging (id,import_ref, "level",taxon_name) VALUES (1,1,'specimens','Falco Peregrinus');
INSERT INTO taxonomy (id, name, level_ref) VALUES (1, 'Falco Coco lus (Brolus 1972)', 1);
INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (2, 'Falco',2,1);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (3, 'Falco Peregrinus', 3,2);

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s ));
select is(3 , (select taxon_ref from staging s where id = 1));

insert into staging (id,import_ref, "level",taxon_name) VALUES (2,1,'specimens','Falco Brolus');

select is(1 ,(select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select  taxon_ref from staging s where id = 2));
select is('taxon=>not_found', (select  status from staging s where id = 2));

insert into staging (id,import_ref, "level",taxon_name) VALUES (3,1,'specimens','Falco coco');

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(1, (select taxon_ref from staging s where id = 3));

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (4, 'Falco Coco lus (Brolus 1974)', 4, 3);
UPDATE staging set taxon_ref = null where id = 3;
UPDATE staging set taxon_name = 'Falco coco',taxon_level_name=null where id = 3;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));
select is('taxon=>too_much', (select  status from staging s where id = 3));

select diag('Test of staging check with levels');

UPDATE staging set taxon_ref = null , taxon_level_name='super_phylum';

delete from taxonomy where id = 4;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));

update taxonomy set level_ref=2 , parent_ref = 1 where id = 3;
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));

update taxonomy set level_ref=3 , parent_ref = 2 where id = 3;

insert into staging (id,import_ref, "level",taxon_name,taxon_level_name) VALUES (4,1,'specimens','Falco Peregrinus','super_phylum' /* 3 */);
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(3, (select taxon_ref from staging s where id = 4));

select diag('Test of staging check with Parent levels');

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (4, 'Falco Coco lus (Brolus 1974)', 4, 3);
update staging SET taxon_ref = null  where id = 3; -- 2 times because of trigger
update staging SET taxon_name = 'Falco Coco lus (Brolus', taxon_level_name ='phylum', taxon_parents = 'domain=>"Falco Peregrinus"' where id = 3;

select is('Falco Coco lus (Brolus', (select taxon_name from staging where id = 3)); 

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));
select is('taxon=>bad_hierarchy'::hstore, (select status from staging where id = 3)); 
select is('Falco Coco lus (Brolus', (select taxon_name from staging where id = 3)); 

update staging SET taxon_parents = '"super_phylum"=>"Falco Peregrinus"'::hstore where id = 3;



select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(4, (select taxon_ref from staging s where id = 3));

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (5, 'Brolz', 2, 1);
update staging SET taxon_parents = '"kingdom"=>"Brolz"'::hstore, taxon_ref = null, status = '' where id = 3;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));



select diag('Test Igs');
INSERT INTO igs(id, ig_num) VALUES (1458, '11');
INSERT INTO igs(id, ig_num, ig_date, ig_date_mask) VALUES (1459, '13', '01/11/2001',48 /* month & year */);
update staging SET ig_num ='11' where id = 2;
update staging SET ig_num ='12' where id = 3;
update staging SET ig_num ='13', ig_date='01/11/2001' where id = 1;


select is(1 , (select min(fct_imp_checker_igs(s.*)::int) from staging s));
select is(1458, (select ig_ref from staging where id = 2));
select is(null, (select ig_ref from staging where id = 3));
select is(1459, (select ig_ref from staging where id = 1));

update staging set ig_date = '02/11/2001' , ig_num = '13' where id = 3;
select is(1 , (select min(fct_imp_checker_igs(s.*)::int) from staging s));
select is(null, (select ig_ref from staging where id = 3)); /* Null or 1459 ?*/

select diag('Test of Collectors');
update people set title = 'Mr' where id = 2;
update staging set collectors = '{Hello World,Paul Andre Duchesne}'::text[], status = delete(status,'taxon') where id =  3;
select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(1, (select count(*)::int from catalogue_people where record_id = 3 and referenced_relation='staging')); 
select is('collectors=>people'::hstore, (select status from staging where id = 3)); 

delete from catalogue_people where record_id = 3 and referenced_relation='staging';

update staging set collectors = '{ROYAL BELGIAN INSTITUTE OF NATURAL SCIENCES,Duchesne Paul Andre}'::text[] where id =  3;
select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(2, (select count(*)::int from catalogue_people where record_id = 3 and referenced_relation='staging')); 
select is(1,(select order_by from catalogue_people where record_id = 3 and referenced_relation='staging' and people_ref = 2));
select is(0,(select order_by from catalogue_people where record_id = 3 and referenced_relation='staging' and people_ref = 1));
select is(''::hstore, (select status from staging where id = 3)); 

select '#' || min(fct_imp_checker_manager(s.*)::integer) from staging s;

select diag('Test of Import');

update staging set level='individual', parent_ref=4 where id =  1;
insert into staging (id,import_ref,parent_ref, "level",room) VALUES (6,1,1,'specimen part',12);

update staging set gtu_code='My Gtu', gtu_ref=null where id = 4;
insert into staging_tag_groups (staging_ref,group_name, sub_group_name, tag_value)
  VALUES(4,'administrative area', 'populated place','Hello; world; ');
update staging set to_import = true;


select is(true, (select fct_importer_dna(1)));
select is(1, (select count(*)::integer from specimen_individuals));
select is(1, (select count(*)::integer from specimen_parts));
select is(1, (select count(*)::integer from specimens where gtu_ref = 1));
select is('Hello; world; ', (select tag_value from tag_groups where gtu_ref = 1));

update staging set gtu_code='My Gtuz' , gtu_ref=null, taxon_name=null, taxon_level_name=null ,status=''where id = 2;

select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(true, (select fct_importer_dna(1)));
select is(2, (select gtu_ref from specimens where id=3));

update staging set gtu_code='My Gtu' , gtu_ref=null, taxon_name=null, taxon_level_name=null,status='', ig_num = null where id = 3;
select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(true, (select fct_importer_dna(1)));
select is(1, (select gtu_ref from specimens where id=4));

select diag('Test of staging copy');


insert into staging (id,import_ref, "level",taxon_name) VALUES (7,1,'specimens','Falco Pérégrinuz');
insert into staging (id,import_ref, "level",taxon_name) VALUES (8,1,'specimens','Falco Pérégrinuz');
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null , (select taxon_ref from staging s where id = 7));
select is(null , (select taxon_ref from staging s where id = 8));

UPDATE staging set taxon_ref = 3 where id = 7;

select is(3 , (select taxon_ref from staging s where id = 7));
select is(3 , (select taxon_ref from staging s where id = 8));
select is('Falco Peregrinus' , (select taxon_name from staging s where id = 8));

UPDATE staging set expedition_name = 'brool' where id = 7;
UPDATE staging set expedition_name = 'brool' where id = 8;
insert into expeditions (id, name) VALUES (2, 'Antar');

update staging set expedition_ref = 2 where id = 8;
select is(2 , (select expedition_ref from staging s where id = 7));
select is(2 , (select expedition_ref from staging s where id = 8));
select is('Antar' , (select expedition_name from staging s where id = 7));

SELECT * FROM finish();


ROLLBACK;
