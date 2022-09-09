<?php

/**
 * Expeditions form base class.
 *
 * @method Expeditions getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseExpeditionsForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['expedition_from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expedition_to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['expedition_to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['expedition_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expedition_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('expeditions[%s]');
  }

  public function getModelName()
  {
    return 'Expeditions';
  }

}
