<?php

/**
 * SpecimensCode form base class.
 *
 * @method SpecimensCode getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensCodeForm extends SpecimensForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_main'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_main'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('specimens_code[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensCode';
  }

}
