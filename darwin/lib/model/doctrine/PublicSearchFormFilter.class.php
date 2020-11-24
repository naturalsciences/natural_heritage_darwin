<?php

/**
 * PublicSearchFormFilter filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PublicSearchFormFilter extends BaseSpecimensFormFilter
{
  public function configure()
  {
  
    //ftheeten 2018 05 29
    $this->tmpRequest=array_merge($_GET,$_POST);
    if(count($this->tmpRequest)>0)
    {
   
        $disable=true;
        foreach($this->tmpRequest as $key=>$value)
        {
            if($key!="specimen_search_filters"&&$key!="include_sub_collections")
            {
                $disable=false;
                break;
            }
            
        }
        if($disable)
        {
         $this->disableLocalCSRFProtection();
        }         
    }
    $this->useFields(array(
        'taxon_name', 'taxon_level_ref', 'litho_name', 'litho_level_ref', 'chrono_name', 'chrono_level_ref',
        'lithology_name', 'lithology_level_ref', 'mineral_name', 'mineral_level_ref'));
    $this->addPagerItems();

    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_common_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['taxon_level_ref']->setAttribute('class','medium_small_size') ;

    $this->widgetSchema['lithology_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['lithology_common_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['lithology_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithology'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['lithology_level_ref']->setAttribute('class','medium_small_size') ;

    $this->widgetSchema['litho_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['litho_common_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['litho_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithostratigraphy'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['litho_level_ref']->setAttribute('class','medium_small_size') ;

    $this->widgetSchema['chrono_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['chrono_common_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['chrono_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'chronostratigraphy'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['chrono_level_ref']->setAttribute('class','medium_small_size') ;

    $this->widgetSchema['mineral_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['mineral_common_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['mineral_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'mineralogy'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['mineral_level_ref']->setAttribute('class','medium_small_size') ;
    $this->widgetSchema->setLabels(array('taxon_name' => 'Taxon',
                                         'chrono_name' => 'Chrono',
                                         'litho_name' => 'Litho',
                                         'lithology_name' => 'Rocks',
                                         'mineral_name' => 'Mineral',
                                        )
                                  );
    $this->widgetSchema['col_fields'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['search_type'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));
    $this->widgetSchema['collection_ref']->addOption('public_only',true);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query

    $this->validatorSchema['col_fields'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                ));
                                                                
                                                                    //ftheeten 2018 05 29
	$this->widgetSchema['include_sub_collections'] = new sfWidgetFormInputCheckbox();
  	////ftheeten 2018 05 29
	$this->validatorSchema['include_sub_collections'] = new sfValidatorPass();
                                                                
    $this->validatorSchema['search_type'] = new sfValidatorString(array('required' => false));
     $this->validatorSchema['gtu_code'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['taxon_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['taxon_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['chrono_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['litho_common_name'] = new sfValidatorInteger(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['litho_level_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['lithology_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['mineral_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorInteger(array('required' => false));

    $this->setWidget('tags',new sfWidgetFormTextarea(array(),  array('class' => 'tag_line', 'cols'=>'50', 'rows'=>'4')));
    $this->setValidator('tags', new sfValidatorString(array('required' => false, 'trim' => true)) );

    $this->widgetSchema['type'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctTypeSearches',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['type'] = new sfValidatorPass();

    $this->widgetSchema['sex'] = new sfWidgetFormDarwinDoctrineChoice(array(
      'model' => 'Specimens',
      'table_method' => 'getDistinctSexes',
      'multiple' => true,
      'expanded' => true,
      'add_empty' => false,
    ));
    $this->validatorSchema['sex'] = new sfValidatorPass();

    $this->widgetSchema['stage'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'Specimens',
        'table_method' => 'getDistinctStages',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    
  
    $this->validatorSchema['stage'] = new sfValidatorPass();
    
      //ftheeten 2018 10 29
    $this->widgetSchema['codes'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['codes'] = new sfValidatorPass();
     $this->widgetSchema['ig_num'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorPass();
    $this->widgetSchema['_csrf_token'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['_csrf_token'] = new sfValidatorPass();



/** New Pagin System ***/
    $this->widgetSchema['order_dir'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_dir'] = new sfValidatorChoice(array('required' => false, 'choices'=> array('asc','desc'),'empty_value'=>'desc'));
    $this->validatorSchema['order_by'] = new sfValidatorString(array('required' => false,'empty_value'=>'collection_name'));

    $this->widgetSchema['current_page'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['current_page'] = new sfValidatorInteger(array('required'=>false,'empty_value'=>1));
/** New Pagin System ***/
    $this->widgetSchema->setNameFormat('specimen_search_filters[%s]');
	
    $protocol_tmp=Doctrine_Core::getTable("Identifiers")->getDistinctProtocol();
	$this->widgetSchema['institution_protocol'] = new sfWidgetFormChoice(array(
       "choices"=> $protocol_tmp
    ));
    $this->validatorSchema['institution_protocol'] = new sfValidatorChoice(
        array(
         "choices"=> $protocol_tmp,
         'multiple' => false,
         "required"=>false
         )
    );
	
	$this->widgetSchema['institution_identifier'] = new sfWidgetFormInput();
	$this->validatorSchema['institution_identifier'] = new sfValidatorPass();
	
	$this->widgetSchema['people_protocol'] = new sfWidgetFormChoice(array(
       "choices"=> $protocol_tmp
    ));
    $this->validatorSchema['people_protocol'] =  new sfValidatorChoice(
        array(
         "choices"=> $protocol_tmp,
         'multiple' => false,
         "required"=>false
         )
    );
	
	$this->widgetSchema['people_identifier'] = new sfWidgetFormInput();
	$this->validatorSchema['people_identifier'] = new sfValidatorPass();
	
	$this->widgetSchema['people_identifier_role'] = new sfWidgetFormChoice(array(
       "choices"=> array(""=>"", "collector"=> "Collector", "determinator"=> "Determinator", "donator"=> "Donator")
    ));
	$this->validatorSchema['people_identifier_role'] =  new sfValidatorChoice(
        array(
         "choices"=> array("collector", "", "determinator", "donator"),
         'multiple' => false,
         "required"=>false
         )
    );
  }

  public function addSexColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('sex',$val);
      else
        $query->andWhere('sex = ?',$val);
    }
    return $query ;
  }

  public function addStageColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('stage',$val);
      else
        $query->andWhere('stage = ?',$val);
    }
    return $query ;
  }

  public function addTypeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('type_search',$val);
      else
        $query->andWhere('type_search = ?',$val);
    }
    return $query ;
  }

  public function addCollectionRefColumnQuery($query, $field, $val)
  { 
        if (count($val) > 0)
        {
            //ftheeten 2018 05 29
            if((boolean)$this->values['include_sub_collections']===true)
            {
                $query->andWhere("collection_path||collection_ref::varchar||'/' SIMILAR TO ?", "%/(".implode('|',$val).")/%") ;               
            }
            else
            {
                $query->andWhereIn('collection_ref',$val) ;               
            }
        }
    return $query;
  }
  
  public function addCommonNamesColumnQuery($query,$relation, $field, $val)
  {
    $query->andWhere($field.' IN ('.$this->ListIdByWord($relation,$val).')');
    return $query;
  }
  public function bind(array $taintedValues = null, array $taintedFiles = null) {
    if(!isset($taintedValues['search_type']) ||  $taintedValues['search_type'] == '')
      $taintedValues['search_type'] = 'zoo';
    if(!isset($taintedValues['col_fields']) || $taintedValues['col_fields'] == '' ) {
      if($taintedValues['search_type'] == 'zoo')
       $taintedValues['col_fields']  = 'collection|gtu|sex|stage|type';
      else
       $taintedValues['col_fields']  = 'collection|gtu';
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function doBuildQuery(array $values)
  {
    $query = Doctrine_Query::create()
      ->from('Specimens s');
    $this->options['query'] = $query;
    $query = parent::doBuildQuery($values);
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));
    if ($values['chrono_level_ref'] != '') $query->andWhere('chrono_level_ref = ?', intval($values['chrono_level_ref']));
    if ($values['litho_level_ref'] != '') $query->andWhere('litho_level_ref = ?', intval($values['litho_level_ref']));
    if ($values['lithology_level_ref'] != '') $query->andWhere('lithology_level_ref = ?', intval($values['lithology_level_ref']));
    if ($values['mineral_level_ref'] != '') $query->andWhere('mineral_level_ref = ?', intval($values['mineral_level_ref']));
    if ($values['taxon_common_name'] != '') $this->addCommonNamesColumnQuery($query,'taxonomy', 'taxon_ref', $values['taxon_common_name']);
    if ($values['chrono_common_name'] != '') $this->addCommonNamesColumnQuery($query,'chronostratigraphy', 'chrono_ref', $values['chrono_common_name']);
    if ($values['litho_common_name'] != '') $this->addCommonNamesColumnQuery($query,'lithostratigraphy', 'litho_ref', $values['litho_common_name']);
    if ($values['lithology_common_name'] != '') $this->addCommonNamesColumnQuery($query,'lithology', 'lithology_ref', $values['lithology_common_name']);
    if ($values['mineral_common_name'] != '') $this->addCommonNamesColumnQuery($query,'mineralogy', 'mineral_ref', $values['mineral_common_name']);
    $this->addNamingColumnQuery($query, 'taxonomy', 'taxon_name_indexed', $values['taxon_name'],'s','taxon_name_indexed');
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'chrono_name_indexed', $values['chrono_name'],'s','chrono_name_indexed');
    $this->addNamingColumnQuery($query, 'lithostratigraphy', 'litho_name_indexed', $values['litho_name'],'s','litho_name_indexed');
    $this->addNamingColumnQuery($query, 'lithology', 'lithology_name_indexed', $values['lithology_name'],'s','lithology_name_indexed');
    $this->addNamingColumnQuery($query, 'mineralogy', 'mineral_name_indexed', $values['mineral_name'],'s','mineral_name_indexed');
    $query->andWhere('collection_is_public = true') ;
    if($values['tags'] != '') $query->andWhere("gtu_country_tag_indexed && getTagsIndexedAsArray(?)",$values['tags']);
    $query->limit($this->getCatalogueRecLimits());
	
	$this->addInstitutionIdentifierQuery($query,   $values["institution_protocol"], $values["institution_identifier"]);
	$this->addPeopleIdentifierQuery($query, $values["people_protocol"], $values["people_identifier"],$values["people_identifier_role"]);
    return $query;
  }

  public function getWithOrderCriteria()
  {
    return $this->getQuery()->orderby($this->getValue('order_by') . ' ' . $this->getValue('order_dir').'');
  }
  
  //ftheeten 2018 10 29
  public function addCodesColumnQuery($query, $field, $val)
  {
    $sql=Array();
    $sql_params = array();
    foreach(explode(";",$val) as $code)
    {

        if(trim($code)  != '') {          
          $sql[] = "EXISTS(select 1 from codes where referenced_relation='specimens' and record_id = s.id AND full_code_indexed ilike '%' || fulltoindex(?) || '%' )";
          $sql_params[] = $code;
          $has_query = true;
        }
        
    }
    if($has_query)
    {
          $query->addWhere("(".implode(" OR ", $sql).")", $sql_params);
    }
    return $query ;
  }
  
  //ftheeten 2018 10 29
  public function addIgNumColumnQuery($query, $field, $val)
  {    
    
    $sql=Array();
    $sql_params = array();
    foreach(explode(";",$val) as $code)
    {

        if(trim($code)  != '') {          
          $sql[] = "ig_num_indexed like fullToIndex(?) ";
          $sql_params[] = $code;
          $has_query = true;
        }
        
    }
    if($has_query)
    {
          $query->addWhere("(".implode(" OR ", $sql).")", $sql_params);
    }
    return $query ;
  }
  
    public function addInstitutionIdentifierQuery($query,  $protocol, $identifier)
  {
	  if(strlen($protocol)>0&&strlen($identifier)>0)
	  {
		   $query->andWhere("EXISTS (select i.id  from Identifiers i where s.institution_ref = i.record_id AND referenced_relation = 'people' AND LOWER(i.protocol)=? AND i.value=?)",array(strtolower($protocol), $identifier));
	  }
	  return $query;
  }
  
  public function addPeopleIdentifierQuery($query,  $protocol, $identifier, $role="collector")
  {
	  if(strlen($protocol)>0&&strlen($identifier)>0)
	  {
		  $sql_params=Array();
		  $id=Doctrine_Core::getTable('Identifiers')->getLinkedId($protocol, $identifier, "people");
		  if($id!==null)
		  {
			  if($role == 'determinator')
			  {
				$build_query = "? =any(spec_ident_ids) " ;
				$sql_params[]=$id;
				
			  }
			  elseif($role == 'collector')
			  {
				 
				  $build_query = "(? =any(spec_coll_ids) OR EXISTS (SELECT cp.id FROM CataloguePeople cp WHERE cp.referenced_relation= 'expeditions' AND cp.people_ref=? AND s.expedition_ref=cp.record_id ))" ;
				  $sql_params=array($id, $id);

			  }
			  elseif($role == 'donator')
			  {
				$build_query .= "? =any(spec_don_sel_ids) " ;
				$sql_params[]=$id;
			  }
			  else
			  {
				  $build_query = "(? =any(spec_coll_ids||spec_ident_ids||spec_don_sel_ids) OR EXISTS (SELECT cp.id FROM CataloguePeople cp WHERE cp.referenced_relation= 'expeditions' AND cp.people_ref=? AND s.expedition_ref=cp.record_id ))" ;
				  $sql_params=array($id, $id);

			  }
			  $query->andWhere($build_query, $sql_params ) ;
		  }
	  
	  }
	  return $query;
	  
  }

}
