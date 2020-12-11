<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class collectionsActions extends DarwinActions
{
	public function executeIndex(sfWebRequest $request)
  {
	   $this->redirect('collections/statistics?hide_menu=off');
  }
  
   
   public function executeStatistics(sfWebRequest $request)
   {
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
		if($coll_obj!==null)
		{
			if($coll_obj->getIsPublic())
			{
				$this->id=$id;
				$this->form = new CollectionsStatisticsFormFilter(array("id"=>$id));
				 return sfView::SUCCESS;
			}
		}
		
		else
	    {
			$this->form = new CollectionsStatisticsFormFilter(array("id"=>$id));
		    return sfView::SUCCESS;
		}
        return $this->setTemplate("index");
   }
   
   

  
  public function executeDisplay_statistics_specimens(sfWebRequest $request)
  {
    $this->getResponse()->setHttpHeader('Content-type','application/json');
    $this->setLayout('json');
    return $this->renderText(json_encode($this->executeDisplay_statistics_specimens_main($request,true)));
  }
  

  
  public function executeDisplay_statistics_types(sfWebRequest $request)
  {
        $this->getResponse()->setHttpHeader('Content-type','application/json');
        $this->setLayout('json');
        return   $this->renderText(json_encode($this->execute_statistics_generic($request, "types",true)));
  }
  
  public function executeDisplay_statistics_taxa(sfWebRequest $request)
  {
        $this->getResponse()->setHttpHeader('Content-type','application/json');
        $this->setLayout('json');
        return   $this->renderText(json_encode($this->execute_statistics_generic($request, "taxa",true)));
  }
  
  public function executeDisplay_all_statistics_csv(sfWebRequest $request)
  {
    $returned=Array();
    
    
    $returned[]="Specimen count";
    $tmp=$this->executeDisplay_statistics_specimens_main($request);
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    $returned[]="Type specimen count";
    $tmp=$this->execute_statistics_generic($request, "types");
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    $returned[]="Taxa in specimen count";
    $tmp=$this->execute_statistics_generic($request, "taxa");
    foreach($tmp as $row)
    {
        $returned[]=implode("\t", $row);
    }
    $returned[]="";
    
    $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
    $this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_statistics.txt"');
    $this->getResponse()->setHttpHeader('Pragma', 'no-cache');
    $this->getResponse()->setHttpHeader('Expires', '0');
    
    $this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
    //$this->getResponse()->setContent($returned);
    $this->getResponse()->sendContent();           
    print(implode("\r\n",$returned));
    return sfView::NONE;           
  }
  
	
}