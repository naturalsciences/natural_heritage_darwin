<?php

/**
 * cataloguepeople actions.
 *
 * @package    darwin
 * @subpackage cataloguepeople
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cataloguepeopleActions extends DarwinActions
{
  public function executePeople(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();  
    if($request->hasParameter('id'))
      $this->cataloguepeople =  Doctrine::getTable('CataloguePeople')->find($request->getParameter('id'));
    else
    {
      $this->cataloguepeople = new CataloguePeople();
      $this->cataloguepeople->setRecordId($request->getParameter('rid'));
      $this->cataloguepeople->setReferencedRelation($request->getParameter('table'));
    }
 
    $this->form = new CataloguePeopleForm($this->cataloguepeople,array('table' => $request->getParameter('table')));

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('catalogue_people'));
      if($this->form->isValid())
      {
        try
        {
          $this->form->save();
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error, 'Darwin 2 :'); 
        }
      }
    }
    $this->form->forceSubType();
    $this->searchForm = new PeopleFormFilter(array('table'=> $request->getParameter('table')));
  }

  public function executeEditOrder(sfWebRequest $request)
  {
    $orders = substr($request->getParameter('order', ','),0,-1);
    $orders_ids = explode(',',$orders);
    Doctrine::getTable('CataloguePeople')->changeOrder(
      $request->getParameter('table'),
      $request->getParameter('rid'),
      $request->getParameter('people_type'),
      $orders_ids
    );
    return $this->renderText('ok');
  }

  public function executeGetSubType(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable('CataloguePeople')->getDistinctSubType($request->getParameter('type'));
  }

}
