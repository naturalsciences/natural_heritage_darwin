
darwinApp.controller('ModalInstanceCtrl', function ($scope, $modalInstance, items, DarwinFactory, $location, $http, $translate, $timeout,  $anchorScroll, collection_by_url, tmhDynamicLocale) {

  $scope.items = items;
  $scope.ctrl={};

 $scope.collection_by_url=collection_by_url;

   $scope.getCallingDomain=function()
  {
  
    return $location.host()
  };
  
  $scope.getStableIdentifierUrl= function()
  {
    var returned="";
	
    if ($scope.getCallingDomain()=="darwinweb.africamuseum.be")
    {
        returned="https://"+$scope.getCallingDomain()+"/object/"+ $scope.ctrl.id_spec_tmp;
    }
	////console.log(returned);
    return returned;
  };
  
   $scope.ctrl.setLanguage = function(code) {
       $scope.ctrl.language=code;
       $translate.use($scope.ctrl.language);

        tmhDynamicLocale.set($scope.ctrl.language);
        
    };
 
  $scope.cancel = function () {
    $modalInstance.dismiss('cancel');
  };
  
  $scope.closeModal = function(){
       
       $modalInstance.close();
       $scope.$emit('detail_closed');
    }
       
       $scope.splitSQLToArray=function(str)
       {
            return str.replace(/({\"|\"})/g,'').split('","');
       };
       
       $scope.assign=function(obj, keyPath, value) {
           lastKeyIndex = keyPath.length-1;
           for (var i = 0; i < lastKeyIndex; ++ i) {
             key = keyPath[i];
             if (!(key in obj))
               obj[key] = {}
             obj = obj[key];
           }
           obj[keyPath[lastKeyIndex]] = value;
           return obj;
        }
       
        $scope.getSpecimen=function(num)
       {
            DarwinFactory.getSpecimen(num).then(
                function(response)
                {
                    $scope.specimen=response;
          
                }
            );
            
           
       }       

       if($scope.items.length>=1)
       {
     
			////console.log("try 1");
			////console.log($scope.items);
             //languages
             
              $scope.ctrl.language = 'en';
              $scope.ctrl.languages = ['en', 'nl', 'fr'];
              $scope.ctrl.updateLanguage = function() {
                $translate.use($scope.ctrl.language);
            };
            $scope.ctrl.id_spec_tmp=$scope.items[0];
            $scope.getSpecimen($scope.ctrl.id_spec_tmp);
             if($location.host()=="193.190.223.5")
             {
                 $scope.page_specimen="http://193.190.223.5/collections/browsecollections/naturalsciences/biology/"+ $scope.collection_by_url+"/darwin_specimen?uuid_spec="+$scope.ctrl.id_spec_tmp;
             }
             else
             {
                $scope.page_specimen="page_specimen/"+$scope.ctrl.id_spec_tmp;
             }
                      
          
       }
});

darwinApp.controller('darwin-detail-controller', function($scope,  DarwinFactory, $location, $http, $translate, $timeout, tmhDynamicLocale)
{

    $scope.ctrl={};
    $scope.collapseMap=true;
    $scope.specimen={};
    $scope.specimen.geoJSON="";
    $scope.goMap=false;
    $scope.specimen.image_obj={};
	$scope.url_prefix="./dw_public_angular/ws/ws.php?";
	$scope.url_test_virtual_col="https://virtualcol.africamuseum.be/proxy_iiif/collective_access_html.php";
	$scope.url_virtual_col="https://virtualcol.africamuseum.be/proxy_iiif/collective_access_html.php?uuid=";
	$scope.url_virtual_col_complete="";
	$scope.url_virtual_col_iiif="https://virtualcol.africamuseum.be/proxy_iiif/collective_access_iiif.php?uuid=";
	$scope.url_virtual_col_iiif_complete="";
	$scope.show_image=false;
	$scope.frame=false;
	
       var path=$location.path();
       path=path.match(/[^\/]+/g);
       
       $scope.ctrl.setLanguage = function(code) {
       $scope.ctrl.language=code;
       $translate.use($scope.ctrl.language);

            tmhDynamicLocale.set($scope.ctrl.language);
        
        };
        
       $scope.getSpecimen=function(num)
       {
			//////console.log("call");
		    //////console.log(num);
            DarwinFactory.getSpecimen(num).then(
                function(response)
                {
			
                    $scope.specimen=response;

					if(!angular.isUndefined($scope.specimen.latitude)&& !angular.isUndefined($scope.specimen.longitude))
					{
						if($scope.specimen.latitude !==null&& $scope.specimen.longitude!==null )
						{
							$scope.createGeoJSON($scope.specimen.latitude,$scope.specimen.longitude );
							 $scope.goMap=true;
							 
						}
					}
					$scope.setImageUrl();
                   
                }
            );
            
           
       }       
       

     $scope.collapseMapSwitch=function()
     {
        $scope.collapseMap=!$scope.collapseMap;
     }
     
     
     

     
     $scope.createGeoJSON=function(tmpLatitude,tmpLongitude)
    {
       
        //alert(length);
        var i=0;
        var featureMain='{ "type": "FeatureCollection","features": [';

            
            var tmpFeature='{ "type": "Feature","geometry": {"type": "Point", "coordinates": [';
            
            
            //attention croisement de coordonénes json OpenLayers 
            var latitude=tmpLongitude;
            var longitude=tmpLatitude;
           
            tmpFeature=tmpFeature+latitude+", "+longitude;
            tmpFeature=tmpFeature+']},"properties": {';
            tmpFeature=tmpFeature+'}';
            tmpFeature=tmpFeature+'}';
            

            featureMain=featureMain+tmpFeature;
        
        //totalItems=items[0].full_count;
         featureMain=featureMain+']}';
         $scope.specimen.geoJSON=featureMain;
    };
    
  $scope.getCallingDomain=function()
  {
  
    return $location.host()
  };
  
  $scope.getStableIdentifierUrl= function()
  {
    var returned="";
	
    if ($scope.getCallingDomain()=="darwinweb.africamuseum.be")
    {
        returned="https://"+$scope.getCallingDomain()+"/object/"+ $scope.ctrl.id_spec_tmp;
    }
	//////console.log(returned);
    return returned;
  };
  
  $scope.getStableIdentifierUrlHTML= function()
  {
    var returned="";
    if ($scope.getCallingDomain()=="darwinweb.africamuseum.be")
    {
        returned="<b>Stable CETAF identifier (permalink): </b> https://"+$scope.getCallingDomain()+"/object/"+ $scope.ctrl.id_spec_tmp;
    }
    return returned;
  };
  
  $scope.getImageUrl= function()
  {
	  
	  return $scope.url_virtual_col_complete;
  }
  
  $scope.getIIIFUrl= function()
  {
	  
	  return $scope.url_virtual_col_iiif_complete;
  }
  
  
    $scope.setImageUrl= function()
  {
    
    if ($scope.getCallingDomain()=="darwinweb.africamuseum.be")
    {
        
		var uuid=$scope.ctrl.id_spec_tmp;
		var base_url=$scope.url_test_virtual_col;
		
		
		
		$.ajax({
			  url  : base_url,
			  type : 'get',
			  data : {"uuid":uuid},
			  error :function (xhr, ajaxOptions, thrownError){
						if(xhr.status==404) 
						{
							
							$scope.show_image=false;
							
						}
					},
			}).done(
				function() {
				 
				  $scope.url_virtual_col_complete=$scope.url_virtual_col+$scope.ctrl.id_spec_tmp;
				  $scope.url_virtual_col_iiif_complete=$scope.url_virtual_col_iiif+$scope.ctrl.id_spec_tmp;
				  //////console.log( $scope.url_virtual_col_complete.length);
				  if( $scope.url_virtual_col_complete.length>0)
				  {				  
				  //////console.log("TRUE");
					$scope.show_image=true;
				  }
				  else
				  {
				  				  //////console.log("FALSE");
					$scope.show_image=false;
				  }
				  $scope.$apply();
				}
			);
    }
    
  };
  
   var lang=DarwinFactory.getHTTPParam("lang");
   var uuid=DarwinFactory.getHTTPParam("uuid");
   //////console.log(uuid);
   if(uuid!="")
   {
		$scope.ctrl.id_spec_tmp=uuid;
         $scope.getSpecimen($scope.ctrl.id_spec_tmp);
		 
   }
   else
   {
	  
	   var url=window.location.href;
	   var elems=url.split("/");
	   var uuid=elems[elems.length-1];
	   
	   var check_uuid=/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(uuid);
	   if(check_uuid)
	   {
			$scope.ctrl.id_spec_tmp=uuid;
		   $scope.getSpecimen($scope.ctrl.id_spec_tmp);
	   }
   }	   
   if(lang=="")
   {
		lang="en";
   }
   var p_frame=DarwinFactory.getHTTPParam("frame");
   if(p_frame=="true")
   {
	  $scope.frame=true;
   }
   $scope.ctrl.language = lang;
   $scope.ctrl.languages = ['en', 'nl', 'fr'];
   $scope.ctrl.setLanguage($scope.ctrl.language);
   
             
/*
       if(path.length==2)
       {

			//////console.log("try 2")
              //languages
             
              $scope.ctrl.language = 'en';
              $scope.ctrl.languages = ['en', 'nl', 'fr'];
              $scope.ctrl.setLanguage($scope.ctrl.language);
            $scope.ctrl.id_spec_tmp=path[1];
            $scope.getSpecimen($scope.ctrl.id_spec_tmp);
            
          
          
       }
       else if(path.length==3)
       {

       //////console.log("try 3")
	   //////console.log(path);
              //languages
             $scope.ctrl.language = "en";
              if(path[1]=="en"||path[1]=="fr"||path[1]=="nl")
              {
                     $scope.ctrl.language = path[1];
              }
              $scope.ctrl.languages = ['en', 'nl', 'fr'];
              
              $scope.ctrl.setLanguage($scope.ctrl.language);
            
            $scope.ctrl.id_spec_tmp=path[2];
            $scope.getSpecimen($scope.ctrl.id_spec_tmp);
            
          
          
       }*/
       
       

});