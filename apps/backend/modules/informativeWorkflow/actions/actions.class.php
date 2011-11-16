<?php

/**
 * informativeWorkflow actions.
 *
 * @package    darwin
 * @subpackage informativeWorkflow
 * @author     DB team <collections@naturalsciences.be>
 */
class informativeWorkflowActions extends DarwinActions
{
  public function executeAdd(sfWebRequest $request)
  {    
    $data = array(
        'referenced_relation' => $request->getParameter('table'),
        'record_id' => $request->getParameter('id'),
        'status' => $request->getParameter('status'),   
        'comment' => $request->getParameter('comment'),    
        'user_ref' => $this->getUser()->getId()) ;    
        
    $workflow = new InformativeWorkflow() ;
    $workflow->fromArray($data) ;
    $workflow->save() ;
    return $this->renderText('ok') ;
  }

}
