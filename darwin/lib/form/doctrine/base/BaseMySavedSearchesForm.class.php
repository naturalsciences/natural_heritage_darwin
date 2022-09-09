<?php

/**
 * MySavedSearches form base class.
 *
 * @method MySavedSearches getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMySavedSearchesForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema   ['name'] = new sfWidgetFormTextarea();
    $this->validatorSchema['name'] = new sfValidatorString();

    $this->widgetSchema   ['search_criterias'] = new sfWidgetFormTextarea();
    $this->validatorSchema['search_criterias'] = new sfValidatorString();

    $this->widgetSchema   ['favorite'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['favorite'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['is_only_id'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_only_id'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['modification_date_time'] = new sfWidgetFormTextarea();
    $this->validatorSchema['modification_date_time'] = new sfValidatorString();

    $this->widgetSchema   ['visible_fields_in_result'] = new sfWidgetFormTextarea();
    $this->validatorSchema['visible_fields_in_result'] = new sfValidatorString();

    $this->widgetSchema   ['subject'] = new sfWidgetFormTextarea();
    $this->validatorSchema['subject'] = new sfValidatorString();

    $this->widgetSchema   ['query_where'] = new sfWidgetFormTextarea();
    $this->validatorSchema['query_where'] = new sfValidatorString();

    $this->widgetSchema   ['query_parameters'] = new sfWidgetFormTextarea();
    $this->validatorSchema['query_parameters'] = new sfValidatorString();

    $this->widgetSchema   ['user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false));
    $this->validatorSchema['user_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('my_saved_searches[%s]');
  }

  public function getModelName()
  {
    return 'MySavedSearches';
  }

}
