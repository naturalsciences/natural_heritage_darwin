<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GtuTable extends DarwinTable
{
  /* function witch return an array of countries sorted by id
    @ListId an array of Id */    
  public function getCountries($listId)
  {
    $q = Doctrine_Query::create()->
       from('TagGroups t')->  
       innerJoin('t.Gtu g')->
       orderBy('g.id')->
       AndWhere('t.sub_group_name = ?','country')->
       WhereIn('g.id',$listId);
       
   $result = $q->execute() ;
   $countries = array() ;
   foreach($result as $tag)
   {
      $str = '<ul class="country_tags">';
      $tags = explode(";",$tag->getTagValue());
      foreach($tags as $value)
        if (strlen($value))
          $str .=  '<li>' . trim($value).'</li>';
      $str .= '</ul><div class="clear" />';
      $countries[$tag->getGtuRef()] = $str ;
   }
   return $countries ;     
  }
}
