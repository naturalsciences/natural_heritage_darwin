<?php

/**
 * TemporalInformation filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTemporalInformationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'gtu_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'specimen_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'from_date_mask' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'from_date'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'to_date_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'to_date'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'gtu_ref'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id')),
      'specimen_ref'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'from_date_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'from_date'      => new sfValidatorPass(array('required' => false)),
      'to_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'to_date'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('temporal_information_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemporalInformation';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'gtu_ref'        => 'ForeignKey',
      'specimen_ref'   => 'ForeignKey',
      'from_date_mask' => 'Number',
      'from_date'      => 'Text',
      'to_date_mask'   => 'Number',
      'to_date'        => 'Text',
    );
  }
}
