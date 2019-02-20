<?php

/**
 * ZzzFranckAssociateFileToRecordMarch2018 form base class.
 *
 * @method ZzzFranckAssociateFileToRecordMarch2018 getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseZzzFranckAssociateFileToRecordMarch2018Form extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'filename'        => new sfWidgetFormTextarea(),
      'date_modified'   => new sfWidgetFormTextarea(),
      'scientific_name' => new sfWidgetFormTextarea(),
      'unitid'          => new sfWidgetFormTextarea(),
      'kindofunit'      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'filename'        => new sfValidatorString(array('required' => false)),
      'date_modified'   => new sfValidatorString(array('required' => false)),
      'scientific_name' => new sfValidatorString(array('required' => false)),
      'unitid'          => new sfValidatorString(array('required' => false)),
      'kindofunit'      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_franck_associate_file_to_record_march2018[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzFranckAssociateFileToRecordMarch2018';
  }

}
