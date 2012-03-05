<?php

/**
 * PropertiesValues filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePropertiesValuesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'property_ref'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CatalogueProperties'), 'add_empty' => true)),
      'property_value'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'property_value_unified'    => new sfWidgetFormFilterInput(),
      'property_accuracy'         => new sfWidgetFormFilterInput(),
      'property_accuracy_unified' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'property_ref'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CatalogueProperties'), 'column' => 'id')),
      'property_value'            => new sfValidatorPass(array('required' => false)),
      'property_value_unified'    => new sfValidatorPass(array('required' => false)),
      'property_accuracy'         => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'property_accuracy_unified' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('properties_values_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PropertiesValues';
  }

  public function getFields()
  {
    return array(
      'id'                        => 'Number',
      'property_ref'              => 'ForeignKey',
      'property_value'            => 'Text',
      'property_value_unified'    => 'Text',
      'property_accuracy'         => 'Number',
      'property_accuracy_unified' => 'Number',
    );
  }
}
