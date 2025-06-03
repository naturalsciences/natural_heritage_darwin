<?php

/**
 * VGeorefForInterface form base class.
 *
 * @method VGeorefForInterface getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVGeorefForInterfaceForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormTextarea();
    $this->validatorSchema['source'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['manual_offset'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['manual_offset'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['country'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['country_iso3166'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country_iso3166'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['geonames_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['geonames_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['wikidata_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['wikidata_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_wkt'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_wkt'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_url'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_url'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_json'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_json'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_metadata_json'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_metadata_json'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_geo_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_geo_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_class'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_class'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['osm_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['osm_place_rank'] = new sfWidgetFormInputText();
    $this->validatorSchema['osm_place_rank'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['epsg'] = new sfWidgetFormTextarea();
    $this->validatorSchema['epsg'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['wkt_from_geom'] = new sfWidgetFormTextarea();
    $this->validatorSchema['wkt_from_geom'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['srid_from_geom'] = new sfWidgetFormTextarea();
    $this->validatorSchema['srid_from_geom'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['geojson_from_geom'] = new sfWidgetFormTextarea();
    $this->validatorSchema['geojson_from_geom'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('v_georef_for_interface[%s]');
  }

  public function getModelName()
  {
    return 'VGeorefForInterface';
  }

}
