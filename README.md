# natural_heritage_darwin

This is the repository of the Darwin CMS for natural sciences collections, hosted at the RBINS.

This version has been developped within the framework of the BELSPO project NaturalHeritage (2017-2019).

It runs on PostgreSQL 9.6 and a forked version of the Symfony 1.4 framework (Lexpress : https://github.com/FriendsOfSymfony1/symfony1), which is compliant with PHP 7+. Sources of this framework are embedded in the vendor folder.

**Most important changes**
   -Possibility to import taxa, gtu, lithostratigraphical classification and specimens by Tab-delimited files (no XML anymore, but the code should remain fucntional). Position of columns, case of columns name are optional. This template is linked to a mechanism that can attribute collection number
   -Parallel taxonomies
   -More JSON API



**Data model**
the PostgreSQL data model is different from the original one of Darwin. It now features a "temporal_information" table between the specimen and the gtu, for chronological information. The sql code contains several fucntion to mirgate from the old data model to the current one in the "public" schema. This migration procedures use the PostgreSQL "ForeignDataWrappers"
  

**Changes related to lexpress are :**
  - method **SaveEmbeddedForms** is replaced by **SaveObjectEmbeddedForms** in forms (different signature but same content). https://github.com/FriendsOfSymfony1/symfony1/issues/103
  - initialisation of the project in **./config/ProjectConfiguration.class.php** is different. The **configureDoctrine** method is replaced by **configureDoctrineEvent**) : https://github.com/FriendsOfSymfony1/symfony1/issues/42
  - the "classic" PHP libraries are required (pdo, xml, xsl, json, ...), but please install **php-apcu** and **php-apcu-bc** (backward compatiability with the old apc API) for the Symfony cache
  

      
