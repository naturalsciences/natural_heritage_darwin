<?php

class widgetFormSelectComplete extends sfWidgetFormDarwinDoctrineChoice
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('change_label');
    $this->addRequiredOption('add_label');
    $this->addOption('forced_choices', false);
    parent::configure($options, $attributes);
  }

  public function getChoices()
  {
    if(! isset($this->choices))
    {
      if($this->getOption('forced_choices') !== false )
        $this->choices = $this->getOption('forced_choices');
      else
        $this->choices = parent::getChoices();
    }
    return $this->choices;
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
      $this->getChoices();
      $widget = '<div id="'.$this->generateId($name).'_parent" class="complete_widget">';
      $class = (!isset($attributes['class']))?'':$attributes['class'];
      if( isset($this->choices[$value]) )
      {
        $widget .= $this->renderTag('input', array( 'type' => 'text', 'id' => $id = $this->generateId($name).'_input', 'class' => 'hidden ' . $class ));
        $widget .= parent::render($name, $value, $attributes, $errors);
        $add_class='';
        $pick_class=' hidden';
      }
      else
      {
        $widget .= $this->renderTag('input', array( 'type' => 'text', 'id' => $id = $this->generateId($name).'_input', 'value' => $value, 'name'=> $name, 'class'=>$class));
        $widget .= parent::render('', $value, array('class'=>'hidden'), $errors);
        $add_class=' hidden';
        $pick_class='';
      }
      $widget .= '<div class="add_item_button'. $add_class .'">'.$this->renderTag('img',array('src'=>'/images/add_green.png', 'alt'=>'+')).'<span>'.__($this->getOption('add_label')).'</span></div>';
      $widget .= '<div class="change_item_button'. $pick_class .'">'.$this->renderTag('img',array('src'=>'/images/refresh_green.png', 'alt'=>'+')).'<span>'.__($this->getOption('change_label')).'</span></div>';
      $widget .= '</div>';
      
      $widget .=  sprintf(<<<EOF
<script type="text/javascript">
$(document).ready(function () {
    $('#%1\$s .add_item_button').click(function()
    {
      parent_id = '%1\$s';
      el = $('#'+parent_id +' select');
      el.addClass('hidden');
      $('#'+parent_id +' input').attr('name', el.attr('name'))
      $('#'+parent_id +' input').removeClass('hidden');
      el.removeAttr('name');
      $('#'+parent_id +' .change_item_button').removeClass('hidden');
      $('#'+parent_id +' .add_item_button').addClass('hidden');
    });

    $('#%1\$s .change_item_button').click(function()
    {
	parent_id = '%1\$s';
        el = $('#'+parent_id +' input');
	el.addClass('hidden');
	$('#'+parent_id +' select').attr('name', el.attr('name'));
	$('#'+parent_id +' select').removeClass('hidden');
	el.removeAttr('name');
        $('#'+parent_id +' .add_item_button').removeClass('hidden');
        $('#'+parent_id +' .change_item_button').addClass('hidden');
    });
});
</script>
EOF
    , $this->generateId($name).'_parent');
      return $widget;
  }
}
