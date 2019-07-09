<?php

/**
 * TemplatePeopleUsersCommCommon form base class.
 *
 * @method TemplatePeopleUsersCommCommon getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseTemplatePeopleUsersCommCommonForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['person_user_ref'] = new sfWidgetFormInputText();
    $this->validatorSchema['person_user_ref'] = new sfValidatorInteger();

    $this->widgetSchema   ['entry'] = new sfWidgetFormTextarea();
    $this->validatorSchema['entry'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('template_people_users_comm_common[%s]');
  }

  public function getModelName()
  {
    return 'TemplatePeopleUsersCommCommon';
  }

}
