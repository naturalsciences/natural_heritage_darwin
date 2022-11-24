<?php

/**
 * VCodesSeries filter form base class.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVCodesSeriesFormFilter extends CodesFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_full'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['code_full'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['serie_indexed'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['serie_indexed'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('v_codes_series_filters[%s]');
  }

  public function getModelName()
  {
    return 'VCodesSeries';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'code_full' => 'Text',
      'serie_indexed' => 'Text',
    ));
  }
}
