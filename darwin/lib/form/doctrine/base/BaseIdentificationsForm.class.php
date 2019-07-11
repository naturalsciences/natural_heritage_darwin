<?php

/**
 * Identifications form base class.
 *
 * @method Identifications getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentificationsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['notion_concerned'] = new sfWidgetFormTextarea();
    $this->validatorSchema['notion_concerned'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['notion_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['notion_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['notion_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['notion_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['value_defined'] = new sfWidgetFormTextarea();
    $this->validatorSchema['value_defined'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['value_defined_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['value_defined_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['determination_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['determination_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormInputText();
    $this->validatorSchema['order_by'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('identifications[%s]');
  }

  public function getModelName()
  {
    return 'Identifications';
  }

}
