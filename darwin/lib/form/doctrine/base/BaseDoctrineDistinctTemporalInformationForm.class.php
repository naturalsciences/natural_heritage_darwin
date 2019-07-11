<?php

/**
 * DoctrineDistinctTemporalInformation form base class.
 *
 * @method DoctrineDistinctTemporalInformation getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseDoctrineDistinctTemporalInformationForm extends DarwinModelForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['from_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['from_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['from_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['from_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['to_date_mask'] = new sfWidgetFormInputText();
    $this->validatorSchema['to_date_mask'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['to_date'] = new sfWidgetFormTextarea();
    $this->validatorSchema['to_date'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['specimen_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'add_empty' => true));
    $this->validatorSchema['specimen_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Specimens'), 'column' => 'id', 'required' => false));

    $this->widgetSchema   ['gtu_ref'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'add_empty' => true));
    $this->validatorSchema['gtu_ref'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Gtu'), 'column' => 'id', 'required' => false));

    $this->widgetSchema->setNameFormat('doctrine_distinct_temporal_information[%s]');
  }

  public function getModelName()
  {
    return 'DoctrineDistinctTemporalInformation';
  }

}
