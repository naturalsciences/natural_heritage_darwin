<?php

/**
 * Gtu form.
 *
 * @package    form
 * @subpackage Gtu
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class GtuForm extends BaseGtuForm
{
  public function configure()
  {
    $this->useFields(array('code', 'gtu_from_date', 'gtu_to_date', 'latitude', 'longitude',
      'lat_long_accuracy', 'elevation', 'elevation_accuracy',
        //ftheeten 2018 08 08
      'coordinates_source',
	  'latitude_dms_degree', 'latitude_dms_minutes', 'latitude_dms_seconds','longitude_dms_degree', 'longitude_dms_minutes', 
	  'longitude_dms_seconds', 'latitude_utm', 'longitude_utm', 'utm_zone', 'latitude_dms_direction', 'longitude_dms_direction'));

    $this->widgetSchema['code'] = new sfWidgetFormInput();
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['gtu_from_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => true
      ),
      array('class' => 'from_date')
    );

    $this->widgetSchema['gtu_to_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      'with_time' => true
      ),
      array('class' => 'to_date')
    );

    $this->validatorSchema['gtu_from_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
      ),
      array('invalid' => 'Date provided is not valid',)
    );

    $this->validatorSchema['gtu_to_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => false,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateUpperBound,
      'with_time' => true
      ),
      array('invalid' => 'Date provided is not valid',)
    );
    
    	//this group ftheeten 2016 02 05
    $this->widgetSchema['coordinates_source']= new sfWidgetFormChoice(array('choices'=>array('DD'=> 'Decimal', 'DMS'=>'Degrees Minutes Seconds', 'UTM'=>'UTM', 'ISSUE'=>'Issue (to check)')));
	$this->widgetSchema['coordinates_source']->setAttributes(array('class'=>'coordinates_source'));
	$this->widgetSchema['coordinates_source']->setDefault(array(0));
	//$this->validatorSchema['coordinates_source'] = new sfValidatorPass();
	$this->widgetSchema['latitude']->setAttributes(array('class'=>'convertDMS2DDLat convertDD2DMSGeneral'));
	$this->widgetSchema['longitude']->setAttributes(array('class'=>'convertDMS2DDLong convertDD2DMSGeneral'));
	//$this->widgetSchema['latitude_dms_degree']=new sfWidgetFormInputText();
	$this->widgetSchema['latitude_dms_degree']->setAttributes(array('class'=>'DMSLatDeg convertDMS2DDGeneralOnLeave vsmall_size '));
	//$this->validatorSchema['latitude_dms_degree'] = new sfValidatorPass();
	//$this->widgetSchema['longitude_dms_degree']=new sfWidgetFormInputText();
	$this->widgetSchema['longitude_dms_degree']->setAttributes(array('class'=>'DMSLongDeg convertDMS2DDGeneralOnLeave vsmall_size'));
	//$this->validatorSchema['longitude_dms_degree'] = new sfValidatorPass();
	//$this->widgetSchema['latitude_dms_minutes']=new sfWidgetFormInputText();
	$this->widgetSchema['latitude_dms_minutes']->setAttributes(array('class'=>'DMSLatMin convertDMS2DDGeneralOnLeave vsmall_size'));
	//$this->validatorSchema['latitude_dms_minutes'] = new sfValidatorPass();
	//$this->widgetSchema['longitude_dms_minutes']=new sfWidgetFormInputText();
	$this->widgetSchema['longitude_dms_minutes']->setAttributes(array('class'=>'DMSLongMin convertDMS2DDGeneralOnLeave vsmall_size'));
	//$this->validatorSchema['longitude_dms_minutes'] = new sfValidatorPass();
	//$this->widgetSchema['latitude_dms_seconds']=new sfWidgetFormInputText();
	$this->widgetSchema['latitude_dms_seconds']->setAttributes(array('class'=>'DMSLatSec convertDMS2DDGeneralOnLeave lsmall_size'));
	//$this->validatorSchema['latitude_dms_seconds'] =new sfValidatorPass();
	//$this->widgetSchema['longitude_dms_seconds']=new sfWidgetFormInputText();
	$this->widgetSchema['longitude_dms_seconds']->setAttributes(array('class'=>'DMSLongSec convertDMS2DDGeneralOnLeave lsmall_size'));
	//$this->validatorSchema['longitude_dms_seconds'] = new sfValidatorPass();
	$this->widgetSchema['latitude_dms_direction'] = new sfWidgetFormChoice(array('choices' => array('1' => 'North', '-1' => 'South')));
	$this->widgetSchema['latitude_dms_direction']->setAttributes(array('class'=>'DMSLatSign convertDMS2DDGeneralOnChange'));
	//$this->validatorSchema['latitude_dms_direction'] = new sfValidatorPass();
	$this->widgetSchema['longitude_dms_direction'] = new sfWidgetFormChoice(array('choices' => array('-1' => 'West', '1' => 'East')));
	$this->widgetSchema['longitude_dms_direction']->setDefault(array(1));
	$this->widgetSchema['longitude_dms_direction']->setAttributes(array('class'=>'DMSLongSign convertDMS2DDGeneralOnChange'));
	//$this->validatorSchema['longitude_dms_direction'] = new sfValidatorPass();
	
	//$this->widgetSchema['latitude_utm']=new sfWidgetFormInputText();
	$this->widgetSchema['latitude_utm']->setAttributes(array('class'=>'UTMLat UTM2DDGeneralOnLeave'));
	//$this->validatorSchema['latitude_utm'] = new sfValidatorPass();
	
	//UTM
	//$this->widgetSchema['longitude_utm']=new sfWidgetFormInputText();
	$this->widgetSchema['longitude_utm']->setAttributes(array('class'=>'UTMLong UTM2DDGeneralOnLeave'));
	//$this->validatorSchema['longitude_utm'] = new sfValidatorPass();
	
	$this->widgetSchema['utm_zone']=new sfWidgetFormChoice(array('choices'=>array(
	'10N'=> 'UTM WGS84 zone 10N', 
	'11N'=> 'UTM WGS84 zone 11N',
	'12N'=> 'UTM WGS84 zone 12N', 
	'13N'=> 'UTM WGS84 zone 13N', 
	'14N'=> 'UTM WGS84 zone 14N', 
	'15N'=> 'UTM WGS84 zone 15N', 
	'16N'=> 'UTM WGS84 zone 16N',
	'17N'=> 'UTM WGS84 zone 17N', 
	'18N'=> 'UTM WGS84 zone 18N', 
	'19N'=> 'UTM WGS84 zone 19N',

	'20N'=> 'UTM WGS84 zone 20N', 
	'21N'=> 'UTM WGS84 zone 21N',
	'22N'=> 'UTM WGS84 zone 22N', 
	'23N'=> 'UTM WGS84 zone 23N', 
	'24N'=> 'UTM WGS84 zone 24N', 
	'25N'=> 'UTM WGS84 zone 25N', 
	'26N'=> 'UTM WGS84 zone 26N',
	'27N'=> 'UTM WGS84 zone 27N', 
	'28N'=> 'UTM WGS84 zone 28N', 
	'29N'=> 'UTM WGS84 zone 29N',  


	'30N'=> 'UTM WGS84 zone 30N', 
	'31N'=> 'UTM WGS84 zone 31N',
	'32N'=> 'UTM WGS84 zone 32N', 
	'33N'=> 'UTM WGS84 zone 33N', 
	'34N'=> 'UTM WGS84 zone 34N', 
	'35N'=> 'UTM WGS84 zone 35N', 
	'36N'=> 'UTM WGS84 zone 36N',
	'37N'=> 'UTM WGS84 zone 37N', 
	'38N'=> 'UTM WGS84 zone 38N', 
	'39N'=> 'UTM WGS84 zone 39N',

	'40N'=> 'UTM WGS84 zone 40N', 
	'41N'=> 'UTM WGS84 zone 41N',
	'42N'=> 'UTM WGS84 zone 42N', 
	'43N'=> 'UTM WGS84 zone 43N', 
	'44N'=> 'UTM WGS84 zone 44N', 
	'45N'=> 'UTM WGS84 zone 45N', 
	'46N'=> 'UTM WGS84 zone 46N',
	'47N'=> 'UTM WGS84 zone 47N', 
	'48N'=> 'UTM WGS84 zone 48N', 
	'49N'=> 'UTM WGS84 zone 49N',

	'50N'=> 'UTM WGS84 zone 50N', 
	'51N'=> 'UTM WGS84 zone 51N',
	'52N'=> 'UTM WGS84 zone 52N', 
	'53N'=> 'UTM WGS84 zone 53N', 
	'54N'=> 'UTM WGS84 zone 54N', 
	'55N'=> 'UTM WGS84 zone 55N', 
	'56N'=> 'UTM WGS84 zone 56N',
	'57N'=> 'UTM WGS84 zone 57N', 
	'58N'=> 'UTM WGS84 zone 58N', 
	'59N'=> 'UTM WGS84 zone 59N',

	'60N'=> 'UTM WGS84 zone 60N', 
	

	'1S'=> 'UTM WGS84 zone 1S',
	'2S'=> 'UTM WGS84 zone 2S', 
	'3S'=> 'UTM WGS84 zone 3S', 
	'4S'=> 'UTM WGS84 zone 4S', 
	'5S'=> 'UTM WGS84 zone 5S', 
	'6S'=> 'UTM WGS84 zone 6S',
	'7S'=> 'UTM WGS84 zone 7S', 
	'8S'=> 'UTM WGS84 zone 8S', 
	'9S'=> 'UTM WGS84 zone 9S',
	
	'10S'=> 'UTM WGS84 zone 10S', 
	'11S'=> 'UTM WGS84 zone 11S',
	'12S'=> 'UTM WGS84 zone 12S', 
	'13S'=> 'UTM WGS84 zone 13S', 
	'14S'=> 'UTM WGS84 zone 14S', 
	'15S'=> 'UTM WGS84 zone 15S', 
	'16S'=> 'UTM WGS84 zone 16S',
	'17S'=> 'UTM WGS84 zone 17S', 
	'18S'=> 'UTM WGS84 zone 18S', 
	'19S'=> 'UTM WGS84 zone 19S',

	'20S'=> 'UTM WGS84 zone 20S', 
	'21S'=> 'UTM WGS84 zone 21S',
	'22S'=> 'UTM WGS84 zone 22S', 
	'23S'=> 'UTM WGS84 zone 23S', 
	'24S'=> 'UTM WGS84 zone 24S', 
	'25S'=> 'UTM WGS84 zone 25S', 
	'26S'=> 'UTM WGS84 zone 26S',
	'27S'=> 'UTM WGS84 zone 27S', 
	'28S'=> 'UTM WGS84 zone 28S', 
	'29S'=> 'UTM WGS84 zone 29S',  


	'30S'=> 'UTM WGS84 zone 30S', 
	'31S'=> 'UTM WGS84 zone 31S',
	'32S'=> 'UTM WGS84 zone 32S', 
	'33S'=> 'UTM WGS84 zone 33S', 
	'34S'=> 'UTM WGS84 zone 34S', 
	'35S'=> 'UTM WGS84 zone 35S', 
	'36S'=> 'UTM WGS84 zone 36S',
	'37S'=> 'UTM WGS84 zone 37S', 
	'38S'=> 'UTM WGS84 zone 38S', 
	'39S'=> 'UTM WGS84 zone 39S',

	'40S'=> 'UTM WGS84 zone 40S', 
	'41S'=> 'UTM WGS84 zone 41S',
	'42S'=> 'UTM WGS84 zone 42S', 
	'43S'=> 'UTM WGS84 zone 43S', 
	'44S'=> 'UTM WGS84 zone 44S', 
	'45S'=> 'UTM WGS84 zone 45S', 
	'46S'=> 'UTM WGS84 zone 46S',
	'47S'=> 'UTM WGS84 zone 47S', 
	'48S'=> 'UTM WGS84 zone 48S', 
	'49S'=> 'UTM WGS84 zone 49S',

	'50S'=> 'UTM WGS84 zone 50S', 
	'51S'=> 'UTM WGS84 zone 51S',
	'52S'=> 'UTM WGS84 zone 52S', 
	'53S'=> 'UTM WGS84 zone 53S', 
	'54S'=> 'UTM WGS84 zone 54S', 
	'55S'=> 'UTM WGS84 zone 55S', 
	'56S'=> 'UTM WGS84 zone 56S',
	'57S'=> 'UTM WGS84 zone 57S', 
	'58S'=> 'UTM WGS84 zone 58S', 
	'59S'=> 'UTM WGS84 zone 59S',

	'60S'=> 'UTM WGS84 zone 60S'
	
	)));
    
    $this->widgetSchema['utm_zone']->setAttributes(array('class'=>'UTM2DDGeneralOnLeave UTMZone'));

    $this->widgetSchema['lat_long_accuracy']->setLabel('Accuracy');
    $this->widgetSchema['elevation_accuracy']->setLabel('Accuracy');
    $this->validatorSchema['latitude'] = new sfValidatorNumber(array('required'=>false,'trim' => true, 'min' => '-90', 'max'=>'90'));
    $this->validatorSchema['longitude'] = new sfValidatorNumber(array('required'=>false,'trim' => true, 'min' => '-180', 'max'=>'180'));
    $this->validatorSchema['lat_long_accuracy'] = new sfValidatorNumber(array('required'=>false,'trim' => true, 'min' => '0.0000001'));
    $this->validatorSchema['elevation_accuracy'] = new sfValidatorNumber(array('required'=>false, 'trim' => true, 'min' => '0.0000001'));
    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorSchemaCompare(
          'gtu_from_date',
          '<=',
          'gtu_to_date',
          array('throw_global_error' => true),
          array('invalid'=>'The "begin" date cannot be above the "end" date.')
        ),
        new sfValidatorCallback(array('callback'=> array($this, 'checkLatLong'))),
        new sfValidatorCallback(array('callback'=> array($this, 'checkElevation'))),
      )
    ));


    $subForm = new sfForm();
    $this->embedForm('newVal',$subForm);
    $this->embedRelation('TagGroups');
  }

  public function checkElevation($validator, $values)
  {
    if($values['elevation'] != '' && $values['elevation_accuracy'] == '')
    {
      $error = new sfValidatorError($validator, 'You must enter an accuracy for the elevation.' );
      throw new sfvalidatorErrorSchema($validator, array('elevation_accuracy' => $error));
    }
    return $values;
  }

  public function checkLatLong($validator, $values)
  {
    if($values['latitude'] != '' || $values['longitude'] != '')
    {
      if($values['latitude'] == '' || $values['longitude'] == '')
      {
        $error = new sfValidatorError($validator, 'You must enter valid latitude And longitude' );
        $field = 'longitude';
        if($values['latitude'] == '') $field = 'latitude';
        throw new sfvalidatorErrorSchema($validator, array($field => $error));
      }
      if($values['lat_long_accuracy'] == '')
      {
        $error = new sfValidatorError($validator, 'You must enter an accuracy for your position');
        throw new sfvalidatorErrorSchema($validator, array('lat_long_accuracy' => $error));
      }
    }
    return $values;
  }

  public function addValue($num, $group="", $TagGroup = null)
  {
      if(!$TagGroup)
        $val = new TagGroups();
      else
        $val = $TagGroup;
      if($group != '')
      	$val->setGroupName($group);

      $val->Gtu = $this->getObject();
      $form = new TagGroupsForm($val);

      $this->embeddedForms['newVal']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newVal', $this->embeddedForms['newVal']);
   }

    public function bind(array $taintedValues = null, array $taintedFiles = null)
    {
      if(isset($taintedValues['newVal']))
      {
        foreach($taintedValues['newVal'] as $key=>$newVal)
        {
          if (!isset($this['newVal'][$key]))
          {
            $this->addValue($key);
          }
        }
      }
      parent::bind($taintedValues, $taintedFiles);
    }

    public function saveEmbeddedForms($con = null, $forms = null)
    {

      if (null === $forms)
      {
        $value = $this->getValue('newVal');
        foreach($this->embeddedForms['newVal']->getEmbeddedForms() as $name => $form)
        {
          if (!isset($value[$name]['tag_value'])  || $value[$name]['tag_value'] == '')
          {
            unset($this->embeddedForms['newVal'][$name]);
          }
        }

        $value = $this->getValue('TagGroups');
        foreach($this->embeddedForms['TagGroups']->getEmbeddedForms() as $name => $form)
        {

          if (!isset($value[$name]['tag_value']) || $value[$name]['tag_value'] == '' )
          {
            $form->getObject()->delete();
            unset($this->embeddedForms['TagGroups'][$name]);
          }
        }
      }
      return parent::saveEmbeddedForms($con, $forms);
    }

  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/leaflet/leaflet.js';
    $javascripts[]='/js/map.js';
    //ftheeten 2016 02 05
    $javascripts[]='/proj4js-2.3.12/proj4js-2.3.12/dist/proj4-src.js';
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    return $items;
  }
}
