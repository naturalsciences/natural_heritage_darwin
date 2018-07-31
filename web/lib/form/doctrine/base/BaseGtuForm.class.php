<?php

/**
 * Gtu form base class.
 *
 * @method Gtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseGtuForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'code'                    => new sfWidgetFormTextarea(),
      'gtu_from_date_mask'      => new sfWidgetFormInputText(),
      'gtu_from_date'           => new sfWidgetFormTextarea(),
      'gtu_to_date_mask'        => new sfWidgetFormInputText(),
      'gtu_to_date'             => new sfWidgetFormTextarea(),
      'latitude'                => new sfWidgetFormInputText(),
      'longitude'               => new sfWidgetFormInputText(),
      'coordinates_source'      => new sfWidgetFormTextarea(),
      'latitude_dms_degree'     => new sfWidgetFormInputText(),
      'latitude_dms_minutes'    => new sfWidgetFormInputText(),
      'latitude_dms_seconds'    => new sfWidgetFormInputText(),
      'latitude_dms_direction'  => new sfWidgetFormInputText(),
      'longitude_dms_degree'    => new sfWidgetFormInputText(),
      'longitude_dms_minutes'   => new sfWidgetFormInputText(),
      'longitude_dms_seconds'   => new sfWidgetFormInputText(),
      'longitude_dms_direction' => new sfWidgetFormInputText(),
      'latitude_utm'            => new sfWidgetFormInputText(),
      'longitude_utm'           => new sfWidgetFormInputText(),
      'utm_zone'                => new sfWidgetFormInputText(),
      'location'                => new sfWidgetFormTextarea(),
      'lat_long_accuracy'       => new sfWidgetFormInputText(),
      'elevation'               => new sfWidgetFormInputText(),
      'elevation_accuracy'      => new sfWidgetFormInputText(),
      'elevation_unit'          => new sfWidgetFormTextarea(),
      'iso3166'                 => new sfWidgetFormTextarea(),
      'iso3166_subdivision'     => new sfWidgetFormTextarea(),
      'wkt_str'                 => new sfWidgetFormTextarea(),
      'ecosystem'               => new sfWidgetFormTextarea(),
      'original_coordinates'    => new sfWidgetFormTextarea(),
      'elevation_max'           => new sfWidgetFormInputText(),
      'depth_min'               => new sfWidgetFormInputText(),
      'depth_max'               => new sfWidgetFormInputText(),
      'depth_accuracy'          => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'code'                    => new sfValidatorString(),
      'gtu_from_date_mask'      => new sfValidatorInteger(array('required' => false)),
      'gtu_from_date'           => new sfValidatorString(array('required' => false)),
      'gtu_to_date_mask'        => new sfValidatorInteger(array('required' => false)),
      'gtu_to_date'             => new sfValidatorString(array('required' => false)),
      'latitude'                => new sfValidatorNumber(array('required' => false)),
      'longitude'               => new sfValidatorNumber(array('required' => false)),
      'coordinates_source'      => new sfValidatorString(array('required' => false)),
      'latitude_dms_degree'     => new sfValidatorInteger(array('required' => false)),
      'latitude_dms_minutes'    => new sfValidatorNumber(array('required' => false)),
      'latitude_dms_seconds'    => new sfValidatorNumber(array('required' => false)),
      'latitude_dms_direction'  => new sfValidatorInteger(array('required' => false)),
      'longitude_dms_degree'    => new sfValidatorInteger(array('required' => false)),
      'longitude_dms_minutes'   => new sfValidatorNumber(array('required' => false)),
      'longitude_dms_seconds'   => new sfValidatorNumber(array('required' => false)),
      'longitude_dms_direction' => new sfValidatorInteger(array('required' => false)),
      'latitude_utm'            => new sfValidatorNumber(array('required' => false)),
      'longitude_utm'           => new sfValidatorNumber(array('required' => false)),
      'utm_zone'                => new sfValidatorPass(array('required' => false)),
      'location'                => new sfValidatorString(array('required' => false)),
      'lat_long_accuracy'       => new sfValidatorNumber(array('required' => false)),
      'elevation'               => new sfValidatorNumber(array('required' => false)),
      'elevation_accuracy'      => new sfValidatorNumber(array('required' => false)),
      'elevation_unit'          => new sfValidatorString(array('required' => false)),
      'iso3166'                 => new sfValidatorString(array('required' => false)),
      'iso3166_subdivision'     => new sfValidatorString(array('required' => false)),
      'wkt_str'                 => new sfValidatorString(array('required' => false)),
      'ecosystem'               => new sfValidatorString(array('required' => false)),
      'original_coordinates'    => new sfValidatorString(array('required' => false)),
      'elevation_max'           => new sfValidatorNumber(array('required' => false)),
      'depth_min'               => new sfValidatorNumber(array('required' => false)),
      'depth_max'               => new sfValidatorNumber(array('required' => false)),
      'depth_accuracy'          => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('gtu[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Gtu';
  }

}
