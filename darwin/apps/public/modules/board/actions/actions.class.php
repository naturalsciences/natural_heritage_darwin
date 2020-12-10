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
  public function executeIndex(sfWebRequest $request)
  {
    $this->specimens = Doctrine::getTable('Specimens')->getRandomPublicSpec(3);
  }

  public function executeTour(sfWebRequest $request)
  {  }

  public function executeAbout(sfWebRequest $request)
  {
    $this->forward('board', 'contact');
  }

  public function executeContact(sfWebRequest $request)
  {
    if($this->getUser()->getCulture() == "nl")
    {
      $this->file1 = "http://projects.naturalsciences.be/attachments/226/Bijlage_2_N_v1_2010.pdf" ;
      $this->file2 = "http://projects.naturalsciences.be/attachments/227/Bijlage_4_N_v1_2010.pdf" ;
    }
    elseif($this->getUser()->getCulture() == "fr")
    {
      $this->file1 = "http://projects.naturalsciences.be/attachments/224/annexe_2_F_v1_2010.pdf" ;
      $this->file2 = "http://projects.naturalsciences.be/attachments/225/annexe_4_F_v1_2010.pdf" ;
    }
    else
    {
      $this->file1 = "http://projects.naturalsciences.be/attachments/224/annexe_2_F_v1_2010.pdf" ;
      $this->file2 = "http://projects.naturalsciences.be/attachments/225/annexe_4_F_v1_2010.pdf" ;
    }
    $this->contact = array(
        "mail" => sfConfig::get('dw_contactMail'),
    );
  }

  public function executeTermOfUse(sfWebRequest $request)
  {  }

  public function executeLang(sfWebRequest $request)
  {
    if(! in_array($request->getParameter('lang'), array('en','fr','nl','es_ES')))
      $this->forward404();
    $this->getUser()->setCulture($request->getParameter('lang'));
    $referer = $this->getRequest()->getReferer();
    $this->redirect($referer ? $referer : '@homepage');
  }
}
