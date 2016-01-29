<?php

/**
 * taxonomy actions.
 *
 * @package    darwin
 * @subpackage taxonomy
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class taxonomyActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_taxonomy_widget';
  protected $table = 'taxonomy';

  public function executeChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeMultipleChoose(sfWebRequest $request)
  {
    $name = $request->hasParameter('name')?$request->getParameter('name'):'' ;
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id, 'name' => $name));
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->forward404Unless(
      $taxon = Doctrine::getTable('Taxonomy')->find($request->getParameter('id')),
      sprintf('Object taxonomy does not exist (%s).',$request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {
      $this->number_child = Doctrine::getTable('Taxonomy')->hasChildrens('Taxonomy',$taxon->getId());
      if($this->number_child)
      {
        $this->link_delete = 'taxonomy/delete?confirm=1&id='.$taxon->getId();
        $this->link_cancel = 'taxonomy/edit?id='.$taxon->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      }
    }

    try
    {
      $taxon->delete();
      $this->redirect('taxonomy/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new TaxonomyForm($taxon);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->no_right_col = Doctrine::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
      $this->setTemplate('edit');
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = new Taxonomy() ;
    $taxa = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $taxa);
    if($request->hasParameter('taxonomy')) $taxa->fromArray($request->getParameter('taxonomy'));
    // if there is no duplicate $taxa is an empty array
    $this->form = new TaxonomyForm($taxa);
  }

  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->form = new TaxonomyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));

    $this->no_right_col = Doctrine::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());

    $this->forward404Unless($taxa,'Taxa not Found');
    $this->form = new TaxonomyForm($taxa);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));

    $this->forward404Unless($taxa,'Taxa not Found');
    $this->no_right_col = Doctrine::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new TaxonomyForm($taxa);

    $this->processForm($request,$this->form);

    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    $this->searchForm = new TaxonomyFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $form->bind( $request->getParameter($form->getName()),$request->getFiles($form->getName()) );
    if ($form->isValid())
    {
      try
      {
        $form->save();
        $this->redirect('taxonomy/edit?id='.$form->getObject()->getId());
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
    $this->taxon = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));
    $this->forward404Unless($this->taxon,'Taxa not Found');
    $this->form = new TaxonomyForm($this->taxon);
    $this->loadWidgets();
  }
}
