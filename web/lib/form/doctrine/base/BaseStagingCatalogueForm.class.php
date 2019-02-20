<?php

/**
 * StagingCatalogue form base class.
 *
 * @method StagingCatalogue getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingCatalogueForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'import_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false)),
      'name'                  => new sfWidgetFormTextarea(),
      'level_ref'             => new sfWidgetFormInputText(),
      'parent_ref'            => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'catalogue_ref'         => new sfWidgetFormInputText(),
      'is_reference_taxonomy' => new sfWidgetFormInputCheckbox(),
      'source_taxonomy'       => new sfWidgetFormTextarea(),
      'name_cluster'          => new sfWidgetFormInputText(),
      'imported'              => new sfWidgetFormInputCheckbox(),
      'import_exception'      => new sfWidgetFormTextarea(),
      'staging_hierarchy'     => new sfWidgetFormTextarea(),
      'darwin_hierarchy'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'import_ref'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'))),
      'name'                  => new sfValidatorString(),
      'level_ref'             => new sfValidatorInteger(),
      'parent_ref'            => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'required' => false)),
      'catalogue_ref'         => new sfValidatorInteger(array('required' => false)),
      'is_reference_taxonomy' => new sfValidatorBoolean(array('required' => false)),
      'source_taxonomy'       => new sfValidatorString(array('required' => false)),
      'name_cluster'          => new sfValidatorInteger(array('required' => false)),
      'imported'              => new sfValidatorBoolean(array('required' => false)),
      'import_exception'      => new sfValidatorString(array('required' => false)),
      'staging_hierarchy'     => new sfValidatorString(array('required' => false)),
      'darwin_hierarchy'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_catalogue[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingCatalogue';
  }

}
