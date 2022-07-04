<?php
class TaxonomyTopLevelValidatorSchema extends sfValidatorSchema
{
	
   protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('parent_level_missing', 'Please define a parent taxon');
    
  }
  
  protected function doClean($value)
  {
	  
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);
	
	$level_ref=$value["level_ref"];
	$parent_ref=$value["parent_ref"];
	$hierarchy=Doctrine_Core::getTable('PossibleUpperLevels')->findOneByLevelRef($level_ref);
	$upper=$hierarchy->getLevelUpperRef();

	if(is_numeric($upper)  && !is_numeric($parent_ref) )
	{

		 $errorSchema->addError(new sfValidatorError($this, 'parent_level_missing'));
	}
	elseif(is_numeric($upper)&& !is_numeric($parent_ref))
	{

		 $errorSchema->addError(new sfValidatorError($this, 'parent_level_missing'));
	}
	
	 if (count($errorSchemaLocal))
    {
       throw new sfValidatorErrorSchema($this, $errorSchemaLocal);
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }
	 return $value;
  }
}