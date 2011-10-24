<?php

/**
 * Individuals actions.
 *
 * @package    darwin
 * @subpackage individuals
 * @author     DB team <collections@naturalsciences.be>
 */
class individualsActions extends DarwinActions
{
  protected $widgetCategory = 'individuals_widget';
  protected $table = 'specimen_individuals';

  protected function getSpecimenIndividualsForm(sfWebRequest $request)
  {
    if(! $request->hasParameter('id'))
    {
      $this->spec_individual = new SpecimenIndividuals(); 
      $duplic = $request->getParameter('duplicate_id','0') ;          
      if ($duplic) // then it's a duplicate individual
      {
        $this->spec_individual = $this->getRecordIfDuplicate($duplic,$this->spec_individual);    
        // set all necessary widgets to visible 
        if($request->hasParameter('all_duplicate'))        
          Doctrine::getTable('SpecimenIndividuals')->getRequiredWidget($this->spec_individual, $this->getUser()->getId(), 'individuals_widget',1);
      }    
    }
    else
    {
      $this->spec_individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id'));
      $this->forward404Unless($this->spec_individual);
    }
    if($this->spec_individual->isNew())
    {
      $this->specimen = Doctrine::getTable('Specimens')->findExcept($request->getParameter('spec_id'));

      $this->forward404Unless($this->specimen);

      $this->spec_individual->Specimens = $this->specimen;

    }
    else
    {
      $this->specimen = Doctrine::getTable('Specimens')->findExcept($this->spec_individual->getSpecimenRef());
    }

    $individual = new SpecimenIndividualsForm($this->spec_individual);

    return $individual;
  }

  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if($request->hasParameter('id') && !$this->getUser()->isA(Users::ADMIN))
    {    
      if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))
        $this->redirect("individuals/view?id=".$request->getParameter('id')) ;  
    }
    $this->individual = $this->getSpecimenIndividualsForm($request);  

    if($this->individual->getObject()->isNew())
    {
      $duplic = $request->getParameter('duplicate_id') ;
      if($duplic)
      {
        // reembed duplicated comment
        $Comments = Doctrine::getTable('Comments')->findForTable('specimen_individuals',$duplic) ;
        foreach ($Comments as $key=>$val)
        {
          $comment = new Comments() ;
          $comment = $this->getRecordIfDuplicate($val->getId(),$comment); 
          $this->individual->addComments($key, $comment) ;          
        }
        // reembed duplicated external url
        $ExtLinks = Doctrine::getTable('ExtLinks')->findForTable('specimen_individuals',$duplic) ;
        foreach ($ExtLinks as $key=>$val)
        {
          $links = new ExtLinks() ;
          $links = $this->getRecordIfDuplicate($val->getId(),$links); 
          $this->individual->addExtLinks($key, $links) ;          
        }        
        //reembed identification
        $Identifications = Doctrine::getTable('Identifications')->getIdentificationsRelated('specimen_individuals',$duplic) ;
        foreach ($Identifications as $key=>$val)
        {
          $identification = new Identifications() ;
          $identification = $this->getRecordIfDuplicate($val->getId(),$identification); 
          $this->individual->addIdentifications($key, $val->getOrderBy(), $identification);
          $Identifier = Doctrine::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $val->getId()) ;     
          foreach ($Identifier as $key2=>$val2)
          {
            $ident = $this->individual->getEmbeddedForm('newIdentification')->getEmbeddedForm($key);
            $ident->addIdentifiers($key2,$val2->getPeopleRef(),0);        
            $this->individual->reembedNewIdentification($ident, $key);    
          }
        }
      }
    }
    if($request->isMethod('post'))
    {
      $this->individual->bind( $request->getParameter('specimen_individuals') );
      if( $this->individual->isValid())
      {
        try
        {
          $this->individual->save();
          $this->redirect('individuals/overview?spec_id='.$this->individual->getObject()->getSpecimenRef());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $extd_message = '';
          if(preg_match('/unique constraint "unq_specimen_individuals"/i',$ne->getMessage()))
          {
            $dup_spec = Doctrine::getTable('SpecimenIndividuals')->findDuplicate($this->individual->getObject());
            if(!$dup_spec)
            {
                $this->logMessage('Duplicate Individual not found: '. json_encode($this->individual->getObject()->toArray()), 'err');
            }
            else
            {
              $extd_message = '<br /><a href="'.$this->getController()->genUrl( 'individuals/edit?id='.$dup_spec->getId() ).'">'.
                $this->getI18N()->__('Go the the original record')
                .'</a>';
            }
          }
          $error = new sfValidatorError(new savedValidator(),$e->getMessage().$extd_message);
          $this->individual->getErrorSchema()->addError($error, 'Darwin2 :');
        }
      }
    }
    $this->loadWidgets();
  }

  public function executeOverview(sfWebRequest $request)
  {  
    $this->specimen = Doctrine::getTable('Specimens')->findExcept($request->getParameter('spec_id',0));
    $this->forward404Unless($this->specimen, sprintf('Specimen does not exist (%s).', $request->getParameter('spec_id',0)));
    $this->individuals = Doctrine::getTable('SpecimenIndividuals')->findBySpecimenRef($this->specimen->getId());
    $this->view_only = false ;    
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->view_only=true ;
    if(!$this->getUser()->isA(Users::ADMIN))
    {    
      if(! Doctrine::getTable('Specimens')->hasRights('spec_ref', $this->specimen->getId(), $this->getUser()->getId()))
 
      // if this user is not in collection Right, so the overview is displayed in readOnly
        $this->view_only = true;
    }
  }

  public function executeAddIdentification(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))

      $this->forwardToSecureAction();
    $number = intval($request->getParameter('num'));
    $order_by = intval($request->getParameter('order_by',0));
    $individual_form = $this->getSpecimenIndividualsForm($request);
    $individual_form->addIdentifications($number, $order_by);
    return $this->renderPartial('specimen/spec_identifications',
      array(
        'form' => $individual_form['newIdentification'][$number],
        'row_num' => $number,
        'module' => 'individuals',
        'spec_id' => $individual_form->getObject()->getSpecimenRef(),
        'id' => $request->getParameter('id',0),
        'individual_id' => 0,
    ));
  }

  public function executeAddIdentifier(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();  
    if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))

      $this->forwardToSecureAction();

    $this->spec_individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('individual_id'));
    $individual_form = new SpecimenIndividualsForm($this->spec_individual);

    $individual_form->loadEmbedIndentifications();

    $number = intval($request->getParameter('num'));
    $people_ref = intval($request->getParameter('people_ref')) ; 
    $identifier_number = intval($request->getParameter('identifier_num'));
    $identifier_order_by = intval($request->getParameter('iorder_by',0));
    $ident = null;

    if($request->hasParameter('identification_id'))
    {
      $ident = $individual_form->getEmbeddedForm('Identifications')->getEmbeddedForm($number);
      $ident->addIdentifiers($identifier_number,$people_ref, $identifier_order_by);
      $individual_form->reembedIdentifications($ident, $number);
      return $this->renderPartial('specimen/spec_identification_identifiers',array('form' => $individual_form['Identifications'][$number]['newIdentifier'][$identifier_number], 'rownum'=>$identifier_number, 'identnum' => $number));
    }
    else
    {
      $individual_form->addIdentifications($number, 0);
      $ident = $individual_form->getEmbeddedForm('newIdentification')->getEmbeddedForm($number);
      $ident->addIdentifiers($identifier_number,$people_ref, $identifier_order_by);
      $individual_form->reembedNewIdentification($ident, $number);
      return $this->renderPartial('specimen/spec_identification_identifiers',array('form' => $individual_form['newIdentification'][$number]['newIdentifier'][$identifier_number], 'rownum'=>$identifier_number, 'identnum' => $number));
    }
  }

  public function executeAddComments(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();  
    if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))
      $this->forwardToSecureAction();    
    $number = intval($request->getParameter('num'));
    $spec = null;

    if($request->hasParameter('id') && $request->getParameter('id'))
      $spec = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id') );
    $form = new SpecimenIndividualsForm($spec);
    $form->addComments($number);
    return $this->renderPartial('specimen/spec_comments',array('form' => $form['newComments'][$number], 'rownum'=>$number));
  }
 
  public function executeAddExtLinks(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))
      $this->forwardToSecureAction();    
    $number = intval($request->getParameter('num'));
    $spec = null;

    if($request->hasParameter('id') && $request->getParameter('id'))
      $spec = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id') );
    $form = new SpecimenIndividualsForm($spec);
    $form->addExtLinks($number);
    return $this->renderPartial('specimen/spec_links',array('form' => $form['newExtLinks'][$number], 'rownum'=>$number));
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER))
      $this->forwardToSecureAction();  
    if(!$this->getUser()->isA(Users::ADMIN))
    {
      if(! Doctrine::getTable('Specimens')->hasRights('individual_ref',$request->getParameter('id'), $this->getUser()->getId()))
        $this->forwardToSecureAction();
    }
    $ind = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id'));
    $this->forward404Unless($ind, 'Individual does not exist');
    try
    {
      $ind->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $request->checkCSRFProtection();
      $this->form = new specimenIndividualsForm($ind);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error); 
      $this->loadWidgets();
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('individuals/overview?spec_id='.$ind->getSpecimenRef());
  }  
  
  public function executeView(sfWebRequest $request)
  {
    $this->forward404Unless($this->individual = Doctrine::getTable('SpecimenIndividuals')->findExcept($request->getParameter('id')),'Individual does not exist');
    $this->loadWidgets(null,$this->individual->Specimens->getCollectionRef()); 
  } 
}
