<?php

/**
 * SpecimensAccompanying filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSpecimensAccompanyingFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'accompanying_type' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_ref'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true)),
      'taxon_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Taxonomy'), 'add_empty' => true)),
      'mineral_ref'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mineralogy'), 'add_empty' => true)),
      'form'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'quantity'          => new sfWidgetFormFilterInput(),
      'unit'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'accompanying_type' => new sfValidatorPass(array('required' => false)),
      'specimen_ref'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Specimens'), 'column' => 'id')),
      'taxon_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Taxonomy'), 'column' => 'id')),
      'mineral_ref'       => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mineralogy'), 'column' => 'id')),
      'form'              => new sfValidatorPass(array('required' => false)),
      'quantity'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'unit'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('specimens_accompanying_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SpecimensAccompanying';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'accompanying_type' => 'Text',
      'specimen_ref'      => 'ForeignKey',
      'taxon_ref'         => 'ForeignKey',
      'mineral_ref'       => 'ForeignKey',
      'form'              => 'Text',
      'quantity'          => 'Number',
      'unit'              => 'Text',
    );
  }
}
