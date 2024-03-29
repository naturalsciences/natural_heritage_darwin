<?php

/**
 * SpecimensTools form base class.
 *
 * @method SpecimensTools getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensToolsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_tool_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => false));
    $this->validatorSchema['collecting_tool_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_tool_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => false));
    $this->validatorSchema['collecting_tool_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_tools[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensTools';
  }

}
