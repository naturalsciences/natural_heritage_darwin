<?php

//ftheeten 2016 11 24

 class SpecimensCodeIGFormFilter extends BaseSpecimensFormFilter
{
    public function configure()
    {
        $this->useFields(array('collection_ref','ig_num'));
        
         $this->addPagerItems();
        $this->widgetSchema['caller_id'] = new sfWidgetFormInputHidden();
        $this->validatorSchema['caller_id'] = new sfValidatorString(array('required' => false));
        
        
        $this->widgetSchema['collection_ref'] = new sfWidgetFormInputText();
        //$this->widgetSchema['collection_ref']->setAttribute('disabled','disable');
        $this->widgetSchema['collection_ref']->setAttribute('class','collection_chooser');
        $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));
        
        $this->widgetSchema['ig_num'] = new sfWidgetFormInputText();
        $this->widgetSchema['ig_from_date'] = new widgetFormJQueryFuzzyDate(
          $this->getDateItemOptions(),
          array('class' => 'from_date')
        );
        
        	//ftheeten 2015 01 08
        $this->widgetSchema['code_boolean'] = new sfWidgetFormChoice(array('choices' => array('OR' => 'OR', 'AND' => 'AND')));
        ////ftheeten 2015 01 08
        $this->validatorSchema['code_boolean'] = new sfValidatorPass();
        
        //ftheeten 2015 09 09
        $this->widgetSchema['code_exact_match'] = new sfWidgetFormInputCheckbox();//array('default' => FALSE));
           ////ftheeten 2015 09 09
        $this->validatorSchema['code_exact_match'] = new sfValidatorPass();
/*
        $this->widgetSchema['ig_to_date'] = new widgetFormJQueryFuzzyDate(
          $this->getDateItemOptions(),
          array('class' => 'to_date')
        );

        $this->widgetSchema['ig_num']->setAttributes(array('class'=>'small_size'));
        $this->validatorSchema['ig_num'] = new sfValidatorString(array('required' => false, 'trim' => true));
        $this->validatorSchema['ig_from_date'] = new fuzzyDateValidator(array(
          'required' => false,
          'from_date' => true,
          'min' => $minDate,
          'max' => $maxDate,
          'empty_value' => $dateLowerBound,
          ),
          array('invalid' => 'Date provided is not valid',)
        );

        $this->validatorSchema['ig_to_date'] = new fuzzyDateValidator(array(
          'required' => false,
          'from_date' => false,
          'min' => $minDate,
          'max' => $maxDate,
          'empty_value' => $dateUpperBound,
          ),
          array('invalid' => 'Date provided is not valid',)
        );

        $this->validatorSchema->setPostValidator(new sfValidatorSchemaCompare(
          'ig_from_date',
          '<=',
          'ig_to_date',
          array('throw_global_error' => true),
          array('invalid'=>'The "begin" date cannot be above the "end" date.')
        ));
*/
    
        $subForm = new sfForm();
        $this->embedForm('Codes',$subForm);
        
        

    }
    
    
     public function addCodeValue($num)
  {
      $form = new CodeLineForm();
      $this->embeddedForms['Codes']->embedForm($num, $form);
      $this->embedForm('Codes', $this->embeddedForms['Codes']);
  }
  
    public function addCodesColumnQuery($query, $field, $val)
  {

    $str_params = '';
    $str_params_part = '' ;
    $params = array();
    $params_part = array() ;
    foreach($val as $i => $code)
    {
      if(empty($code)) continue;
      $sql = '';
      $sql_params = array();
      $has_query = false;
      if(ctype_digit($code['code_from']) && ctype_digit($code['code_to'])) {
          $sql = " code_num BETWEEN ? AND ? ";
          $sql_params = array($code['code_from'], $code['code_to']);
          $has_query = true;
        }
        if($code['code_part']  != '') {
          if($has_query) $sql .= ' AND ';
          //$sql .= " full_code_indexed ilike '%' || fulltoindex(?) || '%' ";
          //ftheeten 20140922
		    //ftheeten 20150909 (if on exact match
		  if($this->code_exact_match==FALSE)
		  {
			//ftheeten 20140922
			$sql .= " full_code_indexed ilike (SELECT '%'||fulltoindex||'%' FROM fulltoindex(?))";
		  }
		  else if($this->code_exact_match==TRUE)
		  {
			$sql .= " full_code_indexed ilike (SELECT fulltoindex FROM fulltoindex(?))";
		  }
		  
		  $sql_params[] = $code['code_part'];
          $has_query = true;
        }
        //if($has_query)
        //  $query->addWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
		if($has_query)
		{
		//ftheeten 2015 01 08
			if($this->code_boolean=='OR')
			{
				$query->orWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
			}
			else
			{
				$query->andWhere("EXISTS(select 1 from codes where  referenced_relation='specimens' and record_id = s.id AND $sql)", $sql_params);
			}
		}
	
	}

    return $query ;
  }
  
   public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/ui.complete.js';
    return $javascripts;
  }

  

}