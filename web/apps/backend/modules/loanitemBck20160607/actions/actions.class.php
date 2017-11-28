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
    $loanitem = Doctrine::getTable('LoanItems')->find($loan_item_id);

    $this->forward404Unless($loanitem, sprintf('Object loanitem does not exist (%s).', $loan_item_id));
    if($this->getUser()->isAtLeast(Users::ADMIN)) return $loanitem ;
    $right = Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$loanitem->getLoanRef());
    if(!$right)
    {
      if ($this->getUser()->isAtLeast(Users::MANAGER)) $this->redirect('loan/view?id='.$loan->getId());
      else $this->forwardToSecureAction();
    }
    if($right==="view")
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
  public function executeDeleteChecked(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($items = explode(',',$request->getParameter('ids')) );
    if(!$id = doctrine::getTable('loanItems')->getLoanRef($items)) $this->forwardToSecureAction();
    if(!$this->getUser()->isAtLeast(Users::ADMIN) && Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$id) !== true)
      $this->forwardToSecureAction();
    if($request->isXmlHttpRequest()) 
    {
      try
      {
        doctrine::getTable('loanItems')->deleteChecked($items) ;        
        return $this->renderText('ok');
      }
      catch(Doctrine_Exception $ne)
      {      }      
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
    $this->loan_item = Doctrine::getTable('LoanItems')->find($request->getParameter('id'));
    $this->forward404Unless($this->loan_item, sprintf('Object loan item does not exist (%s).', $request->getParameter('id')));

    if(!$this->getUser()->isAtLeast(Users::MANAGER) && !Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan_item->getLoanRef() ))
      $this->forwardToSecureAction();
    $this->loadWidgets();
  }

  public function executeShowmaintenances(sfWebRequest $request)
  {
    $this->loan_item = Doctrine::getTable('LoanItems')->find($request->getParameter('id'));
    $this->forward404Unless($this->loan_item, sprintf('Object loan item does not exist (%s).', $request->getParameter('id')));

    if(!$this->getUser()->isAtLeast(Users::ADMIN) && !Doctrine::getTable('loanRights')->isAllowed($this->getUser()->getId(),$this->loan_item->getLoanRef() ))
      $this->forwardToSecureAction();

    $this->maintenances =  Doctrine::getTable('CollectionMaintenance')->getRelatedArray('loan_items', $this->loan_item->getId());
  }

  public function executeGetIgNum(sfWebRequest $request)
  {
    $ig = Doctrine::getTable('Igs')->findIgBySpecimenRef($request->getParameter('id'));
    $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    if($ig)
      return $this->renderText( json_encode(array('ig_num'=>$ig->getIgNum(), 'ig_ref'=>$ig->getId())));
    return $this->renderText( json_encode(array('ig_num'=> '', 'ig_ref'=>'')));
  }

  public function executeAddInsurance(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
    $form = new LoanItemWidgetForm();
    $form->addInsurances($number, array());
    return $this->renderPartial('specimen/insurances',array('form' => $form['newInsurances'][$number], 'rownum'=>$number));
  }
}
