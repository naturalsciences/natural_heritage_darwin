<?php

/**
 * TaxonomyMetadata form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TaxonomyMetadataForm extends BaseTaxonomyMetadataForm
{
  public function configure()
  {
    $this->useFields(array('taxonomy_name', 'is_reference_taxonomy', 'definition', 'url_website', 'url_webservice', 'creation_date', 'source'));
    $this->widgetSchema['taxonomy_name'] = new sfWidgetFormInput();
    $this->widgetSchema['taxonomy_name']->setAttributes(array('class'=>'large_size'));
    $this->widgetSchema['table'] = new sfWidgetFormInputHidden(array('default'=>'taxonomymetadata'));
    
    $this->validatorSchema['table'] = new sfValidatorString(array('required' => false));
    
    
     $this->widgetSchema['is_reference_taxonomy'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_reference_taxonomy'] = new sfValidatorBoolean(array('required' => false));
    
    $this->widgetSchema['source'] = new sfWidgetFormInput();
    $this->widgetSchema['source']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['source'] = new sfValidatorString(array('required' => true));
    
    $this->widgetSchema['url_website'] = new sfWidgetFormInput();
    $this->widgetSchema['url_website']->setAttributes(array('class'=>'medium_size'));
     $this->widgetSchema['url_webservice'] = new sfWidgetFormInput();
    $this->widgetSchema['url_webservice']->setAttributes(array('class'=>'medium_size'));
    
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    //ftheeten 2016 07 07
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);
    
      
    $this->widgetSchema['creation_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
    $this->validatorSchema['creation_date'] = new fuzzyDateValidator(array(
      'required' => true,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',
    ));
    
  }
  
    public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/jquery-datepicker-lang.js';
    $javascripts[]='/js/ui.complete.js';
	
    return $javascripts;
  }

  public function getStylesheets()
  {
    $javascripts=parent::getStylesheets();
    $javascripts['/css/ui.datepicker.css']='all';
    return $javascripts;
  }
}
