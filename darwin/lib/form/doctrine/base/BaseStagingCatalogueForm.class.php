<?php

/**
 * StagingCatalogue form base class.
 *
 * @method StagingCatalogue getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingCatalogueForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['level_ref'] = new sfValidatorInteger();

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['catalogue_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['catalogue_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['source_taxonomy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_taxonomy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_cluster'] = new sfWidgetFormInputText();
    $this->validatorSchema['name_cluster'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['imported'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['import_exception'] = new sfWidgetFormTextarea();
    $this->validatorSchema['import_exception'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['staging_hierarchy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['staging_hierarchy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['darwin_hierarchy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['darwin_hierarchy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['parent_ref_internal'] = new sfWidgetFormInputText();
    $this->validatorSchema['parent_ref_internal'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['parent_updated'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['parent_updated'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true));
    $this->validatorSchema['parent_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_catalogue[%s]');
  }

  public function getModelName()
  {
    return 'StagingCatalogue';
  }

}
