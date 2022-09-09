<?php

/**
 * StagingCatalogue filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingCatalogueFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['catalogue_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['catalogue_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['source_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_taxonomy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_cluster'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_cluster'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['imported'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['imported'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['import_exception'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['import_exception'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['staging_hierarchy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['staging_hierarchy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['darwin_hierarchy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['darwin_hierarchy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parent_ref_internal'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parent_ref_internal'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['parent_updated'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['parent_updated'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id'));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_catalogue_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingCatalogue';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'name' => 'Text',
      'level_ref' => 'Number',
      'parent_ref' => 'ForeignKey',
      'catalogue_ref' => 'Number',
      'is_reference_taxonomy' => 'Boolean',
      'source_taxonomy' => 'Text',
      'name_cluster' => 'Number',
      'imported' => 'Boolean',
      'import_exception' => 'Text',
      'staging_hierarchy' => 'Text',
      'darwin_hierarchy' => 'Text',
      'parent_ref_internal' => 'Number',
      'parent_updated' => 'Boolean',
      'parent_ref' => 'ForeignKey',
      'import_ref' => 'ForeignKey',
    ));
  }
}
