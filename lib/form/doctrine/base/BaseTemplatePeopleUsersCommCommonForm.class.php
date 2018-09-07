<?php

/**
 * TemplatePeopleUsersCommCommon form base class.
 *
 * @method TemplatePeopleUsersCommCommon getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleUsersCommCommonForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'person_user_ref' => new sfWidgetFormInputText(),
      'entry'           => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_user_ref' => new sfValidatorInteger(),
      'entry'           => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('template_people_users_comm_common[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersCommCommon';
  }

}
