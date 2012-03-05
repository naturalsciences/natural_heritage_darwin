<?php

/**
 * CatalogueProperties form base class.
 *
 * @method CatalogueProperties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCataloguePropertiesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'referenced_relation'        => new sfWidgetFormTextarea(),
      'record_id'                  => new sfWidgetFormInputText(),
      'property_type'              => new sfWidgetFormTextarea(),
      'property_sub_type'          => new sfWidgetFormTextarea(),
      'property_sub_type_indexed'  => new sfWidgetFormTextarea(),
      'property_qualifier'         => new sfWidgetFormTextarea(),
      'property_qualifier_indexed' => new sfWidgetFormTextarea(),
      'date_from_mask'             => new sfWidgetFormInputText(),
      'date_from'                  => new sfWidgetFormTextarea(),
      'date_to_mask'               => new sfWidgetFormInputText(),
      'date_to'                    => new sfWidgetFormTextarea(),
      'property_unit'              => new sfWidgetFormTextarea(),
      'property_accuracy_unit'     => new sfWidgetFormTextarea(),
      'property_method'            => new sfWidgetFormTextarea(),
      'property_method_indexed'    => new sfWidgetFormTextarea(),
      'property_tool'              => new sfWidgetFormTextarea(),
      'property_tool_indexed'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation'        => new sfValidatorString(),
      'record_id'                  => new sfValidatorInteger(),
      'property_type'              => new sfValidatorString(),
      'property_sub_type'          => new sfValidatorString(array('required' => false)),
      'property_sub_type_indexed'  => new sfValidatorString(array('required' => false)),
      'property_qualifier'         => new sfValidatorString(array('required' => false)),
      'property_qualifier_indexed' => new sfValidatorString(array('required' => false)),
      'date_from_mask'             => new sfValidatorInteger(array('required' => false)),
      'date_from'                  => new sfValidatorString(array('required' => false)),
      'date_to_mask'               => new sfValidatorInteger(array('required' => false)),
      'date_to'                    => new sfValidatorString(array('required' => false)),
      'property_unit'              => new sfValidatorString(array('required' => false)),
      'property_accuracy_unit'     => new sfValidatorString(array('required' => false)),
      'property_method'            => new sfValidatorString(array('required' => false)),
      'property_method_indexed'    => new sfValidatorString(array('required' => false)),
      'property_tool'              => new sfValidatorString(array('required' => false)),
      'property_tool_indexed'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('catalogue_properties[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueProperties';
  }

}
