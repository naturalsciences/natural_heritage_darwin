<?php

/**
 * Bibliography form base class.
 *
 * @method Bibliography getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseBibliographyForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['title'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title'] = new sfValidatorString();

    $this->widgetSchema   ['title_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['title_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type'] = new sfValidatorString();

    $this->widgetSchema   ['abstract'] = new sfWidgetFormTextarea();
    $this->validatorSchema['abstract'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['year'] = new sfWidgetFormInputText();
    $this->validatorSchema['year'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['reference'] = new sfWidgetFormTextarea();
    $this->validatorSchema['reference'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['uri_protocol'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri_protocol'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['uri'] = new sfWidgetFormTextarea();
    $this->validatorSchema['uri'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema->setNameFormat('bibliography[%s]');
  }

  public function getModelName()
  {
    return 'Bibliography';
  }

}
