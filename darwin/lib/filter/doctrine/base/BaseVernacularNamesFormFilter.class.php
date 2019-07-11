<?php

/**
 * VernacularNames filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVernacularNamesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['referenced_relation'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['referenced_relation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['record_id'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['record_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['community'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['community'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('vernacular_names_filters[%s]');
  }

  public function getModelName()
  {
    return 'VernacularNames';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'referenced_relation' => 'Text',
      'record_id' => 'Number',
      'community' => 'Text',
      'name' => 'Text',
      'name_indexed' => 'Text',
    ));
  }
}
