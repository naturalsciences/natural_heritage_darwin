<?php

/**
 * LostPwdValidatorSchema validates the ask of a password renew page trough e-mail
 *
 * @package    symfony
 * @subpackage validator
 * @author     DarWIN2 team <darwin-ict@naturalsciences.be>
 */

class LostPwdValidatorSchema extends sfValidatorBase
{
  /**
   * @see sfValidatorBase
   */

  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('no_association', "We didn't find any reference with the login and password you provided");
  }

  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    
    $user = Doctrine::getTable('Users')->getUserByLoginAndEMail($value['user_name'], $value['user_email']);
    if(! $user)
    {
       $user = Doctrine::getTable('Users')->getUserByLoginWithEmailOnly($value['user_name']);
       if($user && count($user->UsersComm) ==0)
        $user=null;
    }

    if(!$user)
    {
      $errorSchema->addError(new sfValidatorError($this, 'no_association'), 'global');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
