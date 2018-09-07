<?php

/**
 * TemplatePeople form base class.
 *
 * @method TemplatePeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'is_physical'           => new sfWidgetFormInputCheckbox(),
      'sub_type'              => new sfWidgetFormTextarea(),
      'formated_name'         => new sfWidgetFormTextarea(),
      'formated_name_indexed' => new sfWidgetFormTextarea(),
      'formated_name_unique'  => new sfWidgetFormTextarea(),
      'title'                 => new sfWidgetFormTextarea(),
      'family_name'           => new sfWidgetFormTextarea(),
      'given_name'            => new sfWidgetFormTextarea(),
      'additional_names'      => new sfWidgetFormTextarea(),
      'birth_date_mask'       => new sfWidgetFormInputText(),
      'birth_date'            => new sfWidgetFormDate(),
      'gender'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'is_physical'           => new sfValidatorBoolean(array('required' => false)),
      'sub_type'              => new sfValidatorString(array('required' => false)),
      'formated_name'         => new sfValidatorString(),
      'formated_name_indexed' => new sfValidatorString(),
      'formated_name_unique'  => new sfValidatorString(),
      'title'                 => new sfValidatorString(array('required' => false)),
      'family_name'           => new sfValidatorString(),
      'given_name'            => new sfValidatorString(array('required' => false)),
      'additional_names'      => new sfValidatorString(array('required' => false)),
      'birth_date_mask'       => new sfValidatorInteger(array('required' => false)),
      'birth_date'            => new sfValidatorDate(array('required' => false)),
      'gender'                => new sfValidatorString(array('max_length' => 1, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('template_people[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeople';
  }

}
