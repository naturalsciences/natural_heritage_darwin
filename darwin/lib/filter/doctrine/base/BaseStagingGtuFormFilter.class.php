<?php

/**
 * StagingGtu filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingGtuFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['pos_in_file'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['pos_in_file'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date_included'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['date_included'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['tags_merged'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['tags_merged'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['sensitive_information_withheld'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['sensitive_information_withheld'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['station_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['station_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sampling_code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['sampling_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sampling_field_number'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_field_number'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['event_cluster_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['event_cluster_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['event_order'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['event_order'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collections'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collections'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collectors'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collectors'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expeditions'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expeditions'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collection_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collection_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collector_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collector_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expedition_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expedition_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166_subdivision'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166_subdivision'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['countries'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['countries'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tags'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tags'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tags_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tags_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['locality_text'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['locality_text'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['locality_text_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['locality_text_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ecology_text'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ecology_text'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ecology_text_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ecology_text_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coordinates_format'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_format'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['latitude1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['longitude1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['latitude2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['longitude2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gis_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gis_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coordinates_wkt'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_wkt'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coordinates_datum'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_datum'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coordinates_proj_ref'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_proj_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['coordinates_original'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_original'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['coordinates_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['coordinates_accuracy_text'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['coordinates_accuracy_text'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['station_baseline_elevation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['station_baseline_elevation'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['station_baseline_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['station_baseline_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['sampling_elevation_start'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_elevation_start'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['sampling_elevation_end'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_elevation_end'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['sampling_elevation_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_elevation_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['original_elevation_data'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['original_elevation_data'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sampling_depth_start'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_depth_start'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['sampling_depth_end'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_depth_end'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['sampling_depth_accuracy'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_depth_accuracy'] = new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false)));

    $this->widgetSchema   ['original_depth_data'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['original_depth_data'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_date_begin'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_date_begin'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_date_begin_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_date_begin_mask'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_date_end'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_date_end'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_date_end_mask'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_date_end_mask'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_time_begin'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_time_begin'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['collecting_time_end'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['collecting_time_end'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sampling_method'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_method'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sampling_fixation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sampling_fixation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['imported'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['import_exception'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['import_exception'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => true));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_gtu_filters[%s]');
  }

  public function getModelName()
  {
    return 'StagingGtu';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'import_ref' => 'ForeignKey',
      'pos_in_file' => 'Number',
      'status' => 'Text',
      'date_included' => 'Boolean',
      'tags_merged' => 'Boolean',
      'sensitive_information_withheld' => 'Boolean',
      'gtu_ref' => 'Number',
      'station_type' => 'Text',
      'sampling_code' => 'Text',
      'sampling_field_number' => 'Text',
      'event_cluster_code' => 'Text',
      'event_order' => 'Text',
      'ig_num' => 'Text',
      'ig_num_indexed' => 'Text',
      'collections' => 'Text',
      'collectors' => 'Text',
      'expeditions' => 'Text',
      'collection_refs' => 'Text',
      'collector_refs' => 'Text',
      'expedition_refs' => 'Text',
      'iso3166' => 'Text',
      'iso3166_subdivision' => 'Text',
      'countries' => 'Text',
      'tags' => 'Text',
      'tags_indexed' => 'Text',
      'locality_text' => 'Text',
      'locality_text_indexed' => 'Text',
      'ecology_text' => 'Text',
      'ecology_text_indexed' => 'Text',
      'coordinates_format' => 'Text',
      'latitude1' => 'Text',
      'longitude1' => 'Text',
      'latitude2' => 'Text',
      'longitude2' => 'Text',
      'gis_type' => 'Text',
      'coordinates_wkt' => 'Text',
      'coordinates_datum' => 'Text',
      'coordinates_proj_ref' => 'Number',
      'coordinates_original' => 'Text',
      'coordinates_accuracy' => 'Number',
      'coordinates_accuracy_text' => 'Text',
      'station_baseline_elevation' => 'Number',
      'station_baseline_accuracy' => 'Number',
      'sampling_elevation_start' => 'Number',
      'sampling_elevation_end' => 'Number',
      'sampling_elevation_accuracy' => 'Number',
      'original_elevation_data' => 'Text',
      'sampling_depth_start' => 'Number',
      'sampling_depth_end' => 'Number',
      'sampling_depth_accuracy' => 'Number',
      'original_depth_data' => 'Text',
      'collecting_date_begin' => 'Text',
      'collecting_date_begin_mask' => 'Text',
      'collecting_date_end' => 'Text',
      'collecting_date_end_mask' => 'Text',
      'collecting_time_begin' => 'Text',
      'collecting_time_end' => 'Text',
      'sampling_method' => 'Text',
      'sampling_fixation' => 'Text',
      'imported' => 'Boolean',
      'import_exception' => 'Text',
      'import_ref' => 'ForeignKey',
    ));
  }
}
