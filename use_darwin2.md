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
 2. a second one with the "***darwin2***" user, to manipulate data in Darwin


