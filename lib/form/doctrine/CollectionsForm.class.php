<?php

/**
 * Collections form.
 *
 * @package    form
 * @subpackage Collections
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CollectionsForm extends BaseCollectionsForm
{
  public function configure()
  {
    $this->useFields(array(
      'is_public',
      'code',
      'name',
      'institution_ref',
      'main_manager_ref',
      'staff_ref',
      'parent_ref',
      'collection_type',
    ));

    $this->widgetSchema['is_public'] = new sfWidgetFormInputCheckbox(array ('default' => 'true'), array('title' => 'checked = public'));
    $this->validatorSchema['is_public'] = new sfValidatorBoolean() ;
    $this->widgetSchema['code'] = new sfWidgetFormInputText();
    $this->widgetSchema['code']->setAttributes(array('class'=>'small_size'));
    $this->widgetSchema['name'] = new sfWidgetFormInputText();
    $this->widgetSchema['name']->setAttributes(array('class'=>'medium_size'));
    $this->widgetSchema['institution_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Institutions',
      'link_url' => 'institution/choose?with_js=1',
      'method' => 'getFamilyName',
      'box_title' => $this->getI18N()->__('Choose Institution'),
      'complete_url' => 'catalogue/completeName?table=institutions',
    ));
    $this->widgetSchema['institution_ref']->setLabel('Institution');

    $this->widgetSchema['main_manager_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Users',
      'link_url' => 'user/choose',
      'method' => 'getFormatedName',
      'box_title' => $this->getI18N()->__('Choose Manager'),
      'complete_url' => 'catalogue/completeName?table=users',
    ));

    $this->widgetSchema['staff_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Users',
      'link_url' => 'user/choose',
      'method' => 'getFormatedName',
      'nullable' => true,
      'box_title' => $this->getI18N()->__('Choose Staff Member'),
      'complete_url' => 'catalogue/completeName?table=users',
     ));
    $this->widgetSchema['staff_ref']->setLabel('Staff Member');

    $this->widgetSchema['main_manager_ref']->setLabel('Conservator');

    $this->widgetSchema['parent_ref'] = new sfWidgetFormChoice(array(
      'choices' =>  array(),
    ));
    $this->widgetSchema['parent_ref']->setLabel('Parent collection');

    $this->widgetSchema->setHelps(array(
      'is_public' => "Uncheck this option if you want your collection to be private. So this collection won't be visible in the public interface neither by simply registered user",
      'main_manager_ref' => "Specify the main manager for this collection, you can add other manager on the rights table below", )
    );

    $this->validatorSchema['collection_type'] = new sfValidatorChoice(array('choices' => array('mix' => 'mix', 'observation' => 'observation', 'physical' => 'physical'), 'required' => true));

    if(! $this->getObject()->isNew() || isset($this->options['duplicate']))
      $this->widgetSchema['parent_ref']->setOption('choices', Doctrine::getTable('Collections')->getDistinctCollectionByInstitution($this->getObject()->getInstitutionRef()) );
    elseif(isset($this->options['new_with_error']))
      $this->widgetSchema['parent_ref']->setOption('choices', Doctrine::getTable('Collections')->getDistinctCollectionByInstitution($this->options['institution']));

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkSelfAttached')))
    );

    $subForm = new sfForm();
    $this->embedForm('CollectionsRights',$subForm);
    foreach(Doctrine::getTable('CollectionsRights')->getAllUserRef($this->getObject()->getId()) as $key=>$vals)
    {
      $form = new CollectionsRightsForm($vals);
      $this->embeddedForms['CollectionsRights']->embedForm($key, $form);
    }
    //Re-embedding the container
    $this->embedForm('CollectionsRights', $this->embeddedForms['CollectionsRights']);

    $subForm = new sfForm();
    $this->embedForm('newVal',$subForm);
  }

  public function addValue($num,$user_id,$rights)
  {
    $val = new CollectionsRights() ;
    $val->Collections = $this->getObject();
    $val->setUserRef($user_id) ;
    $val->setDbUserType($rights) ;
    $form = new CollectionsRightsForm($val,array('user_id'=>$user_id));
    $this->embeddedForms['newVal']->embedForm($num, $form);
    //Re-embedding the container
    $this->embedForm('newVal', $this->embeddedForms['newVal']);
  }

  public function checkSelfAttached($validator, $values)
  {
    if(! empty($values['id']) )
    {
      if($values['parent_ref'] == $values['id'])
      {
        $error = new sfValidatorError($validator, "A collection can't be attached to itself");
        throw new sfValidatorErrorSchema($validator, array('parent_ref' => $error));
      }
    }
    return $values;
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if(isset($taintedValues['newVal']))
    {
      foreach($taintedValues['newVal'] as $key=>$newVal)
      {
        if (!isset($this['newVal'][$key]))
        {
          $this->addValue($key,$newVal['user_ref'],$newVal['db_user_type']);
        }
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $value = $this->getValue('CollectionsRights');
      foreach($this->embeddedForms['CollectionsRights']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['user_ref']))
        {
          $form->getObject()->delete();
          unset($this->embeddedForms['CollectionsRights'][$name]);
        }
      }

      $value = $this->getValue('newVal');
      foreach($this->embeddedForms['newVal']->getEmbeddedForms() as $name => $form)
      {
        if (!isset($value[$name]['user_ref']) || $value[$name]['user_ref'] == sfContext::getInstance()->getUser()->getId())
        {
          unset($this->embeddedForms['newVal'][$name]);
        }
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }
}
