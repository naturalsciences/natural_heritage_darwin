<?php

/**
 * StagingProperties form base class.
 *
 * @method StagingProperties getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingPropertiesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_main_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_main_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_uuid'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_uuid'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'column' => 'id', 'required' => false));

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

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['to_import'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['imported'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('staging_properties[%s]');
  }

  public function getModelName()
  {
    return 'StagingProperties';
  }

}
