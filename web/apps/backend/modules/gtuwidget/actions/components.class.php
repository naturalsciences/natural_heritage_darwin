<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage speicmen_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
 
 //ftheeten 2018 11 29
class gtuwidgetComponents extends sfComponents
{
 
  protected function defineForm()
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER))  {
      print("<div class='warn_message'>".__("You don't have rights to edit these informations !")."</div>");
    }
    if(! isset($this->form) )
    {
      if(isset($this->eid) && $this->eid !== null)
      {
        $gtu = Doctrine_Core::getTable('Gtu')->find($this->eid);
        $this->form = new GtuForm($gtu);
        $this->gtu_id = $this->eid;
        /*if(!$this->getUser()->isA(Users::ADMIN))
        {
          if(! Doctrine_Core::getTable('Specimens')->hasRights('spec_ref', $this->eid, $this->getUser()->getId())) {
            print("<div class='warn_message'>".__("You don't have rights to edit these informations !")."</div>");
          }
        }*/
      }
      else
      {
        $this->form = new GtuForm();
        $this->gtu_id = 0;
      }
      if(!isset($this->individual_id)) $this->individual_id = 0;
    }
    elseif(! isset($this->individual_id) )
    {
      $this->individual_id = 0;
      $this->gtu_id = $this->form->getObject()->getId();
    }

    if(!isset($this->eid))
      $this->eid = $this->form->getObject()->getId();
    if(! isset($this->module) )
    {
      $this->module = 'gtu';
    }
    /*if(! isset($this->addCodeUrl)) {
      $this->addCodeUrl = $this->module.'/addCode';
    }*/
  }
  
  public function executeRefTemporalInformation()
  {  

    $this->defineForm();
    if(!isset($this->form['newGtuTemporalInformationForm']))
    {
      $this->form->loadEmbed('GtuTemporalInformationForm');
    }
	
  }
 }
 ?>
