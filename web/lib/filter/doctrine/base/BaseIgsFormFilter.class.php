<?php

/**
 * Igs filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseIgsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ig_num'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ig_num_indexed' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ig_date_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'ig_date'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ig_num'         => new sfValidatorPass(array('required' => false)),
      'ig_num_indexed' => new sfValidatorPass(array('required' => false)),
      'ig_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'ig_date'        => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('igs_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Igs';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'ig_num'         => 'Text',
      'ig_num_indexed' => 'Text',
      'ig_date_mask'   => 'Number',
      'ig_date'        => 'Text',
    );
  }
}
