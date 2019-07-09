<?php

/**
 * SpecimensCodes form base class.
 *
 * @method SpecimensCodes getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseSpecimensCodesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema   ['code_category'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_category'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['full_code_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['full_code_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['code_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['code_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('specimens_codes[%s]');
  }

  public function getModelName()
  {
    return 'SpecimensCodes';
  }

}
