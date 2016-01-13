<?php

class MaLithostratigraphyRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['lithostratigraphy_ref'] = new widgetFormCompleteButtonRef(array(
       'model' => 'Lithostratigraphy',
       'link_url' => 'lithostratigraphy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Lithostratigraphic unit'),
       'nullable' => true,
       'button_class'=>'',
       'complete_url' => 'catalogue/completeName?table=lithostratigraphy',
     ));

    $this->widgetSchema['lithostratigraphy_ref']->setLabel('Choose New Lithostratigraphic unit');
    $this->validatorSchema['lithostratigraphy_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Lithostratigraphy',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['lithostratigraphy_ref'];
    if($new_taxon =='') $new_taxon = 0;
    $query->set('s.litho_ref', '?', $new_taxon);
    return $query;
  }

}
