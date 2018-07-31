<?php

/**
 * Imports filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseImportsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'filename'                       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_ref'                       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => true)),
      'format'                         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'collection_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => true)),
      'state'                          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'updated_at'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'initial_count'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_finished'                    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'errors_in_import'               => new sfWidgetFormFilterInput(),
      'template_version'               => new sfWidgetFormFilterInput(),
      'exclude_invalid_entries'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'taxonomy_name'                  => new sfWidgetFormFilterInput(),
      'is_reference_taxonomy'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'source_taxonomy'                => new sfWidgetFormFilterInput(),
      'creation_date'                  => new sfWidgetFormFilterInput(),
      'creation_date_mask'             => new sfWidgetFormFilterInput(),
      'definition_taxonomy'            => new sfWidgetFormFilterInput(),
      'url_website_taxonomy'           => new sfWidgetFormFilterInput(),
      'url_webservice_taxonomy'        => new sfWidgetFormFilterInput(),
      'specimen_taxonomy_ref'          => new sfWidgetFormFilterInput(),
      'working'                        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'mime_type'                      => new sfWidgetFormFilterInput(),
      'taxonomy_kingdom'               => new sfWidgetFormFilterInput(),
      'gtu_include_date'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'gtu_tags_in_merge'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'sensitive_information_withheld' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'source_database'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'filename'                       => new sfValidatorPass(array('required' => false)),
      'user_ref'                       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Users'), 'column' => 'id')),
      'format'                         => new sfValidatorPass(array('required' => false)),
      'collection_ref'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Collections'), 'column' => 'id')),
      'state'                          => new sfValidatorPass(array('required' => false)),
      'created_at'                     => new sfValidatorPass(array('required' => false)),
      'updated_at'                     => new sfValidatorPass(array('required' => false)),
      'initial_count'                  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_finished'                    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'errors_in_import'               => new sfValidatorPass(array('required' => false)),
      'template_version'               => new sfValidatorPass(array('required' => false)),
      'exclude_invalid_entries'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'taxonomy_name'                  => new sfValidatorPass(array('required' => false)),
      'is_reference_taxonomy'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'source_taxonomy'                => new sfValidatorPass(array('required' => false)),
      'creation_date'                  => new sfValidatorPass(array('required' => false)),
      'creation_date_mask'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'definition_taxonomy'            => new sfValidatorPass(array('required' => false)),
      'url_website_taxonomy'           => new sfValidatorPass(array('required' => false)),
      'url_webservice_taxonomy'        => new sfValidatorPass(array('required' => false)),
      'specimen_taxonomy_ref'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'working'                        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'mime_type'                      => new sfValidatorPass(array('required' => false)),
      'taxonomy_kingdom'               => new sfValidatorPass(array('required' => false)),
      'gtu_include_date'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'gtu_tags_in_merge'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'sensitive_information_withheld' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'source_database'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('imports_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Imports';
  }

  public function getFields()
  {
    return array(
      'id'                             => 'Number',
      'filename'                       => 'Text',
      'user_ref'                       => 'ForeignKey',
      'format'                         => 'Text',
      'collection_ref'                 => 'ForeignKey',
      'state'                          => 'Text',
      'created_at'                     => 'Text',
      'updated_at'                     => 'Text',
      'initial_count'                  => 'Number',
      'is_finished'                    => 'Boolean',
      'errors_in_import'               => 'Text',
      'template_version'               => 'Text',
      'exclude_invalid_entries'        => 'Boolean',
      'taxonomy_name'                  => 'Text',
      'is_reference_taxonomy'          => 'Boolean',
      'source_taxonomy'                => 'Text',
      'creation_date'                  => 'Text',
      'creation_date_mask'             => 'Number',
      'definition_taxonomy'            => 'Text',
      'url_website_taxonomy'           => 'Text',
      'url_webservice_taxonomy'        => 'Text',
      'specimen_taxonomy_ref'          => 'Number',
      'working'                        => 'Boolean',
      'mime_type'                      => 'Text',
      'taxonomy_kingdom'               => 'Text',
      'gtu_include_date'               => 'Boolean',
      'gtu_tags_in_merge'              => 'Boolean',
      'sensitive_information_withheld' => 'Boolean',
      'source_database'                => 'Text',
    );
  }
}
