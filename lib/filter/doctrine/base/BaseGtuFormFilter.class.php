<?php

/**
 * Gtu filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseGtuFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'code'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_from_date_mask' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_from_date'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_to_date_mask'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_to_date'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'latitude'           => new sfWidgetFormFilterInput(),
      'longitude'          => new sfWidgetFormFilterInput(),
      'location'           => new sfWidgetFormFilterInput(),
      'lat_long_accuracy'  => new sfWidgetFormFilterInput(),
      'elevation'          => new sfWidgetFormFilterInput(),
      'elevation_accuracy' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'code'               => new sfValidatorPass(array('required' => false)),
      'gtu_from_date_mask' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_from_date'      => new sfValidatorPass(array('required' => false)),
      'gtu_to_date_mask'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_to_date'        => new sfValidatorPass(array('required' => false)),
      'latitude'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'location'           => new sfValidatorPass(array('required' => false)),
      'lat_long_accuracy'  => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation_accuracy' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('gtu_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Gtu';
  }

  public function getFields()
  {
    return array(
      'id'                 => 'Number',
      'code'               => 'Text',
      'gtu_from_date_mask' => 'Number',
      'gtu_from_date'      => 'Text',
      'gtu_to_date_mask'   => 'Number',
      'gtu_to_date'        => 'Text',
      'latitude'           => 'Number',
      'longitude'          => 'Number',
      'location'           => 'Text',
      'lat_long_accuracy'  => 'Number',
      'elevation'          => 'Number',
      'elevation_accuracy' => 'Number',
    );
  }
}
