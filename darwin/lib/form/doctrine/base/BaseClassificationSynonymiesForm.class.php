<?php

/**
 * ClassificationSynonymies form base class.
 *
 * @method ClassificationSynonymies getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseClassificationSynonymiesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['group_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['group_id'] = new sfValidatorInteger();

    $this->widgetSchema   ['group_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_name'] = new sfValidatorString();

    $this->widgetSchema   ['is_basionym'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_basionym'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormInputText();
    $this->validatorSchema['order_by'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['synonym_record_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['synonym_record_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['original_synonym'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['original_synonym'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['syn_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['syn_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['syn_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['syn_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('classification_synonymies[%s]');
  }

  public function getModelName()
  {
    return 'ClassificationSynonymies';
  }

}
