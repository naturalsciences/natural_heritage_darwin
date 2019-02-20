<?php

class MaRelFilesForm extends BaseMultimediaForm
{
  public function configure() {
    parent::configure();
    $this->useFields(
      array(
        'visible',
        'publishable'
      )
    );
  }


  public function doMassAction($user_id, $items, $values)
  {
    $query = Doctrine_Query::create()
                           ->update('Multimedia m')
                           ->set("m.visible", ($values['visible']===false?'false':'true'))
                           ->set("m.publishable", ($values['publishable']===false?'false':'true'))
                           ->where("m.referenced_relation = ?", array('specimens'))
                           ->andWhere('m.record_id in (select fct_filter_encodable_row(?,?,?))', array(implode(',',$items),'spec_ref', $user_id));

    $query->execute();
  }

}
