<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UsersTable extends Doctrine_Table
{
    /**
    * Get an user with his username and password in internal system
    * @param string $username The username
    * @param string $password The password of the user
    * @return a record with the user or null if it's not found
    */
    public function getUserByPassword($username, $password)
    {
        $q = Doctrine_Query::create()
            ->from('Users u')
            ->leftJoin('u.UsersLoginInfos ul')
            ->andWhere('ul.user_name = ?',$username)
            ->andWhere('ul.password = ?',sha1(sfConfig::get('app_salt').$password))
            ->andWhere('ul.system_id is null');
        return $q->fetchOne();
    }
}