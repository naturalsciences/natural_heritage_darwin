<?php

/**
 * Expeditions form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team (darwin-ict@naturalsciences.be)
 *
 */
class ExpeditionsForm extends BaseExpeditionsForm
{
 /**
  * Configure the form with its widgets and validators
  *
  * @var   array         $yearsKeyVal    Array of years - constructed from two bound coming from configuration parameters
  * @var   array         $years          Array of years taking keys and values from $yearsKeyVal
  * @var   array         $dateText       Array constructed for default empty values that should be displayed in select boxes
  * @var   FuzzyDateTime $minDate        FuzzyDateTime object instantiated to define the date lower bound
  * @var   FuzzyDateTime $maxDate        FuzzyDateTime object instantiated to define the date upper bound
  * @var   FuzzyDateTime $dateLowerBound FuzzyDateTime object instantiated to define the lowest date possible
  * @var   FuzzyDateTime $dateUpperBound FuzzyDateTime object instantiated to define the upper date possible
  *
  */
  public function configure()
  {

    unset($this['name_ts'], 
          $this['name_indexed'], 
          $this['name_language_full_text'], 
          $this['expedition_from_date_mask'], 
          $this['expedition_to_date_mask']
         );

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition_from_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                                      'image'=>'/images/calendar.gif', 
                                                                                      'format' => '%day%/%month%/%year%', 
                                                                                      'years' => $years,
                                                                                      'empty_values' => $dateText,
                                                                                     ),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['expedition_to_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                                    'image'=>'/images/calendar.gif', 
                                                                                    'format' => '%day%/%month%/%year%', 
                                                                                    'years' => $years,
                                                                                    'empty_values' => $dateText, 
                                                                                   ),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => true, 'trim' => true));
    $this->validatorSchema['expedition_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',
                                                                                 )
                                                                           );
    $this->validatorSchema['expedition_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',
                                                                               )
                                                                         );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('expedition_from_date', 
                                                                          '<=', 
                                                                          'expedition_to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );

    $subForm = new sfForm();
    $this->embedForm('Members',$subForm);   
    foreach(Doctrine::getTable('CataloguePeople')->getPeopleRelated('expeditions','member',$this->getObject()->getId()) as $key=>$vals)
    {
      $form = new PeopleAssociationsForm($vals);
      $this->embeddedForms['Members']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('Members', $this->embeddedForms['Members']); 
    
    $subForm = new sfForm();
    $this->embedForm('newMember',$subForm);
  }

  public function addMember($num, $people_ref,$order_by=0 , $user = null)
  {
      $options = array('referenced_relation' => 'expeditions', 'people_type' => 'member', 'people_ref' => $people_ref, 'order_by' => $order_by);
      if(!$user)
       $val = new CataloguePeople();
      else
       $val = $user ; 
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new PeopleAssociationsForm($val);
      $this->embeddedForms['newMember']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newMember', $this->embeddedForms['newMember']);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newMember']))
    {
      foreach($taintedValues['newMember'] as $key=>$newVal)
      {
        if (!isset($this['newMember'][$key]))
        {
          $this->addMember($key,$newVal['people_ref']);
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
   if (null === $forms)
   {
      $value = $this->getValue('Members');
      foreach($this->embeddedForms['Members']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Members'][$name]);
        }
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
          $form->getObject()->setReferencedRelation('expeditions');
        }
      }

      $value = $this->getValue('newMember');
      foreach($this->embeddedForms['newMember']->getEmbeddedForms() as $name => $form)
      {
        $form->getObject()->setRecordId($this->getObject()->getId());
        $form->getObject()->setReferencedRelation('expeditions');
      } 
   }
   return parent::saveEmbeddedForms($con, $forms);
  }
}
