<?php

/**
 * PeopleRelationships filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleRelationshipsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['relationship_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['person_1_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['person_1_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['person_2_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => true));
    $this->validatorSchema['person_2_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Child'), 'column' => 'id'));

    $this->widgetSchema   ['path'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['path'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_from'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_from'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_from_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['activity_date_to'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_to_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['person_user_role'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['person_user_role'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['person_1_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['person_1_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['person_2_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => true));
    $this->validatorSchema['person_2_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Child'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_relationships_filters[%s]');
  }

  public function getModelName()
  {
    return 'PeopleRelationships';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'relationship_type' => 'Text',
      'person_1_ref' => 'ForeignKey',
      'person_2_ref' => 'ForeignKey',
      'path' => 'Text',
      'activity_date_from' => 'Text',
      'activity_date_from_mask' => 'Number',
      'activity_date_to' => 'Text',
      'activity_date_to_mask' => 'Number',
      'person_user_role' => 'Text',
      'person_1_ref' => 'ForeignKey',
      'person_2_ref' => 'ForeignKey',
    ));
  }
}
