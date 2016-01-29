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
        $this->addOption('notExistingAddTitle', 'This entry does not exist. Would you like to add it ?');
        $this->addOption('notExistingAddValues', array('Yes', 'No'));
        $this->addOption('notExistingAddSelected', 0);
        $this->addOption('autocomplete', true);
        $this->addOption('autocomplete_max', 40);
        $this->addOption('autocomplete_minChars', 3);
        $this->addOption('autocomplete_autoFill', true);
        $this->addOption('nullable', false);
        $this->addOption('is_hidden', false);
    }

  public function getStylesheets()
  {
    return array('/css/ui.datepicker.css' => 'all');
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
    $input = '<ul class="container"><li>';
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
    
    $input .=  sprintf(<<<EOF
<script type="text/javascript">
$(document).ready(function () {
    var cache = {},
      lastXhr;
      $('#%1\$s').autocomplete({
      minLength: %6\$s,
      source: function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }

        lastXhr = $.get('%3\$s', {q : request.term, limit: %5\$s }, function( data, status, xhr ) {
          ndata = data.split("\\n");
          cache[ term ] = ndata;
          if ( xhr === lastXhr ) {
            response( ndata );
          }
        });
      },
      select: function(event, ui) {
        $('#%1\$s').closest('ul').children('li#toggledMsg').slideUp("fast");
      }
    }).bind('blur',function (event) {
        $(this).prev().val('');
        $(this).closest('ul').find('#toggledMsg select').val(0);

        $.ajax({type: "GET",
            url: '%2\$s',
            async:false, //Unfortunaley keep this to avoid racing condition
            data: {'searchedCrit' : $(this).val()},
            success: function(html){
              if (html == 'not found') {
                  $('#%1\$s').closest('ul').children('li#toggledMsg').show();
                  $('#%1\$s').trigger('missing');
              }
              else {
                $('#%1\$s').closest('ul').children('li#toggledMsg').hide();
                $('#%8\$s').val(html);
              }
            }
        });
      });
  });
</script>

EOF
 ,$showedInputName,
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
