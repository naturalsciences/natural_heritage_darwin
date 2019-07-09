<?php

/**
 * StagingCollectingMethods filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingCollectingMethodsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['collecting_method_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true));
    $this->validatorSchema['collecting_method_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id'));

    $this->widgetSchema   ['staging_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true));
    $this->validatorSchema['staging_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_collecting_methods_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingCollectingMethods';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'staging_ref' => 'ForeignKey',
      'collecting_method_ref' => 'ForeignKey',
      'collecting_method_ref' => 'ForeignKey',
      'staging_ref' => 'ForeignKey',
    ));
  }
}
