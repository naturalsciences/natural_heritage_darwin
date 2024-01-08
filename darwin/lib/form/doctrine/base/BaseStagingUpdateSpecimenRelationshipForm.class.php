<?php

/**
 * StagingUpdateSpecimenRelationship form base class.
 *
 * @method StagingUpdateSpecimenRelationship getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingUpdateSpecimenRelationshipForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['relationship_type'] = new sfValidatorString();

    $this->widgetSchema   ['specimen_submitted_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_submitted_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_uuid'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_uuid'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_main_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_main_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_related_submitted_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_related_submitted_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['specimen_related_uuid'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_related_uuid'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_related_main_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['specimen_related_main_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['to_import'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['imported'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('staging_update_specimen_relationship[%s]');
  }

  public function getModelName()
  {
    return 'StagingUpdateSpecimenRelationship';
  }

}
