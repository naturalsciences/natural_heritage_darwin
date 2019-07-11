<?php

class MaAcquisitionDateForm extends BaseForm
{
  public function configure()
  {
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);


    $this->widgetSchema['acquisition_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=> 'en',
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );

    $this->validatorSchema['acquisition_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['acquisition_date'];
    $query->set('s.acquisition_date', '?', $new_taxon);
    return $query;
  }

}
