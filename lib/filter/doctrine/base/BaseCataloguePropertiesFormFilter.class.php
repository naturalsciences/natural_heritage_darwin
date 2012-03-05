<?php

/**
 * CatalogueProperties filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCataloguePropertiesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'property_type'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'property_sub_type'          => new sfWidgetFormFilterInput(),
      'property_sub_type_indexed'  => new sfWidgetFormFilterInput(),
      'property_qualifier'         => new sfWidgetFormFilterInput(),
      'property_qualifier_indexed' => new sfWidgetFormFilterInput(),
      'date_from_mask'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_from'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_to_mask'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_to'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'property_unit'              => new sfWidgetFormFilterInput(),
      'property_accuracy_unit'     => new sfWidgetFormFilterInput(),
      'property_method'            => new sfWidgetFormFilterInput(),
      'property_method_indexed'    => new sfWidgetFormFilterInput(),
      'property_tool'              => new sfWidgetFormFilterInput(),
      'property_tool_indexed'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation'        => new sfValidatorPass(array('required' => false)),
      'record_id'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'property_type'              => new sfValidatorPass(array('required' => false)),
      'property_sub_type'          => new sfValidatorPass(array('required' => false)),
      'property_sub_type_indexed'  => new sfValidatorPass(array('required' => false)),
      'property_qualifier'         => new sfValidatorPass(array('required' => false)),
      'property_qualifier_indexed' => new sfValidatorPass(array('required' => false)),
      'date_from_mask'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date_from'                  => new sfValidatorPass(array('required' => false)),
      'date_to_mask'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date_to'                    => new sfValidatorPass(array('required' => false)),
      'property_unit'              => new sfValidatorPass(array('required' => false)),
      'property_accuracy_unit'     => new sfValidatorPass(array('required' => false)),
      'property_method'            => new sfValidatorPass(array('required' => false)),
      'property_method_indexed'    => new sfValidatorPass(array('required' => false)),
      'property_tool'              => new sfValidatorPass(array('required' => false)),
      'property_tool_indexed'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('catalogue_properties_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueProperties';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'referenced_relation'        => 'Text',
      'record_id'                  => 'Number',
      'property_type'              => 'Text',
      'property_sub_type'          => 'Text',
      'property_sub_type_indexed'  => 'Text',
      'property_qualifier'         => 'Text',
      'property_qualifier_indexed' => 'Text',
      'date_from_mask'             => 'Number',
      'date_from'                  => 'Text',
      'date_to_mask'               => 'Number',
      'date_to'                    => 'Text',
      'property_unit'              => 'Text',
      'property_accuracy_unit'     => 'Text',
      'property_method'            => 'Text',
      'property_method_indexed'    => 'Text',
      'property_tool'              => 'Text',
      'property_tool_indexed'      => 'Text',
    );
  }
}
