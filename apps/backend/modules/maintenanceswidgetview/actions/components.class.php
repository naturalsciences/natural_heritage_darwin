<?php

/**
 * specimen components actions.
 *
 * @package    darwin
 * @subpackage loan_widget
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class maintenanceswidgetviewComponents extends sfComponents
{
  protected function defineObject()
  {
    $this->table ="collection_maintenance";
    if(! isset($this->maintenance) )
      $this->maintenance = Doctrine::getTable('CollectionMaintenance')->find($this->eid);
  }

  public function executeRefProperties()
  {
    $this->defineObject();
  }

  public function executeRefRelatedFiles()
  {
    $this->defineObject();
    $this->files = Doctrine::getTable('Multimedia')->findForTable('collection_maintenance', $this->maintenance->getId()) ;
    $this->atLeastOneFileVisible = true;
  }

  public function executeRefComments()
  { 
    $this->defineObject();
    $this->Comments = Doctrine::getTable('Comments')->findForTable('collection_maintenance', $this->maintenance->getId()) ;
  }
  
  public function executeExtLinks()
  {
    $this->defineObject();
    $this->links = Doctrine::getTable('extLinks')->findForTable('collection_maintenance', $this->maintenance->getId()) ;  
  }  
}
