<?php

/**
 * Imports filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseImportsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['filename'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['filename'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema   ['format'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['format'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['state'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['state'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['initial_count'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['initial_count'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['is_finished'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_finished'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['errors_in_import'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['errors_in_import'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['template_version'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['template_version'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['exclude_invalid_entries'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['exclude_invalid_entries'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['taxonomy_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxonomy_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_reference_taxonomy'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['source_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_taxonomy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['definition_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['definition_taxonomy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['url_website_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['url_website_taxonomy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['url_webservice_taxonomy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['url_webservice_taxonomy'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['specimen_taxonomy_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['specimen_taxonomy_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['working'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['working'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mime_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['taxonomy_kingdom'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['taxonomy_kingdom'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['merge_gtu'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['merge_gtu'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['gtu_include_date'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['gtu_include_date'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['gtu_tags_in_merge'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['gtu_tags_in_merge'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['add_collection_prefix'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['add_collection_prefix'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['sensitive_information_withheld'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['sensitive_information_withheld'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['source_database'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source_database'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true));
    $this->validatorSchema['collection_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('imports_filters[%s]');
  }

  public function getModelName()
  {
    return 'Imports';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'filename' => 'Text',
      'user_ref' => 'ForeignKey',
      'format' => 'Text',
      'collection_ref' => 'ForeignKey',
      'state' => 'Text',
      'created_at' => 'Text',
      'updated_at' => 'Text',
      'initial_count' => 'Number',
      'is_finished' => 'Boolean',
      'errors_in_import' => 'Text',
      'template_version' => 'Text',
      'exclude_invalid_entries' => 'Boolean',
      'taxonomy_name' => 'Text',
      'is_reference_taxonomy' => 'Boolean',
      'source_taxonomy' => 'Text',
      'creation_date' => 'Text',
      'creation_date_mask' => 'Number',
      'definition_taxonomy' => 'Text',
      'url_website_taxonomy' => 'Text',
      'url_webservice_taxonomy' => 'Text',
      'specimen_taxonomy_ref' => 'Number',
      'working' => 'Boolean',
      'mime_type' => 'Text',
      'taxonomy_kingdom' => 'Text',
      'merge_gtu' => 'Boolean',
      'gtu_include_date' => 'Boolean',
      'gtu_tags_in_merge' => 'Boolean',
      'add_collection_prefix' => 'Boolean',
      'sensitive_information_withheld' => 'Boolean',
      'source_database' => 'Text',
      'collection_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
    ));
  }
}
