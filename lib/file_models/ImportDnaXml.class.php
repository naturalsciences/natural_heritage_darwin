<?php
class ImportDnaXml implements IImportModels
{
  private $simpleArrayField = array(
    'category' => 'category',
    'level' => 'level',
    'sex' => 'individual_sex',
    'type' => 'individual_type',
    'stage' => 'individual_stage',
    'specimen_part' => 'part',
    'expedition_name' => 'expedition_name',
    'expedition_from_date' => 'expedition_from_date',
    'expedition_from_date_mask' => 'expedition_from_date_mask',
    'expedition_to_date' => 'expedition_to_date',
    'expedition_to_date_mask' => 'expedition_to_date_mask',
    'gtu_code' => 'gtu_code',
    'gtu_from_date' => 'gtu_from_date',
    'gtu_from_date_mask' => 'gtu_from_date_mask',
    'latitude' => 'gtu_latitude',
    'longitude' => 'gtu_longitude',
    'lat_long_accuracy' => 'gtu_lat_long_accuracy',
    'elevation_value' => 'gtu_elevation',
    'elevation_accuracy' => 'gtu_elevation_accuracy',
    'shelf' => 'shelf',
    'container_type' => 'container_type',
    'container_storage' => 'container_storage',
    'container' => 'container',
    'sub_container_type' => 'sub_container_type',
    'sub_container_storage' => 'sub_container_storage',
    'sub_container' => 'sub_container',
    'group_name' => 'group_name',
    'sub_group_name' => 'sub_group_name',
    'tag_value' => 'tag_value',
    'property_type' => 'property_type',
    'date_from' => 'date_from',
    'date_from_mask' => 'date_from_mask',
    'date_to' => 'date_to',
    'date_to_mask' => 'date_to_mask',
    'sub_type' => 'property_sub_type',
    'property_qualifier' => 'property_qualifier',
    'property_unit' => 'property_unit',
    'property_method' => 'property_method',
    'property_tool' => 'property_tool',
    'code_prefix' => 'code_prefix',
    'code_prefix_separator' => 'code_prefix_separator',
    'code_suffix' => 'code_suffix',
    'code_suffix_separator' => 'code_suffix_separator',
    'code_date' => 'code_date',
    'code_date_mask' => 'code_date_mask',
    'part_count_min' => 'part_count_min',
    'part_count_max' => 'part_count_max',
    'family_name' => 'institution_name',
    'acquisition_category' => 'acquisition_category',
    'acquisition_date_mask' => 'acquisition_date_mask',
    'acquisition_date' => 'acquisition_date',
    'ig_num' => 'ig_num',
    'ig_date' => 'ig_date',
    'ig_date_mask' => 'ig_date_mask',
  ) ;

  public function parseFile($file,$id)
  {
    $xml = new DOMDocument();
    $xml->load($file) ;
    $this->import_id = $id ;
    $msgs = $xml->documentElement;
    $this->createAndsaveStaging($msgs);
  }

  protected function createAndSaveStaging($msgs,$id=null)
  {
    if(!$msgs->childNodes->length) return ;
    foreach ($msgs->childNodes as $msg)
    {

      if($msg->nodeName != "collection_object") continue;
      $this->complex_nodes = array();
      $object = new Staging() ;
      $object['parent_ref'] = $id ;
      $object['import_ref'] = $this->import_id ;
      $this->fillSimpleObject($msg,$object) ;
      $object->save() ;
      $this->parseAndAdd($object->getId()) ;
    }

  }

  protected function parseAndAdd($id)
  {
    $array = $this->complex_nodes ;
    foreach($array as $key => $node)
    {
      if($key == 'collection_objects') $this->createAndSaveStaging($node,$id) ;
      elseif($key == 'codes') $this->processWithCodesNode($array[$key],$id);
      elseif($key == 'identifications') $this->processWithIdentificationsNode($array[$key],$id);
      elseif($key == 'comments') $this->processWithCommentsNode($array[$key],$id);
      elseif($key == 'properties') $this->processWithPropertiesNode($array[$key],$id);
      elseif($key == 'tag_groups') $this->processWithTagGroupsNode($array[$key],$id);
      elseif($key == 'collectors') $this->processWithCollectorsNode($array[$key],$id);
      elseif($key == 'donators') $this->processWithDonatorsNode($array[$key],$id);
      else throw new Exception('Unknown node '.$key);
    }
  }

