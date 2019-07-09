<?php

/**
 * CollectionsRights form.
 *
 * @package    form
 * @subpackage CollectionsRights
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CollectionsRightsForm extends BaseCollectionsRightsForm
{
  public function configure()
  {
    unset($this['id'],
          $this['collection_ref']) ;
    $user_id=isset($option) ? $this->options['user_id'] : $this->getObject()->getUserRef() ;
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden();
    if($user_id == 0 ) $this->widgetSchema['user_ref']->setLabel('nobody') ;
    else $this->widgetSchema['user_ref']->setLabel(Doctrine_Core::getTable('Users')->findUser($user_id)->getFormatedName()) ;
    $this->widgetSchema['db_user_type'] = new sfWidgetFormChoice(array(
      'choices' =>  Users::getTypes(array('screen' => 2,'db_user_type' => Users::ADMIN)),
    ));    
    $this->widgetSchema->setDefault('db_user_type',Users::REGISTERED_USER) ;
    $this->widgetSchema->setHelp('user_ref',"Specify which function you want to give to this user for your collection. Two buttons are available on edition. The button 'on sub collection...' allow you to also give right for this user on sub collections managed by you. With the button 'manage widget' (visible only for register_user function), you can allow the visibility (read only) of private widget wicth a registered user normaly won't have right to see it") ;
    $this->validatorSchema['db_user_type'] = new sfValidatorPass();
    $this->validatorSchema['user_ref'] = new sfValidatorPass();
    $this->mergePostValidator(new CollectionsRightsValidatorSchema());     
  }
}
