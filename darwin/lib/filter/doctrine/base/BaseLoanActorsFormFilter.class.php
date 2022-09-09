<?php

/**
 * LoanActors filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoanActorsFormFilter extends CataloguePeopleFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['record_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Loans'), 'add_empty' => true));
    $this->validatorSchema['record_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Loans'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('loan_actors_filters[%s]');
  }

  public function getModelName()
  {
    return 'LoanActors';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'record_id' => 'ForeignKey',
    ));
  }
}
