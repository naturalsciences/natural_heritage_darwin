# Natural Heritage Darwin :

This is the repository of the Darwin CMS for natural sciences collections, hosted at the RBINS.

This version has been developped within the framework of the BELSPO project NaturalHeritage (2017-2019).

It runs on PostgreSQL 9.6 and a forked version of the Symfony 1.4 framework (Lexpress : https://github.com/FriendsOfSymfony1/symfony1), which is compliant with PHP 7+. Sources of this framework are embedded in the vendor folder.

**Most important changes :**
  - Possibility to import taxa, gtu, lithostratigraphical classification and specimens by Tab-delimited files (no XML anymore, but the code should remain fucntional). Position of columns, case of columns name are optional. This template is linked to a mechanism that can attribute collection number
  - Parallel taxonomies
  - Creation of JSON APIs (see below)
  
 **JSON APIs**
   - List : 
      - List all collections : https://darwin.naturalsciences.be/public_dev.php/json/get_collections_catalogue
      
      - Describe  a specific collection : https://darwin.naturalsciences.be/public_dev.php/json/get_collections_catalogue?id={id}
          - e.g. : https://darwin.naturalsciences.be/public_dev.php/json/get_collections_catalogue?id=4
        Or  : https://darwin.naturalsciences.be/public_dev.php/json/get_collections_catalogue?code={code}
        
          - e.g : https://darwin.naturalsciences.be/public_dev.php/json/get_collections_catalogue?code=Rhopa
          
      - Detailed view on a collection: 
https://darwin.naturalsciences.be/public_dev.php/json/Get_collection_detail?id={id} 
        Or https://darwin.naturalsciences.be/public_dev.php/json/Get_collection_detail?code={code}
           - e.g. : https://darwin.naturalsciences.be/public_dev.php/json/Get_collection_detail?code=Rhopa
           
      - Check taxon existence :https://darwin.naturalsciences.be/public_dev.php/search/getTaxon?taxon-name={taxon-name}&taxon-level={taxon-level}
           - e.g:  https://darwin.naturalsciences.be/public_dev.php/search/getTaxon?taxon-name=Tilapia
           
      - check taxon hierarchy (GBIF style): https://darwin.naturalsciences.be/public_dev.php/search/checkTaxonHierarchy?taxon-name={taxon_name)&canonical={true|false}
           - e.g:https://darwin.naturalsciences.be/public_dev.php/search/checkTaxonHierarchy?taxon-name=Tilapia%20Test&canonical=false
      - Browse specimens inside of a collection: https://darwin.naturalsciences.be/public_dev.php/json/getcollectionjson?collection={collection_code}
           - e.g. https://darwin.naturalsciences.be/public_dev.php/json/getcollectionjson?collection=TEST_DEVELOPPERS (NOTE: doens't go currently in sub-collections)
      - JSON representation of a specimen  : https://darwin.naturalsciences.be/public.php/json/getjson?id={id}
           - e.g.: https://darwin.naturalsciences.be/public.php/json/getjson?id=923000

**Data model :**
  - The PostgreSQL data model is different from the original one of Darwin. It now features a "temporal_information" table between the specimen and the gtu, for chronological information. The sql code contains several fucntion to mirgate from the old data model to the current one in the "public" schema. This migration procedures use the PostgreSQL "ForeignDataWrappers"
  

**Changes related to lexpress are :**
  - method **SaveEmbeddedForms** is replaced by **SaveObjectEmbeddedForms** in forms (different signature but same content). https://github.com/FriendsOfSymfony1/symfony1/issues/103
  - initialisation of the project in **./config/ProjectConfiguration.class.php** is different. The **configureDoctrine** method is replaced by **configureDoctrineEvent**) : https://github.com/FriendsOfSymfony1/symfony1/issues/42
  - the "classic" PHP libraries are required (pdo, xml, xsl, json, ...), but please install **php-apcu** and **php-apcu-bc** (backward compatiability with the old apc API) for the Symfony cache
  

      
