<?php
class MultimediaFileValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('invalid_file_type', 'this type of extention is not allowed here');
    $this->addMessage('file_not_found', "Please don't try stupid things, don't touch the uri");
    $this->addMessage('title', 'At least a title is required.');
  }
   
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
    if (!$value['mime_type'])
    {
      return array();
    }
    elseif (!$value['title'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'title'));
    }

    if(isset($value['uri']))
    {
      if( ! preg_match("/^[a-zA-Z0-9\.]+$/", $value['uri'])) {
        $errorSchemaLocal->addError(new sfValidatorError($this, 'file_not_found'));
      }
      if(! file_exists(sfConfig::get('sf_upload_dir').'/multimedia/temp/'.$value['uri'])) {
        $errorSchemaLocal->addError(new sfValidatorError($this, 'file_not_found'));    
      }
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'title');
    }
    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
    return $value;
  }
}
