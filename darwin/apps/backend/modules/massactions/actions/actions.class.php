<?php

/**
 * massactions actions.
 *
 * @package    darwin
 * @subpackage massactions
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class massactionsActions extends DarwinActions
{
  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER)) {
      $this->forwardToSecureAction();
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new BaseMassActionForm();
    if($request->isMethod('post') && $request->getParameter('mass_action','') != '')
    {
      $actions = $request->getParameter('mass_action',array());
      $this->form->bind($actions);
      if($this->form->isValid())
      {
        $this->form->doMassAction($this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
        $nb_item = count($this->form->getValue('item_list'));
       $this->redirect('massactions/status?nb_item='.$nb_item);
      }
	 
      $items_ids = $this->getUser()->getAllPinned('specimen');
      $this->items = Doctrine_Core::getTable('Specimens')->getByMultipleIds($items_ids, $this->getUser()->getId());
    } else {
      $items_ids = $this->getUser()->getAllPinned('specimen');
      $this->items = Doctrine_Core::getTable('Specimens')->getByMultipleIds($items_ids, $this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
    }
  }

  public function executeStatus(sfWebRequest $request)
  {
    $this->nb_items = $request->getParameter('nb_item',0);
    $this->session_messages=$_SESSION["mass_action_messages"];
    unset($_SESSION["mass_action_messages"]);
  }

  public function executeGetSubForm(sfWebRequest $request)
  {
    $this->source = $request->getParameter('source','specimen');
    $this->mAction = $request->getParameter('maction','');
    $this->form = new BaseMassActionForm();
    $this->form->addSubForm($this->mAction);
  }
  
    //ftheeten 2018 11 22
   public function executeAddPeople(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
	$this->form = new BaseMassActionForm();
	$this->form->addSubForm("collectors");
	$sub_form=$this->form->add_people("collectors", $number);
	
	return $this->renderPartial('addPeople',array('form' => $sub_form, 'row_line'=>$number));
  }
  
   public function executeAddDonator(sfWebRequest $request)
  {
    $number = intval($request->getParameter('num'));
	$this->form = new BaseMassActionForm();
	$this->form->addSubForm("donators");
	$sub_form=$this->form->add_people("donators", $number);
	
	return $this->renderPartial('addDonator',array('form' => $sub_form, 'row_line'=>$number));
  }
  
}
