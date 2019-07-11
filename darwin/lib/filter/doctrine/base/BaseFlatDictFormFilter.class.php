<?php

/**
 * FlatDict filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseFlatDictFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['dict_field'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['dict_field'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['dict_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['dict_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('flat_dict_filters[%s]');
  }

  public function getModelName()
  {
    return 'FlatDict';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'dict_field' => 'Text',
      'dict_value' => 'Text',
    ));
  }
}
