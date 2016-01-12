<?php 
class widgetFormTagEntry extends sfWidgetFormInput
{
  protected function configure($options = array(), $attributes = array())
  {                                                                      
    parent::configure($options, $attributes);                          
    $this->addRequiredOption('choices');                                 
  }

  function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $input = $this->renderTag('input', array_merge(array('type' => 'hidden', 'name' => $name, 'value' => $value), $attributes));
    $gen_id = $this->generateId($name);
    $array_possible = $this->getOption('choices');
    $array_selected = explode(',',$value);

    $input .= '<ul class="tag_selected" id="'.$gen_id.'_selected">';

    foreach($array_selected as $item)
    {
      if(isset($array_possible[$item]))
      {
	$input .= '<li alt="a_'.$item;
	$input .= '">'.$array_possible[$item].'<img src="/images/widget_help_close.png"></li>';

      }
    }

    $input .= '</ul>';

    $input .= '<ul class="tag_available" id="'.$gen_id.'_available">';

    foreach($array_possible as $i => $item)
    {
      $input .= '<li alt="a_'.$i.'"';
      if(array_search($i, $array_selected) !== false)
         $input .= ' class="hidden"';
      $input .= '">'.$item.'</li>';
    }

    $input .= '</ul>';
    $input .= sprintf(<<<EOF
<script type="text/javascript"> 
$(document).ready(function () {
   //ADD
  $('#%1\$s li').bind('click',function() {
	new_elem = $('<li class="'+$(this).attr('class')+'" alt="'+$(this).attr('alt')+'">'+$(this).text()+'<img src="/images/widget_help_close.png"></li>');
    $('#%2\$s').append(new_elem);
    value = trim($(this).attr('alt').substr(2));
    $(this).addClass('hidden');
    if($('#%3\$s').val() =='')
      $('#%3\$s').val(value)
    else
      $('#%3\$s').val( $('#%3\$s').val() + ',' + value);
	new_elem.find('img').click(remove_tag);
  });
  
  
  //REMOVE
  $('#%2\$s img').click(remove_tag);

  function remove_tag() {
    avail_el = $('#%1\$s [alt$="'+$(this).parent().attr('alt')+'"]');

    $(this).parent().remove();
    avail_el.removeClass('hidden');
    value = trim(avail_el.attr('alt').substr(2));
    old_value = $('#%3\$s').val();
    old_value = old_value.replace(value,'');
    old_value = old_value.replace(/,,/g, ',');
    old_value = old_value.replace(/^,/,'');
    old_value = old_value.replace(/,$/,'');
    $('#%3\$s').val(old_value);
  }
});
</script>                                                                                                                            
EOF
,$gen_id.'_available',
$gen_id.'_selected',
$gen_id);

    return  $input;
  }

}
