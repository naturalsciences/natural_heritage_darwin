<?php
class PropertyValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('property_value', 'The value is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if ($value['property_accuracy'] && !$value['property_value'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'required'), 'property_value');
    }

    if (!$value['property_value'] && !$value['property_accuracy'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'property_value');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
