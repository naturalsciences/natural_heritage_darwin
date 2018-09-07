<?php

class MaIgRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['ig_ref'] = new widgetFormInputChecked(
      array(
        'model' => 'Igs',
        'method' => 'getIgNum',
        'nullable' => true,
        'link_url' => 'igs/searchFor',
        'notExistingAddTitle' => $this->getI18N()->__('This I.G. number does not exist. Would you like to automatically insert it ?'),
        'notExistingAddValues' => array(
          $this->getI18N()->__('No'),
          $this->getI18N()->__('Yes')
        ),
      )
    );
    $this->validatorSchema['ig_ref'] = new sfValidatorString();
  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_taxon = $values['ig_ref'];
    if($new_taxon =='') $new_taxon = 0;
    $query->set('s.ig_ref', '?', $new_taxon);
    return $query;
  }

}
