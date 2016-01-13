<?php
class Hstore extends ArrayObject{

  /**
   * Make the creation and replacement of an array composed from a hstore field
   * @param $ar A string representing the content of a hstore field in the database
   */
  public function import($ar)
  {
    // regex replace the "=>" hstore key/value association by a json separator
    // rounded by double-quotes ":" and replace the annoying characters \n\t\r
    $ar_preged = "{".str_replace("\n","\\n",preg_replace("/[\r\t]+/","",preg_replace("/\"=>\"/i","\":\"",$ar)))."}";
    // json_decode the regexped string and produce an associative array
    $store = json_decode($ar_preged, true);
    // If for some reason the production of the array didn't succeed...
    if (!is_array($store)) {
      // ... produce one by passing the hstore stringified field back to postgres
      // and by looping throught the result to build a correct associative array
      $store = array();
      $conn = Doctrine_Manager::connection();
      $temp = $conn->fetchAssoc(
        "SELECT (each( ? ::hstore )).key, (each( ? ::hstore )).value",
        array($ar,$ar)
      );
      $conn->close();
      foreach( $temp as $values ) {
        $store[$values['key']] = $values['value'];
      }
    }
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
