<?php

/**
 * Parts actions.
 *
 * @package    darwin
 * @subpackage parts
 * @author     DB team <collections@naturalsciences.be>
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
      if(! Doctrine::getTable('Specimens')->hasRights('part_ref',$request->getParameter('id'), $this->getUser()->getId()))
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
        // reembed duplicated comment
        $Comments = Doctrine::getTable('Comments')->findForTable('specimen_parts',$duplic) ;
        foreach ($Comments as $key=>$val)
        {
          $comment = new Comments() ;
          $comment = $this->getRecordIfDuplicate($val->getId(),$comment); 
          $this->form->addComments($key, $comment) ;          
        }
        // reembed duplicated external url
        $ExtLinks = Doctrine::getTable('ExtLinks')->findForTable('specimen_parts',$duplic) ;
        foreach ($ExtLinks as $key=>$val)
        {
          $links = new ExtLinks() ;
          $links = $this->getRecordIfDuplicate($val->getId(),$links); 
          $this->form->addExtLinks($key, $links) ;          
        }            
        // reembed duplicated codes
        $Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('specimen_parts',$duplic) ;
        foreach ($Codes as $key=>$val)
        {
           $code = new Codes() ;
           $code = $this->getRecordIfDuplicate($val->getId(),$code);  
           $this->form->addCodes($key,null,$code);
        } 
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
      $this->form->bind( $request->getParameter('specimen_parts') );
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
      if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))
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
    $collectionId = $request->getParameter('collection_id', null);
    $form = $this->getSpecimenPartForm($request);
    $form->addCodes($number, $collectionId);
    return $this->renderPartial('specimen/spec_codes',array('form' => $form['newCode'][$number], 'rownum'=>$number));
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
    $form->addExtLinks($number);
    return $this->renderPartial('specimen/spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }
    
  public function executeAddComments(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $number = intval($request->getParameter('num'));
    $form = new SpecimenPartsForm();
    $form->addComments($number);
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
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(! Doctrine::getTable('Specimens')->hasRights('part_ref',$request->getParameter('id'), $this->getUser()->getId()))
        $this->forwardToSecureAction();
    }
    $part = Doctrine::getTable('SpecimenParts')->findExcept($request->getParameter('id'));    
    $this->forward404Unless($part, 'Part does not exist');
    try
    {
      $part->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $request->checkCSRFProtection();
      $this->form = new specimenPartsForm($part);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('parts/overview?id='.$part->getSpecimenIndividualRef());
  }    
  
  public function executeView(sfWebRequest $request)
  {
    $this->part = Doctrine::getTable('SpecimenParts')->find($request->getParameter('id'));
    $this->forward404Unless($this->part,'Part does not exist');  
    $this->loadWidgets(null,$this->part->Individual->Specimens->getCollectionRef()); 
  }   


  public function executeChoosePinned(sfWebRequest $request)
  {
    $items_ids = $this->getUser()->getAllPinned('part');
    $this->items = Doctrine::getTable('Specimens')->getByMultipleIds($items_ids,'part', $this->getUser()->getId(), $this->getUser()->isAtLeast(Users::ADMIN));
  }
}
