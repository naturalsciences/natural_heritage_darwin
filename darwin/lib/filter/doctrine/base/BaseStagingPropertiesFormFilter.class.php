<?php

/**
 * StagingProperties filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingPropertiesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_main_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_main_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_uuid'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_uuid'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema   ['property_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['property_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['applies_to'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['applies_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['applies_to_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['applies_to_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_from_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['date_from'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_from'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_to_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['date_to'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['date_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_quantitative'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_quantitative'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['property_unit'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['property_unit'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['method'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['method'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['method_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['method_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lower_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['lower_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lower_value_unified'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lower_value_unified'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['upper_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['upper_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['upper_value_unified'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['upper_value_unified'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['property_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['property_accuracy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_import'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['to_import'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['imported'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['imported'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimen'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimen'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_properties_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingProperties';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'specimen_main_id' => 'Text',
      'specimen_uuid' => 'Text',
      'specimen_ref' => 'ForeignKey',
      'property_type' => 'Text',
      'applies_to' => 'Text',
      'applies_to_indexed' => 'Text',
      'date_from_mask' => 'Number',
      'date_from' => 'Text',
      'date_to_mask' => 'Number',
      'date_to' => 'Text',
      'is_quantitative' => 'Boolean',
      'property_unit' => 'Text',
      'method' => 'Text',
      'method_indexed' => 'Text',
      'lower_value' => 'Text',
      'lower_value_unified' => 'Number',
      'upper_value' => 'Text',
      'upper_value_unified' => 'Number',
      'property_accuracy' => 'Text',
      'status' => 'Text',
      'to_import' => 'Boolean',
      'imported' => 'Boolean',
      'import_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
    ));
  }
}
