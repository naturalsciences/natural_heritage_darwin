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

    $this->widgetSchema   ['type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['comment'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment'] = new sfValidatorString();

    $this->widgetSchema   ['comment_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['comment_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('ext_links[%s]');
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

}
