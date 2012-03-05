<?php

/**
 * SpecimensTools filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensToolsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'specimen_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'collecting_tool_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'specimen_ref'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'collecting_tool_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingTools'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('specimens_tools_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensTools';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'specimen_ref'        => 'ForeignKey',
      'collecting_tool_ref' => 'ForeignKey',
    );
  }
}
