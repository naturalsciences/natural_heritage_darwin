darwinApp.controller('darwin-controller', function($scope, DarwinFactory, PagerService, $location, $http, $translate, $timeout, $modal, $locale, tmhDynamicLocale,$sanitize, storageFactory, $window)
{



console.log("INIT_CONTROLLER");

 $scope.ctrl={};
 $scope.ctrl.date={};
 $scope.ctrl.date.validationError=false;
 $scope.collapseMap=true;
 $scope.Math = window.Math;
 $scope.message="";
 $scope.current_collection={};
 $scope.show_collection_list=true;
 $scope.current_collection.id=-1;
 $scope.collection_by_url=-1;
$scope.pages_specimen={}; 
$scope.wkt="";
$scope.frame=false;


$scope.detail_url="https://darwinweb.africamuseum.be/dw_public_angular/form/specimen_detail.html?uuid=";
$scope.detail_url_frame="https://darwinweb.africamuseum.be/dw_public_angular/form/specimen_detail.html?uuid=";
$scope.detail_url_current=$scope.detail_url;


$scope.url_prefix="../ws/ws.php?";
  
  //languages
  $scope.ctrl.language = 'en';
  $scope.ctrl.languages = ['en', 'nl', 'fr'];
  $scope.ctrl.updateLanguage = function() {
        $translate.use($scope.ctrl.language);
        //synchronized with the name of tiles in the i18n javascript folder in "common"
        tmhDynamicLocale.set($scope.ctrl.language);
        
    };
    
  $scope.ctrl.setLanguage = function(code) {
       $scope.ctrl.language=code;
       $translate.use($scope.ctrl.language);

        tmhDynamicLocale.set($scope.ctrl.language);
        
    };
  
$scope.go_to_anchor=function(anchor) {
  var loc = document.location.toString().split('#')[0];
  document.location = loc + '#' + anchor;
  return false;
}
  
     
  $scope.clean_interface=function()
  {
	  $scope.ctrl.class={};
	  $scope.ctrl.class.selected=-1;
	  $scope.ctrl.order={};  
	  $scope.ctrl.order.selected=-1;  
	  
	  $scope.ctrl.family={};
	  $scope.ctrl.family.selected=-1;
	  $scope.ctrl.genus={};  
	  $scope.ctrl.genus.selected=-1;  

	  $scope.ctrl.species={};  
	  $scope.ctrl.species.selected=-1;  

	  $scope.ctrl.sp_num={};  
	  $scope.ctrl.sp_num.selected=-1;

	  $scope.ctrl.country={};  
	  $scope.ctrl.country.selected=-1;    

	  $scope.ctrl.locality={};  
	  $scope.ctrl.locality.selected=-1;

	  $scope.ctrl.collector={};  
	  $scope.ctrl.collector.selected=-1;
	 
	 
	  $scope.current_type={};
	  $scope.current_type.name='-1';

	  /*DarwinFactory.getTypes();
	  */
	  $scope.ctrl.types={};  
	  $scope.ctrl.types.selected=-1;
	
	   $scope.selectedTypes =["types", "non-type"];
	   
	   $scope.checkBoxModel = {};
	   $scope.checkBoxModel.has_image= false;
	   $scope.checkBoxModel.has_3d = false;
	   $scope.checkBoxModel.georef_only = false;
	  
	  
	   $scope.selectionN=90;
	   $scope.selectionW=-180;
	   $scope.selectionE=180;
	   $scope.selectionS=-90;
	   
	   $scope.ctrl.beginDay="";
	   $scope.ctrl.beginMonth="";
	   $scope.ctrl.beginYear="";
	   $scope.ctrl.endDay="";
	   $scope.ctrl.endMonth="";
	   $scope.ctrl.endYear="";   

		$scope.wkt="";
		
  }
  
   $scope.clean_interface();
   
  $scope.search_results={};
  $scope.geoJSON="";
  $scope.interceptMapClick="";
   
  /* $scope.ctrl.dateRangeStart=Date();
  $scope.ctrl.dateRangeEnd=Date();
   */
   
  $scope.pageSize=25;

  $scope.currentPage=1;
  
  $scope.sortOrder="-1";

  $scope.pager = {};  
  $scope.items= {};
  
  $scope.currentSearchURL="";
  
 $scope.collapseMapSwitch=function()
 {
    $scope.collapseMap=!$scope.collapseMap;
 }
  
  $scope.getPage =function(page)
  {
    return $scope.setPage(page,$scope.currentSearchURL );
  }
  
  $scope.setLinks=function()
      {
        for(var i=0; i<$scope.items.length; i++)
        {
            var obj=$scope.items[i];
             if($location.host()=="193.190.223.5")
            {
                $scope.pages_specimen[obj.id]="http://193.190.223.5/collections/browsecollections/naturalsciences/biology/"+ $scope.collection_by_url+"/darwin_specimen?id_spec="+obj.uuid;
            }
            else if($location.host()=="darwinweb.africamuseum.be")
            {
                //$scope.pages_specimen[obj.id]="http://darwinweb.africamuseum.be/page_specimen/"+obj.uuid;
				var p_frame="false";
				if($scope.frame)
				{
					p_frame="true";
				}
				$scope.pages_specimen[obj.id]=$scope.detail_url_current+obj.uuid+"&lang="+$scope.ctrl.language+"&frame="+p_frame;
            }
            else if($location.host()=="www.africamuseum.be")
            {
                $scope.pages_specimen[obj.id]="http://www.africamuseum.be/collections/browsecollections/naturalsciences/biology/"+ $scope.collection_by_url+"/darwin_specimen?id_spec="+obj.uuid;
            }
            else
            {
                scope.pages_specimen[obj.id]="page_specimen/"+obj.uuid;
            }
          }
      }


  $scope.keep_url=function(urlQuery)
  {
	
	  storageFactory.save("search_url", urlQuery);
	  //going to next page there !!
	  
	  if($scope.ctrl.language.length>0)
	  {
		 
		   $window.location.href = './specimen_data.html?lang='+$scope.ctrl.language;
	  }
	  else
	  {
		$window.location.href = './specimen_data.html';
	  }
  }
  
    $scope.keep_url_georef=function(urlQuery)
  {
	
	  storageFactory.save("search_url_georef", urlQuery);
	    
  }
  

  $scope.ctrl.getLowestTaxa= function() {
    var returned=-1;
    if($scope.ctrl.species.selected !="-1" && $scope.ctrl.species.selected.toString().trim().length>0)
    {
	
        returned=$scope.ctrl.species.selected;
        $scope.message="Lowest taxon: species";
    }
    else if($scope.ctrl.genus.selected !="-1" && $scope.ctrl.genus.selected.toString().trim().length>0)
    {
        returned=$scope.ctrl.genus.selected;
        $scope.message="Lowest taxon: Genus";
    }
    else if($scope.ctrl.family.selected !="-1" && $scope.ctrl.family.selected.toString().trim().length>0 )
    {
        returned=$scope.ctrl.family.selected;
        
        $scope.message="Lowest taxon: Family";
    }
    else if($scope.ctrl.order.selected !="-1" && $scope.ctrl.order.selected.toString().trim().length>0 )
    {
        returned=$scope.ctrl.order.selected;
        
        $scope.message="Lowest taxon: Order";
    }
    else if($scope.ctrl.class.selected !="-1" && $scope.ctrl.class.selected.toString().trim().length>0 )
    {
        returned=$scope.ctrl.class.selected;
        
        $scope.message="Lowest taxon: Class";
    }

    return returned;
  
  };
  
  $scope.ctrl.getLowestTaxaDarwinCode= function() {
    var returned=-1;
    if($scope.ctrl.species.selected !="-1" && $scope.ctrl.species.selected.toString().trim().length>0)
    {
	
        returned=48;
        $scope.message="Lowest taxon: species";
    }
    else if($scope.ctrl.genus.selected !="-1" && $scope.ctrl.genus.selected.toString().trim().length>0)
    {
        returned=41;
        $scope.message="Lowest taxon: Genus";
    }
    else if($scope.ctrl.family.selected !="-1" && $scope.ctrl.family.selected.toString().trim().length>0 )
    {
        returned=34;
        
        $scope.message="Lowest taxon: Family";
    }
    else if($scope.ctrl.order.selected !="-1" && $scope.ctrl.order.selected.toString().trim().length>0 )
    {
        returned=28;
        
        $scope.message="Lowest taxon: Order";
    }
    else if($scope.ctrl.class.selected !="-1" && $scope.ctrl.class.selected.toString().trim().length>0 )
    {
        returned=12;
        
        $scope.message="Lowest taxon: Class";
    }

    return returned;
  
  };
  
  
    $scope.ctrl.getLowestTaxaByDarwinRank= function(code) {
    var returned=-1;
    if($scope.ctrl.species.selected !="-1"&& code>=48)
    {
        returned=$scope.ctrl.species.selected;
        $scope.message="Lowest taxon: species";
    }
    else if($scope.ctrl.genus.selected !="-1"&& code>=41)
    {
        returned=$scope.ctrl.genus.selected;
        $scope.message="Lowest taxon: Genus";
    }
    else if($scope.ctrl.family.selected!="-1"&& code>=34)
    {
        returned=$scope.ctrl.family.selected;
        
        $scope.message="Lowest taxon: Family";
    }
    else if($scope.ctrl.order.selected!="-1"&& code>=28)
    {
        returned=$scope.ctrl.order.selected;
        
        $scope.message="Lowest taxon: Order";
    }
    else if($scope.ctrl.class.selected!="-1"&& code>=12)
    {
        returned=$scope.ctrl.class.selected;
        
        $scope.message="Lowest taxon: Class";
    }

    return returned;
  
  };
  
      $scope.ctrl.getLowestTaxaByCode= function(rank) {
    var returned=-1;
    if($scope.ctrl.species.selected !="-1"&& rank!="species")
    {
        returned=$scope.ctrl.species.selected;
        $scope.message="Lowest taxon: species";
    }
    else if($scope.ctrl.genus.selected !="-1"&& rank!="genus")
    {
        returned=$scope.ctrl.genus.selected;
        $scope.message="Lowest taxon: Genus";
    }
    else if($scope.ctrl.family.selected!="-1"&& rank!="family")
    {
        returned=$scope.ctrl.family.selected;
        
        $scope.message="Lowest taxon: Family";
    }
    else if($scope.ctrl.order.selected!="-1"&& rank!="order")
    {
        returned=$scope.ctrl.order.selected;
        
        $scope.message="Lowest taxon: Order";
    }
    else if($scope.ctrl.class.selected!="-1"&& rank!="class")
    {
        returned=$scope.ctrl.class.selected;
        
        $scope.message="Lowest taxon: Class";
    }

    return returned;
  
  };
  
  

  
  
   $scope.ctrl.reinit= function() 
   {
      $scope.ctrl.class={};
      $scope.ctrl.class.selected=-1;
      $scope.ctrl.order={};  
      $scope.ctrl.order.selected=-1;  
   
      $scope.ctrl.family={};
      $scope.ctrl.family.selected=-1;
      $scope.ctrl.genus={};  
      $scope.ctrl.genus.selected=-1;  

      $scope.ctrl.species={};  
      $scope.ctrl.species.selected=-1;  

      $scope.ctrl.sp_num={};  
      $scope.ctrl.sp_num.selected=-1;

      $scope.ctrl.country={};  
      $scope.ctrl.country.selected=-1; 
      $scope.countries =[];      

      $scope.ctrl.locality={};  
      $scope.ctrl.locality.selected=-1;

      $scope.ctrl.collector={};  
      $scope.ctrl.collector.selected=-1;  
   };
   


    //console.log("LOAD_COLL");
    DarwinFactory.getCollections().success(
        function(response)
        {
		    //console.log(response);
			response.unshift({"id":-1, "name":"ALL"});
            $scope.collections=response;
          
        }
    );
    
    /*DarwinFactory.getTypes().success(
        function(response)
        {
            $scope.types=response;
            
        }
    );*/
    
    $scope.resyncRank = function(param) 
    {
       
       //alert(param);
       //alert($scope.ctrl.genus.selected);
       //alert( $scope.genus[1].name);
       $scope.ctrl.getLowestTaxa();
       if(param=="country")
       {
            if($scope.ctrl.country.selected.length==0)
            {
                $scope.ctrl.country.selected=-1;
            }
       }
       if(param=="localities")
       {
            if($scope.ctrl.locality.selected.length==0)
            {
                $scope.ctrl.locality.selected=-1;
            }
       }
	   if(param=="specimen_number")
	   {
		////console.log("spec_nb");
		////console.log($scope.ctrl.sp_num.selected);
	////console.log($scope.ctrl.sp_num);
		if($scope.ctrl.sp_num.selected.length==0)
            {
				////console.log("reinit number");
                $scope.ctrl.sp_num.selected=-1;
            }
	   }
    };

    
    $scope.changeCollection= function()
    {
         $scope.ctrl.reinit();
    }
    
    
    $scope.refreshGeneric = function(pattern, codeLevel,  includeLower) {
        var mode=0;
        var  suffix_lower='false';
        if(includeLower===true)
        {
            suffix_lower='true';
        }
        if(pattern.length>0)
        {
           
            var collection=-1;
            var urlTmp='';
            if( $scope.current_collection.id!="-1")
            {
                mode=mode+1;
                collection=$scope.current_collection.id;
            }
           var darwinCode= $scope.ctrl.getLowestTaxaDarwinCode();
           
           var parent=-1;
       //////console.log(codeLevel);
	    //////console.log(darwinCode);
           if(darwinCode>=0&&(darwinCode<codeLevel&&darwinCode !=-1))
           {    
                
                  
                mode=mode+10;
                parent=$scope.ctrl.getLowestTaxaByDarwinRank(darwinCode);
                
               
           }
          
           if(mode==0)
           {
                //urlTmp='darwin/taxonomy'+ suffix_lower+'/'+pattern+'/'+codeLevel+'/';
				urlTmp=$scope.url_prefix+'operation=get_taxon&q='+pattern+'&rank_id='+codeLevel+"&include_lower="+suffix_lower;
           }
           else if(mode==1)
           {
                //urlTmp='darwin/taxonomy_by_collection'+ suffix_lower+'/'+pattern+'/'+codeLevel+'/'+collection;
				urlTmp=$scope.url_prefix+'operation=get_taxon&q='+pattern+'&rank_id='+codeLevel+"&include_lower="+suffix_lower+"&collection_id="+collection;
           }
           else if(mode==10)
           {
                //urlTmp='darwin/taxonomy_by_parent'+ suffix_lower+'/'+pattern+'/'+codeLevel+'/'+parent;
				urlTmp=$scope.url_prefix+'operation=get_taxon&q='+pattern+'&rank_id='+codeLevel+"&include_lower="+suffix_lower+'&parent_id='+parent;
           }
           else if(mode==11)
           {
                //urlTmp='darwin/taxonomy_by_collection_and_parent'+ suffix_lower+'/'+pattern+'/'+ codeLevel + '/'+collection+'/'+parent;
				urlTmp=$scope.url_prefix+'operation=get_taxon&q='+pattern+'&rank_id='+codeLevel+"&include_lower="+suffix_lower+"&collection_id="+collection+'&parent_id='+parent;
           }
           
            return $http.get(urlTmp);
         }
		 return {};
    };
    
    $scope.classes =[];
    $scope.refreshClasses = function(pattern) {
		if(pattern.length>0)
        {
			return $scope.refreshGeneric(pattern, 12,false).then(function(response) {

						$scope.classes = response.data;
					   
					  });
		}
    };
    
    $scope.orders =[];
    $scope.refreshOrder = function(pattern) {
		if(pattern.length>0)
        {
			return $scope.refreshGeneric(pattern, 28,false).then(function(response) {

						$scope.orders = response.data;
					   
					  });
		}
    };
    
    
    $scope.families =[];
    $scope.refreshFamilies = function(pattern) {
		if(pattern.length>0)
        {
			return $scope.refreshGeneric(pattern, 34,false).then(function(response) {

						$scope.families = response.data;
					   
					  });
		}
    };
    
    $scope.genus =[];
    $scope.refreshGenera = function(pattern) {
		if(pattern.length>0)
        {
			return $scope.refreshGeneric(pattern, 41,false).then(function(response) {

						$scope.genus = response.data;
					   
					  });
		}
    };
    
    $scope.species =[];
    $scope.refreshSpecies = function(pattern) {
		if(pattern.length>0)
        {
         return $scope.refreshGeneric(pattern, 48,true).then(function(response) {

                    $scope.species = response.data;
                   
                  });
		}
    };
    
    //to handle search on label without overloading the server (much better response time)
     $scope.sp_num =[];

  $scope.refreshSpecimenNumbers = function(pattern) {

 	//var urlTmp='darwin/specimen_number/'+pattern;
	var urlTmp=$scope.url_prefix+'operation=get_codes&q='+pattern;
           	 //if($scope.current_collection.id!='-1')
            //	{
               		 //urlTmp=urlTmp+"/"+$scope.current_collection.id;
					 urlTmp=urlTmp+"&col="+$scope.current_collection.id;
                	if($scope.ctrl.getLowestTaxa()!="-1")
                	{
                    		//urlTmp=urlTmp+"&"+$scope.ctrl.getLowestTaxa();
							 urlTmp=urlTmp+"&taxon="+$scope.ctrl.getLowestTaxa();
                	}
               		 return $http.get(urlTmp)
                   	 .then(function(response) {

                   	 $scope.sp_num = response.data;
					 ////console.log($scope.sp_num);
                    	});
            /*}
            else
            {
                return null;
            }*/

            
    };

    
    $scope.countries =[];
     $scope.refreshCountries = function(pattern) {
            //if(pattern.length>2)
            //{
                //var urlTmp='darwin/countries/'+pattern;
				var urlTmp=$scope.url_prefix+"operation=get_countries&q="+pattern
            
                urlTmp=urlTmp+"&col="+$scope.current_collection.id;
                if($scope.ctrl.getLowestTaxa()!="-1")
                {
                    //urlTmp=urlTmp+"/"+$scope.ctrl.getLowestTaxa();
					urlTmp=urlTmp+"&taxon="+$scope.ctrl.getLowestTaxa();
                }
                else
                {
                    //urlTmp=urlTmp+"/-1";
					urlTmp=urlTmp+"&taxon=-1";
                }
                return $http.get(urlTmp)
                    .then(function(response) {

                    $scope.countries = response.data;
                    });
              /*}
             else
             {
                return null;
             }*/
           
            
    };
    
    
     $scope.localities =[];
     $scope.refreshLocalities = function(pattern) 
	 {
           

                /*var urlTmp='darwin/localities/'+pattern;
                urlTmp=urlTmp+"/"+$scope.current_collection.id;
                urlTmp=urlTmp+"/"+$scope.ctrl.getLowestTaxa();
                urlTmp=urlTmp+"/"+$scope.ctrl.country.selected;*/
				var urlTmp=$scope.url_prefix+"operation=get_localities&q="+pattern;
				urlTmp=urlTmp+"&col="+$scope.current_collection.id;
				urlTmp=urlTmp+"&taxon="+$scope.ctrl.getLowestTaxa();
				urlTmp=urlTmp+"&country="+$scope.ctrl.country.selected;
                return $http.get(urlTmp)
                        .then(function(response) {
                            $scope.localities = response.data;
                        });
             
             
    };
    


     $scope.collectors =[];
     $scope.refreshCollectors = function(pattern) {
            //if(pattern.length>2)
            //{
                //var urlTmp='darwin/collectors/'+pattern;
				var urlTmp=$scope.url_prefix+"operation=get_collectors&q="+pattern;
                //urlTmp=urlTmp+"/"+$scope.current_collection.id;
				urlTmp=urlTmp+"&col="+$scope.current_collection.id;
                if($scope.ctrl.getLowestTaxa()!="-1")
                {
                    //urlTmp=urlTmp+"/"+$scope.ctrl.getLowestTaxa();
					urlTmp=urlTmp+"&taxon="+$scope.ctrl.getLowestTaxa();
                }
                else
                {
                    urlTmp=urlTmp+"&taxon=-1";
                }
				urlTmp=urlTmp+"&taxon=-1";
                return $http.get(urlTmp)
                        .then(function(response) {
                            $scope.collectors = response.data;
                        });
              //}
             //else
             //{
             //   $scope.collectors=null;
             //}             
             
    };

    
//Date

  $scope.todayBegin = function() {
    $scope.dateBegin = new Date();
  };
  //$scope.todayBegin();
  
    $scope.todayEnd = function() {
    $scope.dateEnd = new Date();
  };
  //$scope.todayEnd();

  $scope.clear = function () {
    $scope.dateBegin = null;
  };

  // Disable weekend selection
  $scope.disabled = function(date, mode) {
    return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
  };

  $scope.toggleMin = function() {
    $scope.minDate = $scope.minDate ? null : new Date();
  };
  $scope.toggleMin();

  $scope.openDateBegin = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDateBegin = true;
  };
  
  $scope.openDateEnd = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedDateEnd = true;
  };

  $scope.dateOptions = {
    formatYear: 'yyyy',
    startingDay: 1
  };

  
  $scope.format = 'dd-MMMM-yyyy';
