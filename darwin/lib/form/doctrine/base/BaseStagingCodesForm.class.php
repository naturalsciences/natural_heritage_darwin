<?php

/**
 * StagingCodes form base class.
 *
 * @method StagingCodes getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingCodesForm extends DarwinModelForm
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

    $this->widgetSchema   ['code_category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['code_date_mask'] = new sfValidatorInteger(array('required' => false));

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

    $this->widgetSchema->setNameFormat('staging_codes[%s]');
  }

  public function getModelName()
  {
    return 'StagingCodes';
  }

}
