<?php

/**
 * Loans form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoansForm extends BaseLoansForm
{
  public function configure()
  {
    unset($this['search_indexed']);
						//JMHerpers 2018 02 15 Inversion of max and Min to have most recent dates on top
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')),1970);
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');

    $this->widgetSchema['name'] = new sfWidgetFormInput();
    //rmca 2016 06 16
    $this->widgetSchema['name']->setAttribute('class', 'loan_class');
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false,
        'empty_values' => $dateText,
	//rmca 2016 12 01
 	'default' => date("m/d/Y"), 


      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false,
        'empty_values' => $dateText,
      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['extended_to_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false,
        'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );

    $this->widgetSchema['sender'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['receiver'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['users'] = new sfWidgetFormInputHidden(array('default'=>1));
	
    /* Input file for related files */
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));        

    /*Validators*/

    $this->validatorSchema['name'] = new sfValidatorString(array('required' => true)) ;
    $this->validatorSchema['from_date'] = new sfValidatorDate(
      array(
        'required' => false,
        'min' => $minDate->getDateTime(),
        'date_format' => 'd/M/Y',
      ),
      array('invalid' => 'Invalid date "from"')
    );
    $this->validatorSchema['to_date'] = new sfValidatorDate(
      array(
        'required' => false,
        'min' => $minDate->getDateTime(),
        'date_format' => 'd/M/Y',
      ),
      array('invalid' => 'Invalid date "to"')
    );
    $this->validatorSchema['extended_to_date'] = new sfValidatorDate(
      array(
        'required' => false,
        'min' => $minDate->getDateTime(),
        'date_format' => 'd/M/Y',
      ),
      array('invalid' => 'Invalid date "Extended"')
    );

    $this->validatorSchema['sender'] = new sfValidatorPass();
    $this->validatorSchema['receiver'] = new sfValidatorPass();
    $this->validatorSchema['users'] = new sfValidatorPass();
    //Loan form is submited to upload file, when called like that we don't want some fields to be required
    $this->validatorSchema['filenames'] = new sfValidatorPass();

    $this->mergePostValidator( new LoanValidatorDates()) ;

    /*Labels*/

    $this->widgetSchema->setLabels(array('from_date' => 'Starts on',
                                         'to_date' => 'Ends on',
                                         'filenames' => 'Add File'
                                        )
                                  );


    $this->validatorSchema['Comments_holder'] = new sfValidatorPass();
    $this->widgetSchema['Comments_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['RelatedFiles_holder'] = new sfValidatorPass();
    $this->widgetSchema['RelatedFiles_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Insurances_holder'] = new sfValidatorPass();
    $this->widgetSchema['Insurances_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

	//testft opv 2016 06 14
   	/* Collection Reference */
    	$this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(
		array(
      			'model' => 'Collections',
      		'link_url' => 'collection/choose',
      		'method' => 'getName',
      		'box_title' => $this->getI18N()->__('Choose Collection'),
      		'button_class'=>'',
      		'complete_url' => 'catalogue/completeName?table=collections',
    		),
		array(
			'class'=>'rmca_coll_4_loan',

		));
	$this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));
	
	//jim herpers 2018 03 26
	$this->widgetSchema['institution_receiver'] = new widgetFormSelectComplete(array('model' => 'Loans',
                                                                        'table_method' => 'getDistinctInstitutions',
                                                                        'method' => 'getFormatedName',
                                                                        'key_method' =>  'getValinstit',
                                                                        'add_empty' => true,
                                                                        'change_label' => 'Pick an institution in the list',
                                                                        'add_label' => 'Add another institution',
                                                                       )
                                                                 );
	
	$this->widgetSchema['address_receiver'] = new sfWidgetFormInput();
    $this->widgetSchema['address_receiver']->setAttribute('class', 'large_size');
   	$this->widgetSchema['zip_receiver'] = new sfWidgetFormInput();
    $this->widgetSchema['zip_receiver']->setAttribute('class', 'small_size');
	$this->widgetSchema['city_receiver'] = new sfWidgetFormInput();
    $this->widgetSchema['city_receiver']->setAttribute('class', 'small_size');

	$this->widgetSchema['country_receiver'] = new widgetFormSelectComplete(array('model' => 'Loans',
                                                                        'table_method' => 'getDistinctCountries',
                                                                        'method' => 'getCountries',
                                                                        'key_method' => 'getCountries',
                                                                        'add_empty' => true,
                                                                        'change_label' => 'Pick a country in the list',
                                                                        'add_label' => 'Add another country',
                                                                       )
                                                                 );
    #2019 02 26                                                             
    $this->widgetSchema['collection_manager'] = new sfWidgetFormInput();
    $this->widgetSchema['collection_manager']->setDefault("Didier van den Spiegel");
    $this->validatorSchema['collection_manager'] = new sfValidatorString(array('required' => true)) ;
    
    $this->widgetSchema['collection_manager_title'] = new sfWidgetFormInput();
    $this->widgetSchema['collection_manager_title']->setDefault("Head of collections");
    $this->validatorSchema['collection_manager_title'] = new sfValidatorString(array('required' => true)) ;
    
    $this->widgetSchema['collection_manager_mail'] = new sfWidgetFormInput();
    $this->widgetSchema['collection_manager_mail']->setDefault("didier.van.den.spiegel@africamuseum.be");
    $this->validatorSchema['collection_manager_mail'] = new sfValidatorString(array('required' => true)) ;
    
    
    $this->widgetSchema['non_cites'] = new sfWidgetFormInputCheckBox();
    $this->widgetSchema['non_cites']->setDefault(true);
    $this->validatorSchema['non_cites']== new sfValidatorBoolean() ;
    //$this->widgetSchema['collection_manager']->setAttribute();                                                          
                                                                 
  }
  
  public function addUsers($num, $user_ref, $order_by=0)
  {
    if(! isset($this['newUsers'])) $this->loadEmbedUsers();
    $val = new LoanRights();
    $val->setUserRef($user_ref) ;
    $val->setLoanRef($this->getObject()->getId());
    $form = new LoanRightsForm($val);
    $this->embeddedForms['newUsers']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newUsers', $this->embeddedForms['newUsers']);
  }

  public function loadEmbedUsers()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Users',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('LoanRights')->findByLoanRef($this->getObject()->getId()) as $key=>$vals)
      {
        $form = new LoanRightsForm($vals);
        $this->embeddedForms['Users']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Users', $this->embeddedForms['Users']);
    }

    $subForm = new sfForm();
    $this->embedForm('newUsers',$subForm);
  }  
  
  public function addActorsSender($num, $people_ref, $order_by=0)
  {
    if(! isset($this['newActorsSender'])) $this->loadEmbedActorsSender();
    $options = array('referenced_relation' => 'loans', 'people_ref' => $people_ref, 'people_type' => 'sender', 'order_by' => $order_by);
    $val = new CataloguePeople();
    $val->fromArray($options);
    $val->setRecordId($this->getObject()->getId());
    $form = new ActorsForm($val);
    $this->embeddedForms['newActorsSender']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newActorsSender', $this->embeddedForms['newActorsSender']);
  }

  public function loadEmbedActorsSender()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('ActorsSender',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'sender','loans') as $key=>$vals)
      {
        $form = new ActorsForm($vals);
        $this->embeddedForms['ActorsSender']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ActorsSender', $this->embeddedForms['ActorsSender']);
    }

    $subForm = new sfForm();
    $this->embedForm('newActorsSender',$subForm);
  } 

  public function addActorsReceiver($num, $people_ref, $order_by=0)
  {
    if(! isset($this['newActorsReceiver'])) $this->loadEmbedActorsReceiver();
    $options = array('referenced_relation' => 'loans', 'people_ref' => $people_ref, 'people_type' => 'receiver', 'order_by' => $order_by);
    $val = new CataloguePeople();     
    $val->fromArray($options);
    $val->setRecordId($this->getObject()->getId());
    $form = new ActorsForm($val);
    $this->embeddedForms['newActorsReceiver']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newActorsReceiver', $this->embeddedForms['newActorsReceiver']);
  }
   
  public function loadEmbedActorsReceiver()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('ActorsReceiver',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'receiver','loans') as $key=>$vals)
      {
        $form = new ActorsForm($vals);
        $this->embeddedForms['ActorsReceiver']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ActorsReceiver', $this->embeddedForms['ActorsReceiver']);
    }

    $subForm = new sfForm();
    $this->embedForm('newActorsReceiver',$subForm);
  }

  public function addInsurances($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Insurances', new InsurancesSubForm(DarwinTable::newObjectFromArray('Insurances',$options)), $num);
  }

  public function addRelatedFiles($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('RelatedFiles', new MultimediaForm(DarwinTable::newObjectFromArray('Multimedia',$options)), $num);
  }

  public function addComments($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Comments', new CommentsSubForm(DarwinTable::newObjectFromArray('Comments',$options)), $num);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations 
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved*/

    if(!isset($taintedValues['sender']))
    {
      $this->offsetUnset('ActorsSender');
      unset($taintedValues['ActorsSender']);
      $this->offsetUnset('newActorsSender');
      unset($taintedValues['newActorsSender']);
    }
    else
    {
      $this->loadEmbedActorsSender();
      if(isset($taintedValues['newActorsSender']))
      {
        foreach($taintedValues['newActorsSender'] as $key=>$newVal)
        {
          if (!isset($this['newActorsSender'][$key]))
          {
            $this->addActorsSender($key,$newVal['people_ref'],$newVal['order_by']);
          }
          $taintedValues['newActorsSender'][$key]['record_id'] = 0;
        }
      }
    }

    if(!isset($taintedValues['users']))
    {
      $this->offsetUnset('Users');
      unset($taintedValues['Users']);
      $this->offsetUnset('newUsers');
      unset($taintedValues['newUsers']);
    }
    else
    {
      $this->loadEmbedUsers();
      if(isset($taintedValues['newUsers']))
      {
        foreach($taintedValues['newUsers'] as $key=>$newVal)
        {
          if (!isset($this['newUsers'][$key]))
          {
            $this->addUsers($key,$newVal['user_ref']);
          }
          $taintedValues['newUsers'][$key]['loan_ref'] = 0;
        }
      }
    }

    if(!isset($taintedValues['receiver']))
    {
      $this->offsetUnset('ActorsReceiver');
      unset($taintedValues['ActorsReceiver']);
      $this->offsetUnset('newActorsReceiver');
      unset($taintedValues['newActorsReceiver']);
    }
    else
    {
      $this->loadEmbedActorsReceiver();
      if(isset($taintedValues['newActorsReceiver']))
      {
        foreach($taintedValues['newActorsReceiver'] as $key=>$newVal)
        {
          if (!isset($this['newActorsReceiver'][$key]))
          {
            $this->addActorsReceiver($key,$newVal['people_ref'],$newVal['order_by']);
          }
          $taintedValues['newActorsReceiver'][$key]['record_id'] = 0;
        }
      }
    }

    $this->bindEmbed('Insurances', 'addInsurances' , $taintedValues);
    $this->bindEmbed('Comments', 'addComments' , $taintedValues);
    $this->bindEmbed('RelatedFiles', 'addRelatedFiles' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);   
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Comments', 'comment' ,$forms, array('referenced_relation'=>'loans', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'loans', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'loans', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Insurances', 'insurance_value' ,$forms, array('referenced_relation'=>'loans', 'record_id' => $this->getObject()->getId()));

    if (null === $forms && $this->getValue('sender'))
    {
      $value = $this->getValue('newActorsSender');

      foreach($this->embeddedForms['newActorsSender']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['people_ref'] ))
          unset($this->embeddedForms['newActorsSender'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
		  //JMHerpers 2018 04 10 change from 128 to 4 (contact)
          if(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(4));
        }
      }
      $value = $this->getValue('ActorsSender');
      foreach($this->embeddedForms['ActorsSender']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['ActorsSender'][$name]);
        }
		//JMHerpers 2018 04 10 change from 128 to 4 (contact)
        elseif(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(4));
      }
    } 
    if (null === $forms && $this->getValue('users'))
    {
      $value = $this->getValue('newUsers');
      foreach($this->embeddedForms['newUsers']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['user_ref'] )) {
          unset($this->embeddedForms['newUsers'][$name]);
        } //On add, current user will be added by trigger
        elseif($value[$name]['user_ref'] != sfContext::getInstance()->getUser()->getId() || sfContext::getInstance()->getUser()->isAtLeast(Users::ADMIN) )
        { 
          $form->getObject()->setLoanRef($this->getObject()->getId());
        }
        else unset($this->embeddedForms['newUsers'][$name]);
      }
      $value = $this->getValue('Users');
      foreach($this->embeddedForms['Users']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['user_ref'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Users'][$name]);
        }
      }
    }
    if (null === $forms && $this->getValue('receiver'))
    {
      $value = $this->getValue('newActorsReceiver');
      foreach($this->embeddedForms['newActorsReceiver']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['people_ref'] ))
          unset($this->embeddedForms['newActorsReceiver'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
		  //JMHerpers 2018 04 10 change from 128 to 4 (contact)
          if(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(4));
        }
      }
      $value = $this->getValue('ActorsReceiver');
      foreach($this->embeddedForms['ActorsReceiver']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['ActorsReceiver'][$name]);
        }
		//JMHerpers 2018 04 10 change from 128 to 4 (contact)
        elseif(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(4));
      }
    }

    return parent::saveEmbeddedForms($con, $forms);
  }

  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Comments' )
      return Doctrine::getTable('Comments')->findForTable('loans', $record_id);
    if( $emFieldName =='RelatedFiles' )
      return Doctrine::getTable('Multimedia')->findForTable('loans', $record_id);
    if( $emFieldName =='Insurances' )
      return Doctrine::getTable('Insurances')->findForTable('loans', $record_id);
  }

  public function duplicate($id)
  {
    // reembed duplicated comment
    $Comments = Doctrine::getTable('Comments')->findForTable('loans',$id) ;
    foreach ($Comments as $key=>$val)
    {
      $comment = new Comments();
      $comment->fromArray($val->toArray());
      $form = new CommentsSubForm($comment);
      $this->attachEmbedRecord('Comments', $form, $key);
    }

    // reembed duplicated insurances
    $Insurances = Doctrine::getTable('Insurances')->findForTable('loans',$id) ;
    foreach ($Insurances as $key=>$val)
    {
      $insurance = new Insurances() ;
      $insurance->fromArray($val->toArray());
      $form = new InsurancesSubForm($insurance);
      $this->attachEmbedRecord('Insurances', $form, $key);
    }
  }

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName =='Comments' )
      return new CommentsSubForm($values);
    if( $emFieldName =='RelatedFiles' )
      return new MultimediaForm($values);
    if( $emFieldName =='Insurances' )
      return new InsurancesSubForm($values);
  }

  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/jquery-datepicker-lang.js';
    $javascripts[]='/js/button_ref.js'; 
    $javascripts[]='/js/catalogue_people.js';   
    $javascripts[]='/js/ui.complete.js';
    return $javascripts;
  }

  public function getStylesheets()
  {
    $javascripts=parent::getStylesheets();
    $javascripts['/css/ui.datepicker.css']='all';
    return $javascripts;
  }
}
