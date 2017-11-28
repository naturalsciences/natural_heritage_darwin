<?php

/**
 * Comments form.
 *
 * @package    form
 * @subpackage Comments
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class EcologySubForm extends CommentsSubForm
{
  public function configure()
  {
     $this->useFields(array('id','notion_concerned','comment'));
     $this->getWidget('notion_concerned')->setDefault('ecology');
     
    //parent::configure();
    $this->widgetSchema['notion_concerned'] =new sfWidgetFormInputHidden();
    $this->getWidget('notion_concerned')->setDefault('ecology');
    //$this->validatorSchema['notion_concerned'] =new sfValidatorPass();
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->mergePostValidator(new CommentsValidatorSchema());
  }
}
