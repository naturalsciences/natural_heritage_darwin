<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class specimensearchwidgetComponents extends sfComponents
{
  protected function defineForm()
  {
    if(!$this->form)
    {
     $this->form = new SpecimensFormFilter() ;
    }
  }

  public function executeRefCollection()
  {
    $this->defineForm();
  }

  public function executeRefTaxon()
  {
    $this->defineForm();
  }
  public function executeRefLitho()
  {
    $this->defineForm();
  }
  public function executeRefLithology()
  {
    $this->defineForm();
  }
  public function executeRefChrono()
  {
    $this->defineForm();
  }
  public function executeRefMineral()
  {
    $this->defineForm();
  }

  public function executeSpecIds()
  {
    $this->defineForm();
  }

  public function executeRefGtu()
  {
    $this->defineForm();
    if(!$this->form) $this->form->addGtuTagValue(0);
  }

  public function executeType()
  {
    $this->defineForm();
  }

  public function executeSex()
  {
    $this->defineForm();
  }

  public function executeStage()
  {
    $this->defineForm();
  }

  public function executeStatus()
  {
    $this->defineForm();
  }

  public function executeSocial()
  {
    $this->defineForm();
  }

  public function executeRockform()
  {
    $this->defineForm();
  }

  public function executeRefIgs()
  {
    $this->defineForm();
  }


  public function executeCodes()
  {
    $this->defineForm();
    if(! $this->form->isBound() && count($this->form['Codes']) == 0)
      $this->form->addCodeValue(0);
  }

  public function executeMethods()
  {
    $this->defineForm();
  }

  public function executeTools()
  {
    $this->defineForm();
  }

  public function executeMultimedia()
  {
    $this->defineForm();
  }

  public function executeLocalisation()
  {
    $this->defineForm();
  }

  public function executeAcquisitions()
  {
    $this->defineForm();
  }

  public function executeContainer()
  {
    $this->defineForm();
  }

  public function executeLatlong()
  {
    $this->defineForm();
  }

  public function executeExpedition()
  {
    $this->defineForm();
  }

  public function executePeople_role()
  {
    $this->defineForm();
  }

  public function executePart()
  {
    $this->defineForm();
  }

  public function executeProperties()
  {
    $this->defineForm();
  }
  public function executeComments()
  {
    $this->defineForm();
  }
  public function executeStatusAndCount()
  {
    $this->defineForm() ;
  }
}
