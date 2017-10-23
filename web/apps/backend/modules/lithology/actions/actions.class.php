<?php

/**
 * lithology actions.
 *
 * @package    darwin
 * @subpackage lithology
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class lithologyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_lithology_widget';
  protected $table = 'lithology';
  
  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()) && ! strstr('search',$this->getActionName()))
    {
      if(! $this->getUser()->isAtLeast(Users::ENCODER))
      {
        $this->forwardToSecureAction();
      }
    }
  }
  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;  
    $this->setLevelAndCaller($request);
    $this->searchForm = new LithologyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  { 
    $unit = Doctrine::getTable('Lithology')->find($request->getParameter('id'));
    $this->forward404Unless(
      $unit,
      sprintf('Object lithology does not exist (%s).', $request->getParameter('id'))
    );
    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine::getTable('Lithology')->hasChildrens('Lithology',$unit->getId());
      if($this->number_child)
      {
        $this->link_delete = 'lithology/delete?confirm=1&id='.$unit->getId();
        $this->link_cancel = 'lithology/edit?id='.$unit->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }
    try
    {
      $unit->delete();
      $this->redirect('lithology/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LithologyForm($unit);
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
      $this->no_right_col = Doctrine::getTable('Lithology')->testNoRightsCollections('lithology_ref',$request->getParameter('id'), $this->getUser()->getId());
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $litho = new Lithology() ;
    $duplic = $request->getParameter('duplicate_id','0');
    $litho = $this->getRecordIfDuplicate($duplic, $litho);
    if($request->hasParameter('lithology')) $litho->fromArray($request->getParameter('lithology'));        
    $this->form = new LithologyForm($litho);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new LithologyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }
    
  public function executeEdit(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithology')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Lithology')->testNoRightsCollections('lithology_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->form = new LithologyForm($unit);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithology')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Lithology')->testNoRightsCollections('lithology_ref',$request->getParameter('id'), $this->getUser()->getId());    
    $this->form = new LithologyForm($unit);
    
    $this->processForm($request,$this->form);

    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new LithologyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try{
	      $form->save();
	      $this->redirect('lithology/edit?id='.$form->getObject()->getId());
            }
            catch(Doctrine_Exception $ne)
            {
	      $e = new DarwinPgErrorParser($ne);
	      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
	      $form->getErrorSchema()->addError($error); 
      }
    }
  }
  public function executeView(sfWebRequest $request)
  {
    $this->litho = Doctrine::getTable('Lithology')->find($request->getParameter('id'));
    $this->forward404Unless($this->litho,'Lithologic unit not Found');
    $this->form = new LithologyForm($this->litho);    
    $this->loadWidgets();
  }
}
