<?php
class LoanOverviewForm extends sfForm
{

  public function configure()
  {
    $subForm = new sfForm();
    if(isset($this->options['no_load']))
      $items = array();
    else
      $items = Doctrine::getTable('LoanItems')->findForLoan($this->options['loan']->getId());
    foreach ($items as $index => $childObject)
    {
      $form = new LoanItemsForm($childObject);
      $subForm->embedForm($index, $form);
     // $subForm->getWidgetSchema()->setLabel($index, (string) $childObject);
    }
    $this->embedForm('LoanItems', $subForm);

    $subForm2 = new sfForm();
    $this->embedForm('newLoanItems', $subForm2);

    $this->widgetSchema->setNameFormat('loan_overview[%s]');
    
    //ftheeten 2016 11 25
    $this->widgetSchema['code_part'] = new sfWidgetFormInput(array(),array());
	//ftheeten 2015 06 04
    $this->widgetSchema['code_part']->setAttributes(array('class'=>'class_rmca_input_mask autocomplete_for_code large_size'));
    $this->validatorSchema['code_part'] = new sfValidatorString(array('required'=>false,'trim'=>true));
    $this->widgetSchema['selected_id']= new sfWidgetFormInputHidden();
    $this->widgetSchema['selected_id']->setAttributes(array('class'=>'catch_selection'));
    $this->validatorSchema['selected_id'] = new sfValidatorPass();

  }

  
  public function addItem($num,$spec_ref=null)
  {
    $item = new LoanItems() ;
    if($spec_ref){
      $spec = Doctrine::getTable('Specimens')->find($spec_ref);
      if($spec) {
        $item->setSpecimenRef($spec->getId()) ;
        $item->setIgRef($spec->getIgRef()) ;
      }
    }
    $form = new LoanItemsForm($item);
    $this->embeddedForms['newLoanItems']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newLoanItems', $this->embeddedForms['newLoanItems']);
  }


  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newLoanItems']))
    {
      foreach($taintedValues['newLoanItems'] as $key=>$newVal)
      {
        $taintedValues['newLoanItems'][$key]['loan_ref'] = $this->options['loan']->getId();
        if (!isset($this['newLoanItems'][$key]))
        {
          $this->addItem($key);
        }
      }
    }

    if(isset($taintedValues['LoanItems']))
    {
      foreach($taintedValues['LoanItems'] as $key=>$newVal)
      {
        $taintedValues['LoanItems'][$key]['loan_ref'] = $this->options['loan']->getId();
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }


  public function save()
  {
    $value = $this->getValues();
    foreach($this->embeddedForms['newLoanItems']->getEmbeddedForms() as $name => $form)
    {
      if (!isset($value['newLoanItems'][$name]['item_visible']))
      {
        unset($this->embeddedForms['newLoanItems'][$name]);
      }
      else
      {
        $form->updateObject($value['newLoanItems'][$name]);
        $form->getObject()->save();
      }
    }

    foreach($this->embeddedForms['LoanItems']->getEmbeddedForms() as $name => $form)
    {
      if (!isset($value['LoanItems'][$name]['item_visible']))
      {
        $form->getObject()->delete();
        unset($this->embeddedForms['LoanItems'][$name]);
      }
      else
      {
        $form->updateObject($value['LoanItems'][$name]);
        $form->getObject()->save();
      }
    }
    
  }
  public function getJavaScripts()
  {
    $javascripts=parent::getJavascripts();
    $javascripts[]='/js/catalogue_people.js';   
    return $javascripts;
  }
  public function getStylesheets()
  {
    return array('/css/ui.datepicker.css' => 'all');
  }
}
