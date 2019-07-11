<?php

/**
 * CatalogueLevels filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCatalogueLevelsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['level_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level_sys_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_sys_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['optional_level'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['optional_level'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['level_order'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['level_order'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('catalogue_levels_filters[%s]');
  }

  public function getModelName()
  {
    return 'CatalogueLevels';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'level_type' => 'Text',
      'level_name' => 'Text',
      'level_sys_name' => 'Text',
      'optional_level' => 'Boolean',
      'level_order' => 'Number',
    ));
  }
}
