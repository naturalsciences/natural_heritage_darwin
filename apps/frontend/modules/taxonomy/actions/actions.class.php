<?php

/**
 * taxonomy actions.
 *
 * @package    darwin
 * @subpackage taxonomy
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class taxonomyActions extends sfActions
{
  public function executeChoose(sfWebRequest $request)
  {
    $this->searchForm = new SearchTaxonForm();
    $this->setLayout(false);
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless(
      $taxa = Doctrine::getTable('Taxonomy')->find(array($request->getParameter('id'))),
      sprintf('Object taxonomy does not exist (%s).', array($request->getParameter('id')))
    );

    try
    {
      $taxa->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $this->form = new TaxonomyForm($taxa);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error); 
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('taxonomy/index');
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TaxonomyForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->form = new TaxonomyForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('edit');
  }
    
  public function executeEdit(sfWebRequest $request)
  {
    $taxa = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));
    $this->forward404Unless($taxa,'Taxa not Found');
    $this->form = new TaxonomyForm($taxa);
    
    $relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy',$taxa->getId());
    $this->form->loadRelationsForms($relations);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $taxa = Doctrine::getTable('Taxonomy')->find($request->getParameter('id'));
    $this->forward404Unless($taxa,'Taxa not Found');
    $this->form = new TaxonomyForm($taxa);
    
    $relations = Doctrine::getTable('CatalogueRelationships')->getRelationsForTable('taxonomy',$taxa->getId());
    $combination = false;
    $this->processForm($request,$this->form);
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
    $this->searchForm = new SearchTaxonForm();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->searchForm = new SearchTaxonForm();
    $this->searchResults($this->searchForm,$request);
    $this->setLayout(false);
  }

  public function executeTree(sfWebRequest $request)
  {
    $this->items = Doctrine::getTable('Taxonomy')->findWithParents($request->getParameter('id'));
    $this->setLayout(false);
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind( $request->getParameter($form->getName()) );
    if ($form->isValid())
    {
      $conn = $form->getObject()->getTable()->getConnection();
      try{
	$conn->beginTransaction();
	if(! $form->getObject()->isNew())
	  Doctrine::getTable('CatalogueRelationships')->deleteRelationsForTable('taxonomy',$form->getObject()->getId());
	$form->save();
	$conn->commit();
	$this->redirect('taxonomy/edit?id='.$form->getObject()->getId());
      }
      catch(sfStopException $e)
      { throw $e; }
      catch(Exception $e)
      {
	$conn->rollBack();
	$error = new sfValidatorError(new savedValidator(),$e->getMessage());
	$form->getErrorSchema()->addError($error); 
      }
    }
  }

  protected function searchResults($form, $request)
  {
    if($request->getParameter('searchTaxon','') !== '')
    {
      $form->bind($request->getParameter('searchTaxon'));
      if ($form->isValid())
      {
 	$this->taxons = Doctrine::getTable('Taxonomy')
 	  ->getByNameLike($form->getValue('name'), $form->getValue('level'));
      }
    }

  }
}