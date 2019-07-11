<?php
/*
 * Source : http://danielfamily.com/techblog/?p=37
 * This is a simple short-hand wrapper for Doctrine_Query. It provides
 * a shorter class name and a few additional functions.
 */
class DQ extends Doctrine_Query
{
  /**
   * Returns a DQ object to get started
   *
   * @return DQ
   */
  public static function create($conn = null,$class = null) {
    return new DQ($conn,$class);
  }

  /**
   * This function will wrap the current dql where statement
   * in parenthesis. This allows more complex dql statements
   * It can be called multiple times during the creation of the dql
   * where clause.
   *
   * @return $this
   */
  public function whereParenWrap() {
    $where = $this->_dqlParts['where'];
    $where_count = count($where);
    if ($where_count == 1) {
      array_unshift($where, '(');
      array_push($where, ')');
    }
    elseif ($where_count > 1) {
      $where_first_part = array_slice($where, 0, $where_count-1,true);
      $where_last_part = array(end($where));
      $where = array_merge($where_first_part,array('('),$where_last_part,array(')'));
    }
    $this->_dqlParts['where'] = $where;

    return $this;
  }            

  /**
   * Create and andWhere if the where parameter is not empty
   *
   * @param string $where where string
   * @param mixed[] $params parameters
   *
   * @return DQ this object
   */
  public function andWhereIf($where, $params = array()) {
    return empty($where)
      ? $this
      : $this->andWhere($where, $params);
  }

  /**
   * Create and orWhere if the where parameter is not empty
   *
   * @param string $where where string
   * @param mixed[] $params parameters
   *
   * @return DQ this object
   */
  public function orWhereIf($where, $params = array()) {
    return empty($where)
      ? $this
      : $this->orWhere($where, $params);
  }

}
