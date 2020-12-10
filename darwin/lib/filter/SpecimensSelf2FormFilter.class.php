<?php

/**
 * Taxonomy filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class SpecimensSelf2FormFilter extends BaseSpecimensFormFilter
{
  public function configure()
  {
    $this->useFields(array('ig_num','taxon_name','collection_name')) ;

    $this->addPagerItems();
    $this->widgetSchema->setNameFormat('searchSpecimen[%s]');
    $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['code'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['taxon_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema->setLabels(array('code' => 'Exact Specimen code',
                                         'taxon_name' => 'Taxon',
                                         'taxon_level' => 'Level',
                                         'collection_name' => 'Collections',
                                         'ig_num' => 'I.G. unit'
                                        )
                                  );
    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => $this->getI18N()->__('All')
      ));
    $this->widgetSchema['collection_name'] = new sfWidgetFormInputText(array(), array('class'=>'medium_size'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false,
                                                                 'trim' => true
                                                                )
                                                          );
    $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));


    $this->widgetSchema['taxon_level_ref'] = new sfWidgetFormDarwinDoctrineChoice(array(
        'model' => 'CatalogueLevels',
        'table_method' => array('method'=>'getLevelsByTypes','parameters'=>array(array('table'=>'taxonomy'))),
        'add_empty' => 'All'
      ));
    $this->widgetSchema['taxon_level_ref']->setAttribute('class','medium_small_size') ;
    $this->validatorSchema['taxon_level_ref'] = new sfValidatorInteger(array('required' => false));
    
    //ftheeten 2016 11 24
    
     $this->widgetSchema['collection_ref'] = new sfWidgetFormInputText();
        //$this->widgetSchema['collection_ref']->setAttribute('disabled','disable');
        $this->widgetSchema['collection_ref']->setAttribute('class','collection_chooser');
        $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>false));
        
       
        
         	//ftheeten 2015 01 08
        $this->widgetSchema['code_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
        ////ftheeten 2015 01 08
        $this->validatorSchema['code_boolean'] = new sfValidatorPass();
        
         $subForm = new sfForm();
        $this->embedForm('Codes',$subForm);

  }

  public function addCodeColumnQuery(Doctrine_Query $query, $field, $values)
  {
  
    if ($values != "")
    {
      $alias = $query->getRootAlias();    
      $conn_MGR = Doctrine_Manager::connection();
      $query->leftJoin($alias.'.SpecimensCodes cod')
          ->andWhere("cod.referenced_relation = ?", array('specimens'))
          ->andWhere("cod.record_id = $alias.id")
          ->andWhere("cod.full_code_indexed = fullToIndex(".$conn_MGR->quote($values, 'string').") ");
    }
    return $query;
  }
  
  public function addCodesColumnQuery($query, $field, $val)
  {
  
    print("MAMA");

    $str_params = '';
    $str_params_part = '' ;
    $params = array();
    $params_part = array() ;
    foreach($val as $i => $code)
    {
 
      if(empty($code)) continue;
      $sql = '';
      $sql_params = array();
      $has_query = false;
      if(ctype_digit($code['code_from']) && ctype_digit($code['code_to'])) {
          $sql = " code_num BETWEEN ? AND ? ";
          $sql_params = array($code['code_from'], $code['code_to']);
          $has_query = true;
        }
        if($code['code_part']  != '') {
          if($has_query) $sql .= ' AND ';
          //$sql .= " full_code_indexed ilike '%' || fulltoindex(?) || '%' ";
          //ftheeten 20140922
		    //ftheeten 20150909 (if on exact match
		  if($this->code_exact_match==FALSE)
		  {
			//ftheeten 20140922
			$sql .= " full_code_indexed ilike (SELECT '%'||fulltoindex||'%' FROM fulltoindex(?))";
		  }
		  else if($this->code_exact_match==TRUE)
		  {
			$sql .= " full_code_indexed ilike (SELECT fulltoindex FROM fulltoindex(?))";
		  }
		  
		  $sql_params[] = $code['code_part'];
          $has_query = true;
        }
        //if($has_query)
        //  $query->addWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
		if($has_query)
		{
        
		//ftheeten 2015 01 08
			/*if($this->code_boolean=='OR')
			{
				$query->orWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
			}
			else
			{
				$query->andWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
			}*/
            
            $query->andWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
		}
	
	}

    return $query ;
  }

  public function addGtuCodeColumnQuery($query, $field, $val)
  {
    if($val != '')
    {
      $query->andWhere("
        (station_visible = true AND  gtu_code ilike ? )
        OR
        (station_visible = false AND collection_ref in (".implode(',',$this->encoding_collection).")
          AND gtu_code ilike ? )", array('%'.$val.'%','%'.$val.'%'));
      $query->whereParenWrap();
    }
    return $query ;
  }
  
  
  public function addIgNumColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $conn_MGR = Doctrine_Manager::connection();
       $query->andWhere("ig_num_indexed like concat(fullToIndex(".$conn_MGR->quote($values, 'string')."), '%') ");
     }
     return $query;
  } 
  
  public function addCollectionNameColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $conn_MGR = Doctrine_Manager::connection();
       $query->andWhere("collection_ref in (SELECT c.id FROM Collections c WHERE c.name_indexed like concat(fullToIndex(".$conn_MGR->quote($values, 'string')."), '%')) ");
     }
     return $query;
  }  
   
  public function addCallerIdColumnQuery(Doctrine_Query $query, $field, $values)
  {
     if ($values != "")
     {
       $alias = $query->getRootAlias();       
       $query->andWhere($alias.'.id != ?', $values);
     }
     return $query;
  }
  
  public function doBuildQuery(array $values)
  {  
    $query = parent::doBuildQuery($values);
    
    if(!empty($values['collection_ref'])) {
      $this->cols = $values['collection_ref'];
    }
    $query->andwhere('collection_ref = ? ', $this->cols);
    if ($values['taxon_level_ref'] != '') $query->andWhere('taxon_level_ref = ?', intval($values['taxon_level_ref']));    
    $this->addNamingColumnQuery($query, 'taxonomy', 'taxon_name_indexed', $values['taxon_name'],null,'taxon_name_indexed');
    $query->limit($this->getCatalogueRecLimits());
    return $query;
  } 
  
  
    public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
   

    if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes'])) {
      foreach($taintedValues['Codes'] as $key=>$newVal) {
        if (!isset($this['Codes'][$key])) {
          $this->addCodeValue($key);
          print("allo");
        }
      }
    } else {
      $this->offsetUnset('Codes') ;
      $subForm = new sfForm();
      $this->embedForm('Codes',$subForm);
      $taintedValues['Codes'] = array();
    }
		//ftheeten 2015 01 08
	$this->code_boolean='AND';
	 if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes']) && isset($taintedValues['code_boolean'])) 
	 {
		if($taintedValues['code_boolean']=='OR')
		{
			$this->code_boolean='OR';
		}
	}
	
	
	//ftheeten 2015 01 08
	$this->gtu_boolean='AND';
	 if( isset($taintedValues['gtu_boolean'])) 
	 {
		if($taintedValues['gtu_boolean']=='OR')
		{
			$this->gtu_boolean='OR';
		}
	}
		
	
     //ftheeten 2015 09 09
	$this->code_exact_match=FALSE;
	if(isset($taintedValues['Codes'])&& is_array($taintedValues['Codes']) && isset($taintedValues['code_exact_match'])) 
	{
		if($taintedValues['code_exact_match']==TRUE)
		{
			$this->code_exact_match=TRUE;
		}
	}
	

	
    parent::bind($taintedValues, $taintedFiles);
  }
  
   public function addCodeValue($num)
  {
      $form = new CodeLineForm();
      $this->embeddedForms['Codes']->embedForm($num, $form);
      $this->embedForm('Codes', $this->embeddedForms['Codes']);
  }
}
