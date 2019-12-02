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
  protected $idxSubQuery=1;
  public function configure()
  {

    $this->hasTags=False;
    $this->useFields(array('code', 'gtu_from_date', 'gtu_to_date'));
    $this->addPagerItems();
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['code'] = new sfWidgetFormInputText();
    $this->widgetSchema['id'] = new sfWidgetFormInputText();
    $this->widgetSchema['id']->setAttributes(array('class'=>'gtu_id_callback'));
	//ftheeten 2018 03 14 added "taxonomy name callback"
    $this->widgetSchema['code']->setAttributes(array('class'=>'gtu_code_callback'));
    $this->widgetSchema['tags'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate(
      $this->getDateItemOptions(),
      array('class' => 'from_date')
    );

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

	
	//ftheeten 2018 08 05
	 $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));
    //ftheeten 2017 01 13
    $this->widgetSchema['collection_ref']->setAttributes(array('class'=>'collection_ref'));
    $this->validatorSchema['collection_ref'] = new sfValidatorPass(); //Avoid duplicate the query
    //ftheeten 2008 08 09
    $this->widgetSchema['expedition'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition']->setAttributes(array('class'=>'autocomplete_for_expeditions'));
    $this->validatorSchema['expedition'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
     //ftheeten 2018 03 23
    $this->widgetSchema['ig_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['ig_number'] = new sfValidatorString(array('required' => false, 'trim' => true));
    
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    
    //2018 11 22
	$this->widgetSchema['tag_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
	$this->validatorSchema['tag_boolean'] = new sfValidatorPass();
    
    $subForm = new sfForm();
    $this->embedForm('Tags',$subForm);
  }

  public function addCodeColumnQuery($query, $field, $val)
  {
    if($val == '') return $query;
    //ftheeten 2019 01 24 case insensitive
    $query->andWhere("LOWER(code) ilike ? ", "%" . strtolower($val) . "%");
  }

   public function addIdColumnQuery($query, $field, $val)
  {
    if($val == '') return $query;
    //ftheeten 2019 01 24 case insensitive
    $query->andWhere("id = ? ",$val);
  }

  public function addTagsColumnQuery($query, $field, $val)
  {

    $conn_MGR = Doctrine_Manager::connection();
    $tagList = '';
    $whereList=Array();
    foreach($val as $line)
    {
      $line_val = $line['tag'];
      if( $line_val != '')
      {
        //$tagList = $conn_MGR->quote($line_val, 'string');
        $tagList =$line_val;
        $sqlClause=Array();
		
        $tagList=trim($tagList);
        $tagList=trim($tagList, ";");
        foreach(explode(";", $tagList  ) as $tagvalue)
        {
            if(strlen(trim( $tagvalue))>0)
            {
                $tagvalue = $conn_MGR->quote($tagvalue, 'string');
                if(strtolower($this->tag_boolean)=="and")
                {
                     $sqlClause[]="(tag_values_indexed::varchar ~ fulltoindex($tagvalue))";
                }
                else
                {
                    $sqlClause[]="(tag_indexed::varchar ~ fulltoindex($tagvalue))";
                }
            }
        }
        //$query->andWhere(implode(" OR ",$sqlClause ));
        //$query->andWhere("tag_values_indexed && getTagsIndexedAsArray($tagList)");
        $whereList[]=implode(" OR ",$sqlClause );
      }
	  
    }
    if(count($whereList)>0)
    {
        $this->hasTags=True;
        $query->andWhere("(". implode(" ".$this->tag_boolean." ",$whereList ).")");
    }
    if($this->hasTags)
      {
		    $query->select('d.*')->from('DoctrineTemporalInformationGtuGroupTags d');
			$query->addOrderBy("LENGTH(tag)");
			$query->addOrderBy("fct_rmca_gtu_orderby_pattern(tag,$tagvalue )");
	  }

    return $query;
  }

  public function addLatLonColumnQuery($query, $values)
  {
    if( $values['lat_from'] != '' && $values['lon_from'] != '' && $values['lon_to'] != ''  && $values['lat_to'] != '' )
    {
		if(is_numeric($values['lat_from'])&&is_numeric($values['lon_from'])&&is_numeric($values['lon_to'])&&is_numeric($values['lat_to']))
		{
			$postgis_polygon= "public.ST_MakeEnvelope(".min(array($values['lon_from'] , $values['lon_to'] )).",".min(array($values['lat_from'] , $values['lat_to'] )).", ".max(array($values['lon_from'] , $values['lon_to'] )).",".max(array($values['lat_from'] , $values['lat_to'] )).",4326)";
			$query->andWhere("public.ST_INTERSECTS($postgis_polygon, public.ST_SetSRID(public.ST_Point(longitude, latitude),4326))");		
		}
   }
    return $query;
  }
  
  //ftheeten 2018 08 08
  public function addExpeditionColumnExplicit($query, $values)
  {

    if($values['expedition'] !='')
    {
        $query->andWhere("
   		(EXISTS (SELECT d.id
			  FROM Expeditions e1 WHERE 
			   name_indexed = fulltoindex(?) AND e1.id = ANY ( d.expedition_refs) ))
        OR 
        (
        EXISTS (SELECT s.gtu_ref FROM Specimens s , Expeditions e2   WHERE  s.gtu_ref=d.id AND s.expedition_ref=e2.id   AND e2.name_indexed=fulltoindex(?)) 
        
        ) ", Array( $values['expedition'], $values['expedition']));
        $this->idxSubQuery++;
    }
    return $query;
  }
  
  //ftheeten 2018 12 01
    public function addDateFromToColumnQuery(Doctrine_Query $query, array $dateFields, $val_from, $val_to)
  {

	$date=false;
	if (count($dateFields) > 0)
    {
      if($val_from->getMask() > 0 && $val_to->getMask() > 0)
      {
        if (count($dateFields) == 1)
        {
	       $date=true;
          $query->andWhere(" (t1.".$dateFields[0] . " Between ? and ? ) ",
                           array($val_from->format('d/m/Y'),
                                 $val_to->format('d/m/Y')
                                )
                          );
        }
        else
        {
		  $date=true;
          $query->andWhere("(". $dateFields[0] . " Between ? AND ?) OR (".$dateFields[1] ." Between ? AND ?)  ", 
            array($val_from->format('d/m/Y'),$val_to->format('d/m/Y'),$val_from->format('d/m/Y'),$val_to->format('d/m/Y')));
        }
      }
      elseif ($val_from->getMask() > 0)
      { 
	    $date=true;
        $sql = "(". $dateFields[0] . " >= ? AND ". $dateFields[0] . "_mask > 0 )";
        $dateFieldsCount = count($dateFields);
        for ($i = 1; $i <= $dateFieldsCount; $i++)
        {
          $vals[] = $val_from->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " >= ? AND ". $dateFields[1] . "_mask > 0) ";
        $query->andWhere($sql,
                         $vals
                        );
      }
      elseif ($val_to->getMask() > 0)
      {
		$date=true;
        $sql =  $dateFields[0] . " <= ? AND " . $dateFields[0] . "_mask > 0 ";
        $dateFieldsCount = count($dateFields);
        for ($i = 1; $i <= $dateFieldsCount; $i++)
        {
          $vals[] = $val_to->format('d/m/Y');
        }
        if (count($dateFields) > 1) $sql .= " OR (" . $dateFields[1] . " <= ? AND " . $dateFields[1] . "_mask > 0) ";
        $query->andWhere($sql,
                         $vals
                        );
      }
	   if($date)
	   {
		if($this->hasTags)
		{
		  $query->select('d.*')->from('DoctrineTemporalInformationGtuGroupUnnestTags d');
		}
		else
		{
			$query->select('d.*')->from('DoctrineTemporalInformationGtuGroupUnnest d');
		}
	   }
	}
   
    return $query;
  }
  
    //ftheeten 2018 08 05
   public function addCollectionRefColumnQuery($query, $values, $val)
  {
    if( $val != '' )
    {     
      $query->andWhere(' collection_ref = ?  ', $val);
    }
    return $query;
  }

  
      //ftheeten 2018 03 23
   public function addIGNumberColumnQuery($query, $values, $val)
  {
    if( $val != '' )
    {     
      //$query->andWhere('id IN (SELECT s.gtu_ref FROM specimens s WHERE ig_num= ?)', $val);
	  $query->andWhere('(EXISTS (SELECT s.id FROM specimens s WHERE  s.gtu_ref=d.id AND  s.ig_num= ?))', $val);
    }
    return $query;
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->tag_boolean='AND';
	 if(isset($taintedValues['tag_boolean'])) 
	 {
		if(strtolower($taintedValues['tag_boolean'])=='or')
		{
			$this->tag_boolean='OR';
		}
	}
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
   
     $query = DQ::create()
        ->select('d.*')
      ->from('DoctrineTemporalInformationGtuGroup d');
    $this->addCodeColumnQuery($query, $values,$values["code"]);
    $this->addIdColumnQuery($query, $values,$values["id"]);
    $this->addIgNumberColumnQuery($query, $values,$values["ig_number"]);     
    $this->addTagsColumnQuery($query, $values,$values["Tags"]);
    $this->addExpeditionColumnExplicit($query,$values);
    $this->addCollectionRefColumnQuery($query, $values,$values["collection_ref"]);
    $this->addLatLonColumnQuery($query,$values);
    

    $fields = array('from_date', 'to_date');    
    $this->addDateFromToColumnQuery($query, $fields, $values['gtu_from_date'], $values['gtu_to_date']);
    $query->andWhere("id > 0 ");
    return $query;
  }
  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/leaflet/leaflet.js';
    $javascripts[]='/leaflet/leaflet.markercluster-src.js';
    $javascripts[]='/js/map.js';
    $javascripts[]= '/Leaflet.draw/dist/leaflet.draw.js';
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    $items['leaflet/MarkerCluster.css']='all';
  	$items['/Leaflet.draw/dist/leaflet.draw.css']='all';
    return $items;
  }

}
