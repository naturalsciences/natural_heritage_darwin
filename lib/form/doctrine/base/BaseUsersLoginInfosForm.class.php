<?php

/**
 * UsersLoginInfos form base class.
 *
 * @method UsersLoginInfos getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUsersLoginInfosForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'user_ref'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'login_type'   => new sfWidgetFormTextarea(),
      'user_name'    => new sfWidgetFormTextarea(),
      'password'     => new sfWidgetFormTextarea(),
      'login_system' => new sfWidgetFormTextarea(),
      'renew_hash'   => new sfWidgetFormTextarea(),
      'last_seen'    => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_ref'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'login_type'   => new sfValidatorString(array('required' => false)),
      'user_name'    => new sfValidatorString(array('required' => false)),
      'password'     => new sfValidatorString(array('required' => false)),
      'login_system' => new sfValidatorString(array('required' => false)),
      'renew_hash'   => new sfValidatorString(array('required' => false)),
      'last_seen'    => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_login_infos[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersLoginInfos';
  }

}
