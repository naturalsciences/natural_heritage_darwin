<?php

/**
 * IgsSearch filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseIgsSearchFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'ig_num'                  => new sfWidgetFormFilterInput(),
      'ig_num_indexed'          => new sfWidgetFormFilterInput(),
      'ig_date_mask'            => new sfWidgetFormFilterInput(),
      'expedition_name'         => new sfWidgetFormFilterInput(),
      'expedition_name_indexed' => new sfWidgetFormFilterInput(),
      'expedition_ref'          => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'ig_num'                  => new sfValidatorPass(array('required' => false)),
      'ig_num_indexed'          => new sfValidatorPass(array('required' => false)),
      'ig_date_mask'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'expedition_name'         => new sfValidatorPass(array('required' => false)),
      'expedition_name_indexed' => new sfValidatorPass(array('required' => false)),
      'expedition_ref'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('igs_search_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'IgsSearch';
  }

  public function getFields()
  {
    return array(
      'ig_num'                  => 'Text',
      'ig_num_indexed'          => 'Text',
      'ig_date_mask'            => 'Number',
      'ig_ref'                  => 'Number',
      'expedition_name'         => 'Text',
      'expedition_name_indexed' => 'Text',
      'expedition_ref'          => 'Number',
    );
  }
}
