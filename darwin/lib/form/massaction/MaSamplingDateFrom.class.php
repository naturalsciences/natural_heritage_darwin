<?php

class MaSamplingDateForm extends sfForm
{
  public function configure()
  {
         $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd', 'hour'=>'hh', 'minute'=>'mm', 'second'=>'ss');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    //ftheeten 2018 11 30
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);
    
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate(array(
      
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => true
      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate(array(
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => true
      ),
      array('class' => 'to_date')
    );
    
     $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      'with_time' => true
      ),
      array('invalid' => 'Date provided is not valid',)
    );
    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorSchemaCompare(
          'gtu_from_date',
          '<=',
          'gtu_to_date',
          array('throw_global_error' => true),
          array('invalid'=>'The "begin" date cannot be above the "end" date.')
        )
      )
    ));
  }

 

  public function doGroupedAction($query,$values, $items)
  {
    $new_date_from = $values['gtu_from_date'];
    $query->set('s.gtu_from_date', '?', $new_date_from);
    $query->set('s.gtu_from_date_mask', '?', $values['gtu_from_date']->getMask());
    $new_date_to = $values['gtu_to_date'];
    $query->set('s.gtu_to_date', '?', $new_date_to);
    $query->set('s.gtu_to_date_mask', '?', $values['gtu_to_date']->getMask());
    return $query;
  }


}