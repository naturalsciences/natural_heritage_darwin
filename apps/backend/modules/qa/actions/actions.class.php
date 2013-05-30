<?php

/**
 * qa actions.
 *
 * @package    darwin
 * @subpackage help
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class qaActions extends DarwinActions
{
  public function executeIndex(sfWebRequest $request) {
    $this->forward404Unless ( $this->getUser()->isAtLeast(Users::ENCODER) );

    $this->fixes = Doctrine::getTable("CodeToCorrect")->getForUser($this->getUser());
    $this->fix_count = Doctrine::getTable("CodeToCorrect")->getForUserCount($this->getUser());
    return $q;
  }
  
  public function executeMove(sfWebRequest $request) {
    $this->forward404Unless ( $this->getUser()->isAtLeast(Users::ENCODER) );

    $ret = Doctrine::getTable("CodeToCorrect")->move(
      $this->getUser(),
      $request->getParameter('from'),
      $request->getParameter('to')
    );

    return $this->renderText(json_encode(array('status'=> 'ok')));
  }
}
