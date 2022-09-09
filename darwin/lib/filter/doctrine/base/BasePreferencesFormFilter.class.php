<?php

/**
 * Preferences filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePreferencesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['pref_key'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['pref_key'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['pref_value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['pref_value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('preferences_filters[%s]');
  }

  public function getModelName()
  {
    return 'Preferences';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'pref_key' => 'Text',
      'pref_value' => 'Text',
      'user_ref' => 'ForeignKey',
      'user_ref' => 'ForeignKey',
    ));
  }
}
