<?php

/**
 * InformativeWorkflow form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <collections@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InformativeWorkflowForm extends BaseInformativeWorkflowForm
{
  public function configure()
  {
    unset(
      $this['id'],
      $this['modification_date_time'],
      $this['people_ref'],
      $this['formated_name']);
    $statuses = $this->options['available_status'] ; 
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $statuses,
    ));
    
    $this->validatorSchema['comment'] = new sfValidatorString(array('trim'=>true, 'required'=>true));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($statuses), 'required' => true));
  }
}
