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
      'insurance_year'      => new sfWidgetFormFilterInput(),
      'insurer_ref'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'insurance_value'     => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'insurance_currency'  => new sfValidatorPass(array('required' => false)),
      'insurance_year'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'insurer_ref'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
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
      'insurance_year'      => 'Number',
      'insurer_ref'         => 'ForeignKey',
    );
  }
}
