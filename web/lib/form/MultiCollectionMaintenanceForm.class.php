<?php

/**
 * CollectionMaintenance form.
 *
 * @package    form
 * @subpackage CollectionMaintenance
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class MultiCollectionMaintenanceForm extends BaseCollectionMaintenanceForm
{
  public function configure()
  {
	$this->useFields(array('people_ref', 'category', 'action_observation', 'description','modification_date_time'));

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));

	$this->widgetSchema['modification_date_time'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
		'image'=>'/images/calendar.gif', 
		'format' => '%day%/%month%/%year%', 
		'years' => $years,
		'with_time' => true
	  ),
	  array('class' => 'from_date')
	  );


    $this->validatorSchema['modification_date_time'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
	  ),
	  array('invalid' => 'Invalid date "from"')
    );
    $this->setDefault('modification_date_time', time());
    $this->widgetSchema['people_ref'] = new widgetFormJQueryDLookup(
      array(
        'model' => 'People',
        'method' => 'getFormatedName',
        'nullable' => true,
        'fieldsHidders' => array(),
      ),
      array('class' => 'hidden',)
    );
    $this->widgetSchema['people_ref']->setLabel('Person');

    $this->validatorSchema['people_ref'] = new sfValidatorInteger();

	$this->widgetSchema['category'] = new sfWidgetFormChoice(array('choices' => array('action' => 'Action', 'observation'=>'Observation')));
	$this->widgetSchema['category']->setLabel('Type');

	$this->widgetSchema['action_observation']->setLabel('Maintenance Achieved');

  $forced_choices = false;
  $default = null;
  if(
    isset($this->options['forced_action_observation_options'])
    && is_array($this->options['forced_action_observation_options'])
    && count($this->options['forced_action_observation_options']) > 0
  ) {
    $forced_choices = $this->options['forced_action_observation_options'];
    $default = current(array_keys($forced_choices));
  }

	$this->widgetSchema['action_observation'] = new widgetFormSelectComplete(array(
	  'model' => 'CollectionMaintenance',
	  'table_method' => 'getDistinctActions',
	  'method' => 'getAction',
	  'key_method' => 'getAction',
	  'add_empty' => false,
	  'change_label' => 'Pick an action in the list',
	  'add_label' => 'Add another action',
    'forced_choices'=>$forced_choices,
    'default'=>$default,
    ));
  }
}
