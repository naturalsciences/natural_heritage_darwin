<?php

/**
 * loan actions.
 *
 * @package    darwin
 * @subpackage loan
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanitemActions extends DarwinActions
{
  protected $widgetCategory = 'loanitem_widget';

  protected function checkRight($loan_item_id)  
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($loanitem = Doctrine::getTable('LoanItems')->findExcept($loan_item_id), sprintf('Object loanitem does not exist (%s).', array($loan_item_id)));
    if(!$right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$loanitem->getLoanRef()))
      $this->forwardToSecureAction();
    if($right==="view") $this->redirect('loanitem/view?id='.$loanitem->getId());      
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
    
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
/*@TODO: Change User permission !! */

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
}
