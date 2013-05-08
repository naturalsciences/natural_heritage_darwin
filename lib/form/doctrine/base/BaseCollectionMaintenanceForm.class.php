<?php

/**
 * CollectionMaintenance form base class.
 *
 * @method CollectionMaintenance getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCollectionMaintenanceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'record_id'              => new sfWidgetFormInputText(),
      'referenced_relation'    => new sfWidgetFormTextarea(),
      'people_ref'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false)),
      'category'               => new sfWidgetFormTextarea(),
      'action_observation'     => new sfWidgetFormTextarea(),
      'description'            => new sfWidgetFormTextarea(),
      'description_indexed'    => new sfWidgetFormTextarea(),
      'modification_date_time' => new sfWidgetFormTextarea(),
      'modification_date_mask' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'record_id'              => new sfValidatorInteger(),
      'referenced_relation'    => new sfValidatorString(),
      'people_ref'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'))),
      'category'               => new sfValidatorString(array('required' => false)),
      'action_observation'     => new sfValidatorString(),
      'description'            => new sfValidatorString(array('required' => false)),
      'description_indexed'    => new sfValidatorString(array('required' => false)),
      'modification_date_time' => new sfValidatorString(),
      'modification_date_mask' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('collection_maintenance[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CollectionMaintenance';
  }

}
