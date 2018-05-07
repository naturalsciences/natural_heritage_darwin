<?php

/**
 * LoanItems filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLoanItemsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'loan_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loan'), 'add_empty' => true)),
      'ig_ref'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Ig'), 'add_empty' => true)),
      'from_date'                => new sfWidgetFormFilterInput(),
      'to_date'                  => new sfWidgetFormFilterInput(),
      'specimen_ref'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DarwinParts'), 'add_empty' => true)),
      'details'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_count_tot'       => new sfWidgetFormFilterInput(),
      'specimen_count_males'     => new sfWidgetFormFilterInput(),
      'specimen_count_females'   => new sfWidgetFormFilterInput(),
      'specimen_count_juveniles' => new sfWidgetFormFilterInput(),
      'specimen_part'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'specimen_count'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'loan_ref'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loan'), 'column' => 'id')),
      'ig_ref'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Ig'), 'column' => 'id')),
      'from_date'                => new sfValidatorPass(array('required' => false)),
      'to_date'                  => new sfValidatorPass(array('required' => false)),
      'specimen_ref'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('DarwinParts'), 'column' => 'id')),
      'details'                  => new sfValidatorPass(array('required' => false)),
      'specimen_count_tot'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_males'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_females'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_count_juveniles' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'specimen_part'            => new sfValidatorPass(array('required' => false)),
      'specimen_count'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('loan_items_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LoanItems';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'loan_ref'                 => 'ForeignKey',
      'ig_ref'                   => 'ForeignKey',
      'from_date'                => 'Text',
      'to_date'                  => 'Text',
      'specimen_ref'             => 'ForeignKey',
      'details'                  => 'Text',
      'specimen_count_tot'       => 'Number',
      'specimen_count_males'     => 'Number',
      'specimen_count_females'   => 'Number',
      'specimen_count_juveniles' => 'Number',
      'specimen_part'            => 'Text',
      'specimen_count'           => 'Text',
    );
  }
}
