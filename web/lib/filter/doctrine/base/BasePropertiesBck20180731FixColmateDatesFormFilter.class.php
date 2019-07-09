<?php

/**
 * PropertiesBck20180731FixColmateDates filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePropertiesBck20180731FixColmateDatesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['property_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['property_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['applies_to'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['applies_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['applies_to_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['applies_to_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date_from'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date_to'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['is_quantitative'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_quantitative'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['property_unit'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['property_unit'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['method'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['method'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['method_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['method_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lower_value'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lower_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lower_value_unified'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lower_value_unified'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['upper_value'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['upper_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['upper_value_unified'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['upper_value_unified'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['property_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['property_accuracy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('properties_bck20180731_fix_colmate_dates_filters[%s]');
  }

  public function getModelName()
  {
    return 'PropertiesBck20180731FixColmateDates';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'property_type' => 'Text',
      'applies_to' => 'Text',
      'applies_to_indexed' => 'Text',
      'date_from_mask' => 'Number',
      'date_from' => 'Date',
      'date_to_mask' => 'Number',
      'date_to' => 'Date',
      'is_quantitative' => 'Boolean',
      'property_unit' => 'Text',
      'method' => 'Text',
      'method_indexed' => 'Text',
      'lower_value' => 'Text',
      'lower_value_unified' => 'Number',
      'upper_value' => 'Text',
      'upper_value_unified' => 'Number',
      'property_accuracy' => 'Text',
    ));
  }
}
