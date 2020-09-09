<?php

/**
 * methods_and_tools actions.
 *
 * @package    darwin
 * @subpackage methods_and_tools
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class methods_and_toolsActions extends DarwinActions
{

  protected $widgetCategory = 'catalogue_methods_and_tools_widget';

  /**
    * Action executed when trying to add a new method in collecting methods table
    * Returns id if successfull and error message if not
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeAddMethod(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Test well action is Ajaxly called and that value parameter exist
    $this->forward404Unless($request->isMethod(sfRequest::POST)
                            && $request->isXmlHttpRequest()
                            && $request->hasParameter('value')
                           );
    try
    {
      // Define a new object and try to add and save new value passed
      $newMethod = new CollectingMethods;
      $newMethod->setMethod($request->getParameter('value'));
      $newMethod->save();
      // Return id of new record saved
      $response = $newMethod->getId();
    }
    catch (Doctrine_Exception $ne)
    {
      // Return database error if occurs
      $e = new DarwinPgErrorParser($ne);
      $response = $e->getMessage();
    }
    return $this->renderText($response);
  }

  /**
    * Action executed when trying to add a new tool in collecting tools table
    * Returns id if successfull and error message if not
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeAddTool(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Test well action is Ajaxly called and that value parameter exist
    $this->forward404Unless($request->isMethod(sfRequest::POST)
                            && $request->isXmlHttpRequest()
                            && $request->hasParameter('value')
                           );
    try
    {
      // Define a new object and try to add and save new value passed
      $newTool = new CollectingTools;
      $newTool->setTool($request->getParameter('value'));
      $newTool->save();
      // Return id of new record saved
      $response = $newTool->getId();
    }
    catch (Doctrine_Exception $ne)
    {
      // Return database error if occurs
      $e = new DarwinPgErrorParser($ne);
      $response = $e->getMessage();
    }
    return $this->renderText($response);
  }

  /**
    * Action executed when calling the general index
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeIndex(sfWebRequest $request)
  {
    if($request->getParameter('notion','')=='tool')
    {
      $this->executeToolsIndex($request);
      $this->setTemplate('toolsIndex');
    }
    else
    {
      $this->executeMethodsIndex($request);
      $this->setTemplate('methodsIndex');
    }
  }

  /**
    * Action executed when calling the methods directly
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeMethodsIndex(sfWebRequest $request)
  {
    // Set common values with default order by field = method
    $this->setCommonValues('methods_and_tools', 'method', $request);
    // Adapt the s_url (search url) to give the right notion to trigger the search on'
    $this->s_url = 'methods_and_tools/search?notion=method&is_choose='.$this->is_choose;
    //  Initialization of the Search methods form
    $this->form = new CollectingMethodsFormFilter();
  }

  /**
    * Action executed when calling the tools directly
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeToolsIndex(sfWebRequest $request)
  {
    // Set common values with default order by field = tool
    $this->setCommonValues('methods_and_tools', 'tool', $request);
    // Adapt the s_url (search url) to give the right notion to trigger the search on'
    $this->s_url = 'methods_and_tools/search?notion=tool&is_choose='.$this->is_choose;
    //  Initialization of the Search methods form
    $this->form = new CollectingToolsFormFilter();
  }

  /**
    * Action executed when searching an expedition - trigger by the click on the search button
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeSearch(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    // Test also that notion is well defined as tool or method
    $this->forward404Unless($request->isMethod('post') &&
                            ($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool')
                           );
    // Define notion parameter
    $this->notion = $request->getParameter('notion');
    $this->setCommonValues('methods_and_tools', $this->notion, $request);
    // Adapt the s_url (search url) to give the right notion to trigger the search on'
    $this->s_url = 'methods_and_tools/search?notion='.$this->notion.'&is_choose='.$this->is_choose;
    // Instantiate a new form filter
    if($this->notion=='method')
    {
      $this->form = new CollectingMethodsFormFilter();
    }
    else
    {
      $this->form = new CollectingToolsFormFilter();
    }
    // Triggers the search result function
    $this->searchResults($this->form, $request);
  }

  /**
    * Method executed when searching an expedition - trigger by the click on the search button
    * @param SearchExpeditionForm $form    The search expedition form instantiated that will be binded with the data contained in request
    * @param sfWebRequest         $request Request coming from browser
    * @var   int                  $pagerSlidingSize: Get the config value to define the range size of pager to be displayed in numbers (i.e.: with a value of 5, it will give this: << < 1 2 3 4 5 > >>)
    */
  protected function searchResults($form, sfWebRequest $request)
  {
    if($request->getParameter('searchMethodsAndTools','') !== '')
    {
      // Bind form with data contained in searchExpedition array
      $form->bind($request->getParameter('searchMethodsAndTools'));
      // Test that the form binded is still valid (no errors)
      if ($form->isValid())
      {
        // Define all properties that will be either used by the data query or by the pager
        // They take their values from the request. If not present, a default value is defined
        $query = $form->getQuery()->orderby($this->orderBy . ' ' . $this->orderDir);
        // Define in one line a pager Layout based on a pagerLayoutWithArrows object
        // This pager layout is based on a Doctrine_Pager, itself based on a customed Doctrine_Query object (call to the getExpLike method of ExpeditionTable class)
        $this->pagerLayout = new PagerLayoutWithArrows(new DarwinPager($query,
                                                                          $this->currentPage,
                                                                          $form->getValue('rec_per_page')
                                                                         ),
                                                       new Doctrine_Pager_Range_Sliding(array('chunk' => $this->pagerSlidingSize)),
                                                       $this->getController()->genUrl($this->s_url.$this->o_url).'/page/{%page_number}'
                                                      );
        // Sets the Pager Layout templates
        $this->setDefaultPaggingLayout($this->pagerLayout);
        // If pager not yet executed, this means the query has to be executed for data loading
        if (! $this->pagerLayout->getPager()->getExecuted())
           $this->methods_and_tools = $this->pagerLayout->execute();
      }
      $this->level = $this->getUser()->getDbUserType() ;
    }
  }

  /**
    * Action executed when calling expedition/new: will trigger the display of a new empty form for new expedition encoding
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeNew(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Forward to a 404 page if notion is not defined as tool or method
    $this->forward404Unless($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool');
    // Get duplicate id parameter
    $duplic = $request->getParameter('duplicate_id','0') ;
    // Define notion parameter
    $this->notion = $request->getParameter('notion');
    if($this->notion=='method')
    {
      $method = new CollectingMethods() ;
      $method = $this->getRecordIfDuplicate($duplic, $method);
      // Initialization of a new encoding expedition form
      $this->form = new CollectingMethodsForm($method);
    }
    else
    {
      $tool = new CollectingTools() ;
      $tool = $this->getRecordIfDuplicate($duplic, $tool);
      // Initialization of a new encoding expedition form
      $this->form = new CollectingToolsForm($tool);
    }
  }

  /**
    * Action executed when creating a new expedition by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeCreate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the method used is not a post
    // Test also that notion is well defined as tool or method
    $this->forward404Unless($request->isMethod('post') &&
                            ($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool')
                           );
    // Define notion parameter
    $this->notion = $request->getParameter('notion');
    // Instantiate a new form
    if($this->notion=='method')
    {
      $this->form = new CollectingMethodsForm();
    }
    else
    {
      $this->form = new CollectingToolsForm();
    }
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    // Set the template to new
    $this->setTemplate('new');
  }

  /**
    * Action executed when calling expedition/edit: will trigger the display of a form for expedition encoding
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeEdit(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Forward to a 404 page if notion is not defined as tool or method
    $this->forward404Unless($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool');
    // Set notion parameter
    $this->notion = $request->getParameter('notion');
    if($this->notion=='method')
    {
      // Forward to a 404 page if the requested method id is not found
      $method = Doctrine_Core::getTable('CollectingMethods')->find($request->getParameter('id'));
      $this->forward404Unless($method, sprintf('Object method does not exist (%s).', $request->getParameter('id')));
      // Otherwise initialize the method encoding form
      $this->form = new CollectingMethodsForm($method);
    }
    else
    {
      // Forward to a 404 page if the requested tool id is not found
      $tool = Doctrine_Core::getTable('CollectingTools')->find($request->getParameter('id'));
      $this->forward404Unless($tool, sprintf('Object tool does not exist (%s).', $request->getParameter('id')));
      // Otherwise initialize the tool encoding form
      $this->form = new CollectingToolsForm($tool);
    }
    $this->loadWidgets();
  }

  /**
    * Action executed when updating expedition data by clicking on the save after edition
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeUpdate(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // If method is <> from post or put and if the id edited and to be saved doesn't exist anymore... forward to a 404 page
    $this->forward404Unless($request->isMethod('post') || $request->isMethod('put'));
    // Forward to a 404 page if notion is not defined as tool or method
    $this->forward404Unless($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool');
    // Set notion parameter
    $this->notion = $request->getParameter('notion');
    if($this->notion=='method')
    {
      $method = Doctrine_Core::getTable('CollectingMethods')->find($request->getParameter('id'));
      $this->forward404Unless($method, sprintf('Object method does not exist (%s).', $request->getParameter('id')));
      // Instantiate a new method form
      $this->form = new CollectingMethodsForm($method);
    }
    else
    {
      $tool = Doctrine_Core::getTable('CollectingTools')->find($request->getParameter('id'));
      $this->forward404Unless($tool, sprintf('Object tool does not exist (%s).', $request->getParameter('id')));
      // Instantiate a new tool form
      $this->form = new CollectingToolsForm($tool);
    }
    // Process the form for saving informations
    $this->processForm($request, $this->form);
    $this->loadWidgets();
    // Set the template to edit
    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      try
      {
        $item = $form->save();
        $this->redirect('methods_and_tools/edit?notion='.$this->notion.'&id='.$item->getId());
      }
      catch(Doctrine_Exception $ne)
      {
        $e = new DarwinPgErrorParser($ne);
        $error = new sfValidatorError(new savedValidator(),$e->getMessage());
        $form->getErrorSchema()->addError($error);
      }
    }
  }

  /**
    * Action executed when deleting an expedition by clicking on the delete link
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeDelete(sfWebRequest $request)
  {
    if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
    // Trigger the protection against the XSS attack
    $request->checkCSRFProtection();
    // Forward to a 404 page if the expedition to be deleted has not been found
    // Forward to a 404 page if notion is not defined as tool or method
    $this->forward404Unless($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool');
    // Set notion parameter
    $this->notion = $request->getParameter('notion');
    if($this->notion=='method')
    {
      $tool_or_method = Doctrine_Core::getTable('CollectingMethods')->find($request->getParameter('id'));
      $this->forward404Unless($tool_or_method, sprintf('Object method does not exist (%s).', $request->getParameter('id')));
    }
    else
    {
      $tool_or_method = Doctrine_Core::getTable('CollectingTools')->find($request->getParameter('id'));
      $this->forward404Unless($tool_or_method, sprintf('Object tool does not exist (%s).', $request->getParameter('id')));
    }
    // Effectively triggers the delete method of the expedition table
    try
    {
      $tool_or_method->delete();
      $this->redirect('methods_and_tools/'.$this->notion.'sIndex');
    }
    catch(Doctrine_Exception $ne)
    {
      $e = new DarwinPgErrorParser($ne);
      $error = new sfValidatorError(new savedValidator(),$e->getMessage());
      if($this->notion=='method')
      {
        $this->form = new CollectingMethodsForm($tool_or_method);
      }
      else
      {
        $this->form = new CollectingToolsForm($tool_or_method);
      }
      $this->form->getErrorSchema()->addError($error);
      $this->loadWidgets();
      $this->setTemplate('edit');
    }
  }

  /**
    * Action executed when calling the expeditions from an other screen
    * @param sfWebRequest $request Request coming from browser
    */
  public function executeChoose(sfWebRequest $request)
  {
    // Forward to a 404 page if the method used is not a post
    // Test also that notion is well defined as tool or method
    $this->forward404Unless($request->getParameter('notion','')=='method' || $request->getParameter('notion','')=='tool');
    // Define notion parameter
    $this->notion = $request->getParameter('notion');
    $this->setCommonValues('methods_and_tools', $this->notion, $request);
    // Adapt the s_url (search url) to give the right notion to trigger the search on'
    $this->s_url = 'methods_and_tools/search?notion='.$this->notion.'&is_choose='.$this->is_choose;
    // Instantiate a new form filter
    if($this->notion=='method')
    {
      $this->form = new CollectingMethodsFormFilter();
    }
    else
    {
      $this->form = new CollectingToolsFormFilter();
    }
    // Remove surrounding layout
    $this->setLayout(false);
  }
  
    public function executeDownloadTab(sfWebRequest $request)
  {  
      
	   if($this->getUser()->isA(Users::REGISTERED_USER)) $this->forwardToSecureAction();
	   $notion=$request->getParameter('notion','tool');
	   $this->getResponse()->setHttpHeader('Content-type','text/tab-separated-values');
		$this->getResponse()->setHttpHeader('Content-disposition','attachment; filename="darwin_'.$notion.'.txt"');
		$this->getResponse()->setHttpHeader('Pragma', 'no-cache');
		$this->getResponse()->setHttpHeader('Expires', '0');
		
		$this->getResponse()->sendHttpHeaders(); //edited to add the missed sendHttpHeaders
		
		$this->getResponse()->sendContent();   
	   
        
        if($notion=="method")
        {
            $form = new CollectingMethodsFormFilter();
        }
        else
        {
            $form = new CollectingToolsFormFilter();
        }
		
	
		if($request->getParameter("searchMethodsAndTools",'') !== '')
		{
           
            $form->bind($request->getParameter("searchMethodsAndTools"));
            if ($form->isValid())
            {
               
                $query = $form->getQuery();			
                $result = $query->execute();                
                $returned=Array();
                foreach($result as $row)
                {
					$line=Array();
					$line[]=$row->getId();                    
                    $line[]=$row->getName();
                    $line[]=$row->getCountspecimens();
                    $line[]=$row->getCollections();
                    if(isset($comments[$row->getId()]))
					{
						 $line[]=preg_replace('/\r\n?/', ".", $comments[$row->getId()]);
					}
					else
					{
						$line[]="";
					}                    
                    $returned[]=implode("\t", $line);
                }
                print(implode("\t", array("id","Name", "nb_specimens", "collections" ))."\r\n");
                print(implode("\r\n", $returned));
            }
       
		}
		
		return sfView::NONE;           
	  
  }

}

