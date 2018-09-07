<?php

/**
 * PeopleRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePeopleRelationshipsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'relationship_type'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'person_1_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'person_2_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => true)),
      'path'                    => new sfWidgetFormFilterInput(),
      'activity_date_from'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activity_date_from_mask' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activity_date_to'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activity_date_to_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'person_user_role'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'relationship_type'       => new sfValidatorPass(array('required' => false)),
      'person_1_ref'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'person_2_ref'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Child'), 'column' => 'id')),
      'path'                    => new sfValidatorPass(array('required' => false)),
      'activity_date_from'      => new sfValidatorPass(array('required' => false)),
      'activity_date_from_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'activity_date_to'        => new sfValidatorPass(array('required' => false)),
      'activity_date_to_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'person_user_role'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('people_relationships_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PeopleRelationships';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'relationship_type'       => 'Text',
      'person_1_ref'            => 'ForeignKey',
      'person_2_ref'            => 'ForeignKey',
      'path'                    => 'Text',
      'activity_date_from'      => 'Text',
      'activity_date_from_mask' => 'Number',
      'activity_date_to'        => 'Text',
      'activity_date_to_mask'   => 'Number',
      'person_user_role'        => 'Text',
    );
  }
}
