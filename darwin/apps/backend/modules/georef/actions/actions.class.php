<?php

class georefActions extends DarwinActions
{

	//https://nominatim.openstreetmap.org/search?q=kinshasa&format=json&extratags=1&countrycodes=cd&polygon_geojson=1
	//e.g : https://darwin.naturalsciences.be/darwin/backend_dev.php/georef/query_nominatim?q=kinshasa
	/*
	public function executeQuery_nominatim(sfWebRequest $request)
	{
		$results=Array();
		if($request->hasParameter('q') )
		{
			$tag=$request->getParameter('q');
        
		}
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results));
	}*/
	
	public function executeIndex(sfWebRequest $request)
	{
		$this->form = new GeorefFormFilter();
		$this->form->addValue(0);
	}
	
	public function executeNew(sfWebRequest $request)
	{
		$georef = new Georef() ;
		
		$this->name="";
		$this->country_iso3166="";
		if ($request->hasParameter('name'))
		{
			$this->name=$request->getParameter('name');
			$georef->setName($this->name);
			
		}
		if ($request->hasParameter('country_iso3166'))
		{
			$this->country_iso3166=$request->getParameter('country_iso3166');
			$georef->setCountryIso3166($this->country_iso3166);
			
		}
		$this->form = new GeorefForm($georef);
	}
	
	public function executeEdit(sfWebRequest $request)
	{
		
		$georef = new Georef() ;
		$this->v_georef=null;
		if ($request->hasParameter('id'))
		{
			$this->id=$request->getParameter('id');
			if(is_numeric($this->id))
			{
				$this->v_georef=Doctrine_Core::getTable('VGeorefForInterface')->findOneById($this->id) ;
				$georef->setId($this->v_georef->getId());
				$georef->setSource($this->v_georef->getSource());
				$georef->setName($this->v_georef->getName());
				$georef->setCountryIso3166($this->v_georef->getCountryIso3166());
				$georef->setOsmId($this->v_georef->getOsmId());
				$georef->setGeonamesId($this->v_georef->getGeonamesId());
				$georef->setOsmId($this->v_georef->getOsmId());
				$georef->setGeonamesId($this->v_georef->getGeonamesId());
				$georef->setWikidataId($this->v_georef->getWikidataId());
				$georef->setOsmJson($this->v_georef->getOsmMetadataJson());
				$georef->setOsmUrl($this->v_georef->getOsmUrl());
				$georef->setOsmPlaceRank($this->v_georef->getOsmPlaceRank());
				$georef->setEpsg($this->v_georef->getEpsg());
				
				$georef->setCreationDate($this->v_georef->getCreationDate());
				$this->geojson=$this->v_georef->getGeojsonFromGeom();
				$items_ids = $this->getUser()->getAllPinned('gtu');
				$this->items=Doctrine_Core::getTable('DoctrineTemporalInformationGtuGroupTags')->getByMultipleIds($items_ids);
				$this->linked_items=Doctrine_Core::getTable('GtuToGeoref')->getGtuByGeorefRelated($this->id);

				//$this->georef=$georef;
				$this->form = new GeorefForm($georef);
				return;
			}
			
		}
		
		$this->forward404();
	}
	
