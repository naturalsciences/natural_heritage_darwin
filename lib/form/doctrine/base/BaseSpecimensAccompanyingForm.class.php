<?php

/**
 * SpecimensAccompanying form base class.
 *
 * @method SpecimensAccompanying getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSpecimensAccompanyingForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'accompanying_type' => new sfWidgetFormTextarea(),
      'specimen_ref'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false)),
      'taxon_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'mineral_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'form'              => new sfWidgetFormTextarea(),
      'quantity'          => new sfWidgetFormInputText(),
      'unit'              => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'accompanying_type' => new sfValidatorString(array('required' => false)),
      'specimen_ref'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'))),
      'taxon_ref'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'required' => false)),
      'mineral_ref'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'required' => false)),
      'form'              => new sfValidatorString(array('required' => false)),
      'quantity'          => new sfValidatorNumber(array('required' => false)),
      'unit'              => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens_accompanying[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensAccompanying';
  }

}
