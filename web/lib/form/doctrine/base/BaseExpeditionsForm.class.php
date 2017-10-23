<?php

/**
 * Expeditions form base class.
 *
 * @method Expeditions getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseExpeditionsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'name'                      => new sfWidgetFormTextarea(),
      'name_indexed'              => new sfWidgetFormTextarea(),
      'expedition_from_date_mask' => new sfWidgetFormInputText(),
      'expedition_from_date'      => new sfWidgetFormTextarea(),
      'expedition_to_date_mask'   => new sfWidgetFormInputText(),
      'expedition_to_date'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                      => new sfValidatorString(),
      'name_indexed'              => new sfValidatorString(array('required' => false)),
      'expedition_from_date_mask' => new sfValidatorInteger(array('required' => false)),
      'expedition_from_date'      => new sfValidatorString(array('required' => false)),
      'expedition_to_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'expedition_to_date'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('expeditions[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Expeditions';
  }

}
