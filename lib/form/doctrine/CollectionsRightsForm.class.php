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
    unset($this['id']) ;
    $this->useFields(array('collection_ref', 'user_ref'));
    $this->widgetSchema['user_ref'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['user_ref']->setLabel(Doctrine::getTable('Users')->findUser($this->options['user_id'])->getFormatedName()) ;
    $this->widgetSchema['collection_ref'] = new sfWidgetFormInput();
    $this->validatorSchema['user_ref'] = new sfValidatorinteger(array('required' => false)) ;
    $this->validatorSchema['collection_ref'] = new sfValidatorinteger(array('required' => false)) ;       
  }
}
