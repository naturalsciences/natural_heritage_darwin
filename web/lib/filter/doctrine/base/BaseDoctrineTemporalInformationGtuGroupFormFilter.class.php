<?php

/**
 * DoctrineTemporalInformationGtuGroup filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDoctrineTemporalInformationGtuGroupFormFilter extends BaseFormFilterDoctrine
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
      'location'                => new sfWidgetFormFilterInput(),
      'lat_long_accuracy'       => new sfWidgetFormFilterInput(),
      'elevation'               => new sfWidgetFormFilterInput(),
      'elevation_accuracy'      => new sfWidgetFormFilterInput(),
      'import_ref'              => new sfWidgetFormFilterInput(),
      'collector_refs'          => new sfWidgetFormFilterInput(),
      'expedition_refs'         => new sfWidgetFormFilterInput(),
      'collection_ref'          => new sfWidgetFormFilterInput(),
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
      'array_from_date_mask'    => new sfWidgetFormFilterInput(),
      'array_from_date'         => new sfWidgetFormFilterInput(),
      'array_to_date_mask'      => new sfWidgetFormFilterInput(),
      'array_to_date'           => new sfWidgetFormFilterInput(),
      'comments'                => new sfWidgetFormFilterInput(),
      'properties'              => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'code'                    => new sfValidatorPass(array('required' => false)),
      'gtu_from_date_mask'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_from_date'           => new sfValidatorPass(array('required' => false)),
      'gtu_to_date_mask'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'gtu_to_date'             => new sfValidatorPass(array('required' => false)),
      'latitude'                => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'longitude'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'location'                => new sfValidatorPass(array('required' => false)),
      'lat_long_accuracy'       => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation'               => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'elevation_accuracy'      => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'import_ref'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'collector_refs'          => new sfValidatorPass(array('required' => false)),
      'expedition_refs'         => new sfValidatorPass(array('required' => false)),
      'collection_ref'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'array_from_date_mask'    => new sfValidatorPass(array('required' => false)),
      'array_from_date'         => new sfValidatorPass(array('required' => false)),
      'array_to_date_mask'      => new sfValidatorPass(array('required' => false)),
      'array_to_date'           => new sfValidatorPass(array('required' => false)),
      'comments'                => new sfValidatorPass(array('required' => false)),
      'properties'              => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('doctrine_temporal_information_gtu_group_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DoctrineTemporalInformationGtuGroup';
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
      'location'                => 'Text',
      'lat_long_accuracy'       => 'Number',
      'elevation'               => 'Number',
      'elevation_accuracy'      => 'Number',
      'import_ref'              => 'Number',
      'collector_refs'          => 'Text',
      'expedition_refs'         => 'Text',
      'collection_ref'          => 'Number',
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
      'array_from_date_mask'    => 'Text',
      'array_from_date'         => 'Text',
      'array_to_date_mask'      => 'Text',
      'array_to_date'           => 'Text',
      'comments'                => 'Text',
      'properties'              => 'Text',
    );
  }
}
