<?php

/**
 * ClassificationSynonymies form base class.
 *
 * @method ClassificationSynonymies getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseClassificationSynonymiesForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'group_id'            => new sfWidgetFormInputText(),
      'group_name'          => new sfWidgetFormTextarea(),
      'is_basionym'         => new sfWidgetFormInputCheckbox(),
      'order_by'            => new sfWidgetFormInputText(),
      'synonym_record_id'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(),
      'group_id'            => new sfValidatorInteger(),
      'group_name'          => new sfValidatorString(),
      'is_basionym'         => new sfValidatorBoolean(array('required' => false)),
      'order_by'            => new sfValidatorInteger(array('required' => false)),
      'synonym_record_id'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('classification_synonymies[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ClassificationSynonymies';
  }

}
