<?php

class MaDonatorForm extends BaseForm
{
   public function configure()
  {
  
		 $this->widgetSchema->setNameFormat('mass_action[MassActionForm][donators][%s]');
		 $subForm = new sfForm();
		$this->embedForm('Peoples',$subForm);
		
		  $this->validatorSchema['Peoples'] = new sfValidatorPass(array('required' => false));

		
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
		
		$list_people=Array();
		foreach($values['Peoples'] as $people_val )
		{
			$list_people[]=$people_val['people_ref']; 
			
		}
		
		$query = Doctrine_Query::create()->select('id')->from('Specimens s');
		$query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
		$results = $query->execute();
		foreach($results as $result)
		{
			$old_data=Doctrine_Core::getTable('CataloguePeople')->getPeopleRelated('specimens','donator',  $result->getId());
			foreach($old_data as $obj)
			{
				$obj->delete();
			}
			$i=1;
			foreach($list_people  as $p_id)
			{
				if($p_id!==null)
				{
					$cataloguepeople = new CataloguePeople();
					$cataloguepeople->setRecordId($result->getId());
					$cataloguepeople->setReferencedRelation("specimens");
					$cataloguepeople->setPeopleRef($p_id);
					$cataloguepeople->setPeopleType("donator");
					$cataloguepeople->setOrderBy($i);
					$cataloguepeople->save();
					$i++;
				}
				
			}
		}
  }
  
  
}