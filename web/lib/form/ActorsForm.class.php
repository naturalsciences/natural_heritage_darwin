<?php

/**
 * CataloguePeople form.
 *
 * @package    form
 * @subpackage CataloguePeople
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ActorsForm extends CataloguePeopleForm
{
  public function configure()
  {
    unset($this['referenced_relation']);
    $this->widgetSchema['people_type'] = new sfWidgetFormInputHidden();    
    
    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['people_ref'] = new sfValidatorInteger(array('required'=>false));    
    
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();    
    $this->validatorSchema['record_id'] = new sfValidatorInteger();
    
    $types = CataloguePeople::getTypes() ;
    
    $this->widgetSchema['order_by'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['order_by'] = new sfValidatorInteger();
    
    $this->widgetSchema['people_sub_type'] = new sfWidgetFormChoice(array(
                                                       'choices'=>$types,
                                                       'expanded'=> true,
                                                       'multiple' => true,
                                                       'renderer_options' => 
                                                       array('formatter' => array('ActorsForm', 'RenderInLine'))
                                                       ));

    $this->validatorSchema['people_sub_type'] = new sfValidatorChoice(array('choices'=>array_keys($types),
                                                                            'required' => false,
                                                                            'multiple' => true
                            ));
 
    $people_id= $this->getObject()->getPeopleRef() ;
    if($people_id)
    {
      $people = Doctrine::getTable('People')->find($this->getObject()->getPeopleRef()) ;
      $this->widgetSchema['people_ref']->setLabel($people->getFormatedName()) ;
    }
    else 
    {
      $this->widgetSchema['people_ref']->setAttribute('class','hidden_record');
      $this->validatorSchema['people_sub_type']->addOption('required', false) ;
    }                            
  }
  
  public static function RenderInLine($widget, $inputs) 
  {
   $result = '';
   foreach ($inputs as $input) 
      $result .= '<td>' . $input ['input'] . '</td>';
   return $result;
  }  
}
