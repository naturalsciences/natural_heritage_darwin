<?php

/**
 * StagingMultimedia filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingMultimediaFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['is_digital'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_digital'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['description'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['uri'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['filename'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['filename'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mime_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['visible'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['visible'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['publishable'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['publishable'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['extracted_info'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['extracted_info'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['technical_parameters'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['technical_parameters'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['external_uri'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['external_uri'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['internet_protocol'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['internet_protocol'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['field_observations'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['field_observations'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('staging_multimedia_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingMultimedia';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'is_digital' => 'Boolean',
      'type' => 'Text',
      'sub_type' => 'Text',
      'title' => 'Text',
      'description' => 'Text',
      'uri' => 'Text',
      'filename' => 'Text',
      'creation_date' => 'Text',
      'creation_date_mask' => 'Number',
      'mime_type' => 'Text',
      'visible' => 'Boolean',
      'publishable' => 'Boolean',
      'extracted_info' => 'Text',
      'technical_parameters' => 'Text',
      'external_uri' => 'Text',
      'internet_protocol' => 'Text',
      'field_observations' => 'Text',
    ));
  }
}
