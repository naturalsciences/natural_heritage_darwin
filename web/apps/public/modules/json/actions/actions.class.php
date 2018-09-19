<?php
  error_reporting(E_ERROR | E_PARSE);
/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class jsonActions extends DarwinActions
{
	protected $path_query="json/get_collections_catalogue";
    protected $path_query_detail="json/get_collection_detail";
	
	public function getCollArray($coll_obj, $recurs_parent=TRUE, $recurs_child=TRUE)
	{
		$returned=Array();
		//= Doctrine_Core::getTable("Collections")->findOneById($id);
		if(is_object($coll_obj))
        {	
			if(is_int($coll_obj->getId()))
			{
				$main_coll=Array();
				$main_coll["id"]=$coll_obj->getId();
				$main_coll["code"]=$coll_obj->getCode();
				$main_coll["name"]=$coll_obj->getName();
				$main_coll["uri"]=sfContext::getInstance()->getController()->genUrl('@homepage', true).$this->path_query_detail."/id/".$coll_obj->getId();
				
				$parent=$coll_obj->getParent();
				if(is_object($parent)&&$recurs_parent===TRUE)
				{
					$main_coll["parent_collection"]=$this->getCollArray($parent,FALSE, FALSE);
				}
				
				if($recurs_child===TRUE)
				{
					$children=Doctrine_Core::getTable("Collections")->findByParentRef($coll_obj->getId());
					//print(count($childs));
					$sub_collections=Array();
					$i=0;
					$iter=$children->getIterator();
					foreach($iter as $key=>$sub_coll)
					{
						$sub_collections[]=$this->getCollArray($sub_coll, false);
						$i++;
					}
					if($i==0)
					{
							$sub_collections=null;
					}
					$main_coll["sub_collections"]=$sub_collections;
				}
			}
			$returned=$main_coll;
			
        }
		return $returned;		
	}
	
	public function executeGet_collections_catalogue(sfWebRequest $request)
	{
		$results = Array();
		$coll_obj=null;
		$id=-1;
        if($request->hasParameter('id'))
        {
            $id= $request->getParameter('id');
			$coll_obj= Doctrine_Core::getTable("Collections")->findOneById($id);
        }
        elseif($request->hasParameter('code'))
        {
            $coll_obj= Doctrine_Core::getTable("Collections")->findOneByCode($request->getParameter('code'));
            if(is_object($coll_obj))
            {
                 $id= $coll_obj->getId();
            }
            
        }
		else
		{
			$children=Doctrine_Core::getTable("Collections")->findByPath("/");

			$sub_collections=Array();
			$i=0;
			$iter=$children->getIterator();
			foreach($iter as $key=>$sub_coll)
			{
				$sub_collections[]=$this->getCollArray($sub_coll);
				$i++;
			}
			if($i==0)
			{
					$sub_collections=null;
			}
			$results["sub_collections"]=$sub_collections;
			
		}
		if(is_object($coll_obj))
        {
			$results[]=$this->getCollArray($coll_obj);
        }
		
        
		
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results,JSON_UNESCAPED_SLASHES));
	}
	
	public function executeGet_collection_detail(sfWebRequest $request)
	{
		$results = Array();
		$coll_obj=null;
		$id=-1;
        if($request->hasParameter('id'))
        {
            $id= $request->getParameter('id');
			$coll_obj= Doctrine_Core::getTable("Collections")->findOneById($id);
        }
        elseif($request->hasParameter('code'))
        {
            $coll_obj= Doctrine_Core::getTable("Collections")->findOneByCode($request->getParameter('code'));
            if(is_object($coll_obj))
            {
                 $id= $coll_obj->getId();
            }
            
        }
		if(is_object($coll_obj))
        {
			$coll_count= Doctrine_Core::getTable("Collections")->countSpecimens($id);
			$results["count_data"]=$coll_count;			
			$coll_types= Doctrine_Core::getTable("Collections")->countTypeSpecimens($id);
			$results["type_data"]=$coll_types;		
			$spatial_coverage= Doctrine_Core::getTable("Collections")->getSpatialCoverage($id);
			$results["spatial_coverage"]=$spatial_coverage;			
			$temporal_coverage= Doctrine_Core::getTable("Collections")->getTemporalCoverage($id);
			$results["temporal_coverage"]=$temporal_coverage;
			
			$sub_collections=Array();
			$coll_count_subs= Doctrine_Core::getTable("Collections")->countSpecimens($id, "", "", "", "", true);
			$sub_collections["count_data"]=$coll_count_subs;
			$coll_types_subs= Doctrine_Core::getTable("Collections")->countTypeSpecimens($id, "", "", "", "", true);
			$sub_collections["type_data"]=$coll_types_subs;
			$spatial_coverage_subs= Doctrine_Core::getTable("Collections")->getSpatialCoverage($id, true);
			$sub_collections["spatial_coverage"]=$spatial_coverage_subs;
			$temporal_coverage_subs= Doctrine_Core::getTable("Collections")->getTemporalCoverage($id, true);
			$sub_collections["temporal_coverag"]=$temporal_coverage_subs;
			
			$results["with_sub_collections_included"]=$sub_collections;
			
        }
		
        
		
		$this->getResponse()->setContentType('application/json');
		return  $this->renderText(json_encode($results,JSON_UNESCAPED_SLASHES));
	}
}

