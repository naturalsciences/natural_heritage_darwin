<?php

/**
 * TemplatePeople filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['is_physical'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_physical'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['sub_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sub_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['formated_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name_indexed'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['formated_name_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['formated_name_unique'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['formated_name_unique'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['title'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['title'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['family_name'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['family_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['given_name'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['given_name'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['additional_names'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['additional_names'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['birth_date_mask'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['birth_date_mask'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['birth_date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['birth_date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['gender'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['gender'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('template_people_filters[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeople';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'is_physical' => 'Boolean',
      'sub_type' => 'Text',
      'formated_name' => 'Text',
      'formated_name_indexed' => 'Text',
      'formated_name_unique' => 'Text',
      'title' => 'Text',
      'family_name' => 'Text',
      'given_name' => 'Text',
      'additional_names' => 'Text',
      'birth_date_mask' => 'Number',
      'birth_date' => 'Date',
      'gender' => 'Text',
    ));
  }
}
