<?php

/**
 * VIgsSpecStats form base class.
 *
 * @method VIgsSpecStats getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVIgsSpecStatsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num'] = new sfValidatorString();

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorString();

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['ig_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['nagoya_status'] = new sfWidgetFormTextarea();
    $this->validatorSchema['nagoya_status'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_type'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['spec_count_by_collection'] = new sfWidgetFormInputText();
    $this->validatorSchema['spec_count_by_collection'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['spec_count'] = new sfWidgetFormInputText();
    $this->validatorSchema['spec_count'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['complete'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['complete'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['id'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false));

    $this->widgetSchema->setNameFormat('v_igs_spec_stats[%s]');
  }

  public function getModelName()
  {
    return 'VIgsSpecStats';
  }

}
