<?php

/**
 * comment actions.
 *
 * @package    darwin
 * @subpackage comment
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class commentActions extends DarwinActions
{
  protected $ref_id = array('specimens' => 'spec_ref','specimen_individuals' => 'individual_ref','specimen_parts' => 'part_ref') ;
  public function executeComment(sfWebRequest $request)
  { 
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction(); 
    if($request->hasParameter('id'))    
    {
      $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
      $this->forward404Unless($r,'No such item');  
      if(!$this->getUser()->isA(Users::ADMIN))   
      {
        if(in_array($request->getParameter('table'),array_keys($this->ref_id)) )
        {
          $spec = Doctrine::getTable('specimenSearch')->getRecordByRef($this->ref_id[$request->getParameter('table')],$request->getParameter('id'));
          if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections($this->ref_id[$request->getParameter('table')],
                                                                                                          $request->getParameter('id'), 
                                                                                                          $this->getUser()->getId())))    
            $this->forwardToSecureAction();    
        }
      }
    } 
    if($request->hasParameter('cid'))
      $this->comment =  Doctrine::getTable('Comments')->findExcept($request->getParameter('cid'));
    else
    {
     $this->comment = new Comments();
     $this->comment->setRecordId($request->getParameter('id'));
     $this->comment->setReferencedRelation($request->getParameter('table'));
    }
     
    $this->form = new CommentsForm($this->comment,array('table' => $request->getParameter('table')));
    
    if($request->isMethod('post'))
    { 
      $this->form->bind($request->getParameter('comments'));
      if($this->form->isValid())
      {
        try{
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
