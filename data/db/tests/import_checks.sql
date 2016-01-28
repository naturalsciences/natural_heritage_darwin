\unset ECHO
\i unit_launch.sql
SELECT plan(86);

select diag('Test of staging check without levels');
update people set name_formated_indexed = fulltoindex(coalesce(given_name,'') || coalesce(family_name,''));

insert into imports (id, user_ref, format, filename, collection_ref) VALUES (1,1,'brol','brol.xml',2);
insert into staging (id,import_ref,taxon_name) VALUES (12,1,'');

select is(true , (select fct_imp_checker_manager(s.*) from staging s));
select is(null , (select taxon_ref from staging s where id = 12));

insert into staging (id,import_ref,taxon_name) VALUES (1,1,'Falco Peregrinus simpl');
INSERT INTO taxonomy (id, name, level_ref) VALUES (10, 'Falco Coco lus (Brolus 1972)', 1);
INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (20, 'Falco',2,10);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (30, 'Falco Peregrinus simpl', 3,20);
INSERT INTO taxonomy (id, name, level_ref, parent_ref) VALUES (31, 'Falco Phyl', 4,30);
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword) VALUES ('staging', 1, 'GenusOrMonomial', 'Falco');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword) VALUES ('staging', 1, 'AuthorTeamAndYear', 'Peregrinus simpl');

select is(2::BIGINT,(select count(*) from classification_keywords where referenced_relation = 'staging' and record_id in (1,3)));

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s ));
select is(30 , (select taxon_ref from staging s where id = 1));

select is(0::BIGINT,(select count(*) from classification_keywords where referenced_relation = 'staging' and record_id in (1,3)));

insert into staging (id,import_ref,taxon_name) VALUES (2,1,'Falco Brolus');

select is(1 ,(select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select  taxon_ref from staging s where id = 2));
select is('taxon=>not_found', (select  status from staging s where id = 2));

insert into staging (id,import_ref,taxon_name) VALUES (3,1,'Falco coco');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword) VALUES ('staging', 3, 'GenusOrMonomial', 'Falco coco lus');
INSERT INTO classification_keywords (referenced_relation, record_id, keyword_type, keyword) VALUES ('staging', 3, 'AuthorTeamOriginalAndYear', 'Brolus, 1972');

select is(2::BIGINT,(select count(*) from classification_keywords where referenced_relation = 'staging' and record_id in (1,3)));

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(NULL, (select taxon_ref from staging s where id = 3));

UPDATE staging set taxon_name = 'Falco coco Lus Brolus1972' where id = 3;
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(10, (select taxon_ref from staging s where id = 3));

select is(0::BIGINT,(select count(*) from classification_keywords where referenced_relation = 'staging' and record_id in (1,3)));
select is(4::BIGINT,(select count(*) from classification_keywords where referenced_relation = 'taxonomy' and record_id in (10,30)));

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (40, 'Falco Coco lus (Brolus 1974)', 4, 30);
UPDATE staging set taxon_ref = null where id = 3;
UPDATE staging set taxon_name = 'Falco coco lus',taxon_level_name=null where id = 3;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));
select is('taxon=>not_found', (select  status from staging s where id = 3));

select diag('Test of staging check with levels');

UPDATE staging set taxon_ref = null , taxon_level_name='super_phylum';

delete from taxonomy where id = 40;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));

update taxonomy set level_ref=2 , parent_ref = 10 where id = 30;
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));

update taxonomy set level_ref=3 , parent_ref = 20 where id = 30;

insert into staging (id,import_ref,taxon_name,taxon_level_name) VALUES (4,1,'Falco Peregrinus simpl','super_phylum' /* 3 */);
select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(30, (select taxon_ref from staging s where id = 4));

select diag('Test of staging check with Parent levels');

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (40, 'Falco Coco lus (Brolus 1974)', 4, 30);
update staging SET taxon_ref = null where id = 3; -- 2 times because of trigger
update staging SET taxon_name = 'Falco Coco lus (Brolus', taxon_level_name ='phylum', taxon_parents = 'domain=>"Falco Peregrinus simpl"' where id = 3;

select is('Falco Coco lus (Brolus', (select taxon_name from staging where id = 3));

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null, (select taxon_ref from staging s where id = 3));
select is('taxon=>not_found'::hstore, (select status from staging where id = 3));
select is('Falco Coco lus (Brolus', (select taxon_name from staging where id = 3));

