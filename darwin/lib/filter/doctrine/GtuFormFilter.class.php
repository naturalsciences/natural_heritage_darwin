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
  }

  public function addCodeColumnQuery($query, $field, $val)
  {
    if($val == '') return $query;
    $query->andWhere("code ilike ? ", "%" . $val . "%");
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
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    $items['leaflet/MarkerCluster.css']='all';
	$items["/Leaflet.draw-master/dist/leaflet.draw.css"]=  'all';
    return $items;
  }

}
