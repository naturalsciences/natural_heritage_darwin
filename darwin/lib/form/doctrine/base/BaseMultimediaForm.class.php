<?php

/**
 * Multimedia form base class.
 *
 * @method Multimedia getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMultimediaForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['is_digital'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_digital'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title'] = new sfValidatorString();

    $this->widgetSchema   ['description'] = new sfWidgetFormTextarea();
    $this->validatorSchema['description'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['filename'] = new sfWidgetFormInputText();
    $this->validatorSchema['filename'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['search_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['search_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['creation_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['creation_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mime_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mime_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['visible'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['visible'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['publishable'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['publishable'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['extracted_info'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extracted_info'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['technical_parameters'] = new sfWidgetFormTextarea();
    $this->validatorSchema['technical_parameters'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['external_uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['external_uri'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['internet_protocol'] = new sfWidgetFormTextarea();
    $this->validatorSchema['internet_protocol'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['field_observations'] = new sfWidgetFormTextarea();
    $this->validatorSchema['field_observations'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('multimedia[%s]');
  }

  public function getModelName()
  {
    return 'Multimedia';
  }

}
