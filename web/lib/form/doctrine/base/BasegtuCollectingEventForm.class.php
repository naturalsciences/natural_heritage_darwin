<?php

/**
 * gtuCollectingEvent form base class.
 *
 * @method gtuCollectingEvent getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasegtuCollectingEventForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputText(),
      'event_code'         => new sfWidgetFormTextarea(),
      'gtu_ref'            => new sfWidgetFormInputText(),
      'expedition_ref'     => new sfWidgetFormInputText(),
      'gtu_from_date_mask' => new sfWidgetFormInputText(),
      'gtu_from_date'      => new sfWidgetFormTextarea(),
      'gtu_to_date_mask'   => new sfWidgetFormInputText(),
      'gtu_to_date'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorPass(array('required' => false)),
      'event_code'         => new sfValidatorString(),
      'gtu_ref'            => new sfValidatorInteger(array('required' => false)),
      'expedition_ref'     => new sfValidatorInteger(array('required' => false)),
      'gtu_from_date_mask' => new sfValidatorInteger(array('required' => false)),
      'gtu_from_date'      => new sfValidatorString(array('required' => false)),
      'gtu_to_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'gtu_to_date'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('gtu_collecting_event[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'gtuCollectingEvent';
  }

}
