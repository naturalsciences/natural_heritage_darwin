<?php

/**
 * Bibliography form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BibliographyForm extends BaseBibliographyForm
{
  public function configure()
  {
    $this->useFields(array('title', 'type', 'abstract','year'));
    $this->widgetSchema['title'] = new sfWidgetFormInputText();
    $this->widgetSchema['title']->setAttributes(array('class'=>'medium_size'));
    $this->validatorSchema['title'] = new sfValidatorString(array('required' => true, 'trim' => true));

    $this->validatorSchema['year'] = new sfValidatorInteger(array('required'=>false,'min'=> 0,'max' => date('Y')+2  ));
    $this->widgetSchema['year']->setAttributes(array('class'=>'small_size'));

    $choices = Bibliography::getAvailableTypes();
    $this->widgetSchema['type'] =  new sfWidgetFormChoice(array(
      'choices' =>  $choices,  
    ));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required'=>true,'choices'=>array_keys($choices)));

    $subForm = new sfForm();
    $this->embedForm('Authors',$subForm);   
    foreach(Doctrine::getTable('CataloguePeople')->getPeopleRelated('bibliography','author',$this->getObject()->getId()) as $key=>$vals)
    {
      $form = new PeopleAssociationsForm($vals);
      $this->embeddedForms['Authors']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('Authors', $this->embeddedForms['Authors']); 
    
    $subForm = new sfForm();
    $this->embedForm('newAuthor',$subForm);
  }

  public function addAuthor($num, $people_ref,$order_by=0 , $user = null)
  {
      $options = array('referenced_relation' => 'bibliography', 'people_type' => 'author', 'people_ref' => $people_ref, 'order_by' => $order_by);
      if(!$user)
       $val = new CataloguePeople();
      else
       $val = $user ; 
      $val->fromArray($options);
      $val->setRecordId($this->getObject()->getId());
      $form = new PeopleAssociationsForm($val);
      $this->embeddedForms['newAuthor']->embedForm($num, $form);
      //Re-embedding the container
      $this->embedForm('newAuthor', $this->embeddedForms['newAuthor']);
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newAuthor']))
    {
      foreach($taintedValues['newAuthor'] as $key=>$newVal)
      {
        if (!isset($this['newAuthor'][$key]))
        {
          $this->addAuthor($key,$newVal['people_ref']);
        }
        $taintedValues['newAuthor'][$key]['record_id'] = 0;
        $taintedValues['newAuthor'][$key]['referenced_relation'] = 'bibliography';

      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
   if (null === $forms)
   {
      $value = $this->getValue('Authors');
      foreach($this->embeddedForms['Authors']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['people_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['Authors'][$name]);
        }
        else
        {
          $form->getObject()->setRecordId($this->getObject()->getId());
          $form->getObject()->setReferencedRelation('bibliography');
        }
      }

      $value = $this->getValue('newAuthor');
      foreach($this->embeddedForms['newAuthor']->getEmbeddedForms() as $name => $form)
      {
        $form->getObject()->setRecordId($this->getObject()->getId());
        $form->getObject()->setReferencedRelation('bibliography');
      } 
   }
   return parent::saveEmbeddedForms($con, $forms);
  }
}
