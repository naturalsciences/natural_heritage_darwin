<?php

/**
 * Identifiers form base class.
 *
 * @method Identifiers getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentifiersForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['protocol'] = new sfWidgetFormTextarea();
    $this->validatorSchema['protocol'] = new sfValidatorString();

    $this->widgetSchema   ['value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['value'] = new sfValidatorString();

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['creation_date'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('identifiers[%s]');
  }

  public function getModelName()
  {
    return 'Identifiers';
  }

}
