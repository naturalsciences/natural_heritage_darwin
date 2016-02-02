<?php


class CollectingMethodsTable extends DarwinTable
{
    
  public static function getInstance()
  {
      return Doctrine_Core::getTable('CollectingMethods');
  }

  public function getAll()
  {
    $q = Doctrine_Query::create()
      ->from('CollectingMethods')
      ->orderBy('method_indexed');
    return $q->execute();
  }

  public function fetchMethods()
  {
    $response = array();
    $q = Doctrine_Query::create()
        ->select('id, method')
        ->from('CollectingMethods')
        ->orderBy('method_indexed');
    $result = $q->fetchArray();
    foreach ($result as $value)
    {
      $response[$value['id']] = $value['method'];
    }
    return $response;
  }
  
  public function checkIfMethod($method)
  {
    $q = Doctrine_Query::create()
      ->from('CollectingMethods')
      ->where('method_indexed=fulltoindex(?)',$method)
      ->orderBy('method_indexed');
    return $q->fetchOne();
  }

}
