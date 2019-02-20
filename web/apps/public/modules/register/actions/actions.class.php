<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class registerActions extends DarwinActions
{
  /*Function sending an email to the specified user to confirm he's been well registered*/
  protected function sendConfirmationMail($userParams)
  {
    $message = $this->getMailer()->compose();
    $message->setFrom(array(sfConfig::get('dw_mailer_sender') => 'DaRWIN 2 team'));
    if(is_array($userParams))
    {
      if (isset($userParams['mail']) && isset($userParams['name']) && isset($userParams['physical']))
      {
        if(!empty($userParams['mail']))
        {
          $message->setTo($userParams['mail']);
          $message->setSubject($this->getI18N()->__('DaRWIN 2 registration'));
          $message->setBody($this->getPartial('confirmationMail', array('userParams'=>$userParams)),'text/plain');
          $this->getMailer()->send($message);
        }
      }
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new RegisterForm();
    $this->form->addLoginInfos(0);
    $this->form->addComm(0);
    
    if(($request->isMethod('post') && $request->getParameter('users','') !== '' ))
    {
      $captcha = array(
        'recaptcha_challenge_field' => $request->getParameter('recaptcha_challenge_field'),
        'recaptcha_response_field'  => $request->getParameter('recaptcha_response_field'),
      );
      $this->form->bind(array_merge($request->getParameter('users'), array('captcha' => $captcha)));
      if ($this->form->isValid())
      {
        try
        {
          $this->user = $this->form->save();
          $this->user->addUserWidgets();
          $userInfos = $request->getParameter('users');
          $mail = '';
          if (isset($userInfos['RegisterCommForm'][0]['entry']))
            $mail = $userInfos['RegisterCommForm'][0]['entry'];
          $username = '';
          if (isset($userInfos['RegisterLoginInfosForm'][0]['user_name']))
            $username = $userInfos['RegisterLoginInfosForm'][0]['user_name'];
          $base_params =  array('physical' => $this->user->getIsPhysical(),
                                'name' => $this->user->getFamilyName(),
                                'title' => $this->user->getTitle()
                               );
          $suppl_params = array('mail' => $mail,
                                'username' => $username
                               );
          // send an email to the registered user
          $this->getUser()->setCulture($this->form->getValue('selected_lang'));
          $this->sendConfirmationMail(array_merge($base_params,$suppl_params));
          $this->redirect('register/succeeded?'.http_build_query($base_params));
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
  }
  
  /*When registration succeeded redirect on a succeeded page with users parameter to be used*/
  public function executeSucceeded(sfWebRequest $request)
  {
    $this->params = array('physical'=> $request->getParameter('physical', 'physical'),
                          'name' => $request->getParameter('name', ''),
                          'title' => $request->getParameter('title', '')
                         );
  }

  /*When password renew succeeded redirect on a succeeded page*/
  public function executeRenewPwdSucceeded()
  {
  }

  public function executeLogin(sfWebRequest $request)
  {
    $this->redirectIf($this->getUser()->isAuthenticated(), $this->getContext()->getConfiguration()->generateBackendUrl('homepage', array(), $request));
    $referer = $this->getRequest()->getReferer();
    $login_params = array();
    $this->form = new LoginForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('login'));
      if ($this->form->isValid())
      {
        $this->getUser()->setAuthenticated(true);
        if(in_array($this->form->user->getSelectedLang(),array('en','fr','nl'))) //Prevent errors
        {
          $this->getUser()->setCulture($this->form->user->getSelectedLang());
        }

        $this->getUser()->setHelpIcon(Doctrine::getTable("Preferences")->getPreference($this->form->user->getId(),'help_message_activated',true));
        sfContext::getInstance()->getLogger()->debug('LOGIN: '.$this->form->getValue('username').' '.$this->form->user->getId() );
        $this->getUser()->setAttribute('db_user_id',$this->form->user->getId());
        $this->getUser()->setAttribute('db_user_type',$this->form->user->getDbUserType());
        if(in_array($this->form->user->getSelectedLang(),array('en','fr','nl'))) //Prevent errors
        {
          $this->getUser()->setCulture($this->form->user->getSelectedLang());
        }
      }
      else
      {
        $login_params['l_err'] = '1';
      }
      //$this->redirect($this->getContext()->getConfiguration()->generateBackendUrl('homepage', $login_params, $request));
    }
    $this->redirect($referer);
  }
  
  public function executeLogout(sfWebRequest $request)
  {
    $referer = $this->getRequest()->getReferer();
    $this->getUser()->clearCredentials();
    $this->getUser()->setAuthenticated(false);
    if(!$referer)
      $this->redirect('@homepage');
    else
      $this->redirect($referer);
  }

}
