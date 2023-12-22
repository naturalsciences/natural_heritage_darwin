<?php

/**
 * GtuIso3166 filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuIso3166FormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['iso3166_code'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['iso3166_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['iso3166_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166_2_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166_2_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['iso3166_2_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['iso3166_2_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level2_subdivision_code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level2_subdivision_code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['level2_subdivision_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['level2_subdivision_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['search_field'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['search_field'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('gtu_iso3166_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuIso3166';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'iso3166_code' => 'Text',
      'iso3166_name' => 'Text',
      'iso3166_2_code' => 'Text',
      'iso3166_2_name' => 'Text',
      'level2_subdivision_code' => 'Text',
      'level2_subdivision_name' => 'Text',
      'search_field' => 'Text',
    ));
  }
}
