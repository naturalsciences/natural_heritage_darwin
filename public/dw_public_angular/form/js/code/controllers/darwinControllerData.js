darwinApp.controller('darwin-controller-data', function($scope, DarwinFactory, PagerService, $location, $http, $translate, $timeout, $modal, $locale, tmhDynamicLocale,$sanitize, storageFactory)
{
	$scope.detail_url="https://darwin.naturalsciences.be/dw_public/form/specimen_detail.html?uuid=";
	$scope.detail_url_frame="https://darwin.naturalsciences.be/dw_public/form/specimen_detail.html?uuid=";
	$scope.detail_url_current=$scope.detail_url;

   $scope.ctrl={};
	$scope.url="";
	$scope.currentSearchURL="";
	$scope.pageSize=25;
	$scope.nb_pages="0";

	$scope.currentPage=1;  
	$scope.sortOrder="-1";
	$scope.pager = {};  
	$scope.items= {};
	$scope.pages_specimen={}; 
	$scope.geo_ref_in_page=0;
	$scope.geo_ref_in_dataset=0;
	
	$scope.ctrl.current_sort_order="specimen_number";
	$scope.ctrl.sort_criteria={}
	$scope.ctrl.sort_criteria[$translate.instant('SPECIMEN_NUMBER')]="specimen_number";
	$scope.ctrl.sort_criteria[$translate.instant('COUNTRIES')]="country";
	$scope.ctrl.sort_criteria[$translate.instant('TAXON_NAME')]="taxon_name"
	$scope.ctrl.sort_criteria[$translate.instant('FAMILY')]="family";
	$scope.ctrl.sort_criteria[$translate.instant('START_COLLECTING_DATE')]="collecting_date";
	$scope.ctrl.sort_criteria[$translate.instant('LATITUDE')]="latitude";
	$scope.ctrl.sort_criteria[$translate.instant('LONGITUDE')]="longitude";
	////console.log($scope.ctrl.sort_criteria)
	 //languages
  $scope.ctrl.language = 'en';
  $scope.ctrl.languages = ['en', 'nl', 'fr'];
  $scope.ctrl.updateLanguage = function() {
  $translate.use($scope.ctrl.language);
        //synchronized with the name of tiles in the i18n javascript folder in "common"
  tmhDynamicLocale.set($scope.ctrl.language);
  $scope.ctrl.show_map=true;
  $scope.ctrl.sort_direction="ascending";     
 };
    
  $scope.ctrl.setLanguage = function(code) {
       $scope.ctrl.language=code;
       $translate.use($scope.ctrl.language);

        tmhDynamicLocale.set($scope.ctrl.language);
        
    };
  
    $scope.changeSortOrder=function()
	{
		////console.log("CHANGED");
		////console.log($scope.ctrl.current_sort_order);
		$scope.getPage(1);
		
	}
	
     $scope.getGeoRefInPage=function()
	{
		////console.log($scope.geo_ref_in_page);
		return $scope.geo_ref_in_page;	
		
	}
	
	  $scope.getGeoRefInDataset=function()
	{
		////console.log($scope.geo_ref_in_dataset);
		return $scope.geo_ref_in_dataset;	
		
	}
	
	 var createGeoJSON=function(items)
    {
		
        var length=items.length;
        //alert(length);
        var i2=0;
		$scope.geo_ref_in_page=0;
        var featureMain='{ "type": "FeatureCollection","features": [';
        for(i=0; i<length;i++)
        {
            //console.log("geojson item");
			//console.log(items[i]);
           
            //attention croisement de coordonénes json OpenLayers 
			if(i==0)
			{
				
				$scope.geo_ref_in_dataset=items[i].georef_count;
			}
            var latitude=items[i].longitude;
            var longitude=items[i].latitude;
           
                           
            if(latitude !==null&& !angular.isUndefined(latitude)&&longitude!==null&& !angular.isUndefined(longitude))
            {
				$scope.geo_ref_in_page+=1;
                var tmpFeature='{ "type": "Feature","geometry": {"type": "Point", "coordinates": [';
            
                var label=items[i].code_display;
                var taxon=items[i].taxon_name;
				
				if(label !== undefined)
				{
					label=label.replace(/\n/g, ' ');
					label=label.replace(/\r/g, ' ');
				}
				if(taxon !== undefined)
				{
					taxon=taxon.replace(/\n/g, ' ');
					taxon=taxon.replace(/\r/g, ' ');
				}
				
				var uuid=items[i].uuid;
                tmpFeature=tmpFeature+latitude+", "+longitude;
                tmpFeature=tmpFeature+']},"properties": {';
				tmpFeature=tmpFeature+'"uuid":"'+uuid+'", ';
                tmpFeature=tmpFeature+'"code_display":"'+label+'", ';
                tmpFeature=tmpFeature+'"taxon":"'+taxon+'", ';
                tmpFeature=tmpFeature+'"latitude":"'+latitude+'", ';
                tmpFeature=tmpFeature+'"longitude":"'+longitude+'" ';
                tmpFeature=tmpFeature+'}';
                tmpFeature=tmpFeature+'}';
                if(i2>0)
                {
                      featureMain=featureMain+",";
                }
                featureMain=featureMain+tmpFeature;
                i2++;
            }
        }
        //totalItems=items[0].full_count;
         featureMain=featureMain+']}';
         $scope.geoJSON=featureMain;
		 //////console.log($scope.geoJSON);
		 ////console.log($scope.geo_ref_in_page);
		 if($scope.geo_ref_in_page>0)
		 {
			 $scope.ctrl.show_map=true;
		 }
		 else
		 {
			 $scope.ctrl.show_map=false;
		 }
    };
	
	$scope.displayNbPages=function()
	{
		return $scope.nb_pages;
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
            else if($location.host()=="darwin.naturalsciences.be")
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
				////console.log(obj.id);
				////console.log(obj.uuid);
                $scope.pages_specimen[obj.id]="page_specimen/"+obj.uuid;
            }
          }
		  ////console.log($scope.pages_specimen);
      }
	  
	  
 	  
   $scope.getPage =function(page)
  {
    return $scope.setPage(page,$scope.currentSearchURL );
  }
	
	 $scope.setPage= function (page, urlQuery) {
		 ////console.log(urlQuery);
        $scope.currentSearchURL=urlQuery;
       /*if (page < 1 || page > $scope.pager.totalPages) {
                return;
          }*/
		  
		 if($scope.ctrl.current_sort_order!="")
		 {
			 
			 urlQuery=urlQuery+"&sort_order="+$scope.ctrl.current_sort_order;
		 }
		  
		  ////console.log($scope.ctrl.sort_direction);
		 if($scope.ctrl.sort_direction=="descending")
		 {
			 
			 urlQuery=urlQuery+"&sort_direction=descending";
		 }
		 else
		 {
			urlQuery=urlQuery+"&sort_direction=ascending";
		 }
		 ////console.log(urlQuery);
        // get pager object from service
		//////console.log();
        var pagePromise = PagerService.GetPager(urlQuery, page, $scope.pageSize, $scope.sortOrder="-1");
		pagePromise.then(function(result) {
		   //////console.log("received");
			$scope.pager=result;
		   if(typeof $scope.pager.totalPages !=='undefined')
		  {
           if (page < 1 || page > $scope.pager.totalPages) {
			   //////console.log(page);
			   //////console.log($scope.pager.totalPages);
			   //////console.log("issue3");
                return;
			}
			else
		  {
			  //////console.log("issue2");
		  }
		  }
		  else
		  {
			  //////console.log("issue1");
		  }

      
  
        $scope.items= $scope.pager.items;
		$scope.nb_pages=Math.ceil($scope.pager.totalItems / $scope.pager.pageSize);
         $scope.setLinks();
        createGeoJSON($scope.items);
		////console.log( $scope.items);
		
    });
         
          
      };

	$scope.url="";
	$scope.init = function () {
	
	
		////console.log("START");
	    $scope.url= storageFactory.get("search_url");
		//$scope.url_georef= storageFactory.get("search_url_georef");
		////console.log($scope.url);
		$scope.setPage(1,$scope.url);
		/*$http.get($scope.url_georef).then(function(response) {

                   	 ////console.log("COUNT_GEO_REF");
						////console.log(response.data[0].count_geo);
						$scope.geo_ref_in_dataset=response.data[0].count_geo;
                    	});;*/
		
	};
	
	//initialisation zone
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
   
    if(p_frame=="true")
   {
	  $scope.frame=true;
	   $scope.detail_url_current=$scope.detail_url_frame;
   }
   
   if(p_type.toLowerCase()=="on")
   {
		$scope.selectedTypes =["types"];
   }

   
	$scope.init();
	

});
