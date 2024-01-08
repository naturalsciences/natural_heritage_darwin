<?php

class MaInformativeworkflowForm extends BaseInformativeWorkflowForm
{
  public function configure()
  {
	$this->useFields(array('status','comment')) ;
    $statuses = array('available_status' => informativeWorkflow::getAvailableStatus(sfContext::getInstance()->getUser()->getDbUserType())); 
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>true));
    $this->validatorSchema['status'] = new sfValidatorPass();//Choice(array('choices'  => array_keys($statuses), 'required' => true));
  }
  
  public function doMassAction($user_id, $items, $values)
  {
    $query = Doctrine_Query::create()->select('id')->from('Specimens s');
    $query->andWhere('s.id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));
    $results = $query->execute();
	$user_id=sfContext::getInstance()->getUser()->getId();
    foreach($results as $result)
    {
      $workflow = new InformativeWorkflow();
      $workflow->fromArray($values);
      $workflow->setRecordId($result->getId());
	  $workflow->setUserRef($user_id);
      $workflow->setReferencedRelation("specimens");
      $workflow->save();
    }
  }
  
 }