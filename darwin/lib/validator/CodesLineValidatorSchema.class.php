<?php
class CodesLineValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('wrong_type', '"Between" concerns only numerical parts of codes');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if (($value['code_from'] && $value['code_to']===""))
    {
       $errorSchemaLocal->addError(new sfValidatorError($this, 'required'), 'code_to');
    }

    if($value['code_from']==="" && $value['code_to'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'required'), 'code_from');
    }

    if($value['code_from'] !=="" && $value['code_to'] !=="" && ( !ctype_digit($value['code_from']) || !ctype_digit($value['code_to'])) )
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'wrong_type'), 'code_from');
    }

    if (!$value['code_from'] && !$value['code_to'] && !$value['code_part'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'code_from');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
