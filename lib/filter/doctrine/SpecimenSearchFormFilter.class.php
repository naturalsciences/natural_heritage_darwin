<?php

/**
 * SpecimenSearch filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimenSearchFormFilter extends BaseSpecimenSearchFormFilter
{
  public function configure()
  {
    $this->addPagerItems();
    
    $this->widgetSchema['gtu_code'] = new sfWidgetFormInputText();
    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => 'All'
      ));
      
    $this->widgetSchema['lithology_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['lithology_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithology'))),
        'add_empty' => 'All'
      ));
      
    $this->widgetSchema['litho_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['litho_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'lithostratigraphy'))),
        'add_empty' => 'All'
      ));   
         
    $this->widgetSchema['chrono_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['chrono_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'chronostratigraphy'))),
        'add_empty' => 'All'
      )); 
           
    $this->widgetSchema['mineral_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['mineral_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'mineralogy'))),
        'add_empty' => 'All'
      ));      
    $this->widgetSchema->setLabels(array('gtu_code' => 'Sampling Location code',
                                         'taxon_name' => 'Taxon',
                                         'taxon_level_ref' => 'Level'
                                        )
                                  );
    $this->widgetSchema['col_fields'] = new sfWidgetFormInputHidden();
    $this->setDefault('col_fields','category|taxon|collection|type|gtu');
    $this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));

    $this->widgetSchema['spec_ids'] = new sfWidgetFormTextarea(array('label'=>'#ID list'));
    
    $this->validatorSchema['spec_ids'] = new sfValidatorString( array('required' => false,'trim' => true));
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
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorString(array('required' => false));
    $this->validatorSchema['chrono_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['chrono_level_ref'] = new sfValidatorString(array('required' => false));    
    $this->validatorSchema['litho_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['litho_level_ref'] = new sfValidatorString(array('required' => false));  
    $this->validatorSchema['lithology_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['lithology_level_ref'] = new sfValidatorString(array('required' => false));  
    $this->validatorSchema['mineral_name'] = new sfValidatorString(array('required' => false,
                                                                       'trim' => true
                                                                      )
                                                                );
    $this->validatorSchema['mineral_level_ref'] = new sfValidatorString(array('required' => false));              
//    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
  	$this->hasJoinTaxa = false;
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('app_yearRangeMin')), intval(sfConfig::get('app_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('app_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('app_dateUpperBound'));
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema->setLabels(array('gtu_from_date' => 'Between',
                                         'gtu_to_date' => 'and',
                                        )
                                  );
    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',)
                                                                           );
    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',)
                                                                         );

    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);

  /**
  * Individuals Fields
  */
    $this->is_individual_joined = false;
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

    $this->widgetSchema['status'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctStates',
        'method' => 'getState',
        'key_method' => 'getState',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['status'] = new sfValidatorPass();

    $this->widgetSchema['social'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctSocialStatuses',
        'method' => 'getSocialStatus',
        'key_method' => 'getSocialStatus',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['social'] = new sfValidatorPass();

    $this->widgetSchema['rockform'] = new sfWidgetFormDoctrineChoice(array(
        'model' => 'SpecimenIndividuals',
        'table_method' => 'getDistinctRockForms',
        'method' => 'getRockForm',
        'key_method' => 'getRockForm',
        'multiple' => true,
        'expanded' => true,
        'add_empty' => false,
    ));
    $this->validatorSchema['rockform'] = new sfValidatorPass();

    unset($this['SpecimenIndividual'], $this['Specimen'], $this['Collection'], $this['CollectionInstitution'], $this['CollectionMainManager'], 
$this['CollectionParent'], $this['Expedition'], $this['Gtu'], $this['GtuParent'], $this['Taxonomy'], $this['TaxonomyLevel'], $this['TaxonomyParent'], 
$this['Lithostratigraphy'], $this['LithostratigraphyLevel'], $this['LithostratigraphyParent'], $this['Chronostratigraphy'], $this['ChronostratigraphyLevel'], 
$this['ChronostratigraphyParent'], $this['Lithology'], $this['LithologyLevel'], $this['LithologyParent'], $this['Mineralogy'], $this['MineralogyLevel'],
$this['MineralogyParent'], $this['HostTaxon'], $this['HostTaxonLevel'], $this['HostTaxonParent'], $this['Ig']);

    sfWidgetFormSchema::setDefaultFormFormatterName('list');
  }

  public function joinIndividual($query)
  {
    $alias = $query->getRootAlias();
     if(! $this->is_individual_joined)
        $query->leftJoin($alias.'.SpecimenIndividual i');
    $this->is_individual_joined = true;
  }

  public function addSexColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $this->joinIndividual($query);
      if(is_array($val))
        $query->andWhereIn('i.sex',$val);
      else
        $query->andWhere('i.sex = ?',$val);
    }
    return $query ;
  }

  public function addStageColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $this->joinIndividual($query);
      if(is_array($val))
        $query->andWhereIn('i.stage',$val);
      else
        $query->andWhere('i.stage = ?',$val);
    }
    return $query ;
  }

  public function addStatusColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $this->joinIndividual($query);
      if(is_array($val))
        $query->andWhereIn('i.state',$val);
      else
        $query->andWhere('i.state = ?',$val);
    }
    return $query ;
  }

  public function addSocialColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $this->joinIndividual($query);
      if(is_array($val))
        $query->andWhereIn('i.social_status',$val);
      else
        $query->andWhere('i.social_status = ?',$val);
    }
    return $query ;
  }

  public function addRockformColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $this->joinIndividual($query);
      if(is_array($val))
        $query->andWhereIn('i.rock_form',$val);
      else
        $query->andWhere('i.rock_form = ?',$val);
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

  public function addGtuCodeColumnQuery($query, $field, $val)
  {
    if($val != '')
      $query->andWhere("gtu_code = ?", $val);
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
    if(isset($taintedValues['Tags']))
    {
      foreach($taintedValues['Tags'] as $key=>$newVal)
      {
        if (!isset($this['Tags'][$key]))
        {
          $this->addGtuTagValue($key);
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }
  
  public function addSpecIdsColumnQuery($query, $field, $val)
  {
    $ids = explode(',', $val);
    $clean_ids =array();
    foreach($ids as $id)
    {
      if(ctype_digit($id))
        $clean_ids[] = $id;
    }
    if(! empty($clean_ids))
      $query->andWhereIn("spec_ref", $clean_ids);
    return $query ;
  }
  
  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
    if (count($values['collection_ref']) > 0) 
    {
      array_pop($values['collection_ref']) ;      
      $query->WhereIn('collection_ref',$values['collection_ref']) ;
    }  
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));
    if ($values['chrono_level_ref'] != '') $query->andWhere('chrono_level_ref = ?', intval($values['chrono_level_ref']));
    if ($values['litho_level_ref'] != '') $query->andWhere('litho_level_ref = ?', intval($values['litho_level_ref']));    
    if ($values['lithology_level_ref'] != '') $query->andWhere('lithology_level_ref = ?', intval($values['lithology_level_ref']));
    if ($values['mineral_level_ref'] != '') $query->andWhere('mineral_level_ref = ?', intval($values['mineral_level_ref']));
    $this->addNamingColumnQuery($query, 'taxonomy', 'name_indexed', $values['taxon_name'],null,'taxon_name_indexed');
    $this->addNamingColumnQuery($query, 'chronostratigraphy', 'name_indexed', $values['chrono_name'],null,'chrono_name_indexed');    
    $this->addNamingColumnQuery($query, 'lithostratigraphy', 'name_indexed', $values['litho_name'],null,'litho_name_indexed');        
    $this->addNamingColumnQuery($query, 'lithology', 'name_indexed', $values['lithology_name'],null,'lithology_name_indexed');    
    $this->addNamingColumnQuery($query, 'mineralogy', 'name_indexed', $values['mineral_name'],null,'mineral_name_indexed');        
    $fields = array('gtu_from_date', 'gtu_to_date');
    $this->addDateFromToColumnQuery($query, $fields, $values['gtu_from_date'], $values['gtu_to_date']);
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  }
}
