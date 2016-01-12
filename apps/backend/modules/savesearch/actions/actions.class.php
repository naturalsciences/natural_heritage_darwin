<?php

/**
 * savesearch actions.
 *
 * @package    darwin
 * @subpackage savesearch
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class savesearchActions extends sfActions
{
  // Remove pinned form a saved search 
  public function executeRemovePin(sfWebRequest $request)
  {
    if($request->getParameter('search') && ctype_digit($request->getParameter('search')) && $request->getParameter('ids',"") != "" )
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('search'), $this->getUser()->getId());
      $this->forward404Unless($saved_search);

      $prev_req = $saved_search->getUnserialRequest();
      $old_ids = $saved_search->getAllSearchedId();

      $remove_ids = explode(',',$request->getParameter('ids'));

      $old_ids = array_diff($old_ids, $remove_ids);

      $old_ids = array_unique($old_ids);
      $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$old_ids);
      $saved_search->setUnserialRequest($prev_req);
      $saved_search->save();
      return $this->renderText('ok');  
    } 
    return $this->renderText('nok');
  }

  public function executePin(sfWebRequest $request)
  {
    if( in_array($request->getParameter('source',""), array('specimen')))
    {
      $source =  $request->getParameter('source',"");
      if($request->getParameter('id') && ctype_digit($request->getParameter('id')))
      {
        if($request->getParameter('status') === '1')
          $this->getUser()->addPinTo($request->getParameter('id'), $source);
        else
          $this->getUser()->removePinTo($request->getParameter('id'), $source);

        return $this->renderText(json_encode(array(
          'status' => 'ok',
          'pinned' => $this->getUser()->getAllPinned($source),
        )));
      }
      elseif($request->getParameter('mid','') != '')
      {
        $ids = explode(',',$request->getParameter('mid'));
        foreach($ids as $id)
        {
          $id = trim($id);
          if(ctype_digit($id))
          {
            if($request->getParameter('status') === '1')
              $this->getUser()->addPinTo($id, $source);
            else
              $this->getUser()->removePinTo($id, $source);
          }
        }
        return $this->renderText(json_encode(array(
          'status' => 'ok',
          'pinned' => $this->getUser()->getAllPinned($source),
        )));
      }
    }
    $this->forward404();
  }
  
  public function executeFavorite(sfWebRequest $request)
  {
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    $this->forward404Unless($saved_search);
    if($request->getParameter('status') === '1')
      $saved_search->setFavorite(true);
    else
      $saved_search->setFavorite(false);

    $saved_search->save();
    return $this->renderText('ok');
  }
  
  public function executeSaveSearch(sfWebRequest $request)
  {
    $this->is_spec_search = false;
    /// FETCH an exisiting saved search for edition
    if($request->getParameter('id'))
    {
      $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('id'), $this->getUser()->getId());
    }
    else
    {
      ///Create a new saved search
      if($request->getParameter('my_saved_searches') != '')
      {
        $tab = $request->getParameter('my_saved_searches') ;
        $source = $tab['subject'] ;
      }
      else
        $source = $request->getParameter('source',"") ;
      $saved_search = new MySavedsearches() ;
      $cols_str = $request->getParameter('cols');
      $cols = explode('|',$cols_str);
      $saved_search->setVisibleFieldsInResult($cols);
      if($request->getParameter('type') == 'pin')
      {
        $this->forward404unless(in_array($source, array('specimen')));
        $saved_search->setSubject($source);

        $this->is_spec_search=true;
        if($request->getParameter('list_nr') == 'create')
        {
          $ids=implode(',',$this->getUser()->getAllPinned($saved_search->getSubject()) );
          if($ids=="") return $this->renderText('<ul class="error_list"><li>'.$this->getContext()->getI18N()->__('You must select a least 1 specimen').'</li></ul>');
          $criterias = array('specimen_search_filters'=> array('spec_ids' => $ids));
          $saved_search->setIsOnlyId(true);
        }
        else
        {
          $saved_search = Doctrine::getTable('MySavedSearches')->getSavedSearchByKey($request->getParameter('list_nr'), $this->getUser()->getId());

          $prev_req = $saved_search->getUnserialRequest();
          $old_ids = $saved_search->getAllSearchedId();

          $new_ids = array_merge($old_ids, $this->getUser()->getAllPinned($saved_search->getSubject()) ); 
          $new_ids = array_unique($new_ids);
          $prev_req['specimen_search_filters']['spec_ids'] = implode(',',$new_ids);
          $criterias = $prev_req;
        }
      } 
      else
      {
        $criterias = $request->getPostParameters();
        $saved_search->setIsOnlyId(false);
        $saved_search->setSubject($source);
      }
      $saved_search->setUnserialRequest($criterias) ;
    }

    $saved_search->setUserRef($this->getUser()->getId()) ;

    $this->form = new MySavedSearchesForm($saved_search,array('type'=>$request->getParameter('type'), 'is_reg_user' => $this->getUser()->isA(Users::REGISTERED_USER)));

    if($request->getParameter('my_saved_searches') != '')
    { 
      $this->form->bind($request->getParameter('my_saved_searches'));
      if ($this->form->isValid())
      {
        try{
          $this->form->save();
          $search = $this->form->getObject();
          if( $search->getIsOnlyId() === true )
            $this->getUser()->clearPinned($this->form->getValue('subject'));

          return $this->renderText('ok,' . $search->getId());
        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          return $this->renderText($e->getMessage());
        }
      }
    }
  }

  public function executeDeleteSavedSearch(sfWebRequest $request)
  {
    $r = Doctrine::getTable( DarwinTable::getModelForTable($request->getParameter('table')) )->find($request->getParameter('id'));
    $this->forward404Unless($r,'No such item');
    try{
      $is_spec_search = $r->getIsOnlyId();
      $r->delete();
      if(! $request->isXmlHttpRequest())
      {
        if($is_spec_search)
          return $this->redirect('savesearch/index?specimen=true');
        else
          return $this->redirect('savesearch/index');
      }
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $this->renderText($e->getMessage());
    }
    return $this->renderText("ok");
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $q = Doctrine::getTable('MySavedSearches')
        ->addUserOrder(null, $this->getUser()->getId());

    $this->is_only_spec = false;

    if($request->getParameter('specimen') != '')
      $this->is_only_spec = true;
    $this->searches = Doctrine::getTable('MySavedSearches')
        ->addIsSearch($q, ! $this->is_only_spec)
        ->execute();
  }
}
