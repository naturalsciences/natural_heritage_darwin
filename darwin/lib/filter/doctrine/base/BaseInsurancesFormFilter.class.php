<?php

/**
 * Insurances filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseInsurancesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'insurance_value'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'insurance_currency'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'insurer_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'date_from'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_from_mask'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_to'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'date_to_mask'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'contact_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Contact'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'insurance_value'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'insurance_currency'  => new sfValidatorPass(array('required' => false)),
      'insurer_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
      'date_from'           => new sfValidatorPass(array('required' => false)),
      'date_from_mask'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'date_to'             => new sfValidatorPass(array('required' => false)),
      'date_to_mask'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'contact_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Contact'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('insurances_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Insurances';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'insurance_value'     => 'Number',
      'insurance_currency'  => 'Text',
      'insurer_ref'         => 'ForeignKey',
      'date_from'           => 'Text',
      'date_from_mask'      => 'Number',
      'date_to'             => 'Text',
      'date_to_mask'        => 'Number',
      'contact_ref'         => 'ForeignKey',
    );
  }
}
