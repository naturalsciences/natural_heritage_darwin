<?php

/**
 * ExtLinks filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseExtLinksFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'referenced_relation' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'record_id'           => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'url'                 => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'comment'             => new sfWidgetFormFilterInput(),
      'comment_indexed'     => new sfWidgetFormFilterInput(),
      'category'            => new sfWidgetFormFilterInput(),
      'contributor'         => new sfWidgetFormFilterInput(),
      'disclaimer'          => new sfWidgetFormFilterInput(),
      'license'             => new sfWidgetFormFilterInput(),
      'display_order'       => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'referenced_relation' => new sfValidatorPass(array('required' => false)),
      'record_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'url'                 => new sfValidatorPass(array('required' => false)),
      'comment'             => new sfValidatorPass(array('required' => false)),
      'comment_indexed'     => new sfValidatorPass(array('required' => false)),
      'category'            => new sfValidatorPass(array('required' => false)),
      'contributor'         => new sfValidatorPass(array('required' => false)),
      'disclaimer'          => new sfValidatorPass(array('required' => false)),
      'license'             => new sfValidatorPass(array('required' => false)),
      'display_order'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('ext_links_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ExtLinks';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'referenced_relation' => 'Text',
      'record_id'           => 'Number',
      'url'                 => 'Text',
      'comment'             => 'Text',
      'comment_indexed'     => 'Text',
      'category'            => 'Text',
      'contributor'         => 'Text',
      'disclaimer'          => 'Text',
      'license'             => 'Text',
      'display_order'       => 'Number',
    );
  }
}
