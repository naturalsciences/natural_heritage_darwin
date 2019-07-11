<?php

/**
 * OldMultimedia form base class.
 *
 * @method OldMultimedia getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseOldMultimediaForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_digital'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_digital'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title'] = new sfValidatorString();

    $this->widgetSchema   ['title_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title_indexed'] = new sfValidatorString();

    $this->widgetSchema   ['subject'] = new sfWidgetFormTextarea();
    $this->validatorSchema['subject'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['coverage'] = new sfWidgetFormInputText();
    $this->validatorSchema['coverage'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['apercu_path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['apercu_path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['copyright'] = new sfWidgetFormTextarea();
    $this->validatorSchema['copyright'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['license'] = new sfWidgetFormTextarea();
    $this->validatorSchema['license'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['descriptive_ts'] = new sfWidgetFormTextarea();
    $this->validatorSchema['descriptive_ts'] = new sfValidatorString();

    $this->widgetSchema   ['descriptive_language_full_text'] = new sfWidgetFormTextarea();
    $this->validatorSchema['descriptive_language_full_text'] = new sfValidatorString();

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormDate();
    $this->validatorSchema['creation_date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['publication_date_from'] = new sfWidgetFormDate();
    $this->validatorSchema['publication_date_from'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['publication_date_from_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['publication_date_from_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['publication_date_to'] = new sfWidgetFormDate();
    $this->validatorSchema['publication_date_to'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['publication_date_to_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['publication_date_to_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mime_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('old_multimedia[%s]');
  }

  public function getModelName()
  {
    return 'OldMultimedia';
  }

}
