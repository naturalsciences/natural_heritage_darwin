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
    unset($this['id']);

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    array_unshift($yearsKeyVal, 0);
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $years[0]='-';
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->widgetSchema['insurance_currency'] = new widgetFormSelectComplete(array('model' => 'Insurances',
                                                                                   'table_method' => 'getDistinctCurrencies',
                                                                                   'method' => 'getCurrencies',
                                                                                   'key_method' => 'getCurrencies',
                                                                                   'add_empty' => false,
                                                                                   'change_label' => 'Pick a currency in the list',
                                                                                   'add_label' => 'Add another currency',
                                                                                   )
                                                                            );
    $this->widgetSchema['insurance_year'] = new sfWidgetFormChoice(array('choices'  => $years,
                                                                        )
                                                                  );
    $this->widgetSchema['insurer_ref'] = new widgetFormJQueryDLookup(
      array(
	'model' => 'People',
	'method' => 'getFormatedName',
	'nullable' => true,
        'fieldsHidders' => array('insurances_insurance_value', 
                                 'insurances_insurance_currency', 
                                 'insurances_insurance_currency_input', 
                                 'insurances_insurance_year',),
      ),
      array('class' => 'hidden',)
    );

    $this->widgetSchema->setLabels(array('insurance_value' => 'Value' ,
                                         'insurance_currency' => 'Currency',
                                         'insurance_year' => 'Reference year',
                                         'insurer_ref' => 'Insurer'
                                        )
                                  );
    $this->validatorSchema['insurance_year'] = new sfValidatorChoice(array('choices' => $yearsKeyVal));

  }
  
}