update staging SET taxon_parents = '"super_phylum"=>"Falco Peregrinus simpl"'::hstore where id = 3;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(NULL, (select taxon_ref from staging s where id = 3));

INSERT INTO taxonomy (id, name, level_ref,parent_ref) VALUES (50, 'Brolz', 2, 10);
update staging SET taxon_parents = ''::hstore, taxon_ref = null, status = '' where id = 3;

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));

select is(NULL, (select taxon_ref from staging s where id = 3));

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
select is(1459, (select ig_ref from staging where id = 3));

select diag('Test of Collectors');
update people set title = 'Mr' where id = 2;
insert into staging_people(id, record_id, referenced_relation, people_type, formated_name)
VALUES(nextval('staging_people_id_seq'), 3, 'staging', 'collector','Paul Andre Duduche') ;
select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(1, (select count(*)::int from staging_people where record_id = 3 and referenced_relation='staging'));
select is('taxon=>not_found, people=>people'::hstore, (select status from staging where id = 3));

UPDATE staging_people set formated_name = 'Duchesne Paul Andre' where record_id=3 and referenced_relation='staging' ;
insert into staging_people(id, record_id, referenced_relation, people_type, formated_name)
VALUES(nextval('staging_people_id_seq'), 3, 'staging', 'collector','ROYAL BELGIAN INSTITUTE OF NATURAL SCIENCES') ;

select is(1 , (select min(fct_imp_checker_people(s.*)::int) from staging s));
select is(2, (select count(*)::int from staging_people where record_id = 3 and referenced_relation='staging'));
select is(1,(select order_by from staging_people where record_id = 3 and referenced_relation='staging' and people_ref = 2));
select is('taxon=>not_found'::hstore, (select status from staging where id = 3));

select '# ' || min(fct_imp_checker_manager(s.*)::integer) from staging s;

select diag('Test of Import');

insert into staging (id,import_ref,room) VALUES (6,1,'12');

update staging set gtu_code='My Gtu', gtu_ref=null where id = 4;
insert into staging_tag_groups (staging_ref,group_name, sub_group_name, tag_value)
  VALUES(4,'administrative area', 'populated place','Hello; world; ');
insert into staging_info (id,staging_ref, referenced_relation)
  VALUES(1,4,'gtu') ;
insert into comments(referenced_relation,record_id, notion_concerned, comment)
  VALUES('staging_info',1,'general','info') ;
update staging set to_import = true;

select is(0, (select count(*)::int from gtu));
select is(true, (select fct_importer_abcd(1)));

select is(1, (select count(*)::integer from specimens where gtu_ref = 1));
select is('Hello; world; ', (select tag_value from tag_groups where gtu_ref = 1));
select is ('info', (select comment from comments where referenced_relation='gtu' and record_id=1));
update staging set gtu_code='My Gtuz' , gtu_ref=null, taxon_name=null, taxon_level_name=null ,status='',to_import=true where id = 2;

select is(1 , (select min(fct_imp_checker_gtu(s.*)::int) from staging s));
select is(true, (select fct_importer_abcd(1)));
select is(NULL, (select gtu_ref from specimens where id=6));
update imports set is_finished=false where id=1 ;
insert into staging (id,import_ref,taxon_name,specimen_status) VALUES (7,1,'Falco Pérégrinuz','osef');
insert into staging (id,import_ref,taxon_name) VALUES (8,1,'Falco Pérégrinuz');
insert into staging_relationship (relationship_type, referenced_relation, record_id, staging_related_ref)
  VALUES('host', 'staging', 7, 8 ) ;
insert into collecting_methods (id,method) VALUES(1, 'all by mylself') ;
insert into staging_collecting_methods VALUES(1,8,1);

select is(1 , (select min(fct_imp_checker_manager(s.*)::int) from staging s));
select is(null , (select taxon_ref from staging s where id = 7));
select is(null , (select taxon_ref from staging s where id = 8));

UPDATE staging set taxon_ref = 30 where id = 7;

select is(30 , (select taxon_ref from staging s where id = 7));
select is(30 , (select taxon_ref from staging s where id = 8));
select is('Falco Peregrinus simpl' , (select taxon_name from staging s where id = 8));

UPDATE staging set expedition_name = 'brool' where id = 7;
UPDATE staging set expedition_name = 'brool' where id = 8;
insert into expeditions (id, name) VALUES (2, 'Antar');

update staging set expedition_ref = 2 where id = 8;
select is(2 , (select expedition_ref from staging s where id = 7));
select is(2 , (select expedition_ref from staging s where id = 8));

