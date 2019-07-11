<?php

/**
 * IgsSearch form base class.
 *
 * @method IgsSearch getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIgsSearchForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['ig_num'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_num_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ig_num_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ig_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['ig_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['ig_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('ig_ref')), 'empty_value' => $this->getObject()->get('ig_ref'), 'required' => false));

    $this->widgetSchema   ['expedition_name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_ref'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema->setNameFormat('igs_search[%s]');
  }

  public function getModelName()
  {
    return 'IgsSearch';
  }

}
