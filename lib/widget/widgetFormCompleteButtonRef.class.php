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

    $jsonData = array();
    $buttonRefData = '';
    if ($this->getOption('field_level_id') != '') {
      $jsonData['field_level_id'] = 'function(){return $("#'.$this->getOption('field_level_id').'").val();}';
    }
    if ($this->getOption('field_to_clean_class') != '') {
      $jsonData['field_to_clean_class'] = '"'.$this->getOption('field_to_clean_class').'"';
      $buttonRefData = $this->getOption('field_to_clean_class');
    }


    $input .= $this->renderContentTag(
      'div',
      link_to(' ',
              $this->getOption('link_url'),
              array(
                'class' => 'but_more',
                'title'=>$this->getOption('box_title'),
                'data-field-to-clean' => $buttonRefData
              )
      ),
      array(
        'title'=> $this->getOption('box_title'),
        'id' => $this->generateId($name).'_button',
        'class' => 'ref_name' .$class
      )
    );

    $input .= '<script  type="text/javascript">
      $(document).ready(function () {
      $("#'.$this->generateId($name).'_name").catcomplete({source: "'.url_for($this->getOption('complete_url')).'"';
      if (count($jsonData) !==0) {
        $input .= ', data : {';
        foreach($jsonData as $key=>$val) {
          $input .= $key.': '.$val.',';
        }
        $input = rtrim($input, ',');
        $input .= '}';
      }
      $input .= '});
      $("#'.$this->generateId($name).'_button a.but_more").click(button_ref_modal);';

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
    $this->addOption('field_level_id');
    $this->addOption('field_to_clean_class');
  }

  public function getJavaScripts()
  {
    $js = parent::getJavaScripts();
    $js[] = 'ui.complete.js';
    return $js;
  }

}
