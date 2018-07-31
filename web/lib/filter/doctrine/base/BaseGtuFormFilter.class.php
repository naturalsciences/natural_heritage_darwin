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
      'code'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_from_date_mask'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_from_date'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_to_date_mask'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gtu_to_date'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'latitude'                => new sfWidgetFormFilterInput(),
      'longitude'               => new sfWidgetFormFilterInput(),
      'coordinates_source'      => new sfWidgetFormFilterInput(),
      'latitude_dms_degree'     => new sfWidgetFormFilterInput(),
      'latitude_dms_minutes'    => new sfWidgetFormFilterInput(),
      'latitude_dms_seconds'    => new sfWidgetFormFilterInput(),
      'latitude_dms_direction'  => new sfWidgetFormFilterInput(),
      'longitude_dms_degree'    => new sfWidgetFormFilterInput(),
      'longitude_dms_minutes'   => new sfWidgetFormFilterInput(),
      'longitude_dms_seconds'   => new sfWidgetFormFilterInput(),
      'longitude_dms_direction' => new sfWidgetFormFilterInput(),
      'latitude_utm'            => new sfWidgetFormFilterInput(),
      'longitude_utm'           => new sfWidgetFormFilterInput(),
      'utm_zone'                => new sfWidgetFormFilterInput(),
      'location'                => new sfWidgetFormFilterInput(),
      'lat_long_accuracy'       => new sfWidgetFormFilterInput(),
      'elevation'               => new sfWidgetFormFilterInput(),
      'elevation_accuracy'      => new sfWidgetFormFilterInput(),
      'elevation_unit'          => new sfWidgetFormFilterInput(),
      'iso3166'                 => new sfWidgetFormFilterInput(),
      'iso3166_subdivision'     => new sfWidgetFormFilterInput(),
      'wkt_str'                 => new sfWidgetFormFilterInput(),
      'ecosystem'               => new sfWidgetFormFilterInput(),
      'original_coordinates'    => new sfWidgetFormFilterInput(),
      'elevation_max'           => new sfWidgetFormFilterInput(),
      'depth_min'               => new sfWidgetFormFilterInput(),
      'depth_max'               => new sfWidgetFormFilterInput(),
      'depth_accuracy'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'code'                    => new sfValidatorPass(array('required' => false)),
      'gtu_from_date_mask'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_from_date'           => new sfValidatorPass(array('required' => false)),
      'gtu_to_date_mask'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_to_date'             => new sfValidatorPass(array('required' => false)),
      'latitude'                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'coordinates_source'      => new sfValidatorPass(array('required' => false)),
      'latitude_dms_degree'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'latitude_dms_minutes'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'latitude_dms_seconds'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'latitude_dms_direction'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'longitude_dms_degree'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'longitude_dms_minutes'   => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude_dms_seconds'   => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude_dms_direction' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'latitude_utm'            => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude_utm'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'utm_zone'                => new sfValidatorPass(array('required' => false)),
      'location'                => new sfValidatorPass(array('required' => false)),
      'lat_long_accuracy'       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation_accuracy'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation_unit'          => new sfValidatorPass(array('required' => false)),
      'iso3166'                 => new sfValidatorPass(array('required' => false)),
      'iso3166_subdivision'     => new sfValidatorPass(array('required' => false)),
      'wkt_str'                 => new sfValidatorPass(array('required' => false)),
      'ecosystem'               => new sfValidatorPass(array('required' => false)),
      'original_coordinates'    => new sfValidatorPass(array('required' => false)),
      'elevation_max'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'depth_min'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'depth_max'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'depth_accuracy'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
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
      'id'                      => 'Number',
      'code'                    => 'Text',
      'gtu_from_date_mask'      => 'Number',
      'gtu_from_date'           => 'Text',
      'gtu_to_date_mask'        => 'Number',
      'gtu_to_date'             => 'Text',
      'latitude'                => 'Number',
      'longitude'               => 'Number',
      'coordinates_source'      => 'Text',
      'latitude_dms_degree'     => 'Number',
      'latitude_dms_minutes'    => 'Number',
      'latitude_dms_seconds'    => 'Number',
      'latitude_dms_direction'  => 'Number',
      'longitude_dms_degree'    => 'Number',
      'longitude_dms_minutes'   => 'Number',
      'longitude_dms_seconds'   => 'Number',
      'longitude_dms_direction' => 'Number',
      'latitude_utm'            => 'Number',
      'longitude_utm'           => 'Number',
      'utm_zone'                => 'Text',
      'location'                => 'Text',
      'lat_long_accuracy'       => 'Number',
      'elevation'               => 'Number',
      'elevation_accuracy'      => 'Number',
      'elevation_unit'          => 'Text',
      'iso3166'                 => 'Text',
      'iso3166_subdivision'     => 'Text',
      'wkt_str'                 => 'Text',
      'ecosystem'               => 'Text',
      'original_coordinates'    => 'Text',
      'elevation_max'           => 'Number',
      'depth_min'               => 'Number',
      'depth_max'               => 'Number',
      'depth_accuracy'          => 'Number',
    );
  }
}
