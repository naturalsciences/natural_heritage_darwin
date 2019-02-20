<?php

/**
 * PropertiesBck20180731FixColmateDates filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePropertiesBck20180731FixColmateDatesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormFilterInput(),
      'referenced_relation' => new sfWidgetFormFilterInput(),
      'record_id'           => new sfWidgetFormFilterInput(),
      'property_type'       => new sfWidgetFormFilterInput(),
      'applies_to'          => new sfWidgetFormFilterInput(),
      'applies_to_indexed'  => new sfWidgetFormFilterInput(),
      'date_from_mask'      => new sfWidgetFormFilterInput(),
      'date_from'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'date_to_mask'        => new sfWidgetFormFilterInput(),
      'date_to'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'is_quantitative'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'property_unit'       => new sfWidgetFormFilterInput(),
      'method'              => new sfWidgetFormFilterInput(),
      'method_indexed'      => new sfWidgetFormFilterInput(),
      'lower_value'         => new sfWidgetFormFilterInput(),
      'lower_value_unified' => new sfWidgetFormFilterInput(),
      'upper_value'         => new sfWidgetFormFilterInput(),
      'upper_value_unified' => new sfWidgetFormFilterInput(),
      'property_accuracy'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'property_type'       => new sfValidatorPass(array('required' => false)),
      'applies_to'          => new sfValidatorPass(array('required' => false)),
      'applies_to_indexed'  => new sfValidatorPass(array('required' => false)),
      'date_from_mask'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date_from'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'date_to_mask'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date_to'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'is_quantitative'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'property_unit'       => new sfValidatorPass(array('required' => false)),
      'method'              => new sfValidatorPass(array('required' => false)),
      'method_indexed'      => new sfValidatorPass(array('required' => false)),
      'lower_value'         => new sfValidatorPass(array('required' => false)),
      'lower_value_unified' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'upper_value'         => new sfValidatorPass(array('required' => false)),
      'upper_value_unified' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'property_accuracy'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('properties_bck20180731_fix_colmate_dates_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PropertiesBck20180731FixColmateDates';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'property_type'       => 'Text',
      'applies_to'          => 'Text',
      'applies_to_indexed'  => 'Text',
      'date_from_mask'      => 'Number',
      'date_from'           => 'Date',
      'date_to_mask'        => 'Number',
      'date_to'             => 'Date',
      'is_quantitative'     => 'Boolean',
      'property_unit'       => 'Text',
      'method'              => 'Text',
      'method_indexed'      => 'Text',
      'lower_value'         => 'Text',
      'lower_value_unified' => 'Number',
      'upper_value'         => 'Text',
      'upper_value_unified' => 'Number',
      'property_accuracy'   => 'Text',
    );
  }
}
