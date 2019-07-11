<?php

/**
 * UsersLoginInfos filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersLoginInfosFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['login_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['login_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['user_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['password'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['password'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['login_system'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['login_system'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['renew_hash'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['renew_hash'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['last_seen'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['last_seen'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('users_login_infos_filters[%s]');
  }

  public function getModelName()
  {
    return 'UsersLoginInfos';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'user_ref' => 'ForeignKey',
      'login_type' => 'Text',
      'user_name' => 'Text',
      'password' => 'Text',
      'login_system' => 'Text',
      'renew_hash' => 'Text',
      'last_seen' => 'Text',
      'user_ref' => 'ForeignKey',
    ));
  }
}
