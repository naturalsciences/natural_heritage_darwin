<?php

/**
 * GtuCountry form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GtuCountryForm extends BaseGtuCountryForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
   
	
	 $this->useFields(array('iso3166','name_en','name_1','name_2','name_3','name_4','historical_name_1','historical_name_2','historical_name_3','historical_name_4','lang_name_1','lang_name_2','lang_name_3','lang_name_4','lang_historical_name_1','historical_name_2','lang_historical_name_3','lang_historical_name_4', 'from_date', 'to_date', 'comments'));
	 
    
   
	$this->widgetSchema['iso3166'] = new sfWidgetFormInputText();
	$this->widgetSchema['name_en'] = new sfWidgetFormInputText();
	$this->widgetSchema['name_1'] = new sfWidgetFormInputText();
	$this->widgetSchema['name_2'] = new sfWidgetFormInputText();
	$this->widgetSchema['name_3'] = new sfWidgetFormInputText();
	$this->widgetSchema['name_4'] = new sfWidgetFormInputText();
	$this->widgetSchema['historical_name_1'] = new sfWidgetFormInputText();
	$this->widgetSchema['historical_name_2'] = new sfWidgetFormInputText();
	$this->widgetSchema['historical_name_3'] = new sfWidgetFormInputText();
	$this->widgetSchema['historical_name_4'] = new sfWidgetFormInputText();
	
	
	
	  $this->widgetSchema['lang_name_1'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getLangName1',
        'key_method' => 'getLangName1',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_name_1']  = new sfValidatorString(array('required'=>false));
	
	 $this->widgetSchema['lang_name_2'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getLangName2',
        'key_method' => 'getLangName2',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_name_2']  = new sfValidatorString(array('required'=>false));
	
	 $this->widgetSchema['lang_name_3'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getLangName3',
        'key_method' => 'getLangName3',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_name_3']  = new sfValidatorString(array('required'=>false));
	
	
	 $this->widgetSchema['lang_name_4'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getLangName4',
        'key_method' => 'getLangName4',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_name_4']  = new sfValidatorString(array('required'=>false));
	
	 $this->widgetSchema['lang_historical_name_1'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getHistoricalName1',
        'key_method' => 'getHistoricalName1',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_historical_name_1']  = new sfValidatorString(array('required'=>false));
	
	 $this->widgetSchema['lang_historical_name_2'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getHistoricalName2',
        'key_method' => 'getHistoricalName2',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_historical_name_2']  = new sfValidatorString(array('required'=>false));
	
	 $this->widgetSchema['lang_historical_name_3'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getHistoricalName3',
        'key_method' => 'getHistoricalName3',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_historical_name_3']  = new sfValidatorString(array('required'=>false));
	
	
	 $this->widgetSchema['lang_historical_name_4'] = new widgetFormSelectComplete(array(
        'model' => 'GtuCountry',
        'table_method' => 'getDistinctLanguages',
        'method' => 'getHistoricalName4',
        'key_method' => 'getHistoricalName4',
        'add_empty' => true,
       'change_label' => 'Pick a community in the list',
       'add_label' => 'Add another community',
   ));
    $this->validatorSchema['lang_historical_name_4']  = new sfValidatorString(array('required'=>false));
	 
	 $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd', 'hour'=>'hh', 'minute'=>'mm', 'second'=>'ss');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    //ftheeten 2018 11 30
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);
   
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => false
      ),
      array('class' => 'from_date')
    );
	
	   $this->validatorSchema['from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => false
      ),
      array('invalid' => 'Date provided is not valid',)
    );
	
	 $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => false
      ),
      array('class' => 'from_date')
    );
	
	   $this->validatorSchema['to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      'with_time' => false
      ),
      array('invalid' => 'Date provided is not valid',)
    );

  }
  
    protected function getDateItemOptions()
  {
    //ftheeten 2019 01 07 reverse range
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    return array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' =>$dateText,
	  "default"=>array('year'=>'', 'month'=>'', 'day'=>'')
    );
  }
}
