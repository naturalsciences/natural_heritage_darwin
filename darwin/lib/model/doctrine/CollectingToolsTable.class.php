<?php


class CollectingToolsTable extends DarwinTable
{

  public static function getInstance()
  {
      return Doctrine_Core::getTable('CollectingTools');
  }

  public function fetchTools()
  {
    $response = array();
    $q = Doctrine_Query::create()
        ->select('id, tool')
        ->from('CollectingTools')
        ->orderBy('tool_indexed');
    $result = $q->fetchArray();
    foreach ($result as $value)
    {
      $response[$value['id']] = $value['tool'];
    }
    return $response;
  }

  public function getAll()
  {
    $q = Doctrine_Query::create()
      ->from('CollectingTools')
      ->orderBy('tool_indexed');
    return $q->execute();
  }

  public function addTool($tool)
  {
    // Define a new object and try to add and save new value passed
    $newTool = new CollectingTools;
    $newTool->setTool($tool);
    try
    {
      $newTool->save();
    }
    catch (Doctrine_Exception $ne)
    {
      // Return database error if occurs
      $e = new DarwinPgErrorParser($ne);
      return $e->getMessage();
    }
    // Return id of new record saved
    return $newTool->getId();
  }
}
