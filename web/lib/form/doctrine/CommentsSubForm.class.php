<?php

/**
 * Comments form.
 *
 * @package    form
 * @subpackage Comments
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CommentsSubForm extends CommentsForm
{
  public function configure()
  {
    $this->useFields(array('id','notion_concerned','comment'));
    $choices = CommentsTable::getNotionsFor($this->getObject()->getReferencedRelation());
    $this->widgetSchema['notion_concerned'] =  new sfWidgetFormChoice(array(
      'choices' =>  $choices,  
    ));
    $this->widgetSchema['notion_concerned']->setAttributes(array('class' => 'small_size')) ;

    /* Validators */
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['notion_concerned'] = new sfValidatorChoice(array('required'=>false,'choices'=>array_keys($choices)));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    /*Comments post-validation to empty null values*/
    $this->mergePostValidator(new CommentsValidatorSchema());
  }
}
