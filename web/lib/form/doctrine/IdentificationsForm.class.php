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
  public function configure()
  {

    $this->useFields(array('id', 'referenced_relation', 'record_id', 'notion_date', 'notion_concerned', 'value_defined', 'determination_status', 'order_by'));
	//JMHerpers 2018 02 15 Inversion of max and Min to have most recent dates on top
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')),intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);
    $choices = array('all'=>'All', 'taxonomy'=> 'Taxon.', 'mineralogy' => 'Miner.', 'chronostratigraphy' => 'Chron.',
      'lithostratigraphy' => 'Litho.', 'lithology' => 'Lithology', 'type'=> 'Type', 
      'sex' => 'Sex', 'stage' => 'Stage', 'social_status' => 'Social', 'rock_form' => 'Rock') ;

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
    $this->validatorSchema['notion_concerned'] = new sfValidatorChoice(array('required' => false, 'choices'=>array_keys($choices)));
    $this->widgetSchema['value_defined'] = new sfWidgetFormInput();
    //ftheeten 2018 09 18 new class identification_subject
    $this->widgetSchema['value_defined']->setAttributes(array('class'=>'xlsmall_size identification_subject'));
    $this->validatorSchema['value_defined'] = new sfValidatorString(array('required' => false, 'trim'=>true));
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
    foreach(Doctrine::getTable('CataloguePeople')->getPeopleRelated('identifications', 'identifier', $this->getObject()->getId()) as $key=>$vals)
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
