# natural_heritage_darwin

This is the repository of the Darwin CMS for naturalsciences collections, hosted at the RBINS.

This version has been developped within the framework of the BELSPO project NaturalHeritage (2017-2019).

It runs on PostgreSQL 9.6 and a forked version of the Symfony 1.4 framework (Lexpress), which is compliant with PHP 7+. Sources of this framework are embedded in the vendor folder.

**Changes related to lexpress are :**
  - method **SaveEmbeddedForms** is replaced by **SaveObjectEmbeddedForms** in forms (different signature but same content). https://github.com/FriendsOfSymfony1/symfony1/issues/103
  - intialisation of the project in **./config/ProjectConfiguration.class.php** is different (method **configureDoctrine** is replaced by **configureDoctrineEvent**) : https://github.com/FriendsOfSymfony1/symfony1/issues/42
  - the "classic" PHP libraries are required (pdo, xml, xsl, json, ...), but please install **php-apcu** and **php-apcu-bc** (backward compatiability with the apc API) for the Symfony cache
  
      
