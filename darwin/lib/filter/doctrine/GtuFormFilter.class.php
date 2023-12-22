<?php

/**
 * Gtu filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class GtuFormFilter extends BaseGtuFormFilter
{
  public function configure()
  {

    $this->useFields(array('code', 'gtu_from_date', 'gtu_to_date','nagoya'));
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['code'] = new sfWidgetFormInputText();
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );
	
	//JMHerpers 4/9/2019
	static $nagoyaanswers = array(
		"yes" 		=> "Yes",
		"no" 		=> "No",
		"not defined"     	=> "Not defined",
		NULL =>"Yes or No"
	);
 
	$this->widgetSchema['nagoya'] = new sfWidgetFormChoice(array(
      'choices' =>  $nagoyaanswers,
    ));
	$this->setDefault('nagoya', NULL);

    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'to_date')
    );
    $this->widgetSchema->setLabels(array(
      'gtu_from_date' => 'Between',
      'gtu_to_date' => 'and',
    ));

    $this->validatorSchema['tags'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid',)
    );
    $this->widgetSchema['lat_from'] = new sfWidgetForminput();
    $this->widgetSchema['lat_from']->setLabel('Latitude');
    $this->widgetSchema['lat_to'] = new sfWidgetForminput();
    $this->widgetSchema['lon_from'] = new sfWidgetForminput();
    $this->widgetSchema['lon_from']->setLabel('Longitude');
    $this->widgetSchema['lon_to'] = new sfWidgetForminput();

    $this->validatorSchema['lat_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-90', 'max'=>'90'));
    $this->validatorSchema['lon_from'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));
    $this->validatorSchema['lat_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-90', 'max'=>'90'));
    $this->validatorSchema['lon_to'] = new sfValidatorNumber(array('required'=>false,'min' => '-180', 'max'=>'180'));

    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
      'gtu_from_date',
      '<=',
      'gtu_to_date',
      array('throw_global_error' => true),
      array('invalid'=>'The "begin" date cannot be above the "end" date.')
    ));

    
    //ftheeten 2018 03 23
    $this->widgetSchema['ig_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_number'] = new sfValidatorString(array('required' => false, 'trim' => true));  
   
    
    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);
	
	//People 
	 $fields_to_search = array(
      'spec_coll_ids' => $this->getI18N()->__('Collector'),
      'spec_don_sel_ids' => $this->getI18N()->__('Donator or seller'),
      'ident_ids' => $this->getI18N()->__('Identifier')
    );
	$this->widgetSchema["people_ref"]= new sfWidgetFormChoice(array('choices'=>array(), "multiple"=>true));
	$this->widgetSchema['people_ref']->setAttributes(array("class"=> 'select2_people'));
	 $this->widgetSchema['role_ref'] = new sfWidgetFormChoice(
      array('choices'=> $fields_to_search,
            'multiple' => true,
            'expanded' => true,
			 'label' => $this->getI18N()->__('Choose people role'),
      ),
	  array('class'=> 'role_ref'));
    //$this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required' => false)) ;
	$this->validatorSchema['people_ref'] = new sfValidatorPass() ;
    $this->validatorSchema['role_ref'] = new sfValidatorChoice(array('choices'=>array_keys($fields_to_search), 'required'=>false)) ;
    $this->validatorSchema['role_ref'] = new sfValidatorPass() ;
	//ftheeten 2016/01/07
		$this->widgetSchema['people_fuzzy'] = new sfWidgetFormInputText();
	$this->widgetSchema['people_fuzzy']->setAttributes(array("class"=> 'class_fuzzy_people'));
	$this->validatorSchema['people_fuzzy'] = new sfValidatorString(array('required' => false)) ;
	$this->validatorSchema['people_fuzzy'] = new sfValidatorPass() ;
  }

  public function addCodeColumnQuery($query, $field, $val)
  {
    if($val == '') return $query;
    $query->andWhere("LOWER(REPLACE(REPLACE(LOWER(code),' ',''),'.','')) LIKE '%'||LOWER(REPLACE(REPLACE(LOWER(?),' ',''),'.',''))||'%' ", $val );
  }

  public function addTagsColumnQuery($query, $field, $val)
  {
    $alias = $query->getRootAlias();
    $conn_MGR = Doctrine_Manager::connection();
    $tagList = '';

    /*foreach($val as $line)
    {
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        $tagList = $conn_MGR->quote($line_val, 'string');
        $query->andWhere("tag_values_indexed && getTagsIndexedAsArray($tagList)");
      }
    }*/
	//ftheeten 2016 02 12 
	$alias="tags";
	$idxAlias=1;
	   foreach($val as $line)
    {
	  $alias=$alias.$idxAlias;
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        $tagList = $conn_MGR->quote($line_val, 'string');
		 if($line['fuzzy_matching_tag']=="on")
		{
			$query->andWhere("id IN (SELECT $alias.gtu_ref FROM tags $alias WHERE ($alias.tag_indexed
					LIKE
					ANY(SELECT '%'||fulltoindex(regexp_split_to_table($tagList,','),TRUE)||'%'))
					)
					
					
					");
					
		}
		else
		{
			$query->andWhere("tag_values_indexed && getTagsIndexedAsArray($tagList)");
		}
	  }
	  $idxAlias++;
    }
	

