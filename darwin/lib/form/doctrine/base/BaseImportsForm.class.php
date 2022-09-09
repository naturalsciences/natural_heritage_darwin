<?php

/**
 * Imports form base class.
 *
 * @method Imports getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseImportsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['filename'] = new sfWidgetFormTextarea();
    $this->validatorSchema['filename'] = new sfValidatorString();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['format'] = new sfWidgetFormTextarea();
    $this->validatorSchema['format'] = new sfValidatorString();

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['state'] = new sfWidgetFormTextarea();
    $this->validatorSchema['state'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormTextarea();
    $this->validatorSchema['created_at'] = new sfValidatorString();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormTextarea();
    $this->validatorSchema['updated_at'] = new sfValidatorString();

    $this->widgetSchema   ['initial_count'] = new sfWidgetFormInputText();
    $this->validatorSchema['initial_count'] = new sfValidatorInteger();

    $this->widgetSchema   ['is_finished'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_finished'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['errors_in_import'] = new sfWidgetFormTextarea();
    $this->validatorSchema['errors_in_import'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['template_version'] = new sfWidgetFormTextarea();
    $this->validatorSchema['template_version'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['exclude_invalid_entries'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['exclude_invalid_entries'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['taxonomy_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxonomy_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['source_taxonomy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_taxonomy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['definition_taxonomy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['definition_taxonomy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['url_website_taxonomy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['url_website_taxonomy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['url_webservice_taxonomy'] = new sfWidgetFormTextarea();
    $this->validatorSchema['url_webservice_taxonomy'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_taxonomy_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['working'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['working'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mime_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['taxonomy_kingdom'] = new sfWidgetFormTextarea();
    $this->validatorSchema['taxonomy_kingdom'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['merge_gtu'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['merge_gtu'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['gtu_include_date'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['gtu_include_date'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['gtu_tags_in_merge'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['gtu_tags_in_merge'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['add_collection_prefix'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['add_collection_prefix'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['sensitive_information_withheld'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['sensitive_information_withheld'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['source_database'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source_database'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('imports[%s]');
  }

  public function getModelName()
  {
    return 'Imports';
  }

}
