<?php

/**
 * lithostratigraphy actions.
 *
 * @package    darwin
 * @subpackage lithostratigraphy
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class lithostratigraphyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_lithostratigraphy_widget';
  protected $table = 'lithostratigraphy';
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
    $this->searchForm = new LithostratigraphyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless(
      $unit,
      sprintf('Object lithostratigraphy does not exist (%s).', $request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine::getTable('Lithostratigraphy')->hasChildrens('Lithostratigraphy',$unit->getId());
      if($this->number_child)
      {
        $this->link_delete = 'lithostratigraphy/delete?confirm=1&id='.$unit->getId();
        $this->link_cancel = 'lithostratigraphy/edit?id='.$unit->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }

    try
    {
      $unit->delete();
      $this->redirect('lithostratigraphy/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new LithostratigraphyForm($unit);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
      $this->no_right_col = Doctrine::getTable('Lithostratigraphy')->testNoRightsCollections('litho_ref',$request->getParameter('id'), $this->getUser()->getId());

    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $litho = new Lithostratigraphy() ;
    $duplic = $request->getParameter('duplicate_id','0');
    $litho = $this->getRecordIfDuplicate($duplic, $litho);
    if($request->hasParameter('lithostratigraphy')) $litho->fromArray($request->getParameter('lithostratigraphy'));
    $this->form = new LithostratigraphyForm($litho);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new LithostratigraphyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Lithostratigraphy')->testNoRightsCollections('litho_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new LithostratigraphyForm($unit);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Lithostratigraphy')->testNoRightsCollections('litho_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new LithostratigraphyForm($unit);

    $this->processForm($request,$this->form);
    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new LithostratigraphyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try{
	      $form->save();
	      $this->redirect('lithostratigraphy/edit?id='.$form->getObject()->getId());
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
    $this->litho = Doctrine::getTable('Lithostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($this->litho,'Lithostratigraphic unit not Found');
    $this->form = new LithostratigraphyForm($this->litho);
    $this->loadWidgets();
  }
}
