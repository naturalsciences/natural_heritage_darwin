<?php

/**
 * RegisterLoginInfos form.
 *
 * @package    form
 * @subpackage RegisterLoginInfos
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class RegisterLoginInfosForm extends UsersLoginInfosForm
{

  public function configure()
  {
    parent::configure();
    unset($this['login_type'], $this['id']);
    $this->widgetSchema->setLabels(array('new_password'=>'Password', 'user_name'=>'Login'));
    $this->widgetSchema['user_name']->setAttributes(array('class'=>'medium_small_size required_field'));
    $this->widgetSchema['new_password']->setAttributes(array('class'=>'medium_small_size required_field'));
    $this->widgetSchema['confirm_password']->setAttributes(array('class'=>'medium_small_size required_field'));
    $this->validatorSchema['user_name'] = new sfValidatorString(
      array('required' => true, 'min_length' => 4, 'trim' => true),
      array('min_length' => '"%value%" must be at least %min_length% characters.',
      'required' => 'Login is required'
      )
    );

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
          array(
            'required'=>'You need to confirm your password',
            'min_length' => 'Password must be at least %min_length% characters length'
          )
        )
      )
    );
    $this->validatorSchema['user_ref'] = new sfValidatorInteger(array('required'=>false));
  }

  public function checkPassword($validator, $values)
  {
    if($values['new_password'] != $values['confirm_password'])
    {
      $error = new sfValidatorError($validator, 'Password does not match' );
      throw new sfvalidatorErrorSchema($validator, array('confirm_password' => $error));
    }
    if(! empty($values['user_name']) )
    {
        $this->user = Doctrine::getTable('UsersLoginInfos')->getUserByUserName($values['user_name']);
        if ($this->user)
        {
          $error = new sfValidatorError($validator, 'Login is already used, please provide an other');
          // throw an error bound to the password field
          throw new sfValidatorErrorSchema($validator, array('user_name' => $error));
        }
    }
    return $values;
  }
}
