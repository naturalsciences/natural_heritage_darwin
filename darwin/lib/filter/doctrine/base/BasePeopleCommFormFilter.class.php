<?php

/**
 * PeopleComm filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePeopleCommFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_user_ref' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true)),
      'comm_type'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'tag'             => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entry'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'person_user_ref' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id')),
      'comm_type'       => new sfValidatorPass(array('required' => false)),
      'tag'             => new sfValidatorPass(array('required' => false)),
      'entry'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('people_comm_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PeopleComm';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'person_user_ref' => 'ForeignKey',
      'comm_type'       => 'Text',
      'tag'             => 'Text',
      'entry'           => 'Text',
    );
  }
}
