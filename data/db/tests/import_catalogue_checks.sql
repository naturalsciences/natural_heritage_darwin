\unset ECHO
\i unit_launch.sql
SELECT plan(2);

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

-- insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (11,1,'Lizamontidae',34,4);
-- insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (13,1,'Lizamontia',41,11);
-- insert into staging_catalogue (id, import_ref, name, level_ref, parent_ref) values (15,1,'Liza propolis',48,13);


SELECT * FROM finish();


ROLLBACK;
