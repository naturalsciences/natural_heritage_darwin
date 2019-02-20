<?php
class IdentificationsValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('value_defined', 'At least a value is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if (!$value['value_defined'] && $value['notion_concerned'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'value_defined'));
    }
    elseif (!$value['value_defined'] && !$value['notion_concerned'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'value_defined');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
