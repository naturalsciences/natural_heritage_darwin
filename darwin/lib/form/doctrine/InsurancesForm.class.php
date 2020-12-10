<?php

/**
 * Insurances form.
 *
 * @package    form
 * @subpackage Insurances
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class InsurancesForm extends BaseInsurancesForm
{
  public function configure()
  {
    $this->useFields(array('referenced_relation', 'record_id','insurance_currency', 'insurer_ref', 'contact_ref', 'insurance_value', 'date_from', 'date_to' ));

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->widgetSchema['insurance_currency'] = new widgetFormSelectComplete(array(
      'model' => 'Insurances',
      'table_method' => 'getDistinctCurrencies',
      'method' => 'getCurrencies',
      'key_method' => 'getCurrencies',
      'add_empty' => false,
      'change_label' => 'Pick a currency in the list',
      'add_label' => 'Add another currency'
    ));

    $this->widgetSchema['insurer_ref'] = new widgetFormJQueryDLookup(
      array(
        'model' => 'People',
        'method' => 'getFormatedName',
        'nullable' => true,
        'fieldsHidders' => array(
          'insurances_insurance_value',
          'insurances_insurance_currency',
          'insurances_insurance_currency_input',
          'insurances_contact_ref_name'
        ),
      ),
      array('class' => 'hidden',)
    );
    $this->widgetSchema['contact_ref'] = new widgetFormJQueryDLookup(
      array(
        'model' => 'People',
        'divname' => 'contact_',
        'method' => 'getFormatedName',
        'nullable' => true,
        'fieldsHidders' => array(
          'insurances_insurance_value',
          'insurances_insurance_currency',
          'insurances_insurance_currency_input',
          'insurances_insurer_ref_name'
        ),
      ),
      array('class' => 'hidden',)
    );
    $this->widgetSchema->setLabels(array(
      'insurance_value' => 'Value' ,
      'insurance_currency' => 'Currency',
      'insurer_ref' => 'Insurer',
      'contact_ref' => 'Person of contact'
    ));

					//JMHerpers 2018 02 15 Inversion of max and Min to have most recent dates on top
	$yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')),intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>  $this->getCurrentCulture(),
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => false ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=> $this->getCurrentCulture(),
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => false),
      array('class' => 'to_date')
    );

    $this->validatorSchema['date_from'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
      ),
      array('invalid' => 'Invalid date "from"')
    );

    $this->validatorSchema['date_to'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      'with_time' => true
      ),
      array('invalid' => 'Invalid date "to"')
    );

    $this->validatorSchema->setPostValidator(
      new sfValidatorSchemaCompare(
      'date_from',
      '<=',
      'date_to',
      array('throw_global_error' => true),
      array('invalid'=>'The "from" date cannot be above the "to" date.'))
    ) ;
  }
}
