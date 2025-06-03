<?php

/**
 * Georef filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGeorefFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['manual_offset'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['manual_offset'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['country'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country_iso3166'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['country_iso3166'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['geonames_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['geonames_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['wikidata_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['wikidata_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_wkt'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_wkt'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_url'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_url'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_json'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_json'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_metadata_json'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_metadata_json'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_geo_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_geo_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_class'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_class'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['osm_place_rank'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['osm_place_rank'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['epsg'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['epsg'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('georef_filters[%s]');
  }

  public function getModelName()
  {
    return 'Georef';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'source' => 'Text',
      'manual_offset' => 'Boolean',
      'name' => 'Text',
      'country' => 'Text',
      'country_iso3166' => 'Text',
      'osm_id' => 'Text',
      'geonames_id' => 'Text',
      'wikidata_id' => 'Text',
      'osm_wkt' => 'Text',
      'osm_url' => 'Text',
      'osm_json' => 'Text',
      'osm_metadata_json' => 'Text',
      'osm_geo_type' => 'Text',
      'osm_class' => 'Text',
      'osm_type' => 'Text',
      'osm_place_rank' => 'Number',
      'epsg' => 'Text',
      'creation_date' => 'Text',
    ));
  }
}
