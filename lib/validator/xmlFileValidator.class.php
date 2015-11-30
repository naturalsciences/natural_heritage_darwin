<?php

class xmlFileValidator extends sfValidatorFile
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('invalid_format', 'Invalid Xml file format <br /><u>Detail :</u>');
    $this->addMessage('invalid_line', '- %error%');
    $this->addMessage('unreadable_file', 'This file is unreadable.');
    $this->addOption('xml_path_file') ; 
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
    parent::doClean($value);
    libxml_use_internal_errors(true) ;
    $xml = new DOMDocument();
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if(! file_exists($value['tmp_name'])) {
      throw new sfValidatorError($this, 'unreadable_file');
    }
    // $xml_file = file_get_contents($value['tmp_name']) ;
    if(!$xml->load($value['tmp_name']))
      throw new sfValidatorError($this, 'unreadable_file');
    if(!$xml->schemaValidate(sfConfig::get('sf_web_dir').$this->getOption('xml_path_file')))
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'invalid_format'), 'invalid_format_ABCD');
      $errors = libxml_get_errors();
      $i=0;
      foreach ($errors as $error) {
          $error_msg = $this->displayXmlError($error);
          $errorSchemaLocal->addError(new sfValidatorError($this, $error_msg), 'invalid_line');
          if($i++ > 100) break;
      }
      libxml_clear_errors();
      if (count($errorSchemaLocal))
      {
        $errorSchema->addError($errorSchemaLocal);
      }

      if (count($errorSchema))
      {
        throw new sfValidatorErrorSchema($this, $errorSchema);
      }
    }
    $class = $this->getOption('validated_file_class');
    return new $class($value['name'], 'text/xml', $value['tmp_name'], $value['size'], $this->getOption('path'));
  }

  function displayXmlError($error)
  {
    $error_list  = "";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $error_list .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $error_list .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $error_list .= "Fatal Error $error->code: ";
            break;
    }
    $error_list .= trim($error->message)."\n  Line: $error->line \n";
    return($error_list);
  }
}
