<?php

/**
 * Loans form base class.
 *
 * @method Loans getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoansForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormTextarea();
    $this->validatorSchema['description'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['search_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['search_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['extended_to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extended_to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('loans[%s]');
  }

  public function getModelName()
  {
    return 'Loans';
  }

}
