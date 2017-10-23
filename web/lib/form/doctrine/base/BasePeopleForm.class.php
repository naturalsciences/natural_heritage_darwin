<?php

/**
 * People form base class.
 *
 * @method People getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePeopleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'is_physical'             => new sfWidgetFormInputCheckbox(),
      'sub_type'                => new sfWidgetFormTextarea(),
      'formated_name'           => new sfWidgetFormTextarea(),
      'formated_name_indexed'   => new sfWidgetFormTextarea(),
      'title'                   => new sfWidgetFormTextarea(),
      'family_name'             => new sfWidgetFormTextarea(),
      'given_name'              => new sfWidgetFormTextarea(),
      'additional_names'        => new sfWidgetFormTextarea(),
      'birth_date_mask'         => new sfWidgetFormInputText(),
      'birth_date'              => new sfWidgetFormTextarea(),
      'gender'                  => new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F'))),
      'end_date_mask'           => new sfWidgetFormInputText(),
      'end_date'                => new sfWidgetFormTextarea(),
      'activity_date_from'      => new sfWidgetFormTextarea(),
      'activity_date_from_mask' => new sfWidgetFormInputText(),
      'activity_date_to'        => new sfWidgetFormTextarea(),
      'activity_date_to_mask'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'is_physical'             => new sfValidatorBoolean(),
      'sub_type'                => new sfValidatorString(array('required' => false)),
      'formated_name'           => new sfValidatorString(array('required' => false)),
      'formated_name_indexed'   => new sfValidatorString(array('required' => false)),
      'title'                   => new sfValidatorString(array('required' => false)),
      'family_name'             => new sfValidatorString(),
      'given_name'              => new sfValidatorString(array('required' => false)),
      'additional_names'        => new sfValidatorString(array('required' => false)),
      'birth_date_mask'         => new sfValidatorInteger(array('required' => false)),
      'birth_date'              => new sfValidatorString(array('required' => false)),
      'gender'                  => new sfValidatorChoice(array('choices' => array(0 => 'M', 1 => 'F'), 'required' => false)),
      'end_date_mask'           => new sfValidatorInteger(array('required' => false)),
      'end_date'                => new sfValidatorString(array('required' => false)),
      'activity_date_from'      => new sfValidatorString(array('required' => false)),
      'activity_date_from_mask' => new sfValidatorInteger(array('required' => false)),
      'activity_date_to'        => new sfValidatorString(array('required' => false)),
      'activity_date_to_mask'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('people[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'People';
  }

}
