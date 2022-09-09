<?php

/**
 * StagingGtu form base class.
 *
 * @method StagingGtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseStagingGtuForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema   ['pos_in_file'] = new sfWidgetFormInputText();
    $this->validatorSchema['pos_in_file'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_included'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['date_included'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['tags_merged'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['tags_merged'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['sensitive_information_withheld'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['sensitive_information_withheld'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['station_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['station_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sampling_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sampling_code'] = new sfValidatorString();

    $this->widgetSchema   ['sampling_field_number'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sampling_field_number'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['event_cluster_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['event_cluster_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['event_order'] = new sfWidgetFormTextarea();
    $this->validatorSchema['event_order'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collections'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collections'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collectors'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collectors'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expeditions'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expeditions'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collection_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collector_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collector_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['iso3166'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['iso3166_subdivision'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166_subdivision'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['countries'] = new sfWidgetFormTextarea();
    $this->validatorSchema['countries'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['tags'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['tags_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tags_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['locality_text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['locality_text'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['locality_text_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['locality_text_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ecology_text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ecology_text'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ecology_text_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ecology_text_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coordinates_format'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_format'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['latitude1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['latitude1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['longitude1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['longitude1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['latitude2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['latitude2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['longitude2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['longitude2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gis_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gis_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coordinates_wkt'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_wkt'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coordinates_datum'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_datum'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coordinates_proj_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['coordinates_proj_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['coordinates_original'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_original'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coordinates_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['coordinates_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['coordinates_accuracy_text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_accuracy_text'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['station_baseline_elevation'] = new sfWidgetFormInputText();
    $this->validatorSchema['station_baseline_elevation'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['station_baseline_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['station_baseline_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['sampling_elevation_start'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_elevation_start'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['sampling_elevation_end'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_elevation_end'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['sampling_elevation_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_elevation_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['original_elevation_data'] = new sfWidgetFormTextarea();
    $this->validatorSchema['original_elevation_data'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sampling_depth_start'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_depth_start'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['sampling_depth_end'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_depth_end'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['sampling_depth_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['sampling_depth_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['original_depth_data'] = new sfWidgetFormTextarea();
    $this->validatorSchema['original_depth_data'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_date_begin'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_date_begin'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_date_begin_mask'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_date_begin_mask'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_date_end'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_date_end'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_date_end_mask'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_date_end_mask'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_time_begin'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_time_begin'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collecting_time_end'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collecting_time_end'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sampling_method'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sampling_method'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sampling_fixation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sampling_fixation'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['imported'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['imported'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['import_exception'] = new sfWidgetFormTextarea();
    $this->validatorSchema['import_exception'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false));
    $this->validatorSchema['import_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('staging_gtu[%s]');
  }

  public function getModelName()
  {
    return 'StagingGtu';
  }

}
