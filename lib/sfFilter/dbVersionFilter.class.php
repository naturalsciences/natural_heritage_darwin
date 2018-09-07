<?php
class dbVersionFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    $this->getContext()->set ('is_outdated', false) ;

    $sql = 'SELECT id as version from db_version';
    $dbh = Doctrine_Manager::connection()->getDbh();
    $sth = $dbh->prepare($sql);
    $sth->execute();
    $db_version = $sth->fetch(PDO::FETCH_COLUMN, 0);



    $files = sfFinder::type('file')
      ->sort_by_name()
      ->name('*.sql')->in(sfConfig::get('sf_data_dir') .'/db/changes/')
      ;



    if(preg_match('/^(\d{4})\-/', basename(end($files)), $matches)) {
      $interface_version = (int)$matches[1];
      if($interface_version != $db_version){
        $this->getContext()->set ('is_outdated' ,true);
        $this->getContext()->getLogger()->notice(sprintf('DB version is different from the interface : DB is %d and interface is %d', $interface_version, $db_version));
      }
    }
    $filterChain->execute();
  }
}
