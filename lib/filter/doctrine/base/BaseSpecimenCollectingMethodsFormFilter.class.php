<?php

/**
 * SpecimenCollectingMethods filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimenCollectingMethodsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'specimen_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'collecting_method_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('CollectingMethods'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'specimen_ref'          => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'collecting_method_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('CollectingMethods'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('specimen_collecting_methods_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimenCollectingMethods';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'specimen_ref'          => 'ForeignKey',
      'collecting_method_ref' => 'ForeignKey',
    );
  }
}
