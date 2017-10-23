<?php

/**
 * chronostratigraphy actions.
 *
 * @package    darwin
 * @subpackage chronostratigraphy
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class chronostratigraphyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_chronostratigraphy_widget';
  protected $table = 'chronostratigraphy';

  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()))
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
    $this->searchForm = new ChronostratigraphyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Chronostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless(
      $unit,
      sprintf('Object chronostratigraphy does not exist (%s).', $request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine::getTable('Chronostratigraphy')->hasChildrens('Chronostratigraphy',$unit->getId());
      if($this->number_child)
      {
        $this->link_delete = 'chronostratigraphy/delete?confirm=1&id='.$unit->getId();
        $this->link_cancel = 'chronostratigraphy/edit?id='.$unit->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }

    try
    {
      $unit->delete();
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new ChronostratigraphyForm($unit);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
      $this->no_right_col = Doctrine::getTable('Chronostratigraphy')->testNoRightsCollections('chrono_ref',$request->getParameter('id'), $this->getUser()->getId());
      return ;
    }
    $this->redirect('chronostratigraphy/index');
  }

  public function executeNew(sfWebRequest $request)
  {
    $chrono = new Chronostratigraphy();
    $duplic = $request->getParameter('duplicate_id','0');
    $chrono = $this->getRecordIfDuplicate($duplic, $chrono);
    if($request->hasParameter('chronostratigraphy')) $chrono->fromArray($request->getParameter('chronostratigraphy'));
    // if there is no duplicate $chrono is an empty array
    $this->form = new ChronostratigraphyForm($chrono);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new ChronostratigraphyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Chronostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Chronostratigraphy')->testNoRightsCollections('chrono_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->form = new ChronostratigraphyForm($unit);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Chronostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Chronostratigraphy')->testNoRightsCollections('chrono_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new ChronostratigraphyForm($unit);

    $this->processForm($request,$this->form);

    $this->loadWidgets();

    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new ChronostratigraphyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try{
	      $form->save();
	      $this->redirect('chronostratigraphy/edit?id='.$form->getObject()->getId());
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
    $this->chrono = Doctrine::getTable('Chronostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($this->chrono,'Chrono not Found');
    $this->form = new ChronostratigraphyForm($this->chrono);
    $this->loadWidgets();
  }
}
