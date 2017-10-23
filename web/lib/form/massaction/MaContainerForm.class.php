<?php

class MaContainerForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['container'] = new sfWidgetFormInput();

    $this->widgetSchema['container']->setLabel('Container');
    $this->validatorSchema['container'] = new sfValidatorString(array('required' => false));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['container'];
    $query->set('p.container', '?', $new_taxon);
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