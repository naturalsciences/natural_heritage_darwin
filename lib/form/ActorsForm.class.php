<?php

/**
 * CataloguePeople form.
 *
 * @package    form
 * @subpackage CataloguePeople
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class ActorsForm extends CataloguePeopleForm
{
  public function configure()
  {
    $this->widgetSchema['people_ref'] = new sfWidgetFormInputHidden();

    $types = array(
       'sender' => $this->getI18N()->__('Sender'),
      'receiver' => $this->getI18N()->__('Receiver'),
      );

    if($this->getObject()->getReferencedRelation()=='loans')
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
    $this->widgetSchema['people_sub_type']->setOption('forced_choices', Doctrine::getTable('CataloguePeople')->getDistinctSubType($this->getObject()->getPeopleType()) );

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
    $this->widgetSchema['people_sub_type']->setOption('forced_choices', Doctrine::getTable('CataloguePeople')->getDistinctSubType($this->getObject()->getPeopleType()) );
  }
}
