<?php

/**
 * Users form base class.
 *
 * @method Users getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseUsersForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_physical'] = new sfValidatorBoolean();

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['family_name'] = new sfValidatorString();

    $this->widgetSchema   ['given_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['given_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormTextarea();
    $this->validatorSchema['additional_names'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['birth_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['birth_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['birth_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['birth_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['gender'] = new sfWidgetFormChoice(array('choices' => array('M' => 'M', 'F' => 'F')));
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('choices' => array(0 => 'M', 1 => 'F'), 'required' => false));

    $this->widgetSchema   ['db_user_type'] = new sfWidgetFormInputText();
    $this->validatorSchema['db_user_type'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['people_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['selected_lang'] = new sfWidgetFormTextarea();
    $this->validatorSchema['selected_lang'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['default_widget_collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidgetCollection'), 'add_empty' => true));
    $this->validatorSchema['default_widget_collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidgetCollection'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['taxonomic_manager'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['taxonomic_manager'] = new sfValidatorBoolean();

    $this->widgetSchema   ['people_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['people_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema   ['default_widget_collection_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidgetCollection'), 'add_empty' => true));
    $this->validatorSchema['default_widget_collection_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DefaultWidgetCollection'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('users[%s]');
  }

  public function getModelName()
  {
    return 'Users';
  }

}
