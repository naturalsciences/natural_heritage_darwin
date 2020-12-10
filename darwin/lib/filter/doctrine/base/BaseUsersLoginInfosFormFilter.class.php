<?php

/**
 * UsersLoginInfos filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUsersLoginInfosFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_ref'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'login_type'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_name'    => new sfWidgetFormFilterInput(),
      'password'     => new sfWidgetFormFilterInput(),
      'login_system' => new sfWidgetFormFilterInput(),
      'renew_hash'   => new sfWidgetFormFilterInput(),
      'last_seen'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'user_ref'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'login_type'   => new sfValidatorPass(array('required' => false)),
      'user_name'    => new sfValidatorPass(array('required' => false)),
      'password'     => new sfValidatorPass(array('required' => false)),
      'login_system' => new sfValidatorPass(array('required' => false)),
      'renew_hash'   => new sfValidatorPass(array('required' => false)),
      'last_seen'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_login_infos_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersLoginInfos';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'user_ref'     => 'ForeignKey',
      'login_type'   => 'Text',
      'user_name'    => 'Text',
      'password'     => 'Text',
      'login_system' => 'Text',
      'renew_hash'   => 'Text',
      'last_seen'    => 'Text',
    );
  }
}