//END Date

    $scope.toggleTypeSelection = function(param) { 
			//////console.log(param);
             if ($scope.selectedTypes.indexOf(param)>-1) 
            {           
               $scope.selectedTypes.splice($scope.selectedTypes.indexOf(param));
            }
            // is newly selected
            else 
            {
                
                $scope.selectedTypes.push(param)
            }
			//////console.log($scope.selectedTypes);
      };
      
  $scope.buildDate=function(day, month, year)
  {

    if(!angular.isDefined(month)||isNaN(month))
    {
        
        month=1;
    }
    if(month.length<2)
    {
        month="0".concat(month)
    }
    
    if(!angular.isDefined(day)||isNaN(day))
    {
       
        day=1;
    }
     if(day.length<2)
    {
        day="0".concat(day)
    }
    
    var tmpDate= moment(year.concat("-").concat(month).concat("-").concat(day)).format('YYYY-MM-DD');
    if(!moment(tmpDate,'YYYY-MM-DD').isValid())
    {
       
         $scope.ctrl.date.validationError = true;
         return -1;
    }
    else
    {
         $scope.ctrl.date.validationError = false;
         return tmpDate;
         
    }
    
  }  
 
  
  $scope.substitute_http=function(elem)
  {
	if(typeof collectors === 'string')
	 {
	  elem=elem.elem("&"," ").replace("="," ");
	 }
	 else if(Object.prototype.toString.call(elem) === '[object Array]')
	 {
		////console.log("is_array");
		elem=elem.map(function(x) {return x.replace("&"," ").replace("="," ");}
		)
	 }
	 return elem;
  }	
  
  $scope.getData = function(collection, taxas, spec_nr, countries, localities, collectors, coll_date_begin, coll_date_end, types, p_has_images, p_has_3d, north, south, west, east, wkt, p_georef_only) 
  {
     ////console.log(collectors);
	 collectors= $scope.substitute_http(collectors);
	 ////console.log(collectors);
	 countries= $scope.substitute_http(countries);
	 localities= $scope.substitute_http(localities);
	 
	 var urlTmp= $scope.url_prefix +'operation=search_specimen&collections='+collection+'&taxas='+taxas+'&number='+spec_nr+'&countries='+countries+'&localities='+localities+'&collectors='+collectors+'&gathering_begin='+coll_date_begin+'&gathering_end='+coll_date_end+'&types='+types+'&has_images='+p_has_images+'&has_3d='+p_has_3d+'&north='+north+'&south='+south+'&west='+west+'&east='+east+'&wkt='+wkt+"&georef_only="+p_georef_only;
       
	   var urlTmpGeoRef= $scope.url_prefix +'operation=count_georef_specimen&collections='+collection+'&taxas='+taxas+'&number='+spec_nr+'&countries='+countries+'&localities='+localities+'&collectors='+collectors+'&gathering_begin='+coll_date_begin+'&gathering_end='+coll_date_end+'&types='+types+'&has_images='+p_has_images+'&has_3d='+p_has_3d+'&north='+north+'&south='+south+'&west='+west+'&east='+east+'&wkt='+wkt+"&georef_only="+p_georef_only;
	   
	   
	   
	   //to next page there
	     $scope.keep_url_georef(urlTmpGeoRef);
		 $scope.keep_url(urlTmp);
		 
         //$scope.setPage(1,urlTmp);
        
       
    
    }   




  $scope.open = function (nbspec) 
  {


    var modalInstance = $modal.open({
      templateUrl: 'detail_specimen',
      controller: 'ModalInstanceCtrl',
      size: 'lg',
      resolve: {
        items:  function () {
          return [nbspec];
        }
      }
    });
   };
   
   //MAIN_SEARCH
     $scope.get_specimens = function() {
    //////console.log("search");

	//collection
    
    var colls=$scope.current_collection.id;
		//taxon (un seul pour tous les rangs)=> appel à une fonction et non appel ctrl direct
	   
		var taxas=$scope.ctrl.getLowestTaxa();
		//specimen number
		
		var numbersTmp=$scope.ctrl.sp_num.selected;
		////console.log("numbers=");
	   ////console.log(numbersTmp);
	   numbers='-1';
	   numbersTmp= $scope.substitute_http(numbersTmp);
	   if(Object.prototype.toString.call(numbersTmp) === '[object Array]')
	   {
			numbers=numbersTmp.join("|");
	   }
	   
	   
	   /*
		if(numbersTmp===undefined)
		{
			numbers='-1';
		}

		if( numbersTmp.value===undefined)
		{
			numbers='-1';
		}
		else
		{
			numbers=numbersTmp.value;
		}*/
		
	  //  alert(numbers);
		//countries
		var countries=$scope.ctrl.country.selected
	//localities
	//alert(countries);
		
		var localities=$scope.ctrl.locality.selected;
		//alert(localities);
	//collectors
		
		var collectors=$scope.ctrl.collector.selected;
		  //  alert(collectors);
	   
		var startDateVar='-1';

		if(angular.isDefined($scope.ctrl.beginYear))
		{
		
			startDateVar=$scope.buildDate($scope.ctrl.beginDay, $scope.ctrl.beginMonth, $scope.ctrl.beginYear);
		}
	   
		

		var endDateVar='-1';
		if(angular.isDefined($scope.ctrl.endYear))
		{
		
			endDateVar=$scope.buildDate($scope.ctrl.endDay, $scope.ctrl.endMonth, $scope.ctrl.endYear);
		}
		//alert(endDateVar);
	   
		
		//types
		var types="-1";
	   // alert(types.length); 
	   //////console.log($scope.selectedTypes);
		if($scope.selectedTypes.length>0)
		{
			types=$scope.selectedTypes;
		}
	   //alert(types);
	   // alert(types);
		//image
	   var has_image=false;
	   var has_3d=false;
	   var georef_only=false;
	   //alert($scope.checkboxModel);
	   if(angular.isDefined($scope.checkboxModel))
	   {
		   if(angular.isDefined($scope.checkboxModel.has_image))
		   {
			if($scope.checkboxModel.has_image===true)
			{
				has_image=true;
			}
		   }
		   
		   if(angular.isDefined($scope.checkboxModel.has_3d))
		   {
			 if($scope.checkboxModel.has_3d===true)
			{
				has_3d=true;
			}
		   }
		   
		   if(angular.isDefined($scope.checkboxModel.georef_only))
		   {
			 if($scope.checkboxModel.georef_only===true)
			{
				georef_only=true;
			}
		   }
		}
		 //3D     
		//alert("images")
		
		
	   
		//alert("image");
		//alert(has_image);
		//alert("3d");
		//alert(has_3d);
		
	   var north=$scope.selectionN;
	   var south=$scope.selectionS;
	   var west=$scope.selectionW;
	   var east=$scope.selectionE;

	   var wkt=$scope.wkt;
		$scope.getData(colls, taxas, numbers, countries, localities, collectors, startDateVar, endDateVar, types, has_image, has_3d, north, south, west, east, wkt, georef_only);
	

  }
   
   //END MAIN_SEARCH

   var lang=DarwinFactory.getHTTPParam("lang");
   var collection=DarwinFactory.getHTTPParam("collection");
   var p_frame=DarwinFactory.getHTTPParam("frame");
   var p_type=DarwinFactory.getHTTPParam("types");
   
   
   if(lang!="")
   {
	if(lang=="en"||lang=="fr"||lang=="nl")
    {
        $scope.ctrl.setLanguage(lang);
    }
   }
   if(collection!="")
   {
	  $scope.collection_by_url=collection;
	 //console.log($scope.collection_by_url);
     DarwinFactory.getCollectionID($scope.collection_by_url).then(
        function(response)
        {
			
            if((response.data.length)>0)
            {
                $scope.current_collection.id=response.data[0].id;
				//console.log("Hide_collection");
				$scope.show_collection_list=false;
				//////console.log($scope.current_collection.id);
				DarwinFactory.getSubCollectionByID( $scope.current_collection.id).then(
					 function(response2)
					{
						//scope.collections=response2;
						//console.log("====>");
						//console.log(response2.data);
						$scope.collections=response2.data;
						for(var i=0; i<$scope.collections.length; i++ )
						{
							if($scope.collections[i].id==$scope.current_collection.id)
							{
								name=$scope.collections[i].name;
								$scope.current_collection=$scope.collections[i];
							}
						} 
					}
				);
				/*for(var i=0; i<$scope.collections.length; i++ )
				{
					if($scope.collections[i].id==$scope.current_collection.id)
					{
						name=$scope.collections[i].name;
						$scope.current_collection=$scope.collections[i];
					}
				} */
				/*DarwinFactory.getTypes($scope.current_collection.id).success(
					function(response)
					{
						$scope.types=response;
						
					}
				);		*/
				
				//$scope.types={"name":"types", "name":"non-type"};
           }
          
        }
    );
   }
   else
   {
	   
	   //console.log("NO_COLL");
   }
   $scope.types=[{"name":"types"}, {"name":"non-type"}];
   /*else
   {
		DarwinFactory.getTypes().success(
					function(response)
					{
						$scope.types=response;
						
					}
				);		
   }*/
   if(p_frame=="true")
   {
	  $scope.frame=true;
	   $scope.detail_url_current=$scope.detail_url_frame;
   }
   
   if(p_type.toLowerCase()=="on")
   {
		$scope.selectedTypes =["types"];
   }

  
  $scope.ctrl.coordDDToDMS=function(coord)
  {
    coord=Math.abs(coord);
    var tmpDeci=coord-Math.floor(coord);
    var tmpMinute=Math.floor(tmpDeci*60);
    var tmpDeci2=tmpDeci-(tmpMinute/60);
    var tmpSeconde=Math.floor(tmpDeci2*3600);
    return Math.floor(coord).toString().concat('\xB0').concat(tmpMinute).concat("'").concat(tmpSeconde).concat('"');
  }

});
