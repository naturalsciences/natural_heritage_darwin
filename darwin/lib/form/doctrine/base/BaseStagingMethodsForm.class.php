<?php

/**
 * StagingMethods form base class.
 *
 * @method StagingMethods getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingMethodsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => false));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => false));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_methods[%s]');
  }

  public function getModelName()
  {
    return 'StagingMethods';
  }

}
