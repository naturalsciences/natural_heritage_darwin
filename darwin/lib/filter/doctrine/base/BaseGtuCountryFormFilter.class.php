<?php

/**
 * GtuCountry filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuCountryFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['iso3166'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['iso3166'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_en'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_en'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_name_1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_name_1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_name_2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_name_2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_name_3'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_name_3'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_3'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_3'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_name_4'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_name_4'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_4'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_4'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_historical_name_1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['historical_name_1'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['historical_name_1'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_historical_name_2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['historical_name_2'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['historical_name_2'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_3'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_historical_name_3'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['historical_name_3'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['historical_name_3'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lang_historical_name_4'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lang_historical_name_4'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['historical_name_4'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['historical_name_4'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['from_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['comments'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['comments'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['last_update_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['last_update_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('gtu_country_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuCountry';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'iso3166' => 'Text',
      'name_en' => 'Text',
      'lang_name_1' => 'Text',
      'name_1' => 'Text',
      'lang_name_2' => 'Text',
      'name_2' => 'Text',
      'lang_name_3' => 'Text',
      'name_3' => 'Text',
      'lang_name_4' => 'Text',
      'name_4' => 'Text',
      'lang_historical_name_1' => 'Text',
      'historical_name_1' => 'Text',
      'lang_historical_name_2' => 'Text',
      'historical_name_2' => 'Text',
      'lang_historical_name_3' => 'Text',
      'historical_name_3' => 'Text',
      'lang_historical_name_4' => 'Text',
      'historical_name_4' => 'Text',
      'from_date_mask' => 'Number',
      'from_date' => 'Text',
      'to_date_mask' => 'Number',
      'to_date' => 'Text',
      'comments' => 'Text',
      'creation_date' => 'Text',
      'last_update_date' => 'Text',
    ));
  }
}
