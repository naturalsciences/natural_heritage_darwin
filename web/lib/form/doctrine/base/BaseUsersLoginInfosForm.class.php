<?php

/**
 * UsersLoginInfos form base class.
 *
 * @method UsersLoginInfos getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersLoginInfosForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['login_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['login_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['user_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['user_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['password'] = new sfWidgetFormTextarea();
    $this->validatorSchema['password'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['login_system'] = new sfWidgetFormTextarea();
    $this->validatorSchema['login_system'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['renew_hash'] = new sfWidgetFormTextarea();
    $this->validatorSchema['renew_hash'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['last_seen'] = new sfWidgetFormTextarea();
    $this->validatorSchema['last_seen'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('users_login_infos[%s]');
  }

  public function getModelName()
  {
    return 'UsersLoginInfos';
  }

}
