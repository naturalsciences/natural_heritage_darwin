<?php

/**
 * GtuIso3166 form base class.
 *
 * @method GtuIso3166 getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuIso3166Form extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['iso3166_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166_code'] = new sfValidatorString();

    $this->widgetSchema   ['iso3166_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166_name'] = new sfValidatorString();

    $this->widgetSchema   ['iso3166_2_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166_2_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['iso3166_2_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['iso3166_2_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['level2_subdivision_code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['level2_subdivision_code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['level2_subdivision_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['level2_subdivision_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['search_field'] = new sfWidgetFormTextarea();
    $this->validatorSchema['search_field'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('gtu_iso3166[%s]');
  }

  public function getModelName()
  {
    return 'GtuIso3166';
  }

}
