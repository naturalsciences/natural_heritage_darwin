<?php

/**
 * WidgetProfilesDefinition form base class.
 *
 * @method WidgetProfilesDefinition getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseWidgetProfilesDefinitionForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['profile_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'add_empty' => false));
    $this->validatorSchema['profile_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'column' => 'id'));

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString();

    $this->widgetSchema   ['order_by'] = new sfWidgetFormInputText();
    $this->validatorSchema['order_by'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['col_num'] = new sfWidgetFormInputText();
    $this->validatorSchema['col_num'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['mandatory'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['mandatory'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['visible'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['visible'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['opened'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['opened'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_available'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_available'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['icon_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['icon_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['title_perso'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title_perso'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['all_public'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['all_public'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['profile_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'add_empty' => false));
    $this->validatorSchema['profile_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('WidgetProfiles'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('widget_profiles_definition[%s]');
  }

  public function getModelName()
  {
    return 'WidgetProfilesDefinition';
  }

}
