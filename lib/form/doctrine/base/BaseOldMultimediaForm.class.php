<?php

/**
 * OldMultimedia form base class.
 *
 * @method OldMultimedia getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseOldMultimediaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputText(),
      'is_digital'                     => new sfWidgetFormInputCheckbox(),
      'type'                           => new sfWidgetFormTextarea(),
      'sub_type'                       => new sfWidgetFormTextarea(),
      'title'                          => new sfWidgetFormTextarea(),
      'title_indexed'                  => new sfWidgetFormTextarea(),
      'subject'                        => new sfWidgetFormTextarea(),
      'coverage'                       => new sfWidgetFormInputText(),
      'apercu_path'                    => new sfWidgetFormTextarea(),
      'copyright'                      => new sfWidgetFormTextarea(),
      'license'                        => new sfWidgetFormTextarea(),
      'uri'                            => new sfWidgetFormTextarea(),
      'descriptive_ts'                 => new sfWidgetFormTextarea(),
      'descriptive_language_full_text' => new sfWidgetFormTextarea(),
      'creation_date'                  => new sfWidgetFormDate(),
      'creation_date_mask'             => new sfWidgetFormInputText(),
      'publication_date_from'          => new sfWidgetFormDate(),
      'publication_date_from_mask'     => new sfWidgetFormInputText(),
      'publication_date_to'            => new sfWidgetFormDate(),
      'publication_date_to_mask'       => new sfWidgetFormInputText(),
      'parent_ref'                     => new sfWidgetFormInputText(),
      'path'                           => new sfWidgetFormTextarea(),
      'mime_type'                      => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorInteger(),
      'is_digital'                     => new sfValidatorBoolean(array('required' => false)),
      'type'                           => new sfValidatorString(array('required' => false)),
      'sub_type'                       => new sfValidatorString(array('required' => false)),
      'title'                          => new sfValidatorString(),
      'title_indexed'                  => new sfValidatorString(),
      'subject'                        => new sfValidatorString(array('required' => false)),
      'coverage'                       => new sfValidatorPass(array('required' => false)),
      'apercu_path'                    => new sfValidatorString(array('required' => false)),
      'copyright'                      => new sfValidatorString(array('required' => false)),
      'license'                        => new sfValidatorString(array('required' => false)),
      'uri'                            => new sfValidatorString(array('required' => false)),
      'descriptive_ts'                 => new sfValidatorString(),
      'descriptive_language_full_text' => new sfValidatorString(),
      'creation_date'                  => new sfValidatorDate(array('required' => false)),
      'creation_date_mask'             => new sfValidatorInteger(array('required' => false)),
      'publication_date_from'          => new sfValidatorDate(array('required' => false)),
      'publication_date_from_mask'     => new sfValidatorInteger(array('required' => false)),
      'publication_date_to'            => new sfValidatorDate(array('required' => false)),
      'publication_date_to_mask'       => new sfValidatorInteger(array('required' => false)),
      'parent_ref'                     => new sfValidatorInteger(array('required' => false)),
      'path'                           => new sfValidatorString(array('required' => false)),
      'mime_type'                      => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('old_multimedia[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'OldMultimedia';
  }

}
