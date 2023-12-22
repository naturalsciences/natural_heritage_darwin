<?php

/**
 * ClassificationSynonymies form.
 *
 * @package    form
 * @subpackage ClassificationSynonymies
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ClassificationSynonymiesForm extends BaseClassificationSynonymiesForm
{
  public function configure()
  {
    unset($this['id'], $this['is_basionym'], $this['group_id'], $this['syn_date_mask']);

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['group_name'] = new sfWidgetFormChoice(array(
    	'choices' => Doctrine_Core::getTable('ClassificationSynonymies')->findGroupnames(),
	'expanded' => false)
    );

    $this->widgetSchema['record_id'] = new widgetFormJQueryDLookup(
      array(
	'model' => DarwinTable::getModelForTable($this->options['table']),
	'method' => 'getName',
	'nullable' => false,
        'fieldsHidders' => array('classification_synonymies_group_name',),
      ),
      array('class' => 'hidden',)
    );
	
	$yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
	$minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));

	
    
	 $this->widgetSchema['syn_date'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );

    $this->widgetSchema['merge'] = new sfWidgetFormInputCheckbox();

    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => true));
    $this->validatorSchema['merge'] = new sfValidatorChoice(array('required' => true,'choices' => array('true', 't', 'yes', 'y', 'on', 1)));
	
	 $this->validatorSchema['syn_date'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid',
    ));
  }
}