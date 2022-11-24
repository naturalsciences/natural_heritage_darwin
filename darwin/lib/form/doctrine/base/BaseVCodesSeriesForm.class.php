<?php

/**
 * VCodesSeries form base class.
 *
 * @method VCodesSeries getObject() Returns the current form's model object
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id$
 */
abstract class BaseVCodesSeriesForm extends CodesForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['code_full'] = new sfWidgetFormTextarea();
    $this->validatorSchema['code_full'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['serie_indexed'] = new sfWidgetFormTextarea();
    $this->validatorSchema['serie_indexed'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema->setNameFormat('v_codes_series[%s]');
  }

  public function getModelName()
  {
    return 'VCodesSeries';
  }

}
