<?php

/**
 * Specimens actions.
 *
 * @package    darwin
 * @subpackage specimen
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class specimenActions extends DarwinActions
{
  protected $widgetCategory = 'specimen_widget';

  protected function getSpecimenForm(sfWebRequest $request, $fwd404=false, $parameter='id', $options=array())
  {
    $spec = null;

    if ($fwd404)
      $this->forward404Unless($spec = Doctrine_Core::getTable('Specimens')->find($request->getParameter($parameter,0)));
    elseif($request->hasParameter($parameter) && $request->getParameter($parameter))
      $spec = Doctrine_Core::getTable('Specimens')->find($request->getParameter($parameter));

    $form = new SpecimensForm($spec, $options);
    return $form;
  }

  /**
   * Return a code mask for a given collection
   * @param \sfWebRequest $request
   * @return string $codeMask The code mask defined for the collection passed as request parameter
   */
  protected function getCodeMask(sfWebRequest $request) {
    $codeMask='';
    $collId = intval($request->getParameter('collection_id', '0'));
    if (
      $collId === 0 &&
      $request->getParameter('module', '') === 'specimen' &&
      $request->getParameter('id',0) !== 0
    ) {
      $specimen = Doctrine_Core::getTable(DarwinTable::getModelForTable('specimens'))->find($request->getParameter('id'));
      $collId = $specimen->getCollectionRef();
    }
    if ( $collId > 0 ) {
      $collTmp = Doctrine_Core::getTable('Collections')->find($collId);
      if($collTmp)
      {
        $codeMask=$collTmp->getCodeMask();
      }
    }
    return $codeMask;
  }

  /**
   * Return a code mask for a given collection
   * @param \sfWebRequest $request
   * @return \sfView The code mask expected
   * @throws \sfError404Exception If the request is not ajax made
   */
  public function executeGetCodeMask(sfWebRequest $request) {
    $this->forward404Unless($request->isXmlHttpRequest());
    return $this->renderText($this->getCodeMask($request));
  }

  public function executeConfirm(sfWebRequest $request)
  {
  }

  public function executeGetStorage(sfWebRequest $request)
  {
    if($request->getParameter('item')=="container")
      $items = Doctrine_Core::getTable('Specimens')->getDistinctContainerStorages($request->getParameter('type'));
    else
      $items = Doctrine_Core::getTable('Specimens')->getDistinctSubContainerStorages($request->getParameter('type'));
    return $this->renderPartial('options', array('items'=> $items ));
  }

  public function executeAddCode(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $codeMask = $this->getCodeMask($request);
    $form = $this->getSpecimenForm($request,false,'id',array('code_mask'=>$codeMask));
    $form->addCodes($number, array('collection_ref' => $request->getParameter('collection_id', null)));
	  return $this->renderPartial('spec_codes',array('form' => $form['newCodes'][$number], 'rownum'=>$number, 'codemask'=> $codeMask));
  }

  public function executeAddCollector(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref')) ;
    $form = $this->getSpecimenForm($request);
    $form->addCollectors($number,array('people_ref' => $people_ref), $request->getParameter('iorder_by',0));
    return $this->renderPartial('spec_people_associations',array('type'=>'collector','form' => $form['newCollectors'][$number], 'row_num'=>$number));
  }

  public function executeAddBiblio(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $bibliography_ref = intval($request->getParameter('biblio_ref')) ;

    $form = $this->getSpecimenForm($request);
    $form->addBiblio($number, array( 'bibliography_ref' => $bibliography_ref), $request->getParameter('iorder_by',0));
    return $this->renderPartial('biblio_associations',array('form' => $form['newBiblio'][$number], 'row_num'=>$number));
  }

  public function executeAddDonator(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref')) ;
    $form = $this->getSpecimenForm($request);
    $form->addDonators($number, array('people_ref' => $people_ref), $request->getParameter('iorder_by',0));
    return $this->renderPartial('spec_people_associations',array('type'=>'donator','form' => $form['newDonators'][$number], 'row_num'=>$number));
  }

  public function executeAddComments(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenForm($request);
    $form->addComments($number, '');
    return $this->renderPartial('spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }

  public function executeAddExtLinks(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenForm($request);
    $form->addExtLinks($number);
    return $this->renderPartial('spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }

  public function executeAddSpecimensRelationships(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenForm($request);
    $form->addSpecimensRelationships($number, array());
    return $this->renderPartial('specimens_relationships', array('form' => $form['newSpecimensRelationships'][$number], 'rownum'=>$number));
  }

  public function executeAddIdentification(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $order_by = intval($request->getParameter('order_by',0));
    $spec_form = $this->getSpecimenForm($request, false, 'spec_id');
    $spec_form->addIdentifications($number, $order_by);
    return $this->renderPartial('spec_identifications',array('form' => $spec_form['newIdentification'][$number], 'row_num' => $number, 'module'=>'specimen', 'spec_id'=>$request->getParameter('spec_id',0),'individual_id'=>$request->getParameter('individual_id',0)));
  }

  public function executeAddIdentifier(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $spec_form = $this->getSpecimenForm($request, false, 'spec_id');
    $spec_form->loadEmbedIndentifications();
    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref')) ;
    $identifier_number = intval($request->getParameter('identifier_num'));
    $identifier_order_by = intval($request->getParameter('iorder_by',0));
    $ident = null;

    if($request->hasParameter('identification_id'))
    {
      $ident = $spec_form->getEmbeddedForm('Identifications')->getEmbeddedForm($number);
      $ident->addIdentifiers($identifier_number,$people_ref, $identifier_order_by);
      $spec_form->reembedIdentifications($ident, $number);
      return $this->renderPartial('spec_identification_identifiers',array('form' => $spec_form['Identifications'][$number]['newIdentifier'][$identifier_number], 'rownum'=>$identifier_number, 'identnum' => $number));
    }
    else
    {
      $spec_form->addIdentifications($number, 0);
      $ident = $spec_form->getEmbeddedForm('newIdentification')->getEmbeddedForm($number);
      $ident->addIdentifiers($identifier_number,$people_ref, $identifier_order_by);
      $spec_form->reembedNewIdentification($ident, $number);
      return $this->renderPartial('spec_identification_identifiers',array('form' => $spec_form['newIdentification'][$number]['newIdentifier'][$identifier_number], 'rownum'=>$identifier_number, 'identnum' => $number));
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    if ($request->hasParameter('duplicate_id')) // then it's a duplicate specimen
    {
      $specimen = new Specimens() ;
      $duplic = $request->getParameter('duplicate_id','0') ;
	  if(!$duplic && $request->hasParameter('split_id'))
	  {
		  $duplic = $request->getParameter('split_id','0') ;
	  }
      $specimen = $this->getRecordIfDuplicate($duplic,$specimen,true);
      // set all necessary widgets to visible
      if($request->hasParameter('all_duplicate'))
        Doctrine_Core::getTable('Specimens')->getRequiredWidget($specimen, $this->getUser()->getId(), 'specimen_widget',1);
      $this->form = new SpecimensForm($specimen);
      if($duplic)
      {
		$this->duplic=$duplic;
        $this->form->duplicate($duplic);

        //reembed identification
         $Identifications = Doctrine_Core::getTable('Identifications')->getIdentificationsRelated('specimens',$duplic) ;
        foreach ($Identifications as $key=>$val)
        {
          $identification = new Identifications() ;
          $identification = $this->getRecordIfDuplicate($val->getId(),$identification);
          $this->form->addIdentifications($key, $val->getOrderBy(), $identification);
          $Identifier = Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;
          foreach ($Identifier as $key2=>$val2)
          {
            $ident = $this->form->getEmbeddedForm('newIdentification')->getEmbeddedForm($key);
            $ident->addIdentifiers($key2,$val2->getPeopleRef(),0);
            $this->form->reembedNewIdentification($ident, $key);
          }
        }
        $tools = $specimen->SpecimensTools->toArray() ;
        if(count($tools))
        {
          $tab = array() ;
          foreach ($tools as $key=>$tool)
            $tab[] = $tool['collecting_tool_ref'] ;
            //ftheeten 2016 06 24
          $this->getUser()->setAttribute('callbackTools', $tab);
          $this->form->setDefault('collecting_tools_list',$tab);
        }

        $methods = $specimen->SpecimensMethods->toArray() ;
        if(count($methods))
        {
          $tab = array() ;
          foreach ($methods as $key=>$method)
            $tab[] = $method['collecting_method_ref'] ;
            //ftheeten 2016 06 24
          $this->getUser()->setAttribute('callbackMethods', $tab);
          $this->form->setDefault('collecting_methods_list',$tab);
        }
		
		$this->duplicateFlag=true;
        $this->duplicate_id=$duplic;
		if($request->hasParameter("part_id"))
		{
			if(is_numeric($request->getParameter("part_id")))
			{
				$this->part_id=$request->getParameter("part_id");
				$this->partFlag=true;				
			}		  
		}
      }      
      
    }
    else
    {
      $spec = new Specimens();
      $this->form = new SpecimensForm($spec);
    }
    $this->loadWidgets();
  }

  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $this->forward404Unless($request->isMethod('post'),'You must submit your data with Post Method');
    $spec = new Specimens();
    $this->form = new SpecimensForm($spec);
    $this->processForm($request, $this->form,'create');
    $this->loadWidgets();

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
  
    $spec_ids= $this->getIDFromCollectionNumber($request);
    if(count($spec_ids>0))
    {
        $spec_id=$spec_ids[0];
        $count=count($spec_ids);
	}
    else
    {
        $spec_id=-1;
        $count=0;   
    }
    $this->count=$count;
    $request->setParameter('id', $spec_id);
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    $codeMask = $this->getCodeMask($request);
    $this->form = $this->getSpecimenForm($request, true, 'id', array('code_mask'=>$codeMask));
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref',$spec_id, $this->getUser()->getId()))
        $this->redirect("specimen/view?id=".$request->getParameter('id')) ;
    }
    $this->loadWidgets();
    $this->setTemplate('new');
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->loadWidgets();

    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $codeMask = $this->getCodeMask($request);
    $this->form = $this->getSpecimenForm($request,true,'id',array('code_mask'=>$codeMask));

    $this->processForm($request, $this->form, 'update');

    $this->setTemplate('new');
  }

  protected function processForm(sfWebRequest $request, sfForm $form, $action = 'create' )
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $wasNew = $form->isNew();
        $autoCodeForUpdate = false;
        if (!$wasNew) {
          $collection = Doctrine_Core::getTable('Collections')->findOneById($form->getObject()->getCollectionRef());
          $autoCodeForUpdate = !$collection->getCodeAutoIncrementForInsertOnly();
        }
		
		
        $specimen = $form->save();
		if($request->hasParameter("part_id"))
		{
			$this->handleRelationsOfDuplicate( $request->getParameter("part_id",-1), $specimen->getId(), "part_of");
		}
		elseif($request->hasParameter("duplicate_id"))
		{
			if($request->getParameter("keep_duplicate","off")=="on")
			{
				$this->handleRelationsOfDuplicate($specimen->getId(), $request->getParameter("duplicate_id",-1));
			}
		}
        //ftheeten 2018 02 08
		$this->addCollectionCookie($specimen);
        if ($wasNew || $autoCodeForUpdate) {
          Doctrine_Core::getTable('Collections')->afterSaveAddCode($specimen->getCollectionRef(), $specimen->getId());
        }
        $this->redirect('specimen/edit?id='.$specimen->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        if($action == 'create') {
          //If Problem in saving embed forms set dirty state
          $form->getObject()->state('TDIRTY');
        }
        $e = new DarwinPgErrorParser($ne);
        $extd_message = '';
        if(preg_match('/unique constraint "unq_specimens"/i',$ne->getMessage()))
        {
          $dup_spec = Doctrine_Core::getTable('Specimens')->findDuplicate($form->getObject());
          if(!$dup_spec)
          {
              $this->logMessage('Duplicate Specimen not found: '. json_encode($form->getObject()->toArray()), 'err');
          }
          else
          {
            $extd_message = '<br /><a href="'.$this->getController()->genUrl( 'specimen/edit?id='.$dup_spec->getId() ).'">'.
              $this->getI18N()->__('Go the the original record')
              .'</a>';
          }
        }
        $error = new sfValidatorError(new savedValidator(),$e->getMessage().$extd_message);
        $form->getErrorSchema()->addError($error, 'Darwin2 :');
      }
    }
  }

  /**
    * Action executed when calling the expeditions from an other screen
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeChoose(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    // Initialization of the Search expedition form
    $this->form = new SpecimensSelfFormFilter(array('caller_id'=>$this->caller_id));
    // Remove surrounding layout
    $this->setLayout(false);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->setLevelAndCaller($request);
    // Initialization of the Search expedition form
    $this->form = new SpecimensSelfFormFilter(array('caller_id'=> $this->caller_id));
  }

  /**
    * Action executed when searching an expedition - trigger by the click on the search button
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    $this->forward404Unless($request->isMethod('post'));
    $this->setCommonValues('specimen', 'collection_name', $request);
    $item = $request->getParameter('searchSpecimen',array(''));
    // Instantiate a new specimen form
    $this->form = new SpecimensSelfFormFilter(array('caller_id'=>$item['caller_id']));
    // Triggers the search result function
    $this->searchResults($this->form, $request);
  }

  /**
    * Method executed when searching an expedition - trigger by the click on the search button
    * @param SpecimensSelfFormFilter $form    The search expedition form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults(SpecimensSelfFormFilter $form, sfWebRequest $request)
  {
    if($request->getParameter('searchSpecimen','') !== '')
    {
      // Bind form with data contained in searchExpedition array
      $form->bind($request->getParameter('searchSpecimen'));
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
           $this->specimens = $this->pagerLayout->execute();


        $specs = array();
        foreach($this->specimens as $specimen)
        {
          $specs[$specimen->getId()] = $specimen->getId();
        }
        $specCodes = Doctrine_Core::getTable('Codes')->getCodesRelatedArray('specimens', $specs);
        $this->codes = array();
        foreach($specCodes as $code)
        {
          if(! isset($this->codes[$code->getRecordId()]) )
            $this->codes[$code->getRecordId()] = array();
          $this->codes[$code->getRecordId()][] = $code;
        }
      }
    }
  }

  public function executeSameTaxon(sfWebRequest $request)
  {
    if($request->getParameter('specId') && $request->getParameter('taxonId'))
    {
      $result = Doctrine_Core::getTable('Specimens')->findOneById($request->getParameter('specId'));
      if($result)
      {
        return ($result->getTaxonRef() == $request->getParameter('taxonId'))?$this->renderText("ok"):$this->renderText("not ok");
      }
    }
    return $this->renderText("ok");
  }

  public function executeGetTaxon(sfWebRequest $request)
  {
    $this->forward404Unless($request->getParameter('specId') && $request->getParameter('targetField'));
    $targetField = $request->getParameter('targetField');
    $specimen = Doctrine_Core::getTable('Specimens')->findOneById($request->getParameter('specId'));
    $this->forward404Unless($specimen);
    return $this->renderText(json_encode(array($targetField=> $specimen->Taxonomy->getId(), $targetField.'_name' => $specimen->Taxonomy->getName()) ));
  }

  public function executeDelete(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();
    $spec = Doctrine_Core::getTable('Specimens')->find($request->getParameter('id'));
    $this->forward404Unless($spec, 'Specimen does not exist');
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(in_array($spec->getCollectionRef(),Doctrine_Core::getTable('Specimens')->hasRights('spec_ref',$request->getParameter('id'), $this->getUser()->getId())))
        $this->forwardToSecureAction();
    }
    try
    {
      $spec->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $this->form = new specimensForm($spec);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('new');
      return ;
    }
    $this->redirect('specimensearch/index');
  }

  public function executeView(sfWebRequest $request)
  {
	 
	 //ftheeten 2017 10 09 (replace $request->getParameter('id') by spec_id)
	$spec_ids= $this->getIDFromCollectionNumber($request);
    if(count($spec_ids>0))
    {
        $spec_id=$spec_ids[0];
        $count=count($spec_ids);
	}
    else
    {
        $spec_id=-1;
        $count=0;   
    }
    $this->specimen = Doctrine_Core::getTable('Specimens')->fetchOneWithRights($spec_id, $this->getUser());
    $this->count= $count;
    $this->hasEncodingRight = false;

    if($this->getUser()->isAtLeast(Users::ENCODER)) {
      if( $this->getUser()->isA(Users::ADMIN) ||
        Doctrine_Core::getTable('Specimens')->hasRights('spec_ref',$spec_id, $this->getUser()->getId())) {

        $this->hasEncodingRight = true;
      }
    }
    $this->forward404Unless($this->specimen,'Specimen does not exist');

    $this->loadWidgets(null,$this->specimen->getCollectionRef());
  }

  public function executeAddInsurance(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $form = new SpecimensForm();
    $form->addInsurances($number, array());
    return $this->renderPartial('specimen/insurances',array('form' => $form['newInsurances'][$number], 'rownum'=>$number));
  }

  public function executeEditMaintenance(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();

    //We edit a maintenance
    if($request->getParameter('id', null) !== null)
      $maint = Doctrine_Core::getTable('CollectionMaintenance')->find($request->getParameter('id'));
    //We add a maintenance
    elseif($request->getParameter('rid', null) !== null) {
      $maint = new CollectionMaintenance();
      $maint->setRecordId($request->getParameter('rid'));
      $maint->setReferencedRelation('specimens');
    }
    $this->forward404unless($maint);
    $this->form = new CollectionMaintenanceForm($maint);
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('collection_maintenance'));

      if($this->form->isValid())
      {
        try
        {
          $this->form->save();
          return $this->renderText('ok');
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
  }

  public function executeChoosePinned(sfWebRequest $request)
  {
    $items_ids = $this->getUser()->getAllPinned('specimen');
    $this->items = Doctrine_Core::getTable('Specimens')->getByMultipleIds($items_ids, $this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
  }
  
      //ftheeten 2018 02 08
   public function addCollectionCookie( $specimen)
   {
	    $this->getResponse()->setCookie('collection_ref_session',$specimen->getCollectionRef());
        $this->getResponse()->setCookie('institution_ref_session',$specimen->getCollections()->getInstitutionRef());
	   
   }
   
   //2019 04 24
   public function executeDeleteLinkedObject(sfWebRequest $request)
   {
	   $id=$request->getParameter('id', null);
	   $table=$request->getParameter('table', null);
	   if($id !== null && $table !==null ) 
	   {
		   $obj=Doctrine_Core::getTable($table)->find($id);
		   if($obj)
		   {
			   $obj->delete();
                 $this->getResponse()->setHttpHeader('Content-type','application/json');
				 return $this->renderText(json_encode(array("deleted"=> "yes") ));
		   }
		   else
		   {
               $this->getResponse()->setHttpHeader('Content-type','application/json');
			  return $this->renderText(json_encode(array("deleted"=> "no") ));			   
		   }
	   }
	      return sfView::NONE; 
	   
   }
   
      //JMherpers 2019 05 02
    public function executeGetCitesAndTaxonomy(sfWebRequest $request) {
		$taxonname=$request->getParameter('id', null);
		$cites = false;
		$ref_taxonomy = false;
		$taxonomyname = false;
		$cites2=0;
		$ref_taxonomy2="";
		$taxonomyname2 ="";
		
		$cites=Doctrine_Query::create()->
			select('t.cites')->
			from('Taxonomy t')->
			where('t.name = ?', $taxonname)->
			fetchOne();
			
		if($cites["cites"] == true ) {
			$cites2=1;
		}
		
		$ref_taxonomy=Doctrine_Query::create()->
			select('t.metadata_ref')->
			from('Taxonomy t')->
			where('t.name = ?', $taxonname)->
			fetchOne();
		
		if($ref_taxonomy["metadata_ref"] != 0 ) {
			$ref_taxonomy2=$ref_taxonomy["metadata_ref"];
		}
		
		$taxonomyname=Doctrine_Query::create()->
			select('tm.taxonomy_name')->
			from('TaxonomyMetadata tm')->
			where('tm.id = ?', $ref_taxonomy2)->
			fetchOne();
			
		if($taxonomyname["taxonomy_name"] != "" ) {
			$taxonomyname2=$taxonomyname["taxonomy_name"];
		}
			
		return $this->renderText($cites2."--".$taxonomyname2);
   }
   
    //JMherpers 2019 05 02
    public function executeGetNagoyaCollection(sfWebRequest $request) {
		$collectionid=$request->getParameter('id', null);
		$nagoya = false;
		$nagoya2="no";
		
		$nagoya=Doctrine_Query::create()->
			select('c.nagoya')->
			from('Collections c')->
			where('c.id = ?', $collectionid)->
			fetchOne();
			
		if($nagoya["nagoya"] == true ) {
			$nagoya2="yes";
		}
		/*if(!isset($nagoya["nagoya"]))
		{
			$nagoya2="not_found";
		}
		else if($nagoya["nagoya"] == true ) {
			$nagoya2="yes";
		}*/
		$this->getResponse()->setHttpHeader('Content-type','application/json');
		return $this->renderText(json_encode(array("nagoya"=>$nagoya2)));
	}
   
    //JMherpers 2019 05 02
    public function executeGetNagoyaGTU(sfWebRequest $request) {
		$gtuid=$request->getParameter('id', null);
		$nagoya = false;
		$nagoya2="no";
		
		$nagoya=Doctrine_Query::create()->
			select('g.nagoya')->
			from('gtu g')->
			where('g.id = ?', $gtuid)->
			fetchOne();

		if($nagoya["nagoya"] == true ) {
			$nagoya2="yes";
		}
		
		$this->getResponse()->setHttpHeader('Content-type','application/json');
		return $this->renderText(json_encode(array("nagoya"=>$nagoya2)));
   }
   
   #2020 01 14
   public function executeDoiview(sfWebRequest $request)
   {
        $spec_ids= $this->getIDFromCollectionNumber($request);
        if(count($spec_ids>0))
        {
            $spec_id=$spec_ids[0];
            $count=count($spec_ids);
        }
        else
        {
            $spec_id=-1;
            $count=0;   
        }
        $this->specimen = Doctrine_Core::getTable('Specimens')->fetchOneWithRights($spec_id, $this->getUser());
        $this->count= 1;
        
        $this->getResponse()->setHttpHeader('Content-type','text/xml');
        return $this->renderText($this->specimen->getXMLDataCite());
   }
   
  protected function handleRelationsOfDuplicate($src_id, $dest_id,$relationship_type="duplicated_from" )
  {
        
                        if($src_id!='-1'&&$dest_id!='-1')
                        {
							$obj=new SpecimensRelationships();
							$obj->setUnitType("specimens");
							$obj->setRelationshipType($relationship_type);
							$obj->setSpecimenRef($dest_id);
							$obj->setSpecimenRelatedRef($src_id);
							$obj->save();
							
                        }
                  
  }
  
  
  public function executeCopyCode(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
	 $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenForm($request);
	$testVal=$request->getParameter('id', null);
    if(strlen(trim($testVal))>0)
    {
		$resCodes=Doctrine_Core::getTable('Codes')->getCodesRelated("specimens", $testVal);
		foreach ($resCodes as $key=>$val)
        {
          $form->addCodes($number, $val,0, TRUE);
		  $this->renderPartial('spec_codes',array('form' => $form['newCodes'][$number], 'rownum'=>$number));
		  $number++;
        }		
	}
	 return $this->renderText("ok");
     
  }
  
}
