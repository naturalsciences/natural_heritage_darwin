<?php

/**
 * PossibleUpperLevels form base class.
 *
 * @method PossibleUpperLevels getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePossibleUpperLevelsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['level_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_ref')), 'empty_value' => $this->getObject()->get('level_ref'), 'required' => false));

    $this->widgetSchema   ['level_upper_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['level_upper_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_upper_ref')), 'empty_value' => $this->getObject()->get('level_upper_ref'), 'required' => false));

    $this->widgetSchema   ['level_upper_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['level_upper_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_upper_ref')), 'empty_value' => $this->getObject()->get('level_upper_ref'), 'required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['level_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('level_ref')), 'empty_value' => $this->getObject()->get('level_ref'), 'required' => false));

    $this->widgetSchema->setNameFormat('possible_upper_levels[%s]');
  }

  public function getModelName()
  {
    return 'PossibleUpperLevels';
  }

}
