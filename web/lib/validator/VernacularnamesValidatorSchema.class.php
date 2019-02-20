<?php
class VernacularnamesValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('community', 'The value is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if (!$value['name']) {
      return array();
    } elseif (!$value['community']) {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'community'));
    }

    if (count($errorSchemaLocal)) {
      $errorSchema->addError($errorSchemaLocal, 'community');
    }

    if (count($errorSchema)) {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
