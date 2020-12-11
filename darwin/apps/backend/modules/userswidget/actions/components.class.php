<?php

class userswidgetComponents extends sfComponents
{
  public function executeAddress()
  {
    $this->addresses =  Doctrine_Core::getTable('UsersAddresses')->fetchByUser($this->eid);
  }
  
  public function executeComm()
  {
    $this->comms =  Doctrine_Core::getTable('UsersComm')->fetchByUser($this->eid);
  }
  
  public function executeInfo()
  {
     $this->login_info =  Doctrine_Core::getTable('UsersLoginInfos')->getInfoForUser($this->eid);
  }
  
  public function executeIdentifiers()
  {
     $this->identifiers =  Doctrine_Core::getTable('Identifiers')->findForTable("users", $this->eid);
  }
}
