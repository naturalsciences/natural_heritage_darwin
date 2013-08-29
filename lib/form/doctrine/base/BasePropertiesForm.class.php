<?php

/**
 * Properties form base class.
 *
 * @method Properties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePropertiesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'property_type'       => new sfWidgetFormTextarea(),
      'applies_to'          => new sfWidgetFormTextarea(),
      'applies_to_indexed'  => new sfWidgetFormTextarea(),
      'date_from_mask'      => new sfWidgetFormInputText(),
      'date_from'           => new sfWidgetFormTextarea(),
      'date_to_mask'        => new sfWidgetFormInputText(),
      'date_to'             => new sfWidgetFormTextarea(),
      'is_quantitative'     => new sfWidgetFormInputCheckbox(),
      'property_unit'       => new sfWidgetFormTextarea(),
      'method'              => new sfWidgetFormTextarea(),
      'method_indexed'      => new sfWidgetFormTextarea(),
      'lower_value'         => new sfWidgetFormTextarea(),
      'lower_value_unified' => new sfWidgetFormInputText(),
      'upper_value'         => new sfWidgetFormTextarea(),
      'upper_value_unified' => new sfWidgetFormInputText(),
      'property_accuracy'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(),
      'property_type'       => new sfValidatorString(),
      'applies_to'          => new sfValidatorString(array('required' => false)),
      'applies_to_indexed'  => new sfValidatorString(array('required' => false)),
      'date_from_mask'      => new sfValidatorInteger(array('required' => false)),
      'date_from'           => new sfValidatorString(array('required' => false)),
      'date_to_mask'        => new sfValidatorInteger(array('required' => false)),
      'date_to'             => new sfValidatorString(array('required' => false)),
      'is_quantitative'     => new sfValidatorBoolean(array('required' => false)),
      'property_unit'       => new sfValidatorString(array('required' => false)),
      'method'              => new sfValidatorString(array('required' => false)),
      'method_indexed'      => new sfValidatorString(array('required' => false)),
      'lower_value'         => new sfValidatorString(array('required' => false)),
      'lower_value_unified' => new sfValidatorNumber(array('required' => false)),
      'upper_value'         => new sfValidatorString(array('required' => false)),
      'upper_value_unified' => new sfValidatorNumber(array('required' => false)),
      'property_accuracy'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('properties[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Properties';
  }

}
