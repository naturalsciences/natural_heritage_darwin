<?php

/**
 * MultimediaTodelete filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseMultimediaTodeleteFormFilter extends DarwinModelFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['uri'] = new sfWidgetFormFilterInput(array('with_empty' => false));
    $this->validatorSchema['uri'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('multimedia_todelete_filters[%s]');
  }

  public function getModelName()
  {
    return 'MultimediaTodelete';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'uri' => 'Text',
    ));
  }
}
