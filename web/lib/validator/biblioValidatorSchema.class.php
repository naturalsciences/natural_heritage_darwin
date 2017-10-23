<?php
class BiblioValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('bibliography_ref', 'At least a value is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
    if (!$value['bibliography_ref'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'bibliography_ref');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
