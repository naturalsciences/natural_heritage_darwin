<?php

/**
 * PeopleRelationships form base class.
 *
 * @method PeopleRelationships getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleRelationshipsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['relationship_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['person_1_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => false));
    $this->validatorSchema['person_1_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['person_2_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => false));
    $this->validatorSchema['person_2_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'column' => 'id'));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_from'] = new sfWidgetFormTextarea();
    $this->validatorSchema['activity_date_from'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['activity_date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['activity_date_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['activity_date_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['activity_date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['person_user_role'] = new sfWidgetFormTextarea();
    $this->validatorSchema['person_user_role'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['person_1_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => false));
    $this->validatorSchema['person_1_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['person_2_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'add_empty' => false));
    $this->validatorSchema['person_2_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Child'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_relationships[%s]');
  }

  public function getModelName()
  {
    return 'PeopleRelationships';
  }

}
