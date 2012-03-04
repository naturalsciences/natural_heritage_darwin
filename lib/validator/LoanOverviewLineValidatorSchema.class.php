<?php
class LoanOverviewLineValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('from_date', 'The return date must be after the expedition date.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if($value['item_visible'] != "true")
    {
      return array();
    }

    if ($value['from_date'] != '' &&  $value['to_date'] != '' && $value['from_date'] > $value['to_date']) {
      $errorSchema->addError(new sfValidatorError($this, 'from_date'));
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
