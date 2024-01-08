<?php

/**
 * Properties form base class.
 *
 * @method Properties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePropertiesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['property_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_type'] = new sfValidatorString();

    $this->widgetSchema   ['applies_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['applies_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['applies_to_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['applies_to_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormTextarea();
    $this->validatorSchema['date_from'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormTextarea();
    $this->validatorSchema['date_to'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_quantitative'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_quantitative'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['property_unit'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_unit'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['method'] = new sfWidgetFormTextarea();
    $this->validatorSchema['method'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['method_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['method_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lower_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lower_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lower_value_unified'] = new sfWidgetFormInputText();
    $this->validatorSchema['lower_value_unified'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['upper_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['upper_value'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['upper_value_unified'] = new sfWidgetFormInputText();
    $this->validatorSchema['upper_value_unified'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['property_accuracy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['property_accuracy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Imports'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('properties[%s]');
  }

  public function getModelName()
  {
    return 'Properties';
  }

}
