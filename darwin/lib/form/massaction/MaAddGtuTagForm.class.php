<?php

class MaAddGtuTagForm extends DarwinModelForm
{
  public function configure()
  {
     

    $this->widgetSchema->setNameFormat('tag_groups[%s]');
    
    


    $this->widgetSchema['group_name'] = new sfWidgetFormChoice(array("choices"=>TagGroups::getGroups()), array("class"=>"mass_tags_group"));
    //$this->widgetSchema['group_name']->setDefault('administrative');

    $this->widgetSchema['sub_group_name'] = new sfWidgetFormChoice(array("choices"=>array()), array("class"=>"mass_tags_sub_group"));
    
    



   

    $this->widgetSchema   ['tag_value'] = new sfWidgetFormTextarea();
    $this->validatorSchema['tag_value'] = new sfValidatorString();
    
      $this->validatorSchema['group_name'] = new sfValidatorString();  
    $this->validatorSchema['sub_group_name'] = new sfValidatorString();
    $this->validatorSchema['group_name']->setOption('required', true);
    $this->validatorSchema['group_name']->setOption('trim', true);
    $this->validatorSchema['sub_group_name']->setOption('required', true);
    $this->validatorSchema['sub_group_name']->setOption('trim', true);
    $this->validatorSchema['tag_value']->setOption('required', true);
    $this->validatorSchema['tag_value']->setOption('trim', true);


    $this->widgetSchema['tag_value'] = new sfWidgetFormInputText(array(),array('class'=>'inline tag_val'));
     $this->validatorSchema['tag_value'] = new sfValidatorString();
    
   $this->mergePostValidator(new sfValidatorCallback(
			array('callback' => array($this, 'checkTagsOnlyInSelection'))));
    
  }
  
  public function checkTagsOnlyInSelection($validator, $values, $arguments)
  {				
        
        $items= sfContext::getInstance()->getUser()->getAllPinned('specimen');
        foreach($items as $item)
        {
            $query1 = Doctrine_Query::create()->select('s.*')->from('Specimens s')->where("id=?",$item);
            $result1 = $query1->fetchOne();
            $gtu_ref=$result1->getGtuRef();
            if(is_numeric($gtu_ref))
            {
                $query2 = Doctrine_Query::create()->select('s.id')->from('Specimens s')->where("gtu_ref=? AND  NOT ( s.id = ANY('{".implode(",",$items )."}'::int[]))",$gtu_ref);
                $results2 = $query2->execute();
                if(count($results2)>0)
                {
                    throw new sfValidatorError($validator, "Update rejected, GTU n° ".$gtu_ref. " in specimen ".$item. " bound to other specimens");
                }
            }
        
       }
		return $values;
		
		
		
	}
  
    public function doMassAction($user_id, $items, $values)
  {

    $query = Doctrine_Query::create()->select('id')->from('Specimens s');
    $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
    $results = $query->execute();

    foreach($results as $result)
    {
    

      $gtu_ref=$result->getGtuRef();
      if($gtu_ref!==null)
      {
        if(is_numeric($gtu_ref))
        {
            $tag = new TagGroups();
            $tag->fromArray($values);
            $tag->setGtuRef($result->getGtuRef());            
            $tag->save();
        }
      }
    }
  }
  
 }