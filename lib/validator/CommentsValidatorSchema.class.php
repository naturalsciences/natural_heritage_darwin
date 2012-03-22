<?php
class CommentsValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('comment', 'At least a comment is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
    if (!$value['comment'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'comment');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
