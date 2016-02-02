<?php

/**
 * RegisterLoginInfos form.
 *
 * @package    form
 * @subpackage RegisterLoginInfos
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class RenewPwdForm extends sfForm
{

  public function configure()
  {
    $this->widgetSchema['id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['hash'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['new_password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['confirm_password'] = new sfWidgetFormInputPassword();

    $this->widgetSchema->setLabels(array('new_password'=>'Password', 'confirm_password'=>'Confirm password'));
    $this->widgetSchema['new_password']->setAttributes(array('class'=>'medium_small_size required_field'));
    $this->widgetSchema['confirm_password']->setAttributes(array('class'=>'medium_small_size required_field'));

    $this->validatorSchema['new_password'] = new sfValidatorAnd(
                                              array(
                                                new sfValidatorRegex(
                                                  array('pattern' => "/\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*/"),
                                                  array('invalid' => 'Password must contain at least a case mix and at least one digit')
                                                  ),
                                                new sfValidatorString(
                                                  array('min_length' => 6, 'trim'=>true, 'required'=>true),
                                                  array('required'=>'You need to provide a password',
                                                        'min_length' => 'Password must be at least %min_length% characters length'
                                                       )
                                                  )
                                              )
                                             );
    $this->validatorSchema['confirm_password'] = new sfValidatorAnd(
                                                  array(
                                                    new sfValidatorRegex(
                                                      array('pattern' => "/\w*(?=\w*\d)(?=\w*[a-z])(?=\w*[A-Z])\w*/"),
                                                      array('invalid' => 'Password must contain at least a case mix and at least one digit')
                                                      ),
                                                    new sfValidatorString(
                                                      array('min_length' => 6, 'trim'=>true, 'required'=>true),
                                                      array('required'=>'You need to confirm your password',
                                                            'min_length' => 'Password must be at least %min_length% characters length'
                                                          )
                                                      )
                                                  )
                                                );
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>true));
    $this->validatorSchema['hash'] = new sfValidatorString(array('required'=>true, 'trim'=>true));
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('new_password', '==', 'confirm_password',
                                                                          array(),
                                                                          array('invalid' => 'Passwords must be equals and can not contains spaces')
                                                                         )
                                            );
    $this->widgetSchema->setNameFormat('renew_pwd[%s]');
  }
}
