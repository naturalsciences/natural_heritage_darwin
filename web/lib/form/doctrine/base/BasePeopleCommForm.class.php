<?php

/**
 * PeopleComm form base class.
 *
 * @method PeopleComm getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleCommForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['comm_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comm_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['tag'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['entry'] = new sfWidgetFormTextarea();
    $this->validatorSchema['entry'] = new sfValidatorString();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_comm[%s]');
  }

  public function getModelName()
  {
    return 'PeopleComm';
  }

}
