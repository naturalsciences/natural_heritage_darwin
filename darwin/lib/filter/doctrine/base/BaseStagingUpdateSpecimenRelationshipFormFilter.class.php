<?php

/**
 * StagingUpdateSpecimenRelationship filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingUpdateSpecimenRelationshipFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['relationship_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['relationship_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_submitted_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_submitted_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['specimen_uuid'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_uuid'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_main_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_main_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen1'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_related_submitted_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_related_submitted_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['specimen_related_uuid'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_related_uuid'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_related_main_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_related_main_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen2'), 'column' => 'id'));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['to_import'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['imported'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['imported'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen1'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen1'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_related_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen2'), 'add_empty' => true));
    $this->validatorSchema['specimen_related_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen2'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_update_specimen_relationship_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingUpdateSpecimenRelationship';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'relationship_type' => 'Text',
      'specimen_submitted_ref' => 'Number',
      'specimen_uuid' => 'Text',
      'specimen_main_code' => 'Text',
      'specimen_ref' => 'ForeignKey',
      'specimen_related_submitted_ref' => 'Number',
      'specimen_related_uuid' => 'Text',
      'specimen_related_main_code' => 'Text',
      'specimen_related_ref' => 'ForeignKey',
      'import_ref' => 'ForeignKey',
      'status' => 'Text',
      'to_import' => 'Boolean',
      'imported' => 'Boolean',
      'import_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
      'specimen_related_ref' => 'ForeignKey',
    ));
  }
}
