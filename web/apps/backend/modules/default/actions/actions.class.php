<?php

/**
 * default actions.
 *
 * @package    darwin
 * @subpackage default
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
  /**
  * Error page for page not found (404) error
  *
  */
  public function executeError404()
  {
    $this->contact = array(
        "mail" => sfConfig::get('dw_contactMail'),
    );
  }

  /**
  * Warning page for restricted area - requires login
  *
  */
  public function executeSecure()
  {
  }

  /**
  * Module disabled
  *
  */
  public function executeDisabled()
  {
  }
}
