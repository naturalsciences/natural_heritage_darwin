<?php

/**
 * InformativeWorkflow filter form.
 *
 * @package    darwin
 * @subpackage filter
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InformativeWorkflowFormFilter extends BaseInformativeWorkflowFormFilter
{
  public function configure()
  {
    $this->useFields(array('status'));
    $this->addPagerItems();
    $this->widgetSchema->setNameFormat('searchWorkflows[%s]');        
    $status = informativeWorkflow::getAvailableStatus('all')  ;  

    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
        'choices'  => $status,
    ));    
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices'  => array_keys($status), 'required' => true));      
  } 
}
