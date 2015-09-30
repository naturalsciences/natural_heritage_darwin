\set ECHO all
\i unit_launch.sql
SELECT plan(25);

select diag('Test of taxonomy import');
select diag('-- First mimic of xml file with creation of a basic taxonomical structure --');

insert into imports (id, user_ref, format, filename, collection_ref) values (1,1,'taxon','taxon_test.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref) values (1,1,'Eucaryota',1);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (2,1,'Animalia',2,1);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (3,1,'Chordata',4,2);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (4,1,'Vertebrata',5,3);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (5,1,'Gnathostomata',11,4);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (6,1,'Actinopterygii',12,5);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (7,1,'Neopterygii',13,6);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (8,1,'Acanthopterygii',27,7);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (9,1,'Mugiliformes',28,8);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (10,1,'Mugilidae',34,9);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (11,1,'Liza',41,10);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (12,1,'Liza officinalis',48,11);

select is(true ,
          (select fct_importer_catalogue(1,'taxonomy')),
          'Perform the import of staging catalogue entries'
         );
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   order by id',
                  $$
                    VALUES (1,1,'Eucaryota',1,0,1),
                           (2,1,'Animalia',2,1,5),
                           (3,1,'Chordata',4,2,6),
                           (4,1,'Vertebrata',5,3,7),
                           (5,1,'Gnathostomata',11,4,8),
                           (6,1,'Actinopterygii',12,5,9),
                           (7,1,'Neopterygii',13,6,10),
                           (8,1,'Acanthopterygii',27,7,11),
                           (9,1,'Mugiliformes',28,8,12),
                           (10,1,'Mugilidae',34,9,13),
                           (11,1,'Liza',41,10,14),
                           (12,1,'Liza officinalis',48,11,15)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select diag('
-- Second mimic of xml file with creation of a branched taxonomical structure: Lizamontidae family and bellow --');

insert into imports (id, user_ref, format, filename, collection_ref) values (2,1,'taxon','taxon_test_2.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (13,2,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (14,2,'Lizamontidae',34,13,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (15,2,'Lizamontia',41,14,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (16,2,'Lizamontia barbecue',48,15,NULL);

select is(true ,
          (select fct_importer_catalogue(2,'taxonomy')),
          'Perform the import of staging catalogue entries'
);
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 2
                   order by id',
                  $$
                    VALUES (13,2,'Vertebrata',5,0,7),
                           (14,2,'Lizamontidae',34,13,16),
                           (15,2,'Lizamontia',41,14,17),
                           (16,2,'Lizamontia barbecue',48,15,18)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (16,18)
                   order by id',
                  $$
                    VALUES ('Lizamontidae'),
                           ('Lizamontia barbecue')
                  $$,
                  'Test the values were well inserted in the taxonomy table telling everything is ok');

select diag('-- Third mimic of xml file with modification of existing taxonomical entries --');

insert into imports (id, user_ref, format, filename, collection_ref) values (3,1,'taxon','taxon_test_3.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (17,3,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (18,3,'Lizamontidae Duchesne,2015',34,17,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (19,3,'Lizamontia',41,18,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (20,3,'Lizamontia barbecue Duchesne,2015',48,19,NULL);

select is(true ,
          (select fct_importer_catalogue(3,'taxonomy')),
          'Perform the import of staging catalogue entries'
);
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 3
                   order by id',
                  $$
                    VALUES (17,3,'Vertebrata',5,0,7),
                           (18,3,'Lizamontidae Duchesne,2015',34,17,16),
                           (19,3,'Lizamontia',41,18,17),
                           (20,3,'Lizamontia barbecue Duchesne,2015',48,19,18)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (16,18)
                   order by id',
                  $$
                    VALUES ('Lizamontidae Duchesne,2015'),
                           ('Lizamontia barbecue Duchesne,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select diag('-- Fourth mimic of xml file with same info than the second mimic: should keep the existing entries modified in the third serie of tests --');

insert into imports (id, user_ref, format, filename, collection_ref) values (4,1,'taxon','taxon_test_4.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (21,4,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (22,4,'Lizamontidae',34,21,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (23,4,'Lizamontia',41,22,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (24,4,'Lizamontia barbecue',48,23,NULL);

select is(true ,
          (select fct_importer_catalogue(4,'taxonomy')),
          'Perform the import of staging catalogue entries'
);
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 4
                   order by id',
                  $$
                    VALUES (21,4,'Vertebrata',5,0,7),
                           (22,4,'Lizamontidae',34,21,16),
                           (23,4,'Lizamontia',41,22,17),
                           (24,4,'Lizamontia barbecue',48,23,18)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (16,18)
                   order by id',
                  $$
                    VALUES ('Lizamontidae Duchesne,2015'),
                           ('Lizamontia barbecue Duchesne,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select is(0, (select count(*)::integer from taxonomy where id > 18), 'Check that no taxa inserted since second test');

select diag('-- Fifth mimic of xml file with a perfect match for species: a new species is introduced ("Lizamontia barbecue" without author) and we try to reimport the same file as previous --');

insert into imports (id, user_ref, format, filename, collection_ref) values (5,1,'taxon','taxon_test_5.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (19,'Lizamontia barbecue',48,17);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (25,5,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (26,5,'Lizamontidae',34,25,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (27,5,'Lizamontia',41,26,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (28,5,'Lizamontia barbecue',48,27,NULL);

select is(true ,
          (select fct_importer_catalogue(5,'taxonomy')),
          'Perform the import of staging catalogue entries'
);
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 5
                   order by id',
                  $$
                    VALUES (25,5,'Vertebrata',5,0,7),
                           (26,5,'Lizamontidae',34,25,16),
                           (27,5,'Lizamontia',41,26,17),
                           (28,5,'Lizamontia barbecue',48,27,19)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select diag('-- Sixth mimic of xml file with a foreseen fail: a new species is introduced ("Lizamontia barbecue Duchesne,1976") and we try to reimport the same file as previous -
             It cannot guess to associate the record to Lizamontia barbecue Duchesne,2015 or to Lizamontia barbecue Duchesne,1976 --');

insert into imports (id, user_ref, format, filename, collection_ref) values (6,1,'taxon','taxon_test_6.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (20,'Lizamontia barbecue Duchesne,1976',48,17);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (29,6,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (30,6,'Lizamontidae',34,29,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (31,6,'Lizamontia',41,30,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (32,6,'Lizamontia barbecue Duchesne',48,31,NULL);

select throws_ok('select fct_importer_catalogue(6,''taxonomy'')','Could not import this file, Lizamontia barbecue Duchesne exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree');

select diag('-- Seventh mimic of xml file with a foreseen fail: A new entry at the top of three is mispelled and cannot therefore be found and associated nor created --');

insert into imports (id, user_ref, format, filename, collection_ref) values (7,1,'taxon','taxon_test_7.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (33,7,'Vertebrota',5,NULL,NULL);

select throws_ok('select fct_importer_catalogue(7,''taxonomy'')','Could not import this file, Vertebrota (level sub phylum) does not exist in DaRWIN and cannot be attached, correct your file or create this taxonomy manually');

select diag('-- Eighth mimic of xml file with a foreseen fail: A new Mugilidae family entry has been created elsewhere and linked to Animalia directly and we try to associate a Mugilidae but we do not know which one to choose --');

insert into imports (id, user_ref, format, filename, collection_ref) values (8,1,'taxon','taxon_test_8.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (21,'Mugilidae',34,9);
alter sequence taxonomy_id_seq restart with 22;
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (34,8,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (35,8,'Mugilidae',34,34,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (36,8,'Mugilix',41,35,NULL);

select throws_ok('select fct_importer_catalogue(8,''taxonomy'')','Could not import this file, Mugilidae exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree');

select diag('-- Nineth mimic of xml file with a correction of existing Darwin 2 name that should not be applied: a new species is introduced ("Lizamontia barbecue Emery");
                as the Lizamontia barbecue entry is not alone--');

insert into imports (id, user_ref, format, filename, collection_ref) values (9,1,'taxon','taxon_test_9.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (37,9,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (38,9,'Lizamontidae',34,37,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (39,9,'Lizamontia',41,38,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (40,9,'Lizamontia barbecue Emery,2015',48,39,NULL);

select is(true ,
          (select fct_importer_catalogue(9,'taxonomy')),
          'Perform the import of staging catalogue entries'
);
select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 9
                     and id::integer = 40',
                  $$
                    VALUES (40,9,'Lizamontia barbecue Emery,2015',48,39,22)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');
select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (19,22)
                   order by id',
                  $$
                    VALUES ('Lizamontia barbecue'),
                           ('Lizamontia barbecue Emery,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select diag('-- Tenth mimic of xml file taxonomy import: Test the case insensitivity--');

insert into imports (id, user_ref, format, filename, collection_ref) values (10,1,'taxon','taxon_test_10.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (41,10,'vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (42,10,'lizamontidae',34,41,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (43,10,'lizamontia',41,42,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (44,10,'lizaMonTia bArBecue eMery,2015',48,43,NULL);

select is(true ,
          (select fct_importer_catalogue(10,'taxonomy')),
          'Perform the import of staging catalogue entries'
);

select results_eq ('select max(id)::integer
                   from taxonomy
                  ',
                  $$
                    VALUES (22)
                  $$,
                  'Test no entry has been created in taxonomy table'
                 );

select results_eq('select id::integer,
                          import_ref::integer,
                          name::text,
                          level_ref::integer,
                          (coalesce(parent_ref,0))::integer,
                          catalogue_ref::integer
                   from staging_catalogue
                   where import_ref::integer = 10
                     and id::integer = 44',
                  $$
                    VALUES (44,10,'lizaMonTia bArBecue eMery,2015',48,43,22)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select diag('-- Eleven mimic of xml file with an adaptation to check if succeed with the second Mugilidae set to invalid - By default will fail because option do not exclude the invalid of the search - Try twice with the two options --');

insert into imports (id, user_ref, format, filename, collection_ref) values (11,1,'taxon','taxon_test_11.xml',NULL);
update taxonomy set status = 'invalid' where id = 21;
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (45,11,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (46,11,'Mugilidae',34,34,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (47,11,'Mugilix',41,35,NULL);

select throws_ok('select fct_importer_catalogue(11,''taxonomy'')','Could not import this file, Mugilidae exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree');

update imports set state = 'loaded' where id = 11;

select is(true ,
          (select fct_importer_catalogue(11,'taxonomy', true)),
          'Perform the import of staging catalogue entries'
);

SELECT * FROM finish();

ROLLBACK;
