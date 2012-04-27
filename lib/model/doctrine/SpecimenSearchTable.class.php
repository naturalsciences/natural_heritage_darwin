<?php


class SpecimenSearchTable extends Doctrine_Table
{
    protected static $widget_flat_array = array(
      'collection_ref' => 'refCollection',
      'gtu_ref' => 'refGtu' ,
      'gtu_code' => 'refGtu' ,
      'gtu_from_date' => 'refGtu' ,
      'gtu_to_date' => 'refGtu' ,
      'gtu_tag_values_indexed' => 'refGtu' ,
      'Tags' => 'refGtu' ,
      'taxon_name' => 'refTaxon' ,
      'taxon_level_ref' => 'refTaxon' ,
      'litho_name' => 'refLitho' ,
      'litho_level_ref' => 'refLitho' ,
      'litho_level_name' => 'refLitho' ,
      'chrono_name' => 'refChrono' ,
      'chrono_level_ref' => 'refChrono' ,
      'chrono_level_name' => 'refChrono' ,
      'lithology_name' => 'refLithology' ,
      'lithology_level_ref' => 'refLithology' ,
      'lithology_level_name' => 'refLithology' ,
      'mineral_name' => 'refMineral' ,
      'mineral_level_ref' => 'refMineral',
      'mineral_level_name' => 'refMineral',
      'Codes' => 'codes',
      'sex' => 'sex',
      'stage' => 'stage',
      'status' => 'status',
      'social' => 'social',
      'rockform' => 'rockform',
      'tools' => 'tools',
      'methods' => 'methods',
      'building' => 'localisation',
      'floor' => 'localisation',
      'room' => 'localisation',
      'row' => 'localisation',
      'shelf' => 'localisation',
      'container' => 'container',
      'sub_container' => 'container',
    );

    public static function getInstance()
    {
        return Doctrine_Core::getTable('SpecimenSearch');
    }

    public function getRecordByRef($ref = "spec_ref", $id)
    {
      $q = Doctrine_Query::create()
        ->from('SpecimenSearch s')
        ->where('s.'.$ref.' = ?',$id) ;
      return $q->fetchOne() ;
    }
    
    public function getRequiredWidget($criterias, $user, $category)
    {
      $req_widget = array() ;
      foreach($criterias as $key => $fields)
      {
        if ($key == "rec_per_page") continue ;
        if($key == "gtu_from_date" && $fields == array('day'=>'','month'=>'','year'=>'')) continue;
        if($key == "gtu_to_date" && $fields == array('day'=>'','month'=>'','year'=>'')) continue;
        if ($fields == "") continue ;
        if(isset(self::$widget_flat_array[$key]))
        { 
          $req_widget[self::$widget_flat_array[$key]] = 1 ;
        }
      }
      Doctrine::getTable('MyWidgets')->forceWidgetOpened($user, $category ,array_keys($req_widget));
    }

    /**
    * Fetch all specimens by an array of ids
    * @param array $ids Ids of specimen to search
    * @return Doctrine_collection
    */
    public function getByMultipleIds(array $ids, $type = "specimen", $user_id = -1, $is_admin = false)
    {
      if( empty($ids))
        return $ids;

      if($type == 'specimen')
      {
        $q = Doctrine_Query::create()
        ->select('s.spec_ref, s.taxon_name')
        ->from('SpecimenSearch s')
        ->wherein('s.spec_ref', $ids)
        ->groupBy('s.spec_ref, s.taxon_name')
        ->orderBy('spec_ref');
      }
      elseif($type == 'individual')
      {
        $q = Doctrine_Query::create()
        ->select('s.spec_ref, s.individual_ref, s.taxon_name')
        ->from('IndividualSearch s')
        ->wherein('s.individual_ref', $ids)
        ->groupBy('s.spec_ref, s.individual_ref, s.taxon_name')
        ->orderBy('spec_ref,individual_ref');
      }
      elseif($type == 'part')
      {
        $q = Doctrine_Query::create()
        ->select('s.id, s.spec_ref, s.individual_ref, s.part_ref, s.taxon_name')
        ->from('PartSearch s')
        ->wherein('s.part_ref', $ids)
        ->groupBy('s.spec_ref, s.individual_ref, s.part_ref, s.taxon_name, s.id')
        ->orderBy('spec_ref, individual_ref, part_ref');  
      }
      else return array(); //Error
      //if(!$is_admin)
      //  $q->andWhere('s.collection_ref in (select fct_search_authorized_encoding_collections(?))',$user_id);
      return $q->execute();
    }


