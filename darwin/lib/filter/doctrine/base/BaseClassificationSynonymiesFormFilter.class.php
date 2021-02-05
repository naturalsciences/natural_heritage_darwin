<?php

/**
 * ClassificationSynonymies filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseClassificationSynonymiesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['group_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['group_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['group_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_basionym'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_basionym'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['order_by'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['order_by'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['synonym_record_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['synonym_record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema->setNameFormat('classification_synonymies_filters[%s]');
  }

  public function getModelName()
  {
    return 'ClassificationSynonymies';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'group_id' => 'Number',
      'group_name' => 'Text',
      'is_basionym' => 'Boolean',
      'order_by' => 'Number',
      'synonym_record_id' => 'Number',
    ));
  }
}
