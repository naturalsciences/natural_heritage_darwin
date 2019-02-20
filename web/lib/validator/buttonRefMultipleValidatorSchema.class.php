<?php
/**
 * Created by PhpStorm.
 * User: duchesne
 * Date: 29/07/15
 * Time: 10:22
 */
class buttonRefMultipleValidatorSchema extends sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('wrong_structure',
                      'The catalogue units passed to the form do not follow expected structure. Please call your application administrator'
    );
    $this->addMessage('wrong_type',
                      'The catalogue units passed to the form is not referenced by an identifier. Please call your application administrator'
    );
  }

  protected function doClean($value)
  {
    $errorSchema = new sfValidatorErrorSchema($this);
    $errorSchemaLocal = new sfValidatorErrorSchema($this);

    if ($value['catalogue_unit_ref'])
    {
      if(!is_numeric($value['catalogue_unit_ref']) && !strpos($value['catalogue_unit_ref'], ',')) {
        $errorSchemaLocal->addError(new sfValidatorError($this, 'wrong_type'), 'catalogue_unit_ref');
      }
      elseif (strpos($value['catalogue_unit_ref'], ',')) {
        $ids = preg_split('/[,]/', $value['catalogue_unit_ref']);
        foreach($ids as $key=>$id_value) {
          if(!is_numeric($id_value)) {
            $errorSchemaLocal->addError(new sfValidatorError($this, 'wrong_structure'), 'catalogue_unit_ref');
            break;
          }
        }
      }
    }

    if (count($errorSchemaLocal))
    {
      $errorSchema->addError($errorSchemaLocal, 'catalogue_unit_ref');
    }

    if (count($errorSchema))
    {
      throw new sfValidatorErrorSchema($this, $errorSchema);
    }

    return $value;
  }
}
