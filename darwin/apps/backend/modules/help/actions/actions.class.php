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
    $this->help_language = 'en';
    $user = Doctrine_Core::getTable("Users")->find($this->getUser()->getId());
    if ( $user ) {
      $this->help_language = $user->getSelectedLang();
    }
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
}
