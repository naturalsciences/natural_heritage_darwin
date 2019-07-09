<?php

/**
 * PropertiesBck20180731FixColmateDates form base class.
 *
 * @method PropertiesBck20180731FixColmateDates getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePropertiesBck20180731FixColmateDatesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['property_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['applies_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['applies_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['applies_to_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['applies_to_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormDateTime();
    $this->validatorSchema['date_from'] = new sfValidatorDateTime(array('required' => false));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormDateTime();
    $this->validatorSchema['date_to'] = new sfValidatorDateTime(array('required' => false));

    $this->widgetSchema   ['is_quantitative'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_quantitative'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['property_unit'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_unit'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['method'] = new sfWidgetFormTextarea();
    $this->validatorSchema['method'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['method_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['method_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lower_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lower_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lower_value_unified'] = new sfWidgetFormInputText();
    $this->validatorSchema['lower_value_unified'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['upper_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['upper_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['upper_value_unified'] = new sfWidgetFormInputText();
    $this->validatorSchema['upper_value_unified'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['property_accuracy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_accuracy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('properties_bck20180731_fix_colmate_dates[%s]');
  }

  public function getModelName()
  {
    return 'PropertiesBck20180731FixColmateDates';
  }

}
