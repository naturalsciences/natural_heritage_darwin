<?php

/**
 * CatalogueProperties form.
 *
 * @package    form
 * @subpackage CatalogueProperties
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CataloguePropertiesForm extends BaseCataloguePropertiesForm
{
  public function configure()
  {
    unset(
      $this['property_sub_type_indexed'],
      $this['property_qualifier_indexed'],
      $this['property_method_indexed'],
      $this['property_tool_indexed'],
      $this['date_from_mask'],
      $this['date_to_mask'],
      $this['id']
    );

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal)).'/1/1 0:0:0');
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal)).'/12/31 23:59:59');
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array('culture'=>  $this->getCurrentCulture(),
                                                                               'image'=> '/images/calendar.gif', 
                                                                               'format' => '%day%/%month%/%year%', 
                                                                               'years' => $years,
									       'with_time' => true ),
                                                                         array('class' => 'from_date')
                                                                        );
    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array('culture'=> $this->getCurrentCulture(),
                                                                             'image'=> '/images/calendar.gif', 
                                                                             'format' => '%day%/%month%/%year%', 
                                                                             'years' => $years,
                                                                             'with_time' => true),
                                                                       array('class' => 'to_date')
                                                                      );

    $this->validatorSchema['date_from'] = new fuzzyDateValidator(array(
      'required' => false,
      'from_date' => true,
      'min' => $minDate,
      'max' => $maxDate,
      'empty_value' => $dateLowerBound,
      'with_time' => true
    ),
    array('invalid' => 'Invalid date "from"')
    );

    $this->validatorSchema['date_to'] = new fuzzyDateValidator(array(
	'required' => false,
	'from_date' => false,
	'min' => $minDate,
	'max' => $maxDate,
	'empty_value' => $dateUpperBound,
	'with_time' => true
    ),
    array('invalid' => 'Invalid date "to"')
    );

     $this->validatorSchema->setPostValidator(
	new sfValidatorSchemaCompare(
	  'date_from',
	  '<=', 
          'date_to', 
          array('throw_global_error' => true),
	  array('invalid'=>'The "from" date cannot be above the "to" date.')
	)
      );
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->widgetSchema['property_type'] = new widgetFormSelectComplete(array(
        'model' => 'CatalogueProperties',
        'table_method' => array('method' => 'getDistinctType', 'parameters' => array($this->options['ref_relation'])),
        'method' => 'getType',
        'key_method' => 'getType',
        'add_empty' => true,
	'change_label' => 'Pick a type in the list',
	'add_label' => 'Add another type',
    ));
    
    $this->widgetSchema['property_sub_type'] = new widgetFormSelectComplete(array(
        'model' => 'CatalogueProperties',
	'change_label' => 'Pick a sub-type in the list',
	'add_label' => 'Add another sub-type',
    ));
    if(! $this->getObject()->isNew())
      $this->widgetSchema['property_sub_type']->setOption('forced_choices', Doctrine::getTable('CatalogueProperties')->getDistinctSubType($this->getObject()->getPropertyType()) );
    else
      $this->widgetSchema['property_sub_type']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['property_qualifier'] = new widgetFormSelectComplete(array(
        'model' => 'CatalogueProperties',
	'change_label' => 'Pick a qualifier in the list',
	'add_label' => 'Add another qualifier',
    ));
    if(! $this->getObject()->isNew())
      $this->widgetSchema['property_qualifier']->setOption('forced_choices', Doctrine::getTable('CatalogueProperties')->getDistinctQualifier($this->getObject()->getPropertySubType()) );
    else
      $this->widgetSchema['property_qualifier']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['property_accuracy_unit'] = new widgetFormSelectComplete(array(
        'model' => 'CatalogueProperties',
	'change_label' => '',//'Pick an unit in the list',
	'add_label' => '',//'Add another unit',
    ));
    if(! $this->getObject()->isNew())
      $this->widgetSchema['property_accuracy_unit']->setOption('forced_choices', Doctrine::getTable('CatalogueProperties')->getDistinctUnit($this->getObject()->getPropertyType()) );
    else
      $this->widgetSchema['property_accuracy_unit']->setOption('forced_choices', array(''=>''));

    $this->widgetSchema['property_unit'] = new widgetFormSelectComplete(array(
        'model' => 'CatalogueProperties',
	'change_label' => '',//Pick a unit in the list',
	'add_label' => '',//'Add another unit'
    ));
    if(! $this->getObject()->isNew())
      $this->widgetSchema['property_unit']->setOption('forced_choices', Doctrine::getTable('CatalogueProperties')->getDistinctUnit($this->getObject()->getPropertyType()) );
    else
      $this->widgetSchema['property_unit']->setOption('forced_choices',array(''=>''));

    $this->widgetSchema['property_method'] = new sfWidgetFormInput();
    $this->widgetSchema['property_method']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['property_tool'] = new sfWidgetFormInput();
    $this->widgetSchema['property_tool']->setAttributes(array('class'=>'medium_size'));

    $this->embedRelation('PropertiesValues');
    
    $subForm = new sfForm();
    $this->embedForm('newVal',$subForm);
  }
  
  public function addValue($num)
  {
      $val = new PropertiesValues();
      $val->CatalogueProperties = $this->getObject();
      $form = new PropertiesValuesForm($val);
  
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
	  if (!isset($value[$name]['property_value']))
	  {
	    unset($this->embeddedForms['newVal'][$name]);
	  }
	}

	$value = $this->getValue('PropertiesValues');
	foreach($this->embeddedForms['PropertiesValues']->getEmbeddedForms() as $name => $form)
	{
	  
	  if (!isset($value[$name]['property_value']))
	  {
	    $form->getObject()->delete();
	    unset($this->embeddedForms['PropertiesValues'][$name]);
	  }
	}
      }
      return parent::saveEmbeddedForms($con, $forms);
    }

}
