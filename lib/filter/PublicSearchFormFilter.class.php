<?php

/**
 * PublicSearchFormFilter filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PublicSearchFormFilter extends BaseSpecimensFlatFormFilter
{
  public function configure()
  {
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
    $this->setDefault('col_fields','collection|gtu|sex|stage|type');                                    
    $this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));
    $this->widgetSchema['collection_ref']->addOption('public_only',true);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query                                  

    $this->validatorSchema['col_fields'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                ));
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
   
    $this->widgetSchema['type'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctTypeSearches',
        'method' => 'getTypeSearchFormated',
        'key_method' => 'getTypeSearch',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['type'] = new sfValidatorPass();    
    $this->widgetSchema['sex'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctSexes',
        'method' => 'getSexSearchFormated',
        'key_method' => 'getSex',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['sex'] = new sfValidatorPass();

    $this->widgetSchema['stage'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctStages',
        'method' => 'getStageSearchFormated',
        'key_method' => 'getStage',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['stage'] = new sfValidatorPass();



/** New Pagin System ***/
    $this->widgetSchema['order_dir'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_dir'] = new sfValidatorChoice(array('required' => false, 'choices'=> array('asc','desc'),'empty_value'=>'desc'));
    $this->validatorSchema['order_by'] = new sfValidatorString(array('required' => false,'empty_value'=>'collection_name'));
    
    $this->widgetSchema['current_page'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['current_page'] = new sfValidatorInteger(array('required'=>false,'empty_value'=>1));
/** New Pagin System ***/
    $this->widgetSchema->setNameFormat('specimen_search_filters[%s]');
  }
  
  public function addSexColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('individual_sex',$val);
      else
        $query->andWhere('individual_sex = ?',$val);
    }
    return $query ;
  }

  public function addStageColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('individual_stage',$val);
      else
        $query->andWhere('individual_stage = ?',$val);
    }
    return $query ;
  } 
  
  public function addTypeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      if(is_array($val))
        $query->andWhereIn('individual_type_search',$val);
      else
        $query->andWhere('individual_type_search = ?',$val);
    }
    return $query ;
  }    

  public function addCollectionRefColumnQuery($query, $field, $val)
  {
    if (count($val) > 0) 
    {
      $query->andWhereIn('collection_ref',$val) ;
    }
    return $query;
  }
  public function addCommonNamesColumnQuery($query,$relation, $field, $val)
  {  
    $query->andWhere($field.' IN ('.$this->ListIdByWord($relation,$val).')');
    return $query;
  }     
  public function doBuildQuery(array $values)
  {
    $query = Doctrine_Query::create()
      ->from('SpecimenIndividuals i')
      ->innerJoin('i.SpecimensFlat s');
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
    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['taxon_name'],null,'taxon_name_indexed');
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'name_indexed', $values['chrono_name'],null,'chrono_name_indexed');    
    $this->addNamingColumnQuery($query, 'lithostratigraphy', 'name_indexed', $values['litho_name'],null,'litho_name_indexed');        
    $this->addNamingColumnQuery($query, 'lithology', 'name_indexed', $values['lithology_name'],null,'lithology_name_indexed');    
    $this->addNamingColumnQuery($query, 'mineralogy', 'name_indexed', $values['mineral_name'],null,'mineral_name_indexed');           
    $query->andWhere('collection_is_public = true') ;
    if($values['tags'] != '') $query->andWhere("gtu_country_tag_indexed && getTagsIndexedAsArray(?)",$values['tags']);   
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  }

  public function getWithOrderCriteria()
  {
    return $this->getQuery()->orderby($this->getValue('order_by') . ' ' . $this->getValue('order_dir').'');
  }
}
