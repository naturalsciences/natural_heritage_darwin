<?php

class stagingActions extends DarwinActions
{
  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER))
    {
      $this->forwardToSecureAction();
    }
  }

  public function executeMarkok(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('import'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('import'));

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();
    $this->import = Doctrine::getTable('Imports')->markOk($this->import->getId());
    return $this->redirect('import/index');
  }

  /*
   * not used anymore, so temporarily commented until a solution is found to bring
   * back the feature of taxonomic hierarchy auto-creation
   *
  public function executeCreateTaxon(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('import'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('import'));

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();
    //$this->import = Doctrine::getTable('Staging')->markTaxon($this->import->getId());
    return $this->redirect('import/index');

  }
  */
  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $line = Doctrine::getTable('Staging')->find($request->getParameter('id'));
    $this->import = Doctrine::getTable('Imports')->find($line->getImportRef());

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $line->delete();
    if($request->isXmlHttpRequest())
    {
      return $this->renderText('ok');
    }
    return $this->redirect('staging/index?import=');
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('import'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('import'));
    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->setCommonValues('staging', 'id', $request);

    $this->form = new StagingFormFilter(null, array('import' =>$this->import));
    $filters = $request->getParameter('staging_filters');

    $this->form->bind($filters);
    if($this->form->isValid())
    {
      $query = $this->form->getQuery();
      // Define the pager
      $pager = new DarwinPager($query, $this->form->getValue('current_page'), $this->form->getValue('rec_per_page'));

      $this->setCommonValues('search', 'collection_name', $request);
      $params = $request->isMethod('post') ? $request->getPostParameters() : $request->getGetParameters();

      $this->s_url = 'staging/search'.'?import='.$request->getParameter('import');
      $this->o_url = '';

      $this->pagerLayout = new PagerLayoutWithArrows(
          new DarwinPager(
            $query,
            $this->currentPage,
            $this->form->getValue('rec_per_page')
          ),
          new Doctrine_Pager_Range_Sliding(
            array('chunk' => $this->pagerSlidingSize)
          ),
          $this->getController()->genUrl($this->s_url.$this->o_url) . '/page/{%page_number}'
        );

      $this->setDefaultPaggingLayout($this->pagerLayout);
      $this->pagerLayout->setTemplate('<li data-page="{%page_number}"><a href="{%url}">{%page}</a></li>');

      // If pager not yet executed, this means the query has to be executed for data loading
      if (! $this->pagerLayout->getPager()->getExecuted())
        $this->search = $this->pagerLayout->execute();

      /** Let's Fetch id for codes */
      $ids = array();
      foreach($this->search as $k=>$v)
      {
        $ids[] = $v->getId();
      }

      $codes = Doctrine::getTable('Codes')->getCodesRelatedArray('staging',$ids) ;
      $linked = Doctrine::getTable('Staging')->findLinked($ids) ;
      foreach($this->search as $k=>$v)
      {
        foreach($codes as $code)
        {
          if($code['record_id'] == $v->getId())
          {
            $v->codes[] = $code;
          }
        }
        foreach($linked as $link)
        {
          if($link['record_id'] == $v->getId())
            $v->setLinkedInfo($link['cnt']);
        }
      }
      $this->displayModel = new DisplayImportABCD();
      $this->search_type = $this->form->getValue('bio_geo');
      $this->fields = $this->displayModel->getColumns($this->search_type);
    }
  }
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('import'));
    $this->import = Doctrine::getTable('Imports')->find($request->getParameter('import'));
    $this->forward404Unless($this->import);

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->form = new StagingFormFilter(null, array('import' =>$this->import));
  }

  public function executeEdit(sfWebRequest $request)
  {
    $staging = Doctrine::getTable('Staging')->findOneById($request->getParameter('id'));
    $this->import = Doctrine::getTable('Imports')->find($staging->getImportRef());

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->fields = $staging->getFields() ;
    $form_fields = array() ;
    if($this->fields)
    {
      foreach($this->fields as $key => $values)
        $form_fields[] = $values['fields'] ;
    }
    if(in_array('taxon_ref', $form_fields))
    {
      $parent = new Hstore ;
      $parent->import($staging->getTaxonParents()) ;
      $taxon_parent = $parent->getArrayCopy() ;
      $taxon_parent[$staging->getTaxonLevelName()] = $staging->getTaxonName();
      $this->taxon_level_name = $staging->getTaxonLevelName();
      $this->catalogues_taxon = Doctrine::getTable('Taxonomy')->getLevelParents('Taxonomy', $taxon_parent) ;
    }
    if(in_array('litho_ref', $form_fields))
    {
      $parent = new Hstore ;
      $parent->import($staging->getLithoParents()) ;
      $parents = $parent->getArrayCopy() ;
      $parents[$staging->getLithoLevelName()] = $staging->getLithoName();
      $this->litho_level_name = $staging->getLithoLevelName();
      $this->catalogues_litho = Doctrine::getTable('Lithostratigraphy')->getLevelParents('Lithostratigraphy', $parents) ;
    }
    if(in_array('lithology_ref', $form_fields))
    {
      $parent = new Hstore ;
      $parent->import($staging->getLithologyParents()) ;
      $parents = $parent->getArrayCopy() ;
      $parents[$staging->getLithologyLevelName()] = $staging->getLithologyName();
      $this->lithology_level_name = $staging->getLithologyLevelName();
      $this->catalogues_lithology = Doctrine::getTable('Lithology')->getLevelParents('Lithology', $parents) ;
    }
    if(in_array('chrono_ref', $form_fields))
    {
      $parent = new Hstore ;
      $parent->import($staging->getChronoParents()) ;
      $parents = $parent->getArrayCopy() ;
      $parents[$staging->getChronoLevelName()] = $staging->getChronoName();
      $this->chrono_level_name = $staging->getChronoLevelName();
      $this->catalogues_chrono = Doctrine::getTable('Chronostratigraphy')->getLevelParents('Chronostratigraphy', $parents) ;
    }
    if(in_array('mineral_ref', $form_fields))
    {
      $parent = new Hstore ;
      $parent->import($staging->getMineralParents()) ;
      $parents = $parent->getArrayCopy() ;
      $parents[$staging->getMineralLevelName()] = $staging->getMineralName();
      $this->mineral_level_name = $staging->getMineralLevelName();
      $this->catalogues_mineral = Doctrine::getTable('Mineralogy')->getLevelParents('Mineralogy', $parents) ;
    }
    $this->form = new StagingForm($staging, array('fields' => $form_fields));

  }

  public function executeUpdate(sfWebRequest $request)
  {
    $staging = Doctrine::getTable('Staging')->findOneById($request->getParameter('id'));

    $this->import = Doctrine::getTable('Imports')->find($staging->getImportRef());

    if(! Doctrine::getTable('collectionsRights')->hasEditRightsFor($this->getUser(),$this->import->getCollectionRef()))
       $this->forwardToSecureAction();

    $this->fields = $staging->getFields() ;
    $form_fields = array() ;
    if($this->fields)
    {
      foreach($this->fields as $key => $values)
        $form_fields[] = $values['fields'] ;
    }
    $this->form = new StagingForm($staging, array('fields' => $form_fields));

    $this->processForm($request,$this->form, $form_fields);

    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, array $fields)
  {
    $form->bind( $request->getParameter($form->getName()) );
    if ($form->isValid())
    {
      try
      {
        $form->save();
        return $this->redirect('staging/index?import='.$form->getObject()->getImportRef());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error);
      }
    }
  }
}
