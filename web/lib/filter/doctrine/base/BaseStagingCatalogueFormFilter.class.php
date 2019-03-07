<?php

/**
 * StagingCatalogue filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStagingCatalogueFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'import_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true)),
      'name'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'level_ref'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'catalogue_ref'         => new sfWidgetFormFilterInput(),
      'is_reference_taxonomy' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'source_taxonomy'       => new sfWidgetFormFilterInput(),
      'name_cluster'          => new sfWidgetFormFilterInput(),
      'imported'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'import_exception'      => new sfWidgetFormFilterInput(),
      'staging_hierarchy'     => new sfWidgetFormFilterInput(),
      'darwin_hierarchy'      => new sfWidgetFormFilterInput(),
      'parent_ref_internal'   => new sfWidgetFormFilterInput(),
      'parent_updated'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'import_ref'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id')),
      'name'                  => new sfValidatorPass(array('required' => false)),
      'level_ref'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_ref'            => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'catalogue_ref'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_reference_taxonomy' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'source_taxonomy'       => new sfValidatorPass(array('required' => false)),
      'name_cluster'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'imported'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'import_exception'      => new sfValidatorPass(array('required' => false)),
      'staging_hierarchy'     => new sfValidatorPass(array('required' => false)),
      'darwin_hierarchy'      => new sfValidatorPass(array('required' => false)),
      'parent_ref_internal'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_updated'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('staging_catalogue_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingCatalogue';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'import_ref'            => 'ForeignKey',
      'name'                  => 'Text',
      'level_ref'             => 'Number',
      'parent_ref'            => 'ForeignKey',
      'catalogue_ref'         => 'Number',
      'is_reference_taxonomy' => 'Boolean',
      'source_taxonomy'       => 'Text',
      'name_cluster'          => 'Number',
      'imported'              => 'Boolean',
      'import_exception'      => 'Text',
      'staging_hierarchy'     => 'Text',
      'darwin_hierarchy'      => 'Text',
      'parent_ref_internal'   => 'Number',
      'parent_updated'        => 'Boolean',
    );
  }
}
