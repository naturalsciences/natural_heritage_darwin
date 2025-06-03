<?php

/**
 * GtuToCountry form base class.
 *
 * @method GtuToCountry getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuToCountryForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['country_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'add_empty' => false));
    $this->validatorSchema['country_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'column' => 'id'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['country_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'add_empty' => false));
    $this->validatorSchema['country_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('GtuCountry'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('gtu_to_country[%s]');
  }

  public function getModelName()
  {
    return 'GtuToCountry';
  }

}
