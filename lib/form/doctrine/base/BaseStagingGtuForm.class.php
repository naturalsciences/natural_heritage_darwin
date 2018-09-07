<?php

/**
 * StagingGtu form base class.
 *
 * @method StagingGtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseStagingGtuForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputHidden(),
      'import_ref'                     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Import'), 'add_empty' => false)),
      'status'                         => new sfWidgetFormTextarea(),
      'date_included'                  => new sfWidgetFormInputCheckbox(),
      'tags_merged'                    => new sfWidgetFormInputCheckbox(),
      'sensitive_information_withheld' => new sfWidgetFormInputCheckbox(),
      'gtu_ref'                        => new sfWidgetFormInputText(),
      'station_type'                   => new sfWidgetFormTextarea(),
      'sampling_code'                  => new sfWidgetFormTextarea(),
      'sampling_field_number'          => new sfWidgetFormTextarea(),
      'event_cluster_code'             => new sfWidgetFormTextarea(),
      'event_order'                    => new sfWidgetFormTextarea(),
      'ig_num'                         => new sfWidgetFormTextarea(),
      'ig_num_indexed'                 => new sfWidgetFormTextarea(),
      'collections'                    => new sfWidgetFormTextarea(),
      'collectors'                     => new sfWidgetFormTextarea(),
      'expeditions'                    => new sfWidgetFormTextarea(),
      'collection_refs'                => new sfWidgetFormTextarea(),
      'collector_refs'                 => new sfWidgetFormTextarea(),
      'expedition_refs'                => new sfWidgetFormTextarea(),
      'iso3166'                        => new sfWidgetFormTextarea(),
      'iso3166_subdivision'            => new sfWidgetFormTextarea(),
      'countries'                      => new sfWidgetFormTextarea(),
      'tags'                           => new sfWidgetFormTextarea(),
      'tags_indexed'                   => new sfWidgetFormTextarea(),
      'locality_text'                  => new sfWidgetFormTextarea(),
      'locality_text_indexed'          => new sfWidgetFormTextarea(),
      'ecology_text'                   => new sfWidgetFormTextarea(),
      'ecology_text_indexed'           => new sfWidgetFormTextarea(),
      'coordinates_format'             => new sfWidgetFormTextarea(),
      'latitude1'                      => new sfWidgetFormTextarea(),
      'longitude1'                     => new sfWidgetFormTextarea(),
      'latitude2'                      => new sfWidgetFormTextarea(),
      'longitude2'                     => new sfWidgetFormTextarea(),
      'gis_type'                       => new sfWidgetFormTextarea(),
      'coordinates_wkt'                => new sfWidgetFormTextarea(),
      'coordinates_datum'              => new sfWidgetFormTextarea(),
      'coordinates_proj_ref'           => new sfWidgetFormInputText(),
      'coordinates_original'           => new sfWidgetFormTextarea(),
      'coordinates_accuracy'           => new sfWidgetFormInputText(),
      'coordinates_accuracy_text'      => new sfWidgetFormTextarea(),
      'station_baseline_elevation'     => new sfWidgetFormInputText(),
      'station_baseline_accuracy'      => new sfWidgetFormInputText(),
      'sampling_elevation_start'       => new sfWidgetFormInputText(),
      'sampling_elevation_end'         => new sfWidgetFormInputText(),
      'sampling_elevation_accuracy'    => new sfWidgetFormInputText(),
      'original_elevation_data'        => new sfWidgetFormTextarea(),
      'sampling_depth_start'           => new sfWidgetFormInputText(),
      'sampling_depth_end'             => new sfWidgetFormInputText(),
      'sampling_depth_accuracy'        => new sfWidgetFormInputText(),
      'original_depth_data'            => new sfWidgetFormTextarea(),
      'collecting_date_begin'          => new sfWidgetFormTextarea(),
      'collecting_date_begin_mask'     => new sfWidgetFormTextarea(),
      'collecting_date_end'            => new sfWidgetFormTextarea(),
      'collecting_date_end_mask'       => new sfWidgetFormTextarea(),
      'collecting_time_begin'          => new sfWidgetFormTextarea(),
      'collecting_time_end'            => new sfWidgetFormTextarea(),
      'sampling_method'                => new sfWidgetFormTextarea(),
      'sampling_fixation'              => new sfWidgetFormTextarea(),
      'imported'                       => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'import_ref'                     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Import'))),
      'status'                         => new sfValidatorString(array('required' => false)),
      'date_included'                  => new sfValidatorBoolean(array('required' => false)),
      'tags_merged'                    => new sfValidatorBoolean(array('required' => false)),
      'sensitive_information_withheld' => new sfValidatorBoolean(array('required' => false)),
      'gtu_ref'                        => new sfValidatorInteger(array('required' => false)),
      'station_type'                   => new sfValidatorString(array('required' => false)),
      'sampling_code'                  => new sfValidatorString(),
      'sampling_field_number'          => new sfValidatorString(array('required' => false)),
      'event_cluster_code'             => new sfValidatorString(array('required' => false)),
      'event_order'                    => new sfValidatorString(array('required' => false)),
      'ig_num'                         => new sfValidatorString(array('required' => false)),
      'ig_num_indexed'                 => new sfValidatorString(array('required' => false)),
      'collections'                    => new sfValidatorString(array('required' => false)),
      'collectors'                     => new sfValidatorString(array('required' => false)),
      'expeditions'                    => new sfValidatorString(array('required' => false)),
      'collection_refs'                => new sfValidatorString(array('required' => false)),
      'collector_refs'                 => new sfValidatorString(array('required' => false)),
      'expedition_refs'                => new sfValidatorString(array('required' => false)),
      'iso3166'                        => new sfValidatorString(array('required' => false)),
      'iso3166_subdivision'            => new sfValidatorString(array('required' => false)),
      'countries'                      => new sfValidatorString(array('required' => false)),
      'tags'                           => new sfValidatorString(array('required' => false)),
      'tags_indexed'                   => new sfValidatorString(array('required' => false)),
      'locality_text'                  => new sfValidatorString(array('required' => false)),
      'locality_text_indexed'          => new sfValidatorString(array('required' => false)),
      'ecology_text'                   => new sfValidatorString(array('required' => false)),
      'ecology_text_indexed'           => new sfValidatorString(array('required' => false)),
      'coordinates_format'             => new sfValidatorString(array('required' => false)),
      'latitude1'                      => new sfValidatorString(array('required' => false)),
      'longitude1'                     => new sfValidatorString(array('required' => false)),
      'latitude2'                      => new sfValidatorString(array('required' => false)),
      'longitude2'                     => new sfValidatorString(array('required' => false)),
      'gis_type'                       => new sfValidatorString(array('required' => false)),
      'coordinates_wkt'                => new sfValidatorString(array('required' => false)),
      'coordinates_datum'              => new sfValidatorString(array('required' => false)),
      'coordinates_proj_ref'           => new sfValidatorInteger(array('required' => false)),
      'coordinates_original'           => new sfValidatorString(array('required' => false)),
      'coordinates_accuracy'           => new sfValidatorNumber(array('required' => false)),
      'coordinates_accuracy_text'      => new sfValidatorString(array('required' => false)),
      'station_baseline_elevation'     => new sfValidatorNumber(array('required' => false)),
      'station_baseline_accuracy'      => new sfValidatorNumber(array('required' => false)),
      'sampling_elevation_start'       => new sfValidatorNumber(array('required' => false)),
      'sampling_elevation_end'         => new sfValidatorNumber(array('required' => false)),
      'sampling_elevation_accuracy'    => new sfValidatorNumber(array('required' => false)),
      'original_elevation_data'        => new sfValidatorString(array('required' => false)),
      'sampling_depth_start'           => new sfValidatorNumber(array('required' => false)),
      'sampling_depth_end'             => new sfValidatorNumber(array('required' => false)),
      'sampling_depth_accuracy'        => new sfValidatorNumber(array('required' => false)),
      'original_depth_data'            => new sfValidatorString(array('required' => false)),
      'collecting_date_begin'          => new sfValidatorString(array('required' => false)),
      'collecting_date_begin_mask'     => new sfValidatorString(array('required' => false)),
      'collecting_date_end'            => new sfValidatorString(array('required' => false)),
      'collecting_date_end_mask'       => new sfValidatorString(array('required' => false)),
      'collecting_time_begin'          => new sfValidatorString(array('required' => false)),
      'collecting_time_end'            => new sfValidatorString(array('required' => false)),
      'sampling_method'                => new sfValidatorString(array('required' => false)),
      'sampling_fixation'              => new sfValidatorString(array('required' => false)),
      'imported'                       => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('staging_gtu[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'StagingGtu';
  }

}
