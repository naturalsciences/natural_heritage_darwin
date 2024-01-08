<?php
class RelatedFileForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['filenames'] = new sfWidgetFormInputFile();

    $this->widgetSchema['filenames']->setLabel("Add File") ;
    $this->widgetSchema['filenames']->setAttributes(array('class' => 'Add_related_file'));
	
    $this->validatorSchema['filenames'] = new sfValidatorFilePHP8(
      array(
          'required' => true,
          //'mime_type_guessers' => array('guessFromFileinfo'),
          'validated_file_class' => 'myValidatedFile'
      ));
    $this->validatorSchema['filenames']->setOption('mime_type_guessers', array(
    array($this->validatorSchema['filenames'], 'guessFromFileinfo'),
    array($this->validatorSchema['filenames'], 'guessFromFileBinary'),
    array($this->validatorSchema['filenames'], 'guessFromMimeContentType')
  ));
  	//ftheeten 2023 04 11 PHP8
	 $this->validatorSchema->addOption('allow_extra_fields', true);
   
  }
  
  //ftheeten php 8
  protected function doBind(array $values)
  {

	$go_map=true;
	if(!array_key_exists("name", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["name"]))
	{
		$go_map=false;	
	}
	if(!array_key_exists("full_path", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["full_path"]))
	{
		$go_map=false;	
	}
	if(!array_key_exists("type", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["type"]))
	{
		$go_map=false;	
	}
	if(!array_key_exists("tmp_name", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["tmp_name"]))
	{
		$go_map=false;	
	}
	if(!array_key_exists("error", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["error"]))
	{
		$go_map=false;	
	}
	if(!array_key_exists("size", $values))
	{
		$go_map=false;
	}
	elseif(!array_key_exists("filenames", $values["size"]))
	{
		$go_map=false;	
	}
	if($go_map)
	{

		$values["filenames"]=Array();
		$values["filenames"]["error"]=$values["error"]["filenames"];
		$values["filenames"]["name"]=$values["name"]["filenames"];
		$values["filenames"]["type"]=$values["type"]["filenames"];
		$values["filenames"]["tmp_name"]=$values["tmp_name"]["filenames"];
		$values["filenames"]["size"]=$values["size"]["filenames"];
		
	}
	
	
    $this->values = $this->validatorSchema->clean($values);
  }
  
  
}
