<?php


class PreferencesTable extends Doctrine_Table
{
  public static function getInstance()
  {
    return Doctrine_Core::getTable('Preferences');
  }

  public function getPreference($user_id, $key, $take_from_default=true)
  {
    $result = Doctrine_Query::create()
      ->useResultCache(true, 3600, 'users_'.$user_id.'_'.$key)
//       ->setResultCacheLifeSpan(15) //15 sec
      ->from('Preferences p')
      ->andwhere('p.user_ref = ?', $user_id)
      ->andWhere('p.pref_key = ?',$key)
      ->fetchOne();
    if($result)
      return $result->getPrefValue();
    elseif($take_from_default)
      return $this->getDefaultValue($key);
    else return null;
  }

  public function setPreference($user_id, $key, $value)
  {
    $result = Doctrine_Query::create()
      ->from('Preferences p')
      ->andwhere('p.user_ref = ?', $user_id)
      ->andWhere('p.pref_key = ?',$key)
      ->fetchOne();

    if(!$result)
    {
      $result = new Preferences();
      $result->fromArray(array(
        'user_ref' => $user_id,
        'pref_key' => $key,
      ));
    }

    $result->setPrefValue($value);
    $result->save();
    return true;
  }
  public function getAllPreferences($user_id, $asked_keys = null)
  {
    $q = Doctrine_Query::create()
      ->from('Preferences p')
      ->andwhere('p.user_ref = ?', $user_id)
      ->andWhereIn('p.pref_key',$asked_keys);
    $query_results = $q->execute();
    $results = array();
    foreach($query_results as $row)
    {
      $key = $row->getPrefKey();
      $results[$key] = $row->getPrefValue();
    }

    foreach($asked_keys as $missing)
    {
      if(! isset($results[$missing]))
        $results[$missing] = '';
    }
    return $results;
  }

  public function saveAllPreferences($user_id, $keys)
  {
    foreach($keys as  $key => $value)
    {
      $this->setPreference($user_id, $key, $value);
    }
    $manager = Doctrine_Manager::getInstance();
    $cacheDriver = $manager->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
    try {
      $cacheDriver->deleteByPrefix('users_'.$user_id);
    } catch(Exception $e) {
      //When doing test we cannot clear doctrine cache as there isn't any
    }
  }

  public function getDefaultValue($key)
  {
    switch($key)
    {
      case 'search_cols_specimen': return 'taxon|individual_type|sex|stage|building|floor|room|row|shelf|container|container_storage|loans'; break;
      case 'board_search_rec_pp': return '10'; break;
      case 'board_spec_rec_pp': return '10'; break;
      case 'default_search_rec_pp': return '10'; break;
      case 'help_message_activated': return true; break;
    }
  }
}

