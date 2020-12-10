<?php

/**
 * StagingRelationship filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStagingRelationshipFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'record_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'relationship_type'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'staging_related_ref' => new sfWidgetFormFilterInput(),
      'taxon_ref'           => new sfWidgetFormFilterInput(),
      'mineral_ref'         => new sfWidgetFormFilterInput(),
      'institution_ref'     => new sfWidgetFormFilterInput(),
      'institution_name'    => new sfWidgetFormFilterInput(),
      'source_name'         => new sfWidgetFormFilterInput(),
      'source_id'           => new sfWidgetFormFilterInput(),
      'unit_type'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quantity'            => new sfWidgetFormFilterInput(),
      'unit'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'relationship_type'   => new sfValidatorPass(array('required' => false)),
      'staging_related_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'taxon_ref'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'mineral_ref'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'institution_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'institution_name'    => new sfValidatorPass(array('required' => false)),
      'source_name'         => new sfValidatorPass(array('required' => false)),
      'source_id'           => new sfValidatorPass(array('required' => false)),
      'unit_type'           => new sfValidatorPass(array('required' => false)),
      'quantity'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'unit'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_relationship_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingRelationship';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'record_id'           => 'Number',
      'referenced_relation' => 'Text',
      'relationship_type'   => 'Text',
      'staging_related_ref' => 'Number',
      'taxon_ref'           => 'Number',
      'mineral_ref'         => 'Number',
      'institution_ref'     => 'Number',
      'institution_name'    => 'Text',
      'source_name'         => 'Text',
      'source_id'           => 'Text',
      'unit_type'           => 'Text',
      'quantity'            => 'Number',
      'unit'                => 'Text',
    );
  }
}
