<?php

class myUser extends sfBasicSecurityUser
{
  public function getId()
  {
    return $this->getAttribute('db_user_id');
  }

  public function getDbUserType()
  {
    return $this->getAttribute('db_user_type');
  }

  public function setCulture($culture)
  {
    if(in_array($culture, array('en','fr','nl')))
    {
      parent::setCulture($culture);
    }
    else
      parent::setCulture('en');
  }

  /**
   * Save the visible columns in the search
   * @param array $columns Array of columns names
   */
  public function storeVisibleCols($columns)
  {
    $this->setAttribute('spec_search_columns',$columns);
  }

  /**
   * Fetch the visible columns in the search
   * @return array an array of visible fields
   */
  public function fetchVisibleCols()
  {
    return explode('|',Doctrine::getTable('Preferences')->getPreference($this->getId(),'search_cols_specimen',true));
  }


  public function storeRecPerPage($number)
  {
    $this->setAttribute('rec_per_page',$number);
  }

  public function fetchRecPerPage()
  {
    return $this->getAttribute('rec_per_page',Doctrine::getTable('Preferences')->getPreference($this->getId(),'default_search_rec_pp',true));
  }


  public function removePinTo($id,$source)
  {
    $pins = $this->getAttribute('spec_pinned_'.$source, array());
    if( ($key = array_search($id, $pins)) !== false)
      unset($pins[$key]);

    $this->setAttribute('spec_pinned_'.$source, $pins);

  }

  public function clearPinned($source)
  {
     $this->setAttribute('spec_pinned_'.$source,array());
  }

  public function addPinTo($id, $source)
  {
    $pins = $this->getAttribute('spec_pinned_'.$source, array());
    if(array_search($id, $pins) === false)
      $pins[] = $id;
    $pins = array_unique($pins);
    $this->setAttribute('spec_pinned_'.$source,$pins);
  }

  public function getAllPinned($source)
  {
    return $this->getAttribute('spec_pinned_'.$source,array());
  }

  public function isPinned($id, $source)
  {
    $pins = $this->getAttribute('spec_pinned_'.$source, array());
    return (array_search($id, $pins) === false) ? false : true;
  }

  public function isAtLeast($role)
  {
    return $this->getDbUserType() >= $role;
  }

  public function isA($role)
  {
    return $this->getDbUserType() == $role;
  }

  /**
   * @return a boolean to know if the help icon is displayed on forms or not
   */
  public function getHelpIcon()
  {
    return $this->getAttribute('helpIcon') ;
  }

  public function setHelpIcon($val)
  {
    $this->setAttribute('helpIcon',$val);
  }


  public function getPreference($name, $take_def)
  {
    return Doctrine::getTable("Preferences")->getPreference($this->getId(), $name, $take_def);
  }
}
