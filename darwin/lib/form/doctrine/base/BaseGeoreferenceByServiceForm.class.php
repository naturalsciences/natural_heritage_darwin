<?php

/**
 * GeoreferenceByService form base class.
 *
 * @method GeoreferenceByService getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGeoreferenceByServiceForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['data_origin'] = new sfWidgetFormTextarea();
    $this->validatorSchema['data_origin'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['root_urls_service'] = new sfWidgetFormTextarea();
    $this->validatorSchema['root_urls_service'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['data_urls'] = new sfWidgetFormTextarea();
    $this->validatorSchema['data_urls'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['service_ids'] = new sfWidgetFormTextarea();
    $this->validatorSchema['service_ids'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['service_responses'] = new sfWidgetFormTextarea();
    $this->validatorSchema['service_responses'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['response_names'] = new sfWidgetFormTextarea();
    $this->validatorSchema['response_names'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['service_categories'] = new sfWidgetFormTextarea();
    $this->validatorSchema['service_categories'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['service_queries'] = new sfWidgetFormTextarea();
    $this->validatorSchema['service_queries'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['count_specimens'] = new sfWidgetFormInputText();
    $this->validatorSchema['count_specimens'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['country'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['country_iso'] = new sfWidgetFormTextarea();
    $this->validatorSchema['country_iso'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['longitude'] = new sfWidgetFormTextarea();
    $this->validatorSchema['longitude'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['latitude'] = new sfWidgetFormTextarea();
    $this->validatorSchema['latitude'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['geom_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['geom_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['geom_wkt'] = new sfWidgetFormTextarea();
    $this->validatorSchema['geom_wkt'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['date_query'] = new sfWidgetFormTextarea();
    $this->validatorSchema['date_query'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['wfs_url'] = new sfWidgetFormTextarea();
    $this->validatorSchema['wfs_url'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['wfs_table'] = new sfWidgetFormTextarea();
    $this->validatorSchema['wfs_table'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['wfs_id'] = new sfWidgetFormTextarea();
    $this->validatorSchema['wfs_id'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('georeference_by_service[%s]');
  }

  public function getModelName()
  {
    return 'GeoreferenceByService';
  }

}
