<?php
class InsurancesValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('insurance_value', 'At least a value is required.');
	$this->addMessage('insurance_or_score_value', 'At least a value or disaster score is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
    if ($value['insurance_currency'] && $value['referenced_relation']=="specimens" && !$value['insurance_value'] && (!$value['disaster_recovery_score'] or $value['disaster_recovery_score']===null ))
    {
	  print("!!!!!!!!!!!!!!!1");
      $errorSchemaLocal->addError(new sfValidatorError($this, 'insurance_or_score_value'));
    }
	elseif ($value['insurance_currency'] && $value['referenced_relation']!="specimens" && !$value['insurance_value'])
	{
		print("!!!!!!!!!!!!!!!2");
		 $errorSchemaLocal->addError(new sfValidatorError($this, 'insurance_value'));
	}
    elseif ((!$value['insurance_value'] && !$value['insurance_currency'] && $value['referenced_relation']!="specimens"))
    {
		print("!!!!!!!!!!!!!!!3");
      return array();
    }
	else
	{
		
		print("!!!!!!!!!!!!!!!4");
	}

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'insurance_value');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
 
    return $value;
  }
}
