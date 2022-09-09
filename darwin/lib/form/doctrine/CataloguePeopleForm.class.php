<?php

/**
 * CataloguePeople form.
 *
 * @package    form
 * @subpackage CataloguePeople
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CataloguePeopleForm extends BaseCataloguePeopleForm
{
  public function configure()
  {
    $this->widgetSchema['referenced_relation'] = new sfWidgetFormInputHidden();
    $this->widgetSchema['record_id'] = new sfWidgetFormInputHidden();
    //ftheeten 2020 09 10 bug pass validation to save
    $this->validatorSchema['record_id'] = new sfValidatorPass();
    $this->widgetSchema['people_ref'] = new widgetFormJQueryDLookup(
      array(
            'model' => 'People',
            'method' => 'getFormatedName',
            'nullable' => false,
            'fieldsHidders' => array('catalogue_people_people_type', 
                                     'catalogue_people_people_sub_type',),
      ),
      array('class' => 'hidden',)
    );

    $types = array(
       'author' => $this->getI18N()->__('Author'),
      'expert' => $this->getI18N()->__('Expert'),
      );

    if($this->getObject()->getReferencedRelation()=='expeditions')
    {
      $types = array('member' => $this->getI18N()->__('Members'));
      $this->getObject()->setPeopleType('member');
    }

    $this->widgetSchema['people_type'] = new sfWidgetFormChoice(array('choices'=>$types));

    $this->validatorSchema['people_type'] = new sfValidatorChoice(array('choices'=>array_keys($types)));

    $this->widgetSchema['people_sub_type'] = new widgetFormSelectComplete(array(
        'model' => 'CataloguePeople',
        'change_label' => 'Pick a type in the list',
        'add_label' => 'Add another type',
    ));

    if($this->getObject()->isNew() && $this->getObject()->getPeopleType()=="author")
      $this->widgetSchema['people_sub_type']->setDefault('Main Author');
    else
      $this->widgetSchema['people_sub_type']->setDefault('General');
    $this->widgetSchema['people_sub_type']->setOption('forced_choices', Doctrine_Core::getTable('CataloguePeople')->getDistinctSubType($this->getObject()->getPeopleType()) );

    $this->widgetSchema->setLabels(array('people_type' => $this->getI18N()->__('Type'),
                                         'people_sub_type' => $this->getI18N()->__('Sub-Type'),
                                         'people_ref' => $this->getI18N()->__('Associated'),
                                        )
                                  );
    $this->widgetSchema->setHelps(array('people_type' => $this->getI18N()->__('Type'),
                                         'people_sub_type' => $this->getI18N()->__('Sub-Type'),
                                         'people_ref' => $this->getI18N()->__('Associated'),
                                        )
                                  );
  }
  
  public function forceSubType()
  {
    $this->widgetSchema['people_sub_type']->setOption('forced_choices', Doctrine_Core::getTable('CataloguePeople')->getDistinctSubType($this->getObject()->getPeopleType()) );
  }
}
