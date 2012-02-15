<?php
class LoanOverviewLineValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if($value['item_visible'] != "true")
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'details');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
