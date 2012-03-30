<?php

/**
 * Expeditions form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team (darwin-ict@naturalsciences.be)
 *
 */
class ExpeditionsForm extends BaseExpeditionsForm
{
 /**
  * Configure the form with its widgets and validators
  */
  public function configure()
  {
    $this->useFields(array('name','expedition_from_date', 'expedition_to_date'));
    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['expedition_from_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                                      'image'=>'/images/calendar.gif', 
                                                                                      'format' => '%day%/%month%/%year%', 
                                                                                      'years' => $years,
                                                                                      'empty_values' => $dateText,
                                                                                     ),
                                                                                array('class' => 'from_date')
                                                                               );
    $this->widgetSchema['expedition_to_date'] = new widgetFormJQueryFuzzyDate(array('culture'=>$this->getCurrentCulture(), 
                                                                                    'image'=>'/images/calendar.gif', 
                                                                                    'format' => '%day%/%month%/%year%', 
                                                                                    'years' => $years,
                                                                                    'empty_values' => $dateText, 
                                                                                   ),
                                                                              array('class' => 'to_date')
                                                                             );
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['name'] = new sfValidatorString(array('required' => true, 'trim' => true));
    $this->validatorSchema['expedition_from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                  'from_date' => true,
                                                                                  'min' => $minDate,
                                                                                  'max' => $maxDate, 
                                                                                  'empty_value' => $dateLowerBound,
                                                                                 ),
                                                                            array('invalid' => 'Date provided is not valid',
                                                                                 )
                                                                           );
    $this->validatorSchema['expedition_to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                                'from_date' => false,
                                                                                'min' => $minDate,
                                                                                'max' => $maxDate,
                                                                                'empty_value' => $dateUpperBound,
                                                                               ),
                                                                          array('invalid' => 'Date provided is not valid',
                                                                               )
                                                                         );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('expedition_from_date', 
                                                                          '<=', 
                                                                          'expedition_to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );

    $this->validatorSchema['Members_holder'] = new sfValidatorPass();
    $this->widgetSchema['Members_holder'] = new sfWidgetFormInputHidden(array('default'=>1));

    $this->loadEmbed('Members');//force load of member
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->bindEmbed('Members', 'addMembers' , $taintedValues);
    parent::bind($taintedValues, $taintedFiles);
  }

  public function addMembers($num, $values, $order_by=0)
  {
    $options = array('referenced_relation' => 'expeditions', 'people_type' => 'member', 'people_ref' => $values['people_ref'], 'order_by' => $order_by,
      'record_id' => $this->getObject()->getId());
    $this->attachEmbedRecord('Members', new PeopleAssociationsForm(DarwinTable::newObjectFromArray('CataloguePeople',$options)), $num);
  }


  public function getEmbedRecords($emFieldName, $record_id = false)
  {
    if($record_id === false)
      $record_id = $this->getObject()->getId();
    if( $emFieldName =='Members' )
      return Doctrine::getTable('CataloguePeople')->getPeopleRelated('expeditions','member', $record_id);
  }

  public function getEmbedRelationForm($emFieldName, $values)
  {
    if( $emFieldName == 'Members')
      return new PeopleAssociationsForm($values);
  }

  public function duplicate($id)
  {
    // reembed duplicated members
    $Catalogue = Doctrine::getTable('CataloguePeople')->findForTableByType('expeditions',$id) ;
    if(isset($Catalogue['member'])) {
      foreach ($Catalogue['member'] as $key=>$val) {
        $this->addMembers($key, array('people_ref' => $val->getPeopleRef()),$val->getOrderBy());
      }
    }
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    $this->saveEmbed('Members', 'people_ref', $forms, array('people_type'=>'member','referenced_relation'=>'expeditions', 'record_id' => $this->getObject()->getId()));
    return parent::saveEmbeddedForms($con, $forms);
  }
}
