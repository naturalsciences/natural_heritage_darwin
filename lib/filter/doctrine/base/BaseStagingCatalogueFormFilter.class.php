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
      'import_ref'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true)),
      'name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'level_ref'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'catalogue_ref' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'import_ref'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id')),
      'name'          => new sfValidatorPass(array('required' => false)),
      'level_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_ref'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'catalogue_ref' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'            => 'Number',
      'import_ref'    => 'ForeignKey',
      'name'          => 'Text',
      'level_ref'     => 'Number',
      'parent_ref'    => 'ForeignKey',
      'catalogue_ref' => 'Number',
    );
  }
}
