<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MySavedSearches extends BaseMySavedSearches
{
  public function getDefaultMDT() 
  {
    $q = Doctrine_Query::create()
        ->select('s.modification_date_time')
        ->from('MySavedSearches s')
        ->where('s.user_ref = ?',$this->getUserRef())
        ->orderBy('s.modification_date_time DESC')
        ->fetchOne() ;
    if($q) return ("last saved on ".substr($q->getModificationDateTime(),0,19)) ;
    else return ("Not yet saved") ;
  }

  public function getModificationDate()
  {
    return new DateTime($this->getModificationDateTime());
  }
  
  public function getVisibleFieldsInResult()
  {
    return $fields_as_str = explode('|', $this->_get('visible_fields_in_result'));
  }

  public function getVisibleFieldsInResultStr()
  {
    return $this->_get('visible_fields_in_result');
  }

  public function setVisibleFieldsInResult($val)
  {
    if(is_array($val))
      $this->_set('visible_fields_in_result', implode('|',$val) );
    else
      $this->_set('visible_fields_in_result',$val);
  }

  // Returns an array from the serialized string stored in search_criterias field
  public function getUnserialRequest() {
    return json_decode($this->getSearchCriterias(), true);
  }
  
  /*
  * set the serialized string stored in search_criterias field
  */
  public function setUnserialRequest($req) {
    return $this->setSearchCriterias(json_encode($req));
  }
  
  // Returns the searched ID from the serialized string stored in search_criterias field
  public function getSearchedIdString()
  {
    $prev_req = $this->getUnserialRequest();
    return $prev_req['specimen_search_filters']['spec_ids'];
  }

  public function getAllSearchedId()
  {
    $prev_req = $this->getUnserialRequest();
    if(isset($prev_req['specimen_search_filters']['spec_ids']) && $prev_req['specimen_search_filters']['spec_ids'] != "")
      $old_ids = explode(',',$prev_req['specimen_search_filters']['spec_ids']);
    else
      $old_ids = array();
    return $old_ids;
  }

  public function getNumberOfIds()
  {
    $ids = $this->getAllSearchedId();
    return count($ids);
  }
}