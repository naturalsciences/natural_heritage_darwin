<?php

/**
 * search actions.
 *
 * @package    darwin
 * @subpackage search
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class registerActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new RegisterForm();
    $this->form->addLoginInfos(0);
    $this->form->addComm(0);
    $this->form->addLanguages(0);
    
    // If the search has been triggered by clicking on the search button or with pinned specimens
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
          $this->getUser()->setAuthenticated(true);
          sfContext::getInstance()->getLogger()->debug('LOGIN: '.$this->form->getValue('user_name').' '.$this->user->getId() );
          $this->getUser()->setAttribute('db_user_id',$this->user->getId());
          $this->getUser()->setAttribute('db_user_type',$this->user->getDbUserType());
          $lang = Doctrine::getTable("UsersLanguages")->getPreferredLanguage($this->user->getId());
          if($lang) //prevent from crashing if lang is set
          {
              $this->getUser()->setCulture($lang->getLanguageCountry());
          }
          $this->redirect($this->getContext()->getConfiguration()->generateFrontendUrl('homepage'));
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
    $this->setTemplate('index');
  }
  
  public function executeLogin(sfWebRequest $request)
  {
    $this->redirectIf($this->getUser()->isAuthenticated(),$this->getContext()->getConfiguration()->generateFrontendUrl('homepage'));
    $referer = $this->getRequest()->getReferer();
    $this->form = new LoginForm(null, array('thin'=>true));
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('login'));
      if ($this->form->isValid())
      {
        $this->getUser()->setAuthenticated(true);
        sfContext::getInstance()->getLogger()->debug('LOGIN: '.$this->form->getValue('username').' '.$this->form->user->getId() );
        $this->getUser()->setAttribute('db_user_id',$this->form->user->getId());
        $this->getUser()->setAttribute('db_user_type',$this->form->user->getDbUserType());
        $lang = Doctrine::getTable("UsersLanguages")->getPreferredLanguage($this->form->user->getId());
        if($lang) //prevent from crashing if lang is set
        {
            $this->getUser()->setCulture($lang->getLanguageCountry());
        }
        $this->redirect($this->getContext()->getConfiguration()->generateFrontendUrl('homepage'));
      }
    }
    $this->redirect($referer);
  }
}
