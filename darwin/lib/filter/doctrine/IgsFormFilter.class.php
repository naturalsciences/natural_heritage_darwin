<?php

/**
 * Igs filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class IgsFormFilter extends BaseIgsFormFilter
{
  public function configure()
  {
    $this->useFields(array('ig_num'));
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                     array('class' => 'from_date')
                                                                    );
    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                   array('class' => 'to_date')
                                                                  );
    $this->widgetSchema->setNameFormat('searchIg[%s]');
    $this->widgetSchema->setLabels(array('from_date' => 'Between',
                                         'to_date' => 'and',
                                        )
                                  );
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size ig_num_search'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
	
	$this->widgetSchema['ig_num_exact'] = new sfWidgetFormInputCheckbox();
	$this->setDefault('ig_num_exact', false);
	$this->widgetSchema['ig_num_exact']->setAttributes(array('class'=>'ig_num_exact'));

    $this->validatorSchema['ig_num_exact'] = new sfValidatorPass();
	
    $this->validatorSchema['from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                       'from_date' => true,
                                                                       'min' => $minDate,
                                                                       'max' => $maxDate, 
                                                                       'empty_value' => $dateLowerBound,
                                                                      ),
                                                                 array('invalid' => 'Date provided is not valid',)
                                                                );
    $this->validatorSchema['to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                     'from_date' => false,
                                                                     'min' => $minDate,
                                                                     'max' => $maxDate,
                                                                     'empty_value' => $dateUpperBound,
                                                                    ),
                                                               array('invalid' => 'Date provided is not valid',)
                                                              );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('from_date', 
                                                                          '<=', 
                                                                          'to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );
      	//people widget
    $this->widgetSchema['people_ref'] = new widgetFormButtonRef(array(
      'model' => 'People',
      'link_url' => 'people/searchBoth',
      'box_title' => $this->getI18N()->__('Choose people'),
	  'label' => $this->getI18N()->__('Choose people'),
      'nullable' => true,
      'button_class'=>'people_ref people_ref_0',
      ),
      array('class'=>'inline',)
    );

    $fields_to_search = array(
      'spec_coll_ids' => $this->getI18N()->__('Collector'),
      'spec_don_sel_ids' => $this->getI18N()->__('Donator or seller'),
      'ident_ids' => $this->getI18N()->__('Identifier')
    );

    $this->widgetSchema['role_ref'] = new sfWidgetFormChoice(
      array('choices'=> $fields_to_search,
            'multiple' => true,
            'expanded' => true,
			 'label' => $this->getI18N()->__('Choose people role'),
      ),
	  array('class'=> 'role_ref_0'));
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorChoice(array('choices'=>array_keys($fields_to_search), 'required'=>false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorPass() ;
	//ftheeten 2016/01/07
		$this->widgetSchema['people_fuzzy'] = new sfWidgetFormInputText();
	$this->widgetSchema['people_fuzzy']->setAttributes(array("class"=> 'class_fuzzy_people_0'));
	$this->validatorSchema['people_fuzzy'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['people_fuzzy'] = new sfValidatorPass() ;
	
	$this->widgetSchema['collection_ref'] = new sfWidgetCollectionList(array('choices' => array()));
    $this->widgetSchema['collection_ref']->addOption('public_only',false);
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query
	
	$this->widgetSchema['nagoya_status'] = new sfWidgetFormInputText();
	$this->validatorSchema['nagoya_status'] = new sfValidatorPass(); 
  
  //phv 2023/03/09
  $this->widgetSchema['ig_type']= new sfWidgetFormChoice(array(
         'choices' =>  Igs::getIgTypeAllowedValue(),
         ));
  $this->validatorSchema['ig_type'] = new sfValidatorPass(array('trim'=>true, 'required'=>false));
  
    $this->widgetSchema   ['complete'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['complete'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));
	
	$this->widgetSchema['comment_indexed'] = new sfWidgetFormInputText();
	$this->validatorSchema['comment_indexed'] = new sfValidatorPass(); 
  
  
  
  
  
  }

  
  public function nagoyaStatusColumnQuery(Doctrine_Query $query, $field, $values)
  {
	if ($values != "")
     {
       $conn_MGR = Doctrine_Manager::connection();
       $query->andWhere("LOWER(nagoya_status) like concat('%', ?, '%') ", strtolower( $values));
     }
     return $query;
  }
  public function addIgNumColumnQuery(Doctrine_Query $query, $field, $values, $exact)
  {
	
     if ($values != "")
     {
	   $field="ig_num_indexed";
	   $fct="fullToIndex";
	   if(isset($exact))
		{
			 if(strtolower($exact)=="on")
			 {
				  $field="ig_num";
				  $fct="LOWER";
			 }
		}
		 $conn_MGR = Doctrine_Manager::connection();
		$query->andWhere($field." like concat('%',".$fct."(?), '%') ", $values);
      
       
     }
     return $query;
  }
  
  public function addPeopleSearchColumnQuery(Doctrine_Query $query, $people_id, $field_to_use)
  {	
	$query->leftJoin("i.Specimens s on i.id=s.ig_ref");
    $alias1="cp";

	
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
	{
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;
	}
	$nb2=0;  
    foreach($field_to_use as $field)
    {
		   $alias1=$alias1.$nb2;

		  if($field == 'ident_ids')
		  {
			$build_query .= "s.spec_ident_ids @> ARRAY[$people_id]::int[] OR " ;
		  }
		  elseif($field == 'spec_coll_ids')
		  {
			 $build_query .= "(s.spec_coll_ids @> ARRAY[$people_id]::int[] OR (s.expedition_ref IN (SELECT $alias1.record_id FROM CataloguePeople $alias1 WHERE $alias1.referenced_relation= 'expeditions' AND $alias1.people_ref= $people_id) )) OR " ;

		  }
		  else
		  {
			$build_query .= "s.spec_don_sel_ids @> ARRAY[$people_id]::int[] OR " ;
		  }
		  $nb2++;
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;

    $query->andWhere($build_query) ;
    return $query ;
  }
  
  public function addCollectionRef(Doctrine_Query $query, $collection_ref_array)
  {
		if ( count( $collection_ref_array ) === count( array_filter( $collection_ref_array, 'is_numeric' ) ) ) 
		{		
			$str_col=implode(",", $collection_ref_array);
			$sql_where=  " EXISTS (SELECT p.id FROM Specimens p WHERE p.ig_ref=i.id AND p.collection_ref= ANY ('{".$str_col."}') ) ";		
			$query->andWhere($sql_where) ;
		}
	return $query;
  }
  
  
  public function addIgType(Doctrine_Query $query, $ig_type)
  {
		if ( $ig_type != '' )  
		{		
			$query->andWhere("ig_type =? " , $ig_type) ;
		}
	return $query;
  }
  
  
  public function addPeopleSearchColumnQueryFuzzy(Doctrine_Query $query, $people_name, $field_to_use)
  {
  
    $query->leftJoin("i.Specimens s on i.id=s.ig_ref");
    $alias1="ppa";
	$alias2="ppb";
	$alias3="cp";
	$alias4="ppc";
	$alias5="ppd";
	
	
	
    $build_query = '';
    if(! is_array($field_to_use) || count($field_to_use) < 1)
      $field_to_use = array('ident_ids','spec_coll_ids','spec_don_sel_ids') ;
	 $sql_params = array();
    foreach($field_to_use as $field)
    {
      if($field == 'ident_ids')
      {
        $build_query .= "s.spec_ident_ids && (SELECT array_agg($alias1.id) FROM people $alias1 WHERE fulltoindex(formated_name_indexed) LIKE  '%'||fulltoindex(?)||'%' ) OR " ;
		$sql_params[]=$people_name;
      }
      elseif($field == 'spec_coll_ids')
      {
        $build_query .= "(s.spec_coll_ids && (SELECT array_agg($alias2.id) FROM people $alias2 WHERE fulltoindex(formated_name_indexed)LIKE '%'||fulltoindex(?)||'%' ) OR s.expedition_ref IN (SELECT $alias3.record_id FROM CataloguePeople $alias3 WHERE $alias3.referenced_relation= 'expeditions' AND $alias3.people_ref IN (SELECT $alias4.id FROM people $alias4 WHERE fulltoindex(formated_name_indexed) LIKE '%'||fulltoindex(?)||'%')) ) OR " ;
		$sql_params[]=$people_name;
		$sql_params[]=$people_name;
      }
      else
      {
        $build_query .= "s.spec_don_sel_ids && (SELECT array_agg($alias5.id) FROM people $alias5 WHERE fulltoindex(formated_name_indexed) LIKE '%'||fulltoindex(?)||'%' ) OR " ;
		$sql_params[]=$people_name;
      }
    }
    // I remove the last 'OR ' at the end of the string
    $build_query = substr($build_query,0,strlen($build_query) -3) ;

   $query->andWhere($build_query, $sql_params) ;
	
    return $query ;
  }
  
  
  public function addComplete(Doctrine_Query $query, $complete)
  {
	return $query->andWhere("complete=?", $complete) ;
  }
  
  public function addCommentIndexed(Doctrine_Query $query, $val)
  {
	return $query->andWhere("EXISTS (SELECT c.id FROM Comments c WHERE referenced_relation='igs' AND LOWER(comment) LIKE '%'||LOWER(?)||'%' AND record_id=i.id)", $val) ;
  }

  public function doBuildQuery(array $values)
  {

    $query = DQ::create()
      ->select('DISTINCT i.*')->from("VIgsSpecStats i"); //parent::doBuildQuery($values);
    $fields = array('ig_date');
    $this->addIgNumColumnQuery($query, $fields, $values['ig_num'], $values['ig_num_exact']);
    $this->addDateFromToColumnQuery($query, $fields, $values['from_date'], $values['to_date']);
    if ($values['people_ref'] != '')
	{	
		$this->addPeopleSearchColumnQuery($query, $values['people_ref'], $values['role_ref']);
	}
	if ($values['people_fuzzy'] != '') 
	{
		$this->addPeopleSearchColumnQueryFuzzy($query, $values['people_fuzzy'], $values['role_ref']);
	}
  	if (is_array($values['collection_ref']))
	{
		if(count($values['collection_ref'])>0)
		{
			$this->addCollectionRef($query, $values['collection_ref']);
		}
    }
	if($values["nagoya_status"]!="")
	{
		$this->nagoyaStatusColumnQuery($query, $values['nagoya_status'], $values['nagoya_status']);
	}
	
	if($values["complete"]!="")
	{
		$this->addComplete($query, $values['complete']);
	}
	
	if($values["comment_indexed"]!="")
	{
		$this->addCommentIndexed($query, $values['comment_indexed']);
	}
	
	$this->addIgType($query, $values['ig_type']);
	$query->andWhere("id > 0 ");
    return $query;
  }
  
  
}
