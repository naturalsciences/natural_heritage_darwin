<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TagGroupsTable extends DarwinTable
{
  public function getDistinctSubGroups($group)
  {
    if(is_null($group))
      $q = $this->createFlatDistinct('tag_groups', 'sub_group_name', 'sgn');
    else
      $q = $this->createFlatDistinctDepend('tag_groups', 'sub_group_name', $group, 'sgn');
    
    $a = DarwinTable::CollectionToArray($q->execute(), 'sgn');
    return array_merge(array(''=>''),$a);
	}

  /**
   * For the list of tags given by user, sends a new list of suggested tags that
   * are (directly or indirectly) associated to given ones. This can enhance the
   * search results
   * @param $value List of tags provided by user
   * @param string $group The tag group concerned if of application
   * @param string $sub_group The tag sub group concerned if of application
   * @return array The list of tags suggestions proposed to user
   */
  public function getPropositions($value, $group="", $sub_group="")
  {
    $conn = Doctrine_Manager::connection();
    $tags_display_limit = intval(sfConfig::get('tagsDisplayLimit', '10'));
    $limit = " LIMIT $tags_display_limit ";
    $tags = $conn->quote($value, 'string');
    $q_group = $conn->quote($group, 'string');
    $grouping_clause = " group_type = $q_group";
    $q_sub_group = $conn->quote($sub_group, 'string');
    $sub_grouping_clause = " AND sub_group_type = $q_sub_group";

    $sql = "SELECT min (tag) as tag,
                   count(*) as cnt ,
                   1 as precision
            FROM (
                    SELECT group_ref
                    FROM tags
                    WHERE tag_indexed IN
                    (
                      SELECT DISTINCT(fulltoIndex(tags)) as u_tag
                      FROM regexp_split_to_table($tags, ';') as tags
                      WHERE fulltoIndex(tags) != ''
                    )
                  ) as x
            INNER JOIN tags t
            ON t.group_ref = x.group_ref
            WHERE tag_indexed NOT IN
            (
              SELECT distinct(fulltoIndex(tags)) as u_tag 
               FROM regexp_split_to_table($tags, ';') as tags 
               WHERE fulltoIndex(tags) != ''
            ) ";

    if($group !="")
      $sql .= " AND $grouping_clause ";
    if($sub_group != "")
      $sql .= $sub_grouping_clause;

    $sql .= " GROUP BY tag_indexed
              ORDER BY cnt desc $limit ";
    $result = $conn->fetchAssoc($sql);

    $max = 0;
    $min = 0;
    $nbr_of_steps= 5;

    foreach($result as $i => $item)
    {
      if($max < $item['cnt'])
        $max = $item['cnt'];
      if($min > $item['cnt'])
        $min = $item['cnt'];
    }
    $step = ($max - $min) / $nbr_of_steps;
    foreach($result as $i => $item)
    {
      $value .= ';'.$item['tag'];
      $result[$i]['size'] = round($item['cnt'] / $step);
    }
    $tags = $conn->quote($value, 'string');

    $tags_lower_limit_for_searching_others = intval(sfConfig::get('tagsLowerLimit','4'));

    if (count($result) < $tags_lower_limit_for_searching_others)
    {
      if( $group != "" &&  $sub_group != "")
      {
        $sql = "SELECT min(tag) as tag,
                       min(similarity(x.tag, tagsi)) as sims,
                       2 as size
                FROM
                  (
                    SELECT DISTINCT(tag) as tag
                    FROM tags
                    WHERE $grouping_clause $sub_grouping_clause
                  ) as x
                  INNER JOIN
                  (
                    SELECT trim(tagsi) as tagsi
                    FROM regexp_split_to_table($tags, ';') AS tagsi
                  ) as y
                  ON x.tag % y.tagsi
                WHERE
                  fulltoindex(x.tag) NOT IN (
                                              SELECT DISTINCT(fulltoIndex(tags)) AS u_tag
                                              FROM
                                              regexp_split_to_table($tags, ';') AS tags
                                              WHERE fulltoIndex(tags) != ''
                                            )
                GROUP BY  fulltoindex(tag)
                ORDER BY sims desc $limit " ;
      }
      else
      {
        $sql = "SELECT tag,
                       similarity(tag, u_tags) as sims
                FROM tags as t
                INNER JOIN
                (
                  SELECT DISTINCT (tagsi) as u_tags
                  FROM regexp_split_to_table($tags, ';') as tagsi
                  WHERE fulltoIndex(tagsi) != ''
                ) as taglist
                ON t.tag % u_tags
                WHERE tag_indexed NOT IN (
                                            SELECT distinct(fulltoIndex(tags)) as u_tag
                                            FROM regexp_split_to_table($tags, ';') as tags
                                            WHERE fulltoIndex(tags) != ''
                                         )";
        if($group !="")
          $sql .= 'AND '.$grouping_clause;
        if($sub_group != "")
          $sql .= $sub_grouping_clause;
        $sql .= " ORDER BY similarity(tag, u_tags) desc, tag asc";

        $sql = "SELECT tag, 2 as size
                FROM ( $sql ) as subquery
                GROUP BY tag
                ORDER BY min(sims) desc $limit ";
      }
      $fuzzyResults = $conn->fetchAssoc($sql);
      $result = array_merge($result, $fuzzyResults);
    }
    
    return $result;
  }

  public function fetchTag($ids)
  {
    $q = Doctrine_Query::create()
         ->from('TagGroups g')
         ->innerJoin('g.Tags t')
         ->andWherein('g.gtu_ref', $ids);
    $r = $q->execute();
    $results = array();
    foreach($r as $i)
    {
      if(!isset($results[$i->getGtuRef()]))
        $results[$i->getGtuRef()] = array();

      $results[$i->getGtuRef()][] = $i;
    }
    return $results;
  }

  public function fetchByGtuRefs($ids)
  {
    if(empty($ids)) return array();
    $q = Doctrine_Query::create()
         ->from('TagGroups g')
         ->andWherein('g.gtu_ref', $ids);
     return  $q->execute();
  }
  

  public function getByIdGroup($id, $group, $sub_group)
  {
	  $q = Doctrine_Query::create()
         ->from('TagGroups g')
         ->where('g.gtu_ref=?', $id)
		 ->andWhere('group_name=?', $group)
		 ->andWhere('sub_group_name=?', $sub_group);
     return  $q->execute();
  }

}
