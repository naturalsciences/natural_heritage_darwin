<?php

/**
 * WidgetProfiles form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
class WidgetProfilesForm extends BaseWidgetProfilesForm
{
  /**
   * @see DarwinModelForm
   */
  public function configure()
  {
    //parent::configure();	
	$this->widgetSchema['duplicate'] = new sfWidgetFormInputHidden(array('default'=>0));
	$this->widgetSchema['duplicate']->setAttributes(array('class'=>'duplicate')); 
	$this->validatorSchema['duplicate'] = new sfValidatorPass();
	$this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['name']->setAttributes(array('class'=>'small_size'));
	
	$this->validatorSchema['name'] = new sfValidatorString(array('required'=>false));
	
	$this->widgetSchema['creator_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Users'), 'add_empty' => false, "default"=>sfContext::getInstance()->getUser()->getId()));

	
	$yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
	$minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(date('Y').'/12/31');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);
	
	$this->widgetSchema['creation_date'] = new widgetFormJQueryFuzzyDate(
      array('culture'=> $this->getCurrentCulture(), 
            'image'=> '/images/calendar.gif',       
            'format' => '%day%/%month%/%year%',    
            'years' => $years,                     
            'empty_values' => $dateText,           
      ),                                      
      array('class' => 'from_date')                
    );
	
	 $this->validatorSchema['creation_date'] = new fuzzyDateValidator(
      array(
        'required' => false,                       
        'from_date' => true,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );
  }
}
