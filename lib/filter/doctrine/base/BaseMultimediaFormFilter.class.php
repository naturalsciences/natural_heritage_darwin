<?php

/**
 * Multimedia filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseMultimediaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'is_digital'                     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'type'                           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_type'                       => new sfWidgetFormFilterInput(),
      'title'                          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'title_indexed'                  => new sfWidgetFormFilterInput(),
      'subject'                        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'coverage'                       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'apercu_path'                    => new sfWidgetFormFilterInput(),
      'copyright'                      => new sfWidgetFormFilterInput(),
      'license'                        => new sfWidgetFormFilterInput(),
      'uri'                            => new sfWidgetFormFilterInput(),
      'descriptive_ts'                 => new sfWidgetFormFilterInput(),
      'descriptive_language_full_text' => new sfWidgetFormFilterInput(),
      'creation_date'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'creation_date_mask'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_from'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_from_mask'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_to'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_to_mask'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parent'), 'add_empty' => true)),
      'path'                           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mime_type'                      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'is_digital'                     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'type'                           => new sfValidatorPass(array('required' => false)),
      'sub_type'                       => new sfValidatorPass(array('required' => false)),
      'title'                          => new sfValidatorPass(array('required' => false)),
      'title_indexed'                  => new sfValidatorPass(array('required' => false)),
      'subject'                        => new sfValidatorPass(array('required' => false)),
      'coverage'                       => new sfValidatorPass(array('required' => false)),
      'apercu_path'                    => new sfValidatorPass(array('required' => false)),
      'copyright'                      => new sfValidatorPass(array('required' => false)),
      'license'                        => new sfValidatorPass(array('required' => false)),
      'uri'                            => new sfValidatorPass(array('required' => false)),
      'descriptive_ts'                 => new sfValidatorPass(array('required' => false)),
      'descriptive_language_full_text' => new sfValidatorPass(array('required' => false)),
      'creation_date'                  => new sfValidatorPass(array('required' => false)),
      'creation_date_mask'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'publication_date_from'          => new sfValidatorPass(array('required' => false)),
      'publication_date_from_mask'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'publication_date_to'            => new sfValidatorPass(array('required' => false)),
      'publication_date_to_mask'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_ref'                     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parent'), 'column' => 'id')),
      'path'                           => new sfValidatorPass(array('required' => false)),
      'mime_type'                      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('multimedia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Multimedia';
  }

  public function getFields()
  {
    return array(
      'id'                             => 'Number',
      'is_digital'                     => 'Boolean',
      'type'                           => 'Text',
      'sub_type'                       => 'Text',
      'title'                          => 'Text',
      'title_indexed'                  => 'Text',
      'subject'                        => 'Text',
      'coverage'                       => 'Text',
      'apercu_path'                    => 'Text',
      'copyright'                      => 'Text',
      'license'                        => 'Text',
      'uri'                            => 'Text',
      'descriptive_ts'                 => 'Text',
      'descriptive_language_full_text' => 'Text',
      'creation_date'                  => 'Text',
      'creation_date_mask'             => 'Number',
      'publication_date_from'          => 'Text',
      'publication_date_from_mask'     => 'Number',
      'publication_date_to'            => 'Text',
      'publication_date_to_mask'       => 'Number',
      'parent_ref'                     => 'ForeignKey',
      'path'                           => 'Text',
      'mime_type'                      => 'Text',
    );
  }
}