  // this function fill the Staging Object with simple tag, all complex node are saved into an array to be proceeded afted
  // gtu, expedition, donators and collectors are fields found in the staging table, so we don't need the staging id a we can handle then directly
  // other tags need the staging id, so I store then in an array to deal with then after the saving
  protected function fillSimpleObject($nodes,$object)
  {
    foreach ($nodes->childNodes as $childNode)
    {
      // text node doesn't interest us
      if($childNode->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if($childNode->childNodes->length == 1) $this->getSimpleField($childNode,$object);
      elseif($childNode->nodeName == 'gtu') $this->processWithGtuNode($childNode,$object);
      elseif($childNode->nodeName == 'expedition') $this->fillSimpleObject($childNode,$object);
      elseif($childNode->nodeName == 'acquisition') $this->fillSimpleObject($childNode,$object);
      elseif($childNode->nodeName == 'ig') $this->fillSimpleObject($childNode,$object);
      elseif($childNode->nodeName == 'institution') $this->fillSimpleObject($childNode,$object);
      elseif($childNode->nodeName == 'taxon') $this->processWithTaxonNode($childNode,$object);
      elseif($childNode->nodeName == 'chrono') $this->processWithChronoNode($childNode,$object);
      elseif($childNode->nodeName == 'litho') $this->processWithLithoNode($childNode,$object);
      elseif($childNode->nodeName == 'lithology') $this->processWithLithologyNode($childNode,$object);
      elseif($childNode->nodeName == 'mineralogy') $this->processWithMineralogyNode($childNode,$object);
      else $this->complex_nodes[$childNode->nodeName] = $childNode ;
    }
  }

  // function used to get an XML tag and return the associated field in the Staging table
  protected function getSimpleField($xml_node, $object)
  {
    if(isset($this->simpleArrayField[$xml_node->nodeName])) $object[$this->simpleArrayField[$xml_node->nodeName]] = $xml_node->nodeValue ;
  }

  /**
   * This function fill all gtu fields from Staging
   * if a tag_groups tag is found, we store it and deal with it when the staging is saved and the staging id is known
   */
  public function processWithGtuNode($xml_node,$object)
  {
    foreach ($xml_node->childNodes as $gtu_node)
    {
      // text node doesn't interest us
      if($gtu_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if($gtu_node->childNodes->length == 1) $this->getSimpleField($gtu_node,$object);
      else
      {
        if($gtu_node->nodeName == 'tag_groups')
        {
          $this->complex_nodes['tag_groups'] = $gtu_node ;
        }
        elseif($gtu_node->nodeName == 'comments')
        {
          $this->complex_nodes['comments']['node'] = $gtu_node ;
          $this->complex_nodes['comments']['notion_concerned'] = 'gtu' ;
        }
        elseif($gtu_node->nodeName == 'properties')
        {
          $this->complex_nodes['properties']['node'] = $gtu_node ;
          $this->complex_nodes['properties']['notion_concerned'] = 'gtu' ;
        }
        elseif($gtu_node->nodeName == 'lat_long' || $gtu_node->nodeName == 'elevation') $this->fillSimpleObject($gtu_node,$object) ;
        else throw new Exception('Unknown node '.$gtu_node->nodeName.' in gtu');
      }
    }
  }

  /**
   * This function create and tag groups found in the staging_tag_group table
   */
  public function processWithTagGroupsNode($tag_groups,$id)
  {
    $tags = $tag_groups->getElementsByTagName("tag_group");
    foreach($tags as $tags_infos)
    {
      $tag = new StagingTagGroups() ;
      $tag['staging_ref'] = $id ;
      $this->fillSimpleObject($tags_infos,$tag) ;
      $tag->save() ;
    }
  }

  /**
   * This function fill the collector field from Staging
   * $collectors is defined to be used as an array in the db with all collectors separated by a ','
   */
  public function processWithCollectorsNode($xml_node,$id)
  {
    $collector_node = $xml_node->getElementsByTagName("collector");
    $order_by = 0 ;
    foreach ($collector_node as $collector)
    {
      if($collector->nodeName == "#text") continue;
      $people = new stagingPeople();
      $people->fromArray(array('people_type' => 'collector', 'record_id' => $id, 'referenced_relation' => 'staging','order_by' => $order_by++));
      $people['formated_name'] = $collector->getElementsByTagName("formated_name")->item(0)->nodeValue;
      $people->save() ;
    }
  }


   /**
   * This function fill all taxon fields from Staging
   * $taxon_parent may containt all field referenced in $array_level separated by ","
   */
  public function processWithTaxonNode($xml_node,$object)
  {
    $taxon_parent = new Hstore();
    $array_level = array("phylum","class","order","family","genus","sub_genus","species","sub_species","sub_family") ;
    foreach ($xml_node->childNodes as $taxon_node)
    {
      // text node doesn't interest us
      if($taxon_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if(in_array($taxon_node->nodeName,$array_level)) $taxon_parent[$taxon_node->nodeName] = $taxon_node->nodeValue;
      elseif($taxon_node->nodeName == 'name') $object['taxon_name'] = $taxon_node->nodeValue ;
      elseif($taxon_node->nodeName == 'level') $object['taxon_level_name'] = $taxon_node->nodeValue ;
      elseif($taxon_node->nodeName == 'comments')
      {
        $this->complex_nodes['comments']['node'] = $taxon_node ;
        $this->complex_nodes['comments']['notion_concerned'] = 'taxon' ;
      }
      elseif($taxon_node->nodeName == 'properties')
      {
        $this->complex_nodes['properties']['node'] = $taxon_node ;
        $this->complex_nodes['properties']['notion_concerned'] = 'taxon' ;
      }
      else throw new Exception('Unknown node '.$taxon_node->nodeName.' in taxon');
    }
    $object['taxon_parents'] = $taxon_parent->export();
  }

   /**
   * This function fill all chrono fields from Staging
   * $chrono_parent may containt all field referenced in $array_level separated by ","
   */
  public function processWithChronoNode($xml_node,$object)
  {
    $chrono_parent = new Hstore();
    // this array below is a taxon array ! I must change it as soon as I get the good list
    $array_level = array("phylum","class","order","family","genus","sub_genus","species","sub_species") ;
    foreach ($xml_node->childNodes as $chrono_node)
    {
      // text node doesn't interest us
      if($chrono_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if(in_array($chrono_node->nodeName,$array_level)) $chrono_parent[$chrono_node->nodeName] = $chrono_node->nodeValue;
      elseif($chrono_node->nodeName == 'name') $object['chrono_name'] = $chrono_node->nodeValue ;
      elseif($chrono_node->nodeName == 'level') $object['chrono_level_name'] = $chrono_node->nodeValue ;
      elseif($chrono_node->nodeName == 'comments')
      {
        $this->complex_nodes['comments']['node'] = $chrono_node ;
        $this->complex_nodes['comments']['notion_concerned'] = 'chrono' ;
      }
      elseif($chrono_node->nodeName == 'properties')
      {
        $this->complex_nodes['properties']['node'] = $chrono_node ;
        $this->complex_nodes['properties']['notion_concerned'] = 'chrono' ;
      }
      else throw new Exception('Unknown node '.$chrono_node->nodeName.' in chrono');
    }
    $object['chrono_parents'] = $chrono_parent->export();
  }

   /**
   * This function fill all litho fields from Staging
   * $chrono_parent may containt all field referenced in $array_level separated by ","
   */
  public function processWithLithoNode($xml_node,$object)
  {
    $litho_parent = new Hstore();
    // this array below is a litho array ! I must change it as soon as I get the good list
    $array_level = array("phylum","class","order","family","genus","sub_genus","species","sub_species") ;
    foreach ($xml_node->childNodes as $litho_node)
    {
      // text node doesn't interest us
      if($litho_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if(in_array($litho_node->nodeName,$array_level)) $litho_parent[$litho_node->nodeName] = $litho_node->nodeValue;
      elseif($litho_node->nodeName == 'name') $object['litho_name'] = $litho_node->nodeValue ;
      elseif($litho_node->nodeName == 'level') $object['litho_level_name'] = $litho_node->nodeValue ;
      elseif($litho_node->nodeName == 'comments')
      {
        $this->complex_nodes['comments']['node'] = $litho_node ;
        $this->complex_nodes['comments']['notion_concerned'] = 'litho' ;
      }
      elseif($litho_node->nodeName == 'properties')
      {
        $this->complex_nodes['properties']['node'] = $litho_node ;
        $this->complex_nodes['properties']['notion_concerned'] = 'litho' ;
      }
      else throw new Exception('Unknown node '.$litho_node->nodeName.' in litho');
    }
    $object['litho_parents'] = $litho_parent->export();
  }

   /**
   * This function fill all lithology fields from Staging
   * $chrono_parent may containt all field referenced in $array_level separated by ","
   */
  public function processWithLithologyNode($xml_node,$object)
  {
    $litho_parent = new Hstore();
    // this array below is a litho array ! I must change it as soon as I get the good list
    $array_level = array("phylum","class","order","family","genus","sub_genus","species","sub_species") ;
    foreach ($xml_node->childNodes as $litho_node)
    {
      // text node doesn't interest us
      if($litho_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if(in_array($litho_node->nodeName,$array_level)) $litho_parent[$litho_node->nodeName] = $litho_node->nodeValue;
      elseif($litho_node->nodeName == 'name') $object['lithology_name'] = $litho_node->nodeValue ;
      elseif($litho_node->nodeName == 'level') $object['lithology_level_name'] = $litho_node->nodeValue ;
      elseif($litho_node->nodeName == 'comments')
      {
        $this->complex_nodes['comments']['node'] = $litho_node ;
        $this->complex_nodes['comments']['notion_concerned'] = 'lithology' ;
      }
      elseif($litho_node->nodeName == 'properties')
      {
        $this->complex_nodes['properties']['node'] = $litho_node ;
        $this->complex_nodes['properties']['notion_concerned'] = 'lithology' ;
      }
      else throw new Exception('Unknown node '.$litho_node->nodeName.' in lithology');
    }
    $object['lithology_parents'] = $litho_parent->export();
  }

   /**
   * This function fill all lithology fields from Staging
   * $chrono_parent may containt all field referenced in $array_level separated by ","
   */
  public function processWithMineralogyNode($xml_node,$object)
  {
    $mineral_parent = new Hstore();
    // this array below is a mineral array ! I must change it as soon as I get the good list
    $array_level = array("phylum","class","order","family","genus","sub_genus","species","sub_species") ;
    foreach ($xml_node->childNodes as $mineral_node)
    {
      // text node doesn't interest us
      if($mineral_node->nodeName == "#text") continue;
      // if a node have not more than one child, it's a node without childen
      if(in_array($mineral_node->nodeName,$array_level)) $mineral_parent[$mineral_node->nodeName] = $mineral_node->nodeValue;
      elseif($mineral_node->nodeName == 'name') $object['mineralogy_name'] = $mineral_node->nodeValue ;
      elseif($mineral_node->nodeName == 'level') $object['mineralogy_level_name'] = $mineral_node->nodeValue ;
      elseif($mineral_node->nodeName == 'comments')
      {
        $this->complex_nodes['comments']['node'] = $mineral_node ;
        $this->complex_nodes['comments']['notion_concerned'] = 'mineralogy' ;
      }
      elseif($mineral_node->nodeName == 'properties')
      {
        $this->complex_nodes['properties']['node'] = $mineral_node ;
        $this->complex_nodes['properties']['notion_concerned'] = 'mineralogy' ;
      }
      else throw new Exception('Unknown node '.$mineral_node->nodeName.' in mineralogy');
    }
    $object['mineralogy_parents'] = $mineral_parent->export();
  }

  /**
   * This function create and save all Codes found
   */
  public function processWithCodesNode($code_groups,$id)
  {
    $codes = $code_groups->getElementsByTagName("code");
    foreach($codes as $codes_node)
    {
      $code = new Codes() ;
      $code['record_id'] = $id ;
      $code['referenced_relation'] = 'staging' ;
      foreach($codes_node->childNodes as $codes_info)
      {
        if($codes_info->nodeName == "#text") continue;
        if($codes_info->nodeName == "value") $code['code'] = $codes_info->nodeValue ;
        if($codes_info->nodeName == "category") $code['code_category'] = $codes_info->nodeValue ;
        elseif($codes_info->nodeName == 'comments')
        {
          $this->complex_nodes['comments']['node'] = $codes_info ;
          $this->complex_nodes['comments']['notion_concerned'] = 'codes' ;
        }
        elseif($codes_info->nodeName == 'properties')
        {
          $this->complex_nodes['properties']['node'] = $codes_info ;
          $this->complex_nodes['properties']['notion_concerned'] = 'codes' ;
        }
        else $this->getSimpleField($codes_info,$code);
      }
      try
      {
        $code->save() ;
      }
      catch(Doctrine_Exception $e)
      {}//NOTHING

    }
  }

  /**
   * This function create and save all Identifications found
   * $identifiers contains all referenced people is an identification, these identifier are saved in the determination_status field
   * if $comments is an array, I put the tag concerned just before notion concerned
   */
  public function processWithIdentificationsNode($xml_node,$id)
  {
    $node = $xml_node->getElementsByTagName("identification");
    foreach($node as $identifications)
    {
      $as_identifier = false ;
      $identification = new Identifications() ;
      $identification['record_id'] = $id ;
      $identification['referenced_relation'] = 'staging' ;
      foreach ($identifications->childNodes as $identification_info)
      {
        if($identification_info->nodeName == "#text") continue;
        if($identification_info->nodeName == 'notion_concerned') $identification['notion_concerned'] = $identification_info->nodeValue;
        elseif($identification_info->nodeName == 'value') $identification['value_defined'] = $identification_info->nodeValue;
        elseif($identification_info->nodeName == 'identifiers')
        {
          $identifier_to_save = $identification_info;
          $as_identifier = true ;
        }
        elseif($identification_info->nodeName == 'comments')
        {
          $this->complex_nodes['comments']['node'] = $identification_info ;
          $this->complex_nodes['comments']['notion_concerned'] = 'identifications' ;
        }
        elseif($identification_info->nodeName == 'properties')
        {
          $this->complex_nodes['properties']['node'] = $identification_info ;
          $this->complex_nodes['properties']['notion_concerned'] = 'identifications' ;
        }
      }
      $identification->save() ;
      if($as_identifier)
      {
        $identifier = $identifier_to_save->getElementsByTagName("identifier")    ;
        $order_by = 0 ;
        foreach($identifier as $ident_tag)
        {
          $people = new stagingPeople();
          $people->fromArray(array('people_type' => 'identifier', 'record_id' => $identification->getId(), 'referenced_relation' => 'identifications','order_by' => $order_by++)) ;
          $people['formated_name'] = $ident_tag->getElementsByTagName("formated_name")->item(0)->nodeValue;
          $people->save() ;
        }
      }
    }
  }

  /**
   * This function fill the donator field from Staging
   * $donators is defined to be used as an array in the db with all donators separated by a ','
   */
  public function processWithDonatorsNode($xml_node,$id)
  {
    $donator_node = $xml_node->getElementsByTagName("donator");
    $order_by = 0 ;
    foreach ($donator_node as $donator)
    {
      $people = new stagingPeople();
      $people->fromArray(array('people_type' => 'donator', 'record_id' => $id, 'referenced_relation' => 'staging','order_by' => $order_by++)) ;
      $people['formated_name'] = $donator->getElementsByTagName("formated_name")->item(0)->nodeValue;
      $people->save() ;
    }
  }


  /**
   * This function create and save all Comments found
   * $comments can be an array when the tag is found in another complex tag (such as gtu)
   * if $comments is an array, I put the tag concerned just before notion concerned
   */
  public function processWithCommentsNode($comments,$id)
  {
    $notion = "" ;
    if(gettype($comments) == "array")
    {
      $xml_node = $comments['node']->getElementsByTagName("comment") ;
      $notion = $comments['notion_concerned'].'/' ;
    }
    else $xml_node = $comments->getElementsByTagName("comment") ;
    foreach($xml_node as $comment_info)
    {
      if($comment_info->nodeName == "#text") continue;
      $comment = new Comments() ;
      $comment['record_id'] = $id ;
      $comment['referenced_relation'] = 'staging' ;
      $comment['notion_concerned'] = $notion.$comment_info->getElementsByTagName("notion_concerned")->item(0)->nodeValue;
      $comment['comment'] = $comment_info->getElementsByTagName("value")->item(0)->nodeValue;
      $comment->save() ;
    }
  }

  /**
   * This function create and save all Properties found
   * $properties can be an array when the tag is found in another complex tag (such as gtu)
   * if $properties is an array, I put the tag concerned just before notion concerned
   * $property_values contains the property_values' tag, because I have to handle then after having saved the property in order to give the id
   */
  public function processWithPropertiesNode($properties,$id)
  {
    $notion = "" ;
    if(gettype($properties) == "array")
    {
      $xml_node = $properties['node']->getElementsByTagName("property") ;
      $notion = $properties['notion_concerned'].'/' ;
    }
    else $xml_node = $properties->getElementsByTagName("property") ;
    foreach($xml_node as $nodes)
    {
      $property = new CatalogueProperties() ;
      $property['record_id'] = $id ;
      $property['referenced_relation'] = 'staging' ;
      $property_values = '' ;
      foreach($nodes->childNodes as $properties_info)
      {
        if($properties_info->nodeName == "#text") continue;
        if($properties_info->nodeName == "property_values") $property_values = $properties_info ;
        else $this->getSimpleField($properties_info,$property);
      }
      $property->save() ;
      if($property_values) $this->processWithPropertiesValues($property_values,$property->getId()) ;
    }
  }

  /**
   * This function create and save all Properties values found on a properties tag
   */
  public function processWithPropertiesValues($properties_values,$id)
  {
    $properties_value = $properties_values->getElementsByTagName("property_value") ;
    foreach($properties_value as $properties_value_node)
    {
      $property = new PropertiesValues();
      $property['property_ref'] = $id ;
      foreach($properties_value_node->childNodes as $values)
      {
        if($values->nodeName == "#text") continue;
        if($values->nodeName == 'value') $property['property_value'] = $values->nodeValue;
        if($values->nodeName == 'accuracy') $property['property_accuracy'] = $values->nodeValue;
      }
      $property->save();
    }
  }
}
