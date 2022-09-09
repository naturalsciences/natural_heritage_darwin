<?php
//ftheeten 2016 07 05

class TagGroupsEcologyform extends TagGroupsForm
{
   public function configure()
   {
        $this->useFields(array('id', 'group_name','sub_group_name','international_name', 'tag_value'));
       
     
        parent::configure();
        
        $this->widgetSchema['group_name'] =new sfWidgetFormInputHidden();
        $this->getWidget('group_name')->setDefault('habitat');
        $this->validatorSchema['group_name'] = new sfValidatorPass();
        $this->widgetSchema['sub_group_name'] =new sfWidgetFormInputHidden();
        $this->getWidget('sub_group_name')->setDefault('ecology');
        $this->validatorSchema['sub_group_name'] = new sfValidatorPass();
        $this->widgetSchema['international_name'] =new sfWidgetFormInputHidden();
        $this->validatorSchema['international_name'] = new sfValidatorPass();
        $this->widgetSchema['tag_value']=new sfWidgetFormTextarea();
        $this->validatorSchema['tag_value'] = new sfValidatorPass();
   }
}

