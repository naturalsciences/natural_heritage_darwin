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
      'lat_long_accuracy', 'elevation', 'elevation_accuracy'));

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
    return $javascripts;
  }

  public function getStylesheets() {
    $items=parent::getStylesheets();
    $items['/leaflet/leaflet.css']='all';
    return $items;
  }
}
