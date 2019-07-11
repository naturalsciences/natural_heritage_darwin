<?php

/**
 * ClassificationKeywords form base class.
 *
 * @method ClassificationKeywords getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseClassificationKeywordsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['keyword_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['keyword_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['keyword'] = new sfWidgetFormTextarea();
    $this->validatorSchema['keyword'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('classification_keywords[%s]');
  }

  public function getModelName()
  {
    return 'ClassificationKeywords';
  }

}
