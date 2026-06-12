<?php

/**
 * Identifications form.
 *
 * @package    form
 * @subpackage Identifications
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class IdentificationsForm extends BaseIdentificationsForm
{
	protected $index = 0;
	
	 public function setIndex($i)
	{
	  $this->index = $i;
	}
	
  public function configure()
  {

    $this->useFields(array('id', 'referenced_relation', 'record_id', 'notion_date', 'notion_concerned', 'value_defined', 'determination_status',
		'identifications_count_min', 'identifications_count_max',
		'identifications_count_males_min', 'identifications_count_males_max',
      'identifications_count_females_min', 'identifications_count_females_max', 
      'identifications_count_juveniles_min', 'identifications_count_juveniles_max', 'identifications_count_types_min', 'identifications_count_types_max',	'order_by'));

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);
    $choices = Identifications::$categories ;
	


    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    $this->widgetSchema['notion_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                             'image'=>'/images/calendar.gif', 
                                                                             'format' => '%day%/%month%/%year%', 
                                                                             'years' => $years,
                                                                             'empty_values' => $dateText,
                                                                            ),
                                                                       array('class' => 'to_date')
                                                                      );
    $this->validatorSchema['notion_date'] = new fuzzyDateValidator(array('required' => false,
                                                                         'from_date' => true,
                                                                         'min' => $minDate,
                                                                         'max' => $maxDate, 
                                                                         'empty_value' => $dateLowerBound,
                                                                        ),
                                                                   array('invalid' => 'Date provided is not valid',
                                                                        )
                                                                  );
    $this->widgetSchema['notion_concerned'] = new sfWidgetFormChoice(array(
        'choices' => $choices
      ));
    $this->validatorSchema['notion_concerned'] = new sfValidatorChoice(array('required' => true, 'choices'=>array_keys($choices)));
    $this->widgetSchema['value_defined'] = new sfWidgetFormInput();
    //ftheeten 2018 09 18 new class identification_subject
    $this->widgetSchema['value_defined']->setAttributes(array('class'=>'xlsmall_size identification_subject'));
    $this->validatorSchema['value_defined'] = new sfValidatorString(array('required' => false, 'trim'=>true), array("required"=>"Identification subjet is missing"));
    $this->widgetSchema['determination_status'] = new widgetFormSelectComplete(array(
        'model' => 'Identifications',
        'table_method' => 'getDistinctDeterminationStatus',
        'method' => 'getDeterminationStatus',
        'key_method' => 'getDeterminationStatus',
        'add_empty' => true,
        'change_label' => '',
        'add_label' => '',
    ));
    $this->widgetSchema['determination_status']->setAttributes(array('class'=>'vvvsmall_size'));
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_by'] = new sfValidatorInteger();
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

    /* Identifiers sub form */
    
    $subForm = new sfForm();
    $this->embedForm('Identifiers',$subForm);   
    foreach(Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $this->getObject()->getId()) as $key=>$vals)
    {
      $form = new IdentifiersForm($vals);
      $this->embeddedForms['Identifiers']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('Identifiers', $this->embeddedForms['Identifiers']);

    $subForm = new sfForm();
    $this->embedForm('newIdentifier',$subForm);

    /*Identifications post-validation to empty null values*/
    $this->mergePostValidator(new IdentificationsValidatorSchema());
	
	//counter
	
	$this->widgetSchema['accuracy'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	 $this->widgetSchema['accuracy']->setAttributes(array('class' => 'set_accuracy'));
$this->validatorSchema['accuracy'] = new sfValidatorPass();
    //ftheeten 2016 06 22
    $this->widgetSchema['accuracy_males'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true
    ));
	$this->widgetSchema['accuracy_males']->setAttributes(array('class' => 'set_accuracy_males'));
    $this->validatorSchema['accuracy_males'] = new sfValidatorPass(); 
    
    
    $this->widgetSchema['accuracy_females'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	$this->widgetSchema['accuracy_females']->setAttributes(array('class' => 'set_accuracy_females'));		
    $this->validatorSchema['accuracy_females'] = new sfValidatorPass(); 
    
     $this->widgetSchema['accuracy_juveniles'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	$this->widgetSchema['accuracy_juveniles']->setAttributes(array('class' => 'set_accuracy_juveniles'));
    $this->validatorSchema['accuracy_juveniles'] = new sfValidatorPass();
	
	 $this->widgetSchema['accuracy_types'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	$this->widgetSchema['accuracy_types']->setAttributes(array('class' => 'set_accuracy_types'));
    $this->validatorSchema['accuracy_types'] = new sfValidatorPass();

	$default_accuracy=0;
	$default_accuracy_males=0;
	$default_accuracy_females=0;
	$default_accuracy_juveniles=0;
	$default_accuracy_types=0;
	$display_max_count="display:none";
	$display_max_count_males="display:none";
	$display_max_count_females="display:none";
	$display_max_count_juveniles="display:none";
	$display_max_count_types="display:none";
	 if (!$this->getObject()->isNew())
	 {
		$min=$this->getObject()->getIdentificationsCountMin();
		$max=$this->getObject()->getIdentificationsCountMax();
		$min_males=$this->getObject()->getIdentificationsCountMalesMin();
		$max_males=$this->getObject()->getIdentificationsCountMalesMax();
		$min_females=$this->getObject()->getIdentificationsCountFemalesMin();
		$max_females=$this->getObject()->getIdentificationsCountFemalesMax();
		$min_juveniles=$this->getObject()->getIdentificationsCountJuvenilesMin();
		$max_juveniles=$this->getObject()->getIdentificationsCountJuvenilesMax();
		$min_types=$this->getObject()->getIdentificationsCountTypesMin();
		$max_types=$this->getObject()->getIdentificationsCountTypesMax();
		$default_accuracy= $this->compare_count($min, $max);
		$default_accuracy_males= $this->compare_count($min_males, $max_males);
		$default_accuracy_females= $this->compare_count($min_females, $max_females);
		$default_accuracy_juveniles= $this->compare_count($min_juveniles, $max_juveniles);
		$default_accuracy_types= $this->compare_count($min_types, $max_types);
		if($default_accuracy==1)
		{
			$display_max_count="";
		}
		else
		{
			$default_accuracy=0;	
		}
		
		if($default_accuracy_males==1)
		{
			$display_max_count_males="";
		}
		else
		{
			$default_accuracy_males=0;	
		}
		
		if($default_accuracy_females==1)
		{
			$display_max_count_females="";
		}
		else
		{
			$default_accuracy_females=0;	
		}
		
		if($default_accuracy_females==1)
		{
			$display_max_count_juveniles="";
		}
		else
		{
			$default_accuracy_females=0;	
		}
		
		if($default_accuracy_types==1)
		{
			$display_max_count_types="";
		}
		else
		{
			$default_accuracy_types=0;	
		}
	  }
	
    $this->setDefault('accuracy', $default_accuracy);
	$this->setDefault('accuracy_males', $default_accuracy_males);
	$this->setDefault('accuracy_females', $default_accuracy_females);
    $this->setDefault('accuracy_juveniles', $default_accuracy_juveniles);
	$this->setDefault('accuracy_types', $default_accuracy_types);
    
    
 
	
	$this->widgetSchema['identifications_count_min']->setAttributes(array('class' => 'ident_counter ident_counter_min vident_counter_min'));
	$this->widgetSchema['identifications_count_max']->setAttributes(array('class' => 'ident_counter ident_counter_max vident_counter_max', "style"=>$display_max_count));
	
	$this->widgetSchema['identifications_count_males_min']->setAttributes(array('class' => 'ident_counter ident_counter_males_min vident_counter_males_min'));
	$this->widgetSchema['identifications_count_males_max']->setAttributes(array('class' => 'ident_counter  ident_counter_males_max vident_counter_males_max', "style"=>$display_max_count_males));
	
	$this->widgetSchema['identifications_count_females_min']->setAttributes(array('class' => 'ident_counter ident_counter_females_min vident_counter_females_min'));
	$this->widgetSchema['identifications_count_females_max']->setAttributes(array('class' => 'ident_counter  ident_counter_females_max vident_counter_females_max', "style"=>$display_max_count_females ));	
	
	$this->widgetSchema['identifications_count_juveniles_min']->setAttributes(array('class' => 'ident_counter ident_counter_juveniles_min vident_counter_juveniles_min'));
	$this->widgetSchema['identifications_count_juveniles_max']->setAttributes(array('class' => 'ident_counter  ident_counter_juveniles_max vident_counter_juveniles_max', "style"=>$display_max_count_juveniles));
	
	$this->widgetSchema['identifications_count_types_min']->setAttributes(array('class' => 'ident_counter ident_counter_types_min vident_counter_types_min'));
	$this->widgetSchema['identifications_count_types_max']->setAttributes(array('class' => 'ident_counter  ident_counter_types_max vident_counter_types_max', "style"=>$display_max_count_types));
	
	$this->validatorSchema['identifications_count_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_males_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_males_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_females_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_females_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_juveniles_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_juveniles_max'] = new sfValidatorInteger(array('required'=>false));
	
	$this->validatorSchema['identifications_count_types_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_types_max'] = new sfValidatorInteger(array('required'=>false));



	
	
	  $this->mergePostValidator(new sfValidatorSchemaCompare('identifications_count_min', '<=', 'identifications_count_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));
    
     //ftheeten 2016 06 22
	$this->mergePostValidator(new sfValidatorSchemaCompare('identifications_count_males_min', '<=', 'identifications_count_males_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));
    $this->mergePostValidator(new sfValidatorSchemaCompare('identifications_count_females_min', '<=', 'identifications_count_females_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));
    
     //ftheeten 2016 06 22
	$this->mergePostValidator(new sfValidatorSchemaCompare('identifications_count_juveniles_min', '<=', 'identifications_count_juveniles_max',
      array(),
      array('invalid' => 'The min number ("%left_field%") must be lower or equal the max number ("%right_field%")' )
    ));
  }

  public function addIdentifiers($num,$people_ref, $order_by=0)
  {
      $options = array('referenced_relation' => 'identifications', 'people_type' => 'identifier', 'order_by' => $order_by, 'people_ref' => $people_ref);
      $val = new CataloguePeople();
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new IdentifiersForm($val);
      $this->embeddedForms['newIdentifier']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newIdentifier', $this->embeddedForms['newIdentifier']);
  }
  
  protected function compare_count($min, $max)
  {
	  $returned=0;
	  if(!is_null($min)&& !is_null($max))
	  {
		 if(is_numeric($min)&& is_numeric($max))
		  {
			  if((int)$min!=(int)$max)
			  {
				  $returned=1;
			  }
		  }  
	  }
	  return $returned;
  }


}
