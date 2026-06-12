<?php

class MaAddIdentificationForm extends BaseForm
{
  public function configure()
  {
	  $this->widgetSchema->setNameFormat('mass_action[MassActionForm][add_identification][%s]');
     $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMax')), intval(sfConfig::get('dw_yearRangeMin')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $maxDate->setStart(false);
    $choices = Identifications::$categories ;

    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['referenced_relation'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['record_id'] = new sfValidatorInteger(array('required' => false));
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
	 $this->validatorSchema['determination_status'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['determination_status']->setAttributes(array('class'=>'vvvsmall_size'));
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_by'] = new sfValidatorInteger(array('required' => false));
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));

	$this->widgetSchema['use_taxon_as_value'] = new WidgetFormInputCheckboxDarwin();
	$this->validatorSchema['use_taxon_as_value'] = new sfValidatorBoolean(array('required' => false));//sfValidatorPass();

   
    $choices_b=Array("add"=>"Add identification", "replace_last"=>"Replace last identification", "replace_all"=>"Delete all identifications and replace");
	
	$this->widgetSchema['behaviour'] = new sfWidgetFormChoice(array(
        'choices' => $choices_b
      ));
	$this->validatorSchema['behaviour'] = new sfValidatorChoice(array('required' => false, 'choices'=>array_keys($choices_b)));
     /* Identifiers sub form */
	$subForm = new sfForm();
    $this->embedForm('Peoples',$subForm);
	$this->validatorSchema['Peoples'] = new sfValidatorPass(array('required' => false));
    /*Identifications post-validation to empty null values*/

  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {

	if(isset($taintedValues['Peoples'])&& is_array($taintedValues['Peoples']))
	{

		 foreach($taintedValues['Peoples'] as $key=>$newVal) 
		 {

			if (!isset($this['Peoples'][$key]))
			{

				$this->addPeopleValue($key);
			}
		 }
	}
	else 
	{
      $this->offsetUnset('Peoples') ;
      $subForm = new sfForm();
      $this->embedForm('Peoples',$subForm);
      $taintedValues['Peoples'] = array();
    }
	parent::bind($taintedValues, $taintedFiles);
  }
   
  public function addPeopleValue($num)
  {
	 
      $form = new PeopleLineForm(null,array('num'=>$num));
      $this->embeddedForms['Peoples']->embedForm($num, $form);
     // $this->embedForm('Peoples', $this->embeddedForms['Peoples']);
	  return $form;
  }  
 

  public function doMassAction($user_id, $items, $values)
  {
	
    $query = Doctrine_Query::create()->select('id, taxon_name')->from('Specimens s');
    $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
    $results = $query->execute();
	
	
	$notion_concerned=$values["notion_concerned"];
	$value_defined=$values["value_defined"];
	$determination_status=$values["determination_status"];
	
	$array_date=$values["notion_date"]->getDateTimeAsArray();
	$iso_date=$array_date["year"]."-".$array_date["month"]."-".$array_date["day"];
	$mask=$values["notion_date"]->getMask();
	$use_taxon_flag=false;
	$use_taxon=$values["use_taxon_as_value"];
	
	$behaviour=$values["behaviour"];
	if(strtolower($use_taxon)=="on" or $use_taxon==1)
	{
		$use_taxon_flag=true;
	}
	
    $people=$values["Peoples"];
	
	$people_ref=Array();
	foreach( $people as $p)
	{
		$people_ref[]=$p["people_ref"];
	}
	
    foreach($results as $result)
    {
	  
	  if($behaviour=="replace_last")
		{
			$ident= Doctrine_Core::getTable('Identifications')->getLastIdentificationRelated('specimens',$result->getId());
			if($ident!==null)
			{
				$ident->delete();
			}
		}
		elseif($behaviour=="replace_all")
		{
			 $exists= Doctrine_Core::getTable('Identifications')->getIdentificationsRelated('specimens',$result->getId());
			 foreach($exists as $ident)
			 {
				  $ident->delete();
			 }
		}
	  if($use_taxon_flag)
	  {
		  $value_defined=$result->getTaxonName();
	  }
	  $identification=new Identifications();
	  $identification->setReferencedRelation("specimens");
	  $identification->setRecordId($result->getId());
	  $identification->setNotionConcerned($notion_concerned);
	  $identification->setNotionDate($iso_date);
	  $identification->setNotionDateMask($mask);
	  $identification->setValueDefined($value_defined);
	  $identification->setDeterminationStatus($determination_status);
	  $exists= Doctrine_Core::getTable('Identifications')->getIdentificationsRelated('specimens',$result->getId());
	  $identification->setOrderBy(count($exists));
	  $identification->save();
	  $i=0;
      foreach($people_ref as $id_p)
	  {
		  $cp=new CataloguePeople();
		  $cp->setReferencedRelation("identifications");
		  $cp->setPeopleType("identifier");
		  $cp->setPeopleRef($id_p);
		  $cp->setRecordId($identification->getId());
		  $cp->setOrderBy($i);
		  $cp->save();
		  $i++;
		  
	  }
	 
	  
	  
    }
  }

}
