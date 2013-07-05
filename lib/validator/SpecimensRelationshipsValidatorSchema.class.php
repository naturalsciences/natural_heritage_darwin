<?php
class SpecimensRelationshipsValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('unit_ref', 'At least a reference to an accompanying element is required.');
  }
 
  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if($value['unit_type']=='specimens') {
      $value['mineral_ref'] = null;
      $value['taxon_ref'] = null;
    } elseif($value['unit_type']=='taxonomy') {
      $value['mineral_ref'] = null;
      $value['specimen_related_ref'] = null;
    } elseif($value['unit_type']=='mineral'){
      $value['taxon_ref'] = null;
      $value['specimen_related_ref'] = null;
    }

    // If type is known but nothing else
    if (!$value['taxon_ref'] && !$value['mineral_ref'] && !$value['specimen_related_ref'] && $value['relationship_type'])
    {
      $errorSchemaLocal->addError(new sfValidatorError($this, 'unit_ref'));
    }

    if (!$value['taxon_ref'] && !$value['mineral_ref'] && !$value['specimen_related_ref'] && !$value['relationship_type'])
    {
      return array();
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'unit_ref');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $value;
  }
}