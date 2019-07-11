<?php

/**
 * LoanActors form base class.
 *
 * @method LoanActors getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanActorsForm extends CataloguePeopleForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_actors[%s]');
  }

  public function getModelName()
  {
    return 'LoanActors';
  }

}
