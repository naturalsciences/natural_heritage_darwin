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
      - List all collections : https://darwin.naturalsciences.be/public.php/json/get_collections_catalogue
      
      - Describe  a specific collection : https://darwin.naturalsciences.be/public.php/json/get_collections_catalogue?id={id}
          - e.g. : https://darwin.naturalsciences.be/public.php/json/get_collections_catalogue?id=4
        Or  : https://darwin.naturalsciences.be/public.php/json/get_collections_catalogue?code={code}
        
          - e.g : https://darwin.naturalsciences.be/public.php/json/get_collections_catalogue?code=Rhopa
          
      - Detailed view on a collection: 
https://darwin.naturalsciences.be/public.php/json/Get_collection_detail?id={id} 
        Or https://darwin.naturalsciences.be/public.php/json/Get_collection_detail?code={code}
           - e.g. : https://darwin.naturalsciences.be/public.php/json/Get_collection_detail?code=Rhopa
           
      - Check taxon existence :https://darwin.naturalsciences.be/public.php/search/getTaxon?taxon-name={taxon-name}&taxon-level={taxon-level}
           - e.g:  https://darwin.naturalsciences.be/public.php/search/getTaxon?taxon-name=Tilapia
           
      - check taxon hierarchy (GBIF style): https://darwin.naturalsciences.be/public.php/search/checkTaxonHierarchy?taxon-name={taxon_name)&canonical={true|false}
           - e.g:https://darwin.naturalsciences.be/public.php/search/checkTaxonHierarchy?taxon-name=Tilapia%20Test&canonical=false
      - Browse specimens inside of a collection: https://darwin.naturalsciences.be/public.php/json/getcollectionjson?collection={collection_code}
           - e.g. https://darwin.naturalsciences.be/public.php/json/getcollectionjson?collection=TESTELOPPERS (NOTE: doesn't go currently in sub-collections)
      - JSON representation of a specimen  : https://darwin.naturalsciences.be/public.php/json/getjson?id={id}
           - e.g.: https://darwin.naturalsciences.be/public.php/json/getjson?id=923000
		   
      - Backend : Identifiers for people
           - Raw : https://darwin.naturalsciences.be/backend.php/people?identifier_protocol=ORCID&identifier_value=0000-0002-4048-7728 
           - View people in JSON : https://darwin.naturalsciences.be/backend.php/people?identifier_protocol=ORCID&identifier_value=0000-0002-4048-7728&format=application/json 

      - Backend in View institution 
	       - Raw : https://darwin.naturalsciences.be/backend.php/institution?identifier_protocol=GRID&identifier_value=grid.20478.39 
           - Biew institution in JSON : https://darwin.naturalsciences.be/backend.php/institution?identifier_protocol=GRID&identifier_value=grid.20478.39&format=application/json 

      - View specimens by people ("role" => "collector", "donator", "determinator" or  empty (all))
           - https://darwin.naturalsciences.be/backend.php/specimensearch/search/?&specimen_search_filters[people_protocol]=ORCID&specimen_search_filters[people_identifier]=0000-0002-4048-7728&specimen_search_filters[people_identifier_role]=collector&specimen_search_filters[rec_per_page]=10 
           - https://darwin.naturalsciences.be/backend.php/specimensearch/search/?&specimen_search_filters[people_protocol]=ORCID&specimen_search_filters[people_identifier]=0000-0002-4048-7728&specimen_search_filters[rec_per_page]=10 


      - View specimens by institution (public) : https://darwin.naturalsciences.be/public.php/search/search?&specimen_search_filters[institution_protocol]=Wikidata&specimen_search_filters[institution_identifier]=Q16665660 

      - View specimens by people (public) ; role => collector or donator or determinator or  empty (all) : https://darwin.naturalsciences.be/public.php/search/search?&specimen_search_filters[people_protocol]=ORCID&specimen_search_filters[people_identifier]=0000-0002-4048-7728&specimen_search_filters[people_identifier_role]=collector 

      - public specimen by institution in JSON format (paging mechanism) :  https://darwin.naturalsciences.be/public.php/json/get_institution_identifier_json?identifier_protocol=Wikidata&identifier_value=Q16665660 

      - public specimen by people in JSON format (paging mechanism) ; role => collector or donator or determinator or  empty (all) : https://darwin.naturalsciences.be/public.php/json/get_people_identifier_json?identifier_protocol=ORCID&identifier_value=0000-0002-4048-7728&role=identifier

**Data model :**
  - The PostgreSQL data model is different from the original one of Darwin. It now features a "temporal_information" table between the specimen and the gtu, for chronological information. The sql code contains several fucntion to mirgate from the old data model to the current one in the "public" schema. This migration procedures use the PostgreSQL "ForeignDataWrappers"
  

**Changes related to lexpress are :**
  - method **SaveEmbeddedForms** is replaced by **SaveObjectEmbeddedForms** in forms (different signature but same content). https://github.com/FriendsOfSymfony1/symfony1/issues/103
  - initialisation of the project in **./config/ProjectConfiguration.class.php** is different. The **configureDoctrine** method is replaced by **configureDoctrineEvent**) : https://github.com/FriendsOfSymfony1/symfony1/issues/42
  - the "classic" PHP libraries are required (pdo, xml, xsl, json, ...), but please install **php-apcu** and **php-apcu-bc** (backward compatiability with the old apc API) for the Symfony cache
  
**Configuration of PostgreSQL**

 After having run the script containing the schema (in **darwin/data/db_schema**):
 
    alter user darwin2 set search_path=darwin2,public;
   
    alter database darwin2 set datestyle to 'ISO, DMY';

      
**Configuration of PHP**

  Packages :
     php-apcu
     php-mbstring
     php-curl
     php-pgsql
     php-pdo
