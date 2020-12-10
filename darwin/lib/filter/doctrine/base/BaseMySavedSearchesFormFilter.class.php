<?php

/**
 * MySavedSearches filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMySavedSearchesFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_ref'                 => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'name'                     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'search_criterias'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'favorite'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_only_id'               => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'modification_date_time'   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'visible_fields_in_result' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'subject'                  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'query_where'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'query_parameters'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'user_ref'                 => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'name'                     => new sfValidatorPass(array('required' => false)),
      'search_criterias'         => new sfValidatorPass(array('required' => false)),
      'favorite'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_only_id'               => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'modification_date_time'   => new sfValidatorPass(array('required' => false)),
      'visible_fields_in_result' => new sfValidatorPass(array('required' => false)),
      'subject'                  => new sfValidatorPass(array('required' => false)),
      'query_where'              => new sfValidatorPass(array('required' => false)),
      'query_parameters'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('my_saved_searches_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MySavedSearches';
  }

  public function getFields()
  {
    return array(
      'id'                       => 'Number',
      'user_ref'                 => 'ForeignKey',
      'name'                     => 'Text',
      'search_criterias'         => 'Text',
      'favorite'                 => 'Boolean',
      'is_only_id'               => 'Boolean',
      'modification_date_time'   => 'Text',
      'visible_fields_in_result' => 'Text',
      'subject'                  => 'Text',
      'query_where'              => 'Text',
      'query_parameters'         => 'Text',
    );
  }
}
