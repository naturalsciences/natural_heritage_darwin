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
    //modidied by ftheeten 2016 11 22 to add category
    //$this->useFields(array('id','url','comment'));
    $this->useFields(array('id','url','comment', 'category', 'contributor', 'disclaimer', 'license', 'display_order'));

    $this->widgetSchema['url'] = new sfWidgetFormInputText();
    $this->widgetSchema['url']->setAttributes(array('class'=>'small_medium_size'));
    //ftheeten 2016 11 22
    $this->widgetSchema['category']= new sfWidgetFormChoice(array(
        'choices' => array('document'=>'document','image link'=>'image_link', 'html snippet link'=>'html_snippet' , 'thumbnail'=>'thumbnail' ),
    ));
    
    /* Validators */
    $this->validatorSchema['id'] = new sfValidatorInteger(array('required'=>false));
    $this->validatorSchema['url'] = new sfValidatorString(array('required'=>false));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['category'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['contributor'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['disclaimer'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['license'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['display_order'] = new sfValidatorInteger(array('required'=>false));
    $this->mergePostValidator(new ExtLinksValidatorSchema());

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
