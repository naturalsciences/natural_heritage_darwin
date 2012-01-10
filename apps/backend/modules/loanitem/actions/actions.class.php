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

  public function executeEdit(sfWebRequest $request)
  {
    // Forward to a 404 page if the requested expedition id is not found
    $this->forward404Unless($item = Doctrine::getTable('LoanItems')->findExcept($request->getParameter('id')), sprintf('Object loan item does not exist (%s).', array($request->getParameter('id'))));
    $this->form = new LoanItemsForm($item);
    $this->loadWidgets();
  }



  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $item = $form->save();
        $this->redirect('loan/edit?id='.$item->getId());
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
    $this->forward404Unless($loan = Doctrine::getTable('Loans')->find(array($request->getParameter('id'))), sprintf('Object loans does not exist (%s).', array($request->getParameter('id'))));
    try
    {
      $loan->delete();
      $this->redirect('loan/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LoansForm($loan);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }
}
