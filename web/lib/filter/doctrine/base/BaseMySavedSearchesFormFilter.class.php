<?php

/**
 * MySavedSearches filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMySavedSearchesFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['search_criterias'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['search_criterias'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['favorite'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['favorite'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['is_only_id'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_only_id'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['modification_date_time'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['visible_fields_in_result'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['visible_fields_in_result'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['subject'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['subject'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['query_where'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['query_where'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['query_parameters'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['query_parameters'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('my_saved_searches_filters[%s]');
  }

  public function getModelName()
  {
    return 'MySavedSearches';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'user_ref' => 'ForeignKey',
      'name' => 'Text',
      'search_criterias' => 'Text',
      'favorite' => 'Boolean',
      'is_only_id' => 'Boolean',
      'modification_date_time' => 'Text',
      'visible_fields_in_result' => 'Text',
      'subject' => 'Text',
      'query_where' => 'Text',
      'query_parameters' => 'Text',
      'user_ref' => 'ForeignKey',
    ));
  }
}
