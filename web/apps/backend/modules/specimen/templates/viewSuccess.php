<?php slot('title', __('View Specimen'));  ?>
<?php include_partial('widgets/list', array('widgets' => $widget_list, 'category' => 'specimen','eid'=> $specimen->getId(),'view' => true)); ?>
<?php use_stylesheet('widgets.css') ?>
<?php use_javascript('widgets.js') ?>
<?php use_javascript('button_ref.js') ?>
<div class="page">
  <h3 class="spec">
  
  <span class="title"><?php echo __('View Specimen');?></span>
    <span class="specimen_actions">
        <?php if($sf_user->isPinned($specimen->getId(), 'specimen')) {
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off hidden'));
        }else{
          $txt = image_tag('blue_pin_on.png', array('class'=>'pin_but pin_on hidden'));
          $txt .= image_tag('blue_pin_off.png', array('class'=>'pin_but pin_off'));
        }?>
        <?php echo link_to($txt, 'savesearch/pin?source=specimen&id='.$specimen->getId(), array('class'=>'pin_link'));?>
        <?php if($hasEncodingRight):?>
          <?php echo link_to(image_tag('edit.png', array("title" => __("Edit"))), 'specimen/edit?id='.$specimen->getId()); ?>
        <?php endif;?>
    </span>
  </h3>
  <div class="encod_screen edition" id="intro">
  <?php if($count>1):?>
    <div><ul class="error_list main_error_list"><?php print($count);?> Specimen with same collection number</ul></div>
  <?php endif;?> 
   <div>
      <?php include_partial('widgets/screen', array(
        'widgets' => $widgets,
        'category' => 'specimenwidgetview',
        'columns' => 2,
        'options' => array('eid'=> $specimen->getId(), 'level' => 2, 'view' => true),
      )); ?>
    </div>

    <p class="clear"></p>
    <p align="right">
      &nbsp;<a class="bt_close" href="<?php echo url_for('specimensearch/index') ?>" id="spec_cancel"><?php echo __('Back');?></a>
    </p>
  </div>
</div>
<script  type="text/javascript">

$(document).ready(function () {
  $('body').catalogue({});
  $('.pin_but').click(function(e){
    e.preventDefault();
    if($(this).hasClass('pin_on'))
    {
      $(this).parent().find('.pin_off').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 0;
    }
    else
    {
      $(this).parent().find('.pin_on').removeClass('hidden');
      $(this).addClass('hidden') ;
      pin_status = 1;
    }
    $.get( $(this).parent().attr('href') + '/status/' + pin_status,function (html){});
  });
});
</script>
