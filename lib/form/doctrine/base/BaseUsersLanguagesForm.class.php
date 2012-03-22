<?php

/**
 * UsersLanguages form base class.
 *
 * @method UsersLanguages getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUsersLanguagesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'users_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'language_country'   => new sfWidgetFormTextarea(),
      'mother'             => new sfWidgetFormInputCheckbox(),
      'preferred_language' => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'users_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'language_country'   => new sfValidatorString(array('required' => false)),
      'mother'             => new sfValidatorBoolean(array('required' => false)),
      'preferred_language' => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_languages[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UsersLanguages';
  }

}
