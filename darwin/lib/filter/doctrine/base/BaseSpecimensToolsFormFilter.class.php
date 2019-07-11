<?php

/**
 * SpecimensTools filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensToolsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_tool_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => true));
    $this->validatorSchema['collecting_tool_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingTools'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_tool_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => true));
    $this->validatorSchema['collecting_tool_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingTools'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_tools_filters[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensTools';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'specimen_ref' => 'ForeignKey',
      'collecting_tool_ref' => 'ForeignKey',
      'specimen_ref' => 'ForeignKey',
      'collecting_tool_ref' => 'ForeignKey',
    ));
  }
}
