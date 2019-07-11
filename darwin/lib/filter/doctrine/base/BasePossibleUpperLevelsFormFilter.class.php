<?php

/**
 * PossibleUpperLevels filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePossibleUpperLevelsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'PossibleUpperLevels', 'column' => 'level_ref'));

    $this->widgetSchema   ['level_upper_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level_upper_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'PossibleUpperLevels', 'column' => 'level_upper_ref'));

    $this->widgetSchema   ['level_upper_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level_upper_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'PossibleUpperLevels', 'column' => 'level_upper_ref'));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'PossibleUpperLevels', 'column' => 'level_ref'));

    $this->widgetSchema->setNameFormat('possible_upper_levels_filters[%s]');
  }

  public function getModelName()
  {
    return 'PossibleUpperLevels';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'level_ref' => 'Number',
      'level_upper_ref' => 'Number',
      'level_upper_ref' => 'Number',
      'level_ref' => 'Number',
    ));
  }
}
