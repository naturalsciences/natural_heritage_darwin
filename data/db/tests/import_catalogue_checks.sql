\set ECHO all
\i unit_launch.sql
SELECT plan(35);

select diag('Test of taxonomy import');
select diag('-- First mimic of xml file with creation of a basic taxonomical structure --');

/* insert the import line */
insert into imports (id, user_ref, format, filename, collection_ref) values (1,1,'taxon','taxon_test.xml',NULL);
/* insert the staging_catalogue lines */
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
insert into staging_catalogue (id, import_ref, name, level_ref) values (13,1,'Animalia',2);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (14,1,'Chordata',4,13);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (15,1,'Vertebrata',5,14);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (16,1,'Mugilix',41,15);
/* insert keywords for some of them */
insert into classification_keywords (referenced_relation, record_id, keyword_type, keyword)
    values
      ('staging_catalogue',1,'GenusOrMonomial','Eucaryota'),
      ('staging_catalogue',2,'GenusOrMonomial','Animalia'),
      ('staging_catalogue',4,'GenusOrMonomial','Vertebrata'),
      ('staging_catalogue',12,'GenusOrMonomial','Liza'),
      ('staging_catalogue',12,'SpeciesEpithet','officinalis')
;
/*
select diag('List of staging catalogue before clean');
select diag((select array_to_string(array_agg(id || '-' || name || '-' || level_ref || '-' || coalesce(parent_ref,0)),E'\n')
             from staging_catalogue));

*/

select is(true,
  (select fct_clean_staging_catalogue(1)),
  'Perform cleaning of surnumerary entries - done @load import'
);

select is(
  1::BIGINT,
  (select count(id) from staging_catalogue where name = 'Vertebrata'),
  'Check there is only one Vertebrata left...'
);

select is(
    4,
    (select id from staging_catalogue where name = 'Vertebrata'),
    '... and that is well the id 4'
);

select is(
    5::BIGINT,
    (select count(id) from classification_keywords where referenced_relation = 'staging_catalogue'),
    'Number of keywords is well still of 4 after clean up'
);

/*
select diag('List of staging catalogue after clean and before import');
select diag((select array_to_string(array_agg(id || '-' || name || '-' || level_ref || '-' || coalesce(parent_ref,0)),E'\n')
             from staging_catalogue));

select diag('List of taxa before import');
select diag((select array_to_string(array_agg(id || '-' || name || '-' || level_ref || '-' || coalesce(parent_ref,0)),E'\n')
             from taxonomy));

*/
select is(true ,
          (select fct_importer_catalogue(1,'taxonomy')),
          'Perform the import of staging catalogue entries'
         );

/*
select diag('List of staging catalogue after clean and after import');
select diag((select array_to_string(array_agg(id || '-' || name || '-' || level_ref || '-' || coalesce(parent_ref,0)),E'\n')
             from staging_catalogue));

select diag('List of taxa imported');
select diag((select array_to_string(array_agg(id || '-' || name || '-' || level_ref || '-' || coalesce(parent_ref,0)),E'\n')
            from taxonomy));
*/

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
                           (3,1,'Chordata',4,5,6),
                           (4,1,'Vertebrata',5,6,7),
                           (5,1,'Gnathostomata',11,7,8),
                           (6,1,'Actinopterygii',12,8,9),
                           (7,1,'Neopterygii',13,9,10),
                           (8,1,'Acanthopterygii',27,10,11),
                           (9,1,'Mugiliformes',28,11,12),
                           (10,1,'Mugilidae',34,12,13),
                           (11,1,'Liza',41,13,14),
                           (12,1,'Liza officinalis',48,14,16),
                           (16,1,'Mugilix',41,7,15)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select is(
    1::BIGINT,
    (select count(id) from classification_keywords where referenced_relation = 'staging_catalogue'),
    'Number of keywords is now of 1 for the ones associated with staging_catalogue table: Eucaryota keyword was already set an stay associated with staging_catalogue then...'
);

select is(
    8::BIGINT,
    (select count(id) from classification_keywords where referenced_relation = 'taxonomy'),
    '... and of 8 for the ones associated with taxonomy table (4 for top levels and 4 for the new ones'
);

-- select diag(referenced_relation || ' ' || record_id::text || ' ' || keyword_type || ' ' || keyword || ' ' || keyword_indexed) from classification_keywords where referenced_relation = 'taxonomy';

select results_eq('select record_id::integer
                   from classification_keywords
                   where referenced_relation = ''taxonomy''
                     and record_id > 4
                   order by record_id',
                  $$
                    VALUES (5),
                           (7),
                           (16),
                           (16)
                  $$,
                  'Test the values were well reassociated with the taxon id in classification_keywords');

