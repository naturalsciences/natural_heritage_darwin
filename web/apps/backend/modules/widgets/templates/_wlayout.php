<?php $read_only = (isset($options['view']) && $options['view'])?true:false ; ?>
<?php $widget_content = get_component($category, $widget, $sf_data->getRaw('options')); ?>
<li class="widget" id="<?php echo $widget;?>" <?php if(isset($col_num)):?> col-ref="<?php echo $col_num;?>" <?php endif;?>>

  <div class="widget_top_bar">
    <div class="widget_top_button <?php if($is_opened):?> hidden<?php endif;?>"></div>
    <div class="widget_bottom_button <?php if(! $is_opened):?> hidden<?php endif;?> "></div>
    
    <div class="widget_top_bar_button">
      <?php if($category=='boardwidget'):?>
          <a href="#" class="widget_refresh" ><?php echo image_tag('widget_refresh.png', 'alt=Refresh');?></a>
      <?php endif;?>
      <?php if(! $is_mandatory):?>
          <a href="#" class="widget_close" ><?php echo image_tag('widget_close.png', 'alt=Close');?></a>
      <?php endif;?>
    </div>
    <span>
      <?php if($category!='boardwidget' && !$read_only) : ?>          
        <?php echo help_ico(MyWidgets::getHelpIcon($category,$widget),$sf_user);?>
      <?php endif ; ?>
      <?php echo __($title); ?>
    </span>
  </div>
  <div class="widget_content <?php if(! $is_opened):?>hidden<?php endif;?>">

        <?php echo $widget_content; ?>
  </div>

</li>
