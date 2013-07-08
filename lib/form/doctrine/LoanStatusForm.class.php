<?php

/**
 * LoanStatus form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LoanStatusForm extends BaseLoanStatusForm
{
  public function configure()
  {
    $this->useFields(array('status','comment')) ;
    $statuses = $this->options['available_status'] ; 
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>false));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
  }
}
