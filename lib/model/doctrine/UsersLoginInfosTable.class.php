<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UsersLoginInfosTable extends DarwinTable
{
  public function getInfoForUser($user_id, $system=null)
  {
    $q = Doctrine_Query::create()
            ->from('UsersLoginInfos u')
            ->andWhere('u.user_ref = ?', $user_id);
    if( $system !== null)
    {
      $q->andWhere('u.login_system = ?',$system);
    }
    return $q->execute();
  }
  
  public function getPasswordByType($user_id, $type)
  {
    $q = Doctrine_Query::create()
            ->from('UsersLoginInfos u')
            ->andWhere('u.user_ref = ?', $user_id)
            ->andWhere('u.login_type = ?', $type); 
    return $q->fetchOne() ;
  }

  /**
  * Get a user login info with his username in internal system
  * This function is used to check a username is already used or not
  * @param string $username The username
  * @return a record with the user or null if it's not found
  */
  public function getUserByUserName($userName, $loginType='local', $loginSystem = null)
  {
      $q = Doctrine_Query::create()
          ->from('UsersLoginInfos ul')
          ->andWhere('ul.user_name = ?',$userName)
          ->andWhere('ul.login_type = ?', $loginType);
      if ($loginSystem === null)
      {
        $q->andWhere('ul.login_system is null');
      }
      else
      {
        $q->andWhere('ul.login_system = ?', $loginSystem);
      }
      return $q->fetchOne();
  }

}
