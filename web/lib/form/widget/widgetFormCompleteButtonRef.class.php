<?php

class widgetFormCompleteButtonRef extends widgetFormButtonRef
{

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (isset($attributes['class'])) {
      $class = ' '.$attributes['class'];
    } else {
      $attributes['class'] = '';
      $class = '';
    }

    $values = array_merge(array('text' => '', 'is_empty' => false), is_array($value) ? $value : array());

    $input = '<div class="complete_ref">'. parent::ParentRender($name, $value, $attributes, $errors);

    $obj_name = $this->getName($value);
    if($this->getOption('default_name'))
      $obj_name = $this->getOption('default_name') ;

    $complete_options = array(
      'id' => $this->generateId($name)."_name",
      'value' => $obj_name,
      'placeholder' => $this->getOption('box_title'),
      'class' => "ref_name" . $class,
      'type'=> 'search',
    );

    if(! $this->getOption('nullable')) {
      $complete_options['required'] = 'required';
    }

    $input .= $this->renderTag('input',$complete_options );

    if ($this->getOption('button_class') != '') {
      $class .= ' '.$this->getOption('button_class');
    }

    if($this->getOption('button_is_hidden') && $value == 0)
    {
      $class .= ' hidden';
    }

    if($this->getOption('deletable'))
    {
      $options = array(
        'src' => '/images/remove.png',
              'id' => $this->generateId($name)."_clear",
        'class' => "ref_clear_people" . $class
      );
      $input .= $this->renderTag('img',$options);
    }

    $input .= $this->renderContentTag('div',link_to(' ', $this->getOption('link_url'), array('class' => 'but_more','title'=>$this->getOption('box_title') )),
      array('title'=> $this->getOption('box_title'),
      'id' => $this->generateId($name).'_button',
      'class' => 'ref_name' .$class,
    ));

    $input .= '<script  type="text/javascript">
      $(document).ready(function () {
      $("#'.$this->generateId($name).'_name").catcomplete({source: "'.url_for($this->getOption('complete_url')).'"});
      $("#'.$this->generateId($name).'_button a.but_more").click(button_ref_modal);
	  //ftheeten 2018 03 19 to allow empty collection
	  $("#'.$this->generateId($name).'_name").change(
		function()
		{
		    var tmpVal=$("#'.$this->generateId($name).'_name").attr(\'value\');
			if( tmpVal.length==0)
		    {
				
				$("#'.$this->generateId($name).'").attr(\'value\',-1) ;
				$("#'.$this->generateId($name).'_name").val(\'\') ;
			}
		}
	  );
	  ';

   if($this->getOption('deletable'))
        $input .= '$("#'.$this->generateId($name).'_clear").click(function(){
          if(confirm("'.$this->getOption('confirm_msg').'"))
          {
            $("#'.$this->generateId($name).'").attr(\'value\',-1) ;
            $("#'.$this->generateId($name).'_name").val(\'\') ;
          }
        });';

    $input .= '
    });</script>
    </div>';

    return $input;
  }


  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('complete_url');
  }

  public function getJavaScripts()
  {
    $js = parent::getJavaScripts();
    $js[] = 'ui.complete.js';
    return $js;
  }

}
