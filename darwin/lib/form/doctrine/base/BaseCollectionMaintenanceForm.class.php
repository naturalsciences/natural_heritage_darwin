<?php

/**
 * CollectionMaintenance form base class.
 *
 * @method CollectionMaintenance getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCollectionMaintenanceForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['action_observation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['action_observation'] = new sfValidatorString();

    $this->widgetSchema   ['description'] = new sfWidgetFormTextarea();
    $this->validatorSchema['description'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['description_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['description_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormTextarea();
    $this->validatorSchema['modification_date_time'] = new sfValidatorString();

    $this->widgetSchema   ['modification_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['modification_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['people_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false));
    $this->validatorSchema['people_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('collection_maintenance[%s]');
  }

  public function getModelName()
  {
    return 'CollectionMaintenance';
  }

}