	public function executeSaveNominatimGeoRef(sfWebRequest $request)
	{
		$returned=Array();
		
		$geojson_osm_tmp=$request->getParameter("geojson_osm", null);
		$geojson_object=null;
		$metadata_osm_tmp=$request->getParameter("metadata_osm", null);
		$metadata_object=null;
		if($geojson_osm_tmp !==null)
		{
			
			$geojson_osm=json_decode($geojson_osm_tmp, $assoc = true);
			$geojson_object=$geojson_osm_tmp;
			
		}
		else
		{
			$geojson_osm=[];
		}
		if($metadata_osm_tmp !==null)
		{
			$metadata_osm=json_decode($metadata_osm_tmp, $assoc = true);
			$metadata_object=$metadata_osm_tmp;
		}
		else
		{
			$metadata_osm=[];
		}
		$osm_id=$request->getParameter("osm_id", -1);
		
		$osm_geotype=$request->getParameter("osm_geotype", "");
		$osm_class=$request->getParameter("osm_class", "");
		$osm_type=$request->getParameter("osm_type", "");
		$osm_name=$request->getParameter("osm_name", "");
		$osm_place_rank=$request->getParameter("osm_place_rank", -1);
		
		$returned=[
			"osm_id"=> $osm_id,
			"osm_geotype"=> $osm_geotype,
			"osm_class"=> $osm_class,
			"osm_type"=> $osm_type,
			"osm_place_rank"=> $osm_place_rank,
		];
		$returned["dw_status"]="not_saved";
		
		if($osm_id !=-1 && count($geojson_osm)>0 && $osm_name !="")
		{
			try
			{			
				$georef=new Georef();
				$georef->setOsmId($osm_id );
				$georef->setSource("OSM");
				$georef->setName($osm_name);
				if(strlen($osm_geotype)>0)
				{
					$georef->setOsmGeoType($osm_geotype );
				}
				if(strlen($osm_class)>0)
				{
					$georef->setOsmClass($osm_class );
				}
				if(strlen($osm_type)>0)
				{
					$georef->setOsmType($osm_type );
				}
				if($osm_place_rank>0)
				{
					$georef->setOsmPlaceRank($osm_place_rank );
				}
				if($metadata_object!==null)
				{
					$georef->setOsmMetadataJson($metadata_object );
				}
				$georef->setOsmJson($geojson_object );
				$georef->save();
				$returned["dw_status"]="saved";
			}
			catch(Doctrine_Exception $ne)
			{
			  $e = new DarwinPgErrorParser($ne);
			  $returned["dw_status"]="doctrine_exception";
			  $returned["dw_message"]=$e->getMessage();
			}
			catch(Exception $e)
			{			 
			  $returned["dw_status"]="doctrine_exception";
			  $returned["dw_message"]=$e->getMessage();
			}
		}
		
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($returned));
		
		
	}
	
	public function executeGet_json(sfWebRequest $request)
	{
		$returned=Array();
		$id=null;
		if($request->hasParameter('id') )
		{
			$id=$request->getParameter('id');
        
		}
		if(!is_numeric($id))
		{
			$id=null;
		}
		if($id!==null)
		{
		$tmp=Doctrine_Core::getTable('VGeorefForInterface')->findOneById($id) ;
			if($tmp!==null)
			{
				
				$returned["id"]=$tmp->getId();
				$returned["source"]=$tmp->getSource();
				$returned["name"]=$tmp->getName();
				$returned["country"]=$tmp->getCountry();
				$returned["country_iso3166"]=$tmp->getCountryIso3166();
				$returned["osm_id"]=$tmp->getOsmId();
				$returned["geonames_id"]=$tmp->getGeonamesId();
				$returned["wikidata_id"]=$tmp->getWikidataId();
				$returned["osm_url"]=$tmp->getOsmUrl();
				$returned["osm_metadata_json"]=json_decode($tmp->getOsmMetadataJson());
				$returned["osm_geo_type"]=$tmp->getOsmGeoType();
				$returned["osm_class"]=$tmp->getOsmClass();
				$returned["osm_type"]=$tmp->getOsmType();
				$returned["osm_place_rank"]=$tmp->getOsmPlaceRank();
				$returned["epsg"]=$tmp->getEpsg();
				$returned["srid_from_geom"]=$tmp->getSridFromGeom();
				$returned["geojson"]=json_decode($tmp->getGeojsonFromGeom());
				$returned["creation_date"]=$tmp->getCreationDate();
			}
		}
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($returned));
		
	}
	
	
	public function executeAddGtuToGeoref(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $georef_ref = intval($request->getParameter('georef_ref'));
	$gtu_ref = intval($request->getParameter('gtu_ref'));
	$georef_ref_id = intval($request->getParameter('georef_ref_id', -1));
    $this->form = new GeorefForm();
    $this->form->addGtuToGeoref($number,array('gtu_ref'=>$gtu_ref, 'georef_ref'=> $georef_ref),$request->getParameter('iorder_by',0));
	$item=Doctrine_Core::getTable('DoctrineTemporalInformationGtuGroupTags')->findOneById($gtu_ref);

    return $this->renderPartial('linked_item_gtu',array('form' =>  $this->form['newGtuToGeoref'][$number], 'row_num'=>$number, "item"=>$item, "georef_ref_id"=> $georef_ref_id));
  }
  

  public function  executeGetExistingGtuToGeoref(sfWebRequest $request)
  {
	  
	   $georef_ref = intval($request->getParameter('georef_ref', -1));
	
	   $returned=Array();
	   
	  if($georef_ref >=0)
	  {
		  $items=Doctrine_Core::getTable('GtuToGeoref')->getGtuByGeorefRelated($georef_ref);
		 
		  foreach($items as $item)
		  {
			  
			  $returned[]=["gtu_ref"=>$item->getGtuRef(), "id"=> $item->getId()];
		  }
	  }
	   $this->getResponse()->setContentType('application/json');
	  return  $this->renderText(json_encode(Array("returned"=>$returned)));
  }
  
  public function executeCreateGeorefGtuLink(sfWebRequest $request)
  {
	  $georef_ref = intval($request->getParameter('georef_ref', -1));
	  $gtu_ref = intval($request->getParameter('gtu_ref', -1));
	  if($gtu_ref>=0&&$georef_ref >=0)
	  {
		  $items=Doctrine_Core::getTable('GtuToGeoref')->getGtuByGeorefFullyRelated($georef_ref, $gtu_ref);
		  if(count($items)==0)
		  {
			  $link=new GtuToGeoref();
			  $link->setGeorefRef($georef_ref);
			  $link->setGtuRef($gtu_ref);
			  $link->save();
			  $this->getResponse()->setContentType('application/json');
			  return  $this->renderText(json_encode(Array("status"=>"recorded")));
			  
		  }
		  else
		  {
				$this->getResponse()->setContentType('application/json');
			    return  $this->renderText(json_encode(Array("status"=>"already_exists")));
		  }
	  }
	  $this->getResponse()->setContentType('application/json');
	  return  $this->renderText(json_encode(Array("status"=>"not_created")));
	  
  }
  
  function executeDelete_relation(sfWebRequest $request)
  {
	  $georef_ref = intval($request->getParameter('id', -1));
	  $params=["deleted"=> false];
	  if($georef_ref!=-1)
	  {
		   $item=Doctrine_Core::getTable('GtuToGeoref')->findOneById($georef_ref);
		   if($item!==null)
		   {
			   $item->delete();
			   $params=["deleted"=> true];
		   }
	  }
	  return  $this->renderText(json_encode($params));
  }

	
	

}