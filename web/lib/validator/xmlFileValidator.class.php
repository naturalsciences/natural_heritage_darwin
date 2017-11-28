<?php

class xmlFileValidator extends sfValidatorFile
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('invalid_format', 'Invalid Xml file format <br /><u>Detail :</u>');
    $this->addMessage('invalid_line', '- %error%');
    $this->addMessage('unreadable_file', 'This file is unreadable.');
    $this->addOption('xml_path_file') ; 
    //ftheeten 2017 09 07
    $this->addMessage('notab', 'The imported text file is not a tab delimited file');
    parent::configure($options, $messages);
  }

  protected function doClean($value)
  {
 
    //ftheeten 2016 02 22
	$disableXSDValidation=sfConfig::get('dw_disableXSDValidation');
	
    parent::doClean($value);
    libxml_use_internal_errors(true) ;
    $xml = new DOMDocument();
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if(! file_exists($value['tmp_name'])) {
      throw new sfValidatorError($this, 'unreadable_file');
    }
  
    //ftheeten 2017 09 07 (art for tab-delimited
    if(mime_content_type($value['tmp_name'])=="text/plain")
    {
        $delimiter=$this->getFileDelimiter($value['tmp_name'], $checkLines = 2);
        if($delimiter!=='\t')
        {
            throw new sfValidatorError($this, 'notab');
        }
        $class = $this->getOption('validated_file_class');
        return new $class($value['name'], 'text/plain', $value['tmp_name'], $value['size'], $this->getOption('path'));
    }
    else
    {
        if(!$xml->load($value['tmp_name']))
        {
          throw new sfValidatorError($this, 'unreadable_file');
        }
        //ftheeten 2016 02 22 to disable the validation of XML (aim is avoiding resolving external schemas)
        if($disableXSDValidation===false)
        { 
        
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
        }
        
        $class = $this->getOption('validated_file_class');
        return new $class($value['name'], 'text/xml', $value['tmp_name'], $value['size'], $this->getOption('path'));
	}
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
  
  //ftheeten 2017 09 07
  function getFileDelimiter($file, $checkLines = 2)
  {
        $file = new SplFileObject($file);
        $delimiters = array(
          ',',
          '\t',
          ';',
          '|',
          ':'
        );
        $results = array();
        $i = 0;
         while($file->valid() && $i <= $checkLines){
            $line = $file->fgets();
            foreach ($delimiters as $delimiter){
                $regExp = '/['.$delimiter.']/';
                $fields = preg_split($regExp, $line);
                if(count($fields) > 1){
                    if(!empty($results[$delimiter])){
                        $results[$delimiter]++;
                    } else {
                        $results[$delimiter] = 1;
                    }   
                }
            }
           $i++;
        }
        $results = array_keys($results, max($results));
        return $results[0];
    }
}
