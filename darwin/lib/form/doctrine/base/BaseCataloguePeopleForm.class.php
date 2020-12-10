<?php

/**
 * CataloguePeople form base class.
 *
 * @method CataloguePeople getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCataloguePeopleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'), 'add_empty' => false)),
      'people_type'         => new sfWidgetFormTextarea(),
      'people_sub_type'     => new sfWidgetFormTextarea(),
      'order_by'            => new sfWidgetFormInputText(),
      'people_ref'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Bibliography'))),
      'people_type'         => new sfValidatorString(array('required' => false)),
      'people_sub_type'     => new sfValidatorString(),
      'order_by'            => new sfValidatorInteger(array('required' => false)),
      'people_ref'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'))),
    ));

    $this->widgetSchema->setNameFormat('catalogue_people[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CataloguePeople';
  }

}
