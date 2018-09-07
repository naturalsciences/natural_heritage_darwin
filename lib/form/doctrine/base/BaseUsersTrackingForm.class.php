<?php

/**
 * UsersTracking form base class.
 *
 * @method UsersTracking getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUsersTrackingForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'referenced_relation'    => new sfWidgetFormTextarea(),
      'record_id'              => new sfWidgetFormInputText(),
      'user_ref'               => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false)),
      'action'                 => new sfWidgetFormTextarea(),
      'modification_date_time' => new sfWidgetFormTextarea(),
      'old_value'              => new sfWidgetFormTextarea(),
      'new_value'              => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation'    => new sfValidatorString(),
      'record_id'              => new sfValidatorInteger(),
      'user_ref'               => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'))),
      'action'                 => new sfValidatorString(array('required' => false)),
      'modification_date_time' => new sfValidatorString(),
      'old_value'              => new sfValidatorString(array('required' => false)),
      'new_value'              => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_tracking[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersTracking';
  }

}
