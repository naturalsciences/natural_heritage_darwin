<?php
class trackingFilter extends sfFilter
{
  public function execute ($filterChain)
  {
    if ($this->isFirstCall())
    {
      $user = $this->getContext()->getUser();

      if($user->isAuthenticated())
      {

        //Check if the user still exists
        $q = Doctrine_Query::create()
          ->useResultCache(true)
          ->setResultCacheLifeSpan(60 * 5) //5 minutes
          ->from('Users')
          ->andWhere('id = ?',$user->getId());
        $usr=$q->fetchOne();

        if(!$usr)
        {
          $user->clearCredentials();
          $user->setAuthenticated(false);

          return $this->getContext()->getController()->redirect('homepage');
        }
        $user->setAttribute('db_user_type', $usr->getDbUserType());
      }
      if($user->isAuthenticated() && sfConfig::get('dw_tracking_enabled',null))
      {
        $conn = Doctrine_Manager::connection();
        $conn->exec("select fct_set_user( ? );", array($user->getId()));
      }

      if($user->isAuthenticated() && function_exists('apache_note')) {
        apache_note('username', $user->getId() );
        apache_note('sessionID', session_id());
      }
    }

    $filterChain->execute();
  }
}