select diag('
-- Second mimic of xml file with creation of a branched taxonomical structure: Lizamontidae family and bellow --');

insert into imports (id, user_ref, format, filename, collection_ref) values (2,1,'taxon','taxon_test_2.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (17,2,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (18,2,'Lizamontidae',34,17,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (19,2,'Lizamontia',41,18,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (20,2,'Lizamontia barbecue',48,19,NULL);

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
                    VALUES (17,2,'Vertebrata',5,0,7),
                           (18,2,'Lizamontidae',34,7,17),
                           (19,2,'Lizamontia',41,17,18),
                           (20,2,'Lizamontia barbecue',48,18,19)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');


select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (17,19)
                   order by id',
                  $$
                    VALUES ('Lizamontidae'),
                           ('Lizamontia barbecue')
                  $$,
                  'Test the values were well inserted in the taxonomy table telling everything is ok');

select diag('-- Third mimic of xml file with modification of existing taxonomical entries --');

insert into imports (id, user_ref, format, filename, collection_ref) values (3,1,'taxon','taxon_test_3.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (21,3,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (22,3,'Lizamontidae Duchesne,2015',34,21,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (23,3,'Lizamontia',41,22,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (24,3,'Lizamontia barbecue Duchesne,2015',48,23,NULL);

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
                    VALUES (21,3,'Vertebrata',5,0,7),
                           (22,3,'Lizamontidae Duchesne,2015',34,7,17),
                           (23,3,'Lizamontia',41,17,18),
                           (24,3,'Lizamontia barbecue Duchesne,2015',48,18,19)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (17,19)
                   order by id',
                  $$
                    VALUES ('Lizamontidae Duchesne,2015'),
                           ('Lizamontia barbecue Duchesne,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select diag('-- Fourth mimic of xml file with same info than the second mimic: should keep the existing entries modified in the third serie of tests --');

insert into imports (id, user_ref, format, filename, collection_ref) values (4,1,'taxon','taxon_test_4.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (25,4,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (26,4,'Lizamontidae',34,25,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (27,4,'Lizamontia',41,26,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (28,4,'Lizamontia barbecue',48,27,NULL);

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
                    VALUES (25,4,'Vertebrata',5,0,7),
                           (26,4,'Lizamontidae',34,7,17),
                           (27,4,'Lizamontia',41,17,18),
                           (28,4,'Lizamontia barbecue',48,18,19)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (17,19)
                   order by id',
                  $$
                    VALUES ('Lizamontidae Duchesne,2015'),
                           ('Lizamontia barbecue Duchesne,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select is(0, (select count(*)::integer from taxonomy where id > 19), 'Check that no taxa inserted since second test');

select diag('-- Fifth mimic of xml file with a perfect match for species: a new species is introduced ("Lizamontia barbecue" without author) and we try to reimport the same file as previous --');

insert into imports (id, user_ref, format, filename, collection_ref) values (5,1,'taxon','taxon_test_5.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (20,'Lizamontia barbecue',48,18);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (29,5,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (30,5,'Lizamontidae',34,29,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (31,5,'Lizamontia',41,30,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (32,5,'Lizamontia barbecue',48,31,NULL);

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
                    VALUES (29,5,'Vertebrata',5,0,7),
                           (30,5,'Lizamontidae',34,7,17),
                           (31,5,'Lizamontia',41,17,18),
                           (32,5,'Lizamontia barbecue',48,18,20)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select diag('-- Sixth mimic of xml file with a foreseen fail: a new species is introduced ("Lizamontia barbecue Duchesne,1976") and we try to reimport the same file as previous -
             It cannot guess to associate the record to Lizamontia barbecue Duchesne,2015 or to Lizamontia barbecue Duchesne,1976 --');

insert into imports (id, user_ref, format, filename, collection_ref) values (6,1,'taxon','taxon_test_6.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (21,'Lizamontia barbecue Duchesne,1976',48,18);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (33,6,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (34,6,'Lizamontidae',34,33,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (35,6,'Lizamontia',41,34,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (36,6,'Lizamontia barbecue Duchesne',48,35,NULL);

select throws_ok('select fct_importer_catalogue(6,''taxonomy'')',E'Case 3, Could not import this file, Lizamontia barbecue Duchesne exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: 36');

select diag('-- Seventh mimic of xml file with a foreseen fail: A new entry at the top of three is mispelled and cannot therefore be found and associated nor created --');

insert into imports (id, user_ref, format, filename, collection_ref) values (7,1,'taxon','taxon_test_7.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (37,7,'Vertebrota',5,NULL,NULL);

select throws_ok('select fct_importer_catalogue(7,''taxonomy'')','Could not import this file, Vertebrota (level sub phylum) does not exist in DaRWIN and cannot be attached, correct your file or create this taxonomy manually');

select diag('-- Eighth mimic of xml file with a foreseen fail: A new Mugilidae family entry has been created elsewhere and linked to Animalia directly and we try to associate a Mugilidae but we do not know which one to choose --');

insert into imports (id, user_ref, format, filename, collection_ref) values (8,1,'taxon','taxon_test_8.xml',NULL);
insert into taxonomy (id,name,level_ref,parent_ref) values (22,'Mugilidae',34,9);
alter sequence taxonomy_id_seq restart with 23;
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (38,8,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (39,8,'Mugilidae',34,38,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (40,8,'Mugilix',41,39,NULL);

select throws_ok('select fct_importer_catalogue(8,''taxonomy'')',E'Case 2, Could not import this file, Mugilidae exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: 39');

select diag('-- Nineth mimic of xml file with a correction of existing Darwin 2 name that should not be applied: a new species is introduced ("Lizamontia barbecue Emery");
                as the Lizamontia barbecue entry is not alone--');

insert into imports (id, user_ref, format, filename, collection_ref) values (9,1,'taxon','taxon_test_9.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (41,9,'Vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (42,9,'Lizamontidae',34,41,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (43,9,'Lizamontia',41,42,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (44,9,'Lizamontia barbecue Emery,2015',48,43,NULL);

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
                     and id::integer = 44',
                  $$
                    VALUES (44,9,'Lizamontia barbecue Emery,2015',48,18,23)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');
select results_eq('select name::text
                   from taxonomy
                   where id::integer IN (19,20,23)
                   order by id',
                  $$
                    VALUES ('Lizamontia barbecue Duchesne,2015'),
                           ('Lizamontia barbecue'),
                           ('Lizamontia barbecue Emery,2015')
                  $$,
                  'Test the values were well updated in the taxonomy table telling everything is ok');

select diag('-- Tenth mimic of xml file taxonomy import: Test the case insensitivity--');

insert into imports (id, user_ref, format, filename, collection_ref) values (10,1,'taxon','taxon_test_10.xml',NULL);

insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (45,10,'vertebrata',5,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (46,10,'lizamontidae',34,45,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (47,10,'lizamontia',41,46,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (48,10,'lizaMonTia bArBecue eMery,2015',48,47,NULL);

select is(true ,
          (select fct_importer_catalogue(10,'taxonomy')),
          'Perform the import of staging catalogue entries'
);

select results_eq ('select max(id)::integer
                   from taxonomy
                  ',
                  $$
                    VALUES (23)
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
                     and id::integer = 48',
                  $$
                    VALUES (48,10,'lizaMonTia bArBecue eMery,2015',48,18,23)
                  $$,
                  'Test the values were well set in the import table telling everything is ok');

select diag('-- Eleven mimic of xml file with an adaptation to check if succeed with the second Mugilidae set to invalid - By default will fail because option do not exclude the invalid of the search - Try twice with the two options --');

insert into imports (id, user_ref, format, filename, collection_ref) values (11,1,'taxon','taxon_test_11.xml',NULL);
update taxonomy set status = 'invalid' where id = 13;
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (49,11,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (50,11,'Mugilidae',34,49,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (51,11,'Mugilix',41,50,NULL);

select throws_ok('select fct_importer_catalogue(11,''taxonomy'')',E'Case 2, Could not import this file, Mugilidae exists more than 1 time in DaRWIN, correct the catalogue (or file) to import this tree.\nStaging Catalogue Line: 50');

update imports set state = 'loaded' where id = 11;

select is(true ,
          (select fct_importer_catalogue(11,'taxonomy', true)),
          'Perform the import of staging catalogue entries'
);

insert into imports (id, user_ref, format, filename, collection_ref) values (12,1,'taxon','taxon_test_12.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (52,12,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (53,12,'? #"\%',34,52,NULL);

select throws_ok('select fct_importer_catalogue(12,''taxonomy'')',E'Case 0, Could not import this file, ? #"\\% is not a valid name.\nStaging Catalogue Line: 53');


select diag('-- Thirteen mimic of xml file with something that should succeed: A new Mugilidae family entry to be connected with  --');

insert into imports (id, user_ref, format, filename, collection_ref) values (13,1,'taxon','taxon_test_13.xml',NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (54,13,'Animalia',2,NULL,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (55,13,'Mugiliformidae',33,54,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (56,13,'Mugilidae',34,55,NULL);
insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref, catalogue_ref) values (57,13,'Mugilix',41,56,NULL);

select is(true ,
          (select fct_importer_catalogue(11,'taxonomy')),
          'Perform the import of staging catalogue entries'
);

delete from imports where id = 1;

select is(
    0::BIGINT,
    (select count(id) from classification_keywords where referenced_relation = 'staging_catalogue'),
    'Number of keywords is now 0 due to cascade delete'
);

SELECT * FROM finish();

ROLLBACK;
