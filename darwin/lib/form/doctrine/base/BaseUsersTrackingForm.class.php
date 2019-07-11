<?php

/**
 * UsersTracking form base class.
 *
 * @method UsersTracking getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersTrackingForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['action'] = new sfWidgetFormTextarea();
    $this->validatorSchema['action'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormTextarea();
    $this->validatorSchema['modification_date_time'] = new sfValidatorString();

    $this->widgetSchema   ['old_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['old_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['new_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['new_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('users_tracking[%s]');
  }

  public function getModelName()
  {
    return 'UsersTracking';
  }

}
