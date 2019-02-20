<?php

/**
 * TemporalInformation form base class.
 *
 * @method TemporalInformation getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTemporalInformationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'gtu_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true)),
      'specimen_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'from_date_mask' => new sfWidgetFormInputText(),
      'from_date'      => new sfWidgetFormTextarea(),
      'to_date_mask'   => new sfWidgetFormInputText(),
      'to_date'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'gtu_ref'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'required' => false)),
      'specimen_ref'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'required' => false)),
      'from_date_mask' => new sfValidatorInteger(array('required' => false)),
      'from_date'      => new sfValidatorString(array('required' => false)),
      'to_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'to_date'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('temporal_information[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemporalInformation';
  }

}
