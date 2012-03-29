<?php

/**
 * loan actions.
 *
 * @package    darwin
 * @subpackage loan
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanitemActions extends DarwinActions
{
  protected $widgetCategory = 'loanitem_widget';

  protected function checkRight($loan_item_id)  
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($loanitem = Doctrine::getTable('LoanItems')->findExcept($loan_item_id), sprintf('Object loanitem does not exist (%s).', array($loan_item_id)));
    if($this->getUser()->isAtLeast(Users::ADMIN)) return $loanitem ;
    $right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$loanitem->getLoanRef());
    if(!$right && !$this->getUser()->isAtLeast(Users::MANAGER))
      $this->forwardToSecureAction();
    if($right==="view" || $this->getUser()->isAtLeast(Users::MANAGER))
      $this->redirect('loanitem/view?id='.$loanitem->getId());      
    return $loanitem ;
  }  

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $loan = $this->checkRight($request->getParameter('id')) ;
    $this->form = new LoanItemWidgetForm($loan);
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeEdit(sfWebRequest $request)
  {
    $loan = $this->checkRight($request->getParameter('id')) ;
    $this->form = new LoanItemWidgetForm($loan);
    $this->loadWidgets();
    $this->setTemplate('edit') ;    
  }

  public function executeAddComments(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = new LoanItemWidgetForm();
    $form->addComments($number);
    return $this->renderPartial('specimen/spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));    
    if ($form->isValid())
    {

      try
      {
        $item = $form->save();
        $this->redirect('loanitem/edit?id='.$item->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error); 
      }
    }
  }


  public function executeDelete(sfWebRequest $request)
  {
    $loan = $this->checkRight($request->getParameter('id')) ;
    try
    {
      $loan->delete();
      $this->redirect('loan/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LoanItemWidgetForm($loan);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  public function executeMaintenances(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($items = explode(',',$request->getParameter('ids')) );
    if(!$id = doctrine::getTable('loanItems')->getLoanRef($items)) $this->forwardToSecureAction();
    if(!$this->getUser()->isAtLeast(Users::ADMIN) && Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$id) !== true)
      $this->forwardToSecureAction();
    $this->form = new MultiCollectionMaintenanceForm();
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('collection_maintenance'));

      if($this->form->isValid())
      {
        try
        {
          $obj = $this->form->updateObject();
          $obj->setReferencedRelation('loan_items');
          foreach($items as $it)
          {
            $o = clone $obj;
            $o->setRecordId($it);
            $o->save();
          }
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error); 
        }
      }
    }
    //return $this->renderText('ok '.implode('-',$items)) ;
  }

  public function executeView(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($this->loan_item = Doctrine::getTable('LoanItems')->findExcept($request->getParameter('id')), sprintf('Object loan item does not exist (%s).', array($request->getParameter('id'))));

    if(!$this->getUser()->isAtLeast(Users::MANAGER) && !Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan_item->getLoanRef() ))
      $this->forwardToSecureAction();
    $this->loadWidgets();
  }

  public function executeShowmaintenances(sfWebRequest $request)
  {
    $this->forward404Unless($this->loan_item = Doctrine::getTable('LoanItems')->findExcept($request->getParameter('id')), sprintf('Object loan item does not exist (%s).', array($request->getParameter('id'))));

    if(!$this->getUser()->isAtLeast(Users::ADMIN) && !Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan_item->getLoanRef() ))
      $this->forwardToSecureAction();

    $this->maintenances =  Doctrine::getTable('CollectionMaintenance')->getRelatedArray('loan_items', $this->loan_item->getId());
  }
  public function executeDelmaintenance(sfWebRequest $request)
  {
    $maint = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter('id'));
    $this->forward404Unless($maint->getReferencedRelation() == 'loan_items');
    $this->loan_item = Doctrine::getTable('LoanItems')->findExcept($maint->getRecordId());

    $rights = $this->getUser()->isAtLeast(Users::ADMIN) && Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan_item->getLoanRef() );
    if(! $rights === true)
      $this->forwardToSecureAction();

    $maint->delete();
    return $this->renderText('ok');
  }

  public function executeGetIgNum(sfWebRequest $request)
  {
    /** @Todo: Change for flat_less */
    $spec = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($request->getParameter('id'));
    $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    return $this->renderText( json_encode(array('ig_num'=>$spec->getIgNum(), 'ig_ref'=>$spec->getIgRef())));
  }
}
