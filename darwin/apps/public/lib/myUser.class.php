<?php

class myUser extends sfBasicSecurityUser
{
  public function setCulture($culture)
  {
    if(in_array($culture, array('en','fr','nl','es_ES')))
    {
      parent::setCulture($culture);
    }
    else
      parent::setCulture('en');
  }
  public function setHelpIcon($val)
  {
    $this->setAttribute('helpIcon',$val);
  }

  public function getId()
  {
    return -1;
  }
    
   public function isAtLeast($role)
  {
    return Users::ANONYMOUS >= $role;
  }
}
