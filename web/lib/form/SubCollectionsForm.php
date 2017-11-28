<?php

/**
 * Collections form.
 *
 * @package    form
 * @subpackage Collections
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class SubCollectionsForm extends sfForm
{
  public function configure()
  {
    $subForm = new sfForm();
    $collections = Doctrine::getTable('Collections')->fetchByCollectionParent($this->options['current_user'],$this->options['user_ref'], $this->options['collection_ref']);

    foreach ($collections as $record)
    {
      if(count($record->CollectionsRights) )
      {
        $right = $record->CollectionsRights[0];
      }
      else
      {
        $right = new CollectionsRights();
        $right->setCollectionRef($record->getId());
        $right->setDbUserType(0);
        $right->setUserRef($this->options['user_ref']);
      }

      $form = new SubCollectionsRightsForm($right, array('collection' => $record));
      $subForm->embedForm($record->getId(), $form);
    }

    $this->embedForm('collections',$subForm);
    $this->widgetSchema->setNameFormat('sub_collection[%s]');
  }
  
  public function save()
  {
    $values = $this->getValues();

    foreach($this->embeddedForms['collections']->getEmbeddedForms() as $key => $prefs)
    {
      if($values['collections'][$key]['db_user_type'] == '')
      {
         $prefs->getObject()->delete();
      }
      else
      {
        $prefs->updateObject($values['collections'][$key]);
        $prefs->getObject()->save();
      }
    }
  }
}
