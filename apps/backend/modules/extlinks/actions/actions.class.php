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
      $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');     
      if(in_array($request->getParameter('table'),array_keys($this->ref_id)) )
      {
        if(! Doctrine::getTable('Specimens')->hasRights($this->ref_id[$request->getParameter('table')],$request->getParameter('id'), $this->getUser()->getId()))
          $this->forwardToSecureAction();    
      }
    } 
    if($request->hasParameter('cid'))
      $this->links =  Doctrine::getTable('ExtLinks')->find($request->getParameter('cid'));
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
}
