<?php

class widgetFormButtonRef extends sfWidgetFormInputHidden
{

  public function ParentRender($name, $value = null, $attributes = array(), $errors = array()){
    return parent::render($name, $value, $attributes, $errors);
  }
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
      $class = array('class'=>'');
      if (isset($attributes['class']))
      {
        $class = array_merge($class, $attributes);
        $attributes['class'] .= ' hidden';
      }
      $class = ' '.$class['class'];
      $values = array_merge(array('text' => '', 'is_empty' => false), is_array($value) ? $value : array());
      $obj_name = $this->getName($value);
      if($this->getOption('default_name')) $obj_name = $this->getOption('default_name') ;
      $edit_route = $this->getOption( 'edit_route', null );
      $edit_route_params = $this->getOption( 'edit_route_params', array() );
      if ( !empty( $edit_route ) && !empty ( $obj_name ) && count( $edit_route_params ) != 0 ) {
        $route_params = array();
        foreach ( $edit_route_params as $route_param ) {
          switch ( $route_param ) {
            case "id":
              $route_params[$route_param] = $value;
            case "name":
              $route_params[$route_param] = $obj_name;
          }
        }
        $obj_name = link_to( $obj_name, $edit_route.'?'.http_build_query( $route_params ) );
      }
      $input = parent::render($name, $value, $attributes, $errors);
      $input .= $this->renderContentTag('div',$obj_name, array(
        'id' => $this->generateId($name)."_name",
        'class' => "ref_name" . $class,
      ));

      if($this->getOption('nullable'))
      {
        $options = array(
          'src' => '/images/remove.png',
                'id' => $this->generateId($name)."_clear",
          'class' => "ref_clear" . $class
        );

        if($value == 0)
          $options['class'] .= ' hidden';
        $input .= $this->renderTag('img',$options);
      }

      if (strlen($this->getOption('button_class')) > 0)
      {
        $class .= ' '.$this->getOption('button_class');
      }

      if($this->getOption('button_is_hidden') && $value == 0)
      {
        $class .= ' hidden';
      }
      $input .= '<div title="'.$this->getOption('box_title').'" id="'.$this->generateId($name).'_button" class="ref_name' .$class. '">';
      $in_text = '<span class="on';
      if(! ($obj_name=='' || $obj_name=='-'))  $in_text .=' hidden';
      $in_text .='">'. __('Choose !').'</span><span class="off';
      if($obj_name=='' || $obj_name=='-')  $in_text .=' hidden';
      $in_text .= '">'. __('Change !').'</span>';

      $url_params = '';
      if(count($this->getOption('url_params')) != 0 ) {
        $url_params = '?';
        foreach($this->getOption('url_params') as $k=>$v){
          $url_params .= urlencode($k).'='.urlencode($v);
        }
      }
      $input .= '<a href="'.url_for($this->getOption('link_url')).$url_params.'" class="but_text">'.$in_text.'</a>';

      $input .= '</div>';
      $input .= '<script  type="text/javascript">
$(document).ready(function () {
$("#'.$this->generateId($name).'_button a.but_text").click(button_ref_modal);';

      if($this->getOption('nullable'))
        $input .= '$("#'.$this->generateId($name).'_clear").click(button_ref_clear);';
      $input .= '});</script>';

      return $input;
  }

  protected function configure($options = array(), $attributes = array())
  {
      parent::configure($options, $attributes);
      $this->addRequiredOption('model');
      $this->addOption('confirm_msg') ;
      $this->addOption('method', '__toString');
      $this->addOption('nullable', false);
      $this->addOption('deletable', false);
      $this->addOption('is_hidden', false);
      $this->addOption('button_is_hidden', false);
      $this->addRequiredOption('link_url');
      $this->addRequiredOption('box_title');
      $this->addOption('box_remove_title','Delete this object ?'); //default value should not be used, because it will not be translated
      $this->addOption('button_class', 'button');
      $this->addOption('default_name', null);
      $this->addOption('url_params', array());
      $this->addOption('edit_route', null);
      $this->addOption('edit_route_params', array());
  }

  public function getJavaScripts()
  {
    return array('/js/button_ref.js');
  }

  public function getName($value, $default='')
  {
    if(is_numeric($value) && $value !=0)
        $object = Doctrine::getTable($this->getOption('model'))->find($value);
    else
        return $default;
    if(! $object)
        return $default;
    $method = $this->getOption('method');

    try {
        return  $object->$method();
    } catch (Exception $e) {
        throw $e;
    }
  }

}
