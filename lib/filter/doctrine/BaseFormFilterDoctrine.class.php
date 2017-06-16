<?php

/**
 * Project filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterBaseTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class BaseFormFilterDoctrine extends sfFormFilterDoctrine
{
  public function setup()
  {
  }

  public function addPagerItems()
  {
    $recPerPages = array("1"=>"1", "2"=>"2", "5"=>"5", "10"=>"10","20"=>"20", "25"=>"25", "50"=>"50", "75"=>"75", "100"=>"100", "800"=>"800");

    $this->widgetSchema['rec_per_page'] = new sfWidgetFormChoice(array('choices' => $recPerPages), array('class'=>'rec_per_page'));
    $user = sfContext::getInstance()->getUser();
    if($user)
    {
      $default = Doctrine::getTable('Preferences')->getPreference(
        $user->getId() ,
        'default_search_rec_pp'
      );
    } else {
      $default = 10;
    }
    $this->setDefault('rec_per_page', $default);
    $this->widgetSchema->setLabels(array('rec_per_page' => 'Records per page: ',));
    $this->validatorSchema['rec_per_page'] = new sfValidatorChoice(array('required' => false, 'choices'=>$recPerPages, 'empty_value'=>strval(sfConfig::get('dw_recPerPage'))));
    $this->hasPager = true;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
      if(isset($this->hasPager) && $this->hasPager)
      {
        if(! isset($taintedValues['rec_per_page']))
        {
          $taintedValues['rec_per_page'] = $this['rec_per_page']->getValue();
        }
      }
      parent::bind($taintedValues, $taintedFiles);
  }

  protected function getDateItemOptions()
  {
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    return array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
    );
  }

  protected function getCatalogueRecLimits()
  {
    return intval(sfConfig::get('dw_catalogueRecLimit'));
  }

  /**
  * Add query for table containing "name" and "name_indexed" columns
  * @param Doctrine_Query $query an existing doctrine query
  * @param string $table a table where we should look at
  * @param string $field The field searched
  * @param string $values The value of the search field
  * @param string $alias The Alias for the searched table if null the root alias is taken
  * @return Doctrine_Query the modified doctrine query
  */
  public function addNamingColumnQuery(Doctrine_Query $query, $table, $field, $values, $alias = null, $flat_field = null)
  {
    $search = self::splitNameQuery($values);
    $terms = self::getAllTerms($search);
    foreach ($search['with'] as $search_term) {
      if(strpos($search_term, '^') === 0) {
        $query->andWhere($alias.'.'.$field." like fulltoindex(?) || '%' ", $search_term);
      }
      $query->andWhere($alias.'.'.$field." like '%' || fulltoindex(?) || '%' ", $search_term);
    }
    unset($search['with']);
    foreach($search as $item) {
      foreach ($item as $search_term) {
        if(strpos($search_term, '^') === 0) {
          $query->andWhere($alias.'.'.$field." not like fulltoindex(?) || '%' ", $search_term);
        }
        $query->andWhere($alias.'.'.$field." not like '%' || fulltoindex(?) || '%' ", $search_term);
      }
    }

  }

  /**
  * Get a searched term and give a string containing all proposition separeted by $separator
  * @param string $term the searched term
  * @param string $proposition the array of propositions fetched in word db
  * @param string $separator the separator between fields
  * @return a string containing all proposition for this term separeted by $separator
  */
  protected static function getWordsForTerms($term, $propositions, $separator = '|' )
  {
    $str = '';
    foreach($propositions as $i => $result)
    {
      if($result['search'] == $term)
      {
        if($str == '')
          $str = $result['word'];
        else
          $str .= $separator.$result['word'];
      }
    }
    if($str == '') $str = $term;
    return $str;
  }

  /**
  * splitNameQuery: Analyse a query string to split elements
  * in remove all non-alphanum char (except - and _ )
  * The array si organized with a 'with' key (all search terms) and other keys represent a 'NOT' search
  * Terms will be trimed and -- is used as not term
  * falco, peregrin (1985) --brolus test -- choze will be exported as:
  * Array ( [with] => Array(
  *          [0] => falco
  *          [1] => peregrin
  *          [2] => 1985)
  *   [0] => Array(
  *          [0] => brolus
  *          [1] => test)
  *   [1] => Array(
  *          [0] => choze)
  * )
  *
  * @param string $query_string The search string
  * @return array an array of analysed terms
  */
  public static function splitNameQuery($query_string)
  {
    $results = preg_split('/--/',$query_string);
    $query_array = array('with'=>'', 'without'=>array());
    foreach($results as $i => $query_part)
    {
      if(trim($query_part) == '')
      continue;

      $query_part = preg_replace('/[\(&\;\,\|\↑\€\←\↓\œ\→\?\.\\\'\"\)\$]/u', ' ', $query_part);

      if($i == 0)
        $query_array['with'] = trim($query_part);
      else
        $query_array['without'][] = trim($query_part);
    }

    $searched_terms = array();
    foreach(array_merge(array('with'=>$query_array['with']),$query_array['without']) as $i => $query_words)
    {
      $words = explode(" ", $query_words);
      $searched_terms[$i] = array();
      foreach($words as $word)
      {
        $word = trim($word);
        if($word == '' || $word == '-') continue;
        $searched_terms[$i][] = $word;
      }
      if(empty($searched_terms[$i]))
        unset($searched_terms[$i]);
    }
    if(!isset($searched_terms['with']))
      $searched_terms['with'] = array();

    return $searched_terms;
  }

  /**
  * Extract query items from the analysed array
  * @param array analysed array with key with and without and query splitted in terms
  * @return array array with all terms searched
  */
  protected static function getAllTerms($analysed_array)
  {
    $terms = array();
    foreach($analysed_array as $key => $analysed)
    {
      foreach($analysed as $query)
      {
      $terms[] = $query;
      }
    }
    return $terms;
  }

  public function addExactDateFromToColumnQuery(Doctrine_Query $query, array $dateFields, $val_from, $val_to)
  {
    if (count($dateFields) > 0) {
      if ($val_from->getMask() > 0)
      {
        $query->andWhere(" CASE WHEN " . $dateFields[0] . " is NULL THEN '01/01/0001' ELSE ".$dateFields[0]." END >= ? ", $val_from->format('d/m/Y'));
      }
      if ($val_to->getMask() > 0)
      {
        $query->andWhere(" CASE WHEN " . $dateFields[1] . " is NULL THEN '31/12/2038' ELSE ".$dateFields[1]." END <= ? ", $val_to->format('d/m/Y'));
      }
    }
    return $query;
  }

  public function addDateFromToColumnQuery(Doctrine_Query $query, array $dateFields, $val_from, $val_to)
  {
    if (count($dateFields) > 0)
    {
      if($val_from->getMask() > 0 && $val_to->getMask() > 0)
      {
        if (count($dateFields) == 1)
        {
          $query->andWhere($dateFields[0] . " Between ? and ? ",
                           array($val_from->format('d/m/Y'),
                                 $val_to->format('d/m/Y')
                                )
                          );
        }
        else
        {
          $query->andWhere("( " . $dateFields[0] . " Between ? AND ? OR ".$dateFields[1] ." Between ? AND ? )", 
            array($val_from->format('d/m/Y'),$val_to->format('d/m/Y'),$val_from->format('d/m/Y'),$val_to->format('d/m/Y')));
        }
      }
      elseif ($val_from->getMask() > 0)
      {
        $sql = " (" . $dateFields[0] . " >= ? AND " . $dateFields[0] . "_mask > 0) ";
        $dateFieldsCount = count($dateFields);
        for ($i = 1; $i <= $dateFieldsCount; $i++)
        {
          $vals[] = $val_from->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " >= ? AND " . $dateFields[1] . "_mask > 0) ";
        $query->andWhere($sql,
                         $vals
                        );
      }
      elseif ($val_to->getMask() > 0)
      {
        $sql = " (" . $dateFields[0] . " <= ? AND " . $dateFields[0] . "_mask > 0) ";
        $dateFieldsCount = count($dateFields);
        for ($i = 1; $i <= $dateFieldsCount; $i++)
        {
          $vals[] = $val_to->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " <= ? AND " . $dateFields[1] . "_mask > 0) ";
        $query->andWhere($sql,
                         $vals
                        );
      }
    }
    return $query;
  }

  public function addLevelColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $levels = array();
       $pul = Doctrine::getTable('PossibleUpperLevels')->findByLevelRef($values)->toArray();
       foreach ($pul as $key=>$val)
       {
         $levels[]=$val['level_upper_ref'];
       }

       if (count($levels)>0)
       {
         $query->andWhereIn('level_ref', $levels);
       }
     }
     return $query;
  }

  public function addCallerIdColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $alias = $query->getRootAlias();
       $query->andWhere($alias.'.id != ?', $values);
     }
     return $query;
  }

  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  public function getCurrentCulture()
  {
    return sfContext::getInstance()->getUser()->getCulture();
  }

  public function getJavascripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/searchForm.js';
    return $javascripts;
  }

  /**
  * return an array of vernacular_name id
  * @param relation the relation concerned (taxonomy, chrono, litho...)
  * @param $val a list of words to include or exclude
  * @return string an list of id separated by ','
  */
  protected function ListIdByWord($relation,$val)
  {
    $q = Doctrine_Query::create()
      ->select('v.record_id')
      ->from('VernacularNames v')
      ->andWhere('v.referenced_relation = ?', $relation);

    $this->addNamingColumnQuery($q, 'vernacular_names', 'name_indexed', $val, 'v');
    $results = $q->execute();
    $list = "" ;

    foreach($results as $key=>$result)
      $list .= $result->getRecordId()."," ;

    if($list == "") return (-1) ;
    return (substr($list,0,strlen($list)-1)) ; //return list of id without the last ','
  }


  public function addRelationItemColumnQuery($query, $values)
  {
    $relation = $values['relation'];
    $val= $values['item_ref'];
    if($val != 0)
    {
      if($relation == 'child')
      {
        $item  = $this->getTable()->find($val);
        $query->andWhere("path like ?", $item->getPath().''.$item->getId().'/%');
      }
      elseif($relation == 'direct_child')
      {
        $query->andWhere("parent_ref = ?",$val);
      }
      elseif($relation =='synonym')
      {
        $synonyms = Doctrine::getTable('ClassificationSynonymies')->findSynonymsIds($this->getTable()->getTableName(), $val);
        if(empty($synonyms))
          $query->andWhere('0=1'); //False
        $query->andWhereIn("id",$synonyms)
          ->andWhere('id != ?',$val); // remove himself
      }
    }
    return $query ;
  }

  public function addCatalogueRelationColumnQuery($query, $item_ref, $relation, $table, $field_prefix, $parent_syn_included = false)
  {
    if($item_ref != 0)
    {

      // Put the item_ref passed into an array
      $items = array($item_ref);
      // Initialize the where clause string and array of parameters for the
      // relation 'child'
      $whereClause = '';
      $whereClauseParams = array();

      // If we've got to include also the synonyms of the item_ref passed...
      if (($relation == 'child' || $relation == 'direct_child') && $parent_syn_included === true ) {
        // Get the list of synonyms ids
        $synonyms = Doctrine::getTable('ClassificationSynonymies')->findSynonymsIds($table, $item_ref);
        // If there are synonyms...
        if (count($synonyms) != 0) {
          // merge the result with the initialized array
          $items = array_unique(array_merge($items, $synonyms));
        }
      }

      if($relation == 'equal')
      {
        $query->andWhere($field_prefix."_ref = ?", $item_ref);
      }
      elseif($relation == 'child')
      {
        $list_of_items = implode(',',$items);
        $item  = Doctrine::getTable($table)->findBySql("id = ANY('{ $list_of_items }' :: int[])");
        foreach ($item as $element) {
          $whereClause .= "OR ${field_prefix}_path like ? ";
          $whereClauseParams[] = $element->getPath().$element->getId().'/%';
        }
        if ( $whereClause != '') {
          $whereClause = ltrim($whereClause, 'OR');
          $query->andWhere($whereClause, $whereClauseParams);
        }
      }
      elseif($relation == 'direct_child')
      {
        $query->andWhereIn($field_prefix."_parent_ref",$items);
      }
      elseif($relation =='synonym')
      {
        $synonyms = Doctrine::getTable('ClassificationSynonymies')->findSynonymsIds($table, $item_ref);
        if(empty($synonyms))
          $query->andWhere('0=1'); //False
        $query->andWhereIn($field_prefix."_ref",$synonyms)
          ->andWhere($field_prefix."_ref != ?",$item_ref); // remove himself
      }
    }
    return $query ;
  }

  public static function getCollectionWithRights($user, $with_writing=false)
  {
      if($user->isA(Users::ADMIN))
      {
        $res = array(0=>0);
        $results = Doctrine_Query::create()
          ->select('id')
          ->from('Collections')->fetchArray();
        foreach($results as $row)
        {
          $res[] = $row['id'];
        }
        return $res;
      }
      $conn = Doctrine_Manager::connection();
      $sql = "SELECT collection_ref from collections_rights where user_ref = :userid ";
      if($with_writing === false)
        $sql .= "UNION select id as collection_ref from collections where is_public = true";
      else
        $sql .= " and db_user_type >= 2";

      $q = $conn->prepare($sql);
      $q->execute(array(':userid' => $user->getId()));
      $colls = $q->fetchAll();
      $results = array(0=>0);
      foreach($colls as $col)
      {
        $results[] = $col[0];
      }
      return $results;
  }
}
