<?php

/**
 * Expeditions filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseExpeditionsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name_indexed'              => new sfWidgetFormFilterInput(),
      'expedition_from_date_mask' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'expedition_from_date'      => new sfWidgetFormFilterInput(),
      'expedition_to_date_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'expedition_to_date'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                      => new sfValidatorPass(array('required' => false)),
      'name_indexed'              => new sfValidatorPass(array('required' => false)),
      'expedition_from_date_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expedition_from_date'      => new sfValidatorPass(array('required' => false)),
      'expedition_to_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expedition_to_date'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('expeditions_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Expeditions';
  }

  public function getFields()
  {
    return array(
      'id'                        => 'Number',
      'name'                      => 'Text',
      'name_indexed'              => 'Text',
      'expedition_from_date_mask' => 'Number',
      'expedition_from_date'      => 'Text',
      'expedition_to_date_mask'   => 'Number',
      'expedition_to_date'        => 'Text',
    );
  }
}
