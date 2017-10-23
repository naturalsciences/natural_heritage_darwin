<?php

/**
 * board actions.
 *
 * @package    darwin
 * @subpackage board
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class boardActions extends DarwinActions
{
  protected $widgetCategory = 'board_widget';

  public function executeIndex(sfWebRequest $request)
  {
    $this->loadWidgets();
  }

}
