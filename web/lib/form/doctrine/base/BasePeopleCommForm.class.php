<?php

/**
 * PeopleComm form base class.
 *
 * @method PeopleComm getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePeopleCommForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'person_user_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false)),
      'comm_type'       => new sfWidgetFormTextarea(),
      'tag'             => new sfWidgetFormTextarea(),
      'entry'           => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_user_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'))),
      'comm_type'       => new sfValidatorString(array('required' => false)),
      'tag'             => new sfValidatorString(array('required' => false)),
      'entry'           => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('people_comm[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PeopleComm';
  }

}
