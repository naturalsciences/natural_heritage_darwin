<?php

/**
 * SpecimenSearch filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PublicSearchFormFilter extends BaseSpecimenSearchFormFilter
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
    $this->setDefault('col_fields','taxon|collection|gtu|sex|stage|type|taxon_common_name');                                    
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
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['chrono_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );                                                                
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorString(array('required' => false));    
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['litho_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );                                                                
    $this->validatorSchema['litho_level_ref'] = new sfValidatorString(array('required' => false));  
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['lithology_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );                                                                
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorString(array('required' => false));  
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['mineral_common_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );                                                                
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorString(array('required' => false));                                                                 

    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);   
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();    
    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim' => true));    
   
    $this->widgetSchema['type'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctTypeGroups',
        'method' => 'getTypeGroupFormated',
        'key_method' => 'getTypeGroup',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['type'] = new sfValidatorPass();    
    $this->widgetSchema['sex'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctSexes',
        'method' => 'getSex',
        'key_method' => 'getSex',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['sex'] = new sfValidatorPass();

    $this->widgetSchema['stage'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctStages',
        'method' => 'getStage',
        'key_method' => 'getStage',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['stage'] = new sfValidatorPass();
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
  public function addTagsColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $conn_MGR = Doctrine_Manager::connection();
    $tagList = '';
    foreach($val as $line)
    {
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        $tagList = $conn_MGR->quote($line_val, 'string');
        $query->andWhere("gtu_tag_values_indexed && getTagsIndexedAsArray($tagList)");
      }
    }
    return $query ;
  }   
  public function addGtuTagValue($num)
  {
      $form = new TagLineForm();
      $this->embeddedForms['Tags']->embedForm($num, $form);
      $this->embedForm('Tags', $this->embeddedForms['Tags']);
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['collection_ref']))
      $this->widgetSchema['collection_ref']->addOption('listCheck',$taintedValues['collection_ref']) ;
    if(isset($taintedValues['Tags'])&& is_array($taintedValues['Tags']))
    {
      foreach($taintedValues['Tags'] as $key=>$newVal)
      {
        if (!isset($this['Tags'][$key]))
        {
          $this->addGtuTagValue($key);
        }
      }
    }
    else $this->offsetUnset('Tags') ;
    parent::bind($taintedValues, $taintedFiles);
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
    $fields = SpecimenSearchTable::getFieldsByType();  
    $str = '';
    $array_fld = array_merge($fields['specimens'],$fields['individuals']);
    foreach($array_fld as $fld)
    {
      $str .= ' dummy_first( '. $fld .' ) as '.$fld.' ,' ;
    }

    $query = Doctrine_Query::create()
      ->from('IndividualSearch s')
      ->select($str .' MIN(id) as id,  false as with_types')
      ->andWhere('individual_ref != 0 ')
      ->groupBy('individual_ref'); 
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
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  }  
}