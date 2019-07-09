<?php

/**
 * DoctrineTemporalInformationGtuGroupUnnestTags filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineTemporalInformationGtuGroupUnnestTagsFormFilter extends DarwinModelFormFilter
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

    $this->widgetSchema   ['location'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['location'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lat_long_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lat_long_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['elevation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['elevation'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['elevation_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['elevation_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['import_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['collector_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collector_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

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

    $this->widgetSchema   ['from_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comments'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comments'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['properties'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['properties'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tag'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tag_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('doctrine_temporal_information_gtu_group_unnest_tags_filters[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineTemporalInformationGtuGroupUnnestTags';
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
      'location' => 'Text',
      'lat_long_accuracy' => 'Number',
      'elevation' => 'Number',
      'elevation_accuracy' => 'Number',
      'import_ref' => 'Number',
      'collector_refs' => 'Text',
      'expedition_refs' => 'Text',
      'collection_ref' => 'Number',
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
      'from_date_mask' => 'Number',
      'from_date' => 'Text',
      'to_date_mask' => 'Number',
      'to_date' => 'Text',
      'comments' => 'Text',
      'properties' => 'Text',
      'tag' => 'Text',
      'tag_indexed' => 'Text',
    ));
  }
}
