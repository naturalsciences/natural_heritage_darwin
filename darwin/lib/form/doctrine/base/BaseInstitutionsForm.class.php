<?php

/**
 * Institutions form base class.
 *
 * @method Institutions getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseInstitutionsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_physical'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['family_name'] = new sfValidatorString();

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormTextarea();
    $this->validatorSchema['additional_names'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema->setNameFormat('institutions[%s]');
  }

  public function getModelName()
  {
    return 'Institutions';
  }

}
