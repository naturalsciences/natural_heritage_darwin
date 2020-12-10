<?php

/**
 * UsersLoginInfos form.
 *
 * @package    form
 * @subpackage UsersLoginInfos
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class UsersLoginInfosForm extends BaseUsersLoginInfosForm
{
  public function configure()
  {
    $this->useFields(array('login_type', 'user_name', 'user_ref'));

    if($this->getObject()->isNew())
    {
      $this->widgetSchema['login_type'] = new sfWidgetFormChoice(array('choices' => array('local' => 'local' ,'ldap'=>'ldap'))) ;
      $this->validatorSchema['login_type'] = new sfValidatorChoice(array('choices' => array('local' => 'local', 'ldap'=>'ldap'))) ;
      $this->widgetSchema['new_password'] = new sfwidgetFormInputPassword();
      $this->validatorSchema['new_password'] = new sfValidatorString(
        array('required' => false, 'min_length' => 4),
        array('min_length' => '"%value%" must be at least %min_length% characters.'));
      $this->widgetSchema['confirm_password'] = new sfwidgetFormInputPassword();
      $this->validatorSchema['confirm_password'] = new sfValidatorString(array('required' => false));
      $this->widgetSchema['user_name'] = new sfWidgetFormInputText() ;
      $this->validatorSchema['user_name'] = new sfValidatorString(
        array('required' => true, 'min_length' => 4),
        array('min_length' => '"%value%" must be at least %min_length% characters.')
      );
    }
    else
    {
      $this->widgetSchema['login_type'] = new sfWidgetFormInputHidden() ;
      $this->validatorSchema['login_type'] = new sfValidatorPass(array('required' => true)) ;
      $this->widgetSchema['new_password'] = new sfwidgetFormInputPassword();
      $this->validatorSchema['new_password'] = new sfValidatorString(array('required' => false));
      $this->widgetSchema['confirm_password'] = new sfwidgetFormInputPassword();
      $this->validatorSchema['confirm_password'] = new sfValidatorString(array('required' => false));
      $this->widgetSchema['user_name'] = new sfWidgetFormInputHidden() ;
      $this->validatorSchema['user_name'] = new sfValidatorPass(array('required' => true)) ;
    }
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden() ;

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkPassword')))
    );
  }

  public function checkPassword($validator, $values)
  {
    if(! isset($values['login_type']))
      $values['login_type'] = 'local';
    $this->password = Doctrine::getTable('UsersLoginInfos')->getPasswordByType($values['user_ref'], $values['login_type']);
    if($this->getObject()->isNew()) //if isset so we're in edition mode
    {
      if($this->password)
      {
        $error = new sfValidatorError($validator, sprintf('type %s already exist for this user',$values['login_type']));
        // throw an error bound to the password field
        throw new sfValidatorErrorSchema($validator, array('login_type' => $error));
      }
    }
    if($values['login_type'] == 'local'){
      if($values['new_password'] == '') {
        $error = new sfValidatorError($validator, 'Required' ) ;
        throw new sfvalidatorErrorSchema($validator, array('new_password' => $error));
      }
      if($values['confirm_password'] == '') {
        $error = new sfValidatorError($validator, 'Required' ) ;
        throw new sfvalidatorErrorSchema($validator, array('confirm_password' => $error));
      }
    }
    if($values['new_password'] != $values['confirm_password'])
    {
      $error = new sfValidatorError($validator, 'Password does not match' ) ;
      throw new sfvalidatorErrorSchema($validator, array('new_password' => $error));
    }

    return $values;
  }
}
