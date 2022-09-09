<?php

/**
 * UsersComm form base class.
 *
 * @method UsersComm getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersCommForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['comm_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comm_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['entry'] = new sfWidgetFormTextarea();
    $this->validatorSchema['entry'] = new sfValidatorString();

    $this->widgetSchema   ['tag'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('users_comm[%s]');
  }

  public function getModelName()
  {
    return 'UsersComm';
  }

}