/*    if(strlen($tagList))
    {
      $tagList = substr($tagList, 0, -1); //remove last ','
      $query->andWhere("id in (select getGtusForTags(array[$tagList]))");
    }*/
	
    return $query;
  }

  public function addLatLonColumnQuery($query, $values)
  {
    if( $values['lat_from'] != '' && $values['lon_from'] != '' && $values['lon_to'] != ''  && $values['lat_to'] != '' )
    {
      //ftheeten 2018 02 03 inver lat lon
      $horizontal_box = "((".$values['lon_from'].",-180),(".$values['lon_to'].",180))";
      $query->andWhere("box(? :: text) @> location",$horizontal_box);

      $vert_box = "((".$values['lon_from'].",".$values['lat_from']."),(".$values['lon_to'].",".$values['lat_to']."))";
      // Look for a wrapped box (ie. between RUSSIA and USA)
      if( (float)$values['lon_to'] < (float) $values['lon_from']) {
        $query->andWhere(" NOT box(? :: text) @> location", $vert_box);
      } else {
        // Not wrapped, as in a normal world search
        $query->andWhere("box(? :: text) @> location", $vert_box);
      }
      $query->andWhere('location is not null');
    }
    return $query;
  }
  
    //ftheeten 2018 03 23
   public function addIGNumberColumnQuery($query, $values, $val)
  {
    if( $val != '' )
    {     
      $query->andWhere('id IN (SELECT s.gtu_ref FROM specimens s WHERE ig_num= ?)', $val);
    }
    return $query;
  }
  
  public function add_people_query($query, $val)
  {
	  $people_ref=$val["people_ref"];
	  $fuzzy=$val["people_fuzzy"];
	  $roles=$val["role_ref"];
	  $flag_fuzzy=false;
	  if($roles===null)
	  {
		   $roles=Array("spec_coll_ids","spec_don_sel_id", "ident_ids" );
	  }
	  if(count($roles)==0)
	  {
		  $roles=Array("spec_coll_ids","spec_don_sel_id", "ident_ids" );
	  }
	  if($fuzzy!==null)
	  {
			if(strlen(trim($fuzzy))>0)
			{
			 
			  
			  $flag_fuzzy=true;
			  $id_fuzzy=Doctrine_Core::getTable('People')->completeAsArray(null, $fuzzy, false);
			  
			  
			  foreach($id_fuzzy as $item)
			  {
				  $people_ref[]=$item["value"];
			  }
		  
		}  
	  }
	  $params=Array();
	  $elems=Array();
	  if($people_ref===null)
	  {
		  $people_ref=Array();
	  }
	  if(count($people_ref)>0)
	  {
		  foreach($people_ref as $p)
		  {
			  foreach($roles as $r)
			  {
				  if($r=="spec_coll_ids")
				  {
					  $elems[]="s.spec_coll_ids @> ARRAY[?]::int[]";
					  $params[]=$p;
				  }
				  elseif($r=="spec_don_sel_ids")
				  {
					  $elems[]="s.spec_don_sel_ids @> ARRAY[?]::int[]";
					   $params[]=$p;
				  }
				  elseif($r=="ident_ids")
				  {
					  $elems[]="s.spec_ident_ids @> ARRAY[?]::int[]";
					   $params[]=$p;
				  }
			  }
		  }
		  $part_sql="EXISTS (SELECT s.id FROM Specimens s WHERE (".implode(" OR ",$elems ).") AND ".$query->getRootAlias().".id=s.gtu_ref )";
		 
		  $query->andWhere($part_sql, $params);
		  $query->addOrderBy(" (select count(*) from Tags where gtu_Ref=".$query->getRootAlias().".id)");
	  }
	  elseif($flag_fuzzy)
	  {
		$query->andWhere("1=2");
	  }
	 
	  return $query;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['Tags']))
    {
      foreach($taintedValues['Tags'] as $key=>$newVal)
      {
        if (!isset($this['Tags'][$key]))
        {
          $this->addValue($key);
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function addValue($num)
  {
      $form = new TagLineForm(null,array('num'=>$num));
      $this->embeddedForms['Tags']->embedForm($num, $form);
      $this->embedForm('Tags', $this->embeddedForms['Tags']);
  }

  public function doBuildQuery(array $values)
  {
    $query = parent::doBuildQuery($values);
	
 
    $this->addLatLonColumnQuery($query,$values);
	
	$this->add_people_query($query,$values);

    $alias = $query->getRootAlias();
	

    $fields = array('gtu_from_date', 'gtu_to_date');
    $this->addDateFromToColumnQuery($query, $fields, $values['gtu_from_date'], $values['gtu_to_date']);
    $query->andWhere("id > 0 ");
	
	//JMHerpers 6/9/19
	if ($values['nagoya'] != NULL)	$query->andWhere('nagoya = ?',  $values['nagoya']);

    return $query;
  }
  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/leaflet/leaflet.js';
    $javascripts[]='/leaflet/leaflet.markercluster-src.js';
    $javascripts[]='/js/map.js';
	$javascripts[]= "/Leaflet.draw-master/dist/leaflet.draw.js";
	$javascripts[]= "/select2-4.0.5/dist/js/select2.full.min.js";
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    $items['leaflet/MarkerCluster.css']='all';
	$items["/Leaflet.draw-master/dist/leaflet.draw.css"]=  'all';
	$items["/select2-4.0.5/dist/css/select2.min.css"]=  'all';
    return $items;
  }

}
