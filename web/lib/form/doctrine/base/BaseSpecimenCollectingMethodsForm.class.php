<?php

/**
 * SpecimenCollectingMethods form base class.
 *
 * @method SpecimenCollectingMethods getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimenCollectingMethodsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimen_collecting_methods[%s]');
  }

  public function getModelName()
  {
    return 'SpecimenCollectingMethods';
  }

}
