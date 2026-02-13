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
      'identifications_count_juveniles_min', 'identifications_count_juveniles_max',	'order_by'));

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


    $this->setDefault('accuracy', 1);
    $this->validatorSchema['accuracy'] = new sfValidatorPass();
    
    //ftheeten 2016 06 22
    $this->widgetSchema['accuracy_males'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true
    ));
	$this->widgetSchema['accuracy_males']->setAttributes(array('class' => 'set_accuracy_males'));
    $this->setDefault('accuracy_males', 1);
    $this->validatorSchema['accuracy_males'] = new sfValidatorPass(); 
    
    
    $this->widgetSchema['accuracy_females'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	$this->widgetSchema['accuracy_females']->setAttributes(array('class' => 'set_accuracy_females'));		
    $this->setDefault('accuracy_females', 1);
    $this->validatorSchema['accuracy_females'] = new sfValidatorPass(); 
    
     $this->widgetSchema['accuracy_juveniles'] = new sfWidgetFormChoice(array(
        'choices'  => array($this->getI18N()->__('exact'), $this->getI18N()->__('imprecise')),
        'expanded' => true,
    ));
	$this->widgetSchema['accuracy_juveniles']->setAttributes(array('class' => 'set_accuracy_juveniles'));
    $this->setDefault('accuracy_juveniles', 1);
    $this->validatorSchema['accuracy_juveniles'] = new sfValidatorPass(); 
	
	$this->widgetSchema['identifications_count_min']->setAttributes(array('class' => 'ident_counter'));
	$this->widgetSchema['identifications_count_max']->setAttributes(array('class' => 'ident_counter'));
	
	$this->widgetSchema['identifications_count_males_min']->setAttributes(array('class' => 'ident_counter'));
	$this->widgetSchema['identifications_count_males_max']->setAttributes(array('class' => 'ident_counter'));
	
	$this->widgetSchema['identifications_count_females_min']->setAttributes(array('class' => 'ident_counter'));
	$this->widgetSchema['identifications_count_females_max']->setAttributes(array('class' => 'ident_counter'));	
	
	$this->widgetSchema['identifications_count_juveniles_min']->setAttributes(array('class' => 'ident_counter'));
	$this->widgetSchema['identifications_count_juveniles_max']->setAttributes(array('class' => 'ident_counter'));
	
	
	$this->validatorSchema['identifications_count_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_males_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_males_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_females_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_females_max'] = new sfValidatorInteger(array('required'=>false));

	$this->validatorSchema['identifications_count_juveniles_min'] = new sfValidatorInteger(array('required'=>false));
	$this->validatorSchema['identifications_count_juveniles_max'] = new sfValidatorInteger(array('required'=>false));



	
	
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

}
