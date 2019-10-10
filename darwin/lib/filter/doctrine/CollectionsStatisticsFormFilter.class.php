<?php

//ftheeten 2018 24 24
class CollectionsStatisticsFormFilter extends BaseCollectionsFormFilter
{
  public function configure()
  {
    //$this->useFields(array('id'));
    //$this->widgetSchema['id'] = new sfWidgetFormInputText(array(), array("class"=>"id_collection"));
	
	$this->widgetSchema['id'] =new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'ref_name',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));
    //ftheeten 2017 01 13
    $this->widgetSchema['id']->setAttributes(array('class'=>'collection_ref'));
	// $this->widgetSchema['collection_ref']->addOption('default',$this->getOption('collection_id'));
    $minDate = new FuzzyDateTime(strval(min(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max(range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')))).'/12/31'));
    $maxDate->setStart(false);
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
    $this->widgetSchema['from_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                     array('class' => 'from_date', 'id' => 'from_date')
                                                                    );
    $this->widgetSchema['to_date'] = new widgetFormJQueryFuzzyDate($this->getDateItemOptions(),
                                                                   array('class' => 'to_date')
                                                                  );
    $this->widgetSchema->setNameFormat('statistics[%s]');
    $this->widgetSchema->setLabels(array('from_date' => 'Between',
                                         'to_date' => 'and',
                                        )
                                  );
    $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size ig_num'));
    $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
    $this->validatorSchema['from_date'] = new fuzzyDateValidator(array('required' => false,
                                                                       'from_date' => true,
                                                                       'min' => $minDate,
                                                                       'max' => $maxDate, 
                                                                       'empty_value' => $dateLowerBound,
                                                                      ),
                                                                 array('invalid' => 'Date provided is not valid',)
                                                                );
    $this->validatorSchema['to_date'] = new fuzzyDateValidator(array('required' => false,
                                                                     'from_date' => false,
                                                                     'min' => $minDate,
                                                                     'max' => $maxDate,
                                                                     'empty_value' => $dateUpperBound,
                                                                    ),
                                                               array('invalid' => 'Date provided is not valid',)
                                                              );
    $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare('from_date', 
                                                                          '<=', 
                                                                          'to_date', 
                                                                          array('throw_global_error' => true), 
                                                                          array('invalid'=>'The "begin" date cannot be above the "end" date.')
                                                                         )
                                            );
	
	
  }
  
  
  
  public function getJavascripts()
  {
	$javascripts[]=Array();
	$javascripts=parent::getJavascripts();
   
    $javascripts[]='/js/jquery-datepicker-lang.js';
	
    $javascripts[]='/js/ui.complete.js';
   
	
	
    return $javascripts;
  }

  public function getStylesheets()
  {
    $javascripts=parent::getStylesheets();
    $javascripts['/css/ui.datepicker.css']='all';
    $javascripts['/css/main.css']='all';

    return $javascripts;
  }
}
?>