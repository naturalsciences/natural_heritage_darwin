<?php

/**
 * TemplateClassifications form base class.
 *
 * @method TemplateClassifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplateClassificationsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['level_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['level_ref'] = new sfValidatorInteger();

    $this->widgetSchema   ['status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['local_naming'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['local_naming'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['color'] = new sfWidgetFormTextarea();
    $this->validatorSchema['color'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['path'] = new sfWidgetFormTextarea();
    $this->validatorSchema['path'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['parent_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['parent_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('template_classifications[%s]');
  }

  public function getModelName()
  {
    return 'TemplateClassifications';
  }

}
