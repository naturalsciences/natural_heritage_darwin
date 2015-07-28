<?php

/**
 * report actions.
 *
 * @package    darwin
 * @subpackage report
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class reportActions extends DarwinActions
{
  private $widgets;
  private $widgets_options;
  private $widgets_second_line_count = 0;

  /**
   * Sets the different variables in charge of storing the different dynamic
   * fields to be rendered on the report builder page
   * @param string $name the report name
   */
  private function setWidgetsOptions($name) {
    // Get the list of dynamicaly rendered fields depending the report targeted
    $this->widgets = Reports::getRequiredFieldForReport($name) ;
    // Get the list of options for these dynamicaly rendered fields
    $this->widgets_options = Reports::getRequiredFieldForReportOptions($name);
    // Count the ones that are dedicated to be set on a second line
    $this->widgets_second_line_count = 0;
    foreach(array_keys($this->widgets) as $widget_name){
      if(isset($this->widgets_options[$widget_name]) && !empty($this->widgets_options[$widget_name]['second_line']) && $this->widgets_options[$widget_name]['second_line']) {
        $this->widgets_second_line_count += 1;
      }
    }
  }

  public function preExecute()
  {
    if(! $this->getUser()->isAtLeast(Users::MANAGER))
    {
      $this->forwardToSecureAction();
    }
  }

  /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->report_list = Reports::getGlobalReports() ;
  }

  public function executeGetAskedReport(sfWebRequest $request)
  {
    if($request->isXmlHttpRequest())
    {
      //  retrieve all reports already asked by this user
      $user_report = Doctrine::getTable('Reports')->getUserReport($this->getUser()->isAtLeast(Users::ADMIN)?'all':$this->getUser()->getId());
      return $this->renderPartial("user_report_list", array('reports' => $user_report)) ;      
    }
  }

  public function executeGetReport(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('name'));
    $name = $request->getParameter('name');
    if($request->isXmlHttpRequest() && $request->isMethod('post'))
    {
      $this->setWidgetsOptions($name);
      $this->form = new ReportsForm(null,array('fields'=>$this->widgets,
                                               'name' => $name,
                                               'model_name' => $request->getParameter('catalogue','taxonomy')
                                              )
      ) ;
      if($request->getParameter('widgetButtonRefMultipleRefresh', '')=='1') {
        return $this->renderPartial("report_form_widget_button_ref_multiple_only",
                                    array('form' => $this->form,
                                          'fields'=> $this->widgets,
                                          'fields_options'=>$this->widgets_options,
                                          'fields_at_second_line'=>$this->widgets_second_line_count,
                                          'model_name'=> $request->getParameter('catalogue','taxonomy'),
                                          'fast' => Reports::getIsFast($name)
                                         )
        );
      }
      return $this->renderPartial("report_form",
                                  array('form' => $this->form,
                                        'fields'=> $this->widgets,
                                        'fields_options'=>$this->widgets_options,
                                        'fields_at_second_line'=>$this->widgets_second_line_count,
                                        'model_name'=> $request->getParameter('catalogue','taxonomy'),
                                        'fast' => Reports::getIsFast($name)
                                       )
      );
    }
    return false ;
  }

  public function executeAdd(sfWebRequest $request)
  {
    if($request->isMethod('post'))
    {
      $name = $request->getParameter('reports')['name'] ;
      if(!$name)  $this->forwardToSecureAction();
      $this->setWidgetsOptions($name);
      $this->form = new ReportsForm(null,array('fields'=>$this->widgets,
                                               'name' => $name,
                                               'model_name' => $request->getParameter('catalogue','taxonomy')
                                        )
      );
      $this->form->bind($request->getParameter($this->form->getName()));
      if($this->form->isValid())
      {
        $report = new Reports() ;
        $report->fromArray(array(
          'name' => $name,
          'user_ref'=>$this->getUser()->getId(),
          'lang'=>$this->getUser()->getCulture(),
          'format'=>$request->getParameter('reports')['format'],
          'comment'=>$request->getParameter('reports')['comment'],
          ));
        $report->setParameters($request->getParameter('reports')) ;
        //if it's a fast report, no need to save it, it can be downloaded directly
        if(Reports::getIsFast($name)) {
          $file = $report->getUrlReport();
          $this->processDownload($report,$file) ;
        }
        else $report->save() ;
        return $this->renderPartial("info_msg") ;
      }
      return $this->renderPartial("report_form",
                                  array('form' => $this->form,
                                        'fields'=> $this->widgets,
                                        'fields_options'=>$this->widgets_options,
                                        'fields_at_second_line'=>$this->widgets_second_line_count,
                                        'model_name'=> $request->getParameter('catalogue','taxonomy'),
                                        'fast' => Reports::getIsFast($name)
                                  )
      );
    }
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->hasParameter('id'));
    $this->report = Doctrine::getTable('Reports')->find($request->getParameter('id'));
    $uri = $this->report->getUri()?sfConfig::get('sf_upload_dir').$this->report->getUri():null ;
    $this->report->delete() ;
    @unlink($uri) ;
    if($request->isXmlHttpRequest())
    {
      return $this->renderText('ok');
    }
    return $this->redirect('report/index');
  }

  public function executeDownloadFile(sfWebRequest $request)
  {
    $this->setLayout(false);
    $report = Doctrine::getTable('Reports')->find($request->getParameter('id'));  
    $this->forward404Unless(file_exists($file = sfConfig::get('sf_upload_dir').$report->getUri()),sprintf('This file does not exist') );

    // Adding the file to the Response object
    $this->getResponse()->clearHttpHeaders();
    $this->getResponse()->setHttpHeader('Pragma: private', true);
    $this->getResponse()->setHttpHeader('Content-Disposition',
                            'attachment; filename="'.
                            $report->getName().".".$report->getFormat().'"');
    //$this->getResponse()->setContentType(Multimedia::getMimeTypeFor($report->getFormat()));
    $this->getResponse()->setContentType("application/force-download ".Multimedia::getMimeTypeFor($report->getFormat()));
    $this->getResponse()->setHttpHeader('content-type', 'application/octet-stream', true);

    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($file));
    return sfView::NONE;
  }
}
