<?php 
class timeoutFilter extends sfFilter
{
  public function execute ($filterChain)
  {

    if($time = sfConfig::get('dw_queryTimeout',null))
    {
      $conn = Doctrine_Manager::connection();
      $conn->exec("SET statement_timeout = ".$time.";");
    }
    $filterChain->execute();
  }
}