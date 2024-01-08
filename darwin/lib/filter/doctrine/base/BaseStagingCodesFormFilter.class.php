<?php

/**
 * StagingCodes filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingCodesFormFilter extends DarwinModelFormFilter
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

    $this->widgetSchema   ['code_category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code_category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

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

    $this->widgetSchema->setNameFormat('staging_codes_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingCodes';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'specimen_main_id' => 'Text',
      'specimen_uuid' => 'Text',
      'specimen_ref' => 'ForeignKey',
      'code_category' => 'Text',
      'code_prefix' => 'Text',
      'code_prefix_separator' => 'Text',
      'code' => 'Text',
      'code_suffix' => 'Text',
      'code_suffix_separator' => 'Text',
      'code_date_mask' => 'Number',
      'status' => 'Text',
      'to_import' => 'Boolean',
      'imported' => 'Boolean',
      'import_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
    ));
  }
}
