<?php

/**
 * ExtLinks form base class.
 *
 * @method ExtLinks getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseExtLinksForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['url'] = new sfWidgetFormTextarea();
    $this->validatorSchema['url'] = new sfValidatorString();

    $this->widgetSchema   ['comment'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['comment_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['contributor'] = new sfWidgetFormTextarea();
    $this->validatorSchema['contributor'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['disclaimer'] = new sfWidgetFormTextarea();
    $this->validatorSchema['disclaimer'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['license'] = new sfWidgetFormTextarea();
    $this->validatorSchema['license'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['display_order'] = new sfWidgetFormInputText();
    $this->validatorSchema['display_order'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('ext_links[%s]');
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

}
