<?php

/**
 * Loans filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseLoansFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['description'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['description'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['search_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['search_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['from_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['extended_to_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['extended_to_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('loans_filters[%s]');
  }

  public function getModelName()
  {
    return 'Loans';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'name' => 'Text',
      'description' => 'Text',
      'search_indexed' => 'Text',
      'from_date' => 'Text',
      'to_date' => 'Text',
      'extended_to_date' => 'Text',
    ));
  }
}
