<?php

/**
 * TemplatePeopleUsersCommCommon filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleUsersCommCommonFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['person_user_ref'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['entry'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['entry'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('template_people_users_comm_common_filters[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersCommCommon';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'person_user_ref' => 'Number',
      'entry' => 'Text',
    ));
  }
}
