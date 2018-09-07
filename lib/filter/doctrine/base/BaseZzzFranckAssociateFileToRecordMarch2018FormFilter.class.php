<?php

/**
 * ZzzFranckAssociateFileToRecordMarch2018 filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseZzzFranckAssociateFileToRecordMarch2018FormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'filename'        => new sfWidgetFormFilterInput(),
      'date_modified'   => new sfWidgetFormFilterInput(),
      'scientific_name' => new sfWidgetFormFilterInput(),
      'unitid'          => new sfWidgetFormFilterInput(),
      'kindofunit'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'filename'        => new sfValidatorPass(array('required' => false)),
      'date_modified'   => new sfValidatorPass(array('required' => false)),
      'scientific_name' => new sfValidatorPass(array('required' => false)),
      'unitid'          => new sfValidatorPass(array('required' => false)),
      'kindofunit'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('zzz_franck_associate_file_to_record_march2018_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ZzzFranckAssociateFileToRecordMarch2018';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'filename'        => 'Text',
      'date_modified'   => 'Text',
      'scientific_name' => 'Text',
      'unitid'          => 'Text',
      'kindofunit'      => 'Text',
    );
  }
}
