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
          'required' => true,
          //'mime_type_guessers' => array('guessFromFileinfo'),
          'validated_file_class' => 'myValidatedFile'
      ));
    $this->validatorSchema['filenames']->setOption('mime_type_guessers', array(
    array($this->validatorSchema['filenames'], 'guessFromFileinfo'),
    array($this->validatorSchema['filenames'], 'guessFromFileBinary'),
    array($this->validatorSchema['filenames'], 'guessFromMimeContentType')
  ));
  }
  
}
