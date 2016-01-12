<?php
class Hstore extends ArrayObject{

  public function import($ar)
  {
    $store = json_decode('{'.preg_replace("/\"=>\"/i","\":\"",$ar).'}', true);
    $this->exchangeArray($store);
  }

  public function export()
  {
    $str = '';
    $iterator = $this->getIterator();
    while($iterator->valid()) {
      $str .= '"'.$iterator->key().'"=>"'.$iterator->current().'",' ;
      $iterator->next();
    }
    $str = substr($str,0,strlen($str)-1) ;
    return $str;
  }
}
