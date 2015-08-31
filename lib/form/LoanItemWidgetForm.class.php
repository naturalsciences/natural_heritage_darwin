<?php

/**
 * LoanItems form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoanItemWidgetForm extends BaseLoanItemsForm
{
  public function configure()
  {
    $this->useFields(array('id'));
    /* input file for related files */
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setLabel("Add File") ;
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));        
    $this->validatorSchema['filenames'] = new sfValidatorFile(
      array(
          'required' => false,
          'validated_file_class' => 'myValidatedFile'
      ));
    $this->widgetSchema['sender'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['receiver'] = new sfWidgetFormInputHidden(array('default'=>1));        
    $this->widgetSchema['relatedfile'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['insurance'] = new sfWidgetFormInputHidden(array('default'=>1));    

    $this->validatorSchema['sender'] = new sfValidatorPass();
    $this->validatorSchema['receiver'] = new sfValidatorPass();    
    $this->validatorSchema['relatedfile'] = new sfValidatorPass();
    $this->validatorSchema['insurance'] = new sfValidatorPass();
    $this->widgetSchema->setNameFormat('loan_items[%s]');

    $this->validatorSchema['Codes_holder'] = new sfValidatorPass();
    $this->widgetSchema['Codes_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->widgetSchema['prefix_separator'] = new sfWidgetFormDoctrineChoice(array(
                                                                               'model' => 'Codes',
                                                                               'table_method' => 'getDistinctPrefixSep',
                                                                               'method' => 'getCodePrefixSeparator',
                                                                               'key_method' => 'getCodePrefixSeparator',
                                                                               'add_empty' => true,
                                                                             ));

    $this->widgetSchema['prefix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->widgetSchema['suffix_separator'] = new sfWidgetFormDoctrineChoice(array(
                                                                               'model' => 'Codes',
                                                                               'table_method' => 'getDistinctSuffixSep',
                                                                               'method' => 'getCodeSuffixSeparator',
                                                                               'key_method' => 'getCodeSuffixSeparator',
                                                                               'add_empty' => true,
                                                                             ));

    $this->widgetSchema['suffix_separator']->setAttributes(array('class'=>'vvsmall_size'));

    $this->validatorSchema['prefix_separator'] = new sfValidatorPass();
    $this->validatorSchema['suffix_separator'] = new sfValidatorPass();


    $this->validatorSchema['Comments_holder'] = new sfValidatorPass();
    $this->widgetSchema['Comments_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['RelatedFiles_holder'] = new sfValidatorPass();
    $this->widgetSchema['RelatedFiles_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->validatorSchema['Insurances_holder'] = new sfValidatorPass();
    $this->widgetSchema['Insurances_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
  }

  public function addActorsSender($num, $people_ref, $order_by=0)
  {
    if(! isset($this['newActorsSender'])) $this->loadEmbedActorsSender();
    $options = array('referenced_relation' => 'loan_items', 'people_ref' => $people_ref, 'people_type' => 'sender', 'order_by' => $order_by);
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
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'sender','loan_items') as $key=>$vals)
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
    $options = array('referenced_relation' => 'loan_items', 'people_ref' => $people_ref, 'people_type' => 'receiver', 'order_by' => $order_by, 'record_id' => $this->getObject()->getId());
    $val = new CataloguePeople();     
    $val->fromArray($options);
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
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'receiver','loan_items') as $key=>$vals)
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
    $options = array('referenced_relation' => 'loan_items', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Insurances', new InsurancesSubForm(DarwinTable::newObjectFromArray('Insurances',$options)), $num);
  }

  public function addComments($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loan_items', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Comments', new CommentsSubForm(DarwinTable::newObjectFromArray('Comments',$options)), $num);
  }

  public function addRelatedFiles($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loan_items', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('RelatedFiles', new MultimediaForm(DarwinTable::newObjectFromArray('Multimedia',$options)), $num);
  }

  public function addCodes($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'loan_items', 'record_id' => $this->getObject()->getId());
    $options = array_merge($values, $options);
    $this->attachEmbedRecord('Codes', new CodesSubForm(DarwinTable::newObjectFromArray('Codes',$options)), $num);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations 
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved
    */
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
    $this->bindEmbed('Codes', 'addCodes' , $taintedValues);
    $this->bindEmbed('Comments', 'addComments' , $taintedValues);
    $this->bindEmbed('RelatedFiles', 'addRelatedFiles' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);   
  }

  public function save($con = null)
  {
    $this->offsetUnset('filenames');
    return parent::save($con);
  }
  
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Comments', 'comment' ,$forms, array('referenced_relation'=>'loan_items', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('RelatedFiles', 'mime_type' ,$forms, array('referenced_relation'=>'loan_items', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Codes', 'code' ,$forms, array('referenced_relation'=>'loan_items', 'record_id' => $this->getObject()->getId()));
    $this->saveEmbed('Insurances', 'insurance_value' ,$forms, array('referenced_relation'=>'loan_items', 'record_id' => $this->getObject()->getId()));


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
          if(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(128));
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
        elseif(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(128));        
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
          if(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(128));
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
        elseif(!is_array($value[$name]['people_sub_type'])) $form->getObject()->setPeopleSubType(array(128));        
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
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


  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Comments' )
      return Doctrine::getTable('Comments')->findForTable('loan_items', $record_id);
    if( $emFieldName =='RelatedFiles' )
      return Doctrine::getTable('Multimedia')->findForTable('loan_items', $record_id);
    if( $emFieldName =='Insurances' )
      return Doctrine::getTable('Insurances')->findForTable('loan_items', $record_id);
    if( $emFieldName =='Codes' )
      return Doctrine::getTable('Codes')->getCodesRelated('loan_items', $record_id);
  }

  public function duplicate($id)
  {
    // reembed duplicated comment
    $Comments = Doctrine::getTable('Comments')->findForTable('loan_items',$id) ;
    foreach ($Comments as $key=>$val)
    {
      $comment = new Comments();
      $comment->fromArray($val->toArray());
      $form = new CommentsSubForm($comment);
      $this->attachEmbedRecord('Comments', $form, $key);
    }
    // reembed duplicated codes
    $Codes = Doctrine::getTable('Codes')->getCodesRelatedArray('loan_items',$id) ;
    foreach ($Codes as $key=> $code)
    {
      $newCode = new Codes();
      $newCode->fromArray($code->toArray());
      $form = new CodesSubForm($newCode);
      $this->attachEmbedRecord('Codes', $form, $key);
    }
    // reembed duplicated insurances
    $Insurances = Doctrine::getTable('Insurances')->findForTable('loan_items',$id) ;
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
    if( $emFieldName =='Codes' )
      return new CodesSubForm($values);
  }

}
