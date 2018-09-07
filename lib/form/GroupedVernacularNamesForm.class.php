<?php

/**
 * GroupedVernacularNamesForm form.
 *
 * @package    form
 * @subpackage GroupedVernacularNamesForm
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class GroupedVernacularNamesForm extends BaseForm
{
  public function configure()
  {
    $subForm = new sfForm();
    if(isset($this->options['no_load']))
      $names = array();
    else
      $names = Doctrine::getTable('VernacularNames')->findForTable($this->options['table'], $this->options['id']);
    foreach ($names as $index => $childObject)
    {
      $form = new VernacularNamesForm($childObject);
      $subForm->embedForm($index, $form);
    }
    $this->embedForm('VernacularNames', $subForm);

    $subForm2 = new sfForm();
    $this->embedForm('newVal', $subForm2);

    $this->widgetSchema->setNameFormat('grouped_vernacular[%s]');
  }
  
  public function addValue($num)
  {
      $val = new VernacularNames();
      $form = new VernacularNamesForm($val);

      $this->embeddedForms['newVal']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newVal', $this->embeddedForms['newVal']);
   }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newVal']))
    {
        foreach($taintedValues['newVal'] as $key=>$newVal)
        {
          if (!isset($this['newVal'][$key]))
          {
            $taintedValues['newVal'][$key]['referenced_relation'] = $this->options['table'];
            $taintedValues['newVal'][$key]['record_id'] = $this->options['id'];
            $this->addValue($key);
          }
         }
    }

    if(isset($taintedValues['VernacularNames']))
    {
      foreach($taintedValues['VernacularNames'] as $key=>$newVal)
      {
        $taintedValues['VernacularNames'][$key]['referenced_relation'] = $this->options['table'];
        $taintedValues['VernacularNames'][$key]['record_id'] = $this->options['id'];
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function save()
  {
    $value = $this->getValues();

    foreach($this->embeddedForms['VernacularNames']->getEmbeddedForms() as $name => $form)
    {

      if (!isset($value['VernacularNames'][$name]['community']) || $value['VernacularNames'][$name]['community']=='' )
      {
        $form->getObject()->delete();
        unset($this->embeddedForms['VernacularNames'][$name]);
      }
      else
      {
        $form->updateObject($value['VernacularNames'][$name]);
        $form->getObject()->save();
      }
    }
    
    foreach($this->embeddedForms['newVal']->getEmbeddedForms() as $name => $form)
    {
      if (!isset($value['newVal'][$name]['name']) || $value['newVal'][$name]['name']=='' )
      {
        unset($this->embeddedForms['newVal'][$name]);
      }
      else
      {
        $form->updateObject($value['newVal'][$name]);
        $form->getObject()->save();
      }
    }
  }
}
