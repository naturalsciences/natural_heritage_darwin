<?php

class DefWidgetProfileForm extends sfForm
{
	public function configure()
  {
    $subForm = new sfForm();
    foreach ($this->options['list_widgets'] as $index=>$record)
    {
      
        $form = new WidgetProfilesDefinitionForm($record);
        $subForm->embedForm($index, $form);
      
    }
    $this->embedForm('WidgetProfilesDefinition',$subForm);
    $this->widgetSchema->setNameFormat('def_widget_profile[%s]');
  }
  
  public function save()
  {
    $values = $this->getValues();
    
    foreach($this->embeddedForms['WidgetProfilesDefinition']->getEmbeddedForms() as $key => $prefs)
    {
      $prefs->updateObject($values['WidgetProfilesDefinition'][$key]);
      $prefs->getObject()->save();
    }
  }
  
  
}