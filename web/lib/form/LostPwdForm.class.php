<?php

/**
 * Base project form.
 * 
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be> 
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class LostPwdForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema['user_name'] = new sfWidgetFormInputText() ;
    $this->validatorSchema['user_name'] = new sfValidatorString(array('required' => true, 'min_length' => 4, 'trim'=>true),
                                                                array('min_length' => '"%value%" must be at least %min_length% characters.')) ;
    $this->widgetSchema['user_name']->setLabel('Your login');
    $this->widgetSchema['user_name']->setAttributes(array('class'=>'medium_size required_field'));
    $this->widgetSchema['user_email'] = new sfWidgetFormInputText() ;
    $this->widgetSchema['user_email']->setLabel('Your e-mail');
    $this->widgetSchema['user_email']->setAttributes(array('class'=>'medium_size'));

    $this->validatorSchema['user_email'] = new sfValidatorEmail(array('required'=> false),
                                                                array('invalid' => 'E-mail is not of a valid form'
                                                                )
                                                          );
    $this->widgetSchema->setNameFormat('lost_pwd[%s]');
    $this->mergePostValidator(new LostPwdValidatorSchema());
  }
}
