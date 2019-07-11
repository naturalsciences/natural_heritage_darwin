<?php

class MaGtuRefForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema['gtu_ref'] = new widgetFormButtonRef(array(
          'model' => 'Gtu',
          'link_url' => 'gtu/choose?with_js=1',
          'method' => 'getTagsWithCode',
          'box_title' => $this->getI18N()->__('Change Sampling Location'),
          'nullable' => true,
          'button_class'=>'',
      ),
          array('class'=>'inline')
    );

    $this->widgetSchema['gtu_ref']->setLabel('Choose New Sampling location');
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array(
      'model' => 'Gtu',
      'required' => false
    ));

  }

  public function doGroupedAction($query,$values, $items)
  {
    $new_gtu = $values['gtu_ref'];
    if($new_gtu =='') $new_gtu = 0;
    $query->set('s.gtu_ref', '?', $new_gtu);
    return $query;
  }

}
