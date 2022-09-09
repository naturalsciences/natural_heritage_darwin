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
	  
	   //ftheeten 2018 02 08
	  if(strtolower($options["model"])=="collections"&&(strpos($_SERVER['REQUEST_URI'],'specimen')))
	  {
	
        if(array_key_exists('collection_ref_session',$_COOKIE ))
         {
			
			$findCol=Doctrine_Core::getTable("Collections")->find($_COOKIE['collection_ref_session']);
			if(!($findCol))
		    {
				
				//setcookie("collection_ref_session", "", time()-3600);
				unset($_COOKIE['collection_ref_session']);
				$this->addOption('default',NULL);
			}
			else
			{
				
				$this->addOption('default',$_COOKIE['collection_ref_session']);
			}
		}
      }
      
      //ftheeten 2018 02 08
	   if(strtolower($options["model"])=="institutions"&&(strpos($_SERVER['REQUEST_URI'], "/new")||strpos($_SERVER['REQUEST_URI'],'edit')))
	  {
		
        if(array_key_exists('institution_ref_session',$_COOKIE ))
         {
			
			$findCol=Doctrine_Core::getTable("Institutions")->find($_COOKIE['institution_ref_session']);
			if(!($findCol))
		    {
				
				
				unset($_COOKIE['institution_ref_session']);
				$this->addOption('default',NULL);
			}
			else
			{
				
				$this->addOption('default',$_COOKIE['institution_ref_session']);
			}
		}
      }
      
  }

  public function getJavaScripts()
  {
    return array('/js/button_ref.js');
  }

  public function getName($value, $default='')
  {
    if(is_numeric($value) && $value !=0)
        $object = Doctrine_Core::getTable($this->getOption('model'))->find($value);
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
