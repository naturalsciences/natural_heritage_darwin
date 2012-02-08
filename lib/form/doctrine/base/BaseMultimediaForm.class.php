<?php

/**
 * Multimedia form base class.
 *
 * @method Multimedia getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMultimediaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'referenced_relation' => new sfWidgetFormTextarea(),
      'record_id'           => new sfWidgetFormInputText(),
      'is_digital'          => new sfWidgetFormInputCheckbox(),
      'type'                => new sfWidgetFormTextarea(),
      'sub_type'            => new sfWidgetFormTextarea(),
      'title'               => new sfWidgetFormTextarea(),
      'description'         => new sfWidgetFormTextarea(),
      'uri'                 => new sfWidgetFormTextarea(),
      'filename'            => new sfWidgetFormInputText(),
      'search_ts'           => new sfWidgetFormTextarea(),
      'creation_date'       => new sfWidgetFormTextarea(),
      'creation_date_mask'  => new sfWidgetFormInputText(),
      'mime_type'           => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'referenced_relation' => new sfValidatorString(),
      'record_id'           => new sfValidatorInteger(array('required' => false)),
      'is_digital'          => new sfValidatorBoolean(array('required' => false)),
      'type'                => new sfValidatorString(array('required' => false)),
      'sub_type'            => new sfValidatorString(array('required' => false)),
      'title'               => new sfValidatorString(),
      'description'         => new sfValidatorString(array('required' => false)),
      'uri'                 => new sfValidatorString(array('required' => false)),
      'filename'            => new sfValidatorPass(array('required' => false)),
      'search_ts'           => new sfValidatorString(array('required' => false)),
      'creation_date'       => new sfValidatorString(array('required' => false)),
      'creation_date_mask'  => new sfValidatorInteger(array('required' => false)),
      'mime_type'           => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('multimedia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Multimedia';
  }

}
