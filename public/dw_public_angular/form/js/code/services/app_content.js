darwinApp.config(['$httpProvider', function($httpProvider) {
    //initialize get if not there
    if (!$httpProvider.defaults.headers.get) {
        $httpProvider.defaults.headers.get = {};    
    }    

    // Answer edited to include suggestions from comments
    // because previous version of code introduced browser-related errors

    //disable IE ajax request caching
    $httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
    // extra
    $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
    $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
}]);





darwinApp.config([ '$locationProvider' , function( $locationProvider) {
//ftheeten: attention, "base" html tag must be set with the root URL
//in the header (overwritte the THEME_preprocess_html function in template.php of the applied Drupal theme)
//very tedious!!!
    $locationProvider.html5Mode({enabled: true,requireBase: true});
    }]);

    
 darwinApp.config(function(tmhDynamicLocaleProvider) {
    tmhDynamicLocaleProvider.localeLocationPattern('./dw_public/form/js/code/common/i18n/angular-locale_{{locale}}.js');

    
  });

  
darwinApp.config(function($translateProvider) {
	
 $translateProvider.useSanitizeValueStrategy('escape');
  $translateProvider.translations('en', {
	TITLE_SEARCH_PAGE: 'Darwin public search interface | RBINS',
    COLLECTION_SEARCH: 'Search in biological collection',
    TITLE: 'Welcome!',
    MESSAGE: 'This app supports your language!',
    COLLECTION: 'Collection',
    CLASS: 'Class',
    ORDER: 'Order',
    FAMILY: 'Family',
    GENUS: 'Genus',
    SPECIES_AND_BELOW: 'Species or subspecies',
    SPECIMEN_NUMBER: 'Specimen number',
    TAXON_NAME: 'Taxonomical name',
    COUNTRIES: 'Countries',
    LOCALITIES: 'Localities',
    COLLECTORS: 'Collectors',
    START_COLLECTING_DATE: 'Collected after',
    END_COLLECTING_DATE: 'Collected before',
    TYPES: 'Types',
    HAS_IMAGES: 'With images',
    HAS_3D: 'With 3D',
    STEP: 'Step',
    CLEAN: 'Clean',
    SEARCH: 'Search',
    SPECIMEN_FOUND: 'found specimens',
    SHOW_HIDE_MAP: 'Show/Hide map',
    DRAW_ON_MAP: 'Draw on map',
	MOVE_MAP: 'Move map',
    CLEAN_SELECTION: 'Clean selection',
    ENABLE_SELECTION: "To 'enable selection' mode",
    ENABLE_MOVE: "To 'enable move' mode",
    STEP_1_D: 'Step 1°',
    STEP_1_M: 'Step 1\'',
    STEP_1_S: 'Step 1"',
    NORTH: 'North',
    WEST: 'West',
    EAST: 'East',
    SOUTH: 'South',
    
    TYPE_STATUS: 'Zoological type',
    LOCALITY: 'Locality',
    LATITUDE: 'Latitude',
    LONGITUDE: 'Longitude',
    COLLECTING_DATE_FROM: 'Begin of collecting period',
    COLLECTING_DATE_TO: 'End of collecting period',
    COUNTRY: 'Country',
    DONATORS: 'Donator',
    IDENTIFICATION_HISTORY: 'History of scientific identifications',
    
    CLOSE: 'Close',
    OPEN_TAB: 'Open page',
    
    en: 'English',
    nl: 'Nederlands',
    fr: 'Français',
    COLLECTION_SEARCH_ichtyology: 'Ichtyological database of the RBINS',
    COLLECTION_SEARCH_mammalogy: 'Mammal database of the RBINS',
    COLLECTION_SEARCH_coelentera: 'Coelenterata database of the RBINS',
    COLLECTION_SEARCH_arachnomorphae: 'Arachnomorphae database of the RBINS',
    CONTACT_PERSON: 'Contact',
	SEX: 'Sex',
    SPECIMEN_COUNT: 'Specimen count',
	SPECIMEN_COUNT_MALES: 'Specimen count (males)',
	SPECIMEN_COUNT_FEMALES: 'Specimen count (females)',
	DRAW_POLYGON : 'Free polygon',
	STABLE_CETAF_IDENTIFIER: 'Stable CETAF identifier (permalink)',
	ADVANCED_SEARCH_INTERFACE: 'Data management interface',
	IMAGE_LINK : 'Image link',
	RMCA : 'Royal Belgian Institute for Natural Sciences',
	LEGAL_NOTICES : 'Legal notices',
	ADVANCED_BACKEND_INTERFACE : 'Data management interface',
	GEOREF_ONLY: 'Only with geographical coordinates',
	FILTER_SEARCH_GEO: 'Geographic search',
	R25_SPECIMENS_IN_PAGE: '25 specimens in page',
	GEOREFERENCED_IN_PAGE: 'specimens with coordinates (page)',
	GEOREFERENCED_IN_DATASET: 'specimens with coordinates (total)',
	SORT_RESULT: 'Sort',
	ASCENDING: 'Ascending',
	DESCENDING: 'Descending',
	FIRST: 'First',
	LAST: 'Last',
	PREVIOUS: 'Previous',
	NEXT : 'Next',
	IG_NUMBER: 'I.G. Number',
	CITIZEN_SCIENCES : 'Citizen Science',
	PROPERTIES: 'Properties'

  })
  .translations('fr', {
	TITLE_SEARCH_PAGE: 'Darwin public interface de recherche | RBINS',
    COLLECTION_SEARCH: 'Rechercher dans la collection biologique',
    TITLE: 'Bienvenue!',
    MESSAGE: 'Ce site comprend votre langue!',
    COLLECTION: 'Collection',
    CLASS: 'Classe',
    ORDER: 'Ordre',
    FAMILY: 'Famille',
    GENUS: 'Genre',
    SPECIES_AND_BELOW: 'Espèce ou sous-espèce',
    SPECIMEN_NUMBER: 'Numéro de spécimen',
    TAXON_NAME: 'Nom taxonomique',
    COUNTRIES: 'Pays',
    LOCALITIES: 'Localités',
    LOCALITY: 'Localité de collecte',
    COLLECTORS: 'Collecteurs',
    START_COLLECTING_DATE: 'Collecté après',
    END_COLLECTING_DATE: 'Collecté avant',
    TYPES: 'Types',
    HAS_IMAGES: 'Avec images',
    HAS_3D: 'Avec 3D',
    STEP: 'Pas',
    CLEAN: 'Réinitialiser',
    SEARCH: 'Rechercher',
    SPECIMEN_FOUND: 'spécimen(s) trouvé(s)',
    SHOW_HIDE_MAP: 'Afficher/cacher la carte',
    DRAW_ON_MAP: 'Dessiner sur la carte',
	MOVE_MAP: 'Déplacer la carte',
    CLEAN_SELECTION: 'Nettoyer la sélection',
    ENABLE_SELECTION: "Vers mode 'zone de sélection'",
    ENABLE_MOVE: "Vers mode 'déplacer la carte'",
    NORTH: 'nord',
    WEST: 'ouest',
    EAST: 'est',
    SOUTH: 'sud',
    STEP_1_D: 'Pas de 1°',
    STEP_1_M: 'Pas de 1\'',
    STEP_1_S: 'Pas de 1"',
    
    TYPE_STATUS: 'Type zoologique',
    LOCALITY: 'Localité',
    LATITUDE: 'Latitude',
    LONGITUDE: 'Longitude',
    COLLECTING_DATE_FROM: 'Début période de collecte',
    COLLECTING_DATE_TO: 'Fin période de collecte',
    COUNTRY: 'Pays',
    DONATORS: 'Donateurs',
    IDENTIFICATION_HISTORY: 'Historique des déterminations',
    
    CLOSE: 'Fermer',
    OPEN_TAB: 'Ouvrir la fiche',
    
    en: 'English',
    nl: 'Nederlands',
    fr: 'Français',
    COLLECTION_SEARCH_ichtyology: "Base de données ichtyologiques de l'IRSNB",
    COLLECTION_SEARCH_mammalogy: "Base de données mammalogiques de l'IRSNB",
    COLLECTION_SEARCH_coelentera: "Base de données des cœlentérés de l'IRSNB",
    COLLECTION_SEARCH_arachnomorphae: "Base de données des arachnidés de l'IRSNB",
    CONTACT_PERSON: 'Contact',
	SEX: 'Sexe',
    SPECIMEN_COUNT: 'Nombre de spécimens',
	SPECIMEN_COUNT_MALES: 'Nombre de mâles',
	SPECIMEN_COUNT_FEMALES: 'Nombre de femelles',
	DRAW_POLYGON : 'Polygone libre',
	STABLE_CETAF_IDENTIFIER: 'Identifiant stable CETAF  (permalien)',
	ADVANCED_SEARCH_INTERFACE: 'Interface de gestion des données',
	IMAGE_LINK : 'Lien vers images',
	RMCA : 'Institut royal des sciences naturelles de Belgique',
	LEGAL_NOTICES : 'Mentions juridiques',
	ADVANCED_BACKEND_INTERFACE : 'Interface de gestion des données',
	GEOREF_ONLY: 'Seulement avec coordonnées géographiques',
	FILTER_SEARCH_GEO: 'Recherche géographique',
	R25_SPECIMENS_IN_PAGE: '25 spécimens par page',
	GEOREFERENCED_IN_PAGE: 'spécimen avec des coordonnées (page)',
	GEOREFERENCED_IN_DATASET: 'specimens avec des coordonnées (total)',
	SORT_RESULT: 'Trier',
	ASCENDING: 'Croissant',
	DESCENDING: 'Décroissant',
	FIRST: 'Premier',
	LAST: 'Dernier',
	PREVIOUS: 'Précédent',
	NEXT : 'Suivant',
	IG_NUMBER : 'Numéro I.G.',
	CITIZEN_SCIENCES : 'Citizen Science',
	PROPERTIES: 'Propriétés'
    
  })
  .translations('nl', {
	TITLE_SEARCH_PAGE: 'Darwin public zoekinterface | KBIN',
    COLLECTION_SEARCH: 'Zoeken in biologische collectie',
    TITLE: 'Welkom!',
    MESSAGE: 'Deze website biedt ondersteuning in uw taal !',
    COLLECTION: 'Collectie',
    CLASS: 'Klasse',
    ORDER: 'Orde',
    FAMILY: 'Familie',
    GENUS: 'Genus',
    SPECIES_AND_BELOW: 'Soort of ondersoort',
    SPECIMEN_NUMBER: 'Specimennummer',
    TAXON_NAME: 'Taxonomische benaming ',
    
    COUNTRIES: 'Land',
    LOCALITIES: 'Localiteit',
    LOCALITY: 'Plaats van inzameling',
    COLLECTORS: 'Verzamelaar',
    START_COLLECTING_DATE: 'Verzameldatum (begin)',
    END_COLLECTING_DATE: 'Verzameldatum (einde)',
    TYPES: 'Types',
    HAS_IMAGES: 'Met beelden',
    HAS_3D: '3D beelden',
    STEP: 'Stap',
    CLEAN: 'Terugstellen',
    SEARCH: 'Zoeken',
    SPECIMEN_FOUND: 'gevonden specimens',
    SHOW_HIDE_MAP: 'Kaart tonen/verbergen',
    DRAW_ON_MAP: 'Zoekgebied tekenen',
	MOVE_MAP: 'Kaart verplaatsen',
    CLEAN_SELECTION: 'Terugstellen',
    ENABLE_SELECTION: "Naar 'zoekgebied bepalen' mode",
    ENABLE_MOVE: "Naar 'kaart bewegen' mode",
    NORTH: 'Noord',
    WEST: 'West',
    EAST: 'Oost',
    SOUTH: 'Zuid',
    STEP_1_D: 'Stap van 1°',
    STEP_1_M: 'Stap van 1\'',
    STEP_1_S: 'Stap van 1"',
    
    TYPE_STATUS: 'Zoologische type',
    LOCALITY: 'Localiteit',
    LATITUDE: 'Geografische breedtegraad',
    LONGITUDE: 'Geografische lengtegraad',
    COLLECTING_DATE_FROM: 'Verzameldatum (begin)',
    COLLECTING_DATE_TO: 'Verzameldatum (einde)',
    COUNTRY: 'Land',
    DONATORS: 'Schenker',
    IDENTIFICATION_HISTORY: 'Geschiedenis van identificaties',
    
     CLOSE: 'Sluiten',
    OPEN_TAB: 'Beschrijving openen',
    
    en: 'English',
    nl: 'Nederlands',
    fr: 'Français',
    COLLECTION_SEARCH_ichtyology: 'Ichtyologische databank van het KBIN',
    COLLECTION_SEARCH_mammalogy: 'Databank van zoogdieren van het KBIN',
    COLLECTION_SEARCH_coelentera: 'Databank van coelenterata van het KBIN', 
    COLLECTION_SEARCH_arachnomorphae: 'Archnida databank van het KBIN',
    
    CONTACT_PERSON: 'Contact',
    SEX: 'Geslacht',
    SPECIMEN_COUNT: 'Aantal specimens',
	SPECIMEN_COUNT_MALES: 'Aantal specimens (mannelijk)',
	SPECIMEN_COUNT_FEMALES: 'Aantal specimens (vrouwelijk)',
	DRAW_POLYGON : 'Vrije hand polygoon',
	STABLE_CETAF_IDENTIFIER: 'Stable CETAF identifier (permalink)',
	ADVANCED_SEARCH_INTERFACE: 'Interface voor databeheer',
	IMAGE_LINK : 'Link naar beelden',
	RMCA : ' Koninklijk Belgisch Instituut voor Natuurwetenschappen',
	LEGAL_NOTICES : 'Juridische mededelingen',
	ADVANCED_BACKEND_INTERFACE : 'Interface voor gegevensbeheer',
    GEOREF_ONLY: 'Alleen met geografische coördinaten',
    FILTER_SEARCH_GEO: 'Geografische criteria',
	R25_SPECIMENS_IN_PAGE: '25 stalen per pagina',
	GEOREFERENCED_IN_PAGE: 'stalen met geografische coördinaten (pagina)',
	GEOREFERENCED_IN_DATASET: 'stalen met geografische coördinaten (algemeen)',
	SORT_RESULT: 'Sorteren',
	ASCENDING: 'Oplopende',
	DESCENDING: 'Aflopende',
	FIRST: 'Eerste',
	LAST: 'Laatste',
	PREVIOUS: 'Vorige',
	NEXT : 'Volgende',
	IG_NUMBER : 'I.G. nummer',
	CITIZEN_SCIENCES : 'Citizen Science',
	PROPERTIES: 'Eigenschappen / Kenmerken'
     
  });

  $translateProvider.preferredLanguage('en');
});
    

 
darwinApp.factory("DarwinFactory", ['$http', function($http){  
    var obj = {};
    
    obj.getCollections=function()
    {
		//console.log("get_collection");
        return $http.get('../ws/ws.php?operation=get_collections');
    }
    
     obj.getCollectionID=function(collection_name)
    {
			
          //return $http.get('darwin/get_collection_id/'+collection_name);
		  return $http.get('../ws/ws.php?operation=get_collection_id&code='+collection_name);
    }
	
	 obj.getSubCollectionByID=function(col_id)
    {
			
			//console.log("CALL_SUB");
          //return $http.get('darwin/get_collection_id/'+collection_name);
		  return $http.get('../ws/ws.php?operation=get_sub_collections&col='+col_id);
    }
    
     obj.getCollectionIDByTaxa=function(taxa)
    {
          //return $http.get('darwin/get_collection_id/'+collection_name);
		  return $http.get('../ws/ws.php?operation=get_codes&taxon='+taxa)
    }
    
    obj.getTypes=function(collection_id)
    {
        
        //return $http.get('darwin/types/'+collection_id);
		if(collection_id===undefined)
		{
			return $http.get('../ws/ws.php?operation=get_types');
		}
		else
		{
			return $http.get('../ws/ws.php?operation=get_types&col='+collection_id);
		}
    }
    
     obj.splitSQLToArray=function(str)
       {

            return str.replace(/({\"|\"})/g,'').split('","');
       };
     
     obj.assign=function(objTmp, keyPath, value) {
       
           lastKeyIndex = keyPath.length-1;
           for (var i = 0; i < lastKeyIndex; ++ i) {
             key = keyPath[i];
             if (!(key in objTmp))
               objTmp[key] = {}
             objTmp = objTmp[key];
           }
           objTmp[keyPath[lastKeyIndex]] = value;
           return objTmp;
        }
       
    
     obj.getSpecimen=function(num, mode)
     {
			
			var urlTmp="";
			if(mode=="id")
			{
				 urlTmp='./dw_public/ws/ws.php?operation=get_specimen&id='+num;
			}
            else
			{
				 urlTmp='./dw_public/ws/ws.php?operation=get_specimen&uuid='+num;
			}
			//console.log(urlTmp);
             return $http.get(urlTmp)
                .then(function(response) {
               
                    if((response.data.length)>0)
                    {
                        
                            var line=response.data[0];
                          var ids=line.ids;  
                          var id_spec=line.code_display;
						  var ig_num=line.ig_num;
						  
                          var taxon_names_src=line.taxon_name;
                          
                          var split_taxas= obj.splitSQLToArray( taxon_names_src);
                          var taxon_name="";
                          if(split_taxas.length>0)
                          {
                             taxon_name=split_taxas[0];

                          }
                          else
                          {
                             taxon_name= taxon_names_src;
                          }
                          if((response.data.length)>1)
                          {
                            
                            var append_str="";
                           
                            var maxLimit=parseInt(response.data.length);
                            for(var j=1;j<maxLimit;j++)
                            {
                               
                                var line2=response.data[j];
                             
                                
                                 append_str+=" / \nOther identification: ";
                               
                                 var taxon_names_src2=line2.taxon_name;
                          
                                  var split_taxas2= obj.splitSQLToArray( taxon_names_src2);
                                  var taxon_name2="";
                                  if(split_taxas2.length>0)
                                  {
                                     taxon_name2=split_taxas2[0];

                                  }
                                  else
                                  {
                                     taxon_name2= taxon_names_src2;
                                  }
                                  append_str+=taxon_name2;
                            }
                            taxon_name+=append_str;
                          }
                          taxon_name=taxon_name.replace(/(^{|}$)/g,'');
                          
                          var collection_name_full_path=line.collection_name_full_path;
                          var localities=line.localities;
						  var gtu_country_tag_value=line.gtu_country_tag_value;
                          var split_localities= obj.splitSQLToArray(localities);
                          var structureLoc={};

                          /*split_localities.map(function(word)
                          { 
                   
                           
                            var tmp=word.split(/\:/);
                            
                            if(tmp.length==2)
                            {
                                var level_1Tmp=tmp[0];
                                
                                var tmp2=level_1Tmp.split(/\-/);

                                if(tmp2.length==2)
                                {
                                    var level_1=tmp2[0].replace(/\\\"/g,'');
                                    var level_2=tmp2[1].replace(/\\\"/g,'');
                                    var level_3=tmp[1].replace(/\\\"/g,'');
                                    obj.assign(structureLoc, [level_1, level_2],level_3)
                                    
                                }
                            }
                            
                          }
                          );*/
                          var date_from_display=line.date_from_display.replace(/(xxxx|\-xx)/g,'').split("-").reverse().join("/");
                          var date_to_display=line.date_to_display.replace(/(xxxx|\-xx)/g,'').split("-").reverse().join("/");
                          var collectors=obj.splitSQLToArray(line.collectors||'').join("; ");
                          var donators=obj.splitSQLToArray(line.donators||'').join("; ");
						  var uuid=line.uuid;
                         
                          var history_identification={}
                          obj.splitSQLToArray(line.history_identification||'').forEach(function (item) {
                               history_identification[item]=item;
                            });
                            
                            var latitude=line.latitude;
                            var longitude=line.longitude;
                            var type=line.coll_type;
                            var has_images=false;
                            var image_obj={};
                            
                             var urls_thumbnails={};
                             var display_order_thumbnails={};
                             var contributor_thumbnails={};
                             var image_links={};
                            if(line.urls_thumbnails||line.display_order_thumbnails||line.contributor_thumbnails||line.urls_image_links)
                            {
                                 urls_thumbnails=line.urls_thumbnails.split("|");
                                 display_order_thumbnails=line.display_order_thumbnails.split("|");
                                 contributor_thumbnails=line.contributor_thumbnails.split("|");
                                 image_links=line.urls_image_links.split("|");
                                
                               
                                if(urls_thumbnails.length==display_order_thumbnails.length
                                &&urls_thumbnails.length==image_links.length)
                                {
                                    for(var i in urls_thumbnails)
                                    {
                                       var lineTmp={};
                                      
                                       lineTmp.thumbnail=urls_thumbnails[i];
                                       lineTmp.image_link=image_links[i];
                                       if(urls_thumbnails.length==contributor_thumbnails.length)
                                       {
                                            lineTmp.contributor=contributor_thumbnails[i];
                                       }
                                       else if(contributor_thumbnails.length==1)
                                       {
                                            lineTmp.contributor=contributor_thumbnails[0];
                                       }
                                       image_obj[display_order_thumbnails[i]]=lineTmp;
                                       
                                    }
                                    has_images=true;
                                }
                           }
                         
                         
                          var has_3d=false;
                          var d3d_obj={};
                          var urls_3d_snippets={};
                          var contributor_3d_snippets={};
                          if(line.urls_3d_snippets||line.contributor_3d_snippets)
                          {
							  if(line.urls_3d_snippets==null)
							  {
								  line.urls_3d_snippets="";
							  }
							  if(line.contributor_3d_snippets==null)
							  {
								  line.contributor_3d_snippets="";
							  }
                              urls_3d_snippets=line.urls_3d_snippets.split("|");
                              contributor_3d_snippets=line.contributor_3d_snippets.split("|");
                              
                              
                               for(var i in urls_3d_snippets)
                               {
                                   var lineTmp={};
                                      
                                    lineTmp.snippet=urls_3d_snippets[i];
                                   
                                     if(urls_3d_snippets.length==contributor_3d_snippets.length)
                                     {
                                         contributor=contributor_3d_snippets[i];
                                     }
                                     else if(contributor_3d_snippets.length==1)
                                     {
                                         lineTmp.contributor=contributor_3d_snippets[0];
                                     }
                                      d3d_obj[i]=lineTmp;
                                    
                                       
                                }
                               has_3d=true;
                          }
                            
                         
                          return {
                                status: 'ok',
                                ids:ids,
								uuid:uuid,
								collection_name_full_path:collection_name_full_path,
                                id_spec:id_spec,
								ig_num:ig_num,
                                taxon_name:taxon_name,
								gtu_country_tag_value:gtu_country_tag_value,
                                localities: localities,
                                structureLoc: structureLoc,
                                latitude: latitude,
                                longitude: longitude,
                                date_from_display: date_from_display,
                                date_to_display: date_to_display,
                                collectors:collectors,
                                donators: donators,
                                type: type,
                                history_identification:history_identification,
                                has_images: has_images,
                                image_obj: image_obj,
                                has_3d:has_3d,
                                d3d_obj:d3d_obj, 
							    family: line.family,
								order: line.t_order,
								class: line.class,
								sex: line.sex,
								specimen_count: line.specimen_count_min,
								specimen_count_males: line.specimen_count_males_min,
								specimen_count_females: line.specimen_count_females_min,
								properties:line.properties
								
                            };
                    }
                  
               
              });
               
              return{status: 'fail'};
       };
	   
	   obj.getAllHTTPParams=function(url)
	   {
			//lookahead not working on Safari
			//var regexS = /(?<=&|\?)([^=]*=[^&#]*)/;
			//var regex = new RegExp( regexS,'g' );
			//var results = url.match(regex);            
			var results=null;
			if(url.indexOf("?")>-1)
			{
				var tmp1=url.split("?");
				if(tmp1.length>0)
				{
					var tmp2=tmp1[tmp1.length-1];
					results=tmp2.split("&");
				}
			}
			if(results==null)
			{
				return {};
			}
			else
			{
				returned={};
				for(i=0;i<results.length;i++)
				{
					var tmp=results[i];                
					var regexS2="([^=]+)=([^=]+)";
					var regex2 = new RegExp( regexS2 );
					var results2 = regex2.exec(tmp );                
					returned[results2[1]]=results2[2];
				}
				return returned;
			}   
	   }
		obj.getHTTPParam=function(param)		
		{
			
			var url=window.location.href;
			
			var params= obj.getAllHTTPParams(url);
			
			if(param in params)
			{
				return params[param];
			}
			else
			{
				return "";
			}
		
		}
    return obj;
}]);


darwinApp.factory("PagerService", [ '$http', function($http){
    var obj = {};
    
    var GetPager = function GetPager(rootURL, currentPage, pageSize, sortOrder) {
		//console.log("pager_Called");
        var urlTmp=rootURL+'&size='+pageSize+'&page='+currentPage+'&sort='+sortOrder;

        var dataSet={};
        var totalItems=-1;

        return $http.get(urlTmp)
                .then(function(response) {
                     

               dataSet = response.data;
               if(dataSet.length==0)
               {
                    totalItems=0;
               }
               else
               {
                 totalItems=dataSet[0].full_count;
               }
               
        // default to first page
                currentPage = currentPage || 1;
         
                // default page size is 10
                pageSize = pageSize || 10;
         
                // calculate total pages
                var totalPages = Math.ceil(totalItems / pageSize);
         
                var startPage, endPage;
                if (totalPages <= 10) {
                    // less than 10 total pages so show all
                    startPage = 1;
                    endPage = totalPages;
                } else {
                    // more than 10 total pages so calculate start and end pages
                    if (currentPage <= 6) {
                        startPage = 1;
                        endPage = 10;
                    } else if (currentPage + 4 >= totalPages) {
                        startPage = totalPages - 9;
                        endPage = totalPages;
                    } else {
                        startPage = currentPage - 5;
                        endPage = currentPage + 4;
                    }
                }
         
                // calculate start and end item indexes
                var startIndex = (currentPage - 1) * pageSize;
                var endIndex = startIndex + pageSize;
         
         
                // create an array of pages to ng-repeat in the pager control
                var pages = _.range(startPage, endPage + 1);
         
                // return object with all pager properties required by the view
                return {
                    items: dataSet ,
                    totalItems: totalItems,
                    currentPage: currentPage,
                    pageSize: pageSize,
                    totalPages: totalPages,
                    startPage: startPage,
                    endPage: endPage,
                    startIndex: startIndex,
                    endIndex: endIndex,
                    pages: pages
                };
              });
        
    }
    
    var EmptyPager = function EmptyPager() {
        
        return {
                    items: {} ,
                    totalItems: 0,
                    currentPage: 00,
                    pageSize: 25,
                    totalPages: 0,
                    startPage: 0,
                    endPage: 0,
                    startIndex: 0,
                    endIndex: 0,
                    pages: 0
                };
           
    };
    
     return { GetPager: GetPager,
            EmptyPager: EmptyPager};

    }]);

    
darwinApp.factory('comm_service',
    function()
    {
        var init=-1;
        var savedData = {}
         function set(data) {
           savedData = data;
           init=1;
         }
         function get() {
          return savedData;
         }
         function getInit() {
           
          return init;
         }

         return {
          set: set,
          get: get,
          getInit: getInit
         }

    }
);


darwinApp.factory('storageFactory', ['$window', sessionFactory]);

function sessionFactory($window) {
    return {
        save: save,
        get: get,
        clear: clear,
        _getDirect: _getDirect,
    };

    function save(key, value) {
		//console.log("CALLED");
    var seen = [];
        $window.localStorage.setItem(key, JSON.stringify(value, function(key, val) {
   if (val != null && typeof val == "object") {
        if (seen.indexOf(val) >= 0) {
            return;
        }
        seen.push(val);
    }
    return val;
}));
    }

    function get(key) {
        return  JSON.parse($window.localStorage.getItem(key));
    }

    function clear() {
        $window.localStorage.clear();
    }
    
     function _getDirect(key) {
        $window.localStorage.getItem(key);
    }
}

  
    

/* manually bootstrap Angular */

jQuery(document).ready(function(){
  angular.bootstrap(document.getElementById('darwin-app'),['darwin']);
  });
