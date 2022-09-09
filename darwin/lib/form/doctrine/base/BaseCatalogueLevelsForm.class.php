<?php

/**
 * CatalogueLevels form base class.
 *
 * @method CatalogueLevels getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueLevelsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['level_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['level_type'] = new sfValidatorString();

    $this->widgetSchema   ['level_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['level_name'] = new sfValidatorString();

    $this->widgetSchema   ['level_sys_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['level_sys_name'] = new sfValidatorString();

    $this->widgetSchema   ['optional_level'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['optional_level'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['level_order'] = new sfWidgetFormInputText();
    $this->validatorSchema['level_order'] = new sfValidatorInteger();

    $this->widgetSchema->setNameFormat('catalogue_levels[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueLevels';
  }

}
