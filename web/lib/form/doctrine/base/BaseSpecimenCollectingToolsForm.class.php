<?php

/**
 * SpecimenCollectingTools form base class.
 *
 * @method SpecimenCollectingTools getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimenCollectingToolsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'specimen_ref'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false)),
      'collecting_tool_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'specimen_ref'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'))),
      'collecting_tool_ref' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingTools'))),
    ));

    $this->widgetSchema->setNameFormat('specimen_collecting_tools[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimenCollectingTools';
  }

}
