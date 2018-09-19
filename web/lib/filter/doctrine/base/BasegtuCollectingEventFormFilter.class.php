<?php

/**
 * gtuCollectingEvent filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasegtuCollectingEventFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormFilterInput(),
      'event_code'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_ref'            => new sfWidgetFormFilterInput(),
      'expedition_ref'     => new sfWidgetFormFilterInput(),
      'gtu_from_date_mask' => new sfWidgetFormFilterInput(),
      'gtu_from_date'      => new sfWidgetFormFilterInput(),
      'gtu_to_date_mask'   => new sfWidgetFormFilterInput(),
      'gtu_to_date'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorPass(array('required' => false)),
      'event_code'         => new sfValidatorPass(array('required' => false)),
      'gtu_ref'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expedition_ref'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_from_date_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_from_date'      => new sfValidatorPass(array('required' => false)),
      'gtu_to_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_to_date'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('gtu_collecting_event_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'gtuCollectingEvent';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Text',
      'event_code'         => 'Text',
      'gtu_ref'            => 'Number',
      'expedition_ref'     => 'Number',
      'gtu_from_date_mask' => 'Number',
      'gtu_from_date'      => 'Text',
      'gtu_to_date_mask'   => 'Number',
      'gtu_to_date'        => 'Text',
    );
  }
}
