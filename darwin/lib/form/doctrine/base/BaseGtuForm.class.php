<?php

/**
 * Gtu form base class.
 *
 * @method Gtu getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code'] = new sfValidatorString();

    $this->widgetSchema   ['gtu_from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['gtu_from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gtu_to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['gtu_to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['gtu_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['gtu_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['latitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['longitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['location'] = new sfWidgetFormTextarea();
    $this->validatorSchema['location'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lat_long_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['lat_long_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['elevation'] = new sfWidgetFormInputText();
    $this->validatorSchema['elevation'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['elevation_accuracy'] = new sfWidgetFormInputText();
    $this->validatorSchema['elevation_accuracy'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['import_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['import_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['collector_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['collector_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_refs'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_refs'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['collection_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['coordinates_source'] = new sfWidgetFormTextarea();
    $this->validatorSchema['coordinates_source'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['latitude_dms_degree'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude_dms_degree'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['latitude_dms_minutes'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude_dms_minutes'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['latitude_dms_seconds'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude_dms_seconds'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['latitude_dms_direction'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude_dms_direction'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['longitude_dms_degree'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude_dms_degree'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['longitude_dms_minutes'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude_dms_minutes'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['longitude_dms_seconds'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude_dms_seconds'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['longitude_dms_direction'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude_dms_direction'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['latitude_utm'] = new sfWidgetFormInputText();
    $this->validatorSchema['latitude_utm'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['longitude_utm'] = new sfWidgetFormInputText();
    $this->validatorSchema['longitude_utm'] = new sfValidatorNumber(array('required' => false));

    $this->widgetSchema   ['utm_zone'] = new sfWidgetFormInputText();
    $this->validatorSchema['utm_zone'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['nagoya'] = new sfWidgetFormTextarea();
    $this->validatorSchema['nagoya'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema->setNameFormat('gtu[%s]');
  }

  public function getModelName()
  {
    return 'Gtu';
  }

}
