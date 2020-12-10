<?php

/**
 * LoanActors form base class.
 *
 * @method LoanActors getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLoanActorsForm extends CataloguePeopleForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema->setNameFormat('loan_actors[%s]');
  }

  public function getModelName()
  {
    return 'LoanActors';
  }

}
