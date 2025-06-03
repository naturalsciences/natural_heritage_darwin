<?php

/**
 * GtuCountry form base class.
 *
 * @method GtuCountry getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuCountryForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['iso3166'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166'] = new sfValidatorString();

    $this->widgetSchema   ['name_en'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_en'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_name_1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_name_1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_name_2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_name_2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_name_3'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_name_3'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_3'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_3'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_name_4'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_name_4'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['name_4'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_4'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_historical_name_1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['historical_name_1'] = new sfWidgetFormTextarea();
    $this->validatorSchema['historical_name_1'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_historical_name_2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['historical_name_2'] = new sfWidgetFormTextarea();
    $this->validatorSchema['historical_name_2'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_3'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_historical_name_3'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['historical_name_3'] = new sfWidgetFormTextarea();
    $this->validatorSchema['historical_name_3'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_4'] = new sfWidgetFormTextarea();
    $this->validatorSchema['lang_historical_name_4'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['historical_name_4'] = new sfWidgetFormTextarea();
    $this->validatorSchema['historical_name_4'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['comments'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comments'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['last_update_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['last_update_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('gtu_country[%s]');
  }

  public function getModelName()
  {
    return 'GtuCountry';
  }

}
