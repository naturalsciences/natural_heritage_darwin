<?php

class MaLithologyRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['lithology_ref'] = new widgetFormCompleteButtonRef(array(
       'model' => 'Lithology',
       'link_url' => 'lithology/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Lithologic unit'),
       'nullable' => true,
       'button_class'=>'',
       'complete_url' => 'catalogue/completeName?table=lithology',
     ));

    $this->widgetSchema['lithology_ref']->setLabel('Choose New Lithologic unit');
    $this->validatorSchema['lithology_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Lithology',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['lithology_ref'];
    if($new_taxon =='') $new_taxon = 0;
    $query->set('s.lithology_ref', '?', $new_taxon);
    return $query;
  }

}
