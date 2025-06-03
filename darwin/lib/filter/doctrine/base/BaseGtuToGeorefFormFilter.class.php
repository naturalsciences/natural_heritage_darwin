<?php

/**
 * GtuToGeoref filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuToGeorefFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['georef_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'add_empty' => true));
    $this->validatorSchema['georef_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Georef'), 'column' => 'id'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['georef_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'add_empty' => true));
    $this->validatorSchema['georef_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Georef'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('gtu_to_georef_filters[%s]');
  }

  public function getModelName()
  {
    return 'GtuToGeoref';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'gtu_ref' => 'ForeignKey',
      'georef_ref' => 'ForeignKey',
      'gtu_ref' => 'ForeignKey',
      'georef_ref' => 'ForeignKey',
    ));
  }
}
