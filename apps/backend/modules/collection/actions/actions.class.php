<?php

/**
 * collection actions.
 *
 * @package    darwin
 * @subpackage collection
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class collectionActions extends DarwinActions
{
  protected $widgetCategory = 'catalogue_collections_widget';

  public function executeAddSpecCodes(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $this->forward404Unless($request->hasParameter('id'));
    if(!$this->getUser()->isAtLeast(Users::ADMIN) &&  !Doctrine::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('id'),$this->getUser()->getId()))
      $this->forwardToSecureAction();

    $this->collCodes = Doctrine::getTable('Collections')->find($request->getParameter('id'));
    $this->form = new CollectionsCodesForm($this->collCodes);

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('collections'));
      if($this->form->isValid())
      {
        try
        {
          $this->form->save();
          return $this->renderText('ok');
        }
        catch(Exception $e)
        {
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }
  }

  public function executeExtdinfo(sfWebRequest $request)
  {
    $this->manager = Doctrine::getTable('Users')->getManagerWithId($request->getParameter('id'));
    $this->forward404Unless($this->manager,'No such item');

    $this->coms = Doctrine::getTable('UsersComm')->fetchByUser($this->manager->getId());
    if(ctype_digit($request->getParameter('staffid')))
      $this->staff = Doctrine::getTable('Users')->find($request->getParameter('staffid'));
  }

  public function executeDeleteSpecCodes(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $this->forward404Unless($request->hasParameter('id'),'No id given');
    if(!$this->getUser()->isAtLeast(Users::ADMIN) &&  !Doctrine::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('id'),$this->getUser()->getId()))
      $this->forwardToSecureAction();

    $item = Doctrine::getTable('Collections')->find($request->getParameter('id'));
    $this->forward404Unless($item,'No such item');
    try
    {
      $item->setCodePrefix(Doctrine::getTable('Collections')->getDefaultValueOf('code_prefix'));
      $item->setCodePrefixSeparator(Doctrine::getTable('Collections')->getDefaultValueOf('code_prefix_separator'));
      $item->setCodeSuffix(Doctrine::getTable('Collections')->getDefaultValueOf('code_suffix'));
      $item->setCodeSuffixSeparator(Doctrine::getTable('Collections')->getDefaultValueOf('code_suffix_separator'));
      $item->setCodeAutoIncrement(Doctrine::getTable('Collections')->getDefaultValueOf('code_auto_increment'));
      $item->setCodeAutoIncrement(Doctrine::getTable('Collections')->getDefaultValueOf('code_auto_increment_for_insert_only'));
      $item->save();
      return $this->renderText('ok');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      return $this->renderText($e->getMessage());
    }
  }

  public function executeCompleteOptions(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $this->collections = Doctrine::getTable('Collections')->getDistinctCollectionByInstitution($request->getParameter('institution'));
    $this->setLayout(false);
  }

  /* function that modify the institution when we change the parent_ref */
  public function executeSetInstitution(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $collection = new Collections() ;
    $collection->setInstitutionRef(Doctrine::getTable('Collections')->getInstitutionNameByCollection($request->getParameter('parent_ref'))->getId()) ;
    $this->form = new CollectionsForm($collection);
    $this->setLayout(false);
  }

  public function executeChoose(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::ENCODER) ) $this->forwardToSecureAction();

    $this->institutions = Doctrine::getTable('Collections')->fetchByInstitutionList($this->getUser(),null,false,true);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->institutions = Doctrine::getTable('Collections')->fetchByInstitutionList($this->getUser());
  }

  public function executeNew(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $duplic = $request->getParameter('duplicate_id','0') ;
    $collection = $this->getRecordIfDuplicate($duplic, new Collections());
    $this->form = new CollectionsForm($collection, array('duplicate'=> true));
    if ($duplic)
    {
      $User = Doctrine::getTable('CollectionsRights')->getAllUserRef($collection->getId()) ;
      foreach ($User as $key=>$val)
      {
         $this->form->addValue($key, $val->getUserRef(),'encoder');
      }
    }
  }

  public function executeCreate(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $this->forward404Unless($request->isMethod('post'));
    $options = $request->getParameter('collections');
    $form_options = array('new_with_error' => true);
    if ( $options['institution_ref'] != '' && $options['institution_ref'] !== null ) {
      $form_options['institution'] = $options['institution_ref'];
    }
    $this->form = new CollectionsForm(null, $form_options);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
    if(!$this->getUser()->isAtLeast(Users::ADMIN) &&  !Doctrine::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('id'),$this->getUser()->getId()))
      $this->forwardToSecureAction();
    $collection = Doctrine::getTable('Collections')->find($request->getParameter('id'));
    $this->forward404Unless($collection, 'collections does not exist');
    $this->level = $this->getUser()->getDbUserType() ;
    $this->form = new CollectionsForm($collection);
    $this->loadWidgets();
  }

  public function executeAddValue(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $number = intval($request->getParameter('num'));
    $user_ref = intval($request->getParameter('user_ref'));

    if($request->hasParameter('id'))
    {
      $this->ref_id = $request->getParameter('id') ;
	    $collection = Doctrine::getTable('Collections')->find($this->ref_id) ;
      $form = new CollectionsForm($collection);
    }
    else $form = new CollectionsForm();
    $form->addValue($number,$user_ref,1);

    return $this->renderPartial('coll_rights',array('form' => $form['newVal'][$number],'ref_id' => ''));
  }

  public function executeUpdate(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    $this->level = $this->getUser()->getDbUserType() ;
    $collection = Doctrine::getTable('Collections')->find($request->getParameter('id'));
    $this->forward404Unless($collection, 'collections does not exist');
    $this->form = new CollectionsForm($collection);

    $this->processForm($request, $this->form);
    $this->loadWidgets();

    $this->setTemplate('edit');
  }

   /**
   * @TODO: PREVENT error when has child!
   */
  public function executeDelete(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();
    if(!$this->getUser()->isAtLeast(Users::ADMIN) && !Doctrine::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('id'),$this->getUser()->getId()))
      $this->forwardToSecureAction();
    $request->checkCSRFProtection();
    $collection = Doctrine::getTable('Collections')->find($request->getParameter('id'));

    $this->forward404Unless($collection, 'collections does not exist');

    try
    {
      $collection->delete();
    }
    catch(Doctrine_Connection_Pgsql_Exception $e)
    {
      $this->form = new CollectionsForm($collection);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
      return ;
    }
    $this->redirect('collection/index');
  }

  public function executeRights(sfWebRequest $request)
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER) ) $this->forwardToSecureAction();

    $user_ref = $request->getParameter('user_ref');
    $parent_ref = $request->getParameter('collection_ref');

    /*** Check if the user has rights on this collection ***/
    if(!$this->getUser()->isAtLeast(Users::ADMIN) && !Doctrine::getTable('CollectionsRights')->findOneByCollectionRefAndUserRef($parent_ref,$this->getUser()->getId()))
      $this->forwardToSecureAction();

    $this->form = new SubCollectionsForm(null, array('current_user' => $this->getUser(),'collection_ref' => $parent_ref ,'user_ref' => $user_ref));
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('sub_collection')) ;
      if($this->form->isValid())
      {
        try
        {
          $this->form->save();
        }
        catch(Doctrine_Connection_Pgsql_Exception $e)
        {
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
          return ;
        }
        return $this->renderText('ok') ;
      }
    }
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));

    if ($form->isValid())
    {
        try{
            $collections = $form->save();
            $this->redirect('collection/edit?id='.$collections->getId());
        }
        catch(Exception $e)
        {
            $error = new sfValidatorError(new savedValidator(),$e->getMessage());
            $form->getErrorSchema()->addError($error);
        }
    }
  }

  public function executeWidgetsRight(sfWebRequest $request)
  {
    if($this->getUser()->getDbUserType() < Users::MANAGER ) $this->forwardToSecureAction();
    if(!$this->getUser()->isA(Users::ADMIN))
      if (!Doctrine::getTable('collectionsRights')->findOneByCollectionRefAndUserRef($request->getParameter('collection_ref'),$this->getUser()->getId()))
        $this->forwardToSecureAction();
    $id = $request->getParameter('user_ref');
    $this->form = new WidgetRightsForm(null,array('user_ref' => $id,'collection_ref' => $request->getParameter('collection_ref'))) ;
    $this->user = Doctrine::getTable("Users")->findUser($id) ;
    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('widget_rights')) ;
      if($this->form->isValid())
      {
        $this->form->save();
        return $this->renderText('ok') ;
      }
    }
  }
}
