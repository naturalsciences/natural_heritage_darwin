<?php

/**
 * RegisterComm form.
 *
 * @package    form
 * @subpackage RegisterComm
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class RegisterCommForm extends BaseUsersCommForm
{
  public function configure()
  {
    $comm_mean = UsersComm::getRegisterCommType();
    unset($this['id'], $this['tag']);
    $this->widgetSchema['person_user_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['comm_type'] = new sfWidgetFormInputHidden();
    $this->setDefault('comm_type', $comm_mean);
    $this->widgetSchema['entry'] = new sfWidgetFormInput();
    $this->widgetSchema['entry']->setLabel('e-mail');
    $this->widgetSchema['entry']->setAttributes(array('class'=>'medium_size required_field'));
    $this->validatorSchema['person_user_ref'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['entry'] = new sfValidatorEmail(array('required'=> true, 'trim'=>true),
                                                           array('required'=>'E-mail is required',
                                                                 'invalid' => 'E-mail is not of a valid form'
                                                                )
                                                          );
    $this->validatorSchema['comm_type'] = new sfValidatorString(array('required'=> true, 'empty_value'=> $comm_mean));
  }
}
