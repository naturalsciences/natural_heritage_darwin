<?php

/**
 * Preferences filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePreferencesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'pref_key'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'pref_value' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'user_ref'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'pref_key'   => new sfValidatorPass(array('required' => false)),
      'pref_value' => new sfValidatorPass(array('required' => false)),
      'user_ref'   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('preferences_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Preferences';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'pref_key'   => 'Text',
      'pref_value' => 'Text',
      'user_ref'   => 'ForeignKey',
    );
  }
}
