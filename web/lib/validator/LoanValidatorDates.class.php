<?php
class LoanValidatorDates extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('from_date', 'The end and extended date must be after the start date.');
    $this->addMessage('end_date', 'The end date must be before the extended to date');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if ($value['from_date'] != '' &&  $value['to_date'] != '' && $value['from_date'] > $value['to_date']) {
      $errorSchema->addError(new sfValidatorError($this, 'from_date'));
    }

    if ($value['from_date'] != '' &&  $value['extended_to_date'] != '' && $value['from_date'] > $value['extended_to_date']) {
      $errorSchema->addError(new sfValidatorError($this, 'from_date'));
    }

    if ($value['to_date'] != '' &&  $value['extended_to_date'] != '' && $value['to_date'] > $value['extended_to_date']) {
      $errorSchema->addError(new sfValidatorError($this, 'end_date'));
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $value;
  }
}
