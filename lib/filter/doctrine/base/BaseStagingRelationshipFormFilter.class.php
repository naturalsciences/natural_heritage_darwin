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
      'relationship_type'   => new sfWidgetFormFilterInput(),
      'ref_id'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'relationship_type'   => new sfValidatorPass(array('required' => false)),
      'ref_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'ref_id'              => 'Number',
    );
  }
}
