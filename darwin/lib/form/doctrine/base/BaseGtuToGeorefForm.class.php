<?php

/**
 * GtuToGeoref form base class.
 *
 * @method GtuToGeoref getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseGtuToGeorefForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['georef_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'add_empty' => false));
    $this->validatorSchema['georef_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'column' => 'id'));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => false));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id'));

    $this->widgetSchema   ['georef_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'add_empty' => false));
    $this->validatorSchema['georef_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Georef'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('gtu_to_georef[%s]');
  }

  public function getModelName()
  {
    return 'GtuToGeoref';
  }

}
