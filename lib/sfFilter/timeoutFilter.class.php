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

    $version = $conn->getAttribute(PDO::ATTR_SERVER_VERSION);
    if(strpos($version,'8.') !== 0) {
      $conn->exec("SET application_name = 'darwin';");
    }

    $filterChain->execute();
  }
}
