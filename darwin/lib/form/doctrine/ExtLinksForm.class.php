<?php

/**
 * ExtLinks form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExtLinksForm extends BaseExtLinksForm
{
  public function configure()
  {
    $this->useFields(array('id','type','url','comment', 'is_public'));

    $this->widgetSchema['url'] = new sfWidgetFormInputText();
    $this->widgetSchema['url']->setAttributes(array('class'=>'small_medium_size'));

    
	$this->widgetSchema['is_public'] = new sfWidgetFormInputCheckBox();
	$this->setDefault('is_public', true);
	$this->validatorSchema['is_public'] = new sfValidatorPass();

    /* Validators */
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['url'] = new sfValidatorString(array('required'=>true), array('required'=> 'The URL of the link is missing'));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->mergePostValidator(new ExtLinksValidatorSchema());

    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
      'choices' => ExtLinks::getLinkTypes(),
    ));

    $this->validatorSchema['type'] = new sfValidatorChoice(array("required"=> true,'choices'=>array_keys(ExtLinks::getLinkTypes())), array('required'=> 'Type of link is missing') );
    $this->widgetSchema['type']->setAttributes(array('class'=>'link_type_selector')); 

  }
  public function setRecordRef($relation, $rid)
  {
    $this->ref_relation =$relation;
    $this->ref_record_id = $rid;
  }

  public function updateObject($values = null)
  {
    $object = parent::updateObject($values);
    if(isset($this->ref_relation) && isset($this->ref_record_id))
    {
      $object->setReferencedRelation($this->ref_relation);
      $object->setRecordId($this->ref_record_id);
    }
    return $object;
  }
}
