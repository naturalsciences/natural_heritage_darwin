<?php

/**
 * SpecimensCode filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensCodeFormFilter extends SpecimensFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_main'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_main'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('specimens_code_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensCode';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'code_main' => 'Text',
    ));
  }
}
