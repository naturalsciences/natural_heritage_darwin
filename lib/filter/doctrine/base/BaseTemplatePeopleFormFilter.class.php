<?php

/**
 * TemplatePeople filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTemplatePeopleFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'is_physical'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'sub_type'              => new sfWidgetFormFilterInput(),
      'formated_name'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'formated_name_indexed' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'formated_name_unique'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'title'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'family_name'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'given_name'            => new sfWidgetFormFilterInput(),
      'additional_names'      => new sfWidgetFormFilterInput(),
      'birth_date_mask'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'birth_date'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'gender'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'is_physical'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'sub_type'              => new sfValidatorPass(array('required' => false)),
      'formated_name'         => new sfValidatorPass(array('required' => false)),
      'formated_name_indexed' => new sfValidatorPass(array('required' => false)),
      'formated_name_unique'  => new sfValidatorPass(array('required' => false)),
      'title'                 => new sfValidatorPass(array('required' => false)),
      'family_name'           => new sfValidatorPass(array('required' => false)),
      'given_name'            => new sfValidatorPass(array('required' => false)),
      'additional_names'      => new sfValidatorPass(array('required' => false)),
      'birth_date_mask'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'birth_date'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'gender'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('template_people_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TemplatePeople';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'is_physical'           => 'Boolean',
      'sub_type'              => 'Text',
      'formated_name'         => 'Text',
      'formated_name_indexed' => 'Text',
      'formated_name_unique'  => 'Text',
      'title'                 => 'Text',
      'family_name'           => 'Text',
      'given_name'            => 'Text',
      'additional_names'      => 'Text',
      'birth_date_mask'       => 'Number',
      'birth_date'            => 'Date',
      'gender'                => 'Text',
    );
  }
}