    public static function getFieldsByType($db_user_type = null)
    {
      $fields = array('specimens' =>
        array(
          'spec_ref',
          'category',
          'collection_ref',
//           'collection_code',
          'collection_name',
//           'collection_is_public',
//           'collection_institution_ref',
//           'collection_institution_formated_name',
//           'collection_institution_formated_name_ts',
//           'collection_institution_formated_name_indexed',
//           'collection_institution_sub_type',
//           'collection_main_manager_ref',
//           'collection_main_manager_formated_name',
//           'collection_main_manager_formated_name_ts',
//           'collection_main_manager_formated_name_indexed',
//           'collection_parent_ref',
//           'collection_path',
          'expedition_ref',
          'expedition_name',
//           'expedition_name_ts',
//           'expedition_name_indexed',
          'station_visible',
          'gtu_ref',
          'gtu_code',
//           'gtu_parent_ref',
//           'gtu_path',
//           'gtu_from_date_mask',
//           'gtu_from_date',
//           'gtu_to_date_mask',
//           'gtu_to_date',
          'gtu_tag_values_indexed',
          'gtu_country_tag_value',
          'gtu_elevation',
          'gtu_elevation_accuracy',
          'taxon_ref',
          'taxon_name',
//           'taxon_name_indexed',
//           'taxon_name_order_by',
//           'taxon_level_ref',
//           'taxon_level_name',
//           'taxon_status',
//           'taxon_path',
//           'taxon_parent_ref',
//           'taxon_extinct',
          'chrono_ref',
          'chrono_name',
//           'chrono_name_indexed',
//           'chrono_name_order_by',
//           'chrono_level_ref',
//           'chrono_level_name',
//           'chrono_status',
//           'chrono_path',
//           'chrono_parent_ref',
          'litho_ref',
          'litho_name',
//           'litho_name_indexed',
//           'litho_name_order_by',
//           'litho_level_ref',
//           'litho_level_name',
//           'litho_status',
//           'litho_path',
//           'litho_parent_ref',
          'lithology_ref',
          'lithology_name',
//           'lithology_name_indexed',
//           'lithology_name_order_by',
//           'lithology_level_ref',
//           'lithology_level_name',
//           'lithology_status',
//           'lithology_path',
//           'lithology_parent_ref',
          'mineral_ref',
          'mineral_name',
//           'mineral_name_indexed',
//           'mineral_name_order_by',
//           'mineral_level_ref',
//           'mineral_level_name',
//           'mineral_status',
//           'mineral_path',
//           'mineral_parent_ref',
//           'host_taxon_ref',
//           'host_taxon_name',
//           'host_taxon_name_indexed',
//           'host_taxon_name_order_by',
//           'host_taxon_level_ref',
//           'host_taxon_level_name',
//           'host_taxon_status',
//           'host_taxon_path',
//           'host_taxon_parent_ref',
//           'host_taxon_extinct',
         'ig_ref',
         'ig_num',
//           'ig_num_indexed',
//           'ig_date_mask',
//           'ig_date',
          'acquisition_category',
//           'acquisition_date_mask',
//           'acquisition_date',
          'with_types',
          'with_individuals',
        ),
        'individuals' => array(
          'individual_ref',
          'individual_type',
          'individual_type_group',
          'individual_type_search',
          'individual_sex',
          'individual_state',
          'individual_stage',
          'individual_social_status',
          'individual_rock_form',
          'individual_count_min',
          'individual_count_max',
          'with_parts',
        )
      );
      if ($db_user_type == null || $db_user_type == Users::REGISTERED_USER)
        $fields['parts'] = array(
          'part_ref',
          'part',
          'part_status',
//          'part_code',
          'part_count_min',
          'part_count_max') ;
      else 
       $fields['parts'] = array(
          'part_ref',
          'part',
          'part_status',
          'building',
          'floor',
          'room',
          'row',
          'shelf',
          'container_type',
          'container_storage',
          'container',
          'sub_container_type',
          'sub_container_storage',
          'sub_container',
          'part_count_min',
          'part_count_max',
        ) ;
      return $fields;
    }

    public function getRandomSpecimens($nbr=5)
    {
      $q = Doctrine_Query::create()
        ->from('SpecimenSearch s')
        ->where('s.collection_is_public=true')
        ->orderBy('random()')
        ->limit($nbr)
        ->useResultCache(new Doctrine_Cache_Apc())
        ->setResultCacheLifeSpan(60 * 60 * 24); //1 day
      return $q->execute();
    }
    
    public function getSpecIndivCount()
    {
      $q = Doctrine_Query::create()
        ->select('count(individual_ref)')
        ->from('SpecimenSearch s')
        ->useResultCache(new Doctrine_Cache_Apc())
        ->setResultCacheLifeSpan(60 * 60 * 24); //1 day
      $result = $q->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
      return $result;
    }
}
