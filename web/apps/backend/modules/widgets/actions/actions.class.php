<?php

/**
 * widgets actions.
 *
 * @package    darwin
 * @subpackage widgets
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class widgetsActions extends DarwinActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
 */
  public function executeGetWidgetPosition(sfWebRequest $request)
  {
    $widget = Doctrine::getTable('MyWidgets')->getWidget(
      $this->getUser()->getAttribute('db_user_id'),
      $request->getParameter('widget',false),
      $request->getParameter('category')."_widget"
    );

    $this->forward404unless($widget);

    return $this->renderText(json_encode(array('col_num'=>$widget->getColNum(), 'order_by'=>$widget->getOrderBy())));
  }

  public function executeAddWidget(sfWebRequest $request)
  {
    $widget = Doctrine::getTable('MyWidgets')->getWidget($this->getUser()->getAttribute('db_user_id'),
                                                             $request->getParameter('widget',false),
                                                             $request->getParameter('category')."_widget");
    $this->forward404unless($widget);
  
    $positions = explode(',', $request->getParameter('place','0'));
    $this->forward404unless(count($positions) >= $widget->getColNum() );

    $widget->setVisible(true);
    $widget->setOrderBy($positions[$widget->getColNum()-1]);
    $widget->save();

    Doctrine::getTable('MyWidgets')->incrementOrder(
      $this->getUser()->getAttribute('db_user_id'),
      $request->getParameter('category')."_widget",
      $widget->getColNum(),
      $positions[$widget->getColNum()-1]
    );
    $category = $widget->getComponentFromCategory();
    if ($request->hasParameter('view')) $category .= "view" ;
    return $this->renderPartial('widgets/wlayout',array(
            'widget' => $request->getParameter('widget'),
            'is_opened' => true,
            'is_mandatory' => $widget->getMandatory(),
            'category' => $category,
            'title' => $widget->getTitlePerso(),
            'col_num' => $widget->getColNum(),
            'options' => array(
              'eid' =>  $request->getParameter('eid',null),
              'table' => $widget->getTableFromCategory($request->getParameter('table',null)),
              'view' => $request->hasParameter('view')?true:false,
            ),
    ));
  }

  public function executeChangeStatus(sfWebRequest $request)
  {
    Doctrine::getTable('MyWidgets')
      ->setUserRef($this->getUser()->getAttribute('db_user_id'))
      ->changeWidgetStatus($request->getParameter('category')."_widget", $request->getParameter('widget'), $request->getParameter('status'));
    return $this->renderText("ok");
  }

  public function executeChangeOrder(sfWebRequest $request)
  {
    $col1 = explode(',', $request->getParameter('col1'));
    $col2 = explode(',', $request->getParameter('col2'));
    Doctrine::getTable('MyWidgets')
      ->setUserRef($this->getUser()->getAttribute('db_user_id'))
      ->changeOrder($request->getParameter('category')."_widget", $col1, $col2);
    return $this->renderText(var_export($col1,true).var_export($col2,true));
  }

  public function executeReloadContent(sfWebRequest $request)
  {
    $w = new MyWidgets();
    $w->setCategory($request->getParameter('category'));
    return $this->renderComponent(
      $w->getComponentFromCategory(),
      $request->getParameter('widget'),
      array(
        'eid' =>  $request->getParameter('eid',null),
        'table' => $w->getTableFromCategory($request->getParameter('table')),
        'level' => $this->getUser()->getAttribute('db_user_type')
      )
    );
  }

}
