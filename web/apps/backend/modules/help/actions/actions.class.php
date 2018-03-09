<?php

/**
 * help actions.
 *
 * @package    darwin
 * @subpackage help
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class helpActions extends DarwinActions
{

  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executeContact(sfWebRequest $request)
  {
    $this->contact = array(
        "mail" => sfConfig::get('dw_contactMail'),
        "mail_git" => sfConfig::get('dw_contactMailGit'),
    );
  }

  public function executeAbout(sfWebRequest $request)
  {
  }

  public function executeContrib(sfWebRequest $request)
  {
  }
  
  //ftheeten 2018 03 07  
  public function executeTemplate(sfWebRequest $request)
  {
  }
}
