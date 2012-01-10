<?php

/**
 * Loans form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoansForm extends BaseLoansForm
{
  public function configure()
  {
    unset($this['description_ts']);
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));

    $this->widgetSchema['name'] = new sfWidgetFormInput();
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'from_date')
    );

    $this->validatorSchema['from_date'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => true,
        'min' => $minDate,
        'max' => $maxDate,
       // 'empty_value' => $dateLowerBound,
        'with_time' => false
      ),
      array('invalid' => 'Invalid date "from"')
    );


    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'from_date')
    );

    $this->validatorSchema['to_date'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => false,
        'min' => $minDate,
        'max' => $maxDate,
        'with_time' => false
      ),
      array('invalid' => 'Invalid date "to"')
    );

    $this->widgetSchema['effective_to_date'] = new widgetFormJQueryFuzzyDate(
      array(
        'culture'=> $this->getCurrentCulture(), 
        'image'=>'/images/calendar.gif', 
        'format' => '%day%/%month%/%year%', 
        'years' => $years,
        'with_time' => false
      ),
      array('class' => 'to_date')
    );

    $this->validatorSchema['effective_to_date'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => false,
        'min' => $minDate,
        'max' => $maxDate,
        'with_time' => false
      ),
      array('invalid' => 'Invalid date "effective"')
    );
    /* Labels */
    $this->widgetSchema->setLabels(array('from_date' => 'Start on',
                                         'to_date' => 'Ends on'
                                        )
                                  );    
    $this->widgetSchema['comment'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['actors'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['relatedfiles'] = new sfWidgetFormInputHidden(array('default'=>1));    
    $this->widgetSchema['users'] = new sfWidgetFormInputHidden(array('default'=>1));
    $this->widgetSchema['insurances'] = new sfWidgetFormInputHidden(array('default'=>1));    

    $this->validatorSchema['comment'] = new sfValidatorPass();
    $this->validatorSchema['actors'] = new sfValidatorPass();
    $this->validatorSchema['relatedfiles'] = new sfValidatorPass();
    $this->validatorSchema['users'] = new sfValidatorPass();
    $this->validatorSchema['insurances'] = new sfValidatorPass();               
  }
  
  // NOT CREATED IN ACTION AGAIN  
  public function addActorsSender($num, $obj=null)
  {
      if(! isset($this['newActorsSender'])) $this->loadEmbedActorsSender();
      $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId(), 'people_type' => 'sender');
      if (!$obj) $val = new CataloguePeople();
      else $val = $obj ;      
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
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'sender') as $key=>$vals)
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

  // NOT CREATED IN ACTION AGAIN
  public function addActorsReceiver($num, $obj=null)
  {
      if(! isset($this['newActorsReceiver'])) $this->loadEmbedActorsReceiver();
      $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId(), 'people_type' => 'receiver');
      if (!$obj) $val = new CataloguePeople();
      else $val = $obj ;      
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
      foreach(Doctrine::getTable('CataloguePeople')->findActors($this->getObject()->getId(),'receiver') as $key=>$vals)
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
  
  public function addComments($num, $obj=null)
  {
      if(! isset($this['newComments'])) $this->loadEmbedComments();
      $options = array('referenced_relation' => 'loans', 'record_id' => $this->getObject()->getId());
      if (!$obj) $val = new Comments();
      else $val = $obj ;      
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new CommentsSubForm($val,array('table' => 'loans', 'line_display' => true));
      $this->embeddedForms['newComments']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newComments', $this->embeddedForms['newComments']);
  }  
  
  public function loadEmbedComments()
  {
    if($this->isBound()) return;
    /* Comments sub form */
    $subForm = new sfForm();
    $this->embedForm('Comments',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('comments')->findForTable('loans', $this->getObject()->getId()) as $key=>$vals)
      {
        $form = new CommentsSubForm($vals,array('table' => 'loans'));
        $this->embeddedForms['Comments']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('Comments', $this->embeddedForms['Comments']);
    }

    $subForm = new sfForm();
    $this->embedForm('newComments',$subForm);
    
    /* Actors receiver form */
    $subForm = new sfForm();
    $this->embedForm('ActorsReceiver',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('cataloguePeople')->findActors($this->getObject()->getId(),'receiver') as $key=>$vals)
      {
        $form = new ActorsForm($vals);
        $this->embeddedForms['ActorsReceiver']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ActorsReceiver', $this->embeddedForms['ActorsReceiver']);
    }

    $subForm = new sfForm();
    $this->embedForm('newActorsReceiver',$subForm);  

    /* Actors sender form */
    $subForm = new sfForm();
    $this->embedForm('ActorsSender',$subForm);    
    if($this->getObject()->getId() !='')
    {
      foreach(Doctrine::getTable('cataloguePeople')->findActors($this->getObject()->getId(),'sender') as $key=>$vals)
      {
        $form = new ActorsForm($vals,array('table' => 'loans'));
        $this->embeddedForms['ActorsSender']->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm('ActorsSender', $this->embeddedForms['ActorsSender']);
    }

    $subForm = new sfForm();
    $this->embedForm('newActorsSender',$subForm);      
  } 
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    /* For each embedded informations 
     * test if the widget is on screen by testing a flag field present on the concerned widget
     * If widget is not on screen, remove the field from list of fields to be bound, and than potentially saved
    */

    if(!isset($taintedValues['comment']))
    {
      $this->offsetUnset('Comments');
      unset($taintedValues['Comments']);
      $this->offsetUnset('newComments');
      unset($taintedValues['newComments']);
    }
    else
    {
      $this->loadEmbedComments();
      if(isset($taintedValues['newComments']))
      {
        foreach($taintedValues['newComments'] as $key=>$newVal)
        {
          if (!isset($this['newComments'][$key]))
          {
            $this->addComments($key);
          }
          $taintedValues['newComments'][$key]['record_id'] = 0;
        }
      }
    }

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
            $this->addActorsSender($key);
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
            $this->addActorsReceiver($key);
          }
          $taintedValues['newActorsReceiver'][$key]['record_id'] = 0;
        }
      }
    }    
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms && $this->getValue('comment'))
    {
      $value = $this->getValue('newComments');
      foreach($this->embeddedForms['newComments']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['comment'] ))
          unset($this->embeddedForms['newComments'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
        }
      }
      $value = $this->getValue('Comments');
      foreach($this->embeddedForms['Comments']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['comment'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Comments'][$name]);
        }
      }
    }  
    if (null === $forms && $this->getValue('sender'))
    {
      $value = $this->getValue('newActorsSender');
      foreach($this->embeddedForms['newActorsSender']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['sender'] ))
          unset($this->embeddedForms['newActorsSender'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
        }
      }
      $value = $this->getValue('ActorsSender');
      foreach($this->embeddedForms['ActorsSender']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['sender'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['ActorsSender'][$name]);
        }
      }
    }     
    if (null === $forms && $this->getValue('receiver'))
    {
      $value = $this->getValue('newActorsReceiver');
      foreach($this->embeddedForms['newActorsReceiver']->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name]['receiver'] ))
          unset($this->embeddedForms['newActorsReceiver'][$name]);
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
        }
      }
      $value = $this->getValue('ActorsReceiver');
      foreach($this->embeddedForms['ActorsReceiver']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['receiver'] ))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['ActorsReceiver'][$name]);
        }
      }
    }        
    return parent::saveEmbeddedForms($con, $forms);
  }   
}
