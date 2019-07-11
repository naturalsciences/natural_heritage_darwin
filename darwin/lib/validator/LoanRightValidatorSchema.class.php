<?php
class LoanRightValidatorSchema extends sfValidatorSchema
{
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
    if (!$value['user_ref'])
    {
      return array();
    }
    return $value;
  }
}
