<?php
class CodesValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('code', 'At least one code is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if (!$value['code_category'])
    {
      return array();
    }
    elseif (!$value['code'] && !$value['code_prefix'] && !$value['code_suffix'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'code'));
    }
    

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'code');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
