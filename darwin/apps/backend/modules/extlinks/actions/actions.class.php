<?php

/**
 * comment actions.
 *
 * @package    darwin
 * @subpackage comment
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class extLinksActions extends DarwinActions
{
  protected $ref_id = array('specimens' => 'spec_ref','specimen_individuals' => 'individual_ref','specimen_parts' => 'part_ref') ;
  public function executeExtLinks(sfWebRequest $request)
  { 
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction(); 
    if($request->hasParameter('id'))    
    {
      $r = Doctrine_Core::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');     
      if(in_array($request->getParameter('table'),array_keys($this->ref_id)) )
      {
        if(! Doctrine_Core::getTable('Specimens')->hasRights($this->ref_id[$request->getParameter('table')],$request->getParameter('id'), $this->getUser()->getId()))
          $this->forwardToSecureAction();    
      }
    } 
    if($request->hasParameter('cid'))
      $this->links =  Doctrine_Core::getTable('ExtLinks')->find($request->getParameter('cid'));
    else
    {
     $this->links = new ExtLinks();
     $this->links->setRecordId($request->getParameter('id'));
     $this->links->setReferencedRelation($request->getParameter('table'));
    }
     
    $this->form = new ExtLinksForm($this->links,array('table' => $request->getParameter('table')));
    
    if($request->isMethod('post'))
    { 
      $this->form->bind($request->getParameter('ext_links'));
      if($this->form->isValid())
      {
        try{
          if($this->form->getObject()->isNew())
            $this->form->setRecordRef($request->getParameter('table'), $request->getParameter('id'));
          $this->form->save();
        }
        catch(Exception $e)
        {
          return $this->renderText($e->getMessage());
        }
        return $this->renderText('ok');
      }
    }
  }
  
    //ftheeten 2017 01 09
  public function executeSketchfabSnippet(sfWebRequest $request)
  {
      $id=$request->getParameter('id');
        $this->link = Doctrine_Core::getTable('ExtLinks')->find($id);
   
        $this->form = new ExtLinksForm($this->link);       
        
  }
  
  //ftheeten 2017 01 09
  public function executeIiifViewer(sfWebRequest $request)
  {
      $id=$request->getParameter('id');
        $this->link = Doctrine_Core::getTable('ExtLinks')->find($id);
   
        $this->form = new ExtLinksForm($this->link);       
        
  }
  
    //ftheeten 2017 01 09
  public function executeExtViewer(sfWebRequest $request)
  {
      $id=$request->getParameter('id');
        $this->link = Doctrine_Core::getTable('ExtLinks')->find($id);
   
        $this->form = new ExtLinksForm($this->link);       
        
  }
  
   public function executeGet_ext_links_json($request)
  {
	  $results=Array();
	  if($request->isMethod('post'))
      {
		  $criterias = $request->getPostParameters();
		  if(array_key_exists("specimen_ids",  $criterias))
		  {
			  $record_ids_str=$criterias["specimen_ids"];
			  if(strlen($record_ids_str)>0)
			  {
				$record_ids =json_decode($record_ids_str);
				
				if(count($record_ids)>0)
				{
					$results=Doctrine_Core::getTable('ExtLinks')->getRelatedLinks_as_array("specimens", $record_ids);
				}
			  }
		  }
	
	  }
	  elseif($request->isMethod('get'))
      {
		  if($request->hasParameter('specimen_ids'))
		  {
			  $specimen_ids=$request->getParameter('specimen_ids');
			  $record_ids=explode(",", $specimen_ids);
			  if(count($record_ids)>0)
			  {
				$results=Doctrine_Core::getTable('ExtLinks')->getRelatedLinks_as_array("specimens", $record_ids);
			  }
		  
		  }
	  }
	  $this->getResponse()->setContentType('application/json');
       return  $this->renderText(json_encode($results,JSON_UNESCAPED_SLASHES));
  }
  
}
