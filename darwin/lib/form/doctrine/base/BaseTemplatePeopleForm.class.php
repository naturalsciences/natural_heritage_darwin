<?php

/**
 * TemplatePeople form base class.
 *
 * @method TemplatePeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_physical'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString();

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorString();

    $this->widgetSchema   ['formated_name_unique'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name_unique'] = new sfValidatorString();

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

    $this->widgetSchema   ['birth_date'] = new sfWidgetFormDate();
    $this->validatorSchema['birth_date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['gender'] = new sfWidgetFormInputText();
    $this->validatorSchema['gender'] = new sfValidatorString(array('max_length' => 1, 'required' => false));

    $this->widgetSchema->setNameFormat('template_people[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeople';
  }

}
