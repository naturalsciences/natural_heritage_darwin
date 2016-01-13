<?php
class Hstore extends ArrayObject{

  public function import($ar)
  {
    //$store = json_decode('{'.preg_replace("/\"=>\"/i","\":\"",$ar).'}', true);
    eval("\$store = array({$ar});");
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
