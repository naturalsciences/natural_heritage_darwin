<?php

/**
 * PossibleUpperLevels form base class.
 *
 * @method PossibleUpperLevels getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePossibleUpperLevelsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'level_ref'       => new sfWidgetFormInputHidden(),
      'level_upper_ref' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'level_ref'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_ref')), 'empty_value' => $this->getObject()->get('level_ref'), 'required' => false)),
      'level_upper_ref' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_upper_ref')), 'empty_value' => $this->getObject()->get('level_upper_ref'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('possible_upper_levels[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PossibleUpperLevels';
  }

}
