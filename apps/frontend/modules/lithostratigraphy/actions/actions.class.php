<?php

/**
 * lithostratigraphy actions.
 *
 * @package    darwin
 * @subpackage lithostratigraphy
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class lithostratigraphyActions extends sfActions
{
  public function executeChoose(sfWebRequest $request)
  {
    $this->searchForm = new LithostratigraphyFormFilter(array('table'=> 'lithostratigraphy'));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless(
      $unit = Doctrine::getTable('Lithostratigraphy')->findExcept($request->getParameter('id')),
      sprintf('Object lithostratigraphy does not exist (%s).', array($request->getParameter('id')))
    );

    try
    {
      $unit->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $this->form = new LithostratigraphyForm($unit);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error); 
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('lithostratigraphy/index');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new LithostratigraphyForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new LithostratigraphyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }
    
  public function executeEdit(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithostratigraphy')->findExcept($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->form = new LithostratigraphyForm($unit);
    
    $this->widgets = Doctrine::getTable('MyPreferences')
      ->setUserRef($this->getUser()->getAttribute('db_user_id'))
      ->getWidgets('catalogue_lithostratigraphy_widget');
    if(! $this->widgets) $this->widgets=array();

    $relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('lithostratigraphy',$unit->getId());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $unit = Doctrine::getTable('Lithostratigraphy')->find($request->getParameter('id'));
    $this->forward404Unless($unit,'Unit not Found');
    $this->form = new LithostratigraphyForm($unit);
    
    $relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('lithostratigraphy',$unit->getId());
    $this->processForm($request,$this->form);

    $this->widgets = Doctrine::getTable('MyPreferences')
      ->setUserRef($this->getUser()->getAttribute('db_user_id'))
      ->getWidgets('catalogue_lithostratigraphy_widget');
    if(! $this->widgets) $this->widgets=array();

    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->searchForm = new LithostratigraphyFormFilter(array('table'=> 'lithostratigraphy'));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()) );
    if ($form->isValid())
    {
      try{
	$form->save();
	$this->redirect('lithostratigraphy/edit?id='.$form->getObject()->getId());
      }
      catch(sfStopException $e)
      { throw $e; }
      catch(Exception $e)
      {
	$error = new sfValidatorError(new savedValidator(),$e->getMessage());
	$form->getErrorSchema()->addError($error); 
      }
    }
  }

}