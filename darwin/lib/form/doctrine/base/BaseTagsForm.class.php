<?php

/**
 * Tags form base class.
 *
 * @method Tags getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTagsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['gtu_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('gtu_ref')), 'empty_value' => $this->getObject()->get('gtu_ref'), 'required' => false));

    $this->widgetSchema   ['group_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['group_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('group_ref')), 'empty_value' => $this->getObject()->get('group_ref'), 'required' => false));

    $this->widgetSchema   ['tag'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag'] = new sfValidatorString();

    $this->widgetSchema   ['group_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['group_type'] = new sfValidatorString();

    $this->widgetSchema   ['sub_group_type'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sub_group_type'] = new sfValidatorString();

    $this->widgetSchema   ['tag_indexed'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['tag_indexed'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('tag_indexed')), 'empty_value' => $this->getObject()->get('tag_indexed'), 'required' => false));

    $this->widgetSchema   ['group_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['group_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('group_ref')), 'empty_value' => $this->getObject()->get('group_ref'), 'required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['gtu_ref'] = new sfValidatorChoice(array('choices' => array($this->getObject()->get('gtu_ref')), 'empty_value' => $this->getObject()->get('gtu_ref'), 'required' => false));

    $this->widgetSchema->setNameFormat('tags[%s]');
  }

  public function getModelName()
  {
    return 'Tags';
  }

}
