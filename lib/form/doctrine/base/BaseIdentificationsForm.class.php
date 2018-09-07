<?php

/**
 * Identifications form base class.
 *
 * @method Identifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseIdentificationsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'referenced_relation'   => new sfWidgetFormTextarea(),
      'record_id'             => new sfWidgetFormInputText(),
      'notion_concerned'      => new sfWidgetFormTextarea(),
      'notion_date'           => new sfWidgetFormTextarea(),
      'notion_date_mask'      => new sfWidgetFormInputText(),
      'value_defined'         => new sfWidgetFormTextarea(),
      'value_defined_indexed' => new sfWidgetFormTextarea(),
      'determination_status'  => new sfWidgetFormTextarea(),
      'order_by'              => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation'   => new sfValidatorString(),
      'record_id'             => new sfValidatorInteger(),
      'notion_concerned'      => new sfValidatorString(array('required' => false)),
      'notion_date'           => new sfValidatorString(array('required' => false)),
      'notion_date_mask'      => new sfValidatorInteger(array('required' => false)),
      'value_defined'         => new sfValidatorString(array('required' => false)),
      'value_defined_indexed' => new sfValidatorString(array('required' => false)),
      'determination_status'  => new sfValidatorString(array('required' => false)),
      'order_by'              => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('identifications[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Identifications';
  }

}
