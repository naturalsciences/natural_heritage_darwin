<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class MySavedSearchesTable extends DarwinTable
{
  public function addUserOrder(Doctrine_Query $q = null,$user)
  {
    if (is_null($q))
    {
        $q = Doctrine_Query::create()
            ->from('MySavedSearches s');
    }
    $alias = $q->getRootAlias();
    $q->andWhere($alias . '.user_ref = ?', $user)
        ->orderBy($alias . '.favorite DESC');
    return $q;
  }

  public function addIsSearch(Doctrine_Query $q, $is_search = false)
  {
    $q->andWhere($q->getRootAlias() . '.is_only_id = ?', !$is_search);
    return $q;
  }
  
  public function getSavedSearchByKey($id, $user )
  {
    return $this->addUserOrder(null, $user)
      ->andWhere('id = ?', $id )
      ->fetchOne();
  }

  public function fetchSearch($user_ref, $num_per_page)
  {
    $q = $this->addUserOrder(null,$user_ref);
    $this->addIsSearch($q, true);
    $q->limit($num_per_page);

    return $q->execute();
  }

  public function fetchSpecimens($user_ref, $num_per_page)
  {
    $q = $this->addUserOrder(null,$user_ref);
    $this->addIsSearch($q, false);
    $q->limit($num_per_page);

    return $q->execute();
  }

  public function getListFor($user, $source)
  {
    $q = $this->addUserOrder(null,$user);
    $this->addIsSearch($q, false);
    return $q->andWhere('subject = ?',$source)->execute();
  }

  public function getAllFields($source, $is_reg_user = false)
  {
    $columns = array(
      'category'=>'Category',
      'collection'=>'Collection',
      'taxon'=>'Taxon',
      'type'=>'Type',
      'gtu'=>'Sampling Location',
      'codes'=>'Codes',
	  //these fields added ftheeten 2016 01 11
	  'col_peoples' => 'Collectors',
	  'ident_peoples' => 'Identifiers',
	  'don_peoples' => 'Donators',
	  //
      'chrono'=>'Chronostratigraphy',
      'ig'=>'Inv. General',
      'litho'=>'Lithostratigraphy',
      'lithologic'=>'Lithology',
      'expedition'=>'Expedition',
      'mineral'=>'Mineralogy',
      'count'=>'Count',
      'acquisition_category' => 'Acquisition category',

      'individual_type' => 'Type',
      'sex' => 'Sex',
	  //these two fields added ftheeten 2015/03/31
	  'amount_males'=> 'Amount males',
	  'amount_females'=> 'Amount females',
	  //following field added JMHerpers 2018/01/29
	  'amount_juveniles'=> 'Amount juveniles',
	  //end addition
       //this field added ftheeten 2016/09/13
	  'collecting_dates'=> 'Collecting dates',
      //this field added ftheeten 2016/09/13
	  'ecology'=> 'Ecology',
	  //end addition
	  'state' => 'State',
      'stage'=> 'Stage',
      'social_status' =>'Social Status',
      'rock_form'=>'Rock Form',

      'part'=>'Part',
      'part_status'=>'Part Status',
      'object_name' => 'Object name',
      'building'=>'Building',
      'floor'=>'Floor',
      'room'=>'Room',
      'row'=>'Row',
      'shelf'=>'Shelf',
      'container'=>'Container',
      'container_type'=>'Container Type',
      'container_storage'=>'Container Storage',
      'sub_container'=>'Sub Container',
      'sub_container_type'=>'Sub Container Type',
      'sub_container_storage'=>'Sub Container Storage',
      'specimen_count'=> 'Count',
      //ftheeten 2017 02 08
      'valid_label'=>'Valid label',
    );

    return $columns;
  }
}
