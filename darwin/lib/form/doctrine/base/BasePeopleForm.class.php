<?php

/**
 * People form base class.
 *
 * @method People getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_physical'] = new sfValidatorBoolean();

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['family_name'] = new sfValidatorString();

    $this->widgetSchema   ['given_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['given_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormTextarea();
    $this->validatorSchema['additional_names'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['birth_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['birth_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['birth_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['birth_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gender'] = new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F')));
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array(0 => 'M', 1 => 'F'), 'required' => false));

    $this->widgetSchema   ['end_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['end_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['end_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['end_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_from'] = new sfWidgetFormTextarea();
    $this->validatorSchema['activity_date_from'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['activity_date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['activity_date_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['activity_date_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['activity_date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['activity_date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('people[%s]');
  }

  public function getModelName()
  {
    return 'People';
  }

}
