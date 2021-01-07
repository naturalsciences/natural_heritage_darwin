<?php

/**
 * GeoreferencesByService filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GeoreferencesByServiceFormFilter extends BaseGeoreferencesByServiceFormFilter
{
  /**
   * @see DarwinModelFormFilter
   */
  public function configure()
  {
    //parent::configure();
	
	 $this->useFields(array('data_origin', 'name'));
	 $this->addPagerItems();
	$data_origin=Doctrine_Core::getTable("GeoreferencesByService")->getDistinctDataOrigin();
    $this->widgetSchema['data_origin'] = new sfWidgetFormChoice(array(
       "choices"=> $data_origin,
       'multiple' => true,
    ), array("size"=>2));

    $this->validatorSchema['data_origin'] =  new sfValidatorChoice(
        array(
         "choices"=> $data_origin,
         'multiple' => true,
         "required"=>false
         )
    );

	
	$this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => false, 'trim' => true));
	
	
	$this->widgetSchema->setNameFormat('georeferences_by_service_filters[%s]');
	//parent::configure();

  }
  
   public function addDataOriginColumnQuery($query, $values, $val)
  {
	
    if( $val != '')
    {
		
		$query->andWhere("fulltoindex(data_origin)=fulltoindex(?)", $val);		
	
   }
    return $query;
  }
  
  public function addNameColumnQuery($query, $values, $val)
  {
    if( $val != '')
    {
		
		$query->andWhere("fulltoindex(name)=fulltoindex(?)", $val);		
	
   }
    return $query;
  }
  
   public function bind(array $taintedValues = null, array $taintedFiles = null)
  {

    parent::bind($taintedValues, $taintedFiles);
  }
    public function doBuildQuery(array $values)
  {    
   print_r( $values);
     $query = DQ::create()
        ->select('g.*')
      ->from('GeoreferencesByService g');
	
     $this->addNameColumnQuery($query, $values,$values["name"]);
	  $this->addDataOriginColumnQuery($query, $values,$values["data_origin"]);
    
   
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
