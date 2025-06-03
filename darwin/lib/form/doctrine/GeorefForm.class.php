<?php

/**
 * Georef form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class GeorefForm extends BaseGeorefForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
    //parent::configure();
	$this->useFields(array('name', 'country', 'country_iso3166', 'osm_wkt', 'id', 'source', 'osm_id'));
	
	$this->widgetSchema['id'] = new sfWidgetFormInputText();
	$this->widgetSchema['id']->setAttributes(array('class'=>'georef_id'));
	
	$this->widgetSchema['name'] = new sfWidgetFormInputText();
	$this->widgetSchema['name']->setAttributes(array('class'=>'georef_name'));
	
	$this->widgetSchema['source'] = new sfWidgetFormInputText();
	$this->widgetSchema['source']->setAttributes(array('class'=>'georef_source'));
	
	$this->widgetSchema['country'] = new sfWidgetFormInputText();
	$this->widgetSchema['country']->setAttributes(array('class'=>'georef_country'));
	
	
	$this->widgetSchema['country_iso3166'] = new sfWidgetFormInputText();
	$this->widgetSchema['country_iso3166']->setAttributes(array('class'=>'georef_country_iso3166'));
	
	$this->widgetSchema['osm_json'] = new sfWidgetFormTextarea();
	$this->widgetSchema['osm_json']->setAttributes(array('class'=>'osm_json', 'style'=>'width:500px; height:500px ', 'readonly'=> 'readonly'));
	
	$this->widgetSchema['osm_id'] = new sfWidgetFormInputText();
	$this->widgetSchema['osm_id']->setAttributes(array('class'=>'georef_osm_id'));
	
	
    $this->validatorSchema['osm_json'] = new sfValidatorString(array('required' => false));
	
	$this->validatorSchema['name'] = new sfValidatorString(array('required' => true, 'trim' => true));	
	$this->validatorSchema['country'] = new sfValidatorString(array('required' => false, 'trim' => true));
	$this->validatorSchema['country_iso3166'] = new sfValidatorString(array('required' => false, 'trim' => true));
	$this->validatorSchema['osm_json'] = new sfValidatorString(array('required' => false, 'trim' => true));
	
	$this->widgetSchema['GtuToGeoref_holder'] = new sfWidgetFormInputHidden(array('default'=>1));
	$this->validatorSchema['GtuToGeoref_holder'] = new sfValidatorPass();
	
	
	  $this->loadEmbed('GtuToGeoref');//force load of member
  }
  

  
   public function bind(array $taintedValues = null, array $taintedFiles = null)
    {
	
		$this->bindEmbed('GtuToGeoref', 'addGtuToGeoref' , $taintedValues);
		parent::bind($taintedValues, $taintedFiles);
	}
	
   public function saveObjectEmbeddedForms($con = null, $forms = null)
    {
		 foreach($this->embeddedForms['GtuToGeoref']->getEmbeddedForms() as $name => $form)
		  {
			
			if (!isset($form['gtu_ref']) || $form['gtu_ref']->getValue() == '' )
			  {
					$form->getObject()->delete();
				unset($form['georef_ref']);
				unset($form['gtu_ref']);
			  }
			  
		  }
	  $this->saveEmbed('GtuToGeoref', 'gtu_ref' ,$forms, array('georef_ref' => $this->getObject()->getId()), true);
	
		return parent::saveObjectEmbeddedForms($con, $forms);
	}
	
	
	  //ftheeten 2018 11 29
   public function getEmbedRecords($emFieldName, $record_id = false)
  {

     if($record_id === false)
     {
        $record_id = $this->getObject()->getId();
     }
	 if( $emFieldName =='GtuToGeoref' )
      return Doctrine_Core::getTable('GtuToGeoref')->getGtuRelated($record_id);

  }
  
  public function getEmbedRelationForm($emFieldName, $values)
  {   
    if( $emFieldName =='GtuToGeoref' )
	{
      return new GtuToGeorefForm($values);
	}
  }
  
    public function duplicate($id)
  {
	  $GtuToGeoref= Doctrine_Core::getTable('GtuToGeoref')->findByGeorefRef($id) ;
	  foreach ($GtuToGeoref as $key=>$val)
		{
		  $tmp = new GtuToGeoref() ;
		  $tmp->fromArray($val->toArray());
		  $form = new GtuToGeorefForm($tmp);
		  $this->attachEmbedRecord('GtuToGeoref', $form, $key);
		}
  }
  
  public function addGtuToGeoref($num, $values, $order_by=0)
  {
    $options = array( 'georef_ref' => $this->getObject()->getId());
	//print_r( $options);
	//print_r( $values);
    $options = array_merge($options, $values);
	

	$this->attachEmbedRecord('GtuToGeoref', new GtuToGeorefForm(DarwinTable::newObjectFromArray('GtuToGeoref',$options)), $num);
  
  }
  
    public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
	$javascripts[]='/proj4js-2.3.12/proj4js-2.3.12/dist/proj4-src.js';
    $javascripts[]='/js/catalogue_people.js';   
    return $javascripts;
  }
  

}
