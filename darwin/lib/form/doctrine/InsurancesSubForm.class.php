<?php

/**
 * Insurances form.
 *
 * @package    form
 * @subpackage Insurances
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class InsurancesSubForm extends BaseInsurancesForm
{
  public function configure()
  {
    $this->useFields(array('insurance_currency', 'insurer_ref', 'contact_ref', 'insurance_value', 'date_from', 'date_to' ));
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['insurance_currency'] = new widgetFormSelectComplete(array(
      'model' => 'Insurances',
      'table_method' => 'getDistinctCurrencies',
      'method' => 'getCurrencies',
      'key_method' => 'getCurrencies',
      'add_empty' => false,
      'change_label' => '',
      'add_label' => '',
    ));

    $this->widgetSchema['insurer_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'People',
      'link_url' => 'institution/choose',
      'method' => 'getFormatedName',
      'box_title' => $this->getI18N()->__('Choose Insurer'),
      'nullable' => true,
      'button_class'=>'add_insurance_insurer_ref',
      'complete_url' => 'catalogue/completeName?table=institutions',
     ),
      array('class'=>'inline',)
    );

    $this->widgetSchema['contact_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'People',
      'link_url' => 'people/choose',
      'method' => 'getFormatedName',
      'box_title' => $this->getI18N()->__('Choose contact'),
      'nullable' => true,
      'button_class'=>'add_insurance_contact_ref',
      'complete_url' => 'catalogue/completeName?table=people',
     ),
     array('class'=>'inline',)
    );

    $this->widgetSchema->setLabels(array(
      'insurance_value' => 'Value' ,
      'insurance_currency' => 'Currency',
      'insurer_ref' => 'Insurer:',
      'contact_ref' => 'Contact'
    ));

    $this->widgetSchema['insurance_currency']->setAttributes(array('class'=>'vsmall_size'));

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
      'with_time' => false
      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=> $this->getCurrentCulture(),
      'image'=> '/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'with_time' => false
      ),
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
        array('invalid'=>'The "from" date cannot be above the "to" date.')
      )
    );


    $this->validatorSchema['insurance_currency']->setOption('required', false);

    $this->validatorSchema['insurance_value'] = new sfValidatorNumber(array('required'=>false));

    /*Insurances post-validation to empty null values*/
    $this->mergePostValidator(new InsurancesValidatorSchema());

  }
  
}
