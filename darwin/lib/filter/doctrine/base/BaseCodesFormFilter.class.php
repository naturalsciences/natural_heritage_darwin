<?php

/**
 * Codes filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseCodesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['code_category'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code_category'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_prefix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_prefix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_prefix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_suffix_separator'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_suffix_separator'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['full_code_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['full_code_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['code_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['code_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('codes_filters[%s]');
  }

  public function getModelName()
  {
    return 'Codes';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'code_category' => 'Text',
      'code_prefix' => 'Text',
      'code_prefix_separator' => 'Text',
      'code' => 'Text',
      'code_suffix' => 'Text',
      'code_suffix_separator' => 'Text',
      'full_code_indexed' => 'Text',
      'code_date' => 'Text',
      'code_date_mask' => 'Number',
    ));
  }
}
