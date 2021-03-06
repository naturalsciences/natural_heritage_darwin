<?php

/**
 * People filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_physical'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['family_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['given_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['given_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['additional_names'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['birth_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['birth_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['birth_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['birth_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['gender'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'M' => 'M', 'F' => 'F')));
    $this->validatorSchema['gender'] = new sfValidatorChoice(array('required' => false, 'choices' => array('M' => 'M', 'F' => 'F')));

    $this->widgetSchema   ['end_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['end_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['end_date'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['end_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_from'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_from'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_from_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_from_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['activity_date_to'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_to'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['activity_date_to_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['activity_date_to_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => 'People', 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_filters[%s]');
  }

  public function getModelName()
  {
    return 'People';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'is_physical' => 'Boolean',
      'sub_type' => 'Text',
      'formated_name' => 'Text',
      'formated_name_indexed' => 'Text',
      'title' => 'Text',
      'family_name' => 'Text',
      'given_name' => 'Text',
      'additional_names' => 'Text',
      'birth_date_mask' => 'Number',
      'birth_date' => 'Text',
      'gender' => 'Enum',
      'end_date_mask' => 'Number',
      'end_date' => 'Text',
      'activity_date_from' => 'Text',
      'activity_date_from_mask' => 'Number',
      'activity_date_to' => 'Text',
      'activity_date_to_mask' => 'Number',
      'id' => 'Number',
    ));
  }
}
