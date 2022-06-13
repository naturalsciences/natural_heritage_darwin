<?php

/**
 * Project form base class.
 *
 * @package    form
 * @version    SVN: $Id: sfDoctrineFormBaseTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class BaseFormDoctrine extends sfFormDoctrine
{
  public function setup()
  {
    sfWidgetFormSchema::setDefaultFormFormatterName('list');
  }

  protected function getI18N()
  {
     return sfContext::getInstance()->getI18N();
  }

  public function getCurrentCulture()
  {
    return sfContext::getInstance()->getUser()->getCulture();
  }


/*************************************
*** FOR Embedded Forms
***
**************************************/
  protected function attachEmbedRecord($emFieldName, sfForm $object, $order = 0)
  {
    if(! isset($this['new'.$emFieldName]))
      $this->loadEmbed($emFieldName);
    $this->embeddedForms['new'.$emFieldName]->embedForm($order, $object);

    //Re-embedding the container
    $this->embedForm('new'.$emFieldName, $this->embeddedForms['new'.$emFieldName]);
  }

  public function loadEmbed($emFieldName)
  {
    if($this->isBound()) return;
    $subForm = new sfForm();
    $this->embedForm($emFieldName, $subForm);
    if($this->getObject()->getId() !='')
    {
      foreach($this->getEmbedRecords($emFieldName) as $key=>$vals)
      {
        $form = $this->getEmbedRelationForm($emFieldName, $vals);
        $this->embeddedForms[$emFieldName]->embedForm($key, $form);
      }
      //Re-embedding the container
      $this->embedForm($emFieldName, $this->embeddedForms[$emFieldName]);
    }
    $subForm = new sfForm();
    $this->embedForm('new'.$emFieldName, $subForm);
  }

  /**
  * emtpyField is the field to check if the subForm is there or not
  * $enforce_value are values you want to be in the ref record
  */
  protected function saveEmbed($emFieldName, $emptyField ,$forms, $enforce_value)
  {
    if (null === $forms && $this->getValue($emFieldName.'_holder'))
    {
      $value = $this->getValue('new'.$emFieldName);
      foreach($this->embeddedForms['new'.$emFieldName]->getEmbeddedForms() as $name => $form)
      {
        if(!isset($value[$name][$emptyField]))
          unset($this->embeddedForms['new'.$emFieldName][$name]);
        else
          $form->getObject()->fromArray($enforce_value);
      }
      $value = $this->getValue($emFieldName);
      foreach($this->embeddedForms[$emFieldName]->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name][$emptyField]))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms[$emFieldName][$name]);
        }
      }
    }

  }
  
   protected function saveEmbedArrayFields($emFieldName, $list_fields ,$forms, $enforce_value)
  {
    if (null === $forms && $this->getValue($emFieldName.'_holder'))
    {
      $value = $this->getValue('new'.$emFieldName);
      foreach($this->embeddedForms['new'.$emFieldName]->getEmbeddedForms() as $name => $form)
      {
       
	    $set_flag=false;
		foreach($list_fields as $emptyField)
		{
			if(isset($value[$name][$emptyField])!==false)
		    {
				$set_flag=true;
			}
			 
		}
		if(!$set_flag)
		{
			unset($this->embeddedForms['new'.$emFieldName][$name]);
		}
		else
		{
			$form->getObject()->fromArray($enforce_value);
		}
	  
      }
      $value = $this->getValue($emFieldName);
      foreach($this->embeddedForms[$emFieldName]->getEmbeddedForms() as $name => $form)
      {
        
		$set_flag=false;
		foreach($list_fields as $emptyField)
		{
			if(isset($value[$name][$emptyField])!==false)
		    {
				$set_flag=true;
			}
			 
		}
		if(!$set_flag)
		{
			$form->getObject()->delete();
			unset($this->embeddedForms[$emFieldName][$name]);
		}
		
      }
    }

  }

  /**
  * Fct_add is the function to add an new Embedded Form like addBiblio()
  */
  protected function bindEmbed($emFieldName, $fct_add, $taintedValues)
  {
    if(!isset($taintedValues[$emFieldName.'_holder']))
    {
      $this->offsetUnset($emFieldName);
      unset($taintedValues[$emFieldName]);
      $this->offsetUnset($emFieldName);
      unset($taintedValues[$emFieldName]);
    }
    else
    {
      $this->loadEmbed($emFieldName);
      if(isset($taintedValues['new'.$emFieldName]))
      {
        foreach($taintedValues['new'.$emFieldName] as $key=>$newVal)
        {
          if (!isset($this['new'.$emFieldName][$key]))
          {
            //Call the add function of the embeddedForm
            $this->$fct_add($key, $newVal, $key);
          }
        }
      }
    }
  }
}
