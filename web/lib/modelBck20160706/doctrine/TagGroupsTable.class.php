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

  public function getPropositions($value, $group="", $sub_group="")
  {
    $conn = Doctrine_Manager::connection();
    $limit = "Limit 10 ";
    $tags = $conn->quote($value, 'string');
    $q_group = $conn->quote($group, 'string');
    $grouping_clause = " group_type = $q_group";
    $q_sub_group = $conn->quote($sub_group, 'string');
    $sub_grouping_clause = " AND sub_group_type = $q_sub_group";

    $sql = "select min (tag) as tag, count(*) as cnt , 1 as precision FROM (SELECT group_ref 
                 FROM tags 
                 WHERE tag_indexed IN 
                  (SELECT distinct(fulltoIndex(tags)) as u_tag 
                   FROM regexp_split_to_table($tags, ';') as tags 
                   WHERE fulltoIndex(tags) != ''
                  ) ) as x inner join tags t 
      on t.group_ref = x.group_ref
      WHERE tag_indexed NOT IN 
              (
              SELECT distinct(fulltoIndex(tags)) as u_tag 
               FROM regexp_split_to_table($tags, ';') as tags 
               WHERE fulltoIndex(tags) != ''
              ) ";


    if($group !="")
      $sql .= ' AND '.$grouping_clause;
    if($sub_group != "")
      $sql .= $sub_grouping_clause;

    $sql .= " group by tag_indexed order by cnt desc LIMIT 10";
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
    /* @TODO: Modifiy this hard coded value to use an application parameter instead*/
    if (count($result) < 4)
    {
      if( $group != "" &&  $sub_group != "")
      {
        $sql = "select min(tag) as tag, min(similarity(x.tag, tagsi)) as sims, 2 as size from
          (select distinct(tag) as tag from tags 
            where ". $grouping_clause.$sub_grouping_clause." ) as x
          inner join (select trim(tagsi) as tagsi FROM regexp_split_to_table($tags, ';') AS tagsi) as y
            on x.tag % y.tagsi
          WHERE
            fulltoindex(x.tag) NOT IN (SELECT DISTINCT(fulltoIndex(tags)) AS u_tag FROM
              regexp_split_to_table($tags, ';') AS tags WHERE fulltoIndex(tags) != '' )
         GROUP BY  fulltoindex(tag)
         ORDER BY sims desc ".$limit ;
      }
      else
      {
        $sql = "select tag, similarity(tag, u_tags) as sims
                from tags as t inner join 
                    (select distinct (tagsi) as u_tags
                      from regexp_split_to_table($tags, ';') as tagsi
                      where fulltoIndex(tagsi) != ''
                    ) as taglist on t.tag % u_tags
                where tag_indexed NOT IN 
                (SELECT distinct(fulltoIndex(tags)) as u_tag 
                FROM regexp_split_to_table($tags, ';') as tags 
                WHERE fulltoIndex(tags) != ''
                )";

        if($group !="")
          $sql .= 'AND '.$grouping_clause;
        if($sub_group != "")
          $sql .= $sub_grouping_clause;
        $sql .= " ORDER BY similarity(tag, u_tags) desc, tag asc";

        $sql = "select tag, 2 as size
                from (" .$sql.") as subquery group by tag order by min(sims) desc ".$limit;
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

}