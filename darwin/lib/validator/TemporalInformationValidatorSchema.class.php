<?php
//ftheeten 2018 12 1

class TemporalInformationValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('begin_date', 'At least a begin date is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
	//print_r($value);
    if ((int)$value['from_date_mask']==0&&(int)$value['to_date_mask']==0)
    {
      return array();
	  //$errorSchema->addError($errorSchemaLocal, 'begin_date');
	  //return null;
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'begin_date');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}


?>