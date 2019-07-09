<?php

/**
 * Identifications filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseIdentificationsFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['notion_concerned'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['notion_concerned'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['notion_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['notion_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['notion_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['notion_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['value_defined'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['value_defined'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['value_defined_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['value_defined_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['determination_status'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['determination_status'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['order_by'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('identifications_filters[%s]');
  }

  public function getModelName()
  {
    return 'Identifications';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'notion_concerned' => 'Text',
      'notion_date' => 'Text',
      'notion_date_mask' => 'Number',
      'value_defined' => 'Text',
      'value_defined_indexed' => 'Text',
      'determination_status' => 'Text',
      'order_by' => 'Number',
    ));
  }
}
