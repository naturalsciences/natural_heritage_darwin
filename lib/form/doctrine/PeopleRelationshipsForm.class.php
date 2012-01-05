<?php

/**
 * PeopleRelationships form.
 *
 * @package    form
 * @subpackage PeopleRelationships
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class PeopleRelationshipsForm extends BasePeopleRelationshipsForm
{
  public function configure()
  {
    unset(
      $this['path'],
      $this['activity_date_from_mask'], 
      $this['activity_date_to_mask']
    );
    
    $this->widgetSchema['person_user_role'] = new sfWidgetFormInput();
    $this->widgetSchema['person_2_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['person_1_ref'] = new widgetFormJQueryDLookup(
      array(
	'model' => 'Institutions',
	'method' => 'getFamilyName',
	'nullable' => false,
        'fieldsHidders' => array('people_relationships_relationship_type', 
                                 'people_relationships_person_user_role', 
                                 'people_relationships_activity_date_from_day',
                                 'people_relationships_activity_date_from_day',
                                 'people_relationships_activity_date_from_month',
                                 'people_relationships_activity_date_to_year',
                                 'people_relationships_activity_date_to_month',
                                 'people_relationships_activity_date_to_year',
                                 ),
      ),
      array('class' => 'hidden',)
    );
    $this->widgetSchema['relationship_type'] = new sfWidgetFormChoice(array('choices' => PeopleRelationships::getPossibleTypes()));
    $this->Postvalidators = array();
    $this->initiateActivityItems();
    $this->validatorSchema->setPostValidator(new sfValidatorAnd($this->Postvalidators));
  }

  protected function initiateActivityItems()
  {
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['activity_date_from'] = new widgetFormJQueryFuzzyDate(
      array('culture'=>$this->getCurrentCulture(), 
            'image'=> '/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => $dateText,           
      ),                                      
      array('class' => 'from_date')                
    );      
                                      
    $this->widgetSchema['activity_date_to'] = new widgetFormJQueryFuzzyDate(
      array('culture'=>$this->getCurrentCulture(), 
            'image'=>'/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => $dateText,           
      ),                                      
      array('class' => 'to_date')                
    );      

    $this->validatorSchema['activity_date_from'] = new fuzzyDateValidator(
      array(
        'required' => false,                       
        'from_date' => true,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    $this->validatorSchema['activity_date_to'] = new fuzzyDateValidator(
      array(
        'required' => false,
        'from_date' => false,
        'min' => $minDate,
        'max' => $maxDate,
        'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    $this->Postvalidators[] = new sfValidatorSchemaCompare(
      'birth_date',
      '<=',
      'end_date',
      array('throw_global_error' => true),
      array('invalid'=>'The begin activity date cannot be above the end activity date.')
    );
  }
}
