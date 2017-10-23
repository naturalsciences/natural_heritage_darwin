<?php

/**
 * mineralogy actions.
 *
 * @package    darwin
 * @subpackage mineralogy
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class mineralogyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_mineralogy_widget';
  protected $table = 'mineralogy';
  public function preExecute()
  {
    if (! strstr('view',$this->getActionName()) && ! strstr('index',$this->getActionName()) && ! strstr('searchForLimited',$this->getActionName()) && ! strstr('search',$this->getActionName()) )
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
    $this->searchForm = new MineralogyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Mineralogy')->find($request->getParameter('id'));
    $this->forward404Unless(
      $unit,
      sprintf('Object mineralogy does not exist (%s).', $request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine::getTable('Mineralogy')->hasChildrens('Mineralogy',$unit->getId());
      if($this->number_child)
      {
        $this->link_delete = 'mineralogy/delete?confirm=1&id='.$unit->getId();
        $this->link_cancel = 'mineralogy/edit?id='.$unit->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }

    try
    {
      $unit->delete();
      $this->redirect('mineralogy/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new MineralogyForm($unit);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->no_right_col = Doctrine::getTable('Mineralogy')->testNoRightsCollections('mineral_ref',$request->getParameter('id'), $this->getUser()->getId());
      $this->setTemplate('edit');
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $mineral = new Mineralogy() ;
    $mineral = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $mineral);
    if($request->hasParameter('mineralogy')) $mineral->fromArray($request->getParameter('mineralogy'));
    $this->form = new MineralogyForm($mineral);
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new MineralogyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Mineralogy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Mineralogy')->testNoRightsCollections('mineral_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new MineralogyForm($unit);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Mineralogy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->no_right_col = Doctrine::getTable('Mineralogy')->testNoRightsCollections('mineral_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new MineralogyForm($unit);

    $this->processForm($request,$this->form);

    $this->loadWidgets();

    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new MineralogyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try{
	$form->save();
	$this->redirect('mineralogy/edit?id='.$form->getObject()->getId());
      }
      catch(Doctrine_Exception $ne)
      {
	$e = new DarwinPgErrorParser($ne);
	$error = new sfValidatorError(new savedValidator(),$e->getMessage());
	$form->getErrorSchema()->addError($error);
      }
    }
  }

  public function executeSearchFor(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('get'));
    // Triggers the search ID function
    if($request->getParameter('searchedCrit', '') !== '')
    {
      $unitId = Doctrine::getTable('Mineralogy')->findOneByCode($request->getParameter('searchedCrit'));
      if ($unitId)
        return $this->renderText($unitId->getCode());
      else
        return $this->renderText('not found');
    }
    return $this->renderText('');
  }

  public function executeSearchForLimited(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('get'));
    // Triggers the search ID function
    if($request->getParameter('q', '') !== '' && $request->getParameter('limit', '') !== '')
    {
      $codes = Doctrine::getTable('Mineralogy')->fetchByCodeLimited($request->getParameter('q'), $request->getParameter('limit'));
      if ($codes)
      {
        $values=array();
        foreach($codes as $key=>$value)
        {
          $values[$key]=$value->getCode();
        }
        return $this->renderText(implode("\n",$values));
      }
      else
        return $this->renderText(array(''));
    }
    return $this->renderText(array(''));
  }
  public function executeView(sfWebRequest $request)
  {
    $this->mineral = Doctrine::getTable('Mineralogy')->find($request->getParameter('id'));
    $this->forward404Unless($this->mineral,'Mineralogic unit not Found');
    $this->form = new MineralogyForm($this->mineral);
    $this->loadWidgets();
  }
}
