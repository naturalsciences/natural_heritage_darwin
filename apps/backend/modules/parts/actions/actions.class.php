<?php

/**
 * Parts actions.
 *
 * @package    darwin
 * @subpackage parts
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class partsActions extends DarwinActions
{
  protected $widgetCategory = 'part_widget';
  protected $table = 'specimen_parts';

  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if($request->hasParameter('id') && !$this->getUser()->isA(Users::ADMIN))
    {  
      $spec = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($request->getParameter('id'));
      if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('part_ref',
                                                                                                        $request->getParameter('id'), 
                                                                                                        $this->getUser()->getId())))
        $this->redirect("parts/view?id=".$request->getParameter('id')) ;    
    }
    $this->part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter('id'));
    if($this->part)
    {
      $this->individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($this->part->getSpecimenIndividualRef());
    }
    else
    {
      $this->part= new SpecimenParts();
      $this->individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('indid'));
      $this->forward404Unless($this->individual);
      $this->part->Individual = $this->individual;
      $duplic = $request->getParameter('duplicate_id') ;
      if ($duplic) // then it's a duplicate part
      {
        $this->part = $this->getRecordIfDuplicate($duplic, $this->part);
        // set all necessary widgets to visible 
        if($request->hasParameter('all_duplicate'))        
          Doctrine::getTable('SpecimenParts')->getRequiredWidget($this->part, $this->getUser()->getId(), 'part_widget',1);      
        
      }      
    }

    $this->specimen = Doctrine::getTable('Specimens')->findExcept($this->individual->getSpecimenRef());
    $this->form = new SpecimenPartsForm($this->part, array( 'collection'=>$this->specimen->getCollectionRef(),'individual'=>$this->individual->getId()));

    if($this->form->getObject()->isNew())
    {
      if($duplic)
      {
        $this->form->duplicate($duplic);
        // reembed duplicated insurances
        $Insurances = Doctrine::getTable('Insurances')->findForTable('specimen_parts',$duplic) ;
        foreach ($Insurances as $key=>$val)
        {
          $insurance = new Insurances() ;
          $insurance = $this->getRecordIfDuplicate($val->getId(),$insurance); 
          $this->form->addInsurances($key, $insurance) ;
        }
      }
    }
    if($request->isMethod('post'))
    {
      $this->form->bind( $request->getParameter('specimen_parts'), $request->getFiles('specimen_parts') );
      if( $this->form->isValid() )
      {
        try
        {
          $part = $this->form->save();
          $this->redirect('parts/edit?id='.$part->getId());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error, 'Darwin2 :');
        }
      }
    }
    $this->loadWidgets();
  }

  public function executeChoose(sfWebRequest $request)
  {
    $this->individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id'));
    $this->forward404Unless($this->individual);
    $this->parts = Doctrine::getTable('SpecimenParts')->findForIndividual($this->individual->getId());
    $parts_ids = array();
    foreach($this->parts as $part)
      $parts_ids[] = $part->getId();

    $codes_collection = Doctrine::getTable('Codes')->getCodesRelatedArray($this->table, $parts_ids);
    $this->codes = array();
    foreach($codes_collection as $code)
    {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
    }
  }

  public function executeExtendedInfo(sfWebRequest $request)
  {
    $this->part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter('id'));
    $codes_collection = Doctrine::getTable('Codes')->getCodesRelatedArray('part', $request->getParameter('id'));
    $this->codes = array();
    foreach($codes_collection as $code)
    {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
    }   
  
  }
  
  public function executeOverview(sfWebRequest $request)
  {
    $this->individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id'));
    $this->forward404Unless($this->individual);

    $this->specimen = Doctrine::getTable('Specimens')->findExcept($this->individual->getSpecimenRef());
    $this->forward404Unless($this->specimen);

    $this->parts = Doctrine::getTable('SpecimenParts')->findForIndividual($this->individual->getId());

    $parts_ids = array();
    foreach($this->parts as $part)
      $parts_ids[] = $part->getId();

    $codes_collection = Doctrine::getTable('Codes')->getCodesRelatedArray($this->table, $parts_ids);
    $this->codes = array();
    foreach($codes_collection as $code)
    {
      if(! isset($this->codes[$code->getRecordId()]))
        $this->codes[$code->getRecordId()] = array();
      $this->codes[$code->getRecordId()][] = $code;
    }
    $this->view = false ;    
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->view=true ;
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      $specimen = Doctrine::getTable('SpecimenSearch')->findOneByIndividualRef($request->getParameter('id',0));
      if(in_array($specimen->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('individual_ref',$request->getParameter('id'), $this->getUser()->getId())))  // if this user is not in collection Right, so the overview is displayed in readOnly
      $this->view = true;
    }
  }

  public function executeGetStorage(sfWebRequest $request)
  {
	  if($request->getParameter('item')=="container")
	    $items = Doctrine::getTable('SpecimenParts')->getDistinctContainerStorages($request->getParameter('type'));
	  else
	    $items = Doctrine::getTable('SpecimenParts')->getDistinctSubContainerStorages($request->getParameter('type'));
	  return $this->renderPartial('options', array('items'=> $items ));
  }

  protected function getSpecimenPartForm(sfWebRequest $request, $fwd404=false, $parameter='id')
  {
    $part = null;

    $collectionId = $request->getParameter('collection_id', null);

    if ($fwd404)
      $this->forward404Unless($part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter($parameter,0)), $this->getI18N('Specimen not found'));
    elseif($request->hasParameter($parameter) && $request->getParameter($parameter))
      $part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter($parameter));
    
    $form = new SpecimenPartsForm($part, array('collection'=>$collectionId));
    return $form;
  }

  public function executeAddCode(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenPartForm($request);
    $form->addCodes($number, array());
    return $this->renderPartial('specimen/spec_codes',array('form' => $form['newCodes'][$number], 'rownum'=>$number));
  }

  public function executeAddInsurance(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenPartForm($request);
    $form->addInsurances($number);
    return $this->renderPartial('parts/insurances',array('form' => $form['newInsurance'][$number], 'rownum'=>$number));
  }

  public function executeAddExtLinks(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $form = $this->getSpecimenPartForm($request);
    $form->addExtLinks($number,array());
    return $this->renderPartial('specimen/spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }

  public function executeAddComments(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $form = new SpecimenPartsForm();
    $form->addComments($number,array());
    return $this->renderPartial('specimen/spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }


  public function executeEditMaintenance(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $main = Doctrine::getTable('CollectionMaintenance')->find($request->getParameter('id'));
    $this->forward404unless($main);
    $this->form = new CollectionMaintenanceForm($main);
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
  
  public function executeDelete(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();  
    $spec = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($request->getParameter('id'));
    $this->forward404Unless($spec, 'Part does not exist');
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(in_array($spec->getCollectionRef(),Doctrine::getTable('Specimens')->testNoRightsCollections('part_ref',$request->getParameter('id'), $this->getUser()->getId())))
        $this->forwardToSecureAction();
    }
    $part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter('id'));    
    try
    {
      $part->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $request->checkCSRFProtection();
      $this->form = new specimenPartsForm($spec);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('parts/overview?id='.$spec->getIndividualRef());
  }    
  
  public function executeView(sfWebRequest $request)
  {
    $this->forward404Unless($this->specimen = Doctrine::getTable('SpecimenSearch')->findOneByPartRef($request->getParameter('id')),'Part does not exist');  
    $this->loadWidgets(null,$this->specimen->getCollectionRef()); 
  }

  public function executeChoosePinned(sfWebRequest $request)
  {
    /** @TODO: change this when flat_less branch is merged */
    $items_ids = $this->getUser()->getAllPinned('part');
    $this->items = Doctrine::getTable('SpecimenSearch')->getByMultipleIds($items_ids, 'part', $this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
    /** END TODO */


  }

  public function executeAddBiblio(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $bibliography_ref = intval($request->getParameter('biblio_ref')) ;
    $form = $this->getSpecimenPartForm($request);
    $form->addBiblio($number, array( 'bibliography_ref' => $bibliography_ref), $request->getParameter('iorder_by',0));
    return $this->renderPartial('specimen/biblio_associations',array('form' => $form['newBiblio'][$number], 'row_num'=>$number));
  }
}
