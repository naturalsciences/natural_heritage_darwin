<?php

/**
 * Multimedia filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMultimediaFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormFilterInput(),
      'is_digital'          => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'type'                => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'sub_type'            => new sfWidgetFormFilterInput(),
      'title'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'uri'                 => new sfWidgetFormFilterInput(),
      'filename'            => new sfWidgetFormFilterInput(),
      'search_indexed'      => new sfWidgetFormFilterInput(),
      'creation_date'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'creation_date_mask'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'mime_type'           => new sfWidgetFormFilterInput(),
      'visible'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'publishable'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'extracted_info'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_digital'          => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'type'                => new sfValidatorPass(array('required' => false)),
      'sub_type'            => new sfValidatorPass(array('required' => false)),
      'title'               => new sfValidatorPass(array('required' => false)),
      'description'         => new sfValidatorPass(array('required' => false)),
      'uri'                 => new sfValidatorPass(array('required' => false)),
      'filename'            => new sfValidatorPass(array('required' => false)),
      'search_indexed'      => new sfValidatorPass(array('required' => false)),
      'creation_date'       => new sfValidatorPass(array('required' => false)),
      'creation_date_mask'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'mime_type'           => new sfValidatorPass(array('required' => false)),
      'visible'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'publishable'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'extracted_info'      => new sfValidatorPass(array('required' => false)),
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
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'is_digital'          => 'Boolean',
      'type'                => 'Text',
      'sub_type'            => 'Text',
      'title'               => 'Text',
      'description'         => 'Text',
      'uri'                 => 'Text',
      'filename'            => 'Text',
      'search_indexed'      => 'Text',
      'creation_date'       => 'Text',
      'creation_date_mask'  => 'Number',
      'mime_type'           => 'Text',
      'visible'             => 'Boolean',
      'publishable'         => 'Boolean',
      'extracted_info'      => 'Text',
    );
  }
}
