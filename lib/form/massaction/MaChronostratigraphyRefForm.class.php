<?php

class MaChronostratigraphyRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['chronostratigraphy_ref'] = new widgetFormCompleteButtonRef(array(
       'model' => 'Chronostratigraphy',
       'link_url' => 'chronostratigraphy/choose',
       'method' => 'getName',
       'box_title' => $this->getI18N()->__('Choose Chronostratigraphic unit'),
       'nullable' => true,
       'button_class'=>'',
       'complete_url' => 'catalogue/completeName?table=chronostratigraphy',
    ));

    $this->widgetSchema['chronostratigraphy_ref']->setLabel('Choose New Chronostratigraphy unit');
    $this->validatorSchema['chronostratigraphy_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Chronostratigraphy',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['chronostratigraphy_ref'];
    if($new_taxon =='')
      $new_taxon = 0;
    $query->set('s.chrono_ref', '?', $new_taxon);
    return $query;
  }

}
