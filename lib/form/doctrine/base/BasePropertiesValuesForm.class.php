<?php

/**
 * PropertiesValues form base class.
 *
 * @method PropertiesValues getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePropertiesValuesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'property_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CatalogueProperties'), 'add_empty' => true)),
      'property_value'            => new sfWidgetFormTextarea(),
      'property_value_unified'    => new sfWidgetFormTextarea(),
      'property_accuracy'         => new sfWidgetFormInputText(),
      'property_accuracy_unified' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'property_ref'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CatalogueProperties'), 'required' => false)),
      'property_value'            => new sfValidatorString(),
      'property_value_unified'    => new sfValidatorString(array('required' => false)),
      'property_accuracy'         => new sfValidatorNumber(array('required' => false)),
      'property_accuracy_unified' => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('properties_values[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PropertiesValues';
  }

}
