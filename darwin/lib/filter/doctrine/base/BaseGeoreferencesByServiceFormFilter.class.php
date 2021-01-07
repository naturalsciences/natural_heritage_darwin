<?php

/**
 * GeoreferencesByService filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGeoreferencesByServiceFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['data_origin'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['data_origin'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gtu_refs'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gtu_refs'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['count_specimens'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['count_specimens'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['wfs_url'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['wfs_url'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['wfs_table'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['wfs_table'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['wfs_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['wfs_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['root_urls_service'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['root_urls_service'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['data_urls'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['data_urls'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['service_ids'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['service_ids'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['service_stable_ids'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['service_stable_ids'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['service_responses'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['service_responses'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['response_names'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['response_names'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['service_categories'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['service_categories'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['service_queries'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['service_queries'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_group_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tag_group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag_sub_group_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['tag_sub_group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['country'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['country_iso'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['country_iso'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['longitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['longitude'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['latitude'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['latitude'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['geom_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['geom_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['geom_wkt'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['geom_wkt'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['query_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['query_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['creator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['validation_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['validation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['validator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['validator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['validation_level'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['validation_level'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['validation_comment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['validation_comment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('georeferences_by_service_filters[%s]');
  }

  public function getModelName()
  {
    return 'GeoreferencesByService';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'data_origin' => 'Text',
      'gtu_refs' => 'Text',
      'count_specimens' => 'Number',
      'wfs_url' => 'Text',
      'wfs_table' => 'Text',
      'wfs_id' => 'Text',
      'root_urls_service' => 'Text',
      'data_urls' => 'Text',
      'service_ids' => 'Text',
      'service_stable_ids' => 'Text',
      'service_responses' => 'Text',
      'response_names' => 'Text',
      'service_categories' => 'Text',
      'service_queries' => 'Text',
      'tag_group_name' => 'Text',
      'tag_sub_group_name' => 'Text',
      'name' => 'Text',
      'country' => 'Text',
      'country_iso' => 'Text',
      'longitude' => 'Text',
      'latitude' => 'Text',
      'geom_type' => 'Text',
      'geom_wkt' => 'Text',
      'query_date' => 'Text',
      'creator' => 'Text',
      'validation_date' => 'Text',
      'validator' => 'Text',
      'validation_level' => 'Text',
      'validation_comment' => 'Text',
    ));
  }
}
