<?php

/**
 * CatalogueRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCatalogueRelationshipsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id_1'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id_2'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'relationship_type'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id_1'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'record_id_2'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'relationship_type'   => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('catalogue_relationships_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CatalogueRelationships';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id_1'         => 'Number',
      'record_id_2'         => 'Number',
      'relationship_type'   => 'Text',
    );
  }
}
