<?php

class MaSubContainerForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['sub_container'] = new sfWidgetFormInput();

    $this->widgetSchema['sub_container']->setLabel('Sub Container');
    $this->validatorSchema['sub_container'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['sub_container'];
    $query->set('p.sub_container', '?', $new_taxon);
    return $query;
  }
  
  //ftheeten 2017 07 27
    public function getTable()
    {
        $returned=Array();
        $returned['p']='StorageParts';
        return $returned;
    }  

}