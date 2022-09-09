<?php

/**
 * Gtu filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_from_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['gtu_from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['gtu_from_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['gtu_from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_to_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['gtu_to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['gtu_to_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['gtu_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['latitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['longitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['coordinates_source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['latitude_dms_degree'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude_dms_degree'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['latitude_dms_minutes'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude_dms_minutes'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['latitude_dms_seconds'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude_dms_seconds'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['latitude_dms_direction'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude_dms_direction'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['longitude_dms_degree'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude_dms_degree'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['longitude_dms_minutes'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude_dms_minutes'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['longitude_dms_seconds'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude_dms_seconds'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['longitude_dms_direction'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude_dms_direction'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['latitude_utm'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude_utm'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['longitude_utm'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude_utm'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['utm_zone'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['utm_zone'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['location'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['location'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lat_long_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lat_long_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['elevation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['elevation'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['elevation_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['elevation_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['elevation_unit'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['elevation_unit'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166_subdivision'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166_subdivision'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['wkt_str'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['wkt_str'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['nagoya'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collector_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collector_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id'));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Expeditions'), 'add_empty' => true));
    $this->validatorSchema['expedition_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Expeditions'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('gtu_filters[%s]');
  }

  public function getModelName()
  {
    return 'Gtu';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'code' => 'Text',
      'gtu_from_date_mask' => 'Number',
      'gtu_from_date' => 'Text',
      'gtu_to_date_mask' => 'Number',
      'gtu_to_date' => 'Text',
      'latitude' => 'Number',
      'longitude' => 'Number',
      'coordinates_source' => 'Text',
      'latitude_dms_degree' => 'Number',
      'latitude_dms_minutes' => 'Number',
      'latitude_dms_seconds' => 'Number',
      'latitude_dms_direction' => 'Number',
      'longitude_dms_degree' => 'Number',
      'longitude_dms_minutes' => 'Number',
      'longitude_dms_seconds' => 'Number',
      'longitude_dms_direction' => 'Number',
      'latitude_utm' => 'Number',
      'longitude_utm' => 'Number',
      'utm_zone' => 'Text',
      'location' => 'Text',
      'lat_long_accuracy' => 'Number',
      'elevation' => 'Number',
      'elevation_accuracy' => 'Number',
      'elevation_unit' => 'Text',
      'iso3166' => 'Text',
      'iso3166_subdivision' => 'Text',
      'wkt_str' => 'Text',
      'nagoya' => 'Text',
      'collector_refs' => 'Text',
      'expedition_refs' => 'Text',
      'collection_ref' => 'Number',
      'expedition_ref' => 'ForeignKey',
      'expedition_ref' => 'ForeignKey',
    ));
  }
}
