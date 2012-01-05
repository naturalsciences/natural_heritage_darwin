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

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    array_unshift($yearsKeyVal, 0);
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $years[0]='-';
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required'=>false));
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->widgetSchema['insurance_currency'] = new widgetFormSelectComplete(array('model' => 'Insurances',
                                                                                   'table_method' => 'getDistinctCurrencies',
                                                                                   'method' => 'getCurrencies',
                                                                                   'key_method' => 'getCurrencies',
                                                                                   'add_empty' => false,
                                                                                   'change_label' => '',
                                                                                   'add_label' => '',
                                                                                   )
                                                                            );
    $this->widgetSchema['insurance_year'] = new sfWidgetFormChoice(array('choices'  => $years,
                                                                        )
                                                                  );
    $this->widgetSchema['insurer_ref'] = new widgetFormButtonRef(array(
       'model' => 'People',
       'link_url' => 'institution/choose',
       'method' => 'getFormatedName',
       'box_title' => $this->getI18N()->__('Choose Insurer'),
       'nullable' => true,
       'button_class'=>'',
     ),
      array('class'=>'inline',
           )
    );

    $this->widgetSchema->setLabels(array('insurance_value' => 'Value' ,
                                         'insurance_currency' => 'Currency',
                                         'insurance_year' => 'Year',
                                         'insurer_ref' => 'Insurer:'
                                        )
                                  );

    $this->widgetSchema['insurance_currency']->setAttributes(array('class'=>'vsmall_size'));
    $this->validatorSchema['insurance_currency']->setOption('required', false);

    $this->validatorSchema['insurance_value'] = new sfValidatorNumber(array('required'=>false));
    $this->validatorSchema['insurance_year'] = new sfValidatorChoice(array('choices' => $yearsKeyVal,'required'=>false));

    /*Insurances post-validation to empty null values*/
    $this->mergePostValidator(new InsurancesValidatorSchema());

  }
  
}