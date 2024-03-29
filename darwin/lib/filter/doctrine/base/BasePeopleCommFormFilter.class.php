<?php

/**
 * PeopleComm filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BasePeopleCommFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema   ['comm_type'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['comm_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['tag'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['tag'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['entry'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['entry'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('People'), 'add_empty' => true));
    $this->validatorSchema['person_user_ref'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('People'), 'column' => 'id'));

    $this->widgetSchema->setNameFormat('people_comm_filters[%s]');
  }

  public function getModelName()
  {
    return 'PeopleComm';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'person_user_ref' => 'ForeignKey',
      'comm_type' => 'Text',
      'tag' => 'Text',
      'entry' => 'Text',
      'person_user_ref' => 'ForeignKey',
    ));
  }
}
