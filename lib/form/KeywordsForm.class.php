<?php

/**
 * Base project form.
 * 
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be> 
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class KeywordsForm extends sfForm
{
  public function configure()
  {
    $subForm = new sfForm();
    if(isset($this->options['no_load']))
      $keywords = array();
    else
      $keywords = Doctrine::getTable('ClassificationKeywords')->findForTable($this->options['table'], $this->options['id']);
    foreach ($keywords as $index => $childObject)
    {
      $form = new ClassificationKeywordsForm($childObject);
      $subForm->embedForm($index, $form);
    }
    $this->embedForm('ClassificationKeywords', $subForm);

    $subForm2 = new sfForm();
    $this->embedForm('newKeywords', $subForm2);

    $this->widgetSchema->setNameFormat('keywords[%s]');

  }

  
  public function addKeyword($num, $type="")
  {
    $val = new ClassificationKeywords();
    $val->setKeywordType($type);

    $form = new ClassificationKeywordsForm($val);
  
    $this->embeddedForms['newKeywords']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newKeywords', $this->embeddedForms['newKeywords']);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newKeywords']))
    {
      foreach($taintedValues['newKeywords'] as $key=>$newVal)
      {
        $taintedValues['newKeywords'][$key]['referenced_relation'] = $this->options['table'];
        $taintedValues['newKeywords'][$key]['record_id'] = $this->options['id'];
        if (!isset($this['newKeywords'][$key]))
        {
          $this->addKeyword($key, $taintedValues['newKeywords'][$key]['keyword_type'] );
        }
      }
    }

    if(isset($taintedValues['ClassificationKeywords']))
    {
      foreach($taintedValues['ClassificationKeywords'] as $key=>$newVal)
      {
        $taintedValues['ClassificationKeywords'][$key]['referenced_relation'] = $this->options['table'];
        $taintedValues['ClassificationKeywords'][$key]['record_id'] = $this->options['id'];
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }


  public function save()
  {
    $value = $this->getValues();
    foreach($this->embeddedForms['newKeywords']->getEmbeddedForms() as $name => $form)
    {
      if (!isset($value['newKeywords'][$name]['keyword_type']) || $value['newKeywords'][$name]['keyword_type']=='' )
      {
        unset($this->embeddedForms['newKeywords'][$name]);
      }
      else
      {
        $form->updateObject($value['newKeywords'][$name]);
        $form->getObject()->save();
      }
    }

    foreach($this->embeddedForms['ClassificationKeywords']->getEmbeddedForms() as $name => $form)
    {

      if (!isset($value['ClassificationKeywords'][$name]['keyword_type']) || $value['ClassificationKeywords'][$name]['keyword_type']=='' )
      {
        $form->getObject()->delete();
        unset($this->embeddedForms['ClassificationKeywords'][$name]);
      }
      else
      {
        $form->updateObject($value['ClassificationKeywords'][$name]);
        $form->getObject()->save();
      }
    }
    
  }
}
