<?php

/**
 * Imports form base class.
 *
 * @method Imports getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseImportsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputHidden(),
      'filename'                       => new sfWidgetFormTextarea(),
      'user_ref'                       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false)),
      'format'                         => new sfWidgetFormTextarea(),
      'collection_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'), 'add_empty' => false)),
      'state'                          => new sfWidgetFormTextarea(),
      'created_at'                     => new sfWidgetFormTextarea(),
      'updated_at'                     => new sfWidgetFormTextarea(),
      'initial_count'                  => new sfWidgetFormInputText(),
      'is_finished'                    => new sfWidgetFormInputCheckbox(),
      'errors_in_import'               => new sfWidgetFormTextarea(),
      'template_version'               => new sfWidgetFormTextarea(),
      'exclude_invalid_entries'        => new sfWidgetFormInputCheckbox(),
      'creation_date'                  => new sfWidgetFormTextarea(),
      'creation_date_mask'             => new sfWidgetFormInputText(),
      'working'                        => new sfWidgetFormInputCheckbox(),
      'mime_type'                      => new sfWidgetFormTextarea(),
      'gtu_include_date'               => new sfWidgetFormInputCheckbox(),
      'gtu_tags_in_merge'              => new sfWidgetFormInputCheckbox(),
      'sensitive_information_withheld' => new sfWidgetFormInputCheckbox(),
      'source_database'                => new sfWidgetFormTextarea(),
      'taxonomy_kingdom'               => new sfWidgetFormTextarea(),
      'specimen_taxonomy_ref'          => new sfWidgetFormInputText(),
      'history_taxonomy'               => new sfWidgetFormTextarea(),
      'collection_ref_for_gtu'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'filename'                       => new sfValidatorString(),
      'user_ref'                       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Users'))),
      'format'                         => new sfValidatorString(),
      'collection_ref'                 => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Collections'))),
      'state'                          => new sfValidatorString(array('required' => false)),
      'created_at'                     => new sfValidatorString(),
      'updated_at'                     => new sfValidatorString(),
      'initial_count'                  => new sfValidatorInteger(),
      'is_finished'                    => new sfValidatorBoolean(array('required' => false)),
      'errors_in_import'               => new sfValidatorString(array('required' => false)),
      'template_version'               => new sfValidatorString(array('required' => false)),
      'exclude_invalid_entries'        => new sfValidatorBoolean(array('required' => false)),
      'creation_date'                  => new sfValidatorString(array('required' => false)),
      'creation_date_mask'             => new sfValidatorInteger(array('required' => false)),
      'working'                        => new sfValidatorBoolean(array('required' => false)),
      'mime_type'                      => new sfValidatorString(array('required' => false)),
      'gtu_include_date'               => new sfValidatorBoolean(array('required' => false)),
      'gtu_tags_in_merge'              => new sfValidatorBoolean(array('required' => false)),
      'sensitive_information_withheld' => new sfValidatorBoolean(array('required' => false)),
      'source_database'                => new sfValidatorString(array('required' => false)),
      'taxonomy_kingdom'               => new sfValidatorString(array('required' => false)),
      'specimen_taxonomy_ref'          => new sfValidatorInteger(array('required' => false)),
      'history_taxonomy'               => new sfValidatorString(array('required' => false)),
      'collection_ref_for_gtu'         => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('imports[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Imports';
  }

}
