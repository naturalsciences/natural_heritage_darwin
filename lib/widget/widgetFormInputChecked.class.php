<?php

class widgetFormInputChecked extends sfWidgetFormInputHidden
{
    protected function configure($options = array(), $attributes = array())
    {
        parent::configure($options, $attributes);
        $this->addRequiredOption('model');
        $this->addRequiredOption('link_url', '#');
        $this->addOption('behindScene', true);
        $this->addOption('method', '__toString');
        $this->addOption('notExistingAddDisplay', true);
        $this->addOption('notExistingAddTitle', 'This entry do not exist. Would you like we had it ?');
        $this->addOption('notExistingAddValues', array('Yes', 'No'));
        $this->addOption('notExistingAddSelected', 0);
        $this->addOption('autocomplete', true);
        $this->addOption('autocomplete_max', 40);
        $this->addOption('autocomplete_minChars', 3);
        $this->addOption('autocomplete_autoFill', true);
        $this->addOption('nullable', false);
        $this->addOption('is_hidden', false);
    }

  public function getJavaScripts()
  {
    return array('/js/jquery.autocomplete.js');
  }

  public function getStylesheets()
  {
    return array('/css/jquery.autocomplete.css' => 'all');
  }

  public function getName($value)
  {
    if(is_numeric($value))
      $object = Doctrine::getTable($this->getOption('model'))->find($value);
    else
      return '';
    if(! $object)
      return '';

    $method = $this->getOption('method');
    try
    {
      return  $object->$method();
    }
    catch (Exception $e)
    {
      throw $e;
    }
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $values = array_merge(array('text' => '', 'is_empty' => false), is_array($value) ? $value : array());
    $obj_name = $this->getName($value);
    $showedInputName = $this->generateId($name);
    $inputTagAttributes = array('type' => 'text', 'value' => $this->escapeOnce($obj_name), 'name' => $name);
    $input = '<ul><li>';
    if($this->getOption('behindScene'))
    {
      $input .= parent::render($name, $value, $attributes, $errors);
      array_splice($inputTagAttributes, 2);
      $showedInputName .= "_name";
    }
    $inputTagAttributes['id'] = $showedInputName;
    $input .= $this->renderTag(
      'input',
      $inputTagAttributes
    );
    $input .= '</li>';
    if($this->getOption('notExistingAddDisplay'))
    {
      $input .= '<li id="toggledMsg" class="hidden">'.
        '<label for="'.$this->generateId($name).'_check">'.$this->getOption('notExistingAddTitle').':</label>'.
        '<select id="'.$this->generateId($name).'_check">';
      foreach ($this->getOption('notExistingAddValues') as $key => $option)
      {
        $input .= $this->renderContentTag(
          'option',
          $option,
          array(
            'selected' => ($this->getOption('notExistingAddSelected') == $key) ? "selected" : "",
            'value' => $key,
          )
        );       
      }
      $input .= '</select></li>';       
    }
    $input .= '</ul>';
    if(!function_exists('url_for'))
      sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $input .= sprintf(<<<EOF
<script type="text/javascript">
  var lastCaller;
  function %1\$s_updateHiddenId (value, link_url)
  {
    $.ajax({type: "GET",
            url: link_url,
            data: {'searchedCrit' : value},
            success: function(html){
                                     if (html == 'not found')
                                     {
                                       if (lastCaller == 'onChange')
                                       {
                                         $('#%1\$s').closest('ul').children('li#toggledMsg').show("slow");
                                       }
                                     }
                                     else
                                     {
                                       $('#%1\$s').closest('ul').children('li#toggledMsg').slideUp("fast");
                                       $('#%8\$s').val(html);
                                     }
                                     showAfterRefresh($('#%1\$s').closest('.widget_content'));
                                   }
           });
  }
$(document).ready(function () {
    var link_url = '%2\$s';
    $('#%1\$s').live('change', function()
    {
      hideForRefresh($(this).closest('.widget_content'));
      $(this).prev().val('');
      toggledMsg = $(this).closest('ul').children('li#toggledMsg');
      toggledMsg.find('option:first').attr('selected', "selected");
      toggledMsg.find('option:last').removeAttr('selected');
      lastCaller = "onChange";
      %1\$s_updateHiddenId($(this).val(), link_url);
    });
    if ('%4\$s')
    {
      $("#%1\$s").autocomplete('%3\$s').setOptions({max: %5\$s, minChars: %6\$s, autoFill: '%7\$s'});
      $("#%1\$s").result(function(event, data, formatted) 
                         {
                           hideForRefresh($(this).closest('.widget_content'));
                           lastCaller = "result";
                           %1\$s_updateHiddenId(formatted, link_url);
                         });
    }
});
</script>
EOF
    ,
    $showedInputName,
    url_for($this->getOption('link_url')),
    url_for($this->getOption('link_url').'Limited'),
    $this->getOption('autocomplete'),
    $this->getOption('autocomplete_max'),
    $this->getOption('autocomplete_minChars'),
    $this->getOption('autocomplete_autoFill'),
    $this->generateId($name));
    return $input;
  }  
}
