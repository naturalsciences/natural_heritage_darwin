<?php

/**
 * Igs form base class.
 *
 * @method Igs getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseIgsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'ig_num'         => new sfWidgetFormTextarea(),
      'ig_num_indexed' => new sfWidgetFormTextarea(),
      'ig_date_mask'   => new sfWidgetFormInputText(),
      'ig_date'        => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'ig_num'         => new sfValidatorString(),
      'ig_num_indexed' => new sfValidatorString(),
      'ig_date_mask'   => new sfValidatorInteger(array('required' => false)),
      'ig_date'        => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('igs[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Igs';
  }

}
