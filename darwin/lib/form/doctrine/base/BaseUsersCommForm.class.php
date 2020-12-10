<?php

/**
 * UsersComm form base class.
 *
 * @method UsersComm getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUsersCommForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'person_user_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false)),
      'comm_type'       => new sfWidgetFormTextarea(),
      'entry'           => new sfWidgetFormTextarea(),
      'tag'             => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_user_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'))),
      'comm_type'       => new sfValidatorString(array('required' => false)),
      'entry'           => new sfValidatorString(),
      'tag'             => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_comm[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersComm';
  }

}
