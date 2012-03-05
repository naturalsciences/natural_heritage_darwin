<?php

/**
 * board actions.
 *
 * @package    darwin
 * @subpackage board
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
 
class loginComponents extends sfComponents
{
  /*
   * Display login slot
  */
  public function executeMenuLogin()
  {
    $this->form = new Loginform(null,array('thin' => true)) ;    
  }
}
