<?php

/**
 * FlatDict form base class.
 *
 * @method FlatDict getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseFlatDictForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['dict_field'] = new sfWidgetFormTextarea();
    $this->validatorSchema['dict_field'] = new sfValidatorString();

    $this->widgetSchema   ['dict_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['dict_value'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('flat_dict[%s]');
  }

  public function getModelName()
  {
    return 'FlatDict';
  }

}
