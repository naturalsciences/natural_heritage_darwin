<?php

/**
 * Identifiers filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentifiersFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['protocol'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['protocol'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['value'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['value'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['creation_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['creation_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('identifiers_filters[%s]');
  }

  public function getModelName()
  {
    return 'Identifiers';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'protocol' => 'Text',
      'value' => 'Text',
      'creation_date' => 'Text',
    ));
  }
}
