<?php

class myUser extends sfBasicSecurityUser
{
  public function setCulture($culture)
  {
    if(in_array($culture, array('en','fr','nl')))
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
}
