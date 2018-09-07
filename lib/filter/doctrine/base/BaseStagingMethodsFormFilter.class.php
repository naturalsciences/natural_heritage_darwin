<?php

/**
 * StagingMethods filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseStagingMethodsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'staging_ref'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Staging'), 'add_empty' => true)),
      'collecting_method_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'staging_ref'           => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Staging'), 'column' => 'id')),
      'collecting_method_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('staging_methods_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingMethods';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'staging_ref'           => 'ForeignKey',
      'collecting_method_ref' => 'ForeignKey',
    );
  }
}
