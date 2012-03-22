<?php
/**
 * synonym actions.
 *
 * @package    darwin
 * @subpackage synonym
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class synonymActions extends DarwinActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeAdd(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();   
    $this->classification = new ClassificationSynonymies();
    $this->classification->setReferencedRelation($request->getParameter('table'));

    $this->form = new ClassificationSynonymiesForm($this->classification, array('table' => $request->getParameter('table')));
    
    if($request->isMethod('post'))
    {
	    $this->form->bind($request->getParameter('classification_synonymies'));
	    if($this->form->isValid())
	    {
        if($this->form->getValue('record_id') == $request->getParameter('id'))
        {
          $error = new sfValidatorError(new savedValidator(),'You can\'t synonym yourself!');
          $this->form->getErrorSchema()->addError($error); 
        }
        else
        {
          try
          {
	          $conn = Doctrine_Manager::connection();
	          $conn->beginTransaction();

	          Doctrine::getTable('ClassificationSynonymies')
	            ->mergeSynonyms(
	              $request->getParameter('table'),
	              $this->form->getValue('record_id'),
	              $request->getParameter('id'),
	              $this->form->getValue('group_name')
	            );
	          $conn->commit();
	          return $this->renderText('ok');
          }
          catch(Doctrine_Exception $e)
          {
	          $conn->rollback();
	          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
	          $this->form->getErrorSchema()->addError($error); 
          }
        }
	    }
    }
    $formFilterName = DarwinTable::getFilterForTable($request->getParameter('table'));
    $this->searchForm = new $formFilterName(array('table'=> $request->getParameter('table') ));
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $conn = Doctrine_Manager::connection();
    $conn->beginTransaction();

    $synonym = Doctrine::getTable('ClassificationSynonymies')->find($request->getParameter('id'));
    
    if( Doctrine::getTable('ClassificationSynonymies')->countRecordInGroup($synonym->getGroupId()) > 2)
    {
      $synonym->delete();
    }
    else     //Delete the entire group if there is only two record
    {
      Doctrine::getTable('ClassificationSynonymies')->deleteAllItemInGroup( $synonym->getGroupId() );
    }
    $conn->commit();
    return $this->renderText('ok');
  }

  public function executeEditOrder(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();     
    $this->forward404Unless($request->isMethod('post'));
    Doctrine::getTable('ClassificationSynonymies')->saveOrder(substr($request->getParameter('order', ','),0,-1));
    return $this->renderText('ok');
  }

  public function executeSetBasionym(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod('post'));
    if($request->getParameter('uncheck'))
      Doctrine::getTable('ClassificationSynonymies')->resetBasionym($request->getParameter('group_id'));
    else
      Doctrine::getTable('ClassificationSynonymies')->setBasionym($request->getParameter('group_id'), $request->getParameter('id'));
    return $this->renderText('ok');
  }

  public function executeChecks(sfWebRequest $request)
  {
    return $this->renderText(
      Doctrine::getTable('ClassificationSynonymies')->findGroupIdFor(
      $request->getParameter('table'),
      $request->getParameter('id'),
      $request->getParameter('type'))
    );
  }
}
