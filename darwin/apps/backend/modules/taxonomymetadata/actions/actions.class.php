<?php

//ftheeten 2017 07 17

class taxonomyMetadataActions extends DarwinActions
{
    protected $widgetCategory = 'catalogue_taxonomy_widget';
    protected $table = 'taxonomy_metadata';
    
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

  public function executeDelete(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->forward404Unless(
      $taxon = Doctrine_Core::getTable('TaxonomyMetadata')->find($request->getParameter('id')),
      sprintf('Object taxonomy does not exist (%s).',$request->getParameter('id'))
    );

    if(! $request->hasParameter('confirm'))
    {

        $this->link_delete = 'taxonomymetadata/delete?confirm=1&id='.$taxon->getId();
        $this->link_cancel = 'taxonomymetadata/edit?id='.$taxon->getId();
        $this->setTemplate('warndelete', 'catalogue');
        return;
      
    }

    try
    {
      $taxon->delete();
      $this->redirect('taxonomymetadata/index');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form = new TaxonomyMetadataForm($taxon);
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    //ftheeten 2016 07 06
    $this->collection_ref_for_insertion=-1;
    if(is_numeric($request->getParameter('taxonomymetadata')['collection_ref']))
    {
        $this->collection_ref_for_insertion=$request->getParameter('taxonomymetadata')['collection_ref'];
    }
   
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = new TaxonomyMetadata() ;
    $taxa = $this->getRecordIfDuplicate($request->getParameter('duplicate_id','0'), $taxa);
    if($request->hasParameter('taxonomy_metadata')) $taxa->fromArray($request->getParameter('taxonomy_metadata'));
    // if there is no duplicate $taxa is an empty array
    $this->form = new TaxonomyMetadataForm($taxa);
  }

  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->form = new TaxonomyMetadataForm();
    $this->processForm($request,$this->form);
    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine_Core::getTable('TaxonomyMetadata')->find($request->getParameter('id'));

    /*$this->no_right_col = Doctrine_Core::getTable('Taxonomy')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
**/
    $this->forward404Unless($taxa,'Taxa not Found');
    $this->form = new TaxonomyMetadataForm($taxa);
    $this->loadWidgets();
  }

  public function executeUpdate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $taxa = Doctrine_Core::getTable('TaxonomyMetadata')->find($request->getParameter('id'));

    $this->forward404Unless($taxa,'Taxa not Found');
    //$this->no_right_col = Doctrine_Core::getTable('REGISTERED_USER')->testNoRightsCollections('taxon_ref',$request->getParameter('id'), $this->getUser()->getId());
    $this->form = new TaxonomyMetadataForm($taxa);

    $this->processForm($request,$this->form);

    $this->loadWidgets();
    $this->setTemplate('edit');
  }


  public function executeIndex(sfWebRequest $request)
  {
   // $this->setLevelAndCaller($request);
   // $this->form = new TaxonomyMetadataFormFilter(array('table' => $this->table, 'level' => $this->level, 'caller_id' => $this->caller_id));
    $this->form = new TaxonomyMetadataFormFilter();
    
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
        $this->redirect('taxonomymetadata/edit?id='.$form->getObject()->getId());
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
    $this->taxonomy = Doctrine_Core::getTable('TaxonomyMetadata')->find($request->getParameter('id'));
    //$this->setLevelAndCaller($request);
   $this->searchForm = new TaxonomyFormFilter(array('table' => 'taxonomy'));
    $this->forward404Unless($this->taxonomy,'Taxa not Found');
    $this->form = new TaxonomyMetadataForm($this->taxonomy);
    $this->loadWidgets();
  }
  
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    //used to sort header in choose
    $this->setCommonValues('taxonomymetadata', 'taxonomy_name', $request);
    // Instantiate a new expedition form
    $this->form = new TaxonomyMetadataFormFilter();
    // Triggers the search result function
    $this->searchResults($this->form, $request);    
  }
  
    protected function searchResults(TaxonomyMetadataFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchTaxonomyMetadata','') !== '')
    {
      
      $form->bind($request->getParameter('searchTaxonomyMetadata'));
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        // Define all properties that will be either used by the data query or by the pager
        // They take their values from the request. If not present, a default value is defined
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);
        // Define in one line a pager Layout based on a pagerLayoutWithArrows object
        // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)
        $this->pagerLayout = new PagerLayoutWithArrows(new DarwinPager($query,
                                                                          $this->currentPage,
                                                                          $form->getValue('rec_per_page')
                                                                         ),
                                                       new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                       $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
                                                      );
        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->taxonomymetadata_records = $this->pagerLayout->execute();         
      }
    }
  }
  
    public function executeChoose(sfWebRequest $request)
  {
    $this->form = new TaxonomyMetadataFormFilter();
    $this->form->addValue(0);
  }
}