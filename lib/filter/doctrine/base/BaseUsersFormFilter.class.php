<?php

/**
 * Users filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUsersFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'is_physical'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'sub_type'              => new sfWidgetFormFilterInput(),
      'formated_name'         => new sfWidgetFormFilterInput(),
      'formated_name_indexed' => new sfWidgetFormFilterInput(),
      'title'                 => new sfWidgetFormFilterInput(),
      'family_name'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'given_name'            => new sfWidgetFormFilterInput(),
      'additional_names'      => new sfWidgetFormFilterInput(),
      'birth_date_mask'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'birth_date'            => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'gender'                => new sfWidgetFormChoice(array('choices' => array('' => '', 'M' => 'M', 'F' => 'F'))),
      'db_user_type'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'people_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'selected_lang'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'is_physical'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'sub_type'              => new sfValidatorPass(array('required' => false)),
      'formated_name'         => new sfValidatorPass(array('required' => false)),
      'formated_name_indexed' => new sfValidatorPass(array('required' => false)),
      'title'                 => new sfValidatorPass(array('required' => false)),
      'family_name'           => new sfValidatorPass(array('required' => false)),
      'given_name'            => new sfValidatorPass(array('required' => false)),
      'additional_names'      => new sfValidatorPass(array('required' => false)),
      'birth_date_mask'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'birth_date'            => new sfValidatorPass(array('required' => false)),
      'gender'                => new sfValidatorChoice(array('required' => false, 'choices' => array('M' => 'M', 'F' => 'F'))),
      'db_user_type'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'people_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
      'selected_lang'         => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('users_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Users';
  }

  public function getFields()
  {
    return array(
      'id'                    => 'Number',
      'is_physical'           => 'Boolean',
      'sub_type'              => 'Text',
      'formated_name'         => 'Text',
      'formated_name_indexed' => 'Text',
      'title'                 => 'Text',
      'family_name'           => 'Text',
      'given_name'            => 'Text',
      'additional_names'      => 'Text',
      'birth_date_mask'       => 'Number',
      'birth_date'            => 'Text',
      'gender'                => 'Enum',
      'db_user_type'          => 'Number',
      'people_id'             => 'ForeignKey',
      'selected_lang'         => 'Text',
    );
  }
}