select is('Antar' , (select expedition_name from staging s where id = 7));
update staging set expedition_name=null, taxon_name=null, taxon_level_name=null ,status='',to_import=true where id = 7 ;
update staging set expedition_name=null, taxon_name=null, taxon_level_name=null ,status='',to_import=true where id = 8;

select is (1, (select collecting_method_ref from staging_collecting_methods WHERE staging_ref=8)) ;
select is(true, (select fct_importer_abcd(1)));
select is (NULL, (select collecting_method_ref from staging_collecting_methods WHERE staging_ref=8)) ;
select is (1, (SELECT cm.collecting_method_ref FROM specimen_collecting_methods cm WHERE cm.specimen_ref = 7)) ;
select is('osef', (select specimen_status from specimens where specimen_status='osef')) ;
select is('host', (select relationship_type from specimens_relationships)) ;

select diag('Test of Create Taxon');
select isnt(null, ( SELECT nextval('taxonomy_id_seq')));
select isnt(null, ( SELECT setval('taxonomy_id_seq',10000)));

--set client_min_messages=notice;
insert into staging (id,import_ref,taxon_name) VALUES (8,1,'Falco Pérégrinuz');

update staging set create_taxon = true, taxon_name='Falco longipennis longipennis Swainson, 1837', taxon_level_name='sub_species' ,
taxon_parents = 'species=> "Falco longipennis Swainson, 1837", genus=>"Falco Linnaeus, 1758",
sub_family=>"Falconinae", family=>"Falconidae", sub_order=>"Falcones", order=>"Falconiformes", class=>"Aves", phylum=>"Falco Phyl"'
  where id = 8;

select diag('ici');

select is(true, (select fct_imp_checker_manager(s.*) from staging s where s.id = 8));

select is(true , (select create_taxon from staging s where id = 8) );

select isnt( null , (select  taxon_ref from staging s where id = 8) );

delete from staging where id = 8;

insert into staging (id,import_ref,taxon_name) VALUES (8,1,'Falco Pérégrinuz');

update staging set create_taxon = true, taxon_name='Falco longipennis longipennis Swainson, 1837', taxon_level_name='sub_species' ,
taxon_parents = 'species=> "Falco longipennis Swainson, 1837", genus=>"Falco Linnaeus, 1758",
sub_family=>"Falconinae", family=>"Falconidae", sub_order=>"Falcones", order=>"Falconiformes", class=>"Aves", phylum=>"Falco Phylum"'
  where id = 8;

select is(true, (select fct_imp_checker_manager(s.*) from staging s where s.id = 8));
select is(false , (select  create_taxon from staging s where id = 8) );
select is( null , (select  taxon_ref from staging s where id = 8) );


delete from staging where id = 8;
insert into staging (id,import_ref,taxon_name) VALUES (9,1,'Falco Pérégrinuz');

update staging set create_taxon = true, taxon_name='Falco Testinus', taxon_level_name='sub_species' ,
taxon_parents = '"genus"=>"Falco Linnaeus, 1758", "family"=>"Falco Fam", "species"=>"Falco longipennis Swainson, 1837", "sub_family"=>"Falconinae", phylum=>"Falco Phyl"'
where id = 9;


select is(true, (select fct_imp_checker_manager(s.*) from staging s where s.id = 9));
select is(true , (select  create_taxon from staging s where id = 9) );
select is( null , (select  taxon_ref from staging s where id = 9) );
select is( 'taxon=>"bad_hierarchy"'::hstore , (select  status from staging s where id = 9) );

update staging set create_taxon = true, taxon_name='Falco Testinus', taxon_level_name='sub_species' ,
taxon_parents = '"genus"=>"Falco Linnaeus, 1758", "family"=>"Falconidae", "species"=>"Falco longipennis Swainson, 1837", "sub_family"=>"Falconinae", phylum=>"Falco Phyl"'
where id = 9;


select is(true, (select fct_imp_checker_manager(s.*) from staging s where s.id = 9));
select is(true , (select  create_taxon from staging s where id = 9) );
select isnt( null , (select  taxon_ref from staging s where id = 9) );
select is(''::hstore , (select  status from staging s where id = 9) );

--select t.id, name, parent_Ref, level_sys_name  from taxonomy t inner join catalogue_levels c on t.level_ref = c.id;
--select id, create_taxon, taxon_ref, taxon_name, taxon_parents , status from staging where id = 9;
SELECT * FROM finish();


ROLLBACK;


