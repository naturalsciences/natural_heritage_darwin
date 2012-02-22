<?php
class RelatedFileForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();
    $this->widgetSchema['filenames']->setLabel("Add File") ;
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));        
$this->validatorSchema['filenames'] = new sfValidatorFile(
  array(
      'required' => false,
      'validated_file_class' => 'myValidatedFile'
  ));    
  }
  
}
