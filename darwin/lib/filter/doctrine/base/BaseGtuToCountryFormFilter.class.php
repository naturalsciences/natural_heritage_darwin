<?php

/**
 * GtuToCountry filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuToCountryFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['country_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'add_empty' => true));
    $this->validatorSchema['country_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('GtuCountry'), 'column' => 'id'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['country_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'add_empty' => true));
    $this->validatorSchema['country_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('GtuCountry'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('gtu_to_country_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuToCountry';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'gtu_ref' => 'ForeignKey',
      'country_ref' => 'ForeignKey',
      'gtu_ref' => 'ForeignKey',
      'country_ref' => 'ForeignKey',
    ));
  }
}
