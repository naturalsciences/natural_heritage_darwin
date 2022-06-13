
1 Note on the general architecture
------------------------

Darwin uses  an intermediate architecture between a fully normalized database and a data warehouse.
Data from auxiliary tables (GTU for localities, taxonomies) are copied into the main "specimens" table to speed up search queries on large collections.
The database schema of Darwin features a lot of trigger functions to synchronize both.
Some vocabularies (e.g measurement units like *ft.* and *m.*, list of taxonomic types such as *paratype*, *allotypes etc...* ) are dynamic and semi-controlled.  A list of keyword is kept in a table, used in dropdown or autocomplete, but can be be extended from within the interface. Triggers and Pl/PgSQL functions are used to create and clean these lists.
Text information is also stored both in the originam form and in simplified form (lowercase, removal of diacritics, and optionally speed) via the fulltoindex function to allow fuzzy matching.

2 Direct database connection
------------------------


Triggers and Pl/PgSQL are located in the "**darwin2**" schema. If you need to manipulate data directly from PostgreSQL, you should connect to Darwin via the "***darwin2*** user account rather than the general "***postgres***" (i.e. root) user account of PostgreSQL that looks for functions, tables and triggers only in the "***public***" schema (unless they are prefixed with the schema in the source code).
Therefore, when configuring PgAdmin, you should declare 2 connections to the Darwin server

 1. one with the embedded "***postgres***" account, for backup and technical maintenance tasks on the whole database
 2. a second one with the "***darwin2***" user, to access and modidy data in Darwin directly from PostgreSQL

3 Important Symfony commands
-----------------------------

Need to be run from the base folder (e.g. **/var/www/html/darwin**)

 1. To regenerate the Doctrine (object-oriented PHP counterpart of the database model) defined in **./config/doctrine/schema.yml**

        sudo php symfony doctrine:build-model

and then

         sudo php symfony doctrine:build-forms

and then

        sudo php symfony doctrine:build-filters

 -  clear the cache, otherwise code changes are only visible via the debug interface ./backend_dev.php

       sudo php symfony cc 

 - Darwin tasks in **/dawin/lib/task** folder
Most of them are covered by the backend web interface to import data

		 - load tab-delimited files
			  sudo php symfony darwin:load-import
         - quality check (edtect duplicates, taxonomic conflicts and missing required fields)
           sudo php symfony darwin:check-import --id [ID of import in imports table] 
         - create validated records
            sudo php symfony darwin:check-import --id [ID of import in imports table] --do-import
         - check batch import of localities
           sudo php symfony darwin:import-gtu  --id [ID of import in imports table] 