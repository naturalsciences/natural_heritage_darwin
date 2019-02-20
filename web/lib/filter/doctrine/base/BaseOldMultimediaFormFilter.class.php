<?php

/**
 * OldMultimedia filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseOldMultimediaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_digital'                     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'type'                           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_type'                       => new sfWidgetFormFilterInput(),
      'title'                          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'title_indexed'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'subject'                        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'coverage'                       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'apercu_path'                    => new sfWidgetFormFilterInput(),
      'copyright'                      => new sfWidgetFormFilterInput(),
      'license'                        => new sfWidgetFormFilterInput(),
      'uri'                            => new sfWidgetFormFilterInput(),
      'descriptive_ts'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'descriptive_language_full_text' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'creation_date'                  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'creation_date_mask'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_from'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'publication_date_from_mask'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'publication_date_to'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'publication_date_to_mask'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'parent_ref'                     => new sfWidgetFormFilterInput(),
      'path'                           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mime_type'                      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'creation_date'                  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'creation_date_mask'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'publication_date_from'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'publication_date_from_mask'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'publication_date_to'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'publication_date_to_mask'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'parent_ref'                     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'path'                           => new sfValidatorPass(array('required' => false)),
      'mime_type'                      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('old_multimedia_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'OldMultimedia';
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
      'creation_date'                  => 'Date',
      'creation_date_mask'             => 'Number',
      'publication_date_from'          => 'Date',
      'publication_date_from_mask'     => 'Number',
      'publication_date_to'            => 'Date',
      'publication_date_to_mask'       => 'Number',
      'parent_ref'                     => 'Number',
      'path'                           => 'Text',
      'mime_type'                      => 'Text',
    );
  }
}
